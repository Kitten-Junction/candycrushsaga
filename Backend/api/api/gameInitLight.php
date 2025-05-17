<?php

$sessionKey = $_GET['_session'];

$language = $_SERVER['HTTP_ACCEPT_LANGUAGE'];

$result = callJsonRpc('SagaApi.gameInitLight', [], $sessionKey, 1, JSONRPCEP, $language);
header('Content-Type: application/json');
echo json_encode($result);

?>