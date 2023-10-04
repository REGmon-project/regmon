<?php 
$PATH_2_ROOT = '../';
require_once($PATH_2_ROOT.'_settings.regmon.php');
require($PATH_2_ROOT.'login/validate.php');

require_once($PATH_2_ROOT.'export/inc.export_functions.php');
require_once($PATH_2_ROOT.'php/inc.categories_functions.php');

//give time for export
ini_set("memory_limit", "-1");
ini_set("max_execution_time", "0"); 
set_time_limit(0); 

$POST_gender = $_POST['gender'] ?? array();
$POST_year = $_POST['year'] ?? array();
$POST_sport = $_POST['sport'] ?? array();
$POST_groups = $_POST['group'] ?? array();
$POST_athletes = $_POST['athletes'] ?? array();
$POST_date_from = $_POST['date_from'] ?? '';
$POST_date_to = $_POST['date_to'] ?? '';
$POST_forms = $_POST['forms'] ?? array();
$POST_fields = $_POST['fields'] ?? array();


$Athletes_selected = $LANG->EXPORT_ALL;
$Gender_selected = $LANG->EXPORT_ALL;
$Year_of_birth_selected = $LANG->EXPORT_ALL;
$Sports_selected = $LANG->EXPORT_ALL;
$Groups_selected = $LANG->EXPORT_ALL;
$Date_From_selected = $LANG->EXPORT_ALL;
$Date_To_selected = $LANG->EXPORT_ALL;
$Forms_selected = $LANG->EXPORT_ALL;
$Fields_selected = $LANG->EXPORT_ALL;

$where_Athletes = "WHERE 1 ";
$where_Forms_Data__Groups = "";
$where_Forms_Data__Dates = "";
$where_Forms_Data__Forms = "";


$Groups_All_arr = (array)get_All_Groups_array();
$Groups_available_ids = '';


// Groups ########################################
if (count($POST_groups)) {
	$Groups_selected = '';
	foreach ($POST_groups as $group_id) {
		if ($Groups_selected != '') {
			$Groups_selected .= ', ';
		}
		$group_id = (int)$group_id;
		$Groups_selected .= "'".$Groups_All_arr[$group_id.'']."'";
		
		if ($Groups_available_ids != '') {
			$Groups_available_ids .= ', ';
		}
		$Groups_available_ids .= $group_id;
	}

	if ($Groups_available_ids != '') {
		$where_Forms_Data__Groups .= " AND group_id IN (". $Groups_available_ids .")";
	}
}
else { 
	//if POST_groups == all --check what each one can see
	
	$Groups_select_options_n_ids = get_Groups_select_options_n_ids($UID);
	$Groups_select_options = $Groups_select_options_n_ids[0].'';
	$Groups_available_ids = $Groups_select_options_n_ids[1].'';
}


// Athletes ########################################
$Athletes_available_ids = '';
if (count($POST_athletes)) {
	$athletes_ids_arr = array();
	$athletes_names_arr = array();
	foreach($POST_athletes as $athlete) {
		$athletes_ids_names_arr = explode('|', $athlete);
		$athletes_ids_arr[] = $athletes_ids_names_arr[0];
		$athletes_names_arr[] = $athletes_ids_names_arr[1];
	}
	foreach($athletes_ids_arr as $athlete_id) {
		if ($Athletes_available_ids != '') {
			$Athletes_available_ids .= ', ';
		}
		$Athletes_available_ids .= (int)$athlete_id;
	}
	$Athletes_selected = "'".implode("', '",$athletes_names_arr)."'";
	$where_Athletes .= " AND id IN (".$Athletes_available_ids.")";
}
else {
	//if POST_athletes == all --check what each one can see

	$Athletes_available_ids = '';
	if ($ADMIN OR $LOCATION_ADMIN OR $GROUP_ADMIN OR $GROUP_ADMIN_2) {
		$Select__Athletes__Options_n_ids = get_Select__Athletes__Options_n_ids__for_Admins($UID, $Groups_available_ids);
		//$Select__Athletes__Options = $Select__Athletes__Options_n_ids[0]; //not needed here
		$Athletes_available_ids = $Select__Athletes__Options_n_ids[1].'';
	}
	elseif ($TRAINER) {
		$Select__Athletes__Options_n_ids = get_Select__Athletes__Options_n_ids__for_Trainer($UID, $Groups_available_ids);
		//$Select__Athletes__Options = $Select__Athletes__Options_n_ids[0]; //not needed here
		$Athletes_available_ids = $Select__Athletes__Options_n_ids[1].'';
	}

	$where_Athletes .= " AND id IN (". $Athletes_available_ids .")";
}


