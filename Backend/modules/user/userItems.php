<?php

/////////////////////////////////
// Global Item Service
/////////////////////////////////
function handOutItemWinnings($pdo, $inputJson, $sessionKey) {
    $userId = getUserIdBySessionKey($pdo, $sessionKey);
    if ($userId === null) {
        http_response_code(403);
        exit;
    }

    $userItems = getUserItems($pdo, $userId);
    if (empty($userItems)) {
        $userItems = [];
    }

    $mapping = json_decode(file_get_contents('/var/www/candycrush/Backend/data/boosterIdentifiers.json'), true);

    if (!empty($inputJson)) {
        $inputArray = json_decode($inputJson, true);
        $validItems = true;

        foreach ($inputArray as $item) {
            $type = $item['type'];
            if (!isset($mapping[$type])) {
                $validItems = false;
                break;
            }
        }

        if ($validItems) {
            foreach ($inputArray as $item) {
                $type = $item['type'];
                $amount = $item['amount'];
                $typeId = (int)$mapping[$type];
                $category = (stripos($type, 'charm') !== false) ? "candyCharm" :
                           (stripos($type, 'currency') !== false ? "candyCurrency" : "candyBooster");
                $availability = ($category === "candyCharm") ? 1 : 2;

                $newItem = [
                    "typeId" => $typeId,
                    "type" => $type,
                    "category" => $category,
                    "amount" => $amount,
                    "availability" => $availability,
                    "leaseStatus" => 0
                ];

                $existingIndex = array_search($type, array_column($userItems, 'type'));
                if ($existingIndex !== false) {
                    $userItems[$existingIndex]['amount'] += $amount;
                } else {
                    $userItems[] = $newItem;
                }
            }
            saveUserItems($pdo, $userId, $userItems);
        }
    }

    header('Content-Type: application/json');
    echo json_encode($userItems);
}

function unlockItem($pdo, $type, $sessionKey) {
    $userId = getUserIdBySessionKey($pdo, $sessionKey);
    if ($userId === null) {
        http_response_code(403);
        exit;
    }

    $userItems = getUserItems($pdo, $userId);
    if (empty($userItems)) {
        $userItems = [];
    }

    $mapping = json_decode(file_get_contents('/var/www/candycrush/Backend/data/boosterIdentifiers.json'), true);

    if (isset($mapping[$type])) {
        $typeId = (int)$mapping[$type];
        $category = (stripos($type, 'charm') !== false) ? "candyCharm" :
                   (stripos($type, 'currency') !== false ? "candyCurrency" : "candyBooster");
        $availability = ($category === "candyCharm") ? 1 : 2;
        $newItem = [
            "typeId" => $typeId,
            "type" => $type,
            "category" => $category,
            "availability" => $availability,
            "leaseStatus" => 0,
            "amount" => 0
        ];

        $existingIndex = array_search($type, array_column($userItems, 'type'));
        if ($existingIndex === false) {
            $userItems[] = $newItem;
        }

        saveUserItems($pdo, $userId, $userItems);
        header('Content-Type: application/json');
        echo json_encode($userItems);
    } else {
        http_response_code(400);
        exit;
    }
}

function useUserItems($pdo, $inputJson, $sessionKey) {
    $userId = getUserIdBySessionKey($pdo, $sessionKey);
    if ($userId === null) {
        http_response_code(403);
        exit;
    }

    $userItems = getUserItems($pdo, $userId);
    $mapping = json_decode(file_get_contents('/var/www/candycrush/Backend/data/boosterIdentifiers.json'), true);

    if (!empty($inputJson)) {
        $inputArray = json_decode($inputJson, true);
        $validItems = true;

        foreach ($inputArray as $item) {
            $type = $item['type'];
            if (!isset($mapping[$type])) {
                $validItems = false;
                break;
            }
        }

        if ($validItems) {
            foreach ($inputArray as $item) {
                $type = $item['type'];
                $amountToUse = $item['amount'];

                $existingIndex = array_search($type, array_column($userItems, 'type'));
                if ($existingIndex !== false) {
                    $userItems[$existingIndex]['amount'] -= $amountToUse;
                    if ($userItems[$existingIndex]['amount'] < 0) {
                        $userItems[$existingIndex]['amount'] = 0;
                    }
                }
            }

            saveUserItems($pdo, $userId, $userItems);
        }
    }

    header('Content-Type: application/json');
    echo json_encode($userItems);

    if (method_exists($pdo, 'close')) {
        $pdo->close();
    }
}

