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
	<hr>
	<p style="margin:10px; font-size: 40px; text-align: center;">
		<b>REGmon</b>
	</p>
	<p style="margin: 10px; font-size: 25px; text-align:center;">
		<?=$LANG->INFO_PAGE_SUBTITLE?><br>
	</p>
	<hr>
	<!-- <p style="margin: 10px; font-size: 18px; text-align:center;">
		<?=$LANG->INFO_PAGE_INFO_REGMON1?><br>
	</p>
	<hr> -->
	<p style="margin: 10px; font-size: 18px; text-align:center;">
		<?=$LANG->INFO_PAGE_INFO_REGMON2?><br>
	</p>
	<a href="https://regmon-project.org/" class="support_email">
		https://regmon-project.org/<br>
	</a>
	<!-- <a href="https://github.com/REGmon-project/regmon/" class="support_email">
		https://github.com/REGmon-project/regmon/<br>
		(REGmon Open Source GitHub Repository)<br><br>
	</a> -->
	<!-- <a href="https://github.com/REGmon-project/regmon/" class="support_email">
		https://regmon-project.github.io/<br>
		(REGmon Open Source Project Documentation)<br>
	</a> -->
	<hr>
	<p style="margin: 10px; font-size: 18px; text-align:center;">
		<?=$LANG->INFO_PAGE_INFO_REGMAN?><br>
	</p>
	<!-- <a href="https://www.regman.org/" class="support_email">
		<?=$LANG->INFO_PAGE_INFO_WEBSITE?><br>
		https://www.regman.org/<br><br>
	</a> -->
	<a href="https://osf.io/uz4af/" class="support_email">
		<?=$LANG->INFO_PAGE_INFO_OSF?><br>	
		https://osf.io/uz4af/<br>
	</a>
	</a>
	<!-- <a href="https://www.bisp.de" class="support_email">
		<?=$LANG->INFO_PAGE_INFO_BISP?><br>	
		https://www.bisp.de<br>
	</a> -->
	<hr>
	<p style="margin: 10px; font-size: 18px; text-align:center;">
		<?=$LANG->INFO_PAGE_INFO_CEOS?><br>
	</p>
	<hr>
	<p style="margin: 10px; font-size: 18px; text-align:center;">
		<?=$LANG->INFO_PAGE_CONTACT?><br>
	</p>
	<a href="mailto:<?=$CONFIG['EMAIL']['Support'];?>" class="support_email" target="_blank">
		<?=$CONFIG['EMAIL']['Support'];?>
	</a>
	<hr>
	<p style="margin: 10px; font-size: 18px; text-align:center;">
		Version: <?=$G_Version;?>
	</p>
	<hr>
</div>