<?php
function gameInitLight($pdo, $sessionKey, $acceptLanguage = null) {
    global $modules;
    global $langFolder;
    
    $userData = getCurrentUser($pdo, $sessionKey);
    $tokenData = getUserTokens();
    $userId = getUserIdBySessionKey($pdo, $sessionKey);
    
    $countryCode = 'default';
    if ($acceptLanguage !== null) {
        $lang = substr($acceptLanguage, 0, 2);
        $countryCodeMap = [
            'ar' => 'AR', 'br' => 'BR', 'cn' => 'CN', 'cs' => 'CS', 
            'da' => 'DA', 'de' => 'DE', 'es' => 'ES', 'fi' => 'FI', 
            'fr' => 'FR', 'ge' => 'GE', 'he' => 'HE', 'hi' => 'HI',
            'hr' => 'HR', 'hu' => 'HU', 'id' => 'ID', 'it' => 'IT',
            'jp' => 'JP', 'kr' => 'KR', 'ms' => 'MS', 'nl' => 'NL',
            'no' => 'NO', 'pl' => 'PL', 'pt' => 'PT', 'ru' => 'RU',
            'sk' => 'SK', 'th' => 'TH', 'tr' => 'TR', 'uk' => 'UK',
            'en' => 'US'
        ];
        
        $countryCode = isset($countryCodeMap[$lang]) ? $countryCodeMap[$lang] : 'default';
        
        if ($countryCode === 'US' || $countryCode === 'VN') {
            $countryCode = 'default';
        }
    }
    
    $defaultResources = json_decode(file_get_contents($langFolder . "/default.json"), true);
    
    $filename = strtolower($countryCode) . '.json';
    if (file_exists($langFolder . "/{$filename}")) {
        $resources = json_decode(file_get_contents($langFolder . "/{$filename}"), true);
    } else {
        $resources = $defaultResources;
    }
    
    for ($i = 1201; $i <= 1245; $i++) {
        $key = "map_decoration_text_episode_name_episode" . $i;
        if (!isset($resources['candycrush.candycrush'][$key])) {
            $resources['candycrush.candycrush'][$key] = $defaultResources['candycrush.candycrush'][$key];
        }
    }
    
    $properties = [
        "ad_video_activated" => "true",
        "cutscene_episode_6" => "bunny",
        "cutscene_episode_5" => "unicorn",
        "cutscene_episode_4" => "yeti",
        "cutscene_episode_3" => "dragon",
        "cutscene_episode_2" => "robot",
        "cutscene_episode_1" => "girl"
    ];
    
    $universeDesc = ["episodeDescriptions" => [], "starAchievementItemLocks" => []];
    $events = [];
    $userProfiles = [];
    
    if (!empty($tokenData['oauth_token'])) {
        $socialData = fetchUserSocialData($tokenData['oauth_token']);
        $userProfiles[] = getUserProfileFromSocialData($pdo, $socialData);
        $friendProfiles = getFriendsProfiles($pdo, $socialData);
        foreach ($friendProfiles as $friendProfile) {
            $userProfiles[] = $friendProfile;
        }
    }
    
    $universesData = getUserUniverses($userId);
    $itemBalance = getCurrentUserItems($pdo, $sessionKey);
    
    $gamestart = [
        "currentUser" => $userData,
        "userUniverse" => [
            "episodes" => $universesData,
            "unlockedItems" => []
        ],
        "adsEnabled" => true,
        "daysSinceInstall" => 0,
        "events" => $events,
        "itemBalance" => $itemBalance,
        "language" => $countryCode,
        "properties" => $properties,
        "resources" => $resources,
        "recipes" => [],
        "availableBoosters" => [],
        "userProfiles" => $userProfiles,
        "universeDescription" => $universeDesc
    ];
    
    return $gamestart;
}

