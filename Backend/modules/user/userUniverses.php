<?php

function getUserUniverses($userId) {
    global $userFolder;
    $universes = [];
    $regularEpisodeCount = 189;
    $specialEpisodeStart = 1201;
    $specialEpisodeEnd = 1245;
    
    $lastUncompletedEpisodeFound = false;
    
    for ($episodeId = 1; $episodeId <= $regularEpisodeCount; $episodeId++) {
        $episodeDir = $userFolder . "/{$userId}/{$episodeId}";
        $levelsData = [];
        $levelCount = ($episodeId <= 2) ? 10 : 15;
        $episodeCompleted = true;
        $lastCompletedLevel = 0;
        $hasAnyLevelFile = false;
        
        for ($levelId = 1; $levelId <= $levelCount; $levelId++) {
            $levelFile = "{$episodeDir}/{$levelId}.txt";
            if (file_exists($levelFile)) {
                $hasAnyLevelFile = true;
                $levelData = json_decode(file_get_contents($levelFile), true);
                if ($levelData && 
                    ((isset($levelData['score']) && $levelData['score'] > 0) || 
                     (isset($levelData['stars']) && $levelData['stars'] > 0))) {
                    $lastCompletedLevel = $levelId;
                }
            }
        }
        
        if (!$hasAnyLevelFile && $episodeId > 1 && !$lastUncompletedEpisodeFound) {
            continue;
        }
        
        for ($levelId = 1; $levelId <= $levelCount; $levelId++) {
            $levelFile = "{$episodeDir}/{$levelId}.txt";
            
            $levelData = [
                'id' => $levelId,
                'episodeId' => $episodeId,
                'score' => 0,
                'stars' => 0,
                'unlocked' => false,
                'completedAt' => 0,
                'unlockConditionDataList' => []
            ];
            
            if (file_exists($levelFile)) {
                $existingData = json_decode(file_get_contents($levelFile), true);
                if ($existingData) {
                    $levelData = array_merge($levelData, $existingData);
                }
            }
            
            $isUnlocked = false;
            if ($episodeId == 1 && $levelId == 1) {
                $isUnlocked = true;
            }
            else if ($levelId <= $lastCompletedLevel + 1) {
                $isUnlocked = true;
            }
            
            $levelData['unlocked'] = $isUnlocked;
            $levelsData[] = $levelData;
            
            if (!$isUnlocked && $levelId > $lastCompletedLevel + 1) {
                break;
            }
        }
        
        if ($episodeId == 1 || $hasAnyLevelFile) {
            $universes[] = [
                'id' => $episodeId,
                'levels' => $levelsData
            ];
            
            if (!$episodeCompleted) {
                $lastUncompletedEpisodeFound = true;
            }
        }
        
        if ($lastUncompletedEpisodeFound) {
            break;
        }
    }
    
    for ($episodeId = $specialEpisodeStart; $episodeId <= $specialEpisodeEnd; $episodeId++) {
        $episodeDir = $userFolder . "/{$userId}/{$episodeId}";
        $levelsData = [];
        $levelCount = ($episodeId >= 1201 && $episodeId <= 1202) ? 10 : 15;
        $lastCompletedLevel = 0;
        $hasAnyLevelFile = false;
        
        for ($levelId = 1; $levelId <= $levelCount; $levelId++) {
            $levelFile = "{$episodeDir}/{$levelId}.txt";
            if (file_exists($levelFile)) {
                $hasAnyLevelFile = true;
                $levelData = json_decode(file_get_contents($levelFile), true);
                if ($levelData && 
                    ((isset($levelData['score']) && $levelData['score'] > 0) || 
                     (isset($levelData['stars']) && $levelData['stars'] > 0))) {
                    $lastCompletedLevel = $levelId;
                }
            }
        }
        
        $isFirstSpecialEpisode = ($episodeId == $specialEpisodeStart);
        if (!$hasAnyLevelFile && !$isFirstSpecialEpisode) {
            continue;
        }
        
        for ($levelId = 1; $levelId <= $levelCount; $levelId++) {
            $levelFile = "{$episodeDir}/{$levelId}.txt";
            
            $levelData = [
                'id' => $levelId,
                'episodeId' => $episodeId,
                'score' => 0,
                'stars' => 0,
                'unlocked' => false,
                'completedAt' => 0,
                'unlockConditionDataList' => []
            ];
            
            if (file_exists($levelFile)) {
                $existingData = json_decode(file_get_contents($levelFile), true);
                if ($existingData) {
                    $levelData = array_merge($levelData, $existingData);
                }
            }
            
            $isUnlocked = ($isFirstSpecialEpisode && $levelId == 1) ||
                         ($levelId <= $lastCompletedLevel + 1);
            
            $levelData['unlocked'] = $isUnlocked;
            $levelsData[] = $levelData;
            
            if (!$isUnlocked && $levelId > $lastCompletedLevel + 1) {
                break;
            }
        }
        
        if ($isFirstSpecialEpisode || $hasAnyLevelFile) {
            $universes[] = [
                'id' => $episodeId,
                'levels' => $levelsData
            ];
        }
    }
    
    return $universes;
}

