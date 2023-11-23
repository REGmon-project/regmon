<?php // Config functions

function Generate_Secret_Key(int $length = 64, bool $special_chars = true, bool $extra_special_chars = false):string {
	$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
	if ($special_chars) {
		$chars .= '!@#$%^&*()';
	}
	if ($extra_special_chars) {
		$chars .= '-_ []{}<>~`+=,.;:/?|';
	}
	
	$max = strlen($chars) - 1;
	$key = '';
	for ($j = 0; $j < $length; $j++) {
		$key .= substr($chars, random_int(0, $max), 1);
	}

	return $key;
}

function is_Docker():bool {
    return is_file("/.dockerenv");
}

function is_XAMPP():bool {
	if (isset($_SERVER['SERVER_SOFTWARE']) && strpos($_SERVER['SERVER_SOFTWARE'], 'XAMPP') !== false) {
		return true;
	} else {
		return false;
	}
}

function reload_Config_Page():void {
	header('Location: config.php'); //reload page
}
// HTML #####################################

function get_HTML_Radio_Check_Buttons(string $config_key, string $config_value, string $option1, string $option2, string $label, string $sub_label):string {
	return ''.
		'<label for="'.$config_key.'">'.$label.'</label>'.
		'<div id="'.$config_key.'" class="btn-group" data-toggle="buttons" style="width:100%;">'.
			'<label class="btn btn-default'.($config_value == $option1 ? ' active' : '').'" style="width:50%;">'.
				'<input type="radio" name="'.$config_key.'" value="'.$option1.'"'.($config_value == $option1 ? ' checked' : '').'>'.$option1.
			'</label>'.
			'<label class="btn btn-default'.($config_value == $option2 ? ' active' : '').'" style="width:50%;">'.
				'<input type="radio" name="'.$config_key.'" value="'.$option2.'"'.($config_value == $option2 ? ' checked' : '').'>'.$option2.
			'</label>'.
			'<small class="text-muted">'.$sub_label.'</small>'.
		'</div>'.
		'<br>'.
		'<br>';
}


function get_HTML_Radio_Check_Buttons__On_Off(string $config_key, string $config_value, string $option_on, string $option_off, string $label, string $sub_label, bool $disabled = false):string {
	return ''.
		'<label for="'.$config_key.'">'.$label.'</label>'.
		'<div id="'.$config_key.'" class="btn-group" data-toggle="buttons" style="width:100%;">'.
			'<label class="btn'.($config_value == '1' ? ' active btn-success' : ' btn-default').($disabled ? ' disabled' : '').'" style="width:50%;">'.
				'<input type="radio" name="'.$config_key.'" value="1"'.($config_value == '1' ? ' checked' : '').($disabled ? ' disabled' : '').'>'.$option_on.
			'</label>'.
			'<label class="btn'.($config_value == '0' ? ' active btn-danger' : ' btn-default').($disabled ? ' disabled' : '').'" style="width:50%;">'.
				'<input type="radio" name="'.$config_key.'" value="0"'.($config_value == '0' ? ' checked' : '').($disabled ? ' disabled' : '').'>'.$option_off.
			'</label>'.
		'</div>'.
		'<small class="text-muted">'.$sub_label.'</small>'.
		'<br>'.
		'<br>';
}


function get_HTML_Input(string $config_key, string $config_value, string $input_type, string $label, string $sub_label, string $placeholder, bool $disabled = false):string {
	return ''.
		'<label for="'.$config_key.'">'.$label.'</label>'.
		'<div class="btn-group" style="width:100%;">'.
			'<input type="'.$input_type.'" id="'.$config_key.'" name="'.$config_key.'" value="'.$config_value.'" class="form-control required" placeholder="'.$placeholder.'"'.($input_type == 'number' ? ' min="1" max="99"' : '').($disabled ? ' disabled' : '').'>'.
			'<small class="text-muted">'.$sub_label.'</small>'.
			($sub_label != '' ? '<br>' : '').
			'<br>'.
		'</div>';
}


function get_HTML_Textarea(string $config_key, string $config_value, string $input_type, string $label, string $sub_label, string $placeholder, bool $disabled = false):string {
	return ''.
		'<label for="'.$config_key.'">'.$label.'</label>'.
		'<textarea id="'.$config_key.'" name="'.$config_key.'" class="form-control required" rows="2" cols="10" wrap="soft" maxlength="64" style="overflow:hidden; resize:none;" placeholder="'.$placeholder.'"'.($disabled ? ' disabled' : '').'>'.$config_value.'</textarea>'.
		'<small class="text-muted">'.$sub_label.'</small>'.
		($sub_label != '' ? '<br>' : '').
		'<br>';
}


function get_HTML_Select(string $config_key, string $config_value, mixed $options_arr, string $label, string $sub_label, string $placeholder):string {
	$options = '';
	foreach((array)$options_arr as $option) {
		if (is_array($option)) {
			$option_value = $option[0];
			$option_name = $option[1];
		}
		else {
			$option_value = $option;
			$option_name = $option;
		}
		$selected = '';
		if ($config_value == $option_value) {
			$selected = ' selected';
		}
		if ($option_value == '') {
			$options .= '<option value=""' . $selected . '>' . $placeholder . '</option>';
		}
		else {
			$options .= '<option value="' . $option_value . '"' . $selected . '>' . $option_name . '</option>';	
		}
	}

	$html = ''.
		'<label for="'.$config_key.'">'.$label.'</label>'.
		'<select id="'.$config_key.'" name="'.$config_key.'" class="form-control required">'.
			$options.
		'</select>'.
		'<small class="text-muted">'.$sub_label.'</small>'.
		'<br>'.
		'<br>';

	return $html;
}


function get_Database_Fields(bool $disabled = true):string {
	global $DB_CONFIG, $CONFIG;

	$html = '';

	$html .= get_HTML_Input( //key, value, type, label, sub_label, placeholder, disabled
		'DB_Host', 
		$DB_CONFIG['DB_Host'],
		'text', 
		'Database Hostname', 
		'ex. localhost, localhost:3306. Use "db" for Docker database.', 
		'localhost',
		$disabled
	);

	$html .= get_HTML_Input( //key, value, type, label, sub_label, placeholder, disabled
		'DB_Name', 
		$DB_CONFIG['DB_Name'], 
		'text', 
		'Database Name', 
		'', 
		'regmondb',
		$disabled
	);

	$html .= get_HTML_Input( //key, value, type, label, sub_label, placeholder, disabled
		'DB_User', 
		$DB_CONFIG['DB_User'], 
		'text', 
		'Database User', 
		'', 
		'root',
		$disabled
	);

	if ($disabled) { //not in .env config
		$DB_Pass = Decrypt_String($DB_CONFIG['DB_Pass']);
	}
	else {
		$DB_Pass = $DB_CONFIG['DB_Pass'];
	}
	
	$html .= get_HTML_Input( //key, value, type, label, sub_label, placeholder, disabled
		'DB_Pass', 
		$DB_Pass, 
		'password', 
		'Database Password', 
		'', 
		'root',
		$disabled
	);

	if ($disabled) { //not in .env config

		$html .= '<hr style="margin:0 -5px 20px; border-top:7px double #ccc;">';

		$html .= get_HTML_Input( //key, value, type, label, sub_label, placeholder, disabled
			'DB_Debug_File', 
			$CONFIG['DB_Debug_File'], 
			'text', 
			'Debug Database Queries Filename', 
			'', 
			'__log_query.log',
			false
		);

		$html .= get_HTML_Radio_Check_Buttons__On_Off( //key, value, option_on, option_off, label, sub_label, disabled
			'DB_Debug', 
			$CONFIG['DB_Debug'], 
			'ON', 
			'OFF', 
			'Debug Database Queries', 
			'Writes every sql query to the DB_Debug_File in each file directory',
			false
		);
	}

	return $html;
}


function get_DB_Migrations_Files(string $DB_Migrations_Directory):mixed {
	$DB_Migrations_Files_arr = [];

	if (is_dir($DB_Migrations_Directory)) {
		$files = array();
		//get list and sort
		if ($handle = opendir($DB_Migrations_Directory)) {
			while (false !== ($file = readdir($handle))) {
				if ($file == '.' or $file == '..') {
					continue;
				}
				if (filetype($DB_Migrations_Directory . $file) == 'file') {
					//$timestamp = filemtime($DB_Migrations_Directory . $file);
					//not work all files has the same timestamp if you clone the project
					//$files[$timestamp] = $file;
					$files[] = $file;
				}
			}
			//krsort($files); //key reverse sort --latest first
			//echo '<pre>'; print_r($files);
			closedir($handle);
		}

		$allowed_file_types = "(sql)";

		//loop sorted list of files
		foreach ($files as $file) {
			$ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
			if (preg_match("/\." . $allowed_file_types . "$/i", $file)) {
				$size = round(filesize($DB_Migrations_Directory . $file) / 1024, 2);
				//$get_file = file_get_contents($dir . $file);
				$file_timestamp = filemtime($DB_Migrations_Directory . $file);
				$file_date = date("Y-m-d H:i:s", (int)$file_timestamp);

				//only _init files
				if (substr_count($file, '_init')) {
					$option_value = $file;
					$option_name = $file_date . '&nbsp; - &nbsp;' .$file . ' &nbsp; - &nbsp; ' . $size . 'KB';
		
					$DB_Migrations_Files_arr[] = [$option_value, $option_name];
				}
			}
		}
	}

	return $DB_Migrations_Files_arr;
}


function get_CONFIG__REGmon_Folder():string {
	global $_SERVER;

	$REGmon_Folder = str_replace(
		[$_SERVER['DOCUMENT_ROOT'], $_SERVER['REQUEST_URI']], 
		'', 
		$_SERVER['SCRIPT_FILENAME']
	);

	return $REGmon_Folder;
}


function get_CONFIG_Defaults_array(mixed $POST = array()):mixed {
	global $_SERVER;

	$REGmon_Folder = get_CONFIG__REGmon_Folder();
		
	return array(
		'DB_Debug_File' 	=> $POST['DB_Debug_File'] 	?? '__log_query.log',
		'DB_Debug' 			=> $POST['DB_Debug'] 		?? 0,
		'DOMAIN' 			=> $POST['DOMAIN'] 			?? $_SERVER['SERVER_NAME'], //'localhost'
		'REGmon_Folder' 	=> $POST['REGmon_Folder'] 	?? $REGmon_Folder,
		'Production_Mode' 	=> $POST['Production_Mode'] ?? 0,
		'HTTP' 				=> $POST['HTTP'] 			?? 'http://',
		'Force_Redirect_To_HTTPS' 		=> $POST['Force_Redirect_To_HTTPS'] 	?? 0,
		'Use_Multi_Language_Selector' 	=> $POST['Use_Multi_Language_Selector'] ?? 1,
		'Use_VisualCaptcha' 			=> $POST['Use_VisualCaptcha'] 			?? 1,
		'Default_Language' 				=> $POST['Default_Language'] 			?? 'en',
		'LogLimiter' => array(
			'Max_Attempts' 			 => $POST['LogLimiter_Max_Attempts'] 			?? 5,
			'Block_Minutes' 		 => $POST['LogLimiter_Block_Minutes'] 			?? 10
		),
		'EMAIL' => array(
			'Host' 			=> $POST['EMAIL_Host'] 			?? 'mail.domain.com',
			'Port' 			=> $POST['EMAIL_Port'] 			?? '587',
			'Username' 		=> $POST['EMAIL_Username'] 		?? 'email@domain.com',
			'Password' 		=> $POST['EMAIL_Password'] 		?? '',
			'From_Name' 	=> $POST['EMAIL_From_Name'] 	?? 'App Name',
			'From_Email' 	=> $POST['EMAIL_From_Email'] 	?? 'email@domain.com',
			'Reply_Name' 	=> $POST['EMAIL_Reply_Name'] 	?? 'App Name',
			'Reply_Email'	=> $POST['EMAIL_Reply_Email'] 	?? 'email@domain.com',
			'Support' 		=> $POST['EMAIL_Support'] 		?? 'support@domain.com',
		),
		'SEC_Hash_IP' 		 => $POST['SEC_Hash_IP'] 		?? 1,
		'SEC_Page_Secret' 	 => $POST['SEC_Page_Secret'] 	?? Generate_Secret_Key(),
		'SEC_Hash_Secret' 	 => $POST['SEC_Hash_Secret'] 	?? Generate_Secret_Key(),
		'SEC_Encrypt_Secret' => $POST['SEC_Encrypt_Secret'] ?? Generate_Secret_Key()
	);
}


function Save_Configuration(mixed $config_arr, bool $init = false):string {
	global $db, $CONFIG;
	
	if ($init) { //problems --no user
		//not have $CONFIG
	}
	else { //normal config page
	}

	//set back passwords if empty and we have in CONFIG + Reset + Encrypt
	if ($config_arr['EMAIL']['Password'] == '' AND !$init) {
		$config_arr['EMAIL']['Password'] = $CONFIG['EMAIL']['Password'] ?? '';
	}
	elseif ($config_arr['EMAIL']['Password'] == ' ') {
		//give ' ' to reset field
		$config_arr['EMAIL']['Password'] = '';
	}
	elseif ($config_arr['EMAIL']['Password'] != '') {
		//encrypt pass
		$config_arr['EMAIL']['Password'] = Encrypt_String($config_arr['EMAIL']['Password'].'');
	}
	

	//make json string for db
	$config_json = json_encode($config_arr, JSON_UNESCAPED_UNICODE);
	$config_json = str_replace('\/', '/', $config_json.'');
	
	$values = array();
	$values['val'] = $config_json;


	//check if exist
	$db->fetchRow("SELECT val FROM config WHERE name = 'config'", array());
	if ($db->numberRows() > 0) {
		$db->update($values, "config", " name = 'config'", array());
	}
	else {
		$values['name'] = 'config';
		$db->insert($values, "config");
	}

	return 'OK';
}


function addTime(int $Seconds2Add, string $DateTime):string {
	$timestamp_new = strtotime($DateTime) + $Seconds2Add;
	if (strlen($DateTime) == 10) { //'Y-m-d'
		return date('Y-m-d', $timestamp_new);
	}
	elseif (strlen($DateTime) == 16) { //'Y-m-d H:i'
		return date('Y-m-d H:i', $timestamp_new);
	}
	else { //'Y-m-d H:i:s'
		return date('Y-m-d H:i:s', $timestamp_new);
	}
}


function get_Admin_User_Init_SQL(string $password, string $email, string $datetime):string {
	return "".
		// add admin user
		"INSERT INTO users (id, account, uname, passwd, location_id, group_id, lastname, firstname, email, `level`, `status`, dashboard, created, modified) VALUES ".
		"('1', 'admin', 'admin', '".hash_Password($password)."', 1, 1, 'profile', 'admin', '".$email."', 99, 1, 0, '".$datetime."', '".$datetime."');";
}

function get_Extra_Users_Init_SQL(string $password, string $email, string $datetime):string {
	return "".
		// extra users
		"INSERT INTO users (id, account, uname, passwd, location_id, group_id, lastname, firstname, email, `level`, `status`, dashboard, created, modified) VALUES ".
		"('2', 'user', 'LocationAdmin', '".hash_Password($password)."', 1, 1, 'Admin', 'Location', '".$email."', 50, 1, 0, '".$datetime."', '".$datetime."'),".
		"('3', 'user', 'GroupAdmin', '".hash_Password($password)."', 1, 1, 'Admin', 'Group', '".$email."', 45, 1, 0, '".$datetime."', '".$datetime."'),".
		"('4', 'user', 'GroupAdmin2', '".hash_Password($password)."', 1, 1, 'Admin (reduced)', 'Group', '".$email."', 40, 1, 0, '".$datetime."', '".$datetime."'),".
		"('5', 'user', 'Trainer1', '".hash_Password($password)."', 1, 1, 'Trainer', 'Test', '".$email."', 30, 1, 1, '".$datetime."', '".$datetime."'),".
		"('6', 'user', 'Athlete1', '".hash_Password($password)."', 1, 1, 'Athlete1', 'Test', '".$email."', 10, 1, 1, '".$datetime."', '".$datetime."'),".
		"('7', 'user', 'Athlete2', '".hash_Password($password)."', 1, 1, 'Athlete2', 'Test', '".$email."', 10, 1, 1, '".$datetime."', '".$datetime."');";
}

function get_Extra_LocationGroupsSample_Init_SQL(string $datetime):string {
	return "".
		// location sample data
		"INSERT INTO locations (id, `name`, `status`, admin_id, created, modified) VALUES ".
		"(1, 'Location 1', 1, 2, '".$datetime."', '".$datetime."');".
		"\n".
		// groups sample data
		"INSERT INTO groups (id, location_id, `name`, `status`, private_key, admins_id, forms_select, forms_standard, stop_date, created, modified) VALUES ".
		"(1, 1, 'Group 1 (public)', 1, '', '3,4', '3_3,3_4,3_5,1_1,1_2,4_6', '3_3,4_6', NULL, '".$datetime."', '".$datetime."'),".
		"(2, 1, 'Group 2 (private)', 3, 'secretkey', '3', '3_3,3_4,3_5,1_1,1_2,4_6', '3_4,3_5,1_1,4_6', NULL, '".$datetime."', '".$datetime."');";
}

function get_Extra_CategoriesSample_Init_SQL(string $datetime):string {
	return "".
		// categories sample data [german]
		"INSERT INTO categories (id, parent_id, sort, `name`, `status`, color, created, created_by, modified, modified_by) VALUES ".
		"(1, 0, 1, 'forms for testing', 1, '#cccccc', '".$datetime."', 'Auto_Init','".$datetime."', 'Auto_Init'),".
		"(2, 0, 2, 'Diagnostik', 1, '#f2f2f2', '".$datetime."', 'Auto_Init','".$datetime."', 'Auto_Init'),".
		"(3, 2, 1, 'Psychometrie', 1, '#ED7D31', '".$datetime."', 'Auto_Init','".$datetime."', 'Auto_Init'),".
		"(4, 0, 3, 'Training und Wettkampf', 1, '#5B9BD5', '".$datetime."', 'Auto_Init','".$datetime."', 'Auto_Init');";
}

function get_Extra_User2Groups_Init_SQL(string $datetime):string {
	return "".
		// users2groups
		"INSERT INTO users2groups (id, user_id, group_id, forms_select, `status`, created, created_by, modified, modified_by) VALUES ".
		"(1, 1, 1, '3_3,3_4,3_5,1_1,1_2,4_6', 1, '".$datetime."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(2, 2, 1, '3_3,3_4,3_5,1_1,1_2,4_6', 1, '".$datetime."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(3, 3, 1, '3_3,3_4,3_5,1_1,1_2,4_6', 1, '".$datetime."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(4, 4, 1, '3_3,3_4,3_5,1_1,1_2,4_6', 1, '".$datetime."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(5, 5, 1, '3_3,3_4,3_5,1_1,1_2,4_6', 1, '".$datetime."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(6, 6, 1, '3_3,3_4,3_5,1_1,1_2,4_6', 1, '".$datetime."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(7, 7, 1, '3_3,3_4,3_5,1_1,1_2,4_6', 1, '".$datetime."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(8, 1, 2, '3_3,3_4,3_5,1_1,1_2,4_6', 1, '".$datetime."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(9, 2, 2, '3_3,3_4,3_5,1_1,1_2,4_6', 1, '".$datetime."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(10, 3, 2, '3_3,3_4,3_5,1_1,1_2,4_6', 1, '".$datetime."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(11, 4, 2, '3_3,3_4,3_5,1_1,1_2,4_6', 1, '".$datetime."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(12, 5, 2, '3_3,3_4,3_5,1_1,1_2,4_6', 1, '".$datetime."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(13, 6, 2, '3_3,3_4,3_5,1_1,1_2,4_6', 1, '".$datetime."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(14, 7, 2, '3_3,3_4,3_5,1_1,1_2,4_6', 1, '".$datetime."', 'Auto_Init', '".$datetime."', 'Auto_Init');";
}

function get_Extra_Users2Trainers_Init_SQL(string $datetime):string {
	return "".
		// users2trainers
		"INSERT INTO users2trainers (id, user_id, group_id, trainer_id, forms_select_read, forms_select_write, `status`, created, created_by, modified, modified_by) VALUES ".
		"(1, 6, 1, 5, 'Note_n,3_4,3_5', '3_5', 1, '".$datetime."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(2, 6, 2, 5, '3_4', NULL, 1, '".$datetime."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(3, 7, 1, 5, '3_4,3_5', NULL, 1, '".$datetime."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(4, 7, 2, 5, '3_4,3_5', '3_4,3_5', 1, '".$datetime."', 'Auto_Init', '".$datetime."', 'Auto_Init');";
}

