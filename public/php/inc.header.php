<?php // inc Header

if ($SEC_check != $CONFIG['SEC_Page_Secret']) exit;

if (!isset($PATH_2_ROOT)) $PATH_2_ROOT = '';
?>

	<header id="header-logo">
		<div class="container-logo">
			<table style="width:100%; border-spacing:0;"><tr>
				<td style="text-align:left;"><img src="<?=$PATH_2_ROOT;?>img/REGmon_Logo_<?=$LANG->language;?>.png" width="60%" alt="REGmon"></td>
				<td style="text-align:right;"><img src="<?=$PATH_2_ROOT;?>img/REGman_Foto_Header.jpg" alt="REGmon"></td>
			</tr></table>
		</div>
	</header>