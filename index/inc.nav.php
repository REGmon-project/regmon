<?php // inc nav 

if ($SEC_check != $CONFIG['SEC_Page_Secret']) exit;
?>

	<nav id="nav-header" class="navbar navbar-custom">
		<div id="nav-header-container" class="container-fluid navbar-container">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<span id="dashboard_link2" class="fa fa-th"></span>				
			</div>
			<div id="navbar" class="navbar-collapse collapse">
				<ul class="nav navbar-nav navbar-right">
					<?php /* TODO: offline <li><a href="offline/" class="nav_offline" style="color:#ddd;" target="_blank"><i class="fa fa-wifi" style="font-size:14px; color:#ddd;"></i> &nbsp; Offline</a></li>@@@@@@@@@@@@@ offline disabled for now*/?>
					
				<?php if ($ADMIN) { ?>
					<li><a href="administration.php" class="nav_link nav_admin"><i class="fa fa-cogs"></i> &nbsp; <?=$LANG->BUTTON_ADMINISTRATION;?></a></li>
					
					<li><a href="config.php" class="nav_link"><i class="fa fa-cog" style="font-size:17px;"></i></a></li>
				<?php } ?>

					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bar-chart"></i> &nbsp; <?=$LANG->BUTTON_RESULTS;?> &nbsp; <b class="caret"></b></a>
						<ul class="dropdown-menu" style="padding:1px;">
							<li><a href="forms_results.php" class="nav_link nav_results_group" style="font-size:15px;"><i class="fa fa-bar-chart"></i> &nbsp;<i class="fa fa-user"></i>&nbsp; &nbsp; <?=$LANG->BUTTON_FORMS_RESULTS;?></a></li>
							<li><a href="results.php" class="nav_link nav_results_group" style="font-size:15px;"><i class="fa fa-bar-chart"></i> <i class="fa fa-users"></i> &nbsp; <?=$LANG->BUTTON_RESULTS;?></a></li>
						</ul>
					</li>
			
				<?php if ($CONFIG['Use_Multi_Language_Selector']) { ?>
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" style="padding-left:15px; padding-right:10px;"><img src="img/flags/<?=$LANG->LANG_CURRENT;?>.png" title="" style="margin:-10px 0 -5px 0;" />&nbsp;&nbsp;<span class="caret"></span></a>
						<ul class="dropdown-menu" style="padding:1px; min-width:120px;">
							<li <?=($LANG->LANG_CURRENT=='en'?' class="active"':'');?>><a href="javascript:void(0)" id="lang_en"><img src="img/flags/en.png" /> &nbsp;<?=$LANG->LANG_ENGLISH;?></a></li>
							<li <?=($LANG->LANG_CURRENT=='de'?' class="active"':'');?>><a href="javascript:void(0)" id="lang_de"><img src="img/flags/de.png" /> &nbsp;<?=$LANG->LANG_GERMAN;?></a></li>
						</ul>
					</li>
				<?php } ?>
					
					<li><a href="index/ajax.page.regmon_info.php" class="nav_link nav_profile fancybox fancybox.ajax"><i class="fa fa-info-circle" style="font-size:17px;"></i></a></li>

					<li><a href="login/ajax.page.profile_edit.php" class="nav_link nav_profile fancybox fancybox.ajax"><i class="fa fa-user"></i> &nbsp; <?=$LANG->BUTTON_USER_ACCOUNT;?></a></li>
					
					<li><a href="login/logout.php" class="nav_link nav_logout"><i class="fa fa-lock"></i> &nbsp; <?=$LANG->BUTTON_LOGOUT;?></a></li>
				</ul>
			</div>
		</div>
	</nav>
