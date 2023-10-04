<?php // index

declare(strict_types=1);
require_once('_settings.regmon.php');
require('login/validate.php');
require_once('php/inc.common_functions.php');
require_once('index/inc.index_functions.php');

$groups_admins_options_grid = get_groups_admins_select(true);

$sports_groups_options_grid = get_Sports_Groups(true);
$sports_options_grid = get_Sports_Select_Options(true);

$body_height_options_grid = get_Body_Height_Options('', true);

require('index/inc.groups_athletes_select.php');

//inc.groups_athletes_select.php
/** @var string $Group_2_Location_json */
/** @var string $selected_GROUP_name */
/** @var string $User_2_Groups_json */
?>
<!DOCTYPE html>
<html lang="<?=$LANG->LANG_CURRENT;?>">
<head>
<meta charset="UTF-8" />
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"> 
<meta http-equiv="Pragma" content="no-cache">
<meta name="viewport" content="width=device-width<?php /*, initial-scale=1.0*/?>">
<meta name="description" content="" />
<meta name="keywords" content="" />
<meta name="author" content="MAD" />
<title><?=$LANG->INDEX_PAGE_TITLE;?></title>

<link rel="manifest" href="manifest.json?mad2" />
<link rel="icon" href="img/icons/icon-512x512.png" sizes="512x512">
<link rel="icon" href="img/icons/icon-384x384.png" sizes="384x384">
<link rel="icon" href="img/icons/icon-192x192.png" sizes="192x192">
<link rel="icon" href="img/icons/icon-152x152.png" sizes="152x152">
<link rel="icon" href="img/icons/icon-144x144.png" sizes="144x144">
<link rel="icon" href="img/icons/icon-128x128.png" sizes="128x128">
<link rel="icon" href="img/icons/icon-96x96.png" sizes="96x96">
<link rel="icon" href="img/icons/icon-72x72.png" sizes="72x72">
<meta name="mobile-web-app-capable" content="yes">
<meta name="x5-page-mode" content="app">
<meta name="browsermode" content="application">
<link rel="apple-touch-icon" href="img/icons/icon-72x72.png" sizes="72x72">
<link rel="apple-touch-icon" href="img/icons/icon-96x96.png" sizes="96x96">
<link rel="apple-touch-icon" href="img/icons/icon-128x128.png" sizes="128x128">
<link rel="apple-touch-icon" href="img/icons/icon-144x144.png" sizes="144x144">
<link rel="apple-touch-icon" href="img/icons/icon-152x152.png" sizes="152x152">
<link rel="apple-touch-icon" href="img/icons/icon-192x192.png" sizes="192x192">
<link rel="apple-touch-icon" href="img/icons/icon-384x384.png" sizes="384x384">
<link rel="apple-touch-icon" href="img/icons/icon-512x512.png" sizes="512x512">
<meta name="apple-mobile-web-app-status-bar" content="#456f97" />
<meta name="theme-color" content="#456f97" />
<link rel="icon" type="image/png" href="img/icons/icon-128x128.png" sizes="128x128" />

<link rel="shortcut icon" href="favicon.ico" />

<?php /*<!-- CSS -->*/?>
<link rel="stylesheet" type="text/css" href="node_modules/bootstrap/dist/css/bootstrap.min.css" />
<link rel="stylesheet" type="text/css" href="node_modules/bootstrap/dist/css/bootstrap-theme.min.css">
<link rel="stylesheet" type="text/css" href="node_modules/jquery-ui/dist/themes/smoothness/jquery-ui.min.css">
<link rel="stylesheet" type="text/css" href="node_modules/font-awesome/css/font-awesome.min.css" />
<link rel="stylesheet" type="text/css" href="node_modules/@fontsource/open-sans/latin.css" /><?php /*form*/?>
<link rel="stylesheet" type="text/css" href="node_modules/@fontsource/lato/latin.css" />
<?php /*<link rel="stylesheet" type="text/css" href="forms/css/forms.css<?=$G_VER;?>" />*/?>
<link rel="stylesheet" type="text/css" href="css/menu.css<?=$G_VER;?>" />
<link rel="stylesheet" type="text/css" href="css/menu_res.css<?=$G_VER;?>" />
<link rel="stylesheet" type="text/css" href="css/buttons.css<?=$G_VER;?>" />
<link rel="stylesheet" type="text/css" href="css/sticky-side-buttons.css" />


<script type="text/javascript" src="node_modules/jquery/dist/jquery.min.js"></script>
<script type="text/javascript" src="node_modules/jquery-ui/dist/jquery-ui.min.js"></script>

<script type="text/javascript" src="js/modernizr.custom.js"></script><?php /*<!-- HTML5 and CSS3-in older browsers-->*/?>

