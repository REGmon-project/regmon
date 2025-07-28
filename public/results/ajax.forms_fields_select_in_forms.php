<?php // ajax Forms Fields Select (in Form optgroup) for selected Athletes and Forms

$PATH_2_ROOT = '../';
require_once($PATH_2_ROOT.'_settings.regmon.php');
require($PATH_2_ROOT.'login/validate.php');

require($PATH_2_ROOT.'results/inc.results_functions.php');


$selected_athletes_ids = $_POST['athletes_ids'] ?? false;
$date_from = $_POST['date_from'] ?? false;
$date_to = $_POST['date_to'] ?? false;
$selected_forms_ids = $_POST['forms_ids'] ?? false;

if (!$selected_athletes_ids OR 
	$selected_athletes_ids == "''" OR 
	!$date_from OR 
	!$date_to OR 
	!$selected_forms_ids) 
{
	echo get_No_Data_Error();
	exit;
}

//new --we get $selected_forms_ids as category_form now
if (substr_count($selected_forms_ids, '_') > 0) { //new way  --leave as is for the old way
	$selected_forms_ids_arr = explode(',', $selected_forms_ids);
	$selected_forms_ids = array();
	foreach($selected_forms_ids_arr as $forms_id){
		$forms_id_arr = explode('_', $forms_id);
		//get unique form_id
		$selected_forms_ids[$forms_id_arr[1]] = $forms_id_arr[1];
	}
	$selected_forms_ids = implode(',', $selected_forms_ids);
}

