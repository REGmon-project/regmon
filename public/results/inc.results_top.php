<?php // inc results_top

/** @var string $results_page */

if ($SEC_check != $CONFIG['SEC_Page_Secret']) exit;

$template 	= $_GET['vor'] ?? 0;
$group_id 	= (int)($_REQUEST['group_id'] ?? $GROUP);
$athlete_id = (int)($_REQUEST['athlete_id'] ?? $UID);
$cat_id 	= (int)($_REQUEST['cat_id'] ?? 0);
$form_id 	= (int)($_REQUEST['id'] ?? 0); //form_id

$type 		= $_REQUEST['type'] ?? '';
$is_iframe 	= isset($_REQUEST['is_iframe']) ? true : false;
$is_iOS 	= isset($_REQUEST['is_iOS']) ? true : false;
$timestamp 	= $_REQUEST['timestamp'] ?? false;
$sec 		= $_REQUEST['sec'] ?? '';
$DEBUG 		= (int)($_GET['debug'] ?? 0);

$sec_OK = MD5($CONFIG['SEC_Encrypt_Secret'] . $form_id . $athlete_id . $group_id . $UID);

if (($athlete_id != $UID AND $ATHLETE) OR (isset($_REQUEST['sec']) AND $sec !== $sec_OK)) {
	echo Exit_Message($LANG->NO_ACCESS_RIGHTS);
	exit;
}


// Groups select in locations
$Show_Only_Group_name = ''; //get val from get_Select__Groups__Options
$Select__Groups__Options = get_Select__Groups__Options($UID, $group_id, $results_page);


// date time 
$first_time = strtotime("-6 days"); //-1 week
$last_time = strtotime("now");
$date_from = get_date_time(date("Y-m-d 00:00:00", $first_time));
$date_to = get_date_time(date("Y-m-d 23:59:59", $last_time));
if ($is_iframe AND $timestamp) {
	$first_time = $timestamp - 86400 * 7; //-1 week
	$last_time = $timestamp;
	$date_from = get_date_time(date("Y-m-d H:i:s", $first_time));
	$date_to = get_date_time(date("Y-m-d H:i:s", $last_time));
}


// info for lines, colors, formula
$info_lines_color_formula = get_info_lines_color_formula();


//######################################################
$title = $LANG->RESULTS_PAGE_TITLE;
$no_forms_css = true;
require('php/inc.html_head.php');
//######################################################
?>

<link type="text/css" rel="stylesheet" href="js/plugins/chosen/chosen.min.css" />
<script type="text/javascript" src="js/plugins/chosen/chosen.jquery.min.js"></script>
 
<link type="text/css" rel="stylesheet" href="css/coolfieldset.css?<?=$G_VER;?>" />
<script type="text/javascript" src="js/plugins/jquery.collapsibleFieldset.js?<?=$G_VER;?>"></script>

<link rel="stylesheet" type="text/css" href="node_modules/bootstrap/dist/css/bootstrap-theme.min.css">
<script type="text/javascript" src="node_modules/bootstrap/dist/js/bootstrap.min.js"></script>

<script type="text/javascript" src="node_modules/moment/min/moment.min.js"></script>
<?php if ($LANG->LANG_CURRENT != 'en') { ?>
<script type="text/javascript" src="js/overrides/moment/<?=$LANG->LANG_CURRENT;?>.js"></script>
<?php } ?>
<link rel="stylesheet" type="text/css" href="node_modules/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css" />
<script type="text/javascript" src="node_modules/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>

<link rel="stylesheet" type="text/css" href="node_modules/bootstrap-multiselect/dist/css/bootstrap-multiselect.css"/>
<script type="text/javascript" src="node_modules/bootstrap-multiselect/dist/js/bootstrap-multiselect.min.js"></script>
<link rel="stylesheet" type="text/css" href="node_modules/bootstrap-colorpicker/dist/css/bootstrap-colorpicker.min.css">
<script type="text/javascript" src="node_modules/bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js"></script>

