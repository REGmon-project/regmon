<?php // validate that the user is logged in
// can be used by any page you want to protect

$PATH_2_ROOT = '../';
require_once(__DIR__.'/'.$PATH_2_ROOT.'_settings.regmon.php');

require_once(__DIR__.'/'.$PATH_2_ROOT.'login/inc.login_functions.php');


$login = $PATH_2_ROOT.$CONFIG['REGmon_Folder'].'login.php'; //login page
$logout = $PATH_2_ROOT.$CONFIG['REGmon_Folder'].'login/logout.php'; //logout page

$this_page = substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],'/')+1);

$UID = false;

// Check if the cookies are set
if (isset($_COOKIE['USERNAME']) AND isset($_COOKIE['UID'])) {

	//Get values from cookies
	$UID = (int)($_COOKIE['UID'] ?? 0);
	$USERNAME = $_COOKIE['USERNAME'] ?? '';
	$HASH = $_COOKIE['HASH'] ?? '';
	$UIP = $_SERVER['REMOTE_ADDR'] ?? '';	

	$USER = $db->fetchRow("SELECT * FROM users WHERE id=? AND uname=? AND status = 1", array($UID, $USERNAME));
	if ($db->numberRows())  {
		$ACCOUNT = $USER['account'];
		$USERNAME = $USER['uname'];
		$LEVEL = $USER['level'];
		$STATUS = $USER['status'];
		$GROUP = $USER['group_id'];
		$LOCATION = $USER['location_id'];
		
		/**
		 * check if user HASH match
		 * include username, password, (IP) 
		 * so if any of them changes, logout the user
		 */
		$hash_string = $CONFIG['SEC_Hash_Secret'] . $USERNAME . ($CONFIG['SEC_Hash_IP'] ? $UIP : '') . $USER['passwd'];
		unset($USER['passwd']);
		
		//validate HASH
		if (!verify_Secret($HASH, $hash_string, $CONFIG['SEC_Hash_Secret'])) {
			//################################
			header( 'Location: ' . $logout );
			//################################
			exit;
		}
		
		//global vars
		$ADMIN = $LOCATION_ADMIN = $THIS_LOCATION_ADMIN = $GROUP_ADMIN = $GROUP_ADMIN_2 = $THIS_GROUP_ADMIN = $THIS_GROUP_ADMIN_2 = $TRAINER = $ATHLETE = false;
		$USER_TYPE = ''; //not used at the moment
		$THIS_GROUP = $GROUP;
		
		if ($LEVEL == 99) {
			$ADMIN = true;
			$USER_TYPE = 'ADMIN';
		}
		elseif ($LEVEL == 50) {
			$LOCATION_ADMIN = true;
			$USER_TYPE = 'LOCATION_ADMIN';
			
			//check if it is the Current Location Admin
			$ST_admin = $db->fetchRow('SELECT admin_id FROM locations WHERE id = ? AND status = 1 AND admin_id = ? ORDER BY id', array($LOCATION, $UID)); 
			if ($db->numberRows() > 0)  {
				if ($ST_admin['admin_id'] == $UID) {
					$THIS_LOCATION_ADMIN = true;
				}
			}
		}
		elseif ($LEVEL == 40 OR $LEVEL == 45) {
			if ($LEVEL == 45) {
				$GROUP_ADMIN = true;
				$USER_TYPE = 'GROUP_ADMIN';
			}
			elseif ($LEVEL == 40) {
				$GROUP_ADMIN_2 = true;
				$USER_TYPE = 'GROUP_ADMIN_2';
			}
			
			//check if it is the Current Group Admin
			//at forms_results.php it takes another group_id
			if ($this_page == 'forms_results.php' AND isset($_REQUEST['group_id'])) {
				$THIS_GROUP = $_REQUEST['group_id'];
			}
			$GR_admin = $db->fetchRow('SELECT id, name, admins_id FROM `groups` WHERE id = ? AND location_id = ? AND status > 0 ORDER BY id', array($THIS_GROUP, $LOCATION)); 
			if ($db->numberRows() > 0)  {
				if (in_array($UID, explode(',', $GR_admin['admins_id']??''))) {
					if ($LEVEL == 45) {
						$THIS_GROUP_ADMIN = true;
					}
					elseif ($LEVEL == 40) {
						$THIS_GROUP_ADMIN_2 = true;
					}
				}
			}
		}
		elseif ($LEVEL == 30) {
			$TRAINER = true;
			$USER_TYPE = 'TRAINER';
		}
		elseif ($LEVEL == 10) {
			$ATHLETE = true;
			$USER_TYPE = 'ATHLETE';
		}
		else { //unknown level
			$USER = false;
		}
	}
	else {
		$USER = false;
	}
}
else {
	$USER = false;
}


if ($USER === false) {
	if ($this_page == 'ajax.php') {
		echo 'session expired';
	}
	elseif ($this_page == 'ajax.grid.group_users.php') {
		echo 'session expired';
	}
	elseif ($this_page == 'ajax.forms_menu.php') {
		echo '<script>parent.location.href="'.$login.'";</script>';
	}
	elseif ($this_page == 'ajax.requests_count.php') {
		echo 'login';
	}
	else {
		//################################
		header( 'Location: ' . $login );
		//################################
	}
	exit;
}


//continue to the page that call this
?>