// get forms_data counts  --check DATE again
$where_forms = '';
$Forms_with_Forms_Data_arr = array();
$forms_data = $db->fetch("SELECT COUNT(*) AS count, form_id, category_id 
FROM forms_data 
WHERE CONCAT(group_id,'_',user_id) IN ($selected_athletes_ids) 
AND form_id > 0 AND status = 1 
AND timestamp_start >= '$date_from' AND timestamp_start <= '$date_to' 
GROUP BY form_id, category_id 
ORDER BY category_id, form_id", array());
if ($db->numberRows() > 0)  {
	foreach ($forms_data as $form_data_columns) {
		$Forms_with_Forms_Data_arr[$form_data_columns['form_id']] = $form_data_columns['count'];
		if ($where_forms != '') {
			$where_forms .= ',';
		}
		//$where_forms .= "'".$form_data_columns['category_id'].'_'.$form_data_columns['form_id']."'";
		$where_forms .= $form_data_columns['form_id'];
	}
}
else {
	$where_forms = '0';
}



//##################################################
//SAVES
$Forms_Templates_Data = array();
//can see all at the moment
//$saves = $db->fetch("SELECT * FROM templates_forms WHERE user_id=? AND group_id=? ORDER BY form_id, name", array($athlete_id, $group_id)); 
$saves = $db->fetchAllwithKey2("SELECT id, form_id, name 
FROM templates_forms 
WHERE form_id IN ($selected_forms_ids) AND form_id IN ($where_forms) 
ORDER BY form_id, name", array(), 'form_id', 'id'); //, data_json
if ($db->numberRows() > 0) {
	foreach ($saves as $fid => $save_id) {
		$saves_tmp = '';
		foreach ($save_id as $save_id => $save) {
			$Forms_Templates_Data[$fid][$save_id] = $save['name'];
		}
	}
}
//print_r($Forms_Templates_Data);
//##################################################



$forms_fields_option = '';
$forms_fields_option_Notes = '';

// get available forms
$forms = array();
$rows = $db->fetch("SELECT id, name, data_names 
FROM forms 
WHERE status = 1 AND id IN ($selected_forms_ids) AND id IN ($where_forms) 
ORDER BY name", array());
//print_r($rows); exit;
if ($db->numberRows() > 0)  {
	//html support only one level optgroup
	$FLD_open_group = false;
	$FLD_group = '';
	$FLD_group_last = '';
	$FLD_group_last_id = 0;
	foreach ($rows as $row) {
		$FLD_group = $row['name'];
		//Group
		if ($FLD_group <> $FLD_group_last) {
			if ($FLD_open_group) {
				$forms_fields_option .= '</optgroup>';
				//saves
				if (isset($Forms_Templates_Data[$FLD_group_last_id])) {
					$forms_fields_option .= '<optgroup label="'.$FLD_group_last.'-'.$LANG->TEMPLATES.'">';
					foreach ($Forms_Templates_Data[$FLD_group_last_id] as $save_id => $save_name) {
						$forms_fields_option .= '<option value="SV_'.$FLD_group_last_id.'_'.$save_id.'">'.$save_name.'</option>';
					}
					$forms_fields_option .= '</optgroup>';
				}
			}
			$forms_fields_option .= '<optgroup label="'.$FLD_group.' ('.$Forms_with_Forms_Data_arr[$row['id']].')">';
			$FLD_open_group = true;
		}
		//options
		$fields_arr = (array)json_decode($row['data_names'], true);
		foreach ($fields_arr as $key => $field) {
			//EXTRA Fields ///////////////////////////////////////
			if ($field[1] == '_Period') {
				$forms_fields_option .= '<option value="'.$row['id'].'_'.$key.'_From">'.$field[0].'_'.$LANG->FROM.'</option>';
				$forms_fields_option .= '<option value="'.$row['id'].'_'.$key.'_To">'.$field[0].'_'.$LANG->TO.'</option>';
			}
			if ($field[1] == '_Dropdown' OR $field[1] == '_RadioButtons') {
				$forms_fields_option .= '<option value="'.$row['id'].'_'.$key.'_S">'.$field[0].'_S</option>';
			}
			//EXTRA Fields ///////////////////////////////////////
			$forms_fields_option .= '<option value="'.$row['id'].'_'.$key.'">'.$field[0].'</option>';
		}
		$FLD_group_last = $FLD_group;
		$FLD_group_last_id = $row['id'];
	}
	if ($FLD_open_group) {
		$forms_fields_option .= '</optgroup>';
		//saves for the last form
		if (isset($Forms_Templates_Data[$FLD_group_last_id])) {
			$forms_fields_option .= '<optgroup label="'.$FLD_group_last.'-'.$LANG->TEMPLATES.'">';
			foreach ($Forms_Templates_Data[$FLD_group_last_id] as $save_id => $save_name) {
				$forms_fields_option .= '<option value="SV_'.$FLD_group_last_id.'_'.$save_id.'">'.$save_name.'</option>';
			}
			$forms_fields_option .= '</optgroup>';
		}
	}
	
	//notes
	$notes = $db->fetchRow("SELECT COUNT(*) AS count FROM notes 
WHERE showInGraph = 1 AND CONCAT(group_id,'_',user_id) IN ($selected_athletes_ids) AND timestamp_start >= '$date_from' AND timestamp_start <= '$date_to' 
ORDER BY timestamp_start", array());
	if ($db->numberRows() > 0)  {
		if ($notes['count']) {
			$forms_fields_option_Notes .= '<optgroup label="'.$LANG->NOTE.' ('.$notes['count'].')">';
			$forms_fields_option_Notes .= '<option value="note_1">'.$LANG->NOTE.'</option>';
			$forms_fields_option_Notes .= '<option value="note_2">'.$LANG->NOTE_PERIOD.'</option>';
			$forms_fields_option_Notes .= '</optgroup>';
		}
	}
}
?>

<span id="Select__Forms_Fields__Row__Span">
	<hr style="margin:10px;">
	<div id="Select__Forms_Fields__Row">
		<span class="wiz-title"><?=$LANG->RESULTS_SELECT_FIELDS;?> : &nbsp; </span>
		<select id="Select__Forms_Fields" name="Select__Forms_Fields" multiple="multiple" style="display:none;">
			<?=$forms_fields_option_Notes . $forms_fields_option;?>
		</select> &nbsp; 
<?php if ($forms_fields_option != '') { ?>
		<button id="Button__Select__Forms_Fields__Submit" class="forward" title="<?= $LANG->RESULTS_BUTTON_APPLY_CHANGES;?>"></button>
<?php } else { //change Loading to NoData ?>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<?=get_No_Data_Error();?>
<?php } ?>
	</div>
</span>
