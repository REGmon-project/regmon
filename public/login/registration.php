<?php // resisters a new user and send emails to admins to activate the account

$uname = isset($_POST['uname']) ? $_POST['uname'] : exit;

// load language & database ##########
require_once('no_validate.php');
// ###################################
$PATH_2_ROOT = '../';

$register_ERROR = '';

//the following check is additional so the $db->insert not return duplicate error
$row = $db->fetchRow("SELECT * FROM users WHERE uname = ?", array($uname)); 
if ($db->numberRows() > 0)  {
	$register_ERROR = $LANG->WARN_USERNAME_EXIST;
}
else {
	$location_name = '';
	$group_name = '';
	
	$values = array();			
	foreach ($_POST as $key => $val) {
		$key = trim((string)$key); 
		if ($key != 'sport') $val = trim((string)$val); 
		switch($key) {
			//case 'account': 
			case 'uname': 
			case 'passwd': 
			case 'lastname': 
			case 'firstname': 
			case 'body_height': 
			case 'sex': 
			case 'email': 
			//case 'telephone': 
			//case 'sport': 
			//case 'year': 
			//case 'month': 
			//case 'day': 
			//case 'level': 
			//case 'status':
				$values[$key] = $val;
			  break;
		}
	}		

	// Check if all fields are filled up
	if (trim($values['uname']) == '') {
		echo $LANG->WARN_EMPTY_USERNAME;
		exit;
	}
	elseif (trim($values['passwd']) != trim($_POST['pass_confirm'])) {
		echo $LANG->WARN_CONFIRM_PASSWORD; 
		exit;
	}

	//check pass < 8 chars
	if (strlen($values['passwd']) < 8) {
		echo $LANG->WARN_PASSWORD_CHARS;
		exit;
	}

	//check password strength
	if (!(preg_match("#[0-9]+#", $values['passwd']) AND //one number
		  preg_match("#[a-z]+#", $values['passwd']) AND //one a-z
		  preg_match("#[A-Z]+#", $values['passwd']))) //one A-Z
	{
		echo $LANG->WARN_WEAK_PASSWORD;
		exit;
	}
		
	$values['passwd'] = hash_Password($values['passwd']);
	$values['account'] = 'user';
	$values['level'] = 10;
	$values['status'] = 0;
	$values['telephone'] = $_POST['countryCode'].' '.$_POST['telephone'];
	if ($_POST['year'] != '' AND $_POST['month'] != '' AND $_POST['day'] != '') {
		$values['birth_date'] = $_POST['year'].'-'.$_POST['month'].'-'.$_POST['day'];
	}
	$level = explode('_', $_POST['profile']??'10_Athlete');
	$values['level'] = $level[0];
	$profile = $level[1];

	if ($_POST['location_group'] == 'Private') {
		$gr_loc = $db->fetchRow("SELECT gr.id, gr.location_id, gr.status, gr.private_key, gr.name, l.name AS location_name 
FROM `groups` gr 
LEFT JOIN locations l ON l.id = gr.location_id 
WHERE gr.status = 3 AND gr.private_key = ?", array($_POST['private_key'])); 
		if ($db->numberRows() > 0)  {
			$values['location_id'] 	= $gr_loc['location_id'];
			$location_name 			= $gr_loc['location_name'];
			$values['group_id'] 	= $gr_loc['id'];
			$group_name 			= $gr_loc['name'];
		}
		else {
			$register_ERROR = $LANG->REGISTER_PRIVATE_KEY_ERROR;
		}
	}
	else {
		$location_group = explode('|', $_POST['location_group'].'|||');
		$values['location_id'] 	= $location_group[0];
		$location_name 			= $location_group[1];
		$values['group_id'] 	= $location_group[2];
		$group_name 			= $location_group[3];
	}

	$values['last_ip'] = '';
	$values['modified'] = get_date_time_SQL('now');
	$values['created'] = get_date_time_SQL('now');

	$activate_code = hash_Secret($CONFIG['SEC_Encrypt_Secret'] . $values['account'] . $values['uname'] . $values['passwd'], $CONFIG['SEC_Encrypt_Secret']);
	
	//check if we have a new sport to activate
	$values['sport'] = '';
	$sport_to_user = '';
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
			if ($values['sport'] != '') {
				$values['sport'] .= ', ';
			}
			if ($sport_to_user != '') {
				$sport_to_user .= ', ';
			}
			if ($sport_to_admin != '') {
				$sport_to_admin .= ', ';
			}

			//if sport not exist
			if (!in_array($sport, $sports_arr)) {
				$activate_sport_code = MD5($CONFIG['SEC_Encrypt_Secret'] . $values['uname'].$sport);
				$activate_sport_link = "<a href='".$CONFIG['HTTP'].$CONFIG['DOMAIN'].'/'.$CONFIG['REGmon_Folder']."login/new_sport_suggestion.php?sport=".$sport."&uname=".$values['uname']."&code=".$activate_sport_code."' target='_blank'>".$LANG->REGISTER_APPROVE_PROPOSAL."</a>";
				
				$sport_to_user .= '<u style="color:blue;">'.$sport.' ('.$LANG->REGISTER_APPROVE_WAIT.')</u>';
				$sport_to_admin .= '<u style="color:blue;">'.$sport.' ('.$activate_sport_link.')</u>';
			}
			else {
				$values['sport'] .= $sport;
				$sport_to_user .= $sport;
				$sport_to_admin .= $sport;
			}
		}
	}
	
	//Insert
	$insert_id = $db->insert($values, "users");

	if (!$insert_id) {
		$register_ERROR = $LANG->INSERT_ERROR;
	}
	else {
		if (substr_count($insert_id, 'Duplicate entry') <> 0) { //db give the error into insert_id
			$register_ERROR = $LANG->WARN_USERNAME_EXIST;
		}
		else {

			//group access - insert
			$values2 = array();			
			$values2['user_id'] = $insert_id;
			$values2['group_id'] = $values['group_id'];
			$values2['status'] = '10';
			$values2['created'] = get_date_time_SQL('now');
			$values2['created_by'] = $values['uname'];
			$values2['modified'] = get_date_time_SQL('now');
			$values2['modified_by'] = $values['uname'];
			$users2groups = $db->insert($values2, "users2groups");
			
			// Email ##############################
			require($PATH_2_ROOT.'php/inc.email.php');
			
			//New user account email
			$profile_title = $profile;
			$profile = $level[0] > 10 ? '<b>'.$profile.'</b>' : $profile;

			$params_Subject = [
				'{HTTP}' => $CONFIG['HTTP'],
				'{DOMAIN}' => $CONFIG['DOMAIN'],
				'{REGmon_Folder}' => $CONFIG['REGmon_Folder'],
				'{Group}' => $group_name,
				'{Sport}' => $sport_to_user
			];
			$Subject = strtr($LANG->EMAIL_NEW_ACCOUNT_SUBJECT, $params_Subject);

			$params_Message = [
				'{Username}' => $_POST['uname'],
				'{Lastname}' => $_POST['lastname'],
				'{Firstname}' => $_POST['firstname'],
				'{Sport}' => $sport_to_user,
				'{Body_Height}' => $_POST['body_height'],
				// '{Gender}' => ($_POST['sex']=='0' ? 'Männlich' : ($_POST['sex']=='1' ? 'Weiblich' : 'Divers')),
				'{Email}' => $_POST['email'],
				'{Telephone}' => $_POST['countryCode'].' '.$_POST['telephone'],
				'{Location}' => $location_name,
				'{Group}' => $group_name,
				'{Profile}' => $profile,
				'{HTTP}' => $CONFIG['HTTP'],
				'{DOMAIN}' => $CONFIG['DOMAIN'],
				'{REGmon_Folder}' => $CONFIG['REGmon_Folder']
			];
			//replace strings
			$Message = strtr($LANG->EMAIL_NEW_ACCOUNT_MESSSAGE, $params_Message);

			if (SendEmail($_POST['email'], $Subject, $Message) == 'OK') {}
			else error_log($_POST['email'].', '. $Subject.', User Email Not Send');

			
			//Admin email for activation of new user account
			$admin_email = array();
			$admin_rows = $db->fetch("SELECT u.lastname, u.email FROM users u
				LEFT JOIN `groups` gr ON gr.id = ?
				WHERE FIND_IN_SET( u.id, gr.admins_id )", array($values['group_id']));
			if (!$db->numberRows()) {
				//get the admin email if the group not have an group admin
				$admin_rows = $db->fetch("SELECT lastname, email FROM users WHERE account = 'admin' AND level = 99", array());
			}
			foreach ($admin_rows as $admin) {
				$activate_link = "<a href='".$CONFIG['HTTP'].$CONFIG['DOMAIN'].'/'.$CONFIG['REGmon_Folder']."login/account_activation.php?uname=".$values['uname']."&code=".$activate_code."&adm=".$admin['lastname']."' target='_blank'>";

				$Subject_admin = strtr($LANG->EMAIL_NEW_ACCOUNT_ADMIN_SUBJECT, $params_Subject);

				//overwrite some admin vals
				$params_Message['{Sport}'] = $sport_to_admin;
				//here may have new sports activation link
				$params_Message['{Activate_Link}'] = $activate_link;

				$Message_admin = strtr($LANG->EMAIL_NEW_ACCOUNT_ADMIN_MESSSAGE, $params_Message);
				
				if (SendEmail($admin['email'], $Subject_admin, $Message_admin) == 'OK') {}
				else error_log($Subject_admin.', Admin Email Not Send');
			}
		}
	} //if insert_id
}

