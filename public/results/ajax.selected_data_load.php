<?php 
$PATH_2_ROOT = '../';
require_once($PATH_2_ROOT.'_settings.regmon.php');
require($PATH_2_ROOT.'login/validate.php');

require($PATH_2_ROOT.'results/inc.results_functions.php');

//print_r($_POST); exit;

$data_ids = $_POST['data_ids'] ?? false; // form_field, ...
$athletes_ids = $_POST['athletes_ids'] ?? false; // group_athlete, ...
$date_from = $_POST['date_from'] ?? false;
$date_to = $_POST['date_to'] ?? false;
if (!$data_ids OR !$athletes_ids OR $athletes_ids == "''" OR !$date_from OR !$date_to) {
	echo get_No_Data_Error();
	exit;
}

/** @var mixed $FORMS_DO */
$FORMS_DO = array();

$data_ids_arr = explode(',', $data_ids);
$forms_fields_2_show = array();
$selected_forms_arr = array();
$selected_SAVES_arr = array();
foreach ($data_ids_arr as $form_field) {
	$form_field_arr = explode('_', $form_field);
	$base_form_id = $form_field_arr[0];
	$field_id = $form_field_arr[1];

	if ($base_form_id == 'SV') { //saved template
		$base_form_id = $form_field_arr[1];
		$save_id = $form_field_arr[2];
		
		$selected_SAVES_arr[] = $save_id;
		$FORMS_DO['saves'][$base_form_id][] = $save_id;
	}
	elseif ($base_form_id == 'note') { //notes
		$FORMS_DO[$base_form_id][] = $field_id;
	}
	else { //form field
		if (!in_array($base_form_id, $selected_forms_arr)) {
			$selected_forms_arr[] = $base_form_id;
		}

		$field_id_String = (isset($form_field_arr[2]) ? '_' . $form_field_arr[2] : '');
		
		$forms_fields_2_show[$base_form_id][] = $field_id . $field_id_String;
		$FORMS_DO[$base_form_id][] = $field_id . $field_id_String;
	}
}


