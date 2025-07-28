<?php 
declare(strict_types=1);
require_once('_settings.regmon.php');
require('login/validate.php');

require_once('php/inc.common_functions.php');
require_once('export/inc.export_functions.php');

 /** @var int $UID */

$Groups_select_options_n_ids = get_Groups_select_options_n_ids($UID);
$Groups_select_options = $Groups_select_options_n_ids[0] . '';
$Groups_available_ids = $Groups_select_options_n_ids[1] . '';


//get Select__Athletes__Options + Athletes_available_ids
//######################################
//put current user at the top of the options list
$a_name = ($USER['lastname'] != '' ? $USER['lastname'] : $USER['uname']);
$a_vorname = ($USER['firstname'] != '' ? $USER['firstname'] : $USER['uname']);
$Select__Athletes__Options = '<option value="'.$UID.'|'.$a_vorname.' '.$a_name.'">'.$a_vorname.' '.$a_name.' - '.$USER['sport'].'</option>';
$Athletes_available_ids = '';
if ($ADMIN OR $LOCATION_ADMIN OR $GROUP_ADMIN OR $GROUP_ADMIN_2) {
	$Select__Athletes__Options_n_ids = get_Select__Athletes__Options_n_ids__for_Admins($UID, $Groups_available_ids);
	$Select__Athletes__Options .= $Select__Athletes__Options_n_ids[0];
	$Athletes_available_ids = $Select__Athletes__Options_n_ids[1];
}
elseif ($TRAINER) {
	$Select__Athletes__Options_n_ids = get_Select__Athletes__Options_n_ids__for_Trainer($UID, $Groups_available_ids);
	$Select__Athletes__Options .= $Select__Athletes__Options_n_ids[0];
	$Athletes_available_ids = $Select__Athletes__Options_n_ids[1];
}
elseif ($ATHLETE) {
	//only current user
	$Athletes_available_ids = $UID;
}
//###########################################


// get sports for the available Athletes
$where_athletes = '';
if (!$ADMIN) {
	$where_athletes = "WHERE id IN (".$Athletes_available_ids.")";
}
$Sports_select_options = '';
$sports = $db->fetch("SELECT DISTINCT(sport) AS sport FROM users $where_athletes ORDER BY sport", array()); 
if ($db->numberRows() > 0)  {
	foreach ($sports as $sport) {
		$Sports_select_options .= '<option value="'.html_chars($sport['sport']).'">'.html_chars($sport['sport']).'</option>';
	}
}


// get available Forms select options
$Forms_select_options = get_Available_Forms($UID);

// get available notes count
$Notes_count = 0;
if ($ATHLETE) { //only for Athletes
	$notes = $db->fetchRow("SELECT COUNT(*) AS count FROM notes WHERE showInGraph = 1 AND user_id=? ORDER BY timestamp_start", array($UID));
	$Notes_count = $notes['count'];
}
$Notes_select_option = '<option value="Note">'.$LANG->NOTE . ($ATHLETE?' ('.$Notes_count.')':'').'</option>';

// put notes as first option
$Forms_select_options = $Notes_select_option . $Forms_select_options;

//#####################################################################################
$title = $LANG->EXPORT_PAGE_TITLE;
require('php/inc.html_head.php');
//#####################################################################################
?>
<?php /*<!-- Jquery -->*/?>
<link type="text/css" rel="stylesheet" href="node_modules/jquery-ui/dist/themes/smoothness/jquery-ui.min.css">
<link type="text/css" rel="stylesheet" href="js/plugins/chosen/chosen.min.css" />
<script type="text/javascript" src="js/plugins/chosen/chosen.jquery.min.js"></script>

<!-- OTHER JS --> 
<link type="text/css" rel="stylesheet" href="node_modules/bootstrap/dist/css/bootstrap-theme.min.css">
<script type="text/javascript" src="node_modules/bootstrap/dist/js/bootstrap.min.js"></script>

