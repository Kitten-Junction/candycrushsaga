<?php
class KingEventManager {
    private $events = [];
    private $msgIdCounter = 0;
    
    const LEVEL_COMPLETED = 'LEVEL_COMPLETED';
    const LEVEL_UNLOCKED = 'LEVEL_UNLOCKED';
    const EPISODE_COMPLETED = 'EPISODE_COMPLETED';
    const EPISODE_UNLOCKED = 'EPISODE_UNLOCKED';
    
    public function addLevelCompletedEvent($episodeId, $levelId) {
        $eventData = [
            'episodeId' => $episodeId,
            'levelId' => $levelId
        ];
        return $this->addEvent(self::LEVEL_COMPLETED, $eventData);
    }
    
    public function addLevelUnlockedEvent($episodeId, $levelId, $unlockType = 0) {
        $eventData = [
            'episodeId' => $episodeId,
            'levelId' => $levelId,
            'unlockType' => $unlockType
        ];
        return $this->addEvent(self::LEVEL_UNLOCKED, $eventData);
    }

    public function addEpisodeCompletedEvent($episodeId) {
        $eventData = [
            'episodeId' => $episodeId
        ];
        return $this->addEvent(self::EPISODE_COMPLETED, $eventData);
    }

    public function addEpisodeUnlockedEvent($episodeId) {
        $eventData = [
            'episodeId' => $episodeId
        ];
        return $this->addEvent(self::EPISODE_UNLOCKED, $eventData);
    }

    public function addMessageEvent($type, $data) {
        return $this->addEvent($type, $data);
    }
    
    private function addEvent($type, $data) {
        $event = [
            'msgId' => $this->msgIdCounter++,
            'type' => $type,
            'data' => json_encode($data)
        ];
        $this->events[] = $event;
        return $event;
    }
    
    public function getEvents() {
        return $this->events;
    }
    
    public function clearEvents() {
        $this->events = [];
        $this->msgIdCounter = 0;
    }
    
    public function hasLevelEvent($type, $episodeId, $levelId) {
        foreach ($this->events as $event) {
            if ($event['type'] === $type) {
                $data = json_decode($event['data'], true);
                if ($data['episodeId'] === $episodeId && 
                    (isset($data['levelId']) ? $data['levelId'] === $levelId : true)) {
                    return true;
                }
            }
        }
        return false;
    }
}
?>
