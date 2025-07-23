<?php
$PATH_2_ROOT = '../';
require_once($PATH_2_ROOT.'_settings.regmon.php');

$cookie_options = array(
    'expires' => time()-3600,
    'path' => '/'.$CONFIG['REGmon_Folder'],
    //'domain' => null,
    'secure' => false,
    'httponly' => false,
    'samesite' => 'Lax' // None || Lax || Strict
);

setcookie ("USERNAME", '', $cookie_options);
setcookie ("HASH", '', $cookie_options);
setcookie ("ATHLETE", '', $cookie_options);
setcookie ("UID", '', $cookie_options);
setcookie ("ACCOUNT", '', $cookie_options);
//setcookie ("LANG", '', $cookie_options); // we keep the LANG
//setcookie ("DASHBOARD", '', $cookie_options);

ini_set('session.cookie_samesite', 'Lax');
ini_set('session.cookie_secure', 'Off');
ini_set('session.cookie_httponly', 'Off');

session_start();
session_unset();
session_destroy();

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
