<?php // ajax Requests Count - return the number of unhandled requests 
$PATH_2_ROOT = '../';
require_once($PATH_2_ROOT.'_settings.regmon.php');
require($PATH_2_ROOT.'login/validate.php');

$group_id = (int)($_POST['group_id'] ?? false);
if (!$group_id) exit;

$rows = 0;
if ($ADMIN OR $LOCATION_ADMIN OR $GROUP_ADMIN OR $GROUP_ADMIN_2) {
	//group requests
	$rows = $db->fetch("SELECT u2g.status AS request_status, u2g.modified, u2g.created, u.id, u.uname, u.lastname, u.firstname, u.sport, u.body_height, u.sex, u.birth_date, u.email, u.level 
	FROM users2groups u2g 
	JOIN users u ON u.id = u2g.user_id 
	WHERE u2g.group_id = ? AND u2g.status > 5 AND u2g.status != 15 AND u2g.status != 11 
	ORDER BY u2g.id", array($group_id)); 
}
elseif ($ATHLETE) {
	//athlete requests
	$rows = $db->fetch("SELECT u2t.status AS request_status, u2t.modified, u2t.created, u.id, u.lastname, u.firstname, u.sport 
	FROM users2groups u2g 
	JOIN users u ON (u.id = u2g.user_id AND u.level > 10 AND u.status = 1) 
	JOIN users2trainers u2t ON (u.id = u2t.trainer_id AND u2g.group_id = u2t.group_id AND u2t.user_id = ?)
	WHERE u2g.group_id = ? AND u2g.status = 1 AND u2t.status > 5 AND u2t.status != 15 
	ORDER BY u2t.id", array($UID, $group_id)); 
}

echo count($rows);
?>