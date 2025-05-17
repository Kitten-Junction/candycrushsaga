<?php

function setCandyProperty($pdo, $arg0, $arg1, $sessionKey) {
    if (isset($arg0) && isset($arg1) && isset($sessionKey)) {
        $propertyName = $arg0;
        $propertyValue = strtolower($arg1) === 'true' ? "true" : "false";
        
        $allowedProperties = [
            "idWorldIntroduction", "idExplainSwitching1", "idExplainSwitching2", "idExplainSwitching3", "idExplainSwitching4",
            "idExplainSwitchingRandom", "TRKidExplainSwitchingRandom", "TRKidOwlModeMoonFill", "TRKidOwlModeMoonStruckReached",
            "TRKidOwlModeMoonStruckOngoing", "idExplainLightUp1", "idExplainLightUp2", "TRKidExplainLightUp", "idMovesLeft",
            "idScoreMeter", "idStriped1", "idStriped2", "idStriped3", "idStriped4", "idScoreLimit", "idScoreLimitReached",
            "idWrapped1", "idWrapped2", "idStripedWrapped1", "idColorBomb1", "idColorBomb2", "idColorBomb3", "idDropDown1",
            "idOrderMode1", "idOwlModeIntro", "idOwlModeScale", "idOwlModeScaleFrozenEGP", "idOwlModeMoonStruckReached",
            "idOwlModeScaleFrozenIngame", "idOwlModeMoonStruckOngoing", "popCharmShop", "introduceBoosterCandyExtraMoves",
            "introduceBoosterCandyHammer", "introduceBoosterCandyColorBomb", "introduceBoosterCandySwedishFish",
            "introduceBoosterCandyCoconutLiquorice", "introduceBoosterCandyFreeSwitch", "introduceBoosterCandyExtraTime",
            "introduceBoosterCandyStripedWrapped", "introduceBoosterCandyAntiPeppar", "introduceBoosterCandyJoker",
            "introduceBoosterCandySweetTeeth", "introduceBoosterCandyMoonStruck", "introduceBoosterCandyShuffle",
            "introduceBoosterCandyBubbleGum", "introduceBoosterCandyUfoIngame", "seedBoosterCandyUfoIngame",
            "introduceBoosterCandyStripedBrush", "introduceBooster", "seedBooster", "explainOwlSign", "explainOwlFalling",
            "introduceFrog", "frogEat", "frogEatToBeFull", "frogFull", "introduceSugarTrack", "introduceSugarDrop",
            "introducePopcorn", "introduceMagicMixerInOrderMode", "recievedCCSMobileInstallReward", "introduceHardCurrency",
            "unlimitedLifeEndTime", "sodaSynergiesHasGivenEmail", "extraMovesEndTime", "hasUFOBoosterBeenSeeded",
            "celebrateLevel2000", "completedLevel2000"
        ];
        
        if (!in_array($propertyName, $allowedProperties)) {
            http_response_code(403);
            return;
        }
        
        try {
            $userId = getUserIdBySessionKey($pdo, $sessionKey);
            if ($userId === null) {
                http_response_code(403);
                return;
            }
            $stmt = $pdo->prepare("SELECT candyProperties FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result) {
                $properties = json_decode($result['candyProperties'], true);
                if (!$properties) {
                    $properties = ['candyProperties' => []];
                }
            } else {
                $properties = ['candyProperties' => []];
            }
            
            if (isset($properties['candyProperties'][$propertyName]) && 
                $properties['candyProperties'][$propertyName] === "true" &&
                $propertyValue === "false") {
                http_response_code(403);
                return;
            }
            
            $properties['candyProperties'][$propertyName] = $propertyValue;
            $jsonProperties = json_encode($properties);
            if ($result) {
                $stmt = $pdo->prepare("UPDATE users SET candyProperties = ? WHERE id = ?");
            } else {
                $stmt = $pdo->prepare("INSERT INTO users (candyProperties, id) VALUES (?, ?)");
            }
            $stmt->execute([$jsonProperties, $userId]);
            http_response_code(200);
        } catch (Exception $e) {
            http_response_code(500);
        }
    } else {
        http_response_code(400);
    }
}

function getCandyProperties($pdo, $sessionKey) {
    
    if (!isset($sessionKey)) {
        http_response_code(400);
        return;
    }

    try {
        $userId = getUserIdBySessionKey($pdo, $sessionKey);
        if ($userId === null) {
            http_response_code(403);
            return;
        }

        $stmt = $pdo->prepare("SELECT candyProperties FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result && !empty($result['candyProperties'])) {
            header('Content-Type: application/json');
            echo $result['candyProperties'];
            exit();
        } else {
            http_response_code(404);
            exit();
        }
    } catch (Exception $e) {
        http_response_code(500);
        exit();
    }
}

?>