function gameEnd3($json, $pdo, $sessionKey) {
    global $userFolder;
    global $levelFolder;
    
    $eventManager = new KingEventManager();

    $arg0 = $json;

    $data = $json;

    $timeLeftPercent = $data['timeLeftPercent'];
    $checksum = $data['cs'];
    $seed = $data['seed'];
    $levelId = $data['levelId'];
    $episodeId = $data['episodeId'];
    $score = $data['score'];
    $currentTimestamp = time();

    $userData = getCurrentUser($pdo, $sessionKey);
    $tokenData = getUserTokens();
    $accessToken = $tokenData['oauth_token'];
    $userId = getUserIdBySessionKey($pdo, $sessionKey);
    $universesData = getUserUniverses($userId);
    $topList = getLevelTopList($episodeId, $levelId, $pdo, $accessToken, $userId);

    $secretKey = 'BuFu6gBFv79BH9hk';
    $toHash = "{$episodeId}:{$levelId}:{$score}:{$timeLeftPercent}:{$userId}:{$seed}:{$secretKey}";
    $md5Hash = md5($toHash);
    $calculatedChecksum = substr($md5Hash, 0, 6);
    if ($calculatedChecksum !== $checksum) {
        http_response_code(403);
        return;
    }

    $levelFile = $levelFolder . "/episode" . $episodeId . "level" . $levelId . ".txt";
    if (!file_exists($levelFile)) {
        http_response_code(404);
        return;
    }

    $levelData = json_decode(file_get_contents($levelFile), true);
    if (!$levelData || !isset($levelData['scoreTargets'])) {
        http_response_code(500);
        return;
    }

    $scoreTargets = $levelData['scoreTargets'];
    $newStars = 0;
    if ($score >= $scoreTargets[0]) $newStars = 1;
    if ($score >= $scoreTargets[1]) $newStars = 2;
    if ($score >= $scoreTargets[2]) $newStars = 3;

    $newStarLevel = false;
    $bestResult = false;
    $universeFound = false;

    function isLastLevelInEpisode($episodeId, $levelId) {
        if ($episodeId <= 2) return $levelId === 10;
        if ($episodeId >= 1201 && $episodeId <= 1202) return $levelId === 10;
        return $levelId === 15;
    }

    function isSpecialEpisode($episodeId) {
        return $episodeId >= 1201 && $episodeId <= 1245;
    }

    function getNextSpecialEpisode($episodeId) {
        return ($episodeId >= 1201 && $episodeId < 1245) ? $episodeId + 1 : null;
    }

    foreach ($universesData as &$universe) {
        if ($universe['id'] === $episodeId) {
            foreach ($universe['levels'] as &$level) {
                if ($level['id'] === $levelId) {
                    $universeFound = true;
                    
                    if ($level['score'] < $score) {
                        $isFirstCompletion = ($level['score'] == 0 && $level['stars'] == 0);
                        
                        $level['score'] = $score;
                        $level['stars'] = $newStars;
                        $level['completedAt'] = $currentTimestamp;
                        $newStarLevel = true;
                        $bestResult = true;
                        
                        handleLevelCompletion(
                            $userId,
                            $episodeId,
                            $levelId,
                            $score,
                            $newStars,
                            $currentTimestamp,
                            $isFirstCompletion,
                            $userFolder,
                            $eventManager
                        );
                    }
                    break 2;
                }
            }
        }
    }

    if (isset($data['reason']) && $data['reason'] !== 0) {
        $gameEnd = [
            "bestResult" => false,
            "newStarLevel" => false,
            "episodeId" => $episodeId,
            "levelId" => $levelId,
            "score" => 0,
            "stars" => 0,
            "events" => $eventManager->getEvents(),
            "userUniverse" => [
                "episodes" => $universesData,
                "unlockedItems" => []
            ],
            "currentUser" => getCurrentUser($pdo, $sessionKey),
            "levelToplist" => $topList
        ];
    } else {
        KingLifeSystem::KingGameWin($pdo, $userId);
        
        $gameEnd = [
            "bestResult" => $bestResult,
            "newStarLevel" => $newStarLevel,
            "episodeId" => $episodeId,
            "levelId" => $levelId,
            "score" => $score,
            "stars" => $newStars,
            "events" => $eventManager->getEvents(),
            "userUniverse" => [
                "episodes" => $universesData,
                "unlockedItems" => []
            ],
            "currentUser" => $userData,
            "levelToplist" => $topList
        ];
    }

    return $gameEnd;
}

function handleLevelCompletion($userId, $episodeId, $levelId, $score, $newStars, $currentTimestamp, $isFirstCompletion, $userFolder, $eventManager) {
    $episodeDir = $userFolder . "/{$userId}/{$episodeId}";
    if (!is_dir($episodeDir)) {
        mkdir($episodeDir, 0777, true);
    }
    
    if ($isFirstCompletion) {
        $eventManager->addLevelCompletedEvent($episodeId, $levelId);
        
        if (isLastLevelInEpisode($episodeId, $levelId)) {
            $eventManager->addEpisodeCompletedEvent($episodeId);
            
            $nextEpisodeId = null;
            if (isSpecialEpisode($episodeId)) {
                $nextEpisodeId = getNextSpecialEpisode($episodeId);
            } else if ($episodeId <= 189) {
                $nextEpisodeId = $episodeId + 1;
            }
            
            if ($nextEpisodeId !== null) {
                handleNextEpisodeUnlock($userId, $nextEpisodeId, $userFolder, $eventManager);
            }
        }
    }

    saveLevelData($userId, $episodeId, $levelId, $score, $newStars, $currentTimestamp, $userFolder);
    handleNextLevelUnlock($userId, $episodeId, $levelId, $userFolder, $eventManager);
}

