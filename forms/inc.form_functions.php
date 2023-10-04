<?php // Form Row Item Functions

if ($SEC_check != $CONFIG['SEC_Page_Secret']) exit;

function get_Form_Rows(mixed $rows):string {
	global $LANG, $EDIT, $G_ROW, $items_num;

	$Item_Types_that_Count_as_Items = array(
		"_Text", 
		"_Textarea", 
		"_Number", 
		"_Date", 
		"_Time", 
		"_Period", 
		"_Dropdown", 
		"_Dropdown_Select_Only", 
		"_RadioButtons", 
		"_Radio_Buttons_Select_Only"
	);
	
	//rows ######################################
	$html = '';
	foreach ((array)$rows as $row) {
		$G_ROW++;
		$rw = $G_ROW;

		if ($EDIT) {
			$html .= ''.
			'<li id="Row_'.$rw.'" class="row_sort" data-row="'.$rw.'">'.
				'<div class="row_drag">'.
					'<input type="hidden" name="row_no" value="'.$rw.'">'.
					'<span class="Row-drag trans5 hid"><i class="fa fa-arrows"></i></span>'.
				'</div>'.
				'<div class="row_div">';
		}

		if ($EDIT) {
			$html .= '<table class="RowTable-edit text_inline box2 trans" border="0"><tbody>';
		} else {
			$html .= '<table class="RowTable text_inline box2" border="0"><tbody>';
		}
		
		$html .= 		'<tr class="row_item_sort" data-row="'.$rw.'">';

		//items ###########################
		$items_count = 0;
		if (isset($row['items'])) {
			$items_arr = (array)$row['items'];
			$items_count = count($items_arr);
			foreach ($items_arr as $item) {
				$html .= get_Form_Row_Item($item['type'].'', $rw, $item);
				//if ($item['required'] == '1') $items_num++; //only required items
				//all items can have value
				if (in_array($item['type'], $Item_Types_that_Count_as_Items)) {
					$items_num++;
				}
			}
		}
		//items end #######################

		
		if ($EDIT) { //dummy td.rowItem-init to have a starting point for inserting more items
			$html .= 		'<td class="rowItem-init' . ($items_count ? ' hidden' : '') . '" style="width:1%;"></td>';
		}

		$html .= 		'</tr>';

		$html .= 	'</tbody></table>';
						
		if ($EDIT) {
			$html .= ''.
				'</div>'.
				//Row Actions
				'<div class="row_actions">'.
					//Row add
					'<span class="rowItem-add trans5 hid" data-row="'.$rw.'" title="'.$LANG->FORM_ITEM_ADD.'">'.
						'<i class="fa fa-plus-circle"></i>'.
					'</span>'.
					//Row copy
					'<span class="Row-copy trans5 hid" data-row="'.$rw.'" title="'.$LANG->FORM_ROW_DUPLICATE.'">'.
						'<span class="fa-stack">'.
							'<i class="fa fa-circle-thin fa-stack-2x"></i>'.
							'<i class="fa fa-copy fa-stack-1x" style="font-weight:bold;"></i>'.
						'</span>'.
					'</span>'.
					//Row delete
					'<span class="Row-remove trans5 hid">'.
						'<i class="fa fa-times-circle" title="'.$LANG->FORM_ROW_DELETE.'"></i>'.
					'</span>'.
				'</div>'.
			'</li>';
		}
	}
	//rows end ###############################

	return $html;
}


