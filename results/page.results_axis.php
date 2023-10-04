<?php 
$PATH_2_ROOT = '../';
require_once($PATH_2_ROOT.'_settings.regmon.php');
require($PATH_2_ROOT.'login/validate.php');

/** @var int $UID */
/** @var int $GROUP */
/** @var bool $ADMIN */
/** @var bool $LOCATION_ADMIN */
/** @var bool $GROUP_ADMIN */
/** @var bool $GROUP_ADMIN_2 */
/** @var array<array> $CONFIG */
/** @var mixed $LANG */
/** @var mixed $db */
/** @var string $G_VER */


$DEBUG = (int)($_GET['debug'] ?? 0);

$results_page = 'AXIS';
$athlete_id = $UID;
$group_id = $GROUP;

if (!$ADMIN AND !$LOCATION_ADMIN AND !$GROUP_ADMIN AND !$GROUP_ADMIN_2) {
	echo Exit_Message($LANG->NO_ACCESS_RIGHTS);
	exit;
}


//######################################################
//SAVE
$axis_saved_data = '';
$saves_html = '<select id="saved_select" name="saved_select" class="form-control">';
$saves = $db->fetch("SELECT * FROM templates_axis ORDER BY name", array());
if ($db->numberRows() > 0) {
	foreach ($saves as $save) {
		$saves_html .= '<option value="' . $save['id'] . '">' . $save['name'] . '</option>';
		if ($axis_saved_data != '') {
			$axis_saved_data .= ',';
		}
		$axis_saved_data .= $save['id'] . ':' . $save['data_json'];
	}
}
$saves_html .= '</select>';
$saves_html .= '<script>var axis_saved_data = {'.$axis_saved_data.'}</script>';	
//######################################################


//##################################################
$title = $LANG->RESULTS_PAGE_TITLE;
$no_forms_css = true;
require($PATH_2_ROOT.'php/inc.html_head.php');
//######################################################
?>
<?php /* //debug for mobiles with no console
<script type="text/javascript" src="https://getfirebug.com/firebug-lite-debug.js"></script>*/?>

<link type="text/css" rel="stylesheet" href="<?=$PATH_2_ROOT;?>js/plugins/chosen/chosen.min.css" />
<script type="text/javascript" src="<?=$PATH_2_ROOT;?>js/plugins/chosen/chosen.jquery.min.js"></script>
 
<link type="text/css" rel="stylesheet" href="<?=$PATH_2_ROOT;?>css/coolfieldset.css?<?=$G_VER;?>" />
<script type="text/javascript" src="<?=$PATH_2_ROOT;?>js/plugins/jquery.collapsibleFieldset.js?<?=$G_VER;?>"></script>

<link type="text/css" rel="stylesheet" href="<?=$PATH_2_ROOT;?>node_modules/bootstrap/dist/css/bootstrap-theme.min.css">
<script type="text/javascript" src="<?=$PATH_2_ROOT;?>node_modules/bootstrap/dist/js/bootstrap.min.js"></script>

<script type="text/javascript" src="<?=$PATH_2_ROOT;?>node_modules/moment/min/moment.min.js"></script>
<?php if ($LANG->LANG_CURRENT != 'en') { ?>
<script type="text/javascript" src="<?=$PATH_2_ROOT;?>js/overrides/moment/<?=$LANG->LANG_CURRENT;?>.js"></script>
<?php } ?>
<link type="text/css" rel="stylesheet" href="<?=$PATH_2_ROOT;?>node_modules/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css" />
<script type="text/javascript" src="<?=$PATH_2_ROOT;?>node_modules/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>

<link type="text/css" rel="stylesheet" href="<?=$PATH_2_ROOT;?>node_modules/bootstrap-multiselect/dist/css/bootstrap-multiselect.css"/>
<script type="text/javascript" src="<?=$PATH_2_ROOT;?>node_modules/bootstrap-multiselect/dist/js/bootstrap-multiselect.min.js"></script>
<link type="text/css" rel="stylesheet" href="<?=$PATH_2_ROOT;?>node_modules/bootstrap-colorpicker/dist/css/bootstrap-colorpicker.min.css">
<script type="text/javascript" src="<?=$PATH_2_ROOT;?>node_modules/bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js"></script>

<script type="text/javascript" src="<?=$PATH_2_ROOT;?>node_modules/jquery-validation/dist/jquery.validate.js"></script>
<?php if ($LANG->LANG_CURRENT != 'en') { ?>
<script type="text/javascript" src="<?=$PATH_2_ROOT;?>js/overrides/query-validation/messages_<?=$LANG->LANG_CURRENT;?>.min.js"></script>
<?php } ?>
<script type="text/javascript" src="<?=$PATH_2_ROOT;?>node_modules/bs-confirmation/bootstrap-confirmation.min.js"></script>

<script src="<?=$PATH_2_ROOT;?>node_modules/highcharts/highcharts.js"></script>
<script src="<?=$PATH_2_ROOT;?>node_modules/highcharts/modules/no-data-to-display.js"></script>

