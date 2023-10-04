<?php // ajax Forms 2 Categories

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
				case 'form_id': 
				case 'category_id': 
				case 'status': 
					$values[$key] = $val;
				  break;
				case 'sort': 
					if ($val != '') $values[$key] = $val;
				  break;
				case 'stop_date':	
					if ($val != '') $values[$key] = get_date_SQL($val.'');
					//we need to pass NULL as array
					else $values[$key] = array("NULL"); //=NULL
				  break;
			}
		}		
		
		// Check if all fields are filled up
		if ($values['form_id'] == '') {
			echo $LANG->NO_FORM_SELECTED;
			exit;
		}

		//without test for duplicate name --we want duplicate names

		// INSERT
		if ($action == 'add') 
		{
			$values['modified'] = get_date_time_SQL('now');
			$values['modified_by'] = $USERNAME;
			$values['created'] = get_date_time_SQL('now');
			$values['created_by'] = $USERNAME;
			
			$insert_id = $db->insert($values, "forms2categories");
			
			echo check_insert_result($insert_id);
		}
		// UPDATE
		elseif ($action == 'edit') 
		{
			$values['modified'] = get_date_time_SQL('now');
			$values['modified_by'] = $USERNAME;
			
			$result = $db->update($values, "forms2categories", "id=?", array($id));

			echo check_update_result($result);
		}

	  break;
	
	case 'del': // DELETE
		
		$result = $db->delete("forms2categories", "id=?", array($id));
		echo check_delete_result($result);
		
	  break;

	
	case 'categories_forms_all': // SELECT all -json for local 
	case 'view': // SELECT one category forms
	default: //view 
		if ($action == 'categories_forms_all') {
			$response = array();
			$where = "";
		} else {
			$response = new stdClass();

			$where = " WHERE f2c.category_id = ".$ID;
		}
		$rows = $db->fetch("SELECT f2c.id, f2c.form_id, f2c.category_id, f2c.sort, f2c.status, f2c.stop_date, f2c.created, f2c.created_by, f2c.modified, f2c.modified_by, f.name, f.name2, f.status as form_status 
FROM forms2categories f2c 
LEFT JOIN forms f ON f.id = f2c.form_id 
$where 
ORDER BY f2c.sort, f.name", array());
		$i=0;
		if ($db->numberRows() > 0)  {
			foreach ($rows as $row) {
				// if form is inactive put the same to category/form
				$status = ($row['form_status']==1 ? $row['status'] : 0);
				// categories_forms_all --json for local
				if ($action == 'categories_forms_all') {
					$response[$row['category_id']][] = array(
						'acc'			=> '',
						'id'			=> $row['id'],
						'category_id'	=> $row['category_id'],
						'form_id'		=> $row['form_id'],
						'form_select'	=> $row['form_id'],
						'form_name'		=> $row['name'].' ('.$row['name2'].')', //extern (intern)
						'sort'			=> $row['sort'],
						'status'		=> $status,
						'stop_date'		=> get_date_SQL($row['stop_date'].''),
						'created'		=> get_date_time_SQL($row['created'].''),
						'created_by'	=> $row['created_by'],
						'modified'		=> get_date_time_SQL($row['modified'].''),
						'modified_by'	=> $row['modified_by']
					);
				}
				// view --normal load
				else {
					/** @var mixed $response */
					$response->rows[$i] = array(
						'',
						$row['id'],
						$row['category_id'],
						$row['form_id'],
						$row['form_id'],
						$row['name'],
						$row['sort'],
						$status,
						get_date_SQL($row['stop_date'].''),
						get_date_time_SQL($row['created'].''),
						$row['created_by'],
						get_date_time_SQL($row['modified'].''),
						$row['modified_by']
					);
				}
				$i++;
			}
		}
		
		$response = json_encode($response);
		
		if ($action == 'categories_forms_all') {
			if ($response == '[]') //if empty
				echo '{}';
			else 
				echo $response;
		}
		else { //view
			if ($response == '""') //if empty
				echo '{"rows":[]}';
			else 
				echo $response;
		}
			
	  break;
}
?>