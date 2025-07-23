<?php // ajax page profile edit --inline page (its not iframe, it uses the index page DOM)
$PATH_2_ROOT = '../';
require_once($PATH_2_ROOT.'_settings.regmon.php');
require('validate.php');

require_once($PATH_2_ROOT.'php/inc.common_functions.php');

//echo '<pre>';print_r($_SERVER);
//for not call this as a single page but only from ajax
if (!isset($_SERVER['HTTP_X_FANCYBOX']) and !isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
	exit;
}

$uid = '';
$uname = '';
$lastname = '';
$firstname = '';
$email = '';
$telephone = '';
$birth_date = '';
	$birth_year = '';
	$birth_month = '';
	$birth_day = '';
$sport = '';
$sex = '';
$body_height = '';
$level = '';
$dashboard = '';
$lastlogin = '';
$logincount = '';
$last_ip = '';
$created = '';
$modified = '';
$location_name = '';
$group_id = 0;
$group_name = '';
$profile = '';
$level_id = '';


$row = $db->fetchRow("SELECT * FROM users WHERE id = ?", array($UID));
$i=0;
if ($db->numberRows() > 0)  {
	$uid = $row['id'];
	$uname = $row['uname'];
	$lastname = $row['lastname'];
	$firstname = $row['firstname'];

	$birth_date = get_date_SQL($row['birth_date'].'');
	if ($birth_date != '') {
		$birth_arr = explode('-', $birth_date);
		$birth_year = $birth_arr[0];
		$birth_month = $birth_arr[1];
		$birth_day = $birth_arr[2];
	}

	if (is_string($row['sport'])){
		$sport = $row['sport'];
	}

	$sex = $row['sex'];

	if (is_string($row['body_height'])){
		$body_height = $row['body_height'];
	}
	
	$email = $row['email'];
	$telephone = $row['telephone'];

	$level = (int)$row['level'];
		if ($level == 99) $profile = $LANG->LVL_ADMIN;
	elseif ($level == 50) $profile = $LANG->LVL_LOCATION;
	elseif ($level == 45) $profile = $LANG->LVL_GROUP_ADMIN;
	elseif ($level == 40) $profile = $LANG->LVL_GROUP_ADMIN_2;
	elseif ($level == 30) $profile = $LANG->LVL_TRAINER;
	elseif ($level == 10) $profile = $LANG->LVL_ATHLETE;

	$dashboard = $row['dashboard'];
	$lastlogin = get_date_time($row['lastlogin'].'');
	$logincount = $row['logincount'];
	$last_ip = $row['last_ip'];
	$created = get_date_time($row['created'].'');
	$modified = get_date_time($row['modified'].'');
	
	//location/group name
	$st_gr = $db->fetchRow("SELECT l.name AS location_name, gr.name AS group_name 
		FROM locations l 
		LEFT JOIN `groups` gr ON l.id = gr.location_id 
		WHERE gr.id = ?", array($row['group_id']));
	if ($db->numberRows() > 0)  {
		$location_name = $st_gr['location_name'];
		$group_name = $st_gr['group_name'];
		$group_id = $row['group_id'];
		$level_id = $row['level'];
	}
}

$SP_select_options = get_Sports_Select_Options_By_Group($sport);

$body_height_options = get_Body_Height_Options($body_height);

?>
<style>
/*required*/
input.required.form-control { 
    background: linear-gradient(90deg, white,#ff7777,red,#d5d5d5);
    background-color: white;
    background-repeat: no-repeat;
    background-position: 100%;
    background-size: 5px 100%;
}
</style>

	<form id="profile_edit" style="width:100%;" role="form" autocomplete="off">
		<h3 style="text-align:center;width:100%;"><?=$LANG->PROFILE_USER_ACCOUNT;?></h3>
		<input type="hidden" name="uid" value="<?=$uid;?>">
		<input type="text" style="display:none">
		<input type="password" style="display:none"><?php /*hack to disable autocomplete*/?>
		<div class="form-group">
			<label for="uname"><?=$LANG->REGISTER_USERNAME;?></label><br>
			<input type="text" id="uname" name="uname" class="required form-control" placeholder="<?=$LANG->REGISTER_USERNAME;?>" value="<?=$uname;?>">
		</div>
		<div class="form-group">
			<label for="passwd"><?=$LANG->REGISTER_PASSWORD;?></label><br>
			<input type="password" id="passwd" name="passwd" class="form-control" placeholder="<?=$LANG->REGISTER_PASSWORD;?>" value="" autocomplete="off"<?php /*readonly onfocus="this.removeAttribute('readonly');" - hack to disable autocomplete*/?>>
			<small class="text-muted"><?=$LANG->PROFILE_NO_CHANGE_PASSWORD;?></small>
		</div>
		<div class="form-group">
			<label for="pass_confirm"><?=$LANG->REGISTER_PASS_CONFIRM;?></label><br>
			<input type="password" id="pass_confirm" name="pass_confirm" class="form-control" placeholder="<?=$LANG->REGISTER_PASS_CONFIRM;?>" value="" autocomplete="off">
		</div>
		<div class="form-group">
			<label for="email"><?=$LANG->REGISTER_EMAIL;?></label><br>
			<input type="email" name="email" class="required form-control" placeholder="<?=$LANG->REGISTER_EMAIL;?>" value="<?=$email;?>">
		</div>
		<div class="form-group">
			<label for="firstname"><?=$LANG->REGISTER_FIRST_NAME;?></label><br>
			<input type="text" name="firstname" class="form-control" placeholder="<?=$LANG->REGISTER_FIRST_NAME;?>" value="<?=$firstname;?>">
		</div>
		<div class="form-group">
			<label for="lastname"><?=$LANG->REGISTER_LAST_NAME;?></label><br>
			<input type="text" name="lastname" class="form-control" placeholder="<?=$LANG->REGISTER_LAST_NAME;?>" value="<?=$lastname;?>">
		</div>
		<div class="form-group">
			<label for="sport"><?=$LANG->REGISTER_SPORT;?></label><br>
			<select id="SPORTS_select" name="sport" class="form-control chosen-select" data-placeholder="<?=$LANG->REGISTER_SPORT;?>" multiple>
				<?=$SP_select_options;?>
			</select>
		</div>
		<div class="form-group">
			<label for="body_height"><?=$LANG->REGISTER_BODY_HEIGHT;?></label><br>
			<select name="body_height" class="form-control">
				<option value=""><?=$LANG->REGISTER_BODY_HEIGHT;?></option>
				<?=$body_height_options;?>
			</select>
		</div>
		<div class="form-group">
			<label for="sex"><?=$LANG->REGISTER_SEX;?></label>
			<div class="btn-group" data-toggle="buttons" style="width:100%;">
				<label class="btn btn-default<?=($sex=='0'?' active':'');?>" style="width:33%;">
					<input type="radio" name="sex" class="required" value="0"<?=($sex=='0'?' checked':'');?>><?=$LANG->REGISTER_MALE;?>
				</label> 
				<label class="btn btn-default<?=($sex=='1'?' active':'');?>" style="width:33%;">
					<input type="radio" name="sex" class="required" value="1"<?=($sex=='1'?' checked':'');?>><?=$LANG->REGISTER_FEMALE;?>
				</label> 
				<label class="btn btn-default<?=($sex=='2'?' active':'');?>" style="width:33%;">
					<input type="radio" name="sex" class="required" value="2"<?=($sex=='2'?' checked':'');?>><?=$LANG->REGISTER_OTHER;?>
				</label> 
			</div>
		</div>
		<div class="form-group">
			<label for="day"><?=$LANG->REGISTER_BIRTH_DATE;?></label><br>
			<span style="display:inline-block; width:30%;">
				<select class="form-control" name="birth_day">
					<option value="" selected><?=$LANG->REGISTER_DAY;?></option>
					<?php for($i=1; $i <= 31; $i++) echo '<option value="'.$i.'"'.($birth_day==$i?' selected':'').'>'.$i.'</option>';?>
				</select>
			</span>
			<span style="display:inline-block; width:30%;">
				<select class="form-control" name="birth_month">
					<option value=""><?=$LANG->REGISTER_MONTH;?></option>
					<?php for($i=1; $i <= 12; $i++) echo '<option value="'.$i.'"'.($birth_month==$i?' selected':'').'>'.$i.'</option>';?>
				</select>
			</span>
			<span style="display:inline-block; width:35%;">
				<select class="form-control" name="birth_year">
					<option value=""><?=$LANG->REGISTER_YEAR;?></option>
					<?php for($i=(date('Y')-75); $i <= date('Y'); $i++) echo '<option value="'.$i.'"'.($birth_year==$i?' selected':'').'>'.$i.'</option>';?>
				</select>
			</span>
		</div>
		<div class="form-group">
			<label for="telephone"><?=$LANG->REGISTER_TELEPHONE;?></label><br>
			<input type="tel" id="telephone" name="telephone" class="form-control" value="<?=$telephone;?>" placeholder="<?=$LANG->REGISTER_TELEPHONE;?>">
		</div>
		<div class="form-group">
			<label for="dashboard"><?=$LANG->PROFILE_DASHBOARD_ON_LOGIN;?></label>
			<div class="btn-group" data-toggle="buttons" style="width:100%;">
				<label class="btn btn-default<?=($dashboard=='0'?' active':'');?>" style="width:49%;">
					<input type="radio" name="dashboard" class="required" value="0"<?=($dashboard=='0'?' checked':'');?>><?=$LANG->PROFILE_DASHBOARD_CLOSED;?>
				</label> 
				<label class="btn btn-default<?=($dashboard=='1'?' active':'');?>" style="width:49%;">
					<input type="radio" name="dashboard" class="required" value="1"<?=($dashboard=='1'?' checked':'');?>><?=$LANG->PROFILE_DASHBOARD_OPENED;?>
				</label> 
			</div>
		</div>
		<br>
		<div class="form-group only_text">
			<label><?=$LANG->PROFILE_LOCATION;?></label>
			<span><?=$location_name;?></span>
			<input type="hidden" name="location_name" value="<?=$location_name;?>">
		</div>
		<div class="form-group only_text">
			<label><?=$LANG->PROFILE_GROUP;?></label>
			<span><?=$group_name;?></span>
			<input type="hidden" name="group_id" value="<?=$group_id;?>">
			<input type="hidden" name="group_name" value="<?=$group_name;?>">
		</div>
		<div class="form-group only_text">
			<label><?=$LANG->PROFILE_LEVEL;?></label>
			<span><?=$profile;?></span>
			<input type="hidden" name="level_id" value="<?=$level_id;?>">
			<input type="hidden" name="profile" value="<?=$profile;?>">
		</div>
		<div class="form-group only_text">
			<label><?=$LANG->PROFILE_LAST_LOGIN;?></label>
			<span><?=$lastlogin;?></span>
		</div>
		<div class="form-group only_text">
			<label><?=$LANG->PROFILE_LAST_IP;?></label>
			<span><?=$last_ip;?></span>
		</div>
		<div class="form-group only_text">
			<label><?=$LANG->PROFILE_LOGIN_COUNT;?></label>
			<span><?=$logincount;?></span>
		</div>
		<div class="form-group only_text">
			<label><?=$LANG->MODIFIED;?></label>
			<span><?=$modified;?></span>
		</div>
		<div class="form-group only_text">
			<label><?=$LANG->CREATED;?></label>
			<span><?=$created;?></span>
		</div>
		<div class="clearfix"></div>
		<br>
		<div id="profile_alerts" class="form-group"></div>
		<div style="text-align:center;">
			<button type="button" id="profile_save" class="save" style="margin:5px;"><?=$LANG->SAVE;?> &nbsp; </button>
		</div>
		<script>jQuery(function(){ init_Profile_Edit(); });</script>
		<br>
	</form>
