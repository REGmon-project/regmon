<?php 
$PATH_2_ROOT = '../';
require_once($PATH_2_ROOT.'_settings.regmon.php');
require($PATH_2_ROOT.'login/validate.php');

require($PATH_2_ROOT.'results/inc.results_functions.php');

//print_r($_POST); exit;

$id = (int)($_POST['id'] ?? 0); //forms_data_id
$athlete_id = (int)($_POST['athlete_id'] ?? $UID);
$group_id = (int)($_POST['group_id'] ?? $GROUP);



//if TRAINER we limit to forms that can see 
$where_trainer = '';
$where_trainer2 = '';
if ($TRAINER) { //from ajax.forms_data.php but little changed
	if ($athlete_id == '-1' OR $athlete_id != $UID) { //athlete-trainer
		if ($athlete_id == '-1') $athlete_id = $UID; //self as athlete-trainer
		$trainer_id = $UID;
		
		//Users2Trainers //get trainer selected forms
		$trainer_forms_selected_read_str = '';
		$trainer_forms_selected_write_str = '';
		$row = $db->fetchRow("SELECT forms_select_read, forms_select_write FROM users2trainers WHERE user_id = ? AND group_id = ? AND trainer_id = ?", array($athlete_id, $group_id, $trainer_id)); 
		if ($db->numberRows() > 0) {
			if ($row['forms_select_read'] != '') {
				//$trainer_forms_selected_read_str = $row['forms_select_read'];
				$trainer_forms_selected_read_str = "'".implode("','", explode(',', $row['forms_select_read']??''))."'";
			}
			if ($row['forms_select_write'] != '') {
				//$trainer_forms_selected_write_str = $row['forms_select_write'];
				$trainer_forms_selected_write_str = "'".implode("','", explode(',', $row['forms_select_write']??''))."'";
			}
		}
		if ($trainer_forms_selected_read_str == '') $trainer_forms_selected_read_str = '0';
		if ($trainer_forms_selected_write_str == '') $trainer_forms_selected_write_str = '0';
		
		$where_trainer = "AND CONCAT(category_id,'_',form_id) IN (".$trainer_forms_selected_read_str.") ";
		$where_trainer2 = "AND CONCAT(f2c.category_id,'_',f2c.form_id) IN (".$trainer_forms_selected_read_str.") ";
	}
}
//for all
if ($athlete_id == '-1') $athlete_id = $UID; //self as athlete-trainer



