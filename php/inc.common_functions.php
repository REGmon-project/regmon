<?php // General Functions

if ($SEC_check != $CONFIG['SEC_Page_Secret']) exit;

function get_locations_select(bool $options_grid=false):string {
	global $db;
	$locations_select = '<select>'; 
	$locations_select .= '<option></option>'; 
	$locations_options_grid = ':'; 
	$rows = $db->fetch("SELECT id, name FROM locations WHERE status = 1 ORDER BY name", array()); 
	if ($db->numberRows() > 0)  {
		foreach ($rows as $row) {
			$name = html_chars($row['name']);
			$locations_select .= '<option value="' . $row['id'] . '">' . $name . '</option>';
			$locations_options_grid .= ';' . $row['id'] . ':' . $name;
		}
	}
	$locations_select .= '</select>'; 
	
	if ($options_grid) {
		return $locations_options_grid;
	}
	else {
		return $locations_select;
	}
}

function get_locations_admins_select(bool $options_grid = false):string {
	global $db;
	//Admin Select Options
	$locations_admins_select = '<select>'; 
	$locations_admins_select .= '<option>&nbsp;</option>'; 
	$locations_admins_options_grid = ':'; 
	$rows = $db->fetch("SELECT id, uname FROM users WHERE status = 1 AND level = 50 ORDER BY id", array());
	if ($db->numberRows() > 0) {
		foreach ($rows as $row) {
			$uname = html_chars($row['uname']);
			$locations_admins_select .= '<option value="' . $row['id'] . '">' . $uname . '</option>';
			$locations_admins_options_grid .= ';' . $row['id'] . ':' . $uname;
		}
	}
	$locations_admins_select .= '</select>'; 

	if ($options_grid) {
		return $locations_admins_options_grid;
	}
	else {
		return $locations_admins_select;
	}
}

function get_groups_select(bool $options_grid = false):string {
	global $db;

	$groups_select = '<select>'; 
	$groups_select .= '<option>&nbsp</option>'; 
	$groups_options_grid = ':'; 

	$rows = $db->fetch("SELECT id, name FROM `groups` WHERE status > 0 ORDER BY name", array()); 
	if ($db->numberRows() > 0)  {
		foreach ($rows as $row) {
			$name = html_chars($row['name']);
			$groups_select .= '<option value="' . $row['id'] . '">' . $name . '</option>';
			$groups_options_grid .= ';' . $row['id'] . ':' . $name;
		}
	}
	$groups_select .= '</select>'; 
	
	if ($options_grid) {
		return $groups_options_grid;
	}
	else {
		return $groups_select;
	}
}

function get_groups_admins_select(bool $options_grid = false, string $where = ''):string {
	global $db;
	$groups_admins_select = '<select>'; 
	//$groups_admins_select .= '<option>&nbsp</option>'; //we not need empty option for multiple
	$groups_admins_options_grid = ':'; 

	$rows = $db->fetch("SELECT id, uname FROM users WHERE status = 1 AND (level = 40 OR level = 45) $where ORDER BY id", array()); 
	if ($db->numberRows() > 0)  {
		foreach ($rows as $row) {
			$uname = html_chars($row['uname']);
			$groups_admins_select .= '<option value="' . $row['id'] . '">' . $uname . '</option>';
			$groups_admins_options_grid .= ';' . $row['id'] . ':' . $uname;
		}
	}

	if ($options_grid) {
		return $groups_admins_options_grid;
	}
	else {
		return $groups_admins_select;
	}
}

function get_Sports_Groups(bool $grid_options = false, bool $get_array = false):mixed {
	global $db;
	$sport_groups_array = array();
	$sports_groups_select = '<select>'; 
	$sports_groups_select .= '<option>&nbsp;</option>';
	$sport_groups_grid_options = '0:'; 
	$rows = $db->fetch("SELECT id, name FROM sports WHERE status = 1 AND parent_id = 0 ORDER BY name", array()); 
	if ($db->numberRows() > 0) {
		foreach ($rows as $row) {
			$sport_groups_array[$row['id']] = $row['name'];
			$name = html_chars($row['name']);
			$sports_groups_select .= '<option value="' . $row['id'] . '">' . $name . '</option>';
			$sport_groups_grid_options .= ';' . $row['id'] . ':' . html_chars($row['name']);
		}
	}
	$sports_groups_select .= '</select>';
	if ($grid_options) {
		return $sport_groups_grid_options;
	}
	if ($get_array) {
		return $sport_groups_array;
	}
	return $sports_groups_select;
}

