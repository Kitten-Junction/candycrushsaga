<?php

$episodeId = $_GET['arg0'];
$levelId = $_GET['arg1'];

$sessionKey = $_GET['_session'];

$result = callJsonRpc('AppSagaApi.getLevelToplist2', [$episodeId, $levelId], $sessionKey);
header('Content-Type: application/json');
echo json_encode($result);

?>
