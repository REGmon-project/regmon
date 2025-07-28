if (!Production_Mode) "use strict"; //remove on production so "const" works on iOS Safari



function Html_Template__Fieldset_Axis(axis_id, axis_name) {
	return ''+
	'<span class="close_item close_axis" data-val="'+ axis_id +'"></span>'+
	'<fieldset id="'+ axis_id +'" class="coolfieldset axs">'+
		'<legend>'+ axis_name +'</legend>'+
		'<div>'+
			'<div class="form-group">'+
				'<label>'+LANG.RESULTS.Y_AXIS.LABEL+'</label>'+
				'<input type="text" name="axis_name[]" value="" class="form-control" style="width:170px;"/>'+
				'<input type="hidden" name="axis_id[]" value="'+ axis_id +'"/>'+
			'</div> '+
			'<div class="form-group">'+
				'<label>'+LANG.RESULTS.Y_AXIS.POSITION+'</label>'+
				'<select name="axis_pos_sel[]" class="form-control">'+
					'<option value="false">'+LANG.RESULTS.Y_AXIS.POSITION_LEFT+'</option>'+
					'<option value="true">'+LANG.RESULTS.Y_AXIS.POSITION_RIGHT+'</option>'+
				'</select>'+
			'</div> '+
			'<div class="form-group">'+
				'<label>'+LANG.RESULTS.DIAGRAM_COLOR+'</label>'+
				'<span class="fa fa-close color_remove" style="cursor:pointer; position:relative; margin:29px 2px -24px 0; float:right; color:#ddd;"></span>'+
				'<input type="text" name="axis_color[]" value="" class="form-control cpA" style="width:80px; color:white; text-shadow:black 1px 1px;" placeholder="'+LANG.RESULTS.AUTO+'"/>'+
			'</div> '+
			'<div class="form-group">'+
				'<label>'+LANG.RESULTS.Y_AXIS.GRID_WIDTH+'</label>'+
				'<select name="axis_grid_sel[]" class="form-control" style="width:80px;">'+
					'<option value="0">0 px</option>'+
					'<option value="1">1 px</option>'+
					'<option value="2">2 px</option>'+
					'<option value="3">3 px</option>'+
					'<option value="4">4 px</option>'+
					'<option value="5">5 px</option>'+
				'</select>'+
			'</div> '+
			'<div class="form-group">'+
				'<label>'+LANG.RESULTS.Y_AXIS.MIN+'</label>'+
				'<input type="text" name="axis_min[]" value="" class="form-control" style="width:42px;" placeholder="'+LANG.RESULTS.AUTO+'"/>'+
			'</div> '+
			'<div class="form-group">'+
				'<label>'+LANG.RESULTS.Y_AXIS.MAX+'</label>'+
				'<input type="text" name="axis_max[]" value="" class="form-control" style="width:42px;" placeholder="'+LANG.RESULTS.AUTO+'"/>'+
			'</div> '+
		'</div>'+
	'</fieldset>';
} //end Html_Template__Fieldset_Axis

	
function HTML_Template__Fieldset__Athlete(athlete_id, athlete_name) {
	Debug1(' 1.Html.Fieldset.Athlete.', '-', get_Function_Name(), '-', [...arguments]);

	let Button__Fieldset_Athlete__Remove = '';
	let Fieldset__Title = ' (<u>' + athlete_name + '</u>) {' + athlete_id + '}';
	
	const Input__Diagram__Athlete_Name__Show = '<input type="hidden" id="Diagram__Athlete_Name__Show_'+athlete_id+'" value="0">';
		
	let Diagram__Athlete_Name__Show = '' +
		'<label style="font-weight:600;"><i>' + LANG.RESULTS.SHOW_ATHLETE_NAME + ':&nbsp;&nbsp;</i>' +
			'<input type="checkbox" onchange="this.nextSibling.value=this.checked==true?1:0;">'+
			Input__Diagram__Athlete_Name__Show +
		'</label>';
	
	
	if (V_RESULTS_PAGE == 'FORMS_RESULTS') {
		if (!V_ATHLETE) {
			Button__Fieldset_Athlete__Remove = '<span class="close_item Button__Fieldset_Athlete__Remove" data-val="' + athlete_id + '"></span>';
		}
		if (V_ATHLETE || V_IS_IFRAME) {
			Fieldset__Title = '';
			Diagram__Athlete_Name__Show = '' +
				'<label style="height:15px;">' +
					'&nbsp;' + Input__Diagram__Athlete_Name__Show +
				'</label>';
		}
	}
	

	return '' +
		Button__Fieldset_Athlete__Remove+
		'<fieldset id="fs-ATHLETE_'+athlete_id+'" class="coolfieldset fieldset1">'+
			'<legend style="font-size:18px;">'+LANG.RESULTS.DATA+'&nbsp;'+Fieldset__Title+'&nbsp;</legend>'+
			'<div>'+
				'<span class="Fieldsets__Expand_Collapse" title="'+LANG.RESULTS.COLLAPSE_EXPAND_ALL+'"><i class="fa fa-minus-square-o close_all"></i><i class="fa fa-plus-square-o open_all"></i></span>'+
				'<div style="text-align:center; line-height:12px; margin-top:-5px;">'+
					Diagram__Athlete_Name__Show +
					'<input type="hidden" id="Diagram__Athlete_Name_'+athlete_id+'" value="'+athlete_name+'">'+
				'</div>'+
				'<div id="Select__Athlete_Data__Div_'+athlete_id+'" style="padding-top:10px; text-align:center;"">'+
					//here comes the buttons and select to add data
				'</div>'+
			'</div>'+
		'</fieldset>';
} //end HTML_Template__Fieldset__Athlete
	

function HTML_Template__Fieldset__Athlete_Form(base_form_id, form_group_id, form_name, save_id, save_name) {
	Debug1('  2.Html.Fieldset.Form.', '-', get_Function_Name(), '-', [...arguments]);

	let remove_athlete_Form = '';
	let start_collapsed_class = '';
	let start_hidden_style = '';
	let Legend__Form_Name = '';
	let Diagram__Form_Name__Full = '';
	let Button_Calculation_Add = '';
	let Forms_Templates_Actions = '';
	
	if (V_RESULTS_PAGE == 'FORMS_RESULTS') {
		//we have remove only in FORMS_RESULTS page
		remove_athlete_Form = '<span id="Button__Fieldset_Athlete_Form__Remove'+form_group_id+'" class="close_item remove_form"></span>';
		start_collapsed_class = ''; //start expanded
		start_hidden_style = ''; //start visible
		Legend__Form_Name = form_name + (save_id ? ' (' + save_name + ')' : '');
		Diagram__Form_Name__Full = '<input type="hidden" id="Diagram__Form_Name__Full_' + form_group_id + '" value="' + Legend__Form_Name + '">';
		Button_Calculation_Add = HTML_Template__Calculation_Button(form_group_id, save_id);
		Forms_Templates_Actions = HTML_Template__Forms_Templates__Actions(form_group_id, save_id);
	}
	
	else { //V_RESULTS_PAGE == 'RESULTS'
		start_collapsed_class = ' collapsed'; //start collapsed
		start_hidden_style = ' style="display:none;"'; //start hidden
		Legend__Form_Name = form_name;
	}
	
	const Athlete_FORM_Fieldset_Template = ''+
		remove_athlete_Form +
		'<fieldset id="fs-ATHLETE-FORM_'+form_group_id+'" class="coolfieldset fieldset2'+start_collapsed_class+'">'+
			'<legend style="font-size:15px;">'+
				Legend__Form_Name + ' {#F' + form_group_id + '}'+'&nbsp;'+
			'</legend>'+
			'<form id="F'+form_group_id+'"'+start_hidden_style+'>'+
				'<div style="text-align:center; line-height:12px; margin-top:-10px;">'+
					'<label style="vertical-align:text-bottom; font-weight:600;">'+
						'<i>'+LANG.RESULTS.SHOW_FORM_NAME+':&nbsp;&nbsp;</i>'+
						'<input type="checkbox" onchange="this.nextSibling.value=this.checked==true?1:0;">'+
						'<input type="hidden" id="Diagram__Form_Name__Show_'+form_group_id+'" value="0">'+
					'</label>'+
					'<input type="hidden" id="Diagram__Form_Name_'+form_group_id+'" value="'+form_name+'">'+
					Diagram__Form_Name__Full+
				'</div>'+
				'<div id="div-ATHLETE-FORM_'+form_group_id+'">'+
					//here goes the form fields
				'</div>'+
				'<div>'+
				//if not notes form
				(base_form_id == 'note' ? '' :
					//Calculation Button and Template actions
					Button_Calculation_Add +
					Forms_Templates_Actions
				)+
				'</div>'+
				'<div style="clear:both;"></div>' + //for not breaking the table the prev float right
				'<div id="Div__Table__Athlete_Form_'+form_group_id+'" style="margin-top:5px; overflow:auto; background:white;">'+
					'<table id="Table__Athlete_Form_'+form_group_id+'" class="data_table">'+
						//here comes the table
					'</table>'+
				'</div>'+
			'</form>'+
		'</fieldset>';

	return Athlete_FORM_Fieldset_Template;
} //end HTML_Template__Fieldset__Athlete_Form