function get_Extra_FormsTagsSample_Init_SQL(string $datetime):string {
	return "".
		// forms sample data [german/english]
		"INSERT INTO forms (id, `name`, name2, `status`, tags, data_json, data_names, created, created_by, modified, modified_by) VALUES ".
		"(1, 'empty form (external name)', 'empty form (internal name)', 1, 'test', NULL, NULL, '".$datetime."', 'Auto_Init','".$datetime."', 'Auto_Init'),".
		"(2, 'form with all elements', 'form for testing purposes', 1, 'test', '{\"title\":\"test (test)\",\"timer\":{\"has\":\"0\",\"min\":\"0\",\"period\":\"min\"},\"days\":{\"has\":\"0\",\"arr\":[1,2,3,4,5,6,7]},\"pages\":[{\"no\":1,\"display_times\":\"0\",\"title\":\"page 1\",\"title_center\":true,\"rows\":[{\"no\":1,\"items\":[{\"type\":\"_Space\",\"no\":1,\"width\":\"100\"}]},{\"no\":2,\"items\":[{\"type\":\"_Line\",\"no\":1,\"width\":\"100\"}]},{\"no\":3,\"items\":[{\"type\":\"_Html\",\"no\":1,\"text\":\"<h1>Header 1<br></h1>\",\"width\":\"100\"}]},{\"no\":4,\"items\":[{\"type\":\"_Label\",\"no\":1,\"label\":\"description\",\"align\":\"left\",\"bold\":\"2\",\"width\":\"50\"},{\"type\":\"_Text\",\"no\":2,\"unid\":1,\"name\":\"text1\",\"placeholder\":\"placeholder\",\"required\":\"0\",\"width\":\"50\"}]},{\"no\":5,\"items\":[{\"type\":\"_Textarea\",\"no\":1,\"unid\":2,\"name\":\"text2\",\"placeholder\":\"placeholder\",\"required\":\"0\",\"width\":\"100\"}]},{\"no\":6,\"items\":[{\"type\":\"_Number\",\"no\":1,\"unid\":3,\"name\":\"number1\",\"placeholder\":\"placeholder\",\"required\":\"1\",\"min\":\"0\",\"max\":\"100\",\"decimal\":false,\"width\":\"100\"}]},{\"no\":7,\"items\":[{\"type\":\"_Date\",\"no\":1,\"unid\":4,\"name\":\"date1\",\"placeholder\":\"placeholder\",\"required\":\"1\",\"width\":\"50\"},{\"type\":\"_Time\",\"no\":2,\"unid\":5,\"name\":\"time1\",\"placeholder\":\"placeholder\",\"required\":\"1\",\"width\":\"50\"}]},{\"no\":8,\"items\":[{\"type\":\"_Period\",\"no\":1,\"unid\":6,\"name\":\"duration\",\"placeholder_from\":\"placeholder\",\"placeholder_to\":\"placeholder\",\"placeholder\":\"placeholder\",\"required\":\"1\",\"width\":\"100\"}]},{\"no\":9,\"items\":[{\"type\":\"_Line\",\"no\":1,\"width\":\"100\"}]},{\"no\":10,\"items\":[{\"type\":\"_Label\",\"no\":1,\"label\":\"description\",\"align\":\"center\",\"bold\":\"0\",\"width\":\"50\"},{\"type\":\"_Dropdown\",\"no\":2,\"unid\":7,\"name\":\"list1\",\"opt\":\"placeholder\",\"dd\":\"1\",\"has_color\":\"0\",\"color\":\"120|0\",\"required\":\"1\",\"width\":\"50\"}]},{\"no\":11,\"items\":[{\"type\":\"_RadioButtons\",\"no\":1,\"unid\":8,\"name\":\"list2\",\"has_title\":\"1\",\"title\":\"description\",\"talign\":\"center\",\"rdd\":\"5\",\"has_color\":\"1\",\"color\":\"120|0\",\"required\":\"0\",\"width\":\"100\"}]},{\"no\":12,\"items\":[{\"type\":\"_Accordion\",\"no\":1,\"accType\":false,\"width\":\"100\",\"Panels\":[{\"type\":\"_Accordion_Panel\",\"no\":1,\"acc_no\":1,\"label\":\"accordion panel\",\"align\":\"center\",\"bold\":\"2\",\"open\":false,\"Rows\":[{\"no\":1,\"items\":[{\"type\":\"_Label\",\"no\":1,\"label\":\"description\",\"align\":\"left\",\"bold\":\"0\",\"width\":\"50\"},{\"type\":\"_Number\",\"no\":2,\"unid\":9,\"name\":\"number2\",\"placeholder\":\"placeholder\",\"required\":\"0\",\"min\":\"5\",\"max\":\"10\",\"decimal\":true,\"width\":\"50\"}]}]},{\"type\":\"_Accordion_Panel\",\"no\":1,\"acc_no\":2,\"label\":\"accordion panel 2\",\"align\":\"center\",\"bold\":\"2\",\"open\":false,\"Rows\":[]},{\"type\":\"_Accordion_Panel\",\"no\":1,\"acc_no\":3,\"label\":\"accordion panel 3\",\"align\":\"center\",\"bold\":\"2\",\"open\":false,\"Rows\":[]}]}]}]},{\"no\":2,\"display_times\":\"0\",\"title\":\"page 2\",\"title_center\":true,\"rows\":[{\"no\":1,\"items\":[{\"type\":\"_Html\",\"no\":1,\"text\":\"<h2>Header 2<br></h2>\",\"width\":\"100\"}]},{\"no\":2,\"items\":[{\"type\":\"_RadioButtons\",\"no\":1,\"unid\":10,\"name\":\"list3\",\"has_title\":\"1\",\"title\":\"description\",\"talign\":\"left\",\"rdd\":\"5\",\"has_color\":\"0\",\"color\":\"120|0\",\"required\":\"0\",\"width\":\"100\"}]}]}]}', '{\"1\":[\"text1\",\"_Text\"],\"2\":[\"text2\",\"_Textarea\"],\"3\":[\"number1\",\"_Number\"],\"4\":[\"date1\",\"_Date\"],\"5\":[\"time1\",\"_Time\"],\"6\":[\"duration\",\"_Period\"],\"7\":[\"list1\",\"_Dropdown\"],\"8\":[\"list2\",\"_RadioButtons\"],\"9\":[\"number2\",\"_Number\"],\"10\":[\"list3\",\"_RadioButtons\"]}', '".$datetime."', 'Auto_Init','".$datetime."', 'Auto_Init'),".
		"(3, 'Akutmaß Erholung und Beanspruchung (AEB)','offizielles REGman-Formular (AEB)',1,'Beanspruchung,Erholung,Fragebogen,Psychometrie','{\"title\":\"AEB, aktuelle Version (Akutmaß Erholung und Beanspruchung (AEB))\",\"timer\":{\"has\":\"1\",\"min\":\"15\",\"period\":\"min\"},\"days\":{\"has\":\"0\",\"arr\":[1,2,3,4,5,6,7]},\"pages\":[{\"no\":1,\"display_times\":\"3\",\"title\":\"Akutmaß Erholung und Beanspruchung (AEB)\",\"title_center\":true,\"rows\":[{\"no\":1,\"items\":[{\"type\":\"_Html\",\"no\":1,\"text\":\"Auf der nächsten Seite finden Sie eine Reihe von Aussagen, die sich auf ihr körperliches und seelisches Befinden beziehen. Bitte überlegen Sie bei jeder Aussagen, in welchem Maße diese auf Sie zutrifft.&nbsp;<p>Zur Beurteilung steht ihnen eine siebenfach abgestufte Skala zur Verfügung.&nbsp;</p>\",\"width\":\"100\"}]},{\"no\":2,\"items\":[{\"type\":\"_Space\",\"no\":1,\"width\":\"100\"}]},{\"no\":3,\"items\":[{\"type\":\"_Label\",\"no\":1,\"label\":\"Im Augenblick fühle ich mich...\",\"align\":\"left\",\"bold\":\"0\",\"width\":\"100\"}]},{\"no\":4,\"items\":[{\"type\":\"_RadioButtons\",\"no\":1,\"unid\":1,\"name\":\"AEB_Beispiel\",\"has_title\":\"1\",\"title\":\"1) erholt\",\"talign\":\"left\",\"rdd\":\"9\",\"has_color\":\"0\",\"color\":\"0|0\",\"required\":\"0\",\"width\":\"100\"}]},{\"no\":5,\"items\":[{\"type\":\"_Space\",\"no\":1,\"width\":\"100\"}]},{\"no\":6,\"items\":[{\"type\":\"_Html\",\"no\":1,\"text\":\"Bitte denken Sie nicht zu lange über eine Aussage nach, sondern treffen Sie möglichst spontan ein Wahl.&nbsp;<p>Überlegen Sie bitte nicht, welche Beantwortung auf den ersten Blick einen bestimmten Eindruck vermittelt, sondern stufen Sie die Aussagen so ein, wie es für Sie persönlich am ehesten zutrifft. Es gibt dabei keine richtig oder falschen Antworten.&nbsp;</p>\",\"width\":\"100\"}]}]},{\"no\":2,\"display_times\":\"0\",\"title\":\"Akutmaß Erholung und Beanspruchung (AEB)\",\"title_center\":true,\"rows\":[{\"no\":1,\"items\":[{\"type\":\"_Html\",\"no\":1,\"text\":\"Im Folgenden befindet sich eine Liste von Adjektiven, die den Zustand von Erholung und Beanspruchung beschreiben. Nimm bitte für jedes Adjektiv eine Einschätzung vor und setze ein Kreuz an die Stelle, die für Deinen jetzigen Zustand am ehesten zutrifft.&nbsp;\",\"width\":\"100\"}]},{\"no\":2,\"items\":[{\"type\":\"_Space\",\"no\":1,\"width\":\"100\"}]},{\"no\":3,\"items\":[{\"type\":\"_Label\",\"no\":1,\"label\":\"Im Augenblick fühle ich mich...\",\"align\":\"left\",\"bold\":\"0\",\"width\":\"100\"}]},{\"no\":4,\"items\":[{\"type\":\"_RadioButtons\",\"no\":1,\"unid\":2,\"name\":\"AEB_1\",\"has_title\":\"1\",\"title\":\"1) erholt\",\"talign\":\"left\",\"rdd\":\"9\",\"has_color\":\"0\",\"color\":\"0|0\",\"required\":\"1\",\"width\":\"100\"}]},{\"no\":5,\"items\":[{\"type\":\"_RadioButtons\",\"no\":1,\"unid\":3,\"name\":\"AEB_2\",\"has_title\":\"1\",\"title\":\"2) muskulär überanstrengt\",\"talign\":\"left\",\"rdd\":\"9\",\"has_color\":\"0\",\"color\":\"0|0\",\"required\":\"1\",\"width\":\"100\"}]},{\"no\":6,\"items\":[{\"type\":\"_RadioButtons\",\"no\":1,\"unid\":4,\"name\":\"AEB_3\",\"has_title\":\"1\",\"title\":\"3) zufrieden\",\"talign\":\"left\",\"rdd\":\"9\",\"has_color\":\"0\",\"color\":\"0|0\",\"required\":\"1\",\"width\":\"100\"}]},{\"no\":7,\"items\":[{\"type\":\"_RadioButtons\",\"no\":1,\"unid\":5,\"name\":\"AEB_4\",\"has_title\":\"1\",\"title\":\"4) unmotiviert\",\"talign\":\"left\",\"rdd\":\"9\",\"has_color\":\"0\",\"color\":\"0|0\",\"required\":\"1\",\"width\":\"100\"}]},{\"no\":8,\"items\":[{\"type\":\"_RadioButtons\",\"no\":1,\"unid\":6,\"name\":\"AEB_5\",\"has_title\":\"1\",\"title\":\"5) aufmerksam\",\"talign\":\"left\",\"rdd\":\"9\",\"has_color\":\"0\",\"color\":\"0|0\",\"required\":\"1\",\"width\":\"100\"}]},{\"no\":9,\"items\":[{\"type\":\"_RadioButtons\",\"no\":1,\"unid\":7,\"name\":\"AEB_6\",\"has_title\":\"1\",\"title\":\"6) bedrückt\",\"talign\":\"left\",\"rdd\":\"9\",\"has_color\":\"0\",\"color\":\"0|0\",\"required\":\"1\",\"width\":\"100\"}]},{\"no\":10,\"items\":[{\"type\":\"_RadioButtons\",\"no\":1,\"unid\":8,\"name\":\"AEB_7\",\"has_title\":\"1\",\"title\":\"7) kraftvoll\",\"talign\":\"left\",\"rdd\":\"9\",\"has_color\":\"0\",\"color\":\"0|0\",\"required\":\"1\",\"width\":\"100\"}]},{\"no\":11,\"items\":[{\"type\":\"_RadioButtons\",\"no\":1,\"unid\":9,\"name\":\"AEB_8\",\"has_title\":\"1\",\"title\":\"8) geschafft\",\"talign\":\"left\",\"rdd\":\"9\",\"has_color\":\"0\",\"color\":\"0|0\",\"required\":\"1\",\"width\":\"100\"}]},{\"no\":12,\"items\":[{\"type\":\"_RadioButtons\",\"no\":1,\"unid\":10,\"name\":\"AEB_9\",\"has_title\":\"1\",\"title\":\"9) ausgeruht\",\"talign\":\"left\",\"rdd\":\"9\",\"has_color\":\"0\",\"color\":\"0|0\",\"required\":\"1\",\"width\":\"100\"}]},{\"no\":13,\"items\":[{\"type\":\"_RadioButtons\",\"no\":1,\"unid\":11,\"name\":\"AEB_10\",\"has_title\":\"1\",\"title\":\"10) muskulär ermüdet\",\"talign\":\"left\",\"rdd\":\"9\",\"has_color\":\"0\",\"color\":\"0|0\",\"required\":\"1\",\"width\":\"100\"}]},{\"no\":14,\"items\":[{\"type\":\"_RadioButtons\",\"no\":1,\"unid\":12,\"name\":\"AEB_11\",\"has_title\":\"1\",\"title\":\"11) ausgeglichen\",\"talign\":\"left\",\"rdd\":\"9\",\"has_color\":\"0\",\"color\":\"0|0\",\"required\":\"1\",\"width\":\"100\"}]},{\"no\":15,\"items\":[{\"type\":\"_RadioButtons\",\"no\":1,\"unid\":13,\"name\":\"AEB_12\",\"has_title\":\"1\",\"title\":\"12) antriebslos\",\"talign\":\"left\",\"rdd\":\"9\",\"has_color\":\"0\",\"color\":\"0|0\",\"required\":\"1\",\"width\":\"100\"}]},{\"no\":16,\"items\":[{\"type\":\"_RadioButtons\",\"no\":1,\"unid\":14,\"name\":\"AEB_13\",\"has_title\":\"1\",\"title\":\"13) aufnahmefähig\",\"talign\":\"left\",\"rdd\":\"9\",\"has_color\":\"0\",\"color\":\"0|0\",\"required\":\"1\",\"width\":\"100\"}]},{\"no\":17,\"items\":[{\"type\":\"_RadioButtons\",\"no\":1,\"unid\":15,\"name\":\"AEB_14\",\"has_title\":\"1\",\"title\":\"14) gestresst\",\"talign\":\"left\",\"rdd\":\"9\",\"has_color\":\"0\",\"color\":\"0|0\",\"required\":\"1\",\"width\":\"100\"}]},{\"no\":18,\"items\":[{\"type\":\"_RadioButtons\",\"no\":1,\"unid\":16,\"name\":\"AEB_15\",\"has_title\":\"1\",\"title\":\"15) leistungsfähig\",\"talign\":\"left\",\"rdd\":\"9\",\"has_color\":\"0\",\"color\":\"0|0\",\"required\":\"1\",\"width\":\"100\"}]},{\"no\":19,\"items\":[{\"type\":\"_RadioButtons\",\"no\":1,\"unid\":17,\"name\":\"AEB_16\",\"has_title\":\"1\",\"title\":\"16) entkräftet\",\"talign\":\"left\",\"rdd\":\"9\",\"has_color\":\"0\",\"color\":\"0|0\",\"required\":\"1\",\"width\":\"100\"}]},{\"no\":20,\"items\":[{\"type\":\"_RadioButtons\",\"no\":1,\"unid\":18,\"name\":\"AEB_17\",\"has_title\":\"1\",\"title\":\"17) muskulär locker\",\"talign\":\"left\",\"rdd\":\"9\",\"has_color\":\"0\",\"color\":\"0|0\",\"required\":\"1\",\"width\":\"100\"}]},{\"no\":21,\"items\":[{\"type\":\"_RadioButtons\",\"no\":1,\"unid\":19,\"name\":\"AEB_18\",\"has_title\":\"1\",\"title\":\"18) lustlos\",\"talign\":\"left\",\"rdd\":\"9\",\"has_color\":\"0\",\"color\":\"0|0\",\"required\":\"1\",\"width\":\"100\"}]},{\"no\":22,\"items\":[{\"type\":\"_RadioButtons\",\"no\":1,\"unid\":20,\"name\":\"AEB_19\",\"has_title\":\"1\",\"title\":\"19) gut gelaunt\",\"talign\":\"left\",\"rdd\":\"9\",\"has_color\":\"0\",\"color\":\"0|0\",\"required\":\"1\",\"width\":\"100\"}]},{\"no\":23,\"items\":[{\"type\":\"_RadioButtons\",\"no\":1,\"unid\":21,\"name\":\"AEB_20\",\"has_title\":\"1\",\"title\":\"20) genervt\",\"talign\":\"left\",\"rdd\":\"9\",\"has_color\":\"0\",\"color\":\"0|0\",\"required\":\"1\",\"width\":\"100\"}]},{\"no\":24,\"items\":[{\"type\":\"_RadioButtons\",\"no\":1,\"unid\":22,\"name\":\"AEB_21\",\"has_title\":\"1\",\"title\":\"21) mental hellwach\",\"talign\":\"left\",\"rdd\":\"9\",\"has_color\":\"0\",\"color\":\"0|0\",\"required\":\"1\",\"width\":\"100\"}]},{\"no\":25,\"items\":[{\"type\":\"_RadioButtons\",\"no\":1,\"unid\":23,\"name\":\"AEB_22\",\"has_title\":\"1\",\"title\":\"22) muskulär übersäuert\",\"talign\":\"left\",\"rdd\":\"9\",\"has_color\":\"0\",\"color\":\"0|0\",\"required\":\"1\",\"width\":\"100\"}]},{\"no\":26,\"items\":[{\"type\":\"_RadioButtons\",\"no\":1,\"unid\":24,\"name\":\"AEB_23\",\"has_title\":\"1\",\"title\":\"23) energiegeladen\",\"talign\":\"left\",\"rdd\":\"9\",\"has_color\":\"0\",\"color\":\"0|0\",\"required\":\"1\",\"width\":\"100\"}]},{\"no\":27,\"items\":[{\"type\":\"_RadioButtons\",\"no\":1,\"unid\":25,\"name\":\"AEB_24\",\"has_title\":\"1\",\"title\":\"24) überlastet\",\"talign\":\"left\",\"rdd\":\"9\",\"has_color\":\"0\",\"color\":\"0|0\",\"required\":\"1\",\"width\":\"100\"}]},{\"no\":28,\"items\":[{\"type\":\"_RadioButtons\",\"no\":1,\"unid\":26,\"name\":\"AEB_25\",\"has_title\":\"1\",\"title\":\"25) körperlich entspannt\",\"talign\":\"left\",\"rdd\":\"9\",\"has_color\":\"0\",\"color\":\"0|0\",\"required\":\"1\",\"width\":\"100\"}]},{\"no\":29,\"items\":[{\"type\":\"_RadioButtons\",\"no\":1,\"unid\":27,\"name\":\"AEB_26\",\"has_title\":\"1\",\"title\":\"26) muskulär verhärtet\",\"talign\":\"left\",\"rdd\":\"9\",\"has_color\":\"0\",\"color\":\"0|0\",\"required\":\"1\",\"width\":\"100\"}]},{\"no\":30,\"items\":[{\"type\":\"_RadioButtons\",\"no\":1,\"unid\":28,\"name\":\"AEB_27\",\"has_title\":\"1\",\"title\":\"27) alles im Griff habend\",\"talign\":\"left\",\"rdd\":\"9\",\"has_color\":\"0\",\"color\":\"0|0\",\"required\":\"1\",\"width\":\"100\"}]},{\"no\":31,\"items\":[{\"type\":\"_RadioButtons\",\"no\":1,\"unid\":29,\"name\":\"AEB_28\",\"has_title\":\"1\",\"title\":\"28) energielos\",\"talign\":\"left\",\"rdd\":\"9\",\"has_color\":\"0\",\"color\":\"0|0\",\"required\":\"1\",\"width\":\"100\"}]},{\"no\":32,\"items\":[{\"type\":\"_RadioButtons\",\"no\":1,\"unid\":30,\"name\":\"AEB_29\",\"has_title\":\"1\",\"title\":\"29) konzentriert\",\"talign\":\"left\",\"rdd\":\"9\",\"has_color\":\"0\",\"color\":\"0|0\",\"required\":\"1\",\"width\":\"100\"}]},{\"no\":33,\"items\":[{\"type\":\"_RadioButtons\",\"no\":1,\"unid\":31,\"name\":\"AEB_30\",\"has_title\":\"1\",\"title\":\"30) leicht reizbar\",\"talign\":\"left\",\"rdd\":\"9\",\"has_color\":\"0\",\"color\":\"0|0\",\"required\":\"1\",\"width\":\"100\"}]},{\"no\":34,\"items\":[{\"type\":\"_RadioButtons\",\"no\":1,\"unid\":32,\"name\":\"AEB_31\",\"has_title\":\"1\",\"title\":\"31) voller Power\",\"talign\":\"left\",\"rdd\":\"9\",\"has_color\":\"0\",\"color\":\"0|0\",\"required\":\"1\",\"width\":\"100\"}]},{\"no\":35,\"items\":[{\"type\":\"_RadioButtons\",\"no\":1,\"unid\":33,\"name\":\"AEB_32\",\"has_title\":\"1\",\"title\":\"32) körperlich platt\",\"talign\":\"left\",\"rdd\":\"9\",\"has_color\":\"0\",\"color\":\"0|0\",\"required\":\"1\",\"width\":\"100\"}]}]}]}','{\"1\":[\"AEB_Beispiel\",\"_RadioButtons\"],\"2\":[\"AEB_1\",\"_RadioButtons\"],\"3\":[\"AEB_2\",\"_RadioButtons\"],\"4\":[\"AEB_3\",\"_RadioButtons\"],\"5\":[\"AEB_4\",\"_RadioButtons\"],\"6\":[\"AEB_5\",\"_RadioButtons\"],\"7\":[\"AEB_6\",\"_RadioButtons\"],\"8\":[\"AEB_7\",\"_RadioButtons\"],\"9\":[\"AEB_8\",\"_RadioButtons\"],\"10\":[\"AEB_9\",\"_RadioButtons\"],\"11\":[\"AEB_10\",\"_RadioButtons\"],\"12\":[\"AEB_11\",\"_RadioButtons\"],\"13\":[\"AEB_12\",\"_RadioButtons\"],\"14\":[\"AEB_13\",\"_RadioButtons\"],\"15\":[\"AEB_14\",\"_RadioButtons\"],\"16\":[\"AEB_15\",\"_RadioButtons\"],\"17\":[\"AEB_16\",\"_RadioButtons\"],\"18\":[\"AEB_17\",\"_RadioButtons\"],\"19\":[\"AEB_18\",\"_RadioButtons\"],\"20\":[\"AEB_19\",\"_RadioButtons\"],\"21\":[\"AEB_20\",\"_RadioButtons\"],\"22\":[\"AEB_21\",\"_RadioButtons\"],\"23\":[\"AEB_22\",\"_RadioButtons\"],\"24\":[\"AEB_23\",\"_RadioButtons\"],\"25\":[\"AEB_24\",\"_RadioButtons\"],\"26\":[\"AEB_25\",\"_RadioButtons\"],\"27\":[\"AEB_26\",\"_RadioButtons\"],\"28\":[\"AEB_27\",\"_RadioButtons\"],\"29\":[\"AEB_28\",\"_RadioButtons\"],\"30\":[\"AEB_29\",\"_RadioButtons\"],\"31\":[\"AEB_30\",\"_RadioButtons\"],\"32\":[\"AEB_31\",\"_RadioButtons\"],\"33\":[\"AEB_32\",\"_RadioButtons\"]}','".$datetime."', 'Auto_Init','".$datetime."', 'Auto_Init'),".
		"(4, 'Kurzskala Erholung und Beanspruchung (KEB)', 'offizielles REGman-Formular (KEB)', 1, 'Beanspruchung,Erholung,Fragebogen,Psychometrie', '{\"title\":\"Kurzskala Erholung und Beanspruchung (KEB) (offizielles REGman-Formular (KEB))\",\"timer\":{\"has\":\"1\",\"min\":\"5\",\"period\":\"min\"},\"days\":{\"has\":\"0\",\"arr\":[1,2,3,4,5,6,7]},\"pages\":[{\"no\":1,\"display_times\":\"3\",\"title\":\"Kurzskala Erholung und Beanspruchung (KEB)\",\"title_center\":true,\"rows\":[{\"no\":1,\"items\":[{\"type\":\"_Label\",\"no\":1,\"label\":\"Hinweise zur Bearbeitung des Fragebogens\",\"align\":\"left\",\"bold\":\"0\",\"width\":\"100\"}]},{\"no\":2,\"items\":[{\"type\":\"_Html\",\"no\":1,\"text\":\"<p>Auf der nächsten Seite finden sich eine Reihe von Aussagen, die sich auf Dein körperliches und seelisches Befinden beziehen. Bitte überlege bei jeder Aussage, in welchem Maße diese auf Dich zutrifft. <br></p><p>Zur Beurteilung steht Dir eine siebenfach abgestufte Skala zur Verfügung. <br></p>\",\"width\":\"100\"}]},{\"no\":3,\"items\":[{\"type\":\"_Space\",\"no\":1,\"width\":\"100\"}]},{\"no\":4,\"items\":[{\"type\":\"_Html\",\"no\":1,\"text\":\"<b> Körperliche Leistungsfähigkeit</b><h3><span style=\\\\\\\"font-weight: normal;\\\\\\\"><i style=\\\\\\\"font-size: 12.75px; color: inherit; background-color: transparent;\\\\\\\">z.B.&nbsp;</i><i style=\\\\\\\"font-size: 12.75px; color: inherit; background-color: transparent;\\\\\\\">kraftvoll,&nbsp;</i><i style=\\\\\\\"font-size: 12.75px; color: inherit; background-color: transparent;\\\\\\\">leistungsfähig,&nbsp;</i><i style=\\\\\\\"font-size: 12.75px; color: inherit; background-color: transparent;\\\\\\\">energiegeladen,&nbsp;</i><i style=\\\\\\\"font-size: 12.75px; color: inherit; background-color: transparent;\\\\\\\">voller Power</i></span></h3><h4><span style=\\\\\\\"font-weight: normal;\\\\\\\"><sub><span style=\\\\\\\"vertical-align: super;\\\\\\\"><i></i></span><i style=\\\\\\\"\\\\\\\"><p style=\\\\\\\"\\\\\\\"></p></i></sub><p style=\\\\\\\"vertical-align: super;\\\\\\\"></p></span></h4>\",\"width\":\"100\"}]},{\"no\":5,\"items\":[{\"type\":\"_RadioButtons\",\"no\":1,\"unid\":1,\"name\":\"KEB_Beispiel\",\"has_title\":\"0\",\"title\":\"\",\"talign\":\"left\",\"rdd\":\"9\",\"has_color\":\"0\",\"color\":\"0|0\",\"required\":\"0\",\"width\":\"100\"}]},{\"no\":6,\"items\":[{\"type\":\"_Space\",\"no\":1,\"width\":\"100\"}]},{\"no\":7,\"items\":[{\"type\":\"_Html\",\"no\":1,\"text\":\"<p>Bitte denke nicht zu lange über eine Aussage nach, sondern triff möglichst spontan eine Wahl.</p><p>Überlege bitte nicht, welche Beantwortung möglicherweise auf den ersten Blick einen bestimmten Eindruck vermittelt, sondern stufe die Aussagen so ein, wie es für Dich persönlich am ehesten zutrifft. Es gibt dabei keine richtigen oder falschen Antworten. <br></p>\",\"width\":\"100\"}]}]},{\"no\":2,\"display_times\":\"0\",\"title\":\"Kurzskala Erholung und Beanspruchung (KEB)\",\"title_center\":true,\"rows\":[{\"no\":1,\"items\":[{\"type\":\"_Html\",\"no\":1,\"text\":\"<h2 style=\\\\\\\"text-align: center;\\\\\\\"><span style=\\\\\\\"background-color: transparent;\\\\\\\"></span></h2><hr><h2 style=\\\\\\\"text-align: center;\\\\\\\"><span style=\\\\\\\"background-color: transparent;\\\\\\\"><b>Kurzskala Erholung</b></span></h2><hr><h2 style=\\\\\\\"text-align: center;\\\\\\\"><span style=\\\\\\\"background-color: transparent;\\\\\\\"></span></h2>\",\"width\":\"100\"}]},{\"no\":2,\"items\":[{\"type\":\"_Html\",\"no\":1,\"text\":\"Im Folgenden geht es um verschiedene Facetten deines derzeitigen Erholungszustandes. Die Ausprägung \\\\\\\"trifft voll zu\\\\\\\" symbolisiert dabei den besten von dir jemals erreichten Erholungszustand.&nbsp;\",\"width\":\"100\"}]},{\"no\":3,\"items\":[{\"type\":\"_Space\",\"no\":1,\"width\":\"100\"}]},{\"no\":4,\"items\":[{\"type\":\"_Html\",\"no\":1,\"text\":\"<b> Körperliche Leistungsfähigkeit</b><h3><span style=\\\\\\\"font-weight: normal;\\\\\\\"><i style=\\\\\\\"font-size: 12.75px; color: inherit; background-color: transparent;\\\\\\\">z.B.&nbsp;</i><i style=\\\\\\\"font-size: 12.75px; color: inherit; background-color: transparent;\\\\\\\">kraftvoll,&nbsp;</i><i style=\\\\\\\"font-size: 12.75px; color: inherit; background-color: transparent;\\\\\\\">leistungsfähig,&nbsp;</i><i style=\\\\\\\"font-size: 12.75px; color: inherit; background-color: transparent;\\\\\\\">energiegeladen,&nbsp;</i><i style=\\\\\\\"font-size: 12.75px; color: inherit; background-color: transparent;\\\\\\\">voller Power</i></span></h3><h4><span style=\\\\\\\"font-weight: normal;\\\\\\\"><sub><span style=\\\\\\\"vertical-align: super;\\\\\\\"><i></i></span><i style=\\\\\\\"\\\\\\\"><p style=\\\\\\\"\\\\\\\"></p></i></sub><p style=\\\\\\\"vertical-align: super;\\\\\\\"></p></span></h4>\",\"width\":\"100\"}]},{\"no\":5,\"items\":[{\"type\":\"_RadioButtons\",\"no\":1,\"unid\":2,\"name\":\"Körperliche Leistungsfähigkeit\",\"has_title\":\"0\",\"title\":\"\",\"talign\":\"left\",\"rdd\":\"9\",\"has_color\":\"0\",\"color\":\"0|0\",\"required\":\"1\",\"width\":\"100\"}]},{\"no\":6,\"items\":[{\"type\":\"_Space\",\"no\":1,\"width\":\"100\"}]},{\"no\":7,\"items\":[{\"type\":\"_Html\",\"no\":1,\"text\":\"<b> Mentale Leistungsfähigkeit</b><h3><span style=\\\\\\\"font-weight: normal;\\\\\\\"><i style=\\\\\\\"font-size: 12.75px; color: inherit; background-color: transparent;\\\\\\\">z.B.&nbsp;</i><i style=\\\\\\\"font-size: 12.75px; color: inherit; background-color: transparent;\\\\\\\">aufmerksam,&nbsp;</i><i style=\\\\\\\"font-size: 12.75px; color: inherit; background-color: transparent;\\\\\\\">aufnahmefähig,&nbsp;</i><i style=\\\\\\\"font-size: 12.75px; color: inherit; background-color: transparent;\\\\\\\">konzentriert,&nbsp;</i><i style=\\\\\\\"font-size: 12.75px; color: inherit; background-color: transparent;\\\\\\\">mental hellwach</i></span></h3><h4><span style=\\\\\\\"font-weight: normal;\\\\\\\"><sub><span style=\\\\\\\"vertical-align: super;\\\\\\\"><i></i></span><i style=\\\\\\\"\\\\\\\"><p style=\\\\\\\"\\\\\\\"></p></i></sub><p style=\\\\\\\"vertical-align: super;\\\\\\\"></p></span></h4>\",\"width\":\"100\"}]},{\"no\":8,\"items\":[{\"type\":\"_RadioButtons\",\"no\":1,\"unid\":3,\"name\":\"Mentale Leistungsfähigkeit\",\"has_title\":\"0\",\"title\":\"\",\"talign\":\"left\",\"rdd\":\"9\",\"has_color\":\"0\",\"color\":\"0|0\",\"required\":\"1\",\"width\":\"100\"}]},{\"no\":9,\"items\":[{\"type\":\"_Space\",\"no\":1,\"width\":\"100\"}]},{\"no\":10,\"items\":[{\"type\":\"_Html\",\"no\":1,\"text\":\"<b>Emotionale Ausgeglichenheit</b><h3><span style=\\\\\\\"font-weight: normal;\\\\\\\"><i style=\\\\\\\"font-size: 12.75px; color: inherit; background-color: transparent;\\\\\\\">z.B.&nbsp;</i><i style=\\\\\\\"font-size: 12.75px; color: inherit; background-color: transparent;\\\\\\\">zufrieden,&nbsp;</i><i style=\\\\\\\"font-size: 12.75px; color: inherit; background-color: transparent;\\\\\\\">ausgeglichen,&nbsp;</i><i style=\\\\\\\"font-size: 12.75px; color: inherit; background-color: transparent;\\\\\\\">gut gelaunt,&nbsp;</i><i style=\\\\\\\"font-size: 12.75px; color: inherit; background-color: transparent;\\\\\\\">alles im Griff habend</i></span></h3><h4><span style=\\\\\\\"font-weight: normal;\\\\\\\"><sub><span style=\\\\\\\"vertical-align: super;\\\\\\\"><i></i></span><i style=\\\\\\\"\\\\\\\"><p style=\\\\\\\"\\\\\\\"></p></i></sub><p style=\\\\\\\"vertical-align: super;\\\\\\\"></p></span></h4>\",\"width\":\"100\"}]},{\"no\":11,\"items\":[{\"type\":\"_RadioButtons\",\"no\":1,\"unid\":4,\"name\":\"Emotionale Ausgeglichenheit\",\"has_title\":\"0\",\"title\":\"\",\"talign\":\"left\",\"rdd\":\"9\",\"has_color\":\"0\",\"color\":\"0|0\",\"required\":\"1\",\"width\":\"100\"}]},{\"no\":12,\"items\":[{\"type\":\"_Space\",\"no\":1,\"width\":\"100\"}]},{\"no\":13,\"items\":[{\"type\":\"_Html\",\"no\":1,\"text\":\"<b>Allgemeiner Erholungszustand</b><h3><span style=\\\\\\\"font-weight: normal;\\\\\\\"><i style=\\\\\\\"font-size: 12.75px; color: inherit; background-color: transparent;\\\\\\\">z.B.&nbsp;</i><i style=\\\\\\\"font-size: 12.75px; color: inherit; background-color: transparent;\\\\\\\">erholt,&nbsp;</i><i style=\\\\\\\"font-size: 12.75px; color: inherit; background-color: transparent;\\\\\\\">ausgeruht,&nbsp;</i><i style=\\\\\\\"font-size: 12.75px; color: inherit; background-color: transparent;\\\\\\\">muskulär locker,&nbsp;</i><i style=\\\\\\\"font-size: 12.75px; color: inherit; background-color: transparent;\\\\\\\">körperlich entspannt</i></span></h3><h4><span style=\\\\\\\"font-weight: normal;\\\\\\\"><sub><span style=\\\\\\\"vertical-align: super;\\\\\\\"><i></i></span><i style=\\\\\\\"\\\\\\\"><p style=\\\\\\\"\\\\\\\"></p></i></sub><p style=\\\\\\\"vertical-align: super;\\\\\\\"></p></span></h4>\",\"width\":\"100\"}]},{\"no\":14,\"items\":[{\"type\":\"_RadioButtons\",\"no\":1,\"unid\":5,\"name\":\"Allgemeiner Erholungszustand\",\"has_title\":\"0\",\"title\":\"\",\"talign\":\"left\",\"rdd\":\"9\",\"has_color\":\"0\",\"color\":\"0|0\",\"required\":\"1\",\"width\":\"100\"}]},{\"no\":15,\"items\":[{\"type\":\"_Html\",\"no\":1,\"text\":\"<h2 style=\\\\\\\"text-align: center;\\\\\\\"><span style=\\\\\\\"background-color: transparent;\\\\\\\"></span></h2><hr><h2 style=\\\\\\\"text-align: center;\\\\\\\"><span style=\\\\\\\"background-color: transparent;\\\\\\\"><b>Kurzskala Beanspruchung&nbsp;</b></span></h2><hr><h2 style=\\\\\\\"text-align: center;\\\\\\\"><span style=\\\\\\\"background-color: transparent;\\\\\\\"></span></h2>\",\"width\":\"100\"}]},{\"no\":16,\"items\":[{\"type\":\"_Html\",\"no\":1,\"text\":\"Im Folgenden geht es um verschiedene Facetten deines derzeitigen Beanspruchungszustandes. Die Ausprägung \\\\\\\"trifft voll zu\\\\\\\" symbolisiert dabei den höchsten von dir jemals erreichten Beanspruchungszustand.&nbsp;\",\"width\":\"100\"}]},{\"no\":17,\"items\":[{\"type\":\"_Space\",\"no\":1,\"width\":\"100\"}]},{\"no\":18,\"items\":[{\"type\":\"_Html\",\"no\":1,\"text\":\"<b> Muskuläre Beanspruchung</b><h3><span style=\\\\\\\"font-weight: normal;\\\\\\\"><i style=\\\\\\\"font-size: 12.75px; color: inherit; background-color: transparent;\\\\\\\">z.B.&nbsp;</i><i style=\\\\\\\"font-size: 12.75px; color: inherit; background-color: transparent;\\\\\\\">muskulär überanstrengt,&nbsp;</i><i style=\\\\\\\"font-size: 12.75px; color: inherit; background-color: transparent;\\\\\\\">muskulär ermüdet,&nbsp;</i><i style=\\\\\\\"font-size: 12.75px; color: inherit; background-color: transparent;\\\\\\\">muskulär übersäuert,&nbsp;</i><i style=\\\\\\\"font-size: 12.75px; color: inherit; background-color: transparent;\\\\\\\">muskulär verhärtet</i></span></h3><h4><span style=\\\\\\\"font-weight: normal;\\\\\\\"><sub><span style=\\\\\\\"vertical-align: super;\\\\\\\"><i></i></span><i style=\\\\\\\"\\\\\\\"><p style=\\\\\\\"\\\\\\\"></p></i></sub><p style=\\\\\\\"vertical-align: super;\\\\\\\"></p></span></h4>\",\"width\":\"100\"}]},{\"no\":19,\"items\":[{\"type\":\"_RadioButtons\",\"no\":1,\"unid\":6,\"name\":\"Muskuläre Beanspruchung\",\"has_title\":\"0\",\"title\":\"\",\"talign\":\"left\",\"rdd\":\"9\",\"has_color\":\"0\",\"color\":\"0|0\",\"required\":\"1\",\"width\":\"100\"}]},{\"no\":20,\"items\":[{\"type\":\"_Space\",\"no\":1,\"width\":\"100\"}]},{\"no\":21,\"items\":[{\"type\":\"_Html\",\"no\":1,\"text\":\"<b> Aktivierungsmangel</b><h3><span style=\\\\\\\"font-weight: normal;\\\\\\\"><sub><i style=\\\\\\\"font-size: 12.75px; color: inherit; background-color: transparent;\\\\\\\">z.B.&nbsp;</i></sub><i style=\\\\\\\"font-size: 12.75px; vertical-align: sub; color: inherit; background-color: transparent;\\\\\\\">unmotiviert,&nbsp;</i><i style=\\\\\\\"font-size: 12.75px; vertical-align: sub; color: inherit; background-color: transparent;\\\\\\\">antriebslos,&nbsp;</i><i style=\\\\\\\"font-size: 12.75px; vertical-align: sub; color: inherit; background-color: transparent;\\\\\\\">lustlos,&nbsp;</i><i style=\\\\\\\"font-size: 12.75px; vertical-align: sub; color: inherit; background-color: transparent;\\\\\\\">energielos</i></span></h3><h4><span style=\\\\\\\"font-weight: normal;\\\\\\\"><sub><span style=\\\\\\\"vertical-align: super;\\\\\\\"><i></i></span><i style=\\\\\\\"\\\\\\\"><p style=\\\\\\\"\\\\\\\"></p></i></sub><p style=\\\\\\\"vertical-align: super;\\\\\\\"></p></span></h4>\",\"width\":\"100\"}]},{\"no\":22,\"items\":[{\"type\":\"_RadioButtons\",\"no\":1,\"unid\":7,\"name\":\"Aktivierungsmangel\",\"has_title\":\"0\",\"title\":\"\",\"talign\":\"left\",\"rdd\":\"9\",\"has_color\":\"0\",\"color\":\"0|0\",\"required\":\"1\",\"width\":\"100\"}]},{\"no\":23,\"items\":[{\"type\":\"_Space\",\"no\":1,\"width\":\"100\"}]},{\"no\":24,\"items\":[{\"type\":\"_Html\",\"no\":1,\"text\":\"<b> Emotionale Unausgeglichenheit</b><h3><span style=\\\\\\\"font-weight: normal;\\\\\\\"><sub><i style=\\\\\\\"font-size: 12.75px; color: inherit; background-color: transparent;\\\\\\\">z.B.&nbsp;</i></sub><i style=\\\\\\\"font-size: 12.75px; vertical-align: sub; color: inherit; background-color: transparent;\\\\\\\">bedrückt,&nbsp;</i><i style=\\\\\\\"font-size: 12.75px; vertical-align: sub; color: inherit; background-color: transparent;\\\\\\\">gestresst,&nbsp;</i><i style=\\\\\\\"font-size: 12.75px; vertical-align: sub; color: inherit; background-color: transparent;\\\\\\\">genervt,&nbsp;</i><i style=\\\\\\\"font-size: 12.75px; vertical-align: sub; color: inherit; background-color: transparent;\\\\\\\">leicht reizbar</i></span></h3><h4><span style=\\\\\\\"font-weight: normal;\\\\\\\"><sub><span style=\\\\\\\"vertical-align: super;\\\\\\\"><i></i></span><i style=\\\\\\\"\\\\\\\"><p style=\\\\\\\"\\\\\\\"></p></i></sub><p style=\\\\\\\"vertical-align: super;\\\\\\\"></p></span></h4>\",\"width\":\"100\"}]},{\"no\":25,\"items\":[{\"type\":\"_RadioButtons\",\"no\":1,\"unid\":8,\"name\":\"Emotionale Unausgeglichenheit\",\"has_title\":\"0\",\"title\":\"\",\"talign\":\"left\",\"rdd\":\"9\",\"has_color\":\"0\",\"color\":\"0|0\",\"required\":\"1\",\"width\":\"100\"}]},{\"no\":26,\"items\":[{\"type\":\"_Space\",\"no\":1,\"width\":\"100\"}]},{\"no\":27,\"items\":[{\"type\":\"_Html\",\"no\":1,\"text\":\"<b> Allgemeiner Beanspruchungszustand</b><h3><span style=\\\\\\\"font-weight: normal;\\\\\\\"><sub><i style=\\\\\\\"font-size: 12.75px; color: inherit; background-color: transparent;\\\\\\\">z.B.&nbsp;</i></sub><i style=\\\\\\\"font-size: 12.75px; vertical-align: sub; color: inherit; background-color: transparent;\\\\\\\">geschafft,&nbsp;</i><i style=\\\\\\\"font-size: 12.75px; vertical-align: sub; color: inherit; background-color: transparent;\\\\\\\">entkräftet,&nbsp;</i><i style=\\\\\\\"font-size: 12.75px; vertical-align: sub; color: inherit; background-color: transparent;\\\\\\\">überlastet,&nbsp;</i><i style=\\\\\\\"font-size: 12.75px; vertical-align: sub; color: inherit; background-color: transparent;\\\\\\\">körperlich platt</i></span></h3><h4><span style=\\\\\\\"font-weight: normal;\\\\\\\"><sub><span style=\\\\\\\"vertical-align: super;\\\\\\\"><i></i></span><i style=\\\\\\\"\\\\\\\"><p style=\\\\\\\"\\\\\\\"></p></i></sub><p style=\\\\\\\"vertical-align: super;\\\\\\\"></p></span></h4>\",\"width\":\"100\"}]},{\"no\":28,\"items\":[{\"type\":\"_RadioButtons\",\"no\":1,\"unid\":9,\"name\":\"Allgemeiner Beanspruchungszustand\",\"has_title\":\"0\",\"title\":\"\",\"talign\":\"left\",\"rdd\":\"9\",\"has_color\":\"0\",\"color\":\"0|0\",\"required\":\"1\",\"width\":\"100\"}]}]}]}', '{\"1\":[\"KEB_Beispiel\",\"_RadioButtons\"],\"2\":[\"Körperliche Leistungsfähigkeit\",\"_RadioButtons\"],\"3\":[\"Mentale Leistungsfähigkeit\",\"_RadioButtons\"],\"4\":[\"Emotionale Ausgeglichenheit\",\"_RadioButtons\"],\"5\":[\"Allgemeiner Erholungszustand\",\"_RadioButtons\"],\"6\":[\"Muskuläre Beanspruchung\",\"_RadioButtons\"],\"7\":[\"Aktivierungsmangel\",\"_RadioButtons\"],\"8\":[\"Emotionale Unausgeglichenheit\",\"_RadioButtons\"],\"9\":[\"Allgemeiner Beanspruchungszustand\",\"_RadioButtons\"]}', '".$datetime."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(5, 'Schlafdokumentation (kurz)', 'Schlafdokumentation (minimal)', 1, 'Erholung,Psychometrie,Schlaf,sleep', '{\"title\":\"Schlafdokumentation (kurz) (Schlafdokumentation (minimal))\",\"timer\":{\"has\":\"0\",\"min\":\"0\",\"period\":\"min\"},\"days\":{\"has\":\"0\",\"arr\":[1,2,3,4,5,6,7]},\"pages\":[{\"no\":1,\"display_times\":\"0\",\"title\":\"Dokumentation Deiner Nacht / Deines Schlafs\",\"title_center\":true,\"rows\":[{\"no\":1,\"items\":[{\"type\":\"_Html\",\"no\":1,\"text\":\"<hr><div style=\\\\\\\"text-align: center;\\\\\\\">Wie lange hast Du geschlafen?<br><hr></div>\",\"width\":\"100\"}]},{\"no\":2,\"items\":[{\"type\":\"_Period\",\"no\":1,\"unid\":1,\"name\":\"Schlafdauer\",\"placeholder_from\":\"von\",\"placeholder_to\":\"bis\",\"placeholder\":\"Schlafdauer hier eintragen\",\"required\":\"1\",\"width\":\"100\"}]},{\"no\":3,\"items\":[{\"type\":\"_Html\",\"no\":1,\"text\":\"<hr><div style=\\\\\\\"text-align: center;\\\\\\\">Wie hast Du geschlafen?<br></div><hr>\",\"width\":\"100\"}]},{\"no\":4,\"items\":[{\"type\":\"_RadioButtons\",\"no\":1,\"unid\":2,\"name\":\"Schlafqualität\",\"has_title\":\"0\",\"title\":\"\",\"talign\":\"left\",\"rdd\":\"17\",\"has_color\":\"1\",\"color\":\"0|120\",\"required\":\"1\",\"width\":\"100\"}]},{\"no\":5,\"items\":[{\"type\":\"_Html\",\"no\":1,\"text\":\"<hr><div style=\\\\\\\"text-align: center;\\\\\\\">Möchtest Du noch etwas hinzufügen?<br></div><hr>\",\"width\":\"100\"}]},{\"no\":6,\"items\":[{\"type\":\"_Textarea\",\"no\":1,\"unid\":3,\"name\":\"zusätzlicher Kommentar Schlafdokumentation\",\"placeholder\":\"Hier kannst Du Kommentare oder zusätzliche Informationen eintragen (optional)\",\"required\":\"0\",\"width\":\"100\"}]}]}]}', '{\"1\":[\"Schlafdauer\",\"_Period\"],\"2\":[\"Schlafqualität\",\"_RadioButtons\"],\"3\":[\"zusätzlicher Kommentar Schlafdokumentation\",\"_Textarea\"]}', '".$datetime."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(6, 'Training Load (Session-RPE Methode)', 'offizielles REGman-Formular (Training Load)', 1, 'Beanspruchung,Belastung,Load,Psychometrie,RPE,Training Load', '{\"title\":\"offizielles REGman-Formular für den allgemeinen Training Load (Se (Training Load (Session-RPE Methode))\",\"timer\":{\"has\":\"0\",\"min\":\"0\",\"period\":\"min\"},\"days\":{\"has\":\"0\",\"arr\":[1,2,3,4,5,6,7]},\"pages\":[{\"no\":1,\"display_times\":\"0\",\"title\":\"Dokumentation des Training Loads (Session-RPE Methode)\",\"title_center\":true,\"rows\":[{\"no\":1,\"items\":[{\"type\":\"_Line\",\"no\":1,\"width\":\"100\"}]},{\"no\":2,\"items\":[{\"type\":\"_RadioButtons\",\"no\":1,\"unid\":1,\"name\":\"Art (Training oder Wettkampf)\",\"has_title\":\"0\",\"title\":\"\",\"talign\":\"left\",\"rdd\":\"39\",\"has_color\":\"0\",\"color\":\"120|0\",\"required\":\"1\",\"width\":\"100\"}]},{\"no\":3,\"items\":[{\"type\":\"_Line\",\"no\":1,\"width\":\"100\"}]},{\"no\":4,\"items\":[{\"type\":\"_Number\",\"no\":1,\"unid\":2,\"name\":\"Dauer (Training / Wettkampf)\",\"placeholder\":\"Trainings-/Wettkampfdauer [min]\",\"required\":\"1\",\"min\":\"0\",\"max\":\"800\",\"decimal\":false,\"width\":\"100\"}]},{\"no\":5,\"items\":[{\"type\":\"_Line\",\"no\":1,\"width\":\"100\"}]},{\"no\":6,\"items\":[{\"type\":\"_RadioButtons\",\"no\":1,\"unid\":3,\"name\":\"Rating of Perceived Exertion (RPE)\",\"has_title\":\"1\",\"title\":\"Subjektives Belastungsempfinden\",\"talign\":\"left\",\"rdd\":\"27\",\"has_color\":\"0\",\"color\":\"0|0\",\"required\":\"1\",\"width\":\"100\"}]},{\"no\":7,\"items\":[{\"type\":\"_Line\",\"no\":1,\"width\":\"100\"}]},{\"no\":8,\"items\":[{\"type\":\"_Textarea\",\"no\":1,\"unid\":4,\"name\":\"Kontext / zusätzliche Informationen\",\"placeholder\":\"zusätzliche Informationen und Kommentare (optional)\",\"required\":\"0\",\"width\":\"100\"}]}]}]}', '{\"1\":[\"Art (Training oder Wettkampf)\",\"_RadioButtons\"],\"2\":[\"Dauer (Training / Wettkampf)\",\"_Number\"],\"3\":[\"Rating of Perceived Exertion (RPE)\",\"_RadioButtons\"],\"4\":[\"Kontext / zusätzliche Informationen\",\"_Textarea\"]}', '".$datetime."', 'Auto_Init', '".$datetime."', 'Auto_Init');".
		"\n".
		// tags sample data [german/english]
		"INSERT INTO tags (id, `name`, `status`, created, created_by, modified, modified_by) VALUES ".
		"(1, 'test', 1, '".$datetime."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(2, 'Beanspruchung', 1, '".$datetime."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(3, 'Erholung', 1, '".$datetime."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(4, 'Fragebogen', 1, '".$datetime."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(5, 'Psychometrie', 1, '".$datetime."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(6, 'Schlaf', 1, '".$datetime."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(7, 'sleep', 1, '".$datetime."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(8, 'Belastung', 1, '".$datetime."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(9, 'Load', 1, '".$datetime."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(10, 'RPE', 1, '".$datetime."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(11, 'Training Load', 1, '".$datetime."', 'Auto_Init', '".$datetime."', 'Auto_Init');";
}

