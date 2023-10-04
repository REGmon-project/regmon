<?php // inc Form Header 

//from form.php
/** @var bool $PREVIEW */
/** @var bool $EDIT */
/** @var bool $VIEW */
/** @var bool $CHANGE */
/** @var bool $HAVE_DATA */
/** @var int $group_id */
/** @var int $form_id */
/** @var int $timer */
/** @var int $timer_time_sec */
/** @var int $timer_time_step */
/** @var int $timer_time_min */
/** @var int $timer_time_period */
/** @var int $answers_step */
/** @var bool $is_iOS */
/** @var int $category_id */
/** @var int $athlete_id */
/** @var mixed $FORM_DATA */
/** @var int $HAVE_DATA_NUM */
/** @var string $form_title */
/** @var int $days_has */
/** @var mixed $days_arr */
/** @var string $selected_date */
/** @var string $cat_form_id */

if ($SEC_check != $CONFIG['SEC_Page_Secret']) exit;

//#####################################################################################
$no_forms_css = true;
$title = $LANG->FORMS_PAGE_TITLE;
require('php/inc.html_head.php');
//#####################################################################################
?>
<link rel="stylesheet" type="text/css" href="node_modules/jquery-ui/dist/themes/smoothness/jquery-ui.min.css">
<script type="text/javascript" src="node_modules/jquery-ui/ui/i18n/datepicker-de.js"></script>

<script type="text/javascript" src="node_modules/@kflorence/jquery-wizard/src/jquery.wizard.js"></script>

<link type="text/css" rel="stylesheet" href="css/overrides/icheck/skins/square/aero_new.css">
<script type="text/javascript" src="node_modules/icheck/icheck.min.js"></script>

<script type="text/javascript" src="node_modules/jquery-validation/dist/jquery.validate.js"></script>
<?php if ($LANG->LANG_CURRENT != 'en') { ?>
<script type="text/javascript" src="js/overrides/query-validation/messages_<?=$LANG->LANG_CURRENT;?>.min.js"></script>
<?php } ?>

<link type="text/css" rel="stylesheet" href="node_modules/bootstrap/dist/css/bootstrap-theme.min.css">
<script type="text/javascript" src="node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
<script type="text/javascript" src="node_modules/bs-confirmation/bootstrap-confirmation.min.js"></script>

<link type="text/css" rel="stylesheet" href="js/plugins/chosen/chosen.min.css" />
<script type="text/javascript" src="js/plugins/chosen/chosen.jquery.min.js"></script>

<link type="text/css" rel="stylesheet" href="css/coolfieldset.css?<?=$G_VER;?>" />
<script type="text/javascript" src="js/plugins/jquery.collapsibleFieldset.js?<?=$G_VER;?>"></script>

<link type="text/css" rel="stylesheet" href="js/plugins/clockpicker/clockpicker.css" />
<script type="text/javascript" src="js/plugins/clockpicker/clockpicker.js"></script>

<script type="text/javascript" src="node_modules/moment/min/moment.min.js"></script>
<?php if ($LANG->LANG_CURRENT != 'en') { ?>
<script type="text/javascript" src="js/overrides/moment/<?=$LANG->LANG_CURRENT;?>.js"></script>
<?php } ?>
<link type="text/css" rel="stylesheet" href="node_modules/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css" />
<script type="text/javascript" src="node_modules/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>

<link type="text/css" rel="stylesheet" href="node_modules/bootstrap-multiselect/dist/css/bootstrap-multiselect.css"/>
<script type="text/javascript" src="node_modules/bootstrap-multiselect/dist/js/bootstrap-multiselect.min.js"></script>

<link type="text/css" rel="stylesheet" href="node_modules/bootstrap-colorpicker/dist/css/bootstrap-colorpicker.min.css">
<script type="text/javascript" src="node_modules/bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js"></script>

<link type="text/css" rel="stylesheet" href="css/sticky-side-buttons.css" />
<script type="text/javascript" src="js/sticky-side-buttons.js"></script>