// get trainer read form permissions
// for trainers we filter once again with forms permissions
if ($TRAINER) {
	$Trainer_Forms_Read_Permissions_arr = get_Trainer_Forms_Read_Permissions_array($UID, $Athletes_available_ids);
}


//###################################
//Info Panel - Selection Presentation
//gender
if (count($POST_gender)) {
	$Gender_selected = "'".str_replace(array('0','1','2'), array($LANG->REGISTER_MALE, $LANG->REGISTER_FEMALE, $LANG->REGISTER_OTHER), implode("', '",$POST_gender))."'";
	$where_Athletes .= " AND sex IN (". "'".implode("','",$POST_gender)."'" .")";
}
//year
if (count($POST_year)) {
	$where_year = "";
	foreach ($POST_year as $year) {
		if ($where_year != '') {
			$where_year .= " OR ";
		}
		$where_year .= " birth_date LIKE '$year%' ";
	}
	$where_Athletes .= " AND ( ".$where_year." ) ";
	$Year_of_birth_selected = "'".implode("', '",$POST_year)."'";
}
//sport
if (count($POST_sport)) {
	$where_sports = "";
	foreach($POST_sport as $sport) {
		if ($where_sports != '') {
			$where_sports .= " OR ";
		}
		$where_sports .= "sport LIKE '%".$sport."%'";
	}
	$where_Athletes = " AND ( ".$where_sports." ) ";
	$Sports_selected = "'".implode("', '",$POST_sport)."'";
}
//date from
if ($POST_date_from != '') {
	$date_from_sql = get_date_SQL($POST_date_from);
	$where_Forms_Data__Dates .= " AND timestamp_start >= '$date_from_sql 00:00:00'";
	$Date_From_selected = $POST_date_from;
}
//date to
if ($POST_date_to != '') {
	$date_to_sql = get_date_SQL($POST_date_to);
	$where_Forms_Data__Dates .= " AND timestamp_start <= '$date_to_sql 23:59:59'";
	$Date_To_selected = $POST_date_to;
}


//Forms
$has_Notes = false;
if (count($POST_forms)) {
	if (in_array('Note', $POST_forms)) {
		unset($POST_forms[0]);
		$has_Notes = true;
	}

	$Forms_available_ids = '';
	if (count($POST_forms)) {
		foreach($POST_forms as $form_id) {
			if ($Forms_available_ids != '') {
				$Forms_available_ids .= ', ';
			}
			$Forms_available_ids .= (int)$form_id;
		}
	}

	if ($Forms_available_ids == '') {
		$Forms_available_ids = '0'; //we give 0 so it finds nothing
	}

	$where_Forms_Data__Forms = " AND form_id IN (".$Forms_available_ids.")";
	$where_forms = " AND id IN (".$Forms_available_ids.")";

	$forms_arr = array();

	//TODO: maybe we need all forms for export even disabled if they have data
	$forms_rows = $db->fetch("SELECT id, name FROM forms WHERE status = 1 $where_forms", array());
	if ($db->numberRows() > 0)  {
		foreach ($forms_rows as $row) {
			$forms_arr[$row['id']] = $row['name'];
		}
	}
	//get selected forms text
	$Forms_selected = '';
	if (count($POST_forms)) {
		foreach ($POST_forms as $form_id) {
			if ($Forms_selected != '') {
				$Forms_selected .= ', ';
			}
			$Forms_selected .= "'".($forms_arr[$form_id] ?? '')."'";
		}
	}
	if ($has_Notes) {
		$Forms_selected = "'".$LANG->NOTE."'" . ($Forms_selected != '' ? ', ' : '') . $Forms_selected;
	}
}


//Fields 
if (count($POST_fields)) {
	$Fields_selected = "'".str_replace(
		array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9'), 
		array(
			$LANG->EXPORT_HEADER_USER_ID, 
			$LANG->EXPORT_HEADER_FIRSTNAME, 
			$LANG->EXPORT_HEADER_LASTNAME, 
			$LANG->EXPORT_HEADER_GENDER, 
			$LANG->EXPORT_HEADER_BIRTH_DATE, 
			$LANG->EXPORT_HEADER_SPORT, 
			$LANG->EXPORT_HEADER_GROUP_ID, 
			$LANG->EXPORT_HEADER_SRV_DATE, 
			$LANG->EXPORT_HEADER_SRV_TIME, 
			$LANG->EXPORT_HEADER_SRV_DATE_NO
		), 
		implode("', '",$POST_fields)
	)."'";
}


