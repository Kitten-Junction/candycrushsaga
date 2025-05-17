<?php
include $modules . '/user/userData.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 400 Bad Request');
    header('Content-Type: text/html; charset=utf-8');
    echo '<html><head>
<meta http-equiv="content-type" content="text/html;charset=utf-8">
<title>400 Bad Request</title>
</head>
<body text=#000000 bgcolor=#ffffff>
<h1>Error: Bad Request</h1>
<h2>Your client has issued a malformed or illegal request.</h2>
<h2></h2>
</body></html>';
    exit;
}

class PlatformRpcServer {
    private $pdo;
    private $sessionKey;
    private $userData;
    private $tokenData;
    private $userId;
    private $serverTime;
    
    public function __construct($pdo, $sessionKey) {
        $this->pdo = $pdo;
        $this->sessionKey = $sessionKey;
        $this->userData = $this->getCurrentUser($pdo, $sessionKey);
        $this->tokenData = $this->getUserTokens();
        $this->userId = $this->getUserIdBySessionKey($pdo, $sessionKey);
        $this->serverTime = time();
    }
    
    public function handleRequests($rawInput) {
        if (strpos($_SERVER['HTTP_CONTENT_ENCODING'], 'gzip') !== false) {
            $rawInput = gzdecode($rawInput);
        }
        
        $requests = json_decode($rawInput, true);

        $responses = [];
        foreach ($requests as $req) {
            $response = $this->processRequest($req);
            $responses[] = $response;
        }
        
        return $responses;
    }
    
    private function processRequest($req) {
        $method = $req['method'] ?? '';
        $params = $req['params'] ?? [];
        
        $authenticatedMethods = [
            'ClientHealthTracking.clientException2',
        ];

        if (in_array($method, $authenticatedMethods) && !$this->userId) {
            return [
                'jsonrpc' => '2.0', 
                'id' => $req['id'],
                'error' => [
                    'code' => 2,
                    'message' => 'Authentication error'
                ]
            ];
        }

        $determinedResult = $this->dispatchMethod($method, $params);
        
        if ($determinedResult === null) {
            return [
                'jsonrpc' => '2.0',
                'id' => $req['id'],
                'error' => [
                    'code' => -32601,
                    'message' => 'Method not found'
                ]
            ];
        }
        
        return [
            'jsonrpc' => '2.0',
            'id' => $req['id'],
            'result' => $determinedResult
        ];
    }
    
    private function dispatchMethod($method, $params) {
        $methodHandlers = [
            'ClientHealthTracking.clientException2' => function($params) {
                if (!is_array($params) || count($params) < 2) return [];

                $exceptionText = $params[1];
                $userId = $this->userId ?: 'unknown';
                $timestamp = time();
                
                $folder = '/var/www/candycrush/Backend/data/health_logs';

                $filename = $folder . "/exception_{$userId}_{$timestamp}.txt";

                file_put_contents($filename, $exceptionText);

                return [];
            },
        ];
        
        if (isset($methodHandlers[$method])) {
            return $methodHandlers[$method]($params);
        }
        
        return null;
    }

    private function getCurrentUser($pdo, $sessionKey) {
        return getCurrentUser($pdo, $sessionKey);
    }

    private function getUserTokens() {
        return getUserTokens();
    }
    
    private function getUserIdBySessionKey($pdo, $sessionKey) {
        return getUserIdBySessionKey($pdo, $sessionKey);
    }
}

$isSecure = true;
$sessionKey = $_GET['_session'];
$rawInput = file_get_contents('php://input');

$PlatformRpcServer = new PlatformRpcServer($pdo, $sessionKey);
$responses = $PlatformRpcServer->handleRequests($rawInput);

header('Content-Type: application/json');
echo json_encode($responses, JSON_UNESCAPED_SLASHES);