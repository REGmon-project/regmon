<?php // ajax Forms

//ajax.php
/** @var string $action */
/** @var int $id */
/** @var int $ID */
/** @var string $where */

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
				case 'name2': 
				case 'tags': 
				case 'status': 
				//case 'details': 
					$values[$key] = $val;
				  break;
			}
		}		
		
		// Check if all fields are filled up
		if (trim($values['name']) == '') 		{ echo $LANG->EMPTY_FORM_NAME; exit;}

		$tags = '';
		if (isset($values['tags'])) {
			$tags = explode(',', $values['tags']);
			foreach ($tags as $tag) {
				if (trim($tag) != '') {
					$rowT = $db->fetchRow("SELECT name FROM tags WHERE name=?", array(trim($tag))); 
					if ($db->numberRows() > 0)  {} //exists
					else { //Insert
						$valuesT = array();
						$valuesT['name'] = trim($tag);
						$valuesT['modified'] = get_date_time_SQL('now');
						$valuesT['modified_by'] = $USERNAME;
						$valuesT['created'] = get_date_time_SQL('now');
						$valuesT['created_by'] = $USERNAME;
						$insert_id = $db->insert($valuesT, "tags");
					}
				}
			}
		}

		//without test for dublicate name --we want dublicate names

		// INSERT 
		if ($action == 'add') 
		{
			$values['modified'] = get_date_time_SQL('now');
			$values['modified_by'] = $USERNAME;
			$values['created'] = get_date_time_SQL('now');
			$values['created_by'] = $USERNAME;
			
			$insert_id = $db->insert($values, "forms");
			
			echo check_insert_result($insert_id);
		}
		// UPDATE 
		elseif ($action == 'edit') 
		{
			$values['modified'] = get_date_time_SQL('now');
			$values['modified_by'] = $USERNAME;
			
			$result = $db->update($values, "forms", "id=?", array($id));

			echo check_update_result($result);
		}

	  break;
	
	case 'del': // DELETE a form
		
		$row = $db->fetchRow("SELECT COUNT(*) AS forms_data_count FROM forms_data WHERE form_id=? AND status = 1", array($id)); 
		if ($row['forms_data_count'] > 0)  {
			echo str_replace('{DATA_NUM}', $row['forms_data_count'], $LANG->WARN_FORM_DELETE).'<br>'.
				'<button class="del_form_data" type="button">'.$LANG->FORMS_DATA_DELETE_DATA.'</button>'.
				'<script>'.
					'jQuery(function($){'.
						'$(".del_form_data").on("click",function(){'.
							'$.ajax({url:"php/ajax.php?i=forms_data&oper=del&ID='.$id.'", success:function(data, result){'.
								'$(".del_form_data").hide().after('.
									'"<b><u>"+(data=="OK_delete"?"'.$LANG->FORMS_DATA_DELETED.'":data)+"</u></b>"'.
								');'.
							'}});'.
						'});'.
					'});'.
				'</script>'; //Cannot delete. Have 5 data collection.<br>Delete data.
		}
		else {
			$result = $db->delete("forms", "id=?", array($id));
			echo check_delete_result($result);
			
			//TODO: maybe we need to give a warning and a button to confirm deletion of forms2categories
			//delete from forms2categories too
			$result = $db->delete("forms2categories", "form_id=?", array($id));
		}		
		
	  break;


	case 'form_duplicate': // INSERT - Duplicate Form 
		
		$row = $db->fetchRow("SELECT * FROM forms f WHERE id = ?", array($ID));
		if ($db->numberRows() > 0)  {
			unset($row['id']);
			$row['name'] .= '_kopie';
			$row['modified'] = get_date_time_SQL('now');
			$row['modified_by'] = $USERNAME;
			$row['created'] = get_date_time_SQL('now');
			$row['created_by'] = $USERNAME;
			
			$insert_id = $db->insert($row, "forms");
			
			echo check_insert_result($insert_id);
		}
		
	  break;


	case 'get_forms_select': // SELECT - Get Forms 
	case 'get_forms_select_empty': // SELECT - Get Forms with empty option  
		
		$options = '<select>';
		if ($action == 'get_forms_select_empty') {
			$options .= '<option></option>';
		}

		$rows = $db->fetch("SELECT id, name, name2 FROM forms WHERE status = 1 ORDER BY name", array()); 
		if ($db->numberRows() > 0)  {
			foreach ($rows as $row) {
				$options .= '' .
					'<option value="' . $row['id'] . '">' .
						//extern (intern)
						html_chars($row['name']) . ' (' . html_chars($row['name2']) . ')' .
					'</option>';
			}
		}
		$options .= '</select>'; 
		
		echo $options;
		
	  break;


	case 'view': // SELECT 
	default:
		if ($where != '') $where = ' WHERE 1 ' .$where;
		$response = new stdClass();
		$rows = $db->fetch("SELECT id, name, name2, status, tags, created, created_by, modified, modified_by, 
(SELECT GROUP_CONCAT(CAST(category_id AS CHAR)) FROM forms2categories WHERE form_id = f.id GROUP BY form_id) AS categories_ids 
FROM forms f $where ORDER BY name", array()); //$sidx $sord
		$i=0;
		if ($db->numberRows() > 0)  {
		
			//$edit_icons = '<span class="fa fa-gear" style="color:#2e6e9e;"></span>'
			//			.'<span class="fa fa-file-text-o" style="margin-left:-5px; color:#2e6e9e;"></span>'
			//			.'<span class="fa fa-external-link" style="margin-left:10px; color:#2e6e9e;"></span>';
			
			foreach ($rows as $row) {
				$response->rows[$i] = $response->rows[$i]['cell'] = array(
					'',
					$row['id'],
					$row['name'],
					$row['name2'],
					$row['tags'],
					$row['categories_ids'], //($row['categories_ids']!=''?$row['categories_ids']:''),
					$row['status'],
					'', //details icon --we do it on grid 
					get_date_time_SQL($row['created'].''),
					$row['created_by'],
					get_date_time_SQL($row['modified'].''),
					$row['modified_by']
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