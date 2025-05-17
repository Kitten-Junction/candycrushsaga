<?php

function processPurchase($pdo, $sessionKey, $productPackageType, $recipientUserId) {
    $productPackages = json_decode(file_get_contents('/var/www/candycrush/Backend/data/product_prices.json'), true);
    $boosterMapping = json_decode(file_get_contents('/var/www/candycrush/Backend/data/boosterIdentifiers.json'), true);

    $transactionId = generateTransactionId();
    $isPurchaseForAnotherUser = false;
    
    try {
        $userId = getUserIdBySessionKey($pdo, $sessionKey);

        $package = null;
        foreach ($productPackages as $pkg) {
            if ((string)$pkg['productId'] === (string)$productPackageType) {
                $package = $pkg;
                break;
            }
        }
        
        if ($recipientUserId !== null && $recipientUserId != $userId) {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE id = ?");
            $stmt->execute([$recipientUserId]);
            if (!$stmt->fetch()) {
                throw new Exception("Recipient user not found");
            }
            $isPurchaseForAnotherUser = true;
        } else {
            $recipientUserId = $userId;
        }
        
        createPendingTransaction($pdo, $transactionId, $userId, $productPackageType, $package['price'], $recipientUserId);
        
        $userBalance = getUserHardCurrency($pdo, $userId);
        if ($userBalance < $package['price']) {
            updateTransactionStatus($pdo, $transactionId, 'failed', 'Insufficient hard currency');
            return [
                'status' => 'error',
                'error' => 'Insufficient gold',
                'transactionId' => $transactionId,
                'isPurchaseForAnotherUser' => $isPurchaseForAnotherUser
            ];
        }

        if ((string)$productPackageType === '17001') {
            subtractHardCurrency($pdo, $sessionKey, $package['price']);
            
            $stmt = $pdo->prepare("SELECT maxLives FROM users WHERE id = ?");
            $stmt->execute([$recipientUserId]);
            $userData = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $stmt = $pdo->prepare("UPDATE users SET lives = ?, timeToNextRegeneration = NULL WHERE id = ?");
            $result = $stmt->execute([$userData['maxLives'], $recipientUserId]);
            
            updateTransactionStatus($pdo, $transactionId, 'completed');
            return [
                'status' => 'ok',
                'error' => '',
                'transactionId' => $transactionId,
                'isPurchaseForAnotherUser' => $isPurchaseForAnotherUser
            ];
        }

        subtractHardCurrency($pdo, $sessionKey, $package['price']);

        if (in_array((string)$productPackageType, ['17525', '17520'])) {
            updateTransactionStatus($pdo, $transactionId, 'completed');
            return [
                'status' => 'ok',
                'error' => '',
                'transactionId' => $transactionId,
                'isPurchaseForAnotherUser' => $isPurchaseForAnotherUser
            ];
        }

        $userItems = getUserItems($pdo, $recipientUserId) ?? [];
        
        foreach ($userItems as &$item) {
            $item['typeId'] = (string)$item['typeId'];
        }
        unset($item);

        foreach ($package['items'] as $packageItem) {
            $deliverData = json_decode($packageItem['deliverData'], true);
            $itemId = $packageItem['itemType'];
            $fullItemName = $boosterMapping[$itemId] ?? null;
            
            $packInfo = extractPackInfo($fullItemName);
            $baseItemId = $boosterMapping[$packInfo['name']] ?? null;
            $baseItemId = (string)$baseItemId;
            
            $amount = isset($deliverData['amount']) ? (int)$deliverData['amount'] : $packInfo['amount'];
            
            $category = determineCategory($package['productType']);
            $availability = ($category === 'candyCharm') ? 1 : 2;
            
            $newItem = [
                "typeId" => $baseItemId,
                "type" => $packInfo['name'],
                "category" => $category,
                "amount" => $amount,
                "availability" => $availability,
                "leaseStatus" => 0
            ];
            
            $found = false;
            foreach ($userItems as $key => $existingItem) {
                if ($existingItem['typeId'] === $baseItemId && 
                    $existingItem['type'] === $packInfo['name'] && 
                    $existingItem['category'] === $category) {
                    $userItems[$key]['amount'] += $amount;
                    $found = true;
                    break;
                }
            }
            
            if (!$found) {
                $userItems[] = $newItem;
            }
        }

        $consolidatedItems = [];
        foreach ($userItems as $item) {
            $key = $item['typeId'] . '_' . $item['type'] . '_' . $item['category'];
            if (isset($consolidatedItems[$key])) {
                $consolidatedItems[$key]['amount'] += $item['amount'];
            } else {
                $consolidatedItems[$key] = $item;
            }
        }
        $userItems = array_values($consolidatedItems);
        
        saveUserItems($pdo, $recipientUserId, $userItems);
        
        updateTransactionStatus($pdo, $transactionId, 'completed');
        return [
            'status' => 'ok',
            'error' => '',
            'transactionId' => $transactionId,
            'isPurchaseForAnotherUser' => $isPurchaseForAnotherUser
        ];
    } catch (Exception $e) {
        if (isset($transactionId)) {
            updateTransactionStatus($pdo, $transactionId, 'failed', $e->getMessage());
        }
        
        return [
            'status' => 'error',
            'error' => $e->getMessage(),
            'transactionId' => $transactionId ?? null,
            'isPurchaseForAnotherUser' => $isPurchaseForAnotherUser ?? false
        ];
    }
}

