<?php // export Functions

// export ####################################

function get_Available_Forms(int $user_id):string {
	global $db, $ATHLETE;

	$where_forms = '';
	if ($ATHLETE) { // only for Athletes
		// get Forms with forms_data
		$Forms_with_Forms_Data_arr = (array)get_Forms_with_Forms_Data_count_array($user_id);
		if (count($Forms_with_Forms_Data_arr)) {
			$where_forms = implode(',', array_keys($Forms_with_Forms_Data_arr));
		} else {
			$where_forms = '0';
		}
		$where_forms = "AND id IN (".$where_forms.")";
	}

	$Forms_select_options = '';
	$forms_arr = array();
	//TODO: maybe we need all forms for export even disabled if they have data
	$forms = $db->fetch("SELECT id, name FROM forms WHERE status = 1 $where_forms ORDER BY name", array());
	if ($db->numberRows() > 0)  {
		foreach ($forms as $form) {
			if (!isset($forms_arr[$form['id']])) { //include each form once
				$Forms_select_options .= '' .
					'<option value="' . $form['id'] . '">' .
						$form['id'] . '. ' . html_chars($form['name']) .
						($ATHLETE ? ' (' . $Forms_with_Forms_Data_arr[$form['id']] . ')' : '') .
					'</option>';
				$forms_arr[$form['id']] = 1;
			}
		}
	}
	return $Forms_select_options;
}

