<?php // ajax check private key

$private_key = $_GET['private_key'] ?? '';
$location_id = $_GET['location_id'] ?? 0;

// load language & database ##########
require_once('no_validate.php');
// ###################################


$where = '';
if ($location_id) { //registered user
	$where = 'AND location_id = '.((int)$location_id);
}  

if ($private_key != '') {
	$group = $db->fetchRow("SELECT id FROM `groups` WHERE status = 3 AND private_key = ? $where", array($private_key)); 
	if ($db->numberRows() > 0)  {
		if ($location_id) echo $group['id'];
		else echo 'true';
	}
	else {
		echo 'false';
	}
}
else {
	echo 'false';
}
?>