function get_Extra_Forms2Categories_Init_SQL(string $datetime):string {
	return "".
		// forms2categories
		"INSERT INTO forms2categories (id, form_id, category_id, sort, `status`, stop_date, created, created_by, modified, modified_by) VALUES ".
		"(1, 1, 1, 1, 1, NULL, '".$datetime."', 'Auto_Init','".$datetime."', 'Auto_Init'),".
		"(2, 2, 1, 2, 1, NULL, '".$datetime."', 'Auto_Init','".$datetime."', 'Auto_Init'),".
		"(3, 3, 3, 1, 1, NULL, '".$datetime."', 'Auto_Init','".$datetime."', 'Auto_Init'),".
		"(4, 4, 3, 2, 1, NULL, '".$datetime."', 'Auto_Init','".$datetime."', 'Auto_Init'),".
		"(5, 5, 3, 3, 1, NULL, '".$datetime."', 'Auto_Init','".$datetime."', 'Auto_Init'),".
		"(6, 6, 4, 1, 1, NULL, '".$datetime."', 'Auto_Init','".$datetime."', 'Auto_Init');";
}

function get_Extra_StandardFormTemplatesSample_Init_SQL(string $datetime):string {
	return "".
		// standard form templates data
		"INSERT INTO users2forms (id, user_id, group_id, category_id, form_id, template_id, created, created_by, modified, modified_by) VALUES ".
		"(1, 6, 1, 4, 6, 6, '".$datetime."', 'Auto_Init','".$datetime."', 'Auto_Init'),".
		"(2, 6, 1, 3, 4, 7, '".$datetime."', 'Auto_Init','".$datetime."', 'Auto_Init'),".
		"(3, 6, 1, 3, 5, 4, '".$datetime."', 'Auto_Init','".$datetime."', 'Auto_Init');";
}

function get_Extra_DashboardSample_Init_SQL(string $datetime):string {
	return "".
		// dashboard sample data [german]
		"INSERT INTO dashboard (id, user_id, group_id, `name`, `type`, options, sort, color, created, created_by, modified, modified_by) VALUES ".
		"(1, 6, 1, 'Kurzskala Erholung und Beanspruchung (KEB)', 'form', '3_4', 1, 'rgb(237, 125, 49)', '".$datetime."', 'Auto_Init','".$datetime."', 'Auto_Init'),".
		"(2, 6, 1, 'Training Load (Session-RPE Methode)', 'form', '4_6', 2, '#5B9BD5', '".$datetime."', 'Auto_Init','".$datetime."', 'Auto_Init'),".
		"(3, 6, 1, 'Meine Formularauswahl', 'link', 1, 3, '#008000', '".$datetime."', 'Auto_Init','".$datetime."', 'Auto_Init'),".
		"(4, 6, 1, 'Training Load (RPE>5)', 'forms_results', '6__2__1__10__week', 4, '#cccccc', '".$datetime."', 'Auto_Init','".$datetime."', 'Auto_Init'),".
		"(5, 6, 1, 'Form Template Test', 'forms_results', '4__3__1__2__month', 5, '#333399', '".$datetime."', 'Auto_Init','".$datetime."', 'Auto_Init'),".
		"(6, 6, 1, 'Result Template Test', 'results', '1__1__2__month', 6, '#aa24ff', '".$datetime."', 'Auto_Init','".$datetime."', 'Auto_Init'),".
		"(7, 6, 1, 'Körperliche Leistungsfähigkeit, Schlafdauer und Training Load (wöchentlich gemittelt/aggregiert)', 'results', '3__1__6__week', 7, '#00dc82', '".$datetime."', 'Auto_Init','".$datetime."', 'Auto_Init');";
}

function get_Extra_AxisSample_Init_SQL(string $datetime):string {
	return "".
		// axis sample data
		"INSERT INTO templates_axis (id, user_id, location_id, group_id, `name`, data_json, created, created_by, modified, modified_by) VALUES ".
		"(1, 1, 1, 1, 'Auto Y-Axis', '{\"axis\":{\"id\":\"axis_\",\"name\":\"\",\"color\":\"\",\"min\":\"\",\"max\":\"\",\"pos\":\"false\",\"grid\":\"0\"}}', '".$datetime."', 'Auto_Init','".$datetime."', 'Auto_Init'),".
		"(2, 1, 1, 1, 'AEB/KEB-Achse (links)', '{\"axis\":{\"id\":\"\",\"name\":\"AEB/KEB (0 [trifft gar nicht zu] bis 6 [trifft voll zu])\",\"color\":\"\",\"min\":\"0\",\"max\":\"6\",\"pos\":\"false\",\"grid\":\"0\"}}', '".$datetime."', 'Auto_Init','".$datetime."', 'Auto_Init'),".
		"(3, 1, 1, 1, 'AEB/KEB-Achse (rechts)', '{\"axis\":{\"id\":\"\",\"name\":\"AEB/KEB (0 [trifft gar nicht zu] bis 6 [trifft voll zu])\",\"color\":\"\",\"min\":\"0\",\"max\":\"6\",\"pos\":\"true\",\"grid\":\"0\"}}', '".$datetime."', 'Auto_Init','".$datetime."', 'Auto_Init'),".
		"(4, 1, 1, 1, 'Schlafdauer (links)', '{\"axis\":{\"id\":\"\",\"name\":\"Schlafdauer [Stunden]\",\"color\":\"\",\"min\":\"0\",\"max\":\"\",\"pos\":\"false\",\"grid\":\"0\"}}', '".$datetime."', 'Auto_Init','".$datetime."', 'Auto_Init'),".
		"(5, 1, 1, 1, 'Schlafdauer (rechts)', '{\"axis\":{\"id\":\"\",\"name\":\"Schlafdauer [Stunden]\",\"color\":\"\",\"min\":\"0\",\"max\":\"\",\"pos\":\"true\",\"grid\":\"0\"}}', '".$datetime."', 'Auto_Init','".$datetime."', 'Auto_Init'),".
		"(6, 1, 1, 1, 'Training Load (links)', '{\"axis\":{\"id\":\"\",\"name\":\"Training Load (Session-RPE) [arbitrary units]\",\"color\":\"\",\"min\":\"0\",\"max\":\"\",\"pos\":\"false\",\"grid\":\"0\"}}', '".$datetime."', 'Auto_Init','".$datetime."', 'Auto_Init'),".
		"(7, 1, 1, 1, 'Training Load (rechts)', '{\"axis\":{\"id\":\"\",\"name\":\"Training Load (Session-RPE) [arbitrary units]\",\"color\":\"\",\"min\":\"0\",\"max\":\"\",\"pos\":\"true\",\"grid\":\"0\"}}', '".$datetime."', 'Auto_Init','".$datetime."', 'Auto_Init'),".
		"(8, 1, 1, 1, 'Dauer in Stunden (links)', '{\"axis\":{\"id\":\"\",\"name\":\"Dauer [Stunden]\",\"color\":\"\",\"min\":\"0\",\"max\":\"\",\"pos\":\"false\",\"grid\":\"0\"}}', '".$datetime."', 'Auto_Init','".$datetime."', 'Auto_Init'),".
		"(9, 1, 1, 1, 'Dauer in Stunden (rechts)', '{\"axis\":{\"id\":\"\",\"name\":\"Dauer [Stunden]\",\"color\":\"\",\"min\":\"0\",\"max\":\"\",\"pos\":\"true\",\"grid\":\"0\"}}', '".$datetime."', 'Auto_Init','".$datetime."', 'Auto_Init'),".
		"(10, 1, 1, 1, 'Subjektives Belastungsempfinden RPE (links)', '{\"axis\":{\"id\":\"\",\"name\":\"Subjektives Belastungsempfinden (RPE) [0 (Ruhe) bis 10 (Maximal)]\",\"color\":\"\",\"min\":\"0\",\"max\":\"\",\"pos\":\"false\",\"grid\":\"0\"}}', '".$datetime."', 'Auto_Init','".$datetime."', 'Auto_Init'),".
		"(11, 1, 1, 1, 'Subjektives Belastungsempfinden RPE (rechts)', '{\"axis\":{\"id\":\"\",\"name\":\"Subjektives Belastungsempfinden (RPE) [0 (Ruhe) bis 10 (Maximal)]\",\"color\":\"\",\"min\":\"0\",\"max\":\"\",\"pos\":\"true\",\"grid\":\"0\"}}', '".$datetime."', 'Auto_Init','".$datetime."', 'Auto_Init'),".
		"(12, 1, 1, 1, 'Dauer in Minuten (links)', '{\"axis\":{\"id\":\"\",\"name\":\"Dauer [Minuten]\",\"color\":\"\",\"min\":\"0\",\"max\":\"\",\"pos\":\"false\",\"grid\":\"0\"}}', '".$datetime."', 'Auto_Init','".$datetime."', 'Auto_Init'),".
		"(13, 1, 1, 1, 'Dauer in Minuten (rechts)', '{\"axis\":{\"id\":\"\",\"name\":\"Dauer [Minuten]\",\"color\":\"\",\"min\":\"0\",\"max\":\"\",\"pos\":\"true\",\"grid\":\"0\"}}', '".$datetime."', 'Auto_Init','".$datetime."', 'Auto_Init');";
}

function get_FormTemplates_Init_SQL(string $datetime):string {
	return "" .
		// form templates sample data
		"INSERT INTO templates_forms (id, user_id, location_id, group_id, form_id, `name`, data_json, created, created_by, modified, modified_by) VALUES ".
		"(1, 6, 1, 1, 6, 'test_form_template_TL', '{\"data\":{\"_2\":{\"name\":\"Dauer (Training / Wettkampf)\",\"sel_val\":\"6|2\",\"base_form_id\":\"6\",\"form_id\":\"6\",\"form_name\":\"Training Load (Session-RPE Methode)\",\"field_name\":\"Dauer (Training / Wettkampf)\",\"field_type\":\"_Number\",\"field_num\":\"2\",\"cell_id\":\"RC\",\"data_or_calc\":\"data\",\"show\":\"0\",\"type\":\"line\",\"line\":\"Solid\",\"p_range\":\"0\",\"color\":\"\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_1\"},\"_3\":{\"name\":\"Rating of Perceived Exertion (RPE)\",\"sel_val\":\"6|3\",\"base_form_id\":\"6\",\"form_id\":\"6\",\"form_name\":\"Training Load (Session-RPE Methode)\",\"field_name\":\"Rating of Perceived Exertion (RPE)\",\"field_type\":\"_RadioButtons\",\"field_num\":\"3\",\"cell_id\":\"RE\",\"data_or_calc\":\"data\",\"show\":\"0\",\"type\":\"line\",\"line\":\"Solid\",\"p_range\":\"0\",\"color\":\"\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_1\"},\"_B1\":{\"name\":\"TL (RPE kleiner gleich 5)\",\"sel_val\":\"6|1\",\"base_form_id\":\"6\",\"form_id\":\"6\",\"form_name\":\"Training Load (Session-RPE Methode)\",\"field_name\":\"Formula1\",\"field_type\":\"_Number\",\"field_num\":\"B1\",\"cell_id\":\"BA\",\"data_or_calc\":\"calc\",\"show\":\"1\",\"type\":\"column\",\"line\":\"Solid\",\"p_range\":\"0\",\"color\":\"\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_1\",\"formula_cells\":\"1\",\"formula_period\":\"lines\",\"formula_after\":\"0\",\"formula_X_axis_show\":\"0\",\"formula_Full_Period\":\"0\",\"formula_input\":\"if({RE}<6,{RC}*{RE},\\\\\\\"\\\\\\\")\"}},\"athlete_name_show\":\"0\",\"form_name_show\":\"0\"}', '".$datetime."', 'Auto_Init','".$datetime."', 'Auto_Init'),".
		"(2, 6, 1, 1, 6, 'test_form_template2_TL', '{\"data\":{\"_2\":{\"name\":\"Dauer (Training / Wettkampf)\",\"sel_val\":\"6|2\",\"base_form_id\":\"6\",\"form_id\":\"6\",\"form_name\":\"Training Load (Session-RPE Methode)\",\"field_name\":\"Dauer (Training / Wettkampf)\",\"field_type\":\"_Number\",\"field_num\":\"2\",\"cell_id\":\"RC\",\"data_or_calc\":\"data\",\"show\":\"0\",\"type\":\"line\",\"line\":\"Solid\",\"p_range\":\"0\",\"color\":\"\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_1\"},\"_3\":{\"name\":\"Rating of Perceived Exertion (RPE)\",\"sel_val\":\"6|3\",\"base_form_id\":\"6\",\"form_id\":\"6\",\"form_name\":\"Training Load (Session-RPE Methode)\",\"field_name\":\"Rating of Perceived Exertion (RPE)\",\"field_type\":\"_RadioButtons\",\"field_num\":\"3\",\"cell_id\":\"RE\",\"data_or_calc\":\"data\",\"show\":\"0\",\"type\":\"line\",\"line\":\"Solid\",\"p_range\":\"0\",\"color\":\"\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_1\"},\"_B1\":{\"name\":\"TL (RPE größer als 5)\",\"sel_val\":\"6|1\",\"base_form_id\":\"6\",\"form_id\":\"6\",\"form_name\":\"Training Load (Session-RPE Methode)\",\"field_name\":\"Formula1\",\"field_type\":\"_Number\",\"field_num\":\"B1\",\"cell_id\":\"BA\",\"data_or_calc\":\"calc\",\"show\":\"1\",\"type\":\"column\",\"line\":\"Solid\",\"p_range\":\"0\",\"color\":\"\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_1\",\"formula_cells\":\"1\",\"formula_period\":\"lines\",\"formula_after\":\"0\",\"formula_X_axis_show\":\"0\",\"formula_Full_Period\":\"0\",\"formula_input\":\"if({RE}>5,{RC}*{RE},\\\\\\\"\\\\\\\")\"}},\"athlete_name_show\":\"0\",\"form_name_show\":\"0\"}', '".$datetime."', 'Auto_Init','".$datetime."', 'Auto_Init'),".
		"(3, 6, 1, 1, 4, 'test_form_template_KEB', '{\"data\":{\"_2\":{\"name\":\"Körperliche Leistungsfähigkeit\",\"sel_val\":\"4|2\",\"base_form_id\":\"4\",\"form_id\":\"4\",\"form_name\":\"Kurzskala Erholung und Beanspruchung (KEB)\",\"field_name\":\"Körperliche Leistungsfähigkeit\",\"field_type\":\"_RadioButtons\",\"field_num\":\"2\",\"cell_id\":\"RD\",\"data_or_calc\":\"data\",\"show\":\"1\",\"type\":\"line\",\"line\":\"Solid\",\"p_range\":\"0\",\"color\":\"\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_2\"},\"_B1\":{\"name\":\"Calculation1\",\"sel_val\":\"4|1\",\"base_form_id\":\"4\",\"form_id\":\"4\",\"form_name\":\"Kurzskala Erholung und Beanspruchung (KEB)\",\"field_name\":\"Formula1\",\"field_type\":\"_Number\",\"field_num\":\"B1\",\"cell_id\":\"BA\",\"data_or_calc\":\"calc\",\"show\":\"1\",\"type\":\"scatter\",\"line\":\"Solid\",\"p_range\":\"0\",\"color\":\"\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_1\",\"formula_cells\":\"1\",\"formula_period\":\"lines\",\"formula_after\":\"0\",\"formula_X_axis_show\":\"0\",\"formula_Full_Period\":\"0\",\"formula_input\":\"2*{RD}\"},\"_B2\":{\"name\":\"Calculation2\",\"sel_val\":\"4|2\",\"base_form_id\":\"4\",\"form_id\":\"4\",\"form_name\":\"Kurzskala Erholung und Beanspruchung (KEB)\",\"field_name\":\"Formula2\",\"field_type\":\"_Number\",\"field_num\":\"B2\",\"cell_id\":\"BB\",\"data_or_calc\":\"calc\",\"show\":\"1\",\"type\":\"scatter\",\"line\":\"Solid\",\"p_range\":\"0\",\"color\":\"\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_1\",\"formula_cells\":\"1\",\"formula_period\":\"lines\",\"formula_after\":\"0\",\"formula_X_axis_show\":\"0\",\"formula_Full_Period\":\"0\",\"formula_input\":\"10*{BA}\"}},\"athlete_name_show\":\"0\",\"form_name_show\":\"0\"}', '".$datetime."', 'Auto_Init','".$datetime."', 'Auto_Init'),".
		"(4, 6, 1, 1, 5, 'Schlafdauer und -qualität', '{\"data\":{\"_1\":{\"name\":\"Schlafdauer\",\"sel_val\":\"5|1\",\"base_form_id\":\"5\",\"form_id\":\"5\",\"form_name\":\"Schlafdokumentation (kurz)\",\"field_name\":\"Schlafdauer\",\"field_type\":\"_Period\",\"field_num\":\"1\",\"cell_id\":\"RC\",\"data_or_calc\":\"data\",\"show\":\"0\",\"type\":\"line\",\"line\":\"Solid\",\"p_range\":\"0\",\"color\":\"\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_1\"},\"_2\":{\"name\":\"Schlafqualität\",\"sel_val\":\"5|2\",\"base_form_id\":\"5\",\"form_id\":\"5\",\"form_name\":\"Schlafdokumentation (kurz)\",\"field_name\":\"Schlafqualität\",\"field_type\":\"_RadioButtons\",\"field_num\":\"2\",\"cell_id\":\"RE\",\"data_or_calc\":\"data\",\"show\":\"0\",\"type\":\"line\",\"line\":\"Solid\",\"p_range\":\"0\",\"color\":\"\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_1\"},\"_B1\":{\"name\":\"gute / sehr gute Schlafqualität\",\"sel_val\":\"5|1\",\"base_form_id\":\"5\",\"form_id\":\"5\",\"form_name\":\"Schlafdokumentation (kurz)\",\"field_name\":\"Formula1\",\"field_type\":\"_Number\",\"field_num\":\"B1\",\"cell_id\":\"BA\",\"data_or_calc\":\"calc\",\"show\":\"1\",\"type\":\"scatter\",\"line\":\"Solid\",\"p_range\":\"0\",\"color\":\"#00ad27\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_4\",\"formula_cells\":\"1\",\"formula_period\":\"lines\",\"formula_after\":\"0\",\"formula_X_axis_show\":\"0\",\"formula_Full_Period\":\"0\",\"formula_input\":\"if({RE}>6,{RC}/60)\"},\"_B2\":{\"name\":\"mittelmäßige Schlafqualität\",\"sel_val\":\"5|2\",\"base_form_id\":\"5\",\"form_id\":\"5\",\"form_name\":\"Schlafdokumentation (kurz)\",\"field_name\":\"Formula2\",\"field_type\":\"_Number\",\"field_num\":\"B2\",\"cell_id\":\"BB\",\"data_or_calc\":\"calc\",\"show\":\"1\",\"type\":\"scatter\",\"line\":\"Solid\",\"p_range\":\"0\",\"color\":\"#fff400\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_4\",\"formula_cells\":\"1\",\"formula_period\":\"lines\",\"formula_after\":\"0\",\"formula_X_axis_show\":\"0\",\"formula_Full_Period\":\"0\",\"formula_input\":\"if(AND({RE}>3,{RE}<7),{RC}/60)\"},\"_B3\":{\"name\":\"schlechte / sehr schlechte Schlafqualität\",\"sel_val\":\"5|3\",\"base_form_id\":\"5\",\"form_id\":\"5\",\"form_name\":\"Schlafdokumentation (kurz)\",\"field_name\":\"Formula3\",\"field_type\":\"_Number\",\"field_num\":\"B3\",\"cell_id\":\"BC\",\"data_or_calc\":\"calc\",\"show\":\"1\",\"type\":\"scatter\",\"line\":\"Solid\",\"p_range\":\"0\",\"color\":\"#ff0000\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_4\",\"formula_cells\":\"1\",\"formula_period\":\"lines\",\"formula_after\":\"0\",\"formula_X_axis_show\":\"0\",\"formula_Full_Period\":\"0\",\"formula_input\":\"if({RE}<4,{RC}/60)\"},\"_B4\":{\"name\":\"Schlafdauer\",\"sel_val\":\"5|4\",\"base_form_id\":\"5\",\"form_id\":\"5\",\"form_name\":\"Schlafdokumentation (kurz)\",\"field_name\":\"Formula4\",\"field_type\":\"_Number\",\"field_num\":\"B4\",\"cell_id\":\"BD\",\"data_or_calc\":\"calc\",\"show\":\"1\",\"type\":\"spline\",\"line\":\"Solid\",\"p_range\":\"0\",\"color\":\"rgba(0,0,0,0.15)\",\"markers\":\"false\",\"labels\":\"false\",\"axis\":\"axis_4\",\"formula_cells\":\"1\",\"formula_period\":\"lines\",\"formula_after\":\"0\",\"formula_X_axis_show\":\"0\",\"formula_Full_Period\":\"0\",\"formula_input\":\"{RC}/60\"}},\"athlete_name_show\":\"0\",\"form_name_show\":\"0\"}', '".$datetime."', 'Auto_Init','".$datetime."', 'Auto_Init'),".
		"(5, 6, 1, 1, 6, 'Training Load (Trainingsdauer*RPE)', '{\"data\":{\"_2\":{\"name\":\"Dauer (Training / Wettkampf)\",\"sel_val\":\"6|2\",\"base_form_id\":\"6\",\"form_id\":\"6\",\"form_name\":\"Training Load (Session-RPE Methode)\",\"field_name\":\"Dauer (Training / Wettkampf)\",\"field_type\":\"_Number\",\"field_num\":\"2\",\"cell_id\":\"RC\",\"data_or_calc\":\"data\",\"show\":\"0\",\"type\":\"line\",\"line\":\"Solid\",\"p_range\":\"0\",\"color\":\"\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_1\"},\"_3\":{\"name\":\"Rating of Perceived Exertion (RPE)\",\"sel_val\":\"6|3\",\"base_form_id\":\"6\",\"form_id\":\"6\",\"form_name\":\"Training Load (Session-RPE Methode)\",\"field_name\":\"Rating of Perceived Exertion (RPE)\",\"field_type\":\"_RadioButtons\",\"field_num\":\"3\",\"cell_id\":\"RE\",\"data_or_calc\":\"data\",\"show\":\"0\",\"type\":\"line\",\"line\":\"Solid\",\"p_range\":\"0\",\"color\":\"\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_1\"},\"_B1\":{\"name\":\"Training Load (Session-RPE Methode)\",\"sel_val\":\"6|1\",\"base_form_id\":\"6\",\"form_id\":\"6\",\"form_name\":\"Training Load (Session-RPE Methode)\",\"field_name\":\"Formula1\",\"field_type\":\"_Number\",\"field_num\":\"B1\",\"cell_id\":\"BA\",\"data_or_calc\":\"calc\",\"show\":\"1\",\"type\":\"column\",\"line\":\"Solid\",\"p_range\":\"0\",\"color\":\"rgb(124,181,236)\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_6\",\"formula_cells\":\"1\",\"formula_period\":\"lines\",\"formula_after\":\"0\",\"formula_X_axis_show\":\"0\",\"formula_Full_Period\":\"0\",\"formula_input\":\"{RC}*{RE}\"}},\"athlete_name_show\":\"0\",\"form_name_show\":\"0\"}', '".$datetime."', 'Auto_Init','".$datetime."', 'Auto_Init'),".
		"(6, 6, 1, 1, 6, 'Trainingsdauer (Umfang), Subjektives Belastungsempfinden (RPE) und Training Load (Trainingsdauer*RPE)', '{\"data\":{\"_1_S\":{\"name\":\"Art (Training oder Wettkampf)_S\",\"sel_val\":\"6|1_S\",\"base_form_id\":\"6\",\"form_id\":\"6\",\"form_name\":\"Training Load (Session-RPE Methode)\",\"field_name\":\"Art (Training oder Wettkampf)_S\",\"field_type\":\"_Text\",\"field_num\":\"1_S\",\"cell_id\":\"RA\",\"data_or_calc\":\"data\",\"show\":\"1\",\"type\":\"line\",\"line\":\"Solid\",\"p_range\":\"0\",\"color\":\"\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_1\"},\"_1\":{\"name\":\"Art (Training oder Wettkampf)\",\"sel_val\":\"6|1\",\"base_form_id\":\"6\",\"form_id\":\"6\",\"form_name\":\"Training Load (Session-RPE Methode)\",\"field_name\":\"Art (Training oder Wettkampf)\",\"field_type\":\"_RadioButtons\",\"field_num\":\"1\",\"cell_id\":\"RB\",\"data_or_calc\":\"data\",\"show\":\"0\",\"type\":\"line\",\"line\":\"Solid\",\"p_range\":\"0\",\"color\":\"\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_1\"},\"_2\":{\"name\":\"Dauer (Training / Wettkampf)\",\"sel_val\":\"6|2\",\"base_form_id\":\"6\",\"form_id\":\"6\",\"form_name\":\"Training Load (Session-RPE Methode)\",\"field_name\":\"Dauer (Training / Wettkampf)\",\"field_type\":\"_Number\",\"field_num\":\"2\",\"cell_id\":\"RC\",\"data_or_calc\":\"data\",\"show\":\"0\",\"type\":\"line\",\"line\":\"Solid\",\"p_range\":\"0\",\"color\":\"\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_1\"},\"_3_S\":{\"name\":\"Rating of Perceived Exertion (RPE)_S\",\"sel_val\":\"6|3_S\",\"base_form_id\":\"6\",\"form_id\":\"6\",\"form_name\":\"Training Load (Session-RPE Methode)\",\"field_name\":\"Rating of Perceived Exertion (RPE)_S\",\"field_type\":\"_Text\",\"field_num\":\"3_S\",\"cell_id\":\"RD\",\"data_or_calc\":\"data\",\"show\":\"1\",\"type\":\"line\",\"line\":\"Solid\",\"p_range\":\"0\",\"color\":\"\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_1\"},\"_3\":{\"name\":\"Rating of Perceived Exertion (RPE)\",\"sel_val\":\"6|3\",\"base_form_id\":\"6\",\"form_id\":\"6\",\"form_name\":\"Training Load (Session-RPE Methode)\",\"field_name\":\"Rating of Perceived Exertion (RPE)\",\"field_type\":\"_RadioButtons\",\"field_num\":\"3\",\"cell_id\":\"RE\",\"data_or_calc\":\"data\",\"show\":\"0\",\"type\":\"line\",\"line\":\"Solid\",\"p_range\":\"0\",\"color\":\"\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_1\"},\"_4\":{\"name\":\"Kontext / zusätzliche Informationen\",\"sel_val\":\"6|4\",\"base_form_id\":\"6\",\"form_id\":\"6\",\"form_name\":\"Training Load (Session-RPE Methode)\",\"field_name\":\"Kontext / zusätzliche Informationen\",\"field_type\":\"_Textarea\",\"field_num\":\"4\",\"cell_id\":\"RF\",\"data_or_calc\":\"data\",\"show\":\"1\",\"type\":\"line\",\"line\":\"Solid\",\"p_range\":\"0\",\"color\":\"\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_1\"},\"_B1\":{\"name\":\"Dauer (Training)\",\"sel_val\":\"6|1\",\"base_form_id\":\"6\",\"form_id\":\"6\",\"form_name\":\"Training Load (Session-RPE Methode)\",\"field_name\":\"Formula1\",\"field_type\":\"_Number\",\"field_num\":\"B1\",\"cell_id\":\"BA\",\"data_or_calc\":\"calc\",\"show\":\"1\",\"type\":\"column\",\"line\":\"Solid\",\"p_range\":\"0\",\"color\":\"rgb(255,228,86)\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_12\",\"formula_cells\":\"1\",\"formula_period\":\"lines\",\"formula_after\":\"0\",\"formula_X_axis_show\":\"0\",\"formula_Full_Period\":\"0\",\"formula_input\":\"IF({RB}=1,{RC},\'NA\')\"},\"_B2\":{\"name\":\"Dauer (Wettkampf)\",\"sel_val\":\"6|2\",\"base_form_id\":\"6\",\"form_id\":\"6\",\"form_name\":\"Training Load (Session-RPE Methode)\",\"field_name\":\"Formula2\",\"field_type\":\"_Number\",\"field_num\":\"B2\",\"cell_id\":\"BB\",\"data_or_calc\":\"calc\",\"show\":\"1\",\"type\":\"column\",\"line\":\"Solid\",\"p_range\":\"0\",\"color\":\"rgb(209,176,0)\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_12\",\"formula_cells\":\"1\",\"formula_period\":\"lines\",\"formula_after\":\"0\",\"formula_X_axis_show\":\"0\",\"formula_Full_Period\":\"0\",\"formula_input\":\"IF({RB}=2,{RC},\'NA\')\"},\"_B3\":{\"name\":\"RPE (Training)\",\"sel_val\":\"6|3\",\"base_form_id\":\"6\",\"form_id\":\"6\",\"form_name\":\"Training Load (Session-RPE Methode)\",\"field_name\":\"Formula3\",\"field_type\":\"_Number\",\"field_num\":\"B3\",\"cell_id\":\"BC\",\"data_or_calc\":\"calc\",\"show\":\"1\",\"type\":\"column\",\"line\":\"Solid\",\"p_range\":\"0\",\"color\":\"#f77676\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_10\",\"formula_cells\":\"1\",\"formula_period\":\"lines\",\"formula_after\":\"0\",\"formula_X_axis_show\":\"0\",\"formula_Full_Period\":\"0\",\"formula_input\":\"IF({RB}=1,{RE},\'NA\')\"},\"_B4\":{\"name\":\"RPE (Wettkampf)\",\"sel_val\":\"6|4\",\"base_form_id\":\"6\",\"form_id\":\"6\",\"form_name\":\"Training Load (Session-RPE Methode)\",\"field_name\":\"Formula4\",\"field_type\":\"_Number\",\"field_num\":\"B4\",\"cell_id\":\"BD\",\"data_or_calc\":\"calc\",\"show\":\"1\",\"type\":\"column\",\"line\":\"Solid\",\"p_range\":\"0\",\"color\":\"#d90000\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_10\",\"formula_cells\":\"1\",\"formula_period\":\"lines\",\"formula_after\":\"0\",\"formula_X_axis_show\":\"0\",\"formula_Full_Period\":\"0\",\"formula_input\":\"IF({RB}=2,{RE},\'NA\')\"},\"_B5\":{\"name\":\"Training Load (Training)\",\"sel_val\":\"6|5\",\"base_form_id\":\"6\",\"form_id\":\"6\",\"form_name\":\"Training Load (Session-RPE Methode)\",\"field_name\":\"Formula5\",\"field_type\":\"_Number\",\"field_num\":\"B5\",\"cell_id\":\"BE\",\"data_or_calc\":\"calc\",\"show\":\"1\",\"type\":\"scatter\",\"line\":\"Solid\",\"p_range\":\"0\",\"color\":\"#5f82ff\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_7\",\"formula_cells\":\"1\",\"formula_period\":\"lines\",\"formula_after\":\"0\",\"formula_X_axis_show\":\"0\",\"formula_Full_Period\":\"0\",\"formula_input\":\"IF({RB}=1,{RC}*{RE},\'NA\')\"},\"_B6\":{\"name\":\"Training Load (Wettkampf)\",\"sel_val\":\"6|6\",\"base_form_id\":\"6\",\"form_id\":\"6\",\"form_name\":\"Training Load (Session-RPE Methode)\",\"field_name\":\"Formula6\",\"field_type\":\"_Number\",\"field_num\":\"B6\",\"cell_id\":\"BF\",\"data_or_calc\":\"calc\",\"show\":\"1\",\"type\":\"scatter\",\"line\":\"Solid\",\"p_range\":\"0\",\"color\":\"#002bc4\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_7\",\"formula_cells\":\"1\",\"formula_period\":\"lines\",\"formula_after\":\"0\",\"formula_X_axis_show\":\"0\",\"formula_Full_Period\":\"0\",\"formula_input\":\"IF({RB}=2,{RC}*{RE},\'NA\')\"}},\"athlete_name_show\":\"0\",\"form_name_show\":\"0\"}', '".$datetime."', 'Auto_Init','".$datetime."', 'Auto_Init'),".
		"(7, 6, 1, 1, 4, 'KEB (alle Dimensionen blau/orange)', '{\"data\":{\"_1_S\":{\"name\":\"KEB_Beispiel_S\",\"sel_val\":\"4|1_S\",\"base_form_id\":\"4\",\"form_id\":\"4\",\"form_name\":\"Kurzskala Erholung und Beanspruchung (KEB)\",\"field_name\":\"KEB_Beispiel_S\",\"field_type\":\"_Text\",\"field_num\":\"1_S\",\"cell_id\":\"RA\",\"data_or_calc\":\"data\",\"show\":\"1\",\"type\":\"line\",\"line\":\"Solid\",\"p_range\":\"0\",\"color\":\"\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_1\"},\"_1\":{\"name\":\"KEB_Beispiel\",\"sel_val\":\"4|1\",\"base_form_id\":\"4\",\"form_id\":\"4\",\"form_name\":\"Kurzskala Erholung und Beanspruchung (KEB)\",\"field_name\":\"KEB_Beispiel\",\"field_type\":\"_RadioButtons\",\"field_num\":\"1\",\"cell_id\":\"RB\",\"data_or_calc\":\"data\",\"show\":\"0\",\"type\":\"line\",\"line\":\"Solid\",\"p_range\":\"0\",\"color\":\"\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_1\"},\"_2_S\":{\"name\":\"Körperliche Leistungsfähigkeit_S\",\"sel_val\":\"4|2_S\",\"base_form_id\":\"4\",\"form_id\":\"4\",\"form_name\":\"Kurzskala Erholung und Beanspruchung (KEB)\",\"field_name\":\"Körperliche Leistungsfähigkeit_S\",\"field_type\":\"_Text\",\"field_num\":\"2_S\",\"cell_id\":\"RC\",\"data_or_calc\":\"data\",\"show\":\"1\",\"type\":\"line\",\"line\":\"Solid\",\"p_range\":\"0\",\"color\":\"\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_1\"},\"_2\":{\"name\":\"Körperliche Leistungsfähigkeit\",\"sel_val\":\"4|2\",\"base_form_id\":\"4\",\"form_id\":\"4\",\"form_name\":\"Kurzskala Erholung und Beanspruchung (KEB)\",\"field_name\":\"Körperliche Leistungsfähigkeit\",\"field_type\":\"_RadioButtons\",\"field_num\":\"2\",\"cell_id\":\"RD\",\"data_or_calc\":\"data\",\"show\":\"1\",\"type\":\"spline\",\"line\":\"Solid\",\"p_range\":\"0\",\"color\":\"rgb(0,163,255)\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_2\"},\"_3_S\":{\"name\":\"Mentale Leistungsfähigkeit_S\",\"sel_val\":\"4|3_S\",\"base_form_id\":\"4\",\"form_id\":\"4\",\"form_name\":\"Kurzskala Erholung und Beanspruchung (KEB)\",\"field_name\":\"Mentale Leistungsfähigkeit_S\",\"field_type\":\"_Text\",\"field_num\":\"3_S\",\"cell_id\":\"RE\",\"data_or_calc\":\"data\",\"show\":\"1\",\"type\":\"line\",\"line\":\"Solid\",\"p_range\":\"0\",\"color\":\"\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_1\"},\"_3\":{\"name\":\"Mentale Leistungsfähigkeit\",\"sel_val\":\"4|3\",\"base_form_id\":\"4\",\"form_id\":\"4\",\"form_name\":\"Kurzskala Erholung und Beanspruchung (KEB)\",\"field_name\":\"Mentale Leistungsfähigkeit\",\"field_type\":\"_RadioButtons\",\"field_num\":\"3\",\"cell_id\":\"RF\",\"data_or_calc\":\"data\",\"show\":\"1\",\"type\":\"spline\",\"line\":\"Dot\",\"p_range\":\"0\",\"color\":\"rgb(0,163,255)\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_2\"},\"_4_S\":{\"name\":\"Emotionale Ausgeglichenheit_S\",\"sel_val\":\"4|4_S\",\"base_form_id\":\"4\",\"form_id\":\"4\",\"form_name\":\"Kurzskala Erholung und Beanspruchung (KEB)\",\"field_name\":\"Emotionale Ausgeglichenheit_S\",\"field_type\":\"_Text\",\"field_num\":\"4_S\",\"cell_id\":\"RG\",\"data_or_calc\":\"data\",\"show\":\"1\",\"type\":\"line\",\"line\":\"Solid\",\"p_range\":\"0\",\"color\":\"\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_1\"},\"_4\":{\"name\":\"Emotionale Ausgeglichenheit\",\"sel_val\":\"4|4\",\"base_form_id\":\"4\",\"form_id\":\"4\",\"form_name\":\"Kurzskala Erholung und Beanspruchung (KEB)\",\"field_name\":\"Emotionale Ausgeglichenheit\",\"field_type\":\"_RadioButtons\",\"field_num\":\"4\",\"cell_id\":\"RH\",\"data_or_calc\":\"data\",\"show\":\"1\",\"type\":\"spline\",\"line\":\"ShortDot\",\"p_range\":\"0\",\"color\":\"rgb(0,163,255)\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_2\"},\"_5_S\":{\"name\":\"Allgemeiner Erholungszustand_S\",\"sel_val\":\"4|5_S\",\"base_form_id\":\"4\",\"form_id\":\"4\",\"form_name\":\"Kurzskala Erholung und Beanspruchung (KEB)\",\"field_name\":\"Allgemeiner Erholungszustand_S\",\"field_type\":\"_Text\",\"field_num\":\"5_S\",\"cell_id\":\"RI\",\"data_or_calc\":\"data\",\"show\":\"1\",\"type\":\"line\",\"line\":\"Solid\",\"p_range\":\"0\",\"color\":\"\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_1\"},\"_5\":{\"name\":\"Allgemeiner Erholungszustand\",\"sel_val\":\"4|5\",\"base_form_id\":\"4\",\"form_id\":\"4\",\"form_name\":\"Kurzskala Erholung und Beanspruchung (KEB)\",\"field_name\":\"Allgemeiner Erholungszustand\",\"field_type\":\"_RadioButtons\",\"field_num\":\"5\",\"cell_id\":\"RJ\",\"data_or_calc\":\"data\",\"show\":\"1\",\"type\":\"spline\",\"line\":\"Dash\",\"p_range\":\"0\",\"color\":\"rgb(0,163,255)\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_2\"},\"_6_S\":{\"name\":\"Muskuläre Beanspruchung_S\",\"sel_val\":\"4|6_S\",\"base_form_id\":\"4\",\"form_id\":\"4\",\"form_name\":\"Kurzskala Erholung und Beanspruchung (KEB)\",\"field_name\":\"Muskuläre Beanspruchung_S\",\"field_type\":\"_Text\",\"field_num\":\"6_S\",\"cell_id\":\"RK\",\"data_or_calc\":\"data\",\"show\":\"1\",\"type\":\"line\",\"line\":\"Solid\",\"p_range\":\"0\",\"color\":\"\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_1\"},\"_6\":{\"name\":\"Muskuläre Beanspruchung\",\"sel_val\":\"4|6\",\"base_form_id\":\"4\",\"form_id\":\"4\",\"form_name\":\"Kurzskala Erholung und Beanspruchung (KEB)\",\"field_name\":\"Muskuläre Beanspruchung\",\"field_type\":\"_RadioButtons\",\"field_num\":\"6\",\"cell_id\":\"RL\",\"data_or_calc\":\"data\",\"show\":\"1\",\"type\":\"spline\",\"line\":\"Solid\",\"p_range\":\"0\",\"color\":\"rgb(255,117,0)\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_2\"},\"_7_S\":{\"name\":\"Aktivierungsmangel_S\",\"sel_val\":\"4|7_S\",\"base_form_id\":\"4\",\"form_id\":\"4\",\"form_name\":\"Kurzskala Erholung und Beanspruchung (KEB)\",\"field_name\":\"Aktivierungsmangel_S\",\"field_type\":\"_Text\",\"field_num\":\"7_S\",\"cell_id\":\"RM\",\"data_or_calc\":\"data\",\"show\":\"1\",\"type\":\"line\",\"line\":\"Solid\",\"p_range\":\"0\",\"color\":\"\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_1\"},\"_7\":{\"name\":\"Aktivierungsmangel\",\"sel_val\":\"4|7\",\"base_form_id\":\"4\",\"form_id\":\"4\",\"form_name\":\"Kurzskala Erholung und Beanspruchung (KEB)\",\"field_name\":\"Aktivierungsmangel\",\"field_type\":\"_RadioButtons\",\"field_num\":\"7\",\"cell_id\":\"RN\",\"data_or_calc\":\"data\",\"show\":\"1\",\"type\":\"spline\",\"line\":\"Dot\",\"p_range\":\"0\",\"color\":\"rgb(255,117,0)\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_2\"},\"_8_S\":{\"name\":\"Emotionale Unausgeglichenheit_S\",\"sel_val\":\"4|8_S\",\"base_form_id\":\"4\",\"form_id\":\"4\",\"form_name\":\"Kurzskala Erholung und Beanspruchung (KEB)\",\"field_name\":\"Emotionale Unausgeglichenheit_S\",\"field_type\":\"_Text\",\"field_num\":\"8_S\",\"cell_id\":\"RO\",\"data_or_calc\":\"data\",\"show\":\"1\",\"type\":\"line\",\"line\":\"Solid\",\"p_range\":\"0\",\"color\":\"\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_1\"},\"_8\":{\"name\":\"Emotionale Unausgeglichenheit\",\"sel_val\":\"4|8\",\"base_form_id\":\"4\",\"form_id\":\"4\",\"form_name\":\"Kurzskala Erholung und Beanspruchung (KEB)\",\"field_name\":\"Emotionale Unausgeglichenheit\",\"field_type\":\"_RadioButtons\",\"field_num\":\"8\",\"cell_id\":\"RP\",\"data_or_calc\":\"data\",\"show\":\"1\",\"type\":\"spline\",\"line\":\"ShortDot\",\"p_range\":\"0\",\"color\":\"rgb(255,117,0)\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_2\"},\"_9_S\":{\"name\":\"Allgemeiner Beanspruchungszustand_S\",\"sel_val\":\"4|9_S\",\"base_form_id\":\"4\",\"form_id\":\"4\",\"form_name\":\"Kurzskala Erholung und Beanspruchung (KEB)\",\"field_name\":\"Allgemeiner Beanspruchungszustand_S\",\"field_type\":\"_Text\",\"field_num\":\"9_S\",\"cell_id\":\"RQ\",\"data_or_calc\":\"data\",\"show\":\"1\",\"type\":\"line\",\"line\":\"Solid\",\"p_range\":\"0\",\"color\":\"\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_1\"},\"_9\":{\"name\":\"Allgemeiner Beanspruchungszustand\",\"sel_val\":\"4|9\",\"base_form_id\":\"4\",\"form_id\":\"4\",\"form_name\":\"Kurzskala Erholung und Beanspruchung (KEB)\",\"field_name\":\"Allgemeiner Beanspruchungszustand\",\"field_type\":\"_RadioButtons\",\"field_num\":\"9\",\"cell_id\":\"RR\",\"data_or_calc\":\"data\",\"show\":\"1\",\"type\":\"spline\",\"line\":\"Dash\",\"p_range\":\"0\",\"color\":\"rgb(255,117,0)\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_2\"}},\"athlete_name_show\":\"0\",\"form_name_show\":\"0\"}', '".$datetime."', 'Auto_Init','".$datetime."', 'Auto_Init'),".
		"(8, 6, 1, 1, 4, 'Körperliche Leistungsfähigkeit', '{\"data\":{\"_2\":{\"name\":\"Körperliche Leistungsfähigkeit\",\"sel_val\":\"4|2\",\"base_form_id\":\"4\",\"form_id\":\"4\",\"form_name\":\"Kurzskala Erholung und Beanspruchung (KEB)\",\"field_name\":\"Körperliche Leistungsfähigkeit\",\"field_type\":\"_RadioButtons\",\"field_num\":\"2\",\"cell_id\":\"RD\",\"data_or_calc\":\"data\",\"show\":\"1\",\"type\":\"spline\",\"line\":\"Dash\",\"p_range\":\"0\",\"color\":\"rgb(17,214,0)\",\"markers\":\"false\",\"labels\":\"false\",\"axis\":\"axis_2\"}},\"athlete_name_show\":\"0\",\"form_name_show\":\"0\"}', '".$datetime."', 'Auto_Init','".$datetime."', 'Auto_Init'),".
		"(9, 6, 1, 1, 6, 'Training Load (nicht sichtbar)', '{\"data\":{\"_2\":{\"name\":\"Dauer (Training / Wettkampf)\",\"sel_val\":\"6_S5|2\",\"base_form_id\":\"6\",\"form_id\":\"6_S5\",\"form_name\":\"Training Load (Session-RPE Methode)\",\"field_name\":\"Dauer (Training / Wettkampf)\",\"field_type\":\"_Number\",\"field_num\":\"2\",\"cell_id\":\"RC\",\"data_or_calc\":\"data\",\"show\":\"0\",\"type\":\"line\",\"line\":\"Solid\",\"p_range\":\"0\",\"color\":\"\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_1\"},\"_3\":{\"name\":\"Rating of Perceived Exertion (RPE)\",\"sel_val\":\"6_S5|3\",\"base_form_id\":\"6\",\"form_id\":\"6_S5\",\"form_name\":\"Training Load (Session-RPE Methode)\",\"field_name\":\"Rating of Perceived Exertion (RPE)\",\"field_type\":\"_RadioButtons\",\"field_num\":\"3\",\"cell_id\":\"RE\",\"data_or_calc\":\"data\",\"show\":\"0\",\"type\":\"line\",\"line\":\"Solid\",\"p_range\":\"0\",\"color\":\"\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_1\"},\"_B1\":{\"name\":\"Training Load (Session-RPE Methode)\",\"sel_val\":\"6_S5|1\",\"base_form_id\":\"6\",\"form_id\":\"6_S5\",\"form_name\":\"Training Load (Session-RPE Methode)\",\"field_name\":\"Formula1\",\"field_type\":\"_Number\",\"field_num\":\"B1\",\"cell_id\":\"BA\",\"data_or_calc\":\"calc\",\"show\":\"0\",\"type\":\"column\",\"line\":\"Solid\",\"p_range\":\"0\",\"color\":\"rgb(124,181,236)\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_6\",\"formula_cells\":\"1\",\"formula_period\":\"lines\",\"formula_after\":\"0\",\"formula_X_axis_show\":\"0\",\"formula_Full_Period\":\"0\",\"formula_input\":\"{RC}*{RE}\"}},\"athlete_name_show\":\"0\",\"form_name_show\":\"0\"}', '".$datetime."', 'Auto_Init','".$datetime."', 'Auto_Init');";
}

