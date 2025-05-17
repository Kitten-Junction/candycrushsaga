<?php
// King RPC Server
// (but more prone to death)

include $modules . '/user/userData.php';
include $modules . '/KingSocial.php';
include $modules . '/user/userEpisodeChampions.php';
include $modules . '/user/userUniverses.php';
include $modules . '/user/userItems.php';
include $modules . '/KingIAP.php';
include $modules . '/eventManager.php';
include $modules . '/KingMessages.php';
include $modules . '/user/userSugarTrack.php';
include $modules . '/candyProperty.php';
include $modules . '/JsonRPCFunctions.php';
require_once $modules . '/KingdomAccount.php';

if (isset($_SERVER['HTTP_ACCEPT']) && 
    strpos($_SERVER['HTTP_ACCEPT'], 'text/html') !== false &&
    !isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    (empty($_SERVER['HTTP_USER_AGENT']) || !preg_match('/(curl|wget|postman|insomnia)/i', $_SERVER['HTTP_USER_AGENT']))) {
    
    header('HTTP/1.1 405 Method Not Allowed');
    header('Content-Type: text/html; charset=utf-8');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 400 Bad Request');
    header('Content-Type: text/html; charset=utf-8');
    echo '<html><head>
<meta http-equiv="content-type" content="text/html;charset=utf-8">
<title>400 Bad Request</title>
</head>
<body text=#000000 bgcolor=#ffffff>
<h1>Error: Bad Request</h1>
<h2>Your client has issued a malformed or illegal request.</h2>
<h2></h2>
</body></html>';
    exit;
}

class JsonRpcServer {
    private $pdo;
    private $sessionKey;
    private $userData;
    private $tokenData;
    private $userId;
    private $accessToken;
    private $serverTime;
    private $language;
    
    public function __construct($pdo, $sessionKey, $language) {
        $this->pdo = $pdo;
        $this->sessionKey = $sessionKey;
        $this->userData = $this->getCurrentUser($pdo, $sessionKey);
        $this->tokenData = $this->getUserTokens();
        $this->accessToken = $this->tokenData['oauth_token'] ?? null;
        $this->userId = $this->getUserIdBySessionKey($pdo, $sessionKey);
        $this->serverTime = time();
        $this->language = $language;
    }
    
    public function handleRequests($rawInput) {
        if (strpos($_SERVER['HTTP_CONTENT_ENCODING'], 'gzip') !== false) {
            $rawInput = gzdecode($rawInput);
        }
        
        $requests = json_decode($rawInput, true);

        $responses = [];
        foreach ($requests as $req) {
            $response = $this->processRequest($req);
            $responses[] = $response;
        }
        
        return $responses;
    }
    
    private function processRequest($req) {
        $method = $req['method'] ?? '';
        $params = $req['params'] ?? [];
        
        // Methods that requires VALID authentication
        $authenticatedMethods = [
            'ProductApi.purchase',
            'AppProductApi.purchaseFromKing4',
            'CandyCrushVanityItemApi.updateVanityItem',
            'CandyCrushVanityItemApi.getAllUserVanityItems',
            'AppCandyCrushAPI.hasActiveWheelOfBooster',
            'AppCandyCrushAPI.getJsonFileUrl',
            'AppSagaApi.getFriendProfiles2',
            'AppSagaApi.getFriendsTopBonusLevel2',
            'AppSocialUserApi.getAppFriends3',
            'AppSocialUserApi.getCurrentUser2',
            'AppUniverseApi.syncLevels',
            'AppUniverseApi.getUniverse3',
            'AppSugarTrackApi.syncSugarTrack',
            'AppSugarTrackApi.syncSugarTrackOnGameEnd',
            'AppSugarTrackApi.getSugarTrackLevels',
            'AppSagaApi.getLevelToplist2',
            'OpenGraphPublisher.publishGiveLife',
            //'AppSagaApi.syncCharms',
            'AppVirtualCurrencyApi.getBalance',
            'AppSagaApi.getAllItems',
            'AppCandyCrushAPI.getWheelOfBoosterPrize',
            'AppCandyCrushAPI.getWheelOfBoosterJackpotLevel',
            'AppKingdomApi.setSelectableAvatar',
            'AppKingdomApi.setEmailAndPassword',
            'AppKingdomApi.mergeAccounts',
            'AppKingdomApi.setFullName'
        ];

        if (in_array($method, $authenticatedMethods) && !$this->userId) {
            return [
                'jsonrpc' => '2.0', 
                'id' => $req['id'],
                'error' => [
                    'code' => 2,
                    'message' => 'Authentication error'
                ]
            ];
        }

        $determinedResult = $this->dispatchMethod($method, $params);
        
        if ($determinedResult === null) {
            return [
                'jsonrpc' => '2.0',
                'id' => $req['id'],
                'error' => [
                    'code' => -32601,
                    'message' => 'Method not found'
                ]
            ];
        }
        
        return [
            'jsonrpc' => '2.0',
            'id' => $req['id'],
            'result' => $determinedResult
        ];
    }
    
