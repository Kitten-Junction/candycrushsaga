<?php
// using controller.php from tb because why not

include '../Backend/common.php'; // common funcs & variables, usually static

function throw404() {
    header("HTTP/1.1 404 Not Found");
    header("Content-Type: text/html");
    echo '<!DOCTYPE HTML><html><head> <meta charset="utf-8"/> <title>404</title> <style> *{margin:0;padding:0} .clearfix:after{content:".";display:block;clear:both;visibility:hidden;line-height:0;height:8em} .clearfix{display:block} h1{font-size:10em;margin-bottom:0} pre{font-family:\'Bree Serif\',serif;font-size:2em} #footer{position:absolute;left:0;bottom:0;font-size:.7em} </style></head><body><div class="clearfix"></div><div style="text-align: center"> <h1> Not Found </h1> <p><pre>The requested resource could not be found</pre></p> <div> </div> <div id="footer">-- servero --</div></div></body></html>';
    exit();
}

$urlPath = parse_url($_SERVER['REQUEST_URI']);
$path = ltrim($urlPath['path'], '/');

if (file_exists($backendPath . '/api/' . $path . '.php')) {
    include $backendPath . '/api/' . $path . '.php';
    exit;
}

$urlVariables = explode('/', $path);
$directory = $urlVariables[0];
$subPath = implode('/', array_slice($urlVariables, 1));

if (file_exists($backendPath . '/api/' . $directory . '/' . $subPath . '.php')) {
    include $backendPath . '/api/' . $directory . '/' . $subPath . '.php';
    exit;
}

throw404();

?>
