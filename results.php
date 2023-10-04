<?php 
declare(strict_types=1);
require_once('_settings.regmon.php');
require('login/validate.php');

require('results/inc.results_functions.php');

$results_page = 'RESULTS';
require('results/inc.results_top.php');

//inc,results_top.php
/** @var bool $is_iframe */
/** @var string $date_from */
/** @var string $date_to */
/** @var string $Select__Groups__Options */
/** @var string $info_lines_color_formula */
?>


<section class="container" id="main">
	<div id="wizard_container">

		<div id="top-wizard">
			<div class="wiz-title"><i><?=$LANG->RESULTS_PAGE_HEADER;?>:</i></div>
			<div class="shadow"></div>
		</div>
		
		<div id="middle-wizard">

			<?php /* MAIN */?>

			<div class="row">
				<?php // Templates button accordion?>
				<div class="col-sm-12">
					<div class="panel-group" id="accordion_templates">
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">
									<a data-toggle="collapse" data-parent="#accordion_table" href="#C_Templates" id="C_Templates_link">
										<span class="fa fa-list-alt" style="margin:-2px 0 0 -15px; position:absolute; font-size:14px;"></span>
										<span class="fa fa-table" style="margin:10px 0 0 -14px; position:absolute; font-size:14px;"></span>
										<span class="fa fa-floppy-o" style="margin:0 0 0 0; font-size:18px;"></span>&nbsp; <?=$LANG->TEMPLATES;?>
									</a>
								</h4>
							</div>
							<div id="C_Templates" class="panel-collapse collapse in">
								<div class="panel-body" style="padding:10px 0;">
	<?php //---------------------->> ?>
	<?php /* TEMPLATES Saves ############################################################ */?>
	<div class="col-sm-12" style="text-align:center;">
		<form id="Form__Results_Template">
			<div>
				<span id="Select__Results_Templates__Div"></span>&nbsp;
				<span style="white-space:nowrap;">
					<button id="Results_Template__Load" type="button" class="btn btn-info btn-md"><i style="font-size:17px;" class="fa fa-repeat"></i>&nbsp;&nbsp;<b><?=$LANG->LOAD;?></b></button>&nbsp;
					<button id="Results_Template_Delete" type="button" class="btn btn-danger btn-md"><i style="font-size:17px;" class="fa fa-times-circle"></i>&nbsp;&nbsp;<b><?=$LANG->DELETE;?></b></button>&nbsp;
					<button id="Results_Template_2_Dashboard" type="button" class="btn btn-black btn-md" style="padding:6px;" title="<?=$LANG->RESULTS_TEMPLATES_DASH_TITLE;?>"><i class="fa fa-plus-circle" style="vertical-align:middle; margin-top:-1px;"></i>&nbsp;<i class="fa fa-th" style="font-size:17px; margin-top:-2px; vertical-align:middle;"></i>&nbsp;&nbsp;<b><?=$LANG->DASHBOARD;?></b></button>
				</span>
			</div>
			<hr style="margin:10px;">
			<div>
				<span style="font-size:16px;"><i><?=$LANG->RESULTS_TEMPLATES_SAVE_TEXT;?>:</i></span><br>
				<input type="text" id="Results_Template__Name" value="" placeholder="<?=$LANG->RESULTS_TEMPLATE;?>" class="form-control required" notExist=true />
				<button id="Results_Template__Save" type="button" class="btn btn-success btn-md" style="margin:5px 0;"><i style="font-size:17px;" class="fa fa-floppy-o"></i>&nbsp;&nbsp;<b><?=$LANG->SAVE;?></b></button>
			</div>