function get_Trainer_Groups_array(int $user_id):mixed {
	global $db;

	$Trainer_Groups_arr = array();
	$rows = $db->fetch("SELECT u2g.group_id 
FROM users2groups u2g 
LEFT JOIN users u ON (u.id = u2g.user_id AND u.level = 30 AND u.status = 1) 
WHERE u2g.status = 1 AND u.id = ?", array($user_id)); 
	if ($db->numberRows() > 0)  {
		foreach ($rows as $row) {
			//$Trainer_Groups_arr[] = $row['group_id'];
			//use this --avoid duplicates + isset is faster than in_array
			$Trainer_Groups_arr[$row['group_id']] = 1; //give group_id as key
		}
	}
	return $Trainer_Groups_arr;
}

function get_Athlete_Groups_array(int $user_id):mixed {
	global $db;
	//get Athlete Groups
	$Athlete_Groups_arr = array();
	$rows = $db->fetch("SELECT group_id FROM users2groups WHERE status = 1 AND user_id = ?", array($user_id)); 
	if ($db->numberRows() > 0)  {
		foreach ($rows as $row) {
			$Athlete_Groups_arr[$row['group_id']] = 1; //give group_id as key
		}
	}
	return $Athlete_Groups_arr;
}

function get_Groups_select_options_n_ids(int $user_id):mixed {
	global $db, $TRAINER, $ATHLETE, $ADMIN, $GROUP_ADMIN, $GROUP_ADMIN_2, $LANG;
	
	if ($TRAINER) {
		//get Trainer Groups
		$Trainer_groups_arr = get_Trainer_Groups_array($user_id);
	}
	if ($ATHLETE) {
		//get Athlete Groups
		$Athlete_groups_arr = get_Athlete_Groups_array($user_id);
	}
	
	$Groups_select_options = ''; //Groups (in Locations) Select Options
	$Groups_available_ids = '';
	$where_location = '';
	if (!$ADMIN) {
		//if not admin get groups only from the current user location_id
		//I comment this so users can select groups they has access from any location //MAD
		//$where_location = 'WHERE location_id = "'.$LOCATION.'"';
	}
	//TODO: maybe we need all groups and locations for export even disabled 
	$Groups_rows = $db->fetch("SELECT gr.id, gr.location_id, gr.name, gr.admins_id, gr.status, l.name AS location_name 
FROM `groups` gr 
LEFT JOIN locations l ON l.id = gr.location_id 
WHERE l.status != 0 AND gr.status > 0 $where_location 
ORDER BY gr.location_id, gr.name", array()); 
	if ($db->numberRows() > 0)  {
		$location_open = false;
		$location_name_last = '';
		foreach ($Groups_rows as $row) {
			$Group_Admins_ids_arr = explode(',', $row['admins_id']??'');
	
			//filter out what each level account cannot see
			if (($GROUP_ADMIN OR $GROUP_ADMIN_2) AND !in_array($user_id, $Group_Admins_ids_arr)) {
				continue; //not a group admin of this group
			}
			elseif ($TRAINER AND !isset($Trainer_groups_arr[$row['id']])) {
				continue; //not a trainer of this group
			}
			elseif ($ATHLETE AND !isset($Athlete_groups_arr[$row['id']])) {
				continue; //not an athlete of this group
			}
			
			$location_name = $row['location_name'];
			$group_id = $row['id'];
			$group_name = html_chars($row['name']);
			$group_status = ($row['status']==3 ? $LANG->ST_PRIVATE : $LANG->ST_PUBLIC).', '.
							(($row['status']==1 OR $row['status']==3) ? $LANG->ST_ACTIVE : $LANG->ST_INACTIVE);
			
			//optgroup
			if ($location_name != $location_name_last) {
				if ($location_open) {
					$Groups_select_options .= '</optgroup>';
				}
				$Groups_select_options .= '<optgroup label="'.$location_name.'">';
				$location_open = true;
			}
			
			//option
			$t_option = '<option value="'.$group_id.'">'.$group_name.' ('.$group_status.')'.'</option>';
			$Groups_select_options .= $t_option;
	
			if ($Groups_available_ids != '') {
				$Groups_available_ids .= ',';
			}
			$Groups_available_ids .= $group_id;
			
			$location_name_last = $location_name;
		}
		if ($location_open) {
			$Groups_select_options .= '</optgroup>';
		}
	}

	return array($Groups_select_options, $Groups_available_ids);
}


//export + export_data ####################################


function get_Select__Athletes__Options_n_ids__for_Admins(int $user_id, string $Groups_available_ids):mixed {
	global $db, $ADMIN, $LOCATION_ADMIN, $GROUP_ADMIN, $GROUP_ADMIN_2;

	$where_level = '';
	if ($GROUP_ADMIN or $GROUP_ADMIN_2) {
		$where_level = "AND u.level < 40";
	}
	if ($LOCATION_ADMIN) {
		$where_level = "AND u.level < 50";
	}
	
	$where_groups = ''; //admin view all groups
	if (!$ADMIN) {
		$where_groups = "AND u2g.group_id IN (".$Groups_available_ids.")";
	}

	$Select__Athletes__Options = '';
	$Athletes_available_ids = $user_id;

	$Athletes_available_rows = $db->fetch("SELECT u.id, u.uname, u.firstname, u.lastname, u.level, u.sport 
FROM users u 
LEFT JOIN users2groups u2g ON u.id = u2g.user_id 
WHERE u2g.status = 1 $where_groups AND u.status = 1 $where_level AND u.id != ? 
GROUP BY u.id, u.uname, u.firstname, u.lastname, u.level, u.sport 
ORDER BY u.firstname, u.lastname, u.id", array($user_id));
	if ($db->numberRows() > 0)  {
		foreach ($Athletes_available_rows as $row) {
			$u_name = ($row['lastname'] != '' ? $row['lastname'] : $row['uname']);
			$u_vorname = ($row['firstname'] != '' ? $row['firstname'] : $row['uname']);
			$Select__Athletes__Options .= '' .
				'<option value="' . $row['id'] . '|' . html_chars($u_vorname . ' ' . $u_name) . '">' .
					html_chars($u_vorname . ' ' . $u_name . ' - ' . $row['sport']) .
				'</option>';

			$Athletes_available_ids .= ','.$row['id'];
		}
	}
	return array($Select__Athletes__Options, $Athletes_available_ids);
}


function get_Select__Athletes__Options_n_ids__for_Trainer(int $user_id, string $Groups_available_ids):mixed {
	global $db;

	$where_groups = "AND u2g.group_id IN (" . $Groups_available_ids . ")";
	$Select__Athletes__Options = '';
	$Athletes_available_ids = $user_id;

	$Athletes_available_rows = $db->fetch("SELECT u.id, u.uname, u.lastname, u.firstname, u.sport 
FROM users2groups u2g 
JOIN users u ON (u.id = u2g.user_id AND u.level = 10 AND u.status = 1) 
JOIN users2trainers u2t ON (u.id = u2t.user_id AND u2g.group_id = u2t.group_id AND u2t.status = 1 AND u2t.trainer_id = ?) 
WHERE u2g.status = 1 $where_groups 
GROUP BY u.id, u.uname, u.lastname, u.firstname, u.sport 
ORDER BY u.firstname, u.lastname, u.id", array($user_id)); 
	if ($db->numberRows() > 0) {
		foreach ($Athletes_available_rows as $row) {
			$u_name = ($row['lastname'] != '' ? $row['lastname'] : $row['uname']);
			$u_vorname = ($row['firstname'] != '' ? $row['firstname'] : $row['uname']);
			$Select__Athletes__Options .= '' .
				'<option value="' . $row['id'] . '|' . html_chars($u_vorname . ' ' . $u_name) . '">' .
					html_chars($u_vorname . ' ' . $u_name . ' - ' . $row['sport']) .
				'</option>';
			
			$Athletes_available_ids .= ','.$row['id'];
		}
	}
	return array($Select__Athletes__Options, $Athletes_available_ids);
}


//export_data ####################################

function get_Trainer_Forms_Read_Permissions_array(int $trainer_id, string $Athletes_available_ids):mixed {
	global $db;

	$Trainer_Forms_Read_Permissions_arr = array();
	$where_Athletes = '';
	if ($Athletes_available_ids != '') {
		$where_Athletes = " AND user_id IN (".$Athletes_available_ids.")";
	}
	$row = $db->fetchRow("SELECT user_id, forms_select_read FROM users2trainers WHERE trainer_id = ? $where_Athletes", array($trainer_id));
	if ($db->numberRows() > 0)  {
		if ($row['forms_select_read'] != '') {
			$Trainer_Forms_Read_Permissions_arr[$row['user_id']] = explode(',', $row['forms_select_read']??'');
		}
	}
	return $Trainer_Forms_Read_Permissions_arr;
}

function get_Users_array_n_ids(string $where_Athletes):mixed {
	global $db;
	$users_arr = array();
	$users_ids = '';
	$rows = $db->fetch("SELECT id, uname, lastname, firstname, sex, birth_date, sport FROM users $where_Athletes", array()); 
	if ($db->numberRows() > 0)  {
		foreach ($rows as $row) {
			$a_name = $row['lastname'] != '' ? $row['lastname'] : $row['uname'];
			$a_vorname = $row['firstname'] != '' ? $row['firstname'] : $row['uname'];
			$users_arr[$row['id']] = array(
				$row['id'],
				$a_name,
				$a_vorname,
				$row['sex'],
				get_date($row['birth_date'].''), 
				$row['sport']
			);
			//extra fields
			//$row['account'], $row['uname'], $row['status'], $row['body_height'], $row['email'],
			if ($users_ids != '') {
				$users_ids .= ',';
			}
			$users_ids .= $row['id'];
		}
	}
	return array($users_arr, $users_ids);
}

function get_Group_Users(int $group_id):mixed {
	global $db;
	$users = array();
	$users_ids = array();
	$rows = $db->fetch("SELECT u.id, u.uname, u.lastname, u.firstname, u.sex, u.birth_date, u.sport 
FROM users u 
LEFT JOIN users2groups u2g ON u.id = u2g.user_id 
WHERE u2g.status = 1 AND u.status = 1 AND u2g.group_id=? 
ORDER BY u.firstname, u.lastname, u.id", array($group_id)); 
	if ($db->numberRows() > 0)  {
		foreach ($rows as $row) {
			$users_ids[] = $row['id'];
			$a_name = $row['lastname'] != '' ? $row['lastname'] : $row['uname'];
			$a_vorname = $row['firstname'] != '' ? $row['firstname'] : $row['uname'];
			$users[$row['id']] = array(
				$row['id'],
				$a_name,
				$a_vorname,
				$row['sex'],
				get_date($row['birth_date'].''), 
				$row['sport']
			);
			//extra fields
			//$row['account'], $row['uname'], $row['status'], $row['group_id'], $row['body_height'], $row['email'],
		}
	}
	return array($users_ids, $users);
}

function get_All_Forms():mixed {
	global $db;
	$rows = $db->fetch("SELECT f2c.form_id, f2c.category_id, f.name, f.data_names FROM forms2categories f2c
LEFT JOIN forms f ON form_id = f.id
WHERE f.status = 1 ORDER BY f2c.category_id, f.name", array()); 
	$forms = array();
	if ($db->numberRows() > 0)  {
		foreach ($rows as $row) {
			$forms[$row['category_id']][] = $row; //forms array with cat_id as key
		}
	}
	//print_r($forms);
	return $forms;
}

function get_All_Groups_array():mixed {
	global $db, $LANG;

	$groups = array();
	$groups_db = $db->fetch("SELECT id, name, status FROM `groups` WHERE status > 0", array()); 
	if ($db->numberRows() > 0)  {
		foreach ($groups_db as $row) {
			$groups[$row['id']] = $row['name'] . ($row['status']==3 ? ' ('.$LANG->ST_PRIVATE.')' : '');
		}
	}
	return $groups;
}


?>