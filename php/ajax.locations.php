<?php // ajax Locations

//ajax.php
/** @var string $action */
/** @var int $id */

if ($SEC_check != $CONFIG['SEC_Page_Secret']) exit;

switch ($action) {
	case 'add': // INSERT 
	case 'edit': // UPDATE 
		$values = array();			
		foreach ($_POST as $key => $val) {
			$key = trim((string)$key); 
			$val = trim((string)$val); 
			switch($key) {
				case 'name': 
				case 'status': 
				case 'admin_id': 
					$values[$key] = $val;
				  break;
			}
		}		
		
		// Check if all fields are filled up
		if (trim($values['name']) == '') {
			echo $LANG->EMPTY_LOCATION_NAME;
			exit;
		}
		if (trim($values['admin_id']) == '') {
			$values['admin_id'] = '0';
		}

		//check admin user
		if (!$ADMIN) {
			//this may not needed because only admin can have access
			//Location Admin
			$admin = $db->fetchRow("SELECT u.id, u.lastname FROM users u
					LEFT JOIN locations l ON u.id = l.admin_id
					WHERE l.id = ? AND u.level = 50 AND u.id = ?", array($id, $UID)); 
			if (!$db->numberRows() > 0)  {
				echo $LANG->NEED_ADMIN_RIGHTS;
				exit; //no admin user
			}
		}
		
		$where_id = '';
		if ($id != 0) $where_id = "AND id != $id"; //if edit = we have id  --not include the current id in the name exist check
		$row = $db->fetchRow("SELECT * FROM locations WHERE name=? $where_id", array($values['name']));
		if ($db->numberRows() > 0)  {
			echo $LANG->WARN_LOCATION_EXIST;
		}
		else 
		{
			// INSERT 
			if ($action == 'add')
			{
				//Insert
				$values['modified'] = get_date_time_SQL('now');
				$values['created'] = get_date_time_SQL('now');
				
				$insert_id = $db->insert($values, "locations");
				
				echo check_insert_result($insert_id);
			}
			// UPDATE 
			elseif ($action == 'edit')
			{
				$values['modified'] = get_date_time_SQL('now');
				
				$result = $db->update($values, "locations", "id=?", array($id));

				echo check_update_result($result);
			}
		}

	  break;


	case 'del': // DELETE 
		
		//check admin user
		if (!$ADMIN) {
			//Location Admin
			$admin = $db->fetchRow("SELECT u.id, u.lastname FROM users u
					LEFT JOIN locations l ON u.id = l.admin_id
					WHERE l.id = ? AND u.level = 50 AND u.id = ?", array($id, $UID)); 
			if (!$db->numberRows() > 0)  {
				echo $LANG->NEED_ADMIN_RIGHTS;
				exit; //no admin user
			}
		}

		//TODO: what if Location has Groups ???? need work here

		/*
		$row = $db->fetchRow("SELECT * FROM locations WHERE id=?", array($id));
		
		$result = $db->delete("locations", "id=?", array($id));
			
		echo check_delete_result($result);
		*/

	  break;


	case 'locations_admins_select': // SELECT
		echo get_locations_admins_select();
	  break;

	case 'locations_select': // SELECT
		echo get_locations_select();
	  break;

	case 'locations_options_grid': // SELECT
		echo get_locations_select(true);
	  break;


	case 'view': // SELECT 
	default: //view
		
		$response = new stdClass();
		$sidx = $sidx ?? '';
		$sord = $sord ?? '';
		$order = '';
		if (trim($sidx) != '') {
			$order = "ORDER BY $sidx $sord";
		}
		$rows = $db->fetch("SELECT * FROM locations $order", array());
		$i=0;
		if ($db->numberRows() > 0)  {
			foreach ($rows as $row) {
				$response->rows[$i] = $response->rows[$i]['cell'] = array(
					'',
					$row['id'],
					$row['name'],
					$row['status'],
					$row['admin_id'],
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