<?php // Register

declare(strict_types=1);
require_once('_settings.regmon.php');
// load language & database ##########
require_once('login/no_validate.php');
// ###################################

require_once('php/inc.common_functions.php');


//Locations-Groups Select Options
$Groups_select_options_optgroup = '';
$Groups_select_options = '';
$private_option = '';
$private_groups = array();
$rows = $db->fetch("SELECT gr.id, gr.location_id, gr.status, gr.private_key, gr.stop_date, gr.name, u.email, l.name AS location_name 
FROM `groups` gr 
LEFT JOIN locations l ON l.id = gr.location_id 
LEFT JOIN users u ON u.id IN (gr.admins_id) 
WHERE l.status > 0 AND gr.status > 0 
ORDER BY gr.location_id, gr.name", array()); 
if ($db->numberRows() > 0)  {
	$location_open = false;
	$GP_group = '';
	$location_tmp = '';
	foreach ($rows as $row) 
	{
		$location_id = $row['location_id'];
		$location_name = $row['location_name'];
		$group_id = $row['id'];
		$group_name = html_chars($row['name']);
		$group_email = $row['email'];
		

		//Group
		if ($location_name != $location_tmp) {
			if ($location_open) {
				$Groups_select_options_optgroup .= '</optgroup>';
			}
			$Groups_select_options_optgroup .= '<optgroup label="' . $location_name . '">';
			$location_open = true;
		}
		

		$t_disabled = '';
		if ($group_email == '') $t_disabled = ' disabled'; //mark disabled if no admin email


		$group_expire = false;
		if ($row['stop_date'] AND strtotime($row['stop_date']) < strtotime("now")) {
			$group_expire = true;
		}

		$group_private = false;
		if ($row['status'] == '3' AND !$group_expire) { //if private and not expired
			$group_private = true;
			$private_groups[] = array($group_id, $group_name, $row['private_key']);
		}
		
		//option
		if ($group_expire) {
			$t_option = '';
		}
		elseif ($group_private) {
			$t_option = '';
		}
		else {
			$t_option = '<option value="' . $location_id . '|' . $location_name . '|' . $group_id . '|' . $group_name . '"' . $t_disabled . '>' . $group_name . '</option>';
		}
		if ($location_id == '0') {
			$Groups_select_options .= $t_option;
		} else {
			$Groups_select_options_optgroup .= $t_option;
		}
		
		$location_tmp = $location_name;
	}
	if ($location_open) {
		$Groups_select_options_optgroup .= '</optgroup>';
	}
	if (count($private_groups)) {
		$private_option = '<option value="Private">' . $LANG->REGISTER_PRIVATE_GROUP . '...</option>';
	}
}
$Groups_select_options = $Groups_select_options_optgroup . $Groups_select_options . $private_option;
$PUG_json = json_encode($private_groups); //PrivateUserGroups


$SP_select_options = get_Sports_Select_Options_By_Group();

$body_height_options = get_Body_Height_Options();

//#####################################################################################
$title = $LANG->REGISTER_PAGE_TITLE;
require('php/inc.html_head.php');
//#####################################################################################
?>
<link rel="stylesheet" type="text/css" href="index/css/sticky_navbar.css<?=$G_VER;?>" />
<script type="text/javascript" src="js/plugins/jquery.cookie.js"></script>

<script type="text/javascript" src="node_modules/@kflorence/jquery-wizard/src/jquery.wizard.js"></script>

<link type="text/css" rel="stylesheet" href="css/overrides/icheck/skins/square/aero_new.css">
<script type="text/javascript" src="node_modules/icheck/icheck.min.js"></script>

<link type="text/css" rel="stylesheet" href="js/plugins/chosen/chosen.min.css" />
<script type="text/javascript" src="js/plugins/chosen/chosen.jquery.min.js"></script>

<script type="text/javascript" src="node_modules/jquery-validation/dist/jquery.validate.js"></script>
<?php if ($LANG->LANG_CURRENT != 'en') { ?>
<script type="text/javascript" src="js/overrides/query-validation/messages_<?=$LANG->LANG_CURRENT;?>.min.js"></script>
<?php } ?>

<link rel="stylesheet" type="text/css" href="node_modules/intl-tel-input/build/css/intlTelInput.min.css">
<script type="text/javascript" src="node_modules/intl-tel-input/build/js/intlTelInput-jquery.min.js"></script>

<script type="text/javascript" src="node_modules/bootstrap/dist/js/bootstrap.min.js"></script>

<script type="text/javascript" src="js/lang_<?=$LANG->LANG_CURRENT;?>.js<?=$G_VER;?>"></script>
<script type="text/javascript" src="js/common.js<?=$G_VER;?>"></script>
<script>
const V_REGmon_Folder = '<?=$CONFIG['REGmon_Folder'];?>';
</script>
<script type="text/javascript" src="login/js/register.js<?=$G_VER;?>"></script>
<style>
/*loading*/
.ajaxOverlay {z-index:10000; border:none; margin:0px; padding:0px; width:100%; height:100%; top:0px; left:0px; opacity:0.6; cursor:wait; position:fixed; background-color:rgb(0, 0, 0);}
.ajaxMessage {z-index:10011; position:fixed; padding:0px; margin:0px; width:30%; top:40%; left:35%; text-align:center; color:rgb(255, 255, 0); border:0px; cursor:wait; text-shadow:red 1px 1px; font-size:18px; background-color:transparent;}

label.error {top:20px;}
label.label_gender {padding-left: 20px;}
label#year-error, label#month-error, label#day-error {top:-38px; left:35% !important; white-space:nowrap;}
label#sex-error {top:-40px; left:75%;}
option:disabled {color: #999999;}
ul.floated li { width:32%;}
.data-list li { padding-top: 15px; }
.chosen-container .chosen-choices {font-size:16px; font-weight:400; border:2px solid #d5d5d5; border-radius:0; box-shadow:none; color:#999; padding:6px 10px; padding-top:7px;}
.chosen-container .search-field li {font-size:16px;}
.chosen-container .chosen-results {max-height:400px; font-size:14px;}
.chosen-container .chosen-results li {line-height:11px;}
.styled-select select.form-control {height: 41px;}
.iti--allow-dropdown { width:100%; }
.iti__country-list { max-width:390px; max-height:250px; }

/*required*/
input.required.form-control,
.data-list li.required .chosen-choices,
.data-list li.sex.required { 
    background: linear-gradient(90deg, white,#ff7777,red,#d5d5d5);
    background-color: white;
    background-repeat: no-repeat;
    background-position: 100%;
    background-size: 5px 100%;
}
select.required.form-control { border-right: 3px solid rgba(255, 0, 0, 0.7); }
</style>
</head>
<body>
	<div id="loading" class="ajaxOverlay" style="display:none">
		<div class="ajaxMessage"><img src="img/ldg.gif"></div>
	</div>

	<?php require('php/inc.header.php');?>
	
	<?php require('php/inc.nav_lang.php');?>

    <div class="container" style="margin-top:-30px;">
		<div class="row">
			<div class="col-md-12 main-title" style="text-align:center;">
				<button type="button" class="home"> &nbsp; &nbsp; <?=$LANG->HOMEPAGE;?></button>
				<h1><?=$LANG->REGISTER;?></h1>
			</div>
		</div>
	</div>

	
	<section class="container">

		<div id="wizard_container">


<form name="form1" id="wrapped" action="" method="POST">
	<div id="middle-wizard">
	
		<div class="step">
			<div class="row">
				<?php /*<h3 class="" style="text-align:center;"><?=$LANG->REGISTER_INFO;?></h3>*/?>
				<div class="col-md-6">
					<ul class="data-list">
						<li>
							<label><?=$LANG->REGISTER_USERNAME;?></label>
							<input type="text" name="uname" id="uname" class="required form-control" placeholder="<?=$LANG->REGISTER_USERNAME;?>">
						</li>
						<li>
							<label><?=$LANG->REGISTER_PASSWORD;?></label>
							<input type="password" name="passwd" id="passwd" class="required form-control" placeholder="<?=$LANG->REGISTER_PASSWORD;?>">
						</li>
						<li>
							<label><?=$LANG->REGISTER_PASS_CONFIRM;?></label>
							<input type="password" name="pass_confirm" id="pass_confirm" class="required form-control" placeholder="<?=$LANG->REGISTER_PASS_CONFIRM;?>">
						</li>
						<li>
							<label><?=$LANG->REGISTER_EMAIL;?></label>
							<input type="email" name="email" class="required form-control" placeholder="<?=$LANG->REGISTER_EMAIL;?>">
						</li>
						<li>
							<label><?=$LANG->REGISTER_TELEPHONE;?></label>
							<input type="tel" id="telephone" name="telephone" class="form-control" placeholder="<?=$LANG->REGISTER_TELEPHONE;?>">
							<input type="hidden" id="countryCode" name="countryCode" value="+49">
						</li>
					</ul>
				</div>
				
				<div class="col-md-6">
				
					<ul class="data-list">
						<li>
							<label><?=$LANG->REGISTER_FIRST_NAME;?></label>
							<input type="text" name="firstname" class="form-control" placeholder="<?=$LANG->REGISTER_FIRST_NAME;?>">
						</li>
						<li>
							<label><?=$LANG->REGISTER_LAST_NAME;?></label>
							<input type="text" name="lastname" class="form-control" placeholder="<?=$LANG->REGISTER_LAST_NAME;?>">
						</li>
						<li>
							<label><?=$LANG->REGISTER_SPORT;?></label>
							<select id="SPORTS_select" name="sport[]" class="<?php /*required */?>form-control chosen-select" data-placeholder="<?=$LANG->REGISTER_SPORT;?>" multiple>
								<?=$SP_select_options;?>
							</select>
						</li>
						<li>
							<label><?=$LANG->REGISTER_BODY_HEIGHT;?></label>
							<div class="styled-select">
								<select name="body_height" class="form-control">
									<option value="" selected><?=$LANG->REGISTER_BODY_HEIGHT;?></option>
									<?=$body_height_options;?>
								</select>
							</div>
						</li>
					</ul>
			
				</div>
				
			</div>
			
			
			<div class="row" style="text-align:center;">
				<div style="width:70%;margin:auto;">
					<label><?=$LANG->REGISTER_SEX;?></label>
					<ul class="data-list floated clearfix">
						<li><input name="sex" type="radio" class="check_radio" value="0"><label class="label_gender"> <?=$LANG->REGISTER_MALE;?></label></li>
						<li><input name="sex" type="radio" class="check_radio" value="1"><label class="label_gender"> <?=$LANG->REGISTER_FEMALE;?></label></li>
						<li><input name="sex" type="radio" class="check_radio" value="2"><label class="label_gender"> <?=$LANG->REGISTER_OTHER;?></label></li>
						<li class="sex" style="width:5px; height:44px;"></li>
					</ul>
				</div>
			</div>
			
			<div class="row" style="text-align:center;">
				<div style="width:70%;margin:25px auto;">
					<label><?=$LANG->REGISTER_BIRTH_DATE;?></label>
					<ul class="data-list" id="terms" style="margin-top:-15px;">
						<li>
							<span class="birth_date">
								<div class="styled-select">
									<select class="form-control" name="year">
										<option value="" selected><?=$LANG->REGISTER_YEAR;?></option>
										<?php for($i=(date('Y')-75); $i <= date('Y'); $i++) echo '<option value="'.$i.'">'.$i.'</option>';?>
									</select>
								</div>
							</span>
							<span class="birth_date">
								<div class="styled-select">
									<select class="form-control" name="month">
										<option value="" selected><?=$LANG->REGISTER_MONTH;?></option>
										<?php for($i=1; $i <= 12; $i++) echo '<option value="'.$i.'">'.$i.'</option>';?>
									</select>
								</div>
							</span>
							<span class="birth_date">
								<div class="styled-select">
									<select class="form-control" name="day">
										<option value="" selected><?=$LANG->REGISTER_DAY;?></option>
										<?php for($i=1; $i <= 31; $i++) echo '<option value="'.$i.'">'.$i.'</option>';?>
									</select>
								</div>
							</span>
						</li>
					</ul>
				</div>
			</div>
			
			<br>
			<br>
			
			<div class="row">
				<div class="col-md-6">
					<ul class="data-list">
						<li>
							<label><?=$LANG->REGISTER_GROUP;?></label>
							<div class="styled-select">
								<select id="Select_Group" name="location_group" class="required form-control">
									<option value="" selected><?=$LANG->REGISTER_GROUP;?></option>
									<?=$Groups_select_options;?>
								</select>
							</div>
							<div id="private_group" class="form-group" style="margin-bottom:-15px; display:none;">
								<div class="input-group">
									<input type="text" id="private_key" name="private_key" value="" class="form-control" placeholder="<?=$LANG->REGISTER_PRIVATE_KEY;?>" style="height:45px;"/>
									<span id="private_close" title="<?=$LANG->REGISTER_BACK;?>" class="input-group-addon" style="font-size:18px; cursor:pointer; color:red;"><span class="fa fa-times-circle "></span></span>
								</div>
							</div>
						</li>
					</ul>
				</div>
				<div class="col-md-6">
					<ul class="data-list">
						<li>
							<label><?=$LANG->REGISTER_PROFILE;?></label>
							<div class="styled-select">
								<select name="profile" class="required form-control">
									<option value="10_<?=$LANG->REGISTER_LVL_ATHLETE;?>" selected><?=$LANG->REGISTER_LVL_ATHLETE;?></option>
									<option value="30_<?=$LANG->REGISTER_LVL_TRAINER;?>"><?=$LANG->REGISTER_LVL_TRAINER;?></option>
								</select>
							</div>
						</li>
					</ul>
				</div>
			</div>
			
		</div>
		
		<div class="submit step" id="complete">
			<i class="icon-check"></i>
			<!-- <h1 style="color:#333"><?=$LANG->REGISTER_THANKS;?></h1> -->
			<h3 style="color:#6C3"><?=$LANG->REGISTER_SUBMIT_WAIT;?></h3>
			<button type="submit" id="submit" name="process" class="submit" style="display:none;"><?=$LANG->REGISTER_SUBMIT;?></button>
		</div>
		
	</div>
	
	<div id="bottom-wizard">
		<button type="button" name="backward" class="backward" style="display:none;"><?=$LANG->REGISTER_BACKWARD;?></button>
		<button type="button" name="forward" class="forward"><?=$LANG->REGISTER_SUBMIT;?>&nbsp; &nbsp;</button>
	</div>
</form>

		</div>

		<br>
		<br>

	</section>

	<?php //require('php/inc.footer.php');?>

	<div id="toTop" title="<?=$LANG->PAGE_TOP;?>">&nbsp;</div>

</body>
</html>