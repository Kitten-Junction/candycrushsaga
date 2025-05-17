<?php

$sessionKey = $_GET['_session'];
$episode = json_decode($_GET['arg0'], true);

$result = callJsonRpc('SagaApi.getEpisodeChampions', [$episode], $sessionKey);
header('Content-Type: application/json');
echo json_encode($result);

?>