<?php
function getAllSelectableAvatars() {
    $avatars = [];
    for ($i = 1; $i <= 38; $i++) {
        $avatars[] = [
            "id" => $i,
            "url" => "https://cdn-candycrush.spiritaccount.net/a/a{$i}_100x100.png",
            "urlSquare" => "https://cdn-candycrush.spiritaccount.net/a/a{$i}_50x50.png",
            "urlSmall" => "https://cdn-candycrush.spiritaccount.net/a/a{$i}_50x50.png",
            "urlBig" => "https://cdn-candycrush.spiritaccount.net/a/a{$i}_200x200.png"
        ];
    }
    return [
        "status" => 1,
        "avatars" => $avatars
    ];
}

function setSelectableAvatar($params, $sessionKey, $pdo) {
    $avatarId = intval($params[0]);
    
    if ($avatarId < 1 || $avatarId > 38) {
        return [
            "status" => 0,
            "avatars" => []
        ];
    }
    
    $query = "SELECT id FROM users WHERE kingSessionKey = :sessionKey";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':sessionKey', $sessionKey, PDO::PARAM_STR);
    $stmt->execute();
    
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        return [
            "status" => 0,
            "avatars" => []
        ];
    }
    
    $updateQuery = "UPDATE users SET selectedAvatar = :avatarId WHERE id = :userId";
    $updateStmt = $pdo->prepare($updateQuery);
    $updateStmt->bindParam(':avatarId', $avatarId, PDO::PARAM_INT);
    $updateStmt->bindParam(':userId', $user['id'], PDO::PARAM_INT);
    
    try {
        $updateStmt->execute();
        
        return [
            "status" => 1,
            "avatars" => []
        ];
    } catch (Exception $e) {
        return [
            "status" => 0,
            "avatars" => []
        ];
    }
}

function setFullName($params, $sessionKey, $pdo) {
    $name = $params[0]; 
    
    if (empty($name)) {
        return [
            "status" => "ERR_NAME_MALFORMED",
            "coreUserId" => 0,
            "signInCount" => 0,
            "nameStatus" => -1
        ];
    }
    
    $query = "SELECT id, username, signInCount FROM users WHERE kingSessionKey = :sessionKey";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':sessionKey', $sessionKey, PDO::PARAM_STR);
    $stmt->execute();
    
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        return [
            "status" => "ERR_INVALID_SESSION",
            "coreUserId" => 0,
            "signInCount" => 0,
            "nameStatus" => -1
        ];
    }
    
    $updateQuery = "UPDATE users SET username = :name WHERE id = :userId";
    $updateStmt = $pdo->prepare($updateQuery);
    $updateStmt->bindParam(':name', $name, PDO::PARAM_STR);
    $updateStmt->bindParam(':userId', $user['id'], PDO::PARAM_INT);
    $updateStmt->execute();
    
    return [
        "status" => "OK",
        "coreUserId" => $user['id'],
        "name" => $name,
        "sessionKey" => $sessionKey,
        "signInCount" => $user['signInCount'],
        "nameStatus" => 1
    ];
}

function validateEmailAndPassword($params, $pdo) {
    $email = $params[0];
    $password = $params[1];
    
    $currentSessionKey = $_GET['session'] ?? '';
    
    $query = "SELECT 
                id, 
                password, 
                username, 
                kingSessionKey, 
                signInCount 
              FROM users 
              WHERE email = :email";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();
    
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($password, $user['password'])) {
        return [
            "status" => "OK",
            "coreUserId" => $user['id'],
            "email" => $email,
            "username" => $user['username'],
            "sessionKey" => $user['kingSessionKey'],
            "signInCount" => 0,
            "nameStatus" => -1
        ];
    } else {
        return [
            "status" => "ERR_WRONG_EMAIL_OR_PASSWORD",
            "coreUserId" => 0,
            "email" => $email,
            "signInCount" => 0,
            "nameStatus" => -1
        ];
    }
}

