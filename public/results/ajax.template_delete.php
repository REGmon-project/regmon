<?php // ajax Delete Template
$PATH_2_ROOT = '../';
require_once($PATH_2_ROOT.'_settings.regmon.php');
require($PATH_2_ROOT.'login/validate.php');

//print_r($_POST); exit;

$group_id 	= (int)($_POST['group_id'] ?? 0);
$athlete_id = (int)($_POST['athlete_id'] ?? 0);
$id 		= (int)($_POST['id'] ?? 0);
$form_id 	= (int)($_POST['form_id'] ?? 0);

$template_type = '';
if (isset($_REQUEST['template_type'])) {
		if ($_REQUEST['template_type'] == 'axis') 	 $template_type = 'templates_axis';
	elseif ($_REQUEST['template_type'] == 'forms') 	 $template_type = 'templates_forms';
	elseif ($_REQUEST['template_type'] == 'results') $template_type = 'templates_results';
}


if ($group_id AND $athlete_id AND $id)
{
	if ($id == '1') {
		if ($template_type == 'templates_axis') {
			//id 1 = Auto Axis -> cannot be deleted
			echo '.....' . $LANG->NO_ACCESS_RIGHTS;
			exit;
		}
	}

	//############################################
	// Trainer View of an Athlete -> Trainer Delete 
	if ($TRAINER) {
		//check if not the same user --not for admin
		if ($athlete_id != $UID AND ($UID != 1)) {
			if ($athlete_id == '0') {
				$athlete_id = $UID;
			}
			//Select Athletes in Group with Trainer this User-$UID
			$row = $db->fetchRow("SELECT u.id, u.lastname, u.firstname 
FROM `users2groups` u2g 
JOIN `users` u ON (u.id = u2g.user_id AND (u.level = 10 OR u.id = ?) AND u.status = 1) 
JOIN `users2trainers` u2t ON (u.id = u2t.user_id AND u2g.group_id = u2t.group_id AND u2t.status = 1 AND u2t.trainer_id = ?) 
WHERE u2g.group_id = ? AND u2g.status = 1 AND u.id = ? 
ORDER BY u.id", array($UID, $UID, $group_id, $athlete_id)); 
			if (!$db->numberRows() > 0) {
				echo '.....'.$LANG->NO_ACCESS_RIGHTS;
				exit;
			}
		}
	}
	//############################################
	
	$table = $template_type;

	if ($UID == 1) { //admin can just delete
		$result = $db->delete($table, "id = ?", array($id));
	}
	else { //users only what they saved
		$result = $db->delete($table, "user_id = ? AND id = ?", array($id));
	}

	
	if (!$result) {
		echo str_replace('<br>', "\n", $LANG->DELETE_ERROR); //echo mysql_error();
	}
	else {
		//AXIS
		if ($template_type == 'templates_axis') {
			$t_data = '';
			$html = '<select id="saved_select" name="saved_select" class="form-control">';
			$saves = $db->fetch("SELECT * FROM templates_axis ORDER BY name", array()); 
			if ($db->numberRows() > 0) {
				foreach ($saves as $save) {
					$selected = '';
					if ($id == $save['id']) {
						$selected = ' selected'; //select self
					}

					$html .= '<option value="'.$save['id'].'"'.$selected.'>'.$save['name'].'</option>';
					if ($t_data != '') {
						$t_data .= ',';
					}

					$t_data .= $save['id'].':'.$save['data_json'];
				}
			}
			$html .= '</select>';
			
			$html .= '<script>'.
						'var axis_saved_data = {'.$t_data.'}; '.
						'parent.V_AXIS_DATA = axis_saved_data;'.
					'</script>';	
			
			echo $html;
		}

		//results templates --always we have id
		elseif ($template_type == 'templates_results') {
			//delete results template
			echo '<script>delete V_RESULTS_TEMPLATES['.$id.'];</script>';
		}

		//forms templates
		elseif ($template_type == 'templates_forms') {
			//form data set again
			if ($form_id) {
				$Forms_Templates_Data = '';
				$saves = $db->fetchAllwithKey("SELECT id, form_id, name, data_json FROM templates_forms WHERE form_id=? ORDER BY form_id, name", array($form_id), 'id'); 
				if ($db->numberRows() > 0) {
					foreach ($saves as $save_id => $save) {
						if ($Forms_Templates_Data != '') {
							$Forms_Templates_Data .= ',';
						}
						$Forms_Templates_Data .= '"'.$save_id.'":{"name":"'.$save['name'].'",'.substr($save['data_json'], 1, -1).'}';
					}
				}
				echo '<script>V_FORMS_TEMPLATES['.$form_id.'] = {'.$Forms_Templates_Data.'}</script>';
			}
			//all data set again
			else {
				$rows = $db->fetch("SELECT * FROM templates_forms WHERE user_id=? AND group_id=? ORDER BY name", array($athlete_id, $group_id)); 
				$t_data = '{';
				$html = '<select id="saved_select" name="saved_select" class="form-control">';
				if ($db->numberRows() > 0) {
					$i = 0;
					foreach ($rows as $row) {
						$selected = '';
						if ($id == $row['id']) {
							$selected = ' selected'; //select self
						}

						$html .= '<option value="' . $row['id'] . '"' . $selected . '>' . $row['name'] . '</option>';

						if ($i != 0) {
							$t_data .= ',';
						}
						$t_data .= $row['id'] . ':' . $row['data_json'];
						$i++;
					}
				}
				$t_data .= '}';
				$html .= '</select>';
				$html .= '<script>V_FORMS_TEMPLATES = ' . $t_data . '</script>';
				
				echo $html;
			}
		}
	}
}
else {
	echo 'ERROR';
}
?>