<?php // ajax Dashboard Delete
$PATH_2_ROOT = '../';
require_once($PATH_2_ROOT.'_settings.regmon.php');
require($PATH_2_ROOT.'login/validate.php');
require($PATH_2_ROOT.'index/inc.index_functions.php');

$group_id = (int)($_POST['group_id'] ?? false);
$ath_id = (int)($_POST['ath_id'] ?? false);
$dash_id = (int)($_POST['dash_id'] ?? false);
if (!$group_id OR !$ath_id OR !$dash_id) exit;

//delete dashboard entry
$db->delete("dashboard", "user_id=? AND group_id=? AND id=?", array($ath_id, $group_id, $dash_id));

//return the new Dashboard Links Array
$Dashboard_Links_Arr = get_Dashboard_Links_Array($ath_id, $group_id);
?>
<script>
V_DASHBOARD=[<?=$Dashboard_Links_Arr;?>];
</script>