function generateTransactionId() {
    return uniqid('ccm_', true) . '_' . mt_rand(1000, 9999);
}

function createPendingTransaction($pdo, $transactionId, $userId, $productPackageType, $price, $recipientUserId = null) {
    $stmt = $pdo->prepare("INSERT INTO transactions (transaction_id, user_id, product_id, amount, recipient_user_id, status, created_at) 
                          VALUES (?, ?, ?, ?, ?, 'pending', NOW())");
    $stmt->execute([$transactionId, $userId, $productPackageType, $price, $recipientUserId]);
    return $stmt->rowCount() > 0;
}

function updateTransactionStatus($pdo, $transactionId, $status, $error = null) {
    $stmt = $pdo->prepare("UPDATE transactions SET status = ?, error_message = ?, updated_at = NOW() 
                          WHERE transaction_id = ?");
    $stmt->execute([$status, $error, $transactionId]);
    return $stmt->rowCount() > 0;
}

function getUserHardCurrency($pdo, $userId) {
    $stmt = $pdo->prepare("SELECT gold FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ? (int)$result['gold'] : 0;
}

function purchaseFromKing4($pdo, $packageId, $currencyType, $sessionKey) {
    $productPackages = json_decode(file_get_contents('/var/www/candycrush/Backend/data/mobile_pm.json'), true);
    $boosterMapping = json_decode(file_get_contents('/var/www/candycrush/Backend/data/boosterIdentifiers.json'), true);

    try {
        $package = null;
        foreach ($productPackages as $pkg) {
            if ((string)$pkg['productPackageTypeId'] === (string)$packageId) {
                $package = $pkg;
                break;
            }
        }
        
        if (!$package) {
            throw new Exception("Product package not found");
        }

        $userId = getUserIdBySessionKey($pdo, $sessionKey);
        $itemTypeIds = [];

        if ($currencyType === "KHC") {
            $legalprice = CMC($package['hardCurrencyPrice']);
            
            if ((string)$packageId === '17001') {
                subtractHardCurrency($pdo, $sessionKey, $legalprice);
                
                $stmt = $pdo->prepare("SELECT maxLives FROM users WHERE id = ?");
                $stmt->execute([$userId]);
                $userData = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $stmt = $pdo->prepare("UPDATE users SET lives = ?, timeToNextRegeneration = NULL WHERE id = ?");
                $result = $stmt->execute([$userData['maxLives'], $userId]);
                
                return [
                    'productPackageTypeId' => $packageId,
                    'itemTypeIdToDeliver' => ['17001'],
                    'status' => 'ok',
                    'message' => ''
                ];
            }
            
            subtractHardCurrency($pdo, $sessionKey, $legalprice);
            $userItems = getUserItems($pdo, $userId) ?? [];
            
            foreach ($userItems as &$item) {
                $item['typeId'] = (string)$item['typeId'];
            }
            
            foreach ($package['displayProducts'] as $displayProduct) {
                $itemTypeId = (string)$displayProduct['itemTypeId'];
                $itemTypeIds[] = $itemTypeId;
                $fullItemName = $boosterMapping[$itemTypeId] ?? null;
                
                $packInfo = extractPackInfo($fullItemName);
                $baseItemId = $boosterMapping[$packInfo['name']] ?? null;
                $baseItemId = (string)$baseItemId;
                
                $amount = $packInfo['amount'];
                
                $category = determineCategory($package['productType'] ?? 'candyBooster');
                $availability = ($category === 'candyCharm') ? 1 : 2;
                
                $newItem = [
                    "typeId" => $baseItemId,
                    "type" => $packInfo['name'],
                    "category" => $category,
                    "amount" => $amount,
                    "availability" => $availability,
                    "leaseStatus" => 0
                ];
                
                $found = false;
                foreach ($userItems as $key => $existingItem) {
                    if ($existingItem['typeId'] === $baseItemId && 
                        $existingItem['type'] === $packInfo['name'] && 
                        $existingItem['category'] === $category) {
                        $userItems[$key]['amount'] += $amount;
                        $found = true;
                        break;
                    }
                }
                
                if (!$found) {
                    $userItems[] = $newItem;
                }
            }

            $consolidatedItems = [];
            foreach ($userItems as $item) {
                $key = $item['typeId'] . '_' . $item['type'] . '_' . $item['category'];
                if (isset($consolidatedItems[$key])) {
                    $consolidatedItems[$key]['amount'] += $item['amount'];
                } else {
                    $consolidatedItems[$key] = $item;
                }
            }
            $userItems = array_values($consolidatedItems);
            
            saveUserItems($pdo, $userId, $userItems);
        } else {
            throw new Exception("We only accept gold bars here.");
        }
        
        return [
            'productPackageTypeId' => $packageId,
            'itemTypeIdToDeliver' => $itemTypeIds,
            'status' => 'ok',
            'message' => ''
        ];
    } catch (Exception $e) {
        return [
            'productPackageTypeId' => $packageId,
            'itemTypeIdToDeliver' => [],
            'status' => 'error',
            'message' => $e->getMessage()
        ];
    }
}

// Why King, Why.
function CMC($mobileCurrency) {
    return ceil($mobileCurrency / 100);
}

function extractPackInfo($itemName) {
    $packSuffixes = [
        'One' => 1, 'Two' => 2, 'Three' => 3, 'Four' => 4, 'Five' => 5,
        'Six' => 6, 'Seven' => 7, 'Eight' => 8, 'Nine' => 9, 'Ten' => 10,
        'Eleven' => 11, 'Twelve' => 12, 'Thirteen' => 13, 'Fourteen' => 14,
        'Fifteen' => 15, 'Sixteen' => 16, 'Seventeen' => 17, 'Eighteen' => 18
    ];
    
    foreach ($packSuffixes as $word => $number) {
        if (preg_match("/{$word}Pack$/", $itemName)) {
            return [
                'name' => preg_replace("/{$word}Pack$/", '', $itemName),
                'amount' => $number
            ];
        }
    }
    
    return [
        'name' => $itemName,
        'amount' => 1
    ];
}

function determineCategory($productType) {
    if (stripos($productType, 'charm') !== false) return 'candyCharm';
    if (stripos($productType, 'currency') !== false) return 'candyCurrency';
    return 'candyBooster';
}

?>