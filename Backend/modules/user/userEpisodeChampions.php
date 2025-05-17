<?php

function getEpisodeStars($universes, $episodeId) {
    foreach ($universes as $universe) {
        if ($universe['id'] == $episodeId) {
            $totalStars = 0;
            foreach ($universe['levels'] as $level) {
                $totalStars += $level['stars'] ?? 0;
            }
            return ['stars' => $totalStars, 'exists' => true];
        }
    }
    return ['stars' => 0, 'exists' => false];
}

function getEpisodeChampions($episodeIdData, $sessionKey) {
    global $pdo;
    
    $oauthToken = getOAuthTokenFromSession($pdo, $sessionKey);    
    
    if (is_string($episodeIdData)) {
        $episodeIds = json_decode($episodeIdData, true);
    } else if (is_array($episodeIdData)) {
        $episodeIds = $episodeIdData;
    } else if (is_numeric($episodeIdData)) {
        $episodeIds = [$episodeIdData];
    } else {
        return json_encode([]);
    }
    
    if (!is_array($episodeIds) || empty($episodeIds)) {
        return json_encode([]);
    }
    
    $socialData = fetchUserSocialData($oauthToken);
    $playerProfile = getUserProfileFromSocialData($pdo, $socialData);
    $playerUniverses = getUserUniverses($playerProfile['userId']);
    $friendsProfiles = getFriendsProfiles($pdo, $socialData);
    $champions = [];
    
    foreach ($episodeIds as $episodeId) {
        // Ensure episodeId is numeric
        if (!is_numeric($episodeId)) {
            continue;
        }
        
        $episodeId = (int)$episodeId;
        $playerStarsData = getEpisodeStars($playerUniverses, $episodeId);
        $playerStars = $playerStarsData['stars'];
        $isChampion = true;
        $highestStars = $playerStars;
        $currentChampion = null;
        
        if ($playerStarsData['exists']) {
            $currentChampion = [
                'episodeId' => $episodeId,
                'userId' => $playerProfile['userId'],
                'stars' => $playerStars
            ];
        }
        
        if (is_array($friendsProfiles)) {
            foreach ($friendsProfiles as $friend) {
                $friendUniverses = getUserUniverses($friend['userId']);
                $friendStarsData = getEpisodeStars($friendUniverses, $episodeId);
                
                if ($friendStarsData['exists'] && $friendStarsData['stars'] > $highestStars) {
                    $isChampion = false;
                    $highestStars = $friendStarsData['stars'];
                    $currentChampion = [
                        'episodeId' => $episodeId,
                        'userId' => $friend['userId'],
                        'stars' => $friendStarsData['stars']
                    ];
                }
            }
        }
        
        if ($currentChampion) {
            $champions[] = $currentChampion;
        }
    }
    
    return json_encode($champions);
}

?>
