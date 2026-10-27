<?php
$PATH_2_ROOT = '../';
require_once($PATH_2_ROOT.'_settings.regmon.php');
require_once(__DIR__ . '/inc.cookie_settings.php');

$cookie_options = get_Login_Cookie_Options(time()-3600);

setcookie ("USERNAME", '', $cookie_options);
setcookie ("HASH", '', $cookie_options);
setcookie ("ATHLETE", '', get_Login_Cookie_Options(time()-3600, false));
setcookie ("UID", '', $cookie_options);
setcookie ("ACCOUNT", '', $cookie_options);
//setcookie ("LANG", '', $cookie_options); // we keep the LANG
//setcookie ("DASHBOARD", '', $cookie_options);

configure_Login_Session();

session_start();
session_unset();
session_destroy();
$session_cookie_options = session_get_cookie_params();
unset($session_cookie_options['lifetime']);
$session_cookie_options['expires'] = time()-3600;
setcookie(session_name(), '', $session_cookie_options);


//we need only this but I leave the others for reference
header( 'Location: '.$PATH_2_ROOT.'login.php' );
?>
<!DOCTYPE HTML>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="refresh" content="1;url=/">
        <script type="text/javascript">
            window.location.href = "..";
        </script>
        <title>REGmon - Page Redirection</title>
    </head>
    <body>
        If you are not redirected automatically, follow the link to <a href='..'>REGmon</a>
    </body>
</html>