<?php if ($EDIT) { ?>
	<link type="text/css" rel="stylesheet" href="node_modules/trumbowyg/dist/ui/trumbowyg.min.css">
	<link type="text/css" rel="stylesheet" href="node_modules/trumbowyg/dist/plugins/table/ui/trumbowyg.table.min.css">
	<script type="text/javascript" src="node_modules/trumbowyg/dist/trumbowyg.min.js"></script>
	<script type="text/javascript" src="node_modules/trumbowyg/dist/plugins/table/trumbowyg.table.min.js"></script>
	<script type="text/javascript" src="node_modules/trumbowyg/dist/langs/de.min.js"></script>
<?php } ?>

<link rel="stylesheet" href="node_modules/fancybox/dist/css/jquery.fancybox.css" />
<link rel="stylesheet" href="css/overrides/fancybox/jquery.fancybox.css" />
<script type="text/javascript" src="node_modules/fancybox/dist/js/jquery.fancybox.pack.js"></script> 

<link rel="stylesheet" type="text/css" href="node_modules/sweetalert2/dist/sweetalert2.min.css">
<script type="text/javascript" src="node_modules/sweetalert2/dist/sweetalert2.min.js"></script>

<script type="text/javascript">
	$.fn.bootstrapBtn = $.fn.button.noConflict();
</script>

<script>
const V_VER = '<?=$G_VER;?>';
const V_SRV_ID = <?=$form_id;?>;
const V_EDIT = <?=($EDIT?'true':'false');?>;
const V_PREVIEW = <?=($PREVIEW?'true':'false');?>;
const V_VIEW = <?=($VIEW?'true':'false');?>;
const V_CHANGE = <?=($CHANGE?'true':'false');?>;
const V_HAVE_DATA = <?=($HAVE_DATA?'true':'false');?>;
const V_ADMIN = <?=($ADMIN?'true':'false');?>;
const V_GROUP = <?=$group_id;?>;

const V_COUNTER = <?=(($timer AND !$EDIT AND !$VIEW)?'true':'false')?>;
const V_COUNT_ALL = <?=$timer_time_sec;?>;
const V_COUNT_STEP = <?=$timer_time_step;?>;
const V_ANSWERS_STEP = <?=$answers_step;?>;
var V_ANSWERED = [];
</script>
<script type="text/javascript" src="js/lang_<?=$LANG->LANG_CURRENT;?>.js<?=$G_VER;?>"></script>
<script type="text/javascript" src="js/common.js<?=$G_VER;?>"></script>
<script type="text/javascript" src="forms/js/forms.js<?=$G_VER;?>"></script>
<script type="text/javascript" src="index/js/fancybox_defaults.js<?=$G_VER;?>"></script>
<?php if ($EDIT) { ?>
	<script type="text/javascript" src="forms/js/forms.edit.js<?=$G_VER;?>"></script>
<?php } ?>

<link rel="stylesheet" type="text/css" href="forms/css/forms.css<?=$G_VER;?>">
<link rel="stylesheet" type="text/css" href="forms/css/forms2.css<?=$G_VER;?>">
<?php if ($EDIT) { ?>
	<link rel="stylesheet" type="text/css" href="forms/css/forms.edit.css<?=$G_VER;?>">
<?php } ?>
</head>
<body <?=($PREVIEW?' style="background:#cfffff;"':'');?>><?php //mark PREVIEW with other background ?>

