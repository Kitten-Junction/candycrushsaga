<?php
define('BRONZE', 0);
define('SILVER', 1);
define('GOLD', 2);

class KingSugarTrack {
    private $pdo;
    private $userId;
    private $kingSessionKey;
    private $maxBalance = 600;
    private $nodeCount = 5;
    private $priceTiers = [
        [24, 48, 72],
        [96, 120, 144],
        [180, 216, 264],
        [300, 360, 420],
        [480, 540, 600]
    ];
    
    private $rewardItems = [
        BRONZE => [
            'common' => [
                'CandyStripedWrapped',
                'CandyCoconutLiquorice',
                'CandyColorBomb',
                'CandyFreeSwitch',
                'CandySwedishFish',
                'CandyJoker'
            ],
            'packs' => [1, 2],
            'currency' => [3281, 3282],
            'frequency' => 30
        ],
        SILVER => [
            'common' => [
                'CandyColorBomb',
                'CandyStripedWrapped',
                'CandyFreeSwitch',
                'CandyJoker',
                'CandySwedishFish'
            ],
            'packs' => [1, 2],
            'currency' => [3281, 3282],
            'frequency' => 45
        ],
        GOLD => [
            'common' => [
                'CandyHammer',
                'CandyColorBomb',
                'CandyStripedWrapped',
                'CandyFreeSwitch',
                'CandySwedishFish'
            ],
            'packs' => [1, 2],
            'currency' => [3281, 3282],
            'frequency' => 60
        ]
    ];

    public function __construct($dbId, PDO $pdo) {
        $this->userId = $dbId;
        $this->pdo = $pdo;
        $this->initializeItemMappings();
    }
    
    private function initializeItemMappings() {
        $mapping = json_decode(file_get_contents('/var/www/candycrush/Backend/data/boosterIdentifiers.json'), true);
        $reverseMapping = array_flip($mapping);
        
        foreach ($this->rewardItems as $tier => &$tierItems) {
            $commonItems = [];
            foreach ($tierItems['common'] as $itemName) {
                if (isset($reverseMapping[$itemName])) {
                    $commonItems[] = $reverseMapping[$itemName];
                }
            }
            $tierItems['common'] = $commonItems;
        }
    }

