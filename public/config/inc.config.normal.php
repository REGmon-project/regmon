<?php // config - normal configuration page 

//from config.php
/** @var bool $CONFIG_Key_Missing */

if ((!isset($SEC_check_config)) AND $SEC_check != $CONFIG['SEC_Page_Secret']) exit; 
?>

<form name="form1" id="form_Config" action="config.php" method="POST">
	<div id="middle-wizard">

	<?php if ($CONFIG_Key_Missing) { ?>
		<div class="alert alert-danger" role="alert">
			<h3 style="margin:0;">CONFIG Missing</h3>
		</div>
	<?php } ?>

		<div class="row">
			<div class="col-md-4">

				<fieldset id="fs-General" class="coolfieldset fieldset3">
					<legend>&nbsp; General &nbsp;</legend>
					<div style="text-align:left;">
						<?=get_HTML_Input( //key, value, type, label, sub_label, placeholder
							'DOMAIN', 
							$CONFIG['DOMAIN'], 
							'text', 
							'Application Domain name', 
							'No effect in case of "localhost", it will be localhost.', 
							'',
							true
						);?>

						<?=get_HTML_Input( //key, value, type, label, sub_label, placeholder
							'REGmon_Folder', 
							$CONFIG['REGmon_Folder'], 
							'text', 
							'Application Folder', 
							'set REGmon_Folder if you installed in a directory other than DOCUMENT_ROOT', 
							'',
							true
						);?>

						<?=get_HTML_Radio_Check_Buttons__On_Off( //key, value, option_on, option_off, label, sub_label
							'Production_Mode', 
							($CONFIG['Production_Mode'] ? '1' : '0'), 
							'ON', 
							'OFF', 
							'Production Mode', 
							'Defines the error logging.<br>No effect in case of "localhost", it will be OFF.', 
						);?>

						<?=get_HTML_Select( //key, value, options_arr, label, sub_label, placeholder
							'HTTP', 
							$CONFIG['HTTP'], 
							['http://', 'https://'], 
							'use https:// or http://', 
							'No effect in case of "localhost", it will be http://', 
							''
						);?>

						<?=get_HTML_Radio_Check_Buttons__On_Off( //key, value, option_on, option_off, label, sub_label
							'Force_Redirect_To_HTTPS', 
							$CONFIG['Force_Redirect_To_HTTPS'], 
							'ON', 
							'OFF', 
							'Force redirect to HTTPS', 
							'Force a redirect to https:// if the request is from http://<br>No effect in case of "localhost", it will be OFF.'
						);?>
					</div>
				</fieldset>

				<fieldset id="fs-SEC" class="coolfieldset fieldset3">
					<legend>&nbsp; Secret encryption strings &nbsp;</legend>
					<div style="text-align:left;">
						<?=get_HTML_Radio_Check_Buttons__On_Off( //key, value, option_on, option_off, label, sub_label
							'SEC_Hash_IP', 
							$CONFIG['SEC_Hash_IP'], 
							'ON', 
							'OFF', 
							'SEC Hash IP', 
							'enable the use of user IP in user HASH string. If true and the user IP changes the user will be logged out'
						);?>

						<?=get_HTML_Textarea( //key, value, type, label, sub_label, placeholder
							'SEC_Page_Secret', 
							$CONFIG['SEC_Page_Secret'], 
							'textarea', 
							'SEC Page Secret', 
							'is used for securing pages that not required the validate.php script and from direct calls, mostly ajax. and inc. pages', 
							''
						);?>

						<?=get_HTML_Textarea( //key, value, type, label, sub_label, placeholder
							'SEC_Hash_Secret', 
							$CONFIG['SEC_Hash_Secret'], 
							'textarea', 
							'SEC Hash Secret', 
							'is used for hashing the user HASH cookie that is used to validate the user. If is changed then all users will be logged out', 
							''
						);?>

						<?=get_HTML_Textarea( //key, value, type, label, sub_label, placeholder
							'SEC_Encrypt_Secret', 
							$CONFIG['SEC_Encrypt_Secret'], 
							'textarea', 
							'SEC Encrypt Secret', 
							'is used for encrypting app strings', 
							''
						);?>
					</div>
				</fieldset>

			</div>
			<div class="col-md-4">

				<fieldset id="fs-Database" class="coolfieldset fieldset3">
					<legend>&nbsp; Database Settings &nbsp;</legend>
					<div class="text-muted" style="margin:0 0 5px; color:blue;">
						<i>Loaded from ".env" file</i>
					</div>
					<div style="text-align:left;">
						<?=get_Database_Fields(true);?>
					</div>
				</fieldset>

				<fieldset id="fs-LogLimiter" class="coolfieldset fieldset3">
					<legend>&nbsp; Login Limiter &nbsp;</legend>
					<div class="text-muted" style="margin:-5px 0 5px; font-size:85%;">
						limit brute force attacks
					</div>
					<div style="text-align:left;">
						<?=get_HTML_Input( //key, value, type, label, sub_label, placeholder
							'LogLimiter_Max_Attempts', 
							$CONFIG['LogLimiter']['Max_Attempts'], 
							'number', 
							'Max Attempts', 
							'Number of Max attempts before blocking - def:5', 
							'5'
						);?>

						<?=get_HTML_Input( //key, value, type, label, sub_label, placeholder
							'LogLimiter_Block_Minutes', 
							$CONFIG['LogLimiter']['Block_Minutes'], 
							'number', 
							'Block Minutes', 
							'Time of blocking (in minutes) - def:10', 
							'10'
						);?>

						<hr style="margin:0 -5px 20px; border-top:7px double #ccc;">

						<?=get_HTML_Radio_Check_Buttons__On_Off( //key, value, option_on, option_off, label, sub_label
							'Use_VisualCaptcha', 
							$CONFIG['Use_VisualCaptcha'], 
							'ON', 
							'OFF', 
							'Use VisualCaptcha', 
							'Limit brute force attacks. <br><i>Apache Rewrite Module should be Enabled</i>.'
						);?>
					</div>
				</fieldset>

			</div>
			<div class="col-md-4">

				<fieldset id="fs-EMAIL" class="coolfieldset fieldset3">
					<legend>&nbsp; Email Configuration &nbsp;</legend>
					<div style="text-align:left;">
						<?=get_HTML_Input( //key, value, type, label, sub_label, placeholder
							'EMAIL_Host',
							$CONFIG['EMAIL']['Host'], 
							'text', 
							'SMTP Host' , 
							'sets the SMTP server.', 
							'mail.domain.com'
						);?>

						<?=get_HTML_Select( //key, value, options_arr, label, sub_label, placeholder
							'EMAIL_Port',
							$CONFIG['EMAIL']['Port'], 
							[25, 465, 587], 
							'SMTP Port', 
							'no secure(25) - ssl(465) - tls(587)', 
							''
						);?>

						<?=get_HTML_Input( //key, value, type, label, sub_label, placeholder
							'EMAIL_Username', 
							$CONFIG['EMAIL']['Username'], 
							'text', 
							'SMTP Username' , 
							'', 
							'email@domain.com'
						);?>

						<?=get_HTML_Input( //key, value, type, label, sub_label, placeholder
							'EMAIL_Password', 
							Decrypt_String($CONFIG['EMAIL']['Password']).'', 
							'password', 
							'SMTP Password', 
							'', 
							'Password'
						);?>

						<?=get_HTML_Input( //key, value, type, label, sub_label, placeholder
							'EMAIL_From_Name', 
							$CONFIG['EMAIL']['From_Name'], 
							'text', 
							'From (Name)', 
							'', 
							'App Name'
						);?>

						<?=get_HTML_Input( //key, value, type, label, sub_label, placeholder
							'EMAIL_From_Email', 
							$CONFIG['EMAIL']['From_Email'], 
							'text', 
							'From (Email)',
							'', 
							'email@domain.com'
						);?>

						<?=get_HTML_Input( //key, value, type, label, sub_label, placeholder
							'EMAIL_Reply_Name', 
							$CONFIG['EMAIL']['Reply_Name'], 
							'text', 
							'Reply To (Name)', 
							'', 
							'App Name'
						);?>

						<?=get_HTML_Input( //key, value, type, label, sub_label, placeholder
							'EMAIL_Reply_Email', 
							$CONFIG['EMAIL']['Reply_Email'], 
							'text', 
							'Reply To (Email)', 
							'', 
							'email@domain.com'
						);?>
					</div>

					<hr style="margin:0 -5px 10px; border-top:5px double #ccc;">
					
					<div class="form-group form-inline">
					<?php if (!isset($USER) AND !isset($ADMIN)) { //only on problem ?>
						<div style="color:blue;"><i>You can test Email Configuration after login.</i></div>
					<?php } else { //normal page ?>
						<button id="Config_Test_Email" class="btn btn-success"><i class="fa fa-envelope-o"></i>&nbsp; Test-Email</button>
						<input type="email" class="form-control" id="Your_Test_Email" value="<?=($USER['email'] ?? '');?>" placeholder="Your Email" style="float:right; width:250px;">
					<?php } ?>
					</div>

					<hr style="margin:0 -5px 20px; border-top:7px double #ccc;">


					<div style="text-align:left;">
						<?=get_HTML_Input( //key, value, type, label, sub_label, placeholder
							'EMAIL_Support', 
							$CONFIG['EMAIL']['Support'], 
							'text', 
							'Application Support Email' , 
							'Only for information', 
							''
						);?>
					</div>
				</fieldset>
				
				<fieldset id="fs-Language" class="coolfieldset fieldset3">
					<legend>&nbsp; Language &nbsp;</legend>
					<div style="text-align:left;">
						<?=get_HTML_Radio_Check_Buttons__On_Off( //key, value, option_on, option_off, label, sub_label
							'Use_Multi_Language_Selector', 
							$CONFIG['Use_Multi_Language_Selector'], 
							'ON', 
							'OFF', 
							'Use Multi Language Selector', 
							'Enable/Disable the language selection Dropdown. &nbsp; <i class="fa fa-info-circle" style="font-size:18px; vertical-align:middle;" title="The interface can be translated in multiple languages, '."\n".'but not the content (locations, groups, categories, forms, dropdowns, sports, tags).'."\n".'This will need a lot of work from users that need to deliver the same content for every available language."></i>'
						);?>

						<?=get_HTML_Select( //key, value, options_arr, label, sub_label, placeholder
							'Default_Language', 
							$CONFIG['Default_Language'], 
							['en', 'de'], 
							'Default Language', 
							'Set the default language in case you also set the Use_Multi_Language_Selector = OFF<br>This will lock the Default_Language so cannot be changed from url or cookie', 
							''
						);?>
					</div>
				</fieldset>
		
			</div>
		</div>
		
	</div>
	<div id="bottom-wizard">
	<?php if ($CONFIG_Key_Missing) { //only on problem ?>
		<input type="hidden" name="config_type" value="CONFIG_Key_Init_Save">
	<?php } else { ?>
		<input type="hidden" name="config_type" value="CONFIG_Key_Normal_Save">
	<?php } ?>
		<button type="submit" id="CONFIG_Key_Init_Save" class="save" style="margin:5px;">Save Configuration  &nbsp; </button>
	</div>