<?php if (!$VIEW) { //######################################## ?>
	<div id="progress_cont">
		<div id="progress"></div>
		<div id="time_limit"></div>
		<div id="t_time"></div>
	</div>
	
	<?php //require('php/inc.header.php');?>

	<?php if ($is_iOS) { ?>
	<div style="text-align:center;">
		<a href="." id="home" class="home"> &nbsp; <?=$LANG->HOMEPAGE;?></a>
	</div>
	<?php } ?>

		
	<div id="loading" class="ajaxOverlay" style="display:none">
		<div class="ajaxMessage"><img src="img/ldg.gif"></div>
	</div>

	<?php if ($EDIT) { //sticky-side-buttons ?>
	<div id="ssb-container" class="ssb-btns-right ssb-anim-slide" style="z-index:999;">
		<ul class="ssb-light-hover"> 
			<?php /* we need the ssb-btn-1 and ssb-btn-6 to get the border-radius */ ?>
			<li id="ssb-btn-1"><p><span class="fa fa-file-text-o"></span> <?=$LANG->FORM_PREVIEW;?></p></li>
			<li id="ssb-btn-6"><p><span class="fa fa-floppy-o"></span> <?=$LANG->FORM_SAVE;?></p></li>
		</ul>
	</div>
	<?php } ?>

	<br>

	<form name="form_data" id="form_data" action="" method="POST">
		<input name="form_id" id="form_id" type="hidden" value="<?=$form_id;?>">
		<input name="category_id" id="category_id" type="hidden" value="<?=$category_id;?>">
		<input name="group_id" id="group_id" type="hidden" value="<?=$group_id;?>">
		<input name="athlete_id" id="athlete_id" type="hidden" value="<?=$athlete_id;?>">
	<?php if ($EDIT) { ?>
		<input name="form_json" type="hidden" value="">
		<input name="form_json_names" type="hidden" value="">
	<?php } ?>
	<?php if ($CHANGE AND $FORM_DATA) { ?>
		<input name="change" id="change"type="hidden" value="true">
		<input name="change_id" id="change_id"type="hidden" value="<?=$FORM_DATA['id'].'';?>">
	<?php } ?>
	</form>

	<form name="form1" id="wrapped" action="" method="POST"<?=($HAVE_DATA?' class="HAVE_DATA"':'');?>>

<?php } //if (!$VIEW) //######################################## ?>


    <div class="container">
		<div class="row">
			<div class="col-md-12 main-title" style="text-align:center;">
				<?php /*<button type="button" id="home" class="home"> &nbsp; <?=$LANG->HOMEPAGE;?></button>*/?>
<?php if ($EDIT) { ?>
	<?php if ($HAVE_DATA) { ?>
				<div style="margin-top:15px;" class="alert alert-danger"><?=str_replace('{DATA_NUM}', $HAVE_DATA_NUM.'', $LANG->WARN_FORM_EDIT);?>
		<?php if ($ADMIN) { ?>
					<br>
					<button class="btn btn-sm btn-danger delete_forms_data_2_edit" data-form-id="<?=$form_id;?>" type="button"><?=$LANG->FORMS_DATA_DELETE_DATA;?></button>
		<?php } ?>
				</div>
	<?php } ?>
<?php } ?>
	

<?php if ($PREVIEW) { ?>
				<h3 style="margin-bottom:20px; color:blue;"><?=$LANG->FORM_PREVIEW_TITLE;?></h3>
<?php } ?>


<?php if ($EDIT) { ?>
				<br> <?php //give some space for popups ?>
				<p id="form_name" style="margin-top:15px;"><input type="text" name="form_title" value="<?=html_chars($form_title);?>" style="width:100%; height:42px; text-align:center; font-weight:bold;" disabled></p>
<?php } else { ?>
				<p id="form_name" style="margin-top:15px;"><b><?=$form_title;?></b></p>
<?php } ?>

			</div>
		</div>
	</div>
	
	
	<section class="container" id="main"<?=($VIEW?' style="padding-bottom:0px;"':'');?>>


<?php /*######### Start Form container wizard ################*/?>
<div id="wizard_container"<?=($VIEW?' class="view_mode"':'');?>>


