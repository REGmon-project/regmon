<?php // ajax Athletes Select (in Groups optgroup) for selected Groups

$PATH_2_ROOT = '../';
require_once($PATH_2_ROOT.'_settings.regmon.php');
require($PATH_2_ROOT.'login/validate.php');

require($PATH_2_ROOT.'results/inc.results_functions.php');


$group_ids = $_POST['group_ids'] ?? false;

if (!$group_ids) {
	echo get_No_Data_Error();
	exit;
}


//where_athletes query
$where_athletes = '';
if ($LOCATION_ADMIN) {
	$where_athletes = 'AND (u.level < 50 OR u.id = "'.$UID.'")';
}
elseif ($GROUP_ADMIN OR $GROUP_ADMIN_2) {
	$where_athletes = 'AND (u.level < 40 OR u.id = "'.$UID.'")';
}
elseif ($TRAINER) {
	$trainer_users_ids = $UID; //start with current user
	$trainer_users = $db->fetch("SELECT u.id, u.uname, u.lastname, u.firstname 
FROM `users2groups` u2g 
JOIN `users` u ON (u.id = u2g.user_id AND u.level = 10 AND u.status = 1) 
JOIN `users2trainers` u2t ON (u.id = u2t.user_id AND u2g.group_id = u2t.group_id AND u2t.status = 1 AND u2t.trainer_id = ?) 
WHERE u2g.group_id IN ($group_ids) AND u2g.status = 1 
ORDER BY u.firstname, u.lastname, u.id", array($UID)); 
	if ($db->numberRows() > 0) {
		foreach ($trainer_users as $trainer_user) {
			$trainer_users_ids .= ','.$trainer_user['id'];
		}
	}
	$where_athletes = 'AND (u.level = 10 OR u.id = "'.$UID.'") AND u.id IN ('.$trainer_users_ids.')';
}
elseif ($ATHLETE) {
	$where_athletes = 'AND u.id = "'.$UID.'"';
}

//Athletes Select with Groups optgroup
$Select__Athletes__Options = '';
$rows = $db->fetch("SELECT g.id AS group_id, g.name AS group_name, u.id, u.uname, u.lastname, u.firstname, u.level, u.sport 
FROM users u 
LEFT JOIN users2groups u2g ON u.id = u2g.user_id 
LEFT JOIN `groups` g ON g.id = u2g.group_id 
WHERE u2g.group_id IN ($group_ids) AND u2g.status = 1 AND u.status = 1 $where_athletes 
ORDER BY g.location_id, g.name, u.level DESC, u.firstname, u.lastname, u.id", array());
if ($db->numberRows() > 0) {
	//html support only one level optgroup
	$open_group = false;
	$group = '';
	$group_tmp = '';
	foreach ($rows as $row) {
		$group = $row['group_name'];
		//Group
		if ($group <> $group_tmp) {
			if ($open_group) {
				$Select__Athletes__Options .= '</optgroup>';
			}
			$Select__Athletes__Options .= '<optgroup label="'.$group.'">';
			$open_group = true;
		}
		
		//option
		$athlete_name = $row['lastname'] != '' ? $row['lastname'] : $row['uname'];
		$athlete_vorname = $row['firstname'] != '' ? $row['firstname'] : $row['uname'];
		if ($GROUP_ADMIN AND !$THIS_GROUP_ADMIN AND $UID != $row['id']) {
		}
		elseif ($GROUP_ADMIN_2 AND !$THIS_GROUP_ADMIN_2 AND $UID != $row['id']) {
		}
		else {
			$Select__Athletes__Options .= '<option value="'.$row['group_id'].'_'.$row['id'].'">'.$athlete_vorname.' '.$athlete_name.' - '.$row['sport'].'</option>';
		}
		$group_tmp = $group;
	}
	if ($open_group) {
		$Select__Athletes__Options .= '</optgroup>';
	}
}
?>

<span id="Select__Athletes__Row__Span">
	<hr style="margin:10px;">
	<div id="Select__Athletes__Row">
		<span class="wiz-title"><?=$LANG->RESULTS_SELECT_ATHLETES;?> : &nbsp; </span>
		<select id="Select__Athletes" name="Select__Athletes" multiple="multiple" style="display:none;">
			<?=$Select__Athletes__Options;?>
		</select> &nbsp; 
<?php if ($Select__Athletes__Options != '') { ?>
		<button id="Button__Select__Athletes__Submit" class="forward" title="<?= $LANG->RESULTS_BUTTON_APPLY_CHANGES;?>"></button>
<?php } else { //change Loading to NoData ?>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<?=get_No_Data_Error();?>
<?php } ?>
	</div>
</span>