function get_ResultTemplates_Init_SQL(string $datetime):string {
	return "" .
		// result templates sample data
		"INSERT INTO templates_results (id, user_id, location_id, group_id, `name`, data_json, created, created_by, modified, modified_by) VALUES ".
		"(1, 6, 1, 1, 'Results Template Test', '{\"date_from\":\"2023-09-11 00:00:00\",\"date_to\":\"2023-09-28 23:59:59\",\"groups\":[\"1\"],\"athletes\":[\"1_6\"],\"forms\":[\"3_4\"],\"fields\":[\"SV_4_3\"],\"Data_Forms\":{\"6_4_S3\":{\"_2\":{\"name\":\"Körperliche Leistungsfähigkeit\",\"sel_val\":\"6|4_S3|2\",\"base_form_id\":\"4\",\"form_id\":\"4_S3\",\"form_name\":\"Kurzskala Erholung und Beanspruchung (KEB) (test_form_template3_KEB)\",\"field_name\":\"Körperliche Leistungsfähigkeit\",\"field_type\":\"_RadioButtons\",\"field_num\":\"2\",\"cell_id\":\"RD\",\"data_or_calc\":\"data\",\"show\":\"1\",\"type\":\"line\",\"line\":\"Solid\",\"p_range\":\"0\",\"color\":\"\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_1\",\"ath_id\":\"6\"},\"_B1\":{\"name\":\"Calculation1\",\"sel_val\":\"6|4_S3|1\",\"base_form_id\":\"4\",\"form_id\":\"4_S3\",\"form_name\":\"Kurzskala Erholung und Beanspruchung (KEB) (test_form_template3_KEB)\",\"field_name\":\"Formula1\",\"field_type\":\"_Number\",\"field_num\":\"B1\",\"cell_id\":\"BA\",\"data_or_calc\":\"calc\",\"show\":\"1\",\"type\":\"scatter\",\"line\":\"Solid\",\"p_range\":\"0\",\"color\":\"\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_1\",\"ath_id\":\"6\",\"formula_cells\":\"1\",\"formula_period\":\"lines\",\"formula_after\":\"0\",\"formula_X_axis_show\":\"0\",\"formula_Full_Period\":\"0\",\"formula_input\":\"2*{RD}\"},\"_B2\":{\"name\":\"Calculation2\",\"sel_val\":\"6|4_S3|2\",\"base_form_id\":\"4\",\"form_id\":\"4_S3\",\"form_name\":\"Kurzskala Erholung und Beanspruchung (KEB) (test_form_template3_KEB)\",\"field_name\":\"Formula2\",\"field_type\":\"_Number\",\"field_num\":\"B2\",\"cell_id\":\"BB\",\"data_or_calc\":\"calc\",\"show\":\"1\",\"type\":\"scatter\",\"line\":\"Solid\",\"p_range\":\"0\",\"color\":\"\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_1\",\"ath_id\":\"6\",\"formula_cells\":\"1\",\"formula_period\":\"lines\",\"formula_after\":\"0\",\"formula_X_axis_show\":\"0\",\"formula_Full_Period\":\"0\",\"formula_input\":\"10*{BA}\"},\"form_name_show\":\"0\"}},\"Data_Intervals\":{},\"users_show_name\":{\"6\":\"0\"}}', '".$datetime."', 'Auto_Init','".$datetime."', 'Auto_Init'),".
		"(2, 6, 1, 1, 'KEB, Schlafprotokoll und Training Load', '{\"date_from\":\"2023-10-27 00:00:00\",\"date_to\":\"2023-11-23 23:59:59\",\"groups\":[\"1\"],\"athletes\":[\"1_6\"],\"forms\":[\"4_6\",\"3_4\",\"3_5\"],\"fields\":[\"SV_4_7\",\"SV_5_4\",\"SV_6_6\"],\"Data_Forms\":{\"6_4_S7\":{\"_1_S\":{\"name\":\"KEB_Beispiel_S\",\"sel_val\":\"6|4_S7|1_S\",\"base_form_id\":\"4\",\"form_id\":\"4_S7\",\"form_name\":\"Kurzskala Erholung und Beanspruchung (KEB) (KEB (alle Dimensionen blau/orange))\",\"field_name\":\"KEB_Beispiel_S\",\"field_type\":\"_Text\",\"field_num\":\"1_S\",\"cell_id\":\"RA\",\"data_or_calc\":\"data\",\"show\":\"1\",\"type\":\"line\",\"line\":\"Solid\",\"p_range\":\"0\",\"color\":\"\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_1\",\"ath_id\":\"6\"},\"_1\":{\"name\":\"KEB_Beispiel\",\"sel_val\":\"6|4_S7|1\",\"base_form_id\":\"4\",\"form_id\":\"4_S7\",\"form_name\":\"Kurzskala Erholung und Beanspruchung (KEB) (KEB (alle Dimensionen blau/orange))\",\"field_name\":\"KEB_Beispiel\",\"field_type\":\"_RadioButtons\",\"field_num\":\"1\",\"cell_id\":\"RB\",\"data_or_calc\":\"data\",\"show\":\"0\",\"type\":\"line\",\"line\":\"Solid\",\"p_range\":\"0\",\"color\":\"\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_1\",\"ath_id\":\"6\"},\"_2_S\":{\"name\":\"Körperliche Leistungsfähigkeit_S\",\"sel_val\":\"6|4_S7|2_S\",\"base_form_id\":\"4\",\"form_id\":\"4_S7\",\"form_name\":\"Kurzskala Erholung und Beanspruchung (KEB) (KEB (alle Dimensionen blau/orange))\",\"field_name\":\"Körperliche Leistungsfähigkeit_S\",\"field_type\":\"_Text\",\"field_num\":\"2_S\",\"cell_id\":\"RC\",\"data_or_calc\":\"data\",\"show\":\"1\",\"type\":\"line\",\"line\":\"Solid\",\"p_range\":\"0\",\"color\":\"\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_1\",\"ath_id\":\"6\"},\"_2\":{\"name\":\"Körperliche Leistungsfähigkeit\",\"sel_val\":\"6|4_S7|2\",\"base_form_id\":\"4\",\"form_id\":\"4_S7\",\"form_name\":\"Kurzskala Erholung und Beanspruchung (KEB) (KEB (alle Dimensionen blau/orange))\",\"field_name\":\"Körperliche Leistungsfähigkeit\",\"field_type\":\"_RadioButtons\",\"field_num\":\"2\",\"cell_id\":\"RD\",\"data_or_calc\":\"data\",\"show\":\"1\",\"type\":\"spline\",\"line\":\"Solid\",\"p_range\":\"0\",\"color\":\"rgb(0,163,255)\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_2\",\"ath_id\":\"6\"},\"_3_S\":{\"name\":\"Mentale Leistungsfähigkeit_S\",\"sel_val\":\"6|4_S7|3_S\",\"base_form_id\":\"4\",\"form_id\":\"4_S7\",\"form_name\":\"Kurzskala Erholung und Beanspruchung (KEB) (KEB (alle Dimensionen blau/orange))\",\"field_name\":\"Mentale Leistungsfähigkeit_S\",\"field_type\":\"_Text\",\"field_num\":\"3_S\",\"cell_id\":\"RE\",\"data_or_calc\":\"data\",\"show\":\"1\",\"type\":\"line\",\"line\":\"Solid\",\"p_range\":\"0\",\"color\":\"\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_1\",\"ath_id\":\"6\"},\"_3\":{\"name\":\"Mentale Leistungsfähigkeit\",\"sel_val\":\"6|4_S7|3\",\"base_form_id\":\"4\",\"form_id\":\"4_S7\",\"form_name\":\"Kurzskala Erholung und Beanspruchung (KEB) (KEB (alle Dimensionen blau/orange))\",\"field_name\":\"Mentale Leistungsfähigkeit\",\"field_type\":\"_RadioButtons\",\"field_num\":\"3\",\"cell_id\":\"RF\",\"data_or_calc\":\"data\",\"show\":\"1\",\"type\":\"spline\",\"line\":\"Dot\",\"p_range\":\"0\",\"color\":\"rgb(0,163,255)\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_2\",\"ath_id\":\"6\"},\"_4_S\":{\"name\":\"Emotionale Ausgeglichenheit_S\",\"sel_val\":\"6|4_S7|4_S\",\"base_form_id\":\"4\",\"form_id\":\"4_S7\",\"form_name\":\"Kurzskala Erholung und Beanspruchung (KEB) (KEB (alle Dimensionen blau/orange))\",\"field_name\":\"Emotionale Ausgeglichenheit_S\",\"field_type\":\"_Text\",\"field_num\":\"4_S\",\"cell_id\":\"RG\",\"data_or_calc\":\"data\",\"show\":\"1\",\"type\":\"line\",\"line\":\"Solid\",\"p_range\":\"0\",\"color\":\"\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_1\",\"ath_id\":\"6\"},\"_4\":{\"name\":\"Emotionale Ausgeglichenheit\",\"sel_val\":\"6|4_S7|4\",\"base_form_id\":\"4\",\"form_id\":\"4_S7\",\"form_name\":\"Kurzskala Erholung und Beanspruchung (KEB) (KEB (alle Dimensionen blau/orange))\",\"field_name\":\"Emotionale Ausgeglichenheit\",\"field_type\":\"_RadioButtons\",\"field_num\":\"4\",\"cell_id\":\"RH\",\"data_or_calc\":\"data\",\"show\":\"1\",\"type\":\"spline\",\"line\":\"ShortDot\",\"p_range\":\"0\",\"color\":\"rgb(0,163,255)\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_2\",\"ath_id\":\"6\"},\"_5_S\":{\"name\":\"Allgemeiner Erholungszustand_S\",\"sel_val\":\"6|4_S7|5_S\",\"base_form_id\":\"4\",\"form_id\":\"4_S7\",\"form_name\":\"Kurzskala Erholung und Beanspruchung (KEB) (KEB (alle Dimensionen blau/orange))\",\"field_name\":\"Allgemeiner Erholungszustand_S\",\"field_type\":\"_Text\",\"field_num\":\"5_S\",\"cell_id\":\"RI\",\"data_or_calc\":\"data\",\"show\":\"1\",\"type\":\"line\",\"line\":\"Solid\",\"p_range\":\"0\",\"color\":\"\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_1\",\"ath_id\":\"6\"},\"_5\":{\"name\":\"Allgemeiner Erholungszustand\",\"sel_val\":\"6|4_S7|5\",\"base_form_id\":\"4\",\"form_id\":\"4_S7\",\"form_name\":\"Kurzskala Erholung und Beanspruchung (KEB) (KEB (alle Dimensionen blau/orange))\",\"field_name\":\"Allgemeiner Erholungszustand\",\"field_type\":\"_RadioButtons\",\"field_num\":\"5\",\"cell_id\":\"RJ\",\"data_or_calc\":\"data\",\"show\":\"1\",\"type\":\"spline\",\"line\":\"Dash\",\"p_range\":\"0\",\"color\":\"rgb(0,163,255)\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_2\",\"ath_id\":\"6\"},\"_6_S\":{\"name\":\"Muskuläre Beanspruchung_S\",\"sel_val\":\"6|4_S7|6_S\",\"base_form_id\":\"4\",\"form_id\":\"4_S7\",\"form_name\":\"Kurzskala Erholung und Beanspruchung (KEB) (KEB (alle Dimensionen blau/orange))\",\"field_name\":\"Muskuläre Beanspruchung_S\",\"field_type\":\"_Text\",\"field_num\":\"6_S\",\"cell_id\":\"RK\",\"data_or_calc\":\"data\",\"show\":\"1\",\"type\":\"line\",\"line\":\"Solid\",\"p_range\":\"0\",\"color\":\"\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_1\",\"ath_id\":\"6\"},\"_6\":{\"name\":\"Muskuläre Beanspruchung\",\"sel_val\":\"6|4_S7|6\",\"base_form_id\":\"4\",\"form_id\":\"4_S7\",\"form_name\":\"Kurzskala Erholung und Beanspruchung (KEB) (KEB (alle Dimensionen blau/orange))\",\"field_name\":\"Muskuläre Beanspruchung\",\"field_type\":\"_RadioButtons\",\"field_num\":\"6\",\"cell_id\":\"RL\",\"data_or_calc\":\"data\",\"show\":\"1\",\"type\":\"spline\",\"line\":\"Solid\",\"p_range\":\"0\",\"color\":\"rgb(255,117,0)\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_2\",\"ath_id\":\"6\"},\"_7_S\":{\"name\":\"Aktivierungsmangel_S\",\"sel_val\":\"6|4_S7|7_S\",\"base_form_id\":\"4\",\"form_id\":\"4_S7\",\"form_name\":\"Kurzskala Erholung und Beanspruchung (KEB) (KEB (alle Dimensionen blau/orange))\",\"field_name\":\"Aktivierungsmangel_S\",\"field_type\":\"_Text\",\"field_num\":\"7_S\",\"cell_id\":\"RM\",\"data_or_calc\":\"data\",\"show\":\"1\",\"type\":\"line\",\"line\":\"Solid\",\"p_range\":\"0\",\"color\":\"\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_1\",\"ath_id\":\"6\"},\"_7\":{\"name\":\"Aktivierungsmangel\",\"sel_val\":\"6|4_S7|7\",\"base_form_id\":\"4\",\"form_id\":\"4_S7\",\"form_name\":\"Kurzskala Erholung und Beanspruchung (KEB) (KEB (alle Dimensionen blau/orange))\",\"field_name\":\"Aktivierungsmangel\",\"field_type\":\"_RadioButtons\",\"field_num\":\"7\",\"cell_id\":\"RN\",\"data_or_calc\":\"data\",\"show\":\"1\",\"type\":\"spline\",\"line\":\"Dot\",\"p_range\":\"0\",\"color\":\"rgb(255,117,0)\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_2\",\"ath_id\":\"6\"},\"_8_S\":{\"name\":\"Emotionale Unausgeglichenheit_S\",\"sel_val\":\"6|4_S7|8_S\",\"base_form_id\":\"4\",\"form_id\":\"4_S7\",\"form_name\":\"Kurzskala Erholung und Beanspruchung (KEB) (KEB (alle Dimensionen blau/orange))\",\"field_name\":\"Emotionale Unausgeglichenheit_S\",\"field_type\":\"_Text\",\"field_num\":\"8_S\",\"cell_id\":\"RO\",\"data_or_calc\":\"data\",\"show\":\"1\",\"type\":\"line\",\"line\":\"Solid\",\"p_range\":\"0\",\"color\":\"\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_1\",\"ath_id\":\"6\"},\"_8\":{\"name\":\"Emotionale Unausgeglichenheit\",\"sel_val\":\"6|4_S7|8\",\"base_form_id\":\"4\",\"form_id\":\"4_S7\",\"form_name\":\"Kurzskala Erholung und Beanspruchung (KEB) (KEB (alle Dimensionen blau/orange))\",\"field_name\":\"Emotionale Unausgeglichenheit\",\"field_type\":\"_RadioButtons\",\"field_num\":\"8\",\"cell_id\":\"RP\",\"data_or_calc\":\"data\",\"show\":\"1\",\"type\":\"spline\",\"line\":\"ShortDot\",\"p_range\":\"0\",\"color\":\"rgb(255,117,0)\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_2\",\"ath_id\":\"6\"},\"_9_S\":{\"name\":\"Allgemeiner Beanspruchungszustand_S\",\"sel_val\":\"6|4_S7|9_S\",\"base_form_id\":\"4\",\"form_id\":\"4_S7\",\"form_name\":\"Kurzskala Erholung und Beanspruchung (KEB) (KEB (alle Dimensionen blau/orange))\",\"field_name\":\"Allgemeiner Beanspruchungszustand_S\",\"field_type\":\"_Text\",\"field_num\":\"9_S\",\"cell_id\":\"RQ\",\"data_or_calc\":\"data\",\"show\":\"1\",\"type\":\"line\",\"line\":\"Solid\",\"p_range\":\"0\",\"color\":\"\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_1\",\"ath_id\":\"6\"},\"_9\":{\"name\":\"Allgemeiner Beanspruchungszustand\",\"sel_val\":\"6|4_S7|9\",\"base_form_id\":\"4\",\"form_id\":\"4_S7\",\"form_name\":\"Kurzskala Erholung und Beanspruchung (KEB) (KEB (alle Dimensionen blau/orange))\",\"field_name\":\"Allgemeiner Beanspruchungszustand\",\"field_type\":\"_RadioButtons\",\"field_num\":\"9\",\"cell_id\":\"RR\",\"data_or_calc\":\"data\",\"show\":\"1\",\"type\":\"spline\",\"line\":\"Dash\",\"p_range\":\"0\",\"color\":\"rgb(255,117,0)\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_2\",\"ath_id\":\"6\"},\"form_name_show\":\"0\"},\"6_5_S4\":{\"_1\":{\"name\":\"Schlafdauer\",\"sel_val\":\"6|5_S4|1\",\"base_form_id\":\"5\",\"form_id\":\"5_S4\",\"form_name\":\"Schlafdokumentation (kurz) (Schlafdauer und -qualität)\",\"field_name\":\"Schlafdauer\",\"field_type\":\"_Period\",\"field_num\":\"1\",\"cell_id\":\"RC\",\"data_or_calc\":\"data\",\"show\":\"0\",\"type\":\"line\",\"line\":\"Solid\",\"p_range\":\"0\",\"color\":\"\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_1\",\"ath_id\":\"6\"},\"_2\":{\"name\":\"Schlafqualität\",\"sel_val\":\"6|5_S4|2\",\"base_form_id\":\"5\",\"form_id\":\"5_S4\",\"form_name\":\"Schlafdokumentation (kurz) (Schlafdauer und -qualität)\",\"field_name\":\"Schlafqualität\",\"field_type\":\"_RadioButtons\",\"field_num\":\"2\",\"cell_id\":\"RE\",\"data_or_calc\":\"data\",\"show\":\"0\",\"type\":\"line\",\"line\":\"Solid\",\"p_range\":\"0\",\"color\":\"\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_1\",\"ath_id\":\"6\"},\"_B1\":{\"name\":\"gute / sehr gute Schlafqualität\",\"sel_val\":\"6|5_S4|1\",\"base_form_id\":\"5\",\"form_id\":\"5_S4\",\"form_name\":\"Schlafdokumentation (kurz) (Schlafdauer und -qualität)\",\"field_name\":\"Formula1\",\"field_type\":\"_Number\",\"field_num\":\"B1\",\"cell_id\":\"BA\",\"data_or_calc\":\"calc\",\"show\":\"1\",\"type\":\"scatter\",\"line\":\"Solid\",\"p_range\":\"0\",\"color\":\"#00ad27\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_4\",\"ath_id\":\"6\",\"formula_cells\":\"1\",\"formula_period\":\"lines\",\"formula_after\":\"0\",\"formula_X_axis_show\":\"0\",\"formula_Full_Period\":\"0\",\"formula_input\":\"if({RE}>6,{RC}/60)\"},\"_B2\":{\"name\":\"mittelmäßige Schlafqualität\",\"sel_val\":\"6|5_S4|2\",\"base_form_id\":\"5\",\"form_id\":\"5_S4\",\"form_name\":\"Schlafdokumentation (kurz) (Schlafdauer und -qualität)\",\"field_name\":\"Formula2\",\"field_type\":\"_Number\",\"field_num\":\"B2\",\"cell_id\":\"BB\",\"data_or_calc\":\"calc\",\"show\":\"1\",\"type\":\"scatter\",\"line\":\"Solid\",\"p_range\":\"0\",\"color\":\"#fff400\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_4\",\"ath_id\":\"6\",\"formula_cells\":\"1\",\"formula_period\":\"lines\",\"formula_after\":\"0\",\"formula_X_axis_show\":\"0\",\"formula_Full_Period\":\"0\",\"formula_input\":\"if(AND({RE}>3,{RE}<7),{RC}/60)\"},\"_B3\":{\"name\":\"schlechte / sehr schlechte Schlafqualität\",\"sel_val\":\"6|5_S4|3\",\"base_form_id\":\"5\",\"form_id\":\"5_S4\",\"form_name\":\"Schlafdokumentation (kurz) (Schlafdauer und -qualität)\",\"field_name\":\"Formula3\",\"field_type\":\"_Number\",\"field_num\":\"B3\",\"cell_id\":\"BC\",\"data_or_calc\":\"calc\",\"show\":\"1\",\"type\":\"scatter\",\"line\":\"Solid\",\"p_range\":\"0\",\"color\":\"#ff0000\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_4\",\"ath_id\":\"6\",\"formula_cells\":\"1\",\"formula_period\":\"lines\",\"formula_after\":\"0\",\"formula_X_axis_show\":\"0\",\"formula_Full_Period\":\"0\",\"formula_input\":\"if({RE}<4,{RC}/60)\"},\"_B4\":{\"name\":\"Schlafdauer\",\"sel_val\":\"6|5_S4|4\",\"base_form_id\":\"5\",\"form_id\":\"5_S4\",\"form_name\":\"Schlafdokumentation (kurz) (Schlafdauer und -qualität)\",\"field_name\":\"Formula4\",\"field_type\":\"_Number\",\"field_num\":\"B4\",\"cell_id\":\"BD\",\"data_or_calc\":\"calc\",\"show\":\"1\",\"type\":\"spline\",\"line\":\"Solid\",\"p_range\":\"0\",\"color\":\"rgba(0,0,0,0.15)\",\"markers\":\"false\",\"labels\":\"false\",\"axis\":\"axis_4\",\"ath_id\":\"6\",\"formula_cells\":\"1\",\"formula_period\":\"lines\",\"formula_after\":\"0\",\"formula_X_axis_show\":\"0\",\"formula_Full_Period\":\"0\",\"formula_input\":\"{RC}/60\"},\"form_name_show\":\"0\"},\"6_6_S6\":{\"_1_S\":{\"name\":\"Art (Training oder Wettkampf)_S\",\"sel_val\":\"6|6_S6|1_S\",\"base_form_id\":\"6\",\"form_id\":\"6_S6\",\"form_name\":\"Training Load (Session-RPE Methode) (Trainingsdauer (Umfang), Subjektives Belastungsempfinden (RPE) und Training Load (Trainingsdauer*RPE))\",\"field_name\":\"Art (Training oder Wettkampf)_S\",\"field_type\":\"_Text\",\"field_num\":\"1_S\",\"cell_id\":\"RA\",\"data_or_calc\":\"data\",\"show\":\"1\",\"type\":\"line\",\"line\":\"Solid\",\"p_range\":\"0\",\"color\":\"\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_1\",\"ath_id\":\"6\"},\"_1\":{\"name\":\"Art (Training oder Wettkampf)\",\"sel_val\":\"6|6_S6|1\",\"base_form_id\":\"6\",\"form_id\":\"6_S6\",\"form_name\":\"Training Load (Session-RPE Methode) (Trainingsdauer (Umfang), Subjektives Belastungsempfinden (RPE) und Training Load (Trainingsdauer*RPE))\",\"field_name\":\"Art (Training oder Wettkampf)\",\"field_type\":\"_RadioButtons\",\"field_num\":\"1\",\"cell_id\":\"RB\",\"data_or_calc\":\"data\",\"show\":\"0\",\"type\":\"line\",\"line\":\"Solid\",\"p_range\":\"0\",\"color\":\"\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_1\",\"ath_id\":\"6\"},\"_2\":{\"name\":\"Dauer (Training / Wettkampf)\",\"sel_val\":\"6|6_S6|2\",\"base_form_id\":\"6\",\"form_id\":\"6_S6\",\"form_name\":\"Training Load (Session-RPE Methode) (Trainingsdauer (Umfang), Subjektives Belastungsempfinden (RPE) und Training Load (Trainingsdauer*RPE))\",\"field_name\":\"Dauer (Training / Wettkampf)\",\"field_type\":\"_Number\",\"field_num\":\"2\",\"cell_id\":\"RC\",\"data_or_calc\":\"data\",\"show\":\"0\",\"type\":\"line\",\"line\":\"Solid\",\"p_range\":\"0\",\"color\":\"\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_1\",\"ath_id\":\"6\"},\"_3_S\":{\"name\":\"Rating of Perceived Exertion (RPE)_S\",\"sel_val\":\"6|6_S6|3_S\",\"base_form_id\":\"6\",\"form_id\":\"6_S6\",\"form_name\":\"Training Load (Session-RPE Methode) (Trainingsdauer (Umfang), Subjektives Belastungsempfinden (RPE) und Training Load (Trainingsdauer*RPE))\",\"field_name\":\"Rating of Perceived Exertion (RPE)_S\",\"field_type\":\"_Text\",\"field_num\":\"3_S\",\"cell_id\":\"RD\",\"data_or_calc\":\"data\",\"show\":\"1\",\"type\":\"line\",\"line\":\"Solid\",\"p_range\":\"0\",\"color\":\"\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_1\",\"ath_id\":\"6\"},\"_3\":{\"name\":\"Rating of Perceived Exertion (RPE)\",\"sel_val\":\"6|6_S6|3\",\"base_form_id\":\"6\",\"form_id\":\"6_S6\",\"form_name\":\"Training Load (Session-RPE Methode) (Trainingsdauer (Umfang), Subjektives Belastungsempfinden (RPE) und Training Load (Trainingsdauer*RPE))\",\"field_name\":\"Rating of Perceived Exertion (RPE)\",\"field_type\":\"_RadioButtons\",\"field_num\":\"3\",\"cell_id\":\"RE\",\"data_or_calc\":\"data\",\"show\":\"0\",\"type\":\"line\",\"line\":\"Solid\",\"p_range\":\"0\",\"color\":\"\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_1\",\"ath_id\":\"6\"},\"_4\":{\"name\":\"Kontext / zusätzliche Informationen\",\"sel_val\":\"6|6_S6|4\",\"base_form_id\":\"6\",\"form_id\":\"6_S6\",\"form_name\":\"Training Load (Session-RPE Methode) (Trainingsdauer (Umfang), Subjektives Belastungsempfinden (RPE) und Training Load (Trainingsdauer*RPE))\",\"field_name\":\"Kontext / zusätzliche Informationen\",\"field_type\":\"_Textarea\",\"field_num\":\"4\",\"cell_id\":\"RF\",\"data_or_calc\":\"data\",\"show\":\"1\",\"type\":\"line\",\"line\":\"Solid\",\"p_range\":\"0\",\"color\":\"\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_1\",\"ath_id\":\"6\"},\"_B1\":{\"name\":\"Dauer (Training)\",\"sel_val\":\"6|6_S6|1\",\"base_form_id\":\"6\",\"form_id\":\"6_S6\",\"form_name\":\"Training Load (Session-RPE Methode) (Trainingsdauer (Umfang), Subjektives Belastungsempfinden (RPE) und Training Load (Trainingsdauer*RPE))\",\"field_name\":\"Formula1\",\"field_type\":\"_Number\",\"field_num\":\"B1\",\"cell_id\":\"BA\",\"data_or_calc\":\"calc\",\"show\":\"1\",\"type\":\"column\",\"line\":\"Solid\",\"p_range\":\"0\",\"color\":\"rgb(255,228,86)\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_9\",\"ath_id\":\"6\",\"formula_cells\":\"1\",\"formula_period\":\"lines\",\"formula_after\":\"0\",\"formula_X_axis_show\":\"0\",\"formula_Full_Period\":\"0\",\"formula_input\":\"IF({RB}=1,{RC},\'NA\')\"},\"_B2\":{\"name\":\"Dauer (Wettkampf)\",\"sel_val\":\"6|6_S6|2\",\"base_form_id\":\"6\",\"form_id\":\"6_S6\",\"form_name\":\"Training Load (Session-RPE Methode) (Trainingsdauer (Umfang), Subjektives Belastungsempfinden (RPE) und Training Load (Trainingsdauer*RPE))\",\"field_name\":\"Formula2\",\"field_type\":\"_Number\",\"field_num\":\"B2\",\"cell_id\":\"BB\",\"data_or_calc\":\"calc\",\"show\":\"1\",\"type\":\"column\",\"line\":\"Solid\",\"p_range\":\"0\",\"color\":\"rgb(209,176,0)\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_9\",\"ath_id\":\"6\",\"formula_cells\":\"1\",\"formula_period\":\"lines\",\"formula_after\":\"0\",\"formula_X_axis_show\":\"0\",\"formula_Full_Period\":\"0\",\"formula_input\":\"IF({RB}=2,{RC},\'NA\')\"},\"_B3\":{\"name\":\"RPE (Training)\",\"sel_val\":\"6|6_S6|3\",\"base_form_id\":\"6\",\"form_id\":\"6_S6\",\"form_name\":\"Training Load (Session-RPE Methode) (Trainingsdauer (Umfang), Subjektives Belastungsempfinden (RPE) und Training Load (Trainingsdauer*RPE))\",\"field_name\":\"Formula3\",\"field_type\":\"_Number\",\"field_num\":\"B3\",\"cell_id\":\"BC\",\"data_or_calc\":\"calc\",\"show\":\"1\",\"type\":\"column\",\"line\":\"Solid\",\"p_range\":\"0\",\"color\":\"#f77676\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_10\",\"ath_id\":\"6\",\"formula_cells\":\"1\",\"formula_period\":\"lines\",\"formula_after\":\"0\",\"formula_X_axis_show\":\"0\",\"formula_Full_Period\":\"0\",\"formula_input\":\"IF({RB}=1,{RE},\'NA\')\"},\"_B4\":{\"name\":\"RPE (Wettkampf)\",\"sel_val\":\"6|6_S6|4\",\"base_form_id\":\"6\",\"form_id\":\"6_S6\",\"form_name\":\"Training Load (Session-RPE Methode) (Trainingsdauer (Umfang), Subjektives Belastungsempfinden (RPE) und Training Load (Trainingsdauer*RPE))\",\"field_name\":\"Formula4\",\"field_type\":\"_Number\",\"field_num\":\"B4\",\"cell_id\":\"BD\",\"data_or_calc\":\"calc\",\"show\":\"1\",\"type\":\"column\",\"line\":\"Solid\",\"p_range\":\"0\",\"color\":\"#d90000\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_10\",\"ath_id\":\"6\",\"formula_cells\":\"1\",\"formula_period\":\"lines\",\"formula_after\":\"0\",\"formula_X_axis_show\":\"0\",\"formula_Full_Period\":\"0\",\"formula_input\":\"IF({RB}=2,{RE},\'NA\')\"},\"_B5\":{\"name\":\"Training Load (Training)\",\"sel_val\":\"6|6_S6|5\",\"base_form_id\":\"6\",\"form_id\":\"6_S6\",\"form_name\":\"Training Load (Session-RPE Methode) (Trainingsdauer (Umfang), Subjektives Belastungsempfinden (RPE) und Training Load (Trainingsdauer*RPE))\",\"field_name\":\"Formula5\",\"field_type\":\"_Number\",\"field_num\":\"B5\",\"cell_id\":\"BE\",\"data_or_calc\":\"calc\",\"show\":\"1\",\"type\":\"scatter\",\"line\":\"Solid\",\"p_range\":\"0\",\"color\":\"#5f82ff\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_7\",\"ath_id\":\"6\",\"formula_cells\":\"1\",\"formula_period\":\"lines\",\"formula_after\":\"0\",\"formula_X_axis_show\":\"0\",\"formula_Full_Period\":\"0\",\"formula_input\":\"IF({RB}=1,{RC}*{RE},\'NA\')\"},\"_B6\":{\"name\":\"Training Load (Wettkampf)\",\"sel_val\":\"6|6_S6|6\",\"base_form_id\":\"6\",\"form_id\":\"6_S6\",\"form_name\":\"Training Load (Session-RPE Methode) (Trainingsdauer (Umfang), Subjektives Belastungsempfinden (RPE) und Training Load (Trainingsdauer*RPE))\",\"field_name\":\"Formula6\",\"field_type\":\"_Number\",\"field_num\":\"B6\",\"cell_id\":\"BF\",\"data_or_calc\":\"calc\",\"show\":\"1\",\"type\":\"scatter\",\"line\":\"Solid\",\"p_range\":\"0\",\"color\":\"#002bc4\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_7\",\"ath_id\":\"6\",\"formula_cells\":\"1\",\"formula_period\":\"lines\",\"formula_after\":\"0\",\"formula_X_axis_show\":\"0\",\"formula_Full_Period\":\"0\",\"formula_input\":\"IF({RB}=2,{RC}*{RE},\'NA\')\"},\"form_name_show\":\"0\"}},\"Data_Intervals\":{},\"users_show_name\":{\"6\":\"0\"}}', '".$datetime."', 'Auto_Init','".$datetime."', 'Auto_Init'),".
		"(3, 6, 1, 1, 'Körperliche Leistungsfähigkeit, Schlafdauer und Training Load (wöchentlich gemittelt/aggregiert)', '{\"date_from\":\"2023-08-27 00:00:00\",\"date_to\":\"2023-11-23 23:59:59\",\"groups\":[\"1\"],\"athletes\":[\"1_6\"],\"forms\":[\"4_6\",\"3_4\",\"3_5\"],\"fields\":[\"SV_4_8\",\"SV_5_4\",\"SV_6_9\"],\"Data_Forms\":{\"6_4_S8\":{\"_2\":{\"name\":\"Körperliche Leistungsfähigkeit\",\"sel_val\":\"6|4_S8|2\",\"base_form_id\":\"4\",\"form_id\":\"4_S8\",\"form_name\":\"Kurzskala Erholung und Beanspruchung (KEB) (Körperliche Leistungsfähigkeit)\",\"field_name\":\"Körperliche Leistungsfähigkeit\",\"field_type\":\"_RadioButtons\",\"field_num\":\"2\",\"cell_id\":\"RD\",\"data_or_calc\":\"data\",\"show\":\"1\",\"type\":\"spline\",\"line\":\"Dash\",\"p_range\":\"0\",\"color\":\"rgb(17,214,0)\",\"markers\":\"false\",\"labels\":\"false\",\"axis\":\"axis_2\",\"ath_id\":\"6\"},\"form_name_show\":\"0\"},\"6_5_S4\":{\"_1\":{\"name\":\"Schlafdauer\",\"sel_val\":\"6|5_S4|1\",\"base_form_id\":\"5\",\"form_id\":\"5_S4\",\"form_name\":\"Schlafdokumentation (kurz) (Schlafdauer und -qualität)\",\"field_name\":\"Schlafdauer\",\"field_type\":\"_Period\",\"field_num\":\"1\",\"cell_id\":\"RC\",\"data_or_calc\":\"data\",\"show\":\"0\",\"type\":\"line\",\"line\":\"Solid\",\"p_range\":\"0\",\"color\":\"\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_1\",\"ath_id\":\"6\"},\"_2\":{\"name\":\"Schlafqualität\",\"sel_val\":\"6|5_S4|2\",\"base_form_id\":\"5\",\"form_id\":\"5_S4\",\"form_name\":\"Schlafdokumentation (kurz) (Schlafdauer und -qualität)\",\"field_name\":\"Schlafqualität\",\"field_type\":\"_RadioButtons\",\"field_num\":\"2\",\"cell_id\":\"RE\",\"data_or_calc\":\"data\",\"show\":\"0\",\"type\":\"line\",\"line\":\"Solid\",\"p_range\":\"0\",\"color\":\"\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_1\",\"ath_id\":\"6\"},\"_B1\":{\"name\":\"gute / sehr gute Schlafqualität\",\"sel_val\":\"6|5_S4|1\",\"base_form_id\":\"5\",\"form_id\":\"5_S4\",\"form_name\":\"Schlafdokumentation (kurz) (Schlafdauer und -qualität)\",\"field_name\":\"Formula1\",\"field_type\":\"_Number\",\"field_num\":\"B1\",\"cell_id\":\"BA\",\"data_or_calc\":\"calc\",\"show\":\"1\",\"type\":\"scatter\",\"line\":\"Solid\",\"p_range\":\"0\",\"color\":\"#00ad27\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_4\",\"ath_id\":\"6\",\"formula_cells\":\"1\",\"formula_period\":\"lines\",\"formula_after\":\"0\",\"formula_X_axis_show\":\"0\",\"formula_Full_Period\":\"0\",\"formula_input\":\"if({RE}>6,{RC}/60)\"},\"_B2\":{\"name\":\"mittelmäßige Schlafqualität\",\"sel_val\":\"6|5_S4|2\",\"base_form_id\":\"5\",\"form_id\":\"5_S4\",\"form_name\":\"Schlafdokumentation (kurz) (Schlafdauer und -qualität)\",\"field_name\":\"Formula2\",\"field_type\":\"_Number\",\"field_num\":\"B2\",\"cell_id\":\"BB\",\"data_or_calc\":\"calc\",\"show\":\"1\",\"type\":\"scatter\",\"line\":\"Solid\",\"p_range\":\"0\",\"color\":\"#fff400\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_4\",\"ath_id\":\"6\",\"formula_cells\":\"1\",\"formula_period\":\"lines\",\"formula_after\":\"0\",\"formula_X_axis_show\":\"0\",\"formula_Full_Period\":\"0\",\"formula_input\":\"if(AND({RE}>3,{RE}<7),{RC}/60)\"},\"_B3\":{\"name\":\"schlechte / sehr schlechte Schlafqualität\",\"sel_val\":\"6|5_S4|3\",\"base_form_id\":\"5\",\"form_id\":\"5_S4\",\"form_name\":\"Schlafdokumentation (kurz) (Schlafdauer und -qualität)\",\"field_name\":\"Formula3\",\"field_type\":\"_Number\",\"field_num\":\"B3\",\"cell_id\":\"BC\",\"data_or_calc\":\"calc\",\"show\":\"1\",\"type\":\"scatter\",\"line\":\"Solid\",\"p_range\":\"0\",\"color\":\"#ff0000\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_4\",\"ath_id\":\"6\",\"formula_cells\":\"1\",\"formula_period\":\"lines\",\"formula_after\":\"0\",\"formula_X_axis_show\":\"0\",\"formula_Full_Period\":\"0\",\"formula_input\":\"if({RE}<4,{RC}/60)\"},\"_B4\":{\"name\":\"Schlafdauer\",\"sel_val\":\"6|5_S4|4\",\"base_form_id\":\"5\",\"form_id\":\"5_S4\",\"form_name\":\"Schlafdokumentation (kurz) (Schlafdauer und -qualität)\",\"field_name\":\"Formula4\",\"field_type\":\"_Number\",\"field_num\":\"B4\",\"cell_id\":\"BD\",\"data_or_calc\":\"calc\",\"show\":\"1\",\"type\":\"spline\",\"line\":\"Solid\",\"p_range\":\"0\",\"color\":\"rgba(0,0,0,0.15)\",\"markers\":\"false\",\"labels\":\"false\",\"axis\":\"axis_4\",\"ath_id\":\"6\",\"formula_cells\":\"1\",\"formula_period\":\"lines\",\"formula_after\":\"0\",\"formula_X_axis_show\":\"0\",\"formula_Full_Period\":\"0\",\"formula_input\":\"{RC}/60\"},\"form_name_show\":\"0\"},\"6_6_S9\":{\"_2\":{\"name\":\"Dauer (Training / Wettkampf)\",\"sel_val\":\"6|6_S9|2\",\"base_form_id\":\"6\",\"form_id\":\"6_S9\",\"form_name\":\"Training Load (Session-RPE Methode) (Training Load (nicht sichtbar))\",\"field_name\":\"Dauer (Training / Wettkampf)\",\"field_type\":\"_Number\",\"field_num\":\"2\",\"cell_id\":\"RC\",\"data_or_calc\":\"data\",\"show\":\"0\",\"type\":\"line\",\"line\":\"Solid\",\"p_range\":\"0\",\"color\":\"\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_1\",\"ath_id\":\"6\"},\"_3\":{\"name\":\"Rating of Perceived Exertion (RPE)\",\"sel_val\":\"6|6_S9|3\",\"base_form_id\":\"6\",\"form_id\":\"6_S9\",\"form_name\":\"Training Load (Session-RPE Methode) (Training Load (nicht sichtbar))\",\"field_name\":\"Rating of Perceived Exertion (RPE)\",\"field_type\":\"_RadioButtons\",\"field_num\":\"3\",\"cell_id\":\"RE\",\"data_or_calc\":\"data\",\"show\":\"0\",\"type\":\"line\",\"line\":\"Solid\",\"p_range\":\"0\",\"color\":\"\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_1\",\"ath_id\":\"6\"},\"_B1\":{\"name\":\"Training Load (Session-RPE Methode)\",\"sel_val\":\"6|6_S9|1\",\"base_form_id\":\"6\",\"form_id\":\"6_S9\",\"form_name\":\"Training Load (Session-RPE Methode) (Training Load (nicht sichtbar))\",\"field_name\":\"Formula1\",\"field_type\":\"_Number\",\"field_num\":\"B1\",\"cell_id\":\"BA\",\"data_or_calc\":\"calc\",\"show\":\"0\",\"type\":\"column\",\"line\":\"Solid\",\"p_range\":\"0\",\"color\":\"rgb(124,181,236)\",\"markers\":\"null\",\"labels\":\"false\",\"axis\":\"axis_6\",\"ath_id\":\"6\",\"formula_cells\":\"1\",\"formula_period\":\"lines\",\"formula_after\":\"0\",\"formula_X_axis_show\":\"0\",\"formula_Full_Period\":\"0\",\"formula_input\":\"{RC}*{RE}\"},\"form_name_show\":\"0\"}},\"Data_Intervals\":{\"1\":{\"formula_cells\":\"1\",\"formula_period\":\"weeks\",\"formula_X_axis_show\":\"1\",\"data\":{\"_1\":{\"int_id\":\"1\",\"name\":\"Schlafdauer (wöchentlich gemittelt)\",\"sel_val\":\"1|1\",\"field_id\":\"1\",\"field_name\":\"Formula1\",\"show\":\"1\",\"show_name\":\"0\",\"type\":\"spline\",\"line\":\"Dash\",\"p_range\":\"0\",\"color\":\"#000000\",\"markers\":\"true\",\"labels\":\"true\",\"axis\":\"axis_4\",\"is_single_column\":\"false\",\"is_interval_form\":\"false\",\"interval_form\":\"5_S4\",\"interval_form_name\":\"Schlafdokumentation (kurz) (Schlafdauer und -qualität)\",\"formula_individual\":\"0\",\"formula_input\":\"average({BD})\",\"formula_sub_period\":\"0\"},\"_2\":{\"int_id\":\"1\",\"name\":\"Training Load (wöchentlich aggregiert)\",\"sel_val\":\"1|2\",\"field_id\":\"2\",\"field_name\":\"Formula2\",\"show\":\"1\",\"show_name\":\"1\",\"type\":\"column\",\"line\":\"Solid\",\"p_range\":\"0\",\"color\":\"rgba(124,181,236,0.2)\",\"markers\":\"null\",\"labels\":\"true\",\"axis\":\"axis_7\",\"is_single_column\":\"false\",\"is_interval_form\":\"false\",\"interval_form\":\"6_S9\",\"interval_form_name\":\"Training Load (Session-RPE Methode) (Training Load (nicht sichtbar))\",\"formula_individual\":\"0\",\"formula_input\":\"sum({BA})\",\"formula_sub_period\":\"0\"}}}},\"users_show_name\":{\"6\":\"0\"}}', '".$datetime."', 'Auto_Init','".$datetime."', 'Auto_Init');";
}

