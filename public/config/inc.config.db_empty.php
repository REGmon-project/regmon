<?php // config - Empty Database 

//from config.php
/** @var string $DB_Migration_File_Selected */
/** @var mixed $DB_Migrations_Files_arr */
/** @var string $DB_Migration_File_Error */

if (!isset($SEC_check_config) OR $SEC_check_config != 'APP_Database_Empty') exit;
?>


<form name="form_db" id="form_db" action="config.php" method="POST">

	<div id="middle-wizard">
		<div class="alert alert-danger" role="alert">
			<h3 style="margin:0;">Empty Database</h3>
		</div>

		<div class="row">

			<div class="col-md-3">
			</div>

			<div class="col-md-6">

				<fieldset id="fs-Database" class="coolfieldset fieldset3">
					<legend>&nbsp; Database Migrations &nbsp;</legend>

					<div style="text-align:left; font-family:monospace;">
						<?=get_HTML_Select( //key, value, options_arr, label, sub_label, placeholder
							'DB_Migration_File', 
							$DB_Migration_File_Selected, 
							$DB_Migrations_Files_arr, 
							'Select DB Migration File', 
							'', 
							''
						);?>
					</div>
					
				</fieldset>

			</div>

			<div class="col-md-3">
			</div>

		</div>

	</div>


	<div id="bottom-wizard">

	<?php if ($DB_Migration_File_Error != '') { ?>
		<div class="alert alert-danger" role="alert">
			<?=$DB_Migration_File_Error;?>
		</div>
	<?php } ?>

		<input type="hidden" name="config_type" value="Config_APP_Database_Init">

		<button type="submit" id="Config_Import_Migration_File" class="bttn" style="margin:5px;">
			Import Migration File &nbsp;&nbsp; 
			<i class="fa fa-file-code-o"></i>
			<i class="fa fa-arrow-right" style="margin-left:5px;"></i>
			<i class="fa fa-database" style="margin-left:5px;"></i>
		</button>

	</div>

</form>