function checkAccountStatus($params, $pdo) {
    $email = $params[1];
    
    $query = "SELECT 
                id,
                selectedAvatar, 
                username AS name, 
                country, 
                UNIX_TIMESTAMP(lastLogin) AS lastSignInTime 
              FROM users 
              WHERE email = :email";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();
    
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    $avatarId = $user['selectedAvatar'];
    
    if ($user) {
        return [
            "status" => "EMAIL_USED",
            "coreUserId" => $user['id'],
            "email" => $email,
            "appSocialUserDto" => [
                "userId" => $user['id'],
                "externalUserId" => (string)$user['id'],
                "name" => $user['name'],
                "firstName" => $user['name'],
                "pic" => "https://cdn-candycrush.spiritaccount.net/a/a{$avatarId}_50x50.png",
                "pic100" => "https://cdn-candycrush.spiritaccount.net/a/a{$avatarId}_100x100.png",
                "country" => $user['country'],
                "lastSignInTime" => $user['lastSignInTime'] ?? time(),
                "friendType" => "NONE",
                "pictureUrls" => []
            ]
        ];
    } else {
        return [
            "status" => "EMAIL_UNUSED",
            "coreUserId" => 0,
            "email" => $email
        ];
    }
}

function sendRetrievePasswordEmail($params, $pdo) {
    $email = $params[0];
    
    $query = "SELECT 
                id
              FROM users 
              WHERE email = :email";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();
    
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
    if ($user) {
        return [
            "status" => "1",
            "coreUserId" => $user['id'],
            "email" => $email,
        ];
    } else {
        return [
            "status" => "0",
            "coreUserId" => 0,
            "email" => $email
        ];
    }
}

function connectUsingKingdom2($params, $pdo) {
    $emailParam = $params[1];
    $password = $params[2];
    $country = $params[3];
    $language = $params[4];
    $deviceId = $params[7];
    
    $email = ($emailParam === '@GENERATE@') ? generate_kingdom_email() : $emailParam;
    
    if ($password) {
        $query = "SELECT id, kingSessionKey, password, username, signInCount, email FROM users WHERE email = :email";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        
        if ($stmt->rowCount() === 0) {
            $result = registerKingdomUser($pdo, $email, $password, $country, $language, $deviceId);
            
            return [
                "status" => "NEW_USER",
                "coreUserId" => $result['user_id'],
                "email" => $email,
                "username" => $result['username'],
                "sessionKey" => $result['session_key'],
                "signInCount" => 1,
                "nameStatus" => 1,
                "warnings" => 0
            ];
        } else {
            $user_data = $stmt->fetch(PDO::FETCH_ASSOC);
            $user_id = $user_data['id'];
            $stored_password = $user_data['password'];
            $session_key = $user_data['kingSessionKey'];
            $username = $user_data['username'];
            $signInCount = $user_data['signInCount'] + 1;
            
            if (!$fbConnected && !password_verify($password, $stored_password)) {
                return [
                    "status" => "ERR_WRONG_PASSWORD",
                    "coreUserId" => 0,
                    "email" => $email,
                    "signInCount" => 0,
                    "nameStatus" => -1,
                    "warnings" => 0
                ];
            }
            
            $updateQuery = "UPDATE users SET 
                          lastLogin = NOW(), 
                          signInCount = :signInCount
                          WHERE id = :user_id";
            
            $updateStmt = $pdo->prepare($updateQuery);
            $updateStmt->bindParam(':signInCount', $signInCount, PDO::PARAM_INT);
            $updateStmt->bindParam(':deviceId', $deviceId, PDO::PARAM_STR);
            $updateStmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $updateStmt->execute();
            
            return [
                "status" => "LOGIN",
                "coreUserId" => $user_id,
                "email" => $email,
                "username" => $username,
                "sessionKey" => $session_key,
                "signInCount" => $signInCount,
                "nameStatus" => 1,
                "warnings" => 0
            ];
        }
    }
    
    return null;
}