function saveLevelData($userId, $episodeId, $levelId, $score, $newStars, $currentTimestamp, $userFolder) {
    $savePath = $userFolder . "/{$userId}/{$episodeId}/{$levelId}.txt";
    $levelToSave = [
        "id" => $levelId,
        "episodeId" => $episodeId,
        "score" => $score,
        "stars" => $newStars,
        "unlocked" => true,
        "completedAt" => $currentTimestamp,
        "unlockConditionDataList" => []
    ];
    
    $saveDir = dirname($savePath);
    if (!is_dir($saveDir)) {
        mkdir($saveDir, 0777, true);
    }
    
    file_put_contents($savePath, json_encode($levelToSave, JSON_PRETTY_PRINT));
}

function handleNextEpisodeUnlock($userId, $nextEpisodeId, $userFolder, $eventManager) {
    $eventManager->addEpisodeUnlockedEvent($nextEpisodeId);
    
    $nextEpisodeDir = $userFolder . "/{$userId}/{$nextEpisodeId}";
    if (!is_dir($nextEpisodeDir)) {
        mkdir($nextEpisodeDir, 0777, true);
    }
    
    $firstLevelPath = $nextEpisodeDir . "/1.txt";
    $firstLevel = [
        "id" => 1,
        "episodeId" => $nextEpisodeId,
        "score" => 0,
        "stars" => 0,
        "unlocked" => true,
        "completedAt" => 0,
        "unlockConditionDataList" => []
    ];
    
    file_put_contents($firstLevelPath, json_encode($firstLevel, JSON_PRETTY_PRINT));
    $eventManager->addLevelUnlockedEvent($nextEpisodeId, 1);
}

function handleNextLevelUnlock($userId, $episodeId, $levelId, $userFolder, $eventManager) {
    $nextLevelId = $levelId + 1;
    $maxLevels = getMaxLevelsForEpisode($episodeId);
    
    if ($nextLevelId <= $maxLevels) {
        $nextLevelPath = $userFolder . "/{$userId}/{$episodeId}/{$nextLevelId}.txt";
        
        $isNextLevelNew = true;
        if (file_exists($nextLevelPath)) {
            $existingNextLevel = json_decode(file_get_contents($nextLevelPath), true);
            if ($existingNextLevel && ($existingNextLevel['unlocked'] === true)) {
                $isNextLevelNew = false;
            }
        }
        
        $nextLevel = [
            "id" => $nextLevelId,
            "episodeId" => $episodeId,
            "score" => 0,
            "stars" => 0,
            "unlocked" => true,
            "completedAt" => 0,
            "unlockConditionDataList" => []
        ];
        
        if (file_exists($nextLevelPath)) {
            $existingNextLevel = json_decode(file_get_contents($nextLevelPath), true);
            if ($existingNextLevel) {
                $nextLevel = array_merge($nextLevel, $existingNextLevel);
                $nextLevel['unlocked'] = true;
            }
        }
        
        file_put_contents($nextLevelPath, json_encode($nextLevel, JSON_PRETTY_PRINT));
        
        if ($isNextLevelNew) {
            $eventManager->addLevelUnlockedEvent($episodeId, $nextLevelId);
        }
    }
}

function gameStart2($level, $episode, $pdo, $sessionKey) {
    global $levelFolder;

    $levelId = $level;
    $episodeId = $episode;
    $sessionKey = $sessionKey;

    $levelFile = $levelFolder . "/episode" . $episodeId . "level" . $levelId . ".txt";

    if (!file_exists($levelFile)) {
        header('Content-Type: application/json');
        echo json_encode(["error" => "Level file not found"]);
        exit;
    }

    $levelData = file_get_contents($levelFile);

    $seed = mt_rand(100000, 999999);

    $userData = getCurrentUser($pdo, $sessionKey);
    $userId = getUserIdBySessionKey($pdo, $sessionKey);

    KingLifeSystem::KingGameOver($pdo, $userId);

    $gamestart = [
        "levelData" => $levelData,
        "seed" => $seed,
        "recommendedSeeds" => [],
        "currentUser" => $userData
    ];

    return $gamestart;
}