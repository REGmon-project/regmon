<?php // ajax Categories

//ajax.php
/** @var string $action */
/** @var int $id */
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
				case 'status': 
				case 'color': 
					$values[$key] = $val;
				  break;
				case 'parent_id': 
				case 'sort': 
					if ($val != '') $values[$key] = $val;
				  break;
			}
		}		
		
		// Check if all fields are filled up
		if (trim($values['name']) == '') {
			echo $LANG->EMPTY_CATEGORY_NAME;
			exit;
		}

		//without check for double names --we want double names
		
		// INSERT 
		if ($action == 'add') 
		{
			$values['modified'] = get_date_time_SQL('now');
			$values['modified_by'] = $USERNAME;
			$values['created'] = get_date_time_SQL('now');
			$values['created_by'] = $USERNAME;
			
			$insert_id = $db->insert($values, "categories");
			
			echo check_insert_result($insert_id);
		}
		// UPDATE 
		elseif ($action == 'edit') 
		{
			$values['modified'] = get_date_time_SQL('now');
			$values['modified_by'] = $USERNAME;
			
			$result = $db->update($values, "categories", "id=?", array($id));

			echo check_update_result($result);
		}
		
	  break;
	
	case 'del': // DELETE 
		
		$result = $db->delete("categories", "id=?", array($id));
		echo check_delete_result($result);
		
	  break;



	case 'get_Categories_Select': // SELECT
	case 'get_Categories_Select_Root':  // SELECT
		
		$options = '<select>';
		if ($action == 'get_Categories_Select_Root') {
			$options .= '<option value="0"></option>'; //root
		}

		$rows = $db->fetch("SELECT id, parent_id, name FROM categories WHERE status = 1 ORDER BY parent_id, sort, name", array()); 
		if ($db->numberRows() > 0)  {
			//echo "<pre>";print_r($rows);echo "</pre>";
			$categories_array = array(
				'categories' => array(),
				'parent_categories' => array()
			);
			foreach ($rows as $row) {
				//categories array with id as key
				$categories_array['categories'][$row['id']] = $row;
				//child categories with parent as key
				$categories_array['parent_categories'][$row['parent_id']][] = $row['id'];
			}
			//echo "<pre>";print_r($categories_array);echo "</pre>";

			function buildCategorySelect(mixed $parent, mixed $categories_array, string $action, int $level):string {
				$html = '';
				$space = '';

				for ($i = 1; $i < $level; $i++) {
					$space .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
				}

				if (isset($categories_array['parent_categories'][$parent])) {
					foreach ((array)$categories_array['parent_categories'][$parent] as $cat_id) {

						$dis = (($action=='get_Categories_Select_Root' AND $level==3) ? ' disabled' : '');
						
						$html .= '<option value="'.$cat_id.'"'.$dis.'>'.
									$space.
									html_chars($categories_array['categories'][$cat_id]['name'] ?? '').
								'</option>';

						if (isset($categories_array['parent_categories'][$cat_id])) { //have childs
							//get childs
							$html .= buildCategorySelect($cat_id, $categories_array, $action, $level + 1);
						}
					}
				}
				return $html;
			}

			$options .= buildCategorySelect(0, $categories_array, $action, 1);
		}
		$options .= '</select>';
		
		echo $options;
		
	  break;


	case 'view': // SELECT 
	default: //view

		$response = new stdClass();
		$rows = $db->fetch("SELECT *, 
(SELECT GROUP_CONCAT(CAST(form_id AS CHAR)) FROM forms2categories WHERE category_id = c.id GROUP BY category_id) AS forms_ids 
FROM categories c 
ORDER BY parent_id, sort, name", array()); 
//WHERE c.status = 1 --need to see all here
		$i=0;
		if ($db->numberRows() > 0)  {
			$categories = [];
			$categories_array = [
				'categories' => [],
				'parent_categories' => []
			];
			
			foreach ($rows as $row) {
				//categories array with id as key
				$categories_array['categories'][$row['id']] = $row;
				//child categories with parent as key
				$categories_array['parent_categories'][$row['parent_id']][] = $row['id'];
			}
			//echo "<pre>";print_r($categories_array);echo "</pre>";

			/**
			 * Recursively builds an array of categories and their children
			 *
			 * @param mixed  $parent           Parent category ID
			 * @param mixed  $categories_array Array of categories and their children
			 * @param int    $level            Current level of the category in the hierarchy
			 * @param mixed  $categories       Variable as a reference, so the changes made to it inside the function will be reflected in the variable outside the function.
			 * @return void
			 */
			function buildCategoryArray(mixed $parent, mixed $categories_array, int $level, mixed &$categories):void {
				if (isset($categories_array['parent_categories'][$parent])) {
					foreach ((array)$categories_array['parent_categories'][$parent] as $cat_id) {
						$categories_array['categories'][$cat_id]['level'] = $level-1;
						$categories_array['categories'][$cat_id]['isLeaf'] = !isset($categories_array['parent_categories'][$cat_id])?true:false;
						$categories[] = $categories_array['categories'][$cat_id];
						if (isset($categories_array['parent_categories'][$cat_id])) { //have childs
							buildCategoryArray($cat_id, $categories_array, $level + 1, $categories); //get childs
						}
					}
				}
			}
			
			buildCategoryArray(0, $categories_array, 1, $categories);
			//echo "<pre>";print_r($categories);echo "</pre>";
			//exit;
			
			
			foreach ($categories as $category) {
				$forms_count = ($category['forms_ids']!='' ? count(explode(',', $category['forms_ids'])) : 0);
				$response->rows[$i] = $response->rows[$i]['cell'] = array(
					'',
					$category['id'],
					$category['parent_id'],
					$category['name'],
					$category['sort'],
					$category['color'],
					$category['status'],
					$forms_count,
					$category['forms_ids'],
					get_date_time_SQL($category['created'].''),
					$category['created_by'],
					get_date_time_SQL($category['modified'].''),
					$category['modified_by'],
					
					$category['level'], //level
					$category['parent_id'], //parent_id 
					$category['isLeaf'], //isLeaf
					true, //($category['id']==1?false:true), //expanded
					true, //loaded
					($category['isLeaf']?"fa-folder-o":"fa-folder-open,fa-folder")
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