<?php // ajax Templates

//ajax.php
/** @var string $action */
/** @var int $ID */
/** @var string $where */

if ($SEC_check != $CONFIG['SEC_Page_Secret']) exit;

switch ($action) {
	case 'add': // INSERT 
	case 'edit': // UPDATE 
		$template_type = '';
		if (isset($_REQUEST['template_type'])) {
				if ($_REQUEST['template_type'] == 'axis') 	 $template_type = 'templates_axis';
			elseif ($_REQUEST['template_type'] == 'forms') 	 $template_type = 'templates_forms';
			elseif ($_REQUEST['template_type'] == 'results') $template_type = 'templates_results';
		}
		$table = $template_type;

		$values = array();
		$id 					= (int)($_POST['id'] ?? 0);
		$values['user_id'] 		= (int)($_POST['user_id'] ?? 0);
		$values['location_id'] 	= (int)($_POST['location_id'] ?? 0);
		$values['group_id'] 	= (int)($_POST['group_id'] ?? 0);
		$values['name'] 		= $_POST['templatename'] ?? '';
		// $values['GlobalView'] 	= isset($_POST['GlobalView']) ? ($_POST['GlobalView']=='Ja'?'1':'0') : '1';
		// $values['LocationView'] = isset($_POST['LocationView']) ? ($_POST['LocationView']=='Ja'?'1':'0') : '1';
		// $values['GroupView'] 	= isset($_POST['GroupView']) ? ($_POST['GroupView']=='Ja'?'1':'0') : '1';
		// $values['TrainerView'] 	= isset($_POST['TrainerView']) ? ($_POST['TrainerView']=='Ja'?'1':'0') : '1';
		// $values['Private'] 		= isset($_POST['Private']) ? ($_POST['Private']=='Ja'?'1':'0') : '0';
		$row['modified'] 		= get_date_time_SQL('now');
		$row['modified_by'] 	= $USERNAME;

		if ($template_type == 'templates_forms') {
			$values['form_id'] = (int)($_POST['form_id'] ?? 0);
		}

		$update = $db->update($values, $table, "id=?", array($id));
		
		echo check_update_result($update);
		
	  break;
	

	case 'del': // DELETE 
		//delete at ajax.template_delete.php

	  break;

	  
	case 'template_duplicate': // INSERT - Duplicate Template 
		$template_type = '';
		if (isset($_REQUEST['template_type'])) {
				if ($_REQUEST['template_type'] == 'axis') 	 $template_type = 'templates_axis';
			elseif ($_REQUEST['template_type'] == 'forms') 	 $template_type = 'templates_forms';
			elseif ($_REQUEST['template_type'] == 'results') $template_type = 'templates_results';
		}
		$table = $template_type;
		
		$row = $db->fetchRow("SELECT * FROM $table WHERE id = ?", array($ID));
		if ($db->numberRows() > 0)  {
			unset($row['id']);
			$row['name'] .= '_copy';
			$row['modified'] = get_date_time_SQL('now');
			$row['modified_by'] = $USERNAME;
			$row['created'] = get_date_time_SQL('now');
			$row['created_by'] = $USERNAME;
			
			$insert_id = $db->insert($row, $table);
			
			echo check_insert_result($insert_id);
		}
		
	  break;


	case 'templates_forms': // SELECT 

		$response = new stdClass();

		$forms = $db->fetchAllwithKey("SELECT id, name, name2, status FROM forms WHERE status = 1 $where ORDER BY id", array() ,'id');
		//echo "<pre>";print_r($forms);exit;

		$saves = $db->fetchAllwithKey("SELECT id, user_id, location_id, group_id, form_id, name, created, created_by, modified, modified_by FROM templates_forms ORDER BY form_id, name", array(), 'id'); //fetchAllwithKey2,'form_id', 'id'
		//GlobalView, LocationView, GroupView, TrainerView, Private, 
		$i=0;
		if ($db->numberRows() > 0)  {
			foreach ($saves as $save) {
				$response->rows[$i] = $response->rows[$i]['cell'] = array(
					'',
					$save['id'],
					$save['form_id'],
					(isset($forms[$save['form_id']]) ? $forms[$save['form_id']]['name'].' ('.$forms[$save['form_id']]['name2'].')' :''),
					(isset($forms[$save['form_id']]) ? $forms[$save['form_id']]['status'] :'0'),
					$save['name'],
					$save['user_id'],
					$save['location_id'],
					$save['group_id'],
					// $save['GlobalView'],
					// $save['LocationView'],
					// $save['GroupView'],
					// $save['TrainerView'],
					// $save['Private'],
					$save['created'],
					$save['created_by'],
					$save['modified'],
					$save['modified_by']
				);
				$i++;
			}
		}
		
		$response = json_encode($response);
		
		if ($response == '""') //if empty
			echo '{"rows":[]}';
		else 
			echo $response;
			
	  break;
	  
	  
	case 'templates_results': // SELECT 
		
		$response = new stdClass();
		$saves2 = $db->fetchAllwithKey("SELECT id, user_id, location_id, group_id, name, created, created_by, modified, modified_by FROM templates_results ORDER BY name", array(), 'id'); 
		//GlobalView, LocationView, GroupView, TrainerView, Private, 
		$i=0;
		if ($db->numberRows() > 0)  {
			foreach ($saves2 as $save) {
				$response->rows[$i] = $response->rows[$i]['cell'] = array(
					'',
					$save['id'],
					$save['name'],
					$save['user_id'],
					$save['location_id'],
					$save['group_id'],
					// $save['GlobalView'],
					// $save['LocationView'],
					// $save['GroupView'],
					// $save['TrainerView'],
					// $save['Private'],
					$save['created'],
					$save['created_by'],
					$save['modified'],
					$save['modified_by']
				);
				$i++;
			}
		}
		
		$response = json_encode($response);
		
		if ($response == '""') //if empty
			echo '{"rows":[]}';
		else 
			echo $response;
			
	  break;

	
	case 'templates_axis': // SELECT 
		$response = new stdClass();

		$axis = $db->fetchAllwithKey("SELECT id, user_id, location_id, group_id, name, created, created_by, modified, modified_by FROM templates_axis ORDER BY name", array(), 'id');
		//GlobalView, LocationView, GroupView, TrainerView, Private, 
		$i=0;
		if ($db->numberRows() > 0)  {
			foreach ($axis as $save) {
				$response->rows[$i] = $response->rows[$i]['cell'] = array(
					'',
					$save['id'],
					$save['name'],
					$save['user_id'],
					$save['location_id'],
					$save['group_id'],
					// $save['GlobalView'],
					// $save['LocationView'],
					// $save['GroupView'],
					// $save['TrainerView'],
					// $save['Private'],
					$save['created'],
					$save['created_by'],
					$save['modified'],
					$save['modified_by']
				);
				$i++;
			}
		}
		
		$response = json_encode($response);
		
		if ($response == '""') //if empty
			echo '{"rows":[]}';
		else 
			echo $response;
			
	  break;

}
?>