function get_Sports_Select_Options(bool $grid_options = false):string {
	global $db;
	//Sports Select Options
	$sports_select = '<select>'; 
	//$sports_select .= '<option>&nbsp;</option>'; //not on multiple
	$sports_grid_options = ': '; 
	$rows = $db->fetch("SELECT options FROM sports WHERE status = 1 AND parent_id != 0 ORDER BY options", array()); 
	if ($db->numberRows() > 0)  {
		foreach ($rows as $row) {
			$options = html_chars($row['options']);
			$sports_select .= '<option value="' . $options . '">' . $options . '</option>';
			$sports_grid_options .= ';' . $options . ':' . $options;
		}
	}
	$sports_select .= '</select>';
	if ($grid_options) {
		return $sports_grid_options;
	}
	return $sports_select;
}

function get_Sports_Select_Options_By_Group(string $sport_selected = ''):string {
	global $db;
	//Sports Groups
	$sports_groups_array = get_Sports_Groups(false, true); //get array

	//Sports Select Options
	$Sports_select_options_group = '';
	$Sports_select_options = '';
	$rows = $db->fetch("SELECT id, options, parent_id FROM sports WHERE status = 1 AND parent_id != 0 ORDER BY parent_id, name", array()); 
	if ($db->numberRows() > 0)  {
		$Sports_open_group = false;
		$Sports_group = '';
		$Sports_group_tmp = '';
		$sport_arr = explode(',', $sport_selected);
		foreach ($rows as $row) {
		
			$t_selected = '';
			if (in_array($row['options'], $sport_arr)) $t_selected = ' selected'; //mark selected
			
			if (isset($sports_groups_array[$row['parent_id']])) {
				$Sports_group = $sports_groups_array[$row['parent_id']];
			}
			
			//Group
			if ($Sports_group != $Sports_group_tmp) {
				if ($Sports_open_group) {
					$Sports_select_options_group .= '</optgroup>';
				}
				$Sports_select_options_group .= '<optgroup label="'.html_chars($Sports_group).'">';
				$Sports_open_group = true;
			}
			
			//option
			$options = html_chars($row['options']);
			if ($Sports_group == '') { //no group -put at the end
				$Sports_select_options .= '<option value="' . $options . '"' . $t_selected . '>' . $options . '</option>';
			} else {
				$Sports_select_options_group .= '<option value="' . $options . '"' . $t_selected . '>' . $options . '</option>';
			}
			
			$Sports_group_tmp = $Sports_group;
		}
		if ($Sports_open_group) {
			$Sports_select_options_group .= '</optgroup>';
		}
	}
	$Sports_select_options = $Sports_select_options_group . $Sports_select_options;

	return $Sports_select_options;
}

function get_Body_Height_Options(string $body_height_selected = '', bool $grid_options = false):string {
	$body_height_options = '';
	$user_height_grid_options = '0:';
	$unit = ' cm';
	for ($i = 100; $i <= 250; $i++) {
		$t_selected = '';
		if ($i . $unit == $body_height_selected) {
			$t_selected = ' selected'; //mark selected
		}

		$body_height_options .= '<option value="' . $i . $unit . '"' . $t_selected . '>' . $i . $unit . '</option>';
		$user_height_grid_options .= ';' . $i . $unit . ':' . $i . $unit;
	}
	if ($grid_options) {
		return $user_height_grid_options;
	}
	return $body_height_options;
}

function get_Forms_with_Forms_Data_count_array(int $user_id, mixed $group_id = false):mixed {
	global $db;

	$Forms_with_Forms_Data_arr = array();

	$where_group = '';
	if ($group_id) {
		$where_group = "AND group_id = ?";
	}

	$forms_data = $db->fetch("SELECT COUNT(*) AS count, form_id 
FROM forms_data 
WHERE user_id=? $where_group AND form_id > 0 AND status = 1 
GROUP BY form_id", array($user_id, $group_id)); 
	if ($db->numberRows() > 0)  {
		foreach ($forms_data as $form_data) {
			$Forms_with_Forms_Data_arr[$form_data['form_id']] = $form_data['count'];
		}
	}

	return $Forms_with_Forms_Data_arr;
}

?>
