<?php
// The main database file, there isn't most stuff here because the modules already gives enough food
// simples functions will mostly be here

$host = "";
$user = "root";
$pass = "";
$db = 'candycrush';

try {
  $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  
  // echo "Connected successfully";
} catch (PDOException $e) {
  echo "Connection failed: " . $e->getMessage();
  exit;
}

function getUserIdBySessionKey($pdo, $sessionKey) {
  $g = "SELECT id FROM users WHERE kingSessionKey = :sessionKey OR facebookSessionKey = :sessionKey";
  $stmt = $pdo->prepare($g);
  $stmt->bindValue(':sessionKey', $sessionKey, PDO::PARAM_STR);
  $stmt->execute();
  $userRow = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$userRow) {
      return null;
  }

  return $userRow['id'];
}

function getUserItems($pdo, $userId) {
  $g = "SELECT items FROM user_items WHERE uid = :userId";
  $stmt = $pdo->prepare($g);
  $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
  $stmt->execute();
  $result = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($result && isset($result['items'])) {
      return json_decode($result['items'], true);
  }

  return [];
}

function saveUserItems($pdo, $userId, $items) {
  $itemsJson = json_encode($items);
  $stmt = $pdo->prepare("
      INSERT INTO user_items (uid, items)
      VALUES (:userId, :items)
      ON DUPLICATE KEY UPDATE items = :itemsUpdate
  ");
  $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
  $stmt->bindValue(':items', $itemsJson, PDO::PARAM_STR);
  $stmt->bindValue(':itemsUpdate', $itemsJson, PDO::PARAM_STR);
  $stmt->execute();
}

function getOAuthTokenFromSession($pdo, $sessionKey) {
    $stmt = $pdo->prepare("SELECT oauth_token FROM users WHERE kingSessionKey = :sessionKey OR facebookSessionKey = :sessionKey");
    $stmt->execute(['sessionKey' => $sessionKey]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['oauth_token'] : null;
}

function hasVanityItems($pdo, $sessionKey) {
    $userId = getUserIdBySessionKey($pdo, $sessionKey);
    if (!$userId) {
        return false;
    }
    
    $stmt = $pdo->prepare("
        SELECT 1 
        FROM user_items 
        WHERE uid = ? 
        AND vanity_items IS NOT NULL 
        AND vanity_items != '[]'
        LIMIT 1
    ");
    
    $stmt->execute([$userId]);
    return $stmt->fetch() !== false;
}

function calibrateUserGold($pdo, $userId, $itemsGold) {
    try {
        $stmt = $pdo->prepare("UPDATE users SET gold = :gold WHERE id = :userId");
        return $stmt->execute([
            ':gold' => $itemsGold,
            ':userId' => $userId
        ]);
    } catch (PDOException $e) {
        return false;
    }
}

function getGoldFromItems($itemBalance) {
    if (empty($itemBalance)) {
        return 0;
    }

    $items = is_string($itemBalance) ? json_decode($itemBalance, true) : $itemBalance;
    
    if (!is_array($items)) {
        return 0;
    }

    foreach ($items as $item) {
        if (isset($item['typeId']) && $item['typeId'] === 3280 && 
            isset($item['type']) && $item['type'] === 'CandyHardCurrency') {
            return (int)$item['amount'];
        }
    }
    return 0;
}

?>