</form>

<script>
jQuery(document).ready(function() {

//init
init_HTML_Radio_Check_Buttons__On_Off('Production_Mode');
init_HTML_Radio_Check_Buttons__On_Off('Force_Redirect_To_HTTPS');
init_HTML_Radio_Check_Buttons__On_Off('Use_Multi_Language_Selector');
init_HTML_Radio_Check_Buttons__On_Off('DB_Debug');
init_HTML_Radio_Check_Buttons__On_Off('Use_VisualCaptcha');
init_HTML_Radio_Check_Buttons__On_Off('SEC_Hash_IP');


//check Email
$('#Config_Test_Email').on("click", function (e) {
	const valid_Email_Regex = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;
	const Your_Test_Email = $('#Your_Test_Email').val();
	
	if (Your_Test_Email != '' && valid_Email_Regex.test(Your_Test_Email)) {
		$("#loading").show();
		const post_data = get_Email_Config();
		$.ajax({
			type: "POST",
			url: 'config/ajax.config.php?oper=Test_Email_Config',
			data: post_data,
			success:function(data, result) {
				$("#loading").hide();
				if (data == 'OK') {
					swal('Success!', 'E-Mail sent!', 'success');
				}
				else {
					swal('Error!', data, 'error');
				}
			}
		});
	}

	return false;
});


//validate form
$('form#form_Config').validate();

}); //end jQuery(document).ready


function get_Email_Config() {
	const EMAIL = {
		Host		: $('#EMAIL_Host').val(),
		Port		: $('#EMAIL_Port').val(), //select field
		Username	: $('#EMAIL_Username').val(),
		Password	: $('#EMAIL_Password').val(),
		From_Name	: $('#EMAIL_From_Name').val(),
		From_Email	: $('#EMAIL_From_Email').val(),
		Reply_Name	: $('#EMAIL_Reply_Name').val(),
		Reply_Email	: $('#EMAIL_Reply_Email').val(),
		Support		: $('#EMAIL_Support').val(),
		Your_Test_Email	: $('#Your_Test_Email').val()
	};
	//console.log(EMAIL);

	return EMAIL;
}
</script>