    private function dispatchMethod($method, $params) {
        $methodHandlers = [
            'TrackingApi.track' => function() { return []; },
            'UserMetrics2Api.getUserMetrics' => function() { return []; },
            'ProductApi.getUserCurrency' => function() { return 'USD'; },
            'ProductApi.getAllProductPackages' => function() { return getAllProductPackages(); },
            'AppProductApi.getAllProductPackages' => function() { return json_decode(file_get_contents("/var/www/candycrush/Backend/data/mobile_pm.json")); },
            'ProductApi.purchase' => function($params) { return $this->handlePurchase($params); },
            'PurchaseApi.getPendingAnimations' => function() { return []; },
            'AppProductApi.purchaseFromKing4' => function($params) { return $this->handlePurchaseFromKing4($params); },
            'CandyCrushVanityItemApi.updateVanityItem' => function($params) { return $this->handleUpdateVanityItem($params); },
            'CandyCrushVanityItemApi.getAllUserVanityItems' => function() { return getAllUserVanityItems($this->pdo, $this->sessionKey); },
            'TimeApi.getServerTime' => function() { return $this->serverTime; },
            'AppPlayerCommunityApi.isPlayerCommunityEnabled' => function() { return false; },
            'AppCandyCrushAPI.isPayingUser' => function() { return false; },
            'AppCandyCrushAPI.isPayingUserInApp' => function() { return false; },
            'AppAbTestApi.getAppUserAbCases' => function() {
                $jsonString = '{"cases":[{"version":4,"caseNum":1},{"version":2,"caseNum":1},{"version":6,"caseNum":1},{"version":-1,"caseNum":0},{"version":-1,"caseNum":0},{"version":-1,"caseNum":0},{"version":1,"caseNum":1},{"version":1,"caseNum":1},{"version":-1,"caseNum":0},{"version":-1,"caseNum":0},{"version":-1,"caseNum":0},{"version":-1,"caseNum":0},{"version":1,"caseNum":1},{"version":-1,"caseNum":0},{"version":-1,"caseNum":0},{"version":-1,"caseNum":0},{"version":-1,"caseNum":0},{"version":6,"caseNum":3},{"version":-1,"caseNum":0},{"version":-1,"caseNum":0},{"version":-1,"caseNum":0},{"version":6,"caseNum":1},{"version":-1,"caseNum":0},{"version":-1,"caseNum":0},{"version":-1,"caseNum":0},{"version":-1,"caseNum":0},{"version":1,"caseNum":1},{"version":-1,"caseNum":0},{"version":-1,"caseNum":0},{"version":-1,"caseNum":0},{"version":-1,"caseNum":0},{"version":-1,"caseNum":0},{"version":-1,"caseNum":0},{"version":1,"caseNum":1},{"version":1,"caseNum":1},{"version":-1,"caseNum":0},{"version":5,"caseNum":1},{"version":-1,"caseNum":0},{"version":-1,"caseNum":0},{"version":4,"caseNum":1},{"version":-1,"caseNum":0},{"version":-1,"caseNum":0},{"version":-1,"caseNum":0},{"version":-1,"caseNum":0},{"version":2,"caseNum":1},{"version":2,"caseNum":1},{"version":3,"caseNum":1},{"version":3,"caseNum":1},{"version":-1,"caseNum":0},{"version":-1,"caseNum":0},{"version":-1,"caseNum":0},{"version":5,"caseNum":1},{"version":-1,"caseNum":0},{"version":2,"caseNum":1},{"version":-1,"caseNum":0},{"version":1,"caseNum":1},{"version":-1,"caseNum":0},{"version":1,"caseNum":3},{"version":-1,"caseNum":0},{"version":-1,"caseNum":0},{"version":-1,"caseNum":0}]}';
                return json_decode($jsonString, true);
            },
            'AppSagaApi.getGiveLifeUrlMessage2' => function() {
                $jsonString = '{"id":4,"message":"9IfhHpF_PMkCdbXdRsUsDo52lHJygc0Sjmbw7T1X-Mc:MSM4NTkxMjc3NjUwMzgjMTUwMjk4NDE0MzYjZ2l2ZUxpZmUjIzE3NDMzNjc2NTkjMiNnaXZlTGlmZVRvTWFueSM"}';
                return json_decode($jsonString, true);
            },
            'AppSagaApi.getRequestLifeUrlMessage2' => function() {
                $jsonString = '{"id":3,"message":"Qy1OuwBKySiXprnfbJEMVq0q25Dp5AkWyNuH32CkGb0:MSM4NTk1MzI2MDE4ODQjMTUwMjk4NDE0MzYjcmVxdWVzdExpZmUjIzE3NDM2MzM4ODQjMiNyZXF1ZXN0TGlmZSM"}';
                return json_decode($jsonString, true);
            },
            'SagaApi.getGiveLifeUrlMessage' => function() {
                $jsonString = '{"id":2,"message":"9IfhHpF_PMkCdbXdRsUsDo52lHJygc0Sjmbw7T1X-Mc:MSM4NTkxMjc3NjUwMzgjMTUwMjk4NDE0MzYjZ2l2ZUxpZmUjIzE3NDMzNjc2NTkjMiNnaXZlTGlmZVRvTWFueSM"}';
                return json_decode($jsonString, true);
            },
            'CandyCrushAPI.getBoosterGiftUrlMessage' => function() {
                $jsonString = '{"id":1,"message":"Qy1OuwBKySiXprnfbJEMVq0q25Dp5AkWyNuH32CkGb0:MSM4NTk1MzI2MDE4ODQjMTUwMjk4NDE0MzYjcmVxdWVzdExpZmUjIzE3NDM2MzM4ODQjMiNyZXF1ZXN0TGlmZSM"}';
                return json_decode($jsonString, true);
            },
            'OpenGraphPublisher.publishInGameFeats' => function() { return []; },
            'OpenGraphPublisher.publishCompleteLevel' => function() { return []; },
            'OpenGraphPublisher.publishGiveLife' => function($params) {return $this->publishGiveLife($params); },
            'ServiceLayerApi.getMessages4' => function() {
                $jsonString = '{"msgs":[{"id":1,"type":2,"mode":1,"objective":1,"format":1,"targetAppId":0,"version":1,"payload":{"action":{"key":"BUTTON","primary":"https://thegreenspirit.serv00.net/","primaryType":2,"fallbackType":0,"behaviour":2,"removeBehaviour":0,"notificationTrigger":false,"linkMap":[]},"txts":[{"key":"TITLE","track":"826014","txt":"Thanks for playing!"},{"key":"BUTTON","track":"825974","txt":"More projects"},{"key":"MESSAGE","track":"825994","txt":"Hey everyone! It’s idk speaking! Thanks for trying out our mod, lots of research and patience went into it. Hope you enjoy it!\n- TGS, Inc. Development"}],"imgs":[{"key":"BACKGROUND","track":"395913","url":"http://candycrush.spiritaccount.net/images/message/gurumin.png","fallback":1}],"children":[],"actions":[{"key":"BUTTON","primary":"https://thegreenspirit.serv00.net/","primaryType":2,"fallbackType":0,"behaviour":2,"removeBehaviour":0,"notificationTrigger":false,"linkMap":[]}]},"props":[],"weight":0,"start":1704067200,"dur":2398377600,"forced":false,"persist":false,"push":false,"repea":false,"ovFreq":false,"userGroup":1,"down3g":true,"idProvider":2,"idExternal":"null","reqs":[],"expedite":true,"customDataProps":[],"reqs2":[],"reqsExecutionType":0,"serverAbTest":{"set":false,"groupId":0},"spots":[{"context":"dbd46787","placement":"1945773f"}],"timestamp":1733912295,"freqCapApplyMode":0},{"id":2,"type":2,"mode":1,"objective":1,"format":1,"targetAppId":0,"version":1,"payload":{"action":{"key":"BUTTON","primary":"https://discord.gg/UTkJ5zE6wX","primaryType":2,"fallbackType":0,"behaviour":2,"removeBehaviour":0,"notificationTrigger":false,"linkMap":[]},"txts":[{"key":"TITLE","track":"826014","txt":"The Spirit Lair!"},{"key":"BUTTON","track":"825974","txt":"Let’s Go!"},{"key":"MESSAGE","track":"825994","txt":"Join the official The Green Spirit Discord server to get the latest updates about our community, events, and this mod as well.\n (Must be 13 or older to join)"}],"imgs":[{"key":"BACKGROUND","track":"395913","url":"https://spiritaccount.net/assets/images/spiritaccount_roundy.png","fallback":1}],"children":[],"actions":[{"key":"BUTTON","primary":"https://discord.gg/UTkJ5zE6wX","primaryType":2,"fallbackType":0,"behaviour":2,"removeBehaviour":0,"notificationTrigger":false,"linkMap":[]}]},"props":[],"weight":0,"start":1704067200,"dur":2398377600,"forced":false,"persist":false,"push":false,"repea":false,"ovFreq":false,"userGroup":1,"down3g":true,"idProvider":2,"idExternal":"null","reqs":[],"expedite":true,"customDataProps":[],"reqs2":[],"reqsExecutionType":0,"serverAbTest":{"set":false,"groupId":0},"spots":[{"context":"dbd46787","placement":"1945773f"}],"timestamp":1733912295,"freqCapApplyMode":0},{"id":15180,"type":3,"mode":0,"objective":0,"format":0,"targetAppId":0,"version":3,"payload":{"txts":[],"imgs":[],"children":[],"actions":[]},"props":[],"weight":0,"start":1471507200,"dur":3155592974,"forced":false,"persist":false,"push":false,"repea":false,"ovFreq":false,"userGroup":1,"down3g":false,"idProvider":17,"idExternal":"null","reqs":[{"index":0,"type":0,"condition":"candycrushsaga://killLiveOpId?liveOpId=GamePortalSodaAppstorePermanent215115_2015-11-05-152053"},{"index":1,"type":0,"condition":"candycrushsaga://killLiveOpId?liveOpId=63a744e1-b344-4a38-a39c-3cf3c8b84fdc-updated"}],"expedite":false,"customDataProps":[],"reqs2":[{"index":0,"type":0,"condition":"candycrushsaga://killLiveOpId?liveOpId=GamePortalSodaAppstorePermanent215115_2015-11-05-152053"},{"index":1,"type":0,"condition":"candycrushsaga://killLiveOpId?liveOpId=63a744e1-b344-4a38-a39c-3cf3c8b84fdc-updated"}],"reqsExecutionType":0,"serverAbTest":{"set":false,"groupId":0},"spots":[],"timestamp":1471509434,"freqCapApplyMode":0},{"id":208152,"type":3,"mode":0,"objective":0,"format":0,"targetAppId":0,"version":2,"payload":{"txts":[],"imgs":[],"children":[],"actions":[]},"props":[],"weight":0,"start":1487894400,"dur":752630400,"forced":false,"persist":true,"push":false,"repea":false,"ovFreq":false,"userGroup":1,"down3g":false,"idProvider":33,"idExternal":"null","reqs":[],"expedite":false,"customDataProps":[],"reqs2":[],"reqsExecutionType":0,"abTest":{"name":"ccsm_ads_monitoring","testCases":[{"groupId":0,"payload":{"txts":[],"imgs":[],"children":[],"actions":[]},"props":[],"reqs":[],"reqsExecutionType":0,"customDataProps":[]},{"groupId":1,"payload":{"txts":[],"imgs":[],"children":[],"actions":[]},"props":[],"reqs":[],"reqsExecutionType":0,"customDataProps":[]},{"groupId":2,"payload":{"txts":[],"imgs":[],"children":[],"actions":[]},"props":[],"reqs":[],"reqsExecutionType":0,"customDataProps":[]}]},"serverAbTest":{"set":false,"groupId":0},"spots":[],"timestamp":1563528650,"freqCapApplyMode":0},{"id":302711,"type":3,"mode":0,"objective":0,"format":0,"targetAppId":0,"version":1,"payload":{"txts":[],"imgs":[],"children":[],"actions":[]},"props":[],"weight":0,"start":1487894400,"dur":752630400,"forced":false,"persist":true,"push":false,"repea":false,"ovFreq":false,"userGroup":1,"down3g":false,"idProvider":33,"idExternal":"null","reqs":[],"expedite":false,"customDataProps":[],"reqs2":[],"reqsExecutionType":0,"abTest":{"name":"CCS_events_2020_5perc","testCases":[{"groupId":0,"payload":{"txts":[],"imgs":[],"children":[],"actions":[]},"props":[],"reqs":[],"reqsExecutionType":0,"customDataProps":[]},{"groupId":1,"payload":{"txts":[],"imgs":[],"children":[],"actions":[]},"props":[],"reqs":[],"reqsExecutionType":0,"customDataProps":[]},{"groupId":2,"payload":{"txts":[],"imgs":[],"children":[],"actions":[]},"props":[],"reqs":[],"reqsExecutionType":0,"customDataProps":[]}]},"serverAbTest":{"set":false,"groupId":0},"spots":[],"timestamp":1585588758,"freqCapApplyMode":0},{"id":302713,"type":3,"mode":0,"objective":0,"format":0,"targetAppId":0,"version":1,"payload":{"txts":[],"imgs":[],"children":[],"actions":[]},"props":[],"weight":0,"start":1487894400,"dur":752630400,"forced":false,"persist":true,"push":false,"repea":false,"ovFreq":false,"userGroup":1,"down3g":false,"idProvider":33,"idExternal":"null","reqs":[],"expedite":false,"customDataProps":[],"reqs2":[],"reqsExecutionType":0,"abTest":{"name":"CCS_triggered_2020_10perc","testCases":[{"groupId":0,"payload":{"txts":[],"imgs":[],"children":[],"actions":[]},"props":[],"reqs":[],"reqsExecutionType":0,"customDataProps":[]},{"groupId":1,"payload":{"txts":[],"imgs":[],"children":[],"actions":[]},"props":[],"reqs":[],"reqsExecutionType":0,"customDataProps":[]}]},"serverAbTest":{"set":false,"groupId":0},"spots":[],"timestamp":1585589024,"freqCapApplyMode":0}],"fCaps":[{"type":4,"mode":0,"cap":1,"period":14400},{"type":5,"mode":0,"cap":1,"period":3600},{"type":5,"mode":2,"cap":15,"period":60},{"type":5,"mode":1,"cap":3,"period":86400}],"killSwitch":[],"ts":1743634140,"purge":false,"remove":[],"failedSegmentMessages":[],"resetKS":true,"resetFC":true,"debug":16,"conf":{"cdns":[{"type":1,"host":"https://bling2.midasplayer.com"},{"type":2,"host":"https://contenido-live.midasplayer.net"},{"type":3,"host":"https://candycrush-live.midasplayer.net"}],"sanitiseQueue":3300,"isProviderUpdated":false}}';
                return json_decode($jsonString, true);
            },
            'IGPApi.getTFC3' => function() { $jsonString = '{"c": [],"n": 86400}'; return json_decode($jsonString, true); },
            'OzzyApi.isEnabledForUser3' => function() { return false; },
            'ApplicationSettingsApi.getSettings' => function() {return json_decode(file_get_contents("/var/www/candycrush/Backend/data/appsettings.json"));},
            'AppDatabaseApi.getAppDatabase' => function() {return json_decode(file_get_contents("/var/www/candycrush/Backend/data/appdb.json", true));},
            'AppCandyCrushAPI.getJsonFileUrl' => function($params) { return $this->getJsonFileUrl($params); },
            'AppSagaApi.getFriendProfiles2' => function() { return $this->getFriendProfiles(); },
            'AppSagaApi.getFriendsTopBonusLevel2' => function() { return $this->getFriendsTopBonusLevel($this->pdo, $this->tokenData); },
            'AppSocialUserApi.getAppFriends3' => function() { return $this->getFriendProfiles(); },
            'AppSocialUserApi.getCurrentUser2' => function() { return $this->getCurrentUser2(); },
            'AppUniverseApi.syncLevels' => function($params) { return syncLevels($params, $this->pdo, $this->sessionKey); },
            'AppUniverseApi.getUniverse3' => function() { return $this->getUniverse3(); },
            'AppSagaApi.getMessages2' => function() { return getUserMessages($this->pdo, $this->userId); },
            'AppSugarTrackApi.syncSugarTrack' => function($params) { return $this->handleSugarTrack($params); },
            'AppSugarTrackApi.syncSugarTrackOnGameEnd' => function($params) { return $this->handleSugarTrack($params); },
            'AppSugarTrackApi.getSugarTrackLevels' => function() {
                return json_decode(file_get_contents("/var/www/candycrush/Backend/data/sugartracklevels.json", true));},
            'AppBlobStoreTranslationsApi.getTranslationsUrls' => function() { return []; },
            'ItemDeliveryApi.getPendingDeliveriesByTarget' => function() { return []; },
            'AppSagaApi.getLevelToplist2' => function($params) { return $this->getLevelToplist2($params); },
            'AppSagaApi.syncCharms' => function() { return []; },
            'TrackingApi.getUniqueACId' => function() { return '29734200290'; },
            'AppCandyCrushAPI.unlimitedLifeTimeLeft' => function() { return 0; },
            'AppCandyCrushAPI.reportGameTriggers' => function() { return false; },
            'AppVirtualCurrencyApi.getBalance' => function() { return getBalance($this->sessionKey, $this->pdo); },
            'AppSagaApi.getAllItems' => function() { return $this->getAllItems(); },
            'AppCandyCrushAPI.getWheelOfBoosterPrize' => function() { return BoosterWheel($this->pdo, $this->sessionKey)['prize']; },
            'AppCandyCrushAPI.getWheelOfBoosterJackpotLevel' => function() { return $this->getWheelOfBoosterJackpotLevel(); },
            'AppCandyCrushAPI.hasActiveWheelOfBooster' => function() { return $this->hasActiveWheelOfBooster(); },
            'AppApi.connectUsingKingdom2' => function($params) { return connectUsingKingdom2($params, $this->pdo); },
            'AppFacebookApi.connectUsingFacebook2' => function($params) { return connectUsingFacebook2($params, $this->pdo); },

            // API Migration
            'SagaApi.gameInitLight' => function($params) { return gameInitLight($this->pdo, $this->sessionKey, $this->language); },
            'SagaApi.gameEnd3' => function($params) { return $this->gameEnd3($params); },
            'SagaApi.getEpisodeChampions' => function($params) { return $this->getEpisodeChampions($params); },
            'SagaApi.gameStart2' => function($params) { return $this->gameStart2($params); },
            'SagaApi.setSoundFx' => function($params) { return $this->setSoundFx($params, $this->pdo); },
            'SagaApi.setSoundMusic' => function($params) { return $this->setSoundMusic($params, $this->pdo); },
            'AppCandyCrushAPI.deliverInitialHardCurrencyGift' => function($params) { return $this->deliverInitialHardCurrencyGift($params, $this->pdo); },
            'CandyCrushAPI.getCandyProperties' => function() { return getCandyProperties($this->pdo, $this->sessionKey); },
            'CandyCrushAPI.setCandyProperty' => function() { return $this->setCandyProperty($params, $this->pdo); },
            
            'AppKingdomApi.isKingdomBasicsEnabled' => function() { return true; },
            'AppKingdomApi.setFullName' => function($params) { return setFullName($params, $this->sessionKey, $this->pdo); },
            'AppKingdomApi.validateEmailAndPassword' => function($params) { return validateEmailAndPassword($params, $this->pdo); },
            'AppKingdomApi.checkAccountStatus' => function($params) { return checkAccountStatus($params, $this->pdo); },
            'AppKingdomApi.setSelectableAvatar' => function($params) { return setSelectableAvatar($params, $this->sessionKey, $this->pdo); },
            'AppKingdomApi.setEmailAndPassword' => function($params) { return setEmailAndPassword($params, $this->sessionKey, $this->pdo); },
            'AppKingdomApi.mergeAccounts' => function($params) { return mergeAccounts($params, $this->pdo); },
            'AppKingdomApi.getAllSelectableAvatars' => function() { return getAllSelectableAvatars(); },
			
            "SocialUserApi.getUsers" => function($params) {
				$users = [];
				foreach ($params[0] as $userId) {
					array_push($users, $this->getUserById($userId));
				}
				return $users;
			},
			
			// Kingdom Profile (CURRENTLY PLACEHOLDERS)
			"BlobStoreTranslationsApi.getTranslationsUrls" => function($params) {
				$translationJSON = null;
				// TODO: archive these JSONs (includes other languages)
				if ($params[0] == 1) {
					$translationJSON = "https://contenido-live.midasplayer.net/tr/achievements.json?_v=1assuam";
				}
				else if ($params[0] == 2) {
					$translationJSON = "https://contenido-live.midasplayer.net/tr/gametriggers.json?_v=lowufp";
				}
				else if ($params[0] == 3) {
					$translationJSON = "https://contenido-live.midasplayer.net/tr/gifting.json?_v=tzx9sz";
				}
				else if ($params[0] == 4) {
					$translationJSON = "https://contenido-live.midasplayer.net/tr/quests.json?_v=1gx91q0";
				}
				else if ($params[0] == 5) {
					$translationJSON = "https://contenido-live.midasplayer.net/tr/kingofthehill.json?_v=1ytnbi8";
				}
				return [$translationJSON];
			},
			
            "UserEncounterApi.setUserEncounterEnabled" => function($params) { return ""; /* DB function */ },
            "UserEncounterApi.getUserEncounters" => function($params) { return $this->getEncounters(); },
            "UserEncounterApi.encounter" => function($params) { return $this->getEncounters(); },
			
            "ProfileCardApi.isProfileCardEnabled" => function() { return true; },
            "ProfileCardApi.getActiveKingAppIds" => function() { return array("kingAppIds" => [17, 23, 25, 26, 28, 32, 33, 46]); },
            "ProfileCardApi.getActiveKingApps" => function() { return $this->getActiveKingApps(); },

            "KingLevelApi.setActionAmount" => function($params) { return ""; /* DB function */ },
            "KingLevelApi.getUserKingLevel" => function() { return $this->getUserKingLevel(); },
            "KingLevelApi.getUserKingLevelByUser" => function($params) { return $this->getUserKingLevel($params); },

            "KingdomAchievementApi.startAchievement" => function($params) { return $this->getAchievementData($params); },
            "KingdomAchievementApi.increaseAchievementDataBalance" => function($params) { return $this->getAchievementData($params); },
            "KingdomAchievementApi.getAchievementsByKingApp" => function($params) { return $this->getAchievementsByKingApp($params); },
            "KingdomAchievementApi.getAchievementsByKingAppAndAchievementType" => function($params) { return $this->getAchievementsByKingApp($params); },
            "KingdomAchievementApi.getAchievementDataList" => function($params) { return $this->getAchievementDataList($params); },
            "KingdomAchievementApi.getAchievementDataListByKingApp" => function($params) { return $this->getAchievementDataList($params); },
            "KingdomAchievementApi.getAchievementDataListByKingAppAndAchievementType" => function($params) { return $this->getAchievementDataList($params); },
        ];
        
        if (isset($methodHandlers[$method])) {
            return $methodHandlers[$method]($params);
        }
        
        return null;
    }
    
