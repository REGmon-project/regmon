<?php
declare(strict_types=1);

//from config.php
/** @var string $ENV_File */
/** @var string $DB_Migration_File_Selected */

//echo '<pre>'; print_r($_SERVER);

$Config_Normal_Page = false;

$ENV_File_Missing = false;
$ENV_File_Missing_Error = '';

$APP_Database_Empty = false;
$DB_Migrations_Directory = __DIR__. '/extra/';
$DB_Migration_File_Error = '';

$APP_Admin_User_Missing = false;
$APP_Admin_User_Save_Error = '';

$CONFIG_Key_Missing = false;

$APP_Database_Tables_Missing = false;

$Config_Test_Database_Error = '';
$Config_Test_Database_Success = '';
$ENV_File_Content = '';

if (isset($SEC_check_config)) {
	//we have problems to solve --not a normal config page

	//secure ajax sub pages from direct call
	$SEC_check = $SEC_check_config;
	
	require_once('login/inc.login_functions.php');

	require('config/inc.config.functions.php');

	$REGmon_Folder = get_CONFIG__REGmon_Folder();

	//Load languages
	require_once(__DIR__.'/php/class.language.php');
	//we not have $CONFIG here so we use 'en' as standard
	//else we need to provide the language selection button 
	//and the language keys and translations in the lang_.php files 
	$LANG = Language::getInstance($REGmon_Folder, 'en', false);




	//initial page load and load after action ################################
	if ($SEC_check_config == 'ENV_File_Missing') {
		$ENV_File_Missing = true;

		$DB_CONFIG_Default = [
			'DB_Host' => 'localhost',
			'DB_Name' => 'regmondb',
			'DB_User' => 'dbuser',
			'DB_Pass' => 'root',
		];
		
		//overwrites
		if (is_Docker()) {
			$DB_CONFIG_Default['DB_Host'] = 'db';
		}
		elseif (is_XAMPP()) {
			$DB_CONFIG_Default['DB_User'] = 'root';
			$DB_CONFIG_Default['DB_Pass'] = '';
		}

		//set post values back to form values
		$DB_CONFIG = [
			'DB_Host' => $_POST['DB_Host'] ?? $DB_CONFIG_Default['DB_Host'],
			'DB_Name' => $_POST['DB_Name'] ?? $DB_CONFIG_Default['DB_Name'],
			'DB_User' => $_POST['DB_User'] ?? $DB_CONFIG_Default['DB_User'],
			'DB_Pass' => $_POST['DB_Pass'] ?? $DB_CONFIG_Default['DB_Pass']
		];

		$ENV_File_Content_default = '' .
			'# DB settings' . "\n" .
			'DB_Host=' . $DB_CONFIG['DB_Host'] . "\n".
			'DB_Name=' . $DB_CONFIG['DB_Name'] . "\n".
			'DB_User=' . $DB_CONFIG['DB_User'] . "\n".
			'DB_Pass=' . $DB_CONFIG['DB_Pass'] . "\n";
		
			/* no point -we need this before we can come here
			* so I put it in docker-compose.yml file
			if (is_Docker()) {
				$ENV_File_Content_default .= '' .
					"\n" .
					'# MYSQL settings -- Used only from Docker to set the ROOT MYSQL password.' . "\n" .
					'MYSQL_ROOT_PASSWORD=root' . "\n";
				
			}*/
			
		$ENV_File_Content = $_POST['ENV_File_Content'] ?? $ENV_File_Content_default;
	}

	elseif ($SEC_check_config == 'APP_Database_Empty') {
		$APP_Database_Empty = true;

		//set post values back to form
		$DB_Migration_File_Selected = $_POST['DB_Migration_File'] ?? '';

		$DB_Migrations_Files_arr = get_DB_Migrations_Files($DB_Migrations_Directory);
	}

	elseif ($SEC_check_config == 'APP_Admin_User_Missing') {
		$APP_Admin_User_Missing = true;
	}
	
	elseif ($SEC_check_config == 'CONFIG_Key_Missing') {
		$CONFIG_Key_Missing = true;

		$CONFIG_Default = get_CONFIG_Defaults_array();
		
		if (empty($CONFIG)) {
			$CONFIG = $CONFIG_Default;
		}

		// $CONFIG = $CONFIG ?? $CONFIG_Default;
	}
	
	//never comes here --this need work
	elseif ($SEC_check_config == 'APP_Database_Tables_Missing') {
		$APP_Database_Tables_Missing = true;
		exit;
	}


	//page load after action ##########################################
	if (isset($_POST['config_type'])) {

		if ($_POST['config_type'] == 'Config_Test_Database') {

			// Set MySQLi to throw exceptions 
			mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
			$conn = false;
			
			try {
				//not select Database
				$conn = mysqli_connect($_POST['DB_Host'], $_POST['DB_User'], $_POST['DB_Pass']/*, $_POST['DB_Name']*/);
			}
			catch( mysqli_sql_exception $e ) {
				$Config_Test_Database_Error = 'DB Connection : Fail with Error: ' . $e->getCode() . " | " . $e->getMessage();
			}
	
	
			//conn to DB ok (Host, User, Pass) --> lets check if DB exist
			if ($conn) {
				mysqli_query($conn, "SET NAMES 'UTF8'");
				$DB_Name = mysqli_real_escape_string($conn, $_POST['DB_Name']); 
				$DB_exist_sql = mysqli_query($conn, "SHOW DATABASES LIKE '".$DB_Name."'");
				$DB_exist = false;
				if ($DB_exist_sql instanceof mysqli_result) {
					$DB_exist = mysqli_fetch_array($DB_exist_sql);
				}

				if (is_array($DB_exist)) { //exist
					$Config_Test_Database_Success = 'DB Connection : Success<br>Database "'.$DB_Name.'" Exist';
				}
				else { //not exist
					$Config_Test_Database_Error = 'Database "'.$DB_Name.'" does not exist';
				}
			}
		}

		elseif ($_POST['config_type'] == 'Config_ENV_File_Save') {
			//ENV file Save
			if (isset($_POST['ENV_File_Content'])) {
				$ENV_File_Content = $_POST['ENV_File_Content'];
	
				file_put_contents('.env', $ENV_File_Content);
				if (is_file($ENV_File)) {
					reload_Config_Page();
				}
				else {
					//display error message
					$ENV_File_Missing_Error = 'Error! ".env" file cannot be created';
				}
			}
		}

		elseif ($_POST['config_type'] == 'Config_APP_Database_Init') {
			//get db migration file contents
			$DB_Migration_File = $DB_Migrations_Directory . $DB_Migration_File_Selected;
			$DB_Migration_File_content = file_get_contents($DB_Migration_File);

			if ($DB_Migration_File_content) {
				//Set MySQLi to throw exceptions 
				mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
				
				$db_error = false;
				try {
					//supposed we have a db connection
					$conn = mysqli_connect($DB_CONFIG['DB_Host'], $DB_CONFIG['DB_User'], $DB_CONFIG['DB_Pass'], $DB_CONFIG['DB_Name']);
					if ($conn) {
						mysqli_query($conn, "SET NAMES 'UTF8'");
						//execute multi query
						mysqli_multi_query($conn, $DB_Migration_File_content);
					}
				}
				catch( mysqli_sql_exception $e ) {
					$db_error = true;
					$DB_Migration_File_Error = 'DB Migration : Fail with Error: ' . $e->getCode() . " | " . $e->getMessage();
				}

				if (!$db_error) {
					sleep(1); //give a second to finish
					reload_Config_Page();	
				}
			}
		}

		elseif ($_POST['config_type'] == 'APP_Admin_User_Save') 
		{
			if ($_POST['Admin_Password'] != '' AND 
				$_POST['Admin_Password'] = $_POST['Admin_Password_Confirm'] AND 
				$_POST['Admin_Email'] != '') 
			{
				$password = $_POST['Admin_Password'];
				$email = $_POST['Admin_Email'];
				$Sports_Data = $_POST['Sports_Data'];
				$datetime = date('Y-m-d H:i:s');
				$extra_data = true;
				
				$file_path = "extra/migration/add_sample_data.sql";
				$sql = file_get_contents($file_path);
				if ($sql === false) {
					$APP_Admin_User_Save_Error = 'Admin_User_Save : Fail with Error: ' . 'File not found: ' . $file_path;
					$sql = "";
				}
				
				$sql = str_replace("@PASSWORD@", hash_Password($password), $sql);
				$sql = str_replace("@EMAIL@", $email, $sql);
				
				//sports Data
				if ($Sports_Data == 'en') {
					$sql .= "\n".file_get_contents("extra/migration/add_sports_en.sql");
				}
				elseif ($Sports_Data == 'de') {
					$sql .= "\n".file_get_contents("extra/migration/add_sports_de.sql"); 
				}
				
				//echo '<pre>'.$Main_Data_sql; exit;
				

				//Set MySQLi to throw exceptions 
				mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
				
				$db_error = false;
				$conn = mysqli_connect($DB_CONFIG['DB_Host'], $DB_CONFIG['DB_User'], $DB_CONFIG['DB_Pass'], $DB_CONFIG['DB_Name']);

				try {
					if ($conn) {
						mysqli_query($conn, "SET NAMES 'UTF8'");

						//execute multi query
						if (mysqli_multi_query($conn, $sql)) {
							do {
								//store first result set
								if ($result = mysqli_store_result($conn)) {
									mysqli_free_result($result);
								}
							} while (mysqli_next_result($conn));
							
						}
					}
				}
				catch( mysqli_sql_exception $e ) {
					$db_error = true;
					$APP_Admin_User_Save_Error = 'Admin_User_Save : Fail with Error: ' . $e->getCode() . " | " . $e->getMessage();
				}

				if (!$db_error) {
					reload_Config_Page();	
				}
			}
		}

		elseif ($_POST['config_type'] == 'CONFIG_Key_Init_Save') {
			$Post_CONFIG_array = get_CONFIG_Defaults_array($_POST);

			if (Save_Configuration($Post_CONFIG_array, true) == 'OK') {
				reload_Config_Page();
			}
		}
	}
}
else { 
	//normal config page
	require_once('_settings.regmon.php');
	require('login/validate.php');

	require('config/inc.config.functions.php');


	if (isset($_POST['config_type']) AND $_POST['config_type'] == 'CONFIG_Key_Normal_Save') {

		$Post_CONFIG_array = get_CONFIG_Defaults_array($_POST);
	
		if (Save_Configuration($Post_CONFIG_array, true) == 'OK') {
			reload_Config_Page();
		}
	}


	$Config_Normal_Page = false;
}


