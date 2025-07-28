<?php // inc index main 

//inc.groups_athletes_select.php
/** @var string $Groups_select_options */
/** @var array $private_groups */
/** @var string $Athlete_Name */
/** @var string $Athletes_Select */
/** @var string $selected_GROUP_name */
/** @var string $selected_LOCATION_name */

if ($SEC_check != $CONFIG['SEC_Page_Secret']) exit;

$DIS = ' style="display:none;"';
$DIS2 = ' display:none;';
?>
	<div id="main_container" class="container">	
		<?php /*<header><h1><b><?=$LANG->APP_NAME;?></b><span><?=$LANG->APP_INFO;?></span></h1></header>*/?>
		<?php /*<h3 style="text-align:center;"><b><u><?=$selected_LOCATION_name;?></u></b></h3>*/?>
		<?php /*<h2 style="text-align:center;"><b><?=$selected_GROUP_name;?></b></h2>*/?>
		
		<div class="col-md-12 main_container">
			<div id="main_container_top" class="row top_container">
				<div class="col-sm-12">
					<div id="group_n_athlete">
						<div id="Select_Group_row">
							<span id="Select_Group_row_span">
								<div id="Select_Group_div">
									<span id="Select_Group_title"><?=$LANG->INDEX_GROUP;?> : &nbsp; </span> 
									<select name="Select_Group" id="Select_Group">
										<?=(substr_count($Groups_select_options, '<option') AND count($private_groups)) ? '<option></option>' : '';?>
										<?=$Groups_select_options;?>
									</select>
									<div id="private_group" class="form-group">
										<div class="input-group">
											<input type="text" id="private_key" name="private_key" value="" class="form-control" placeholder="<?=$LANG->INDEX_PRIVATE_KEY;?>"/>
											<span id="private_submit" title="<?=$LANG->INDEX_REQUEST_FOR_GROUP;?>" class="input-group-addon"><span class="fa fa-sign-in"></span> &nbsp; <span class="fa fa-group"></span></span>
											<span id="private_close" title="<?=$LANG->BACK;?>" class="input-group-addon"><span class="fa fa-times-circle"></span></span>
										</div>
									</div>
								</div>
							</span>
						</div>
						<div id="Select_Athletes_row">
							<span id="Select_Athletes_row_span">
								<div id="Athlete_Name_div"><?=$Athlete_Name;?></div>
								<div id="Select_Athletes_div"><?=$Athletes_Select;?></div>
							</span>
						</div>
						<div id="VIEW_select_row">
							<div id="view_radio">
								<div class="btn-group" data-toggle="buttons">
									<label class="btn btn-default options_calendar"><input type="radio" name="options_calendar" id="view_calendar" autocomplete="off" checked><i class="fa fa-calendar"></i> &nbsp; <?=$LANG->INDEX_VIEW_CALENDAR;?></label>
									<label class="btn btn-default options_calendar"><input type="radio" name="options_calendar" id="view_options" autocomplete="off"><i class="fa fa-gear"></i> &nbsp; <?=$LANG->INDEX_VIEW_OPTIONS;?>
									</label>
								</div>
								<span id="requestsCount">
									<span id="requestsCountValue">0</span>
								</span>
								<img id="rC_loading" src="img/ldg.gif">
							</div>
						</div>
						<div class="shadow" style="bottom:-16px;"></div>
					</div>
				</div>
			</div>
			
			<div id="views">
			
				<div id="group_calendar" class="row">
					<div id="calendar"></div>
				</div>
				
				<div id="group_data" class="row">

					<?php /* ##########################################################
					LEFT SIDE #########################################################
					################################################################### */?>
					<div class="col-sm-6">
						<div class="panel-group" id="accordion1">
							<?php /*##### Athlete - Edit Selection of Forms ##############*/?>
							<div class="panel panel-default">
								<div class="panel-heading">
									<h4 class="panel-title">
										<a data-toggle="collapse" data-parent="#accordion1" href="#C_Athlete_Forms_Select" id="C_Athlete_Forms_Select_link" class="collapsed"><?=$LANG->TAB_ATHLETE_2_GROUP_FORM_SELECTION;?> (<?=$selected_GROUP_name;?>)</a>
									</h4>
								</div>
								<div id="C_Athlete_Forms_Select" class="panel-collapse collapse">
									<div class="panel-body" style="text-align:center;">
										<?php /*###### Athlete_Forms_Select_Menu AJAX ######*/?>
										<nav id="A_Athlete_Forms_Select_Menu" class="nav shadow1"></nav>
									</div>
								</div>
							</div>
							<?php /*##### Import-Export Data - ALL #######################*/?>
							<div class="panel panel-default"<?=((!$ATHLETE)?' style="margin-top:6px;"':'');?>>
								<div class="panel-heading">
									<h4 class="panel-title">
										<a data-toggle="collapse" data-parent="#accordion1" href="#C_Import_Export_Data" id="C_Import_Export_Data_link" class="collapsed"><?=$LANG->TAB_IMPORT_EXPORT;?></a>
									</h4>
								</div>
								<div id="C_Import_Export_Data" class="panel-collapse collapse">
									<div class="panel-body">
										<?php /*###### Export Data Button ######*/?>
										<div style="text-align:center; padding:15px;">
											<button type="button" class="export"><?=$LANG->INDEX_EXPORT_LINK;?> &nbsp; </button>
										</div>
										<?php /*###### Import Data Button ######
										<div style="text-align:center; padding:15px;">
											<button type="button" class="import"><?=$LANG->INDEX_IMPORT_LINK;?> &nbsp; </button>
										</div>*/?>
									</div>
								</div>
							</div>
			<?php 	if ($ADMIN OR $LOCATION_ADMIN OR $GROUP_ADMIN OR $GROUP_ADMIN_2) {
						if (!$ADMIN AND !$THIS_LOCATION_ADMIN AND !$THIS_GROUP_ADMIN AND !$THIS_GROUP_ADMIN_2) {
							$dis_panel = true;
						} else {
							$dis_panel = false; 
						}
			?>
							<?php /*##### Group Requests ######################################*/?>
							<div class="panel panel-default"<?=($dis_panel?$DIS:'');?> style="margin-top:8px;">
								<div class="panel-heading h_group_admin">
									<h4 class="panel-title" style="position:relative;">
										<a data-toggle="collapse" data-parent="#accordion1" href="#C_Group_Requests" id="C_Group_Requests_link" class="collapsed"><?=$LANG->TAB_GROUP_ACCESS;?> (<?=$selected_GROUP_name;?>)</a>
										<span id="GRP_requestsCount">
											<span id="GRP_requestsCountValue">0</span>
										</span>
									</h4>
								</div>
								<div id="C_Group_Requests" class="panel-collapse collapse">
									<div class="panel-body">
										<?php /*###### Group Requests AJAX ######*/?>
										<nav id="A_Group_Requests" class="nav"></nav>
									</div>
								</div>
							</div>
							<?php /*##### Group Users ######################################*/?>
							<div class="panel panel-default"<?=($dis_panel?$DIS:'');?> style="margin-top:6px; margin-bottom:7px;">
								<div class="panel-heading h_group_admin">
									<h4 class="panel-title">
										<a data-toggle="collapse" data-parent="#accordion1" href="#C_Group_Users" id="C_Group_Users_link" class="collapsed"><?=$LANG->TAB_GROUP_USERS;?><span id="GROUPS_name"><?='&nbsp; ('.$selected_GROUP_name.')';?></span></a>
									</h4>
								</div>
								<div id="C_Group_Users" class="panel-collapse collapse">
									<div class="panel-body">
										<?php /*###### Group Users AJAX ######*/?>
										<div id="A_Group_Users"></div>
									</div>
								</div>
							</div>
			<?php 	} //end if ($ADMIN OR $LOCATION_ADMIN OR $GROUP_ADMIN OR $GROUP_ADMIN_2) ?>
							<?php /*##### Exit Group - ALL #####################################*/?>
							<div class="panel panel-default">
								<div class="panel-heading">
									<h4 class="panel-title">
										<a data-toggle="collapse" data-parent="#accordion1" href="#C_Group_Leave" id="C_Group_Leave_link" class="collapsed"><?=$LANG->TAB_GROUP_LEAVE;?> (<?=$selected_GROUP_name;?>)</a>
									</h4>
								</div>
								<div id="C_Group_Leave" class="panel-collapse collapse">
									<div class="panel-body">
										<?php /*###### Group Leave Button ######*/?>
										<div id="group_buttons_message_in"></div>
										<div id="group_user_cancel_access_div">
											<button id="group_user_cancel_access" type="submit" class="submit_group" style="display:none;"><?=$LANG->TAB_GROUP_LEAVE;?> (<?=$selected_GROUP_name;?>) &nbsp; &nbsp; &nbsp; </button>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>


					<?php /* ##########################################################
					RIGHT SIDE ########################################################
					################################################################### */?>
					<div class="col-sm-6">
						<div class="panel-group" id="accordion2">
		<?php 	if ($TRAINER) { //Trainers --admin and others to first column
					if (!$THIS_GROUP_TRAINER) {
						$dis_panel = true;
					} else {
						$dis_panel = false;
					}

				 	if ($THIS_GROUP_TRAINER) { //only if trainer and trainer in group
		?>
							<?php /*##### Request_Access_From_Athletes ##########################*/?>
							<div class="panel panel-default"<?=($dis_panel?$DIS:'');?> style="margin-top:-1px;">
								<div class="panel-heading h_trainer">
									<h4 class="panel-title">
										<a data-toggle="collapse" data-parent="#accordion2" href="#C_Request_Access_From_Athletes" id="C_Request_Access_From_Athletes_link" class="collapsed"><?=$LANG->TAB_TRAINER_REQUESTS_2_ATHLETES;?></a>
									</h4>
								</div>
								<div id="C_Request_Access_From_Athletes" class="panel-collapse collapse">
									<div class="panel-body">
										<?php /*###### Request_Access_From_Athletes AJAX ######*/?>
										<div id="A_Request_Access_From_Athletes"></div>
									</div>
								</div>
							</div>
							<?php /*######## Show FormsSelect approvals from Athletes to Trainers #####*/?>
							<div class="panel panel-default">
								<div class="panel-heading">
									<h4 class="panel-title">
										<a data-toggle="collapse" data-parent="#accordion2" href="#C_Trainer_Access_To_Athletes_Forms" id="C_Trainer_Access_To_Athletes_Forms_link" class="collapsed"><?=$LANG->TAB_FORMS_ACCESS_FROM_ATHLETES;?></a>
									</h4>
								</div>
								<div id="C_Trainer_Access_To_Athletes_Forms" class="panel-collapse collapse">
									<div class="panel-body" style="text-align:center;">
										<?php /*###### Trainer_Access_To_Athletes_Forms AJAX ######*/?>
										<nav id="A_Trainer_Access_To_Athletes_Forms" class="nav shadow1"></nav>
									</div>
								</div>
							</div>
		<?php 		} //end if ($THIS_GROUP_TRAINER)
			 	} //end if ($TRAINER)
			 	if ($ATHLETE) { //only Athletes 
		?>
							<?php /*##### Requests from Trainers ###############################*/?>
							<div class="panel panel-default">
								<div class="panel-heading">
									<h4 class="panel-title" style="position:relative;">
										<a data-toggle="collapse" data-parent="#accordion2" href="#C_Requests_From_Trainers" id="C_Requests_From_Trainers_link" class="collapsed"><?=$LANG->TAB_ATHLETE_REQUESTS_FROM_TRAINERS;?></a>
										<span id="ATH_requestsCount">
											<span id="ATH_requestsCountValue">0</span>
										</span>
									</h4>
								</div>
								<div id="C_Requests_From_Trainers" class="panel-collapse collapse">
									<div class="panel-body">
										<?php /*###### Requests from Trainers AJAX ######*/?>
										<nav id="A_Requests_From_Trainers" class="nav"></nav>
									</div>
								</div>
							</div>
							<?php /*######## Athlete_Give_Forms_Access_To_Trainers ##############*/?>
							<div class="panel panel-default">
								<div class="panel-heading">
									<h4 class="panel-title">
										<a data-toggle="collapse" data-parent="#accordion2" href="#C_Athlete_Give_Forms_Access_To_Trainers" id="C_Athlete_Give_Forms_Access_To_Trainers_link" class="collapsed"><?=$LANG->TAB_FORMS_ACCESS_TO_TRAINERS;?></a>
									</h4>
								</div>
								<div id="C_Athlete_Give_Forms_Access_To_Trainers" class="panel-collapse collapse">
									<div class="panel-body" style="text-align:center;">
										<?php /*###### Athlete_Give_Forms_Access_To_Trainers AJAX ######*/?>
										<nav id="A_Athlete_Give_Forms_Access_To_Trainers" class="nav shadow1"></nav>
									</div>
								</div>
							</div>
		<?php  	} //end if ($ATHLETE) { //only Athletes 
				if ($ADMIN OR $LOCATION_ADMIN OR $GROUP_ADMIN OR $GROUP_ADMIN_2) {
					if (!$ADMIN AND !$THIS_LOCATION_ADMIN AND !$THIS_GROUP_ADMIN AND !$THIS_GROUP_ADMIN_2) {
						$dis_panel = true;
					} else $dis_panel = false;
					//new only when 'this' admin can see that 
					if ($ADMIN OR $THIS_LOCATION_ADMIN OR $THIS_GROUP_ADMIN OR $THIS_GROUP_ADMIN_2) {
		?>
							<?php /*######## Group_Forms_Select ###################################*/?>
							<div class="panel panel-default" style="margin-top:-1px;<?=($dis_panel?$DIS2:'');?>">
								<div class="panel-heading h_group_admin">
									<h4 class="panel-title">
										<a data-toggle="collapse" data-parent="#accordion2" href="#C_Group_Forms_Select" id="C_Group_Forms_Select_link" class="collapsed"><?=$LANG->TAB_GROUP_FORMS;?> (<?=$selected_GROUP_name;?>)</a>
									</h4>
								</div>
								<div id="C_Group_Forms_Select" class="panel-collapse collapse">
									<div class="panel-body" style="text-align:center;">
										<?php /*###### Group_Forms_Select AJAX ######*/?>
										<nav id="A_Group_Forms_Select" class="nav shadow1"></nav>
									</div>
								</div>
							</div>
							<?php /*##### Forms Grid ###########################################*/?>
							<div class="panel panel-default"<?=((!$ADMIN AND !$THIS_LOCATION_ADMIN AND !$THIS_GROUP_ADMIN AND !$THIS_GROUP_ADMIN_2)?' style="margin-top:-1px;"':'');?>>
								<div class="panel-heading h_group_admin">
									<h4 class="panel-title">
										<a data-toggle="collapse" data-parent="#accordion2" href="#C_Forms" id="C_Forms_link" class="collapsed"><?=$LANG->TAB_FORMS;?></a>
									</h4>
								</div>
								<div id="C_Forms" class="panel-collapse collapse">
									<div class="panel-body">
										<?php /*###### Forms Grid AJAX ######*/?>
										<div id="A_Forms"></div>
									</div>
								</div>
							</div>
							<?php /*##### Categories ###############################################*/?>
							<div class="panel panel-default"<?=((!$ADMIN AND !$THIS_LOCATION_ADMIN AND !$THIS_GROUP_ADMIN AND !$THIS_GROUP_ADMIN_2)?' style="margin-top:-1px;"':'');?>>
								<div class="panel-heading h_group_admin">
									<h4 class="panel-title">
										<a data-toggle="collapse" data-parent="#accordion2" href="#C_Categories" id="C_Categories_link" class="collapsed"><?=$LANG->TAB_CATEGORIES;?></a>
									</h4>
								</div>
								<div id="C_Categories" class="panel-collapse collapse">
									<div class="panel-body">
										<?php /*###### Categories Grid AJAX ######*/?>
										<div id="A_Categories"></div>
									</div>
								</div>
							</div>
							<?php /*Sports Dropdowns*/?>
							<div class="panel panel-default" style="margin-top:6px;">
								<div class="panel-heading h_group_admin">
									<h4 class="panel-title">
										<a data-toggle="collapse" data-parent="#accordion2" href="#C_Sports_Dropdowns" id="C_Sports_Dropdowns_link" class="collapsed"><?=$LANG->TAB_SPORTS_N_DROPDOWNS;?></a>
									</h4>
								</div>
								<div id="C_Sports_Dropdowns" class="panel-collapse collapse">
									<div class="panel-body">
										<?php /*###### Sports Dropdowns AJAX ######*/?>
										<div id="A_Sports_Dropdowns"></div>
									</div>
								</div>
							</div>
		<?php  		} //end if $ADMIN OR $THIS_LOCATION_ADMIN OR $THIS_GROUP_ADMIN OR $THIS_GROUP_ADMIN_2
		  		} //end if ($ADMIN OR $LOCATION_ADMIN OR $GROUP_ADMIN OR $GROUP_ADMIN_2)
				if ($ADMIN OR $LOCATION_ADMIN OR $GROUP_ADMIN OR $GROUP_ADMIN_2) {
		?>
							<?php /*Location Groups*/?>
							<div class="panel panel-default">
								<div class="panel-heading h_location_admin">
									<h4 class="panel-title">
										<a data-toggle="collapse" data-parent="#accordion2" href="#C_Location_Groups" id="C_Location_Groups_link" class="collapsed"><?=$LANG->TAB_LOCATION_GROUPS;?><span id="LOCATION_GROUPS_name"><?='&nbsp; ('.$selected_LOCATION_name.')';?></span></a>
									</h4>
								</div>
								<div id="C_Location_Groups" class="panel-collapse collapse">
									<div class="panel-body">
										<?php /*###### Location Groups AJAX ######*/?>
										<div id="A_Location_Groups"></div>
									</div>
								</div>
							</div>
		<?php  	} //end if ($ADMIN OR $LOCATION_ADMIN OR $GROUP_ADMIN OR $GROUP_ADMIN_2) ?>
						</div>
					</div>
				</div>
				
			</div>
			
			<?php
			$request_for_group_txt = str_replace('{GROUP}', $selected_GROUP_name, $LANG->REQUEST_FOR_X_GROUP);
			?>
			<div id="group_buttons_message"></div>
			<div id="group_buttons">
				<button id="group_user_request_access" type="submit" name="process" class="submit_group"><?=$request_for_group_txt;?> &nbsp; &nbsp; &nbsp; </button>
				<button id="group_user_request_access_AN" type="submit" name="process" class="submit_group"><?=$request_for_group_txt;?> &nbsp; &nbsp; &nbsp; </button>
				<button id="group_user_request_access_AL_user" type="submit" name="process" class="submit_group"><?=$request_for_group_txt;?> &nbsp; &nbsp; &nbsp; </button>
				<button id="group_user_request_access_AL_groupadmin" type="submit" name="process" class="submit_group"><?=$request_for_group_txt;?> &nbsp; &nbsp; &nbsp; </button>
				<button id="group_user_cancel_request_user" type="submit" name="process" class="submit_group"><?=$LANG->REQUEST_FOR_GROUP_CANCEL;?> &nbsp; &nbsp; &nbsp; </button>
			</div>
		</div>
	</div>
	
	<div style="display:none;">
		<?php /*###### Calendar Forms Select Box AJAX ######*/?>
		<nav id="A_Box_Forms_Menu" class="nav shadow1"></nav>
	</div>

