<?php // inc Permissions 

if ($SEC_check != $CONFIG['SEC_Page_Secret']) exit;

$GlobalView = '1';
$GroupView = '1';
$LocationView = '1';
$TrainerView = '1';
$Private = '0';
?>
<style>
.viewCheck { font-size:16px; color:#ddd; }
.viewCheck.checked { color:#20bc1a; }
.viewCheck.disabled { color:#ddd; }
.viewCheck.private { color:#ddd; }
.PrivateCheck { font-weight:normal; }
.PrivateCheck.checked { font-weight:bold; }
.perm_block { display:inline-block; white-space:nowrap; border-right:2px solid #ccc; height:22px; padding:0 12px 0 5px; }
</style>

<div id="select_permissions" style="font-weight:normal;">
	<b><?=$LANG->PERMISSIONS_VISIBLE_FOR;?>:</b>&nbsp; &nbsp; 
	<span class="perm_block">
		<?=$LANG->PERMISSIONS_GLOBAL;?> : 
		<label class="viewCheck">
			<input type="checkbox" id="GlobalView" style="vertical-align:top;"<?=($GlobalView=='1'?' checked':'');?>>
		</label>
	</span>
	<span class="perm_block">
		<?=$LANG->PERMISSIONS_LOCATION;?> : 
		<label class="viewCheck">
			<input type="checkbox" id="LocationView" style="vertical-align:top;"<?=($LocationView=='1'?' checked':'');?>>
		</label>
	</span>
	<span class="perm_block">
		<?=$LANG->PERMISSIONS_GROUP;?> : 
		<label class="viewCheck">
			<input type="checkbox" id="GroupView" style="vertical-align:top;"<?=($GroupView=='1'?' checked':'');?>>
		</label>
	</span>
	<span class="perm_block">
		<?=$LANG->PERMISSIONS_TRAINERS;?> : 
		<label class="viewCheck">
			<input type="checkbox" id="TrainerView" style="vertical-align:top;"<?=($TrainerView=='1'?' checked':'');?>>
		</label>
	</span>
	<span class="perm_block" style="border-right:0; width:60px;">
		<?=$LANG->PERMISSIONS_PRIVATE;?> : 
		<label class="PrivateCheck">
			<input type="checkbox" id="Private" style="vertical-align:top;"<?=($Private=='1'?' checked':'');?>>
		</label>
	</span>
</div>

<script>
function checkViewFrom(elem) {
	if($(elem).is(':checked')) {
		$(elem).parent().addClass('checked');
	} else {
		$(elem).parent().removeClass('checked');
	}
}
function checkPrivate(elem) {
	if($(elem).is(':checked')) {
		$(elem).parent().addClass('checked');
		$(Perms_elems).each(function(i,el){
			if ($(el).is(':checked')) {
				$(el).trigger("click"); //uncheck
			}
			$(el).prop('disabled', true).trigger('change');
			$(el).parent().addClass('private');
		});
	} else {
		$(elem).parent().removeClass('checked');
		$(Perms_elems).each(function(i,el){
			$(el).prop('disabled', false).trigger('change');
			$(el).parent().removeClass('private');
		});
	}
}

let Perms_elems = ['#GlobalView','#LocationView','#GroupView','#TrainerView'];
jQuery(function() {
	//template Save permissions
	$(Perms_elems).each(function(i,el){
		checkViewFrom(el); //init
		
		$(el).on('change', function(){ 
			let is_checked = $(this).is(':checked');
			if(el == '#TrainerView' && is_checked) {
				checkViewFrom(el);
			}
			else {
				$(Perms_elems).each(function(i2,el2){
					if(is_checked) {
						if(i2 > i) { //check after el -if unchecked
							if (!$(el2).is(':checked')) $(el2).trigger("click");
						}
					} else {
						if(i2 <= i) { //uncheck before el -if checked
							if ($(el2).is(':checked')) $(el2).trigger("click");
						} 
					}
				});
			}
			checkViewFrom(el);
		});
	});
	$('#Private').on('change', function(){ 
		checkPrivate('#Private');
	});
	checkPrivate('#Private');
});
</script>