//#######################################
$title = $LANG->REGISTER_PAGE_TITLE;
require($PATH_2_ROOT.'php/inc.html_head.php');
//#######################################
?>
</head>
<body>

<?php require($PATH_2_ROOT.'php/inc.header.php');?>

<div style="text-align:center;">
	<a href="<?=$PATH_2_ROOT;?>" id="home" class="home"> &nbsp; <?=$LANG->HOMEPAGE;?></a>
</div>

<div class="container">
	<div class="row">
        <div class="col-md-12" style="text-align:center; padding-top:80px;">
		<?php if ($register_ERROR != '') { ?>
			<h1 style="color:#333"><?=$LANG->ERROR;?></h1>
			<br>
			<h3 style="color: #ff0000"><?=$register_ERROR;?></h3>
		<?php } else { ?>
			<!-- <h1 style="color:#333"><?=$LANG->REGISTER_THANKS;?></h1>
			<br> -->
			<h3 style="color: #6C3"><?=$LANG->REGISTER_SUBMIT_SUCCESS;?></h3>
			<h3 style="color: #ffbf00"><?=$LANG->REGISTER_SUBMIT_WAIT_ACTIV;?></h3>
			<?php /*<p>You will be redirect back in 5 seconds.</p>*/?>
		<?php } ?>
        </div>
	</div>
</div>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<?php //require($PATH_2_ROOT.'php/inc.footer.php');?>

</body>
</html>