<?php
function fetchUserSocialData($accessToken) {
    $url = "https://graph.spiritaccount.net/me?fields=picture,friends,name,first_name,last_name,id,is_eligible_promo&access_token={$accessToken}";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    
    if (curl_errno($ch)) {
        error_log('Curl error: ' . curl_error($ch));
        return null;
    }
    
    curl_close($ch);
    return json_decode($response, true);
}

function storeSocialImage($imageUrl, $userId, $tempDir = 'u') {
    $basePath = '/var/www/cdn-candycrush/public_html/';
    $fullPath = $basePath . $tempDir;
    
    if (!file_exists($fullPath)) {
        mkdir($fullPath, 0777, true);
    }
    
    $fileName = $userId . '.jpeg';
    $filePath = $fullPath . '/' . $fileName;
    
    if (file_exists($filePath) && (time() - filemtime($filePath) < 86400)) {
        return "https://cdn-candycrush.spiritaccount.net/" . $tempDir . "/" . $fileName;
    }
    
    $ch = curl_init($imageUrl);
    $fp = fopen($filePath, 'wb');
    
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    
    curl_exec($ch);
    
    if (curl_errno($ch)) {
        error_log('Image download error: ' . curl_error($ch));
        fclose($fp);
        curl_close($ch);
        return null;
    }
    
    fclose($fp);
    curl_close($ch);
    
    return "https://cdn-candycrush.spiritaccount.net/" . $tempDir . "/" . $fileName;
}

function getUserProfileFromSocialData($pdo, $socialData) {
    $stmt = $pdo->prepare("SELECT id FROM users WHERE fbuid = :external_user_id");
    $stmt->execute(['external_user_id' => $socialData['id']]);
    $internalUser = $stmt->fetch(PDO::FETCH_ASSOC);
    $userId = $internalUser ? $internalUser['id'] : 1;

    $universes = getUserUniverses($userId);
    
    $stats = calculateGameStats($universes);

    $picUrl = "https://graph.spiritaccount.net/{$socialData['id']}/picture";
    $picSquareUrl = "https://graph.spiritaccount.net/{$socialData['id']}/picture?type=square";
    $picSmallUrl = "https://graph.spiritaccount.net/{$socialData['id']}/picture?type=small";
    
    $localPicUrl = storeSocialImage($picUrl, $socialData['id'], 'u/normal');
    $localPicSquareUrl = storeSocialImage($picSquareUrl, $socialData['id'], 'u/square');
    $localPicSmallUrl = storeSocialImage($picSmallUrl, $socialData['id'], 'u/small');

    return [
        'userId' => $userId,
        'externalUserId' => $socialData['id'],
        'name' => $socialData['name'] ?? 'User',
        'fullName' => $socialData['name'] ?? 'User',
        'pic' => $localPicUrl ?: $picUrl,
        'picSquare' => $localPicSquareUrl ?: $picSquareUrl,
        'picSmall' => $localPicSmallUrl ?: $picSmallUrl,
        'countryCode' => "ES",
        'topEpisode' => $stats['topEpisode'],
        'topLevel' => $stats['topLevel'],
        'totalStars' => $stats['totalStars'],
        'lastLevelCompletedAt' => $stats['lastLevelCompletedAt'],
        'lastLevelCompletedEpisodeId' => $stats['lastLevelCompletedEpisodeId'],
        'lastLevelCompletedLevelId' => $stats['lastLevelCompletedLevelId'],
        'lastOnlineTime' => time(),
        'friendType' => "NONE"
    ];
}

