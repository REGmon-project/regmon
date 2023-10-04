<?php // ajax Note Save
$PATH_2_ROOT = '../';
require_once($PATH_2_ROOT.'_settings.regmon.php');
require($PATH_2_ROOT.'login/validate.php');

$ID 			= isset($_POST['ID']) ? abs($_POST['ID']) : false; //abs fix negative ID
$group_id 		= isset($_POST['group_id']) ? (int)$_POST['group_id'] : false;
$athlete_id 	= isset($_POST['athlete_id']) ? (int)$_POST['athlete_id'] : false;
$t_isAllDay 	= isset($_POST['t_isAllDay']) ? $_POST['t_isAllDay'] : 'true';
$t_date_start 	= $_POST['t_date_start'] ?? get_date_SQL('now');
$t_date_end 	= $_POST['t_date_end'] ?? get_date_SQL('now');
$t_time_start	= $_POST['t_time_start'] ?? date("H:i");
$t_time_end 	= $_POST['t_time_end'] ?? date("H:i");
$t_title 		= $_POST['t_title'] ?? '';
$t_note 		= $_POST['t_note'] ?? '';
$t_showInGraph 	= isset($_POST['t_showInGraph']) ? $_POST['t_showInGraph'] : 'false';
$t_color 		= $_POST['t_color'] ?? '#aaaaaa';

if ($t_isAllDay == 'true') {
	$selected_date_start = date("Y-m-d H:i:s", (int)strtotime($t_date_start.' 00:00:00'));
	$selected_date_end = date("Y-m-d H:i:s", (int)strtotime($t_date_end.' 23:59:59'));
} else {
	$selected_date_start = date("Y-m-d H:i:s", (int)strtotime($t_date_start.' '.$t_time_start));
	$selected_date_end = date("Y-m-d H:i:s", (int)strtotime($t_date_end.' '.$t_time_end));
}

if ($group_id AND $athlete_id)
{
	//count num of notes in calendar in that day
	if (!$ID) { //dont check if is update
		$selected_date_start_day = get_date_SQL($t_date_start);
		$row = $db->fetchRow("SELECT COUNT(*) AS count FROM notes WHERE user_id = ? AND group_id = ? AND timestamp_start LIKE '$selected_date_start_day%'", array($athlete_id, $group_id));
		if ($row['count'] >= 3) {
			//not accept more than 3 notes a day
			echo 'ERROR-MAX3';
			exit;
		}
	}

	$values = array();
	$values['user_id'] = $athlete_id;
	$values['group_id'] = $group_id;
	$values['isAllDay'] = ($t_isAllDay=='true' ? 1 : 0);
	$values['showInGraph'] = ($t_showInGraph=='true' ? 1 : 0);
	$values['name'] = $t_title;
	$values['notes'] = $t_note;
	$values['color'] = $t_color;
	$values['modified'] = get_date_time_SQL('now');
	$values['timestamp_start'] = $selected_date_start;
	$values['timestamp_end'] = $selected_date_end;
	
	if ($ID) {
		$save = $db->update($values, "notes", "id=?", array($ID));
	}
	else {
		$save = $db->insert($values, "notes");
	}
	
	if ($save) {
		echo 'OK';
	}
	else {
		echo 'ERROR';
	}
}
else {
	echo 'ERROR';
}
?>