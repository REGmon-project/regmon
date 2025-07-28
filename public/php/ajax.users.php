<?php // ajax Users

//ajax.php
/** @var string $action */
/** @var int $id */
/** @var string $where */

if ($SEC_check != $CONFIG['SEC_Page_Secret']) exit;

switch ($action) {
	//case 'group_add_edit': //we get as $_GET['act']
	case 'add': // INSERT
	case 'edit': // UPDATE
		$values = [];
		$allowed_keys = [
			'account', 'uname', 'passwd', 'lastname', 'firstname', 'sport', 
			'sex', 'body_height', 'email', 'telephone', 'level', 'status', 
			'location_id', 'group_id', 'birth_date'
		];
		$merged = array_merge($_POST, $_GET);
		
		$values = [];
		foreach ($merged as $key => $val) {
			$key = trim((string)$key);
			$val = trim((string)$val);
			
			if (in_array($key, $allowed_keys)) {
				if ($key === 'birth_date' && $val !== '') {
					$values[$key] = get_date_SQL($val);
				} elseif ($key !== 'birth_date') {
					$values[$key] = $val;
				}
			}
		}
		
		$status = isset($values['status']) ? $values['status'] : 1;
		$level = isset($values['level']) ? $values['level'] : 10;
		

		//for group edit/add
		if (isset($_GET['act']) AND $_GET['act'] == 'group_add_edit') {
			if ($action == 'add' AND !isset($values['status'])) {
				$values['status'] = 1;
			}
			if (isset($values['sport']) AND $values['sport'] == '') {
				unset($values['sport']);
			}
		}
		
		
		// Check if all fields are filled up
		if ($values['uname'] == '') 		{
			echo $LANG->WARN_EMPTY_USERNAME;
			exit;
		}
		elseif ($values['passwd'] != trim($_POST['pass_confirm'])) {
			echo $LANG->WARN_CONFIRM_PASSWORD;
			exit;
		}
		elseif (!filter_var($values['email'], FILTER_VALIDATE_EMAIL)) {
			echo $LANG->WARN_INVALID_EMAIL;
			exit;
		}

		//password ############################
		//empty password
		if ($values['passwd'] == '') {
			if ($action == 'add') {
				//we need password
				echo $LANG->WARN_EMPTY_PASSWORD;
				exit;
			}
			elseif ($action == 'edit') {
				//if password is empty not save it
				unset($values['passwd']); //delete passwd from array
			}
		}
		//check password
		else {
			//check pass < 8 chars
			if (strlen($values['passwd'].'') < 8) {
				echo $LANG->WARN_PASSWORD_CHARS;
				exit;
			}
				
			//check password strength
			if (!(preg_match("#[0-9]+#", $values['passwd'].'') AND //one number
				  preg_match("#[a-z]+#", $values['passwd'].'') AND //one a-z
				  preg_match("#[A-Z]+#", $values['passwd'].''))) //one A-Z
			{
				echo $LANG->WARN_WEAK_PASSWORD;
				exit;
			}
		
			//$values['passwd'] = MD5($values['passwd']);
			$values['passwd'] = hash_Password($values['passwd'].'');
		}
		//password ############################


		if ($action == 'add') {
			$values['account'] = 'user'; //$ACCOUNT;
			//$values['level'] = 10;
		}

		//the following checks is additional to $db that returns duplicate too if the following not used
		$where_id = '';
		if ($id != 0) {
			$where_id = 'AND id != '.((int)$id); //if edit = have id
		}

		$row = $db->fetchRow("SELECT * FROM users WHERE uname=? $where_id", array($values['uname']));
		if ($db->numberRows() > 0)  {
			echo $LANG->WARN_USERNAME_EXIST;
		}
		else {

			// INSERT ###############################
			if ($action == 'add') {
				$values['last_ip'] = '';
				$values['modified'] = get_date_time_SQL('now');
				$values['created'] = get_date_time_SQL('now');
				
				$insert_id = $db->insert($values, "users");
				
				$save_res = check_insert_result($insert_id);
				
				//for group edit/add
				if (isset($_REQUEST['act']) AND $_REQUEST['act'] == 'group_add_edit' AND $save_res == 'OK_insert') {
					//###############################
					//group access
					$values2 = array();			
					$values2['user_id'] = $insert_id;
					$values2['group_id'] = $values['group_id'];
					$values2['status'] = '1';
					$values2['created'] = get_date_time_SQL('now');
					$values2['created_by'] = $USER['uname'];
					$values2['modified'] = get_date_time_SQL('now');
					$values2['modified_by'] = $USER['uname'];
					$users2groups = $db->insert($values2, "users2groups");
					//###############################
				}
				
				echo $save_res;
			}

			// UPDATE ###############################
			elseif ($action == 'edit')
			{
				if ($values['uname'] == $USERNAME AND $status != '1')  {
					echo $LANG->WARN_DEACT_YOUR_ACC;
					exit;
				}
				if ($id == $UID AND $values['uname'] != $USERNAME)  {
					echo $LANG->WARN_CHANGE_MAIN_NAME;
					exit;
				}
				
				$values['modified'] = get_date_time_SQL('now');
				
				$result = $db->update($values, "users", "id=?", array($id));
				
				echo check_update_result($result);
			}
		}

	  break;


	case 'del': // DELETE 
		
		$row = $db->fetchRow("SELECT * FROM users WHERE id=?", array($id));
		
		if (($row['account'] == 'admin' AND $row['uname'] == 'admin')) {
			echo 'Warning! Admin Account cannot be deleted.';
			exit;
		}
		if ($row['uname'] == $USERNAME)  {
			echo $LANG->WARN_DELETE_YOUR_ACC;
			exit;
		}

		$result = $db->delete("users", "id=?", array($id));
			
		echo check_delete_result($result);

	  break;


	case 'dash_onlogin': // UPDATE
		$values = array();		
		$id = $_POST['ath_id'];
		foreach ($_POST as $key => $val) {
			$key = trim((string)$key); 
			$val = trim((string)$val); 
			switch($key) {
				case 'ath_id':
				case 'dashboard':
					$values[$key] = $val;
				  break;
			}
		}		
		
		$values['modified'] = get_date_time_SQL('now');
		
		$result = $db->update($values, "users", "id=?", array($id));
		
		//echo check_update_result($result);

	  break;
	  


	case 'trainer': // SELECT 
		
		$response = new stdClass();
		$sidx = $sidx ?? '';
		$sord = $sord ?? '';

		if (!$ADMIN AND !$LOCATION_ADMIN AND !$GROUP_ADMIN AND !$GROUP_ADMIN_2 AND !$TRAINER) { 
			echo '{"rows":[]}'; 
			exit; 
		}
		// or just //if ($ATHLETE) { echo '{"rows":[]}'; exit; }


		$group_id = (int)($_REQUEST['group_id'] ?? 0);
		$trainer_id = $UID ?? 0; //for php int
		

		$where_level = "u.level = 10";
		if ($ADMIN) {
			$where_level = "u.level IN (10,30,40,45,50)";
		}
		elseif ($LOCATION_ADMIN) {
			$where_level = "u.level IN (10,30,40,45)";
		}
		elseif ($GROUP_ADMIN or $GROUP_ADMIN_2) {
			$where_level = "u.level IN (10,30)";
		}


		$wher = "";
		$where = $wher . $where;
		$where = str_replace("u2t.status = 'null'", "u2t.status IS NULL", $where);

		$rows = $db->fetch("SELECT u2t.status AS request_status, u.* 
			FROM users2groups u2g 
			JOIN users u ON (u.id = u2g.user_id AND $where_level AND u.status = 1) 
			LEFT JOIN users2trainers u2t ON (u.id = u2t.user_id AND u2g.group_id = u2t.group_id AND u2t.trainer_id = ?) 
			WHERE u2g.group_id = ? AND u2g.status = 1 $where 
			ORDER BY $sidx $sord ", array($trainer_id, $group_id));
		if ($db->numberRows() > 0) {
			$i = 0;
			foreach ($rows as $row) {
				$response->rows[$i] = $response->rows[$i]['cell'] = array(
					$row['id'],
					$row['firstname'],
					$row['lastname'],
					get_date_SQL($row['birth_date'] . ''),
					$row['sport'],
					$row['sex'],
					$row['body_height'],
					get_date_time_SQL($row['created'] . ''),
					get_date_time_SQL($row['modified'] . ''),
					$row['request_status'] == 1 ? '<img class="checklist" data-id="' . $row['id'] . '" src="img/checklist.png" style="cursor:pointer;" title="' . $LANG->TRAINER_2_ATHLETES_ACCESS . '">' : '',
					$row['request_status']
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


	case 'group': // SELECT 
		
		$response = new stdClass();
		$sidx = $sidx ?? '';
		$sord = $sord ?? '';

		if (!$ADMIN AND !$LOCATION_ADMIN AND !$GROUP_ADMIN AND !$GROUP_ADMIN_2) { 
			echo '{"rows":[]}';
			exit; 
		}
		// or just //if ($ATHLETE OR $TRAINER) { echo '{"rows":[]}'; exit; }


		$group_id = (int)($_REQUEST['group_id'] ?? 0);


		$where_level = "AND u.level = 10";
		if ($ADMIN) {
			$where_level = "AND u.level IN (10,30,40,45,50)";
		}
		elseif ($LOCATION_ADMIN) {
			$where_level = "AND u.level IN (10,30,40,45)";
		}
		elseif ($GROUP_ADMIN or $GROUP_ADMIN_2) {
			$where_level = "AND u.level IN (10,30)";
		}
		
		$wher = '';
		$where = $wher . $where;
				
		//users 2 group
		$rows = $db->fetch("SELECT u2g.status AS request_status, u.* 
			FROM `users2groups` u2g 
			JOIN `users` u ON u.id = u2g.user_id $where_level
			WHERE u2g.group_id = ? $where 
			ORDER BY $sidx $sord ", array($group_id)); 
		if ($db->numberRows() > 0)  {
			$i=0;
			foreach ($rows as $row) {
				$response->rows[$i] = $response->rows[$i]['cell'] = array(
					'',
					$row['id'],
					$row['uname'],
					'', //$row['passwd'],
					'', //repeat_pass,
					$row['firstname'],
					$row['lastname'],
					get_date_SQL($row['birth_date'].''),
					$row['sport'],
					$row['sex'].'',
					$row['body_height'],
					$row['email'],
					$row['telephone'],
					$row['level'],
					get_date_time_SQL($row['lastlogin'].''),
					$row['logincount'],
					get_date_time_SQL($row['created'].''),
					get_date_time_SQL($row['modified'].''),
					$row['request_status']
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

		$wher = "WHERE 1 ";
		if (!$ADMIN) {
			$wher .= " AND uname = '".$USERNAME."' ";
		}

		$where = $wher . $where;
		//$sidx = str_replace('pos', 'pos*1', $sidx);
		//$rows = $db->fetch("SELECT * FROM users $where ORDER BY $sidx $sord LIMIT $start, $limit", array()); 
		$rows = $db->fetch("SELECT * FROM users $where ORDER BY id", array()); 
		$i=0;
		if ($db->numberRows() > 0)  {
			$response = new stdClass();
			foreach ($rows as $row) {
				$response->rows[$i] = $response->rows[$i]['cell'] = array(
					'',
					$row['id'],
					//$row['account'],
					$row['uname'],
					'', //passwd,
					'', //repeat_pass,
					$row['location_id'],
					$row['group_id'],
					$row['firstname'],
					$row['lastname'],
					get_date_SQL($row['birth_date'].''),
					$row['sport'],
					$row['sex'],
					$row['body_height'],
					$row['email'],
					$row['telephone'],
					$row['status'],
					$row['level'],
					get_date_time_SQL($row['lastlogin'].''),
					$row['logincount'],
					$row['last_ip'],
					get_date_time_SQL($row['created'].''),
					get_date_time_SQL($row['modified'].''),
					'<div title="'.$LANG->RESULTS.'"'.
						' style="text-align:center;cursor:pointer;"'.
						' onmouseover="jQuery(this).addClass(\'ui-state-hover\');"'.
						' onmouseout="jQuery(this).removeClass(\'ui-state-hover\');">'.
							'<a href="forms_results.php?athlete_id='.$row['id'].'" target="_blank" style="display:inline-block;">'.
								'<span class="ui-icon ui-icon-image" style="float:left;"></span>'.
								'<span class="ui-icon ui-icon-extlink" style="float:left;"></span>'.
							'</a>'.
						'</div>'
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