function getFriendsProfiles($pdo, $friendsData) {
    $friendProfiles = [];
    
    if (!isset($friendsData['friends']['data'])) {
        return $friendProfiles;
    }

    $stmt = $pdo->prepare("SELECT id FROM users WHERE fbuid = :external_user_id");
    
    foreach ($friendsData['friends']['data'] as $friend) {
        $stmt->execute(['external_user_id' => $friend['id']]);
        $internalFriend = $stmt->fetch(PDO::FETCH_ASSOC);
        $friendId = $internalFriend ? $internalFriend['id'] : $friend['id'];

        $universes = getUserUniverses($friendId);
        $stats = calculateGameStats($universes);
        
        if ($stats['totalStars'] > 0) {
            $picUrl = "https://graph.spiritaccount.net/{$friend['id']}/picture";
            $picSquareUrl = "https://graph.spiritaccount.net/{$friend['id']}/picture?type=square";
            $picSmallUrl = "https://graph.spiritaccount.net/{$friend['id']}/picture?type=small";
            
            $localPicUrl = storeSocialImage($picUrl, $friend['id'], 'u/normal');
            $localPicSquareUrl = storeSocialImage($picSquareUrl, $friend['id'], 'u/square');
            $localPicSmallUrl = storeSocialImage($picSmallUrl, $friend['id'], 'u/small');
            
            $friendProfiles[] = [
                'userId' => $friendId,
                'externalUserId' => $friend['id'],
                'name' => $friend['name'],
                'fullName' => $friend['name'],
                'pic' => $localPicUrl ?: $picUrl,
                'picSquare' => $localPicSquareUrl ?: $picSquareUrl,
                'picSmall' => $localPicSmallUrl ?: $picSmallUrl,
                'countryCode' => "ES",
                'topEpisode' => $stats['topEpisode'],
                'topLevel' => $stats['topLevel'],
                'totalStars' => $stats['totalStars'],
                'lastLevelCompletedAt' => $stats['lastLevelCompletedAt'],
                'lastLevelCompletedEpisodeId' => $stats['lastLevelCompletedEpisodeId'],
                'lastLevelCompletedLevelId' => $stats['lastLevelCompletedLevelId'],
                'lastOnlineTime' => time(),
                'friendType' => "NONE"
            ];
        }
    }

    return $friendProfiles;
}

function calculateGameStats($universes) {
    $stats = [
        'topEpisode' => 0,
        'topLevel' => 0,
        'totalStars' => 0,
        'lastLevelCompletedAt' => 0,
        'lastLevelCompletedEpisodeId' => 0,
        'lastLevelCompletedLevelId' => 0
    ];

    foreach ($universes as $universe) {
        $episodeId = $universe['id'];
        
        if ($episodeId >= 1201 && $episodeId <= 1245) {
            continue;
        }

        $currentEpisodeTopLevel = 0;
        foreach ($universe['levels'] as $level) {
            $stats['totalStars'] += $level['stars'] ?? 0;

            if ($level['completedAt'] > $stats['lastLevelCompletedAt']) {
                $stats['lastLevelCompletedAt'] = $level['completedAt'];
                $stats['lastLevelCompletedEpisodeId'] = $episodeId;
                $stats['lastLevelCompletedLevelId'] = $level['id'];
            }

            if ($level['unlocked']) {
                $currentEpisodeTopLevel = max($currentEpisodeTopLevel, $level['id']);
            }
        }

        if ($episodeId > $stats['topEpisode']) {
            $stats['topEpisode'] = $episodeId;
            $stats['topLevel'] = $currentEpisodeTopLevel;
        }
    }

    return $stats;
}

function getSocialFriendsInfo($pdo, $accessToken) {
    $socialData = fetchUserSocialData($accessToken);
    
    if (!$socialData || !isset($socialData['friends']['data'])) {
        return [];
    }
    
    $stmt = $pdo->prepare("SELECT id FROM users WHERE fbuid = :external_user_id");
    $friendsInfo = [];
    
    foreach ($socialData['friends']['data'] as $friend) {
        $stmt->execute(['external_user_id' => $friend['id']]);
        $internalFriend = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $friendsInfo[] = [
            'userId' => $internalFriend ? $internalFriend['id'] : $friend['id'],
            'externalUserId' => $friend['id'],
            'name' => $friend['name']
        ];
    }
    
    return $friendsInfo;
}
?>