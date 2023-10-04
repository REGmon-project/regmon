<?php // ajax Config - test email configuration

declare(strict_types=1);

$PATH_2_ROOT = '../';
require_once($PATH_2_ROOT.'_settings.regmon.php');
require($PATH_2_ROOT.'login/validate.php');

$action = isset($_GET['oper']) ? $_GET['oper'] : '';


switch ($action) {

	case 'Test_Email_Config':

		//override CONFIG EMAIL
		$CONFIG['EMAIL'] = array(
			'Host' => $_POST['Host'] ?? '',
			'Port' => $_POST['Port'] ?? '',
			'Username' => $_POST['Username'] ?? '',
			'Password' => Encrypt_String($_POST['Password'] ?? ''),
			'From_Name' => $_POST['From_Name'] ?? '',
			'From_Email' => $_POST['From_Email'] ?? '',
			'Reply_Name' => $_POST['Reply_Name'] ?? '',
			'Reply_Email' => $_POST['Reply_Email'] ?? '',
			'Support' => $_POST['Support'] ?? ''
		);

		require($PATH_2_ROOT.'php/inc.email.php');

		$Subject = 'Test Email SMTP Config - REGmon';
		$Message = 'Test Email SMTP Config';
		
		echo SendEmail($_POST['Your_Test_Email'] ?? '', $Subject, $Message);

	  break;

}

?>