function HTML_Template__Calculation_Button(form_group_id, save_id) {
	Debug1('     3.Html.Button.', '-', get_Function_Name(), '-', [...arguments]);

	return '' +
		'<button id="Button__Athlete_Form_Field_CALC__Add'+form_group_id+'" type="button" class="btn btn-primary btn-sm" style="padding:2px 10px;">'+
			'<i style="font-size:15px; vertical-align:middle;" class="fa fa-plus"></i>'+
			'&nbsp;&nbsp;<b>'+LANG.RESULTS.CALCULATION+'</b>'+
		'</button>';
		
} //end HTML_Template__Calculation_Button


//Forms Templates Input + Buttons (Save, 2 Dash, Delete)
function HTML_Template__Forms_Templates__Actions(form_group_id, save_id) {
	Debug1('     3.Html.Buttons.', '-', get_Function_Name(), '-', [...arguments]);

	return '' +
		//templates Save/Load/Delete
		'<span style="float:right;">'+						
			//not use form -it breaks the main form --use a span instead
			'<span id="FORM__Forms_Template_Save_'+form_group_id+'" class="save_template" style="position:relative;">'+
				'<input type="text" id="Forms_Template__Save__Name_'+form_group_id+'" value="" placeholder="'+LANG.RESULTS.FORMS_TEMPLATE+'" class="form-control save_name required" notExist=true noData=true />'+
				'<button id="Button__Forms_Template__Save'+form_group_id+'" type="button" class="btn btn-success btn-sm" style="padding:2px 10px;">'+
					'<i style="font-size:15px; vertical-align:middle;" class="fa fa-floppy-o"></i>'+
					'&nbsp;&nbsp;<b>'+LANG.BUTTON_SAVE+'</b>'+
				'</button>'+
			'</span>&nbsp;'+
			'<span style="white-space:nowrap;">'+
			(save_id ? //Save/Delete only a saved one
				'<button id="Button__Forms_Template__2_Dashboard_'+form_group_id+'" type="button" class="btn btn-black btn-md" style="padding:1px 6px;" title="'+LANG.RESULTS.TEMPLATE_DASH_TITLE+'">'+
					'<i class="fa fa-plus-circle" style="vertical-align:middle; margin-top:-1px;"></i>&nbsp;'+
					'<i class="fa fa-th" style="font-size:17px; margin-top:-2px; vertical-align:middle;"></i>'+
				'</button>&nbsp;'+
				'<button id="Button__Forms_Template__Delete'+form_group_id+'" type="button" class="btn btn-danger btn-sm" style="padding:2px 10px;">'+
					'<i style="font-size:15px; vertical-align:middle;" class="fa fa-times-circle"></i>'+
					'&nbsp;&nbsp;<b>'+LANG.BUTTON_DELETE+'</b>'+
				'</button>'
			: '')+
			'</span>'+
		'</span>';
} //end HTML_Template__Forms_Templates__Actions


function HTML_Template__Fieldset__Athlete_Form_Field(data_or_calc, ath_id, base_form_id, field_name, form_field_num, save_id, save_form_id, form_group_id, field_group_id, calx_field_ALPHA_id) {
	Debug1('    3.Html.Fieldset.Field.', '-', get_Function_Name(), '-', [...arguments]);

	let lang_field_name = field_name;
	if (field_name == 'Formula' + form_field_num) {
		lang_field_name = LANG.RESULTS.CALCULATION + '' + form_field_num;
	}
	
	//Remove Fieldset //only for FORMS_RESULTS
	let Button_Fieldset_Athlete_Form_Field_Remove_template = '';
	if (V_RESULTS_PAGE == 'FORMS_RESULTS') {
		Button_Fieldset_Athlete_Form_Field_Remove_template = ''+
			'<span id="Button_Athlete_Form_Field_Remove_'+field_group_id+'" class="close_item remove_form_field" '+
				'data-sel="'+ath_id+'|'+save_form_id+'|'+form_field_num+'" '+
				'data-val="'+data_or_calc+'|'+ath_id+'|'+save_form_id+'|'+form_field_num+'|'+field_name+
			'"></span>';
	}

	//Diagram options
	let Field_options = HTML_Template__Athlete_Form_Field__Diagram_Options(data_or_calc, ath_id, base_form_id, field_name, form_field_num, save_id);

	if (data_or_calc == 'calc') {
		//Formula options
		Field_options += HTML_Template__Athlete_Form_Field__Formula_Options(ath_id, save_form_id, form_field_num, '1', 'lines', '0', '0','0')
	}

	return ''+
		Button_Fieldset_Athlete_Form_Field_Remove_template+
		'<fieldset id="fs-ATHLETE-FORM-FIELD_'+field_group_id+'" class="coolfieldset '+(data_or_calc=='data'?'':'calc calc_')+form_group_id+' collapsed">'+ //start closed
			'<legend>'+
				'<span class="field_name">'+lang_field_name+'</span> {'+calx_field_ALPHA_id+'}'+'&nbsp;'+
			'</legend>'+
			'<div style="display:none;">'+ //start closed
				Field_options+
			'</div>'+
		'</fieldset>';
}


