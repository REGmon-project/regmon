<?php //ajax router

declare(strict_types=1);

$PATH_2_ROOT = '../';
require_once($PATH_2_ROOT.'_settings.regmon.php');
require($PATH_2_ROOT.'login/validate.php');
require_once($PATH_2_ROOT.'php/inc.common_functions.php');

if (!isset($_REQUEST['i'])) {
	exit;
}


$ajax = $_REQUEST['i'];
$action = $_REQUEST['oper'] ?? '';
$id = (int)($_REQUEST['id'] ?? 0); //db_id
$ID = (int)($_REQUEST['ID'] ?? 0); //regmon_id

$sidx = $_REQUEST['sidx'] ?? ''; // get index for sorting
$sord = $_REQUEST['sord'] ?? ''; // get sorting direction

$response = new stdClass();

$where = '';

switch($ajax) {
	case 'users':
	case 'locations':
	case 'groups':
	case 'forms':
	case 'forms_data':
	case 'categories':
	case 'forms2categories':
	case 'dropdowns':
	case 'sports':
	case 'tags':
	case 'templates':
	//case 'users_files':
	//case 'importTrackers':
	//case 'config': //not here -it is a standalone page
		if (file_exists('ajax.'.$ajax.'.php')) {
			include('ajax.'.$ajax.'.php');
		}
  break;
}
	

////////////////////////////////////
function check_update_result(mixed $result):string {
	global $LANG, $db;
	
	if ($result >= 1) {
		return 'OK_update';
	}
	elseif ($result === 0) {
		return $LANG->UPDATE_NOTHING;
	}
	else {
		if (substr_count($db->_error(), 'Duplicate entry') <> 0) {
			return $LANG->UPDATE_ERROR;
		}
		else {
			return $LANG->UPDATE_ERROR;
			//return 'Error! '.$db->_error();
		}
	}
}

////////////////////////////////////
function check_insert_result(mixed $insert_id):string {
	global $LANG, $db;
	
	if (is_int($insert_id)) {
		return 'OK_insert';
	}
	elseif (is_bool($insert_id) AND !$insert_id) { 
		if (substr_count($db->_error(), 'Duplicate entry') <> 0) {
			return $LANG->WARN_USERNAME_EXIST;
		}
		else {
			return $LANG->INSERT_ERROR;
			//return 'Error! '.$db->_error();
		}
	}
	else {
		//we get the error in $insert_id --instead of $db->_error()
		if (is_string($insert_id)) {
			if (substr_count($insert_id, 'Duplicate entry') <> 0) {
				return $LANG->WARN_USERNAME_EXIST;
			}
			else {
				return $insert_id;
			}
		}
		
		return 'OK_insert';
	}
}

////////////////////////////////////
function check_delete_result(mixed $result):string {
	global $LANG;
	
	if (!$result) {
		return $LANG->DELETE_ERROR; //echo mysql_error();
	}
	else {
		return 'OK_delete';
	}
}

?>