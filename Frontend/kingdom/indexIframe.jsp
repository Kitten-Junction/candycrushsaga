<?php
    $cssFile = "/kingdom/css/kingdom-profile.min.css";
    $jsFile = "/kingdom/js/kingdom-profile.min.js";
?>
<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=320, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0" />
        <title>Kingdom Profile</title>
        <link rel="stylesheet" href="<?php echo $cssFile ?>"/>
    </head>
    <body class="portrait">
        <header id="header"></header>
        <section id="kingdomProfileApp"></section>
        <script type="text/javascript" src="<?php echo $jsFile ?>"></script>
        <footer id="footer"></footer>
        <script>
            function onKingdomReady() {
                sendNotificationToProfileNotifier('bootstrap', 'iframe');
            }
        </script>
    </body>
</html>
