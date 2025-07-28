<?php 
declare(strict_types=1);
require_once('_settings.regmon.php');
require('login/validate.php');
require_once('php/inc.common_functions.php');

if (!$ADMIN) exit;

$locations_options_grid = get_locations_select(true);
$locations_admins_options_grid = get_locations_admins_select(true);

$groups_options_grid = get_groups_select(true);
$groups_admins_options_grid = get_groups_admins_select(true);

$sports_groups_options_grid = get_Sports_Groups(true) . '';
$sports_options_grid = get_Sports_Select_Options(true);

$body_height_options_grid = get_Body_Height_Options('', true);
?>
<!DOCTYPE html>
<html lang="<?=$LANG->LANG_CURRENT;?>">
<head>
<meta charset="UTF-8" />
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"> 
<meta name="viewport" content="width=device-width<?php /*, initial-scale=1.0*/?>">
<meta name="description" content="" />
<meta name="keywords" content="" />
<meta name="author" content="MAD" />
<title><?=$LANG->ADMIN_PAGE_TITLE;?></title>

<link rel="shortcut icon" href="favicon.ico" />

<link rel="stylesheet" type="text/css" href="node_modules/bootstrap/dist/css/bootstrap.min.css" />
<link rel="stylesheet" type="text/css" href="node_modules/bootstrap/dist/css/bootstrap-theme.min.css">
<link rel="stylesheet" type="text/css" href="node_modules/jquery-ui/dist/themes/smoothness/jquery-ui.min.css">
<link rel="stylesheet" type="text/css" href="node_modules/font-awesome/css/font-awesome.min.css" />
<link rel="stylesheet" type="text/css" href="node_modules/@fontsource/lato/latin.css" />
<link rel="stylesheet" type="text/css" href="css/menu.css<?=$G_VER;?>" />
<link rel="stylesheet" type="text/css" href="css/menu_res.css<?=$G_VER;?>" />
<link rel="stylesheet" type="text/css" href="css/buttons.css<?=$G_VER;?>" />

<script type="text/javascript" src="node_modules/jquery/dist/jquery.min.js"></script>
<script type="text/javascript" src="node_modules/jquery-ui/dist/jquery-ui.min.js"></script>

<script type="text/javascript" src="js/modernizr.custom.js"></script><?php /*<!-- HTML5 and CSS3-in older browsers-->*/?>

<script type="text/javascript" src="node_modules/moment/min/moment.min.js"></script>
<script type="text/javascript" src="js/plugins/jquery.cookie.js"></script>
<script type="text/javascript" src="js/plugins/jquery.blockUI.js"></script>

<link rel="stylesheet" type="text/css" href="js/plugins/colorPicker/jquery.colorPicker.css" />
<script type="text/javascript" src="js/plugins/colorPicker/jquery.colorPicker.min.js"></script>

<link rel="stylesheet" type="text/css" href="css/overrides/icheck/skins/polaris2/polaris2.css">
<link rel="stylesheet" type="text/css" href="css/overrides/icheck/skins/flat/yellow2s.css">
<script type="text/javascript" src="node_modules/icheck/icheck.min.js"></script>

<link type="text/css" rel="stylesheet" href="js/plugins/chosen/chosen.min.css" />
<script type="text/javascript" src="js/plugins/chosen/chosen.jquery.min.js"></script>

<link rel="stylesheet" type="text/css" href="node_modules/sweetalert2/dist/sweetalert2.min.css">
<script type="text/javascript" src="node_modules/sweetalert2/dist/sweetalert2.min.js"></script>

<link rel="stylesheet" href="node_modules/fancybox/dist/css/jquery.fancybox.css" />
<link rel="stylesheet" href="css/overrides/fancybox/jquery.fancybox.css" />
<script type="text/javascript" src="node_modules/fancybox/dist/js/jquery.fancybox.pack.js"></script> 


<script type="text/javascript" src="node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
<script type="text/javascript" src="node_modules/bs-confirmation/bootstrap-confirmation.min.js"></script>

<script type="text/javascript" src="node_modules/moment/min/moment.min.js"></script>
<?php if ($LANG->LANG_CURRENT != 'en') { ?>
<script type="text/javascript" src="js/overrides/moment/<?=$LANG->LANG_CURRENT;?>.js"></script>
<?php } ?>
<link rel="stylesheet" type="text/css" href="node_modules/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css" />
<script type="text/javascript" src="node_modules/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>


<link rel="stylesheet" type="text/css" href="node_modules/free-jqgrid/dist/css/ui.jqgrid.min.css" />
<link rel="stylesheet" type="text/css" href="node_modules/free-jqgrid/dist/plugins/css/ui.multiselect.min.css" />
<script type="text/javascript" src="node_modules/free-jqgrid/dist/plugins/min/ui.multiselect.js"></script>
<script type="text/javascript" src="node_modules/free-jqgrid/dist/i18n/min/grid.locale-<?=$LANG->LANG_CURRENT;?>.js"></script>
<script type="text/javascript">
	$.fn.bootstrapBtn = $.fn.button.noConflict();
	$.jgrid.no_legacy_api = true;
	$.jgrid.useJSON = true;
</script>
<script type="text/javascript" src="node_modules/free-jqgrid/dist/jquery.jqgrid.min.js"></script>
<?php /*<script type="text/javascript" src="node_modules/free-jqgrid/dist/plugins/min/jquery.jqgrid.showhidecolumnmenu.js"></script>*/?>