<script type="text/javascript" src="node_modules/jquery-validation/dist/jquery.validate.js"></script>
<?php if ($LANG->LANG_CURRENT != 'en') { ?>
<script type="text/javascript" src="js/overrides/query-validation/messages_<?=$LANG->LANG_CURRENT;?>.min.js"></script>
<?php } ?>
<script type="text/javascript" src="node_modules/bs-confirmation/bootstrap-confirmation.min.js"></script>

<link rel="stylesheet" type="text/css" href="css/sticky-side-buttons.css" />
<script type="text/javascript" src="js/sticky-side-buttons.js"></script>

<link rel="stylesheet" href="node_modules/fancybox/dist/css/jquery.fancybox.css" />
<link rel="stylesheet" href="css/overrides/fancybox/jquery.fancybox.css" />
<script type="text/javascript" src="node_modules/fancybox/dist/js/jquery.fancybox.pack.js"></script> 

<link rel="stylesheet" type="text/css" href="node_modules/sweetalert2/dist/sweetalert2.min.css">
<script type="text/javascript" src="node_modules/sweetalert2/dist/sweetalert2.min.js"></script>

<link rel="stylesheet" type="text/css" href="css/overrides/excel-formula-mad/excelFormulaUtilitiesJS.css">
<script type="text/javascript" src="node_modules/excel-formula-mad/dist/excel-formula.min.js"></script>
<script type="text/javascript" src="js/plugins/jquery-calx/numeral.min.js"></script>
<script type="text/javascript" src="js/plugins/jquery-calx/jquery-calx-2.2.7_changed_reduced.js<?=$G_VER;?>"></script>

<?php /* With this css file it not take account the axis options
<link rel="stylesheet" type="text/css" href="node_modules/highcharts/css/highcharts.css"> */?>
<script src="node_modules/highcharts/highcharts.js"></script>
<script src="node_modules/highcharts/modules/exporting.js"></script>
<script src="node_modules/highcharts/modules/offline-exporting.js"></script>
<script src="node_modules/highcharts/modules/no-data-to-display.js"></script>
<script type="text/javascript" src="js/plugins/export/export-csv.js"></script>
<?php /*http://sheetjs.com/demos/table.html*/?>
<script type="text/javascript" src="js/plugins/export/xlsx.core.min.js"></script>
<script type="text/javascript" src="js/plugins/export/Blob.js"></script>
<script type="text/javascript" src="js/plugins/export/FileSaver.js"></script>
<script type="text/javascript" src="js/plugins/export/Export2Excel.js"></script>

<script type="text/javascript" src="js/lang_<?=$LANG->LANG_CURRENT;?>.js<?=$G_VER;?>"></script>
<script>
const Production_Mode = <?=($CONFIG['Production_Mode']?'true':'false');?>;
const V_RESULTS_PAGE = '<?=$results_page;?>';
var V_DEBUG = <?=$DEBUG;?>;
var V_DATE_FROM;
var V_DATE_TO;
var V_DATE_FROM_moment;
var V_DATE_TO_moment;
var V_Chart;
var V_Calx_Sheets = [];
const V_Not_Show_in_Diagram_Types_arr = [
	'_Text',
	'_Textarea',
	'_Date',
	'_Time',
	'_Period_From',
	'_Period_To'
];
</script>
<script type="text/javascript" src="js/common.js<?=$G_VER;?>"></script>
<script type="text/javascript" src="results/js/html_templates.js<?=$G_VER;?>"></script>
<script type="text/javascript" src="results/js/common_functions.js<?=$G_VER;?>"></script>
<script type="text/javascript" src="results/js/export_table_data.js<?=$G_VER;?>"></script>
<script type="text/javascript" src="results/js/diagram_functions.js<?=$G_VER;?>"></script>
<script type="text/javascript" src="results/js/common_jquery.js<?=$G_VER;?>"></script>

