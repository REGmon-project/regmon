<?php 
$PATH_2_ROOT = '../';
require_once($PATH_2_ROOT.'_settings.regmon.php');
require($PATH_2_ROOT.'login/validate.php');
?>
<script type="text/javascript" src="js/grid.sports.js<?=$G_VER;?>"></script>
<script type="text/javascript" src="js/grid.dropdowns.js<?=$G_VER;?>"></script>
<table id="dropdowns" alt="<?=$LANG->DROPDOWNS;?>"></table>
<div id="Dpager"></div>
<br>
<table id="sports" alt="<?=$LANG->SPORTS;?>"></table>
<div id="SPpager"></div>
