<?php // ajax box REGmon Info
$PATH_2_ROOT = '../';
require_once($PATH_2_ROOT.'_settings.regmon.php');

//for not call this as a single page but only from ajax --why not, only an info page
//if (!isset($_SERVER['HTTP_X_FANCYBOX']) AND !isset($_SERVER['HTTP_X_REQUESTED_WITH'])) exit;
?>
<style>
a.support_email {
    display: block;
    text-align: center;
	color: blue;
	font-size: 18px;
}
a.support_email:hover {
	color: black;
}
</style>
<div style="background:white;">
	<a href="https://www.regman.org/" rel="home" target="_blank">
		<img src="img/regman-logo1.jpg" style="width:100%;" alt="REGman.org">
	</a>
	<a href="mailto:<?=$CONFIG['EMAIL']['Support'];?>" class="support_email" target="_blank"><?=$CONFIG['EMAIL']['Support'];?></a>
	<div style="margin:30px 0;">
		<a href="http://www.spowiss.rub.de/" target="_blank" title="Ruhr-Universität Bochum -> http://www.spowiss.rub.de/"><img src="img/Ruhr-Uni-Bochum.png" alt="Ruhr-Universität Bochum -> http://www.spowiss.rub.de/" style="display:block; margin:auto;" <?php /*width="215" height="70"*/?>></a>
	</div>
	<div style="margin:30px 0;">
		<a href="https://www.theorie-praxis.sport.uni-mainz.de/" target="_blank" title="Instituts für Sportwissenschaft -> https://www.theorie-praxis.sport.uni-mainz.de/"><img alt="Instituts für Sportwissenschaft -> https://www.theorie-praxis.sport.uni-mainz.de/" src="img/Mainz.png" style="display:block; margin:auto;" <?php /*width="270" height="70"*/?>></a>
	</div>
	<div style="margin:30px 0;">
		<a href="http://sportmedizin-saarbruecken.de/" target="_blank" title="Institut für Sport- und Präventivmedizin -> http://sportmedizin-saarbruecken.de/"><img alt="Institut für Sport- und Präventivmedizin -> http://sportmedizin-saarbruecken.de/" src="img/Sportmedizin.png" style="display:block; margin:auto;" <?php /*width="249" height="70"*/?>></a>
	</div>
	<br><br>
</div>
