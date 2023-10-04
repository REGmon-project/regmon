<?php // config - ENV file missing 

//from config.php
/** @var string $ENV_File_Content */
/** @var string $ENV_File_Missing_Error */
/** @var string $Config_Test_Database_Error */
/** @var string $Config_Test_Database_Success */

if (!isset($SEC_check_config) OR $SEC_check_config != 'ENV_File_Missing') exit;
?>


<div id="middle-wizard">
	<div class="alert alert-danger" role="alert">
		<h3 style="margin:0;">".env" File Missing</h3>
	</div>

	<div class="row">

			<div class="col-md-5">

				<fieldset id="fs-Database" class="coolfieldset fieldset3">
					<legend>&nbsp; Database Settings &nbsp;</legend>

					<form name="form_db" id="form_db" action="config.php" method="POST">
						<div style="text-align:left;">
							<?=get_Database_Fields(false);?>
						</div>

						<hr style="margin:0 -5px 5px; border-top:7px double #ccc;">
						
						<input type="hidden" name="config_type" value="Config_Test_Database">

						<button id="Config_Test_Database" type="submit" class="btn btn-success  btn-md" style="margin:5px;"  >
							<i class="fa fa-exchange"></i> &nbsp; Test Database Connection</span>
						</button>
					</form>

				</fieldset>

			</div>

			<div class="col-md-1">

				<br>
				Generate ".env" file
				<br>
				<br>
				<div id="set_ENV_File_Content" style="font-size:50px; text-shadow:2px 2px 5px #05ff3a;">
					<i class="fa fa-arrow-circle-o-right"></i>
				</div>
				<br>
				<br>

			</div>



		<div class="col-md-6">

			<fieldset id="fs-ENV_File" class="coolfieldset fieldset3">
				<legend>&nbsp; ".env" File &nbsp;</legend>

				<form name="form_env" id="form_env" action="config.php" method="POST">
					<div style="text-align:left;">
						<textarea id="ENV_File_Content" name="ENV_File_Content" style="width:100%; height:340px;"><?=$ENV_File_Content;?></textarea>
					</div>

					<hr style="margin:0 -5px 5px; border-top:7px double #ccc;">
					
					<input type="hidden" name="config_type" value="Config_ENV_File_Save">

					<button type="submit" id="Config_ENV_File_Save" class="save" style="margin:5px;">Save .env &nbsp; </button>
				</form>

			</fieldset>


		</div>

	</div>

</div>


<div id="bottom-wizard">

<?php if ($ENV_File_Missing_Error != '') { ?>
	<div class="alert alert-danger" role="alert">
		<?=$ENV_File_Missing_Error;?>
	</div>
<?php } ?>

<?php if ($Config_Test_Database_Error != '') { ?>
	<div class="alert alert-danger" role="alert">
		<?=$Config_Test_Database_Error;?>
	</div>
<?php } ?>

<?php if ($Config_Test_Database_Success != '') { ?>
	<div class="alert alert-success" role="alert">
		<?=$Config_Test_Database_Success;?>
	</div>
<?php } ?>

</div>

<script>
jQuery(document).ready(function() {

$('#set_ENV_File_Content').on("click", function (e) {
	$('#ENV_File_Content').val(
		'# DB settings\n' +
		'DB_Host=' + $('#DB_Host').val() + '\n' +
		'DB_Name=' + $('#DB_Name').val() + '\n' +
		'DB_User=' + $('#DB_User').val() + '\n' +
		'DB_Pass=' + $('#DB_Pass').val() + '\n' +
		'\n'
	);
});

}); //end jQuery(document).ready
</script>