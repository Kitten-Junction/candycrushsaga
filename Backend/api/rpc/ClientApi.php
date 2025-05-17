<?php
// King RPC Server
// TODO: Re-arrange the methods!!!

include $modules . '/user/userData.php';
include $modules . '/KingSocial.php';
include $modules . '/user/userUniverses.php';
include $modules . '/user/userItems.php';
include $modules . '/KingIAP.php';
include $modules . '/eventManager.php';
include $modules . '/KingMessages.php';
include $modules . '/user/userSugarTrack.php';
require_once $modules . '/KingdomAccount.php';

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
    
    public function __construct($pdo, $sessionKey) {
        $this->pdo = $pdo;
        $this->sessionKey = $sessionKey;
        $this->userData = $this->getCurrentUser($pdo, $sessionKey);
        $this->tokenData = $this->getUserTokens();
        $this->accessToken = $this->tokenData['oauth_token'] ?? null;
        $this->userId = $this->getUserIdBySessionKey($pdo, $sessionKey);
        $this->serverTime = time();
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
            'AppVirtualCurrencyApi.getBalance',
            'AppSagaApi.getAllItems',
            'AppCandyCrushAPI.getWheelOfBoosterPrize',
            'AppCandyCrushAPI.getWheelOfBoosterJackpotLevel',
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
            'TimeApi.getServerTime' => function() { return $this->serverTime; },

            // Analytics methods
            'TrackingApi.track' => function() { return []; },
            'TrackingApi.appTrack' => function() { return []; },
            'TrackingApi.getUniqueACId' => function() { return '29734200290'; },
            'UserMetrics2Api.getUserMetrics' => function() { return []; },
            'AppClientHealthTracking.trackBreakpadCrashReport' => function() { return []; },

            // IAP Methods
            'ProductApi.getUserCurrency' => function() { return 'USD'; },
            'ProductApi.getAllProductPackages' => function() { return getAllProductPackages(); },
            'AppProductApi.getAllProductPackages' => function() { return json_decode(file_get_contents("/var/www/candycrush/Backend/data/mobile_pm.json")); },
            'MercadoClientV3Api.getProductsAndGroups2' => function() { return json_decode(file_get_contents("/var/www/candycrush/Backend/data/mercado.json")); },
            'ProductApi.purchase' => function($params) { return $this->handlePurchase($params); },
            'PurchaseApi.getPendingAnimations' => function() { return []; },
            'AppProductApi.purchaseFromKing4' => function($params) { return $this->handlePurchaseFromKing4($params); },
            'AppVirtualCurrencyApi.getBalance' => function() { return getBalance($this->sessionKey, $this->pdo); },
            'MercadoClientV3Api.getBalanceWithDeltas' => function() { return getBalanceWithDeltas($this->sessionKey, $this->pdo); },
            'IGPApi.getTFC3' => function() { $jsonString = '{"c": [],"n": 86400}'; return json_decode($jsonString, true); },
            'AppBlobStoreTranslationsApi.getTranslationsUrls' => function() { return []; },
            'ItemDeliveryApi.getPendingDeliveriesByTarget' => function() { return []; },

            'SagaApi.getGiveLifeUrlMessage' => function() {
                $jsonString = '{"id":2,"message":"9IfhHpF_PMkCdbXdRsUsDo52lHJygc0Sjmbw7T1X-Mc:MSM4NTkxMjc3NjUwMzgjMTUwMjk4NDE0MzYjZ2l2ZUxpZmUjIzE3NDMzNjc2NTkjMiNnaXZlTGlmZVRvTWFueSM"}';
                return json_decode($jsonString, true);
            },

            // OpenGraphPublisher methods
            'OpenGraphPublisher.publishInGameFeats' => function() { return []; },
            'OpenGraphPublisher.publishCompleteLevel' => function() { return []; },
            'OpenGraphPublisher.publishGiveLife' => function($params) {return $this->publishGiveLife($params); },

            // Settings Api methods
            'ApplicationSettingsApi.getSettings' => function() {return json_decode(file_get_contents("/var/www/candycrush/Backend/data/appsettings.json"));},
            'AppDatabaseApi.getAppDatabase' => function() {return json_decode(file_get_contents("/var/www/candycrush/Backend/data/appdb.json", true));},

            // AppSocialUserApi methods
            'AppSocialUserApi.getAppFriends3' => function() { return $this->getFriendProfiles(); },
            'AppSocialUserApi.getCurrentUser2' => function() { return $this->getCurrentUser2(); },

            // AppUniverseApi methods
            'AppUniverseApi.syncLevels' => function($params) { return syncLevels($params, $this->pdo, $this->sessionKey); },
            'AppUniverseApi.getUniverse3' => function() { return $this->getUniverse3(); },

            // AppSugarTrackApi methods
            'AppSugarTrackApi.syncSugarTrack' => function($params) { return $this->handleSugarTrack($params); },
            'AppSugarTrackApi.syncSugarTrackOnGameEnd' => function($params) { return $this->handleSugarTrack($params); },
            'AppSugarTrackApi.getSugarTrackLevels' => function() {
                return json_decode(file_get_contents("/var/www/candycrush/Backend/data/sugartracklevels.json", true));},

            // AppSagaApi methods
            'AppSagaApi.getLevelToplist2' => function($params) { return $this->getLevelToplist2($params); },
            'AppSagaApi.syncCharms' => function() { return []; },
            'AppSagaApi.getAllItems' => function() { return $this->getAllItems(); },
            'AppSagaApi.getMessages2' => function() { return getUserMessages($this->pdo, $this->userId); },
            'AppSagaApi.getFriendProfiles2' => function() { return $this->getFriendProfiles(); },
            'AppSagaApi.getFriendsTopBonusLevel2' => function() { return $this->getFriendsTopBonusLevel($this->pdo, $this->tokenData); },
            'AppSagaApi.getGiveLifeUrlMessage2' => function() {
                $jsonString = '{"id":4,"message":"9IfhHpF_PMkCdbXdRsUsDo52lHJygc0Sjmbw7T1X-Mc:MSM4NTkxMjc3NjUwMzgjMTUwMjk4NDE0MzYjZ2l2ZUxpZmUjIzE3NDMzNjc2NTkjMiNnaXZlTGlmZVRvTWFueSM"}';
                return json_decode($jsonString, true);
            },
            'AppSagaApi.getRequestLifeUrlMessage2' => function() {
                $jsonString = '{"id":3,"message":"Qy1OuwBKySiXprnfbJEMVq0q25Dp5AkWyNuH32CkGb0:MSM4NTk1MzI2MDE4ODQjMTUwMjk4NDE0MzYjcmVxdWVzdExpZmUjIzE3NDM2MzM4ODQjMiNyZXF1ZXN0TGlmZSM"}';
                return json_decode($jsonString, true);
            },

            'SagaApi.getRequestLifeUrlMessage' => function() {
                $jsonString = '{"id":3,"message":"Qy1OuwBKySiXprnfbJEMVq0q25Dp5AkWyNuH32CkGb0:MSM4NTk1MzI2MDE4ODQjMTUwMjk4NDE0MzYjcmVxdWVzdExpZmUjIzE3NDM2MzM4ODQjMiNyZXF1ZXN0TGlmZSM"}';
                return json_decode($jsonString, true);
            },

            'SagaApi.getRequestUnlockUrlMessage' => function() {
                $jsonString = '{"id":3,"message":"Qy1OuwBKySiXprnfbJEMVq0q25Dp5AkWyNuH32CkGb0:MSM4NTk1MzI2MDE4ODQjMTUwMjk4NDE0MzYjcmVxdWVzdExpZmUjIzE3NDM2MzM4ODQjMiNyZXF1ZXN0TGlmZSM"}';
                return json_decode($jsonString, true);
            },

            // extremely ass but just life man
            'FacebookEventTracking.trackNotificationSent4' => function($userId = null, $recipientIds = [], $notificationType = null, $method = null, $targetIds = []) {
                if ($method === "requestLifeFromMany" && !empty($recipientIds)) {
                    return $this->requestLife($recipientIds);
                }
                return [];
            },

            // AppCandyCrushAPI methods
            'AppCandyCrushAPI.isPayingUser' => function() { return false; },
            'AppCandyCrushAPI.unlimitedLifeTimeLeft' => function() { return 0; },
            'AppCandyCrushAPI.reportGameTriggers' => function() { return false; },
            'AppCandyCrushAPI.getWheelOfBoosterPrize' => function() { return BoosterWheel($this->pdo, $this->sessionKey)['prize']; },
            'AppCandyCrushAPI.getWheelOfBoosterJackpotLevel' => function() { return $this->getWheelOfBoosterJackpotLevel(); },
            'AppCandyCrushAPI.hasActiveWheelOfBooster' => function() { return $this->hasActiveWheelOfBooster(); },
            'AppCandyCrushAPI.syncLevelAttempts' => function() {
                $jsonString = '{"currentLevelNumber":0,"currentAttempts":0,"firstTryLevels":[0,10,78,79,80,94,109,119,120,121,122,123,124,138,140,153,154,1,2,3,4,5,6,7,8,11,13,15,16,17,18,20,21,22,23,24,25,28,29,30,31,32,34,35,36,37,38,39,40,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,65,66,67,69,71,72,73,74,75,82,83,84,85,86,90,91,92,93,95,96,97,98,99,100,101,102,104,105,107,108,110,111,112,113,114,115,116,117,125,126,127,128,129,130,132,134,135,137,139,141,142,144,145,146,148,149,150,151,152,155,156,157,158,160,161,162,163,164,165,166,167,168,169,170,171,173]}';
                return json_decode($jsonString, true);
            },
            'AppCandyCrushAPI.getJsonFileUrl' => function($params) { return $this->getJsonFileUrl($params); },
            'AppCandyCrushAPI.isPayingUserInApp' => function() { return false; },
            'CandyCrushAPI.getBoosterGiftUrlMessage' => function() {
                $jsonString = '{"id":3,"message":"Qy1OuwBKySiXprnfbJEMVq0q25Dp5AkWyNuH32CkGb0:MSM4NTk1MzI2MDE4ODQjMTUwMjk4NDE0MzYjcmVxdWVzdExpZmUjIzE3NDM2MzM4ODQjMiNyZXF1ZXN0TGlmZSM"}';
                return json_decode($jsonString, true);
            },

            // Vanity API
            'CandyCrushVanityItemApi.updateVanityItem' => function($params) { return $this->handleUpdateVanityItem($params); },
            'CandyCrushVanityItemApi.getAllUserVanityItems' => function() { return getAllUserVanityItems($this->pdo, $this->sessionKey); },

            // Misc methods
            'OzzyApi.isEnabledForUser3' => function() { return false; },
            'AppPlayerCommunityApi.isPlayerCommunityEnabled' => function() { return false; },
            'CandyCrushTemporaryUserDataApi.getData' => function() { return []; },
            'CandyCrushFeatureStatusApi.getEnabledFeatures' => function() { return []; },
            'AppAbTestApi.getAppUserAbCases' => function() { return $this->getAbTestValues(); },
            'AppLiveTaskApi.getAndDeleteMessages' => function() { return []; },
            'ServiceLayerApi.getMessages4' => function() {
                $jsonString = '{"msgs":[{"id":1,"type":2,"mode":1,"objective":1,"format":1,"targetAppId":0,"version":1,"payload":{"action":{"key":"BUTTON","primary":"https://thegreenspirit.serv00.net/","primaryType":2,"fallbackType":0,"behaviour":2,"removeBehaviour":0,"notificationTrigger":false,"linkMap":[]},"txts":[{"key":"TITLE","track":"826014","txt":"Thanks for playing!"},{"key":"BUTTON","track":"825974","txt":"More projects"},{"key":"MESSAGE","track":"825994","txt":"Hey everyone! It’s idk speaking! Thanks for trying out our mod, lots of research and patience went into it. Hope you enjoy it!\n- TGS, Inc. Development"}],"imgs":[{"key":"BACKGROUND","track":"395913","url":"http://candycrush.spiritaccount.net/images/message/gurumin.png","fallback":1}],"children":[],"actions":[{"key":"BUTTON","primary":"https://thegreenspirit.serv00.net/","primaryType":2,"fallbackType":0,"behaviour":2,"removeBehaviour":0,"notificationTrigger":false,"linkMap":[]}]},"props":[],"weight":0,"start":1704067200,"dur":2398377600,"forced":false,"persist":false,"push":false,"repea":false,"ovFreq":false,"userGroup":1,"down3g":true,"idProvider":2,"idExternal":"null","reqs":[],"expedite":true,"customDataProps":[],"reqs2":[],"reqsExecutionType":0,"serverAbTest":{"set":false,"groupId":0},"spots":[{"context":"dbd46787","placement":"1945773f"}],"timestamp":1733912295,"freqCapApplyMode":0},{"id":2,"type":2,"mode":1,"objective":1,"format":1,"targetAppId":0,"version":1,"payload":{"action":{"key":"BUTTON","primary":"https://discord.gg/UTkJ5zE6wX","primaryType":2,"fallbackType":0,"behaviour":2,"removeBehaviour":0,"notificationTrigger":false,"linkMap":[]},"txts":[{"key":"TITLE","track":"826014","txt":"The Spirit Lair!"},{"key":"BUTTON","track":"825974","txt":"Let’s Go!"},{"key":"MESSAGE","track":"825994","txt":"Join the official The Green Spirit Discord server to get the latest updates about our community, events, and this mod as well.\n (Must be 13 or older to join)"}],"imgs":[{"key":"BACKGROUND","track":"395913","url":"https://spiritaccount.net/assets/images/spiritaccount_roundy.png","fallback":1}],"children":[],"actions":[{"key":"BUTTON","primary":"https://discord.gg/UTkJ5zE6wX","primaryType":2,"fallbackType":0,"behaviour":2,"removeBehaviour":0,"notificationTrigger":false,"linkMap":[]}]},"props":[],"weight":0,"start":1704067200,"dur":2398377600,"forced":false,"persist":false,"push":false,"repea":false,"ovFreq":false,"userGroup":1,"down3g":true,"idProvider":2,"idExternal":"null","reqs":[],"expedite":true,"customDataProps":[],"reqs2":[],"reqsExecutionType":0,"serverAbTest":{"set":false,"groupId":0},"spots":[{"context":"dbd46787","placement":"1945773f"}],"timestamp":1733912295,"freqCapApplyMode":0},{"id":15180,"type":3,"mode":0,"objective":0,"format":0,"targetAppId":0,"version":3,"payload":{"txts":[],"imgs":[],"children":[],"actions":[]},"props":[],"weight":0,"start":1471507200,"dur":3155592974,"forced":false,"persist":false,"push":false,"repea":false,"ovFreq":false,"userGroup":1,"down3g":false,"idProvider":17,"idExternal":"null","reqs":[{"index":0,"type":0,"condition":"candycrushsaga://killLiveOpId?liveOpId=GamePortalSodaAppstorePermanent215115_2015-11-05-152053"},{"index":1,"type":0,"condition":"candycrushsaga://killLiveOpId?liveOpId=63a744e1-b344-4a38-a39c-3cf3c8b84fdc-updated"}],"expedite":false,"customDataProps":[],"reqs2":[{"index":0,"type":0,"condition":"candycrushsaga://killLiveOpId?liveOpId=GamePortalSodaAppstorePermanent215115_2015-11-05-152053"},{"index":1,"type":0,"condition":"candycrushsaga://killLiveOpId?liveOpId=63a744e1-b344-4a38-a39c-3cf3c8b84fdc-updated"}],"reqsExecutionType":0,"serverAbTest":{"set":false,"groupId":0},"spots":[],"timestamp":1471509434,"freqCapApplyMode":0},{"id":208152,"type":3,"mode":0,"objective":0,"format":0,"targetAppId":0,"version":2,"payload":{"txts":[],"imgs":[],"children":[],"actions":[]},"props":[],"weight":0,"start":1487894400,"dur":752630400,"forced":false,"persist":true,"push":false,"repea":false,"ovFreq":false,"userGroup":1,"down3g":false,"idProvider":33,"idExternal":"null","reqs":[],"expedite":false,"customDataProps":[],"reqs2":[],"reqsExecutionType":0,"abTest":{"name":"ccsm_ads_monitoring","testCases":[{"groupId":0,"payload":{"txts":[],"imgs":[],"children":[],"actions":[]},"props":[],"reqs":[],"reqsExecutionType":0,"customDataProps":[]},{"groupId":1,"payload":{"txts":[],"imgs":[],"children":[],"actions":[]},"props":[],"reqs":[],"reqsExecutionType":0,"customDataProps":[]},{"groupId":2,"payload":{"txts":[],"imgs":[],"children":[],"actions":[]},"props":[],"reqs":[],"reqsExecutionType":0,"customDataProps":[]}]},"serverAbTest":{"set":false,"groupId":0},"spots":[],"timestamp":1563528650,"freqCapApplyMode":0},{"id":302711,"type":3,"mode":0,"objective":0,"format":0,"targetAppId":0,"version":1,"payload":{"txts":[],"imgs":[],"children":[],"actions":[]},"props":[],"weight":0,"start":1487894400,"dur":752630400,"forced":false,"persist":true,"push":false,"repea":false,"ovFreq":false,"userGroup":1,"down3g":false,"idProvider":33,"idExternal":"null","reqs":[],"expedite":false,"customDataProps":[],"reqs2":[],"reqsExecutionType":0,"abTest":{"name":"CCS_events_2020_5perc","testCases":[{"groupId":0,"payload":{"txts":[],"imgs":[],"children":[],"actions":[]},"props":[],"reqs":[],"reqsExecutionType":0,"customDataProps":[]},{"groupId":1,"payload":{"txts":[],"imgs":[],"children":[],"actions":[]},"props":[],"reqs":[],"reqsExecutionType":0,"customDataProps":[]},{"groupId":2,"payload":{"txts":[],"imgs":[],"children":[],"actions":[]},"props":[],"reqs":[],"reqsExecutionType":0,"customDataProps":[]}]},"serverAbTest":{"set":false,"groupId":0},"spots":[],"timestamp":1585588758,"freqCapApplyMode":0},{"id":302713,"type":3,"mode":0,"objective":0,"format":0,"targetAppId":0,"version":1,"payload":{"txts":[],"imgs":[],"children":[],"actions":[]},"props":[],"weight":0,"start":1487894400,"dur":752630400,"forced":false,"persist":true,"push":false,"repea":false,"ovFreq":false,"userGroup":1,"down3g":false,"idProvider":33,"idExternal":"null","reqs":[],"expedite":false,"customDataProps":[],"reqs2":[],"reqsExecutionType":0,"abTest":{"name":"CCS_triggered_2020_10perc","testCases":[{"groupId":0,"payload":{"txts":[],"imgs":[],"children":[],"actions":[]},"props":[],"reqs":[],"reqsExecutionType":0,"customDataProps":[]},{"groupId":1,"payload":{"txts":[],"imgs":[],"children":[],"actions":[]},"props":[],"reqs":[],"reqsExecutionType":0,"customDataProps":[]}]},"serverAbTest":{"set":false,"groupId":0},"spots":[],"timestamp":1585589024,"freqCapApplyMode":0}],"fCaps":[{"type":4,"mode":0,"cap":1,"period":14400},{"type":5,"mode":0,"cap":1,"period":3600},{"type":5,"mode":2,"cap":15,"period":60},{"type":5,"mode":1,"cap":3,"period":86400}],"killSwitch":[],"ts":1743634140,"purge":false,"remove":[],"failedSegmentMessages":[],"resetKS":true,"resetFC":true,"debug":16,"conf":{"cdns":[{"type":1,"host":"https://bling2.midasplayer.com"},{"type":2,"host":"https://contenido-live.midasplayer.net"},{"type":3,"host":"https://candycrush-live.midasplayer.net"}],"sanitiseQueue":3300,"isProviderUpdated":false}}';
                return json_decode($jsonString, true);
            },
            'AppApi.trackAppStart11' => function() { return []; },

            // Kingdom methods
            'AppApi.connectUsingKingdom2' => function($params) { return connectUsingKingdom2($params, $this->pdo); },
            'AppKingAccountApi.getCurrentAccount' => function() { return $this->getCurrentKingUser(); },
            'AppFacebookApi.connectUsingFacebook2' => function($params) { return connectUsingFacebook2($params, $this->pdo); },
            'AppKingdomApi.isKingdomBasicsEnabled' => function() { return true; },
            'AppKingdomApi.setFullName' => function($params) { return setFullName($params, $this->sessionKey, $this->pdo); },
            'AppKingdomApi.validateEmailAndPassword' => function($params) { return validateEmailAndPassword($params, $this->pdo); },
            'AppKingdomApi.checkAccountStatus' => function($params) { return checkAccountStatus($params, $this->pdo); },
            'AppKingdomApi.sendRetrievePasswordEmail' => function($params) { return sendRetrievePasswordEmail($params, $this->pdo); },
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

    private function getAbTestValues()
    {
        $ABTestNames = [
            "ccsm_unlimited_life_campaign_final" => ["version" => -1, "caseNum" => 0],
            "ccsx_life_pricetest" => ["version" => -1, "caseNum" => 0],
            "ccsx_collab_pricetest" => ["version" => 1, "caseNum" => 1],
            "ccsm_reboarding_map2_cutscene" => ["version" => -1, "caseNum" => 0],
            "ccsm_reboarding_saga_progress_cutscene2" => ["version" => -1, "caseNum" => 0],
            "ccsm_reboarding_saga_progress_cutscene_texts" => ["version" => -1, "caseNum" => 0],
            "ccsm_move_tracking_enabled" => ["version" => 5, "caseNum" => 0],
            "ccsm_revamp_bounce" => ["version" => -1, "caseNum" => 0],
            "ccsm_seniority_v3" => ["version" => -1, "caseNum" => 0],
            "ccsm_dynamic_egp_popup_v3" => ["version" => 3, "caseNum" => 1],
            "ccsm_click_to_switch_v2" => ["version" => -1, "caseNum" => 0],
            "ccsm_faster_fall_speed_v2" => ["version" => -1, "caseNum" => 0],
            "ccsm_onboarding_loadingscreen_tagline_category" => ["version" => 2, "caseNum" => 8],
            "ccsm_in_app_push_notification_question" => ["version" => -1, "caseNum" => 0],
            "ccsm_use_season3_hard_branding_skin" => ["version" => -1, "caseNum" => 0],
            "ccsm_revamp_fish" => ["version" => -1, "caseNum" => 0],
            "ccsm_prebooster_effect" => ["version" => -1, "caseNum" => 0],
            "ccsm_live_tasks_v1" => ["version" => 3, "caseNum" => 1],
            "ccsm_toast_message_plugin" => ["version" => 1, "caseNum" => 1],
            "ccsm_first_attempt_egp" => ["version" => -1, "caseNum" => 0],
            "ccsm_ads_facebook_static_ad" => ["version" => -1, "caseNum" => 0],
            "ccsm_tap_to_skip_v2" => ["version" => -1, "caseNum" => 0],
            "ccsm_inventory_v2" => ["version" => 3, "caseNum" => 1],
            "ccsm_gamelogic_frog_stomach_size" => ["version" => -1, "caseNum" => 0],
            "ccsm_cascade_counter" => ["version" => -1, "caseNum" => 0],
            "ccsm_weighted_candycolors_v2" => ["version" => -1, "caseNum" => 0],
            "ccsm_end_of_episode_reward" => ["version" => -1, "caseNum" => 0],
            "ccsm_eoe_reward_bank_toast" => ["version" => -1, "caseNum" => 0],
            "ccsm_ads_login_calendar_spinner" => ["version" => -1, "caseNum" => 0],
            "ccsm_ads_buy_lives_refresh" => ["version" => -1, "caseNum" => 0],
            "ccsm_navigation_feature_panel" => ["version" => 1, "caseNum" => 1],
            "ccsm_navigation_progress_animation_test" => ["version" => -1, "caseNum" => 0],
            "ccsm_revamp_pepperbomb_v2" => ["version" => -1, "caseNum" => 0],
            "ccsm_kream" => ["version" => -1, "caseNum" => 0],
            "ccsm_end_of_content_celebration" => ["version" => 1, "caseNum" => 1],
            "ccsm_special_round" => ["version" => -1, "caseNum" => 0],
            "ccsm_verify_file_hash" => ["version" => 0, "caseNum" => 1],
            "ccsm_verify_zip_files2" => ["version" => 2, "caseNum" => 1],
            "ccsm_track_device_orientation" => ["version" => -1, "caseNum" => 0],
            "ccsm_new_play_button_ad" => ["version" => -1, "caseNum" => 0],
            "ccsm_special_round_v2" => ["version" => -1, "caseNum" => 0],
            "ccsm_buddy_challenge_quest_duration" => ["version" => -1, "caseNum" => 0],
            "ccsm_ads_egp_reboot" => ["version" => -1, "caseNum" => 0],
            "ccsm_servicelayer_use_deferred_callback_semantics" => ["version" => 5, "caseNum" => 1],
            "ccsm_consolation_prize_v2" => ["version" => -1, "caseNum" => 0],
            "ccsm_consolation_prize_reward" => ["version" => -1, "caseNum" => 0],
            "ccsm_episode_trophy" => ["version" => -1, "caseNum" => 0],
            "ccsm_gifting2_v1" => ["version" => 2, "caseNum" => 1],
            "ccsm_level_timeline_cutscene" => ["version" => -1, "caseNum" => 0],
            "ccsm_recovery_state" => ["version" => 4, "caseNum" => 2],
            "ccsm_fps_throttling" => ["version" => -1, "caseNum" => 0],
            "ccsm_jelly_hint" => ["version" => -1, "caseNum" => 0],
            "ccsm_coconut_wheel_new_behaviour" => ["version" => 1, "caseNum" => 1],
            "ccsm_ads_curtain" => ["version" => -1, "caseNum" => 0],
            "ccsm_mj_game_speed" => ["version" => -1, "caseNum" => 0],
            "ccsm_mj_fish_speed" => ["version" => -1, "caseNum" => 0],
            "ccsm_ingame_extra_boost" => ["version" => -1, "caseNum" => 0],
            "ccsm_map_quick_focus" => ["version" => 1, "caseNum" => 1],
            "ccsm_last_life_notifier" => ["version" => -1, "caseNum" => 0],
            "ccsm_first_attempt_one_liner" => ["version" => -1, "caseNum" => 0],
            "ccsm_first_attempt_reset" => ["version" => -1, "caseNum" => 0],
            "ccsm_post_level_menu_lose_message" => ["version" => -1, "caseNum" => 0],
            "ccsm_sugartrack_toast_message" => ["version" => -1, "caseNum" => 0],
            "ccsm_hard_level_hud" => ["version" => -1, "caseNum" => 0],
            "ccsm_mj_betterhints_delay" => ["version" => -1, "caseNum" => 0],
            "ccsm_mj_betterhints_all" => ["version" => -1, "caseNum" => 0],
            "ccsm_buff_buddies" => ["version" => -1, "caseNum" => 0],
            "ccsm_etl" => ["version" => -1, "caseNum" => 0],
            "ccsm_egp_booster_wheel_freespin_cooldown_choice" => ["version" => -1, "caseNum" => 0],
            "ccsm_hard_super_hard_post_game_anim" => ["version" => -1, "caseNum" => 0],
            "ccsm_remove_etl" => ["version" => -1, "caseNum" => 0],
            "ccsm_tony_toast" => ["version" => -1, "caseNum" => 0],
            "ccsm_fish_match_behaviour" => ["version" => -1, "caseNum" => 0],
            "ccsm_direct_message_evaluation_v2" => ["version" => 0, "caseNum" => 1],
            "ccsm_main_menu_tvshow" => ["version" => 3, "caseNum" => 1],
            "ccsm_remove_play_button_ad" => ["version" => -1, "caseNum" => 0],
            "ccsm_recommended_ingame_booster_2" => ["version" => -1, "caseNum" => 0],
            "ccsm_onboarding_facebook_connect_button_custom_reach_experiment" => ["version" => -1, "caseNum" => 0],
            "ccsm_onboarding_go_to_first_tutorial_directly" => ["version" => 1, "caseNum" => 3],
            "ccsm_onboarding_tutorials_v3" => ["version" => 1, "caseNum" => 1],
            "toothfairy_unlock_mobile" => ["version" => 4, "caseNum" => 1],
            "rating_popup_mobile" => ["version" => 2, "caseNum" => 1],
            "push_notification_mobile" => ["version" => 6, "caseNum" => 1],
            "ccsm_multiple_life_pools_facebook" => ["version" => -1, "caseNum" => 0],
            "pass_beat_friends_mobile" => ["version" => -1, "caseNum" => 0],
            "ccsm_egp_facelift" => ["version" => 1, "caseNum" => 1],
            "ccsx_moonstruck_booster" => ["version" => 1, "caseNum" => 1],
            "ccsm_egp_freeze_dreamworld" => ["version" => -1, "caseNum" => 0],
            "ccsx_unlimited_life_forever" => ["version" => -1, "caseNum" => 0],
            "ccsm_egp_confirm_purchase" => ["version" => -1, "caseNum" => 0],
            "ccsm_hc_20gb" => ["version" => -1, "caseNum" => 0],
            "ccsc_conversion_offer_v2" => ["version" => -1, "caseNum" => 0],
            "ccsm_kingdom_introduction" => ["version" => -1, "caseNum" => 0],
            "ccsm_stable_before_matching_v2" => ["version" => -1, "caseNum" => 0],
            "ccsx_sugar_track" => ["version" => 6, "caseNum" => 3],
            "ccsx_semi_smart_fish" => ["version" => -1, "caseNum" => 0],
            "ccsm_booster_shuffle" => ["version" => -1, "caseNum" => 0],
            "ccsx_booster_ufo_ingame" => ["version" => -1, "caseNum" => 0],
            "ccsx_booster_striped_brush" => ["version" => -1, "caseNum" => 0],
            "ccsm_1rmb_goldpack_tencent" => ["version" => -1, "caseNum" => 0],
            "ccsm_3rmb_goldpack_tencent" => ["version" => -1, "caseNum" => 0],
            "ccsm_3rmb_starterpack_tencent" => ["version" => -1, "caseNum" => 0],
            "ccsm_different_price_higher" => ["version" => -1, "caseNum" => 0],
            "ccsm_topbar_ui_interface" => ["version" => 1, "caseNum" => 1],
            "ccsm_candy_bank_2" => ["version" => -1, "caseNum" => 0],
            "ccsm_candy_shop_title" => ["version" => -1, "caseNum" => 0],
            "ccsm_unlimited_life_products" => ["version" => -1, "caseNum" => 0],
            "ccsm_alternative_ufo_matches" => ["version" => -1, "caseNum" => 0],
            "ccsm_adaptive_difficulty" => ["version" => -1, "caseNum" => 0],
            "ccsx_alt_colorbomb_wrapped" => ["version" => -1, "caseNum" => 0],
            "ccsm_egp_booster_wheel2" => ["version" => -1, "caseNum" => 0],
            "ccsx_ufo_activation_on_seeding" => ["version" => 1, "caseNum" => 1],
            "ccsm_ufo_serverside_seeding" => ["version" => -1, "caseNum" => 0],
            "ccsm_emphasized_toplist" => ["version" => 5, "caseNum" => 1],
            "ccsx_hard_levels_part3" => ["version" => -1, "caseNum" => 0],
            "ccsx_ticket_quest" => ["version" => -1, "caseNum" => 0],
            "ccsm_emphasized_toplist_bank_ad" => ["version" => 4, "caseNum" => 1],
            "ccsm_loading_screen_tips" => ["version" => -1, "caseNum" => 0],
            "ccsm_hard_level_pins" => ["version" => -1, "caseNum" => 0],
            "ccsm_new_candy_shop" => ["version" => -1, "caseNum" => 0],
            "ccsm_remove_retry" => ["version" => -1, "caseNum" => 0],
            "ccsm_remove_dreamworld" => ["version" => -1, "caseNum" => 0],
            "ccsm_emphasized_win_banner" => ["version" => 2, "caseNum" => 1],
            "ccsm_collaboration_lock_type" => ["version" => 3, "caseNum" => 1],
            "ccsm_close_fail_confirmation" => ["version" => 3, "caseNum" => 1],
            "pass_beat_friends_mobile_facebook" => ["version" => -1, "caseNum" => 0],
            "ccsm_friendselector_facebook" => ["version" => -1, "caseNum" => 0],
            "ccsm_conversion_offer_boosters_facebook" => ["version" => -1, "caseNum" => 0],
            "ccsm_conversion_offer_unlimited_life_egp_facebook" => ["version" => 5, "caseNum" => 1],
            "ccsm_hc_conversion_offers_googleplay_facebook" => ["version" => -1, "caseNum" => 0],
            "ccsm_hc_conversion_offers_2_googleplay_facebook" => ["version" => 2, "caseNum" => 1],
            "ccsm_offline_hc_googleplay_facebook" => ["version" => -1, "caseNum" => 0],
            "ccsm_pre_level_boosters_googleplay_facebook" => ["version" => -1, "caseNum" => 0],
            "ccsm_green_candy_v2_googleplay_facebook" => ["version" => -1, "caseNum" => 0],
            "ccsm_paradisebay_portal_googleplay_facebook" => ["version" => 1, "caseNum" => 3],
            "ccsm_smart_ufo_googleplay_facebook" => ["version" => -1, "caseNum" => 0],
            "monocle_101_enabled" => ["version" => -1, "caseNum" => 0]
        ];
        
        return $ABTestNames;
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

    private function getCurrentKingUser() {
        $query = "SELECT id FROM users 
                  WHERE kingSessionKey = :sessionKey OR facebookSessionKey = :sessionKey";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':sessionKey', $this->sessionKey, PDO::PARAM_STR);
        $stmt->execute();
        $sessionData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$sessionData) {
            return null;
        }
        
        $userId = $sessionData['id'];

        return $this->buildCurrentKingUserProfile($userId);
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
    
    private function buildCurrentKingUserProfile($userId) {
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
            "coreUserId" => $userId,
            "toSAndPPAcceptanceDto" => [
                "acceptedVersion" => 3,
                "latestVersion" => 4,
                "latestToSUrl" => "https://king.com/termsAndConditions",
                "latestPPUrl" => "https://king.com/privacyPolicy"
            ],
            "avatarUploadEnabled" => true,
            "editable" => true,
            "name" => $username,
            "avatarUrlSmall" => $avatarUrlSmall,
            "bigAvatarUrl" => $avatarUrl,
            "dateOfBirthKnown" => false,
            "dateOfBirthRequired" => false,
            "ageGateStateId" => 1
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
        
        return getLevelTopList($episodeId, $levelId, $this->pdo, $this->accessToken);
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
        //return GiveLife($this->pdo, $this->sessionKey, $recipientIds);
        return SendMessage($this->pdo, $this->sessionKey, $recipientIds, 'LIFE_GIFT');
    }

    private function requestLife($recipientIds) {
        return SendMessage($this->pdo, $this->sessionKey, $recipientIds, 'LIFE_REQUEST');
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

$jsonRpcServer = new JsonRpcServer($pdo, $sessionKey);
$responses = $jsonRpcServer->handleRequests($rawInput);

header('Content-Type: application/json');
echo json_encode($responses, JSON_UNESCAPED_SLASHES);