function get_Form_Row_Item(string $type, int $row, mixed $item, string $Accordion_Type = '0'):string {
	global $LANG, $EDIT, $HAVE_DATA, $CHANGE, $VIEW, $FORM_DATA; 

	$type_arr = array(
		"_Empty" 	=> $LANG->FORM_ITEM_EMPTY,
		"_Space" 	=> $LANG->FORM_ITEM_SPACE,
		"_Line" 	=> $LANG->FORM_ITEM_LINE,
		"_Label" 	=> $LANG->FORM_ITEM_LABEL,
		"_Html" 	=> $LANG->FORM_ITEM_TEXT_HTML,
		"_Text" 	=> $LANG->FORM_ITEM_TEXT,
		"_Textarea" => $LANG->FORM_ITEM_TEXTAREA,
		"_Number" 	=> $LANG->FORM_ITEM_NUMBER,
		"_Date" 	=> $LANG->FORM_ITEM_DATE,
		"_Time" 	=> $LANG->FORM_ITEM_TIME,
		"_Period" 	=> $LANG->FORM_ITEM_PERIOD,
		"_Dropdown" => $LANG->FORM_ITEM_DROPDOWN,
		"_RadioButtons"	 	=> $LANG->FORM_ITEM_RADIO,
		"_Accordion" 		=> $LANG->FORM_ITEM_ACCORDION,
		"_Accordion_Panel" 	=> $LANG->FORM_ITEM_ACCORDION_PANEL,
		"_Dropdown_Select_Only" 	=> "_Dropdown_Select_Only",
		"_Radio_Buttons_Select_Only"=> "_Radio_Buttons_Select_Only"
	);

	//basic fields
	$no = $item['no'];
	$id = $row.'_'.$no;
	$unid = $item['unid'] ?? $id; 
	$width = $item['width'] ?? '100';
	//required
	$is_required = false;
	if (isset($item['required']) AND $item['required']=='1') $is_required = true;
	//td class required
	$rc_td = '';
	if ($is_required AND !$VIEW) $rc_td = ' required'; //class
	//input class required
	$rc = 'textfield'; //class
	if ($is_required AND !$VIEW) $rc .= ' required'; //class
	//change value
	$ch_val = '';
	if (($CHANGE OR $VIEW) AND $FORM_DATA AND isset($FORM_DATA[$unid])) {
		$ch_val = $FORM_DATA[$unid];
	}

	//basic 2 fields
	$name = $item['name'] ?? ''; 
	$placeholder = $item['placeholder'] ?? ''; 
	$has_color = (isset($item['has_color']) AND $item['has_color']=='1') ? true : false;
	$color = $item['color'] ?? '120|0'; //def=Red-Yellow-Green
	$color_tmp = explode('|', $color.'|');
	$color_a = ($color_tmp[0]!='' ? $color_tmp[0] : '120');
	$color_b = ($color_tmp[1]!='' ? $color_tmp[1] : '0');

	$html = '';	
	$HTML_edit_div = $HTML_edit_type__no = $HTML_edit_link = $HTML_edit_unid = '';
	$HTML_edit_name = $HTML_edit_placeholder = $HTML_edit_required = $HTML_edit_width = $HTML_edit_color = '';

	if ($EDIT) {
		//main standard fields in case of EDIT
		$HTML_edit_type__no = ''.
				'<input type="hidden" name="rowItem_no" value="'.$no.'" class="c_no">'.
				'<input type="hidden" name="rowItem_type" value="'.$type.'" class="c_type">';
		$HTML_edit_link = ''.
				'<div class="c_handler trans50">'.
					'<span class="rowItem_Drag trans10"><i class="fa fa-arrows"></i></span>'.
					'<a class="rowItem_EditLink trans30" data-id="'.$id.'" data-type="'.$type.'" style="cursor:pointer;">'.$type_arr[$type].'</a>'.
				'</div>';
		$rc = 'textfield'; //class


		//rest standard fields in case of EDIT
		//unid
		$HTML_edit_unid = ''.
				'<input name="c_'.$id.'_unid" type="hidden" value="'.$unid.'" class="c_unid">';

		//name
		$HTML_edit_name = ''.
				$LANG->FORM_OUTPUT_NAME.':<br>'.	
				'<input name="c_'.$id.'_name" type="text" value="'.$name.'" class="c_name required">'.
				'<hr class="separator">';

		//placeholder
		$HTML_edit_placeholder = ''.
				$LANG->FORM_PLACEHOLDER.':<br>'.
				'<input name="c_'.$id.'_placeholder" type="text" value="'.$placeholder.'" class="c_placeholder">'.
				'<hr class="separator">';

		//required
		$HTML_edit_required = ''.
				$LANG->FORM_ITEM_REQUIRED.':<span class="span_required">*</span>'.
				'<input type="checkbox"'.($is_required?' checked':'').' onchange="this.nextSibling.value=this.checked==true?1:0;">'.
				'<input type="hidden" name="c_'.$id.'_required" value="'.($is_required?'1':'0').'" class="c_required">'.
				'<hr class="separator">';

		//width
		$HTML_edit_width = ''.
				$LANG->FORM_ITEM_WIDTH.' : <input name="c_'.$id.'_width" value="'.$width.'" class="c_width">';

		//color
		$HTML_edit_color = ''.
				'<div title="'.$LANG->FORM_ITEM_USE_COLOR.'">'.
					$LANG->FORM_ITEM_USE_COLOR.': '.
					'<input type="checkbox"'.($has_color?' checked':'').' onchange="this.nextSibling.value=this.checked==true?1:0;" class="c_'.$id.'_has_color_ck">'.
					'<input type="hidden" name="c_'.$id.'_has_color" value="'.($has_color?'1':'0').'" class="c_has_color c_'.$id.'_has_color">'.
					'<script>'.
						'$(function(){'.
							'$(".c_'.$id.'_has_color_ck").on("change",function(){'.
								'$(".c_'.$id.'_color_a, .c_'.$id.'_color_b").prop("disabled",(this.checked?false:true));'.
							'});'.
						'});'.
					'</script>'.
					'<br>'.
					'<select name="c_'.$id.'_color_a" class="c_color_a c_'.$id.'_color_a" '.($has_color?'':' disabled').'>'.
						'<option value="0"'.($color_a=='0'?' selected':'').'>Red</option>'.
						'<option value="60"'.($color_a=='60'?' selected':'').'>Yellow</option>'.
						'<option value="120"'.($color_a=='120'?' selected':'').'>Green</option>'.
						'<option value="180"'.($color_a=='180'?' selected':'').'>Cyan</option>'.
						'<option value="240"'.($color_a=='240'?' selected':'').'>Blue</option>'.
						'<option value="300"'.($color_a=='300'?' selected':'').'>Magenta</option>'.
						'<option value="360"'.($color_a=='360'?' selected':'').'>Red</option>'.
					'</select>'.
					'<select name="c_'.$id.'_color_b" class="c_color_b c_'.$id.'_color_b" '.($has_color?'':' disabled').'>'.
						'<option value="0"'.($color_b=='0'?' selected':'').'>Red</option>'.
						'<option value="60"'.($color_b=='60'?' selected':'').'>Yellow</option>'.
						'<option value="120"'.($color_b=='120'?' selected':'').'>Green</option>'.
						'<option value="180"'.($color_b=='180'?' selected':'').'>Cyan</option>'.
						'<option value="240"'.($color_b=='240'?' selected':'').'>Blue</option>'.
						'<option value="300"'.($color_b=='300'?' selected':'').'>Magenta</option>'.
						'<option value="360"'.($color_b=='360'?' selected':'').'>Red</option>'.
					'</select>'.
				'</div>'.
				'<hr class="separator">';
	}

	//_Empty ################################################
	if ($type == '_Empty') { //this work without load - runs from the js code
		//we use this when we make a reset to an item
		if ($EDIT) {
			$HTML_edit_div = ''.
				'<div class="rowItem_edit">'.
					$HTML_edit_type__no.
					$HTML_edit_link.
					'<div class="c_content hidden">'.
						'<select class="rowItem_Select" style="width:100%;">'.
							'<option value="_Space">'.$LANG->FORM_ITEM_SPACE.'</option>'.
							'<option value="_Line">'.$LANG->FORM_ITEM_LINE.'</option>'.
							'<option value="_Label">'.$LANG->FORM_ITEM_LABEL.'</option>'.
							'<option value="_Html">'.$LANG->FORM_ITEM_TEXT_HTML.'</option>'.
							'<option value="_Text">'.$LANG->FORM_ITEM_TEXT.'</option>'.
							'<option value="_Textarea">'.$LANG->FORM_ITEM_TEXTAREA.'</option>'.
							'<option value="_Number">'.$LANG->FORM_ITEM_NUMBER.'</option>'.
							'<option value="_Date">'.$LANG->FORM_ITEM_DATE.'</option>'.
							'<option value="_Time">'.$LANG->FORM_ITEM_TIME.'</option>'.
							'<option value="_Period">'.$LANG->FORM_ITEM_PERIOD.'</option>'.
							'<option value="_Dropdown">'.$LANG->FORM_ITEM_DROPDOWN.'</option>'.
							'<option value="_RadioButtons">'.$LANG->FORM_ITEM_RADIO.'</option>'.
							'<option value="_Accordion">'.$LANG->FORM_ITEM_ACCORDION.'</option>'.
						'</select>'.
					'</div>'.
				'</div>';
		}
		$html .= ''.
			'<td id="rowItem_'.$id.'" class="rowItem s_input cm_it empty" style="width:100%;" data-row_item="'.$id.'">'.
				($EDIT 
				? $HTML_edit_div.
				'<div class="rowItem_div">'.
					'<span class="c_empty"></span>'.
				'</div>'
				: ''
				).
			'</td>';
	}
	//_Space ################################################
	elseif ($type == '_Space') {
		if ($EDIT) {
			$HTML_edit_div = ''.
				'<div class="rowItem_edit">'.
					$HTML_edit_type__no.
					$HTML_edit_link.
					'<div class="c_content hidden">'.
						$HTML_edit_width.
					'</div>'.
				'</div>';
		}
		$html .= ''.
			'<td id="rowItem_'.$id.'" class="rowItem s_input cm_it" style="width:'.$width.'%;" data-row_item="'.$id.'">'.
				($EDIT 
				? $HTML_edit_div.
				'<div class="rowItem_div">'.
					'<span class="c_space"></span>'.
				'</div>'
				: ''
				).
			'</td>';
	}
	//_Line #################################################
	elseif ($type == '_Line') {
		if ($EDIT) {
			$HTML_edit_div = ''.
				'<div class="rowItem_edit">'.
					$HTML_edit_type__no.
					$HTML_edit_link.
					'<div class="c_content hidden">'.
						$HTML_edit_width.
					'</div>'.
				'</div>';
		}
		$html .= ''.
			'<td id="rowItem_'.$id.'" class="rowItem s_input cm_it" style="width:'.$width.'%;" data-row_item="'.$id.'">'.
				($EDIT 
				? $HTML_edit_div.
				'<div class="rowItem_div">'.
					'<span class="c_line"><hr></span>'.
				'</div>'
				: '<hr>'
				).
			'</td>'.
		'';
	}
	//_Html #################################################
	elseif ($type == '_Html') {
		$text = htmlspecialchars_decode(($item['text'] ?? '').'');
		if ($EDIT) {
			$HTML_edit_div = ''.
				'<div class="rowItem_edit">'.
					$HTML_edit_type__no.
					$HTML_edit_link.
					'<div class="c_content hidden">'.
						$HTML_edit_width.
					'</div>'.
				'</div>';
		}
		$html .= '	
			<td id="rowItem_'.$id.'" class="rowItem s_input cm_it" style="padding:0; text-align:left; width:'.$width.'%;" data-row_item="'.$id.'">'.
				($EDIT 
				? $HTML_edit_div.
				'<div class="rowItem_div">'.
					'<textarea name="c_'.$id.'_txt" class="textarea c_text" style="width:100%; height:100px;">'.$text.'</textarea>'
				: 	$text
				).
				($EDIT ? '</div>' :'').
			'</td>'.
		'';
	}
	//_Label ################################################
	elseif ($type == '_Label') {
		$label = $item['label'] ?? '';
		$align = $item['align'] ?? 'left';
		$bold = $item['bold'] ?? '0';
		if ($EDIT) {
			$HTML_edit_div = ''.
				'<div class="rowItem_edit">'.
					$HTML_edit_type__no.
					$HTML_edit_link.
	//------------>>>>>>
	'<div class="c_content hidden">'.
		'<div>'.
			$LANG->FORM_ITEM_TITLE.':<br>'.
			'<input name="c_'.$id.'_label" type="text" value="'.html_chars($label).'" class="c_label"><br>'.
		'</div>'.
		'<div class="btn-group btn_group_3" data-toggle="buttons">'.
			'<label class="btn btn-default'.($align=='left'?' active':'').'">'.
				'<input name="c_'.$id.'_align" type="radio" value="left" data-toggle="button"'.($align=='left'?' checked':'').'>'.$LANG->FORM_ITEM_ALIGN_LEFT.
			'</label>'.
			'<label class="btn btn-default'.($align=='center'?' active':'').'">'.
				'<input name="c_'.$id.'_align" type="radio" value="center" data-toggle="button"'.($align=='center'?' checked':'').'>'.$LANG->FORM_ITEM_ALIGN_CENTER.
			'</label>'.
			'<label class="btn btn-default'.($align=='right'?' active':'').'">'.
				'<input name="c_'.$id.'_align" type="radio" value="right" data-toggle="button"'.($align=='right'?' checked':'').'>'.$LANG->FORM_ITEM_ALIGN_RIGHT.
			'</label>'.
		'</div>'.
		'<div class="btn-group btn_group_3" data-toggle="buttons">'.
			'<label class="btn btn-default'.($bold=='2'?' active':'').'">'.
				'<input name="c_'.$id.'_bold" type="radio" value="2" data-toggle="button"'.($bold=='2'?' checked':'').'>'.$LANG->FORM_ITEM_BOLD_NOT.
			'</label>'.
			'<label class="btn btn-default'.($bold=='1'?' active':'').'">'.
				'<input name="c_'.$id.'_bold" type="radio" value="1" data-toggle="button"'.($bold=='1'?' checked':'').'>'.$LANG->FORM_ITEM_BOLD_HALF.
			'</label>'.
			'<label class="btn btn-default'.($bold=='0'?' active':'').'">'.
				'<input name="c_'.$id.'_bold" type="radio" value="0" data-toggle="button"'.($bold=='0'?' checked':'').'>'.$LANG->FORM_ITEM_BOLD_FULL.
			'</label>'.
		'</div>'.
		'<hr class="separator">'.
		$HTML_edit_width.
	'</div>'.
	//------------>>>>>>
				'</div>';
		}
		$html .= '	
			<td id="rowItem_'.$id.'" class="rowItem s_input cm_it break_word" style="text-align:'.$align.'; font-weight:'.($bold=='0'?'700':($bold=='1'?'600':'500')).'; width:'.$width.'%;" data-row_item="'.$id.'">'.
				($EDIT 
				? $HTML_edit_div.
				'<div class="rowItem_div">'.
					'<span class="label_txt">'.html_chars($label).'</span>'
				: 	$label
				).
				($EDIT ? '</div>' :'').
			'</td>'.
		'';
	}
	//_Text #################################################
	elseif ($type == '_Text') {
		if ($EDIT) {
			$HTML_edit_div = ''.
				'<div class="rowItem_edit">'.
					$HTML_edit_type__no.
					$HTML_edit_link.
					'<div class="c_content hidden">'.
						$HTML_edit_unid.
						$HTML_edit_name.
						$HTML_edit_placeholder.
						$HTML_edit_required.
						$HTML_edit_width.
					'</div>'.
				'</div>';
		}
		$html .= ''.
			'<td id="rowItem_'.$id.'" class="rowItem s_input cm_it'.$rc_td.'" style="width:'.$width.'%;" data-row_item="'.$id.'">'.
				($EDIT 
				? $HTML_edit_div.
				'<div class="rowItem_div">'.
					'<input name="c_'.$id.'_tx" type="text" class="'.$rc.' form-control" value="'.'" placeholder="'.$placeholder.'">'
				: 	'<input name="'.$unid.'" type="text" class="'.$rc.' form-control" value="'.$ch_val.'" placeholder="'.$placeholder.'"'.($VIEW?' disabled':'').'>'
				).
				($EDIT ? '</div>' :'').
			'</td>'.
		'';
	}
	//_Textarea #############################################
	elseif ($type == '_Textarea') {
		if ($EDIT) {
			$HTML_edit_div = ''.
				'<div class="rowItem_edit">'.
					$HTML_edit_type__no.
					$HTML_edit_link.
					'<div class="c_content hidden">'.
						$HTML_edit_unid.
						$HTML_edit_name.
						$HTML_edit_placeholder.
						$HTML_edit_required.
						$HTML_edit_width.
					'</div>'.
				'</div>';
		}
		$html .= ''.
			'<td id="rowItem_'.$id.'" class="rowItem s_input cm_it'.$rc_td.'" style="width:'.$width.'%;" data-row_item="'.$id.'">'.
				($EDIT 
				? $HTML_edit_div.
				'<div class="rowItem_div">'.
					'<textarea name="c_'.$id.'_txa" type="text" class="'.$rc.' form-control" placeholder="'.str_replace('<br>',"\n",$placeholder.'').'"></textarea>'
				: ''.
				'<textarea name="'.$unid.'" type="text" class="'.$rc.' form-control" placeholder="'.str_replace('<br>',"\n",$placeholder.'').'"'.($VIEW?' disabled':'').'>'.str_replace('<br>',"\n",$ch_val).'</textarea>'
				).
				($EDIT ? '</div>' :'').
			'</td>'.
		'';
	}
	//_Number ###############################################
	elseif ($type == '_Number') {
		$min = $item['min'];
		$max = $item['max'];
		$decimal = $item['decimal'];
		$validate = '';

		if ($min!='' AND $max!='') $validate = ' range="'.$min.','.$max.'"';
		elseif ($min!='') $validate = ' min="'.$min.'"';
		elseif ($max!='') $validate = ' max="'.$max.'"';

		if ($decimal=='1') $validate .= ' number="true"'; //number = integer + float
		else $validate .= ' digits="true"'; //digits = only numbers, integer
		
		if ($EDIT) {
			$HTML_edit_div = ''.
				'<div class="rowItem_edit">'.
					$HTML_edit_type__no.
					$HTML_edit_link.
	//-------->>>>>>
	'<div class="c_content hidden">'.
		'<div>'.
			$HTML_edit_unid.
			$HTML_edit_name.
			$HTML_edit_placeholder.
			$LANG->FORM_ITEM_MIN.': <input name="c_'.$id.'_nmmin" type="text" value="'.$min.'" style="width:25%;" class="c_min">'.
			' <i class="fa fa-arrow-right"></i> '.
			$LANG->FORM_ITEM_MAX.': <input name="c_'.$id.'_nmmax" type="text" value="'.$max.'" style="width:25%;" class="c_max">'.
		'</div>'.
		'<hr class="separator">'.
		'<div class="btn-group btn_group_2" data-toggle="buttons">'.
			'<label class="btn btn-default'.(!$decimal?' active':'').'">'.
				'<input name="c_'.$id.'_numintdec" type="radio" value="integer" data-toggle="button"'.(!$decimal?' checked':'').'>'.$LANG->FORM_ITEM_INTEGER.
			'</label>'.
			'<label class="btn btn-default'.($decimal?' active':'').'">'.
				'<input name="c_'.$id.'_numintdec" type="radio" value="decimal" data-toggle="button"'.($decimal?' checked':'').'>'.$LANG->FORM_ITEM_DECIMAL.
			'</label>'.
		'</div>'.
		'<hr class="separator">'.
		$HTML_edit_required.
		$HTML_edit_width.
	'</div>'.
	//-------->>>>>>
				'</div>';
		}
		$html .= ''.
			'<td id="rowItem_'.$id.'" class="rowItem s_input cm_it'.$rc_td.'" style="width:'.$width.'%;" data-row_item="'.$id.'">'.
				($EDIT 
				? $HTML_edit_div.
				'<div class="rowItem_div">'.
					'<input name="c_'.$id.'_nm" type="text" class="'.$rc.' form-control" value="'.'" placeholder="'.$placeholder.'">'
				: ''.
				'<input name="'.$unid.'" type="text" class="'.$rc.' form-control" value="'.$ch_val.'" placeholder="'.$placeholder.'"'.$validate.''.($VIEW?' disabled':'').'>'
				).
				($EDIT ? '</div>' :'').
			'</td>'
		.'';
	}
	//_Date #################################################
	elseif ($type == '_Date') {
		if ($EDIT) {
			$HTML_edit_div = ''.
				'<div class="rowItem_edit">'.
					$HTML_edit_type__no.
					$HTML_edit_link.
					'<div class="c_content hidden">'.
						$HTML_edit_unid.
						$HTML_edit_name.
						$HTML_edit_placeholder.
						$HTML_edit_required.
						$HTML_edit_width.
					'</div>'.
				'</div>';
		}
		$html .= ''.
			'<td id="rowItem_'.$id.'" class="rowItem s_input cm_it'.$rc_td.'" style="width:'.$width.'%;" data-row_item="'.$id.'">'.
			  ($EDIT ? $HTML_edit_div . '<div class="rowItem_div">' :'').

				'<div class="input-group date" id="datetimepicker_'.$id.'" style="width:100%;">'.
				($EDIT
					? '<input name="c_'.$id.'_date" type="text" class="'.$rc.' form-control" value="'.'" placeholder="'.$placeholder.'">'
					: '<input name="'.$unid.'" type="text" class="'.$rc.' form-control" value="'.$ch_val.'" placeholder="'.$placeholder.'"'.($VIEW?' disabled':'').'>'
				).
					'<span class="input-group-addon"><span class="fa fa-calendar"></span></span>'.
				'</div>'.
				
			  ($EDIT ? '</div>' :'').
			'</td>'.
		'';
	}
	//_Time #################################################
	elseif ($type == '_Time') {
		if ($EDIT) {
			$HTML_edit_div = ''.
				'<div class="rowItem_edit">'.
					$HTML_edit_type__no.
					$HTML_edit_link.
					'<div class="c_content hidden">'.
						$HTML_edit_unid.
						$HTML_edit_name.
						$HTML_edit_placeholder.
						$HTML_edit_required.
						$HTML_edit_width.
					'</div>'.
				'</div>';
		}
		$html .= ''.
			'<td id="rowItem_'.$id.'" class="rowItem s_input cm_it'.$rc_td.'" style="width:'.$width.'%;" data-row_item="'.$id.'">'.
			  ($EDIT ? $HTML_edit_div . '<div class="rowItem_div">' :'').

				'<div class="input-group clockpicker time" id="clockpicker_'.$id.'" data-placement="bottom" data-align="left" data-default="now">'.
				($EDIT
					? '<input name="c_'.$id.'_time" type="text" class="'.$rc.' form-control time" value="'.'" placeholder="'.$placeholder.'">'
					: '<input name="'.$unid.'" type="text" class="'.$rc.' form-control time" value="'.$ch_val.'" placeholder="'.$placeholder.'"'.($VIEW?' disabled':'').'>'
				).
					'<span class="input-group-addon"><span class="fa fa-clock-o"></span></span>'.
				'</div>'.
				
			  ($EDIT ? '</div>' :'').
			'</td>'.
		'';
	}
	//_Period ###############################################
	elseif ($type == '_Period') {
		$placeholder_from = $item['placeholder_from'] ?? ''; 
		$placeholder_to = $item['placeholder_to'] ?? ''; 
		$placeholder = $item['placeholder'] ?? ''; 
		$ch_val_from = ((($CHANGE OR $VIEW) AND $FORM_DATA AND isset($FORM_DATA[$unid])) ? $FORM_DATA[$unid][0] : '');
		$ch_val_to = ((($CHANGE OR $VIEW) AND $FORM_DATA AND isset($FORM_DATA[$unid])) ? $FORM_DATA[$unid][1] : '');
		$ch_val = ((($CHANGE OR $VIEW) AND $FORM_DATA AND isset($FORM_DATA[$unid])) ? $FORM_DATA[$unid][2] : '');
		if ($EDIT) {
			$HTML_edit_div = ''.
				'<div class="rowItem_edit">'.
					$HTML_edit_type__no.
					$HTML_edit_link.
	//-------->>>>>>
	'<div class="c_content hidden">'.
		$HTML_edit_unid.
		$HTML_edit_name.
		$LANG->FORM_PLACEHOLDER_FROM_TO.':<br>'.
		'<input name="c_'.$id.'_placeholder_from" type="text" value="'.$placeholder_from.'" class="c_placeholder_from" style="width:50%;">'.
		'<input name="c_'.$id.'_placeholder_to" type="text" value="'.$placeholder_to.'" class="c_placeholder_to" style="width:50%;">'.
		'<hr class="separator">'.
		$LANG->FORM_PLACEHOLDER_PERIOD.':<br>'.
		'<input name="c_'.$id.'_placeholder" type="text" value="'.$placeholder.'" class="c_placeholder">'.
		'<hr class="separator">'.
		$HTML_edit_required.
		$HTML_edit_width.
	'</div>'.
	//-------->>>>>>
				'</div>';
		}
		$html .= ''.
			'<td id="rowItem_'.$id.'" class="rowItem s_input cm_it'.$rc_td.'" style="width:'.$width.'%;" data-row_item="'.$id.'">'.
			  ($EDIT ? $HTML_edit_div . '<div class="rowItem_div">' :'').
			
				'<div class="input-group clockpicker period" id="clockpicker_from_'.$id.'" data-id="'.$id.'" data-placement="bottom" data-align="left" data-default="now" style="display:inline-table; width:31%;">'.
					'<span class="input-group-addon" style="padding:6px;"><span class="fa fa-clock-o"></span></span>'.
				($EDIT
					? '<input name="c_'.$id.'_PRDfrom" id="c_'.$id.'_PRDfrom" type="text" class="form-control time period" value="'.'" placeholder="'.$placeholder_from.'">'
					: '<input name="'.$unid.'[0]" id="c_'.$id.'_PRDfrom" type="text" class="form-control time period" value="'.$ch_val_from.'" placeholder="'.$placeholder_from.'"'.($VIEW?' disabled':'').'>'
				).
					'<span class="input-group-addon" style="padding:6px 3px; border-top-right-radius:0; border-bottom-right-radius:0;"><span class="fa fa-long-arrow-right"></span></span>'.
				'</div>'.
				'<div class="input-group clockpicker period" id="clockpicker_to_'.$id.'" data-id="'.$id.'" data-placement="bottom" data-align="center" data-default="now" style="display:inline-table; width:25%; margin-left:-2px;">'.
				($EDIT
					? '<input name="c_'.$id.'_PRDto" id="c_'.$id.'_PRDto" type="text" class="form-control time period" value="'.'" placeholder="'.$placeholder_to.'">'
					: '<input name="'.$unid.'[1]" id="c_'.$id.'_PRDto" type="text" class="form-control time period" value="'.$ch_val_to.'" placeholder="'.$placeholder_to.'"'.($VIEW?' disabled':'').'>'
				).
					'<span class="input-group-addon" style="padding:6px;"><span class="fa fa-clock-o fa-flip-horizontal"></span></span>'.
				'</div>'.
				'<div class="input-group clockpicker period" id="clockpicker_period_'.$id.'" data-id="'.$id.'" data-placement="bottom" data-align="right" data-default="now" style="display:inline-table; width:44%; margin-left:1px;">'.
					'<span class="input-group-addon" style="padding:6px;"><span class="fa fa-pause fa-rotate-90" style="font-size:12px;"></span></span>'.
				($EDIT
					? '<input name="c_'.$id.'_PRDperiod" id="c_'.$id.'_PRDperiod" type="text" class="'.$rc.' form-control time period" value="'.'" placeholder="'.$placeholder.'">'
					: '<input name="'.$unid.'[2]" id="c_'.$id.'_PRDperiod" type="text" class="'.$rc.' form-control time period PRDperiod" value="'.$ch_val.'" placeholder="'.$placeholder.'"'.($VIEW?' disabled':'').'>'
				).
					'<span class="input-group-addon" style="padding:6px;">'.
						'<span class="fa fa-clock-o"></span>'.
						'<span class="fa fa-arrows-h" style="margin:0 1px;"></span>'.
						'<span class="fa fa-clock-o fa-flip-horizontal"></span>'.
					'</span>'.
				'</div>'.
				
			  ($EDIT ? '</div>' : '').
			'</td>'.
		'';
	}
	//_Dropdown #############################################
	elseif ($type == '_Dropdown') {
		$opt = $item['opt'] ?? '';
		$dd = $item['dd'] ?? '';
		if ($EDIT) {
			$HTML_edit_div = ''.
				'<div class="rowItem_edit">'.
					$HTML_edit_type__no.
					$HTML_edit_link.
					'<div class="c_content hidden">'.
						$HTML_edit_unid.
						$HTML_edit_name.
						$LANG->FORM_PLACEHOLDER.':<br>'.
						'<input name="c_'.$id.'_opt" type="text" value="'.$opt.'" class="c_opt">'.
						'<hr class="separator">'.
						$LANG->FORM_DROPDOWN.':<br>'.
						'<select name="c_'.$id.'_dd" class="c_dd">'.
							'<option value="" selected>'.$LANG->FORM_ITEM_SELECT_OPTION.'</option>'.
							get_Available_Dropdowns($dd).
						'</select>'.
						'<hr class="separator">'.
						$HTML_edit_color.
						$HTML_edit_required.
						$HTML_edit_width.
					'</div>'.
				'</div>';
		}
		$html .= ''.
			'<td id="rowItem_'.$id.'" class="rowItem s_input cm_it'.$rc_td.'" style="width:'.$width.'%;" data-row_item="'.$id.'">'.
			($EDIT
				? $HTML_edit_div.
				'<div class="rowItem_div">'.
					'<div class="styled-select">'.
						'<select class="'.$rc.' form-control form-control-color">'.
							'<option value="" selected>'.$opt.'</option>'.
							get_Dropdown_Options($dd, '', false, false, $has_color, $color_a, $color_b).
						'</select>'.
					'</div>'.
				'</div>'
				: ''.
				'<div class="styled-select">'.
					'<select name="'.$unid.'" class="'.$rc.' form-control form-control-color"'.($VIEW?' disabled':'').'>'.
						'<option value="" selected>'.$opt.'</option>'.
						get_Dropdown_Options($dd, $ch_val, false, false, $has_color, $color_a, $color_b).
					'</select>'.
				'</div>'
			).
			'</td>';
	}
	//_Dropdown_Select_Only #################################
	elseif ($type == '_Dropdown_Select_Only') {
		$opt = $item['opt'] ?? '';
		$dd = $item['dd'] ?? '';
		$html .= ''.
			'<select class="'.$rc.' form-control form-control-color">'.
				'<option value="" selected>'.$opt.'</option>'.
				get_Dropdown_Options($dd, '', false, false, $has_color, $color_a, $color_b).
			'</select>';
	}
	//_RadioButtons #########################################
	elseif ($type == '_RadioButtons') {
		$has_title = (isset($item['has_title']) AND $item['has_title']=='1') ? true : false;
		$title = html_chars($item['title'] ?? '');
		$talign = $item['talign'] ?? 'left';
		$rdd = $item['rdd'] ?? '';
		$rc = 'check_radio'; //. (($is_required)?' required':'');
		if ($EDIT) {
			$rc = 'check_radio';
			$HTML_edit_div = ''.
				'<div class="rowItem_edit">'.
					$HTML_edit_type__no.
					$HTML_edit_link.
	//-------->>>>>>
	'<div class="c_content hidden">'.
		$HTML_edit_unid.
		$HTML_edit_name.
		'<div>'.
			$LANG->FORM_ITEM_TITLE.': '.
			'<input type="checkbox"'.($has_title?' checked':'').' onchange="this.nextSibling.value=this.checked==true?1:0;" class="c_'.$id.'_has_title_ck">'.
			'<input type="hidden" name="c_'.$id.'_has_title" value="'.($has_title?'1':'0').'" class="c_has_title c_'.$id.'_has_title"><br>'.
			'<script>'.
				'$(function(){'.
					'$(".c_'.$id.'_has_title_ck").on("change",function(){'.
						'$(".c_'.$id.'_title").prop("disabled",(this.checked?false:true));'.
						'if(!this.checked){$(".btn_rd_align_'.$id.' label").addClass("disabled");}'.
						'else{$(".btn_rd_align_'.$id.' label").removeClass("disabled");}'.
					'});'.
				'});'.
			'</script>'.
			'<input name="c_'.$id.'_title" type="text" value="'.$title.'" class="c_title c_'.$id.'_title"'.($has_title?'':' disabled').'><br>'.
		'</div>'.
		'<div class="btn-group btn_group_3 btn_rd_align_'.$id.'" data-toggle="buttons">'.
			'<label class="btn btn-default'.($talign=='left'?' active':'').($has_title?'':' disabled').'">'.
				'<input name="c_'.$id.'_talign" type="radio" value="left" data-toggle="button"'.($talign=='left'?' checked':'').'>'.$LANG->FORM_ITEM_ALIGN_LEFT.
			'</label>'.
			'<label class="btn btn-default'.($talign=='center'?' active':'').($has_title?'':' disabled').'">'.
				'<input name="c_'.$id.'_talign" type="radio" value="center" data-toggle="button"'.($talign=='center'?' checked':'').'>'.$LANG->FORM_ITEM_ALIGN_CENTER.
			'</label>'.
			'<label class="btn btn-default'.($talign=='right'?' active':'').($has_title?'':' disabled').'">'.
				'<input name="c_'.$id.'_talign" type="radio" value="right" data-toggle="button"'.($talign=='right'?' checked':'').'>'.$LANG->FORM_ITEM_ALIGN_RIGHT.
			'</label>'.
		'</div>'.
		'<hr class="separator">'.
		$LANG->FORM_ITEM_RADIO.':<br>'.
		'<select name="c_'.$id.'_rdd" class="c_rdd">'.
			'<option value="" selected>'.$LANG->FORM_ITEM_SELECT_OPTION.'</option>'.
			get_Available_Dropdowns($rdd).
		'</select><br>'.
		'<hr class="separator">'.
		$HTML_edit_color.
		$HTML_edit_required.
		$HTML_edit_width.
	'</div>'.
	//-------->>>>>>
				'</div>';
		}
		
		//count items and calculate width per item
		$dd_items_arr = (array)get_Dropdown_Options($rdd, '', true, false);
		$dd_items_count = (count($dd_items_arr)>0?count($dd_items_arr):1);
		$width_per_item = 100 / $dd_items_count;
		
		$html .= ''.
			'<td id="rowItem_'.$id.'" class="rowItem s_input cm_it'.$rc_td.'" style="padding:5px 0; width:'.$width.'%;" data-row_item="'.$id.'">'.
			($EDIT ? $HTML_edit_div. '<div class="rowItem_div">' : '');
		
		//radio  --> on create new it has no $rdd, so it comes empty
		$html .= ''.
			'<div class="radio_div">'. // style="border:1px dotted red;"
				'<table class="radio_inline box2" width="100%" border="0"><tbody>'.
				  '<tr class="tr_radio_title'.($has_title?'':' hidden').'">'. //title
					'<td class="s_radio_title" colspan="'.$dd_items_count.'" style="font-weight:bold; text-align:'.$talign.';">'.$title.'</td>'.
				  '</tr>'.
				  '<tr class="tr_radio"><td style="width:99%;">';

		//without this no selected radio are not submited 
		//not work with validator --so only on no required items
		$html .= ((!$EDIT AND !$is_required) ? '<input name="'.$unid.'" type="hidden" value="">' : ''); 
		$empty_label = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		$color_val = 0;
		foreach ($dd_items_arr as $r_no => $r_val) {
			if ($r_no.'' == $r_val.'') $this_val = $r_no; //need .'' bcz else 0=error
			else $this_val = $r_no.'__'.$r_val;
			$t_label = $r_val;
			if ($t_label == '') $t_label = $empty_label;
			$t_color = ($has_color ? get_Color((int)$color_a, (int)$color_b, $dd_items_count, $color_val) : 'none');
			
			$html .= '<div class="s_radio'.($has_color?' radio_color':'').'" style="margin-bottom:5px; width:'.$width_per_item.'%; padding-top:4px; position:relative;">'.// border:1px solid blue; //for a space -> margin-left:1px; 
						'<div class="boxx" style="position:relative; bottom:-42px; margin:-42px auto 0; height:42px; width:42px; border-radius:30px; background-color:'.$t_color.';"></div>'.
						'<div class="nums'.(($ch_val!='' AND ($ch_val==$r_no OR $ch_val==$r_no.'__'.$r_val) AND $VIEW)?' red_bold':'').'">'.$r_no.'</div>'.
				($EDIT
					?	'<input name="c_'.$id.'_radio" type="radio" class="'.$rc.'" value="'.$this_val.'">'
					: 	'<input name="'.$unid.'" type="radio" class="'.$rc.'" value="'.$this_val.'"'.(($ch_val!='' AND ($ch_val==$r_no OR $ch_val==$r_no.'__'.$r_val))?' checked':'').''.($VIEW?' disabled':'').$rc_td.'>'
				).
						'<div class="s_radio_txt" style="margin-top:10px;">'.$t_label.'</div>'. //background-color:'.$t_color.';
					'</div>';
			$color_val++;
		}
		
		//$html .= '<span class="input_required"><i class="fa fa-asterisk"></i></span>';
		$html .=  '</td><td class="required" style="width:1%;"></td></tr>'.
				'</tbody></table>'.
			'</div>';
		//radio end
		
		$html .= ($EDIT ? '</div>' : '');
		$html .= ''.
			'</td>';
	}
	//_Radio_Buttons_Select_Only ############################
	elseif ($type == '_Radio_Buttons_Select_Only') {
		$has_title = (isset($item['has_title']) AND $item['has_title']=='1') ? true : false;
		$title = $item['title'] ?? '';
		$talign = $item['talign'] ?? 'left';
		$rdd = $item['rdd'] ?? '';
		$rc = 'check_radio';
		
		$dd_items_arr = (array)get_Dropdown_Options($rdd, '', true, false);
		$dd_items_count = (count($dd_items_arr)>0?count($dd_items_arr):1);
		$width_per_item = 100 / $dd_items_count; //x items
		
		$html .= ''.
			'<div class="radio_div">'. 
				'<table class="radio_inline box2" width="100%" border="0"><tbody>'.
				  '<tr class="tr_radio_title'.($has_title?'':' hidden').'">'. //title
					'<td class="s_radio_title" colspan="'.$dd_items_count.'" style="font-weight:bold; text-align:'.$talign.';">'.$title.'</td>'.
				  '</tr>'.
				  '<tr class="tr_radio"><td style="width:99%;">';

		$empty_label = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		$color_val = 0;
		foreach ($dd_items_arr as $r_no => $r_val) {
			if ($r_no.'' == $r_val.'') $this_val = $r_no; //need .'' bcz else 0=error
			else $this_val = $r_no.'__'.$r_val;
			$t_label = $r_val;
			if ($t_label == '') $t_label = $empty_label;
			$t_color = ($has_color ? get_Color((int)$color_a, (int)$color_b, $dd_items_count, $color_val) : 'none');
			
			$html .= '<div class="s_radio" style="width:'.$width_per_item.'%; padding-top:4px; position:relative;">'.
						'<div class="boxx" style="position:relative; bottom:-42px; margin:-42px auto 0; height:42px; width:42px; border-radius:30px; background-color:'.$t_color.';"></div>'.
						'<div class="nums">'.$r_no.'</div>'.
						'<input name="c_'.$id.'_radio" type="radio" class="'.$rc.'" value="'.$this_val.'">'.
						'<div class="s_radio_txt" style="margin-top:10px;">'.$t_label.'</div>'. //background-color:'.$t_color.';
					'</div>';
			$color_val++;
		}
		$html .=  '</td><td class="required" style="width:1%;"></td></tr>'.
				'</tbody></table>'.
			'</div>';
	}
	//_Accordion ############################################
	elseif ($type == '_Accordion') {
		//0=basic(1 open), 1=multiple open
		$accType = ((isset($item['accType']) AND $item['accType']) ? '1' : '0');
		$Panels = $item['Panels'] ?? array();
		$row_item = explode('_', $id);
		if ($EDIT) {
			$HTML_edit_div = ''.
				'<div class="rowItem_edit">'.
					$HTML_edit_type__no.
					$HTML_edit_link.
	//-------->>>>>>
	'<div class="c_content hidden">'.
		$LANG->FORM_ITEM_PANEL_OPENING.':<br>'.
		'<div class="btn-group btn_group_1" data-toggle="buttons">'.
			'<label class="btn btn-default'.($accType=='0'?' active':'').'">'.
				'<input name="c_'.$id.'_accType" type="radio" value="0" data-toggle="button"'.($accType=='0'?' checked':'').'>'.$LANG->FORM_ITEM_PANEL_OPEN_ONE.''. //Basic
			'</label>'.
			'<label class="btn btn-default'.($accType=='1'?' active':'').'">'.
				'<input name="c_'.$id.'_accType" type="radio" value="1" data-toggle="button"'.($accType=='1'?' checked':'').'>'.$LANG->FORM_ITEM_PANEL_OPEN_MULTI.''.//multiple
			'</label>'.
		'</div>'.
		'<hr class="separator">'.
		$HTML_edit_width.
	'</div>'.
	//-------->>>>>>
				'</div>';
		}
		$html .= ''.
			'<td id="rowItem_'.$id.'" class="rowItem s_input cm_it" style="width:'.$width.'%;" data-row_item="'.$id.'">'.
			  ($EDIT ? $HTML_edit_div . '<div class="rowItem_div">' : '').
				'<div class="panel-group accordion_group ui-sortable" id="accordion_'.$id.'" role="tablist" aria-multiselectable="true">'.
				
					get_Accordion_Panels($row, $Panels, $accType).
					
					(($EDIT AND !$HAVE_DATA) 
					? 	'<div style="text-align:center; margin-top:10px;">'.
							'<button type="button" class="accPanel-add hid trans10" data-row="'.$row_item[0].'" data-item="'.$row_item[1].'"> &nbsp; '.$LANG->FORM_ITEM_PANEL_ADD.
						'</div>'
					: 	'').
				
				'</div>'.	
			  ($EDIT ? '</div>' : '').
			'</td>';
	}
	//_Accordion_Panel ######################################
	elseif ($type == '_Accordion_Panel') {
		$acc_no = $item['acc_no'] ?? 1;
		$acc_id = $id;
		$open = $item['open'] ?? false;
		$label = html_chars($item['label'] ?? '');
		$align = $item['align'] ?? 'left';
		$bold = $item['bold'] ?? '0';
		//override
		$id = $id.'_'.$acc_no;
		$row_item = explode('_', $id);
		$HTML_edit_type__no = '<input type="hidden" name="rowItem_Acc_no" value="'.$acc_no.'" class="c_acc_no">';
		$HTML_edit_link = '<div class="c_handler_acc trans50">'
					.'<span class="rowItem_AccPanel_Drag trans10"><i class="fa fa-arrows"></i></span>'
					.'<a class="rowItem_AccPanel_EditLink trans30" data-id="'.$id.'" data-type="'.$type.'" style="cursor:pointer;">'.$type_arr[$type].'</a>'
				.'</div>';
		if ($EDIT) {
			$HTML_edit_div = ''.
				'<div class="rowItem_edit">'.
					$HTML_edit_type__no.
					$HTML_edit_link.
	//------------>>>>>>
	'<div class="c_content hidden">'.
		'<div>'.
			$LANG->FORM_ITEM_TITLE.':<br>'.
			'<input name="c_'.$id.'_accLabel" type="text" value="'.$label.'" class="c_accLabel">'.
		'</div>'.
		'<div class="btn-group btn_group_3" data-toggle="buttons">'.
			'<label class="btn btn-default'.($align=='left'?' active':'').'">'.
				'<input name="c_'.$id.'_accAlign" type="radio" value="left" data-toggle="button"'.($align=='left'?' checked':'').'>'.$LANG->FORM_ITEM_ALIGN_LEFT.
			'</label>'.
			'<label class="btn btn-default'.($align=='center'?' active':'').'">'.
				'<input name="c_'.$id.'_accAlign" type="radio" value="center" data-toggle="button"'.($align=='center'?' checked':'').'>'.$LANG->FORM_ITEM_ALIGN_CENTER.
			'</label>'.
			'<label class="btn btn-default'.($align=='right'?' active':'').'">'.
				'<input name="c_'.$id.'_accAlign" type="radio" value="right" data-toggle="button"'.($align=='right'?' checked':'').'>'.$LANG->FORM_ITEM_ALIGN_RIGHT.
			'</label>'.
		'</div>'.
		'<div class="btn-group btn_group_3" data-toggle="buttons">'.
			'<label class="btn btn-default'.($bold=='2'?' active':'').'">'.
				'<input name="c_'.$id.'_accBold" type="radio" value="2" data-toggle="button"'.($bold=='2'?' checked':'').'>'.$LANG->FORM_ITEM_BOLD_NOT.
			'</label>'.
			'<label class="btn btn-default'.($bold=='1'?' active':'').'">'.
				'<input name="c_'.$id.'_accBold" type="radio" value="1" data-toggle="button"'.($bold=='1'?' checked':'').'>'.$LANG->FORM_ITEM_BOLD_HALF.
			'</label>'.
			'<label class="btn btn-default'.($bold=='0'?' active':'').'">'.
				'<input name="c_'.$id.'_accBold" type="radio" value="0" data-toggle="button"'.($bold=='0'?' checked':'').'>'.$LANG->FORM_ITEM_BOLD_FULL.
			'</label>'.
		'</div>'.
		'<hr style="margin:5px 0 5px 0;">'.
		$LANG->FORM_ITEM_PANEL_OPEN_BEGIN.':<br>'.
		'<div class="btn-group btn_group_2" data-toggle="buttons">'.
			'<label class="btn btn-default'.($open?' active':'').'">'.
				'<input name="c_'.$id.'_accOpen" type="radio" value="1" data-toggle="button"'.($open?' checked':'').'>'.$LANG->FORM_ITEM_PANEL_OPENED.
			'</label>'.
			'<label class="btn btn-default'.(!$open?' active':'').'">'.
				'<input name="c_'.$id.'_accOpen" type="radio" value="0" data-toggle="button"'.(!$open?' checked':'').'>'.$LANG->FORM_ITEM_PANEL_CLOSED.
			'</label>'.
		'</div>'.
	'</div>'.
	//------------>>>>>>
				'</div>';
		}
		$html .= ''.
				($EDIT
					? ''.
					'<div id="AccPanel_'.$id.'" class="AccPanel" data-id="'.$id.'">'.
						$HTML_edit_div . 
						'<div class="rowItem_div">'
					: '').
				
				'<div class="panel panel-default acc_sort">'.
					'<div class="panel-heading" role="tab">'.
						'<h4 class="panel-title">'.
							//data-parent="#accordion_'.$acc_id.' for only one open 
							//class="trigger collapsed" -trigger for icon -collapsed for closed
							'<a href="#accordionPanel_'.$id.'"'.($Accordion_Type=='0'?' data-parent="#accordion_'.$acc_id.'"':'').' role="button" data-toggle="collapse" aria-expanded="true" aria-controls="accordionPanel_'.$id.'" class="accLabel trigger'.($open?'':' collapsed').'" style="text-align:'.$align.'; font-weight:'.($bold=='0'?'700':($bold=='1'?'600':'500')).';">'.
								' '.$label.
							'</a>'.
						'</h4>'.
						'</div>'.
					'<div id="accordionPanel_'.$id.'" class="accordionPanel panel-collapse collapse'.($open?' in':'').'" role="tabpanel">'.
						'<div class="panel-body">'.
							($EDIT ? '<ul class="row_sortable">' : '').
							
							((isset($item['Rows']) AND count((array)$item['Rows'])) ? get_Form_Rows($item['Rows']) : '').

							($EDIT ? '</ul>' : '').

							(($EDIT AND !$HAVE_DATA) 
							? ''.
							'<div style="text-align:center; margin-top:10px;">'.
								'<button type="button" id="accPanel_'.$id.'_newRow" class="newRow hid trans10" data-row="'.$row_item[0].'" data-item="'.$row_item[1].'" data-accRow="'.$acc_no.'"> &nbsp; '.$LANG->FORM_ROW_ADD.'</button>'.
							'</div>' 
							: '').

						'</div>'.
					'</div>'.
				'</div>'.
				
				($EDIT ? '</div></div>' : '');
	}

	return $html;
}