<?php if ($VIEW) { ?>
	<div id="top-wizard" style="font-size:15px;">
		<span style="white-space:nowrap;"><?=$LANG->RESULTS_CREATED;?>: <b><?=get_date_time($FORM_DATA['timestamp_start'].'');?></b></span> &nbsp; - &nbsp; 
		<span style="white-space:nowrap;"><?=$LANG->RESULTS_MODIFIED;?>: <b><?=get_date_time($FORM_DATA['modified'].'');?></b></span>
		<div class="shadow"></div>
	</div>
<?php } else { //if (!$VIEW) ##################### ?>

  <?php if ($EDIT) { ?>

	<div id="top-wizard" style="font-size:15px;">
	
		<div class="row">
			<div class="col-sm-6">
				<fieldset id="fs-form_timer" class="coolfieldset<?=($timer?'':' collapsed');?>" style="border-width:2px; padding:0; margin:0; color:#555;">
					<legend>
						<label id="timer_has_label" for="timer_has_ck" style="font-weight:600; margin-left:-12px; margin-bottom:0; color:#555;">&nbsp;&nbsp;&nbsp;&nbsp;<?=$LANG->FORM_HAS_TIME_LIMIT;?> : </label>
						<input type="checkbox" id="timer_has_ck" class="timer_has_ck" onchange="this.nextSibling.value=this.checked==true?1:0; return false;"<?=($timer?' checked':'');?>><input type="hidden" id="timer_has" name="timer_has" value="<?=($timer?'1':'0');?>">
					</legend>
					<div id="form_timer_div"<?=($timer?'':' style="display:none;"');?>>
						<span style="padding:0 5px 5px; display:inline-block;">
							<label for="timer_min" style="font-weight:600; margin-bottom:0;"><?=$LANG->FORM_HAS_TIME_LIMIT;?> : </label>
							<input id="timer_min" name="timer_min" value="<?=$timer_time_min;?>" style="width:50px; padding:0; margin:-8px 10px 0;">
						</span>
						<span style="padding:0 5px; display:inline-block;">
							<label for="timer_period" style="font-weight:600; margin-bottom:0;"><?=$LANG->FORM_TIME_LIMIT_PERIOD;?> : </label>
							<select id="timer_period" name="timer_period" style="width:93px;">
								<option value="sec"<?=($timer_time_period=='sec'?' selected':'');?>><?=$LANG->FORM_TIME_LIMIT_SECONDS;?></option>
								<option value="min"<?=($timer_time_period=='min'?' selected':'');?>><?=$LANG->FORM_TIME_LIMIT_MINUTES;?></option>
								<option value="hour"<?=($timer_time_period=='hour'?' selected':'');?>><?=$LANG->FORM_TIME_LIMIT_HOURS;?></option>
							</select>
						</span>
					</div>
				</fieldset>
			</div>
			<div class="col-sm-6">
				<fieldset id="fs-form_days" class="coolfieldset<?=($days_has?'':' collapsed');?>" style="border-width:2px; padding:0; margin:0; color:#555; padding-bottom:5px;">
					<legend>
						<label id="days_has_label" for="days_has_ck" style="font-weight:600; margin-left:-12px; margin-bottom:0; color:#555;">&nbsp;&nbsp;&nbsp;&nbsp;<?=$LANG->FORM_DAYS_AVAILABLE;?> : </label>
						<input type="checkbox" id="days_has_ck" class="days_has_ck" onchange="this.nextSibling.value=this.checked==true?1:0; return false;"<?=($days_has?' checked':'');?>><input type="hidden" id="days_has" name="days_has" value="<?=($days_has?'1':'0');?>">
					</legend>

					<?php $days_arr = (array)$days_arr;?>

					<div id="form_days_div"<?=($days_has?'':' style="display:none;"');?>>
						<label><?=$LANG->FORM_DAY_MON;?>: </label>
						<input type="checkbox" onchange="this.nextSibling.value=this.checked==true?1:0; return false;"<?=(in_array('1',$days_arr)?' checked':'');?>>
						<input type="hidden" name="days_arr" value="<?=(in_array('1',$days_arr)?'1':'0');?>">
						&nbsp;&nbsp;
						<label><?=$LANG->FORM_DAY_TUE;?>: </label>
						<input type="checkbox" onchange="this.nextSibling.value=this.checked==true?1:0; return false;"<?=(in_array('2',$days_arr)?' checked':'');?>>
						<input type="hidden" name="days_arr" value="<?=(in_array('1',$days_arr)?'1':'0');?>">
						&nbsp;&nbsp;
						<label><?=$LANG->FORM_DAY_WED;?>: </label>
						<input type="checkbox" onchange="this.nextSibling.value=this.checked==true?1:0; return false;"<?=(in_array('3',$days_arr)?' checked':'');?>>
						<input type="hidden" name="days_arr" value="<?=(in_array('1',$days_arr)?'1':'0');?>">
						&nbsp;&nbsp;
						<label><?=$LANG->FORM_DAY_THU;?>: </label>
						<input type="checkbox" onchange="this.nextSibling.value=this.checked==true?1:0; return false;"<?=(in_array('4',$days_arr)?' checked':'');?>>
						<input type="hidden" name="days_arr" value="<?=(in_array('1',$days_arr)?'1':'0');?>">
						&nbsp;&nbsp;
						<label><?=$LANG->FORM_DAY_FRI;?>: </label>
						<input type="checkbox" onchange="this.nextSibling.value=this.checked==true?1:0; return false;"<?=(in_array('5',$days_arr)?' checked':'');?>>
						<input type="hidden" name="days_arr" value="<?=(in_array('1',$days_arr)?'1':'0');?>">
						&nbsp;&nbsp;
						<label><?=$LANG->FORM_DAY_SAT;?>: </label>
						<input type="checkbox" onchange="this.nextSibling.value=this.checked==true?1:0; return false;"<?=(in_array('6',$days_arr)?' checked':'');?>>
						<input type="hidden" name="days_arr" value="<?=(in_array('1',$days_arr)?'1':'0');?>">
						&nbsp;&nbsp;
						<label><?=$LANG->FORM_DAY_SUN;?>: </label>
						<input type="checkbox" onchange="this.nextSibling.value=this.checked==true?1:0; return false;"<?=(in_array('7',$days_arr)?' checked':'');?>>
						<input type="hidden" name="days_arr" value="<?=(in_array('1',$days_arr)?'1':'0');?>">
					</div>
				</fieldset>
			</div>
		</div>
	
		<div class="shadow"></div>
	</div>

  <?php } else { //if (!$EDIT) ##################### ?>

	<div id="top-wizard">
		<div class="row">
			<?php
			$timestamp_start_end_array = get_timestamp_start_end_array($CHANGE, $FORM_DATA, $selected_date);
			$form_date_start = $timestamp_start_end_array[0].'';
			$form_time_start = $timestamp_start_end_array[1].'';
			//$form_date_end = $timestamp_start_end_array[2].'';
			$form_time_end = $timestamp_start_end_array[3].'';
			?>
			<div class="col-sm-6 grouping" style="text-align:right;">
				<div class="date-group" style="white-space:nowrap; margin:1px; height:35px;">
					<div id="form_date_title" class="input-group" style="display:inline-table; top:-8px; font-size:14px;"><b><?=$LANG->FORM_DATE;?> : </b></div>
					<div class="input-group date" style="display:inline-table; width:260px;">
						<span class="input-group-addon" style="width:25px; height:28px; padding:5px;"><span class="fa fa-calendar"></span></span>
						<input name="form_date" type="text" class="form-control textfield required" value="<?=$form_date_start;?>" style="height:28px; padding:4px 17px 4px 12px; text-align:center;">
						<span class="input-group-addon" style="width:25px; height:28px; padding:5px;"><span class="fa fa-calendar"></span></span>
					</div>
				</div>
				<div class="time-group" style="white-space:nowrap; margin:1px; margin-left:-2px; height:35px;">
					<div id="form_time_title" class="input-group" style="display:inline-table; top:-8px; font-size:14px;"><b><?=$LANG->FORM_TIME;?> : </b></div>
					<div id="form_time_div" style="display:inline-block; position:relative;">
						<div class="input-group clockpicker time" style="display:inline-table;" data-placement="bottom" data-align="left" data-default="now">
							<span id="form_time_now" class="btn btn-default btn-sm input-group-addon" style="width:43px; padding:5px; font-size:13px; height:28px; margin-left:2px; margin-right:-4px;">&nbsp;<?=$LANG->FORM_TIME_NOW;?></span>
							<input id="form_time" name="form_time" type="text" class="form-control textfield time required" style="width:72px; height:28px; padding:4px 12px;" value="<?=$form_time_start;?>" title="Start">
							<span class="input-group-addon" style="width:25px; height:28px; padding:3px 4px;"><span class="fa fa-clock-o" style="font-size:17px;"></span></span>
							<span class="input-group-addon" style="width:22px; height:28px; padding:5px 0; border-top-right-radius:0; border-bottom-right-radius:0;"><span class="fa fa-long-arrow-right"></span></span>
						</div>
						<div class="input-group clockpicker time" style="display:inline-table;" data-placement="bottom" data-align="right" data-default="now">
							<input id="form_time_end" name="form_time_end" type="text" class="form-control textfield time required" style="width:72px; height:28px; padding:4px 12px; margin-left:-4px;" value="<?=$form_time_end;?>" title="Ende">
							<span class="input-group-addon" style="width:22px; height:28px; padding:3px 4px;"><span class="fa fa-clock-o fa-flip-horizontal" style="font-size:17px;"></span></span>
						</div>
					</div>	
				</div>
			</div>
<?php //####################################
$Athletes_2_Group = array();
$GroupsNames = array();
$Trainer_Group_in = '';
//User2Group
$Groups_select_options = '';
$rows = $db->fetch("SELECT u2g.group_id, u2g.forms_select, g.name  
FROM users2groups u2g
LEFT JOIN `groups` g ON (u2g.group_id = g.id)
WHERE u2g.status = 1 AND user_id = ?", array($UID)); 
if ($db->numberRows() > 0)  {
	foreach ($rows as $row) {
		//Groups with same cat_form = 1
		$t_selected = ($row['group_id']==$group_id?' selected':'');
		$t_disabled = (substr_count(','.$row['forms_select'].',', ','.$category_id.'_'.$form_id.',') ? '' : ' disabled');
		if (!$t_disabled) { //not show disabled bcz problems later in js
			$Groups_select_options .= '<option value="'.$row['group_id'].'"'.$t_selected/*.$t_disabled*/.'>'.$row['name'].'</option>';
			$GroupsNames[$row['group_id']] = $row['name'];
			$Athletes_2_Group[$UID][] = $row['group_id'];
			if ($Trainer_Group_in != '') {
				$Trainer_Group_in .= ',';
			}
			$Trainer_Group_in .= "'".$row['group_id']."'";
		}
	}
}
if ($Trainer_Group_in == '') $Trainer_Group_in = '0';

//Athletes Name
$a_name = $USER['lastname'] != '' ? $USER['lastname'] : $USER['uname'];
$a_vorname = $USER['firstname'] != '' ? $USER['firstname'] : $USER['uname'];
//$Athletes_Select = '<span style="display:inline-block; padding:4px 40px 4px 12px; font-size:17px; font-weight:bold; color:black;">'.$a_vorname.' &nbsp; '.$a_name.'</span>';

//Athletes Select -give the current user first
$Athletes_Select = '<option value="'.$UID.'" selected>'.$a_vorname.' '.$a_name.'</option>';

if ($TRAINER) {
	$Athletes_2_Group = array();
	$GroupsNames = array();
	$Athletes_Select = '';
	//Select Athletes in Group with Trainer this User-$UID
	/*$rows = $db->fetch("SELECT u.id, u.lastname, u.firstname 
FROM users2groups u2g 
JOIN users u ON u.id = u2g.user_id AND (u.level = 10 OR u.id = ?) AND u.status = 1
JOIN users2trainers u2t ON u.id = u2t.user_id AND u2g.group_id = u2t.group_id AND u2t.status = 1 AND u2t.trainer_id = ?
WHERE u2g.group_id = ? AND u2g.status = 1 ORDER BY u.id", array($UID, $UID, $GROUP)); */
	$rows = $db->fetch("SELECT u2g.group_id, g.name AS group_name, u.id, u.uname, u.lastname, u.firstname, u2t.forms_select_write 
FROM users2groups u2g 
LEFT JOIN `groups` g ON (g.id = u2g.group_id) 
LEFT JOIN users u ON (u.id = u2g.user_id AND u.status = 1) 
LEFT JOIN users2trainers u2t ON (u.id = u2t.user_id AND u2g.group_id = u2t.group_id AND u2t.status = 1 AND u2t.trainer_id = ?) 
WHERE u2g.status = 1 
AND ( 
		( CONCAT(',',u2t.forms_select_write,',') LIKE '%,".$cat_form_id.",%' AND u.level = 10 ) 
		OR 
		( u.id = ? AND u2g.group_id IN (".$Trainer_Group_in.") ) 
	)
ORDER BY u2g.group_id, u.level DESC, u.firstname, u.lastname, u.id", array($UID, $UID));
// AND u2g.group_id = ? //now we get all groups
//, $GROUP //,13_5, = ,13_5, in trainer perms
	//echo "<pre>";print_r($rows);exit;
	if ($db->numberRows() > 0) {
		$GP_open_group = false;
		$GP_group = '';
		$GP_group_tmp = '';
		foreach ($rows as $row) {
			//Group
			$GP_group = $row['group_name'];
			if ($GP_group <> $GP_group_tmp) {
				$GroupsNames[$row['group_id']] = $row['group_name'];
				if ($GP_open_group) {
					$Athletes_Select .= '</optgroup>';
				}
				$Athletes_Select .= '<optgroup label="'.$GP_group.'">';
				$GP_open_group = true;
			}
			
			$Athletes_2_Group[$row['id']][] = $row['group_id'];
			
			$selected = '';
			$u_name = $row['lastname'] != '' ? $row['lastname'] : $row['uname'];
			$u_vorname = $row['firstname'] != '' ? $row['firstname'] : $row['uname'];
			//if ($UID == $row['id']) $selected = ' selected'; //select self
			if (isset($_COOKIE['ATHLETE']) AND $_COOKIE['ATHLETE'] == $row['id']) {
				$selected = ' selected';
			}
			//if ($UID == $row['id']) $row['id'] = -1; //select self but athlete-trainer mode //trainers have lvl 30 so not in query
			$Athletes_Select .= '<option value="'.$row['id'].'"'.$selected.'>'.$u_vorname.' '.$u_name.'</option>';
			
			$GP_group_tmp = $GP_group;
		}
		if ($GP_open_group) {
			$Athletes_Select .= '</optgroup>';
		}
	}
}
//####################################
?>

<script>
const V_GroupsNames = <?=json_encode($GroupsNames);?>;
const V_Athletes_2_Groups = <?=json_encode($Athletes_2_Group);?>;
</script>

			<div class="col-sm-6 grouping" style="text-align:left; border-left:3px solid #aaa;">
				<div id="Form_Select_Groups_row" style="white-space:nowrap; margin:1px; height:35px; margin-left:8px;">
					<span id="Group_Title" style="font-size:14px; font-weight:bold; vertical-align:middle;"><?=$LANG->FORM_GROUP_S;?> : </span> 
					<span class="input-group-addon" style="width:25px; height:28px; padding:5px; display:inline-table; margin-right:-5px; border-top-left-radius:4px; border-bottom-left-radius:4px; "><span class="fa fa-users"></span></span>
					<select id="Form_Select_Groups" name="Form_Select_Groups[]" multiple class="required" style="width:100%; max-width:350px; font-size:17px; vertical-align:middle; color:#444; font-weight:bold;">
						<?=$Groups_select_options;?>
					</select>
					<span class="input-group-addon" style="width:25px; height:28px; padding:5px; display:inline-table; margin-left:-5px; border:2px solid #ccc;"><span class="fa fa-users"></span></span>
				</div>
				<div id="Form_Select_Athlete_row" style="white-space:nowrap; margin:1px; height:35px;">
					<span id="Athlete_Title" style="font-size:14px; font-weight:bold; vertical-align:middle;"><?=$LANG->FORM_ATHLETE_S;?> : </span> 
					<span class="input-group-addon" style="width:25px; height:28px; padding:4px 7px; font-size:16px; display:inline-table; margin-right:-6px; border-top-left-radius:4px; border-bottom-left-radius:4px;"><span class="fa fa-user"></span></span>
					<select id="Form_Select_Athlete" name="Form_Select_Athlete" class="required" style="width:100%; max-width:350px; font-size:17px; vertical-align:middle; color:#444; font-weight:bold;">
						<?=$Athletes_Select;?>
					</select>
					<span class="input-group-addon" style="width:25px; height:28px; padding:4px 7px; font-size:16px; display:inline-table; margin-left:-6px; border:2px solid #ccc;"><span class="fa fa-user"></span></span>
				</div>
			</div>
		</div>
		<div class="shadow"></div>
	</div>
  <?php } //if (!$EDIT) end ##################### ?>

<?php } //if (!VIEW) end ##################### ?>