function HTML_Template__Athlete_Form_Field__Diagram_Options(data_or_calc, ath_id, form_id, field_name, form_field_num, save_id) {
	Debug1('    3.Html.Options.Diagram.', '-', get_Function_Name(), '-', [...arguments]);

	//##### form_id here can be base_form_id or save_form_id (3 or 3_S15)

	let base_form_id = form_id;
	let save_form_id = form_id;
	let form_name = '';

	if (save_id) {
		//give save_id to form_id
		save_form_id = base_form_id + '_S' + save_id;
	}

	if (V_RESULTS_PAGE == 'FORMS_RESULTS') {
		form_name = V_FORM_id_2_name[base_form_id];
	}
	else { //RESULTS
		form_name = V_FORMS_N_FIELDS[save_form_id][0];
	}


	let cell_id = '';
	let field_type = '_Number';
	let show_2_graph = true;

	
	if (data_or_calc != 'calc') {
		if (V_RESULTS_PAGE == 'FORMS_RESULTS') {
			//here V_FORMS_DATA not have saves so use base_form_id
			cell_id = V_FORMS_DATA[ath_id][base_form_id]['_' + form_field_num].cell_id;
			field_type = V_FORMS_DATA[ath_id][base_form_id]['_' + form_field_num].type;
		}
		else { //RESULTS
			//here V_FORMS_DATA has saves also -so use save_form_id
			cell_id = V_FORMS_DATA[ath_id][save_form_id]['_' + form_field_num].cell_id;
			field_type = V_FORMS_DATA[ath_id][save_form_id]['_' + form_field_num].type;
		}

		if (V_Not_Show_in_Diagram_Types_arr.indexOf(field_type) > -1) {
			show_2_graph = false;
		}
		if (base_form_id == 'note' && form_field_num == '1') {
			show_2_graph = true; //Note title
		}
		else if (base_form_id == 'note' && form_field_num == '2') {
			show_2_graph = true; //Note period mins
		}
	}


	let lang_field_name = field_name;
	//calculation
	if (field_name == 'Formula' + form_field_num) {
		lang_field_name = LANG.RESULTS.CALCULATION + '' + form_field_num;
		cell_id = Formula__Get_ALPHA_id(data_or_calc, form_field_num, false);
	}

	const select_val = ath_id + '|' + save_form_id + '|' + form_field_num;

	return ''+
		'<div style="text-align:center; line-height:12px; margin-top:-10px;'+(!show_2_graph?'display:none;':'')+'">'+
			'<label style="vertical-align:text-bottom; font-weight:600;">'+
				'<i>'+LANG.RESULTS.SHOW_IN_GRAPHIC+':&nbsp;&nbsp;</i>'+
				'<input type="checkbox" class="data_diagram_show_ck" onchange="this.nextSibling.value=this.checked==true?1:0;"'+((base_form_id=='note' && form_field_num=='2')?'':' checked')+'>'+
				'<input type="hidden" name="data_diagram_show[]" value="'+((base_form_id=='note' && form_field_num=='2')?'0':'1')+'">'+
			'</label>'+
		'</div>'+
		'<div class="diagram_options"'+(!show_2_graph?' style="display:none;"':'')+'>'+
			'<div class="form-group"'+((base_form_id=='note' && form_field_num=='1')?' style="display:none;"':'')+'>'+
				'<label>Name</label>'+
				// '<input type="text" name="data_graph_name[]" value="'+lang_field_name+'" class="form-control" style="'+(V_ATHLETE_TRAINER?'width:250px;':'width:170px;')+'"/>'+
				'<input type="text" name="data_graph_name[]" value="'+lang_field_name+'" class="form-control" style="width:170px;"/>'+
				'<input type="hidden" name="data_select_val[]" value="'+select_val+'"/>'+
				'<input type="hidden" name="data_athlete_id[]" value="'+ath_id+'"/>'+
				'<input type="hidden" name="data_base_form_id[]" value="'+base_form_id+'"/>'+
				'<input type="hidden" name="data_form_id[]" value="'+save_form_id+'"/>'+
				'<input type="hidden" name="data_form_name[]" value="'+form_name+'"/>'+
				'<input type="hidden" name="data_field_name[]" value="'+field_name+'"/>'+
				'<input type="hidden" name="data_field_type[]" value="'+field_type+'"/>'+
				'<input type="hidden" name="data_field_num[]" value="'+(data_or_calc=='calc'?'B':'')+form_field_num+'"/>'+
				'<input type="hidden" name="data_cell_id[]" value="'+cell_id+'"/>'+
				'<input type="hidden" name="data_or_calc[]" value="'+data_or_calc+'"/>'+
			'</div> '+
			'<div class="form-group"'+((base_form_id=='note' && form_field_num=='1')?' style="display:none;"':'')+'>'+
				'<label>'+LANG.RESULTS.DIAGRAM_TYPE.LABEL+'</label>'+
				// '<select name="data_type_sel[]" class="form-control"'+(V_ATHLETE_TRAINER?'':' style="width:140px;"')+'>'+
				'<select name="data_type_sel[]" class="form-control" style="width:140px;">'+
					'<option value="line">'+		LANG.RESULTS.DIAGRAM_TYPE.LINE+'</option>'+
					'<option value="spline">'+		LANG.RESULTS.DIAGRAM_TYPE.SPLINE+'</option>'+
					'<option value="area">'+		LANG.RESULTS.DIAGRAM_TYPE.AREA+'</option>'+
					'<option value="areaspline">'+	LANG.RESULTS.DIAGRAM_TYPE.AREASPLINE+'</option>'+
					'<option value="column">'+		LANG.RESULTS.DIAGRAM_TYPE.COLUMN+'</option>'+
					'<option value="scatter"'+(data_or_calc=='calc'?' selected':'')+'>'+
													LANG.RESULTS.DIAGRAM_TYPE.SCATTER+'</option>'+ //only markers
				'</select>'+
			'</div> '+
			'<div class="form-group"'+((data_or_calc=='calc' || (base_form_id=='note' && form_field_num=='1'))?' style="display:none;"':'')+'>'+
				'<label>'+LANG.RESULTS.LINE_TYPE.LABEL+'</label>'+
				// '<select name="data_line_sel[]" class="form-control"'+(V_ATHLETE_TRAINER?'':' style="width:120px;"')+'>'+
				'<select name="data_line_sel[]" class="form-control" style="width:120px;">'+
					'<option value="Solid">'+			LANG.RESULTS.LINE_TYPE.SOLID+'</option>'+
					'<option value="Dot">'+				LANG.RESULTS.LINE_TYPE.DOT+'</option>'+
					'<option value="ShortDot">'+		LANG.RESULTS.LINE_TYPE.SHORT_DOT+'</option>'+
					'<option value="Dash">'+			LANG.RESULTS.LINE_TYPE.DASH+'</option>'+
					'<option value="ShortDash">'+		LANG.RESULTS.LINE_TYPE.SHORT_DASH+'</option>'+
					'<option value="LongDash">'+		LANG.RESULTS.LINE_TYPE.LONG_DASH+'</option>'+
					'<option value="DashDot">'+			LANG.RESULTS.LINE_TYPE.DASH_DOT+'</option>'+
					'<option value="ShortDashDot">'+	LANG.RESULTS.LINE_TYPE.SHORT_DASH_DOT+'</option>'+
					'<option value="LongDashDot">'+		LANG.RESULTS.LINE_TYPE.LONG_DASH_DOT+'</option>'+
					'<option value="ShortDashDotDot">'+	LANG.RESULTS.LINE_TYPE.SHORT_DASH_DOT_DOT+'</option>'+
					'<option value="LongDashDotDot">'+	LANG.RESULTS.LINE_TYPE.LONG_DASH_DOT_DOT+'</option>'+
				'</select>'+
			'</div> '+
			'<div class="form-group" style="display:none;">'+
				'<label>'+LANG.RESULTS.COLUMN_WIDTH.LABEL+'</label>'+
				'<select name="data_p_range_sel[]" class="form-control" style="width:130px;">'+
					'<option value="0">'+LANG.RESULTS.COLUMN_WIDTH.AUTO+'</option>'+
					'<option value="30">30 '+LANG.RESULTS.COLUMN_WIDTH.MINUTES+'</option>'+
					'<option value="60">1 '+LANG.RESULTS.COLUMN_WIDTH.HOUR+'</option>'+
					'<option value="120">2 '+LANG.RESULTS.COLUMN_WIDTH.HOURS+'</option>'+
					'<option value="180">3 '+LANG.RESULTS.COLUMN_WIDTH.HOURS+'</option>'+
					'<option value="240">4 '+LANG.RESULTS.COLUMN_WIDTH.HOURS+'</option>'+
					'<option value="300">5 '+LANG.RESULTS.COLUMN_WIDTH.HOURS+'</option>'+
					'<option value="360">6 '+LANG.RESULTS.COLUMN_WIDTH.HOURS+'</option>'+
					'<option value="720">12 '+LANG.RESULTS.COLUMN_WIDTH.HOURS+'</option>'+
					'<option value="1440">1 '+LANG.RESULTS.COLUMN_WIDTH.DAY+'</option>'+
					'<option value="2880">2 '+LANG.RESULTS.COLUMN_WIDTH.DAYS+'</option>'+
					'<option value="4320">3 '+LANG.RESULTS.COLUMN_WIDTH.DAYS+'</option>'+
					'<option value="10080">1 '+LANG.RESULTS.COLUMN_WIDTH.WEEK+'</option>'+
					'<option value="20160">2 '+LANG.RESULTS.COLUMN_WIDTH.WEEKS+'</option>'+
					'<option value="30240">3 '+LANG.RESULTS.COLUMN_WIDTH.WEEKS+'</option>'+
					'<option value="40320">4 '+LANG.RESULTS.COLUMN_WIDTH.WEEKS+'</option>'+
				'</select>'+
			'</div> '+
			// '<div class="form-group"'+(V_ATHLETE_TRAINER?' style="display:none;"':'')+'>'+
			'<div class="form-group">'+
				'<label>'+LANG.RESULTS.DIAGRAM_COLOR+'</label>'+
				'<span class="fa fa-close color_remove" style="cursor:pointer; position:relative; margin:29px 2px -24px 0; float:right; color:#ddd;"></span>'+
				'<input type="text" name="data_color[]" value="" class="form-control cp" style="width:80px; color:white; text-shadow:black 1px 1px;" placeholder="'+LANG.RESULTS.AUTO+'" />'+
			'</div> '+
			// '<div class="form-group"'+((V_ATHLETE_TRAINER||data_or_calc=='calc' || (base_form_id=='note' && form_field_num=='1'))?' style="display:none;"':'')+'>'+
			'<div class="form-group"'+((data_or_calc=='calc' || (base_form_id=='note' && form_field_num=='1'))?' style="display:none;"':'')+'>'+
				'<label>'+LANG.RESULTS.POINT_MARKERS.LABEL+'</label>'+
				'<select name="data_markers_sel[]" class="form-control" style="width:100px;">'+
					'<option value="null">'+LANG.RESULTS.POINT_MARKERS.AUTO+'</option>'+ //on hover
					'<option value="true">'+LANG.RESULTS.POINT_MARKERS.YES+'</option>'+
					'<option value="false">'+LANG.RESULTS.POINT_MARKERS.NO+'</option>'+
				'</select>'+
			'</div> '+
			// '<div class="form-group"'+((V_ATHLETE_TRAINER || (base_form_id=='note' && form_field_num=='1'))?' style="display:none;"':'')+'>'+
			'<div class="form-group"'+((base_form_id=='note' && form_field_num=='1')?' style="display:none;"':'')+'>'+
				'<label>'+LANG.RESULTS.DATA_LABELS.LABEL+'</label>'+
				'<select name="data_labels_sel[]" class="form-control">'+
					'<option value="false">'+LANG.RESULTS.DATA_LABELS.NO+'</option>'+
					'<option value="true">'+LANG.RESULTS.DATA_LABELS.YES+'</option>'+
				'</select>'+
			'</div> '+
			// '<div class="form-group"'+((V_ATHLETE_TRAINER || (base_form_id=='note' && form_field_num=='1'))?' style="display:none;"':'')+'>'+
			'<div class="form-group"'+((base_form_id=='note' && form_field_num=='1')?' style="display:none;"':'')+'>'+
				//'<label>Y-Achse</label>'+
				//'<select name="data_axis_sel[]" class="form-control Select_Axis"><option value="0">Y-Achse 1</option></select>'+
		
				//clone from the one in Axis section ---we need hidden class to not include this axis
				$(".axs_sel").clone().html()+
			'</div>'+
		'</div>';
} //end HTML_Template__Athlete_Form_Field__Diagram_Options


