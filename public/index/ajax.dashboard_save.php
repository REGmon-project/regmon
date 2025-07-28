<?php // ajax Dashboard Save
$PATH_2_ROOT = '../';
require_once($PATH_2_ROOT.'_settings.regmon.php');
require($PATH_2_ROOT.'login/validate.php');
require($PATH_2_ROOT.'index/inc.index_functions.php');

$group_id 	= (int)($_POST['group_id'] ?? false);
$ath_id 	= (int)($_POST['ath_id'] ?? false);
$dash_id 	= isset($_POST['dash_id']) ? (int)$_POST['dash_id'] : 'max';
$name 		= $_POST['name'] ?? false;
$type 		= $_POST['type'] ?? false;
$options 	= $_POST['options'] ?? false;
$sort 		= (int)($_POST['sort'] ?? 0);
$color 		= $_POST['color'] ?? '#cccccc';
if (!$group_id OR !$ath_id OR !$name OR !$type OR !$options) exit;

$values = array();
$values['user_id'] = $ath_id;
$values['group_id'] = $group_id;
$values['name'] = $name;
$values['type'] = $type;
$values['options'] = $options;
$values['color'] = $color;
$values['modified'] = get_date_time_SQL('now');
$values['modified_by'] = $USERNAME;

if ($sort == 'max') {
	$sort_max = $db->fetchRow("SELECT MAX(sort) AS max_sort FROM dashboard WHERE user_id=? AND group_id=?", array($ath_id, $group_id)); 
	$sort = $sort_max['max_sort'] + 1;
}
$values['sort'] = $sort;

if ($dash_id AND $dash_id != '') { //update
	$update = $db->update($values, "dashboard", "user_id=? AND group_id=? AND id=?", array($ath_id, $group_id, $dash_id));
}
else { //new
	$values['created'] = get_date_time_SQL('now');
	$values['created_by'] = $USERNAME;
	$insert = $db->insert($values, "dashboard");
}

//return the new Dashboard Links Array
$Dashboard_Links_Arr = get_Dashboard_Links_Array($ath_id, $group_id);
?>
<script>
V_DASHBOARD=[<?=$Dashboard_Links_Arr;?>];
</script>
