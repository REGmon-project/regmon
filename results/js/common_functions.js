if (!Production_Mode) "use strict"; //remove on production so "const" works on iOS Safari

var V_ColumnName_2_ColumnId = {};
var V_ColumnName_2_ColumnId__Sorted = {};

//this used a lot to make minor changes in functions
if (V_RESULTS_PAGE == 'FORMS_RESULTS') {
}
else { //RESULTS
}


//####################################################
// HELPERS ###########################################

function numRound(num) {
	return Math.round(num*100)/100;
}


//for ESC exit Fullscreen workaround
if (document.addEventListener) {
	document.addEventListener('webkitfullscreenchange', checkFullscreen, false);
	document.addEventListener('mozfullscreenchange', checkFullscreen, false);
	document.addEventListener('fullscreenchange', checkFullscreen, false);
	document.addEventListener('MSFullscreenChange', checkFullscreen, false);
}

function checkFullscreen() {
	//console.log('checkFullscreen', document.fullscreenElement);
	if (!document.fullscreenElement &&
		!document.mozFullScreenElement &&
		!document.webkitFullscreenElement &&
		!document.msFullscreenElement)
	{
		//closeFullscreen(); 
		$('.fa-stack.expand').show(); //we do all
		$('.fa-stack.collapse').hide();
		//console.log('close');
	}
	else {
		$('.fa-stack.expand').hide();
		$('.fa-stack.collapse').show();
		//console.log('open');
	}
}

function openFullscreen(el, el_full) {
	Debug1('X.JS.Click.', '-', get_Function_Name(), '-', [...arguments]);

	let elem = document.getElementById(el_full); 
	if (elem.requestFullscreen) {
		elem.requestFullscreen();
	} else if (elem.mozRequestFullScreen) { //Firefox
		elem.mozRequestFullScreen();
	} else if (elem.webkitRequestFullscreen) { //Chrome, Safari & Opera
		elem.webkitRequestFullscreen();
	} else if (elem.msRequestFullscreen) { //IE/Edge
		elem.msRequestFullscreen();
	}
	if (el_full == 'C_Diagramm_div') {
		$('#container_graph').css('height', '100%');
	}
}

function closeFullscreen(el, el_full) {
	Debug1('X.JS.Click.', '-', get_Function_Name(), '-', [...arguments]);

	if (document.exitFullscreen) {
		document.exitFullscreen();
	} else if (document.mozCancelFullScreen) {
		document.mozCancelFullScreen();
	} else if (document.webkitExitFullscreen) {
		document.webkitExitFullscreen();
	} else if (document.msExitFullscreen) {
		document.msExitFullscreen();
	}
	//if (el_full == 'C_Diagramm_div') {} //do it anyway
	$('#container_graph').css('height', '500px');
}


function Object__Sort_By_Length__Reverse(obj) {
	let ordered_by_length = {};
	Object.keys(obj).sort(function(a,b){
		return Object.keys(b).length - Object.keys(a).length;
	}).forEach(function(key) {
		ordered_by_length[key] = obj[key];
	});

	return ordered_by_length;
}


function collapse_set(el) {
	$(el).collapsible();
	//$(el+" legend").trigger("click"); //close
}


// SCROLL TO Module ==============
function animateToDiv(elem, open) {
	$("html, body").animate({
		scrollTop: $(elem).offset().top
	}, "slow");
	if (open) {
		if (!$(open).hasClass('in')) {
			$(elem).trigger("click");
		}
	}
}

// HELPERS ###########################################
//####################################################



//####################################################
// TEMPLATES #########################################

function DATA__Athlete_Form__Get(form_group_id, remove_athlete_id = false) {
	Debug1('X.Json.Form.', '-', get_Function_Name(), '-', [...arguments]);
	
	//Data
	const data_graph_name 	= $("#F" + form_group_id + " input[name='data_graph_name[]']");
	const data_select_val 	= $("#F" + form_group_id + " input[name='data_select_val[]']");
	const data_athlete_id 	= $("#F" + form_group_id + " input[name='data_athlete_id[]']");
	const data_base_form_id	= $("#F" + form_group_id + " input[name='data_base_form_id[]']");
	const data_form_id 		= $("#F" + form_group_id + " input[name='data_form_id[]']");
	const data_form_name	= $("#F" + form_group_id + " input[name='data_form_name[]']");
	const data_field_name 	= $("#F" + form_group_id + " input[name='data_field_name[]']");
	const data_field_type 	= $("#F" + form_group_id + " input[name='data_field_type[]']");
	const data_field_num 	= $("#F" + form_group_id + " input[name='data_field_num[]']");
	const data_cell_id 		= $("#F" + form_group_id + " input[name='data_cell_id[]']");
	const data_or_calc 		= $("#F" + form_group_id + " input[name='data_or_calc[]']");
	const data_diagram_show = $("#F" + form_group_id + " input[name='data_diagram_show[]']");
	const data_type 		= $("#F" + form_group_id + " select[name='data_type_sel[]']");
	const data_line 		= $("#F" + form_group_id + " select[name='data_line_sel[]']");
	const data_p_range		= $("#F" + form_group_id + " select[name='data_p_range_sel[]']");
	const data_color 		= $("#F" + form_group_id + " input[name='data_color[]']");
	const data_markers		= $("#F" + form_group_id + " select[name='data_markers_sel[]']");
	const data_labels 		= $("#F" + form_group_id + " select[name='data_labels_sel[]']");
	const data_axis 		= $("#F" + form_group_id + " select[name='data_axis_sel[]']");
	const data_num 			= data_graph_name.length;
	
	const data_arr = {};

	for (let i = 0; i < data_num; i++) {

		const field_num = $(data_field_num[i]).val();
		const ath_id 	= $(data_athlete_id[i]).val();
		let sel_val = $(data_select_val[i]).val();
		const obj = {
			name: 		$(data_graph_name[i]).val(),
			sel_val: 	sel_val,
			//ath_id: 	ath_id,
			base_form_id:$(data_base_form_id[i]).val(),
			form_id: 	$(data_form_id[i]).val(),
			form_name: 	$(data_form_name[i]).val(),
			field_name: $(data_field_name[i]).val(),
			field_type: $(data_field_type[i]).val(),
			field_num: 	$(data_field_num[i]).val(),
			cell_id: 	$(data_cell_id[i]).val(),
			data_or_calc: $(data_or_calc[i]).val(),
			show: 		$(data_diagram_show[i]).val(),
			type: 		$(data_type[i]).val(),
			line: 		$(data_line[i]).val(),
			p_range: 	$(data_p_range[i]).val(),
			color: 		$(data_color[i]).val(),
			markers: 	$(data_markers[i]).val(),
			labels: 	$(data_labels[i]).val(),
			axis: 		$(data_axis[i]).val()
		};

		//athlete_id + sel_val
		if (remove_athlete_id) { //remove ath_is for templates
			//remove ath_id from sel_val
			sel_val = ('--' + sel_val).replace('--' + ath_id + '|', '');
			
			//set sel_val
			obj.sel_val = sel_val;
			
			//not set ath_id
		}
		else {
			//set ath_id
			obj.ath_id = ath_id;
		}

		if ($(data_or_calc[i]).val() == 'calc') {
			const calc_id = replaceAll($(data_select_val[i]).val(), '|', '_');
			obj.formula_cells 		= $('#formula_cells_' + calc_id).val();
			obj.formula_period 		= $('#formula_period_' + calc_id).val();
			obj.formula_after 		= $('#formula_after_' + calc_id).val();
			obj.formula_X_axis_show = $('#formula_X_axis_show_' + calc_id).val();
			obj.formula_Full_Period = $('#formula_Full_Period_' + calc_id).val();
			obj.formula_input 		= $('#formula_input_' + calc_id).val();
		}
		data_arr['_' + field_num] = obj;
	}

	return data_arr;
}


function DATA__Interval_Form__Get(interval_id) {
	Debug1('X.Json.INT.', '-', get_Function_Name(), '-', [...arguments]);
	
	//Data main
	const data_graph_name 	= $("#FI" + interval_id + " input[name='data_graph_name[]']");
	const data_select_val 	= $("#FI" + interval_id + " input[name='data_select_val[]']");
	//const data_athlete_id 	= $("#FI" + interval_id + " input[name='data_athlete_id[]']");
	//const data_base_form_id 	= $("#FI" + interval_id + " input[name='data_base_form_id[]']");
	//const data_form_id 		= $("#FI" + interval_id + " input[name='data_form_id[]']");
	//const data_form_name 		= $("#FI" + interval_id + " input[name='data_form_name[]']");
	const data_field_name 		= $("#FI" + interval_id + " input[name='data_field_name[]']");
	//const data_field_type		= $("#FI" + interval_id + " input[name='data_field_type[]']");
	//const data_field_num 		= $("#FI" + interval_id + " input[name='data_field_num[]']");
	//const data_cell_id 		= $("#FI" + interval_id + " input[name='data_cell_id[]']");
	//const data_or_calc 		= $("#FI" + interval_id + " input[name='data_or_calc[]']");
	const data_diagram_show 	= $("#FI" + interval_id + " input[name='data_diagram_show[]']");
	const data_type 		= $("#FI" + interval_id + " select[name='data_type_sel[]']");
	const data_line 		= $("#FI" + interval_id + " select[name='data_line_sel[]']");
	const data_p_range		= $("#FI" + interval_id + " select[name='data_p_range_sel[]']");
	const data_color 		= $("#FI" + interval_id + " input[name='data_color[]']");
	const data_markers		= $("#FI" + interval_id + " select[name='data_markers_sel[]']");
	const data_labels 		= $("#FI" + interval_id + " select[name='data_labels_sel[]']");
	const data_axis 		= $("#FI" + interval_id + " select[name='data_axis_sel[]']");
	//int extra
	const data_int_id 		= $("#FI" + interval_id + " input[name='data_int_id[]']");
	const data_field_id 	= $("#FI" + interval_id + " input[name='data_field_id[]']");
	const data_diagram_ath_name_show = $("#FI" + interval_id + " input[name='data_diagram_ath_name_show[]']");
	
	const data_is_single_column 	= $("#FI" + interval_id + " input[name='is_single_column[]']");
	const data_is_interval_form 	= $("#FI" + interval_id + " input[name='is_interval_form[]']");
	const data_interval_form 		= $("#FI" + interval_id + " [name='interval_form[]']");
	const data_formula_individual 	= $("#FI" + interval_id + " input[name='formula_individual[]']");
	const data_formula_input 		= $("#FI" + interval_id + " textarea[name='formula_input[]']");

	const data_formula_sub_period 		= $("#FI" + interval_id + " input[name='formula_sub_period_INT[]']");
	const data_formula_sub_period_type 	= $("#FI" + interval_id + " select[name='formula_sub_period_Type[]']");
	const data_formula_sub_period_start = $("#FI" + interval_id + " select[name='formula_sub_period_start[]']");
	const data_formula_sub_period_end 	= $("#FI" + interval_id + " select[name='formula_sub_period_end[]']");
	
	//build interval obj
	const data_arr = {
		formula_cells 		: $('#formula_cells_INT_' + interval_id).val(),
		formula_period 		: $('#formula_period_INT_' + interval_id).val(),
		formula_X_axis_show : $('#formula_X_axis_show_INT_' + interval_id).val(),
		data 				: {}
	};
	
	const data_num = data_graph_name.length;

	for (let i = 0; i < data_num; i++) {
		const obj = {
			int_id: 	$(data_int_id[i]).val(), //
			name: 		$(data_graph_name[i]).val(),
			sel_val: 	$(data_select_val[i]).val(),
			field_id: 	$(data_field_id[i]).val(), //
			field_name: $(data_field_name[i]).val(),
			show: 		$(data_diagram_show[i]).val(),
			show_name: 	$(data_diagram_ath_name_show[i]).val(), //
			type: 		$(data_type[i]).val(),
			line: 		$(data_line[i]).val(),
			p_range: 	$(data_p_range[i]).val(),
			color: 		$(data_color[i]).val(),
			markers: 	$(data_markers[i]).val(),
			labels: 	$(data_labels[i]).val(),
			axis: 		$(data_axis[i]).val(),
			//int extra
			is_single_column: 	$(data_is_single_column[i]).val(),
			is_interval_form: 	$(data_is_interval_form[i]).val(),
			interval_form: 		$(data_interval_form[i]).val(),
			interval_form_name: $(data_interval_form[i]).find('option:selected').text(),
			formula_individual: $(data_formula_individual[i]).val(),
			formula_input: 		$(data_formula_input[i]).val(),
			formula_sub_period: $(data_formula_sub_period[i]).val()
		};

		//if formula sub period
		if ($(data_formula_sub_period[i]).val() == '1') {
			obj.formula_sub_period_type = $(data_formula_sub_period_type[i]).val();
			obj.formula_sub_period_start= $(data_formula_sub_period_start[i]).val();
			obj.formula_sub_period_end 	= $(data_formula_sub_period_end[i]).val();
		}

		const field_id = $(data_field_id[i]).val();
		data_arr.data['_' + field_id] = obj;
	}

	return data_arr;
}