//new calculation item value config html
function HTML_Template__Athlete_Form_Field__Formula_Options(ath_id, form_id, form_field_num, formula_cells, formula_period, formula_after, formula_X_axis_show, formula_Full_Period) {
	Debug1('    3.Html.Options.Formula.', '-', get_Function_Name(), '-', [...arguments]);

	//##### form_id here can be base_form_id or save_form_id (3 or 3_S15)

	let formula_disabled = ' disabled'; //only in RESULTS
	if (V_RESULTS_PAGE == 'FORMS_RESULTS') {
		formula_disabled = '';
	}

	const calc_id = ath_id + '_' + form_id + '_' + form_field_num;
	return ''+
		'<div class="calculation_options" style="margin-top:10px;">'+
			'<div class="form-group form-inline-formula" style="margin-top:5px; margin-bottom:0;">'+
				'<label>'+LANG.RESULTS.INTERVAL_1_LINE_PER+': </label> '+
				'<input id="formula_cells_'+calc_id+'" name="formula_cells[]" type="number" min="1" step="1" value="'+formula_cells+'" class="form-control" style="width:70px;" disabled> '+ //start disabled anyway
				'<select id="formula_period_'+calc_id+'" name="formula_period[]" class="form-control"'+formula_disabled+'>'+
					'<option value="lines"'+(formula_period=='lines'?' selected':'')+'>'+LANG.RESULTS.INTERVAL_PERIOD.DATA_LINES+'</option>'+
					'<option value="minutes"'+(formula_period=='minutes'?' selected':'')+'>'+LANG.RESULTS.INTERVAL_PERIOD.MINUTE_S+'</option>'+
					'<option value="hours"'+(formula_period=='hours'?' selected':'')+'>'+LANG.RESULTS.INTERVAL_PERIOD.HOUR_S+'</option>'+
					'<option value="days"'+(formula_period=='days'?' selected':'')+'>'+LANG.RESULTS.INTERVAL_PERIOD.DAY_S+'</option>'+
					'<option value="weeks"'+(formula_period=='weeks'?' selected':'')+'>'+LANG.RESULTS.INTERVAL_PERIOD.WEEK_S+'</option>'+
					'<option value="months"'+(formula_period=='months'?' selected':'')+'>'+LANG.RESULTS.INTERVAL_PERIOD.MONTH_S+'</option>'+
					'<option value="years"'+(formula_period=='years'?' selected':'')+'>'+LANG.RESULTS.INTERVAL_PERIOD.YEAR_S+'</option>'+
				'</select> &nbsp; &nbsp; &nbsp; '+
			'</div> '+
			'<div class="form-group form-inline-formula" style="margin-top:5px; margin-bottom:0;">'+
				'<label>'+LANG.RESULTS.INTERVAL_START_AFTER+': </label> '+
				'<input id="formula_after_'+calc_id+'" name="formula_after[]" type="number" min="0" step="1" value="'+formula_after+'" class="form-control" style="width:50px;"'+formula_disabled+' /> <span id="formula_after_period_txt_'+calc_id+'">'+LANG.RESULTS.INTERVAL_PERIOD.DATA_LINES+'</span> &nbsp; &nbsp; '+
			'</div> '+
			'<div class="form-group form-inline-formula" style="margin-top:3px; margin-bottom:0;">'+
				'<label'+(formula_period=='lines'?' style="color:#aaaaaa;"':'')+'>'+
					'<input type="checkbox" id="formula_X_axis_show_ck_'+calc_id+'" class="formula_X_axis_show_ck" style="vertical-align:text-top;" onchange="this.nextSibling.value=this.checked==true?1:0;"'+(formula_period=='lines'?' disabled':'')+' />'+
					'<input type="hidden" id="formula_X_axis_show_'+calc_id+'" name="formula_X_axis_show[]" value="'+formula_X_axis_show+'" />'+
					'&nbsp;:'+LANG.RESULTS.INTERVAL_EXTRA_X_AXIS+' &nbsp; &nbsp; '+
				'</label>'+
			'</div> '+
			'<div class="form-group form-inline-formula" style="margin-top:3px; margin-bottom:0;'+(formula_period!='lines'?' display:none;':'')+'">'+
				'<label>'+
					'<input type="checkbox" id="formula_Full_Period_ck_'+calc_id+'" class="formula_Full_Period_ck" style="vertical-align:text-top;" onchange="this.nextSibling.value=this.checked==true?1:0;" />'+
					'<input type="hidden" id="formula_Full_Period_'+calc_id+'" name="formula_Full_Period[]" value="'+formula_Full_Period+'" />'+
					'&nbsp;:'+LANG.RESULTS.INTERVAL_FULL_PERIOD+
				'</label>'+
			'</div> '+
			'<div class="form-group has-feedback formula_in" style="width:100%;">'+
				'<label>'+LANG.RESULTS.FORMULA+'</label>'+
				'<div class="input-group">'+
					'<span id="formula_beautify_'+calc_id+'" title="'+LANG.RESULTS.FORMULA_BEAUTIFY+'" class="input-group-addon formula_show_hide"><i class="fa fa-file-code-o"></i></span>'+
					'<span id="formula_refresh_'+calc_id+'" title="'+LANG.RESULTS.FORMULA_CALCULATE+'" class="input-group-addon btn-success formula_show_hide"><i class="fa fa-refresh"></i></span>'+
					'<textarea id="formula_input_'+calc_id+'" class="col-sm-12 form-control formula_input" rows="1" onkeyup="Formula__Beautifier(this.id).update()" style="height:30px;"'+formula_disabled+'></textarea>'+
				'</div>'+
				'<span class="glyphicon glyphicon-warning-sign form-control-feedback hidden" aria-hidden="true"></span>'+
				'<div id="formula_beautify_open_'+calc_id+'" class="formula" style="display:none;">'+
					'<div id="formula_input_'+calc_id+'_out"></div>'+
				'</div>'+
			'</div>'+
		'</div>';
} //end HTML_Template__Athlete_Form_Field__Formula_Options