function getCurrentUserItems($pdo, $sessionKey) {
    $userId = getUserIdBySessionKey($pdo, $sessionKey);
    if ($userId === null) {
        return ['status' => 403, 'data' => null];
    }

    $userItems = getUserItems($pdo, $userId);
    if (empty($userItems)) {
        $userItems = [];
    }

    return $userItems;
}

function giveHardCurrency($pdo, $sessionKey, $amount) {
    $userId = getUserIdBySessionKey($pdo, $sessionKey);
    if ($userId === null) {
        http_response_code(403);
        exit;
    }
    
    $stmt = $pdo->prepare("UPDATE users SET gold = gold + :amount WHERE id = :userId");
    $stmt->execute(['amount' => $amount, 'userId' => $userId]);
    
    $userItems = getUserItems($pdo, $userId);
    if (empty($userItems)) {
        $userItems = [];
    }
    
    $newItem = [
        "typeId" => 3280,
        "type" => "CandyHardCurrency",
        "category" => "candyCurrency",
        "amount" => $amount,
        "availability" => 0,
        "leaseStatus" => 0
    ];
    
    $existingIndex = array_search("CandyHardCurrency", array_column($userItems, 'type'));
    if ($existingIndex !== false) {
        $userItems[$existingIndex]['amount'] += $amount;
    } else {
        $userItems[] = $newItem;
    }
    
    saveUserItems($pdo, $userId, $userItems);
}

function subtractHardCurrency($pdo, $sessionKey, $amount) {
    $userId = getUserIdBySessionKey($pdo, $sessionKey);
    if ($userId === null) {
        http_response_code(403);
        exit;
    }
    
    $stmt = $pdo->prepare("SELECT gold FROM users WHERE kingSessionKey = :sessionKey OR facebookSessionKey = :sessionKey");
    $stmt->execute(['sessionKey' => $sessionKey]);
    $currentGold = $stmt->fetchColumn();
    
    if ($currentGold < $amount) {
        http_response_code(400);
        exit;
    }
    
    $stmt = $pdo->prepare("UPDATE users SET gold = gold - :amount WHERE kingSessionKey = :sessionKey OR facebookSessionKey = :sessionKey");
    $stmt->execute(['amount' => $amount, 'sessionKey' => $sessionKey]);
    
    $userItems = getUserItems($pdo, $userId);
    if (empty($userItems)) {
        $userItems = [];
    }
    
    $existingIndex = array_search("CandyHardCurrency", array_column($userItems, 'type'));
    if ($existingIndex !== false) {
        if ($userItems[$existingIndex]['amount'] >= $amount) {
            $userItems[$existingIndex]['amount'] -= $amount;
        } else {
            $userItems[$existingIndex]['amount'] = 0;
        }
    }
    
    saveUserItems($pdo, $userId, $userItems);
}

function getBalance($sessionKey, $pdo) {
    $userId = getUserIdBySessionKey($pdo, $sessionKey);
    if ($userId === null) {
        http_response_code(403);
        return null;
    }

    $userItems = getUserItems($pdo, $userId);
    $softCurrency = 0;
    foreach ($userItems as $item) {
        if ($item['type'] === 'CandyHardCurrency') {
            $hardCurrency = $item['amount'];
            break;
        }
    }

    return [
        'softCurrency' => $softCurrency, 
        'hardCurrency' => $hardCurrency
    ];
}

function getBalanceWithDeltas($sessionKey, $pdo) {
    $userId = getUserIdBySessionKey($pdo, $sessionKey);
    if ($userId === null) {
        http_response_code(403);
        return null;
    }

    $userItems = getUserItems($pdo, $userId);
    $softCurrency = 0;
    $hardCurrency = 0;

    foreach ($userItems as $item) {
        if ($item['type'] === 'CandyHardCurrency') {
            $hardCurrency = $item['amount'];
        } elseif ($item['type'] === 'CandySoftCurrency') {
            $softCurrency = $item['amount'];
        }
    }

    return [
        'balances' => [
            [
                'currency' => 'KHC',
                'balance' => $hardCurrency
            ],
            [
                'currency' => 'KSC',
                'balance' => $softCurrency
            ]
        ]
    ];
}
/////////////////////////////////
// Global Item Service
/////////////////////////////////