    private function handlePurchase($params) {
        $purchaseParams = $params[0];
        $orderItems = $purchaseParams['orderItems'];
        $currency = $purchaseParams['currency'];
        try {
            $this->pdo->beginTransaction();
            $purchaseResult = null;
            foreach ($orderItems as $orderItem) {
                $productPackageType = $orderItem['productPackageType'];
                
                $recipientUserId = null;
                if (isset($orderItem['receiverCoreUserId'])) {
                    $recipientUserId = $orderItem['receiverCoreUserId'];
                }
                
                $purchaseResult = processPurchase($this->pdo, $this->sessionKey, $productPackageType, $recipientUserId);
                if ($purchaseResult['status'] === 'error') {
                    throw new Exception($purchaseResult['error']);
                }
            }
            $this->pdo->commit();
            return $purchaseResult;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return [
                'status' => 'error',
                'error' => $e->getMessage(),
                'isPurchaseForAnotherUser' => false
            ];
        }
    }
    
    private function handlePurchaseFromKing4($params) {
        $packageId = $params[0]; 
        $currencyType = $params[1];
        $userId = $params[3] ?? null;
        
        try {
            $this->pdo->beginTransaction();
            $purchaseResult = purchaseFromKing4($this->pdo, $packageId, $currencyType, $this->sessionKey);
            
            if ($purchaseResult['status'] === 'error') {
                throw new Exception($purchaseResult['error']);
            }
            
            $this->pdo->commit();
            return $purchaseResult;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return [
                'status' => 'error',
                'error' => $e->getMessage(),
                'isPurchaseForAnotherUser' => false
            ];
        }
    }
    