function setEmailAndPassword($params, $sessionKey, $pdo) {
    $email = $params[0];
    $password = $params[2];
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return [
            "status" => "ERR_EMAIL_MALFORMED",
            "coreUserId" => 0,
            "signInCount" => 0,
            "nameStatus" => -1
        ];
    }
    
    $checkEmailQuery = "SELECT id FROM users WHERE email = :email";
    $checkStmt = $pdo->prepare($checkEmailQuery);
    $checkStmt->bindParam(':email', $email, PDO::PARAM_STR);
    $checkStmt->execute();
    
    if ($checkStmt->rowCount() > 0) {
        return [
            "status" => "ERR_EMAIL_USED",
            "coreUserId" => 0,
            "signInCount" => 0,
            "nameStatus" => -1
        ];
    }
    
    $query = "SELECT id, kingSessionKey, username, signInCount FROM users WHERE kingSessionKey = :sessionKey";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':sessionKey', $sessionKey, PDO::PARAM_STR);
    $stmt->execute();
    
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        return [
            "status" => "ERR_INVALID_SESSION",
            "coreUserId" => 0,
            "signInCount" => 0,
            "nameStatus" => -1
        ];
    }
    
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    $updateQuery = "UPDATE users SET 
                    email = :email, 
                    password = :password 
                    WHERE id = :userId";
    $updateStmt = $pdo->prepare($updateQuery);
    $updateStmt->bindParam(':email', $email, PDO::PARAM_STR);
    $updateStmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
    $updateStmt->bindParam(':userId', $user['id'], PDO::PARAM_INT);
    
    try {
        $updateStmt->execute();
        
        return [
            "status" => "OK",
            "coreUserId" => $user['id'],
            "email" => $email,
            "username" => $user['username'],
            "sessionKey" => $sessionKey,
            "signInCount" => $user['signInCount'],
            "nameStatus" => -1
        ];
    } catch (Exception $e) {
        return [
            "status" => "ERR_INTERNAL_ERROR",
            "coreUserId" => 0,
            "signInCount" => 0,
            "nameStatus" => -1
        ];
    }
}

function connectUsingFacebook2($params, $pdo) {
    $email = $params[0];
    $password = $params[1];
    $facebookAccessToken = $params[2];
    $country = $params[4];
    $language = $params[5];
    $deviceId = $params[7];  

    $fbUserData = getFacebookUserData($facebookAccessToken);
    
    if (!$fbUserData || !isset($fbUserData['id'])) {
        return [
            "status" => "ERR_INTERNAL_ERROR",
            "coreUserId" => 0,
            "signInCount" => 0,
            "nameStatus" => -1
        ];
    }
    
    $fbUserId = $fbUserData['id'];
    $fbName = isset($fbUserData['name']) ? $fbUserData['name'] : null;
    
    $fbQuery = "SELECT id, password, username, kingSessionKey, signInCount, email FROM users WHERE fbuid = :fbuid";
    $fbStmt = $pdo->prepare($fbQuery);
    $fbStmt->bindParam(':fbuid', $fbUserId, PDO::PARAM_STR);
    $fbStmt->execute();
    
    if ($fbStmt->rowCount() > 0) {
        $existingFbUser = $fbStmt->fetch(PDO::FETCH_ASSOC);
        
        $facebookSessionKey = generate_session_key();
        
        $updateQuery = "UPDATE users SET lastLogin = NOW(), 
                        signInCount = signInCount + 1,
                        oauth_token = :access_token,
                        facebookSessionKey = :facebook_session_key,
                        deviceId = :deviceId
                        WHERE id = :user_id";
        $updateStmt = $pdo->prepare($updateQuery);
        $updateStmt->bindParam(':user_id', $existingFbUser['id'], PDO::PARAM_INT);
        $updateStmt->bindParam(':access_token', $facebookAccessToken, PDO::PARAM_STR);
        $updateStmt->bindParam(':facebook_session_key', $facebookSessionKey, PDO::PARAM_STR);
        $updateStmt->bindParam(':deviceId', $deviceId, PDO::PARAM_STR);
        $updateStmt->execute();
        
        return [ 
            "status" => "LOGIN",
            "coreUserId" => $existingFbUser['id'],
            "email" => $existingFbUser['email'],
            "username" => $existingFbUser['username'],
            "sessionKey" => $facebookSessionKey,
            "signInCount" => $existingFbUser['signInCount'] + 1,
            "nameStatus" => 1,
            "warnings" => 0
        ];
    }
    
    $userQuery = "SELECT id, password, username, kingSessionKey, signInCount, fbuid, oauth_token FROM users WHERE email = :email";
    $userStmt = $pdo->prepare($userQuery);
    $userStmt->bindParam(':email', $email, PDO::PARAM_STR);
    $userStmt->execute();
    
    $existingUser = $userStmt->fetch(PDO::FETCH_ASSOC);
    
    if ($existingUser) {
        if ($password && !password_verify($password, $existingUser['password'])) {
            return [
                "status" => "ERR_WRONG_PASSWORD",
                "coreUserId" => 0,
                "signInCount" => 0,
                "nameStatus" => -1,
                "warnings" => 0
            ];
        }
        
        $facebookSessionKey = generate_session_key();
        
        $updateQuery = "UPDATE users SET 
                        fbuid = :fb_user_id, 
                        oauth_token = :access_token,
                        facebookSessionKey = :facebook_session_key,
                        lastLogin = NOW(),
                        signInCount = signInCount + 1,
                        deviceId = :deviceId
                        WHERE id = :user_id";
        
        $updateStmt = $pdo->prepare($updateQuery);
        $updateStmt->bindParam(':fb_user_id', $fbUserId, PDO::PARAM_STR);
        $updateStmt->bindParam(':access_token', $facebookAccessToken, PDO::PARAM_STR);
        $updateStmt->bindParam(':facebook_session_key', $facebookSessionKey, PDO::PARAM_STR);
        $updateStmt->bindParam(':user_id', $existingUser['id'], PDO::PARAM_INT);
        $updateStmt->bindParam(':deviceId', $deviceId, PDO::PARAM_STR);
        $updateStmt->execute();
        
        return [ 
            "status" => "LOGIN",
            "coreUserId" => $existingUser['id'],
            "email" => $email,
            "username" => $existingUser['username'],
            "sessionKey" => $facebookSessionKey,
            "signInCount" => $existingUser['signInCount'] + 1,
            "nameStatus" => 1,
            "warnings" => 0
        ];
    }

    return null;
}

