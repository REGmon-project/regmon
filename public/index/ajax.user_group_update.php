<?php // ajax User Group Update - update active Location, Group of the user
$PATH_2_ROOT = '../';
require_once($PATH_2_ROOT.'_settings.regmon.php');
require($PATH_2_ROOT.'login/validate.php');

//print_r($_POST); exit;
$location_id = isset($_POST['location_id']) ? $_POST['location_id'] : false;
$group_id = isset($_POST['group_id']) ? $_POST['group_id'] : false;
$u_id = isset($_POST['u_id']) ? $_POST['u_id'] : false;
if (!$location_id OR !$group_id OR !$u_id OR $u_id != $UID) exit;

$values = array();
$values['location_id'] = $location_id;
$values['group_id'] = $group_id;
$result = $db->update($values, "users", "id=?", array($UID));
?>