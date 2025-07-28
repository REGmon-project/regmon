<?php //Diagram, Axis, js Data 

//from inc.results_top.php
/** @var bool $is_iframe  */
/** @var int $template */
/** @var int $group_id */
/** @var int $athlete_id */
/** @var int $cat_id */
/** @var int $form_id */
//from forms_result.php
/** @var string $results_page */
/** @var string $u_vorname */
/** @var string $u_name */
/** @var int $template_id */

if ($SEC_check != $CONFIG['SEC_Page_Secret']) exit;
?>

<div class="row">
	
	<?php // Diagram button accordion?>
	<div class="col-sm-12">
		<div class="panel-group" id="accordion_diagram">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion_diagram" href="#C_Diagramm" id="C_Diagramm_link"><span class="fa fa-bar-chart"></span>&nbsp; <?=$LANG->RESULTS_TAB_DIAGRAM;?></a>
					</h4>
				</div>
				<div id="C_Diagramm" class="panel-collapse collapse in">
					<div class="panel-body" style="padding:10px 0;">
						<div id="C_Diagramm_div" style="overflow:auto; position:relative;">
							<span class="graph_fullscreen">
								<span class="fa-stack expand" onclick="openFullscreen(this, 'C_Diagramm_div');" title="<?=$LANG->DIAGRAM_FULLSCREEN_ON;?>"><i class="fa fa-expand"></i></span>
								<span class="fa-stack collapse" onclick="closeFullscreen(this, 'C_Diagramm_div');" title="<?=$LANG->DIAGRAM_FULLSCREEN_OFF;?>" style="display:none;"><i class="fa fa-compress"></i></span>
							</span>
						
							<div id="container_graph" style="min-width:300px; height:500px; margin:0 auto;"></div>
						</div>