function updateUserFacebookInfo($pdo, $userId, $fbUserId, $accessToken, $email, $name) {
    $updateQuery = "UPDATE users SET 
                    fbuid = :fb_user_id, 
                    oauth_token = :access_token";
    
    if ($email) {
        $updateQuery .= ", email = :email";
    }
    
    if ($name) {
        $updateQuery .= ", username = :name";
    }
    
    $updateQuery .= " WHERE id = :user_id";
    
    $updateStmt = $pdo->prepare($updateQuery);
    $updateStmt->bindParam(':fb_user_id', $fbUserId, PDO::PARAM_STR);
    $updateStmt->bindParam(':access_token', $accessToken, PDO::PARAM_STR);
    $updateStmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    
    if ($email) {
        $updateStmt->bindParam(':email', $email, PDO::PARAM_STR);
    }
    
    if ($name) {
        $updateStmt->bindParam(':name', $name, PDO::PARAM_STR);
    }
    
    $updateStmt->execute();
}

// Probably merges game data to the second account, not sure how it works exactly
function mergeAccounts($params, $pdo) {
    $secondEmail = $params[0];
    $secondPassword = $params[1];
    $mainEmail = $params[2];
    $mainPassword = $params[3];
    
    $query = "SELECT id, password FROM users WHERE email = :email";
    
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':email', $secondEmail, PDO::PARAM_STR);
    $stmt->execute();
    $secondAccount = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$secondAccount || !password_verify($secondPassword, $secondAccount['password'])) {
        return [
            "status" => "ERR_WRONG_EMAIL_OR_PASSWORD",
            "coreUserId" => 0,
            "email" => $secondEmail,
            "warnings" => 0
        ];
    }

    if ($mainEmail === '@GENERATE@') {
        return [
            "status" => "CHANGED_CORE_USER",
            "coreUserId" => $secondAccount['id'],
            "email" => $secondEmail,
            "warnings" => 0
        ];
    }
    
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':email', $mainEmail, PDO::PARAM_STR);
    $stmt->execute();
    $mainAccount = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$mainAccount || !password_verify($mainPassword, $mainAccount['password'])) {
        return [
            "status" => "ERR_WRONG_EMAIL_OR_PASSWORD",
            "coreUserId" => 0,
            "email" => $mainEmail,
            "warnings" => 0
        ];
    }
    
    return [
        "status" => "CHANGED_CORE_USER",
        "coreUserId" => $mainAccount['id'],
        "email" => $secondEmail,
        "warnings" => 0
    ];
}