function HTML_Template__Table__Athlete_Form_Field__Add(ath_id, save_form_id, form_field_name, lang_field_name, form_field_data, form_field_data_type, form_name, form_group_id, data_or_calc, $data_table, data_id, cell_id) {
	Debug1('    3.Html.Table.Field.Add.', '-', get_Function_Name(), '-', [...arguments]);
	
	//Table start collapsed in FORMS_RESULTS and expanded in RESULTS
	let table_start_expanded = '';
	let table_start_collapsed = '';
	if (V_RESULTS_PAGE == 'FORMS_RESULTS') {
		//start collapsed
		table_start_collapsed = ' style="display:none;"';
	}
	else { //RESULTS
		//start expanded
		table_start_expanded = ' style="display:none;"';
	}


	// header #####################################

	// init thead if not exist
	if ($data_table.find('thead tr').length == 0) {
		
		$data_table.append(''+
			'<thead>'+
				'<tr class="thead_row1">'+
					//colspan="11" --we put 4 bcz the rest 7 is hidden
				  '<th class="th_colspan" colspan="4" style="text-align:left;">'+
					'<span class="table_expand_collapse">'+
						'<span class="fa-stack collapse" onclick="Table__Collapse(this);" title="'+LANG.RESULTS.TABLE.COLLAPSE+'"'+table_start_collapsed+'><i class="fa fa-minus-square-o"></i></span>'+
						'<span class="fa-stack expand" onclick="Table__Expand(this);" title="'+LANG.RESULTS.TABLE.EXPAND+'"'+table_start_expanded+'><i class="fa fa-plus-square-o"></i></span>'+
					'</span>'+
					'<span class="table_fullscreen">'+
						'<span class="fa-stack expand" onclick="openFullscreen(this, \'Div__Table__Athlete_Form_'+form_group_id+'\');" title="'+LANG.RESULTS.TABLE.FULL_SCREEN_ON+'"><i class="fa fa-expand"></i></span>'+
						'<span class="fa-stack collapse" onclick="closeFullscreen(this, \'\');" title="'+LANG.RESULTS.TABLE.FULL_SCREEN_OFF+'" style="display:none;"><i class="fa fa-compress"></i></span>'+
					'</span>'+
					' &nbsp; &nbsp; '+form_name+ //table header
				  '</th>'+
				'</tr>'+
				//cell_ids headers
				'<tr class="thead_row2"'+table_start_collapsed+'>'+
					'<th>NN</th>'+
					'<th class="dz_hidden">DZ</th>'+
					'<th>DD</th>'+
					'<th class="dz_hidden">DA</th>'+
					'<th class="dz_hidden">DB</th>'+
					'<th class="dz_hidden">DC</th>'+
					'<th>ZZ</th>'+
					'<th class="dz_hidden">ZA</th>'+
					'<th class="dz_hidden">ZB</th>'+
					'<th class="dz_hidden">ZC</th>'+
					'<th class="th_'+data_id+'">'+cell_id+'</th>'+
				'</tr>'+
				//names headers
				'<tr class="thead_row3"'+table_start_collapsed+'>'+
					'<th>&nbsp;</th>'+
					'<th class="dz_hidden">'+LANG.RESULTS.TABLE.DATE_TIME+'</th>'+
					'<th>'+LANG.RESULTS.TABLE.DATE+'</th>'+
					'<th class="dz_hidden">'+LANG.RESULTS.TABLE.DATE_YEAR+'</th>'+
					'<th class="dz_hidden">'+LANG.RESULTS.TABLE.DATE_MONTH+'</th>'+
					'<th class="dz_hidden">'+LANG.RESULTS.TABLE.DATE_DAY+'</th>'+
					'<th>'+LANG.RESULTS.TABLE.TIME+'</th>'+
					'<th class="dz_hidden">'+LANG.RESULTS.TABLE.TIME_HOUR+'</th>'+
					'<th class="dz_hidden">'+LANG.RESULTS.TABLE.TIME_MINUTE+'</th>'+
					'<th class="dz_hidden">'+LANG.RESULTS.TABLE.TIME_SECOND+'</th>'+
					//we need to have this for changing the name of the field if it changes from input
					'<th class="th_'+data_id+'">'+
						'<span class="field_name" data-name="'+form_field_name+'">'+
							lang_field_name+
						'</span>'+
					'</th>'+
				'</tr>'+
			'</thead>'+
		'');
	}
	// if thead exist
	else {
		//add row2 header
		$data_table.find('thead tr.thead_row2 th').eq(-1).after(
			'<th class="th_' + data_id + '">' + cell_id + '</th>'
		);

		//add row3 header
		$data_table.find('thead tr.thead_row3 th').eq(-1).after(
			//we need to have this for changing the name of the field if it changes
			'<th class="th_' + data_id + '">'+
				'<span class="field_name" data-name="' + form_field_name + '">' + 
					lang_field_name + 
				'</span>'+
			'</th>'
		);

		//fix row1 header colspan
		Table__Athlete_Form__Colspan__Update(ath_id, save_form_id);
	}



	// data #####################################

	let cell_type_css = '';
	if (!(V_Not_Show_in_Diagram_Types_arr.indexOf(form_field_data_type) > -1)) {
		cell_type_css = 'num ';
	}

	let $data_table_tbody = $data_table.find('tbody');
	// init tbody if not exist
	if ($data_table_tbody.length === 0) {
		$data_table.append('<tbody' + table_start_collapsed + '></tbody>');
		$data_table_tbody = $data_table.find('tbody');
		let data_table_tbody_html = '';

		form_field_data.forEach(function(data, index) {
			const timestamp = (data[0] + '').slice(0, -3); //milliseconds to seconds
			const date = moment.unix(timestamp); //.format('YYYY-MM-DD'); //.format('HH:mm:ss')
			const line = index + 1;

			const cell_NN = ['NN' + line, line];
			const cell_DZ = ['DZ' + line, date.format('YYYY-MM-DD HH:mm:ss')];
			const cell_DD = ['DD' + line, date.format('YYYY-MM-DD')];
			const cell_DA = ['DA' + line, date.year()];
			const cell_DB = ['DB' + line, (date.month()+1)];
			const cell_DC = ['DC' + line, date.date()];
			const cell_ZZ = ['ZZ' + line, date.format('HH:mm:ss')];
			const cell_ZA = ['ZA' + line, date.hour()];
			const cell_ZB = ['ZB' + line, date.minute()];
			const cell_ZC = ['ZC' + line, date.second()];
			const data_cell = cell_id + line;

			const vis = DATE__is_in_Range__Get_Visible_Hidden(timestamp);
			data_table_tbody_html += ''+
				'<tr class="'+vis+'" data-timestamp="'+timestamp+'">'+
					'<th data-cell="'+cell_NN[0]+'" title="'+cell_NN[0]+'" class="th_aa '+vis+'">'+cell_NN[1]+'</th>'+
					'<th data-cell="'+cell_DZ[0]+'" title="'+cell_DZ[0]+'" class="th_date dz_hidden">'+cell_DZ[1]+'</th>'+
					'<th data-cell="'+cell_DD[0]+'" title="'+cell_DD[0]+'" class="th_date '+vis+'">'+cell_DD[1]+'</th>'+
					'<th data-cell="'+cell_DA[0]+'" title="'+cell_DA[0]+'" class="th_date dz_hidden">'+cell_DA[1]+'</th>'+
					'<th data-cell="'+cell_DB[0]+'" title="'+cell_DB[0]+'" class="th_date dz_hidden">'+cell_DB[1]+'</th>'+
					'<th data-cell="'+cell_DC[0]+'" title="'+cell_DC[0]+'" class="th_date dz_hidden">'+cell_DC[1]+'</th>'+
					'<th data-cell="'+cell_ZZ[0]+'" title="'+cell_ZZ[0]+'" class="th_time '+vis+'">'+cell_ZZ[1]+'</th>'+
					'<th data-cell="'+cell_ZA[0]+'" title="'+cell_ZA[0]+'" class="th_time dz_hidden">'+cell_ZA[1]+'</th>'+
					'<th data-cell="'+cell_ZB[0]+'" title="'+cell_ZB[0]+'" class="th_time dz_hidden">'+cell_ZB[1]+'</th>'+
					'<th data-cell="'+cell_ZC[0]+'" title="'+cell_ZC[0]+'" class="th_time dz_hidden">'+cell_ZC[1]+'</th>'+
					'<td data-cell="'+data_cell+'" title="'+data_cell+'" class="'+
						//cell class
						(data_or_calc == 'data' ? 'raw ' : 'int ') + cell_type_css + 'td_' + data_id + ' ' + vis+
					'">'+data[1]+'</td>'+
				'</tr>';
		});
		$data_table_tbody.append(data_table_tbody_html);
	}

	// if tbody exist
	else {
		$data_table_tbody.find('tr').each(function (index) {
			const line = index + 1;
			const data_cell = cell_id + line;

			if (form_field_data[index]) {
				const timestamp = (form_field_data[index][0] + '').slice(0, -3); //milliseconds to seconds
				const vis = DATE__is_in_Range__Get_Visible_Hidden(timestamp);

				const data_table_tbody_html = '' +
					'<td ' +
						'data-cell="' + data_cell + '" ' +
						'title="' + data_cell + '" ' +
						'class="' + (data_or_calc == 'data' ? 'raw ' : 'int ') + cell_type_css + 'td_' + data_id + ' ' + vis + '"' +
					'>' +
						((vis == 'vis' || data_or_calc == 'data') ? form_field_data[index][1] : '') +
					'</td>';
				
				$(this).find('td').eq(-1).after(data_table_tbody_html);
				//time consuming operation TCO-DOM @@@@@@@@@@@@@@@@
			}

			//if form_field_data missing 
			else {
				//add an empty td
				const data_table_tbody_html = '' +
					'<td ' +
						'data-cell="' + data_cell + '" ' +
						'title="' + data_cell + '" ' +
						'class="' + (data_or_calc == 'data' ? 'raw ' : 'int ') + cell_type_css + 'td_' + data_id + '"' +
					'>' +
						//'&nbsp;'+
					'</td>'
				$(this).find('td').eq(-1).after(data_table_tbody_html);
			}
		});
	}
}




//############################################################
//INTERVALS ##################################################
//############################################################


