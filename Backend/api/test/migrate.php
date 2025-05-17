<?php
session_start();

function fetchFromKingApi($method, $params = [], $sessionKey = null) {
    $request = [
        [
            "jsonrpc" => "2.0",
            "method" => $method,
            "params" => $params,
            "id" => mt_rand(1, 1000)
        ]
    ];
    
    $url = "https://candycrush.king.com/rpc/ClientApi";
    if ($sessionKey) {
        $url .= "?_session=" . urlencode($sessionKey);
    }
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($response, true);
}

function login($email, $password) {
    $loginResponse = fetchFromKingApi("AppApi.connectUsingKingdom2", [
        1, $email, $password, "CA", "en-CA_CA", 54, "", "", "UTC"
    ]);
    
    return $loginResponse[0]['result']['sessionKey'] ?? null;
}

function getUserData($sessionKey) {
    return fetchFromKingApi("SagaApi.gameInitLight", [], $sessionKey);
}

function getUserIdBySK($sessionKey) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT id FROM users WHERE facebookSessionKey = ? OR kingSessionKey = ?");
    $stmt->execute([$sessionKey, $sessionKey]);
    return $stmt->fetchColumn();
}

function migrateUserLevels($targetUserId, $universeData) {
    global $userFolder;
    
    if (!isset($universeData['episodes']) || !is_array($universeData['episodes'])) {
        return false;
    }
    
    foreach ($universeData['episodes'] as $episode) {
        $episodeId = $episode['id'];
        $episodeDir = $userFolder . "/{$targetUserId}/{$episodeId}";
        
        if (!is_dir($episodeDir)) {
            mkdir($episodeDir, 0777, true);
        }
        
        foreach ($episode['levels'] as $level) {
            $levelId = $level['id'];
            $levelFile = "{$episodeDir}/{$levelId}.txt";
            
            file_put_contents($levelFile, json_encode($level));
        }
    }
    
    return true;
}

function migrateAccount($targetUserId, $sourceUserData) {
    global $pdo;

    $goldAmount = $sourceUserData[0]['result']['currentUser']['gold'];

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("UPDATE users SET gold = ? WHERE id = ?");
        $stmt->execute([$goldAmount, $targetUserId]);

        $stmt = $pdo->prepare("DELETE FROM user_items WHERE uid = ?");
        $stmt->execute([$targetUserId]);

        if (isset($sourceUserData[0]['result']['itemBalance'])) {
            $stmt = $pdo->prepare("INSERT INTO user_items (uid, items) VALUES (?, ?)");
            $itemsJson = json_encode($sourceUserData[0]['result']['itemBalance']);
            $stmt->execute([$targetUserId, $itemsJson]);
        }

        $pdo->commit();

        if (isset($sourceUserData[0]['result']['userUniverse'])) {
            migrateUserLevels($targetUserId, $sourceUserData[0]['result']['userUniverse']);
        }

        return true;
    } catch (Exception $e) {
        $pdo->rollBack();
        return false;
    }
}

if (isset($_POST['action'])) {
    $action = $_POST['action'];
    
    if ($action === 'login') {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        $sessionKey = login($email, $password);
        
        if ($sessionKey) {
            $_SESSION['king_session_key'] = $sessionKey;
            $userData = getUserData($sessionKey);
            $_SESSION['king_user_data'] = $userData;
            
            $message = "Login successful";
        } else {
            $message = "Login failed";
        }
    } elseif ($action === 'migrate') {
        $targetSessionKey = $_POST['sa_session_key'] ?? '';

        if (isset($_SESSION['king_user_data']) && $targetSessionKey) {
            $targetUserId = getUserIdBySK($targetSessionKey);

            if ($targetUserId) {
                $result = migrateAccount($targetUserId, $_SESSION['king_user_data']);
                $message = $result ? "Migration successful, you can now close this page." : "Migration failed";

                if ($result) {
                    $_SESSION = [];
                    session_unset();
                    session_destroy();
                }
            } else {
                $message = "User not found.";
            }
        } else {
            $message = "Missing data for migration";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>AM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)),
                        url('https://candycrush.spiritaccount.net/images/backgrounds/bg1.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            min-height: 100vh;
        }
        .container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .form-container {
            background: rgba(33, 37, 41, 0.95);
            border-radius: 15px;
            padding: 2rem;
            width: 100%;
            max-width: 500px;
            color: white;
        }
        .alert {
            margin-bottom: 1rem;
        }
        .form-control {
            background-color: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
        }
        .form-control:focus {
            background-color: rgba(255, 255, 255, 0.15);
            border-color: rgba(255, 255, 255, 0.3);
            color: white;
        }
        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }
    </style>
</head>
<body class="bg-dark text-light">
    <div class="container">
        <div class="form-container">
            <?php if (isset($message)): ?>
            <div class="alert alert-info" role="alert">
                <?php echo $message; ?>
            </div>
            <?php endif; ?>
            
            <?php if (!isset($_SESSION['king_session_key'])): ?>
            <h4 class="mb-4 text-center">Account Migration (1/2)</h4>
            <p>Input your King.com (Kingdom) account details below to start the migration process</p>
            <p class="text-warning">You CANNOT use a Facebook account to migrate. You will need a King.com account, Migration still works if your King.com account is connected to your Facebook account, but it will NOT work if you are using a Facebook account that is connected to King.com (doing so will result in wrong account data)</p>
            
            <form method="post">
                <input type="hidden" name="action" value="login">
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text bg-dark text-light border-secondary">
                            <i class="bi bi-envelope"></i>
                        </span>
                        <input type="email" class="form-control" name="email" placeholder="Email" required>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text bg-dark text-light border-secondary">
                            <i class="bi bi-lock"></i>
                        </span>
                        <input type="password" class="form-control" name="password" placeholder="Password" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Login
                </button>
            </form>
            <?php else: ?>
            <h4 class="mb-4 text-center">Account Migration (2/2)</h4>
            <p>Input your SA CCS session key to migrate your kingdom data to your SA CCS account</p>
            <p class="text-info">Don't know what is your session key? contact idkwhattoput on the Discord server!</p>
            
            <form method="post">
                <input type="hidden" name="action" value="migrate">
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text bg-dark text-light border-secondary">
                            <i class="bi bi-key"></i>
                        </span>
                        <input type="text" class="form-control" name="sa_session_key" placeholder="Session Key" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-success w-100">
                    <i class="bi bi-arrow-right-circle me-2"></i>Migrate
                </button>
            </form>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>