<script type="text/javascript" src="js/plugins/jquery.cookie.js"></script>

<link rel="stylesheet" type="text/css" href="css/overrides/icheck/skins/polaris2/polaris2.css">
<link rel="stylesheet" type="text/css" href="css/overrides/icheck/skins/flat/yellow2s.css">
<link rel="stylesheet" type="text/css" href="css/overrides/icheck/skins/flat/green2s.css">
<script type="text/javascript" src="node_modules/icheck/icheck.min.js"></script>

<link type="text/css" rel="stylesheet" href="js/plugins/chosen/chosen.min.css" />
<script type="text/javascript" src="js/plugins/chosen/chosen.jquery.min.js"></script>

<script type="text/javascript" src="node_modules/jquery-validation/dist/jquery.validate.js"></script>
<?php if ($LANG->LANG_CURRENT != 'en') { ?>
<script type="text/javascript" src="js/overrides/query-validation/messages_<?=$LANG->LANG_CURRENT;?>.min.js"></script>
<?php } ?>

<link rel="stylesheet" type="text/css" href="node_modules/sweetalert2/dist/sweetalert2.min.css">
<script type="text/javascript" src="node_modules/sweetalert2/dist/sweetalert2.min.js"></script>

<link rel="stylesheet" type="text/css" href="node_modules/fancybox/dist/css/jquery.fancybox.css" />
<link rel="stylesheet" type="text/css" href="css/overrides/fancybox/jquery.fancybox.css" />
<script type="text/javascript" src="node_modules/fancybox/dist/js/jquery.fancybox.pack.js"></script> 

<link rel="stylesheet" type="text/css" href="js/plugins/colorPicker/jquery.colorPicker.css" />
<script type="text/javascript" src="js/plugins/colorPicker/jquery.colorPicker.min.js"></script>

<link rel="stylesheet" type="text/css" href="node_modules/intl-tel-input/build/css/intlTelInput.min.css">
<script type="text/javascript" src="node_modules/intl-tel-input/build/js/intlTelInput-jquery.min.js"></script>


<script type="text/javascript" src="node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
<script type="text/javascript" src="node_modules/bs-confirmation/bootstrap-confirmation.min.js"></script>

<link rel="stylesheet" type="text/css" href="node_modules/bootstrap-colorpicker/dist/css/bootstrap-colorpicker.min.css">
<script type="text/javascript" src="node_modules/bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js"></script>

<link rel="stylesheet" type="text/css" href="js/plugins/clockpicker/clockpicker.css" />
<script type="text/javascript" src="js/plugins/clockpicker/clockpicker.js"></script>

<script type="text/javascript" src="node_modules/moment/min/moment.min.js"></script>
<?php if ($LANG->LANG_CURRENT != 'en') { ?>
<script type="text/javascript" src="js/overrides/moment/<?=$LANG->LANG_CURRENT;?>.js"></script>
<?php } ?>

<link rel="stylesheet" type="text/css" href="node_modules/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css" />
<script type="text/javascript" src="node_modules/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>

<link rel="stylesheet" href="node_modules/fullcalendar/dist/fullcalendar.min.css" />
<script type="text/javascript" src="node_modules/fullcalendar/dist/fullcalendar.min.js"></script>
<?php if ($LANG->LANG_CURRENT != 'en') { ?>
<script type="text/javascript" src="js/overrides/fullcalendar/<?=$LANG->LANG_CURRENT;?>.js"></script>
<?php } ?>
<link rel="stylesheet" type="text/css" href="css/overrides.css<?=$G_VER;?>" />
<link rel="stylesheet" type="text/css" href="index/css/dashboard.css<?=$G_VER;?>" />
<link rel="stylesheet" type="text/css" href="index/css/index.css<?=$G_VER;?>" />
<link rel="stylesheet" type="text/css" href="index/css/sticky_navbar.css<?=$G_VER;?>" />

<script type="text/javascript">
	//$.fn.bootstrapBtn = $.fn.button.noConflict();
	$.jgrid = $.jgrid || {};
	$.jgrid.no_legacy_api = true;
	$.jgrid.useJSON = true;