// get forms_data counts
$s_where = '';
$where_forms = '';
$where_form_cat = '';
//if ($id) $s_where = 'AND form_id = ?';
$Forms_with_Forms_Data_arr = array();
$forms_data = $db->fetch("SELECT COUNT(*) AS count, form_id, category_id 
FROM forms_data 
WHERE user_id = ? AND group_id = ? $s_where $where_trainer AND form_id > 0 AND status = 1 GROUP BY form_id, category_id", array($athlete_id, $group_id)); 
if ($db->numberRows() > 0)  {
	foreach ($forms_data as $form_data_columns) {
		if ($form_data_columns['count'] != '0') { //filter forms that not have entries
			$Forms_with_Forms_Data_arr[$form_data_columns['form_id']] = $form_data_columns['count'];
			if ($where_forms != '') {
				$where_forms .= ',';
			}
			$where_forms .= "'".$form_data_columns['category_id'].'_'.$form_data_columns['form_id']."'";
			if ($where_form_cat != '') {
				$where_form_cat .= ',';
			}
			$where_form_cat .= "'".$form_data_columns['category_id'].'_'.$form_data_columns['form_id']."'";
		}
	}
} 
if ($where_forms == '') {
	$where_forms = "'0'";
}
if ($where_form_cat == '') {
	$where_form_cat = "'0'";
}
$where_forms = "AND CONCAT(f2c.category_id,'_',f2c.form_id) IN (".$where_forms.")";
$where_form_cat = "AND CONCAT(fd.category_id,'_',fd.form_id) IN (".$where_form_cat.")";


// get available forms
$forms = array();
$rows = $db->fetch("SELECT f.id, f.name, f.data_names 
FROM forms f 
LEFT JOIN forms2categories f2c ON f2c.form_id = f.id 
LEFT JOIN categories c ON c.id = f2c.category_id 
WHERE f.status = 1 AND c.status = 1 $where_trainer2 $where_forms 
ORDER BY c.parent_id, c.sort, c.id, f.id", array());
if ($db->numberRows() > 0)  {
	foreach ($rows as $row) {
		$forms[$row['id']] = array($row['name'], json_decode($row['data_names'], true));
	}
}
//print_r($forms); exit;


$data = '';
$js_data = '';
$form_id_2_name = '';

if (count($forms)) //if have forms
{
	$first_time = '';
	$last_time = '';
	$date_from = '';
	$date_to = '';
	$series = array();
	$form_id_2_name_arr = array();
	
	//notes from calendar
	$notes = $db->fetch("SELECT * FROM notes WHERE showInGraph = 1 AND user_id=? AND group_id=? ORDER BY timestamp_start", array($athlete_id, $group_id));
	if ($db->numberRows() > 0)  {
		$form_id_2_name_arr['note'] = $LANG->NOTE;
		
		$cell_id = Formula__Get_ALPHA_id('data', 0, false);
		$series['note']['_1']['cell_id'] = $cell_id;
		$series['note']['_1']['num'] = 0;
		$series['note']['_1']['name'] = $LANG->NOTE;
		$series['note']['_1']['type'] = '_Text';
		
		$cell_id = Formula__Get_ALPHA_id('data', 1, false);
		$series['note']['_2']['cell_id'] = $cell_id;
		$series['note']['_2']['num'] = 1;
		$series['note']['_2']['name'] = $LANG->NOTE_PERIOD;
		$series['note']['_2']['type'] = '_Number';
		
		foreach ($notes as $note) {
			//FIXES ###########################################
			//calendar want full day if allDay:true --if same date and diff times not show in calendar
			$start = get_date_time_SQL($note['timestamp_start']);
			$end = get_date_time_SQL($note['timestamp_end']);
			if ($note['isAllDay']=='1') {
				if ($note['timestamp_end'] == '') { //if we not have timestamp_end --old notes
					$start_tmp = explode(' ', $note['timestamp_start']??'');
					$note['timestamp_end'] = $start_tmp[0].' 23:59:59';
				} 
				$end = date("Y-m-d H:i:s", strtotime($note['timestamp_end']) + 1); //end + 1sec bcz is 23:59:59
			}
			else {
				if ($note['timestamp_end'] == '') { //if we not have timestamp_end --old notes
					$end = date("Y-m-d H:i:s", (strtotime($note['timestamp_start']) + (60*60))); //new +60mins
				}
			}
			$diff = round((strtotime($end) - strtotime($start)) / 60);
			//FIXES ###########################################
			
			$series['note']['_1']['data'][] = array(strtotime($start).'000', $note['name'], strtotime($end).'000', $note['color']);
			
			$series['note']['_2']['data'][] = array(strtotime($start).'000', $diff, strtotime($end).'000', $note['color']);
		}
	}


	// get forms data to build data series
	$forms_data = $db->fetch("SELECT fd.form_id, fd.res_json, fd.timestamp_start 
FROM forms_data fd 
LEFT JOIN forms f ON f.id = fd.form_id 
WHERE fd.user_id = ? AND fd.group_id = ? AND fd.status = 1 $where_form_cat
ORDER BY f.name, fd.timestamp_start", array($athlete_id, $group_id)); 
	//echo "<pre>";print_r($forms_data);
	//echo "<pre>";print_r($forms);
	if ($db->numberRows() > 0)  {
		$form_id_last = '';
		foreach ($forms_data as $form_data_columns) {
			$form_id = $form_data_columns['form_id'];
			if (isset($forms[$form_id])) {
				$form_name = $forms[$form_id][0];
				$form_id_2_name_arr[$form_id] = $form_name;
				$form_data_names = (array)$forms[$form_id][1]; //array
				$res_json = (array)json_decode($form_data_columns['res_json'], true);
				
				//if we have less keys than we need  (wrong input) --> need to fix here
				if (count($res_json) != count($form_data_names) AND is_array($form_data_names)) {
					$base_res_json = array();
					//make an array with all keys
					foreach ($form_data_names as $key => $form_data_name) {
						$base_res_json[$key] = '';
					}
					//add the missing keys
					$res_json = $res_json + $base_res_json;
					//sort keys
					ksort($res_json);
				}
				
				if (count($res_json)) {
					$series_count = 0;
					foreach ($res_json as $key => $res) {
						$s_key = '_'.$key;
						$data_name = $form_data_names[$key][0]; //series_name = data_name
						$data_type = $form_data_names[$key][1];
						$data_time = strtotime($form_data_columns['timestamp_start']).'000'; //+date("Z")
						$data_input = $res;
						
						//EXTRA Fields ##########################################
						//Period had array ["16:23","17:23","01:00"]->[from,to,period]
						if ($data_type == '_Period' AND is_array($res)) {
							//we give from, to as extra fields
							//From
							$cell_id = Formula__Get_ALPHA_id('data', $series_count, false);
							$series[$form_id][$s_key.'_From']['cell_id'] = $cell_id;
							$series[$form_id][$s_key.'_From']['num'] = $series_count;
							$series[$form_id][$s_key.'_From']['name'] = $data_name.'_From';
							$series[$form_id][$s_key.'_From']['type'] = "_Time";
							$series[$form_id][$s_key.'_From']['data'][] = array($data_time, $res[0]); 
							$series_count++;
							//To
							$cell_id = Formula__Get_ALPHA_id('data', $series_count, false);
							$series[$form_id][$s_key.'_To']['cell_id'] = $cell_id;
							$series[$form_id][$s_key.'_To']['num'] = $series_count;
							$series[$form_id][$s_key.'_To']['name'] = $data_name.'_To';
							$series[$form_id][$s_key.'_To']['type'] = "_Time";
							$series[$form_id][$s_key.'_To']['data'][] = array($data_time, $res[1]); 
							$series_count++;
						}
						elseif ($data_type == '_Dropdown' OR $data_type == '_RadioButtons') { //2__optionString
							//we give String as extra field
							$cell_id = Formula__Get_ALPHA_id('data', $series_count, false);
							$series[$form_id][$s_key.'_S']['cell_id'] = $cell_id;
							$series[$form_id][$s_key.'_S']['num'] = $series_count;
							$series[$form_id][$s_key.'_S']['name'] = $data_name.'_S';
							$series[$form_id][$s_key.'_S']['type'] = "_Text";
							$data_input = explode('__', $res.''); //can be a single_value or Num__String
							if (isset($data_input[1])) { //Num__String
								$data_input = $data_input[1]; //get string only
							} else {
								$data_input = $data_input[0]; //get single_value
							}
							$series[$form_id][$s_key.'_S']['data'][] = array($data_time, $data_input); 
							$series_count++;
						}
						//EXTRA Fields ##########################################
						

						$cell_id = Formula__Get_ALPHA_id('data', $series_count, false);
						$series[$form_id][$s_key]['cell_id'] = $cell_id;
						$series[$form_id][$s_key]['num'] = $series_count;
						$series[$form_id][$s_key]['name'] = $data_name;
						$series[$form_id][$s_key]['type'] = $data_type;
						

						//Period had array ["16:23","17:23","01:00"]->[from,to,period]
						if ($data_type == '_Period' AND is_array($res)) {
							if ($res[2] != '') {
								//get Period only -> "01:00"
								$time = explode(':', $res[2]??'');
								$data_input = ((int)$time[0] * 60 + (int)$time[1]);
							}
							else $data_input = 0;
						}
						elseif ($data_type == '_Dropdown' OR $data_type == '_RadioButtons') { //2__optionString
							$data_input = explode('__', $res.'__');
							$data_input = $data_input[0]; //get number only
						}
						
						//convert to null -> empty numbers like "" 
						if (!in_array($data_type, array('_Text', '_Textarea', '_Date', '_Time', '_Period_From', '_Period_To'))) {
							if ($data_input == '' or $data_input == '""' or $data_input == "''") {
								$data_input = null;
							}
						}
						
						$series[$form_id][$s_key]['data'][] = array($data_time, $data_input); 
						$series_count++;
					}
				}
				$form_id_last = $form_data_columns['form_id'];
			}
		}
		//echo "<pre>"; print_r($series); echo "</pre>";
		

		$data = json_encode($series, JSON_NUMERIC_CHECK);
		$js_data .= substr($data.'', 1, -1); //we give the { } at the end in case it is empty

		echo '<select id="Select__Athlete_Data_'.$athlete_id.'" name="Select__Athlete_Data_'.$athlete_id.'" multiple="multiple"></select>'.
			' &nbsp; '.
			'<button id="Button__Athlete_Data__Add_'.$athlete_id.'" type="button" class="btn btn-primary btn-sm" style="margin-top:5px;">'.
				'<i style="font-size:15px; vertical-align:middle;" class="fa fa-plus"></i>'.
				'&nbsp;&nbsp;<b>'.$LANG->BUTTON_ADD.'</b>'.
			'</button>';
	} //if forms_data
	//if No Data
	else {
		echo '<div style="text-align:center; font-size:20px;" class="Error_No_Data">'.
				$LANG->RESULTS_NO_DATA.
			'</div>';
	}
	if (count($form_id_2_name_arr)) {
		$form_id_2_name = json_encode($form_id_2_name_arr);
		$form_id_2_name = substr($form_id_2_name.'', 1, -1); //we give the { } at the end in case it is empty
	}
} //if forms
//if No forms
else {
	echo '<div style="text-align:center; font-size:20px;" class="Error_No_Data">'.
			$LANG->RESULTS_NO_DATA.
		'</div>';
}


//set js global values
?>
<script>
$.extend(V_FORM_id_2_name, {<?=$form_id_2_name;?>});
V_FORMS_DATA['<?=$athlete_id;?>'] = {<?=$js_data;?>};
</script>