function Forms_Template__Form_Fields__Load(ath_id, base_form_id, Forms_Template_Data, save_id, save_name) {
	Debug1('********************************************');
	Debug1('  2.Template.Forms.Load.', '-', get_Function_Name(), '-', [...arguments]);

	//add data
	$.each(Forms_Template_Data, function (i, template_data) {
		const form_group_id = ath_id + '_' + base_form_id + '_S' + save_id;
		const field_name = template_data.field_name;
		const field_num = template_data.field_num;
		const data_or_calc = template_data.data_or_calc;
		//data
		if (data_or_calc == 'data') {
			Athlete_Form_Field_DATA__Add(ath_id, base_form_id, field_num, field_name, save_id, save_name);
		}
		//calculation
		else {
			Athlete_Form_Field_CALC__Add(ath_id, base_form_id, base_form_id + '_S' + save_id, form_group_id, save_id, field_num);
		}
	});

	//we not do that here --we do it after this function
	//Forms_Template__Form_Fields__Values__Set(ath_id, base_form_id, save_id, Forms_Template_Data, 'form');

} //end Forms_Template__Form_Fields__Load


//set Saved FORM Values -- here only put saved values back to form
//we give Saved_Data so we can use it for all the following
//1. V_Selected__Data__Forms__Changes
//2. V_FORMS_TEMPLATES
//3. V_RESULTS_TEMPLATES
function Forms_Template__Form_Fields__Values__Set(ath_id, form_id, save_id, Saved_Data, type) {
	Debug1('  2.Json2Table.', '-', get_Function_Name(), '-', [...arguments]);

	let form_group_id = ath_id + '_' + form_id;
	
	if (save_id) {
		form_group_id = ath_id + '_' + form_id + '_S' + save_id;
	}

	//Data fields
	const data_graph_name 	= $("#F" + form_group_id + " input[name='data_graph_name[]']");
	const data_select_val 	= $("#F" + form_group_id + " input[name='data_select_val[]']");
	const data_athlete_id	= $("#F" + form_group_id + " input[name='data_athlete_id[]']");
	//const data_form_id 	= $("#F" + form_group_id + " input[name='data_form_id[]']");
	//const data_form_name = $("#F" + form_group_id + " input[name='data_form_name[]']");
	//const data_field_name = $("#F" + form_group_id + " input[name='data_field_name[]']");
	//const data_field_type = $("#F" + form_group_id + " input[name='data_field_type[]']");
	const data_field_num	= $("#F" + form_group_id + " input[name='data_field_num[]']");
	const data_cell_id		= $("#F" + form_group_id + " input[name='data_cell_id[]']");
	const data_or_calc 		= $("#F" + form_group_id + " input[name='data_or_calc[]']");
	const data_diagram_show = $("#F" + form_group_id + " input[name='data_diagram_show[]']");
	const data_type 		= $("#F" + form_group_id + " select[name='data_type_sel[]']");
	const data_line 		= $("#F" + form_group_id + " select[name='data_line_sel[]']");
	const data_p_range		= $("#F" + form_group_id + " select[name='data_p_range_sel[]']");
	const data_color 		= $("#F" + form_group_id + " input[name='data_color[]']");
	const data_markers		= $("#F" + form_group_id + " select[name='data_markers_sel[]']");
	const data_labels 		= $("#F" + form_group_id + " select[name='data_labels_sel[]']");
	const data_axis 		= $("#F" + form_group_id + " select[name='data_axis_sel[]']");
	

	var	Forms_Template_Data = {};
	var Forms_Template_Change = false;

	if (type == 'Interval' && save_id) {
		//we need to check this 2 data sources for differences and alert
		//let Saved_Data = V_FORMS_TEMPLATES[form_id][save_id].data;
		//let Saved_Data = V_RESULTS_TEMPLATES[save_id].Data_Forms[ath_form_saveid];
		Forms_Template_Data = V_FORMS_TEMPLATES[form_id][save_id].data;
		//check length
		if (Object.keys(Forms_Template_Data).length != Object.keys(Saved_Data).length - 1) { //-1 for form_name_show
			console.warn('not the same length : ' + Object.keys(Forms_Template_Data).length + '!=' + Object.keys(Saved_Data).length - 1, Forms_Template_Data, Saved_Data);
			
			Forms_Template_Change = true;
		}
	}


	let i = 0;

	//put values in form ######################################
	//for each field
	for (let field_id in Saved_Data) {
		if (Object.prototype.hasOwnProperty.call(Saved_Data, field_id)) {
			const Saved_Field_Data = Saved_Data[field_id];
			
			if (field_id == 'form_name_show') {
				continue;
			}
			
			if (type == 'Interval' && save_id) {
				if (Saved_Field_Data.data_or_calc != Forms_Template_Data[field_id].data_or_calc ||
					Saved_Field_Data.field_type != Forms_Template_Data[field_id].field_type)
				{
					Forms_Template_Change = true;

					console.warn('different data type : ' + Saved_Field_Data.data_or_calc + '!=' + Forms_Template_Data[field_id].data_or_calc + '--' + Saved_Field_Data.field_type + '!=' + Forms_Template_Data[field_id].field_type);
				}
			}
			
			//put current athlete in sel_val
			let saved_sel_val = ath_id + '|' + form_id + '|' + Saved_Field_Data.field_num.replace('B', '');
			if (save_id) {
				saved_sel_val = ath_id + '|' + form_id + '_S' + save_id + '|' + Saved_Field_Data.field_num.replace('B', '');
			}
	
			//if sel_val && data_or_calc then we have the right field
			if ($(data_select_val[i]).val() == saved_sel_val &&
				$(data_or_calc[i]).val() == Saved_Field_Data.data_or_calc)
			{
				$(data_graph_name[i]).val(Saved_Field_Data.name).trigger('change');
				
				if (Saved_Field_Data.show != $(data_diagram_show[i]).val()) {
					$(data_diagram_show[i]).prev().trigger("click");
				}
				
				$(data_type[i]).val(Saved_Field_Data.type).trigger('change');
				$(data_line[i]).val(Saved_Field_Data.line);
				$(data_p_range[i]).val(Saved_Field_Data.p_range);

				if (Saved_Field_Data.color != '') {
					//$(data_color[i]).val(Saved_Field_Data.color);
					$(data_color[i]).colorpicker('setValue', Saved_Field_Data.color);
				}

				$(data_markers[i]).val(Saved_Field_Data.markers);
				$(data_labels[i]).val(Saved_Field_Data.labels);
				$(data_axis[i]).val(Saved_Field_Data.axis);
				
				
				//calc have some extra fields
				if (Saved_Field_Data.data_or_calc == 'calc') {
					//get calc_id
					//const calc_id = replaceAll(saved_sel_val, '|', '_');
					const calc_id = form_group_id + '_' + Saved_Field_Data.field_num.replace('B', '');

					//set values in form
					$('#formula_cells_' + calc_id).val(Saved_Field_Data.formula_cells).trigger('change');
					$('#formula_period_' + calc_id).val(Saved_Field_Data.formula_period).trigger('change');
					$('#formula_after_' + calc_id).val(Saved_Field_Data.formula_after).trigger('change');

					//bcz it may be disabled we do it like this
					if (Saved_Field_Data.formula_X_axis_show == '1') {
						$('#formula_X_axis_show_ck_' + calc_id).prop('checked', true);
						$('#formula_X_axis_show_' + calc_id).val('1');
					} else {
						$('#formula_X_axis_show_ck_' + calc_id).prop('checked', false);
						$('#formula_X_axis_show_' + calc_id).val('0');
					}

					//bcz it may be disabled we do it like this
					if (Saved_Field_Data.formula_Full_Period == '1') {
						$('#formula_Full_Period_ck_' + calc_id).prop('checked', true);
						$('#formula_Full_Period_' + calc_id).val('1');
					} else {
						$('#formula_Full_Period_ck_' + calc_id).prop('checked', false);
						$('#formula_Full_Period_' + calc_id).val('0');
					}

					$('#formula_input_' + calc_id).val(Saved_Field_Data.formula_input);
					$("#formula_refresh_" + calc_id).trigger("click");
				}
			}
			i++;
		}
	} //for each field
	

	if (type == 'Interval' && Forms_Template_Change) {
		$('#fs-ATHLETE-FORM_' + form_group_id).after(
			'<div class="Error_Template_Changed">' + LANG.RESULTS.TEMPLATE_FORMS_CHANGED + '</div>'
		);
		alert(LANG.RESULTS.TEMPLATE_FORMS_CHANGED);
	}

} //Forms_Template__Form_Fields__Values__Set

// TEMPLATES #########################################
//####################################################



//###############################################
// HTML #########################################

function Fieldset__Expand_Collapse__Init(element) {
	Debug1(' 1.JS.Init.', '-', get_Function_Name(), '-', [...arguments]);

	$(element + " .open_all," +
	  element + " .close_all").on('click', function ()
	{
		const is_opened = $(element + ' .open_all').is(':hidden');
		$(element + ' .open_all').toggle();
		$(element + ' fieldset legend').each(function (i, el) {
				 if (is_opened && !$(el).parent().hasClass('collapsed')) $(el).trigger("click"); //close
			else if (!is_opened && $(el).parent().hasClass('collapsed')) $(el).trigger("click"); //open
		});
	});
}