    private function handleUpdateVanityItem($params) {
        $type = $params[0];
        $id = $params[1];
        $timeLeftSec = $params[2];
        return updateVanityItem($this->pdo, $type, $id, $timeLeftSec, $this->sessionKey);
    }

    private function setSoundFx($params, $pdo) {
        $sessionKey = $this->sessionKey;
        $soundFxValue = isset($params[0]) ? ($params[0] === "false" ? 0 : 1) : 1;

        $query = "UPDATE users SET soundFx = :sound 
                    WHERE kingSessionKey = :session OR facebookSessionKey = :session";
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            ':sound' => $soundFxValue,
            ':session' => $sessionKey
        ]);
            
        $rowsAffected = $stmt->rowCount();
            
        return [];
    }

    private function setSoundMusic($params, $pdo) {
        $sessionKey = $this->sessionKey;
        $music = isset($params[0]) ? ($params[0] === "false" ? 0 : 1) : 1;
        
        $query = "UPDATE users SET soundMusic = :sound 
                      WHERE kingSessionKey = :session OR facebookSessionKey = :session";
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            ':sound' => $music,
            ':session' => $sessionKey
        ]);
            
        $rowsAffected = $stmt->rowCount();
            
        return [];
    }

    private function deliverInitialHardCurrencyGift($pdo) {
        $amount = 50;
        $userId = $this->userId;
        $sessionKey = $this->sessionKey;

        $stmt = $pdo->prepare("SELECT candyProperties FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result && !empty($result['candyProperties'])) {
            $properties = json_decode($result['candyProperties'], true);
            if (isset($properties['candyProperties']['introduceHardCurrency']) && 
                $properties['candyProperties']['introduceHardCurrency'] === "true") {
                exit;
            }
        }

        giveHardCurrency($pdo, $sessionKey, $amount);
        setCandyProperty($pdo, 'introduceHardCurrency', 'true', $sessionKey);

        return [];
    }

    private function setCandyProperty($params, $pdo) {
        $arg0 = $params[0];
        $arg1 = $params[1];
        $sessionKey = $this->sessionKey;

        setCandyProperty($pdo, $arg0, $arg1, $sessionKey);
    }

    private function gameEnd3($params) {
        $json = $params[0];
        return gameEnd3($json, $this->pdo, $this->sessionKey);
    }

    private function gameStart2($params) {
        $level = $params[0];
        $episode = $params[1];
        return gameStart2($level, $episode, $this->pdo, $this->sessionKey);
    }

    private function getEpisodeChampions($params) {
        $json = $params[0];
        $jsonString = getEpisodeChampions($json, $this->sessionKey);
        return json_decode($jsonString, true);
    }
    
    private function hasActiveWheelOfBooster() {
        $stmt = $this->pdo->prepare("SELECT last_spin_time FROM users WHERE id = ?");
        $stmt->execute([$this->userId]);
        $lastSpin = $stmt->fetchColumn();
        $cooldownPeriod = 86400;
        
        if (!$lastSpin) {
            return true;
        }
        
        $lastSpinTimestamp = strtotime($lastSpin);
        return (time() - $lastSpinTimestamp) > $cooldownPeriod;
    }

    private function getWheelOfBoosterJackpotLevel() {
        $stmt = $this->pdo->prepare("SELECT wheel_spin_streak FROM users WHERE id = ?");
        $stmt->execute([$this->userId]);
        $winstreak = $stmt->fetchColumn();

        return $winstreak;
    }
    
    private function getJsonFileUrl($params) {
        if (empty($params[0])) {
            return false;
        }
        
        $param = $params[0];
        $param = ltrim($param, '/');
        return "https://candycrush.spiritaccount.net/wfp/" . $param;
    }
    
    private function getFriendProfiles() {
        if (empty($this->tokenData['oauth_token'])) {
            return [];
        }
        
        $socialData = fetchUserSocialData($this->tokenData['oauth_token']);
        $userProfiles = [];

        $userProfile = getUserProfileFromSocialData($this->pdo, $socialData);
        $userProfiles[] = $userProfile;

        $friendProfiles = getFriendsProfiles($this->pdo, $socialData);
        foreach ($friendProfiles as $friendProfile) {
            $userProfiles[] = $friendProfile;
        }

        return $userProfiles;
    }
    
    private function getCurrentUser2() {
        $query = "SELECT id, country, facebookSessionKey, kingSessionKey, oauth_token FROM users 
                  WHERE kingSessionKey = :sessionKey OR facebookSessionKey = :sessionKey";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':sessionKey', $this->sessionKey, PDO::PARAM_STR);
        $stmt->execute();
        $sessionData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$sessionData) {
            return null;
        }
        
        $userId = $sessionData['id'];
        $countryc = $sessionData['country'];
        $facebookSessionKey = $sessionData['facebookSessionKey'] ?? null;
        $kingSessionKey = $sessionData['kingSessionKey'] ?? null;
        $oauthToken = $sessionData['oauth_token'] ?? null;
        
        $socialData = null;
        if ($this->sessionKey === $facebookSessionKey && !empty($oauthToken)) {
            $socialData = fetchUserSocialData($oauthToken);
        }

        if (!empty($socialData)) {
            return $this->buildSocialUserProfile($userId, $countryc, $socialData);
        } else {
            return $this->buildKingUserProfile($userId);
        }
    }
    
    private function getUserById($userId) {
        $query = "SELECT country, facebookSessionKey, oauth_token FROM users WHERE id = :userId";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $sessionData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$sessionData) {
            return null;
        }
        
        $countryc = $sessionData['country'];
        $facebookSessionKey = $sessionData['facebookSessionKey'] ?? null;
        $oauthToken = $sessionData['oauth_token'] ?? null;
        
        $socialData = null;
        if ($facebookSessionKey && !empty($oauthToken)) {
            $socialData = fetchUserSocialData($oauthToken);
        }

        if (!empty($socialData)) {
            return $this->buildSocialUserProfile($userId, $countryc, $socialData);
        } else {
            return $this->buildKingUserProfile($userId);
        }
    }
    
    private function buildSocialUserProfile($userId, $country, $socialData) {
        $currentUserProfile = getUserProfileFromSocialData($this->pdo, $socialData);
        
        return [
            "userId" => $userId,
            "externalUserId" => $userId,
            "name" => $currentUserProfile['name'],
            "firstName" => $currentUserProfile['name'],
            "pic" => $currentUserProfile['pic'],
            "pic100" => $currentUserProfile['picSmall'],
            "country" => $country,
            "lastSignInTime" => time(),
            "friendType" => "NONE",
            "pictureUrls" => [
                $currentUserProfile['pic'],
                $currentUserProfile['picSmall']
            ]
        ];
    }
    
    private function buildKingUserProfile($userId) {
        $query = "SELECT selectedAvatar, username, country, UNIX_TIMESTAMP(lastLogin) as lastSignInTime 
                  FROM users WHERE id = :userId";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $userInfo = $stmt->fetch(PDO::FETCH_ASSOC);

        $avatarId = $userInfo['selectedAvatar'] ?? 1;
        $username = $userInfo['username'] ?? "Guest";
        $country = $userInfo['country'] ?? "US";
        $lastSignInTime = $userInfo['lastSignInTime'] ?? time();

        $avatarUrl = "https://cdn-candycrush.spiritaccount.net/a/a{$avatarId}_100x100.png";
        $avatarUrlSmall = "https://cdn-candycrush.spiritaccount.net/a/a{$avatarId}_50x50.png";

        return [
            "userId" => $userId,
            "externalUserId" => $userId,
            "name" => $username,
            "firstName" => $username,
            "pic" => $avatarUrl,
            "pic100" => $avatarUrl,
            "country" => $country,
            "lastSignInTime" => $lastSignInTime,
            "friendType" => "NONE",
            "pictureUrls" => [
                $avatarUrl,
                $avatarUrlSmall
            ]
        ];
    }
    
    private function getUniverse3() {
        return [
            "episodes" => getUserUniverses($this->userId),
            "unlockedItems" => []
        ];
    }

    private function getFriendsTopBonusLevel($pdo, $tokenData) {
        return getFriendsTopBonusLevel($pdo, $tokenData);
    }
    
    private function handleSugarTrack($params) {
        $sugardrops = $params[0];
        
        $sugarTrack = new KingSugarTrack($this->userId, $this->pdo);
        $sugarTrack->initializeUserRewardData();
        
        if (isset($sugardrops) && $sugardrops > 0) {
            return $sugarTrack->addBalance($sugardrops);
        } else {
            return $sugarTrack->getRewardStatus();
        }
    }
    
    private function getLevelToplist2($params) {
        $episodeId = $params[0];
        $levelId = $params[1];
        
        if (empty($this->accessToken)) {
            return [];
        }
        
        return getLevelTopList($episodeId, $levelId, $this->pdo, $this->accessToken, $this->userId);
    }
    
    private function getAllItems() {
        $rawResult = getCurrentUserItems($this->pdo, $this->sessionKey);
        return $this->convertTypeIdToInteger($rawResult);
    }
    
    private function convertTypeIdToInteger($data) {
        if (is_array($data)) {
            foreach ($data as &$item) {
                if (isset($item['typeId'])) {
                    $item['typeId'] = (int)$item['typeId'];
                }
                if (is_array($item)) {
                    $item = $this->convertTypeIdToInteger($item);
                }
            }
        }
        return $data;
    }

    private function publishGiveLife($params) {
        $recipientIds = $params[0];
        return GiveLife($this->pdo, $this->sessionKey, $recipientIds);
    }  
    
    private function getCurrentUser($pdo, $sessionKey) {
        return getCurrentUser($pdo, $sessionKey);
    }

    private function getUserTokens() {
        return getUserTokens();
    }
    
    private function getUserIdBySessionKey($pdo, $sessionKey) {
        return getUserIdBySessionKey($pdo, $sessionKey);
    }
	
	// Kingdom Profile
    private function getEncounters() {
        return array("coreUserId" => $this->userId, "userEncounterDtos" => [], "typeStatusDtos" => []);
    }
	
    private function getActiveKingApps() {
        return [
            array("id" => 17, "carouselImage" => "https://contenido-live.midasplayer.net/pc/app_3_v3.jpg"),
            array("id" => 23, "carouselImage" => "https://contenido-live.midasplayer.net/pc/petrescue_v5.jpg"),
            array("id" => 25, "carouselImage" => "https://contenido-live.midasplayer.net/pc/app_6_v3.jpg"),
            array("id" => 26, "carouselImage" => "https://contenido-live.midasplayer.net/pc/app_5_v3.jpg"),
            array("id" => 28, "carouselImage" => "https://contenido-live.midasplayer.net/pc/app_4_v3.jpg"),
            array("id" => 32, "carouselImage" => "https://contenido-live.midasplayer.net/pc/app_2_v3.jpg"),
            array("id" => 33, "carouselImage" => "https://contenido-live.midasplayer.net/pc/app_1_v3.jpg"),
            array("id" => 46, "carouselImage" => "https://contenido-live.midasplayer.net/pc/app_46_v1.jpg"),
        ];
    }
	
    private function getUserKingLevel($params = []) {
		$coreUserId = isset($params[0]) ? $params[0] : null;
        return array("kingLevel" => 100, "percentOfCurrentLevel" => 0, "kingLevelActionDtos" => [/*array("kingAppId" => 17, "kingLevelActionTypeId" => 1, "amount" => 1)*/]);
    }
	
    private function getAchievementData($params) {
		$achievementId = $params[0];
        //return array("achievementId" => $achievementId, "startTimeInSec" => 100);
		return ['code' => -32602, 'message' => 'Internal error'];
    }
	
    private function getAchievementsByKingApp($params) {
		$kingAppId = $params[0];
		$achievementTypes = isset($params[1]) ? $params[1] : null;
        return [
			//array("id" => 2, "label" => "test achievement", "fromTimeInSec" => 0, "toTimeInSec" => 100, "completeHours" => 1, "imageUrl" => null, "tasks" => [], "type" => 0, "difficulty" => 1, "status" => 0, "finalReward" => null, "achievementOver" => null),
		];
    }
	
    private function getAchievementDataList($params) {
		$coreUserId = $params[0];
		$kingAppId = isset($params[1]) ? $params[1] : null;
		$achievementTypes = isset($params[2]) ? $params[2] : null;
        return array("coreUserId" => $coreUserId, "achievementDataDtos" => [/*array("achievementId" => 1, "startTimeInSec" => 100)*/]);
    }
}

$isSecure = true;
$sessionKey = $_GET['_session'];
$rawInput = file_get_contents('php://input');

$language = $_SERVER['HTTP_ACCEPT_LANGUAGE'];

$jsonRpcServerTest = new JsonRpcServer($pdo, $sessionKey, $language);
$responses = $jsonRpcServerTest->handleRequests($rawInput);

header('Content-Type: application/json');
echo json_encode($responses, JSON_UNESCAPED_SLASHES);