    public function initializeUserRewardData() {
        $nodes = $this->generateRewardNodes();
        
        $nodesJson = json_encode(array_map(function($node) {
            return [
                'cost' => $node['cost'],
                'rewardType' => $node['rewardType'],
                'nodeStatus' => 0,
                'rewardItemTypeIds' => []
            ];
        }, $nodes));
        
        $stmt = $this->pdo->prepare("
            INSERT IGNORE INTO sugartrack 
            (id, balance, cooldown, cooldown_end, sugartrack_nodes) 
            VALUES (:id, 0, 0, 0, :nodes)
        ");
        
        $stmt->execute([
            ':id' => $this->userId,
            ':nodes' => $nodesJson
        ]);
        
        return $nodes;
    }
    
    private function generateRewardNodes() {
        $nodes = [];
        $totalCost = 0;
        $hasGold = false;
        $currentPackSize = 1;
        $maxTotalCost = 450;
        
        for ($i = 0; $i < $this->nodeCount; $i++) {
            $tier = min($i, count($this->priceTiers) - 1);
            $costOptions = $this->priceTiers[$tier];
            
            $weights = [
                BRONZE => max(70 - ($i * 15), 10),
                SILVER => min(60, 30 + ($i * 10)),
                GOLD => $hasGold ? 0 : min(60, 10 + ($i * 15))
            ];
            
            $totalWeight = array_sum($weights);
            $random = rand(1, max(1, $totalWeight));
            $runningTotal = 0;
            $rewardType = BRONZE;
            
            foreach ($weights as $type => $weight) {
                $runningTotal += $weight;
                if ($random <= $runningTotal) {
                    $rewardType = $type;
                    break;
                }
            }
            
            if ($rewardType == GOLD) {
                $hasGold = true;
            }
            
            $costIndex = min($rewardType, count($costOptions) - 1);
            $baseCost = $costOptions[$costIndex];
            
            $varianceFactor = rand(70, 110) / 100;
            $cost = round($baseCost * $varianceFactor);
            
            if ($i == $this->nodeCount - 1) {
                $cost = max(60, $maxTotalCost - $totalCost);
                if (!$hasGold) {
                    $rewardType = GOLD;
                    $hasGold = true;
                }
            } else {
                $totalCost += $cost;
                
                if ($totalCost >= $maxTotalCost) {
                    $totalCost -= $cost;
                    $cost = max(24, $maxTotalCost - $totalCost);
                    $totalCost += $cost;
                }
            }
            
            $nodes[] = [
                'cost' => $cost,
                'rewardType' => $rewardType
            ];
        }
        
        usort($nodes, function($a, $b) {
            return $a['cost'] - $b['cost'];
        });
        
        $this->adjustPackSizes($nodes);
        
        return $nodes;
    }
    
    private function adjustPackSizes(&$nodes) {
        $packsPerType = [
            BRONZE => [1, 2],
            SILVER => [1, 2],
            GOLD => [1, 2]
        ];
        
        $currentPackIndex = [
            BRONZE => 0,
            SILVER => 0,
            GOLD => 0
        ];
        
        foreach ($nodes as &$node) {
            $type = $node['rewardType'];
            $packs = $packsPerType[$type];
            $index = min($currentPackIndex[$type], count($packs) - 1);
            $node['packSize'] = $packs[$index];
            $currentPackIndex[$type]++;
        }
    }

    private function generateRandomRewards($rewardType) {
        $rewards = [];
        $itemCount = rand(3, 8);
        $rewardPool = $this->rewardItems[$rewardType]['common'];
        $packRange = $this->rewardItems[$rewardType]['packs'];
        $currencyPool = $this->rewardItems[$rewardType]['currency'];
        $currencyFrequency = $this->rewardItems[$rewardType]['frequency'];
        
        $currencyCount = 0;
        $maxCurrency = 2;
        
        if ($rewardType >= SILVER) {
            $rewards[] = $currencyPool[array_rand($currencyPool)];
            $currencyCount++;
            $itemCount--;
        }
        
        $packCount = 0;
        $maxPacks = 2;
        
        for ($i = 0; $i < $itemCount; $i++) {
            if ($currencyCount < $maxCurrency && rand(1, 100) <= $currencyFrequency) {
                $rewards[] = $currencyPool[array_rand($currencyPool)];
                $currencyCount++;
                continue;
            }
            
            $itemId = $rewardPool[array_rand($rewardPool)];
            
            if (rand(0, 100) < 40 && !$this->isCurrencyItem($itemId) && $packCount < $maxPacks) {
                $packSize = $packRange[array_rand($packRange)];
                $itemId = $this->convertToPackItem($itemId, $packSize);
                $packCount++;
            }
            
            $rewards[] = $itemId;
        }
        
        $rewards = array_unique($rewards);
        
        while (count($rewards) < 3) {
            $itemId = $rewardPool[array_rand($rewardPool)];
            if (!in_array($itemId, $rewards)) {
                $rewards[] = $itemId;
            }
        }
        
        return array_slice($rewards, 0, 8);
    }
    
    private function convertToPackItem($itemId, $packSize) {
        $mapping = json_decode(file_get_contents('/var/www/candycrush/Backend/data/boosterIdentifiers.json'), true);
        $reverseMapping = array_flip($mapping);
        
        $type = $mapping[$itemId] ?? null;
        if (!$type) return $itemId;
        
        $packWords = [
            1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four', 5 => 'Five',
            6 => 'Six', 7 => 'Seven', 8 => 'Eight', 9 => 'Nine', 10 => 'Ten',
            11 => 'Eleven', 12 => 'Twelve', 13 => 'Fourteen', 15 => 'Fifteen',
            16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen'
        ];
        
        $packType = $type . $packWords[$packSize] . 'Pack';
        return $reverseMapping[$packType] ?? $itemId;
    }

    private function handOutSugarRewards($itemIds) {
        $userItems = getUserItems($this->pdo, $this->userId);
        if (empty($userItems)) {
            $userItems = [];
        }
        
        $mapping = json_decode(file_get_contents('/var/www/candycrush/Backend/data/boosterIdentifiers.json'), true);
        $reverseMapping = array_flip($mapping);
        
        foreach ($itemIds as $itemId) {
            if ($this->isCurrencyItem($itemId)) {
                $amount = $this->getCurrencyAmount($itemId);
                $stmt = $this->pdo->prepare("UPDATE users SET gold = gold + :amount WHERE id = :userId");
                $stmt->execute([
                    ':amount' => $amount,
                    ':userId' => $this->userId
                ]);
                
                $newItem = [
                    "typeId" => 3280,
                    "type" => "CandyHardCurrency",
                    "category" => "candyCurrency",
                    "amount" => $amount,
                    "availability" => 0,
                    "leaseStatus" => 0
                ];
                
                $existingIndex = array_search($newItem['type'], array_column($userItems, 'type'));
                if ($existingIndex !== false) {
                    $userItems[$existingIndex]['amount'] += $amount;
                } else {
                    $userItems[] = $newItem;
                }
            } else {
                $type = $mapping[$itemId] ?? null;
                if ($type) {
                    $amount = 1;
                    if (preg_match('/(One|Two|Three|Four|Five|Six|Seven|Eight|Nine|Ten|Eleven|Twelve|Thirteen|Fourteen|Fifteen|Sixteen|Seventeen|Eighteen)Pack$/', $type, $matches)) {
                        $numberWords = [
                            'One' => 1, 'Two' => 2, 'Three' => 3, 'Four' => 4, 'Five' => 5,
                            'Six' => 6, 'Seven' => 7, 'Eight' => 8, 'Nine' => 9, 'Ten' => 10,
                            'Eleven' => 11, 'Twelve' => 12, 'Thirteen' => 13, 'Fourteen' => 14,
                            'Fifteen' => 15, 'Sixteen' => 16, 'Seventeen' => 17, 'Eighteen' => 18
                        ];
                        $amount = $numberWords[$matches[1]];
                        $type = preg_replace('/(?:One|Two|Three|Four|Five|Six|Seven|Eight|Nine|Ten|Eleven|Twelve|Thirteen|Fourteen|Fifteen|Sixteen|Seventeen|Eighteen)Pack$/', '', $type);
                    }
                    
                    $baseType = $type;
                    $originalId = $reverseMapping[$baseType] ?? $itemId;
                    
                    $category = (stripos($baseType, 'charm') !== false) ? "candyCharm" :
                               (stripos($baseType, 'currency') !== false ? "candyCurrency" : "candyBooster");
                    $availability = ($category === "candyCharm") ? 1 : 2;
                    
                    $newItem = [
                        "typeId" => $originalId,
                        "type" => $baseType,
                        "category" => $category,
                        "amount" => $amount,
                        "availability" => $availability,
                        "leaseStatus" => 0
                    ];
                    
                    $existingIndex = array_search($baseType, array_column($userItems, 'type'));
                    if ($existingIndex !== false) {
                        $userItems[$existingIndex]['amount'] += $amount;
                    } else {
                        $userItems[] = $newItem;
                    }
                }
            }
        }
        
        saveUserItems($this->pdo, $this->userId, $userItems);
    }

    private function isCurrencyItem($itemId) {
        $currencyItems = [
            3281 => 1, 3282 => 2, 3283 => 3, 3284 => 9,
            3285 => 10, 3286 => 12, 3287 => 19, 3288 => 25,
            3289 => 29, 3290 => 39, 3291 => 50, 3292 => 100,
            3293 => 200, 3294 => 500, 3295 => 1000,
            3369 => 70, 3370 => 125, 3371 => 300,
            3372 => 600, 3373 => 800
        ];
        
        return isset($currencyItems[$itemId]);
    }

    private function getCurrencyAmount($itemId) {
        $currencyAmounts = [
            3281 => 1, 3282 => 2, 3283 => 3, 3284 => 9,
            3285 => 10, 3286 => 12, 3287 => 19, 3288 => 25,
            3289 => 29, 3290 => 39, 3291 => 50, 3292 => 100,
            3293 => 200, 3294 => 500, 3295 => 1000,
            3369 => 70, 3370 => 125, 3371 => 300,
            3372 => 600, 3373 => 800
        ];
        
        return $currencyAmounts[$itemId] ?? 0;
    }

    public function addBalance($amount) {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) FROM sugartrack WHERE id = :id
        ");
        $stmt->execute([':id' => $this->userId]);
        
        if ($stmt->fetchColumn() == 0) {
            $this->initializeUserRewardData();
        }

        $stmt = $this->pdo->prepare("
            SELECT cooldown_end, balance, sugartrack_nodes 
            FROM sugartrack 
            WHERE id = :id
        ");
        $stmt->execute([':id' => $this->userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $currentTimeMs = round(microtime(true) * 1000);
        $cooldownEndMs = $result['cooldown_end'] * 1000;

        if ($cooldownEndMs > $currentTimeMs) {
            return [
                'balance' => $result['balance'],
                'cooldownLeft' => $cooldownEndMs - $currentTimeMs,
                'sugarTrackNodes' => json_decode($result['sugartrack_nodes'] ?? '[]', true),
                'version' => 0
            ];
        }

        $newBalance = min($result['balance'] + $amount, $this->maxBalance);

        $stmt = $this->pdo->prepare("
            UPDATE sugartrack 
            SET balance = :balance
            WHERE id = :id
        ");
        $stmt->execute([
            ':balance' => $newBalance,
            ':id' => $this->userId
        ]);

        return $this->getRewardStatus();
    }

    public function getRewardStatus() {
        $stmt = $this->pdo->prepare("
            SELECT balance, cooldown, cooldown_end, sugartrack_nodes 
            FROM sugartrack 
            WHERE id = :id
        ");
        $stmt->execute([':id' => $this->userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $nodes = json_decode($result['sugartrack_nodes'] ?? '[]', true);
        $balance = $result['balance'];
        $nodesUpdated = false;

        foreach ($nodes as $index => &$node) {
            if ($balance >= $node['cost'] && $node['nodeStatus'] == 0) {
                $node['nodeStatus'] = 1;
                $node['rewardItemTypeIds'] = $this->generateRandomRewards($node['rewardType']);
                $this->handOutSugarRewards($node['rewardItemTypeIds']);
                $nodesUpdated = true;
            }
            elseif ($balance >= $node['cost'] && $node['nodeStatus'] == 1) {
                $node['nodeStatus'] = 2;
                $node['rewardItemTypeIds'] = [];
                $nodesUpdated = true;
            }
        }

        if ($nodesUpdated) {
            $stmt = $this->pdo->prepare("
                UPDATE sugartrack 
                SET sugartrack_nodes = :nodes
                WHERE id = :id
            ");
            $stmt->execute([
                ':nodes' => json_encode($nodes),
                ':id' => $this->userId
            ]);
        }

        $allNodesCompleted = array_reduce($nodes, function($carry, $node) {
            return $carry && $node['nodeStatus'] == 2;
        }, true);

        $currentTimeMs = round(microtime(true) * 1000);

        if ($allNodesCompleted) {
            $cooldownEndMs = (time() + 28800) * 1000;
            
            $newNodes = $this->generateRewardNodes();
            $resetNodes = array_map(function($node) {
                return [
                    'cost' => $node['cost'],
                    'rewardType' => $node['rewardType'],
                    'nodeStatus' => 0,
                    'rewardItemTypeIds' => []
                ];
            }, $newNodes);

            $stmt = $this->pdo->prepare("
                UPDATE sugartrack 
                SET balance = 0, 
                    cooldown = :cooldown, 
                    cooldown_end = :cooldown_end,
                    sugartrack_nodes = :nodes
                WHERE id = :id
            ");
            $stmt->execute([
                ':cooldown' => $cooldownEndMs,
                ':cooldown_end' => $cooldownEndMs / 1000,
                ':nodes' => json_encode($resetNodes),
                ':id' => $this->userId
            ]);

            return [
                'balance' => 0,
                'cooldownLeft' => $cooldownEndMs - $currentTimeMs,
                'sugarTrackNodes' => $resetNodes,
                'version' => 2
            ];
        }

        $cooldownEndMs = $result['cooldown_end'] * 1000;
        $cooldownLeft = max(0, $cooldownEndMs - $currentTimeMs);

        return [
            'balance' => $balance,
            'cooldownLeft' => $cooldownLeft,
            'sugarTrackNodes' => $nodes,
            'version' => 2
        ];
    }
}
?>