//new FORM fieldset html
function Fieldset__Athlete_Form__Init(ath_id, form_id, save_id, save_name) {
	Debug1('- 2.Fieldset.Form.Init', '-', get_Function_Name(), '-', [...arguments]);

	const base_form_id = form_id;
	let save_form_id = form_id;
	let form_group_id = ath_id + '_' + form_id;
	let form_name = '';

	if (save_id) {
		save_form_id = base_form_id + '_S' + save_id;
		form_group_id = ath_id + '_' + base_form_id + '_S' + save_id; 
	}

	if (V_RESULTS_PAGE == 'FORMS_RESULTS') {
		form_name = V_FORM_id_2_name[base_form_id];
	}
	else { //RESULTS
		form_name = V_FORMS_N_FIELDS[save_form_id][0];
	}

	
	//add Fieldset_Athlete_Form to HTML
	$('#Select__Athlete_Data__Div_' + ath_id).before(
		HTML_Template__Fieldset__Athlete_Form(base_form_id, form_group_id, form_name, save_id, save_name)
	);
	

	//init Fieldset_Athlete_Form ##########################

	collapse_set('#fs-ATHLETE-FORM_' + form_group_id);
	

	if (V_RESULTS_PAGE == 'FORMS_RESULTS') {
		
		//only for FORMS_RESULTS
		//add Buttons - Calculation, Template (Save, Load, 2 Dashboard)
		Fieldset__Athlete_Form__Extra_Buttons__Init(ath_id, base_form_id, form_group_id, save_id, save_name, save_form_id);

	}
	//RESULTS
	else {
		//V_Calx_Sheets
		if (V_Calx_Sheets.indexOf('#F' + form_group_id) == -1) {
			V_Calx_Sheets.push('#F' + form_group_id);
		}
	}


	Debug1('  2.Calc. Init CALX ===============', [form_group_id]);
	//init CALX
	$('#F' + form_group_id).calx({
		onAfterCalculate : function() {
			USED_DATA__Formula__Update_After_Calculate(this, ath_id, save_form_id);
		}
		//autoCalculate: false,
		//checkCircularReference: true,
		//onAfterRender : function() {}
	});

} //end Fieldset__Athlete_Form__Init
	

function Fieldset__Athlete_Form_Field__Init(data_or_calc, ath_id, form_id, field_name, form_field_num, save_id) {
	Debug1('    3.Fieldset.Field.init.', '-', get_Function_Name(), '-', [...arguments]);

	const base_form_id = form_id;
	let form_group_id = ath_id + '_' + form_id;
	let save_form_id = form_id;

	if (save_id) {
		save_form_id = base_form_id + '_S' + save_id;
		form_group_id = ath_id + '_' + base_form_id + '_S' + save_id;
	}

	if (V_RESULTS_PAGE == 'FORMS_RESULTS') {
		form_name = V_FORM_id_2_name[base_form_id];
	}
	else { //RESULTS
		form_name = V_FORMS_N_FIELDS[save_form_id][0];
	}

	const field_id = ath_id + '_' + save_form_id + '_' + form_field_num;
	const field_group_id = (data_or_calc == 'data' ? 'data_' : 'calc_') + field_id;
	let calx_field_ALPHA_id = '';

	//get cell_id
	if (data_or_calc == 'data') { // we have cell_id
		if (V_RESULTS_PAGE == 'FORMS_RESULTS') {
			calx_field_ALPHA_id = V_FORMS_DATA[ath_id][base_form_id]['_' + form_field_num].cell_id;
		}
		else { //RESULTS
			calx_field_ALPHA_id = V_FORMS_DATA[ath_id][save_form_id]['_' + form_field_num].cell_id;
		}
	}
	else {
		calx_field_ALPHA_id = Formula__Get_ALPHA_id('calc', form_field_num, false);
	}

	V_FORMULA_cell_2_name[form_group_id + '_' + calx_field_ALPHA_id] = field_name;
	
	

	//add template to the HTML #####################
	$('#div-ATHLETE-FORM_' + form_group_id).append(
		HTML_Template__Fieldset__Athlete_Form_Field(data_or_calc, ath_id, base_form_id, field_name, form_field_num, save_id, save_form_id, form_group_id, field_group_id, calx_field_ALPHA_id)
	);
	//time consuming operation TCO-DOM @@@@@@@@@@@@@@@@
	

	//calc
	if (data_or_calc == 'calc')
	{
		//init formula fields #############################################

		$("#formula_cells_"+field_id).on('change', function() {
			V_USED_DATA[ath_id][save_form_id][field_name].rowspan = parseInt($(this).val());
		});

		$("#formula_period_" + field_id).on('change', function() {
			$("#formula_after_period_txt_" + field_id).text(
				$("#formula_period_" + field_id + ' option:selected').text()
			);

			if ($(this).val() != 'lines') { //&& $("#formula_cells_" + field_id).val() > 1
				if (V_RESULTS_PAGE == 'FORMS_RESULTS') {
					//enable formula_cells (1 line per:)
					$("#formula_cells_" + field_id).prop('disabled', false);
					
				} //for RESULTS we need this always disabled  

				$("#formula_X_axis_show_ck_" + field_id).prop('disabled', false).parent().css('color', '');
				
				$("#formula_Full_Period_ck_" + field_id).prop('checked', false).trigger('change').parent().parent().hide();
			}
			else {
				$("#formula_cells_" + field_id).prop('disabled', true).val(1).trigger('change');
				
				$("#formula_X_axis_show_ck_" + field_id).prop('disabled', true).prop('checked', false).trigger('change').parent().css('color', '#aaaaaa');
				
				$("#formula_Full_Period_ck_" + field_id).parent().parent().show();
			}

			//USED_DATA__Rowspan_Period__Update
			V_USED_DATA[ath_id][save_form_id][field_name].rowspan_period = $(this).val();
		});


		//init set ####################
		$("#formula_after_period_txt_" + field_id).text(
			$("#formula_period_" + field_id + ' option:selected').text()
		);

		$("#formula_after_" + field_id).css('background', ($("#formula_after_" + field_id).val() > 0 ? 'aliceblue' : ''));


		//on change ###################
		$("#formula_after_" + field_id).on('change', function() {
			if ($(this).val() > 0) {
				$(this).css('background', 'aliceblue');
			}
			else $(this).css('background', '');
			V_USED_DATA[ath_id][save_form_id][field_name].rowspan_after = parseInt( $(this).val() );
		});

		$("#formula_X_axis_show_ck_" + field_id).on('change', function() {
			V_USED_DATA[ath_id][save_form_id][field_name].formula_X_axis_show = parseInt( $('#formula_X_axis_show_' + field_id).val() );
		});

		$("#formula_Full_Period_ck_" + field_id).on('change', function() {
			V_USED_DATA[ath_id][save_form_id][field_name].formula_Full_Period = parseInt( $('#formula_Full_Period_' + field_id).val() );
		});


		Formula__Beautifier('formula_input_' + field_id).update();
		

		$("#formula_beautify_" + field_id).on('click', function() {
			$("#formula_beautify_open_" + field_id).toggle();
		});


		$("#formula_refresh_" + field_id).on('click', function() {
			try {
				Formula__Update_N_Calculate(ath_id, save_form_id, form_field_num);
			}
			catch (error) {
				$(this).parent().next().removeClass('hidden').parent().addClass('has-error');
				console.warn(error);
			}
		});
	} //end init formula fields #############################################


	//init collapse, color 
	collapse_set('#fs-ATHLETE-FORM-FIELD_' + field_group_id);
	//$('#fs-ATHLETE-FORM-FIELD_' + field_group_id + " legend").trigger("click"); //close
	color_field('#fs-ATHLETE-FORM-FIELD_' + field_group_id + ' .cp');
	color_remove('#fs-ATHLETE-FORM-FIELD_' + field_group_id + ' .color_remove');
	

	//init Show in Diagram checkbox
	$('#fs-ATHLETE-FORM-FIELD_' + field_group_id + ' .data_diagram_show_ck').on('change', function () {
		if (!this.checked) {
			$(this).parent().parent().parent().find('.diagram_options').addClass('hidden');
		} else {
			$(this).parent().parent().parent().find('.diagram_options').removeClass('hidden');
		}
	});


	//Data Name on change/keyup
	$('#fs-ATHLETE-FORM-FIELD_' + field_group_id + " input[name='data_graph_name[]']").on('change keyup', function () {
		//change Object key/name of V_ColumnName_2_ColumnId
		//console.log(field_name, save_form_id, form_field_num, calx_field_ALPHA_id);
		const old_name = V_USED_DATA[ath_id][save_form_id][field_name].name;
		const new_name = $(this).val();
		if (old_name !== new_name && V_ColumnName_2_ColumnId[ath_id][save_form_id][old_name]) {
			//set the new name to V_ColumnName_2_ColumnId
			Object.defineProperty(
				V_ColumnName_2_ColumnId[ath_id][save_form_id],
				new_name,
				Object.getOwnPropertyDescriptor(V_ColumnName_2_ColumnId[ath_id][save_form_id], old_name)
			);
			//delete old name from V_ColumnName_2_ColumnId
			delete V_ColumnName_2_ColumnId[ath_id][save_form_id][old_name];
		}

		//update html fields
		$('#fs-ATHLETE-FORM-FIELD_' + field_group_id + ' span.field_name').text($(this).val());
		$('#Table__Athlete_Form_' + form_group_id + ' span.field_name[data-name="' + field_name + '"]').text(new_name);
		//update V_USED_DATA with the new name
		V_USED_DATA[ath_id][save_form_id][field_name].name = new_name;
	});


	//Diagram type select on change
	$('#fs-ATHLETE-FORM-FIELD_'+field_group_id+" select[name='data_type_sel[]']").on('change', function() {
		if ($(this).val() == 'column') {
			$('#fs-ATHLETE-FORM-FIELD_'+field_group_id+" select[name='data_line_sel[]']").parent().hide();
			$('#fs-ATHLETE-FORM-FIELD_'+field_group_id+" select[name='data_p_range_sel[]']").parent().show();
			$('#fs-ATHLETE-FORM-FIELD_'+field_group_id+" select[name='data_markers_sel[]']").parent().hide();
		}
		else if ($(this).val() == 'scatter') {
			$('#fs-ATHLETE-FORM-FIELD_'+field_group_id+" select[name='data_line_sel[]']").parent().hide();
			$('#fs-ATHLETE-FORM-FIELD_'+field_group_id+" select[name='data_p_range_sel[]']").parent().hide();
			$('#fs-ATHLETE-FORM-FIELD_'+field_group_id+" select[name='data_markers_sel[]']").parent().hide();
		}
		else {
			$('#fs-ATHLETE-FORM-FIELD_'+field_group_id+" select[name='data_line_sel[]']").parent().show();
			$('#fs-ATHLETE-FORM-FIELD_'+field_group_id+" select[name='data_p_range_sel[]']").parent().hide();
			$('#fs-ATHLETE-FORM-FIELD_'+field_group_id+" select[name='data_markers_sel[]']").parent().show();
		}
	});

	
	if (V_RESULTS_PAGE == 'FORMS_RESULTS') {
		//remove_form_field on click --only for FORMS_RESULTS
		$('#Button_Athlete_Form_Field_Remove_' + field_group_id).on('click', function () {
			Debug1('    3.JS.Click. Button_Athlete_Form_Field_Remove_ click', [field_group_id]);

			//remove fieldset
			$(this).next().remove();
			//remove close
			$(this).remove();

			if (data_or_calc == 'data') {
				//re-enable disabled select options
				$('#Select__Athlete_Data_' + ath_id + ' option[value="' + $(this).attr('data-sel') + '"]').prop("disabled", false);
				$("#Select__Athlete_Data_" + ath_id).multiselect('refresh');
			}

			Table__Athlete_Form_Field__Remove($(this).attr('data-val'));
			//Axis__With_No_Data__Remove();

			Debug1('  2.Calc.RC. Refresh/Calculate CALX ===============', [form_group_id]);
			//Update CALX
			$('#F' + form_group_id).calx('refresh');
			//calculate CALX
			$('#F' + form_group_id).calx('calculate');
		});
	}

	//*** we do that on caller so this not called for every field but for all at the end for each form
	//Debug1('  2.Calc.UC. Update/Calculate CALX ===============', [form_group_id]);
	//Update CALX 
	// $('#F'+form_group_id).calx('update');
	//calculate CALX
	// $('#F'+form_group_id).calx('calculate');

	//console.log('------Fieldset__Athlete_Form_Field__Init end');
} //end Fieldset__Athlete_Form_Field__Init


