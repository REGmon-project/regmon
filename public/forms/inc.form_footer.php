<?php // inc Form Footer 

//from form.php
/** @var bool $VIEW */
/** @var bool $PREVIEW */
/** @var bool $EDIT */
/** @var int $pages_num_visible */

if ($SEC_check != $CONFIG['SEC_Page_Secret']) exit;
?>

	<div id="bottom-wizard">
<?php 
if (!$VIEW) { ?>

	<?php if ($PREVIEW) { ?>
		<h4 style="margin-bottom:20px; color:red;"><?=$LANG->FORM_PREVIEW_WARNING;?></h4>
	<?php } ?>

	<?php if (!$EDIT) { ?>
		<button type="button" name="backward" class="backward<?=($pages_num_visible<=1?' hidden':'');?>"> &nbsp; <?=$LANG->FORM_BACKWARD;?></button>
		<button type="button" name="forward" class="forward<?=($pages_num_visible<=1?' hidden':'');?>"><?=$LANG->FORM_FORWARD;?> &nbsp; </button>
		<button type="submit" class="saveSubmit"><?=$LANG->FORM_SAVE;?> &nbsp; </button> <?php //form data save ?>
		<div id="keep_form_open_div">
			<label id="keep_form_open_label" for="keep_form_open_ck" style="font-weight:600; margin:5px 0 0; color:#555;"> <?=$LANG->FORM_KEEP_AFTER_SAVE;?> : </label>
			<input type="checkbox" id="keep_form_open_ck" class="keep_form_open_ck" onchange="this.nextSibling.value=this.checked==true?1:0;"><?php /*need to be in the same line*/?><input type="hidden" id="keep_form_open" value="0">
		</div>
	<?php } else { // if !EDIT ?>

		<?php //if (!$HAVE_DATA) { ?>
		<button type="button" name="preview" class="preview"><?=$LANG->FORM_PREVIEW;?> &nbsp; </button>
		<button type="button" name="save" class="save"><?=$LANG->FORM_SAVE;?> &nbsp; </button>
		<?php //} ?>

	<?php } 
}
?>
	</div><?php // bottom-wizard end ?>

</div>
<?php /*######### End Form container wizard ################*/?>

	</section><?php // main container end ?>

<?php if (!$VIEW) { ?>
</form>
<?php } ?>

<div id="toTop" title="<?=$LANG->PAGE_TOP;?>">&nbsp;</div>
	
<div id="fancy_placeholder" style="height:5px; width:5px;"></div>

</body>
</html>