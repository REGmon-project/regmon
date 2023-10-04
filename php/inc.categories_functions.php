<?php // Categories Functions

if ($SEC_check != $CONFIG['SEC_Page_Secret']) exit;

function getCategoryForms(int $category_id):void {
	global $forms, $order, $category_forms_ordered;
	foreach ($forms[$category_id] as $row) {
		$category_forms_ordered[$row['form_id']] = array($row['form_id'], $row['name'], $row['data_names'], $order);
		$order++;
	}
}

function buildCategory(int $parent):void {
	global $categories;
	if (isset($categories['parent_categories'][$parent])) {
		foreach ($categories['parent_categories'][$parent] as $category_id) {
			if (!isset($categories['parent_categories'][$category_id])) {
				if (isset($categories['forms'][$category_id])) {
					getCategoryForms($category_id); //get forms
				}
			}
			if (isset($categories['parent_categories'][$category_id])) { //have childs
				if (isset($categories['forms'][$category_id])) {
					getCategoryForms($category_id); //get forms
				}
				buildCategory($category_id); //get childs
			}
		}
	}
}

function get_All_Categories():mixed {
	global $db, $forms, $categories;

	//make an array to hold categories info and parent/child keys 
	$categories = array('categories' => array(), 'parent_categories' => array(), 'forms' => array());
	$rows = $db->fetch("SELECT * FROM categories WHERE status = 1 ORDER BY parent_id, sort, name", array()); 
	if ($db->numberRows() > 0)  {
		foreach ($rows as $row) {
			$categories['categories'][$row['id']] = $row; //categories array with id as key
			$categories['parent_categories'][$row['parent_id']][] = $row['id']; //child categories with parent as key
			if (isset($forms[$row['id']])) {
				$categories['forms'][$row['id']] = $forms[$row['id']]; //forms array with cat_id as key
			}
		}
		//echo "<pre>";print_r($categories);echo "</pre>"; exit;
		buildCategory(0);
	}
	//print_r($categories);
	/*Array (
		[categories] => Array (
			[17] => Array (
				[id] => 17
				[parent_id] => 0
				[sort] => 2
				[name] => Diagnostik
				[status] => 1
				[color] => #f2f2f2
				[created] => 2018-11-16 14:19:25
				[created_by] => admin
				[modified] => 2018-11-28 17:39:11
				[modified_by] => admin
			)
		)
		[parent_categories] => Array (
			[17] => Array (
				[0] => 20
				[1] => 21
				[2] => 22
			)
		)
		[forms] => Array (
			[17] => Array (
				[0] => Array (
					[form_id] => 1
					[category_id] => 17
					[name] => Testformular
					[data_names] => {"1":["Textfeld1","_Text"],"2":["number1","_Number"],"3":["number2","_Number"],"4":["Textareafeld1","_Textarea"],"5":["datum1","_Date"],"6":["uhrzeit1","_Time"],"7":["dauer1","_Period"],"8":["dropdown1","_Dropdown"],"9":["fragebogenformat1","_RadioButtons"]}
				)
			)
		)
	)
	*/
	return $categories;
}

?>