//add New FORM fIELD
function Athlete_Form_Field_DATA__Add(ath_id, form_id, form_field_num, field_name, save_id, save_name) {
	Debug1('-- 3.Html.', '-', get_Function_Name(), '-', [...arguments]);
		
	let form_group_id = ath_id + '_' + form_id;
	if (save_id) {
		form_group_id = ath_id + '_' + form_id + '_S' + save_id;
	}
	

	//add Fieldset_Athlete_Form html -if not exist 
	if ($('#fs-ATHLETE-FORM_' + form_group_id).length == 0) {

		Fieldset__Athlete_Form__Init(ath_id, form_id, save_id, save_name);

		Debug1('- 2.Html.-- Fieldset__Athlete_Form__Init --END--');
	}


	//add FIELD data html 
	Fieldset__Athlete_Form_Field__Init('data', ath_id, form_id, field_name, form_field_num, save_id);

	Table__Athlete_Form_Field__Add('data', ath_id, form_id, form_field_num, save_id);


	Debug1('-- 3.Html.--', '-', get_Function_Name(), '--END--');
} //end Athlete_Form_Field_DATA__Add


//add New CALC fIELD ###############  --only in FORMS_RESULTS
function Athlete_Form_Field_CALC__Add(ath_id, base_form_id, form_id, form_group_id, save_id, field_num) {
	Debug1('-- 3.Html. Athlete_Form_Field_CALC__Add', [ath_id, base_form_id, form_id, form_group_id, save_id, field_num]);

	//835 24 24 835_S466 466
	//835 24 S466 835_S466 466
	let calc_num = 0;
	if (field_num != '') {
		calc_num = field_num.replace('B', '');
	}
	else {
		calc_num = Formula__Get_Max_Number(form_group_id) + 1;	
	}
	const field_name = 'Formula' + calc_num;
	
	//if we not have at least 1 item --so we can copy lines
	if (!Object.values(V_USED_DATA[ath_id][form_id])[0]) {
		return false;
	}

	//add FIELD data html 
	Fieldset__Athlete_Form_Field__Init('calc', ath_id, base_form_id, field_name, calc_num, save_id);

	Table__Athlete_Form_Field__Add('calc', ath_id, base_form_id, calc_num, save_id);
	

	Debug1('-- 3.Html.--', '-', get_Function_Name(), '--END--');
} //end Athlete_Form_Field_CALC__Add

// HTML #########################################
//###############################################



//###############################################
// TABLE ########################################

function Table__Collapse(el) {
	Debug1('X.JS.Click.', '-', get_Function_Name(), '-', [...arguments]);

	$(el).hide();
	$(el).next().show();
	$(el).parents('tr')
		.next().hide() //thead_row2
		.next().hide() //thead_row3
		.next().hide() //thead_row4 //int only
		.next().hide(); //thead_row5 //int only
	$(el).parents('thead').next().hide(); //tbody
}

function Table__Expand(el) {
	Debug1('X.JS.Click.', '-', get_Function_Name(), '-', [...arguments]);

	$(el).hide();
	$(el).prev().show();
	$(el).parents('tr')
		.next().show() //thead_row2
		.next().show() //thead_row3
		.next().show() //thead_row4 //int only
		.next().show(); //thead_row5 //int only
	$(el).parents('thead').next().show(); //tbody
}


//add FIELD to athlete table
function Table__Athlete_Form_Field__Add(data_or_calc, ath_id, form_id, form_field_num, save_id) {
	Debug1('    3.Table.Add.', '-', get_Function_Name(), '-', [...arguments]);

	const base_form_id = form_id;

	let save_form_id = form_id;
	let form_group_id = ath_id + '_' + form_id;
	let form_name = '';

	if (save_id) {
		save_form_id = base_form_id + '_S' + save_id;
		form_group_id = ath_id + '_' + base_form_id + '_S' + save_id; 
	}

	if (V_RESULTS_PAGE == 'FORMS_RESULTS') {
		form_name = V_FORM_id_2_name[base_form_id];
	}
	else { //RESULTS
		form_name = V_FORMS_N_FIELDS[save_form_id][0];
	}


	let data_id = '';
	let cell_id = '';
	let form_field_name = '';
	let form_field_data = [];
	let form_field_data_type = "_Number";
	let cell_rowspan_array = [0, '', 0, ''];
	let lang_field_name = '';

	//data
	if (data_or_calc == 'data') {
		let series_data = [];
		if (V_RESULTS_PAGE == 'FORMS_RESULTS') { //here not have saved forms
			series_data = V_FORMS_DATA[ath_id][base_form_id]['_' + form_field_num];
		}
		else { //RESULTS
			series_data = V_FORMS_DATA[ath_id][save_form_id]['_' + form_field_num];
		}
		data_id = 'R' + form_field_num;
		cell_id = series_data.cell_id;
		form_field_name = series_data.name;
		lang_field_name = series_data.name;
		form_field_data = series_data.data;
		form_field_data_type = series_data.type;
	}
	//calculation
	else {
		//data_id = 'B_' + ath_id + '_' + save_form_id + '_' + form_field_num;
		data_id = 'B' + form_field_num;
		cell_id = Formula__Get_ALPHA_id(data_or_calc, form_field_num, false);

		form_field_name = 'Formula' + form_field_num;
		lang_field_name = LANG.RESULTS.CALCULATION + '' + form_field_num;

		//we want to start from V_FROM
		form_field_data = Formula__Get_Empty_Data_Lines(ath_id, save_form_id, cell_id);
		
		cell_rowspan_array = [1, 'lines', 0, 0];
	}


	const form_field_name_lang = (form_field_name + '').replace('Formula', LANG.RESULTS.CALCULATION);

	//ColumnName for replace Names with cell_id
	if (!V_ColumnName_2_ColumnId[ath_id]) {
		V_ColumnName_2_ColumnId[ath_id] = {};
	}
	if (!V_ColumnName_2_ColumnId[ath_id][save_form_id]) {
		V_ColumnName_2_ColumnId[ath_id][save_form_id] = {};
	}
	V_ColumnName_2_ColumnId[ath_id][save_form_id][form_field_name_lang] = cell_id;
	

	const $data_table = $('table#Table__Athlete_Form_' + form_group_id);

	//10 sticky columns
	let table_column = $data_table.find('thead tr.thead_row2 th').length - 10 + 1;

	//when table not exist we get -2
	if (table_column < 1) {
		table_column = 1;
	}


	USED_DATA__Athlete_Form_Field__Add(ath_id, save_form_id, form_field_name, form_field_name_lang, form_field_data, form_field_data_type, cell_id, table_column, cell_rowspan_array);


	HTML_Template__Table__Athlete_Form_Field__Add(ath_id, save_form_id, form_field_name, lang_field_name, form_field_data, form_field_data_type, form_name, form_group_id, data_or_calc, $data_table, data_id, cell_id);
	
} //end Table__Athlete_Form_Field__Add


//remove FIELD from athlete table
function Table__Athlete_Form_Field__Remove(val) { //#####	 --only in FORMS_RESULTS
	Debug1('    3.Table.Remove.', '-', get_Function_Name(), '-', [...arguments]);

	const val_arr = val.split('|');
	const data_or_calc = val_arr[0];
	const ath_id = val_arr[1];
	const form_id = val_arr[2];
	const form_field_num = val_arr[3];
	const form_field_name = val_arr[4];

	let data_id = 'R' + form_field_num;
	if (data_or_calc == 'calc') {
		data_id = 'B' + form_field_num;
	}

	//remove table column
	$('#F' + ath_id + '_' + form_id + ' .td_' + data_id).remove(); //td
	$('#F' + ath_id + '_' + form_id + ' .th_' + data_id).remove(); //th
	

	USED_DATA__Athlete_Form_Field__Remove(ath_id, form_id, form_field_name);


	Table__Athlete_Form__Colspan__Update(ath_id, form_id);


	USED_DATA__Table_Column_Num__Update(ath_id, form_id);

} //end Table__Athlete_Form_Field__Remove


//fix athlete table FORM name Colspan
function Table__Athlete_Form__Colspan__Update(ath_id, form_id) {
	Debug1('    3.Table.Update.', '-', get_Function_Name(), '-', [...arguments]);

	const $data_table = $('table#Table__Athlete_Form_' + ath_id + '_' + form_id);
	const colSpan = $data_table.find('thead tr.thead_row2 th').not('.dz_hidden').length;
	//3 not hidden //10 sticky columns
	if (colSpan == 3) {
		//if all columns removed clear the table to start again with empty table
		$data_table.html('');
	}
	else {
		//set the new rowspan
		$data_table.find('thead tr.thead_row1 th.th_colspan').attr('colSpan', colSpan);
	}
} //end Table__Athlete_Form__Colspan__Update