/////////////////////////////////
// Vanity Item Service
/////////////////////////////////
function updateVanityItem($pdo, $type, $id, $timeLeftSec, $sessionKey) {
    $userId = getUserIdBySessionKey($pdo, $sessionKey);
    if (!$userId) return false;
    
    $stmt = $pdo->prepare("UPDATE user_items SET vanity_items = JSON_SET(
        COALESCE(vanity_items, '[]'),
        '$[0]',
        JSON_OBJECT(
            'type', ?,
            'id', ?,
            'timeLeftSec', ?,
            'show', true,
            'itemText', ''
        )
    ) WHERE uid = ?");
    
    return $stmt->execute([$type, $id, $timeLeftSec, $userId]);
}

function getAllUserVanityItems($pdo, $sessionKey) {

    $userData = getCurrentUser($pdo, $sessionKey);
    $tokenData = getUserTokens();
    if (!$userData) return [];
    
    $accessToken = $tokenData['oauth_token'];
    if (!$accessToken) return [];

    $friendsInfo = getSocialFriendsInfo($pdo, $accessToken);
    if (!$friendsInfo) return [];

    $friendIds = array_map(function($friend) {
        return $friend['userId'];
    }, $friendsInfo);
    
    $friendIds[] = $userData['userId'];

    $placeholders = str_repeat('?,', count($friendIds) - 1) . '?';
    
    $stmt = $pdo->prepare("
        SELECT uid, vanity_items 
        FROM user_items 
        WHERE vanity_items IS NOT NULL 
        AND vanity_items != '[]'
        AND uid IN ($placeholders)
    ");
    
    $stmt->execute($friendIds);
    
    $results = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $userId = (int)$row['uid'];
        $vanityItems = json_decode($row['vanity_items'], true);
        
        $results[] = [
            'coreUserId' => $userId,
            'vanityItems' => $vanityItems
        ];
    }
    
    return $results;
}
/////////////////////////////////
// Vanity Item Service
/////////////////////////////////