//get USERS
$Users_arr_n_ids = get_Users_array_n_ids($where_Athletes);
$users_arr = $Users_arr_n_ids[0];
$users_ids = $Users_arr_n_ids[1];

$Data_Table_HTML = '';
$no_data = false;

if ($users_ids != '') //##################################################
{ //######################################################################


//>>
// Categories ###################################
// make the view order
$category_forms_ordered = array();
$Forms_Data_ordered_arr = array();
//$category_forms_ordered[$row['form_id']] = array($row['form_id'], $row['name'], $row['data_names'], $order); //informatics
$order = 0;


//notes
if ($has_Notes) {
	$category_forms_ordered['0'] = array('0', $LANG->NOTE, '{"1":["'.$LANG->NOTE.'","_Text"],"2":["'.$LANG->NOTE_PERIOD.'","_Number"]}', $order);
	$Forms_Data_ordered_arr = array(array(0,$LANG->NOTE,'{"1":["'.$LANG->NOTE.'","_Text"],"2":["'.$LANG->NOTE_PERIOD.'","_Number"]}','{"1":"'.$LANG->NOTE.'","2":"'.$LANG->NOTE_PERIOD.'"}'));
	$order++;
}


//build Forms Categories #################################
$forms = get_All_Forms();
$categories = get_All_Categories();
//it uses a lot of Global variables - may need improvement here
//$forms, $categories, $order, $category_forms_ordered; //Global variables in use
//########################################################


$where_Forms_Data__Users = " AND user_id IN (".$users_ids.") ";
//where_Forms_Data
$where_Forms_Data = "WHERE 1 ". $where_Forms_Data__Groups . $where_Forms_Data__Dates . $where_Forms_Data__Forms . $where_Forms_Data__Users;


//FORMS_DATA --headers and empty cells --GROUP BY form_id
$forms_data_rows_tmp = $db->fetch("SELECT category_id, form_id, user_id, res_json 
FROM forms_data 
$where_Forms_Data 
AND status = 1 AND form_id > 0 AND res_json != '[]' 
GROUP BY category_id, form_id, user_id, res_json 
ORDER BY form_id", array()); 
foreach ($forms_data_rows_tmp as $data_row) {
	if ($TRAINER) {
		if (!in_array($data_row['category_id'].'_'.$data_row['form_id'], (array)$Trainer_Forms_Read_Permissions_arr[$data_row['user_id']])) {
			//if trainer not have read form permissions
			continue;
		}
	}
	if (isset($category_forms_ordered[$data_row['form_id']])) {
		// a form it may be inactive but forms_data may be available
		$Forms_Data_ordered_arr[$category_forms_ordered[$data_row['form_id']][3]] = array( //order as key --for sort
			$category_forms_ordered[$data_row['form_id']][0], //id
			$category_forms_ordered[$data_row['form_id']][1], //name
			$category_forms_ordered[$data_row['form_id']][2], //data_names form
			$data_row['res_json'] //res_json forms_data
		);
	}
}
//echo "<pre>";print_r($forms_data_rows_tmp);echo "</pre>";
//echo "<pre>";print_r($category_forms_ordered);echo "</pre>";
//echo "<pre>";print_r($Forms_Data_ordered_arr);echo "</pre>";
//exit;
ksort($Forms_Data_ordered_arr);


//init data variables
$data_header_form = '';
$data_header = '';
$data_rows = '';
$forms_data_empty = array();

// get header data -field names
foreach ($Forms_Data_ordered_arr as $Forms_Data_row) {
	$form_id = $Forms_Data_row[0];
	$form_name = $Forms_Data_row[1];
	$form_res_names = (array)json_decode($Forms_Data_row[2],true); //[0=name, 1=type]
	$form_res_names_Num = count($form_res_names);

	$header_forms_data_names[$form_id] = '';
	$forms_data_empty[$form_id] = '';
	$header_form_extra_fields = 0;

	for($i=1; $i<=$form_res_names_Num; $i++) {
		if ($form_res_names[$i][1] == '_Period') { 
			//extra fields for Period (from, to) //Period is array ["20:04","21:04","01:00"]
			$header_forms_data_names[$form_id] .= '<th>'.$form_res_names[$i][0].'_From</th>';
			$forms_data_empty[$form_id] .= '<td></td>';
			$header_form_extra_fields++;

			$header_forms_data_names[$form_id] .= '<th>'.$form_res_names[$i][0].'_To</th>';
			$forms_data_empty[$form_id] .= '<td></td>';
			$header_form_extra_fields++;
		}
		elseif ($form_res_names[$i][1] == '_Dropdown' OR $form_res_names[$i][1] == '_RadioButtons') {
			//extra field for _Dropdown, _RadioButtons (_S) //string representation of value
			$header_forms_data_names[$form_id] .= '<th>'.$form_res_names[$i][0].'_S</th>'; //string val here
			$forms_data_empty[$form_id] .= '<td></td>';
			$header_form_extra_fields++;
		}
		$header_forms_data_names[$form_id] .= '<th>'.$form_res_names[$i][0].'</th>';
		$forms_data_empty[$form_id] .= '<td></td>';
	}

	$header_form[$form_id] = '<th colspan="'.($form_res_names_Num + $header_form_extra_fields).'" class="noCSV">'.$form_name.'</th>';
	$data_header_form .= $header_form[$form_id];
	$data_header .= $header_forms_data_names[$form_id];
	//+1
	$data_header_form .= '<th class="noCSV forms_separator"></th>';
	$data_header .= '<th class="forms_separator"></th>';
}



// get notes
$where_Notes_Data = "WHERE 1 ". $where_Forms_Data__Groups . $where_Forms_Data__Dates . $where_Forms_Data__Users;
$Notes_data_rows = array();
$Notes_data = $db->fetch("SELECT user_id, group_id, `name`, isAllDay, timestamp_start, timestamp_end, modified 
FROM notes 
$where_Notes_Data AND showInGraph = 1 
ORDER BY user_id, group_id, timestamp_start", array());
if ($db->numberRows() > 0)  {
	foreach ($Notes_data as $Note) {
		if ($TRAINER) {
			if (!in_array('Note_n', (array)$Trainer_Forms_Read_Permissions_arr[$Note['user_id']])) {
				//if trainer not have read form permissions
				continue;
			}
		}
		
		//FIXES ########################################
		//calendar want full day if allDay:true --if same date and diff times it will not shown in calendar
		$start = get_date_time_SQL($Note['timestamp_start']);
		$end = get_date_time_SQL($Note['timestamp_end']);
		if ($Note['isAllDay']=='1') {
			if ($Note['timestamp_end'] == '') { //if we not have timestamp_end --old notes
				$start_tmp = explode(' ', $Note['timestamp_start']??'');
				$Note['timestamp_end'] = $start_tmp[0].' 23:59:59';
			} 
			$end = date("Y-m-d H:i:s", strtotime($Note['timestamp_end']) + 1); //end + 1sec bcz is 23:59:59
		}
		else {
			if ($Note['timestamp_end'] == '') { //if we not have timestamp_end --old notes
				$end = date("Y-m-d H:i:s", (strtotime($Note['timestamp_start']) + (60*60))); //new +60mins
			}
		}
		$diff = round((strtotime($end) - strtotime($start)) / 60);
		//FIXES ########################################
		
		$res_json = '{"1":"'.$Note['name'].'","2":"'.$diff.'"}';
		
		$Notes_data_rows[] = array(
			'user_id' => $Note['user_id'],
			'form_id' => 'n',
			'category_id' => 'Note',
			'group_id' => $Note['group_id'],
			'res_json' => $res_json,
			'timestamp_start' => $Note['timestamp_start'],
			'modified' => $Note['modified']
		);
	}
}


//get FORMS_DATA --the data rows
$forms_data_rows = $db->fetch("SELECT * 
FROM forms_data 
$where_Forms_Data AND status = 1 AND form_id > 0 AND res_json != '[]' 
ORDER BY user_id, group_id, timestamp_start", array());
if ($db->numberRows() > 0 OR $has_Notes)  {
	$date_num = 0;
	$date_last = 0;
	$uid_last = 0;
	$gid_last = 0;

	if (count($forms_data_rows) AND $has_Notes) {
		//merge arrays
		$all_data_rows = array_merge($Notes_data_rows, $forms_data_rows);
	}
	elseif ($has_Notes) {
		$all_data_rows = $Notes_data_rows;
	}
	else {
		$all_data_rows = $forms_data_rows;
	}

	//sort by user, group_id, timestamp_start
	usort($all_data_rows, function($a, $b) {
		if ($a['user_id'] == $b['user_id']){
			if ($a['group_id'] == $b['group_id']){
				return strtotime($a['timestamp_start']) - strtotime($b['timestamp_start']);     
			} else {
				return $a['group_id'] - $b['group_id'];
			}
		} else {
			return $a['user_id'] - $b['user_id'];
		}
	});
	//echo "<pre>";print_r($forms_data_rows);echo "</pre>";
	//echo "<pre>";print_r($Notes_data_rows);echo "</pre>";
	//echo "<pre>";print_r($all_data_rows);echo "</pre>";

	foreach ($all_data_rows as $data_row) {
		if ($TRAINER) {
			if (!in_array($data_row['category_id'].'_'.$data_row['form_id'], (array)$Trainer_Forms_Read_Permissions_arr[$data_row['user_id']])) {
				//if trainer not have read form permissions
				continue;
			}
		}
		if ($data_row['form_id'] == 'n') {
			$data_row['form_id'] = '0';
		}
		
		//date-time
		$date_time = explode(' ', get_date_time($data_row['timestamp_start'].''));
		$date_time_2 = explode(' ', get_date_time($data_row['modified'].''));
		if ($date_last != $date_time[0]) {
			$date_num = 1;
		}
		$date_last = $date_time[0];

		//form_data
		$res_json = (array)json_decode($data_row['res_json'],true);

		$form_data_columns = '';
		
		//results
		foreach ($Forms_Data_ordered_arr as $Forms_Data_row) { //loop all Forms_Data
			$form_id = $Forms_Data_row[0];
			if ($data_row['form_id'] == $form_id AND count($res_json)) {
				$form_fields = (array)json_decode($Forms_Data_row[2],true);
				//echo "<pre>"; print_r($res_json); print_r($form_fields); echo "</pre>"; 

				foreach ($form_fields as $key => $name_type) {
					$val = isset($res_json[$key]) ? $res_json[$key] : '';

					//extra fields for Period (from, to) //Period is array ["20:04","21:04","01:00"]
					if ($name_type[1] == '_Period' AND is_array($val)) { 
						$form_data_columns .= '<td>'.$val[0].'</td>'; //export period_from
						$form_data_columns .= '<td>'.$val[1].'</td>'; //export period_to
						if ($val[2] != '') {
							$time = explode(':', $val[2]??''); //get Period only -> "01:00"
							$val = (((int)$time[0]) * 60 + ((int)$time[1])); //convert to minutes
						} else {
							$val = 0; //convert to minutes
						}
					}
					elseif ($name_type[1] == '_RadioButtons' OR $name_type[1] == '_Dropdown') { //radio, dropdown = num__text
						$dd = explode('__', $val.'__');
						$form_data_columns .= '<td>'.$dd[1].'</td>'; //export string
						$val = $dd[0];
					}

					$val = $val . '';
					if (substr_count($val, '.') == 1) { 
						//in excel dots may translated to dates --so we give commas
						$form_data_columns .= '<td>'.str_replace('.', ',', $val).'</td>'; 
					}
					else { //maybe it is a date
						$form_data_columns .= '<td>'.$val.'</td>';
					}
				}
			}
			else {
				$form_data_columns .= $forms_data_empty[$form_id] ?? '';
			}
			//+1
			$form_data_columns .= '<td class="forms_separator"></td>';
		}
		
		$uid = $data_row['user_id'];
		$group_id = $data_row['group_id'];
		$group_name = $Groups_All_arr[$group_id] ?? '';
		$user_gender = ($users_arr[$uid][3] == '1' ? $LANG->REGISTER_FEMALE : ($users_arr[$uid][3] =='2' ? $LANG->REGISTER_OTHER : $LANG->REGISTER_MALE));
		$form_name = $category_forms_ordered[$data_row['form_id']][1] ?? '';

		$tr_row_style = '';
		if ($uid_last != $uid) {
			$tr_row_style .= ' class="users_separator"';
		}
		if ($gid_last != $group_id) {
			$tr_row_style .= ' class="groups_separator"';
		}

		//DATA
		$data_rows .= '<tr'.$tr_row_style.'>'.
			((!count($POST_fields) OR in_array('0',$POST_fields))?'<td nowrap>'.$users_arr[$uid][0].'</td>':'').
			((!count($POST_fields) OR in_array('2',$POST_fields))?'<td nowrap>'.$users_arr[$uid][2].'</td>':'').
			((!count($POST_fields) OR in_array('1',$POST_fields))?'<td nowrap>'.$users_arr[$uid][1].'</td>':'').
			((!count($POST_fields) OR in_array('3',$POST_fields))?'<td nowrap>'.$user_gender.'</td>':'').
			((!count($POST_fields) OR in_array('4',$POST_fields))?'<td nowrap>'.$users_arr[$uid][4].'</td>':'').
			((!count($POST_fields) OR in_array('5',$POST_fields))?'<td nowrap>'.$users_arr[$uid][5].'</td>':'').
			((!count($POST_fields) OR in_array('6',$POST_fields))?'<td nowrap>'.$group_name.'</td>':'').
			((!count($POST_fields) OR in_array('7',$POST_fields))?'<td nowrap>'.$date_time[0].'</td>':'').
			((!count($POST_fields) OR in_array('8',$POST_fields))?'<td nowrap>'.$date_time[1].'</td>':'').
			((!count($POST_fields) OR in_array('9',$POST_fields))?'<td nowrap>'.$date_time_2[0].'</td>':'').
			((!count($POST_fields) OR in_array('10',$POST_fields))?'<td nowrap>'.$date_time_2[1].'</td>':'').
			((!count($POST_fields) OR in_array('11',$POST_fields))?'<td nowrap>'.$date_num.'</td>':'').
			'<td nowrap>'.$form_name.'</td>'.
			'<td class="forms_separator"></td>'.
			$form_data_columns.
		'</tr>';
		$date_num++;
		$uid_last = $uid;
		$gid_last = $group_id;

		//extra fields
		//$data_row['id'], $data_row['user_id'], $data_row['type'], $data_row['name'], get_date_time($data_row['modified'].'')

	} //end foreach all_data_rows


	$Data_Table_HTML = ''.
		// it works better if the scroll is on the page than on a div
		// '<section style="width:100%; overflow-x:scroll;">'.
		'<section>'.
			'<table id="export_data" style="width:100%; font-size:13px;" border="1">'.
				'<thead>'.
					'<tr>'.
						((!count($POST_fields) OR in_array('0',$POST_fields))?'<th rowspan="2">'.$LANG->EXPORT_HEADER_USER_ID.'</th>':'').
						((!count($POST_fields) OR in_array('2',$POST_fields))?'<th rowspan="2">'.$LANG->EXPORT_HEADER_LASTNAME.'</th>':'').
						((!count($POST_fields) OR in_array('1',$POST_fields))?'<th rowspan="2">'.$LANG->EXPORT_HEADER_FIRSTNAME.'</th>':'').
						((!count($POST_fields) OR in_array('3',$POST_fields))?'<th rowspan="2">'.$LANG->EXPORT_HEADER_GENDER.'</th>':'').
						((!count($POST_fields) OR in_array('4',$POST_fields))?'<th rowspan="2">'.$LANG->EXPORT_HEADER_BIRTH_DATE.'</th>':'').
						((!count($POST_fields) OR in_array('5',$POST_fields))?'<th rowspan="2">'.$LANG->EXPORT_HEADER_SPORT.'</th>':'').
						((!count($POST_fields) OR in_array('6',$POST_fields))?'<th rowspan="2">'.$LANG->EXPORT_HEADER_GROUP_ID.'</th>':'').
						((!count($POST_fields) OR in_array('7',$POST_fields))?'<th rowspan="2">'.$LANG->EXPORT_HEADER_SRV_DATE.'</th>':'').
						((!count($POST_fields) OR in_array('8',$POST_fields))?'<th rowspan="2">'.$LANG->EXPORT_HEADER_SRV_TIME.'</th>':'').
						((!count($POST_fields) OR in_array('9',$POST_fields))?'<th rowspan="2">'.$LANG->EXPORT_HEADER_SRV_DATE_2.'</th>':'').
						((!count($POST_fields) OR in_array('10',$POST_fields))?'<th rowspan="2">'.$LANG->EXPORT_HEADER_SRV_TIME_2.'</th>':'').
						((!count($POST_fields) OR in_array('11',$POST_fields))?'<th rowspan="2">'.$LANG->EXPORT_HEADER_SRV_DATE_NO.'</th>':'').
						'<th rowspan="2">'.$LANG->EXPORT_FORM.'</th>'.
						'<th class="noCSV forms_separator"></th>'.
						$data_header_form.
					'</tr>'.
					'<tr>'.
						'<th class="forms_separator"></th>'.
						$data_header.
					'</tr>'.
				'</thead>'.
				'<tbody>'.
					$data_rows.
				'</tbody>'.
			'</table>'.
		'</section>';

} //end if FORMS_DATA
else {
	$no_data = true;
}
//>>


} //end if ($users_ids != '')
else {
	$no_data = true;
}
//######################################################################
if ($no_data) {
	$Data_Table_HTML = '
	<div class="container">
		<div class="step">
			<div class="row">
				<h3 style="text-align:center;">'.$LANG->EXPORT_NO_DATA_WITH_FILTER.'</h3>
			</div>
		</div>
	</div>';
}

//Filename
$Athletes_selected_filename = str_replace("'", '', $Athletes_selected);
/** 
 * @var string $filename 
 */
$filename = $LANG->EXPORT_FILENAME;
$filename = str_replace('{DATE}', date("Ymd"), $filename);
$filename = str_replace('{USERS_SELECTED}', $Athletes_selected_filename, $filename);

//#####################################################################################
$title = $LANG->EXPORT_TABLE_PAGE_TITLE;
require($PATH_2_ROOT.'php/inc.html_head.php');
//#####################################################################################
?>
<?php /*<!-- Jquery -->*/?>
<link type="text/css" rel="stylesheet" href="<?=$PATH_2_ROOT;?>node_modules/jquery-ui/dist/themes/smoothness/jquery-ui.min.css">
<link type="text/css" rel="stylesheet" href="<?=$PATH_2_ROOT;?>js/plugins/chosen/chosen.min.css" />
<script type="text/javascript" src="<?=$PATH_2_ROOT;?>js/plugins/chosen/chosen.jquery.min.js"></script>

<!-- OTHER JS --> 
<link type="text/css" rel="stylesheet" href="<?=$PATH_2_ROOT;?>node_modules/bootstrap/dist/css/bootstrap-theme.min.css">
<script type="text/javascript" src="<?=$PATH_2_ROOT;?>node_modules/bootstrap/dist/js/bootstrap.min.js"></script>

<script type="text/javascript" src="<?=$PATH_2_ROOT;?>node_modules/moment/min/moment.min.js"></script>
<?php if ($LANG->LANG_CURRENT != 'en') { ?>
<script type="text/javascript" src="<?=$PATH_2_ROOT;?>js/overrides/moment/<?=$LANG->LANG_CURRENT;?>.js"></script>
<?php } ?>
<link type="text/css" rel="stylesheet" href="<?=$PATH_2_ROOT;?>node_modules/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css" />
<script type="text/javascript" src="<?=$PATH_2_ROOT;?>node_modules/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>

<script type="text/javascript" src="<?=$PATH_2_ROOT;?>js/plugins/export/table2CSV.js<?=$G_VER;?>"></script>
<script type="text/javascript" src="<?=$PATH_2_ROOT;?>js/plugins/export/xlsx.core.min.js"></script>
<script type="text/javascript" src="<?=$PATH_2_ROOT;?>js/plugins/export/Blob.js"></script>
<script type="text/javascript" src="<?=$PATH_2_ROOT;?>js/plugins/export/FileSaver.js"></script>
<script type="text/javascript" src="<?=$PATH_2_ROOT;?>js/plugins/export/Export2Excel.js"></script>

<script type="text/javascript" src="<?=$PATH_2_ROOT;?>js/lang_<?=$LANG->LANG_CURRENT;?>.js<?=$G_VER;?>"></script>
<script type="text/javascript" src="<?=$PATH_2_ROOT;?>js/common.js<?=$G_VER;?>"></script>
<script type="text/javascript" src="<?=$PATH_2_ROOT;?>export/js/export.js<?=$G_VER;?>"></script>
<style>
#middle-wizard h3 { text-align: center; }
table {	border-collapse:collapse; border-spacing:0; border:2px solid #cccccc; }
th { background-color:#eeeeee; border:1px solid #cccccc; color:#555555; text-align:center; padding:1px 5px;}
td { border:1px solid #cccccc; vertical-align:top; padding:1px 5px; }
.forms_separator { border-left:2px dotted black; border-right:2px dotted black; padding:2px; }
.users_separator { border-top:2px solid; }
.groups_separator { border-top:2px dashed black; }
.download_ico { padding:10px; cursor:pointer; }
</style>
</head>
<body>
	<?php //require($PATH_2_ROOT.'php/inc.header.php');?>

    <div class="container">
		<div class="row">
			<div class="col-md-12 main-title" style="text-align:center;">
				<button type="button" name="backward" class="backward" style="float:left;" onclick="window.history.back();return false;"> &nbsp; <?=$LANG->EXPORT_BACK;?></button>
				<button type="button" id="home_parent" class="home_parent"> &nbsp; <?=$LANG->HOMEPAGE;?></button> &nbsp; 
				<h1><?=$LANG->EXPORT_TABLE;?></h1>
			</div>
		</div>
	</div>

	<section class="container">

		<div id="wizard_container">
			<div id="middle-wizard">
				<div class="step">

					<div class="row">
						<h3><u><?=$LANG->EXPORT_TABLE_CRITERIA;?></u></h3>
					</div>

		<?php if ($ATHLETE) { ?>
					<div class="row">
						<div class="col-md-12">
							<em><?=$LANG->EXPORT_ATHLETES;?></em> : <b><?=$Athletes_selected;?></b>
							<br>
						</div>
					</div>

					<div class="row">
						<div class="col-md-12">
							<em><?=$LANG->EXPORT_GROUP;?></em> : <b><?=$Groups_selected;?></b>
							<br>
						</div>
					</div>
		<?php } else { //not $ATHLETE ?>
					<div class="row">
						<div class="col-md-4">
							<em><?=$LANG->EXPORT_GENDER;?></em> : <b><?=$Gender_selected;?></b>
							<br>
							<em><?=$LANG->EXPORT_YEAR;?></em> : <b><?=$Year_of_birth_selected;?></b>
							<br>
						</div>

						<div class="col-md-8">
							<em><?=$LANG->EXPORT_SPORT;?></em> : <b><?=$Sports_selected;?></b>
							<br>
							<em><?=$LANG->EXPORT_GROUP;?></em> : <b><?=$Groups_selected;?></b>
							<br>
						</div>
					</div>

					<div class="row">
						<div class="col-md-12">
							<em><?=$LANG->EXPORT_ATHLETES;?></em> : <b><?=$Athletes_selected;?></b>
							<br>
						</div>
					</div>
		<?php  } ?>
					
					<div class="row">
						<div class="col-md-4">
							<em><?=$LANG->EXPORT_PERIOD;?> (<?=$LANG->EXPORT_DATE_FROM;?>)</em> : <b><?=$Date_From_selected;?></b>
						</div>
						<div class="col-md-8">
							<em><?=$LANG->EXPORT_PERIOD;?> (<?=$LANG->EXPORT_DATE_TO;?>)</em> : <b><?=$Date_To_selected;?></b>
						</div>
					</div>

					<div class="row">
						<div class="col-md-12">
							<em><?=$LANG->EXPORT_FORM;?></em> : <b><?=$Forms_selected;?></b>
						</div>
					</div>

					<div class="row">
						<div class="col-md-12">
							<em><?=$LANG->EXPORT_VIEW_FIELDS;?></em> : <b><?=$Fields_selected;?></b>
						</div>
					</div>

				</div>
			</div>
			
	<?php if (!$no_data) { ?>	
			<div id="bottom-wizard">
				<h4>
					<?=$LANG->EXPORT_FILE;?> :
					<br>
					<input type="text" id="filename" name="filename" value="<?=$filename;?>" style="width:100%;" /> 
					<br>.
					<span id="download_XLS" class="download_ico"><img src="<?=$PATH_2_ROOT;?>img/xls.png"></span>
					<span id="download_XLSX" class="download_ico"><img src="<?=$PATH_2_ROOT;?>img/xlsx.png"></span>
					<span id="download_CSV" class="download_ico"><img src="<?=$PATH_2_ROOT;?>img/csv.png"></span>
				</h4>
			</div>
	<?php } ?>
		</div>


	</section>

	<br>
	<br>

	<?=$Data_Table_HTML; //echo Data_Table_HTML?>

	<br>
	<br>
	<br>

	<?php //require($PATH_2_ROOT.'php/inc.footer.php');?>

	<div id="toTop" title="<?=$LANG->PAGE_TOP;?>">&nbsp;</div>

</body>
</html>