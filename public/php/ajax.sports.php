<?php // ajax Sports

//ajax.php
/** @var string $action */
/** @var int $id */
/** @var int $ID */

if ($SEC_check != $CONFIG['SEC_Page_Secret']) exit;

switch ($action) {
	case 'add': // INSERT 
	case 'edit': // UPDATE 
		$values = array();			
		foreach ($_POST as $key => $val) {
			$key = trim((string)$key); 
			$val = trim((string)$val); 
			switch($key) {
				case 'parent_id': 
				case 'name': 
				case 'options': 
				case 'status': 
					$values[$key] = $val;
				  break;
			}
		}		
				
		$where_id = '';
		if ($id != 0) {
			$where_id = "AND id != " . ((int)$id); //if edit = have id --not check the same entry
		}

		//sport options
		if (isset($_GET['oper']) AND $_GET['oper'] == 'options') {
			// Check if all fields are filled up
			if (trim($values['options']) == '') {
				echo $LANG->EMPTY_SPORT_NAME;
				exit;
			}

			$row = $db->fetchRow("SELECT id FROM sports WHERE options=? $where_id", array($values['options']));
			if ($db->numberRows() > 0)  {
				echo $LANG->WARN_SPORT_EXIST;
				exit;
			}
		}
		//sport groups
		else {
			// Check if all fields are filled up
			if (trim($values['name']) == '') {
				echo $LANG->EMPTY_SPORT_GROUP;
				exit;
			}

			$row = $db->fetchRow("SELECT id FROM sports WHERE name=? $where_id", array($values['name']));
			if ($db->numberRows() > 0)  {
				echo $LANG->WARN_SPORT_GROUP_EXIST;
				exit;
			}

			$values['parent_id'] = '0';
		}

		// INSERT
		if ($action == 'add') {
			$values['parent_id'] = $ID;
			$values['modified'] = get_date_time_SQL('now');
			$values['created'] = get_date_time_SQL('now');
			
			$insert_id = $db->insert($values, "sports");
			
			echo check_insert_result($insert_id);
		}
		// UPDATE
		elseif ($action == 'edit') {
			$values['modified'] = get_date_time_SQL('now');
			
			$result = $db->update($values, "sports", "id=?", array($id));

			echo check_update_result($result);
		}

	  break;
	  
	  
	case 'del': // DELETE 
		
		//TODO: what if any users already selected this sport ???
		//TODO: what if delete a parent_id ???

		$result = $db->delete("sports", "id=?", array($id));
			
		echo check_delete_result($result);

	  break;
	  

	case 'sports_groups_select': // SELECT 
		echo get_Sports_Groups().'';
	  break;


	case 'sports_select': // SELECT 
		echo '<select>'.get_Sports_Select_Options_By_Group().'</select>';
	  break;
	  

	case 'options': // SELECT 
		
		$response = new stdClass();
		$sidx = $sidx ?? '';
		$sord = $sord ?? '';
		$rows = $db->fetch("SELECT * FROM sports WHERE status = 1 AND parent_id=? ORDER BY $sidx $sord", array($ID)); 
		$i=0;
		if ($db->numberRows() > 0)  {
			foreach ($rows as $row) {
				$response->rows[$i] = $response->rows[$i]['cell'] = array(
					'',
					$row['id'],
					$row['parent_id'],
					$row['options'],
					$row['status'],
					get_date_time_SQL($row['created'].''),
					get_date_time_SQL($row['modified'].'')
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
	  

	case 'view': // SELECT 
	default: //view
		
		$response = new stdClass();
		$sidx = $sidx ?? '';
		$sord = $sord ?? '';
		$rows = $db->fetch("SELECT * FROM sports WHERE parent_id = 0 ORDER BY $sidx $sord", array()); 
		$i=0;
		if ($db->numberRows() > 0)  {
			foreach ($rows as $row) {
				$response->rows[$i] = $response->rows[$i]['cell'] = array(
					'',
					$row['id'],
					$row['name'],
					$row['status'],
					get_date_time_SQL($row['created'].''),
					get_date_time_SQL($row['modified'].'')
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