<?php // inc Nav Lang 

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
			</div>
			<div id="navbar" class="navbar-collapse collapse">
				<ul class="nav navbar-nav navbar-right">
	<?php if ($CONFIG['Use_Multi_Language_Selector']) { ?>
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" style="padding-left:15px; padding-right:10px;"><img src="img/flags/<?=$LANG->LANG_CURRENT;?>.png" title="" style="margin:-10px 0 -5px 0;" />&nbsp;&nbsp;<span class="caret"></span></a>
						<ul class="dropdown-menu" style="padding:1px; min-width:120px;">
							<li <?=($LANG->LANG_CURRENT=='en'?' class="active"':'');?>><a href="javascript:void(0)" id="lang_en"><img src="img/flags/en.png" /> &nbsp;<?=$LANG->LANG_ENGLISH;?></a></li>
							<li <?=($LANG->LANG_CURRENT=='de'?' class="active"':'');?>><a href="javascript:void(0)" id="lang_de"><img src="img/flags/de.png" /> &nbsp;<?=$LANG->LANG_GERMAN;?></a></li>
						</ul>
					</li>
	<?php } ?>
				</ul>
			</div>
		</div>
	</nav>