<?php if ($results_page == 'FORMS_RESULTS') { ?>

	<script type="text/javascript" src="results/js/forms_results.js<?=$G_VER;?>"></script>
	<script type="text/javascript" src="results/js/forms_results_data.js<?=$G_VER;?>"></script>

<?php } else { //RESULTS ?>

	<script type="text/javascript" src="results/js/results.js<?=$G_VER;?>"></script>
	<script type="text/javascript" src="results/js/results_data.js<?=$G_VER;?>"></script>
	<script type="text/javascript" src="results/js/results_data_intervals.js<?=$G_VER;?>"></script>

<?php } ?>

<link rel="stylesheet" type="text/css" href="results/css/results.css">
<?php if ($is_iframe) { ?>
<style type="text/css">
.container { margin-top:15px; }
#main { padding-bottom:30px; }
</style>
<?php } //if ($is_iframe) ?>
<script>
jQuery(function() {
	$(".help_formula").on('click', function() {
		$(this).parent().find(".formula_info_div").toggle();
	});
});
</script>
</head>
<body>

<?php 
// no header
// if (!$is_iframe) {
// 	require('php/inc.header.php');
// } 
?>

<?php if (!$is_iframe OR $is_iOS) { ?>
<div style="text-align:center;">
	<button type="button" class="home"> &nbsp; &nbsp; <?=$LANG->HOMEPAGE;?></button>
</div>
<?php } //if (!$is_iframe OR $is_iOS) ?>


<div id="loading" class="ajaxOverlay" style="display:none">
	<div class="ajaxMessage"><img src="img/ldg.gif"></div>
</div>

<?php /*sticky-side-buttons*/?>
<div id="ssb-container" class="ssb-btns-right ssb-anim-slide" style="z-index:999;">
	<ul class="ssb-light-hover">
	<?php /* we need the ssb-btn-1 and ssb-btn-6 to get the border-radius */ ?>
	<?php if ($results_page == 'FORMS_RESULTS') { ?>
		<li id="ssb-btn-1"><p><span class="fa fa-calendar"></span> <?=$LANG->RESULTS_TAB_PERIOD;?></p></li>
		<li id="ssb-btn-2"><p><span class="fa fa-list-alt"></span> <?=$LANG->RESULTS_TAB_ATHLETE_DATA;?></p></li>
		<li id="ssb-btn-3"><p><span class="fa fa-bar-chart"></span> <?=$LANG->RESULTS_TAB_DIAGRAM;?></p></li>
		<li id="ssb-btn-6"><p><span class="fa fa-refresh"></span> <?=$LANG->RESULTS_UPDATE_DIAGRAM;?></p></li>
	<?php } else { //RESULTS ?>
		<li id="ssb-btn-1"><p><span class="fa fa-floppy-o"></span> <?=$LANG->TEMPLATES;?></p></li>
		<li id="ssb-btn-2"><p><span class="fa fa-calendar"></span> <?=$LANG->RESULTS_TAB_PERIOD_N_DATA;?></p></li>
		<li id="ssb-btn-3"><p><span class="fa fa-list-alt"></span> <?=$LANG->RESULTS_TAB_ATHLETE_DATA;?></p></li>
		<li id="ssb-btn-4"><p><span class="fa fa-table"></span> <?=$LANG->RESULTS_TAB_INTERVAL_DATA;?></p></li>
		<li id="ssb-btn-5"><p><span class="fa fa-bar-chart"></span> <?=$LANG->RESULTS_TAB_DIAGRAM;?></p></li>
		<li id="ssb-btn-6"><p><span class="fa fa-refresh"></span> <?=$LANG->RESULTS_UPDATE_DIAGRAM;?></p></li>
	<?php } ?>
	</ul>
</div>


<?php /* headers removed after request
<div class="container">
<?php if (!$is_iframe) { ?>
	<div class="row h_title">
		<div class="col-md-12 main-title" style="text-align:center;">
			<b><?=$LANG->APP_NAME;?></b><span><?=$LANG->APP_INFO;?></span>
		</div>
	</div>
<?php } //if (!$is_iframe) ?>
	<div class="row">
		<div class="col-md-12 main-title" style="text-align:center;">
			<h1><?=$LANG->RESULTS;?></h1>
		</div>
	</div>
</div>
*/?>

<br>