/////////////////////////////////
// Booster Wheel Service
/////////////////////////////////
function BoosterWheel($pdo, $sessionKey) {
    if (empty($sessionKey)) {
        return [
            'success' => false,
            'status' => 400,
            'prize' => null
        ];
    }
    $userId = getUserIdBySessionKey($pdo, $sessionKey);
    if ($userId === null) {
        return [
            'success' => false,
            'status' => 403,
            'prize' => null
        ];
    }
    $stmt = $pdo->prepare("SELECT last_spin_time FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $lastSpin = $stmt->fetchColumn();
    
    if ($lastSpin && (new DateTime($lastSpin))->diff(new DateTime())->days < 1) {
        return [
            'success' => true,
            'status' => 200,
            'prize' => null
        ];
    }
    $prizes = [
        "CandyHammer",
        "CandyColorBomb",
        "CandySwedishFish",
        "CandyStripedWrapped",
        "CandyWheelOfBoosterJackpot",
        "CandyJoker",
        "CandyCoconutLiquorice",
        "CandyFreeSwitch",
        "CandyVanityItemHat"
    ];
    $hasVanityItems = hasVanityItems($pdo, $sessionKey);
    if ($hasVanityItems) {
        $prizes = array_filter($prizes, function($prize) {
            return $prize !== "CandyVanityItemHat";
        });
    } else {
        $prizes = array_filter($prizes, function($prize) {
            return $prize !== "CandyFreeSwitch";
        });
    }
    $prizes = array_values($prizes);
    $prize = $prizes[array_rand($prizes)];
    $userItems = getUserItems($pdo, $userId) ?: [];
    $mapping = json_decode(file_get_contents('/var/www/candycrush/Backend/data/boosterIdentifiers.json'), true);
    if ($prize === "CandyVanityItemHat") {
        $pdo->prepare("UPDATE users SET last_spin_time = NOW() WHERE id = ?")->execute([$userId]);
        
        return [
            'success' => true,
            'status' => 200,
            'prize' => $prize
        ];
    } elseif ($prize === "CandyWheelOfBoosterJackpot") {
        foreach ($prizes as $booster) {
            if ($booster !== "CandyWheelOfBoosterJackpot") {
                $existingIndex = array_search($booster, array_column($userItems, 'type'));
                if ($existingIndex !== false) {
                    $userItems[$existingIndex]['amount'] += 3;
                } else {
                    $userItems[] = [
                        "typeId" => (int)$mapping[$booster],
                        "type" => $booster,
                        "category" => "candyBooster",
                        "amount" => 3,
                        "availability" => 2,
                        "leaseStatus" => 0
                    ];
                }
            }
        }
    } else {
        $existingIndex = array_search($prize, array_column($userItems, 'type'));
        if ($existingIndex !== false) {
            $userItems[$existingIndex]['amount']++;
        } else {
            $userItems[] = [
                "typeId" => (int)$mapping[$prize],
                "type" => $prize,
                "category" => "candyBooster",
                "amount" => 1,
                "availability" => 2,
                "leaseStatus" => 0
            ];
        }
    }
    $pdo->prepare("UPDATE users SET last_spin_time = NOW() WHERE id = ?")->execute([$userId]);
    saveUserItems($pdo, $userId, $userItems);
    return [
        'success' => true,
        'status' => 200,
        'prize' => $prize
    ];
}

function PaidBoosterWheel($pdo, $sessionKey) {
    $userId = getUserIdBySessionKey($pdo, $sessionKey);
    
    $stmt = $pdo->prepare("SELECT id FROM transactions 
                           WHERE user_id = ? 
                           AND product_id IN ('17525', '17520') 
                           AND status = 'completed' 
                           AND (metadata NOT LIKE '%wheel_consumed%' OR metadata IS NULL)
                           ORDER BY created_at DESC LIMIT 1");
    $stmt->execute([$userId]);
    $transaction = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$transaction) {
        return [
            'success' => false,
            'status' => 200,
            'error' => null
        ];
    }
    
    $transactionId = $transaction['id'];
    $stmt = $pdo->prepare("UPDATE transactions SET metadata = CONCAT(IFNULL(metadata, ''), 'wheel_consumed') WHERE id = ?");
    $stmt->execute([$transactionId]);
    
    $jackpotConfig = [
        "base_chance" => 0.40,
        "streak_1_multiplier" => 0.375,
        "streak_2_multiplier" => 0.33333,
        "reward_streak_1" => 10,
        "reward_streak_2" => 100,
        "reward_streak_3" => 500
    ];
    
    $stmt = $pdo->prepare("SELECT wheel_spin_streak FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $spinStreak = (int)$stmt->fetchColumn();
    
    $prizes = [
        "CandyHammer",
        "CandyColorBomb",
        "CandySwedishFish",
        "CandyStripedWrapped",
        "CandyJoker",
        "CandyCoconutLiquorice",
        "CandyFreeSwitch"
    ];
    
    $starChance = $jackpotConfig['base_chance'];
    if ($spinStreak == 1) {
        $starChance = $jackpotConfig['base_chance'] * $jackpotConfig['streak_1_multiplier'];
    } elseif ($spinStreak == 2) {
        $starChance = $jackpotConfig['base_chance'] * $jackpotConfig['streak_1_multiplier'] * $jackpotConfig['streak_2_multiplier'];
    }
    
    $gotStar = (mt_rand(1, 1000) / 1000) <= $starChance;
    $prize = $prizes[array_rand($prizes)];
    $userItems = getUserItems($pdo, $userId) ?: [];
    
    $mapping = json_decode(file_get_contents('/var/www/candycrush/Backend/data/boosterIdentifiers.json'), true);
    
    $reward = 0;
    $prizeForDisplay = $prize;
    
    if ($gotStar) {
        $spinStreak++;
        if ($spinStreak == 1) {
            $reward = $jackpotConfig['reward_streak_1'];
            $prizeForDisplay = "CandyWheelOfBoosterJackpotHardCurrencyX" . $reward;
            giveHardCurrency($pdo, $sessionKey, $reward);
            $userItems = getUserItems($pdo, $userId) ?: [];
        } 
        elseif ($spinStreak == 2) {
            $reward = $jackpotConfig['reward_streak_2'];
            $prizeForDisplay = "CandyWheelOfBoosterJackpotHardCurrencyX" . $reward;
            giveHardCurrency($pdo, $sessionKey, $reward);
            $userItems = getUserItems($pdo, $userId) ?: [];
        }
        elseif ($spinStreak == 3) {
            $reward = $jackpotConfig['reward_streak_3'];
            $prizeForDisplay = "CandyWheelOfBoosterJackpotHardCurrencyX" . $reward;
            giveHardCurrency($pdo, $sessionKey, $reward);
            $userItems = getUserItems($pdo, $userId) ?: [];
        }
        elseif ($spinStreak > 3) {
            $prizeForDisplay = "CandyWheelOfBoosterJackpot";
            
            foreach ($prizes as $booster) {
                $typeId = $mapping[$booster];
                $existingIndex = array_search($booster, array_column($userItems, 'type'));
                
                if ($booster === "CandyColorBomb") {
                    $typeId = 17004;
                    if ($existingIndex !== false) {
                        $userItems[$existingIndex]['amount'] += 2;
                    } else {
                        $userItems[] = [
                            "typeId" => $typeId,
                            "type" => $booster,
                            "category" => "candyBooster",
                            "amount" => 2,
                            "availability" => 2,
                            "leaseStatus" => 0
                        ];
                    }
                } else {
                    if ($existingIndex !== false) {
                        $userItems[$existingIndex]['amount']++;
                    } else {
                        $userItems[] = [
                            "typeId" => $typeId,
                            "type" => $booster,
                            "category" => "candyBooster",
                            "amount" => 1,
                            "availability" => 2,
                            "leaseStatus" => 0
                        ];
                    }
                }
            }
            saveUserItems($pdo, $userId, $userItems);
        }
    } else {
        $spinStreak = 0;
        
        if ($prize === "CandyColorBomb") {
            $prizeForDisplay = "CandyColorBombX2";
            $typeId = 17004;
            $existingIndex = array_search($prize, array_column($userItems, 'type'));
            
            if ($existingIndex !== false) {
                $userItems[$existingIndex]['amount'] += 2;
            } else {
                $userItems[] = [
                    "typeId" => $typeId,
                    "type" => $prize,
                    "category" => "candyBooster",
                    "amount" => 2,
                    "availability" => 2,
                    "leaseStatus" => 0
                ];
            }
        } else {
            $typeId = $mapping[$prize];
            $existingIndex = array_search($prize, array_column($userItems, 'type'));
            
            if ($existingIndex !== false) {
                $userItems[$existingIndex]['amount']++;
            } else {
                $userItems[] = [
                    "typeId" => $typeId,
                    "type" => $prize,
                    "category" => "candyBooster",
                    "amount" => 1,
                    "availability" => 2,
                    "leaseStatus" => 0
                ];
            }
        }
        saveUserItems($pdo, $userId, $userItems);
    }
    
    $stmt = $pdo->prepare("UPDATE users SET wheel_spin_streak = ? WHERE id = ?");
    $stmt->execute([$spinStreak, $userId]);
    
    return [
        'success' => true,
        'status' => 200,
        'prize' => $prizeForDisplay,
        'reward' => $reward,
        'transactionId' => $transactionId
    ];
}
/////////////////////////////////
// Booster Wheel Service
/////////////////////////////////


/////////////////////////////////
// Extra Content
/////////////////////////////////
function synergieBonus($pdo, $sessionKey) {
    $userId = getUserIdBySessionKey($pdo, $sessionKey);
    if ($userId === null) {
        http_response_code(403);
        exit;
    }

    $stmt = $pdo->prepare("SELECT deliveredSB FROM users WHERE id = :userId");
    $stmt->execute(['userId' => $userId]);
    $delivered = (int)$stmt->fetchColumn();

    if ($delivered === 1) {
        return [];
    }

    giveHardCurrency($pdo, $sessionKey, 10);

    $items = [
        ['type' => 'CandySwedishFish', 'amount' => 1],
        ['type' => 'CandyHammer', 'amount' => 1],
        ['type' => 'CandyStripedWrapped', 'amount' => 1],
        ['type' => 'CandyColorBomb', 'amount' => 1]
    ];

    $inputJson = json_encode($items);
    handOutItemWinnings($pdo, $inputJson, $sessionKey);

    $stmt = $pdo->prepare("UPDATE users SET deliveredSB = 1 WHERE id = :userId");
    $stmt->execute(['userId' => $userId]);

    return [];
}
/////////////////////////////////
// Extra Content
/////////////////////////////////


?>
