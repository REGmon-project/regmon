<?php // inc index initialization

if ($SEC_check != $CONFIG['SEC_Page_Secret']) exit;

//User2Group
$user_2_groups = get_User_2_Groups($UID);

//$User_2_Groups_json = json_encode($user_2_groups); //we get it after in order to get the group expire info

//Trainers All in groups --need this info for groups
$trainers2groups = get_Trainers_2_Groups();

//Locations array
$locations_arr = get_Locations_array();

//Groups Select Options
$selected_LOCATION_name = '';
$selected_GROUP_name = '';
$Groups_select_options_optgroup = '';
$Groups_select_options = '';
$private_option = '';
$groups2location = array(array('0','','','',''));
$private_groups = array();
$where_groups = '';

if (!$ADMIN) {
	$where_groups = 'AND location_id = "' . $LOCATION . '"';
}

$rows = $db->fetch("SELECT id, location_id, name, status, private_key, admins_id, stop_date 
FROM `groups` 
WHERE status > 0 $where_groups 
ORDER BY location_id, name", array()); 
if ($db->numberRows() > 0)  {
	$GP_open_group = false;
	$GP_group = '';
	$GP_group_tmp = '';
	foreach ($rows as $row) {

		$location_id = $row['location_id'];
		$location_name = '';
		$location_admin = '';
		if (isset($locations_arr[$location_id]) AND isset($locations_arr[$location_id][0])) {
			$location_name = $locations_arr[$location_id][0];
			$location_admin = $locations_arr[$location_id][1];
		}

		$GP_group = $location_name;
		$group_admins = $row['admins_id'];
		$group_name = $row['name'];
		$group_id = $row['id'];
		$group_expire = false;

		if ($row['stop_date'] AND strtotime($row['stop_date']) < strtotime("now")) {
			$group_expire = true;
			if (isset($user_2_groups[$group_id]['status']) AND $user_2_groups[$group_id]['status'] == 1) {
				$user_2_groups[$group_id]['status'] = 2;
			}
		}

		$group_private = false;
		if ($row['status'] == '3' AND !$group_expire) { //if private and not expired
			if (!isset($user_2_groups[$group_id]['status']) //if user not know it
				OR (isset($user_2_groups[$group_id]['status']) 
					AND ($user_2_groups[$group_id]['status'] == '0' 
						OR $user_2_groups[$group_id]['status'] == '5' 
						OR $user_2_groups[$group_id]['status'] == '15')
				   )
			   ) {
				$group_private = true;
				$private_groups[] = array($group_id, $group_name, $row['private_key']);
			}
		}

		$group_status = (isset($user_2_groups[$group_id]['status']) ? $user_2_groups[$group_id]['status'] : -1);
		$trainers = '';
		if (isset($trainers2groups[$group_id])) {
			$trainers = $trainers2groups[$group_id];
		}
		
		//Group_2_Location_array for js
		$groups2location[$group_id] = array($location_id, $location_admin, $group_admins, $location_name, $trainers);

		$t_selected = '';
		if ($group_id == $GROUP) {
			$t_selected = ' selected'; //mark selected
			$selected_LOCATION_name = $location_name;
			$selected_GROUP_name = $group_name;
		}
		
		//Group
		if ($GP_group <> $GP_group_tmp) {
			if ($GP_open_group) {
				$Groups_select_options_optgroup .= '</optgroup>';
			}
			$Groups_select_options_optgroup .= '<optgroup label="'.$GP_group.'">';
			$GP_open_group = true;
		}
		
		//group status - class
		$g_class = get_Group_Request_Status_Class($group_status.'');
		//group status - text
		$g_text = $LANG->{'GROUP_STATUS_REQUEST_'.$group_status};
		
		//option
		if ($group_expire AND $group_status != 2) {
			$t_option = '';
		}
		elseif ($group_private) {
			$t_option = '';
		}
		else {
			$t_option = '<option class="'.$g_class.' v_'.$group_id.'" value="'.$group_id.'"'.$t_selected.' data-status="'.$g_text.'">'.$group_name.($row['status']==3?' '.$LANG->INDEX_PRIVATE_GROUP_MARK:'').'</option>';
		}

		if ($GP_group == '') {
			$Groups_select_options .= $t_option;
		} else {
			$Groups_select_options_optgroup .= $t_option;
		}
		
		$GP_group_tmp = $GP_group;
	}
	if ($GP_open_group) {
		$Groups_select_options_optgroup .= '</optgroup>';
	}
	//if we have private groups give an option for that
	if (count($private_groups)) {
		$private_option = '<option value="Private"'.'>'.$LANG->INDEX_PRIVATE_GROUP.'...</option>';
	}
}
$Groups_select_options = $Groups_select_options_optgroup . $Groups_select_options . $private_option;
$Group_2_Location_json = json_encode($groups2location);
$User_2_Groups_json = json_encode($user_2_groups);

$GROUP = $GROUP ?? '';

//Location Admin
//$THIS_LOCATION_ADMIN = false; //go to validate.php
//$LOCATION_Admin = (isset($groups2location[$GROUP][1])?$groups2location[$GROUP][1]:false);
//if ($UID == $LOCATION_Admin) $THIS_LOCATION_ADMIN = true; //go to validate.php

//Group Admin
//$THIS_GROUP_ADMIN = false; //go to validate.php
//$GROUP_Admins = explode(',', (isset($groups2location[$GROUP][2])?$groups2location[$GROUP][2]:false));
//if (in_array($UID, $GROUP_Admins)) $THIS_GROUP_ADMIN = true; //go to validate.php

//Trainer
$THIS_GROUP_TRAINER = false;
$GROUP_Trainers = explode(',', (isset($groups2location[$GROUP][4]) ? $groups2location[$GROUP][4].'' : ''));
if (in_array($UID, $GROUP_Trainers)) {
	$THIS_GROUP_TRAINER = true;
}


//Athletes Name
$a_name = $USER['lastname'] != '' ? $USER['lastname'] : $USER['uname'];
$a_vorname = $USER['firstname'] != '' ? $USER['firstname'] : $USER['uname'];
$Athlete_Name = $a_vorname.' &nbsp; '.$a_name;

//Athletes Select
$Athletes_Select = '';
if ($ADMIN OR $THIS_LOCATION_ADMIN OR $THIS_GROUP_ADMIN OR $THIS_GROUP_ADMIN_2) {
	$where = '';
	if ($THIS_GROUP_ADMIN or $THIS_GROUP_ADMIN_2) {
		$where = "AND u.level < 40";
	}
	if ($THIS_LOCATION_ADMIN) {
		$where = "AND u.level < 50";
	}

	$Athletes_Select = '<span id="Select_Athletes_title">' . $LANG->INDEX_ATHLETE . ' : &nbsp; </span>' .
						'<select name="Select_Athletes" id="Select_Athletes">' .
							'<option value="' . $UID . '" selected>' . 
								$a_vorname . ' ' . $a_name . 
							'</option>';

	$u_rows = $db->fetch("SELECT u.id, u.uname, u.lastname, u.firstname, u.level 
		FROM users u 
		LEFT JOIN users2groups u2g ON u.id = u2g.user_id 
		WHERE u2g.group_id = ? AND u2g.status = 1 AND u.status = 1 $where AND u.id != ? 
		ORDER BY u.level DESC, u.firstname, u.lastname, u.id", array($GROUP, $UID)); //Group USERS
	if ($db->numberRows() > 0)  {
		foreach ($u_rows as $u_row) {
			$u_name = $u_row['lastname'] != '' ? $u_row['lastname'] : $u_row['uname'];
			$u_vorname = $u_row['firstname'] != '' ? $u_row['firstname'] : $u_row['uname'];

			$selected = '';
			if (isset($_COOKIE['ATHLETE']) and $_COOKIE['ATHLETE'] == $u_row['id']) {
				$selected = ' selected';
			}

			$Athletes_Select .= '<option value="' . $u_row['id'] . '"' . $selected . '>' . 
									$u_vorname . ' ' . $u_name . 
								'</option>';
		}
	}
	$Athletes_Select .= '</select>';
	$Athletes_Select .= '<script>jQuery(function(){Select__Athletes__Init();});</script>';
}
elseif ($TRAINER) {
	//give the name in case don't have athlete yet
	$Athletes_Select = '<div class="just_name">'.$a_vorname.' &nbsp; '.$a_name.'</div>';

	//Select Athletes in Group with Trainer this User-$UID
	$rows = $db->fetch("SELECT u.id, u.uname, u.lastname, u.firstname 
		FROM users2groups u2g 
		JOIN users u ON (u.id = u2g.user_id AND u.level = 10 AND u.status = 1) 
		JOIN users2trainers u2t ON (u.id = u2t.user_id AND u2g.group_id = u2t.group_id AND u2t.status = 1 AND u2t.trainer_id = ?) 
		WHERE u2g.group_id = ? AND u2g.status = 1 
		ORDER BY u.firstname, u.lastname, u.id", array($UID, $GROUP)); 
	if ($db->numberRows() > 0) {
		$Athletes_Select = '<span id="Select_Athletes_title">' . $LANG->INDEX_ATHLETE . ' : &nbsp; </span>' .
							'<select name="Select_Athletes" id="Select_Athletes">' .
								'<option value=""></option>' .
								'<option value="' . $UID . '" selected>' .
									$a_vorname . ' ' . $a_name . 
								'</option>';
		foreach ($rows as $row) {
			$u_name = $row['lastname'] != '' ? $row['lastname'] : $row['uname'];
			$u_vorname = $row['firstname'] != '' ? $row['firstname'] : $row['uname'];

			$selected = '';
			//if ($UID == $row['id']) $selected = ' selected'; //select self
			if (isset($_COOKIE['ATHLETE']) and $_COOKIE['ATHLETE'] == $row['id']) {
				$selected = ' selected';
			}

			if ($UID == $row['id']) {
				$row['id'] = -1; //select self but athlete-trainer mode
			}

			$Athletes_Select .= '<option value="' . $row['id'] . '"' . $selected . '>' . 
									$u_vorname . ' ' . $u_name . 
								'</option>';
		}
		$Athletes_Select .= '</select>';
		$Athletes_Select .= '<script>jQuery(function(){Select__Athletes__Init();});</script>';
	}
}
else {
	//give just the name
	$Athletes_Select = '<div class="just_name">'.$a_vorname.' &nbsp; '.$a_name.'</div>';
}
?>