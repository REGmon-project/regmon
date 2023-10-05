<?php // account activation

$uname = isset($_GET['uname'])?$_GET['uname']: exit;
$adm = isset($_GET['adm'])?$_GET['adm']: exit;
$code = isset($_GET['code'])?$_GET['code']: exit;

// load language & database ##########
require_once('no_validate.php');
// ###################################
$PATH_2_ROOT = '../';


$message = '';
$user = $db->fetchRow("SELECT * FROM users WHERE uname = ?", array($uname)); 
if ($db->numberRows() > 0)  {
	if ($user['status'] == '1') {
		$message = $LANG->REGISTER_ACTIVATE_READY;
	}
	else {
		//$activate_code = MD5($user['account'].$user['uname'].$user['passwd']);
		$activate_code = $CONFIG['SEC_Encrypt_Secret'] . $user['account'] . $user['uname'] . $user['passwd'];

		//if ($activate_code == $code) {
		if (verify_Secret($code, $activate_code, $CONFIG['SEC_Encrypt_Secret'])) {
			$message = $LANG->REGISTER_ACTIVATE_OK; 
			$values = array();
			$values['status'] = '1';
			$result = $db->update($values, "users", "uname=?", array($uname));
			
			//group access
			$values = array();
			$values['user_id'] = $user['id'];
			$values['group_id'] = $user['group_id'];
			$values['status'] = '1';
			$values['created'] = $user['created'];
			$values['created_by'] = $user['uname'];
			$values['modified'] = get_date_time_SQL('now');
			$values['modified_by'] = $adm;
			$save = $db->update($values, "users2groups", "user_id=? AND group_id=?", array($user['id'], $user['group_id']));
			
			
			// Email ##############################################
			require($PATH_2_ROOT.'php/inc.email.php');

			/** @var string $Email_Subject */
			$Email_Subject = str_replace('{Username}', $user['uname'], $LANG->EMAIL_ACCOUNT_ACTIVATE_SUBJECT);
			$Email_Subject = str_replace('{HTTP}', $CONFIG['HTTP'], $Email_Subject);
			$Email_Subject = str_replace('{DOMAIN}', $CONFIG['DOMAIN'], $Email_Subject);
			$Email_Subject = str_replace('{REGmon_Folder}', $CONFIG['REGmon_Folder'], $Email_Subject);

			/** @var string $Email_Message */
			$Email_Message = str_replace('{Username}', $user['uname'], $LANG->EMAIL_ACCOUNT_ACTIVATE_MESSSAGE);
			$Email_Message = str_replace('{HTTP}', $CONFIG['HTTP'], $Email_Message);
			$Email_Message = str_replace('{DOMAIN}', $CONFIG['DOMAIN'], $Email_Message);
			$Email_Message = str_replace('{REGmon_Folder}', $CONFIG['REGmon_Folder'], $Email_Message);
						
			if (SendEmail($user['email'], $Email_Subject, $Email_Message) == 'OK') {}
			else error_log($user['email'].', '. $Email_Subject.', Activate User Email Not Send');
		}
		else {
			$message = $LANG->REGISTER_ACTIVATE_CODE_ERROR;
		}
	}
}
else {
	//Account not exists
	$message = $LANG->REGISTER_ACTIVATE_NO_USER;
}		

//#####################################################################################
$title = $LANG->REGISTER_PAGE_TITLE;
require($PATH_2_ROOT.'php/inc.html_head.php');
//#####################################################################################
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
			<!-- <h1 style="color:#333"><?=$LANG->REGISTER;?>!</h1> -->
			<h3 style="color: #6C3"><?=$message;?></h3>
        </div>
	</div>
</div>
<br>
<br>
<?php //require('php/inc.footer.php');?>

</body>
</html>