<?php // ajax Groups

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
				case 'location_id': 
				case 'name': 
				case 'status': 
				case 'admins_id': 
				case 'private_key': 
					$values[$key] = $val;
				  break;
				case 'stop_date':
					if ($val != '') {
						$values[$key] = get_date_SQL($val . '');
					} else {
						$values[$key] = array("NULL"); //=NULL
					}
				  break;
			}
		}
		
		// Check if all fields are filled up
		if ($values['name'] == '') {
			echo $LANG->EMPTY_GROUP_NAME;
			exit;
		}

		if ($ID != 0) { //from interface
			$values['location_id'] = $ID;
		}
		if ($values['location_id'] == '') {
			echo $LANG->EMPTY_LOCATION_ID;
			exit;
		}
		$location_id = $values['location_id'];
		
		//remove first and last commas
		/** @var mixed $values['admins_id'] */
		$values['admins_id'] = ltrim(rtrim($values['admins_id'].'', ','), ',');
		$group_admins_ids_arr = explode(',', $values['admins_id'].'');
		
		$location_admin = $db->fetchRow("SELECT admin_id FROM locations WHERE id=?", array($location_id));
		$location_admin = $location_admin['admin_id']??'';
		
		//check if admin user
		if (!$ADMIN) {
			//check if is this Location Admin
			if ($location_admin != $UID) {
				echo $LANG->NEED_ADMIN_RIGHTS;
				exit; //no admin user
			}
		}
		
		$where_id = '';
		if ($id != 0) $where_id = "AND id != ".((int)$id); //if edit = have id
		$row = $db->fetchRow("SELECT * FROM `groups` WHERE name=? $where_id", array($values['name']));
		if ($db->numberRows() > 0)  {
			echo $LANG->WARN_GROUP_EXIST;
			exit;
		}
		if ($values['private_key'] != '') {
			//check pass < 8 chars
			if (strlen($values['private_key'].'') < 8) {
				echo $LANG->WARN_PRIVATE_KEY_CHARS;
				exit;
			}
			$row = $db->fetchRow("SELECT * FROM `groups` WHERE private_key=? $where_id", array($values['private_key']));
			if ($db->numberRows() > 0)  {
				echo $LANG->WARN_PRIVATE_KEY_EXIST;
				exit;
			}
		}
		if ($values['status'] == '3' AND $values['private_key'] == '') { //private group empty key
			echo $LANG->WARN_PRIVATE_KEY_CHARS;
			exit;
		}
		
		$admin_rows = $db->fetch("SELECT id FROM users WHERE level='99'", array());
		
		
		// INSERT 
		if ($action == 'add')
		{
			$values['modified'] = get_date_time_SQL('now');
			$values['created'] = get_date_time_SQL('now');
			
			$insert_id = $db->insert($values, "`groups`");
			echo check_insert_result($insert_id);
			
			if ($insert_id AND $insert_id == (int)$insert_id) {
				//register Admins in this Group automatic
				$values = array();
				$values['group_id'] = $insert_id;
				$values['status'] = '1';
				$values['created'] = get_date_time_SQL('now');
				$values['created_by'] = 'Auto_Init';
				$values['modified'] = get_date_time_SQL('now');
				$values['modified_by'] = 'Auto_Init';
				
				//Admins
				foreach ($admin_rows as $admin) {
					$values['user_id'] = $admin['id'];
					$save = $db->insert($values, "users2groups");
				}

				//Location Admin
				$values['user_id'] = $location_admin;
				$save = $db->insert($values, "users2groups");

				//Group Admins
				foreach ($group_admins_ids_arr as $group_admins_id) {
					$values['user_id'] = $group_admins_id;
					$save = $db->insert($values, "users2groups"); 
				}
			}
		}
		// UPDATE 
		elseif ($action == 'edit')
		{
			$values['modified'] = get_date_time_SQL('now');
			
			$result = $db->update($values, "`groups`", "id=?", array($id));
			echo check_update_result($result);
			
			//register the selected Group Admins in this Group automatic
			$values = array();
			$values['group_id'] = $id;
			$values['status'] = '1';
			$values['created'] = get_date_time_SQL('now');
			$values['created_by'] = 'Auto_Init';
			$values['modified'] = get_date_time_SQL('now');
			$values['modified_by'] = 'Auto_Init';
			foreach ($group_admins_ids_arr as $group_admin_id) {
				$row = $db->fetchRow("SELECT * FROM users2groups WHERE group_id=? AND user_id = ?", array($id, $group_admin_id));
				if (!$db->numberRows() > 0)  { //not exist -insert
					$values['user_id'] = $group_admin_id;
					$save = $db->insert($values, "users2groups"); //group admin
				}
			}
		}

	  break;


	case 'del': // DELETE 
		
		//check admin user
		if (!$ADMIN) {
			//Location Admin
			$admin = $db->fetchRow("SELECT u.id, u.lastname 
				FROM users u
				LEFT JOIN locations l ON u.id = l.admin_id
				WHERE l.id = ? AND u.level = 50 AND u.id = ?", array($id, $UID)); 
			if (!$db->numberRows() > 0)  {
				echo $LANG->NEED_ADMIN_RIGHTS;
				exit; //no admin user
			}
		}

		//$row = $db->fetchRow("SELECT * FROM `groups` WHERE id=?", array($id));
		
		$result = $db->delete("`groups`", "id=?", array($id));
			
		echo check_delete_result($result);

	  break;
	

	case 'groups_admins_select': // SELECT
		$where = '';
		if (isset($_REQUEST['location_id'])) {
			$where = ' AND location_id = ' . ((int) $_REQUEST['location_id']);
		}
		echo get_groups_admins_select(false, $where);
	  break;


	case 'groups_select': // SELECT
		echo get_groups_select();
	  break;


	case 'view': // SELECT /////////////////////////////////////////////////////////////////
	default: //view
		
		$response = new stdClass();

		$where = '';
		if ($ID != 0) {
			$where = "WHERE location_id = " . $ID;
		}
		$sidx = $sidx ?? '';
		$sord = $sord ?? '';
		$order = '';
		if (trim($sidx) != '') {
			$order = "ORDER BY $sidx $sord";
		}
		$rows = $db->fetch("SELECT * FROM `groups` $where $order", array());
		$i=0;
		if ($db->numberRows() > 0)  {
			foreach ($rows as $row) {
				//don't want groupadmins to see the private groups except if is admins of the group
				if (($GROUP_ADMIN OR $GROUP_ADMIN_2) AND $row['status'] == 3) {
					if (!in_array($UID, explode(',', $row['admins_id']??''))) {
						continue;
					}
				}
				//don't want groupadmins to see inactive groups
				if (($GROUP_ADMIN OR $GROUP_ADMIN_2) AND $row['status'] == 0) {
					continue;
				}
				$response->rows[$i] = $response->rows[$i]['cell'] = array(
					'',
					$row['id'],
					$row['location_id'],
					$row['name'],
					$row['status'],
					$row['private_key'],
					$row['admins_id'],
					get_date_SQL($row['stop_date'].''),
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