function HTML_Template__Fieldset__Interval(interval_id, formula_cells, formula_period, formula_X_axis_show) {
	Debug1('  2.Html.', '-', get_Function_Name(), '-', [...arguments]);

	return ''+		
	'<span class="close_item Button__Fieldset__Interval__Remove" data-val="'+interval_id+'"></span>'+
	'<fieldset id="fs-INTERVAL_'+interval_id+'" class="coolfieldset fieldset3 interval_group" style="margin-bottom:20px;">'+
		'<legend style="font-size:18px;">'+LANG.RESULTS.INTERVAL+'&nbsp;{#FI'+interval_id+'}&nbsp;</legend>'+
		'<div>'+
			'<span class="Fieldsets__Expand_Collapse" title="'+LANG.RESULTS.COLLAPSE_EXPAND_ALL+'"><i class="fa fa-minus-square-o close_all"></i><i class="fa fa-plus-square-o open_all"></i></span>'+
		'</div>'+
		'<form id="FI'+interval_id+'">'+
			'<input type="hidden" id="Diagram__Form_Name_'+interval_id+'" value="'+LANG.RESULTS.INTERVAL+' '+interval_id+'">'+
			'<div style="text-align:center; margin-top:-10px;">'+
				'<div class="form-group form-inline-formula" style="margin-top:5px; margin-bottom:0;">'+
					'<label>'+LANG.RESULTS.INTERVAL_1_LINE_PER+': </label> '+
					'<input id="formula_cells_INT_'+interval_id+'" name="formula_cells[]" type="number" min="1" step="1" value="'+formula_cells+'" class="form-control" style="width:70px;" /> '+
	//------------>>
	'<select id="formula_period_INT_'+interval_id+'" name="formula_period[]" class="form-control">'+
		'<option value="minutes"'+(formula_period=='minutes'?' selected':'')+'>'+LANG.RESULTS.INTERVAL_PERIOD.MINUTE_S+'</option>'+
		'<option value="hours"'	+ (formula_period=='hours'?' selected':'')	+'>'+LANG.RESULTS.INTERVAL_PERIOD.HOUR_S+'</option>'+
		'<option value="days"'	+ (formula_period=='days'?' selected':'')	+'>'+LANG.RESULTS.INTERVAL_PERIOD.DAY_S+'</option>'+
		'<option value="weeks"'	+ (formula_period=='weeks'?' selected':'')	+'>'+LANG.RESULTS.INTERVAL_PERIOD.WEEK_S+'</option>'+
		'<option value="months"'+ (formula_period=='months'?' selected':'')	+'>'+LANG.RESULTS.INTERVAL_PERIOD.MONTH_S+'</option>'+
		'<option value="years"'	+ (formula_period=='years'?' selected':'')	+'>'+LANG.RESULTS.INTERVAL_PERIOD.YEAR_S+'</option>'+
	'</select> &nbsp; &nbsp; &nbsp; '+
	//------------>>
				'</div> '+
				'<div class="form-group form-inline-formula" style="margin-top:3px; margin-bottom:0;">'+
					'<label>'+
						'<input type="checkbox" id="formula_X_axis_show_ck_INT_'+interval_id+'" class="formula_X_axis_show_ck_INT" style="vertical-align:text-top;" onchange="this.nextSibling.value=this.checked==true?1:0;">'+
						'<input type="hidden" id="formula_X_axis_show_INT_'+interval_id+'" name="formula_X_axis_show_INT[]" value="'+formula_X_axis_show+'">'+
						'&nbsp;:'+LANG.RESULTS.INTERVAL_EXTRA_X_AXIS+' &nbsp; &nbsp; '+
					'</label>'+
				'</div> '+
			'</div> '+
			'<hr style="margin:5px 0 10px; border-top:4px dotted #dfdfdf;">'+
			'<div id="div-INTERVAL-FORM_'+interval_id+'">'+
				//here goes the form fields
			'</div>'+
			'<div>'+
				'<button id="Button__Interval_Form_Field__for_RAW_Data__Add_'+interval_id+'" type="button" class="btn btn-primary btn-sm" style="padding:2px 10px;">'+
					'<i style="font-size:15px; vertical-align:middle;" class="fa fa-plus"></i>'+
					'&nbsp;&nbsp;<b>'+LANG.RESULTS.BUTTON_CALC_RAW_DATA+'</b>'+
				'</button>'+
				' &nbsp; '+
				'<button id="Button__Interval_Form_Field__for_INTERVAL_Data__Add_'+interval_id+'" type="button" class="btn btn-primary btn-sm" style="padding:2px 10px;" disabled>'+
					'<i style="font-size:15px; vertical-align:middle;" class="fa fa-plus"></i>'+
					'&nbsp;&nbsp;<b>'+LANG.RESULTS.BUTTON_CALC_INTERVAL+'</b>'+
				'</button>'+
				' &nbsp; '+
				'<button id="Button__Interval_Form_Field__for_INTERVAL_SingleColumn_Data__Add'+interval_id+'" type="button" class="btn btn-primary btn-sm" style="padding:2px 10px;" disabled>'+
					'<i style="font-size:15px; vertical-align:middle;" class="fa fa-plus"></i>'+
					'&nbsp;&nbsp;<b>'+LANG.RESULTS.BUTTON_CALC_INTERVAL_SINGLE+'</b>'+
				'</button>'+
			'</div>'+
			'<div style="clear:both;"></div>'+ //for not break the table the float right
			'<div id="Table__Interval_Form__Div'+interval_id+'" style="margin-top:5px; overflow:auto; background:white;">'+
				'<table id="Table__Interval_Form_'+interval_id+'" class="data_table">'+
					//here comes the table
				'</table>'+
			'</div>'+
		'</form>'+
		
	'</fieldset>';
} //end get_Interval_Data_Fieldset_HTML


//add FIELD to INTERVAL table
function HTML_Template__Table__Interval__Add(interval_id) {
	Debug1('    3.Table.Add.', '-', get_Function_Name(), '-', [...arguments]);

	const $data_table = $('table#Table__Interval_Form_' + interval_id);
	// header
	$data_table.html(''+
		'<thead>'+
			'<tr class="thead_row0"><th class="th_colspan" colspan="7" style="text-align:left;">'+
				'<span class="table_expand_collapse">'+
					'<span class="fa-stack collapse" onclick="Table__Collapse(this);" title="'+LANG.RESULTS.TABLE.COLLAPSE+'"><i class="fa fa-minus-square-o"></i></span>'+
					'<span class="fa-stack expand" onclick="Table__Expand(this);" title="'+LANG.RESULTS.TABLE.EXPAND+'" style="display:none;"><i class="fa fa-plus-square-o"></i></span>'+
				'</span>'+
				'<span class="table_fullscreen">'+
					'<span class="fa-stack expand" onclick="openFullscreen(this, \'Table__Interval_Form__Div'+interval_id+'\');" title="'+LANG.RESULTS.TABLE.FULL_SCREEN_ON+'"><i class="fa fa-expand"></i></span>'+
					'<span class="fa-stack collapse" onclick="closeFullscreen(this, \'\');" title="'+LANG.RESULTS.TABLE.FULL_SCREEN_OFF+'" style="display:none;"><i class="fa fa-compress"></i></span>'+
				'</span>'+
				' &nbsp; &nbsp; '+LANG.RESULTS.INTERVAL+' '+interval_id+
			'</th></tr>'+
			'<tr class="thead_row1">'+
				'<th>&nbsp;</th>'+
				'<th colspan="6" class="brw6">'+LANG.RESULTS.INTERVALS+'</th>'+
			'</tr>'+
			'<tr class="thead_row2">'+
				'<th>&nbsp;</th>'+
				'<th colspan="2">'+LANG.RESULTS.TABLE.INTERVAL_START+'</th>'+
				'<th colspan="2">'+LANG.RESULTS.TABLE.INTERVAL_END+'</th>'+
				'<th colspan="2" class="brw6">'+LANG.RESULTS.TABLE.INTERVAL_MIDDLE+'</th>'+
			'</tr>'+
			'<tr class="thead_row3">'+
				'<th>NN</th>'+
				'<th>DS</th><th>ZS</th>'+
				'<th>DE</th><th>ZE</th>'+
				'<th>DM</th><th class="brw6">ZM</th>'+
			'</tr>'+
			'<tr class="thead_row4">'+
				'<th>&nbsp;</th>'+
				'<th>'+LANG.RESULTS.TABLE.DATE+'</th><th>'+LANG.RESULTS.TABLE.TIME+'</th>'+
				'<th>'+LANG.RESULTS.TABLE.DATE+'</th><th>'+LANG.RESULTS.TABLE.TIME+'</th>'+
				'<th>'+LANG.RESULTS.TABLE.DATE+'</th><th class="brw6">'+LANG.RESULTS.TABLE.TIME+'</th>'+
			'</tr>'+
		'</thead>'+
		'<tbody></tbody>'+
	'');
	
	// data
	const $data_table_tbody = $data_table.find('tbody');

	V_INTERVAL_DATA[interval_id].data.forEach(function(data, index) {
		const line = index + 1;
		const date_start 	= moment(data[0]).format('YYYY-MM-DD');
		const time_start 	= moment(data[0]).format('HH:mm:ss');
		const date_end 		= moment(data[1]).format('YYYY-MM-DD');
		const time_end 		= moment(data[1]).format('HH:mm:ss');
		const date_middle 	= moment(data[2]).format('YYYY-MM-DD');
		const time_middle 	= moment(data[2]).format('HH:mm:ss');
		
		$data_table_tbody.append('' +
			'<tr class="vis" data-timestamp="' + data[2] + '">' +
				'<th data-cell="NN' + line + '" title="NN' + line + '" class="th_aa">' + line + '</th>' +
				'<th data-cell="DS' + line + '" title="DS' + line + '" class="th_date">' + date_start + '</th>' +
				'<th data-cell="ZS' + line + '" title="ZS' + line + '" class="th_time">' + time_start + '</th>' +
				'<th data-cell="DE' + line + '" title="DE' + line + '" class="th_date">' + date_end + '</th>' +
				'<th data-cell="ZE' + line + '" title="ZE' + line + '" class="th_time">' + time_end + '</th>' +
				'<th data-cell="DM' + line + '" title="DM' + line + '" class="th_date">' + date_middle + '</th>' +
				'<th data-cell="ZM' + line + '" title="ZM' + line + '" class="th_time brw6">' + time_middle + '</th>' +
			'</tr>' +
		'');
	});
} //end HTML_Template__Table__Interval__Add