function get_Accordion_Panels(int $rw, mixed $Panels, string $accType):string {
	$html_Panels = '';
	$i = 0;
	foreach ((array)$Panels as $Panel) {
		//0=basic(1 open), 1=multiple --if basic only first can be opened
		if ($accType == '0' and $i != 0) {
			$Panel['open'] = false;
		}
		$html_Panels .= get_Form_Row_Item('_Accordion_Panel', $rw, $Panel, $accType);
		$i++;
	}
	return $html_Panels;
}


function get_Available_Dropdowns(mixed $selected_id):string {
	global $db;
	$dd = ''; 
	$rows = $db->fetch("SELECT id, name FROM dropdowns WHERE status=1 AND name IS NOT NULL ORDER BY name", array()); 
	if ($db->numberRows() > 0)  {
		foreach ($rows as $row) {
			$dd .= '<option value="'.$row['id'].'"'.($selected_id==$row['id']?' selected':'').'>'.$row['name'].'</option>';
		}
	}
	return $dd;
}


function get_Dropdown_Options(mixed $dd, string $val = '', bool $only_vals = false, bool $only_val = false, bool $has_color = false, string $color_a = '120', string $color_b = '0'):mixed {
	global $db;
	$ddn = ''; 
	$only_vals_arr = array(); 
	$only_val_txt = ''; 
	$rows = $db->fetch("SELECT o.id, o.options 
FROM dropdowns d
LEFT JOIN dropdowns o ON o.parent_id = d.id
WHERE d.id=? AND o.status=1 ORDER BY o.options", array($dd)); 
	if ($db->numberRows() > 0)  {
		$arr = array();
		$rows_count = count($rows);
		$color_val_single = 0;
		foreach ($rows as $row) {
			if (substr_count($row['options'], '__') != 0) {
				$tmp = explode('__', $row['options']??'');
				$arr[$tmp[0]] = $tmp[1];
			}
			else {
				$t_color = ($has_color ? get_Color((int)$color_a, (int)$color_b, $rows_count, $color_val_single) : 'none');
				$ddn .=  '<option value="'.$row['options'].'"'.($val==$row['options']?' selected':'').' data-color="'.$t_color.'" style="background-color:'.$t_color.';">'.$row['options'].'</option>';
				$only_vals_arr[$row['options']] = $row['options'];
				if ($val == $row['options']) {
					$only_val_txt = $row['options'];
				}
				$color_val_single++;
			}
		}
		if (count($arr)) {
			ksort($arr);
			$color_val = 0;
			$val_count = count($arr);
			foreach ($arr as $key => $value) {
				$t_color = ($has_color ? get_Color((int)$color_a, (int)$color_b, $val_count, $color_val) : 'none');
				
				$ddn .= '<option value="'.$key.'__'.$value.'"'.
							((("$val"=="$key") OR ($val==$key.'__'.$value))?' selected':'').
							' data-color="'.$t_color.'" style="background-color:'.$t_color.';"'.
						'>'.$value.'</option>';

				$only_vals_arr[$key] = $value;

				if (("$val" == "$key") or ($val == $key . '__' . $value)) {
					$only_val_txt = $key . '__' . $value;
				}
				$color_val++;
			}
		}
	}
	if ($only_val) {
		return $only_val_txt;
	}
	if ($only_vals) {
		return $only_vals_arr;
	}
	return $ddn;
}


function get_Color(int $start, int $end, int $numbers, int $value):string {
	$range = $end - $start;
	$reverse = false;
	if ($range < 0) {
		$range = -$range; //if negative make it positive
		$reverse = true;
	}
	$one_range = $range / ($numbers-1);
	$this_range = $one_range * $value;
	if ($reverse) {
		$hue = $start - $this_range;
	} else {
		$hue = $start + $this_range;
	}
	if ($hue == 360) {
		$hue = 0; //it wants 0 or 359 --else it gives grey
	}
	//echo $start.'--'.$end.'--'.$range.'--'.$reverse.'--'.$one_range.'--'.$this_range.'--'.$hue."<br>";
   	//return "hsl(".$hue.",100%,50%)";
	return convertHSL($hue, 100, 50, true);
}


//https://stackoverflow.com/a/31885018/5833265
/**
 * convert a HSL colorscheme to either Hexadecimal (default) or RGB.
 * 
 * We want a method where we can programmatically generate a series of colors
 * between two values (eg. red to green) which is easy to do with HSL because
 * you just change the hue. (0 = red, 120 = green).  You can use this function
 * to convert those hsl color values to either the rgb or hexadecimal color scheme.
 * e.g. You have
 *   hsl(50, 100%, 50%)
 * To convert,
 * $hex = convertHSL(50,100,50);  // returns #ffd500
 * or 
 * $rgb = convertHSL(50,100,50, false);  // returns rgb(255, 213, 0)
 *  
 * see https://coderwall.com/p/dvsxwg/smoothly-transition-from-green-to-red
 * @param int $h the hue
 * @param int $s the saturation
 * @param int $l the luminance
 * @param bool $toHex whether you want hexadecimal equivalent or rgb equivalent
 * @return string usable in HTML or CSS
 */
function convertHSL(int $h, int $s, int $l, bool $toHex=true):string {
    $h /= 360;
    $s /=100;
    $l /=100;

    $r = $l;
    $g = $l;
    $b = $l;
    $v = ($l <= 0.5) ? ($l * (1.0 + $s)) : ($l + $s - $l * $s);
    if ($v > 0){
        $m = 0;
        $sv = 0;
        $sextant = 0;
        $fract = 0;
		$vsf = 0;
        $mid1 = 0;
        $mid2 = 0;

        $m = $l + $l - $v;
        $sv = ($v - $m ) / $v;
        $h *= 6.0;
        $sextant = floor($h);
        $fract = $h - $sextant;
        $vsf = $v * $sv * $fract;
        $mid1 = $m + $vsf;
        $mid2 = $v - $vsf;

        switch ($sextant) {
            case 0:
                $r = $v;
                $g = $mid1;
                $b = $m;
              break;
            case 1:
                $r = $mid2;
                $g = $v;
                $b = $m;
              break;
            case 2:
				$r = $m;
                $g = $v;
                $b = $mid1;
              break;
            case 3:
                $r = $m;
                $g = $mid2;
                $b = $v;
              break;
            case 4:
                $r = $mid1;
                $g = $m;
                $b = $v;
              break;
            case 5:
                $r = $v;
                $g = $m;
                $b = $mid2;
              break;
        }
    }
    $r = (int)round($r * 255, 0);
    $g = (int)round($g * 255, 0);
    $b = (int)round($b * 255, 0);

    if ($toHex) {
        $r = ($r < 15)? '0' . dechex($r) : dechex($r);
        $g = ($g < 15)? '0' . dechex($g) : dechex($g);
        $b = ($b < 15)? '0' . dechex($b) : dechex($b);
        return "#$r$g$b";
    }
	else {
        return "rgb($r, $g, $b)";    
    }
}


/**
 * Summary of get_ddd
 * @param mixed $CHANGE
 * @param mixed $FORM_DATA
 * @param string $selected_date
 * @return mixed
 */
function get_timestamp_start_end_array($CHANGE, $FORM_DATA, $selected_date) {
	$t_date_time = '';
	$t_date_time_end = '';
	
	if ($CHANGE AND $FORM_DATA) {
		$t_date_time = $FORM_DATA['timestamp_start'];
		$t_date_time_end = $FORM_DATA['timestamp_end'].'';
	}
	else { //init
		if ($selected_date == '') {
			$selected_date = get_date_time_SQL('now');
		}
		if (strlen($selected_date) > 10) {
			$t_date_time = get_date_time_SQL($selected_date);
		}
		else { //rest only date
			$t_minutes = date("i"); //minutes
			if ($t_minutes > 30) $t_minutes = '30';
			else $t_minutes = '00';
			$t_date_time = get_date_SQL($selected_date) . date(" H:").$t_minutes; //.':00';
		}
	}
	$t_date_time = get_date_time_noSecs($t_date_time.'');
	if ($t_date_time_end != '') {
		$t_date_time_end = get_date_time_noSecs($t_date_time_end.'');
	} else {
		$t_date_time_add_1_hour = date("Y-m-d H:i:s", strtotime($t_date_time)+(60*60));
		$t_date_time_end = get_date_time_noSecs($t_date_time_add_1_hour);
	}
	$date_time = explode(' ', $t_date_time);
	$date_time_end = explode(' ', $t_date_time_end);

	return array($date_time[0], $date_time[1], $date_time_end[0], $date_time_end[1]);
}

?>