//saved template form fields
$SAVES_selected_forms_arr = array();
$SAVES_forms_names = array();
if (count($selected_SAVES_arr)) 
{
	$selected_saves = implode(',', $selected_SAVES_arr);
	$saves = $db->fetchAll("SELECT id, form_id, name, data_json 
FROM templates_forms 
WHERE id IN ($selected_saves) 
ORDER BY form_id, name", array()); 
	if ($db->numberRows() > 0) {
		foreach ($saves as $save) {
			$SAVES_forms_names[ $save['id'] ] = $save['name'];
			$data_json = (array)(object)json_decode($save['data_json'], true);
			//echo "<pre>"; print_r($data_json['data']); echo "</pre>";
			foreach ($data_json['data'] as $data_key => $data_field) {
				//only form fields, not 'calculation' formulas
				if ($data_field['data_or_calc'] == 'data') {
					$base_form_id = $data_field['base_form_id'];
					if (!in_array($base_form_id, $SAVES_selected_forms_arr)) {
						$SAVES_selected_forms_arr[] = $base_form_id;
					}
				}
			}
		}
	}
}

$selected_forms = implode(',', $selected_forms_arr);
$SAVES_selected_forms = implode(',', $SAVES_selected_forms_arr);

//print_r($SAVES_forms_names);
//print_r($selected_forms_arr);
//print_r($SAVES_selected_forms_arr);

if ($selected_forms == '') {
	$selected_forms = 0;
}
if ($SAVES_selected_forms == '') {
	$SAVES_selected_forms = 0;
}



// get available forms
$forms = array();
$rows = $db->fetch("SELECT id, name, data_names 
FROM forms 
WHERE status = 1 AND id IN ($selected_forms) OR id IN ($SAVES_selected_forms) 
ORDER BY name", array());
if ($db->numberRows() > 0)  {
	foreach ($rows as $row) {
		$forms[$row['id']] = array(
			$row['name'], 
			json_decode($row['data_names'], true), 
			array()
		);
	}
}
//print_r($forms);

$js_data = '';
$forms_n_fields = array();
$series = array();


//notes from calendar #################################################
if (isset($FORMS_DO['note'])) {
	$notes = $db->fetch("SELECT * 
FROM notes 
WHERE showInGraph = 1 AND CONCAT(group_id,'_',user_id) IN ($athletes_ids) 
AND timestamp_start >= '$date_from' AND timestamp_start <= '$date_to' 
ORDER BY user_id, timestamp_start", array());
	if ($db->numberRows() > 0)  {
		$form_id = 'note';
		$form_id_2_name[$form_id] = $LANG->NOTE;
		$forms_n_fields[$form_id] = array($LANG->NOTE, array(), array()); //add form and init arrays + form name
		
		foreach ($notes as $note) {
			$user_id = $note['user_id'];
			
			//FIXES ########################################
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
					$end = date("Y-m-d H:i:s", (strtotime($note['timestamp_start']) + (60 * 60))); //new +60mins
				}
			}
			$diff = round((strtotime($end) - strtotime($start)) / 60);
			//FIXES ########################################
			
			$cell_id = Formula__Get_ALPHA_id('data', 0, false);
			$series[$user_id][$form_id]['_1']['cell_id'] = $cell_id;
			$series[$user_id][$form_id]['_1']['num'] = 0;
			$series[$user_id][$form_id]['_1']['name'] = $LANG->NOTE;
			$series[$user_id][$form_id]['_1']['type'] = '_Text';
			$series[$user_id][$form_id]['_1']['data'][] = array(strtotime($start).'000', $note['name'], strtotime($end).'000', $note['color']);
			
			$cell_id = Formula__Get_ALPHA_id('data', 1, false);
			$series[$user_id][$form_id]['_2']['cell_id'] = $cell_id;
			$series[$user_id][$form_id]['_2']['num'] = 1;
			$series[$user_id][$form_id]['_2']['name'] = $LANG->NOTE_PERIOD;
			$series[$user_id][$form_id]['_2']['type'] = '_Number';
			$series[$user_id][$form_id]['_2']['data'][] = array(strtotime($start).'000', $diff, strtotime($end).'000', $note['color']);
			
			if (!in_array($user_id, $forms_n_fields[$form_id][2])) {
				$forms_n_fields[$form_id][2][] = $user_id; //add user to this form users
			}
			if (!array_key_exists('_1', $forms_n_fields[$form_id][1])) {
				$forms_n_fields[$form_id][1]['_1'][0] = $LANG->NOTE;
				$forms_n_fields[$form_id][1]['_1'][1] = '_Text';
				$forms_n_fields[$form_id][1]['_1'][2] = 'RA';
			}
			if (!array_key_exists('_2', $forms_n_fields[$form_id][1])) {
				$forms_n_fields[$form_id][1]['_2'][0] = $LANG->NOTE_PERIOD;
				$forms_n_fields[$form_id][1]['_2'][1] = '_Number';
				$forms_n_fields[$form_id][1]['_2'][2] = 'RB';
			}
		}
	}
}
//notes from calendar end #################################################



// get forms data to build data series
$forms_data = $db->fetch("SELECT fd.form_id, fd.res_json, fd.timestamp_start, fd.user_id 
FROM forms_data fd 
LEFT JOIN forms f ON f.id = fd.form_id 
WHERE CONCAT(fd.group_id,'_',fd.user_id) IN ($athletes_ids) 
AND ( 
	fd.form_id IN ($selected_forms) OR 
	fd.form_id IN ($SAVES_selected_forms) 
) 
AND fd.form_id > 0 AND fd.status = 1 
AND fd.timestamp_start >= '$date_from' AND fd.timestamp_start <= '$date_to' 
ORDER BY fd.user_id, f.name, fd.timestamp_start", array()); 
//print_r($forms_data); exit;
if ($db->numberRows() > 0)  {
	$user_id_last = '';
	$form_id_last = '';
	$series_count = 0;
	$_field_key = '';
	foreach ($forms_data as $form_data_fields) 
	{
		$user_id = $form_data_fields['user_id'];
		$form_id = $form_data_fields['form_id'];
		$form_name = $forms[$form_id][0];
		$form_data_names = (array)$forms[$form_id][1]; //array
		
		//forms_n_fields array
		if (!array_key_exists($form_id, $forms_n_fields)) {
			//add form and init arrays + form name
			$forms_n_fields[$form_id] = array($form_name, array(), array());
		}
		if (!in_array($user_id, $forms_n_fields[$form_id][2])) {
			$forms_n_fields[$form_id][2][] = $user_id; //add user to this form users
		}
		foreach ($form_data_names as $n_key => $name_type) {
			//EXTRA Fields #####################################
			if ($name_type[1] == '_Period') {
				if (!array_key_exists('_'.$n_key.'_From', $forms_n_fields[$form_id][1])) {
					$forms_n_fields[$form_id][1]['_'.$n_key.'_From'] = array($name_type[0].'_From', "_Time");
				}
				if (!array_key_exists('_'.$n_key.'_To', $forms_n_fields[$form_id][1])) {
					$forms_n_fields[$form_id][1]['_'.$n_key.'_To'] = array($name_type[0].'_To', "_Time");
				}
			}
			if ($name_type[1] == '_Dropdown' OR $name_type[1] == '_RadioButtons') {
				if (!array_key_exists('_'.$n_key.'_S', $forms_n_fields[$form_id][1])) {
					$forms_n_fields[$form_id][1]['_'.$n_key.'_S'] = array($name_type[0].'_S', "_Text");
				}
			}
			//EXTRA Fields #####################################

			if (!in_array($user_id, $forms_n_fields[$form_id][2])) {
				//add user to this form users
				$forms_n_fields[$form_id][2][] = $user_id;
			}
			if (!array_key_exists('_'.$n_key, $forms_n_fields[$form_id][1])) {
				//add data names to this form
				$forms_n_fields[$form_id][1]['_'.$n_key] = $name_type;
			}
		}
		//forms_n_fields array
		

		//results
		$res_json = (array)json_decode($form_data_fields['res_json'], true);
		

		//if we have less keys than we need  (wrong input) --> need to fix here
		if (count($res_json) != 
		count($form_data_names)) {
			$base_res_json = array();
			//we make an array with all keys
			foreach ($form_data_names as $key => $form_data_name) {
				$base_res_json[$key] = '';
			}
			//we add the missing keys
			$res_json = $res_json + $base_res_json;
			//sort keys
			ksort($res_json);
		}
		

		if (count($res_json)) {
			$series_count = 0;
			foreach ($res_json as $field_key => $field_data) {
				$_field_key = '_' . $field_key;
				$data_name = $form_data_names[$field_key][0]; //series_name = data_name
				$data_type = $form_data_names[$field_key][1];
				$data_time = strtotime($form_data_fields['timestamp_start']) . '000';
				$data_input = $field_data;
				
				//EXTRA Fields #####################################
				//Period have array ["16:23","17:23","01:00"]->[from,to,period]
				if ($data_type == '_Period' AND is_array($field_data)) {
					//we give _From, _To as extra fields

					//From
					//get Cell_id for this field
					$cell_id = Formula__Get_ALPHA_id('data', $series_count, false);
					$forms_n_fields[$form_id][1][$_field_key . '_From'][2] = $cell_id;
					//series
					$series[$user_id][$form_id][$_field_key . '_From']['num'] = $series_count;
					$series[$user_id][$form_id][$_field_key . '_From']['cell_id'] = $cell_id;
					$series[$user_id][$form_id][$_field_key . '_From']['name'] = $data_name . '_From';
					$series[$user_id][$form_id][$_field_key . '_From']['type'] = '_Time';
					$series[$user_id][$form_id][$_field_key . '_From']['data'][] = array($data_time, $field_data[0]); 
					$series_count++;

					//To
					//get Cell_id for this field
					$cell_id = Formula__Get_ALPHA_id('data', $series_count, false);
					$forms_n_fields[$form_id][1][$_field_key . '_To'][2] = $cell_id;
					//series
					$series[$user_id][$form_id][$_field_key . '_To']['num'] = $series_count;
					$series[$user_id][$form_id][$_field_key . '_To']['cell_id'] = $cell_id;
					$series[$user_id][$form_id][$_field_key . '_To']['name'] = $data_name . '_To';
					$series[$user_id][$form_id][$_field_key . '_To']['type'] = '_Time';
					$series[$user_id][$form_id][$_field_key . '_To']['data'][] = array($data_time, $field_data[1]);
					$series_count++;
				}
				//_Dropdown, _RadioButtons have single_value or Num__String
				elseif ($data_type == '_Dropdown' OR $data_type == '_RadioButtons') {
					//we give String as extra field

					//get Cell_id for this field
					$cell_id = Formula__Get_ALPHA_id('data', $series_count, false);
					$forms_n_fields[$form_id][1][$_field_key.'_S'][2] = $cell_id;
					//series
					$series[$user_id][$form_id][$_field_key . '_S']['cell_id'] = $cell_id;
					$series[$user_id][$form_id][$_field_key . '_S']['num'] = $series_count;
					$series[$user_id][$form_id][$_field_key . '_S']['name'] = $data_name . '_S';
					$series[$user_id][$form_id][$_field_key . '_S']['type'] = '_Text';

					//can be a single_value or Num__String
					$data_input = explode('__', $field_data.'');
					//Num__String
					if (isset($data_input[1])) {
						$data_input = $data_input[1] . ''; //get string only
					}
					//single_value
					else {
						$data_input = $data_input[0] . ''; //get single_value
					}
					$series[$user_id][$form_id][$_field_key . '_S']['data'][] = array($data_time, $data_input); 
					$series_count++;
				}
				//EXTRA Fields #####################################
				

				//Main Fields need to be after Extra Fields

				//Main Fields #####################################
				//get Cell_id for this field
				$cell_id = Formula__Get_ALPHA_id('data', $series_count, false);
				$forms_n_fields[$form_id][1][$_field_key][2] = $cell_id;
				
				//series fields
				$series[$user_id][$form_id][$_field_key]['cell_id'] = $cell_id;
				$series[$user_id][$form_id][$_field_key]['num'] = $series_count;
				$series[$user_id][$form_id][$_field_key]['name'] = $data_name;
				$series[$user_id][$form_id][$_field_key]['type'] = $data_type;

				//Extra work for data here
				//Period have array ["16:23","17:23","01:00"]->[from,to,period]
				if ($data_type == '_Period' AND is_array($field_data)) {
					if ($field_data[2] != '') {
						$time = explode(':', $field_data[2] ?? ''); //get Period only -> "01:00"
						$data_input = ((int) $time[0] * 60 + (int) $time[1]);
					}
					else $data_input = 0;
				}
				elseif ($data_type == '_Dropdown' OR $data_type == '_RadioButtons') { //2__optionString
					$data_input = explode('__', $field_data . '__');
					$data_input = $data_input[0]; //get number only
				}
				
				//convert to null -> empty numbers like "" 
				if (!in_array($data_type, array('_Text', '_Textarea', '_Date', '_Time', '_Period_From', '_Period_To'))) {
					if ($data_input == '' OR $data_input == '""' OR $data_input == "''") {
						$data_input = null;
					}
				}

				//series data
				$series[$user_id][$form_id][$_field_key]['data'][] = array($data_time, $data_input); 
				$series_count++;
				//Main Fields #####################################

			} //foreach fields


			//this need to be after foreach fields
			if ($form_id_last != $form_id OR //once for each data_name per form_id 
				$user_id_last != $user_id) //or per user_id
			{
				//copy form data to saved form
				if (isset($FORMS_DO['saves']) AND isset($FORMS_DO['saves'][$form_id_last])) {
					foreach ((array)$FORMS_DO['saves'][$form_id_last] as $save_form_id) {
						//copy form data to saved form
						$series[$user_id_last][$form_id_last . '_S' . $save_form_id] = $series[$user_id_last][$form_id_last];
						
						//forms_n_fields names
						$forms_n_fields[$form_id_last . '_S' . $save_form_id] = $forms_n_fields[$form_id_last];
						$forms_n_fields[$form_id_last . '_S' . $save_form_id][0] .= ' (' . $SAVES_forms_names[$save_form_id] . ')'; //put save name

						//check if base_form_id is selected else remove it from series
						// TODO: check if this is doing the right thing or if it's the right place to do it
						if ($FORMS_DO['saves'][$form_id_last]) {
							unset($series[$user_id_last][$form_id_last]);
							unset($forms_n_fields[$form_id_last]);
						}
					}
				}
			}
		}

		$user_id_last = $user_id;
		$form_id_last = $form_id;
	}
	

	//get the last one #######################
	$cell_id = Formula__Get_ALPHA_id('data', $series_count-1, false);
	$forms_n_fields[$form_id_last][1][$_field_key][2] = $cell_id; //add Cell_id to this field
	
	//copy form data to saved forms
	if (isset($FORMS_DO['saves']) AND isset($FORMS_DO['saves'][$form_id_last])) {
		foreach ((array)$FORMS_DO['saves'][$form_id_last] as $save_form_id) {
			//copy form data to saved form
			$series[$user_id_last][$form_id_last . '_S' . $save_form_id] = $series[$user_id_last][$form_id_last];
			
			//forms_n_fields names
			$forms_n_fields[$form_id_last . '_S' . $save_form_id] = $forms_n_fields[$form_id_last];
			$forms_n_fields[$form_id_last . '_S' . $save_form_id][0] .= ' (' . $SAVES_forms_names[$save_form_id] . ')'; //put save name

			//check if base_form_id is selected else remove it from series
			if (!isset($FORMS_DO[$form_id_last])) {
				unset($series[$user_id_last][$form_id_last]);
				unset($forms_n_fields[$form_id_last]);
			}
		}
	}


	//prepare JSON data
	//echo "<pre>"; print_r($series); echo "</pre>";
	$data = json_encode($series, JSON_NUMERIC_CHECK);

	//give the { } at the end in case it is empty
	$js_data = substr($data.'', 1, -1);

}
else { //if No Data
	echo get_No_Data_Error();
}


//set js global values
?>
<script>
V_FORMS_DO = <?=json_encode($FORMS_DO);?>;
V_FORMS_N_FIELDS = <?=json_encode($forms_n_fields);?>;
V_FORMS_DATA = {<?=$js_data;?>};
</script>