<link rel="stylesheet" type="text/css" href="css/overrides.css<?=$G_VER;?>" />
<script type="text/javascript" src="js/lang_<?=$LANG->LANG_CURRENT;?>.js<?=$G_VER;?>"></script>
<script type="text/javascript" src="js/common.js<?=$G_VER;?>"></script>
<script type="text/javascript" src="js/grid._extend.js<?=$G_VER;?>"></script>
<script type="text/javascript">
const V_ADMIN = <?=(int)$ADMIN;?>;
const V_GROUP_ADMIN_2 = 0;
const V_is_Index_Options = false;
const V_LOCATIONS_OPTIONS = '<?=$locations_options_grid;?>';
const V_LOCATIONS_ADMINS_OPTIONS = '<?=$locations_admins_options_grid;?>';
const V_GROUPS_OPTIONS = '<?=$groups_options_grid;?>';
const V_GROUPS_ADMINS_OPTIONS = '<?=$groups_admins_options_grid;?>';
const V_SPORTS_GROUPS_OPTIONS = '<?=$sports_groups_options_grid;?>';
const V_SPORTS_OPTIONS = '<?=$sports_options_grid;?>';
const V_BODY_HEIGHT_OPTIONS = '<?=$body_height_options_grid;?>';
jQuery(function() {
	//button Export
	$("button.export").on('click',function() {
		window.location.href = 'export.php';
	});
});
</script>
<link rel="stylesheet" type="text/css" href="css/style_grid_admin.css<?=$G_VER;?>" />
<script type="text/javascript" src="admin/grid.locations.js<?=$G_VER;?>"></script>
<script type="text/javascript" src="admin/grid.groups.js<?=$G_VER;?>"></script>
<script type="text/javascript" src="admin/grid.users.js<?= $G_VER; ?>"></script>
<script type="text/javascript" src="js/grid.categories.js<?=$G_VER;?>"></script>
<script type="text/javascript" src="forms/js/grid.forms.js<?=$G_VER;?>"></script>
<script type="text/javascript" src="js/grid.dropdowns.js<?=$G_VER;?>"></script>
<script type="text/javascript" src="js/grid.sports.js<?=$G_VER;?>"></script>
<script type="text/javascript" src="js/grid.tags.js<?=$G_VER;?>"></script>
<script type="text/javascript" src="admin/templates_functions.js<?=$G_VER;?>"></script>
<script type="text/javascript" src="admin/grid.templates_forms.js<?=$G_VER;?>"></script>
<script type="text/javascript" src="admin/grid.templates_results.js<?=$G_VER;?>"></script>
<script type="text/javascript" src="admin/grid.templates_axis.js<?=$G_VER;?>"></script>
</head>
<body>
	
	<div id="container" class="ui-layout-center ui-helper-reset">
		
		<?php //require('php/inc.header.php');?>
	
		<div id="loading" class="ajaxOverlay" style="display:none">
			<div class="ajaxMessage"><img src="img/ldg.gif"></div>
		</div>

		<button type="button" class="home"> &nbsp; &nbsp; <?=$LANG->HOMEPAGE;?></button>
		&nbsp; &nbsp; 
		<button type="button" class="export"><?=$LANG->EXPORT;?> &nbsp; &nbsp; </button>
		<br>
		<br>
		<br>
		<table id="locations" alt="<?=$LANG->ADMIN_LOCATIONS;?>"></table>
		<div id="Lpager"></div>
		<br>
		<br>
		<table id="groups" alt="<?=$LANG->ADMIN_GROUPS;?>"></table>
		<div id="Gpager"></div>
		<br>
		<br>
		<table id="users" alt="<?=$LANG->ADMIN_USERS;?>"></table>
		<div id="Upager"></div>
		<br>
		<hr>
		<br>
		<table id="categories" alt="<?=$LANG->TAB_CATEGORIES;?>"></table>
		<div id="Cpager"></div>
		<br>
		<br>
		<table id="forms" alt="<?=$LANG->TAB_FORMS;?>"></table>
		<div id="Fpager"></div>
		<br>
		<br>
		<table id="dropdowns" alt="<?=$LANG->ADMIN_DROPDOWNS;?>"></table>
		<div id="Dpager"></div>
		<br>
		<br>
		<table id="sports" alt="<?=$LANG->ADMIN_SPORTS;?>"></table>
		<div id="SPpager"></div>
		<br>
		<br>
		<table id="tags" alt="<?=$LANG->ADMIN_TAGS;?>"></table>
		<div id="Tpager"></div>
		<br>
		<hr>
		<br>
		<table id="templates_forms" alt="<?=$LANG->ADMIN_TEMPLATES_FORMS;?>"></table>
		<div id="TFpager"></div>
		<br>
		<br>
		<table id="templates_results" alt="<?=$LANG->ADMIN_TEMPLATES_RESULTS;?>"></table>
		<div id="TRpager"></div>
		<br>
		<br>
		<table id="templates_axis" alt="<?=$LANG->ADMIN_TEMPLATES_Y_AXIS;?>"></table>
		<div id="TApager"></div>
		<br>
		<br>
		<br>
			
		<?php //require('php/inc.footer.php'); ?>
	</div>
</body>
</html>