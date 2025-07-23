<?php // ajax Form Default Template Save
$PATH_2_ROOT = '../';
require_once($PATH_2_ROOT.'_settings.regmon.php');
require($PATH_2_ROOT.'login/validate.php');

$group_id 	= $_POST['group_id'] ?? 0;
$ath_id 	= $_POST['ath_id'] ?? 0;
$cat_id 	= $_POST['cat_id'] ?? 0;
$form_id 	= $_POST['form_id'] ?? 0;
$template_id = $_POST['template_id'] ?? 0;
if (!$group_id OR !$ath_id OR !$cat_id OR !$form_id OR !$template_id) exit;

$values = array();
$values['user_id'] 		= (int)$ath_id;
$values['group_id'] 	= (int)$group_id;
$values['category_id'] 	= (int)$cat_id;
$values['form_id'] 		= (int)$form_id;
$values['template_id'] 	= (int)$template_id;
$values['modified'] 	= get_date_time_SQL('now');
$values['modified_by'] 	= $USERNAME;

$def = $db->fetchRow("SELECT id FROM users2forms WHERE user_id=? AND group_id=? AND category_id=? AND form_id=?", array($ath_id, $group_id, $cat_id, $form_id)); 
if ($db->numberRows() > 0) { //exist
	$update = $db->update($values, "users2forms", "user_id=? AND group_id=? AND category_id=? AND form_id=?", array($ath_id, $group_id, $cat_id, $form_id));
}
else { //new
	$values['created'] = get_date_time_SQL('now');
	$values['created_by'] = $USERNAME;
	$insert = $db->insert($values, "users2forms");
}
?>