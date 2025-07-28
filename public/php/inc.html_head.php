<?php // inc Head 

/** @var string $title */

if ((!isset($SEC_check_config)) AND $SEC_check != $CONFIG['SEC_Page_Secret']) exit;

if (!isset($PATH_2_ROOT)) $PATH_2_ROOT = '';
?>
<!DOCTYPE html>
<html lang="<?=$LANG->LANG_CURRENT;?>">
<head>
<meta charset="utf-8" />
<META HTTP-EQUIV="content-type" CONTENT="text/html; charset=utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"> 
<meta name="viewport" content="width=device-width<?php /*, initial-scale=1.0*/?>">
<meta name="description" content="" />
<meta name="keywords" content="" />
<meta name="author" content="MAD" />
<title><?=$title;?></title>

<link rel="shortcut icon" href="<?=$PATH_2_ROOT;?>favicon.ico" />

<link type="text/css" rel="stylesheet" href="<?=$PATH_2_ROOT;?>node_modules/bootstrap/dist/css/bootstrap.min.css" />
<link type="text/css" rel="stylesheet" href="<?=$PATH_2_ROOT;?>node_modules/font-awesome/css/font-awesome.min.css" />
<link rel="stylesheet" type="text/css" href="<?=$PATH_2_ROOT;?>node_modules/@fontsource/open-sans/latin.css" />
<link rel="stylesheet" type="text/css" href="<?=$PATH_2_ROOT;?>node_modules/@fontsource/lato/latin.css" />
<?php if (!isset($no_forms_css)) { ?>
<link type="text/css" rel="stylesheet" href="<?=$PATH_2_ROOT;?>forms/css/forms.css<?=$G_VER;?>" />
<?php } ?>
<link type="text/css" rel="stylesheet" href="<?=$PATH_2_ROOT;?>css/wizard.css<?=$G_VER;?>" />
<link type="text/css" rel="stylesheet" href="<?=$PATH_2_ROOT;?>css/buttons.css<?=$G_VER;?>" />

<script type="text/javascript" src="<?=$PATH_2_ROOT;?>node_modules/jquery/dist/jquery.min.js"></script>
<script type="text/javascript" src="<?=$PATH_2_ROOT;?>node_modules/jquery-ui/dist/jquery-ui.min.js"></script>

<script type="text/javascript" src="<?=$PATH_2_ROOT;?>js/modernizr.custom.js"></script><!-- HTML5 and CSS3-in older browsers-->
