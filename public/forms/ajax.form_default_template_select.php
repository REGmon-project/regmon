<?php // ajax Form Default Template Select
$PATH_2_ROOT = '../';
require_once($PATH_2_ROOT.'_settings.regmon.php');
require($PATH_2_ROOT.'login/validate.php');

$group_id 	= (int)($_POST['group_id'] ?? 0);
$ath_id 	= (int)($_POST['ath_id'] ?? 0);
$cat_id 	= (int)($_POST['cat_id'] ?? 0);
$form_id 	= (int)($_POST['form_id'] ?? 0);
if (!$group_id OR !$ath_id OR !$cat_id OR !$form_id) exit;

$form_default_template = 0;

//group requests
$row = $db->fetchRow("SELECT template_id 
FROM users2forms 
WHERE user_id=? AND group_id=? AND category_id=? AND form_id=?", array($ath_id, $group_id, $cat_id, $form_id)); 
if ($db->numberRows() > 0)  {
	$form_default_template = $row['template_id'];
}

//$html = '<select id="select_template_'.$ath_id.'_'.$group_id.'_'.$cat_id.'_'.$form_id.'">';
$html = $LANG->FORM_DEFAULT_TEMPLATE.' : <br><select class="select_template" style="width:100%;">';

//get available group form templates
$templates = $db->fetchAll("SELECT id, name 
FROM templates_forms 
WHERE group_id=? AND form_id=? 
ORDER BY name", array($group_id, $form_id)); 
if ($db->numberRows() > 0) {
	foreach ($templates as $template) {
		$html .= '<option value="'.$template['id'].'"'.($form_default_template == $template['id'] ? ' selected' : '').'>'.$template['name'].'</option>';
	}
}
$html .= '</select>';

echo $html;
?>