function HTML_Template__Fieldset__Interval_Form_Field(interval_id, field_name, field_num, is_interval_form, is_single_column) {
	Debug1('    3.Html.', '-', get_Function_Name(), '-', [...arguments]);

	const field_id = interval_id + '_' + field_num;
	const calx_field_ALPHA_id = Formula__Interval__Get_ALPHA_id(field_num, false);

	let lang_field_name = field_name;
	if (field_name == 'Formula' + field_num) {
		lang_field_name = LANG.RESULTS.CALCULATION + '' + field_num;
	}


	const form_select = '' +
		'<div class="form-group form-inline-formula">' +
			'<label>' + LANG.RESULTS.INTERVAL_FORM + ': </label> ' +
			'<select id="Interval_Form_' + field_id + '" name="interval_form[]" class="form-control">' +
				Select_Options__RAW_Forms__Get() +
			'</select>'+
			' &nbsp; &nbsp; &nbsp; ' +
		'</div> ' +
		'<div class="form-group form-inline-formula">' +
			'<label>' + LANG.RESULTS.INTERVAL_FIELD + ': </label> ' +
			'<select id="Interval_Form_Field_' + field_id + '" name="interval_form_field[]" class="form-control">' +
				//load later based on selected Form
				//Select_Options__RAW_Form_Fields__Get() +
			'</select>'+
			' &nbsp; &nbsp; &nbsp; ' +
		'</div> ' +
		'<div class="form-group form-inline-formula" title="'+LANG.RESULTS.INTERVAL_USE_IN_FORMULA+'">' +
			'<input type="text" id="Interval_Form_Field_txt_' + field_id + '" name="interval_form_field_txt[]" value="" class="form-control" style="width:80px; text-align:center;" readonly />' +
		'</div>' +
		' &nbsp; &nbsp; &nbsp; ';
		
	
	const main_period = $("#formula_period_INT_" + interval_id).val();
	
	const _Fieldset__Sub_Period = ''+
		//start closed
		'<fieldset id="fs-INTERVAL-FORM-FIELD_'+field_id+'_period" class="coolfieldset collapsed" style="border-width:1px;">'+
			'<legend>'+
				'<label id="fs-INTERVAL-FORM-FIELD_'+field_id+'_period_label" style="font-weight:600; margin-left:-12px; margin-bottom:0;">'+
					'&nbsp;&nbsp;&nbsp;&nbsp;'+LANG.RESULTS.INTERVAL_USE_SUB_PERIOD+':&nbsp;&nbsp;'+
				'</label>'+
				'<input type="checkbox" class="formula_sub_period_INT_ck" style="vertical-align:text-top;" onchange="this.nextSibling.value=this.checked==true?1:0; return false;">'+
				'<input type="hidden" id="formula_sub_period_INT_'+field_id+'" name="formula_sub_period_INT[]" value="0">'+
			'</legend>'+
			//start closed
			'<div style="display:none;">'+
				'<div class="form-group form-inline-formula">'+
					'<label>' + LANG.RESULTS.INTERVAL_PERIOD_LABEL + ': </label> '+
					'<select id="formula_sub_period_Type_INT_'+field_id+'" name="formula_sub_period_Type[]" class="form-control" disabled="">'+
						'<option value="minutes"'+(main_period=='hours'?' selected':'')+'>'+
							LANG.RESULTS.INTERVAL_PERIOD.MINUTE_S+
						'</option>'+
						'<option value="hours"'+(main_period=='days'?' selected':'')+'>'+
							LANG.RESULTS.INTERVAL_PERIOD.HOUR_S+
						'</option>'+
						'<option value="days"'+(main_period=='weeks'||main_period=='months'?' selected':'')+'>'+
							LANG.RESULTS.INTERVAL_PERIOD.DAY_S+
						'</option>'+
						'<option value="months"'+(main_period=='years'?' selected':'')+'>'+
							LANG.RESULTS.INTERVAL_PERIOD.MONTH_S+
						'</option>'+
					'</select>'+
				'</div> '+
				'<div class="form-group form-inline-formula">'+
					' &nbsp; &nbsp; '+
					Select__Interval_Form_Field__Sub_Period(field_id, main_period) +
				'</div>'+
			'</div>'+
		'</fieldset>';
		
	
	const fieldset_class = (is_single_column ? 'Calculation_Interval_SingleColumn' : (is_interval_form ? 'Calculation_Interval' : 'Calculation_Raw'));
	const fieldset_mark = (is_single_column ? 'I(E)' : (is_interval_form ? 'I' : 'R'));
	
	const _Fieldset__Interval_Form_Field = '' +
		//remove_INT_form_field
		'<span id="Button__Interval_Form_Field__Remove_'+ field_id +'" class="close_item remove_form_field" data-val="'+interval_id+'|'+field_num+'"></span>'+
		//start closed
		'<fieldset id="fs-INTERVAL-FORM-FIELD_'+ field_id +'" class="coolfieldset collapsed '+ fieldset_class +'">'+
			'<legend>'+
				'<span class="field_name">'+ lang_field_name +'</span>'+
				' {'+ calx_field_ALPHA_id +'} &nbsp;'+
				'<span class="super_int"><sup>'+ fieldset_mark +'</sup></span>'+
			'</legend>'+
			'<div style="display:none;">'+ //start closed
				'<div class="form_options"">'+
					(is_interval_form ? '' : form_select)+
					'<div '+(is_interval_form ? 'style="text-align:center; line-height:12px; margin-top:-10px;"' : 'class="form-group form-inline-formula"')+'>'+
						'<label style="font-weight:600;">'+
							'<i>'+LANG.RESULTS.SHOW_IN_GRAPHIC+':&nbsp;&nbsp;</i>'+
							'<input type="checkbox" class="data_diagram_show_ck" style="vertical-align:text-top;" onchange="this.nextSibling.value=this.checked==true?1:0;" checked>'+
							'<input type="hidden" name="data_diagram_show[]" value="1">'+
						'</label>'+
						'&nbsp;&nbsp;&nbsp;&nbsp;'+
						'<label style="font-weight:600;">'+
							'<i>'+LANG.RESULTS.SHOW_ATHLETE_NAME+':&nbsp;&nbsp;</i>'+
							'<input type="checkbox" style="vertical-align:text-top;" onchange="this.nextSibling.value=this.checked==true?1:0;" checked>'+
							'<input type="hidden" name="data_diagram_ath_name_show[]" value="1">'+
						'</label>'+
					'</div>'+
				'</div>'+
				
		
				(is_interval_form ? '' : '<hr style="margin:3px 0 5px; border-top:2px dotted #dfdfdf;">')+
				
		
				HTML_Template__Interval_Form_Field__Diagram_Options(interval_id, field_name, field_num, is_interval_form, is_single_column)+
				
		
				((!is_interval_form && main_period != 'minutes') ? _Fieldset__Sub_Period : '')+
				
		
				HTML_Template__Interval_Form_Field__Formula_Options(interval_id, field_num, is_interval_form, is_single_column)+
				
		
			'</div>'+
		'</fieldset>';
	
	return _Fieldset__Interval_Form_Field;
} //end HTML_Template__Fieldset__Interval_Form_Field