//#####################################################################################
$title = $LANG->CONFIG_PAGE_TITLE;
$no_forms_css = true;
require('php/inc.html_head.php');
//#####################################################################################
?>
<?php /*<!-- Jquery -->*/?>
<link type="text/css" rel="stylesheet" href="node_modules/jquery-ui/dist/themes/smoothness/jquery-ui.min.css">

<!-- OTHER JS --> 
<link type="text/css" rel="stylesheet" href="node_modules/bootstrap/dist/css/bootstrap-theme.min.css">
<script type="text/javascript" src="node_modules/bootstrap/dist/js/bootstrap.min.js"></script>

<script type="text/javascript" src="node_modules/@kflorence/jquery-wizard/src/jquery.wizard.js"></script>
<script type="text/javascript" src="node_modules/jquery-validation/dist/jquery.validate.js"></script>
<?php if ($LANG->LANG_CURRENT != 'en') { ?>
<script type="text/javascript" src="js/overrides/query-validation/messages_<?=$LANG->LANG_CURRENT;?>.min.js"></script>
<?php } ?>

<link rel="stylesheet" type="text/css" href="node_modules/sweetalert2/dist/sweetalert2.min.css">
<script type="text/javascript" src="node_modules/sweetalert2/dist/sweetalert2.min.js"></script>

