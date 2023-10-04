<?php // ajax Form Data Preview - a dummy page to open an iframe 
$PATH_2_ROOT = '../';
?>
<style>
.ajaxOverlay {z-index:1000; border:none; margin:0px; padding:0px; width:100%; height:100%; top:0px; left:0px; opacity:0.6; cursor:wait; position:fixed; background-color:rgb(0, 0, 0);}
.ajaxMessage {z-index:1011; position:fixed; padding:0px; margin:0px; width:30%; top:40%; left:35%; text-align:center; color:rgb(255, 255, 0); border:0px; cursor:wait; text-shadow:red 1px 1px; font-size:18px; background-color:transparent;} 
</style>
<div id="loading" class="ajaxOverlay">
	<div class="ajaxMessage"><img src="<?=$PATH_2_ROOT;?>img/ldg.gif"></div>
</div>