function Table__Athlete_Form__Interval_Rowspans__Get_Array(form_group_id, form_field_num, rowspan, rowspan_period, rowspan_after, data) {
	Debug1('X.Table.Intervals.Array.', '-', get_Function_Name(), '-', [...arguments]);

	//get all rowspans-interval array
	const all_interval_rows_array = DATE__Interval_Rowspans_All__Get_Array(rowspan, rowspan_period, rowspan_after, false);
	
	//make array with all existing rows
	let skip_line = 0;
	let exist_rows_array = [];
	let all_interval_rows_array_len = all_interval_rows_array.length;
	$('table#Table__Athlete_Form_' + form_group_id + ' tbody .td_B' + form_field_num).each(function(index, td) { 
		const $td = $(td);
		const vis_hidden = ($td.hasClass('vis') ? 'vis' : ($td.hasClass('hidden_Grey') ? 'hidden_Grey' : 'hidden_Grey2'));
		if (vis_hidden == 'hidden_Grey') {
			skip_line++;
		}
		const this_timestamp = data[index][0];
		const this_date_moment = moment(this_timestamp);
		const this_date = this_date_moment.format('YYYY-MM-DD HH:mm');
		let push_in_array = false;
		
		if (vis_hidden == 'hidden_Grey' ||
			vis_hidden == 'hidden_Grey2' ||
			index <= (skip_line - 1) ||
			(rowspan_after && this_date_moment < V_DATE_FROM_moment.clone().add(rowspan_after, rowspan_period)))
		{
			//skip lines
		}
		else {
			//make exist_rows_array -> data + rowspan info 
			for (let i = 0; i < all_interval_rows_array_len; i++) {
				push_in_array = false;

				//we have row
				if (all_interval_rows_array[i]) {

					//is the same or after the interval
					if (this_timestamp >= all_interval_rows_array[i][0]) {

						//if next interval exist
						if (all_interval_rows_array[i + 1]) {

							//if is smaller than next interval
							if (this_timestamp < all_interval_rows_array[i + 1][0]) {
								push_in_array = true;
							}
						}

						//we not have next interval, so we have to simulate the range to the current interval
						else if (this_timestamp < moment(all_interval_rows_array[i][0]).add(rowspan, rowspan_period).valueOf()) {
							push_in_array = true;
						}
							
						//if is the same with the last interval
						else if (this_timestamp == all_interval_rows_array[i][0]) {
							push_in_array = true;
						}

						
						if (push_in_array) {
							exist_rows_array.push([
								this_date, //timestamp
								all_interval_rows_array[i][1], //date time
								all_interval_rows_array[i][2], //has_rowspan
								all_interval_rows_array[i][3], //last_rowspan
								all_interval_rows_array[i][4]  //rowspan_group
							]);
							break;
						}
					}
					else break;
				}
				else break;
			}
		}
	});
	//console.log(exist_rows_array);
	
	
	//get how many items each rowspan group have
	let rowspan_groups_arr = [];
	let exist_rows_array_len = exist_rows_array.length;

	for (let i = 0; i < exist_rows_array_len; i++) {
		if (rowspan_groups_arr[exist_rows_array[i][4]]) { //add
			rowspan_groups_arr[exist_rows_array[i][4]]++;
		}
		else { //init
			rowspan_groups_arr[exist_rows_array[i][4]] = 1;
		}
	}

	//put rowspan group num in exist_rows_array
	for (let i = 0; i < exist_rows_array_len; i++) {
		exist_rows_array[i][5] = rowspan_groups_arr[exist_rows_array[i][4]];
	}
	//console.log(rowspan_groups_arr);
	
	return exist_rows_array;
} //end Table__Athlete_Form__Interval_Rowspans__Get_Array

// TABLE ########################################
//###############################################



//###############################################
// V_USED_DATA Json #############################

//update V_USED_DATA Table Column Num
function USED_DATA__Table_Column_Num__Update(ath_id, form_id) {
	Debug1('    3.Json.Update.', '-', get_Function_Name(), '-', [...arguments]);
	
	let table_column = 1;
	$('table#Table__Athlete_Form_' + ath_id + '_' + form_id + ' thead tr.thead_row3 th').each(function(index, th){
		//10 sticky columns
		if (index > 9) {
			const form_field_name = $(th).find('span').attr('data-name');

			V_USED_DATA[ath_id][form_id][form_field_name].table_column = table_column;
			
			table_column++;
		}
	});
}


//remove ATHLETE from V_USED_DATA Json
function USED_DATA__Athlete__Remove(ath_id) { //#####	 --only in FORMS_RESULTS
	Debug1('1.Json.Remove.', '-', get_Function_Name(), '-', [...arguments]);

	delete V_ColumnName_2_ColumnId[ath_id];

	delete V_USED_DATA[ath_id];
}


//remove athlete FORM from V_USED_DATA Json
function USED_DATA__Athlete_Form__Remove(ath_id, form_id) { //#####	 --only in FORMS_RESULTS
	Debug1('  2.Json.Remove.', '-', get_Function_Name(), '-', [...arguments]);
	
	delete V_ColumnName_2_ColumnId[ath_id][form_id];

	delete V_USED_DATA[ath_id][form_id];
}
	

//remove athlete FIELD from V_USED_DATA Json
function USED_DATA__Athlete_Form_Field__Remove(ath_id, form_id, form_field_name) {
	Debug1('    3.Json.Remove.', '-', get_Function_Name(), '-', [...arguments]);
	
	delete V_ColumnName_2_ColumnId[ath_id][form_id][ V_USED_DATA[ath_id][form_id][form_field_name].name ];
	
	delete V_USED_DATA[ath_id][form_id][form_field_name];
}


//add athlete data to USED Json
function USED_DATA__Athlete_Form_Field__Add(ath_id, form_id, form_field_name, form_field_name_lang, form_field_data, form_field_data_type, cell_id, table_column, form_field_rowspan_array) {
	Debug1('    3.Json.Add.', '-', get_Function_Name(), '-', [...arguments]);

	//##### form_id here can be base_form_id or save_form_id (3 or 3_S15)

	//init if not already
	if (!V_USED_DATA[ath_id]) {
		V_USED_DATA[ath_id] = {};
	}
	if (!V_USED_DATA[ath_id][form_id]) {
		V_USED_DATA[ath_id][form_id] = {};
	}
	if (!V_USED_DATA[ath_id][form_id][form_field_name]) {
		V_USED_DATA[ath_id][form_id][form_field_name] = {};
	}

	V_USED_DATA[ath_id][form_id][form_field_name].name 			= form_field_name_lang;
	V_USED_DATA[ath_id][form_id][form_field_name].data 			= form_field_data;
	V_USED_DATA[ath_id][form_id][form_field_name].data_type 	= form_field_data_type;
	V_USED_DATA[ath_id][form_id][form_field_name].cell_id 		= cell_id;
	V_USED_DATA[ath_id][form_id][form_field_name].table_column 	= table_column;
	V_USED_DATA[ath_id][form_id][form_field_name].data_date 	= V_DATE_FROM + '|' + V_DATE_TO;
	
	if (form_field_rowspan_array[0] != '0') {
		V_USED_DATA[ath_id][form_id][form_field_name].rowspan 			= form_field_rowspan_array[0];
		V_USED_DATA[ath_id][form_id][form_field_name].rowspan_period 	= form_field_rowspan_array[1];
		V_USED_DATA[ath_id][form_id][form_field_name].rowspan_after 	= form_field_rowspan_array[2];
		//V_USED_DATA[ath_id][form_id][form_field_name].rowspan_after_period = form_field_rowspan_array[3];
		V_USED_DATA[ath_id][form_id][form_field_name].formula_X_axis_show = form_field_rowspan_array[3];
	}
} //ebd USED_DATA__Athlete_Form_Field__Add

// V_USED_DATA Json #############################
//###############################################



//###############################################
// FORMULA ######################################

//Formula = Athlete_Form_Field__CALC ############

function Formula__Get_Max_Number(form_group_id) {
	Debug2('      4.Calc.Num.', '-', get_Function_Name(), '-', [...arguments]);

	let max = null;
	$('#div-ATHLETE-FORM_' + form_group_id + ' .calc').each(function () {
		//fs-ATHLETE-FORM-FIELD_calc_6_6_1 or fs-ATHLETE-FORM-FIELD_calc_6_6_S1_1 for template
		//we need to get the last element in array
		const element_array = $(this).attr('id').split('_');
		const last_element_index = element_array.length - 1;
		const calc_id = parseInt(element_array[last_element_index]);
		max = (calc_id > max) ? calc_id : max;
	});
	return max;
}


function Formula__Get_Empty_Data_Lines(ath_id, form_id, cell_id) {
	Debug2('      4.Calc.Array.', '-', get_Function_Name(), '-', [...arguments]);

	//if we have an item
	if (Object.values( V_USED_DATA[ath_id][form_id] )[0].data) {
		//let temp_data = Object.values(V_USED_DATA[ath_id][form_id])[0]; //not a copy but a reference to V_USED_DATA
		//let temp_data = Object.values(V_USED_DATA[ath_id][form_id])[0].data.slice(0); //the same with next
		let temp_data = $.extend(true, [], Object.values( V_USED_DATA[ath_id][form_id] )[0].data);
		temp_data.forEach(function(row, index){
			temp_data[index][1] = 0;
			temp_data[index][2] = cell_id + (index + 1);
		});
		return temp_data;
	}
	return [];
}


function Formula__Get_ALPHA_id(data_or_calc, num, second_pass) {
	Debug2('      4.Calc.Num.', '-', get_Function_Name(), '-', [...arguments]);
	
	let prefix = '';
	if (!second_pass) {
		num = (data_or_calc == 'data' ? num : num - 1); //adjust num  only on first pass
		prefix = (data_or_calc == 'data' ? 'R' : 'B'); //put R or B only on first pass
	}

	//extra_ALPHA_id in case ALPHA_id is bigger than 26 letters (English Alphabet)
	const extra_ALPHA_id = (num >= 26 ? Formula__Get_ALPHA_id(data_or_calc, ((num / 26 >> 0) - 1), true) : '');
	const ALPHA_id = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'[num % 26 >> 0];

	return prefix + extra_ALPHA_id + ALPHA_id;
}


//Formula__Get_ALPHA_id_noPrefix  no 'B' prefix
function Formula__Get_ALPHA_id_noPrefix(num, second_pass) {
	Debug2('      4.Calc.Num.', '-', get_Function_Name(), '-', [...arguments]);

	if (!second_pass) {
		num = num - 1; //adjust num  only on first pass
	}

	//in case ALPHA_id is bigger than 26 letters (English Alphabet)
	const extra_ALPHA_id = (num >= 26 ? Formula__Get_ALPHA_id_noPrefix(((num / 26 >> 0) - 1), true) : '');
	const ALPHA_id = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'[num % 26 >> 0];

	return extra_ALPHA_id + ALPHA_id;
}


