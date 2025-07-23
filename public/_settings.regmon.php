<?php
declare(strict_types=1);

namespace REGmon;
use stdClass;


//Version
require_once(__DIR__.'/version.php');
$G_VER = "?ver=" . $G_Version;

//Configuration
require_once(__DIR__.'/config/_config.php');


//TODO: find home for Simple_Extension_System
//now we not have home for the Simple Extension System 
//so for the moment it will remain here

/**
 * Simple Extension System
 * -----------------------------
 * Here you can set overwrites for main php pages you want to extend.
 * This way you can keep the default pages and also make your own extensions
 * without losing the ability to use git pull for updating default code.
 * we can use '__' in extensions pages so .gitignore not count them as changes
 * ===================================================
 */
$CONFIG_SES = [
	/**
	 * if the 'exit_after' is true then exit current page
	 * else continue to the current page after the extension page load
	 * ex.
	 * 'main_page.php' 			=> ['__extension_page.php', exit_after]
	 */
	//'administration.php' 		=> ['__administration.php', true],
	//'export.php' 				=> ['__export.php', true],
	//'form.php' 				=> ['__form.php', true],
	//'forms_results.php'		=> ['__forms_results.php', true],
	//'index.php' 				=> ['__index.php', true],
	//'login.php' 				=> ['__login.php', true],
	//'register.php' 			=> ['__register.php', true],
	//'results.php' 			=> ['__results.php', true],

	//this is not a page but we have a special SES part only for this
	//we need to set the exit_after to false so that can continue
	//'_settings.regmon.php' 	=> ['__settings.regmon.php', false],
];

$CONFIG_SES_enabled = false;
if (count($CONFIG_SES)) {
	$CONFIG_SES_enabled = true;
}


/**
 * Simple Extension System
 * only for this file '_settings.regmon.php', 
 * because we never really call this file directly,
 * so we can extend this too, but can be tricky 
 * ===================================================
 */
//check Simple_Extension_System
if ($CONFIG_SES_enabled) {
	$settings_page = '_settings.regmon.php';
	if (isset($CONFIG_SES[$settings_page])) {
		$extension_page = $CONFIG_SES[$settings_page][0];
		$exit_after = $CONFIG_SES[$settings_page][1];
		if (file_exists($extension_page)) 
		{
			require($extension_page);

			if ($exit_after) {
				//exit current page
				exit;
			}
		}
	}
}


// localhost config
if (substr_count($_SERVER['HTTP_HOST'], 'localhost') OR  
	substr_count($_SERVER['HTTP_HOST'], 'test')) 
{
	$CONFIG['Production_Mode'] = false;
	$CONFIG['HTTP'] = 'http://';
	$CONFIG['DOMAIN'] = $_SERVER['HTTP_HOST'];
}
else {
	
	//redirect to https
	if ($CONFIG['Force_Redirect_To_HTTPS'] AND (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == "off")){
		$redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		header('HTTP/1.1 301 Moved Permanently');
		header('Location: ' . $redirect);
		exit();
	}

}


//logging recommendations from php.ini-production & php.ini-development
if ($CONFIG['Production_Mode']) {
	ini_set('display_errors', '0');
	ini_set('display_startup_errors', '0');
	ini_set('log_errors', '1');
	error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
}
else { //Development
	ini_set('display_errors', '1');
	ini_set('display_startup_errors', '1');
	ini_set('log_errors', '1');
	error_reporting(E_ALL);
}

ini_set('default_charset', 'utf-8');



/**
 * Simple Extension System
 * -----------------------------
 * check if we have an extension for the current file
 * and if extension file exists and require the extension file
 * if the 'exit_after' is true then exit current page
 * else continue to the current page after the extension page finished
 * ===================================================
 */
$current_page = str_replace('/', '', $_SERVER['PHP_SELF']);
//check Simple_Extension_System
if ($CONFIG_SES_enabled) {
	if (isset($CONFIG_SES[$current_page])) {
		$extension_page = $CONFIG_SES[$current_page][0];
		$exit_after = $CONFIG_SES[$current_page][1];
		if (file_exists($extension_page)) {
			require($extension_page);

			if ($exit_after) {
				//exit current page
				exit;
			}
		}
	}
}
?>