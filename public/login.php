<?php // Login

declare(strict_types=1);
require_once('_settings.regmon.php');

ini_set('session.cookie_samesite', 'Lax');
ini_set('session.cookie_secure', 'Off');
ini_set('session.cookie_httponly', 'Off');

// Initialize Session
session_cache_limiter();
session_start();


//secure ajax sub pages from direct call
$SEC_check = $CONFIG['SEC_Page_Secret'];


//Load languages
require_once('php/class.language.php');
$LANG = Language::getInstance($CONFIG['REGmon_Folder']??'', $CONFIG['Default_Language']??'', !!$CONFIG['Use_Multi_Language_Selector']);
?>
<!DOCTYPE html>
<html lang="<?=$LANG->LANG_CURRENT;?>">
<head>
<title><?=$LANG->LOGIN_PAGE_TITLE;?></title>
<meta charset="UTF-8" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="-1">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex, nofollow">
<meta name="Googlebot" content="noindex, nofollow">
<link rel="shortcut icon" href="favicon.ico" type="image/x-icon"> 

<link rel="stylesheet" type="text/css" href="node_modules/bootstrap/dist/css/bootstrap.min.css" />
<link type="text/css" rel="stylesheet" href="node_modules/font-awesome/css/font-awesome.min.css" />
<link rel="stylesheet" type="text/css" href="node_modules/@fontsource/lato/latin.css" />
<link rel="stylesheet" type="text/css" href="css/menu.css<?=$G_VER;?>" />
<link rel="stylesheet" type="text/css" href="login/css/login.css<?= $G_VER; ?>" />
<link rel="stylesheet" type="text/css" href="index/css/sticky_navbar.css<?=$G_VER;?>" />

<script type="text/javascript" src="node_modules/jquery/dist/jquery.min.js"></script>
<script type="text/javascript" src="node_modules/jquery-ui/dist/jquery-ui.min.js"></script>
<script type="text/javascript" src="js/plugins/jquery.cookie.js"></script>
<script type="text/javascript" src="node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
<script type="text/javascript" src="js/lang_<?=$LANG->LANG_CURRENT;?>.js<?=$G_VER;?>"></script>

<?php if ($CONFIG['Use_VisualCaptcha']) { ?>
	<link href="login/visualCaptcha/css/visualcaptcha.css" media="all" rel="stylesheet">
	<script src="login/visualCaptcha/js/visualcaptcha.jquery.js"></script>
	<script src="login/visualCaptcha/js/visualcaptcha.js"></script>
<?php }  ?>

<script type="text/javascript" src="js/common.js<?=$G_VER;?>"></script>
<script>
const V_REGmon_Folder = '<?=$CONFIG['REGmon_Folder'];?>';
</script>
<script src="login/js/login.js<?=$G_VER;?>"></script>
<style>
/*loading*/
.ajaxOverlay {z-index:10000; border:none; margin:0px; padding:0px; width:100%; height:100%; top:0px; left:0px; opacity:0.6; cursor:wait; position:fixed; background-color:rgb(0, 0, 0);}
.ajaxMessage {z-index:10011; position:fixed; padding:0px; margin:0px; width:30%; top:40%; left:35%; text-align:center; color:rgb(255, 255, 0); border:0px; cursor:wait; text-shadow:red 1px 1px; font-size:18px; background-color:transparent;}

