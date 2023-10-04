<?php // ajax Save Template
$PATH_2_ROOT = '../';
require_once($PATH_2_ROOT.'_settings.regmon.php');
require($PATH_2_ROOT.'login/validate.php');

//print_r($_POST); exit;

$group_id = (int)($_POST['group_id'] ?? 0);
$athlete_id = (int)($_POST['athlete_id'] ?? 0);
$id = (int)($_POST['id'] ?? 0);
$title = $_POST['title'] ?? '';
$form_id = (int)($_POST['form_id'] ?? 0); //base_form_id
$data = $_POST['data'] ?? '';

// $GlobalView = (int)($_POST['GlobalView'] ?? 0);
// $LocationView = (int)($_POST['LocationView'] ?? 0);
// $GroupView = (int)($_POST['GroupView'] ?? 0);
// $TrainerView = (int)($_POST['TrainerView'] ?? 0);
// $Private = (int)($_POST['Private'] ?? 0);

$template_type = '';
if (isset($_REQUEST['template_type'])) {
		if ($_REQUEST['template_type'] == 'axis') 	 $template_type = 'templates_axis';
	elseif ($_REQUEST['template_type'] == 'forms') 	 $template_type = 'templates_forms';
	elseif ($_REQUEST['template_type'] == 'results') $template_type = 'templates_results';
}

if ($group_id)
{
	//############################################
	// Trainer View of an Athlete -> Trainer Save 
	if ($TRAINER) {
		if ($athlete_id != $UID) {
			if (!$athlete_id) $athlete_id = $UID;
			//Select Athletes in Group with Trainer this User-$UID
			$row = $db->fetchRow("SELECT u.id, u.lastname, u.firstname 
FROM `users2groups` u2g 
JOIN `users` u ON (u.id = u2g.user_id AND (u.level = 10 OR u.id = ?) AND u.status = 1) 
JOIN `users2trainers` u2t ON (u.id = u2t.user_id AND u2g.group_id = u2t.group_id AND u2t.status = 1 AND u2t.trainer_id = ?) 
WHERE u2g.group_id = ? AND u2g.status = 1 AND u.id = ? 
ORDER BY u.id", array($UID, $UID, $group_id, $athlete_id)); 
			//print_r($rows); 
			if (!$db->numberRows() > 0) {
				echo '<div class="empty_message">'.$LANG->NO_ACCESS_RIGHTS.'</div>';
				exit;
			}
		}
	}
	//############################################
	
	$values = array();
	$values['user_id'] 		= $UID; //$athlete_id;
	$values['location_id'] 	= $LOCATION;
	$values['group_id'] 	= $group_id;
	$values['name'] 		= $title;
	$values['data_json'] 	= $data;
	// $values['GlobalView'] 	= $GlobalView;
	// $values['LocationView'] 	= $LocationView;
	// $values['GroupView'] 	= $GroupView;
	// $values['TrainerView'] 	= $TrainerView;
	// $values['Private'] 		= $Private;
	$values['modified_by'] 	= $USERNAME;
	$values['modified'] 	= get_date_time_SQL('now');

	if ($template_type == 'templates_forms') {
		$values['form_id'] 	= $form_id;
	}

	$table = $template_type;

	//print_r($values);
	if ($id !== 0) {
		$save = $db->update($values, $table, "user_id=? AND group_id=? AND id=?", array($athlete_id, $group_id, $id));
	}
	else {
		$values['created_by'] = $USERNAME;
		$values['created'] = get_date_time_SQL('now');
		$save = true;
		$id = $db->insert($values, $table);
	}
	

	//load new data 
	if ($save) {
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
					$html .= '<option value="' . $save['id'] . '"' . $selected . '>' . $save['name'] . '</option>';
					if ($t_data != '') {
						$t_data .= ',';
					}
					$t_data .= $save['id'] . ':' . $save['data_json'];
				}
			}
			$html .= '</select>';
			$html .= '<script>' .
						'var axis_saved_data = {' . $t_data . '}; ' .
						'parent.V_AXIS_DATA = axis_saved_data; ' .
						'parent.V_Axis__Last_ID = ' . $id . ';' .
					'</script>';

			echo $html;
		}

		//results templates --always we have id
		elseif ($template_type == 'templates_results') {
			$save_data = '';
			
			$save = $db->fetchRow("SELECT id, name, data_json FROM templates_results WHERE id=? ORDER BY name", array($id));
			//, GlobalView, LocationView, GroupView, TrainerView, Private --perms out for now
			if ($db->numberRows() > 0) {
				//$perms = '['.$save['GlobalView'].','.$save['LocationView'].','.$save['GroupView'].','.$save['TrainerView'].','.$save['Private'].']'; 
				//$save_data .= '"name":"'.$save['name'].'", "perms":'.$perms.', '.substr($save['data_json'], 1, -1);

				$save_data .= '"name":"'.$save['name'].'", '.substr($save['data_json'], 1, -1);
			}
			$save_data = '{'.$save_data.'}';
			echo '<script>V_RESULTS_TEMPLATES['.$id.'] = '.$save_data.'</script>';
		}

		//form templates
		elseif ($template_type == 'templates_forms') {
			//form data saves
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
			//all data saves
			else {
				$rows = $db->fetch("SELECT * FROM templates_forms WHERE user_id=? AND group_id=? ORDER BY name", array($athlete_id, $group_id)); 
				$t_data = '';
				$html = '<select id="saved_select" name="saved_select" class="form-control">';
				if ($db->numberRows() > 0) {
					foreach ($rows as $row) {
						$selected = '';
						if ($id == $row['id']) {
							$selected = ' selected'; //select self
						}
						$html .= '<option value="'.$row['id'].'"'.$selected.'>'.$row['name'].'</option>';
						if ($t_data != '') {
							$t_data .= ',';
						}
						$t_data .= $row['id'].':'.$row['data_json'];
					}
				}
				$t_data .= '{'.$t_data.'}';
				$html .= '</select>';
				$html .= '<script>V_FORMS_TEMPLATES = '.$t_data.'</script>';
				
				echo $html;
			}
		}
	}
	else {
		echo 'ERROR';
	}
}
else {
	echo 'ERROR';
}
?>