function Formula__Table__Cells_Rowspan__Update(ath_id, form_id, form_field_num) {
	Debug1('  2.Calc.Set.', '-', get_Function_Name(), '-', [...arguments]);

	//##### form_id here can be base_form_id or save_form_id (3 or 3_S15)

	const form_lines = Object.values( V_USED_DATA[ath_id][form_id] )[0].data.slice(0); //clone
	const form_group_id = ath_id + '_' + form_id;
	const form_field_name = 'Formula' + form_field_num;

	const this_DATA = V_USED_DATA[ath_id][form_id][form_field_name];
	const calc_id = ath_id + '_' + form_id + '_' + form_field_num;
	const cell_id = this_DATA.cell_id;

	const rowspan 				= parseInt($('#formula_cells_' + calc_id).val());
	const rowspan_period 		= $('#formula_period_' + calc_id).val();
	const rowspan_after 		= parseInt($('#formula_after_' + calc_id).val());
	const formula_X_axis_show 	= parseInt($('#formula_X_axis_show_' + calc_id).val());

	//set the new data_date
	this_DATA.rowspan = rowspan;
	this_DATA.rowspan_period = rowspan_period;
	this_DATA.rowspan_after = rowspan_after;
	this_DATA.formula_X_axis_show = formula_X_axis_show;
	this_DATA.data_date = V_DATE_FROM + '|' + V_DATE_TO;
	this_DATA.data = []; //reset data array
	
	///###########################################
	//first we make the rowspan=1 for all so then we can apply the new rowspan easy 
	const table_column = this_DATA.table_column - 1;
	//remove all td_B
	$('#F' + form_group_id + ' .td_B' + form_field_num).remove();

	//loop through tr rows
	$('table#Table__Athlete_Form_' + form_group_id + ' tbody tr').each(function (index, tr) {
		const vis_hidden = ($(this).find('th').eq(0).hasClass('vis') ? 'vis' : ($(this).find('th').eq(0).hasClass('hidden_Grey') ? 'hidden_Grey' : 'hidden_Grey2'));
		let cell_val = ''; //empty if not visible
		if (vis_hidden == 'vis') {
			cell_val = '0';
		}

		//add again with rowspan=1
		const td_cell = '<td class="td_B' + form_field_num + ' ' + vis_hidden + '">' + cell_val + '</td>';
		if (table_column > 0) {
			$(this).find('td').eq(table_column - 1).after(td_cell);
		} else {
			$(this).find('th:last').after(td_cell);
		}
	});
	//###########################################
	

	//###############################
	//apply the new rowspan
	//###############################

	//time intervals
	if (['years', 'months', 'weeks', 'days', 'hours', 'minutes'].indexOf(rowspan_period) > -1)
	{
		//we want intervals to start from V_DATE_FROM
		this_DATA.data_int = DATE__Interval_Rowspans_All__Get_Array(rowspan, rowspan_period, rowspan_after, 'Calculation_Interval');

		this_DATA.data_int_mid = DATE__Interval_Rowspans_All__Get_Array(rowspan, rowspan_period, rowspan_after, 'Calculation_Interval_mid');

		//############################
		//get the array with the interval rowspans
		const exist_rows_array = Table__Athlete_Form__Interval_Rowspans__Get_Array(form_group_id, form_field_num, rowspan, rowspan_period, rowspan_after, form_lines);
		const exist_rows_array_len = exist_rows_array.length;
		//############################

		let cell_num = 1;
		let skip_line = 0;
		let active_group = 0;
		let active_rowspan = rowspan;
		let last_group = -1;

		$('table#Table__Athlete_Form_' + form_group_id + ' tbody .td_B' + form_field_num).each(function(index, td) { 
			const $td = $(td);
			const vis_hidden = ($td.hasClass('vis') ? 'vis' : ($td.hasClass('hidden_Grey') ? 'hidden_Grey' : 'hidden_Grey2'));

			if (vis_hidden == 'hidden_Grey') {
				skip_line++;
			}

			//const line = index - skip_line - rowspan_after;
			//const this_date_moment = moment(data[index][0]);
			const this_timestamp = form_lines[index][0];
			const this_date_moment = moment(this_timestamp);
			const this_date = this_date_moment.format('YYYY-MM-DD HH:mm');

			let is_rowspan_cell = false;
			let is_skip_cell = false;
			let is_start_after_cell = false;
			
			if (vis_hidden == 'hidden_Grey' || vis_hidden == 'hidden_Grey2' || index <= (skip_line - 1)) {
				//skip lines
				is_skip_cell = true;
			}
			else if (rowspan_after && this_date_moment < V_DATE_FROM_moment.clone().add(rowspan_after, rowspan_period)) {
				//skip rowspan_after lines
				is_start_after_cell = true;
			}
			else {
				for (let i = 0; i < exist_rows_array_len; i++) {
					if (exist_rows_array[i][0] == this_date) {
						let this_group = exist_rows_array[i][4];

						if (this_group != last_group) {
							is_rowspan_cell = true;
							active_group = this_group;
							active_rowspan = exist_rows_array[i][5];
						}
						else {
							active_rowspan--;
						}

						last_group = this_group;
						break;
					}
				}
			}

			//skip this cell --rowspan_after
			if (is_skip_cell) {
				//console.log(1, 'is_skip_cell');
				$td.replaceWith(
					'<td data-cell="' + cell_id + cell_num + '" data-format="0[.]00" class="int num td_B' + form_field_num + ' ' + vis_hidden + '"></td>'
				);
				cell_num++;
			}
			//is_start_after_cell
			else if (is_start_after_cell) {
				//console.log(2, 'is_start_after_Cell');
				$td.replaceWith(
					'<td data-cell="' + cell_id + cell_num + '" data-format="0[.]00" class="int num td_B' + form_field_num + ' ' + vis_hidden + ' start_After_Cell"></td>'
				);
				cell_num++;
			}
			//cell with rowspan
			else if (is_rowspan_cell) {
				//console.log(3, 'is_rowspan_cell');
				$td.replaceWith(
					'<td data-cell="' + cell_id + cell_num + '" data-format="0[.]00" class="int num td_B' + form_field_num + ' ' + vis_hidden + '" rowspan="' + active_rowspan + '">0</td>'
				);

				//add to data array
				this_DATA.data.push([
					this_DATA.data_int[active_group][3],	//middle
					0, 										//val
					cell_id + cell_num, 					//cell_id
					0, 										//middle.format
					index + 1, 								//cell_start
					index + active_rowspan, 				//cell_end
					this_DATA.data_int[active_group][0],	//int_start
					this_DATA.data_int[active_group][2] 	//int_end
				]);
				cell_num++;
			}
			//remove this cell --cell covered by a previous rowspan multi-row cell
			else {
				//console.log(4, 'hidden_rowspan');
				//$td.remove(); //--only hide bcz we have problems when cells missing
				$td.replaceWith( //just hide with hidden_rowspan css
					'<td class="int num td_B' + form_field_num + ' hidden_rowspan">0</td>'
				);
			}
		});
	}
	//lines
	else {
		let cell_num = 1;
		let skip_line = 0;
		let active_rowspan = rowspan;
		const data = this_DATA.data;

		$('table#Table__Athlete_Form_' + form_group_id + ' tbody .td_B' + form_field_num).each(function(index, td) { 
			const $td = $(td);
			const vis_hidden = ($td.hasClass('vis') ? 'vis' : ($td.hasClass('hidden_Grey') ? 'hidden_Grey' : 'hidden_Grey2'));

			if (vis_hidden == 'hidden_Grey') {
				skip_line++;
			}

			const line = index - skip_line - rowspan_after;
			const this_timestamp = form_lines[index][0];
			const this_date_moment = moment(this_timestamp);
			const this_date = this_date_moment.format('YYYY-MM-DD HH:mm');

			let is_rowspan_cell = false;
			let is_skip_cell = false;
			let is_start_after_cell = false;
			
			//skip out of date cells || rowspan_after
			if (vis_hidden == 'hidden_Grey' || vis_hidden == 'hidden_Grey2') {
				//skip lines
				is_skip_cell = true;
			}
			else if (index <= (rowspan_after + skip_line - 1)) {
				//skip rowspan_after lines
				is_start_after_cell = true;
			}
			else {
				//only rowspan=1 to lines
				is_rowspan_cell = true;
				active_rowspan = 1;
			}

			//skip this cell --rowspan_after or hidden
			if (is_skip_cell) {
				$td.replaceWith(
					//we want them empty
					'<td data-cell="' + cell_id + cell_num + '" data-format="0[.]00" class="int num td_B' + form_field_num + ' ' + vis_hidden + '"></td>'
				);
				cell_num++;
			}
			//skip this cell --rowspan_after or hidden
			else if (is_start_after_cell) {
				$td.replaceWith(
					//we want them empty
					'<td data-cell="' + cell_id + cell_num + '" data-format="0[.]00" class="int num td_B' + form_field_num + ' ' + vis_hidden + ' start_After_Cell"></td>'
				);
				cell_num++;
			}
			//cell with rowspan
			else if (is_rowspan_cell) {
				$td.replaceWith(
					'<td data-cell="' + cell_id + cell_num + '" data-format="0[.]00" class="int num td_B' + form_field_num + ' ' + vis_hidden + '" rowspan="' + active_rowspan + '">0</td>'
				);

				//add to data array
				this_DATA.data.push([
					this_timestamp, 	//time
					0,					//val
					cell_id + cell_num, //cell_id
					0,					//time.format
					index + 1 			//cell_start
				]);
				cell_num++;
			}
			//remove this cell --cell covered by a previous rowspan multi-row cell
			else {
				//$td.remove(); //--only hide bcz we have problems when cells missing
				$td.replaceWith( //just hide with hidden_rowspan css
					'<td class="int num td_B' + form_field_num + ' hidden_rowspan">0</td>'
				);
				console.warn('check this, lines not have rowspan');
			}
		});
	} //rowspan_period = 'lines'
	
	
	Debug1('  2.Calc.U. Update CALX ===============', [form_group_id]);
	//Refresh CALX
	$('#F'+form_group_id).calx('update'); //we needed here for the sheet to get the new cells
}
	

function Formula__Table__Cells__Update(ath_id, form_id, form_field_num) {
	Debug1('  2.Calc.Copy.', '-', get_Function_Name(), '-', [...arguments]);

	//##### form_id here can be base_form_id or save_form_id (3 or 3_S15)

	const form_group_id = ath_id + '_' + form_id;
	const cell_id = ath_id + '_' + form_id + '_' + form_field_num;
	const formula = $('#formula_input_' + cell_id).val();
	const form_field_name = 'Formula' + form_field_num;
	const this_DATA = V_USED_DATA[ath_id][form_id][form_field_name];
	const formula_Full_Period = this_DATA.formula_Full_Period || 0;
	let new_formula = '';
	let new_formula_excel = '';
	
	//#######################################################################################
	//reverse sort column names for the replace (names to cell_ids)
	V_ColumnName_2_ColumnId__Sorted = Object__Sort_By_Length__Reverse(V_ColumnName_2_ColumnId[ath_id][form_id]);
	//#######################################################################################

	// this works for lines and days -- maybe bcz we already have rowspan
	let line_num = 1;
	
	$('table#Table__Athlete_Form_' + form_group_id + ' tbody .td_B' + form_field_num).not('.hidden_rowspan').each(function(index, td)
	{ 
		const data_cell = $(td).attr('data-cell');
		const row = $(td);
		const cell_rowspan = parseInt( row.attr('rowspan') || 1 );
		//let line_num = ((index + 1) * rowspan) - (rowspan - 1) - (rowspan_after * (rowspan - 1));
		
		new_formula = '';
		new_formula_excel = '';
		is_interval = false;
		let cell_datetime = '';
		if (row.hasClass('hidden_Grey') || row.hasClass('hidden_Grey2')) {
			//out of date cells are empty
		}
		else if (row.hasClass('start_After_Cell')) {
			//skipped rowspan_after cells are empty
		}
		else {
			new_formula = Formula__Replace_Templates_with_ids(formula, line_num, this_DATA.data, this_DATA.rowspan_period, formula_Full_Period, 'user');
			new_formula_excel = Formula__Replace_Templates_with_ids(formula, line_num, this_DATA.data, this_DATA.rowspan_period, formula_Full_Period, 'excel');
			//console.log('Formula__Table__Cells__Update', formula, line_num, this_DATA.data, this_DATA.rowspan_period, formula_Full_Period, new_formula_excel);

			if (new_formula.indexOf('Out of Period') != -1) {
				new_formula = '';
				new_formula_excel = '';
			}

			//there is a problem when the first lines are out of date, 
			//so take the dates from the data_int where they already exist
			//time interval
			if (this_DATA.rowspan_period != 'lines') {
				cell_datetime 			= moment(this_DATA.data[line_num - 1][0]).format('YYYY-MM-DD HH:mm');
				let intStart_datetime 	= moment(this_DATA.data[line_num - 1][6]).format('YYYY-MM-DD HH:mm');
				let intEnd_datetime 	= moment(this_DATA.data[line_num - 1][7]).format('YYYY-MM-DD HH:mm');
				
				new_formula += "\n" +
								cell_datetime + "\n" +
								intStart_datetime + ' -> ' + intEnd_datetime;
				is_interval = true;
			}
			//lines
			else {
				let cell_datetime = moment(this_DATA.data[line_num - 1][0]).format('YYYY-MM-DD HH:mm');
				let intStart_datetime = '';
				let intEnd_datetime = '';

				//start
				//has start with +
				if (this_DATA.data[line_num - 1][5]) {
					//has start with -
					if (this_DATA.data[line_num - 1][7] &&
						(this_DATA.data[line_num - 1][7] < this_DATA.data[line_num - 1][5]))
					{
						//put start with - if is smaller
						intStart_datetime = this_DATA.data[line_num - 1][7];
					}
					else {
						//else put start with +
						intStart_datetime = this_DATA.data[line_num - 1][5];
					}
				}
				//has start with -
				else if (this_DATA.data[line_num - 1][7]) {
					intStart_datetime = this_DATA.data[line_num - 1][7];
				}

				//end
				//has end with +
				if (this_DATA.data[line_num - 1][6]) {
					//has end with -
					if (this_DATA.data[line_num - 1][8] &&
						(this_DATA.data[line_num - 1][8] > this_DATA.data[line_num - 1][6]))
					{
						//put end with - if is bigger
						intEnd_datetime = this_DATA.data[line_num - 1][8];
					}
					else {
						//else put end with +
						intEnd_datetime = this_DATA.data[line_num - 1][6];
					}
				}
				//has end with -
				else if (this_DATA.data[line_num - 1][8]) {
					intEnd_datetime = this_DATA.data[line_num - 1][8];
				}
				
				new_formula += "\n" + cell_datetime + "\n";
				if (intStart_datetime != '') {
					new_formula += intStart_datetime + ' -> ' + intEnd_datetime;
				}
			}
			//increase for the next pass
			line_num++;
		}
		

		//cell format
		$(td).attr('data-format', "0[.]00");
		//cell formula
		if (new_formula_excel != '') {
			$('#F' + form_group_id).calx('getCell', data_cell).setFormula(new_formula_excel);
		}
		else {
			//if no formula set cell to '' empty
			$(td).text('');
		}
		//cell title
		$(td).attr('title', data_cell + ' -> ' + new_formula);
		
		if (is_interval) {
			$(td).attr('data-is_int', true);
			$(td).attr('data-datetime', cell_datetime);
		}
	});


	//Debug1('  2.Calc.U. Update CALX ===============', [form_group_id]);
	//Refresh CALX
	//$('#F'+form_group_id).calx('update'); //refresh
} //end Formula__Table__Cells__Update
	

