<?php

$episodeId = $_GET['arg0'];
$levelId = $_GET['arg1'];

$sessionKey = $_GET['_session'];

$result = callJsonRpc('SagaApi.gameStart2', [$levelId,$episodeId], $sessionKey);
header('Content-Type: application/json');
echo json_encode($result);

?>
