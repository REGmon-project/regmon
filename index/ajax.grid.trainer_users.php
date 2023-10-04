<?php // Trainer Users Grid
$PATH_2_ROOT = '../';
require_once($PATH_2_ROOT.'_settings.regmon.php');
require($PATH_2_ROOT.'login/validate.php');

$group_id = (int)($_POST['group_id'] ?? false);
if (!$group_id) exit;
?>
<script type="text/javascript" src="index/js/grid.trainer_users.js<?=$G_VER;?>"></script>
<table id="trainer_users" alt="<?=$LANG->TRAINER_USERS;?>"></table>
<div id="UTpager"></div>
<br>
<div id="req_athletes_message" style="text-align:center;"></div>
<div style="text-align:center; padding:5px 0 20px;">
	<div class="btn-group" id="req_athlete_action">
		<button id="request_access_athlete" type="button" class="btn btn-info bttn" style="width:265px; height:31px;"><?=$LANG->REQUEST_FOR_ACCESS;?> &nbsp;<span class="req G_yes"></span></button>
		<button type="button" class="btn btn-info bttn dropdown-toggle" style="border-left:1px solid #00AEEF; height:31px;" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			<span class="caret"></span>
			<span class="sr-only"></span>
		</button>
		<ul class="dropdown-menu" style="padding:0; background:transparent;">
			<li id="req_athlete_action_1"><button id="cancel_request_athlete" type="button" class="btn btn-danger bttn bttn_dark" style="width:265px; padding:7px 10px;"><?=$LANG->REQUEST_FOR_ACCESS_CANCEL;?> &nbsp; &nbsp; <span class="req G_no"></span></button></li>
			<li id="req_athlete_action_2"><button id="cancel_access_athlete" type="button" class="btn btn-danger bttn bttn_red" style="width:265px;"><?=$LANG->REQUEST_TRAINER_LEAVE_ATHLETE;?> &nbsp; &nbsp; <span class="req G_leaveR"></span></button></li>
		</ul>
	</div>
</div>
