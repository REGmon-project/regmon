<?php // Email Function

if ($SEC_check != $CONFIG['SEC_Page_Secret']) exit;

$PATH_2_ROOT = '../';

require (__DIR__.'/'.$PATH_2_ROOT.'vendor/autoload.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function SendEmail(string $EmailTo, string $Subject, string $Message):string {
	global $CONFIG;
	$CE = $CONFIG['EMAIL'];

	if ($CE['Username'] == '' OR $EmailTo == '') {
		return 'ERROR: Empty Username or EmailTo';
	}

	//TODO: this need to go to configuration
	date_default_timezone_set('Europe/Berlin');

	//######################################################
	$mail             = new PHPMailer(true);
	$mail->IsSMTP(); 					// telling the class to use SMTP
	$mail->SMTPAuth   = true;       	// enable SMTP authentication
	//$mail->SMTPDebug  = 2; // enables SMTP debug information	1=errors and messages  2=messages only
	
	//for PHP Fatal error:  Uncaught PHPMailer\\PHPMailer\\Exception: SMTP Error: Could not connect to SMTP host.
	//and for self sign certificate uncomment this
	/*$mail->SMTPOptions = array ( 
		'ssl' => array(
			'verify_peer'  => false,
			'verify_peer_name'  => false,
			'allow_self_signed' => true
		)
	);*/
	$mail->Host       = $CE['Host']; 	// sets the SMTP server (localhost)
	if ($CE['Port'] == '465') {
		$mail->SMTPSecure = 'ssl';
	}
	elseif ($CE['Port'] == '587') {
		$mail->SMTPSecure = 'tls';
	}
	$mail->Port       = $CE['Port']; 	 // set the SMTP port for the server (25, 465 or 587)
	$mail->Username   = $CE['Username']; // SMTP account username
	$mail->Password   = Decrypt_String($CE['Password']).''; // SMTP account password
	$mail->CharSet 	  = 'utf-8';
	$mail->SetFrom($CE['From_Email'], $CE['From_Name']);
	if ($CE['Reply_Email'] != '') {
		$mail->AddReplyTo($CE['Reply_Email'], $CE['Reply_Name']);
	}

	//$EmailTo can be an array comma separated
	//$mail->AddAddress($EmailTo);
	array_map(array($mail, 'AddAddress'), explode(',', $EmailTo));


	$mail->Subject = $Subject;
	$mail->MsgHTML($Message);

	try {
		if(!$mail->Send()) {
			return 'ERROR';
		} else {
			return 'OK';
		}
	}
	catch (Exception $e) {
		//if (!substr_count($e->errorMessage(), 'client has exceeded the configured limit')) {}
		error_log($e->errorMessage());
		return $e->getMessage(); //Boring error messages from anything else!
	}
}
?>