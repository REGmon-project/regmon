<?php // inc Notes 

if ($SEC_check != $CONFIG['SEC_Page_Secret']) exit;
?>

	<div style="display:none;">
		<form id="create_Note" role="form">
		
			<div style="text-align:center; line-height:12px; margin-top:10px;">
				<label>	<?=$LANG->NOTE_ALL_DAY;?>:&nbsp;&nbsp;<input type="checkbox" id="isAllDay"></label>
			</div>		
		
			<div id="note_date_div" class="form-group" style="text-align:center;">
				<label id="note_date_start_label" for="note_date_start" style="float:left; font-weight:normal;"><?=$LANG->FROM;?></label>
				<span style="font-weight:700;"><?=$LANG->NOTE_DATE;?>&nbsp;&nbsp;&nbsp;&nbsp;</span>
				<label id="note_date_end_label" for="note_date_end" style="float:right; font-weight:normal;"><?=$LANG->TO;?></label>
				<div style="clear:both;"></div>
				<div class="input-group date" id="datetimepicker_note_start" style="float:left;">
					<input id="note_date_start" type="text" class="form-control textfield required" value="" style="width:100px; height:28px; padding:4px 12px;">
					<span class="input-group-addon" style="width:25px; height:28px; display:inline-block; padding:5px;"><span class="fa fa-calendar"></span></span>
				</div>
				<div class="input-group date" id="datetimepicker_note_end" style="float:right;">
					<input id="note_date_end" type="text" class="form-control textfield required" value="" style="width:100px; height:28px; padding:4px 12px;">
					<span class="input-group-addon" style="width:25px; height:28px; display:inline-block; padding:5px;"><span class="fa fa-calendar"></span></span>
				</div>
				<div style="clear:both;"></div>
			</div>

			<div id="note_time_div" class="form-group" style="text-align:center; position:relative; display:none;">
				<label id="note_time_start_label" for="note_time_start" style="float:left; font-weight:normal;"><?=$LANG->NOTE_HOUR_FROM;?></label>
				<span style="font-weight:700;"><?=$LANG->NOTE_HOUR;?>&nbsp;&nbsp;&nbsp;&nbsp;</span>
				<span id="note_time_now" class="btn btn-default btn-sm input-group-addon" style="width:50px; display:inline-block; padding:2px; height:20px; margin-left:10px; margin-right:-60px;">&nbsp;<?=$LANG->NOTE_HOUR_NOW;?></span>
				<label id="note_time_end_label" for="note_time_end" style="float:right; font-weight:normal;"><?=$LANG->NOTE_HOUR_TO;?></label>
				<div style="clear:both;"></div>
				<div class="input-group clockpicker time" id="clockpicker_note_time_start" style="display:inline-block;" data-placement="bottom" data-align="left" data-default="now">
					<span class="input-group-addon" style="width:25px; height:28px; padding:3px; float:left;"><span class="fa fa-clock-o" style="font-size:17px;"></span></span>
					<input id="note_time_start" name="note_time_start" type="text" class="form-control textfield time required" style="width:80px; height:28px; padding:4px 12px; border-top-left-radius:0; border-bottom-left-radius:0;" value="" title="<?=$LANG->NOTE_HOUR_FROM;?>">
					<span class="input-group-addon" style="width:50px; height:28px; display:inline-block; padding:5px 0; border-top-right-radius:0; border-bottom-right-radius:0;"><span class="fa fa-long-arrow-right"></span></span>
				</div>
				<div class="input-group clockpicker time" id="clockpicker_note_time_end" style="display:inline-block;" data-placement="bottom" data-align="right" data-default="now">
					<input id="note_time_end" name="note_time_end" type="text" class="form-control textfield time required" style="width:80px; height:28px; padding:4px 12px; border-top-left-radius:0; border-bottom-left-radius:0; margin-left:-4px;" value="" title="<?=$LANG->NOTE_HOUR_TO;?>">
					<span class="input-group-addon" style="width:25px; height:28px; display:inline-block; padding:3px 0;"><span class="fa fa-clock-o fa-flip-horizontal" style="font-size:17px;"></span></span>
				</div>
			</div>
			
			<div class="form-group" style="text-align:center;">
				<label for="note_title"><?=$LANG->NOTE_TITLE;?></label>
				<div style="position:relative;"><input id="note_title" type="text" class="form-control textfield required" value=""></div>
			</div>
			<div class="form-group" style="text-align:center;">
				<label for="note_text"><?=$LANG->NOTE_TEXT;?></label>
				<textarea id="note_text" class="form-control" style="min-height:80px;"></textarea>
			</div>

			<div style="text-align:center; line-height:12px; margin-top:10px;">
				<label>	<?=$LANG->NOTE_IN_GRAPHS;?>:&nbsp;&nbsp;<input type="checkbox" id="showInGraph"></label>
			</div>		
			<div id="note_color_div" class="form-group" style="text-align:center;">
				<label for="note_color"><?=$LANG->NOTE_COLOR;?> :&nbsp;</label>
				<input id="note_color" class="form-control cpC" type="text" value="rgba(238,238,238,0.5)" style="display:inline-block; width:220px; color:white; text-shadow:black 1px 1px;"/>
			</div>
			<br>
			<div class="clearfix"></div>
			<div id="note_error" style="text-align:center; color:red; margin:-15px 0 4px 0;" style="display:none;"><?=$LANG->NOTE_DAY_MAX_3;?></div>
			<div style="text-align:center;">
				<button id="note_save" type="button" class="save" style="margin:5px;"><?=$LANG->SAVE;?> &nbsp; </button>
			</div>
			<br>
		</form>
	</div>