//after Calculate FORMULA callback
//copy results to data, data_int, data_int_mid
function USED_DATA__Formula__Update_After_Calculate(calx, ath_id, save_form_id) {
	Debug1('  2.Calc.Json.', '-', get_Function_Name(), '-', [...arguments]);

	const form_group_id = ath_id + '_' + save_form_id;
	const effected_data = {};

	//get effected field names
	calx.affectedCell.forEach(function(cell, index){ //cell=BA1
		if (cell[0] == 'B') {
			const calx_cell = calx.getCell(cell); //Obj
			const num = cell.replace(/^\D+/g, ''); //1
			const column = cell.replace(num, ''); //BA
			const field_name = V_FORMULA_cell_2_name[form_group_id + '_' + column]; //Formula1
			const field_value = calx_cell.getValue(); //calculation result
			const cell_rowspan = parseInt( calx_cell.el.attr('rowspan') || 1 ); //rowspan
			//console.log(cell, field_value, cell_rowspan, field_name, column, num); //BA1 11 1 Formula1 BA 1

			if (!effected_data[field_name]) {
				//init
				effected_data[field_name] = [];
			}

			effected_data[field_name].push([
				cell,
				field_value,
				cell_rowspan
			]);
		}
	});
	//console.log(effected_data);
	
	//set null to all effected_data lines value field
	Object.keys(effected_data).forEach(function(field_name, index) {
		if (V_USED_DATA[ath_id] &&
			V_USED_DATA[ath_id][save_form_id] &&
			V_USED_DATA[ath_id][save_form_id][field_name])
		{
			V_USED_DATA[ath_id][save_form_id][field_name].data.forEach(function (line, index) {
				line[1] = null;
			});
		}
	});

	//set values from effected_data to V_USED_DATA
	Object.keys(effected_data).forEach(function(field_name, index){
		//let line_num = 1;
		let i_row = 0;
		let new_data = effected_data[field_name];
		let new_data_len = new_data.length;
		
		if (V_USED_DATA[ath_id] &&
			V_USED_DATA[ath_id][save_form_id] &&
			V_USED_DATA[ath_id][save_form_id][field_name])
		{
			const this_DATA = V_USED_DATA[ath_id][save_form_id][field_name];
			this_DATA.data.forEach(function(data_arr, data_index) {
				for (let i = i_row; i < new_data_len; i++) {
					const cell_id = new_data[i][0];
					const field_value = new_data[i][1];
					const cell_rowspan = new_data[i][2];
					if (cell_id == data_arr[2]) {
						//value - round number + parseFloat for diagram
						this_DATA.data[data_index][1] = numRound(parseFloat(field_value));
						//date
						this_DATA.data[data_index][3] = moment(this_DATA.data[data_index][0]).format('YYYY-MM-DD HH:mm');
						//this_DATA.dependencies = Object.keys(calx_cell.dependencies);
						i_row++;
						break;
					}
					else {
						//if not find it with the first pass then it is later, not need to continue, go to the next each
						//not return --it may have a lot of disabled cells at start
						//return;
					}
				};
			});
		}
	});
} //end USED_DATA__Formula__Update_After_Calculate
	

//Formula__Update_N_Calculate
function Formula__Update_N_Calculate(ath_id, form_id, form_field_num) {
	Debug1('  2.Calc.Set.', '-', get_Function_Name(), '-', [...arguments]);

	const form_group_id = ath_id + '_' + form_id;


	Debug1('  2.Calc.U. Update CALX ===============', [form_group_id]);
	//Update CALX
	$('#F' + form_group_id).calx('update');
	

	// cells per formula -rowspan 
	Formula__Table__Cells_Rowspan__Update(ath_id, form_id, form_field_num);


	// copy formula to cells
	Formula__Table__Cells__Update(ath_id, form_id, form_field_num);


	Debug1('  2.Calc.C. Calculate CALX ===============', [form_group_id]);
	//calculate CALX
	$('#F' + form_group_id).calx('calculate');
}


function Formula__Beautifier(input, mode) {
	Debug2('      4.Calc.', '-', get_Function_Name(), '-', [...arguments]);

	//https://github.com/joshbtn/excelFormulaUtilitiesJS
	return {
		formula: '',
		input: document.getElementById(input),
		formulaBody: document.getElementById(input + '_out'),
		mode: "beautify",
		numberOfSpaces: 4,
		changeMode: function(mode) {
			this.mode = mode;
			this.update.call(this);
		},
		formulaAreaClicked: function () { },
		update: function () {
			let calc_id = input.replace('formula_input_', '');
			let rowspan_period = $('#formula_period_' + calc_id).val();
			this.formula = this.input.value.trim();
			try {
				switch (this.mode) {
					case "beautify":
						let spaces = '';
						for (let i = 0; i < this.numberOfSpaces; i += 1) {
							spaces += '&nbsp;'
						}

						if (mode == 'INT') {
							this.formula = Formula__Interval__Replace_Templates_with_ids(this.formula, '[n]', [], rowspan_period, 0, 'user', 'INT');
						}
						else {
							this.formula = Formula__Replace_Templates_with_ids(this.formula, '[n]', [], rowspan_period, 0, 'user');
						}

						const options = {
							tmplIndentTab: '<span class="tabbed">' + spaces + '</span>'
						};
						this.formulaBody.innerHTML = window.excelFormulaUtilities.formatFormulaHTML(this.formula, options);
					  break;
				}

				$(this.input).parent().next().addClass('hidden').parent().removeClass('has-error');
				
				if (this.formula.indexOf('Error!') != -1) {
					$(this.input).parent().next().removeClass('hidden').parent().addClass('has-error');
				}
			}
			catch (exception) {
				$(this.input).parent().next().removeClass('hidden').parent().addClass('has-error');
				console.warn(exception);
			}
		}
	};
}

