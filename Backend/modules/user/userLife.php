<?php

class KingLifeSystem {
    private const REGENERATION_INTERVAL = 1800;
    
    public static function KingLife($pdo, &$userData) {
        $now = new DateTime();
        
        if ($userData['timeToNextRegeneration'] === null) {
            if ($userData['lives'] < $userData['maxLives']) {
                $nextRegenerationTime = $now->add(new DateInterval('PT' . self::REGENERATION_INTERVAL . 'S'));
                $stmt = $pdo->prepare("UPDATE users SET timeToNextRegeneration = ? WHERE id = ?");
                $stmt->execute([$nextRegenerationTime->format('Y-m-d H:i:s'), $userData['userId']]);
                $userData['timeToNextRegeneration'] = self::REGENERATION_INTERVAL;
            } else {
                $userData['timeToNextRegeneration'] = -1;
            }
            return;
        }

        $stmt = $pdo->prepare("SELECT lives, timeToNextRegeneration FROM users WHERE id = ? FOR UPDATE");
        $stmt->execute([$userData['userId']]);
        $currentData = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$currentData['timeToNextRegeneration']) {
            $userData['timeToNextRegeneration'] = -1;
            return;
        }

        $regenerationTime = new DateTime($currentData['timeToNextRegeneration']);
        $timeDifference = $now->diff($regenerationTime);
        
        if ($regenerationTime <= $now) {
            $secondsPassed = $now->getTimestamp() - $regenerationTime->getTimestamp();
            $livesToAdd = min(
                floor($secondsPassed / self::REGENERATION_INTERVAL) + 1,
                $userData['maxLives'] - $currentData['lives']
            );
            
            if ($livesToAdd > 0) {
                $newLives = min($currentData['lives'] + $livesToAdd, $userData['maxLives']);
                $userData['lives'] = $newLives;
                
                if ($newLives < $userData['maxLives']) {
                    $nextRegenerationTime = clone $now;
                    $nextRegenerationTime->add(new DateInterval('PT' . self::REGENERATION_INTERVAL . 'S'));
                    $stmt = $pdo->prepare("UPDATE users SET lives = ?, timeToNextRegeneration = ? WHERE id = ?");
                    $stmt->execute([$newLives, $nextRegenerationTime->format('Y-m-d H:i:s'), $userData['userId']]);
                    $userData['timeToNextRegeneration'] = self::REGENERATION_INTERVAL;
                } else {
                    $stmt = $pdo->prepare("UPDATE users SET lives = ?, timeToNextRegeneration = NULL WHERE id = ?");
                    $stmt->execute([$newLives, $userData['userId']]);
                    $userData['timeToNextRegeneration'] = -1;
                }
            }
        } else {
            $userData['timeToNextRegeneration'] = $regenerationTime->getTimestamp() - $now->getTimestamp();
        }
    }

    public static function KingGameWin($pdo, $userId) {
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("
                SELECT lives, maxLives, timeToNextRegeneration 
                FROM users 
                WHERE id = ? FOR UPDATE
            ");
            $stmt->execute([$userId]);
            $userData = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($userData['lives'] >= $userData['maxLives']) {
                $pdo->commit();
                return true;
            }

            $stmt = $pdo->prepare("
                UPDATE users
                SET lives = LEAST(lives + 1, maxLives),
                    timeToNextRegeneration = CASE 
                        WHEN lives + 1 >= maxLives THEN NULL 
                        ELSE timeToNextRegeneration 
                    END
                WHERE id = ?
            ");
            
            $result = $stmt->execute([$userId]);

            if (!$result) {
                $pdo->rollBack();
                return false;
            }

            $pdo->commit();
            return true;

        } catch (Exception $e) {
            $pdo->rollBack();
            return false;
        }
    }

    public static function KingGameOver($pdo, $userId) {
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("SELECT lives, maxLives FROM users WHERE id = ? FOR UPDATE");
            $stmt->execute([$userId]);
            $userData = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$userData || $userData['lives'] <= 0) {
                $pdo->rollBack();
                return false;
            }

            $nextRegenerationTime = new DateTime();
            $nextRegenerationTime->add(new DateInterval('PT' . self::REGENERATION_INTERVAL . 'S'));

            $stmt = $pdo->prepare("
                UPDATE users
                SET lives = lives - 1,
                    timeToNextRegeneration = CASE 
                        WHEN timeToNextRegeneration IS NULL THEN ?
                        ELSE timeToNextRegeneration
                    END
                WHERE id = ? AND lives > 0
            ");
            
            $result = $stmt->execute([
                $nextRegenerationTime->format('Y-m-d H:i:s'),
                $userId
            ]);

            if (!$result) {
                $pdo->rollBack();
                return false;
            }

            $pdo->commit();
            return true;

        } catch (Exception $e) {
            $pdo->rollBack();
            return false;
        }
    }
}

?>