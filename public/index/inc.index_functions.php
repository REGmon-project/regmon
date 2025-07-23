<?php // index Functions

if ($SEC_check != $CONFIG['SEC_Page_Secret']) exit;

// print_r alias function
function PR(mixed $array):void {
	echo "<pre>";
	print_r($array);
	echo "</pre>";
}


function get_User_2_Groups(int $_UID):mixed {
	global $db;
	$user_2_groups = array();
	$rows = $db->fetch("SELECT group_id, status, modified FROM users2groups WHERE user_id = ? ", array($_UID));
	if ($db->numberRows() > 0) {
		foreach ($rows as $row) {
			$user_2_groups[$row['group_id']]['status'] = $row['status'];
			$user_2_groups[$row['group_id']]['modified'] = get_date_time($row['modified'] . '');
		}
	}
	return $user_2_groups;
}


function get_Trainers_2_Groups():mixed {
	global $db;
	$trainers_2_groups = array();
	$rows = $db->fetch("SELECT u2g.group_id, GROUP_CONCAT(CONVERT(u.id, CHAR(11))) AS ids 
		FROM users2groups u2g 
		LEFT JOIN users u ON u.id = u2g.user_id AND u.level = 30 AND u.status = 1 
		WHERE u2g.status = 1 GROUP BY u2g.group_id", array()); 
	if ($db->numberRows() > 0)  {
		foreach ($rows as $row) {
			$trainers_2_groups[$row['group_id']] = $row['ids'];
		}
	}
	return $trainers_2_groups;
}

function get_Locations_array():mixed {
	global $db;
	$locations_arr = array();
	$rows = $db->fetch("SELECT id, name, admin_id FROM locations WHERE status = 1 ORDER BY id", array()); 
	if ($db->numberRows() > 0)  {
		foreach ($rows as $row) {
			$locations_arr[$row['id']] = array($row['name'], $row['admin_id']);
		}
	}
	return $locations_arr;
}

function get_Group_Request_Status_Class(string $group_status):string {
	$status_array = array(
		'0' => 'G_no',
		'1' => 'G_yes',
		'2' => 'G_yesStop',
		'5'	=> 'G_leaveR',
		'15'=> 'G_leaveA',
		'7'	=> 'G_waitLR',
		'17'=> 'G_waitLA',
		'8'	=> 'G_waitN',
		'9'	=> 'G_wait'
	);
	return $status_array[$group_status] ?? '';
}

// get Dashboard Links Array
function get_Dashboard_Links_Array(int $_UID, int $_GROUP):string {
	global $db;
	$dashboard_js_array = '';
	$dash_rows = $db->fetch("SELECT id, name, type, options, sort, color FROM dashboard WHERE user_id=? AND group_id=? ORDER BY  name", array($_UID, $_GROUP)); 
	if ($db->numberRows() > 0) {
		foreach ($dash_rows as $dash) {
			if ($dashboard_js_array != '') {
				$dashboard_js_array .= ',';
			}
			$dashboard_js_array .= '['.$dash['id'].',"'.$dash['name'].'","'.$dash['type'].'","'.$dash['options'].'",'.$dash['sort'].',"'.$dash['color'].'"]';
		}
	}
	return $dashboard_js_array;
}

?>