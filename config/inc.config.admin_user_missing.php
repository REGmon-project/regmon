<?php // config - Admin User Missing 

//from config.php
/** @var string $APP_Admin_User_Save_Error */

if (!isset($SEC_check_config) OR $SEC_check_config != 'APP_Admin_User_Missing') exit;
?>

<form name="form_AdminUser" id="form_AdminUser" action="config.php" method="POST">

	<div id="middle-wizard">
		<div class="alert alert-danger" role="alert">
			<h3 style="margin:0;">Admin User Missing</h3>
		</div>

		<div class="row">

			<div class="col-md-3">
			</div>

			<div class="col-md-6">

				<fieldset id="fs-AdminUser" class="coolfieldset fieldset3">
					<legend>&nbsp; Admin User &nbsp;</legend>

					<div style="text-align:left;">

						<?=get_HTML_Input( //key, value, type, label, sub_label, placeholder, disabled
							'Username', 
							'admin', 
							'text', 
							'Admin Username', 
							'', 
							'',
							true
						);?>
						
						<?=get_HTML_Input( //key, value, type, label, sub_label, placeholder
							'Admin_Password', 
							'', 
							'password', 
							'Admin Password', 
							'', 
							'Admin Password'
						);?>

						<?=get_HTML_Input( //key, value, type, label, sub_label, placeholder
							'Admin_Password_Confirm', 
							'', 
							'password', 
							'Admin Password Confirm', 
							'', 
							'Admin Password Confirm'
						);?>

						<?=get_HTML_Input( //key, value, type, label, sub_label, placeholder
							'Admin_Email', 
							'', 
							'email', 
							'Admin Email',
							'', 
							'email@domain.com'
						);?>

						<hr style="margin:0 -5px 10px; border-top:5px double #ccc;">

						<?=get_HTML_Select( //key, value, options_arr, label, sub_label, placeholder
							'Sports_Data', 
							$Sports_Data ?? 'en', 
							[['no', 'Do not import any data'], ['en', 'Sports Data in English'], ['de', 'Sports Data in German']], 
							'Import Sports Selection Dropdown Data', 
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

	<?php if ($APP_Admin_User_Save_Error != '') { ?>
		<div class="alert alert-danger" role="alert">
			<?=$APP_Admin_User_Save_Error;?>
		</div>
	<?php } ?>

		<input type="hidden" name="config_type" value="APP_Admin_User_Save">

		<button type="submit" id="APP_Admin_User_Save" class="save" style="margin:5px;">Save Main Data &nbsp;&nbsp; </button>

	</div>

</form>

<script>
jQuery(document).ready(function() {

init_HTML_Radio_Check_Buttons__On_Off('Extra_Location_Groups_Users');
init_HTML_Radio_Check_Buttons__On_Off('Extra_SampleData');

//strong password validation method
$.validator.addMethod("strong_password", function (value, element) {
	return (/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])/.test(value));
}, LANG.USERS.PASSWORD_WEAK);

//validate form
$('form#form_AdminUser').validate({
	rules: {
		Admin_Password: {
			required: true,
			minlength: 8,
			strong_password: true
		},
		Admin_Password_Confirm: {
			required: true,
			equalTo: "#Admin_Password"
		},
		Admin_Email: {
			required: true,
			email: true
		}
	}
});

}); //end jQuery(document).ready
</script>