function get_Sports_Init_SQL_EN(string $datetime):string {
	return "".
		// sports sample data [english]
		"INSERT INTO sports (id, parent_id, `name`, options, `status`, created, modified) VALUES ".
		"(1, 0, 'Without Sport Group', NULL, 1, '".$datetime."', '".$datetime."'),".
		"(2, 0, 'Strength and Fitness', NULL, 1, '".$datetime."', '".$datetime."'),".
		"(3, 0, 'Individual Sports', NULL, 1, '".$datetime."', '".$datetime."'),".
		"(4, 0, 'Team Sports', NULL, 1, '".$datetime."', '".$datetime."'),".
		"(5, 0, 'Trend Sports', NULL, 1, '".$datetime."', '".$datetime."'),".
		"(6, 0, 'Martial Arts', NULL, 1, '".$datetime."', '".$datetime."'),".
		"(7, 0, 'Racquet Games', NULL, 1, '".$datetime."', '".$datetime."'),".
		"(11, 1, NULL, 'Nothing', 1, '".$datetime."', '".$datetime."'),".
		"(12, 1, NULL, 'Figure Skating', 1, '".$datetime."', '".$datetime."'),".
		"(13, 1, NULL, 'Artistic Cycling', 1, '".$datetime."', '".$datetime."'),".
		"(14, 1, NULL, 'Dance', 1, '".$datetime."', '".$datetime."'),".
		"(15, 1, NULL, 'Rowing', 1, '".$datetime."', '".$datetime."'),".
		"(16, 1, NULL, 'Sports Student', 1, '".$datetime."', '".$datetime."'),".
		"(17, 2, NULL, 'Fitness', 1, '".$datetime."', '".$datetime."'),".
		"(18, 2, NULL, 'Weight Training', 1, '".$datetime."', '".$datetime."'),".
		"(19, 2, NULL, 'Weight Training', 1, '".$datetime."', '".$datetime."'),".
		"(20, 2, NULL, 'Strength-Fitness', 1, '".$datetime."', '".$datetime."'),".
		"(21, 3, NULL, 'Vaulting', 1, '".$datetime."', '".$datetime."'),".
		"(22, 3, NULL, 'Weightlifting', 1, '".$datetime."', '".$datetime."'),".
		"(23, 3, NULL, 'Crossfit', 1, '".$datetime."', '".$datetime."'),".
		"(24, 3, NULL, 'Kickboxing', 1, '".$datetime."', '".$datetime."'),".
		"(25, 3, NULL, 'Apparatus Gymnastics', 1, '".$datetime."', '".$datetime."'),".
		"(26, 3, NULL, 'Modern Pentathlon', 1, '".$datetime."', '".$datetime."'),".
		"(27, 3, NULL, 'Canoe', 1, '".$datetime."', '".$datetime."'),".
		"(28, 3, NULL, 'Climb', 1, '".$datetime."', '".$datetime."'),".
		"(29, 3, NULL, 'Artistic Gymnastics', 1, '".$datetime."', '".$datetime."'),".
		"(30, 3, NULL, 'Jogging / Running', 1, '".$datetime."', '".$datetime."'),".
		"(31, 3, NULL, 'Athletics', 1, '".$datetime."', '".$datetime."'),".
		"(32, 3, NULL, 'Mountain Bike', 1, '".$datetime."', '".$datetime."'),".
		"(33, 3, NULL, 'Cycling', 1, '".$datetime."', '".$datetime."'),".
		"(34, 3, NULL, 'Swimming', 1, '".$datetime."', '".$datetime."'),".
		"(35, 3, NULL, 'Gymnastics', 1, '".$datetime."', '".$datetime."'),".
		"(36, 3, NULL, 'Triathlon', 1, '".$datetime."', '".$datetime."'),".
		"(37, 4, NULL, 'Basketball', 1, '".$datetime."', '".$datetime."'),".
		"(38, 4, NULL, 'Football', 1, '".$datetime."', '".$datetime."'),".
		"(39, 4, NULL, 'Handball', 1, '".$datetime."', '".$datetime."'),".
		"(40, 4, NULL, 'Water Polo', 1, '".$datetime."', '".$datetime."'),".
		"(41, 4, NULL, 'Ice Hockey', 1, '".$datetime."', '".$datetime."'),".
		"(42, 5, NULL, 'Curling', 1, '".$datetime."', '".$datetime."'),".
		"(43, 5, NULL, 'Paragliding', 1, '".$datetime."', '".$datetime."'),".
		"(44, 5, NULL, 'Kite Surfing', 1, '".$datetime."', '".$datetime."'),".
		"(45, 5, NULL, 'Windsurfing', 1, '".$datetime."', '".$datetime."'),".
		"(46, 5, NULL, 'Diving', 1, '".$datetime."', '".$datetime."'),".
		"(47, 5, NULL, 'Parkour', 1, '".$datetime."', '".$datetime."'),".
		"(48, 5, NULL, 'Snowboarding', 1, '".$datetime."', '".$datetime."'),".
		"(49, 6, NULL, 'Judo', 1, '".$datetime."', '".$datetime."'),".
		"(50, 6, NULL, 'Thai Boxing', 1, '".$datetime."', '".$datetime."'),".
		"(51, 6, NULL, 'Taekwondo', 1, '".$datetime."', '".$datetime."'),".
		"(52, 6, NULL, 'Ju-Jutsu', 1, '".$datetime."', '".$datetime."'),".
		"(53, 6, NULL, 'Jiu jitsu', 1, '".$datetime."', '".$datetime."'),".
		"(54, 7, NULL, 'Volleyball', 1, '".$datetime."', '".$datetime."'),".
		"(55, 7, NULL, 'Tennis', 1, '".$datetime."', '".$datetime."'),".
		"(56, 7, NULL, 'Table Tennis', 1, '".$datetime."', '".$datetime."'),".
		"(57, 7, NULL, 'Beachvolleyball', 1, '".$datetime."', '".$datetime."'),".
		"(58, 7, NULL, 'Badminton', 1, '".$datetime."', '".$datetime."');";
}

function get_Sports_Init_SQL_DE(string $datetime):string {
	return "" .
		// sports sample data [german]
		"INSERT INTO sports (id, parent_id, `name`, options, `status`, created, modified) " . "VALUES" .
		"(1, 0, 'Ohne Zuordnung', NULL, 1, '".$datetime."', '".$datetime."')," .
		"(2, 0, 'Kraft und Fitness', NULL, 1, '".$datetime."', '".$datetime."')," .
		"(3, 0, 'Individualsportarten', NULL, 1, '".$datetime."', '".$datetime."')," .
		"(4, 0, 'Mannschaftssportarten', NULL, 1, '".$datetime."', '".$datetime."')," .
		"(5, 0, 'Trendsportarten', NULL, 1, '".$datetime."', '".$datetime."')," .
		"(6, 0, 'Kampfsport', NULL, 1, '".$datetime."', '".$datetime."')," .
		"(7, 0, 'Rückschlagspiele', NULL, 1, '".$datetime."', '".$datetime."')," .
		"(11, 1, NULL, 'Keine', 1, '".$datetime."', '".$datetime."')," .
		"(12, 1, NULL, 'Eiskunstlauf', 1, '".$datetime."', '".$datetime."')," .
		"(13, 1, NULL, 'Kunstradfahren', 1, '".$datetime."', '".$datetime."')," .
		"(14, 1, NULL, 'Tanzen', 1, '".$datetime."', '".$datetime."')," .
		"(15, 1, NULL, 'Rudern', 1, '".$datetime."', '".$datetime."')," .
		"(16, 1, NULL, 'Sportstudent', 1, '".$datetime."', '".$datetime."')," .
		"(17, 2, NULL, 'Fitness', 1, '".$datetime."', '".$datetime."')," .
		"(18, 2, NULL, 'Kraftsport', 1, '".$datetime."', '".$datetime."')," .
		"(19, 2, NULL, 'Krafttraining', 1, '".$datetime."', '".$datetime."')," .
		"(20, 2, NULL, 'Kraft-Fitness', 1, '".$datetime."', '".$datetime."')," .
		"(21, 3, NULL, 'Voltigieren', 1, '".$datetime."', '".$datetime."')," .
		"(22, 3, NULL, 'Gewichtheben', 1, '".$datetime."', '".$datetime."')," .
		"(23, 3, NULL, 'Crossfit', 1, '".$datetime."', '".$datetime."')," .
		"(24, 3, NULL, 'Kickboxen', 1, '".$datetime."', '".$datetime."')," .
		"(25, 3, NULL, 'Geräteturnen', 1, '".$datetime."', '".$datetime."')," .
		"(26, 3, NULL, 'Moderner Fünfkampf', 1, '".$datetime."', '".$datetime."')," .
		"(27, 3, NULL, 'Kanu', 1, '".$datetime."', '".$datetime."')," .
		"(28, 3, NULL, 'Klettern', 1, '".$datetime."', '".$datetime."')," .
		"(29, 3, NULL, 'Kunstturnen', 1, '".$datetime."', '".$datetime."')," .
		"(30, 3, NULL, 'Jogging / Laufen', 1, '".$datetime."', '".$datetime."')," .
		"(31, 3, NULL, 'Leichtathletik', 1, '".$datetime."', '".$datetime."')," .
		"(32, 3, NULL, 'Mountainbike', 1, '".$datetime."', '".$datetime."')," .
		"(33, 3, NULL, 'Radsport', 1, '".$datetime."', '".$datetime."')," .
		"(34, 3, NULL, 'Schwimmen', 1, '".$datetime."', '".$datetime."')," .
		"(35, 3, NULL, 'Turnen', 1, '".$datetime."', '".$datetime."')," .
		"(36, 3, NULL, 'Triathlon', 1, '".$datetime."', '".$datetime."')," .
		"(37, 4, NULL, 'Basketball', 1, '".$datetime."', '".$datetime."')," .
		"(38, 4, NULL, 'Fußball', 1, '".$datetime."', '".$datetime."')," .
		"(39, 4, NULL, 'Handball', 1, '".$datetime."', '".$datetime."')," .
		"(40, 4, NULL, 'Wasserball', 1, '".$datetime."', '".$datetime."')," .
		"(41, 4, NULL, 'Eishockey', 1, '".$datetime."', '".$datetime."')," .
		"(42, 5, NULL, 'Curling', 1, '".$datetime."', '".$datetime."')," .
		"(43, 5, NULL, 'Paragliding', 1, '".$datetime."', '".$datetime."')," .
		"(44, 5, NULL, 'Kitesurfen', 1, '".$datetime."', '".$datetime."')," .
		"(45, 5, NULL, 'Windsurfen', 1, '".$datetime."', '".$datetime."')," .
		"(46, 5, NULL, 'Tauchen', 1, '".$datetime."', '".$datetime."')," .
		"(47, 5, NULL, 'Parkour', 1, '".$datetime."', '".$datetime."')," .
		"(48, 5, NULL, 'Snowboarding', 1, '".$datetime."', '".$datetime."')," .
		"(49, 6, NULL, 'Judo', 1, '".$datetime."', '".$datetime."')," .
		"(50, 6, NULL, 'Thaiboxen', 1, '".$datetime."', '".$datetime."')," .
		"(51, 6, NULL, 'Taekwondo', 1, '".$datetime."', '".$datetime."')," .
		"(52, 6, NULL, 'Ju-Jutsu', 1, '".$datetime."', '".$datetime."')," .
		"(53, 6, NULL, 'Jiu jitsu', 1, '".$datetime."', '".$datetime."')," .
		"(54, 7, NULL, 'Volleyball', 1, '".$datetime."', '".$datetime."')," .
		"(55, 7, NULL, 'Tennis', 1, '".$datetime."', '".$datetime."')," .
		"(56, 7, NULL, 'Tischtennis', 1, '".$datetime."', '".$datetime."')," .
		"(57, 7, NULL, 'Beachvolleyball', 1, '".$datetime."', '".$datetime."')," .
		"(58, 7, NULL, 'Badminton', 1, '".$datetime."', '".$datetime."');";
}

function get_Extra_DropdownsSample_Init_SQL(string $datetime):string {
	return "".
		// dropdowns sample data [german/english]
		"INSERT INTO dropdowns (id, parent_id, `name`, options, `status`, created, modified) VALUES ".
		"(1, 0, 'Dropdown Demo StringValue', NULL, 1, '".$datetime."', '".$datetime."'),".
		"(2, 1, NULL, 'Option 1', 1, '".$datetime."', '".$datetime."'),".
		"(3, 1, NULL, 'Option 2', 1, '".$datetime."', '".$datetime."'),".
		"(4, 1, NULL, 'Option 3', 1, '".$datetime."', '".$datetime."'),".
		"(5, 0, 'Dropdown Demo Value__String', NULL, 1, '".$datetime."', '".$datetime."'),".
		"(6, 5, NULL, '1__Option 1', 1, '".$datetime."', '".$datetime."'),".
		"(7, 5, NULL, '2__Option 2', 1, '".$datetime."', '".$datetime."'),".
		"(8, 5, NULL, '3__Option 3', 1, '".$datetime."', '".$datetime."'),".
		"(9, 0, 'rating scale (0-6) [german AEB/KEB]', NULL, 1, '".$datetime."', '".$datetime."'),".
		"(10, 9, NULL, '0__trifft gar nicht zu', 1, '".$datetime."', '".$datetime."'),".
		"(11, 9, NULL, '1__', 1, '".$datetime."', '".$datetime."'),".
		"(12, 9, NULL, '2__', 1, '".$datetime."', '".$datetime."'),".
		"(13, 9, NULL, '3__', 1, '".$datetime."', '".$datetime."'),".
		"(14, 9, NULL, '4__', 1, '".$datetime."', '".$datetime."'),".
		"(15, 9, NULL, '5__', 1, '".$datetime."', '".$datetime."'),".
		"(16, 9, NULL, '6__trifft voll zu', 1, '".$datetime."', '".$datetime."'),".
		"(17, 0, 'rating scale (1-9) [german v1]', NULL, 1, '".$datetime."', '".$datetime."'),".
		"(18, 17, NULL, '1__sehr schlecht', 1, '".$datetime."', '".$datetime."'),".
		"(19, 17, NULL, '2__', 1, '".$datetime."', '".$datetime."'),".
		"(20, 17, NULL, '3__schlecht', 1, '".$datetime."', '".$datetime."'),".
		"(21, 17, NULL, '4__', 1, '".$datetime."', '".$datetime."'),".
		"(22, 17, NULL, '5__mittelmäßig', 1, '".$datetime."', '".$datetime."'),".
		"(23, 17, NULL, '6__', 1, '".$datetime."', '".$datetime."'),".
		"(24, 17, NULL, '7__gut', 1, '".$datetime."', '".$datetime."'),".
		"(25, 17, NULL, '8__', 1, '".$datetime."', '".$datetime."'),".
		"(26, 17, NULL, '9__sehr gut', 1, '".$datetime."', '".$datetime."'),".
		"(27, 0, 'RPE CR-10 [german]', NULL, 1, '".$datetime."', '".$datetime."'),".
		"(28, 27, NULL, '0__Ruhe', 1, '".$datetime."', '".$datetime."'),".
		"(29, 27, NULL, '1__Sehr leicht', 1, '".$datetime."', '".$datetime."'),".
		"(30, 27, NULL, '2__Leicht', 1, '".$datetime."', '".$datetime."'),".
		"(31, 27, NULL, '3__Moderat', 1, '".$datetime."', '".$datetime."'),".
		"(32, 27, NULL, '4__Schon Härter', 1, '".$datetime."', '".$datetime."'),".
		"(33, 27, NULL, '5__Hart', 1, '".$datetime."', '".$datetime."'),".
		"(34, 27, NULL, '6__', 1, '".$datetime."', '".$datetime."'),".
		"(35, 27, NULL, '7__Sehr hart', 1, '".$datetime."', '".$datetime."'),".
		"(36, 27, NULL, '8__Wirklich sehr hart', 1, '".$datetime."', '".$datetime."'),".
		"(37, 27, NULL, '9__', 1, '".$datetime."', '".$datetime."'),".
		"(38, 27, NULL, '10__Maximal (mehr geht nicht)', 1, '".$datetime."', '".$datetime."'),".
		"(39, 0, 'training/competition [german]', NULL, 1, '".$datetime."', '".$datetime."'),".
		"(40, 39, NULL, '1__Training', 1, '".$datetime."', '".$datetime."'),".
		"(41, 39, NULL, '2__Wettkampf', 1, '".$datetime."', '".$datetime."');";
}

/*function get_Extra_NotesSampleData_Init_SQL__NEW_way(string $datetime):string {
	$max_Date = strtotime('2023-09-29'); //the latest date in the data we want to install
	$current_Date = strtotime(date('Y-m-d', strtotime($datetime))); //get only the date
	$addTime = $current_Date - $max_Date; //the difference in seconds between current_Date and max_Date

	return "".
		"INSERT INTO notes (id, user_id, group_id, isAllDay, showInGraph, `name`, notes, color, timestamp_start, timestamp_end, modified) VALUES ".
		"(1, 6, 1, 1, 0, 'Urlaub', NULL, 'rgba(238,238,238,0.5)', '".addTime($addTime, '2023-06-05 00:00')."', '".addTime($addTime, '2023-06-05 23:59')."', '".addTime($addTime, '2023-09-29 17:32:30')."'),".
		"(2, 6, 1, 1, 0, 'Schulterprellung', NULL, 'rgba(23,144,190,1)', '".addTime($addTime, '2023-07-12 00:00')."', '".addTime($addTime, '2023-07-12 23:59')."', '".addTime($addTime, '2023-09-29 17:32:30')."'),".
		"(3, 6, 1, 1, 0, 'Urlaub am Bodensee', NULL, 'rgba(238,238,238,0.5)', '".addTime($addTime, '2023-09-09 00:00')."', '".addTime($addTime, '2023-09-09 23:59')."', '".addTime($addTime, '2023-09-29 17:32:30')."'),".
		"(4, 6, 1, 0, 1, 'Test_Note_Name', 'Test_notes', 'rgb(165,165,165)', '".addTime($addTime, '2023-09-29 07:45')."', '".addTime($addTime, '2023-09-29 08:15')."', '".addTime($addTime, '2023-09-29 17:32:30')."');";
}
*/

function get_Extra_NotesSampleData_Init_SQL(string $datetime):string {

	$DaysOffsetFix = "159";

	$DaysOffset   = ['43', '80', '139', '159'];
	$DaysDuration = ['13', '11', '11', '0'];
	$timesStart   = ['00:00', '00:00', '00:00', '07:45'];
	$timesEnd     = ['23:59', '23:59', '23:59', '08:15'];

	// create string array with the date of variable $datetimeStart shifted by DaysOffsetFix + DaysOffSet, complemented by timesStart
	$datetimeStart = array_map(function($d) use ($DaysOffset, $timesStart,$DaysOffsetFix) {
		$datetimeStart = new DateTime();
		$datetimeStart->modify('-'.$DaysOffsetFix.' days');
		$datetimeStart->add(new DateInterval('P'.$DaysOffset[$d].'D'));
		return $datetimeStart->format('Y-m-d').' '.$timesStart[$d];
	}, array_keys($DaysOffset));

	// create string array with the date of variable $datetimeStart shifted by DaysOffsetFix + DaysOffSet, complemented by timesEnd
	$datetimeEnd = array_map(function($d) use ($DaysOffset, $DaysDuration, $timesEnd, $DaysOffsetFix) {
		$datetimeEnd = new DateTime();
		$datetimeEnd->modify('-'.$DaysOffsetFix.' days');
		$datetimeEnd->add(new DateInterval('P'.($DaysOffset[$d]+$DaysDuration[$d]).'D'));
		return $datetimeEnd->format('Y-m-d').' '.$timesEnd[$d];
	}, array_keys($DaysOffset));

	return "".
		// sample notes data (for user-id 6 = "Athlete1") [german]
		"INSERT INTO notes (id, user_id, group_id, isAllDay, showInGraph, `name`, notes, color, timestamp_start, timestamp_end, modified) VALUES ".
		"(1, 6, 1, 1, 0, 'Urlaub', NULL, 'rgba(238,238,238,0.5)', '".$datetimeStart[0]."', '".$datetimeEnd[0]."', '".$datetime."'),".
		"(2, 6, 1, 1, 0, 'Schulterprellung', NULL, 'rgba(23,144,190,1)', '".$datetimeStart[1]."', '".$datetimeEnd[1]."', '".$datetime."'),".
		"(3, 6, 1, 1, 0, 'Urlaub am Bodensee', NULL, 'rgba(238,238,238,0.5)', '".$datetimeStart[2]."', '".$datetimeEnd[2]."', '".$datetime."'),".
		"(4, 6, 1, 0, 1, 'Test_Note_Name', 'Test_notes', 'rgb(165,165,165)', '".$datetimeStart[3]."', '".$datetimeEnd[3]."', '".$datetime."');";		
}