<?php 
//##########################################################
//AXIS
$i = 0;
$axis_data = '{';
$axis_html = '';
$axis_html_2 = ''; //with axis_'id'
$html_axis_table_first = '';
$html_axis_table_rest = '';
$axis_rows = $db->fetch("SELECT * FROM templates_axis ORDER BY name", array()); 
if ($db->numberRows() > 0) {
	foreach ($axis_rows as $axis) {
		//dropdown ###############################
		$selected = '';
		//if ($form_id == $axis['id']) $selected = ' selected'; //select self
		$axis_html .= '<option value="'.$axis['id'].'"'.$selected.'>'.$axis['name'].'</option>';
		$axis_html_2 .= '<option value="axis_'.$axis['id'].'"'.$selected.'>'.$axis['name'].'</option>';
		if ($i != 0) $axis_data .= ',';
		$axis_data .= $axis['id'].':'.$axis['data_json'];
		$i++;
		
		//all axis table #########################
		$data_json = (array)json_decode($axis['data_json'], true);
		$axis_json = (array)$data_json['axis'];
		$axis_pos = ($axis_json['pos']=='true' ? $LANG->RIGHT : $LANG->LEFT);
		$axis_color_style = ($axis_json['color'] ? ' style="background:'.$axis_json['color'].';"' : '');
		$html_axis_tmp = ''.
			'<tr id="axis_'.$axis['id'].'">'.
				'<td style="display:none;">'.$axis['id'].'</td>'.
				'<td>'.$axis['name'].'</td>'.
				'<td>'.$axis_json['name'].'</td>'.
				'<td>'.$axis_pos.'</td>'.
				'<td class="axis_color"'.$axis_color_style.'>'.$axis_json['color'].'</td>'.
				'<td class="num">'.$axis_json['grid'].' px</td>'.
				'<td class="num">'.$axis_json['min'].'</td>'.
				'<td class="num">'.$axis_json['max'].'</td>'.
			'</tr>';
		if ($axis['id'] == '1') {
			$html_axis_table_first = $html_axis_tmp; //id=1 Auto Y-Axis -> first
		}
		else $html_axis_table_rest .= $html_axis_tmp;
	}
}
$axis_data .= '}';
//##########################################################
?>
						<?php // Axis button accordion?>
						<div class="col-sm-12">
							<div class="panel-group" id="accordion_axis">
								<div class="panel panel-default">
									<div class="panel-heading">
										<h4 class="panel-title">
											<a data-toggle="collapse" data-parent="#accordion_axis" href="#C_Axis" id="C_Axis_link"><span class="fa-stack" style="height:18px; line-height:18px;"><i class="fa fa-long-arrow-up fa-stack-1x" style="top:-2px; left:-2px;"></i><i class="fa fa-long-arrow-right fa-stack-1x" style="top:2px; left:2px;"></i></span>&nbsp; <?=$LANG->RESULTS_TAB_Y_AXIS;?></a>
										</h4>
									</div>
									<div id="C_Axis" class="panel-collapse collapse in">
										<div class="panel-body">
												
												
	<fieldset id="fieldset_AXIS" class="coolfieldset fieldset2"<?=(($ATHLETE OR $TRAINER)?' style="display:none"':'');//hide to Athletes?>>
		<legend style="font-size:18px;"><?=$LANG->RESULTS_Y_AXIS_GROUP;?>&nbsp;</legend>
		<div>
			<span class="Fieldsets__Expand_Collapse" title="<?=$LANG->RESULTS_COLLAPSE_EXPAND_ALL;?>"><i class="fa fa-minus-square-o close_all"></i><i class="fa fa-plus-square-o open_all" style="display:none;"></i></span>
			<div style="margin-top:-10px;">&nbsp;</div>
			<?php /* dummy Axis Select for cloning to Forms */?>
			<div class="form-group axs_sel" style="display:none;">
				<label><?=$LANG->RESULTS_Y_AXIS;?></label>
				<select name="data_axis_sel[]" class="form-control Select_Axis">
					<?=$axis_html_2;?>
				</select>
			</div>
			<?php /* ADD AXIS */?>
			<span id="New_Axis_placeholder" style="display:none;"></span>
			<br>
			<div class="form-group" style="width:100%;">
				<span id="Select_Axis_container">
					<select class="Select_Axis form-control">
						<?=$axis_html;?>
					</select>
				</span>
				<button id="Axis__Load" type="button" class="btn btn-info" style="padding:4px 10px; margin-bottom:4px;"><i style="font-size:17px; vertical-align:text-bottom;" class="fa fa-repeat"></i>&nbsp;&nbsp;<b><?=$LANG->LOAD;?></b></button>
				<button id="New_YAxis" type="button" class="btn btn-primary fancybox fancybox.iframe" href="results/page.results_axis.php" style="padding:4px 10px; float:right; margin-bottom:4px;"><i style="font-size:15px; vertical-align:middle;" class="fa fa-plus"></i>&nbsp;&nbsp;<b><?=$LANG->RESULTS_Y_AXIS_CREATE;?></b></button>
			</div>
			
			<fieldset class="coolfieldset collapsed">
				<legend style="text-align:left;"><?=$LANG->RESULTS_Y_AXIS_SHOW_ALL;?>&nbsp;</legend>
				<div style="display:none;">
					<table id="axis_table" class="data_table" style="background:white; width:100%">
						<tr><th colspan="7"><?=$LANG->RESULTS_Y_AXIS;?></th></tr>
						<tr>
							<th><?=$LANG->RESULTS_Y_AXIS_SAVE_NAME;?></th>
							<th><?=$LANG->RESULTS_Y_AXIS_LABEL;?></th>
							<th><?=$LANG->RESULTS_Y_AXIS_POSITION;?></th>
							<th><?=$LANG->RESULTS_COLOR;?></th>
							<th><?=$LANG->RESULTS_Y_AXIS_GRID_WIDTH;?></th>
							<th><?=$LANG->RESULTS_Y_AXIS_MIN;?></th>
							<th><?=$LANG->RESULTS_Y_AXIS_MAX;?></th>
						</tr>
						<?=$html_axis_table_first;?>
						<?=$html_axis_table_rest;?>
					</table>
				</div>
			</fieldset>
			
		</div>
	</fieldset>

										
										</div>
									</div>
								</div>
							</div><?php //accordion_diagram?>
						</div>
						<?php // Axis button accordion end ?>
						
					</div>
				</div>
			</div>
		</div><?php //accordion_diagram?>
	</div>
		
</div><?php //row?>