<?php //require('results/inc.permissions.php'); ?>

		</form>
	</div>
	<?php //---------------------->> ?>

								</div><?php /*.panel-body*/?>
							</div><?php /*#C_Templates*/?>
						</div>
					</div><?php //accordion_table?>
				</div>
				
			</div>

			

			<div class="row">
			
				<?php //Athlete Data button accordion  -- edit and update Diagram options?>
				<div class="col-sm-12">
					<div class="panel-group" id="accordion_options">
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">
									<a data-toggle="collapse" data-parent="#accordion_options" href="#C_Options" id="C_Options_link"><span class="fa fa-calendar"></span>&nbsp; <?=$LANG->RESULTS_TAB_PERIOD_N_DATA;?></a>
								</h4>
							</div>
							<div id="C_Options" class="panel-collapse collapse">
								<div class="panel-body" style="text-align:center;">
								
									<input type="hidden" id="is_iframe" name="is_iframe" value="<?=($is_iframe?'true':'false');?>">
									
									<div style="font-size:16px; font-style:italic;"><?=$LANG->RESULTS_PERIOD_N_DATA_SELECT;?>:</div>
									
									<div class="col-sm-12" style="padding:5px 0 10px 0;">
										<div id="Edit_Date_link" style="text-align:center; padding:10px;">
											<?=get_Results_Period_Selection($date_from, $date_to, true);?>
										</div>
										<hr style="margin:0 10%;">
									</div>
									
									
									<?php /* Select Groups etc ############################### */?>
									<div class="col-sm-12">
										<div id="resetSelect_div">
											<span id="resetSelect" title="<?=$LANG->RESULTS_RESET;?>"><i class="fa fa-times-circle"></i></span>
										</div>
										<?php /* Select Groups ########################## */?>
										<div id="Select__Groups__Row">
											<span id="Select__Groups__Row__Span">
												<div id="Select__Groups__Div">
													<span class="wiz-title"><?=$LANG->RESULTS_SELECT_GROUPS;?> : &nbsp; </span> 
													<select id="Select__Groups" name="Select__Groups" multiple="multiple" style="display:none;">
														<?=$Select__Groups__Options;?>
													</select> &nbsp; 
													<button id="Button__Select__Groups__Submit" class="forward" title="<?=$LANG->RESULTS_BUTTON_APPLY_CHANGES;?>"></button>
												</div>
											</span>
										</div>
										<?php /* Select Athletes ########################## */?>
										<div id="Select__Athletes__Div"></div>
										<?php /* Select Forms ############################# */?>
										<div id="Select__Forms__Div"></div>
										<?php /* Select Form Fields ####################### */?>
										<div id="Select__Forms_Fields__Div"></div>
										<?php /* DATA ##################################### */?>
										<div id="DATA__Div"></div>
									</div>
								</div>
							</div>
						</div>
					</div><?php //accordion?>
				</div>
					
			</div><?php //row?>


			<div class="row">

				<?php //Athlete Data button accordion  -- edit and update Diagram options?>
				<div class="col-sm-12">
					<div class="panel-group" id="accordion_athletes">
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">
									<a data-toggle="collapse" data-parent="#accordion_athletes" href="#C_Athletes_Data" id="C_Athletes_Data_link"><span class="fa fa-list-alt"></span>&nbsp; <?=$LANG->RESULTS_TAB_ATHLETE_DATA;?></a>
								</h4>
							</div>
							<div id="C_Athletes_Data" class="panel-collapse collapse">
								<div class="panel-body" style="padding:10px 0;">
								
									<div class="info_lines_color_formula">
										<?=$info_lines_color_formula;?>
									</div>

									<?php /* Athletes DATA Tables ##################################################### */?>
									<div id="Athletes_Data_Fieldsets" class="col-sm-12"></div>
				
									<div style="clear:both; padding:5px;">
										<button id="Button__Chart__Update" type="button" class="btn btn-success btn-md" style="float:right;"><i style="font-size:17px;" class="fa fa-refresh"></i>&nbsp;&nbsp;<b><?=$LANG->RESULTS_UPDATE_DIAGRAM;?></b></button>
									</div>
									
								</div>
							</div>
						</div>
					</div><?php //accordion?>
				</div>
					
			</div><?php //row?>
			<?php /*<hr style="margin:0 0 20px 0;">*/?>

	
			<div class="row">
			
				<?php // Intervals button accordion?>
				<div class="col-sm-12">
					<div class="panel-group" id="accordion_interval">
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">
									<a data-toggle="collapse" data-parent="#accordion_table" href="#C_Intervals_Data" id="C_Intervals_Data_link"><span class="fa fa-table"></span>&nbsp; <?=$LANG->RESULTS_TAB_INTERVAL_DATA;?></a>
								</h4>
							</div>
							<div id="C_Intervals_Data" class="panel-collapse collapse">
								<div class="panel-body" style="padding:10px 0;">
								
									<div class="info_lines_color_formula">
										<?=$info_lines_color_formula;?>
									</div>

									<?php /* INTERVALS Tables ############################################################ */?>
									<div id="Intervals_Data_Fieldsets" class="col-sm-12"></div>
									
									<div style="clear:both; padding:5px; text-align:center;">
										<button id="DATA__Interval__Add" type="button" class="btn btn-primary btn-sm" style="float:left;" disabled><i style="font-size:15px; vertical-align:middle;" class="fa fa-plus"></i>&nbsp;&nbsp;<b><?=$LANG->RESULTS_INTERVAL_ADD;?></b></button>
										<button id="export_data" type="button" class="btn btn-info btn-md"><i style="font-size:17px;" class="fa fa-sign-out"></i>&nbsp;&nbsp;<b><?=$LANG->RESULTS_DATA_EXPORT;?></b></button>
										<button id="Button__Chart__Update_2" type="button" class="btn btn-success btn-md" style="float:right;"><i style="font-size:17px;" class="fa fa-refresh"></i>&nbsp;&nbsp;<b><?=$LANG->RESULTS_UPDATE_DIAGRAM;?></b></button>
									</div>
									
								</div>
							</div>
						</div>
					</div><?php //accordion_table?>
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