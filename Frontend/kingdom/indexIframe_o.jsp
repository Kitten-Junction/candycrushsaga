
<%@ page import="com.king.core.util.StaticFileManager"%>
<%@ page import="com.king.core.util.ApplicationContainer"%>
<%
    final ApplicationContainer container = (ApplicationContainer) application.getAttribute(ApplicationContainer.APPLICATION_CONTAINER_KEY);
    final StaticFileManager staticFileManager = container.getComponent(StaticFileManager.class);

    boolean isSecure = request.isSecure();
    boolean useCdn = true;
    String cssFile = staticFileManager.getStaticFileURL("/kingdom/css/kingdom-profile.min.css", isSecure, useCdn);
    String jsFile = staticFileManager.getStaticFileURL("/kingdom/js/kingdom-profile.min.js", isSecure, useCdn);
%>
<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=320, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0" />
        <title>Kingdom Profile</title>
        <link rel="stylesheet" href="<%= cssFile %>"/>
    </head>
    <body class="portrait">
        <header id="header"></header>
        <section id="kingdomProfileApp"></section>
        <script type="text/javascript" src="<%= jsFile %>"></script>
        <footer id="footer"></footer>
        <script>
            function onKingdomReady() {
                sendNotificationToProfileNotifier('bootstrap', 'iframe');
            }
        </script>
    </body>
</html>
