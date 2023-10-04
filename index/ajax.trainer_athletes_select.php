<?php // ajax Trainer Athletes Select
$PATH_2_ROOT = '../';
require_once($PATH_2_ROOT.'_settings.regmon.php');
require($PATH_2_ROOT.'login/validate.php');

$group_id = (int)($_POST['group_id'] ?? false);
$athlete_id = (int)($_POST['athlete_id'] ?? false);
if (!$group_id) exit;


$html = '';

//Select Athletes in Group with Trainer this User-$UID
$rows = $db->fetch("SELECT u.id, u.lastname, u.firstname 
FROM users2groups u2g 
JOIN users u ON (u.id = u2g.user_id AND u.level = 10 AND u.status = 1) 
JOIN users2trainers u2t ON (u.id = u2t.user_id AND u2g.group_id = u2t.group_id AND u2t.status = 1 AND u2t.trainer_id = ?) 
WHERE u2g.group_id = ? AND u2g.status = 1 
ORDER BY u.id", array($UID, $group_id)); 
//print_r($rows); 
if ($db->numberRows() > 0) {
	$html = '<label for="Select_Trainer_Athletes" style="font-size:17px;">'.
				$LANG->LVL_ATHLETE.' : &nbsp; '.
			'</label>'.
			'<select name="Select_Trainer_Athletes" id="Select_Trainer_Athletes">';

	$html .= 	'<option value=""></option>';

	foreach ($rows as $row) {
		$selected = '';
		if ($athlete_id == $row['id']) $selected = ' selected';
		$html .= '<option value="'.$row['id'].'"'.$selected.'>'.
					$row['firstname'].' &nbsp; '.$row['lastname'].
					' &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; '.
				'</option>';
	}

	$html .= '</select>';
}
else {
	$html = '<div class="empty_message">'.$LANG->ATHLETES_NOT_AVAILABLE.'</div>';
}

echo $html;
?>
<?php /*<script>jQuery(function(){ init_Trainer__Athletes_Select(); });</script>*/?>
