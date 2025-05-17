<?php
include 'userLife.php';
function getCurrentUser($pdo, $sessionKey) {
    $stmt = $pdo->prepare("
        SELECT 
            id as userId,
            lives,
            timeToNextRegeneration,
            gold,
            soundFx,
            soundMusic,
            maxLives,
            immortal, 
            CASE 
                WHEN deviceId IS NOT NULL AND deviceId != '' THEN 1 
                ELSE 0 
            END as mobileConnected,
            currency,
            NULL as altCurrency,
            NULL as preAuth,
            oauth_token,
            kingSessionKey,
            facebookSessionKey
        FROM users 
        WHERE kingSessionKey = ? OR facebookSessionKey = ?
    ");
    
    $stmt->execute([$sessionKey, $sessionKey]);
    $userData = $stmt->fetch(PDO::FETCH_ASSOC);
   
    if (!$userData) {
        return null;
    }
    
    KingLifeSystem::KingLife($pdo, $userData);
    
    $userId = $userData['userId'];
    $userItems = getUserItems($pdo, $userId);

    $goldAmount = getGoldFromItems($userItems);
    if ($goldAmount > 0) {
        calibrateUserGold($pdo, $userId, $goldAmount);   
    }
    
    $removeIds = [17520, 17525];
    $userItems = array_filter($userItems, function($item) use ($removeIds) {
        return !in_array($item['typeId'], $removeIds);
    });
    $userItems = array_values($userItems);
    
    saveUserItems($pdo, $userId, $userItems);
    
    $boosterInventory = [];
    $unlockedBoosters = [];
    
    foreach ($userItems as $item) {
        if ($item['category'] === 'candyBooster') {
            $boosterInventory[$item['typeId']] = $item['amount'];
            $unlockedBoosters[] = (int) $item['typeId'];
        }
    }
    
    $userData['boosterInventory'] = $boosterInventory;
    $userData['unlockedBoosters'] = array_values(array_unique($unlockedBoosters));
    $userData['altCurrency'] = $userData['altCurrency'] ?? "USD";
    $userData['preAuth'] = $userData['preAuth'] ?? false;
    $userData['mobileConnected'] = (bool)$userData['mobileConnected'];
    
    $output = $userData;
    
    global $userTokens;
    $userTokens = [
        'oauth_token' => $userData['oauth_token'],
        'kingSessionKey' => $userData['kingSessionKey'],
        'facebookSessionKey' => $userData['facebookSessionKey']
    ];
    
    unset($output['oauth_token']);
    unset($output['kingSessionKey']);
    unset($output['facebookSessionKey']);
    
    return $output;
}

// errrahvdatgdvastdgfcasrfdcasdr
function getUserTokens() {
    global $userTokens;
    return $userTokens;
}
?>