<?php 
//#############################################################
//SAVES
$Forms_Templates_Data = '';
//can see all at the moment
$saves = $db->fetchAllwithKey2("SELECT id, form_id, name, data_json FROM templates_forms ORDER BY form_id, name", array(), 'form_id', 'id'); 
if ($db->numberRows() > 0) {
	foreach ($saves as $fid => $save_id) {
		$saves_tmp = '';
		foreach ($save_id as $save_id => $save) {
			$data_json = substr($save['data_json'], 1, -1); //remove {}
			if ($saves_tmp != '') {
				$saves_tmp .= ',';
			}
			$saves_tmp .= '"'.$save_id.'":{"name":"'.$save['name'].'",'.$data_json.'}';
		}
		if ($Forms_Templates_Data != '') {
			$Forms_Templates_Data .= ',';
		}
		$Forms_Templates_Data .= '"'.$fid.'":{'.$saves_tmp.'}';
	}
}

$templates_data = '';
if ($results_page == 'RESULTS') {
	//can see all at the moment
	$saves2 = $db->fetchAllwithKey("SELECT id, name, data_json FROM templates_results ORDER BY name", array(), 'id'); 
	//GlobalView, LocationView, GroupView, TrainerView, Private
	if ($db->numberRows() > 0) {
		$templates_data = '';
		foreach ($saves2 as $save_id => $save) {
			// $perms = '['.
			// 	$save['GlobalView'].','.
			// 	$save['LocationView'].','.
			// 	$save['GroupView'].','.
			// 	$save['TrainerView'].','.
			// 	$save['Private'].
			// ']';
			
			$data_json = substr($save['data_json'], 1, -1); //remove {}
			if ($templates_data != '') {
				$templates_data .= ',';
			}

			//$templates_data .= '"'.$save_id.'":{"name":"'.$save['name'].'", "perms":'.$perms.', '.$data_json.'}';
			$templates_data .= '"'.$save_id.'":{"name":"'.$save['name'].'", '.$data_json.'}';
		}
	}
}

$Forms_Templates_Data = '{'.$Forms_Templates_Data.'}';
if ($results_page == 'RESULTS') {
	$templates_data = '{'.$templates_data.'}';
} else { //$results_page == 'FORMS_RESULTS'
	$templates_data = '{}';
}
//############################################################
?>

<script>
const V_LANG_CURRENT = '<?=$LANG->LANG_CURRENT;?>';
const V_GROUP = <?=(int)$GROUP;?>;
const V_UID = <?=(int)$UID;?>;
const V_ATHLETE = <?=($ATHLETE?'true':'false');?>;
const V_ATHLETE_TRAINER = <?=(($ATHLETE OR $TRAINER)?'true':'false');?>;
const V_IS_IFRAME = <?=($is_iframe?'true':'false');?>;

var V_FORM_id_2_name = {}; <?php //form names -> php?>
var V_FORMULA_cell_2_name = {}; <?php //form fields names -> php?>
var V_FORMS_DATA = {}; <?php //form data by athlete_id -> php?>
var V_USED_DATA = {}; <?php //form data we working on -> js?>
var V_INTERVAL_DATA = {}; <?php //interval data -> js?>
var V_AXIS_DATA = <?=$axis_data;?>; <?php //axis data -> php?>
var V_OPEN_TEMPLATE = '<?=$template;?>';
var V_AUTO_UPDATE_CHART = <?=($template ? 'true' : 'false');?>;

<?php if ($results_page == 'FORMS_RESULTS') { ?>
const V_Group_id = '<?=$group_id;?>';
const V_Athlete_id = '<?=$athlete_id;?>';
const V_Athlete_Name = '<?=$u_vorname.' '.$u_name;?>';
const V_Category_id = '<?=$cat_id;?>';
const V_Form_id = '<?=$form_id;?>';
var V_Template_id = '<?=$template_id;?>';
var V_Auto_Init = <?=($is_iframe?'true':'false');?>;
<?php } else { //RESULTS ?>
const V_Group_id = '<?=$group_id;?>';
const V_Athlete_id = '<?=$athlete_id;?>';
var V_FORMS_N_FIELDS = {}; <?php //form fields,users -> php?>
<?php } ?>
</script>

<div id="add_data" class="hidden">
<script>
const V_FORMS_TEMPLATES = <?=$Forms_Templates_Data;?>;
const V_RESULTS_TEMPLATES = <?=$templates_data;?>;
</script>
</div>
