<?php 
declare(strict_types=1);
require_once('_settings.regmon.php');
require('login/validate.php');

require('results/inc.results_functions.php');

$results_page = 'FORMS_RESULTS'; //need to pass this for inc.results_top
require('results/inc.results_top.php');

//from inc.results_top.php
/** @var bool $is_iframe  */
/** @var int $group_id */
/** @var int $athlete_id */
/** @var int $cat_id */
/** @var int $form_id */
/** @var string $Show_Only_Group_name */
/** @var string $Select__Groups__Options */
/** @var string $info_lines_color_formula */
/** @var string $date_from */
/** @var string $date_to */

$u_name = '';
$u_vorname = '';
$users_data_option = '';


//Users Select
if ($ADMIN OR $LOCATION_ADMIN OR $GROUP_ADMIN OR $GROUP_ADMIN_2) {
	
	$where = '';
	if ($GROUP_ADMIN OR $GROUP_ADMIN_2) {
		$where = 'AND (u.level < 40 OR u.id = "'.$UID.'")';
	}
	$u_rows = $db->fetch("SELECT u.id, u.uname, u.lastname, u.firstname, u.level, u.sport 
		FROM `users` u 
		LEFT JOIN `users2groups` u2g ON u.id = u2g.user_id 
		WHERE u2g.group_id = ? AND u2g.status = 1 AND u.status = 1 $where 
		ORDER BY u.level DESC, u.firstname, u.lastname, u.id", array($group_id)); //Group USERS
	$users_option = '';
	if ($db->numberRows() > 0)  {
		foreach ($u_rows as $u_row) {
			$s_name = $u_row['lastname'] != '' ? $u_row['lastname'] : $u_row['uname'];
			$s_vorname = $u_row['firstname'] != '' ? $u_row['firstname'] : $u_row['uname'];
			if ($GROUP_ADMIN AND !$THIS_GROUP_ADMIN AND $athlete_id!=$u_row['id']) {
			}
			elseif ($GROUP_ADMIN_2 AND !$THIS_GROUP_ADMIN_2 AND $athlete_id!=$u_row['id']) {
			}
			else {
				$users_option .= '<option value="'.$u_row['id'].'"'.($athlete_id==$u_row['id']?' selected':'').'>'.$s_vorname.' '.$s_name.' - '.$u_row['sport'].'</option>'; //+sport
				$users_data_option .= '<option value="'.$u_row['id'].'"'.($athlete_id==$u_row['id']?' selected':'').'>'.$s_vorname.' '.$s_name.'</option>';
			}
			if ($athlete_id == $u_row['id']) {
				$u_name = $s_name;
				$u_vorname = $s_vorname;
			}
		}
	}
}
elseif ($TRAINER AND !$is_iframe) { //from athletes_select.php but changed
	$trainer_users_select = '';

	//Select Athletes in Group with Trainer this User-$UID
	$rows = $db->fetch("SELECT u.id, u.uname, u.lastname, u.firstname FROM `users2groups` u2g 
		JOIN `users` u ON (u.id = u2g.user_id AND u.level = 10 AND u.status = 1) 
		JOIN `users2trainers` u2t ON (u.id = u2t.user_id AND u2g.group_id = u2t.group_id AND u2t.status = 1 AND u2t.trainer_id = ?) 
		WHERE u2g.group_id = ? AND u2g.status = 1 
		ORDER BY u.firstname, u.lastname, u.id", array($UID, $group_id)); 
	//print_r($rows); 
	$has_selected = false;
	if ($db->numberRows() > 0) {
		foreach ($rows as $row) {
			$selected = '';
			if ($athlete_id == $row['id']) {
				$selected = ' selected';
				$has_selected = true;
				
				$u_name = $row['lastname'] != '' ? $row['lastname'] : $row['uname'];
				$u_vorname = $row['firstname'] != '' ? $row['firstname'] : $row['uname'];
			}
			$trainer_users_select .= '<option value="'.$row['id'].'"'.$selected.'>'.$row['firstname'].' '.$row['lastname'].'</option>';
		}
	}
	$s_name = $USER['lastname'] != '' ? $USER['lastname'] : $USER['uname'];
	$s_vorname = $USER['firstname'] != '' ? $USER['firstname'] : $USER['uname'];
	$trainer_users_select = '<option value="'.$UID.'"'.(!$has_selected?' selected':'').'>'.$s_vorname.' '.$s_name.'</option>'.$trainer_users_select;
	$users_data_option = $trainer_users_select;
	/*if (!$has_selected) {
		$u_name = $s_name;
		$u_vorname = $s_vorname;
		$has_selected = true;
	}*/
	if ($athlete_id != $UID AND !$has_selected) {
		echo Exit_Message($LANG->NO_ACCESS_RIGHTS);
		exit;
	}
}
else {
	$u_row = $db->fetchRow("SELECT id, lastname, firstname FROM users WHERE id = ?", array($athlete_id)); //USER
	if ($db->numberRows() > 0)  {
		$u_name = $u_row['lastname'] != '' ? $u_row['lastname'] : $u_row['uname'];
		$u_vorname = $u_row['firstname'] != '' ? $u_row['firstname'] : $u_row['uname'];
		$users_data_option = '<option value="'.$u_row['id'].'" selected>'.$u_vorname.' '.$u_name.'</option>';
	}
}






//form default template
$template_id = 0;
$form_default_template = $db->fetchRow("SELECT template_id FROM users2forms WHERE user_id=? AND group_id=? AND category_id=? AND form_id=?", array($athlete_id, $group_id, $cat_id, $form_id)); 
if ($db->numberRows() > 0) {
	$template_id = $form_default_template['template_id'];
}
?>



<section class="container" id="main">
	<div id="wizard_container">

		<div id="top-wizard">
			<input type="hidden" id="is_iframe" name="is_iframe" value="<?=($is_iframe?'true':'false');?>">
<?php if ($is_iframe) {?>
			<div class="wiz-title"><?=$Show_Only_Group_name;?></div>
<?php } else { ?>
			<div class="group_pad">
				<span class="wiz-title"><?=$LANG->RESULTS_SELECT_GROUP;?> : &nbsp; </span> 
				<select id="Select__Groups" name="Select__Groups">
					<?=$Select__Groups__Options;?>
				</select>
			</div>
<?php } ?>
			<div class="wiz-title" style="margin-top:10px; font-weight:700;"><?=$u_vorname .' &nbsp; '. $u_name;?></div>
			<div class="shadow"></div>
		</div>
		<div id="middle-wizard">

			<?php /* MAIN */?>

			<div class="row">
				<div class="col-sm-12">
					<div id="Edit_Date_link" class="well" style="text-align:center; padding:10px; margin-bottom:10px;">
						<?=get_Results_Period_Selection($date_from, $date_to);?>
					</div>
				</div>
			</div>
	
	
			<div class="row">
				<?php //edit and update Diagram button accordion?>
				<div class="col-sm-12">
					<div class="panel-group" id="accordion">
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">
									<a data-toggle="collapse" data-parent="#accordion" href="#C_Athletes_Data" id="C_Athletes_Data_link"><span class="fa fa-list-alt"></span>&nbsp; <?=$LANG->RESULTS_TAB_ATHLETE_DATA;?></a>
								</h4>
							</div>
							<div id="C_Athletes_Data" class="panel-collapse collapse">
								<div class="panel-body" style="padding:10px 0;">

						
	<?php /* DATA ################################################################ */?>
	<div class="col-sm-12">
		<div class="info_lines_color_formula">
			<?=$info_lines_color_formula;?> 
		</div>
		
		<?php /* Athletes DATA Tables ############################################ */?>
		<div id="Athletes_Data_Fieldsets" class="col-sm-12"></div>
		
		<?php /*init but hide to Athletes*/?>
		<div style="clear:both; padding:5px;<?=(($ATHLETE OR $is_iframe)?' display:none;"':'');?>">
			<div id="Select__Athlete__Toggle" style="padding:5px 0; display:none;">
				<span class="wiz-title"><?=$LANG->LVL_ATHLETE;?> : &nbsp; </span>
				<select id="Select__Athlete">
					<option></option>
					<?=$users_data_option;?>
				</select> &nbsp; 
				<button id="Select__Athlete__Submit" class="forward"></button>
			</div>
		</div>

		<div style="clear:both; text-align:center;">
<?php if (($ADMIN OR $LOCATION_ADMIN OR $GROUP_ADMIN OR $GROUP_ADMIN_2 OR $TRAINER) AND !$is_iframe) { ?>
			<button id="Button__Athlete__Add" type="button" class="btn btn-primary btn-sm" style="float:left;">
				<i style="font-size:15px; vertical-align:middle;" class="fa fa-plus"></i>&nbsp;&nbsp;<b><?=$LANG->RESULTS_ATHLETE_DATA_ADD;?></b>
			</button>
<?php } ?>
			<button id="export_data" type="button" class="btn btn-info btn-md">
				<i style="font-size:17px;" class="fa fa-sign-out"></i>&nbsp;&nbsp;<b><?=$LANG->RESULTS_DATA_EXPORT;?></b>
			</button>
			<button id="Button__Chart__Update" type="button" class="btn btn-success btn-md" style="float:right;">
				<i style="font-size:17px;" class="fa fa-refresh"></i>&nbsp;&nbsp;<b><?=$LANG->RESULTS_UPDATE_DIAGRAM;?></b>
			</button>
		</div>
	</div>
		

								</div>
							</div>
						</div>
					</div><?php //accordion?>
				</div>
			</div><?php //row?>
	
	
<?php require('results/inc.results_bottom.php'); ?>


			<?php /* MAIN End */?>
		</div> 
	</div>
</section>

<?php 
// no footer
// if (!$is_iframe) {
// 	require('php/inc.footer.php');
// }
?>
</body>
</html>