<script type="text/javascript" src="js/lang_<?=$LANG->LANG_CURRENT;?>.js<?=$G_VER;?>"></script>
<script type="text/javascript" src="js/common.js<?=$G_VER;?>"></script>

<link rel="stylesheet" type="text/css" href="config/css/config.css<?=$G_VER;?>">
<script type="text/javascript" src="config/js/config.js<?=$G_VER;?>"></script>
</head>
<body>

	<div id="loading" class="ajaxOverlay" style="display:none">
		<div class="ajaxMessage"><img src="img/ldg.gif"></div>
	</div>

    <div class="container">
		<div class="row">
			<div class="col-md-12 main-title" style="text-align:center;">
				<button type="button" id="home" class="home"> &nbsp; <?=$LANG->HOMEPAGE;?></button>
				<h2 style="margin:20px;"><?=$LANG->CONFIG;?></h2>
			</div>
		</div>
	</div>


	<section style="margin:0 15px 5px; font-size:15px; text-align:center; background:#fff;">
		<div id="wizard_container">

<?php 
//#######################################
//ENV_File_Missing
if ($ENV_File_Missing) {

	require('config/inc.config.env_missing.php');

}

//#######################################
//APP_Database_Empty
elseif ($APP_Database_Empty) {

	require('config/inc.config.db_empty.php');

}

//#######################################
//APP_Admin_User_Missing
elseif ($APP_Admin_User_Missing) {

	require('config/inc.config.admin_user_missing.php');

}

//#######################################
//normal config page + CONFIG_Key_Missing
else {

	require('config/inc.config.normal.php');

}
?>

		</div>
	</section>

	<br>
	<br>

	<?php //require('php/inc.footer.php');?>

	<div id="toTop" title="<?=$LANG->PAGE_TOP;?>">&nbsp;</div>	
</body>
</html>