function get_Extra_FormsSampleData_Init_SQL(string $datetime):string {

	$DaysOffsetFix = "159";

	$DaysOffset = ['0', '0', '1', '1', '2', '2', '2', '3', '3', '4', '4', '5', '5', '5', '6', '6', '7', '7', '7', '8', '8', '9', '9', '10', '10', '11', '11', '12', '12', '13', '13', '13', '14', '14', '15', '15', '16', '16', '17', '17', '18', '18', '19', '19', '20', '20', '21', '21', '22', '22', '23', '23', '24', '24', '25', '25', '26', '26', '27', '27', '28', '28', '29', '29', '30', '30', '31', '31', '31', '32', '32', '33', '33', '34', '34', '35', '35', '35', '36', '36', '37', '37', '38', '38', '38', '39', '39', '40', '40', '41', '41', '41', '42', '42', '43', '43', '44', '44', '45', '45', '46', '46', '47', '47', '48', '48', '49', '49', '50', '50', '51', '51', '52', '52', '53', '53', '54', '54', '54', '55', '55', '56', '56', '57', '57', '58', '58', '59', '59', '60', '60', '61', '61', '61', '62', '62', '63', '63', '64', '64', '65', '65', '66', '66', '66', '67', '67', '68', '68', '69', '69', '70', '70', '71', '71', '72', '72', '72', '73', '73', '74', '74', '75', '75', '75', '76', '76', '77', '77', '78', '78', '78', '79', '79', '80', '80', '81', '81', '82', '82', '83', '83', '84', '84', '85', '85', '85', '86', '86', '87', '87', '88', '88', '89', '89', '90', '90', '91', '91', '92', '92', '92', '93', '93', '94', '94', '95', '95', '96', '96', '96', '97', '97', '98', '98', '99', '99', '100', '100', '101', '101', '101', '102', '102', '103', '103', '104', '104', '105', '105', '105', '106', '106', '107', '107', '108', '108', '109', '109', '110', '110', '111', '111', '112', '112', '112', '113', '113', '114', '114', '115', '115', '115', '116', '116', '117', '117', '118', '118', '119', '119', '119', '120', '120', '121', '121', '122', '122', '123', '123', '124', '124', '125', '125', '126', '126', '127', '127', '128', '128', '129', '129', '130', '130', '130', '131', '131', '132', '132', '133', '133', '133', '134', '134', '135', '135', '136', '136', '137', '137', '138', '138', '138', '139', '139', '140', '140', '141', '141', '142', '142', '145', '145', '146', '146', '147', '147', '148', '148', '149', '149', '150', '150', '151', '151', '152', '152', '153', '153', '154', '154', '154', '155', '155', '156', '156', '157', '157', '158', '158', '159', '159'];
	$timesStart = ['07:30', '08:00', '08:30', '09:00', '07:00', '07:30', '17:15', '07:29', '07:59', '06:55', '07:25', '10:35', '11:05', '14:15', '10:10', '10:40', '09:30', '10:00', '19:35', '08:07', '08:37', '07:23', '07:53', '08:02', '08:32', '07:00', '07:30', '13:30', '14:00', '11:10', '11:40', '14:00', '07:00', '07:30', '07:55', '08:25', '07:33', '08:03', '11:20', '11:50', '07:39', '08:09', '07:55', '08:25', '09:38', '10:08', '07:10', '07:40', '07:50', '08:20', '08:25', '08:55', '07:40', '08:10', '09:36', '10:06', '10:05', '10:35', '11:10', '11:40', '07:00', '07:30', '07:50', '08:20', '08:12', '08:42', '07:41', '08:11', '18:20', '07:55', '08:25', '11:35', '12:05', '11:05', '11:35', '08:20', '08:50', '19:55', '07:20', '07:50', '08:10', '08:40', '08:15', '08:45', '18:15', '07:45', '08:15', '09:30', '10:00', '10:20', '10:50', '17:50', '11:00', '11:30', '08:10', '08:40', '11:20', '11:50', '10:20', '10:50', '09:10', '09:40', '10:40', '11:10', '10:45', '11:15', '10:08', '10:38', '11:16', '11:46', '10:05', '10:35', '09:55', '10:25', '08:20', '08:50', '08:15', '08:45', '16:30', '11:15', '11:45', '08:15', '08:45', '06:55', '07:25', '08:50', '09:20', '08:20', '08:50', '08:45', '09:15', '08:50', '09:20', '15:00', '10:50', '11:20', '06:50', '07:20', '06:55', '07:25', '09:20', '09:50', '08:25', '08:55', '17:45', '08:55', '09:25', '10:45', '11:15', '08:25', '08:55', '08:40', '09:10', '08:45', '09:15', '08:50', '09:20', '19:30', '07:55', '08:25', '07:35', '08:05', '08:55', '09:25', '12:30', '10:15', '10:45', '07:45', '08:15', '07:45', '08:15', '20:00', '08:40', '09:10', '08:15', '08:45', '07:35', '08:05', '09:30', '10:00', '09:15', '09:45', '08:20', '08:50', '07:10', '07:40', '19:25', '07:20', '07:50', '08:30', '09:00', '06:55', '07:25', '09:00', '09:30', '10:45', '11:15', '07:35', '08:05', '06:55', '07:25', '19:15', '07:15', '07:45', '07:45', '08:15', '08:15', '08:45', '09:50', '10:20', '10:30', '09:55', '10:25', '07:45', '08:15', '08:20', '08:50', '08:10', '08:40', '08:25', '08:55', '20:15', '06:50', '07:20', '11:50', '12:20', '09:25', '09:55', '08:00', '08:30', '20:15', '06:55', '07:25', '08:25', '08:55', '06:50', '07:20', '10:05', '10:35', '11:00', '11:30', '09:00', '09:30', '07:45', '08:15', '20:20', '08:05', '08:35', '08:25', '08:55', '07:45', '08:15', '18:45', '08:30', '09:00', '10:00', '10:30', '10:40', '11:10', '07:20', '07:50', '20:20', '06:55', '07:25', '08:03', '08:33', '07:19', '07:49', '07:45', '08:15', '10:00', '10:30', '09:45', '10:15', '07:52', '08:22', '07:00', '07:30', '08:05', '08:35', '08:20', '08:50', '06:55', '07:25', '20:00', '07:05', '07:35', '10:10', '10:40', '07:40', '08:10', '20:30', '08:15', '08:45', '08:10', '08:40', '07:00', '07:30', '07:10', '07:40', '11:00', '11:30', '17:15', '08:55', '09:25', '09:00', '09:30', '09:45', '10:15', '11:00', '11:30', '13:00', '13:30', '09:07', '09:38', '11:07', '11:37', '09:50', '10:20', '08:30', '09:00', '08:20', '08:50', '09:00', '09:30', '08:45', '09:15', '11:30', '12:00', '07:15', '07:45', '20:15', '07:10', '07:40', '07:00', '07:30', '07:15', '07:45', '07:13', '07:43', '09:30', '10:00'];
	$timesEnd   = ['08:00', '08:30', '09:00', '09:30', '07:30', '08:00', '18:00', '07:59', '08:29', '07:25', '07:55', '11:05', '11:35', '15:00', '10:40', '11:10', '10:00', '10:30', '20:30', '08:37', '09:07', '07:53', '08:23', '08:32', '09:02', '07:30', '08:00', '14:00', '14:30', '11:40', '12:10', '16:00', '07:30', '08:00', '08:25', '08:55', '08:03', '08:33', '11:50', '12:20', '08:09', '08:39', '08:25', '08:55', '10:08', '10:38', '07:40', '08:10', '08:20', '08:50', '08:55', '09:25', '08:10', '08:40', '10:06', '10:36', '10:35', '11:05', '11:40', '12:10', '07:30', '08:00', '08:20', '08:50', '08:42', '09:12', '08:11', '08:41', '19:25', '08:25', '08:55', '12:05', '12:35', '11:35', '12:05', '08:50', '09:20', '20:35', '07:50', '08:20', '08:40', '09:10', '08:45', '09:15', '19:25', '08:15', '08:45', '10:00', '10:30', '10:50', '11:20', '18:35', '11:30', '12:00', '08:40', '09:10', '11:50', '12:20', '10:50', '11:20', '09:40', '10:10', '11:10', '11:40', '11:15', '11:45', '10:38', '11:08', '11:46', '12:16', '10:35', '11:05', '10:25', '10:55', '08:50', '09:20', '08:45', '09:15', '19:00', '11:45', '12:15', '08:45', '09:15', '07:25', '07:55', '09:20', '09:50', '08:50', '09:20', '09:15', '09:45', '09:20', '09:50', '17:30', '11:20', '11:50', '07:20', '07:50', '07:25', '07:55', '09:50', '10:20', '08:55', '09:25', '18:50', '09:25', '09:55', '11:15', '11:45', '08:55', '09:25', '09:10', '09:40', '09:15', '09:45', '09:20', '09:50', '20:15', '08:25', '08:55', '08:05', '08:35', '09:25', '09:55', '16:55', '10:45', '11:15', '08:15', '08:45', '08:15', '08:45', '22:00', '09:10', '09:40', '08:45', '09:15', '08:05', '08:35', '10:00', '10:30', '09:45', '10:15', '08:50', '09:20', '07:40', '08:10', '20:40', '07:50', '08:20', '09:00', '09:30', '07:25', '07:55', '09:30', '10:00', '11:15', '11:45', '08:05', '08:35', '07:25', '07:55', '20:20', '07:45', '08:15', '08:15', '08:45', '08:45', '09:15', '10:20', '10:50', '11:10', '10:25', '10:55', '08:15', '08:45', '08:50', '09:20', '08:40', '09:10', '08:55', '09:25', '21:00', '07:20', '07:50', '12:20', '12:50', '09:55', '10:25', '08:30', '09:00', '21:55', '07:25', '07:55', '08:55', '09:25', '07:20', '07:50', '10:35', '11:05', '11:30', '12:00', '09:30', '10:00', '08:15', '08:45', '22:00', '08:35', '09:05', '08:55', '09:25', '08:15', '08:45', '19:30', '09:00', '09:30', '10:30', '11:00', '11:10', '11:40', '07:50', '08:20', '22:00', '07:25', '07:55', '08:33', '09:03', '07:49', '08:19', '08:15', '08:45', '10:30', '11:00', '10:15', '10:45', '08:22', '08:52', '07:30', '08:00', '08:35', '09:05', '08:50', '09:20', '07:25', '07:55', '20:45', '07:35', '08:05', '10:40', '11:10', '08:10', '08:40', '21:30', '08:45', '09:15', '08:40', '09:10', '07:30', '08:00', '07:40', '08:10', '11:30', '12:00', '18:30', '09:25', '09:55', '09:30', '10:00', '10:15', '10:45', '11:30', '12:00', '13:30', '14:00', '09:37', '10:08', '11:37', '12:07', '10:20', '10:50', '09:00', '09:30', '08:50', '09:20', '09:30', '10:00', '09:15', '09:45', '12:00', '12:30', '07:45', '08:15', '21:45', '07:40', '08:10', '07:30', '08:00', '07:45', '08:15', '07:43', '08:13', '10:00', '10:30'];
		
	// create string array with the date of variable $datetimeStart shifted by DaysOffsetFix + DaysOffSet, complemented by timesStart
	$datetimeStart = array_map(function($d) use ($DaysOffset, $timesStart, $DaysOffsetFix) {
		$datetimeStart = new DateTime();
		$datetimeStart->modify('-'.$DaysOffsetFix.' days');
		$datetimeStart->add(new DateInterval('P'.$DaysOffset[$d].'D'));
		return $datetimeStart->format('Y-m-d').' '.$timesStart[$d];
	}, array_keys($DaysOffset));

	// create string array with the date of variable $datetimeStart shifted by DaysOffsetFix + DaysOffSet, complemented by timesEnd
	$datetimeEnd = array_map(function($d) use ($DaysOffset, $timesEnd, $DaysOffsetFix) {
		$datetimeEnd = new DateTime();
		$datetimeEnd->modify('-'.$DaysOffsetFix.' days');
		$datetimeEnd->add(new DateInterval('P'.$DaysOffset[$d].'D'));
		return $datetimeEnd->format('Y-m-d').' '.$timesEnd[$d];
	}, array_keys($DaysOffset));

	return "".
		// actual data for specific forms (for user-id 6 = "Athlete1") [german]
		"INSERT INTO forms_data (id, user_id, form_id, category_id, group_id, res_json, `status`, timestamp_start, timestamp_end, created_by, modified, modified_by) VALUES ".
		"(1, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"4__\",\"3\":\"3__\",\"4\":\"5__\",\"5\":\"4__\",\"6\":\"1__\",\"7\":\"2__\",\"8\":\"1__\",\"9\":\"2__\"}', 1, '".$datetimeStart[0]."', '".$datetimeEnd[0]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(2, 6, 5, 3, 1, '{\"1\":[\"22:55\",\"06:45\",\"07:50\"],\"2\":\"3__schlecht\",\"3\":\"\"}', 1, '".$datetimeStart[1]."', '".$datetimeEnd[1]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(3, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"5__\",\"3\":\"3__\",\"4\":\"4__\",\"5\":\"6__trifft voll zu\",\"6\":\"0__trifft gar nicht zu\",\"7\":\"3__\",\"8\":\"2__\",\"9\":\"0__trifft gar nicht zu\"}', 1, '".$datetimeStart[2]."', '".$datetimeEnd[2]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(4, 6, 5, 3, 1, '{\"1\":[\"23:55\",\"08:20\",\"08:25\"],\"2\":\"5__mittelmäßig\",\"3\":\"\"}', 1, '".$datetimeStart[3]."', '".$datetimeEnd[3]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(5, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"2__\",\"3\":\"1__\",\"4\":\"2__\",\"5\":\"4__\",\"6\":\"1__\",\"7\":\"4__\",\"8\":\"3__\",\"9\":\"2__\"}', 1, '".$datetimeStart[4]."', '".$datetimeEnd[4]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(6, 6, 5, 3, 1, '{\"1\":[\"22:15\",\"07:00\",\"08:45\"],\"2\":\"4__\",\"3\":\"\"}', 1, '".$datetimeStart[5]."', '".$datetimeEnd[5]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(7, 6, 6, 4, 1, '{\"1\":\"1__Training\",\"2\":\"45\",\"3\":\"7__Sehr hart\",\"4\":\"Intervalle 10x2min mit je 1min Pause, insgesamt 7,4 km (mit Polar Uhr gelaufen)\"}', 1, '".$datetimeStart[6]."', '".$datetimeEnd[6]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(8, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"4__\",\"3\":\"2__\",\"4\":\"4__\",\"5\":\"3__\",\"6\":\"3__\",\"7\":\"3__\",\"8\":\"2__\",\"9\":\"4__\"}', 1, '".$datetimeStart[7]."', '".$datetimeEnd[7]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(9, 6, 5, 3, 1, '{\"1\":[\"22:55\",\"07:10\",\"08:15\"],\"2\":\"5__mittelmäßig\",\"3\":\"\"}', 1, '".$datetimeStart[8]."', '".$datetimeEnd[8]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(10, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"5__\",\"3\":\"4__\",\"4\":\"5__\",\"5\":\"5__\",\"6\":\"1__\",\"7\":\"2__\",\"8\":\"1__\",\"9\":\"1__\"}', 1, '".$datetimeStart[9]."', '".$datetimeEnd[9]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(11, 6, 5, 3, 1, '{\"1\":[\"23:00\",\"06:45\",\"07:45\"],\"2\":\"3__schlecht\",\"3\":\"\"}', 1, '".$datetimeStart[10]."', '".$datetimeEnd[10]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(12, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"5__\",\"3\":\"3__\",\"4\":\"4__\",\"5\":\"5__\",\"6\":\"1__\",\"7\":\"1__\",\"8\":\"2__\",\"9\":\"1__\"}', 1, '".$datetimeStart[11]."', '".$datetimeEnd[11]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(13, 6, 5, 3, 1, '{\"1\":[\"01:22\",\"10:30\",\"09:08\"],\"2\":\"5__mittelmäßig\",\"3\":\"\"}', 1, '".$datetimeStart[12]."', '".$datetimeEnd[12]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(14, 6, 6, 4, 1, '{\"1\":\"1__Training\",\"2\":\"45\",\"3\":\"9__Wirklich sehr hart\",\"4\":\"Schneller 5er\"}', 1, '".$datetimeStart[13]."', '".$datetimeEnd[13]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(15, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"3__\",\"3\":\"3__\",\"4\":\"5__\",\"5\":\"3__\",\"6\":\"3__\",\"7\":\"2__\",\"8\":\"1__\",\"9\":\"4__\"}', 1, '".$datetimeStart[14]."', '".$datetimeEnd[14]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(16, 6, 5, 3, 1, '{\"1\":[\"01:05\",\"10:00\",\"08:55\"],\"2\":\"5__mittelmäßig\",\"3\":\"\"}', 1, '".$datetimeStart[15]."', '".$datetimeEnd[15]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(17, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"5__\",\"3\":\"3__\",\"4\":\"4__\",\"5\":\"5__\",\"6\":\"1__\",\"7\":\"1__\",\"8\":\"1__\",\"9\":\"1__\"}', 1, '".$datetimeStart[16]."', '".$datetimeEnd[16]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(18, 6, 5, 3, 1, '{\"1\":[\"00:05\",\"09:30\",\"09:25\"],\"2\":\"4__\",\"3\":\"\"}', 1, '".$datetimeStart[17]."', '".$datetimeEnd[17]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(19, 6, 6, 4, 1, '{\"1\":\"1__Training\",\"2\":\"55\",\"3\":\"4__Schon härter\",\"4\":\"9,5 km\"}', 1, '".$datetimeStart[18]."', '".$datetimeEnd[18]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(20, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"3__\",\"3\":\"2__\",\"4\":\"4__\",\"5\":\"4__\",\"6\":\"3__\",\"7\":\"3__\",\"8\":\"3__\",\"9\":\"3__\"}', 1, '".$datetimeStart[19]."', '".$datetimeEnd[19]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(21, 6, 5, 3, 1, '{\"1\":[\"23:15\",\"07:50\",\"08:35\"],\"2\":\"2__\",\"3\":\"\"}', 1, '".$datetimeStart[20]."', '".$datetimeEnd[20]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(22, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"3__\",\"3\":\"2__\",\"4\":\"3__\",\"5\":\"3__\",\"6\":\"2__\",\"7\":\"3__\",\"8\":\"2__\",\"9\":\"2__\"}', 1, '".$datetimeStart[21]."', '".$datetimeEnd[21]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(23, 6, 5, 3, 1, '{\"1\":[\"23:20\",\"07:05\",\"07:45\"],\"2\":\"5__mittelmäßig\",\"3\":\"\"}', 1, '".$datetimeStart[22]."', '".$datetimeEnd[22]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(24, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"4__\",\"3\":\"4__\",\"4\":\"4__\",\"5\":\"5__\",\"6\":\"1__\",\"7\":\"2__\",\"8\":\"2__\",\"9\":\"2__\"}', 1, '".$datetimeStart[23]."', '".$datetimeEnd[23]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(25, 6, 5, 3, 1, '{\"1\":[\"22:45\",\"07:40\",\"08:55\"],\"2\":\"5__mittelmäßig\",\"3\":\"\"}', 1, '".$datetimeStart[24]."', '".$datetimeEnd[24]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(26, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"4__\",\"3\":\"3__\",\"4\":\"4__\",\"5\":\"5__\",\"6\":\"1__\",\"7\":\"2__\",\"8\":\"2__\",\"9\":\"1__\"}', 1, '".$datetimeStart[25]."', '".$datetimeEnd[25]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(27, 6, 5, 3, 1, '{\"1\":[\"00:00\",\"06:50\",\"06:50\"],\"2\":\"4__\",\"3\":\"\"}', 1, '".$datetimeStart[26]."', '".$datetimeEnd[26]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(28, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"5__\",\"3\":\"5__\",\"4\":\"4__\",\"5\":\"6__trifft voll zu\",\"6\":\"0__trifft gar nicht zu\",\"7\":\"1__\",\"8\":\"1__\",\"9\":\"0__trifft gar nicht zu\"}', 1, '".$datetimeStart[27]."', '".$datetimeEnd[27]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(29, 6, 5, 3, 1, '{\"1\":[\"02:05\",\"13:15\",\"11:10\"],\"2\":\"3__schlecht\",\"3\":\"\"}', 1, '".$datetimeStart[28]."', '".$datetimeEnd[28]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(30, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"3__\",\"3\":\"2__\",\"4\":\"3__\",\"5\":\"3__\",\"6\":\"1__\",\"7\":\"2__\",\"8\":\"1__\",\"9\":\"1__\"}', 1, '".$datetimeStart[29]."', '".$datetimeEnd[29]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(31, 6, 5, 3, 1, '{\"1\":[\"03:30\",\"11:10\",\"07:40\"],\"2\":\"6__\",\"3\":\"\"}', 1, '".$datetimeStart[30]."', '".$datetimeEnd[30]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(32, 6, 6, 4, 1, '{\"1\":\"1__Training\",\"2\":\"120\",\"3\":\"4__Schon härter\",\"4\":\"Beach-Volleyball\"}', 1, '".$datetimeStart[31]."', '".$datetimeEnd[31]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(33, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"2__\",\"3\":\"3__\",\"4\":\"3__\",\"5\":\"1__\",\"6\":\"5__\",\"7\":\"4__\",\"8\":\"2__\",\"9\":\"5__\"}', 1, '".$datetimeStart[32]."', '".$datetimeEnd[32]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(34, 6, 5, 3, 1, '{\"1\":[\"23:15\",\"06:55\",\"07:40\"],\"2\":\"6__\",\"3\":\"\"}', 1, '".$datetimeStart[33]."', '".$datetimeEnd[33]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(35, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"3__\",\"3\":\"2__\",\"4\":\"4__\",\"5\":\"3__\",\"6\":\"4__\",\"7\":\"2__\",\"8\":\"1__\",\"9\":\"4__\"}', 1, '".$datetimeStart[34]."', '".$datetimeEnd[34]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(36, 6, 5, 3, 1, '{\"1\":[\"23:20\",\"07:45\",\"08:25\"],\"2\":\"5__mittelmäßig\",\"3\":\"\"}', 1, '".$datetimeStart[35]."', '".$datetimeEnd[35]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(37, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"2__\",\"3\":\"2__\",\"4\":\"1__\",\"5\":\"3__\",\"6\":\"2__\",\"7\":\"5__\",\"8\":\"5__\",\"9\":\"4__\"}', 1, '".$datetimeStart[36]."', '".$datetimeEnd[36]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(38, 6, 5, 3, 1, '{\"1\":[\"23:40\",\"07:15\",\"07:35\"],\"2\":\"6__\",\"3\":\"\"}', 1, '".$datetimeStart[37]."', '".$datetimeEnd[37]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(39, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"6__trifft voll zu\",\"3\":\"6__trifft voll zu\",\"4\":\"5__trifft gar nicht zu\",\"5\":\"5__\",\"6\":\"0__trifft gar nicht zu\",\"7\":\"1__\",\"8\":\"0__trifft gar nicht zu\",\"9\":\"0__trifft gar nicht zu\"}', 1, '".$datetimeStart[38]."', '".$datetimeEnd[38]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(40, 6, 5, 3, 1, '{\"1\":[\"23:20\",\"11:00\",\"11:40\"],\"2\":\"3__schlecht\",\"3\":\"\"}', 1, '".$datetimeStart[39]."', '".$datetimeEnd[39]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(41, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"4__\",\"3\":\"5__\",\"4\":\"5__\",\"5\":\"4__\",\"6\":\"0__trifft gar nicht zu\",\"7\":\"2__\",\"8\":\"1__\",\"9\":\"1__\"}', 1, '".$datetimeStart[40]."', '".$datetimeEnd[40]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(42, 6, 5, 3, 1, '{\"1\":[\"23:40\",\"06:45\",\"07:05\"],\"2\":\"5__mittelmäßig\",\"3\":\"\"}', 1, '".$datetimeStart[41]."', '".$datetimeEnd[41]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(43, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"2__\",\"3\":\"1__\",\"4\":\"3__\",\"5\":\"2__\",\"6\":\"1__\",\"7\":\"4__\",\"8\":\"3__\",\"9\":\"5__\"}', 1, '".$datetimeStart[42]."', '".$datetimeEnd[42]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(44, 6, 5, 3, 1, '{\"1\":[\"01:50\",\"07:45\",\"05:55\"],\"2\":\"8__\",\"3\":\"\"}', 1, '".$datetimeStart[43]."', '".$datetimeEnd[43]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(45, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"4__\",\"3\":\"4__\",\"4\":\"5__\",\"5\":\"5__\",\"6\":\"0__trifft gar nicht zu\",\"7\":\"2__\",\"8\":\"1__\",\"9\":\"0__trifft gar nicht zu\"}', 1, '".$datetimeStart[44]."', '".$datetimeEnd[44]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(46, 6, 5, 3, 1, '{\"1\":[\"23:10\",\"09:30\",\"10:20\"],\"2\":\"5__mittelmäßig\",\"3\":\"\"}', 1, '".$datetimeStart[45]."', '".$datetimeEnd[45]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(47, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"4__\",\"3\":\"3__\",\"4\":\"4__\",\"5\":\"4__\",\"6\":\"0__trifft gar nicht zu\",\"7\":\"3__\",\"8\":\"2__\",\"9\":\"1__\"}', 1, '".$datetimeStart[46]."', '".$datetimeEnd[46]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(48, 6, 5, 3, 1, '{\"1\":[\"23:35\",\"07:05\",\"07:30\"],\"2\":\"4__\",\"3\":\"\"}', 1, '".$datetimeStart[47]."', '".$datetimeEnd[47]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(49, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"2__\",\"3\":\"3__\",\"4\":\"4__\",\"5\":\"4__\",\"6\":\"0__trifft gar nicht zu\",\"7\":\"4__\",\"8\":\"2__\",\"9\":\"1__\"}', 1, '".$datetimeStart[48]."', '".$datetimeEnd[48]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(50, 6, 5, 3, 1, '{\"1\":[\"00:30\",\"07:45\",\"07:15\"],\"2\":\"6__\",\"3\":\"\"}', 1, '".$datetimeStart[49]."', '".$datetimeEnd[49]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(51, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"4__\",\"3\":\"2__\",\"4\":\"5__trifft gar nicht zu\",\"5\":\"5__\",\"6\":\"0__trifft gar nicht zu\",\"7\":\"2__\",\"8\":\"0__trifft gar nicht zu\",\"9\":\"1__\"}', 1, '".$datetimeStart[50]."', '".$datetimeEnd[50]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(52, 6, 5, 3, 1, '{\"1\":[\"00:45\",\"08:15\",\"07:30\"],\"2\":\"4__\",\"3\":\"\"}', 1, '".$datetimeStart[51]."', '".$datetimeEnd[51]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(53, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"5__\",\"3\":\"5__\",\"4\":\"5__\",\"5\":\"5__\",\"6\":\"0__trifft gar nicht zu\",\"7\":\"2__\",\"8\":\"3__\",\"9\":\"2__\"}', 1, '".$datetimeStart[52]."', '".$datetimeEnd[52]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(54, 6, 5, 3, 1, '{\"1\":[\"23:45\",\"07:35\",\"07:50\"],\"2\":\"3__schlecht\",\"3\":\"\"}', 1, '".$datetimeStart[53]."', '".$datetimeEnd[53]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(55, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"6__trifft voll zu\",\"3\":\"5__\",\"4\":\"4__\",\"5\":\"6__trifft voll zu\",\"6\":\"0__trifft gar nicht zu\",\"7\":\"1__\",\"8\":\"2__\",\"9\":\"0__trifft gar nicht zu\"}', 1, '".$datetimeStart[54]."', '".$datetimeEnd[54]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(56, 6, 5, 3, 1, '{\"1\":[\"22:35\",\"08:05\",\"09:30\"],\"2\":\"5__mittelmäßig\",\"3\":\"\"}', 1, '".$datetimeStart[55]."', '".$datetimeEnd[55]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(57, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"1__\",\"3\":\"0__trifft gar nicht zu\",\"4\":\"1__\",\"5\":\"1__\",\"6\":\"0__trifft gar nicht zu\",\"7\":\"6__trifft voll zu\",\"8\":\"4__\",\"9\":\"5__\"}', 1, '".$datetimeStart[56]."', '".$datetimeEnd[56]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(58, 6, 5, 3, 1, '{\"1\":[\"23:16\",\"10:00\",\"10:44\"],\"2\":\"4__\",\"3\":\"\"}', 1, '".$datetimeStart[57]."', '".$datetimeEnd[57]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(59, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"4__\",\"3\":\"2__\",\"4\":\"3__trifft gar nicht zu\",\"5\":\"3__\",\"6\":\"0__trifft gar nicht zu\",\"7\":\"2__\",\"8\":\"0__trifft gar nicht zu\",\"9\":\"1__\"}', 1, '".$datetimeStart[58]."', '".$datetimeEnd[58]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(60, 6, 5, 3, 1, '{\"1\":[\"00:50\",\"11:10\",\"10:20\"],\"2\":\"3__schlecht\",\"3\":\"\"}', 1, '".$datetimeStart[59]."', '".$datetimeEnd[59]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(61, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"5__\",\"3\":\"5__\",\"4\":\"6__trifft gar nicht zu\",\"5\":\"5__\",\"6\":\"0__trifft gar nicht zu\",\"7\":\"1__\",\"8\":\"0__trifft gar nicht zu\",\"9\":\"1__\"}', 1, '".$datetimeStart[60]."', '".$datetimeEnd[60]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(62, 6, 5, 3, 1, '{\"1\":[\"00:15\",\"06:55\",\"06:40\"],\"2\":\"3__schlecht\",\"3\":\"\"}', 1, '".$datetimeStart[61]."', '".$datetimeEnd[61]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(63, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"4__\",\"3\":\"3__\",\"4\":\"3__\",\"5\":\"4__\",\"6\":\"0__trifft gar nicht zu\",\"7\":\"2__\",\"8\":\"2__\",\"9\":\"1__\"}', 1, '".$datetimeStart[62]."', '".$datetimeEnd[62]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(64, 6, 5, 3, 1, '{\"1\":[\"00:15\",\"07:45\",\"07:30\"],\"2\":\"6__\",\"3\":\"\"}', 1, '".$datetimeStart[63]."', '".$datetimeEnd[63]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(65, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"5__\",\"3\":\"4__\",\"4\":\"6__trifft gar nicht zu\",\"5\":\"5__\",\"6\":\"0__trifft gar nicht zu\",\"7\":\"0__trifft gar nicht zu\",\"8\":\"0__trifft gar nicht zu\",\"9\":\"1__\"}', 1, '".$datetimeStart[64]."', '".$datetimeEnd[64]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(66, 6, 5, 3, 1, '{\"1\":[\"22:55\",\"08:05\",\"09:10\"],\"2\":\"2__\",\"3\":\"\"}', 1, '".$datetimeStart[65]."', '".$datetimeEnd[65]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(67, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"4__\",\"3\":\"4__\",\"4\":\"3__\",\"5\":\"5__\",\"6\":\"0__trifft gar nicht zu\",\"7\":\"2__\",\"8\":\"1__\",\"9\":\"1__\"}', 1, '".$datetimeStart[66]."', '".$datetimeEnd[66]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(68, 6, 5, 3, 1, '{\"1\":[\"00:20\",\"07:30\",\"07:10\"],\"2\":\"5__mittelmäßig\",\"3\":\"\"}', 1, '".$datetimeStart[67]."', '".$datetimeEnd[67]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(69, 6, 6, 4, 1, '{\"1\":\"1__Training\",\"2\":\"65\",\"3\":\"6__\",\"4\":\"10km im Gonsenheimer Wald\"}', 1, '".$datetimeStart[68]."', '".$datetimeEnd[68]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(70, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"3__\",\"3\":\"5__\",\"4\":\"6__\",\"5\":\"2__\",\"6\":\"3__\",\"7\":\"2__\",\"8\":\"1__\",\"9\":\"4__\"}', 1, '".$datetimeStart[69]."', '".$datetimeEnd[69]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(71, 6, 5, 3, 1, '{\"1\":[\"23:45\",\"07:50\",\"08:05\"],\"2\":\"3__schlecht\",\"3\":\"\"}', 1, '".$datetimeStart[70]."', '".$datetimeEnd[70]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(72, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"5__\",\"3\":\"5__\",\"4\":\"3__\",\"5\":\"4__\",\"6\":\"1__\",\"7\":\"2__\",\"8\":\"4__\",\"9\":\"1__\"}', 1, '".$datetimeStart[71]."', '".$datetimeEnd[71]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(73, 6, 5, 3, 1, '{\"1\":[\"01:40\",\"11:30\",\"09:50\"],\"2\":\"1__sehr schlecht\",\"3\":\"\"}', 1, '".$datetimeStart[72]."', '".$datetimeEnd[72]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(74, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"4__\",\"3\":\"4__\",\"4\":\"5__\",\"5\":\"3__\",\"6\":\"1__\",\"7\":\"3__\",\"8\":\"1__\",\"9\":\"2__\"}', 1, '".$datetimeStart[73]."', '".$datetimeEnd[73]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(75, 6, 5, 3, 1, '{\"1\":[\"01:45\",\"11:00\",\"09:15\"],\"2\":\"5__mittelmäßig\",\"3\":\"\"}', 1, '".$datetimeStart[74]."', '".$datetimeEnd[74]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(76, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"4__\",\"3\":\"3__\",\"4\":\"4__\",\"5\":\"4__\",\"6\":\"0__trifft gar nicht zu\",\"7\":\"3__\",\"8\":\"2__\",\"9\":\"2__\"}', 1, '".$datetimeStart[75]."', '".$datetimeEnd[75]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(77, 6, 5, 3, 1, '{\"1\":[\"23:50\",\"08:15\",\"08:25\"],\"2\":\"5__mittelmäßig\",\"3\":\"\"}', 1, '".$datetimeStart[76]."', '".$datetimeEnd[76]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(78, 6, 6, 4, 1, '{\"1\":\"1__Training\",\"2\":\"40\",\"3\":\"5__Hart\",\"4\":\"7km laufen im Volkspark\"}', 1, '".$datetimeStart[77]."', '".$datetimeEnd[77]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(79, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"2__\",\"3\":\"1__\",\"4\":\"2__\",\"5\":\"2__\",\"6\":\"3__\",\"7\":\"4__\",\"8\":\"5__\",\"9\":\"4__\"}', 1, '".$datetimeStart[78]."', '".$datetimeEnd[78]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(80, 6, 5, 3, 1, '{\"1\":[\"23:25\",\"07:10\",\"07:45\"],\"2\":\"5__mittelmäßig\",\"3\":\"\"}', 1, '".$datetimeStart[79]."', '".$datetimeEnd[79]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(81, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"4__\",\"3\":\"3__\",\"4\":\"2__\",\"5\":\"5__\",\"6\":\"0__trifft gar nicht zu\",\"7\":\"3__\",\"8\":\"4__\",\"9\":\"2__\"}', 1, '".$datetimeStart[80]."', '".$datetimeEnd[80]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(82, 6, 5, 3, 1, '{\"1\":[\"23:10\",\"08:00\",\"08:50\"],\"2\":\"3__schlecht\",\"3\":\"\"}', 1, '".$datetimeStart[81]."', '".$datetimeEnd[81]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(83, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"3__\",\"3\":\"2__\",\"4\":\"4__\",\"5\":\"4__\",\"6\":\"1__\",\"7\":\"3__\",\"8\":\"2__\",\"9\":\"2__\"}', 1, '".$datetimeStart[82]."', '".$datetimeEnd[82]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(84, 6, 5, 3, 1, '{\"1\":[\"23:25\",\"08:05\",\"08:40\"],\"2\":\"5__mittelmäßig\",\"3\":\"\"}', 1, '".$datetimeStart[83]."', '".$datetimeEnd[83]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(85, 6, 6, 4, 1, '{\"1\":\"1__Training\",\"2\":\"70\",\"3\":\"3__Moderat\",\"4\":\"10km langsam, mit Martin, Stadionrunde\"}', 1, '".$datetimeStart[84]."', '".$datetimeEnd[84]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(86, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"2__\",\"3\":\"4__\",\"4\":\"2__\",\"5\":\"3__\",\"6\":\"2__\",\"7\":\"4__\",\"8\":\"4__\",\"9\":\"3__\"}', 1, '".$datetimeStart[85]."', '".$datetimeEnd[85]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(87, 6, 5, 3, 1, '{\"1\":[\"00:35\",\"07:40\",\"07:05\"],\"2\":\"7__gut\",\"3\":\"\"}', 1, '".$datetimeStart[86]."', '".$datetimeEnd[86]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(88, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"2__\",\"3\":\"2__\",\"4\":\"4__\",\"5\":\"4__\",\"6\":\"0__trifft gar nicht zu\",\"7\":\"3__\",\"8\":\"1__\",\"9\":\"5__\"}', 1, '".$datetimeStart[87]."', '".$datetimeEnd[87]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(89, 6, 5, 3, 1, '{\"1\":[\"01:40\",\"09:25\",\"07:45\"],\"2\":\"5__mittelmäßig\",\"3\":\"\"}', 1, '".$datetimeStart[88]."', '".$datetimeEnd[88]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(90, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"5__\",\"3\":\"5__\",\"4\":\"6__trifft gar nicht zu\",\"5\":\"5__\",\"6\":\"0__trifft gar nicht zu\",\"7\":\"2__\",\"8\":\"0__trifft gar nicht zu\",\"9\":\"2__\"}', 1, '".$datetimeStart[89]."', '".$datetimeEnd[89]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(91, 6, 5, 3, 1, '{\"1\":[\"00:25\",\"10:10\",\"09:45\"],\"2\":\"4__\",\"3\":\"\"}', 1, '".$datetimeStart[90]."', '".$datetimeEnd[90]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(92, 6, 6, 4, 1, '{\"1\":\"1__Training\",\"2\":\"45\",\"3\":\"5__Hart\",\"4\":\"Brückenrunde nach Gefühl, 7,3 km\"}', 1, '".$datetimeStart[91]."', '".$datetimeEnd[91]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(93, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"2__\",\"3\":\"2__\",\"4\":\"4__\",\"5\":\"3__\",\"6\":\"2__\",\"7\":\"3__\",\"8\":\"2__\",\"9\":\"4__\"}', 1, '".$datetimeStart[92]."', '".$datetimeEnd[92]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(94, 6, 5, 3, 1, '{\"1\":[\"01:45\",\"10:50\",\"09:05\"],\"2\":\"7__gut\",\"3\":\"\"}', 1, '".$datetimeStart[93]."', '".$datetimeEnd[93]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(95, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"4__\",\"3\":\"3__\",\"4\":\"4__\",\"5\":\"4__\",\"6\":\"1__\",\"7\":\"2__\",\"8\":\"1__\",\"9\":\"1__\"}', 1, '".$datetimeStart[94]."', '".$datetimeEnd[94]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(96, 6, 5, 3, 1, '{\"1\":[\"01:10\",\"08:05\",\"06:55\"],\"2\":\"5__mittelmäßig\",\"3\":\"\"}', 1, '".$datetimeStart[95]."', '".$datetimeEnd[95]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(97, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"5__\",\"3\":\"6__trifft voll zu\",\"4\":\"5__trifft gar nicht zu\",\"5\":\"3__\",\"6\":\"1__\",\"7\":\"1__\",\"8\":\"0__trifft gar nicht zu\",\"9\":\"2__\"}', 1, '".$datetimeStart[96]."', '".$datetimeEnd[96]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(98, 6, 5, 3, 1, '{\"1\":[\"23:50\",\"11:15\",\"11:25\"],\"2\":\"3__schlecht\",\"3\":\"\"}', 1, '".$datetimeStart[97]."', '".$datetimeEnd[97]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(99, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"2__\",\"3\":\"1__\",\"4\":\"4__\",\"5\":\"5__\",\"6\":\"0__trifft gar nicht zu\",\"7\":\"4__\",\"8\":\"2__\",\"9\":\"2__\"}', 1, '".$datetimeStart[98]."', '".$datetimeEnd[98]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(100, 6, 5, 3, 1, '{\"1\":[\"00:35\",\"10:15\",\"09:40\"],\"2\":\"5__mittelmäßig\",\"3\":\"\"}', 1, '".$datetimeStart[99]."', '".$datetimeEnd[99]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(101, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"2__\",\"3\":\"3__\",\"4\":\"4__\",\"5\":\"4__\",\"6\":\"1__\",\"7\":\"3__\",\"8\":\"4__\",\"9\":\"1__\"}', 1, '".$datetimeStart[100]."', '".$datetimeEnd[100]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(102, 6, 5, 3, 1, '{\"1\":[\"01:05\",\"09:00\",\"07:55\"],\"2\":\"5__mittelmäßig\",\"3\":\"\"}', 1, '".$datetimeStart[101]."', '".$datetimeEnd[101]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(103, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"5__\",\"3\":\"5__\",\"4\":\"6__trifft gar nicht zu\",\"5\":\"3__\",\"6\":\"3__\",\"7\":\"1__\",\"8\":\"0__trifft gar nicht zu\",\"9\":\"4__\"}', 1, '".$datetimeStart[102]."', '".$datetimeEnd[102]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(104, 6, 5, 3, 1, '{\"1\":[\"02:12\",\"10:35\",\"08:23\"],\"2\":\"4__\",\"3\":\"\"}', 1, '".$datetimeStart[103]."', '".$datetimeEnd[103]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(105, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"4__\",\"3\":\"5__\",\"4\":\"5__trifft gar nicht zu\",\"5\":\"5__\",\"6\":\"0__trifft gar nicht zu\",\"7\":\"2__\",\"8\":\"0__trifft gar nicht zu\",\"9\":\"1__\"}', 1, '".$datetimeStart[104]."', '".$datetimeEnd[104]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(106, 6, 5, 3, 1, '{\"1\":[\"00:00\",\"10:40\",\"10:40\"],\"2\":\"2__\",\"3\":\"\"}', 1, '".$datetimeStart[105]."', '".$datetimeEnd[105]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(107, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"4__\",\"3\":\"3__\",\"4\":\"6__trifft gar nicht zu\",\"5\":\"5__\",\"6\":\"0__trifft gar nicht zu\",\"7\":\"1__\",\"8\":\"0__trifft gar nicht zu\",\"9\":\"2__\"}', 1, '".$datetimeStart[106]."', '".$datetimeEnd[106]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(108, 6, 5, 3, 1, '{\"1\":[\"23:08\",\"09:40\",\"10:32\"],\"2\":\"3__schlecht\",\"3\":\"\"}', 1, '".$datetimeStart[107]."', '".$datetimeEnd[107]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(109, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"6__trifft voll zu\",\"3\":\"4__\",\"4\":\"6__trifft gar nicht zu\",\"5\":\"5__\",\"6\":\"0__trifft gar nicht zu\",\"7\":\"1__\",\"8\":\"0__trifft gar nicht zu\",\"9\":\"2__\"}', 1, '".$datetimeStart[108]."', '".$datetimeEnd[108]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(110, 6, 5, 3, 1, '{\"1\":[\"00:25\",\"11:01\",\"10:36\"],\"2\":\"3__schlecht\",\"3\":\"\"}', 1, '".$datetimeStart[109]."', '".$datetimeEnd[109]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(111, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"4__\",\"3\":\"2__\",\"4\":\"2__\",\"5\":\"5__\",\"6\":\"0__trifft gar nicht zu\",\"7\":\"4__\",\"8\":\"3__\",\"9\":\"1__\"}', 1, '".$datetimeStart[110]."', '".$datetimeEnd[110]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(112, 6, 5, 3, 1, '{\"1\":[\"00:30\",\"09:55\",\"09:25\"],\"2\":\"6__\",\"3\":\"\"}', 1, '".$datetimeStart[111]."', '".$datetimeEnd[111]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(113, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"4__\",\"3\":\"4__\",\"4\":\"6__\",\"5\":\"5__\",\"6\":\"0__trifft gar nicht zu\",\"7\":\"1__\",\"8\":\"1__\",\"9\":\"1__\"}', 1, '".$datetimeStart[112]."', '".$datetimeEnd[112]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(114, 6, 5, 3, 1, '{\"1\":[\"01:05\",\"09:45\",\"08:40\"],\"2\":\"4__\",\"3\":\"\"}', 1, '".$datetimeStart[113]."', '".$datetimeEnd[113]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(115, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"3__\",\"3\":\"3__\",\"4\":\"4__\",\"5\":\"4__\",\"6\":\"0__trifft gar nicht zu\",\"7\":\"4__\",\"8\":\"2__\",\"9\":\"3__\"}', 1, '".$datetimeStart[114]."', '".$datetimeEnd[114]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(116, 6, 5, 3, 1, '{\"1\":[\"00:47\",\"08:18\",\"07:31\"],\"2\":\"6__\",\"3\":\"\"}', 1, '".$datetimeStart[115]."', '".$datetimeEnd[115]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(117, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"3__\",\"3\":\"3__\",\"4\":\"5__\",\"5\":\"4__\",\"6\":\"0__trifft gar nicht zu\",\"7\":\"2__\",\"8\":\"1__\",\"9\":\"2__\"}', 1, '".$datetimeStart[116]."', '".$datetimeEnd[116]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(118, 6, 5, 3, 1, '{\"1\":[\"00:40\",\"08:10\",\"07:30\"],\"2\":\"4__\",\"3\":\"\"}', 1, '".$datetimeStart[117]."', '".$datetimeEnd[117]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(119, 6, 6, 4, 1, '{\"1\":\"1__Training\",\"2\":\"150\",\"3\":\"5__Hart\",\"4\":\"Beachvolleyball\"}', 1, '".$datetimeStart[118]."', '".$datetimeEnd[118]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(120, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"2__\",\"3\":\"3__\",\"4\":\"5__trifft gar nicht zu\",\"5\":\"1__\",\"6\":\"3__\",\"7\":\"1__\",\"8\":\"0__trifft gar nicht zu\",\"9\":\"4__\"}', 1, '".$datetimeStart[119]."', '".$datetimeEnd[119]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(121, 6, 5, 3, 1, '{\"1\":[\"03:02\",\"11:10\",\"08:08\"],\"2\":\"6__\",\"3\":\"\"}', 1, '".$datetimeStart[120]."', '".$datetimeEnd[120]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(122, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"4__\",\"3\":\"3__\",\"4\":\"5__\",\"5\":\"2__\",\"6\":\"3__\",\"7\":\"4__\",\"8\":\"1__\",\"9\":\"3__\"}', 1, '".$datetimeStart[121]."', '".$datetimeEnd[121]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(123, 6, 5, 3, 1, '{\"1\":[\"00:50\",\"08:05\",\"07:15\"],\"2\":\"4__\",\"3\":\"\"}', 1, '".$datetimeStart[122]."', '".$datetimeEnd[122]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(124, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"2__\",\"3\":\"2__\",\"4\":\"3__\",\"5\":\"4__\",\"6\":\"1__\",\"7\":\"4__\",\"8\":\"2__\",\"9\":\"3__\"}', 1, '".$datetimeStart[123]."', '".$datetimeEnd[123]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(125, 6, 5, 3, 1, '{\"1\":[\"22:55\",\"06:45\",\"07:50\"],\"2\":\"4__\",\"3\":\"\"}', 1, '".$datetimeStart[124]."', '".$datetimeEnd[124]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(126, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"2__\",\"3\":\"3__\",\"4\":\"5__\",\"5\":\"4__\",\"6\":\"0__trifft gar nicht zu\",\"7\":\"4__\",\"8\":\"1__\",\"9\":\"2__\"}', 1, '".$datetimeStart[125]."', '".$datetimeEnd[125]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(127, 6, 5, 3, 1, '{\"1\":[\"01:30\",\"08:45\",\"07:15\"],\"2\":\"3__schlecht\",\"3\":\"\"}', 1, '".$datetimeStart[126]."', '".$datetimeEnd[126]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(128, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"4__\",\"3\":\"4__\",\"4\":\"5__\",\"5\":\"4__\",\"6\":\"0__trifft gar nicht zu\",\"7\":\"2__\",\"8\":\"1__\",\"9\":\"2__\"}', 1, '".$datetimeStart[127]."', '".$datetimeEnd[127]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(129, 6, 5, 3, 1, '{\"1\":[\"23:00\",\"08:05\",\"09:05\"],\"2\":\"4__\",\"3\":\"\"}', 1, '".$datetimeStart[128]."', '".$datetimeEnd[128]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(130, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"4__\",\"3\":\"5__\",\"4\":\"6__\",\"5\":\"5__\",\"6\":\"0__trifft gar nicht zu\",\"7\":\"1__\",\"8\":\"1__\",\"9\":\"1__\"}', 1, '".$datetimeStart[129]."', '".$datetimeEnd[129]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(131, 6, 5, 3, 1, '{\"1\":[\"23:35\",\"08:30\",\"08:55\"],\"2\":\"4__\",\"3\":\"\"}', 1, '".$datetimeStart[130]."', '".$datetimeEnd[130]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(132, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"3__\",\"3\":\"4__\",\"4\":\"5__\",\"5\":\"5__\",\"6\":\"0__trifft gar nicht zu\",\"7\":\"2__\",\"8\":\"1__\",\"9\":\"2__\"}', 1, '".$datetimeStart[131]."', '".$datetimeEnd[131]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(133, 6, 5, 3, 1, '{\"1\":[\"23:35\",\"08:40\",\"09:05\"],\"2\":\"4__\",\"3\":\"\"}', 1, '".$datetimeStart[132]."', '".$datetimeEnd[132]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(134, 6, 6, 4, 1, '{\"1\":\"1__Training\",\"2\":\"150\",\"3\":\"2__Leicht\",\"4\":\"Fahrradtour nach Oppenheim\"}', 1, '".$datetimeStart[133]."', '".$datetimeEnd[133]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(135, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"2__\",\"3\":\"2__\",\"4\":\"4__\",\"5\":\"2__\",\"6\":\"0__trifft gar nicht zu\",\"7\":\"3__\",\"8\":\"1__\",\"9\":\"4__\"}', 1, '".$datetimeStart[134]."', '".$datetimeEnd[134]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(136, 6, 5, 3, 1, '{\"1\":[\"03:25\",\"10:40\",\"07:15\"],\"2\":\"6__\",\"3\":\"\"}', 1, '".$datetimeStart[135]."', '".$datetimeEnd[135]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(137, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"3__\",\"3\":\"2__\",\"4\":\"6__trifft gar nicht zu\",\"5\":\"4__\",\"6\":\"0__trifft gar nicht zu\",\"7\":\"1__\",\"8\":\"0__trifft gar nicht zu\",\"9\":\"3__\"}', 1, '".$datetimeStart[136]."', '".$datetimeEnd[136]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(138, 6, 5, 3, 1, '{\"1\":[\"23:50\",\"06:40\",\"06:50\"],\"2\":\"6__\",\"3\":\"\"}', 1, '".$datetimeStart[137]."', '".$datetimeEnd[137]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(139, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"3__\",\"3\":\"5__\",\"4\":\"4__\",\"5\":\"3__\",\"6\":\"0__trifft gar nicht zu\",\"7\":\"2__\",\"8\":\"1__\",\"9\":\"2__\"}', 1, '".$datetimeStart[138]."', '".$datetimeEnd[138]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(140, 6, 5, 3, 1, '{\"1\":[\"23:55\",\"06:50\",\"06:55\"],\"2\":\"6__\",\"3\":\"\"}', 1, '".$datetimeStart[139]."', '".$datetimeEnd[139]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(141, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"5__\",\"3\":\"4__\",\"4\":\"4__\",\"5\":\"5__\",\"6\":\"0__trifft gar nicht zu\",\"7\":\"2__\",\"8\":\"3__\",\"9\":\"1__\"}', 1, '".$datetimeStart[140]."', '".$datetimeEnd[140]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(142, 6, 5, 3, 1, '{\"1\":[\"23:00\",\"09:10\",\"10:10\"],\"2\":\"4__\",\"3\":\"\"}', 1, '".$datetimeStart[141]."', '".$datetimeEnd[141]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(143, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"5__\",\"3\":\"5__\",\"4\":\"6__trifft gar nicht zu\",\"5\":\"5__\",\"6\":\"0__trifft gar nicht zu\",\"7\":\"1__\",\"8\":\"0__trifft gar nicht zu\",\"9\":\"1__\"}', 1, '".$datetimeStart[142]."', '".$datetimeEnd[142]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(144, 6, 5, 3, 1, '{\"1\":[\"23:05\",\"08:20\",\"09:15\"],\"2\":\"5__mittelmäßig\",\"3\":\"\"}', 1, '".$datetimeStart[143]."', '".$datetimeEnd[143]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(145, 6, 6, 4, 1, '{\"1\":\"1__Training\",\"2\":\"65\",\"3\":\"4__Schon härter\",\"4\":\"10km Stadionrunde, sehr warm, über 30 Grad\"}', 1, '".$datetimeStart[144]."', '".$datetimeEnd[144]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(146, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"3__\",\"3\":\"5__\",\"4\":\"6__trifft gar nicht zu\",\"5\":\"2__\",\"6\":\"4__\",\"7\":\"1__\",\"8\":\"0__trifft gar nicht zu\",\"9\":\"4__\"}', 1, '".$datetimeStart[145]."', '".$datetimeEnd[145]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(147, 6, 5, 3, 1, '{\"1\":[\"23:20\",\"08:35\",\"09:15\"],\"2\":\"4__\",\"3\":\"\"}', 1, '".$datetimeStart[146]."', '".$datetimeEnd[146]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(148, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"2__\",\"3\":\"2__\",\"4\":\"5__\",\"5\":\"2__\",\"6\":\"2__\",\"7\":\"1__\",\"8\":\"1__\",\"9\":\"4__\"}', 1, '".$datetimeStart[147]."', '".$datetimeEnd[147]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(149, 6, 5, 3, 1, '{\"1\":[\"01:45\",\"10:35\",\"08:50\"],\"2\":\"5__mittelmäßig\",\"3\":\"\"}', 1, '".$datetimeStart[148]."', '".$datetimeEnd[148]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(150, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"4__\",\"3\":\"4__\",\"4\":\"5__\",\"5\":\"3__\",\"6\":\"2__\",\"7\":\"1__\",\"8\":\"1__\",\"9\":\"3__\"}', 1, '".$datetimeStart[149]."', '".$datetimeEnd[149]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(151, 6, 5, 3, 1, '{\"1\":[\"00:20\",\"08:15\",\"07:55\"],\"2\":\"4__\",\"3\":\"\"}', 1, '".$datetimeStart[150]."', '".$datetimeEnd[150]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(152, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"5__\",\"3\":\"5__\",\"4\":\"6__trifft gar nicht zu\",\"5\":\"5__\",\"6\":\"1__\",\"7\":\"0__trifft gar nicht zu\",\"8\":\"0__trifft gar nicht zu\",\"9\":\"1__\"}', 1, '".$datetimeStart[151]."', '".$datetimeEnd[151]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(153, 6, 5, 3, 1, '{\"1\":[\"23:35\",\"08:20\",\"08:45\"],\"2\":\"4__\",\"3\":\"\"}', 1, '".$datetimeStart[152]."', '".$datetimeEnd[152]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(154, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"4__\",\"3\":\"5__\",\"4\":\"5__trifft gar nicht zu\",\"5\":\"5__\",\"6\":\"1__\",\"7\":\"1__\",\"8\":\"0__trifft gar nicht zu\",\"9\":\"1__\"}', 1, '".$datetimeStart[153]."', '".$datetimeEnd[153]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(155, 6, 5, 3, 1, '{\"1\":[\"00:25\",\"08:35\",\"08:10\"],\"2\":\"4__\",\"3\":\"\"}', 1, '".$datetimeStart[154]."', '".$datetimeEnd[154]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(156, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"5__\",\"3\":\"4__\",\"4\":\"6__\",\"5\":\"6__trifft voll zu\",\"6\":\"0__trifft gar nicht zu\",\"7\":\"1__\",\"8\":\"1__\",\"9\":\"1__\"}', 1, '".$datetimeStart[155]."', '".$datetimeEnd[155]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(157, 6, 5, 3, 1, '{\"1\":[\"23:30\",\"08:40\",\"09:10\"],\"2\":\"3__schlecht\",\"3\":\"\"}', 1, '".$datetimeStart[156]."', '".$datetimeEnd[156]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(158, 6, 6, 4, 1, '{\"1\":\"1__Training\",\"2\":\"45\",\"3\":\"5__Hart\",\"4\":\"7km Volkspark, steigernde Geschwindigkeit\"}', 1, '".$datetimeStart[157]."', '".$datetimeEnd[157]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(159, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"4__\",\"3\":\"5__\",\"4\":\"6__trifft gar nicht zu\",\"5\":\"3__\",\"6\":\"2__\",\"7\":\"1__\",\"8\":\"0__trifft gar nicht zu\",\"9\":\"3__\"}', 1, '".$datetimeStart[158]."', '".$datetimeEnd[158]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(160, 6, 5, 3, 1, '{\"1\":[\"23:35\",\"08:45\",\"09:10\"],\"2\":\"6__\",\"3\":\"\"}', 1, '".$datetimeStart[159]."', '".$datetimeEnd[159]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(161, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"5__\",\"3\":\"4__\",\"4\":\"6__trifft gar nicht zu\",\"5\":\"5__\",\"6\":\"1__\",\"7\":\"1__\",\"8\":\"0__trifft gar nicht zu\",\"9\":\"2__\"}', 1, '".$datetimeStart[160]."', '".$datetimeEnd[160]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(162, 6, 5, 3, 1, '{\"1\":[\"23:40\",\"07:25\",\"07:45\"],\"2\":\"4__\",\"3\":\"\"}', 1, '".$datetimeStart[161]."', '".$datetimeEnd[161]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(163, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"4__\",\"3\":\"5__\",\"4\":\"6__trifft gar nicht zu\",\"5\":\"5__\",\"6\":\"0__trifft gar nicht zu\",\"7\":\"1__\",\"8\":\"0__trifft gar nicht zu\",\"9\":\"1__\"}', 1, '".$datetimeStart[162]."', '".$datetimeEnd[162]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(164, 6, 5, 3, 1, '{\"1\":[\"00:30\",\"08:45\",\"08:15\"],\"2\":\"5__mittelmäßig\",\"3\":\"\"}', 1, '".$datetimeStart[163]."', '".$datetimeEnd[163]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(165, 6, 6, 4, 1, '{\"1\":\"1__Training\",\"2\":\"265\",\"3\":\"3__Moderat\",\"4\":\"Fahrradtour nach Bad Sobernheim (60km) mit einigen Stopps\"}', 1, '".$datetimeStart[164]."', '".$datetimeEnd[164]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(166, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"4__\",\"3\":\"4__\",\"4\":\"5__trifft gar nicht zu\",\"5\":\"3__\",\"6\":\"1__\",\"7\":\"1__\",\"8\":\"0__trifft gar nicht zu\",\"9\":\"3__\"}', 1, '".$datetimeStart[165]."', '".$datetimeEnd[165]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(167, 6, 5, 3, 1, '{\"1\":[\"01:30\",\"10:10\",\"08:40\"],\"2\":\"3__schlecht\",\"3\":\"\"}', 1, '".$datetimeStart[166]."', '".$datetimeEnd[166]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(168, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"4__\",\"3\":\"3__\",\"4\":\"4__trifft gar nicht zu\",\"5\":\"4__\",\"6\":\"1__\",\"7\":\"2__\",\"8\":\"0__trifft gar nicht zu\",\"9\":\"1__\"}', 1, '".$datetimeStart[167]."', '".$datetimeEnd[167]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(169, 6, 5, 3, 1, '{\"1\":[\"00:15\",\"07:35\",\"07:20\"],\"2\":\"6__\",\"3\":\"\"}', 1, '".$datetimeStart[168]."', '".$datetimeEnd[168]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(170, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"4__\",\"3\":\"5__\",\"4\":\"4__\",\"5\":\"4__\",\"6\":\"0__trifft gar nicht zu\",\"7\":\"1__\",\"8\":\"2__\",\"9\":\"1__\"}', 1, '".$datetimeStart[169]."', '".$datetimeEnd[169]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(171, 6, 5, 3, 1, '{\"1\":[\"00:50\",\"07:35\",\"06:45\"],\"2\":\"4__\",\"3\":\"\"}', 1, '".$datetimeStart[170]."', '".$datetimeEnd[170]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(172, 6, 6, 4, 1, '{\"1\":\"1__Training\",\"2\":\"120\",\"3\":\"4__Schon härter\",\"4\":\"Volleyballtraining Bleidenstadt, ohne Springen, Beginn Vorbereitung\"}', 1, '".$datetimeStart[171]."', '".$datetimeEnd[171]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(173, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"2__\",\"3\":\"4__\",\"4\":\"5__trifft gar nicht zu\",\"5\":\"2__\",\"6\":\"3__\",\"7\":\"1__\",\"8\":\"0__trifft gar nicht zu\",\"9\":\"4__\"}', 1, '".$datetimeStart[172]."', '".$datetimeEnd[172]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(174, 6, 5, 3, 1, '{\"1\":[\"00:25\",\"08:30\",\"08:05\"],\"2\":\"4__\",\"3\":\"\"}', 1, '".$datetimeStart[173]."', '".$datetimeEnd[173]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(175, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"3__\",\"3\":\"4__\",\"4\":\"4__\",\"5\":\"3__\",\"6\":\"2__\",\"7\":\"3__\",\"8\":\"1__\",\"9\":\"3__\"}', 1, '".$datetimeStart[174]."', '".$datetimeEnd[174]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(176, 6, 5, 3, 1, '{\"1\":[\"01:30\",\"08:05\",\"06:35\"],\"2\":\"4__\",\"3\":\"\"}', 1, '".$datetimeStart[175]."', '".$datetimeEnd[175]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(177, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"2__\",\"3\":\"2__\",\"4\":\"1__\",\"5\":\"0__trifft gar nicht zu\",\"6\":\"2__\",\"7\":\"2__\",\"8\":\"3__\",\"9\":\"6__trifft voll zu\"}', 1, '".$datetimeStart[176]."', '".$datetimeEnd[176]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(178, 6, 5, 3, 1, '{\"1\":[\"00:55\",\"07:25\",\"06:30\"],\"2\":\"7__gut\",\"3\":\"\"}', 1, '".$datetimeStart[177]."', '".$datetimeEnd[177]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(179, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"3__\",\"3\":\"5__\",\"4\":\"5__\",\"5\":\"2__\",\"6\":\"4__\",\"7\":\"2__\",\"8\":\"1__\",\"9\":\"3__\"}', 1, '".$datetimeStart[178]."', '".$datetimeEnd[178]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(180, 6, 5, 3, 1, '{\"1\":[\"00:00\",\"09:10\",\"09:10\"],\"2\":\"4__\",\"3\":\"\"}', 1, '".$datetimeStart[179]."', '".$datetimeEnd[179]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(181, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"2__\",\"3\":\"2__\",\"4\":\"4__\",\"5\":\"2__\",\"6\":\"2__\",\"7\":\"3__\",\"8\":\"2__\",\"9\":\"3__\"}', 1, '".$datetimeStart[180]."', '".$datetimeEnd[180]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(182, 6, 5, 3, 1, '{\"1\":[\"01:55\",\"09:10\",\"07:15\"],\"2\":\"4__\",\"3\":\"\"}', 1, '".$datetimeStart[181]."', '".$datetimeEnd[181]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(183, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"3__\",\"3\":\"3__\",\"4\":\"4__\",\"5\":\"2__\",\"6\":\"4__\",\"7\":\"4__\",\"8\":\"2__\",\"9\":\"3__\"}', 1, '".$datetimeStart[182]."', '".$datetimeEnd[182]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(184, 6, 5, 3, 1, '{\"1\":[\"23:40\",\"08:10\",\"08:30\"],\"2\":\"6__\",\"3\":\"\"}', 1, '".$datetimeStart[183]."', '".$datetimeEnd[183]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(185, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"4__\",\"3\":\"4__\",\"4\":\"5__\",\"5\":\"3__\",\"6\":\"2__\",\"7\":\"1__\",\"8\":\"1__\",\"9\":\"3__\"}', 1, '".$datetimeStart[184]."', '".$datetimeEnd[184]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(186, 6, 5, 3, 1, '{\"1\":[\"23:05\",\"07:00\",\"07:55\"],\"2\":\"5__mittelmäßig\",\"3\":\"\"}', 1, '".$datetimeStart[185]."', '".$datetimeEnd[185]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(187, 6, 6, 4, 1, '{\"1\":\"1__Training\",\"2\":\"75\",\"3\":\"3__Moderat\",\"4\":\"DIenstagskick, noch mit Schulterbescherden gespielt, eher ruhiger\"}', 1, '".$datetimeStart[186]."', '".$datetimeEnd[186]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(188, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"3__\",\"3\":\"4__\",\"4\":\"5__trifft gar nicht zu\",\"5\":\"2__\",\"6\":\"4__\",\"7\":\"2__\",\"8\":\"0__trifft gar nicht zu\",\"9\":\"4__\"}', 1, '".$datetimeStart[187]."', '".$datetimeEnd[187]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(189, 6, 5, 3, 1, '{\"1\":[\"00:45\",\"07:10\",\"06:25\"],\"2\":\"6__\",\"3\":\"\"}', 1, '".$datetimeStart[188]."', '".$datetimeEnd[188]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(190, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"2__\",\"3\":\"3__\",\"4\":\"4__\",\"5\":\"3__\",\"6\":\"1__\",\"7\":\"4__\",\"8\":\"1__\",\"9\":\"3__\"}', 1, '".$datetimeStart[189]."', '".$datetimeEnd[189]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(191, 6, 5, 3, 1, '{\"1\":[\"01:45\",\"08:20\",\"06:35\"],\"2\":\"4__\",\"3\":\"\"}', 1, '".$datetimeStart[190]."', '".$datetimeEnd[190]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(192, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"4__\",\"3\":\"4__\",\"4\":\"5__\",\"5\":\"3__\",\"6\":\"2__\",\"7\":\"2__\",\"8\":\"1__\",\"9\":\"2__\"}', 1, '".$datetimeStart[191]."', '".$datetimeEnd[191]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(193, 6, 5, 3, 1, '{\"1\":[\"22:50\",\"06:45\",\"07:55\"],\"2\":\"3__schlecht\",\"3\":\"\"}', 1, '".$datetimeStart[192]."', '".$datetimeEnd[192]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(194, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"4__\",\"3\":\"2__\",\"4\":\"2__\",\"5\":\"4__\",\"6\":\"1__\",\"7\":\"5__\",\"8\":\"4__\",\"9\":\"2__\"}', 1, '".$datetimeStart[193]."', '".$datetimeEnd[193]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(195, 6, 5, 3, 1, '{\"1\":[\"00:45\",\"08:45\",\"08:00\"],\"2\":\"4__\",\"3\":\"\"}', 1, '".$datetimeStart[194]."', '".$datetimeEnd[194]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(196, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"4__\",\"3\":\"3__\",\"4\":\"5__\",\"5\":\"4__\",\"6\":\"0__trifft gar nicht zu\",\"7\":\"2__\",\"8\":\"1__\",\"9\":\"2__\"}', 1, '".$datetimeStart[195]."', '".$datetimeEnd[195]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(197, 6, 5, 3, 1, '{\"1\":[\"00:50\",\"10:30\",\"09:40\"],\"2\":\"4__\",\"3\":\"\"}', 1, '".$datetimeStart[196]."', '".$datetimeEnd[196]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(198, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"5__\",\"3\":\"5__\",\"4\":\"4__\",\"5\":\"4__\",\"6\":\"1__\",\"7\":\"2__\",\"8\":\"1__\",\"9\":\"1__\"}', 1, '".$datetimeStart[197]."', '".$datetimeEnd[197]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(199, 6, 5, 3, 1, '{\"1\":[\"23:55\",\"07:25\",\"07:30\"],\"2\":\"4__\",\"3\":\"\"}', 1, '".$datetimeStart[198]."', '".$datetimeEnd[198]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(200, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"4__\",\"3\":\"3__\",\"4\":\"4__\",\"5\":\"3__\",\"6\":\"1__\",\"7\":\"3__\",\"8\":\"1__\",\"9\":\"2__\"}', 1, '".$datetimeStart[199]."', '".$datetimeEnd[199]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(201, 6, 5, 3, 1, '{\"1\":[\"23:35\",\"06:45\",\"07:10\"],\"2\":\"6__\",\"3\":\"\"}', 1, '".$datetimeStart[200]."', '".$datetimeEnd[200]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(202, 6, 6, 4, 1, '{\"1\":\"1__Training\",\"2\":\"65\",\"3\":\"5__Hart\",\"4\":\"10km Stadionrunde, anstrengend\"}', 1, '".$datetimeStart[201]."', '".$datetimeEnd[201]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(203, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"2__\",\"3\":\"3__\",\"4\":\"4__\",\"5\":\"2__\",\"6\":\"3__\",\"7\":\"4__\",\"8\":\"1__\",\"9\":\"4__\"}', 1, '".$datetimeStart[202]."', '".$datetimeEnd[202]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(204, 6, 5, 3, 1, '{\"1\":[\"22:45\",\"07:05\",\"08:20\"],\"2\":\"4__\",\"3\":\"\"}', 1, '".$datetimeStart[203]."', '".$datetimeEnd[203]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(205, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"5__\",\"3\":\"4__\",\"4\":\"6__trifft gar nicht zu\",\"5\":\"5__\",\"6\":\"1__\",\"7\":\"1__\",\"8\":\"0__trifft gar nicht zu\",\"9\":\"1__\"}', 1, '".$datetimeStart[204]."', '".$datetimeEnd[204]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(206, 6, 5, 3, 1, '{\"1\":[\"22:45\",\"07:35\",\"08:50\"],\"2\":\"5__mittelmäßig\",\"3\":\"\"}', 1, '".$datetimeStart[205]."', '".$datetimeEnd[205]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(207, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"5__\",\"3\":\"5__\",\"4\":\"5__trifft gar nicht zu\",\"5\":\"5__\",\"6\":\"0__trifft gar nicht zu\",\"7\":\"1__\",\"8\":\"0__trifft gar nicht zu\",\"9\":\"1__\"}', 1, '".$datetimeStart[206]."', '".$datetimeEnd[206]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(208, 6, 5, 3, 1, '{\"1\":[\"22:45\",\"08:05\",\"09:20\"],\"2\":\"3__schlecht\",\"3\":\"\"}', 1, '".$datetimeStart[207]."', '".$datetimeEnd[207]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(209, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"4__\",\"3\":\"2__\",\"4\":\"2__\",\"5\":\"5__\",\"6\":\"0__trifft gar nicht zu\",\"7\":\"5__\",\"8\":\"4__\",\"9\":\"1__\"}', 1, '".$datetimeStart[208]."', '".$datetimeEnd[208]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(210, 6, 5, 3, 1, '{\"1\":[\"00:05\",\"09:30\",\"09:25\"],\"2\":\"6__\",\"3\":\"\"}', 1, '".$datetimeStart[209]."', '".$datetimeEnd[209]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(211, 6, 6, 4, 1, '{\"1\":\"1__Training\",\"2\":\"40\",\"3\":\"9__Wirklich sehr hart\",\"4\":\"Schneller 5km Lauf im 5min/km Schnitt\"}', 1, '".$datetimeStart[210]."', '".$datetimeEnd[210]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(212, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"4__\",\"3\":\"5__\",\"4\":\"4__trifft gar nicht zu\",\"5\":\"3__\",\"6\":\"1__\",\"7\":\"1__\",\"8\":\"0__trifft gar nicht zu\",\"9\":\"2__\"}', 1, '".$datetimeStart[211]."', '".$datetimeEnd[211]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(213, 6, 5, 3, 1, '{\"1\":[\"02:20\",\"09:40\",\"07:20\"],\"2\":\"6__\",\"3\":\"\"}', 1, '".$datetimeStart[212]."', '".$datetimeEnd[212]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(214, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"5__\",\"3\":\"4__\",\"4\":\"4__\",\"5\":\"5__\",\"6\":\"0__trifft gar nicht zu\",\"7\":\"1__\",\"8\":\"1__\",\"9\":\"2__\"}', 1, '".$datetimeStart[213]."', '".$datetimeEnd[213]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(215, 6, 5, 3, 1, '{\"1\":[\"22:10\",\"07:30\",\"09:20\"],\"2\":\"3__schlecht\",\"3\":\"\"}', 1, '".$datetimeStart[214]."', '".$datetimeEnd[214]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(216, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"5__\",\"3\":\"5__\",\"4\":\"6__trifft gar nicht zu\",\"5\":\"5__\",\"6\":\"0__trifft gar nicht zu\",\"7\":\"1__\",\"8\":\"0__trifft gar nicht zu\",\"9\":\"1__\"}', 1, '".$datetimeStart[215]."', '".$datetimeEnd[215]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(217, 6, 5, 3, 1, '{\"1\":[\"22:45\",\"08:00\",\"09:15\"],\"2\":\"1__sehr schlecht\",\"3\":\"\"}', 1, '".$datetimeStart[216]."', '".$datetimeEnd[216]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(218, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"4__\",\"3\":\"4__\",\"4\":\"5__\",\"5\":\"5__\",\"6\":\"0__trifft gar nicht zu\",\"7\":\"2__\",\"8\":\"1__\",\"9\":\"2__\"}', 1, '".$datetimeStart[217]."', '".$datetimeEnd[217]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(219, 6, 5, 3, 1, '{\"1\":[\"23:15\",\"08:00\",\"08:45\"],\"2\":\"4__\",\"3\":\"\"}', 1, '".$datetimeStart[218]."', '".$datetimeEnd[218]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(220, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"5__\",\"3\":\"6__trifft voll zu\",\"4\":\"5__\",\"5\":\"5__\",\"6\":\"0__trifft gar nicht zu\",\"7\":\"0__trifft gar nicht zu\",\"8\":\"1__\",\"9\":\"1__\"}', 1, '".$datetimeStart[219]."', '".$datetimeEnd[219]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(221, 6, 5, 3, 1, '{\"1\":[\"00:20\",\"08:20\",\"08:00\"],\"2\":\"4__\",\"3\":\"\"}', 1, '".$datetimeStart[220]."', '".$datetimeEnd[220]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(222, 6, 6, 4, 1, '{\"1\":\"1__Training\",\"2\":\"45\",\"3\":\"2__Leicht\",\"4\":\"Langsamer 7km Lauf, Volkspark\"}', 1, '".$datetimeStart[221]."', '".$datetimeEnd[221]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(223, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"3__\",\"3\":\"5__\",\"4\":\"5__trifft gar nicht zu\",\"5\":\"3__\",\"6\":\"2__\",\"7\":\"1__\",\"8\":\"0__trifft gar nicht zu\",\"9\":\"3__\"}', 1, '".$datetimeStart[222]."', '".$datetimeEnd[222]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(224, 6, 5, 3, 1, '{\"1\":[\"01:13\",\"06:40\",\"05:27\"],\"2\":\"4__\",\"3\":\"\"}', 1, '".$datetimeStart[223]."', '".$datetimeEnd[223]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(225, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"3__\",\"3\":\"4__\",\"4\":\"5__\",\"5\":\"4__\",\"6\":\"0__trifft gar nicht zu\",\"7\":\"3__\",\"8\":\"1__\",\"9\":\"3__\"}', 1, '".$datetimeStart[224]."', '".$datetimeEnd[224]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(226, 6, 5, 3, 1, '{\"1\":[\"03:03\",\"11:45\",\"08:42\"],\"2\":\"4__\",\"3\":\"\"}', 1, '".$datetimeStart[225]."', '".$datetimeEnd[225]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(227, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"5__\",\"3\":\"4__\",\"4\":\"4__\",\"5\":\"5__\",\"6\":\"0__trifft gar nicht zu\",\"7\":\"2__\",\"8\":\"1__\",\"9\":\"1__\"}', 1, '".$datetimeStart[226]."', '".$datetimeEnd[226]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(228, 6, 5, 3, 1, '{\"1\":[\"01:20\",\"09:15\",\"07:55\"],\"2\":\"3__schlecht\",\"3\":\"\"}', 1, '".$datetimeStart[227]."', '".$datetimeEnd[227]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(229, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"4__\",\"3\":\"4__\",\"4\":\"4__\",\"5\":\"5__\",\"6\":\"0__trifft gar nicht zu\",\"7\":\"2__\",\"8\":\"1__\",\"9\":\"2__\"}', 1, '".$datetimeStart[228]."', '".$datetimeEnd[228]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(230, 6, 5, 3, 1, '{\"1\":[\"00:30\",\"07:55\",\"07:25\"],\"2\":\"5__mittelmäßig\",\"3\":\"\"}', 1, '".$datetimeStart[229]."', '".$datetimeEnd[229]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(231, 6, 6, 4, 1, '{\"1\":\"1__Training\",\"2\":\"100\",\"3\":\"5__Hart\",\"4\":\"Fußball in Drais + Fahrrad Hin- und Rückfahrt je 8 km\"}', 1, '".$datetimeStart[230]."', '".$datetimeEnd[230]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(232, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"2__\",\"3\":\"4__\",\"4\":\"2__\",\"5\":\"1__\",\"6\":\"4__\",\"7\":\"3__\",\"8\":\"4__\",\"9\":\"5__\"}', 1, '".$datetimeStart[231]."', '".$datetimeEnd[231]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(233, 6, 5, 3, 1, '{\"1\":[\"23:40\",\"06:45\",\"07:05\"],\"2\":\"8__\",\"3\":\"\"}', 1, '".$datetimeStart[232]."', '".$datetimeEnd[232]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(234, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"3__\",\"3\":\"4__\",\"4\":\"4__\",\"5\":\"1__\",\"6\":\"5__\",\"7\":\"2__\",\"8\":\"1__\",\"9\":\"5__\"}', 1, '".$datetimeStart[233]."', '".$datetimeEnd[233]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(235, 6, 5, 3, 1, '{\"1\":[\"23:45\",\"08:15\",\"08:30\"],\"2\":\"5__mittelmäßig\",\"3\":\"\"}', 1, '".$datetimeStart[234]."', '".$datetimeEnd[234]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(236, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"4__\",\"3\":\"4__\",\"4\":\"5__trifft gar nicht zu\",\"5\":\"4__\",\"6\":\"2__\",\"7\":\"1__\",\"8\":\"0__trifft gar nicht zu\",\"9\":\"2__\"}', 1, '".$datetimeStart[235]."', '".$datetimeEnd[235]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(237, 6, 5, 3, 1, '{\"1\":[\"23:35\",\"06:45\",\"07:10\"],\"2\":\"4__\",\"3\":\"\"}', 1, '".$datetimeStart[236]."', '".$datetimeEnd[236]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(238, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"4__\",\"3\":\"3__\",\"4\":\"5__trifft gar nicht zu\",\"5\":\"4__\",\"6\":\"1__\",\"7\":\"2__\",\"8\":\"0__trifft gar nicht zu\",\"9\":\"1__\"}', 1, '".$datetimeStart[237]."', '".$datetimeEnd[237]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(239, 6, 5, 3, 1, '{\"1\":[\"02:05\",\"10:00\",\"07:55\"],\"2\":\"5__mittelmäßig\",\"3\":\"\"}', 1, '".$datetimeStart[238]."', '".$datetimeEnd[238]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(240, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"4__\",\"3\":\"3__\",\"4\":\"4__\",\"5\":\"3__\",\"6\":\"0__trifft gar nicht zu\",\"7\":\"4__\",\"8\":\"1__\",\"9\":\"3__\"}', 1, '".$datetimeStart[239]."', '".$datetimeEnd[239]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(241, 6, 5, 3, 1, '{\"1\":[\"02:05\",\"10:45\",\"08:40\"],\"2\":\"4__\",\"3\":\"\"}', 1, '".$datetimeStart[240]."', '".$datetimeEnd[240]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(242, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"5__\",\"3\":\"5__\",\"4\":\"5__trifft gar nicht zu\",\"5\":\"5__\",\"6\":\"0__trifft gar nicht zu\",\"7\":\"1__\",\"8\":\"0__trifft gar nicht zu\",\"9\":\"1__\"}', 1, '".$datetimeStart[241]."', '".$datetimeEnd[241]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(243, 6, 5, 3, 1, '{\"1\":[\"00:35\",\"08:55\",\"08:20\"],\"2\":\"4__\",\"3\":\"\"}', 1, '".$datetimeStart[242]."', '".$datetimeEnd[242]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(244, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"4__\",\"3\":\"5__\",\"4\":\"4__\",\"5\":\"4__\",\"6\":\"2__\",\"7\":\"3__\",\"8\":\"1__\",\"9\":\"2__\"}', 1, '".$datetimeStart[243]."', '".$datetimeEnd[243]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(245, 6, 5, 3, 1, '{\"1\":[\"00:30\",\"07:40\",\"07:10\"],\"2\":\"6__\",\"3\":\"\"}', 1, '".$datetimeStart[244]."', '".$datetimeEnd[244]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(246, 6, 6, 4, 1, '{\"1\":\"1__Training\",\"2\":\"100\",\"3\":\"7__Sehr hart\",\"4\":\"Fußball in Drais\"}', 1, '".$datetimeStart[245]."', '".$datetimeEnd[245]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(247, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"2__\",\"3\":\"4__\",\"4\":\"6__trifft gar nicht zu\",\"5\":\"2__\",\"6\":\"4__\",\"7\":\"2__\",\"8\":\"0__trifft gar nicht zu\",\"9\":\"5__\"}', 1, '".$datetimeStart[246]."', '".$datetimeEnd[246]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(248, 6, 5, 3, 1, '{\"1\":[\"23:45\",\"08:00\",\"08:15\"],\"2\":\"4__\",\"3\":\"\"}', 1, '".$datetimeStart[247]."', '".$datetimeEnd[247]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(249, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"5__\",\"3\":\"5__\",\"4\":\"6__trifft gar nicht zu\",\"5\":\"4__\",\"6\":\"1__\",\"7\":\"1__\",\"8\":\"0__trifft gar nicht zu\",\"9\":\"2__\"}', 1, '".$datetimeStart[248]."', '".$datetimeEnd[248]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(250, 6, 5, 3, 1, '{\"1\":[\"23:40\",\"08:20\",\"08:40\"],\"2\":\"3__schlecht\",\"3\":\"\"}', 1, '".$datetimeStart[249]."', '".$datetimeEnd[249]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(251, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"5__\",\"3\":\"4__\",\"4\":\"5__trifft gar nicht zu\",\"5\":\"5__\",\"6\":\"1__\",\"7\":\"2__\",\"8\":\"0__trifft gar nicht zu\",\"9\":\"1__\"}', 1, '".$datetimeStart[250]."', '".$datetimeEnd[250]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(252, 6, 5, 3, 1, '{\"1\":[\"23:15\",\"07:35\",\"08:20\"],\"2\":\"6__\",\"3\":\"\"}', 1, '".$datetimeStart[251]."', '".$datetimeEnd[251]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(253, 6, 6, 4, 1, '{\"1\":\"1__Training\",\"2\":\"45\",\"3\":\"3__Moderat\",\"4\":\"7km Volkspark entspannt\"}', 1, '".$datetimeStart[252]."', '".$datetimeEnd[252]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(254, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"3__\",\"3\":\"2__\",\"4\":\"4__\",\"5\":\"3__\",\"6\":\"3__\",\"7\":\"3__\",\"8\":\"2__\",\"9\":\"4__\"}', 1, '".$datetimeStart[253]."', '".$datetimeEnd[253]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(255, 6, 5, 3, 1, '{\"1\":[\"23:20\",\"07:50\",\"08:30\"],\"2\":\"6__\",\"3\":\"\"}', 1, '".$datetimeStart[254]."', '".$datetimeEnd[254]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(256, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"6__trifft voll zu\",\"3\":\"5__\",\"4\":\"6__trifft gar nicht zu\",\"5\":\"6__trifft voll zu\",\"6\":\"0__trifft gar nicht zu\",\"7\":\"1__\",\"8\":\"0__trifft gar nicht zu\",\"9\":\"1__\"}', 1, '".$datetimeStart[255]."', '".$datetimeEnd[255]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(257, 6, 5, 3, 1, '{\"1\":[\"00:45\",\"09:45\",\"09:00\"],\"2\":\"2__\",\"3\":\"\"}', 1, '".$datetimeStart[256]."', '".$datetimeEnd[256]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(258, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"5__\",\"3\":\"5__\",\"4\":\"6__trifft gar nicht zu\",\"5\":\"5__\",\"6\":\"0__trifft gar nicht zu\",\"7\":\"1__\",\"8\":\"0__trifft gar nicht zu\",\"9\":\"1__\"}', 1, '".$datetimeStart[257]."', '".$datetimeEnd[257]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(259, 6, 5, 3, 1, '{\"1\":[\"02:25\",\"10:40\",\"08:15\"],\"2\":\"3__schlecht\",\"3\":\"\"}', 1, '".$datetimeStart[258]."', '".$datetimeEnd[258]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(260, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"4__\",\"3\":\"5__\",\"4\":\"5__trifft gar nicht zu\",\"5\":\"4__\",\"6\":\"0__trifft gar nicht zu\",\"7\":\"1__\",\"8\":\"0__trifft gar nicht zu\",\"9\":\"2__\"}', 1, '".$datetimeStart[259]."', '".$datetimeEnd[259]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(261, 6, 5, 3, 1, '{\"1\":[\"00:50\",\"07:15\",\"06:25\"],\"2\":\"5__mittelmäßig\",\"3\":\"\"}', 1, '".$datetimeStart[260]."', '".$datetimeEnd[260]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(262, 6, 6, 4, 1, '{\"1\":\"1__Training\",\"2\":\"100\",\"3\":\"7__Sehr hart\",\"4\":\"Fußball in Drais, wenig gegessen vorher\"}', 1, '".$datetimeStart[261]."', '".$datetimeEnd[261]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(263, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"3__\",\"3\":\"5__\",\"4\":\"5__trifft gar nicht zu\",\"5\":\"2__\",\"6\":\"4__\",\"7\":\"1__\",\"8\":\"0__trifft gar nicht zu\",\"9\":\"5__\"}', 1, '".$datetimeStart[262]."', '".$datetimeEnd[262]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(264, 6, 5, 3, 1, '{\"1\":[\"00:30\",\"06:45\",\"06:15\"],\"2\":\"4__\",\"3\":\"\"}', 1, '".$datetimeStart[263]."', '".$datetimeEnd[263]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(265, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"3__\",\"3\":\"4__\",\"4\":\"5__trifft gar nicht zu\",\"5\":\"4__\",\"6\":\"2__\",\"7\":\"0__trifft gar nicht zu\",\"8\":\"0__trifft gar nicht zu\",\"9\":\"3__\"}', 1, '".$datetimeStart[264]."', '".$datetimeEnd[264]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(266, 6, 5, 3, 1, '{\"1\":[\"23:25\",\"07:50\",\"08:25\"],\"2\":\"3__schlecht\",\"3\":\"\"}', 1, '".$datetimeStart[265]."', '".$datetimeEnd[265]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(267, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"3__\",\"3\":\"2__\",\"4\":\"4__\",\"5\":\"4__\",\"6\":\"1__\",\"7\":\"4__\",\"8\":\"2__\",\"9\":\"2__\"}', 1, '".$datetimeStart[266]."', '".$datetimeEnd[266]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(268, 6, 5, 3, 1, '{\"1\":[\"23:45\",\"07:10\",\"07:25\"],\"2\":\"4__\",\"3\":\"\"}', 1, '".$datetimeStart[267]."', '".$datetimeEnd[267]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(269, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"3__\",\"3\":\"2__\",\"4\":\"5__\",\"5\":\"3__\",\"6\":\"0__trifft gar nicht zu\",\"7\":\"3__\",\"8\":\"1__\",\"9\":\"3__\"}', 1, '".$datetimeStart[268]."', '".$datetimeEnd[268]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(270, 6, 5, 3, 1, '{\"1\":[\"00:35\",\"07:35\",\"07:00\"],\"2\":\"6__\",\"3\":\"\"}', 1, '".$datetimeStart[269]."', '".$datetimeEnd[269]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(271, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"5__\",\"3\":\"5__\",\"4\":\"6__trifft gar nicht zu\",\"5\":\"5__\",\"6\":\"0__trifft gar nicht zu\",\"7\":\"1__\",\"8\":\"0__trifft gar nicht zu\",\"9\":\"1__\"}', 1, '".$datetimeStart[270]."', '".$datetimeEnd[270]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(272, 6, 5, 3, 1, '{\"1\":[\"00:10\",\"09:55\",\"09:45\"],\"2\":\"1__sehr schlecht\",\"3\":\"\"}', 1, '".$datetimeStart[271]."', '".$datetimeEnd[271]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(273, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"4__\",\"3\":\"4__\",\"4\":\"3__\",\"5\":\"4__\",\"6\":\"0__trifft gar nicht zu\",\"7\":\"1__\",\"8\":\"2__\",\"9\":\"2__\"}', 1, '".$datetimeStart[272]."', '".$datetimeEnd[272]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(274, 6, 5, 3, 1, '{\"1\":[\"01:25\",\"09:35\",\"08:10\"],\"2\":\"6__\",\"3\":\"\"}', 1, '".$datetimeStart[273]."', '".$datetimeEnd[273]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(275, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"3__\",\"3\":\"3__\",\"4\":\"4__\",\"5\":\"4__\",\"6\":\"0__trifft gar nicht zu\",\"7\":\"4__\",\"8\":\"1__\",\"9\":\"2__\"}', 1, '".$datetimeStart[274]."', '".$datetimeEnd[274]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(276, 6, 5, 3, 1, '{\"1\":[\"00:25\",\"07:30\",\"07:05\"],\"2\":\"6__\",\"3\":\"\"}', 1, '".$datetimeStart[275]."', '".$datetimeEnd[275]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(277, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"3__\",\"3\":\"3__\",\"4\":\"2__\",\"5\":\"3__\",\"6\":\"0__trifft gar nicht zu\",\"7\":\"4__\",\"8\":\"3__\",\"9\":\"3__\"}', 1, '".$datetimeStart[276]."', '".$datetimeEnd[276]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(278, 6, 5, 3, 1, '{\"1\":[\"22:55\",\"06:50\",\"07:55\"],\"2\":\"4__\",\"3\":\"\"}', 1, '".$datetimeStart[277]."', '".$datetimeEnd[277]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(279, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"4__\",\"3\":\"4__\",\"4\":\"5__\",\"5\":\"4__\",\"6\":\"0__trifft gar nicht zu\",\"7\":\"2__\",\"8\":\"1__\",\"9\":\"1__\"}', 1, '".$datetimeStart[278]."', '".$datetimeEnd[278]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(280, 6, 5, 3, 1, '{\"1\":[\"23:30\",\"07:55\",\"08:25\"],\"2\":\"3__schlecht\",\"3\":\"\"}', 1, '".$datetimeStart[279]."', '".$datetimeEnd[279]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(281, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"4__\",\"3\":\"3__\",\"4\":\"3__\",\"5\":\"4__\",\"6\":\"0__trifft gar nicht zu\",\"7\":\"3__\",\"8\":\"2__\",\"9\":\"2__\"}', 1, '".$datetimeStart[280]."', '".$datetimeEnd[280]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(282, 6, 5, 3, 1, '{\"1\":[\"23:00\",\"08:10\",\"09:10\"],\"2\":\"4__\",\"3\":\"\"}', 1, '".$datetimeStart[281]."', '".$datetimeEnd[281]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(283, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"4__\",\"3\":\"3__\",\"4\":\"3__\",\"5\":\"4__\",\"6\":\"0__trifft gar nicht zu\",\"7\":\"2__\",\"8\":\"3__\",\"9\":\"2__\"}', 1, '".$datetimeStart[282]."', '".$datetimeEnd[282]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(284, 6, 5, 3, 1, '{\"1\":[\"23:10\",\"06:50\",\"07:40\"],\"2\":\"8__\",\"3\":\"\"}', 1, '".$datetimeStart[283]."', '".$datetimeEnd[283]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(285, 6, 6, 4, 1, '{\"1\":\"1__Training\",\"2\":\"45\",\"3\":\"4__Schon härter\",\"4\":\"7km, erst langsam, dann 3 km schneller\"}', 1, '".$datetimeStart[284]."', '".$datetimeEnd[284]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(286, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"5__\",\"3\":\"3__\",\"4\":\"4__\",\"5\":\"5__\",\"6\":\"1__\",\"7\":\"2__\",\"8\":\"1__\",\"9\":\"2__\"}', 1, '".$datetimeStart[285]."', '".$datetimeEnd[285]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(287, 6, 5, 3, 1, '{\"1\":[\"00:15\",\"07:00\",\"06:45\"],\"2\":\"4__\",\"3\":\"\"}', 1, '".$datetimeStart[286]."', '".$datetimeEnd[286]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(288, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"5__\",\"3\":\"4__\",\"4\":\"5__\",\"5\":\"3__\",\"6\":\"1__\",\"7\":\"1__\",\"8\":\"1__\",\"9\":\"2__\"}', 1, '".$datetimeStart[287]."', '".$datetimeEnd[287]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(289, 6, 5, 3, 1, '{\"1\":[\"00:35\",\"10:00\",\"09:25\"],\"2\":\"5__mittelmäßig\",\"3\":\"\"}', 1, '".$datetimeStart[288]."', '".$datetimeEnd[288]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(290, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"4__\",\"3\":\"4__\",\"4\":\"3__\",\"5\":\"4__\",\"6\":\"0__trifft gar nicht zu\",\"7\":\"3__\",\"8\":\"1__\",\"9\":\"2__\"}', 1, '".$datetimeStart[289]."', '".$datetimeEnd[289]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(291, 6, 5, 3, 1, '{\"1\":[\"00:40\",\"07:30\",\"06:50\"],\"2\":\"5__mittelmäßig\",\"3\":\"\"}', 1, '".$datetimeStart[290]."', '".$datetimeEnd[290]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(292, 6, 6, 4, 1, '{\"1\":\"1__Training\",\"2\":\"60\",\"3\":\"4__Schon härter\",\"4\":\"Fußball in Drais, 4vs4 auf halbem Halbfeld, kurz wegen Verletzung von anderem Spieler\"}', 1, '".$datetimeStart[291]."', '".$datetimeEnd[291]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(293, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"3__\",\"3\":\"3__\",\"4\":\"4__\",\"5\":\"3__\",\"6\":\"2__\",\"7\":\"2__\",\"8\":\"1__\",\"9\":\"2__\"}', 1, '".$datetimeStart[292]."', '".$datetimeEnd[292]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(294, 6, 5, 3, 1, '{\"1\":[\"23:30\",\"08:05\",\"08:35\"],\"2\":\"5__mittelmäßig\",\"3\":\"\"}', 1, '".$datetimeStart[293]."', '".$datetimeEnd[293]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(295, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"5__\",\"3\":\"4__\",\"4\":\"5__\",\"5\":\"5__\",\"6\":\"0__trifft gar nicht zu\",\"7\":\"2__\",\"8\":\"1__\",\"9\":\"1__\"}', 1, '".$datetimeStart[294]."', '".$datetimeEnd[294]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(296, 6, 5, 3, 1, '{\"1\":[\"23:50\",\"08:00\",\"08:10\"],\"2\":\"3__schlecht\",\"3\":\"\"}', 1, '".$datetimeStart[295]."', '".$datetimeEnd[295]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(297, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"4__\",\"3\":\"4__\",\"4\":\"5__\",\"5\":\"4__\",\"6\":\"0__trifft gar nicht zu\",\"7\":\"1__\",\"8\":\"1__\",\"9\":\"1__\"}', 1, '".$datetimeStart[296]."', '".$datetimeEnd[296]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(298, 6, 5, 3, 1, '{\"1\":[\"00:10\",\"06:50\",\"06:40\"],\"2\":\"6__\",\"3\":\"\"}', 1, '".$datetimeStart[297]."', '".$datetimeEnd[297]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(299, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"5__\",\"3\":\"5__\",\"4\":\"2__\",\"5\":\"5__\",\"6\":\"0__trifft gar nicht zu\",\"7\":\"4__\",\"8\":\"4__\",\"9\":\"1__\"}', 1, '".$datetimeStart[298]."', '".$datetimeEnd[298]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(300, 6, 5, 3, 1, '{\"1\":[\"23:30\",\"07:00\",\"07:30\"],\"2\":\"4__\",\"3\":\"\"}', 1, '".$datetimeStart[299]."', '".$datetimeEnd[299]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(301, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"5__\",\"3\":\"3__\",\"4\":\"3__\",\"5\":\"4__\",\"6\":\"0__trifft gar nicht zu\",\"7\":\"4__\",\"8\":\"2__\",\"9\":\"2__\"}', 1, '".$datetimeStart[300]."', '".$datetimeEnd[300]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(302, 6, 5, 3, 1, '{\"1\":[\"01:50\",\"10:50\",\"09:00\"],\"2\":\"5__mittelmäßig\",\"3\":\"\"}', 1, '".$datetimeStart[301]."', '".$datetimeEnd[301]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(303, 6, 6, 4, 1, '{\"1\":\"1__Training\",\"2\":\"75\",\"3\":\"3__Moderat\",\"4\":\"10km Stadionrunde mit Martin\"}', 1, '".$datetimeStart[302]."', '".$datetimeEnd[302]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(304, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"4__\",\"3\":\"5__\",\"4\":\"5__\",\"5\":\"4__\",\"6\":\"1__\",\"7\":\"1__\",\"8\":\"2__\",\"9\":\"2__\"}', 1, '".$datetimeStart[303]."', '".$datetimeEnd[303]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(305, 6, 5, 3, 1, '{\"1\":[\"00:50\",\"08:45\",\"07:55\"],\"2\":\"3__schlecht\",\"3\":\"\"}', 1, '".$datetimeStart[304]."', '".$datetimeEnd[304]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(306, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"5__\",\"3\":\"4__\",\"4\":\"5__trifft gar nicht zu\",\"5\":\"5__\",\"6\":\"0__trifft gar nicht zu\",\"7\":\"1__\",\"8\":\"0__trifft gar nicht zu\",\"9\":\"1__\"}', 1, '".$datetimeStart[305]."', '".$datetimeEnd[305]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(307, 6, 5, 3, 1, '{\"1\":[\"01:05\",\"08:55\",\"07:50\"],\"2\":\"3__schlecht\",\"3\":\"\"}', 1, '".$datetimeStart[306]."', '".$datetimeEnd[306]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(308, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"4__\",\"3\":\"4__\",\"4\":\"5__\",\"5\":\"5__\",\"6\":\"0__trifft gar nicht zu\",\"7\":\"2__\",\"8\":\"1__\",\"9\":\"2__\"}', 1, '".$datetimeStart[307]."', '".$datetimeEnd[307]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(309, 6, 5, 3, 1, '{\"1\":[\"02:25\",\"09:35\",\"07:10\"],\"2\":\"5__mittelmäßig\",\"3\":\"\"}', 1, '".$datetimeStart[308]."', '".$datetimeEnd[308]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(310, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"5__\",\"3\":\"4__\",\"4\":\"6__trifft gar nicht zu\",\"5\":\"5__\",\"6\":\"0__trifft gar nicht zu\",\"7\":\"1__\",\"8\":\"0__trifft gar nicht zu\",\"9\":\"1__\"}', 1, '".$datetimeStart[309]."', '".$datetimeEnd[309]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(311, 6, 5, 3, 1, '{\"1\":[\"23:55\",\"10:50\",\"10:55\"],\"2\":\"1__sehr schlecht\",\"3\":\"\"}', 1, '".$datetimeStart[310]."', '".$datetimeEnd[310]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(312, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"5__\",\"3\":\"5__\",\"4\":\"6__trifft gar nicht zu\",\"5\":\"6__trifft voll zu\",\"6\":\"0__trifft gar nicht zu\",\"7\":\"0__trifft gar nicht zu\",\"8\":\"0__trifft gar nicht zu\",\"9\":\"1__\"}', 1, '".$datetimeStart[311]."', '".$datetimeEnd[311]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(313, 6, 5, 3, 1, '{\"1\":[\"01:55\",\"12:50\",\"10:55\"],\"2\":\"3__schlecht\",\"3\":\"\"}', 1, '".$datetimeStart[312]."', '".$datetimeEnd[312]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(314, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"4__\",\"3\":\"3__\",\"4\":\"4__\",\"5\":\"5__\",\"6\":\"0__trifft gar nicht zu\",\"7\":\"2__\",\"8\":\"1__\",\"9\":\"2__\"}', 1, '".$datetimeStart[313]."', '".$datetimeEnd[313]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(315, 6, 5, 3, 1, '{\"1\":[\"01:15\",\"08:55\",\"07:40\"],\"2\":\"6__\",\"3\":\"\"}', 1, '".$datetimeStart[314]."', '".$datetimeEnd[314]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(316, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"5__\",\"3\":\"4__\",\"4\":\"6__\",\"5\":\"5__\",\"6\":\"0__trifft gar nicht zu\",\"7\":\"0__trifft gar nicht zu\",\"8\":\"2__\",\"9\":\"1__\"}', 1, '".$datetimeStart[315]."', '".$datetimeEnd[315]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(317, 6, 5, 3, 1, '{\"1\":[\"01:30\",\"10:20\",\"08:50\"],\"2\":\"4__\",\"3\":\"\"}', 1, '".$datetimeStart[316]."', '".$datetimeEnd[316]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(318, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"4__\",\"3\":\"3__\",\"4\":\"2__\",\"5\":\"3__\",\"6\":\"1__\",\"7\":\"3__\",\"8\":\"4__\",\"9\":\"3__\"}', 1, '".$datetimeStart[317]."', '".$datetimeEnd[317]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(319, 6, 5, 3, 1, '{\"1\":[\"01:30\",\"09:35\",\"08:05\"],\"2\":\"6__\",\"3\":\"\"}', 1, '".$datetimeStart[318]."', '".$datetimeEnd[318]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(320, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"3__\",\"3\":\"4__\",\"4\":\"4__\",\"5\":\"4__\",\"6\":\"1__\",\"7\":\"2__\",\"8\":\"1__\",\"9\":\"2__\"}', 1, '".$datetimeStart[319]."', '".$datetimeEnd[319]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(321, 6, 5, 3, 1, '{\"1\":[\"01:30\",\"08:25\",\"06:55\"],\"2\":\"5__mittelmäßig\",\"3\":\"\"}', 1, '".$datetimeStart[320]."', '".$datetimeEnd[320]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(322, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"4__\",\"3\":\"4__\",\"4\":\"3__\",\"5\":\"3__\",\"6\":\"0__trifft gar nicht zu\",\"7\":\"4__\",\"8\":\"2__\",\"9\":\"3__\"}', 1, '".$datetimeStart[321]."', '".$datetimeEnd[321]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(323, 6, 5, 3, 1, '{\"1\":[\"00:50\",\"08:15\",\"07:25\"],\"2\":\"5__mittelmäßig\",\"3\":\"\"}', 1, '".$datetimeStart[322]."', '".$datetimeEnd[322]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(324, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"3__\",\"3\":\"4__\",\"4\":\"3__\",\"5\":\"4__\",\"6\":\"0__trifft gar nicht zu\",\"7\":\"2__\",\"8\":\"1__\",\"9\":\"1__\"}', 1, '".$datetimeStart[323]."', '".$datetimeEnd[323]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(325, 6, 5, 3, 1, '{\"1\":[\"01:05\",\"08:55\",\"07:50\"],\"2\":\"5__mittelmäßig\",\"3\":\"\"}', 1, '".$datetimeStart[324]."', '".$datetimeEnd[324]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(326, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"2__\",\"3\":\"2__\",\"4\":\"4__\",\"5\":\"2__\",\"6\":\"0__trifft gar nicht zu\",\"7\":\"3__\",\"8\":\"1__\",\"9\":\"4__\"}', 1, '".$datetimeStart[325]."', '".$datetimeEnd[325]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(327, 6, 5, 3, 1, '{\"1\":[\"02:50\",\"08:40\",\"05:50\"],\"2\":\"4__\",\"3\":\"\"}', 1, '".$datetimeStart[326]."', '".$datetimeEnd[326]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(328, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"5__\",\"3\":\"5__\",\"4\":\"6__trifft gar nicht zu\",\"5\":\"5__\",\"6\":\"0__trifft gar nicht zu\",\"7\":\"0__trifft gar nicht zu\",\"8\":\"0__trifft gar nicht zu\",\"9\":\"1__\"}', 1, '".$datetimeStart[327]."', '".$datetimeEnd[327]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(329, 6, 5, 3, 1, '{\"1\":[\"02:35\",\"11:20\",\"08:45\"],\"2\":\"3__schlecht\",\"3\":\"\"}', 1, '".$datetimeStart[328]."', '".$datetimeEnd[328]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(330, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"4__\",\"3\":\"5__\",\"4\":\"5__\",\"5\":\"5__\",\"6\":\"0__trifft gar nicht zu\",\"7\":\"1__\",\"8\":\"1__\",\"9\":\"1__\"}', 1, '".$datetimeStart[329]."', '".$datetimeEnd[329]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(331, 6, 5, 3, 1, '{\"1\":[\"23:10\",\"07:05\",\"07:55\"],\"2\":\"4__\",\"3\":\"\"}', 1, '".$datetimeStart[330]."', '".$datetimeEnd[330]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(332, 6, 6, 4, 1, '{\"1\":\"1__Training\",\"2\":\"90\",\"3\":\"4__Schon härter\",\"4\":\"Fußball in Drais\"}', 1, '".$datetimeStart[331]."', '".$datetimeEnd[331]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(333, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"3__\",\"3\":\"3__\",\"4\":\"4__\",\"5\":\"3__\",\"6\":\"2__\",\"7\":\"2__\",\"8\":\"1__\",\"9\":\"3__\"}', 1, '".$datetimeStart[332]."', '".$datetimeEnd[332]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(334, 6, 5, 3, 1, '{\"1\":[\"23:50\",\"07:00\",\"07:10\"],\"2\":\"4__\",\"3\":\"\"}', 1, '".$datetimeStart[333]."', '".$datetimeEnd[333]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(335, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"4__\",\"3\":\"4__\",\"4\":\"5__\",\"5\":\"3__\",\"6\":\"1__\",\"7\":\"2__\",\"8\":\"1__\",\"9\":\"3__\"}', 1, '".$datetimeStart[334]."', '".$datetimeEnd[334]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(336, 6, 5, 3, 1, '{\"1\":[\"23:30\",\"06:50\",\"07:20\"],\"2\":\"4__\",\"3\":\"\"}', 1, '".$datetimeStart[335]."', '".$datetimeEnd[335]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(337, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"3__\",\"3\":\"3__\",\"4\":\"5__\",\"5\":\"4__\",\"6\":\"1__\",\"7\":\"2__\",\"8\":\"1__\",\"9\":\"2__\"}', 1, '".$datetimeStart[336]."', '".$datetimeEnd[336]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(338, 6, 5, 3, 1, '{\"1\":[\"23:00\",\"07:00\",\"08:00\"],\"2\":\"4__\",\"3\":\"\"}', 1, '".$datetimeStart[337]."', '".$datetimeEnd[337]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(339, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"3__\",\"3\":\"3__\",\"4\":\"2__\",\"5\":\"3__\",\"6\":\"0__trifft gar nicht zu\",\"7\":\"4__\",\"8\":\"2__\",\"9\":\"4__\"}', 1, '".$datetimeStart[338]."', '".$datetimeEnd[338]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(340, 6, 5, 3, 1, '{\"1\":[\"00:05\",\"07:00\",\"06:55\"],\"2\":\"4__\",\"3\":\"\"}', 1, '".$datetimeStart[339]."', '".$datetimeEnd[339]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(341, 6, 4, 3, 1, '{\"1\":\"\",\"2\":\"5__\",\"3\":\"4__\",\"4\":\"5__trifft gar nicht zu\",\"5\":\"5__\",\"6\":\"0__trifft gar nicht zu\",\"7\":\"1__\",\"8\":\"0__trifft gar nicht zu\",\"9\":\"1__\"}', 1, '".$datetimeStart[340]."', '".$datetimeEnd[340]."', 'Auto_Init', '".$datetime."', 'Auto_Init'),".
		"(342, 6, 5, 3, 1, '{\"1\":[\"00:40\",\"09:25\",\"08:45\"],\"2\":\"4__\",\"3\":\"\"}', 1, '".$datetimeStart[341]."', '".$datetimeEnd[341]."', 'Auto_Init', '".$datetime."', 'Auto_Init');";		
}