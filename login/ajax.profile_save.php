<?php
$PATH_2_ROOT = '../';
require_once($PATH_2_ROOT.'_settings.regmon.php');
require('validate.php');

$values = array();			
foreach ($_POST as $key => $val) {
	$key = trim((string)$key); 
	if ($key != 'sport') $val = trim((string)$val); 
	switch($key) {
		//case 'account': 
		case 'uname': 
		case 'lastname': 
		case 'firstname': 
		case 'body_height': 
		case 'sex': 
		case 'email': 
		case 'telephone': //with countryCode
		case 'dashboard': 
		//case 'sport': 
		//case 'year': 
		//case 'month': 
		//case 'day': 
			$values[$key] = $val;
		  break;
	}
}		

// Check if all fields are filled up
if (trim($values['uname']) == '') {
	echo $LANG->WARN_EMPTY_USERNAME;
	exit;
}
if (trim($_POST['passwd']) != '') {
	if (trim($_POST['passwd']) != trim($_POST['pass_confirm'])) {
		echo $LANG->WARN_CONFIRM_PASSWORD;
		exit;
	}

	//check pass < 8 chars
	if (strlen($_POST['passwd']) < 8) {
		echo $LANG->WARN_PASSWORD_CHARS;
		exit;
	}

	//check password strength
	if (!(preg_match("#[0-9]+#", $_POST['passwd']) AND //one number
		  preg_match("#[a-z]+#", $_POST['passwd']) AND //one a-z
		  preg_match("#[A-Z]+#", $_POST['passwd']))) //one A-Z
	{
		echo $LANG->WARN_WEAK_PASSWORD;
		exit;
	}
		
	$values['passwd'] = hash_Password($_POST['passwd']);
}

if ($_POST['birth_year'] != '' AND $_POST['birth_month'] != '' AND $_POST['birth_day'] != '') {
	$values['birth_date'] = $_POST['birth_year'].'-'.$_POST['birth_month'].'-'.$_POST['birth_day'];
}
$values['modified'] = get_date_time_SQL('now');

$location_name = $_POST['location_name'];
$group_id = $_POST['group_id'];
$group_name = $_POST['group_name'];
$level_id = $_POST['level_id'];
$profile = $_POST['profile'];

//check if we have a new sport to activate
$values['sport'] = '';
$sport_new = '';
$sport_to_admin = '';
if (isset($_POST['sport']) AND count($_POST['sport'])) {
	$sports_arr = array();
	$rows = $db->fetch("SELECT options FROM sports WHERE status = 1 AND parent_id != 0 ORDER BY options", array()); 
	if ($db->numberRows() > 0)  {
		foreach ($rows as $row) {
			$sports_arr[] = $row['options'];
		}
	}
	foreach ($_POST['sport'] as $sport) {
		if ($values['sport'] != '') $values['sport'] .= ', ';
		if ($sport_to_admin != '') $sport_to_admin .= ', ';
		if ($sport_new != '') $sport_new .= ', ';
		//if sport not exist
		if (!in_array($sport, $sports_arr)) {
			$activate_sport_code = MD5($CONFIG['SEC_Encrypt_Secret'] . $values['uname'].$sport);
			$activate_sport_link = "<a href='".$CONFIG['HTTP'].$CONFIG['DOMAIN'].'/'.$CONFIG['REGmon_Folder']."login/new_sport_suggestion.php?sport=".$sport."&uname=".$values['uname']."&code=".$activate_sport_code."' target='_blank'>".$LANG->REGISTER_APPROVE_PROPOSAL."</a>";

			$sport_new .= $sport;
			$sport_to_admin .= '<u style="color:blue;">'.$sport.' ('.$activate_sport_link.')</u>';
		}
		else {
			$values['sport'] .= $sport;
			$sport_to_admin .= $sport;
		}
	}
}

//Update
$update = $db->update($values, "users", "id=?", array($UID));


if ($sport_new != '') {
	// Email #######################################################
	require($PATH_2_ROOT.'php/inc.email.php');

	//Admin email for activation of new sport
	$admin_rows = $db->fetch("SELECT u.lastname, u.email FROM users u
		LEFT JOIN `groups` gr ON gr.id = ?
		WHERE FIND_IN_SET( u.id, gr.admins_id )", array($group_id));
	if ($db->numberRows() > 0)  {} //group admins exist
	else { //else get the admin email
		$admin_rows = $db->fetch("SELECT name, email FROM users WHERE level='99'", array());
	}
	
	//sent email to each group admin
	foreach ($admin_rows as $admin) {
		//Admin email for activation of new Sport
		$profile = ($level_id>10?'<b>'.$profile.'</b>':$profile);
		$Subject_admin = str_replace('{Sports}', $sport_new, $LANG->EMAIL_NEW_SPORTS_ADMIN_SUBJECT);
		/** @var string $Message_admin */
		$Message_admin = str_replace('{Username}', $_POST['uname'], $LANG->EMAIL_NEW_SPORTS_ADMIN_MESSSAGE);
		$Message_admin = str_replace('{Lastname}', $_POST['lastname'], $Message_admin);
		$Message_admin = str_replace('{Firstname}', $_POST['firstname'], $Message_admin);
		$Message_admin = str_replace('{Sport}', $sport_to_admin, $Message_admin);
		$Message_admin = str_replace('{Email}', $_POST['email'], $Message_admin);
		$Message_admin = str_replace('{Telephone}', $_POST['telephone'], $Message_admin);
		$Message_admin = str_replace('{Location}', $location_name, $Message_admin);
		$Message_admin = str_replace('{Group}', $group_name, $Message_admin);
		$Message_admin = str_replace('{Profile}', $profile, $Message_admin);

		if (SendEmail($admin['email'], $Subject_admin, $Message_admin) == 'OK') {}
		else error_log($Subject_admin.', Admin Email Not Send');
	}
}


$success = '<strong>'.$LANG->SUCCESS.'!</strong> '.$LANG->PROFILE_SAVED.' &nbsp; ';
if ($sport_new != '') {
	$success = '<br><br>'.$LANG->SPORTS_NEW_NEED_APPROVE.'<br>'.$sport_new.' &nbsp; ';
}
$error = '<strong>'.$LANG->ERROR.'!</strong> '.$LANG->PROFILE_NOT_SAVED;
?>
<script>
<?php if ($update) { ?>
	parent.Swal({
		type: 'success',
		title: '<?=$success;?>',
		width: '450px'
		//showConfirmButton: false,
		//timer: 2000
	});
	//update dashboard checkbox
	<?php if ($values['dashboard'] == '0') { ?>
		$('#open_dashboard_onlogin').prop('checked', false);
	<?php } else { ?>
		$('#open_dashboard_onlogin').prop('checked', true);
	<?php } ?>
<?php } else { ?>
	parent.Swal({
		type: 'error',
		title: '<?=$error;?>',
		width: '450px'
		//showConfirmButton: false,
		//timer: 2000
	});
<?php } ?>
</script>
