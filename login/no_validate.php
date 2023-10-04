<?php // load defaults without validate a user
// used by pages where we not have a logged in user ex. register.php

$PATH_2_ROOT = '../';
require_once(__DIR__.'/'.$PATH_2_ROOT.'_settings.regmon.php');

require_once(__DIR__.'/'.$PATH_2_ROOT.'login/inc.login_functions.php');


//continue to the page that call this
?>
