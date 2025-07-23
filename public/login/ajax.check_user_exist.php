<?php //ajax check if user exist

$uname = $_GET['uname'] ?? exit;
$uid = $_GET['uid'] ?? 0;


// load language & database ##########
require_once('no_validate.php');
// ###################################


$where = '';
if ($uid) { //if is the same user
	$where = 'AND id != '.((int)$uid);
}  

if ($uname) {
	$user = $db->fetchRow("SELECT * FROM users WHERE uname=? $where", array($uname));
	if ($db->numberRows() > 0)  {
		if ($uid) echo $LANG->NAME_EXIST;
		else echo 'false';
	}
	else {
		if ($uid) echo 'OK';
		else echo 'true';
	}
}
else {
	if ($uid) echo $LANG->ERROR;
	else echo 'false';
}
?>