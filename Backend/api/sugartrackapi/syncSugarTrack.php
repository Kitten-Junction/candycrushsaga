<?php

$balance = $_GET['arg0'];
$scoreData = json_decode($balance, true);

$sessionKey = $_GET['_session'];

$result = callJsonRpc('AppSugarTrackApi.syncSugarTrack', [$balance], $sessionKey);
header('Content-Type: application/json');
echo json_encode($result);

?>