//new interval data items values diagram config html
function HTML_Template__Interval_Form_Field__Diagram_Options(interval_id, field_name, field_num, is_interval_form, is_single_column) {
	Debug1('    3.Html.3.', '-', get_Function_Name(), '-', [...arguments]);

	//const data_or_calc = 'calc';
	//const field_type = '_Number';
	
	let lang_field_name = field_name;
	if (field_name == 'Formula' + field_num) {
		lang_field_name = LANG.RESULTS.CALCULATION + '' + field_num;
	}

	return ''+
		'<div class="diagram_options">'+
			'<div class="form-group">'+
				'<label>'+LANG.RESULTS.DIAGRAM_FIELD_NAME+'</label>'+
				'<input type="text" name="data_graph_name[]" value="'+lang_field_name+'" class="form-control" style="width:170px;"/>'+
				'<input type="hidden" name="data_select_val[]" value="'+interval_id+'|'+field_num+'"/>'+ //select_val
				'<input type="hidden" name="data_int_id[]" value="'+interval_id+'"/>'+
				'<input type="hidden" name="data_field_id[]" value="'+field_num+'"/>'+
				//'<input type="hidden" name="data_form_id[]" value="'+form_id+'"/>'+
				//'<input type="hidden" name="data_form_name[]" value="'+V_FORMS_N_FIELDS[form_id][0]+'"/>'+
				'<input type="hidden" name="data_field_name[]" value="'+field_name+'"/>'+
				//'<input type="hidden" name="data_field_type[]" value="'+field_type+'"/>'+
				//'<input type="hidden" name="data_or_calc[]" value="'+data_or_calc+'"/>'+
				'<input type="hidden" name="is_interval_form[]" value="'+is_interval_form+'"/>'+
				'<input type="hidden" name="is_single_column[]" value="'+is_single_column+'"/>'+
				
				//we give the fields that not exist if it is interval_form
				//INTSC
				(is_interval_form && is_single_column ? '<input type="hidden" name="interval_form[]" value="0"/>' : '')+
				//INT
				(is_interval_form && !is_single_column
					? '<input type="hidden" name="interval_form[]" value="0"/>'+
					  '<input type="hidden" name="formula_individual[]" value="0"/>' 
					: ''
				)+
				//RAW
				(!is_interval_form ? '<input type="hidden" name="formula_individual[]" value="0"/>' : '')+
				//INT + INTSC
				(is_interval_form ?
					'<input type="hidden" name="formula_sub_period_INT[]" value="0"/>'+
					'<input type="hidden" name="formula_sub_period_Type[]" value="0"/>'+
					'<input type="hidden" name="formula_sub_period_start[]" value="0"/>'+
					'<input type="hidden" name="formula_sub_period_end[]" value="0"/>'
				: '')+ //raw
				
			'</div> '+
			'<div class="form-group">'+
				'<label>'+LANG.RESULTS.DIAGRAM_TYPE.LABEL+'</label>'+
				'<select name="data_type_sel[]" class="form-control" style="width:140px;">'+
					'<option value="line">'+			LANG.RESULTS.DIAGRAM_TYPE.LINE+'</option>'+
					'<option value="spline">'+			LANG.RESULTS.DIAGRAM_TYPE.SPLINE+'</option>'+
					'<option value="area">'+			LANG.RESULTS.DIAGRAM_TYPE.AREA+'</option>'+
					'<option value="areaspline">'+		LANG.RESULTS.DIAGRAM_TYPE.AREASPLINE+'</option>'+
					'<option value="column">'+			LANG.RESULTS.DIAGRAM_TYPE.COLUMN+'</option>'+
					'<option value="scatter" selected>'+LANG.RESULTS.DIAGRAM_TYPE.SCATTER+'</option>'+ //only markers
				'</select>'+
			'</div> '+
			'<div class="form-group" style="display:none;">'+
				'<label>'+LANG.RESULTS.LINE_TYPE.LABEL+'</label>'+
				'<select name="data_line_sel[]" class="form-control" style="width:120px;">'+
					'<option value="Solid">'+			LANG.RESULTS.LINE_TYPE.SOLID+'</option>'+
					'<option value="Dot">'+				LANG.RESULTS.LINE_TYPE.DOT+'</option>'+
					'<option value="ShortDot">'+		LANG.RESULTS.LINE_TYPE.SHORT_DOT+'</option>'+
					'<option value="Dash">'+			LANG.RESULTS.LINE_TYPE.DASH+'</option>'+
					'<option value="ShortDash">'+		LANG.RESULTS.LINE_TYPE.SHORT_DASH+'</option>'+
					'<option value="LongDash">'+		LANG.RESULTS.LINE_TYPE.LONG_DASH+'</option>'+
					'<option value="DashDot">'+			LANG.RESULTS.LINE_TYPE.DASH_DOT+'</option>'+
					'<option value="ShortDashDot">'+	LANG.RESULTS.LINE_TYPE.SHORT_DASH_DOT+'</option>'+
					'<option value="LongDashDot">'+		LANG.RESULTS.LINE_TYPE.LONG_DASH_DOT+'</option>'+
					'<option value="ShortDashDotDot">'+	LANG.RESULTS.LINE_TYPE.SHORT_DASH_DOT_DOT+'</option>'+
					'<option value="LongDashDotDot">'+	LANG.RESULTS.LINE_TYPE.LONG_DASH_DOT_DOT+'</option>'+
				'</select>'+
			'</div> '+
			'<div class="form-group" style="display:none;">'+
				'<label>'+LANG.RESULTS.COLUMN_WIDTH.LABEL+'</label>'+
				'<select name="data_p_range_sel[]" class="form-control" style="width:130px;">'+
					'<option value="0">'+		LANG.RESULTS.COLUMN_WIDTH.AUTO+'</option>'+
					'<option value="30">30 '+	LANG.RESULTS.COLUMN_WIDTH.MINUTES+'</option>'+
					'<option value="60">1 '+	LANG.RESULTS.COLUMN_WIDTH.HOUR+'</option>'+
					'<option value="120">2 '+	LANG.RESULTS.COLUMN_WIDTH.HOURS+'</option>'+
					'<option value="180">3 '+	LANG.RESULTS.COLUMN_WIDTH.HOURS+'</option>'+
					'<option value="240">4 '+	LANG.RESULTS.COLUMN_WIDTH.HOURS+'</option>'+
					'<option value="300">5 '+	LANG.RESULTS.COLUMN_WIDTH.HOURS+'</option>'+
					'<option value="360">6 '+	LANG.RESULTS.COLUMN_WIDTH.HOURS+'</option>'+
					'<option value="720">12 '+	LANG.RESULTS.COLUMN_WIDTH.HOURS+'</option>'+
					'<option value="1440">1 '+	LANG.RESULTS.COLUMN_WIDTH.DAY+'</option>'+
					'<option value="2880">2 '+	LANG.RESULTS.COLUMN_WIDTH.DAYS+'</option>'+
					'<option value="4320">3 '+	LANG.RESULTS.COLUMN_WIDTH.DAYS+'</option>'+
					'<option value="10080">1 '+	LANG.RESULTS.COLUMN_WIDTH.WEEK+'</option>'+
					'<option value="20160">2 '+	LANG.RESULTS.COLUMN_WIDTH.WEEKS+'</option>'+
					'<option value="30240">3 '+	LANG.RESULTS.COLUMN_WIDTH.WEEKS+'</option>'+
					'<option value="40320">4 '+	LANG.RESULTS.COLUMN_WIDTH.WEEKS+'</option>'+
				'</select>'+
			'</div> '+
			'<div class="form-group">'+
				'<label>'+LANG.RESULTS.DIAGRAM_COLOR+'</label>'+
				'<span class="fa fa-close color_remove" style="cursor:pointer; position:relative; margin:29px 2px -24px 0; float:right; color:#ddd;"></span>'+
				'<input type="text" name="data_color[]" value="" class="form-control cp" style="width:80px; color:white; text-shadow:black 1px 1px;" placeholder="'+LANG.RESULTS.AUTO+'" />'+
			'</div> '+
			'<div class="form-group" style="display:none;">'+
				'<label>'+LANG.RESULTS.POINT_MARKERS.LABEL+'</label>'+
				'<select name="data_markers_sel[]" class="form-control" style="width:100px;">'+
					'<option value="null">'+ LANG.RESULTS.POINT_MARKERS.AUTO+'</option>'+ //on hover
					'<option value="true">'+ LANG.RESULTS.POINT_MARKERS.YES+'</option>'+
					'<option value="false">'+LANG.RESULTS.POINT_MARKERS.NO+'</option>'+
				'</select>'+
			'</div> '+
			'<div class="form-group">'+
				'<label>'+LANG.RESULTS.DATA_LABELS.LABEL+'</label>'+
				'<select name="data_labels_sel[]" class="form-control">'+
					'<option value="false">'+LANG.RESULTS.DATA_LABELS.NO+'</option>'+
					'<option value="true">'+ LANG.RESULTS.DATA_LABELS.YES+'</option>'+
				'</select>'+
			'</div> '+
			'<div class="form-group">'+
				//clone from the one in Axis section ---we need hidden class to not include this axis
				$(".axs_sel").clone().html()+
			'</div>'+
		'</div>';
} //HTML_Template__Interval_Form_Field__Diagram_Options


//new calculation item value config html
function HTML_Template__Interval_Form_Field__Formula_Options(interval_id, field_num, is_interval_form, is_single_column) {
	Debug1('    3.Html.4.', '-', get_Function_Name(), '-', [...arguments]);

	const calc_id = interval_id + '_' + field_num;

	return ''+
		'<div class="calculation_options">'+
			'<div class="form-group has-feedback formula_in" style="width:100%;">'+
				'<label>'+LANG.RESULTS.FORMULA+'</label>'+
			((is_interval_form && is_single_column) ? 
				'<label style="font-weight:600; padding-left:35px;">'+
					'<i>'+LANG.RESULTS.FORMULA_INDIVIDUAL+'?:&nbsp;&nbsp;</i>'+
					'<input type="checkbox" class="formula_individual_ck" style="vertical-align:text-top;" onchange="this.nextSibling.value=this.checked==true?1:0;">'+
					'<input type="hidden" id="formula_individual_'+calc_id+'" name="formula_individual[]" value="0">'+
					' &nbsp; <i style="color:gray;">{BAA}</i>'+
				'</label>'
			: '')+
				'<div class="input-group">'+
					'<span id="formula_beautify_'+calc_id+'" title="'+LANG.RESULTS.FORMULA_BEAUTIFY+'" class="input-group-addon formula_show_hide"><i class="fa fa-file-code-o"></i></span>'+
					'<span id="formula_refresh_'+calc_id+'" title="'+LANG.RESULTS.FORMULA_CALCULATE+'" class="input-group-addon btn-success formula_show_hide"><i class="fa fa-refresh"></i></span>'+
					'<textarea id="formula_input_'+calc_id+'" name="formula_input[]" class="col-sm-12 form-control formula_input" rows="1" onkeyup="Formula__Beautifier(this.id).update()" style="height:30px;"></textarea>'+
				'</div>'+
				'<span class="glyphicon glyphicon-warning-sign form-control-feedback hidden" aria-hidden="true"></span>'+
				'<div id="formula_beautify_open_'+calc_id+'" class="formula" style="display:none;">'+
					'<div id="formula_input_'+calc_id+'_out"></div>'+
				'</div>'+
			'</div>'+
		'</div>';
} //HTML_Template__Interval_Form_Field__Formula_Options




