<?php // ajax Forms Selection Save
$PATH_2_ROOT = '../';
require_once($PATH_2_ROOT.'_settings.regmon.php');
require($PATH_2_ROOT.'login/validate.php');

if (isset($_POST['group_id']) AND isset($_POST['forms_select'])) 
{
	$group_id = $_POST['group_id'];
	$values = array();
	
	//we get only the "on" = selected items 

	//new format = category_form
	//sel_g_1_c_1_4=on&sel_g_1_c_2_5=on  //select   - read
	//std_g_1_c_1_4=on&std_g_1_c_2_5=on  //standard - write
	
	//we replace default strings so it will remain only the "category_form" ex. 1_4,2_5
	/** @var string $forms_select */
	$forms_select = str_replace('=on&sel_g_'.$group_id.'_c_', ',', $_POST['forms_select']);
	$forms_select = str_replace(array('sel_g_'.$group_id.'_c_', '=on'), '', $forms_select);

	$forms_standard = '';
	if (isset($_POST['forms_standard'])) {
		$forms_standard = str_replace('=on&std_g_'.$group_id.'_c_', ',', $_POST['forms_standard']);
		$forms_standard = str_replace(array('std_g_'.$group_id.'_c_', '=on'), '', $forms_standard);
	}
	
	//group - admin
	if (isset($_POST['admin']) AND $_POST['admin']) {
		$values['forms_select'] = $forms_select;
		$values['forms_standard'] = $forms_standard;
		$values['modified'] = get_date_time_SQL('now');
		$values['modified_by'] = $USERNAME;
		$save = $db->update($values, "`groups`", "id=?", array($group_id));
	}
	//users2trainers - athlete save Trainer Access
	elseif (isset($_POST['trainer']) AND $_POST['trainer'] AND isset($_POST['trainer_id']) AND $_POST['trainer_id']) {
		$trainer_id = $_POST['trainer_id'];
		$values['forms_select_read'] = $forms_select;
		$values['forms_select_write'] = $forms_standard;
		$values['modified'] = get_date_time_SQL('now');
		$values['modified_by'] = $USERNAME;
		$save = $db->update($values, "users2trainers", "user_id=? AND group_id=? AND trainer_id=?", array($UID, $group_id, $trainer_id));
	}
	//users2groups - athlete save Forms Selecting
	//also remove Forms from Trainers Access if missing after the new selection of athlete
	else {
		//update Athlete Forms Selection
		$values['forms_select'] = $forms_select;
		$values['modified'] = get_date_time_SQL('now');
		$values['modified_by'] = $USERNAME;
		$save = $db->update($values, "users2groups", "user_id=? AND group_id=?", array($UID, $group_id));
		
		//update Athlete Trainers Forms Access
		//remove trainers forms if not available any more in athlete forms selection
		$athlete_trainers = $db->fetch("SELECT id, forms_select_read, forms_select_write FROM users2trainers WHERE user_id=? AND group_id=?", array($UID, $group_id));
		//leave Note_n out of this check --add it to the array
		$athlete_forms_selected = explode(',', $forms_select.',Note_n');
		
		foreach($athlete_trainers as $athlete_trainer) {
			$forms_trainer_read_arr = explode(',', $athlete_trainer['forms_select_read']??'');
			$forms_trainer_write_arr = explode(',', $athlete_trainer['forms_select_write']??'');
			$forms_trainer_read_str = '';
			$forms_trainer_write_str = '';
			foreach($forms_trainer_read_arr as $forms_trainer_read) {
				if (in_array($forms_trainer_read, $athlete_forms_selected)) {
					if ($forms_trainer_read_str != '') $forms_trainer_read_str .= ',';
					$forms_trainer_read_str .= $forms_trainer_read;
				}
			}
			foreach($forms_trainer_write_arr as $forms_trainer_write) {
				if (in_array($forms_trainer_write, $athlete_forms_selected)) {
					if ($forms_trainer_write_str != '') $forms_trainer_write_str .= ',';
					$forms_trainer_write_str .= $forms_trainer_write;
				}
			}
			$values = array();
			$values['forms_select_read'] = $forms_trainer_read_str;
			$values['forms_select_write'] = $forms_trainer_write_str;
			//update Athlete Trainers Forms Access
			$save = $db->update($values, "users2trainers", "user_id=? AND group_id=? AND id=?", array($UID, $group_id, $athlete_trainer['id']));
		}
	}
}
?>