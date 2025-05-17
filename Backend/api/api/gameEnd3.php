<?php

$jsonData = $_GET['arg0'];
$scoreData = json_decode($jsonData, true);

$sessionKey = $_GET['_session'];

$result = callJsonRpc('SagaApi.gameEnd3', [$scoreData], $sessionKey);
header('Content-Type: application/json');
echo json_encode($result);

?>