function getLevelTopList($episodeId, $levelId, $pdo, $accessToken, $ownUserId) {
    global $userFolder;
    
    $socialData = fetchUserSocialData($accessToken);
    $socialFriendsInfo = getSocialFriendsInfo($pdo, $accessToken);
    
    if ($socialData) {
        $socialFriendsInfo[] = [
            'userId' => $ownUserId,
            'externalUserId' => $socialData['id'],
            'name' => $socialData['name'] ?? 'You'
        ];
    }
    
    if (!$socialFriendsInfo) {
        return [
            'episodeId' => $episodeId,
            'levelId' => $levelId,
            'toplist' => []
        ];
    }
    
    $scores = [];
    $currentUserAdded = false;
    
    foreach ($socialFriendsInfo as $friend) {
        $userId = $friend['userId'];
        $scorePath = "{$userFolder}/{$userId}/{$episodeId}/{$levelId}.txt";
        
        if (file_exists($scorePath)) {
            $jsonContent = file_get_contents($scorePath);
            $scoreData = json_decode($jsonContent, true);
            if ($scoreData && isset($scoreData['score']) && $scoreData['score'] > 0) {
                $scores[] = [
                    'userId' => (int)$userId,
                    //'name' => $friend['name'],
                    'value' => (int)$scoreData['score']
                ];
                
                if ($userId == $ownUserId) {
                    $currentUserAdded = true;
                }
            }
        }
    }
    
    if (!$currentUserAdded) {
        $scores[] = [
            'userId' => (int)$ownUserId,
            'value' => 0
        ];
    }
    
    usort($scores, function($a, $b) {
        return $b['value'] - $a['value'];
    });
    
    return [
        'episodeId' => $episodeId,
        'levelId' => $levelId,
        'toplist' => $scores
    ];
}

function syncLevels($params, $pdo, $sessionKey) {
    global $userFolder;
    
    $levelData = $params[0];
    $userId = getUserIdBySessionKey($pdo, $sessionKey);
    
    if (!$userId) {
        return [];
    }
    
    foreach ($levelData as $level) {
        if (!isset($level['id']) || !isset($level['episodeId']) || !isset($level['score']) || !isset($level['stars'])) {
            continue;
        }
        
        $levelId = $level['id'];
        $episodeId = $level['episodeId'];
        $score = $level['score'];
        $stars = $level['stars'];
        $unlocked = isset($level['unlocked']) ? $level['unlocked'] : true;
        
        $episodeDir = $userFolder . "/{$userId}/{$episodeId}";
        if (!is_dir($episodeDir)) {
            mkdir($episodeDir, 0777, true);
        }
        
        $levelFile = "{$episodeDir}/{$levelId}.txt";
        $existingData = [];
        
        if (file_exists($levelFile)) {
            $existingData = json_decode(file_get_contents($levelFile), true);
            
            if (!$existingData) {
                $existingData = [];
            }
            
            if (isset($existingData['score']) && $existingData['score'] >= $score &&
                isset($existingData['stars']) && $existingData['stars'] >= $stars) {
                continue;
            }
        }
        
        $levelToSave = [
            "id" => $levelId,
            "episodeId" => $episodeId,
            "score" => $score,
            "stars" => $stars,
            "unlocked" => $unlocked,
            "completedAt" => time(),
            "unlockConditionDataList" => []
        ];
        
        if (!empty($existingData)) {
            $levelToSave = array_merge($existingData, $levelToSave);
        }
        
        file_put_contents($levelFile, json_encode($levelToSave, JSON_PRETTY_PRINT));
        
        $nextLevelId = $levelId + 1;
        $maxLevels = getMaxLevelsForEpisode($episodeId);
        
        if ($nextLevelId <= $maxLevels) {
            $nextLevelFile = "{$episodeDir}/{$nextLevelId}.txt";
            $nextLevelData = [
                "id" => $nextLevelId,
                "episodeId" => $episodeId,
                "score" => 0,
                "stars" => 0,
                "unlocked" => true,
                "completedAt" => 0,
                "unlockConditionDataList" => []
            ];
            
            if (file_exists($nextLevelFile)) {
                $existingNextLevel = json_decode(file_get_contents($nextLevelFile), true);
                if ($existingNextLevel) {
                    $nextLevelData = array_merge($nextLevelData, $existingNextLevel);
                    $nextLevelData['unlocked'] = true;
                }
            }
            
            file_put_contents($nextLevelFile, json_encode($nextLevelData, JSON_PRETTY_PRINT));
        }
    }
    
    return [];
}

function getMaxLevelsForEpisode($episodeId) {
    if ($episodeId <= 2) return 10;
    if ($episodeId >= 1201 && $episodeId <= 1202) return 10;
    return 15;
}

function getFriendsTopBonusLevel($pdo, $tokenData) {
    $friends = [];
    
    if (!empty($tokenData['oauth_token'])) {
        $socialFriendsInfo = getSocialFriendsInfo($pdo, $tokenData['oauth_token']);
        
        if ($socialFriendsInfo) {
            foreach ($socialFriendsInfo as $friend) {
                $friendUserId = $friend['userId'];
                $friendUniverse = getUserUniverses($friendUserId);
                
                $latestEpisode = 0;
                $latestLevel = 0;
                
                foreach ($friendUniverse as $episode) {
                    if ($episode['id'] >= 1201) {
                        foreach ($episode['levels'] as $level) {
                            if (!empty($level['score']) && $level['score'] > 0) {
                                if (
                                    $episode['id'] > $latestEpisode || 
                                    ($episode['id'] == $latestEpisode && $level['id'] > $latestLevel)
                                ) {
                                    $latestEpisode = $episode['id'];
                                    $latestLevel = $level['id'];
                                }
                            }
                        }
                    }
                }
                
                if ($latestEpisode > 0) {
                    $friends[] = [
                        'episodeId' => $latestEpisode,
                        'levelId' => $latestLevel,
                        'friendsCoreUserIds' => [$friendUserId]
                    ];
                }
            }
        }
    }
    
    return $friends;
}

?>
