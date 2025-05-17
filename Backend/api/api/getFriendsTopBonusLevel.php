<?php

$sessionKey = $_GET['_session'];

$result = callJsonRpc('AppSagaApi.getFriendsTopBonusLevel2', [], $sessionKey);
header('Content-Type: application/json');
echo json_encode($result);

?>