//replace templates {RB}+{RD} with ids = {RB1}+{RD1}
function Formula__Replace_Templates_with_ids(formula, id, data, rowspan_period, formula_Full_Period, user_excel) {
	Debug2('      4.Calc.', '-', get_Function_Name(), '-', [...arguments]);

	//add_or_sub = + or -
	function Replace_Templates_with_ids(add_or_sub, column_name, id, data, rowspan_period, formula_Full_Period, user_excel) {
		let column = column_name.split(add_or_sub)[0];
		let line = column_name.split(add_or_sub)[1];
		
		if (id == '[n]') {
			//beautifier
			return column + '[n' + add_or_sub + line + ']';
		}

		if (rowspan_period != 'lines') {
			//time intervals not have + -
			return 'Error!';
		}
		
		//get what after the (+ or -)
		//get the number
		let num = parseInt(line.replace(/\D+/g, ''));
		
		//get the period
		const period = line.replace(num, '').trim();

		if (add_or_sub == '-') {
			num = - num; //make negative
		}

		//get the val
		let val = (data[id - 1][4] + num);
		let new_val = 0;
		
		//period --we have minutes/hours/days/weeks/months/years
		if (period != '') {
			const rowspan = num;
			new_val = Table__Cell_Rowspan_for_Period__Get(id - 1, rowspan, period, data, formula_Full_Period);
			if (new_val == 'Out of Period') {
				return 'Out of Period';
			}
		
			// if (add_or_sub == '-') {
			// 	new_val = new_val - 1;
			// }
			// else if (add_or_sub == '+') {
			// 	new_val = new_val + 1;	
			// }
		
			val = (data[id - 1][4] + parseInt(new_val));
			
			if (user_excel == 'excel') {
				return replaceAll_Object_Exact(column, V_ColumnName_2_ColumnId__Sorted) + '' + val;
			}
			return column + '' + val;
		}
		//lines
		else {
			//if data exist
			if (data[id - 1 + num]) { //num may be negative
				if (user_excel == 'excel') {
					return replaceAll_Object_Exact(column, V_ColumnName_2_ColumnId__Sorted) + '' + val;
				}
				return column + '' + val;
			}
			else {
				return 'Out of Period';
			}
		}
	}


	//check if we have the cells required for rowspan and reduce rowspan if not
	function Table__Cell_Rowspan_for_Period__Get(index, rowspan, period, data, formula_Full_Period) {
		Debug1('X.Table.Td.Rowspan.', '-', get_Function_Name(), '-', [...arguments]);

		//##### data = V_USED_DATA[ath_id][form_id][form_field_name].data ##############

		if (!data[index]) {
			return 0;
		}

		//convert period to moment like
		period = DATE__Moment_Interval_Text__From__Formula_Period(period);
		
		const data_date = moment(data[index][0]);

		let index_i = index;
		let new_rowspan = 0;
		
		while (true)
		{
			if (!data[index_i]) {
				break;
			}

			const timestamp = data[index_i][0];
			const this_date = moment(timestamp);
			const date = moment(this_date);

			let this_data_start = data[index_i][5];
			let this_data_end = data[index_i][6];

			let start = moment(data_date);
			let end = moment(data_date).add(rowspan, period);

			//negative rowspan
			if (rowspan < 0) {
				this_data_start = data[index_i][7];
				this_data_end = data[index_i][8];

				//we put the -days first
				start = moment(data_date).add(rowspan, period);
				end = moment(data_date);
			}


			let is_in_date_range = false;
			
			if (formula_Full_Period) {
				is_in_date_range = ((date.format('YYYY-MM-DD HH:mm:ss') >= start.format('YYYY-MM-DD 00:00:00')) && 
									(date.format('YYYY-MM-DD HH:mm:ss') <= end.format('YYYY-MM-DD 23:59:59')) &&  
									DATE__is_in_Range(timestamp));
				if (is_in_date_range) {
					this_data_start = start.format('YYYY-MM-DD 00:00');
					this_data_end = end.format('YYYY-MM-DD 23:59');
				}
			}
			else {
				is_in_date_range = ((date.format('YYYY-MM-DD HH:mm:ss') >= start.format('YYYY-MM-DD HH:mm:ss')) && 
									(date.format('YYYY-MM-DD HH:mm:ss') <= end.format('YYYY-MM-DD HH:mm:ss')) && 
									DATE__is_in_Range(timestamp));
				if (is_in_date_range) {
					this_data_start = start.format('YYYY-MM-DD HH:mm');
					this_data_end = end.format('YYYY-MM-DD HH:mm');
				}
			}
			
			//if the range is out of the selected period then we want the cells empty 
			if (!DATE__is_in_Range(start.valueOf())) {
				return 'Out of Period';
			}
			if (!DATE__is_in_Range(end.valueOf())) {
				return 'Out of Period';
			}
			
			/*console.log(index, index_i, max_rows, rowspan, new_rowspan, period, 
				date.format('YYYY-MM-DD HH:mm:ss'), start.format('YYYY-MM-DD HH:mm:ss'), end.format('YYYY-MM-DD HH:mm:ss'), is_in_date_range, '--', 
				date.format('YYYY-MM-DD HH:mm:ss') >= start.format('YYYY-MM-DD HH:mm:ss'), 
				date.format('YYYY-MM-DD HH:mm:ss') <= end.format('YYYY-MM-DD HH:mm:ss'), 
				date.format('YYYY-MM-DD HH:mm:ss') < end.format('YYYY-MM-DD HH:mm:ss'), DATE__is_in_Range(timestamp));*/
			
			let index_i2 = index_i;

			if (rowspan < 0) { //negative rowspan
				index_i--;
			} else {
				index_i++;
			}

			if (is_in_date_range) {
				if (rowspan < 0) { //negative rowspan
					if (index_i2 < index) {
						new_rowspan--;
					}
				} else {
					if (index_i2 > index) {
						new_rowspan++;
					}
				}
			}
			if (!is_in_date_range) {
				break;
			}
		}

		return new_rowspan;
	}


	function replacer(match, p1, p2, p3, offset, string) {
		//console.log(0, match, 1, p1, 2, p2, 3, p3, 4,offset, 5,string);
		const column_name = p2;
		//++++ #####################################################
		if (column_name.indexOf('+') != -1) {
			return Replace_Templates_with_ids('+', column_name, id, data, rowspan_period, formula_Full_Period, user_excel);
		}
		//---- #####################################################
		else if (column_name.indexOf('-') != -1) {
			return Replace_Templates_with_ids('-', column_name, id, data, rowspan_period, formula_Full_Period, user_excel);
		}
		//not + or - ###############################################
		else {
			//console.log('no + or -', string, column_name+''+id, id, id - 1);
			//time interval
			if (rowspan_period != 'lines') {
				if (id == '[n]') {
					//beautifier
					return column_name + ':' + column_name +'[n]';
				}
				if (user_excel == 'excel') {
					return replaceAll_Object_Exact(column_name, V_ColumnName_2_ColumnId__Sorted) + '' + data[id - 1][4]+
						':' +
						replaceAll_Object_Exact(column_name, V_ColumnName_2_ColumnId__Sorted) + '' + data[id - 1][5];
				}
				return column_name + '' + data[id - 1][4] + ':' + column_name + '' + data[id - 1][5];
			} 
			//lines
			else {
				if (id == '[n]') {
					//beautifier
					return column_name + '[n]';
				}
				if (user_excel == 'excel') {
					return replaceAll_Object_Exact(column_name, V_ColumnName_2_ColumnId__Sorted) + '' + data[id - 1][4];
				}
				//return column_name + '' + id;
				return column_name + '' + data[id - 1][4];
			}
		}
	}

	return formula.replace(/([\{])(.+?)([\}])/g, replacer);
} //end Formula__Replace_Templates_with_ids

// FORMULA ######################################
//###############################################



//###############################################
// DATE #########################################


function DATE__is_in_Range(timestamp) {
	Debug1('      4.Date.Check.', '-', get_Function_Name(), '-', [...arguments]);

	const date_time = moment(timestamp);
	const is_after_start_date = (date_time.format('YYYY-MM-DD HH:mm:ss') >= V_DATE_FROM_moment.format('YYYY-MM-DD HH:mm:ss'));
	//we have date_time<=V_DATE_TO_moment bcz it is 23:59
	const is_before_end_date = (date_time.format('YYYY-MM-DD HH:mm:ss') <= V_DATE_TO_moment.format('YYYY-MM-DD HH:mm:ss'));
	
	return (is_after_start_date && is_before_end_date);
}


function DATE__is_in_Range__Get_Visible_Hidden(timestamp) {
	Debug2('      4.Date.Check.', '-', get_Function_Name(), '-', [...arguments]);

	const date_time = moment.unix(timestamp);
	const is_after_start_date = (date_time.format('YYYY-MM-DD HH:mm:ss') >= V_DATE_FROM_moment.format('YYYY-MM-DD HH:mm:ss'));
	//we have date_time<=V_DATE_TO_moment bcz it is 23:59
	const is_before_end_date = (date_time.format('YYYY-MM-DD HH:mm:ss') <= V_DATE_TO_moment.format('YYYY-MM-DD HH:mm:ss'));
	
	let vis_hidden_Grey = 'hidden_Grey'; //before
	if (is_after_start_date && !is_before_end_date) { //after
		vis_hidden_Grey = 'hidden_Grey2';
	}
	else if (is_after_start_date && is_before_end_date) { //in
		vis_hidden_Grey = 'vis';
	}

	return vis_hidden_Grey;
}


//get all rowspans-interval array
function DATE__Interval_Rowspans_All__Get_Array(rowspan, rowspan_period, rowspan_after, is_for) {
	Debug1('X.Date.Intervals.Array.', '-', get_Function_Name(), '-', [...arguments]);

	if (rowspan_period == 'lines') {
		console.log('Error! Trying to get intervals with "lines" as period.');
		//stack into loop if this happened --so return here
		return [];
	}

	let has_rowspan = true;
	//put also the rowspan_after
	let next_time_moment = V_DATE_FROM_moment.clone().add(rowspan_after, rowspan_period);
	//get the one period milliseconds
	let one_period_timestamp = next_time_moment.clone().add(rowspan, rowspan_period).valueOf() - next_time_moment.valueOf();
	let all_interval_rows_array = [];
	let rowspan_group = 0;
	let last_rowspan = rowspan;

	//##############################################
	//make virtual array with all time interval rows
	while (true) {
		//previous rowspan end --start again --mark row as has_rowspan
		if (last_rowspan == 0) {
			last_rowspan = rowspan;
			has_rowspan = true;
			rowspan_group++;
		}
		
		if (is_for == 'for_X_axis') {
			if (has_rowspan) {
				all_interval_rows_array.push(next_time_moment.valueOf());
			}
		}
		else if (is_for == 'Calculation_Interval') {
			if (has_rowspan) {
				all_interval_rows_array.push([
					//start of interval milliseconds
					next_time_moment.valueOf(),
					//interval value
					null,
					//end of interval milliseconds
					next_time_moment.clone().add(one_period_timestamp - 1, 'milliseconds').valueOf(),
					//middle of interval milliseconds
					next_time_moment.clone().add(one_period_timestamp / 2, 'milliseconds').valueOf(),
					//start of interval
					next_time_moment.format('YYYY-MM-DD HH:mm'),
					//end of interval
					next_time_moment.clone().add(one_period_timestamp - 1, 'milliseconds').format('YYYY-MM-DD HH:mm'),
					//middle of interval
					next_time_moment.clone().add(one_period_timestamp / 2, 'milliseconds').format('YYYY-MM-DD HH:mm')
				]);
			}
		}
		else if (is_for == 'Calculation_Interval_mid') {
			if (has_rowspan) {
				//get the middle of interval
				all_interval_rows_array.push(
					[next_time_moment.clone().add(one_period_timestamp / 2, 'milliseconds').valueOf(), null]
				);
			}
		}
		else {
			//called from Table__Athlete_Form__Interval_Rowspans__Get_Array
			all_interval_rows_array.push([
				next_time_moment.valueOf(),
				next_time_moment.format('YYYY-MM-DD HH:mm'),
				has_rowspan,
				last_rowspan,
				rowspan_group
			]);
		}
		last_rowspan--;
		has_rowspan = false;
		
		//add 1 period each time
		next_time_moment = next_time_moment.add(1, rowspan_period);
		
		if (next_time_moment > V_DATE_TO_moment) {
			//console.log('out of loop', next_time_moment.format('YYYY-MM-DD HH:mm') +'>'+ V_DATE_TO_moment.format('YYYY-MM-DD HH:mm'));
			break;
		}
	}
	//console.log('DATE__Interval_Rowspans_All__Get_Array', is_for, all_interval_rows_array);

	return all_interval_rows_array;
} //end DATE__Interval_Rowspans_All__Get_Array


//TODO: language new - check this for CALC language additions
//convert period to Moment like interval
function DATE__Moment_Interval_Text__From__Formula_Period(period) {
	Debug1('X.Date.Moment.Intervals.', '-', get_Function_Name(), '-', [...arguments]);

	period = period.toLowerCase();

		//minutes
		 if (period == 'min') 		period = 'minutes';
	else if (period == 'minute') 	period = 'minutes';
	else if (period == 'minuten') 	period = 'minutes';
	//hours
	else if (period == 's') 		period = 'hours';
	else if (period == 'h') 		period = 'hours';
	else if (period == 'stunde') 	period = 'hours';
	else if (period == 'stunden') 	period = 'hours';
	else if (period == 'hour') 		period = 'hours';
	//days
	else if (period == 'd') 		period = 'days';
	else if (period == 'd') 		period = 'days';
	else if (period == 'd') 		period = 'days';
	else if (period == 't') 		period = 'days';
	else if (period == 'tag') 		period = 'days';
	else if (period == 'tage') 		period = 'days';
	else if (period == 'day') 		period = 'days';
	//weeks
	else if (period == 'w') 		period = 'weeks';
	else if (period == 'woche') 	period = 'weeks';
	else if (period == 'wochen') 	period = 'weeks';
	else if (period == 'week') 		period = 'weeks';
	//months
	else if (period == 'm') 		period = 'months';
	else if (period == 'monat') 	period = 'months';
	else if (period == 'monate') 	period = 'months';
	else if (period == 'month') 	period = 'months';
	//years
	else if (period == 'y') 		period = 'years';
	else if (period == 'j') 		period = 'years';
	else if (period == 'jahr') 		period = 'years';
	else if (period == 'jahre') 	period = 'years';
	else if (period == 'year') 		period = 'years';
	
	return period;
}

// DATE #########################################
//###############################################