function registerKingdomUser($pdo, $email, $password, $country, $language) {
    $session_key = generate_session_key();
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $username = generate_random_username();
    
    $defaultCandyProperties = json_encode([
        "candyProperties" => [
            "popCharmShop" => "false",
            "explainOwlSign" => "false",
            "boosterWheelTicket" => "false",
            "celebrateLevel2000" => "false",
            "introduceHardCurrency" => "false",
            "hasUFOBoosterBeenSeeded" => "false",
            "idOwlModeScaleFrozenEGP" => "false",
            "seedBoosterCandyUfoIngame" => "false",
            "introduceBoosterCandyHammer" => "false",
            "introduceBoosterCandyColorBomb" => "false",
            "introduceBoosterCandyUfoIngame" => "false",
            "introduceBoosterCandyExtraMoves" => "false",
            "introduceBoosterCandyFreeSwitch" => "false",
            "introduceBoosterCandySwedishFish" => "false",
            "introduceBoosterCandyStripedBrush" => "false",
            "introduceBoosterCandyStripedWrapped" => "false",
            "introduceBoosterCandyCoconutLiquorice" => "false"
        ]
    ]);
    
    $pdo->beginTransaction();
    
    try {
        $query = "INSERT INTO users (
            email, 
            password, 
            username, 
            kingSessionKey, 
            country, 
            language, 
            CandyProperties, 
            createdAt, 
            lastLogin, 
            signInCount
        ) VALUES (
            :email, 
            :password, 
            :username, 
            :session_key, 
            :country, 
            :language, 
            :candy_properties, 
            NOW(), 
            NOW(), 
            1
        )";
        
        $stmt = $pdo->prepare($query);
        
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':password', $hashed_password, PDO::PARAM_STR);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->bindParam(':session_key', $session_key, PDO::PARAM_STR);
        $stmt->bindParam(':country', $country, PDO::PARAM_STR);
        $stmt->bindParam(':language', $language, PDO::PARAM_STR);
        $stmt->bindParam(':candy_properties', $defaultCandyProperties, PDO::PARAM_STR);
        
        $stmt->execute();
        
        $user_id = $pdo->lastInsertId();
        
        $pdo->commit();
        
        return [
            'success' => true,
            'user_id' => $user_id,
            'session_key' => $session_key,
            'username' => $username
        ];
        
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}

function generate_kingdom_email() {
    $chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
    $username = '';
    
    $length = rand(8, 12);
    for ($i = 0; $i < $length; $i++) {
        $username .= $chars[rand(0, strlen($chars) - 1)];
    }
    
    $username .= rand(100, 999);
    return $username . '@u.king.com';
}

function generate_random_username() {
    $username = "king" . rand(0, 99999);
    return $username;
}

function generate_random_password($length = 12) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()';
    $password = '';
    
    for ($i = 0; $i < $length; $i++) {
        $password .= $chars[rand(0, strlen($chars) - 1)];
    }
    
    return $password;
}

function generate_session_key() {
    $randomBytes = random_bytes(64);
    $base64Data = base64_encode($randomBytes);
    $base64UrlData = strtr($base64Data, '+/', '-_');
    $base64UrlData = rtrim($base64UrlData, '=');
    $version = 5;
    return $base64UrlData . '.' . $version;
}

function getFacebookUserData($accessToken) {
    $url = "https://graph.spiritaccount.net/me?access_token=" . $accessToken . "&format=json&fields=id,name,email";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true);
}