.status{-webkit-border-radius:7px;-moz-border-radius:7px;-ms-border-radius:7px;-o-border-radius:7px;border-radius:7px;
background-color:#fbb6a9;padding:5px;text-align:center;font-family:'Oxygen', sans-serif;font-size:15px;color:#b41c1c;
font-weight:100;margin:0 0 24px}
@media only screen and (max-width: 360px){.status{font-size:13px}}
.status p{margin:0}
</style>
</head>
<body>
	<div id="loading" class="ajaxOverlay" style="display:none">
		<div class="ajaxMessage"><img src="img/ldg.gif"></div>
	</div>

	<?php require('php/inc.header.php');?>
	
	<?php require('php/inc.nav_lang.php');?>

	<div class="container">	
		<header style="padding:2em 2em 1em;">
			<h1><b><?=$LANG->APP_NAME;?></b><span><?=$LANG->APP_INFO;?></span></h1>	
		</header>
		
		<section class="main" style="min-height:100%;">
			<form class="form" id="LoginForm" name="LoginForm" method="post" action="login/authenticate.php">
				<h1 style="text-align:center;"><span class="log-in"><?=$LANG->LOGIN_HEADER;?></span></h1>
				<p class="float">
					<label for="login"><i class="fa fa-user"></i><?=$LANG->LOGIN_USERNAME;?></label>
					<input type="text" id="username" name="username" placeholder="<?=$LANG->LOGIN_USERNAME;?>" autofocus>
				</p>
				<p class="float">
					<label for="password"><i class="fa fa-lock"></i><?=$LANG->LOGIN_PASSWORD;?></label>
					<input type="password" id="password" name="password" placeholder="<?=$LANG->LOGIN_PASSWORD;?>">
				</p>
				<p class="clearfix" style="padding-top:90px;">
					<?php //login messages #################################################?>
					<input type="hidden" name="form_submit" value="1" readonly="readonly" />
					<div class="wrap">
						<div class="pre-captcha-wrapper">
							<div class="captcha-wrapper">
								<div id="status-message">
	<?php
		if (isset($_GET['failure']) AND $_GET['failure'] == '1') {
			echo '<div class="status">'.$LANG->LOGIN_FAIL_TXT_1.'<br>'.$LANG->LOGIN_FAIL_TXT_2.'</div>';
		} 
		elseif (isset($_GET['captchaError']) AND $_GET['captchaError'] == '1') {
			echo '<div class="status">'.$LANG->LOGIN_FAIL_TXT_3.'<br>'.$LANG->LOGIN_FAIL_TXT_4.'</div>';
		}
		elseif (isset($_GET['inactive']) AND $_GET['inactive'] == '1') {
			echo '<div class="status">'.$LANG->LOGIN_FAIL_TXT_5.'<br>'.$LANG->LOGIN_FAIL_TXT_6.'</div>';
		}
		elseif (isset($_GET['blockedIP']) AND $_GET['blockedIP'] == '1') {
			echo '<div class="status">'.
					str_replace('{Max_Attempts}', $CONFIG['LogLimiter']['Max_Attempts'], $LANG->LOGIN_FAIL_TXT_7).'<br>'.
					str_replace('{Block_Minutes}', $CONFIG['LogLimiter']['Block_Minutes'], $LANG->LOGIN_FAIL_TXT_8).
				'</div>';
		}
	?>				
								</div>
								<div id="login-captcha"></div>
							</div>
						</div> 
					</div>
					<?php //end login messages #################################################?>
				</p>
				<div style="text-align:center;">
					<div style="float:left; width:50%; text-align:center;">
						<button type="button" class="new_reg">&nbsp;<?=$LANG->LOGIN_REGISTER;?></button> 
					</div>
					<div style="float:right; width:50%; text-align:center;">
						<button type="submit" class="log_in">&nbsp;<?=$LANG->LOGIN;?>&nbsp;&nbsp;&nbsp;<span class="log_in_ico"></span></button> 
					</div>
				</div>
				<p class="clearfix" style="text-align:center;">
					<br><?=$LANG->LOGIN_CONTACT;?>: <a href="mailto:<?=$CONFIG['EMAIL']['Support'];?>" class="support_email" target="_blank" style="color:blue;"><?=$CONFIG['EMAIL']['Support'];?></a><br>
				</p>
			</form>
		</section>
	</div>
	
	<?php //require('php/inc.footer.php');?>
	
</body>
</html>