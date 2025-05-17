<?php

include $modules . '/candyProperty.php';

$response = setCandyProperty($pdo, $_GET['arg0'] ?? null, $_GET['arg1'] ?? null, $_GET['_session'] ?? null);

echo json_encode($response);

?>