</script>
<link rel="stylesheet" type="text/css" href="node_modules/free-jqgrid/dist/css/ui.jqgrid.min.css" />
<script type="text/javascript" src="node_modules/free-jqgrid/dist/jquery.jqgrid.min.js"></script>
<?php /*<script type="text/javascript" src="node_modules/free-jqgrid/dist/plugins/min/jquery.jqgrid.showhidecolumnmenu.js"></script>*/?>
<script type="text/javascript" src="node_modules/free-jqgrid/dist/i18n/min/grid.locale-<?=$LANG->LANG_CURRENT;?>.js"></script><?php /*de, en*/?>
<script type="text/javascript" src="js/lang_<?=$LANG->LANG_CURRENT;?>.js<?=$G_VER;?>"></script>
<script type="text/javascript" src="js/common.js<?=$G_VER;?>"></script>
<script type="text/javascript" src="js/grid._extend.js<?=$G_VER;?>"></script>
<script type="text/javascript">
//fix bfcach problem when browser back button is pressed and page not load properly
window.addEventListener( "pageshow", function (event) {
	var load_from_bfcach = event.persisted || (typeof window.performance != "undefined" && window.performance.navigation.type === 2);
	if (load_from_bfcach) {
		window.location.reload();
	}
});

const V_REGmon_Folder = '<?=$CONFIG['REGmon_Folder'];?>';
const Production_Mode = <?=($CONFIG['Production_Mode'] ? 'true' : 'false');?>;
var V_ONLINE = true;

<?php echo 'var'
.' V_VER="'.$G_Version.'"'
.',V_Group_2_Location='.$Group_2_Location_json
.',V_GROUPS_ADMINS_OPTIONS="'.$groups_admins_options_grid.'"'
.',V_IS_ADMIN='.(int)($ADMIN OR $LOCATION_ADMIN OR $GROUP_ADMIN OR $GROUP_ADMIN_2)
.',V_IS_TRAINER='.(int)($TRAINER)
.',V_IS_ATHLETE='.(int)($ATHLETE)
.',V_ADMIN='.(int)$ADMIN
.',V_LOCATION_ADMIN='.(int)$THIS_LOCATION_ADMIN
.',V_GROUP_ADMIN='.(int)$THIS_GROUP_ADMIN
.',V_GROUP_ADMIN_2='.(int)$THIS_GROUP_ADMIN_2
.',V_GROUP_TRAINER='.(int)$THIS_GROUP_TRAINER
.',V_UID='.(int)$UID
.',V_ATHLETE='.(isset($_COOKIE['ATHLETE'])?(int)$_COOKIE['ATHLETE']:(int)$UID)
.',V_LOCATION='.(int)$LOCATION
.',V_GROUP='.(int)$GROUP
.',V_GROUP_NAME='.'"'.$selected_GROUP_name.'"'
.',V_TRAINER_R_PERMS=""'
.',V_TRAINER_W_PERMS=""'
.',V_DASHBOARD=[]'
.',V_SPORTS_GROUPS_OPTIONS="'.$sports_groups_options_grid.'"'
.',V_SPORTS_OPTIONS="'.$sports_options_grid.'"'
.',V_BODY_HEIGHT_OPTIONS="'.$body_height_options_grid.'"'
.',V_FORMS_TEMPLATES_OPTIONS=""'
.',V_RESULTS_TEMPLATES_OPTIONS=""'
.',V_CATEGORIES_FORMS_OPTIONS=""'
.',V_USER_LVL_OPTIONS="10:'.$LANG->LVL_ATHLETE.';30:'.$LANG->LVL_TRAINER.(($THIS_LOCATION_ADMIN OR $ADMIN)?';40:'.$LANG->LVL_GROUP_ADMIN_2:'').(($THIS_LOCATION_ADMIN OR $ADMIN)?';45:'.$LANG->LVL_GROUP_ADMIN:'').($ADMIN?';50:'.$LANG->LVL_LOCATION:'').'"'
.',V_is_Index_Options=true'
.',V_User_2_Groups='.$User_2_Groups_json.';'."\n";
?>
</script>
<script type="text/javascript" src="forms/js/forms.menu.js<?=$G_VER;?>"></script>
<script type="text/javascript" src="index/js/fancybox_defaults.js<?=$G_VER;?>"></script>
<script type="text/javascript" src="index/js/notes.js<?=$G_VER;?>"></script>
<script type="text/javascript" src="index/js/options.js<?=$G_VER;?>"></script>
<script type="text/javascript" src="index/js/index.js<?=$G_VER;?>"></script>
<script type="text/javascript" src="index/js/sticky_navbar.js<?=$G_VER;?>"></script>
<script type="text/javascript" src="index/js/dashboard.js<?=$G_VER;?>"></script>
</head>
<body>
	<div id="loading" class="ajaxOverlay" style="display:none">
		<div class="ajaxMessage"><img src="img/ldg.gif"></div>
	</div>

	<?php require('index/inc.dashboard.php');?>
		
	<?php require('php/inc.header.php');?>

	<?php require('index/inc.nav.php');?>

	<?php require('index/inc.index_main.php');?>

	<?php require('index/inc.notes.php');?>

	<?php //require('php/inc.footer.php');?>

</body>
</html>