<script type="text/javascript" src="node_modules/moment/min/moment.min.js"></script>
<?php if ($LANG->LANG_CURRENT != 'en') { ?>
<script type="text/javascript" src="js/overrides/moment/<?=$LANG->LANG_CURRENT;?>.js"></script>
<?php } ?>
<link type="text/css" rel="stylesheet" href="node_modules/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css" />
<script type="text/javascript" src="node_modules/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>

<script type="text/javascript" src="js/lang_<?=$LANG->LANG_CURRENT;?>.js<?=$G_VER;?>"></script>
<script type="text/javascript" src="js/common.js<?=$G_VER;?>"></script>
<script type="text/javascript" src="export/js/export.js<?=$G_VER;?>"></script>
<style>
#middle-wizard h3, #middle-wizard h4 { text-align: center; }
.input-group-addon { border-width:1px; border-color:#aaaaaa; }
.input-group-addon span.fa { font-size:16px; }
.input-group.date input { font-size:13px; height:30px; border-width:1px; border-color:#aaaaaa; }
</style>
</head>
<body>
	<?php //require('php/inc.header.php');?>
	
    <div class="container">
		<div class="row">
			<div class="col-md-12 main-title" style="text-align:center;">
				<button type="button" id="home" class="home"> &nbsp; <?=$LANG->HOMEPAGE;?></button>
				<h1><?=$LANG->EXPORT;?></h1>
			</div>
		</div>
	</div>

	<section class="container">

		<form name="form1" id="wrapped" action="export/export_data.php" method="POST">
			
			<div id="wizard_container">

				<div id="middle-wizard">
		
					<div class="step">
						<div class="row">
							<h3><u><?=$LANG->EXPORT_PERSONAL;?></u></h3>

				<?php if ($ATHLETE) { ?>
							<div class="col-md-12">
								<input type="hidden" name="athletes[]" value="<?=$UID.'|'.$a_vorname.' '.$a_name;?>">
								<em><b><?=$LANG->EXPORT_GROUP;?></b></em><br>
								<select name="group[]" data-placeholder="<?=$LANG->EXPORT_ALL;?>" style="width:100%;" multiple class="chosen-select">
									<?=$Groups_select_options;?>
								</select>
								<br>
								<br>
							</div>
				<?php } else { //not $ATHLETE ?>
							<div class="col-md-6">
								<em><b><?=$LANG->EXPORT_GENDER;?></b></em><br>
								<select name="gender[]" data-placeholder="<?=$LANG->EXPORT_ALL;?>" style="width:100%;" multiple class="chosen-select">
									<option value="0"><?=$LANG->REGISTER_MALE;?></option>
									<option value="1"><?=$LANG->REGISTER_FEMALE;?></option>
									<option value="2"><?=$LANG->REGISTER_OTHER;?></option>
								</select>
								<br>
								<br>
								<em><b><?=$LANG->EXPORT_YEAR;?></b></em><br>
								<select name="year[]" data-placeholder="<?=$LANG->EXPORT_ALL;?>" style="width:100%;" multiple class="chosen-select">
									<?for($i=1950; $i <= 2010; $i++) echo '<option value="'.$i.'">'.$i.'</option>';?>
								</select>
								<br>
								<br>
							</div>
							
							<div class="col-md-6">
								<em><b><?=$LANG->EXPORT_SPORT;?></b></em><br>
								<select name="sport[]" data-placeholder="<?=$LANG->EXPORT_ALL;?>" style="width:100%;" multiple class="chosen-select">
									<?=$Sports_select_options;?>
								</select>
								<br>
								<br>
								<em><b><?=$LANG->EXPORT_GROUP;?></b></em><br>
								<select name="group[]" data-placeholder="<?=$LANG->EXPORT_ALL;?>" style="width:100%;" multiple class="chosen-select">
									<?=$Groups_select_options;?>
								</select>
								<br>
								<br>
							</div>
							
							<div class="col-md-12">
								<em><b><?=$LANG->EXPORT_ATHLETES;?></b></em><br>
								<select name="athletes[]" data-placeholder="<?=$LANG->EXPORT_ALL;?>" style="width:100%;" multiple class="chosen-select">
									<?=$Select__Athletes__Options;?>
								</select>
								<br>
								<br>
							</div>
				<?php } ?>
							
						</div>
						
						<br>
						
						<div class="row">
							<h3><u><?=$LANG->EXPORT_DATE;?></u></h3>

							<div class="col-md-6">
								<em><b><?=$LANG->EXPORT_PERIOD;?> (<?=$LANG->EXPORT_DATE_FROM;?>)</b></em><br>
								<div class="input-group date" id="datetimepicker_from">
									<span class="input-group-addon"><span class="fa fa-calendar"></span></span>
									<input type="text" id="date_from" name="date_from" class="form-control" value="" placeholder="<?=$LANG->DATE_FROM;?>"/>
								</div>
								<br>
							</div>
							
							<div class="col-md-6">
								<em><b><?=$LANG->EXPORT_PERIOD;?> (<?=$LANG->EXPORT_DATE_TO;?>)</b></em><br>
								<div class="input-group date" id="datetimepicker_to">
									<span class="input-group-addon"><span class="fa fa-calendar"></span></span>
									<input type="text" id="date_to" name="date_to" class="form-control" value="" placeholder="<?=$LANG->DATE_TO;?>"/>
								</div>
								<br>
							</div>

						</div>
						
						<br>
						
						<div class="row">
							<h3><u><?=$LANG->EXPORT_DATA;?></u></h3>

							<div class="col-md-6">
								<em><b><?=$LANG->EXPORT_FORM;?></b></em><br>
								<select name="forms[]" data-placeholder="<?=$LANG->EXPORT_ALL;?>" style="width:100%;" multiple class="chosen-select">
									<?=$Forms_select_options;?>
								</select>
								<br>
								<br>
							</div>
							
							<div class="col-md-6">
								<em><b><?=$LANG->EXPORT_VIEW_FIELDS;?></b></em><br>
								<select name="fields[]" data-placeholder="<?=$LANG->EXPORT_ALL;?>" style="width:100%;" multiple class="chosen-select">
									<option value="0"><?=$LANG->EXPORT_HEADER_USER_ID;?></option>
									<option value="1"><?=$LANG->EXPORT_HEADER_LASTNAME;?></option>
									<option value="2"><?=$LANG->EXPORT_HEADER_FIRSTNAME;?></option>
									<option value="3"><?=$LANG->EXPORT_HEADER_GENDER;?></option>
									<option value="4"><?=$LANG->EXPORT_HEADER_BIRTH_DATE;?></option>
									<option value="5"><?=$LANG->EXPORT_HEADER_SPORT;?></option>
									<option value="6"><?=$LANG->EXPORT_HEADER_GROUP_ID;?></option>
									<option value="7"><?=$LANG->EXPORT_HEADER_SRV_DATE;?></option>
									<option value="8"><?=$LANG->EXPORT_HEADER_SRV_TIME;?></option>
									<option value="9"><?=$LANG->EXPORT_HEADER_SRV_DATE_2;?></option>
									<option value="10"><?=$LANG->EXPORT_HEADER_SRV_TIME_2;?></option>
									<option value="11"><?=$LANG->EXPORT_HEADER_SRV_DATE_NO;?></option>
								</select>
								<br>
								<br>
							</div>

						</div>
						
					</div>
			
				</div>
		
				<div id="bottom-wizard">
					<button type="submit" id="submit" class="forward"><?=$LANG->REGISTER_SUBMIT;?></button>
				</div>

			</div>

		</form>

	</section>

	<br>
	<br>

	<?php //require('php/inc.footer.php');?>

	<div id="toTop" title="<?=$LANG->PAGE_TOP;?>">&nbsp;</div>

</body>
</html>