// Facebook OAUTH Functions
// The below function was made exclusively to CCS Flash
function handleWebLogin($signed_request, $pdo) {
    $parsed_request = parse_signed_request($signed_request);
    
    if (!$parsed_request) {
        return [
            'success' => false,
            'error' => 'Invalid signed request'
        ];
    }
    
    if (!isset($parsed_request['user_id'])) {
        return [
            'success' => false,
            'error' => 'Facebook user ID not provided'
        ];
    }
    
    $fb_user_id = $parsed_request['user_id'];
    $oauth_token = $parsed_request['oauth_token'] ?? '';
    
    $fbQuery = "SELECT id, facebookSessionKey, fbuid FROM users WHERE fbuid = :fbuid";
    $fbStmt = $pdo->prepare($fbQuery);
    $fbStmt->bindParam(':fbuid', $fb_user_id, PDO::PARAM_STR);
    $fbStmt->execute();
    
    if ($fbStmt->rowCount() > 0) {
        $existingUser = $fbStmt->fetch(PDO::FETCH_ASSOC);
        
        $updateQuery = "UPDATE users SET 
                        lastLogin = NOW(), 
                        signInCount = signInCount + 1,
                        oauth_token = :oauth_token 
                        WHERE id = :user_id";
        $updateStmt = $pdo->prepare($updateQuery);
        $updateStmt->bindParam(':user_id', $existingUser['id'], PDO::PARAM_INT);
        $updateStmt->bindParam(':oauth_token', $oauth_token, PDO::PARAM_STR);
        $updateStmt->execute();
        
        return [
            'success' => true,
            'fb_uid' => $fb_user_id,
            'session_key' => $existingUser['facebookSessionKey']
        ];
    } else {
        $email = generate_kingdom_email();
        $password = generate_random_password();
        $country = 'US';
        $language = 'en';
        
        $fbUserData = getFacebookUserData($oauth_token);
        
        $kingdomSK = generate_session_key();
        $fbSK = generate_session_key();
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $username = generate_random_username();
        
        $defaultCandyProperties = json_encode([
            "candyProperties" => [
                "popCharmShop" => "false",
                "explainOwlSign" => "false",
                "boosterWheelTicket" => "false",
                "celebrateLevel2000" => "false",
                "introduceHardCurrency" => "false",
                "hasUFOBoosterBeenSeeded" => "false",
                "idOwlModeScaleFrozenEGP" => "false",
                "seedBoosterCandyUfoIngame" => "false",
                "introduceBoosterCandyHammer" => "false",
                "introduceBoosterCandyColorBomb" => "false",
                "introduceBoosterCandyUfoIngame" => "false",
                "introduceBoosterCandyExtraMoves" => "false",
                "introduceBoosterCandyFreeSwitch" => "false",
                "introduceBoosterCandySwedishFish" => "false",
                "introduceBoosterCandyStripedBrush" => "false",
                "introduceBoosterCandyStripedWrapped" => "false",
                "introduceBoosterCandyCoconutLiquorice" => "false"
            ]
        ]);
        
        $pdo->beginTransaction();
        
        try {
            $query = "INSERT INTO users (
                email, 
                password, 
                username,
                kingSessionKey,  
                facebookSessionKey, 
                fbuid,
                oauth_token,
                country, 
                language, 
                CandyProperties, 
                createdAt, 
                lastLogin, 
                signInCount
            ) VALUES (
                :email, 
                :password, 
                :username, 
                :kingSK,
                :facebookSK,
                :fb_user_id,
                :oauth_token,
                :country, 
                :language, 
                :candy_properties, 
                NOW(), 
                NOW(), 
                1
            )";
            
            $stmt = $pdo->prepare($query);
            
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':password', $hashed_password, PDO::PARAM_STR);
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->bindParam(':kingSK', $kingdomSK, PDO::PARAM_STR);
            $stmt->bindParam(':facebookSK', $fbSK, PDO::PARAM_STR);
            $stmt->bindParam(':fb_user_id', $fb_user_id, PDO::PARAM_STR);
            $stmt->bindParam(':oauth_token', $oauth_token, PDO::PARAM_STR);
            $stmt->bindParam(':country', $country, PDO::PARAM_STR);
            $stmt->bindParam(':language', $language, PDO::PARAM_STR);
            $stmt->bindParam(':candy_properties', $defaultCandyProperties, PDO::PARAM_STR);
            
            $stmt->execute();
            
            $user_id = $pdo->lastInsertId();
            
            $pdo->commit();
            
            return [
                'success' => true,
                'fb_uid' => $fb_user_id,
                'session_key' => $fbSK
            ];
            
        } catch (Exception $e) {
            $pdo->rollBack();
            return [
                'success' => false,
                'error' => 'Database error: ' . $e->getMessage()
            ];
        }
    }
}

function parse_signed_request($signed_request) {
    list($encoded_sig, $payload) = explode('.', $signed_request, 2); 
    $secret = APP_SECRET;
    $sig = base64_url_decode($encoded_sig);
    $data = json_decode(base64_url_decode($payload), true);
    
    $expected_sig = hash_hmac('sha256', $payload, $secret, $raw = true);
    if ($sig !== $expected_sig) {
        error_log('Bad Signed JSON signature!');
        return null;
    }
    return $data;
}

function base64_url_decode($input) {
    return base64_decode(strtr($input, '-_', '+/'));
}
?>