<script type="text/javascript" src="<?=$PATH_2_ROOT;?>js/lang_<?=$LANG->LANG_CURRENT;?>.js<?=$G_VER;?>"></script>
<script>
const Production_Mode = <?=($CONFIG['Production_Mode']?'true':'false');?>;
const V_RESULTS_PAGE = '<?=$results_page;?>';
const V_Athlete_id = '<?=$athlete_id;?>';
const V_Group_id = '<?= $group_id; ?>';
var V_DEBUG = <?=$DEBUG;?>;
</script>
<script type="text/javascript" src="<?=$PATH_2_ROOT;?>js/common.js<?=$G_VER;?>"></script>
<script type="text/javascript" src="<?=$PATH_2_ROOT;?>results/js/diagram_functions.js<?=$G_VER;?>"></script>
<script type="text/javascript" src="<?=$PATH_2_ROOT;?>results/js/axis.js<?=$G_VER;?>"></script>

<link rel="stylesheet" type="text/css" href="<?=$PATH_2_ROOT;?>results/css/results.css">
<style type="text/css">
.wiz-title { font-size:20px; }
#saved_select { width:100%; max-width:245px; }
</style>
</head>
<body>

<br>

<section class="container" id="main">
 <div id="wizard_container">
  <div id="top-wizard" style="padding:10px;">
	<div class="wiz-title"><?=$LANG->RESULTS_PAGE_Y_AXIS;?></div>
	<div class="shadow"></div>
  </div>
  <div id="middle-wizard" style="padding-top:30px; padding-left:10px; padding-right:10px;">

		
	<div class="row">
		<?php /* LEFT SIDE - DATA ################################################################ */?>
		<div class="col-sm-4">
			<fieldset id="fieldset_DATA" class="coolfieldset ft collapsible">
				<legend style="font-size:18px;" title="Collapse"><?=$LANG->RESULTS_Y_AXIS_DATA;?>&nbsp;<span class="collapsible-indicator"></span></legend>
				<div>
					<fieldset id="data1" class="coolfieldset dat">
						<legend><?=$LANG->RESULTS_Y_AXIS_VALUE;?> 1&nbsp;</legend>
						<div>
							<div class="form-group">
								<label><?=$LANG->RESULTS_Y_AXIS_VALUE_NAME;?></label>
								<input type="text" name="data_name[]" value="<?=$LANG->RESULTS_Y_AXIS_VALUE;?> 1" class="form-control"/>
							</div>  &nbsp; 
							<div class="form-group">
								<label><b><?=$LANG->RESULTS_Y_AXIS_VALUE;?></b></label>
								<b><input type="text" name="data_val[]" value="5" class="form-control" style="width:60px;"></b>
							</div>							
						</div>
					</fieldset>
					<fieldset id="data2" class="coolfieldset dat">
						<legend><?=$LANG->RESULTS_Y_AXIS_VALUE;?> 2&nbsp;</legend>
						<div>
							<div class="form-group">
								<label><?=$LANG->RESULTS_Y_AXIS_VALUE_NAME;?></label>
								<input type="text" name="data_name[]" value="<?=$LANG->RESULTS_Y_AXIS_VALUE;?> 2" class="form-control"/>
							</div>  &nbsp; 
							<div class="form-group">
								<label><b><?=$LANG->RESULTS_Y_AXIS_VALUE;?></b></label>
								<b><input type="text" name="data_val[]" value="10" class="form-control" style="width:60px;"></b>
							</div>							
						</div>
					</fieldset>
					<fieldset id="data3" class="coolfieldset dat">
						<legend><?=$LANG->RESULTS_Y_AXIS_VALUE;?> 3&nbsp;</legend>
						<div>
							<div class="form-group">
								<label><?=$LANG->RESULTS_Y_AXIS_VALUE_NAME;?></label>
								<input type="text" name="data_name[]" value="<?=$LANG->RESULTS_Y_AXIS_VALUE;?> 3" class="form-control"/>
							</div> &nbsp; 
							<div class="form-group">
								<label><b><?=$LANG->RESULTS_Y_AXIS_VALUE;?></b></label>
								<b><input type="text" name="data_val[]" value="15" class="form-control" style="width:60px;"></b>
							</div>							
						</div>
					</fieldset>
				</div>
			</fieldset>
		</div>
		
		<?php /* RIGHT SIDE - AXIS ################################################################ */?>
		<div class="col-sm-8">
		  <form id="save_form">
			<fieldset id="fieldset_AXIS" class="coolfieldset ft">
				<legend style="font-size:18px;"><?=$LANG->RESULTS_Y_AXIS_GROUP;?>&nbsp;</legend>
				<div>
					<fieldset id="axis" class="coolfieldset axs">
						<legend><?=$LANG->RESULTS_Y_AXIS;?>&nbsp;</legend>
						<div>
							<div class="form-group">
								<label><?=$LANG->RESULTS_Y_AXIS_LABEL;?></label>
								<input type="text" id="axis_name" value="" class="form-control" style="width:170px;" required />
								<input type="hidden" id="axis_id" value=""/>
							</div> 
							<div class="form-group">
								<label><?=$LANG->RESULTS_Y_AXIS_POSITION;?></label>
								<select id="axis_pos_sel" class="form-control">
									<option value="false"><?=$LANG->RESULTS_Y_AXIS_POS_LEFT;?></option>
									<option value="true"><?=$LANG->RESULTS_Y_AXIS_POS_RIGHT;?></option>
								</select>
							</div> 
							<div class="form-group">
								<label><?=$LANG->RESULTS_COLOR;?></label>
								<span class="fa fa-close color_remove cpAx" style="cursor:pointer; position:relative; margin:29px 2px -24px 0; float:right; color:#dddddd;"></span>
								<input type="text" id="axis_color" value="" class="form-control cpA" style="width:80px; color:white; text-shadow:black 1px 1px;" placeholder="<?=$LANG->RESULTS_AUTO;?>"/>
							</div> 
							<div class="form-group">
								<label><?=$LANG->RESULTS_Y_AXIS_GRID_WIDTH;?></label>
								<select id="axis_grid_sel" class="form-control" style="width:80px;">
									<option value="0">0 px</option>
									<option value="1">1 px</option>
									<option value="2">2 px</option>
									<option value="3">3 px</option>
									<option value="4">4 px</option>
									<option value="5">5 px</option>
								</select>
							</div> 
							<div class="form-group">
								<label><?=$LANG->RESULTS_Y_AXIS_MIN;?></label>
								<input type="text" id="axis_min" value="" class="form-control" style="width:42px;" placeholder="<?=$LANG->RESULTS_AUTO;?>"/>
							</div> 
							<div class="form-group">
								<label><?=$LANG->RESULTS_Y_AXIS_MAX;?></label>
								<input type="text" id="axis_max" value="" class="form-control" style="width:42px;" placeholder="<?=$LANG->RESULTS_AUTO;?>"/>
							</div> 
						</div>
					</fieldset>
				</div>
			</fieldset>

			<fieldset id="fieldset_SAVE" class="coolfieldset ft">
				<legend style="font-size:18px;"><?=$LANG->RESULTS_Y_AXIS_SAVE;?>&nbsp;</legend>
				<div>
					<?php /* SAVE */?>
					<div class="form-group" style="width:100%; position:relative;">
						<label><?=$LANG->RESULTS_Y_AXIS_SAVE_NAME;?></label><br>
						<input type="text" id="save_name" name="save_name" value="" class="form-control" style="width:100%; max-width:318px; margin-bottom:5px; display:inline-block;" required />
						<button id="save_selected" type="button" class="btn btn-success btn-sm" style="display:inline-block; margin-top:-3px;"><i style="font-size:17px; vertical-align:middle;" class="fa fa-floppy-o"></i>&nbsp;&nbsp;<b><?=$LANG->SAVE;?></b></button>
					</div>
					<div class="form-group" style="width:100%; padding-top:10px;">
						<span id="saved_select_container">
							<?=$saves_html;?>
						</span>
						<span style="white-space:nowrap;">
							<button id="load_saved" type="button" class="btn btn-info btn-sm" style="margin-bottom:5px;"><i style="font-size:17px; vertical-align:middle;" class="fa fa-repeat"></i>&nbsp;&nbsp;<b><?=$LANG->LOAD;?></b></button>
							<button id="delete_saved" type="button" class="btn btn-danger btn-sm" style="margin-bottom:5px;"><i style="font-size:17px; vertical-align:middle;" class="fa fa-times-circle"></i>&nbsp;&nbsp;<b><?=$LANG->DELETE;?></b></button>
						<span>
					</div>
				</div>
			</fieldset>

			<div style="text-align:right;">
				<span class="help_colors" title="<img src='<?=$PATH_2_ROOT;?>img/highcharts_colors.png' class='img_colors'/>">
				<span class="help_question">?</span> <?=$LANG->RESULTS_INFO_STANDARD_COLORS;?></span> &nbsp; &nbsp; 
				<span class="help_lines" title="<img src='<?=$PATH_2_ROOT;?>img/chart_lines_<?=$LANG->LANG_CURRENT;?>.png' class='img_lines'/>">
					<span class="help_question">?</span> <?=$LANG->RESULTS_INFO_LINES;?>
				</span>
			</div>
		  </form>
		</div>
	</div>
	<div style="text-align:center;">
		<button id="Button__Chart__Update" type="button" class="btn btn-success btn-md"><i style="font-size:17px;" class="fa fa-refresh"></i>&nbsp;&nbsp;<b><?=$LANG->RESULTS_UPDATE_DIAGRAM;?></b></button>
	</div>
	
	<hr style="margin:10px 0;">

	<div id="cont_graph" style="min-width:300px; height:500px; margin:0 auto;"></div>


  </div>
 </div>
</section>

</body>
</html>