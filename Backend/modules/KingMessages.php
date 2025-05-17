<?php
/*
* SA - King CCS Messages API
* Easily creates notification messages (aka events)
* Usage: 
* SendMessage($pdo, $sessionKey, $recipientIds, 'XXX_XXX', ['XXX' => $XXX, 'XXX' => $XXX]);
*/

function SendMessage($pdo, $sessionKey, $recipientIds, $messageType, $extraData = []) {
    $fromId = getUserIdBySessionKey($pdo, $sessionKey);
    $accessToken = getCurrentUser($pdo, $sessionKey)['oauth_token'];
    $friendIds = array_column(getSocialFriendsInfo($pdo, $accessToken), 'userId');
    $recipients = array_filter(array_map('trim', explode(',', $recipientIds)));
    
    $validRecipients = array_intersect($recipients, $friendIds);
    
    if (empty($validRecipients)) return [];
    
    $placeholders = implode(',', array_fill(0, count($validRecipients), '?'));
    $params = array_merge($validRecipients, [$fromId]);
    
    $pdo->prepare("DELETE FROM messages 
                  WHERE user_id IN ($placeholders) 
                  AND type = ? 
                  AND JSON_EXTRACT(data, '$.fromId') = ?
                  AND processed = 0")->execute(array_merge($params, [$messageType]));
    
    $messageData = array_merge(['fromId' => $fromId], $extraData);
    $encodedData = json_encode($messageData);
    
    $stmt = $pdo->prepare("INSERT INTO messages (user_id, type, data) VALUES (?, ?, ?)");
    
    foreach ($validRecipients as $recipientId) {
        $stmt->execute([$recipientId, $messageType, $encodedData]);
    }
    
    return [];
}


/*function GiveLife($pdo, $sessionKey, $recipientIds) {
    $fromId = getUserIdBySessionKey($pdo, $sessionKey);
    $accessToken = getCurrentUser($pdo, $sessionKey)['oauth_token'];
    $friendIds = array_column(getSocialFriendsInfo($pdo, $accessToken), 'userId');
    $recipients = array_filter(array_map('trim', explode(',', $recipientIds)));
    
    $validRecipients = array_intersect($recipients, $friendIds);
    
    if (empty($validRecipients)) return [];
    
    $placeholders = implode(',', array_fill(0, count($validRecipients), '?'));
    $params = array_merge($validRecipients, [$fromId]);
    
    $pdo->exec("DELETE FROM messages 
                WHERE user_id IN ($placeholders) 
                AND type = 'LIFE_GIFT' 
                AND JSON_EXTRACT(data, '$.fromId') = ?
                AND processed = 0");
                
    $msgData = json_encode(['fromId' => $fromId]);
    
    $stmt = $pdo->prepare("INSERT INTO messages (user_id, type, data) VALUES (?, 'LIFE_GIFT', ?)");
    
    foreach ($validRecipients as $recipientId) {
        $stmt->execute([$recipientId, $msgData]);
    }
    
    return [];
}

function RequestUnlock($pdo, $sessionKey, $recipientIds, $episodeId, $levelId) {
    $fromId = getUserIdBySessionKey($pdo, $sessionKey);
    $accessToken = getCurrentUser($pdo, $sessionKey)['oauth_token'];
    $friendIds = array_column(getSocialFriendsInfo($pdo, $accessToken), 'userId');
    $recipients = array_filter(array_map('trim', explode(',', $recipientIds)));
    
    $validRecipients = array_intersect($recipients, $friendIds);
    
    if (empty($validRecipients)) return [];
    
    $placeholders = implode(',', array_fill(0, count($validRecipients), '?'));
    $params = array_merge($validRecipients, [$fromId]);
    
    $pdo->exec("DELETE FROM messages 
                WHERE user_id IN ($placeholders) 
                AND type = 'LEVEL_UNLOCK_REQUEST' 
                AND JSON_EXTRACT(data, '$.fromId') = ?
                AND processed = 0");
                
    $msgData = json_encode([
        'fromId' => $fromId,
        'episodeId' => $episodeId,
        'levelId' => $levelId
    ]);
    
    $stmt = $pdo->prepare("INSERT INTO messages (user_id, type, data) VALUES (?, 'LEVEL_UNLOCK_REQUEST', ?)");
    
    foreach ($validRecipients as $recipientId) {
        $stmt->execute([$recipientId, $msgData]);
    }
    
    return [];
}*/

function processSingleMessage($pdo, $userId) {
    $stmt = $pdo->prepare("SELECT id, type, data FROM messages WHERE user_id = ? AND processed = 0 LIMIT 1");
    $stmt->execute([$userId]);
    $message = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$message) {
        return null;
    }

    $eventManager = new KingEventManager();
    $type = $message['type'];
    $data = json_decode($message['data'], true);

    $eventManager->addMessageEvent($type, $data);

    $updateStmt = $pdo->prepare("UPDATE messages SET processed = 1 WHERE id = ?");
    $updateStmt->execute([$message['id']]);

    return $eventManager->getEvents()[0] ?? null;
}

function getUserMessages($pdo, $userId, $markProcessed = false) {
    $eventManager = new KingEventManager();

    $stmt = $pdo->prepare("SELECT id, type, data FROM messages WHERE user_id = ? AND processed = 0");
    $stmt->execute([$userId]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $messageIds = [];

    foreach ($messages as $msg) {
        $type = $msg['type'];
        $data = json_decode($msg['data'], true);

        $eventManager->addMessageEvent($type, $data);
        $messageIds[] = $msg['id'];
    }

    if ($markProcessed && !empty($messageIds)) {
        $placeholders = implode(',', array_fill(0, count($messageIds), '?'));
        $stmt = $pdo->prepare("UPDATE messages SET processed = 1 WHERE id IN ($placeholders)");
        $stmt->execute($messageIds);
    }

    return $eventManager->getEvents();
}

?>