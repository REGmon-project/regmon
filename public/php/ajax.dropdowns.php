<?php // ajax Dropdowns

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

		//options
		if (isset($_GET['oper']) AND $_GET['oper'] == 'options') {
			// Check if all fields are filled up
			if (trim($values['options']) == '') {
				echo $LANG->EMPTY_DROPDOWN_OPTION;
				exit;
			}

			$row = $db->fetchRow("SELECT id FROM dropdowns WHERE parent_id = ? AND options = ?", array($ID, $values['options']));
			if ($db->numberRows() > 0)  {
				echo $LANG->WARN_DROPDOWN_OPTION_EXIST;
				exit;
			}
		}
		//dropdown name
		else {
			// Check if all fields are filled up
			if (trim($values['name']) == '') {
				echo $LANG->EMPTY_DROPDOWN_NAME;
				exit;
			}

			$row = $db->fetchRow("SELECT id FROM dropdowns WHERE name=? $where_id", array($values['name']));
			if ($db->numberRows() > 0)  {
				echo $LANG->WARN_DROPDOWN_NAME_EXIST;
				exit;
			}
		}
		
		// INSERT 
		if ($action == 'add') {
			$values['parent_id'] = $ID;
			$values['modified'] = get_date_time_SQL('now');
			$values['created'] = get_date_time_SQL('now');
			
			$insert_id = $db->insert($values, "dropdowns");
			
			echo check_insert_result($insert_id);
		}
		// UPDATE 
		elseif ($action == 'edit') {
			$values['modified'] = get_date_time_SQL('now');
			
			$result = $db->update($values, "dropdowns", "id=?", array($id));

			echo check_update_result($result);
		}

	  break;
	  

	case 'del': // DELETE 
		
		$row = $db->fetchRow("SELECT * FROM dropdowns WHERE id=?", array($id));
		if ($db->numberRows() > 0)  {

			$row2 = $db->fetchRow("SELECT * FROM dropdowns WHERE parent_id=?", array($id));
			if ($db->numberRows() > 0)  {
				echo str_replace('{OPTIONS_NUM}', $db->numberRows(), $LANG->WARN_DROPDOWN_DELETE); //'Cannot delete. Have 5 options'
			}
			else {
				$result = $db->delete("dropdowns", "id=?", array($id));
				echo check_delete_result($result);
			}
		}

	  break;


	case 'options': // SELECT 
		
		$response = new stdClass();
		$sidx = $sidx ?? '';
		$sord = $sord ?? '';
		$rows = $db->fetch("SELECT * FROM dropdowns WHERE parent_id=? ORDER BY $sidx $sord", array($ID)); 
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
		$rows = $db->fetch("SELECT * FROM dropdowns WHERE parent_id = 0 ORDER BY $sidx $sord", array()); 
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