<?php
// Generate game configuration files
// Please run this tool everytime you edit an level on the /data/level_data/ folder
// php GCG.php

$gameModeNames = json_decode(file_get_contents('/var/www/candycrush/Backend/api/candycrushapi/getGameModes.php'), true);
$LevelModeIds = [];
$episode = 1;
$level = 1;
$sections = [];

function getWorldId($episode) {
    $a = 10;
    $b = $a * 6;
    return $episode <= $b ? ceil($episode / 6) : $a + ceil(($episode - $b) / 3);
}

function getDWWorldId($episode) {
    $episode -= 1200;
    $a = 10;
    $b = $a * 6;
    return $episode <= $b ? ceil($episode / 6) + 1200 : $a + ceil(($episode - $b) / 3) + 1200;
}

$totalLevel = 1;
$world = getWorldId($episode);
$hasDWSwapped = false;

while ($episode <= 1246) {
    if ($level > 15) {
        $episode++;
        $world = getWorldId($episode);
        if ($episode >= 1201) {
            $world = getDWWorldId($episode);
            if (!$hasDWSwapped) {
                $hasDWSwapped = true;
            }
        }
        $level = 1;
    }
    
    $levelFilePath = "/var/www/candycrush/Backend/data/level_data/episode{$episode}level{$level}.txt";
    if (file_exists($levelFilePath)) {
        $leveldata = file_get_contents($levelFilePath);
        if (!isset($sections[$world])) {
            $sections[$world] = [];
        }
        
        $jsonlevel = json_decode($leveldata, true);
        $gameModeId = array_search($jsonlevel['gameModeName'], $gameModeNames);
        $LevelModeIds[] = $gameModeId;
        
        $sections[$world][] = [
            'episode' => $episode,
            'level' => $level,
            'gameData' => $leveldata,
            'totalLevel' => $totalLevel
        ];
        
        $totalLevel++;
    }
    $level++;
}

$config = [];
foreach ($sections as $section => $sectionData) {
    $config = array_merge($config, $sectionData);
    file_put_contents(
        "/var/www/candycrush/Frontend/resources/game-configurations{$section}.json",
        json_encode($sectionData, JSON_PRETTY_PRINT)
    );
}

file_put_contents('/var/www/candycrush/Frontend/resources/game-configuration.json', json_encode($config, JSON_PRETTY_PRINT));
file_put_contents('/var/www/candycrush/Backend/api/candycrushapi/getGameModePerLevel.php', json_encode($LevelModeIds));