<?php

// App configuration
define("APP_ID", "1234");
define("APP_SECRET", "1234");

// JSON RPC Endpoint, EDIT THE SERVER URL TO YOURS!
// Change to JsonRpcTest in development branch
// Change to ClientApi in release
define("JSONRPCEP", "https://candycrush.spiritaccount.net/rpc/JsonRpcTest");

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

$sessionKey = $_GET['_session'];

// External paths (URLs)
$hostname = 'candycrush.spiritaccount.net';
$hostURL = 'https://candycrush.spiritaccount.net';
$cdnURL = 'https://candycrush.spiritaccount.net/swf';
$apiURL = 'https://candycrush.spiritaccount.net/api/ClientApi';

// Internal paths
$backendPath = "../Backend";
$frontendPath = "../Frontend";
$levelFolder = $backendPath . '/data/level_data';
$userFolder = $backendPath . '/data/user_data';
$modules = $backendPath . '/modules';
$langFolder = $backendPath . '/data/lang_data';

// Database module
include $backendPath . '/database.php';

function generateRandomString($length = 10) {
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-_';
	$charactersLength = strlen($characters);
	$randomString = '';
	for ($i = 0; $i < $length; $i++) {
		$randomString .= $characters[random_int(0, $charactersLength - 1)];
	}
	return $randomString;
}

function getABTestCases() {
	global $backendPath;
	$map = json_decode(file_get_contents($backendPath . '/data/ab_tests.json'));
	return $map;
}

function getSeasonalBG($hostname, $isSecure) {
    $currentMonth = (int)date('m');
    $currentDay = (int)date('d');
    
    $baseUrl = ($isSecure ? "https" : "http") . "://" . $hostname . "/images/backgrounds/";
    $randomString = generateRandomString();
    
    $imageName = "bg-ccs-gradient.jpg";
    
    if ($currentMonth == 10) {
        $imageName = "bg-ccs-halloween.jpg";
    } elseif ($currentMonth == 12) {
        $imageName = "bg-ccs-xmas.jpg";
    }
    
    return $baseUrl . $imageName . "?_v=" . $randomString;
}

function callJsonRpc($method, $params = [], $sessionKey1 = null, $customId = 1, $endpoint = JSONRPCEP, $acceptLanguage = null) {
    $url = $sessionKey1 ? $endpoint . '?_session=' . $sessionKey1 : $endpoint;
    
    $request = [
        [
            'jsonrpc' => '2.0',
            'method' => $method,
            'params' => $params,
            'id' => $customId
        ]
    ];
    
    $headers = ['Content-Type: application/json'];
    if ($acceptLanguage) {
        $headers[] = 'Accept-Language: ' . $acceptLanguage;
    }
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_COOKIE, session_name() . '=' . session_id());
    
    $response = curl_exec($ch);
    
    if (curl_errno($ch)) {
        throw new Exception('cURL Error: ' . curl_error($ch));
    }
    
    curl_close($ch);
    
    $result = json_decode($response, true);
    
    if (isset($result[0]['error'])) {
        throw new Exception('JSON-RPC Error: ' . $result[0]['error']['message']);
    }
    
    return $result[0]['result'] ?? null;
}

/*
 * Product & Product Packages Manager
 * 
 * Configure prices at /data/product_prices.json on backend
 *
 *
 * Prices legend (since I can't place it in the config):
 *
 * - productType: Product package name
 * - productId: Product package ID
 * - price: Fixed price (acts as a "sale price" for `listPrice` if it's not equal)
 * - listPrice: Initial price (acts as a "original price" for `price` if its not equal)
 * - currency: Currency ID, can be one of the real world currencies for real money, or use KHC for Gold Bars
 *    + Please add 2 extra zeros to the real world currency packages, as they count as cents, not actual dollars.
 * - items: An array of products, each object containing:
 *    + itemType: The product ID (can be found in com.king.constants.ItemType)
 *    + discountFactorPercent: Discount percentage (not sure how this works exactly) (Optional, default 0)
 *    + deliverData: The data that you will receive if the purchase goes through
 */

function getAllProductPackages() {
	global $backendPath;
    $productPackageMappings = json_decode(file_get_contents($backendPath . '/data/product_mappings.json'), true);
    $productPackagePrices = json_decode(file_get_contents($backendPath . '/data/product_prices.json'), true);
    $getNameIdPairs = function($mappings) {
        $pairs = [];
        foreach ($mappings as $key => $value) {
            if (!is_numeric($key)) {
                $pairs[$key] = $value;
            }
        }
        return $pairs;
    };
    $packages = [];
    foreach ($getNameIdPairs($productPackageMappings) as $pName => $pId) {
		foreach ($productPackagePrices as $productPackageData) {
			if ($productPackageData['productId'] === $pId && $productPackageData['productType'] === $pName) {
				$g = array("type" => $pId, "products" => isset($productPackageData['items']) ? $productPackageData['items'] : []);
				
				$defaultCurrency = 'USD';
				$currency = isset($productPackageData['currency']) ? $productPackageData['currency'] : $defaultCurrency;
				
				if (isset($productPackageData['currency']) && $productPackageData['currency'] === "KHC") {
					$price = isset($productPackageData['price']) ? $productPackageData['price'] * 100 : 0;
					$listPrice = isset($productPackageData['listPrice']) ? $productPackageData['listPrice'] * 100 : 0;
				} else {
					$price = isset($productPackageData['price']) ? $productPackageData['price'] : 0;
					$listPrice = isset($productPackageData['listPrice']) ? $productPackageData['listPrice'] : 0;
				}
				
				$g['prices'] = [array("cents" => $price, "currency" => $currency)];
				$g['listPrices'] = [array("cents" => $listPrice, "currency" => $currency)];
				
				if (isset($g['products']) && is_array($g['products'])) {
					foreach ($g['products'] as $k => $v) {
						if (!isset($g['products'][$k]['discountFactorPercent'])) {
							$g['products'][$k]['discountFactorPercent'] = 0;
						}
						if (!isset($g['products'][$k]['prices'])) {
							$g['products'][$k]['prices'] = $g['prices'];
						}
						if (!isset($g['products'][$k]['listPrices'])) {
							$g['products'][$k]['listPrices'] = $g['listPrices'];
						}
					}
				}
				
				$packages[] = $g;
			}
		}
    }
    return $packages;
}

// CCS Product Packages

?>
