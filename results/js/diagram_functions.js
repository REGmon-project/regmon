if (!Production_Mode) "use strict"; //remove on production so "const" works on iOS Safari

var V_Axis__Num = 0;
var V_Axis__Last_ID = 0;
var V_DATE__FROM_TO__Last;


function color_field(el) {
	//https://mjolnic.com/bootstrap-colorpicker/
	$(el).colorpicker({
		colorSelectors: {
			'#7cb5ec': '#7cb5ec',
			'#434348': '#434348',
			'#90ed7d': '#90ed7d',
			'#f7a35c': '#f7a35c',
			'#8085e9': '#8085e9',
			'#f15c80': '#f15c80',
			'#e4d354': '#e4d354',
			'#2b908f': '#2b908f',
			'#f45b5b': '#f45b5b',
			'#91e8e1': '#91e8e1'
		}
	}).on('changeColor', function(e) {
		if ($(this).val() != '') {
			$(this).css('background', e.color);
		} else {
			$(this).css('background', '');
		}
	});
}

function color_remove(el) {
	$(el).on('click',function() {
		$(this).next().val('').css('background', '');
	});
}


//#############################################################
// AXIS #######################################################

//new axis
function Axis__Add(element, axis_id, axis_name) {
	Debug1('  2.Chart.Axis.New.', '-', get_Function_Name(), '-', [...arguments]);

	V_Axis__Num++;
	const axis_num = V_Axis__Num;
	
	axis_id = (typeof axis_id !== 'undefined' ? 'axis_' + axis_id : 'axis' + axis_num);
	axis_name = typeof axis_name !== 'undefined' ? axis_name : LANG.RESULTS.Y_AXIS.LABEL + ' ' + axis_num;
	
		
	$(element).before(
		Html_Template__Fieldset_Axis(axis_id, axis_name)
	);


	collapse_set('#' + axis_id);
	color_field('#' + axis_id + ' .cpA');
	color_remove('#' + axis_id + ' .color_remove');
	

	//add option to axis selects
	$.each($("select[name='data_axis_sel[]']") , function (i, ser) {
		//if not exist
		if ($(this).find("option[value='" + axis_id + "']").length == 0) {
			$(this).append($("<option></option>").attr("value", axis_id).text(axis_name));
		}
	});
	

	//close_item click
	$(".close_axis").on('click',function() {
		//remove fieldset
		$(this).next().remove();
		//remove close
		$(this).remove();
		$('.close_axis:first').addClass('first_item');
	});
	$('.close_axis:first').addClass('first_item');

} //end Axis__Add
	

function Axis__After_Action__Update(action, axis_obj, axis_id, axis_title) {
	Debug1('  2.Chart.Axis.Update.', '-', get_Function_Name(), '-', [...arguments]);

	const axis = axis_obj.axis;
	let Table_Axis_Row = '';

	if (action == 'new' || action == '') {
		Table_Axis_Row = ''+
			'<td style="display:none;">' + axis.id + '</td>' +
			'<td>' + axis_title + '</td>' +
			'<td>' + axis.name + '</td>' +
			'<td>' + (axis.pos == 'true' ? 'Rechts' : 'Links') + '</td>' +
			'<td class="axis_color"' + (axis.color ? ' style="background:' + axis.color + ';"' : '') + '>' + axis.color + '</td>' +
			'<td class="num">' + axis.grid + ' px</td>' +
			'<td class="num">' + axis.min + '</td>' +
			'<td class="num">' + axis.max + '</td>' +
		'';
	}

	//new
	if (action == 'new') {
		//add option to Forms selects
		if (action == 'new' && V_Axis__Last_ID != 0) {
			$("select[name='data_axis_sel[]']").each(function (i, ser) {
				$(this).append(
					$("<option></option>").attr("value", 'axis_' + V_Axis__Last_ID).text(axis_title)
				);
			});
			$('#Select_Axis_container .Select_Axis').append(
				$("<option></option>").attr("value", V_Axis__Last_ID).text(axis_title)
			);
		}
		
		//add axis to axis table
		$('#axis_table').append(
			'<tr id="axis_' + V_Axis__Last_ID + '">' +
				Table_Axis_Row +
			'</tr>'
		);
	}
	//update
	else if (action == 'update') {
		//update axis to axis table
		$('#axis_' + axis_id).html( //tr
			Table_Axis_Row
		);
	}
	//delete
	else if (action == 'delete') {
		//remove option from Forms selects
		$('select[name^="res_axis"] option[value="' + id + '"]').remove();
		$('#axis_' + id).remove();
	}

	//reset
	V_Axis__Last_ID = 0;

} //end Axis__After_Action__Update


//Load Axis
function Axis__Load() {
	Debug1('  2.Chart.Axis.Load.', '-', get_Function_Name(), '-', [...arguments]);

	const selected_axis_id = $("#Select_Axis_container .Select_Axis").val();
	const selected_axis_name = $("#Select_Axis_container .Select_Axis option:selected").text();
	
	if (!selected_axis_id) {
		return;
	}
	
	//not process if already loaded
	let exist = false;
	$.each($("input[name='axis_id[]']"), function(i, saved) {
		if ($(saved).val() == 'axis_' + selected_axis_id) {
			exist = true;
			return false;
		}
	});
	
	if (!exist) {
		Axis__Add('#New_Axis_placeholder', selected_axis_id, selected_axis_name);
		
		//Axis fields
		const axis_id 		= $("input[name='axis_id[]']");
		const axis_name 	= $("input[name='axis_name[]']");
		const axis_color 	= $("input[name='axis_color[]']");
		const axis_min 		= $("input[name='axis_min[]']");
		const axis_max 		= $("input[name='axis_max[]']");
		const axis_pos_sel	= $("select[name='axis_pos_sel[]']");
		const axis_grid_sel	= $("select[name='axis_grid_sel[]']");
		
		const i = axis_name.length - 1;
		const axis_data = V_AXIS_DATA[selected_axis_id].axis;
		
		$(axis_id[i]).val('axis_' + selected_axis_id);
		$(axis_name[i]).val(axis_data.name);
		
		if (axis_data.color != '') {
			$(axis_color[i]).colorpicker('setValue', axis_data.color);
		}
		$(axis_min[i]).val(axis_data.min);
		$(axis_max[i]).val(axis_data.max);
		$(axis_pos_sel[i]).val(axis_data.pos);
		$(axis_grid_sel[i]).val(axis_data.grid);
	}
} //end Axis__Load


//remove axis when we remove data depending on them only
function Axis__With_No_Data__Remove() {
	Debug1('  2.Chart.Axis.Remove.', '-', get_Function_Name(), '-', [...arguments]);

	//Data
	const data_axis 		= $("select[name='data_axis_sel[]']");
	const data_graph_name 	= $("input[name='data_graph_name[]']");
	const data_num 			= data_graph_name.length;
	
	//count axis
	const axis_arr = [];
	for (let i = 0; i < data_num; i++) {
		axis_arr.push( $(data_axis[i]).val() );
	}
	
	//axis
	const axis_id = $("input[name='axis_id[]']");
	const axis_num = axis_id.length;

	//Axis
	for (let i = 0; i < axis_num; i++) {
		//axis not exist in data
		if (axis_arr.indexOf($(axis_id[i]).val()) == -1) {
			if ($(axis_id[i]).val().indexOf('_') != -1) {
				$('#' + $(axis_id[i]).val()).prev().trigger('click');
			}
		}
	}
} //end Axis__With_No_Data__Remove

// AXIS #######################################################
//#############################################################



//#############################################################
// DIAGRAM ####################################################


function Chart__Extra_X_Axis__Interval_Column_Points__Get(rowspan, rowspan_period, rowspan_after, pointRange, all_interval_rows_array, is_column) {
	Debug1('  2.Chart.', '-', get_Function_Name(), '-', [...arguments]);

	const columns_data_arr = all_interval_rows_array;
	const columns_data_len = columns_data_arr.length;
	const columns_data_arr_new = [];
	
	//get only rows with values
	if (is_column) {
		for (let j = 0; j < columns_data_len; j++) {
			if (columns_data_arr[j][1] != null) {
				columns_data_arr_new.push(columns_data_arr[j]);
			}
		}
	}
	else { //axis --just put timestamps in array
		for (let j = 0; j < columns_data_len; j++) {
			columns_data_arr_new.push([columns_data_arr[j]]);
		}
	}

	const columns_data_arr_new_len = columns_data_arr_new.length;
	
	const interval_arr = DATE__Interval_Rowspans_All__Get_Array(rowspan, rowspan_period, rowspan_after, 'for_X_axis');
	const interval_arr_len = interval_arr.length;

	const interval_arr_new = [];
	const interval_arr_new_text = [];
	
	for (let i = 0; i < interval_arr_len; i++) {

		for (let j = 0; j < columns_data_arr_new_len; j++) {

			if (columns_data_arr_new[j][0] >= interval_arr[i]) {
				if (interval_arr[i + 1]) {
					if (columns_data_arr_new[j][0] < interval_arr[i + 1]) { //is in
						const mid = (interval_arr[i + 1] - interval_arr[i]) / 2;
						if (is_column) {
							interval_arr_new.push([
								(interval_arr[i] + mid),
								columns_data_arr_new[j][1]
							]);
						}
						else {
							interval_arr_new.push(interval_arr[i] + mid);
							interval_arr_new_text.push([
								interval_arr[i] + mid,
								Chart__Extra_X_Axis__Rowspan_Period__2__LANG_Period__Get(rowspan, rowspan_period) + ' (' + (j + 1) + ')'
							]);
						}
						break;
					}
				}
				else {
					if (columns_data_arr_new[j][0] < (interval_arr[i] + pointRange)) { //is in
						const mid = ((interval_arr[i] + pointRange) - interval_arr[i]) / 2;

						if (is_column) {
							interval_arr_new.push([
								(interval_arr[i] + mid),
								columns_data_arr_new[j][1]
							]);
						}
						else {
							interval_arr_new.push(interval_arr[i] + mid);
							interval_arr_new_text.push([
								interval_arr[i] + mid,
								Chart__Extra_X_Axis__Rowspan_Period__2__LANG_Period__Get(rowspan, rowspan_period) + ' (' + (j + 1) + ')'
							]);
						}

						break;
					}
				}
			}
		}
	}
	//console.log(interval_arr, columns_data_arr, columns_data_arr_new, interval_arr_new);
	
	if (is_column) {
		return interval_arr_new;
	}
	else {
		return [interval_arr_new, interval_arr_new_text];
	}
}


function Chart__Extra_X_Axis__Rowspan_Period__2__LANG_Period__Get(rowspan, period) { //helper function
	let lang_period = '';
	if (period == 'minutes') {
		if (rowspan > 1) {
			lang_period = LANG.RESULTS.INTERVAL_PERIOD.MINUTES;
		} else {
			lang_period = LANG.RESULTS.INTERVAL_PERIOD.MINUTE;
		}
	}
	else if (period == 'hours') {
		if (rowspan > 1) {
			lang_period = LANG.RESULTS.INTERVAL_PERIOD.HOURS;
		} else {
			lang_period = LANG.RESULTS.INTERVAL_PERIOD.HOUR;
		}
	}
	else if (period == 'days') {
		if (rowspan > 1) {
			lang_period = LANG.RESULTS.INTERVAL_PERIOD.DAYS;
		} else {
			lang_period = LANG.RESULTS.INTERVAL_PERIOD.DAY;
		}
	}
	else if (period == 'weeks') {
		if (rowspan > 1) {
			lang_period = LANG.RESULTS.INTERVAL_PERIOD.WEEKS;
		} else {
			lang_period = LANG.RESULTS.INTERVAL_PERIOD.WEEK;
		}
	}
	else if (period == 'months') {
		if (rowspan > 1) {
			lang_period = LANG.RESULTS.INTERVAL_PERIOD.MONTHS;
		} else {
			lang_period = LANG.RESULTS.INTERVAL_PERIOD.MONTH;
		}
	}
	else if (period == 'years') {
		if (rowspan > 1) {
			lang_period = LANG.RESULTS.INTERVAL_PERIOD.YEARS;
		} else {
			lang_period = LANG.RESULTS.INTERVAL_PERIOD.YEAR;
		}
	}

	return rowspan + ' ' + lang_period;
}


function Chart__Rowspan_Period__2__Milliseconds__Get(rowspan, period) { //helper function
	let milliseconds = 1000;
		 if (period == 'minutes') 	milliseconds = parseInt(rowspan) *              60 * 1000;
	else if (period == 'hours') 	milliseconds = parseInt(rowspan) *            3600 * 1000;
	else if (period == 'days') 		milliseconds = parseInt(rowspan) *       24 * 3600 * 1000;
	else if (period == 'weeks') 	milliseconds = parseInt(rowspan) *   7 * 24 * 3600 * 1000;
	else if (period == 'months') 	milliseconds = parseInt(rowspan) *  30 * 24 * 3600 * 1000;
	else if (period == 'years') 	milliseconds = parseInt(rowspan) * 365 * 24 * 3600 * 1000;
	return milliseconds;
}


function Chart__Rowspan_Period__2__zIndex__Get(rowspan, period) { //helper function
	let zIndex = -1;
		 if (period == 'minutes') 	zIndex = parseInt(rowspan) *              6;
	else if (period == 'hours') 	zIndex = parseInt(rowspan) *            360;
	else if (period == 'days') 		zIndex = parseInt(rowspan) *       24 * 360;
	else if (period == 'weeks') 	zIndex = parseInt(rowspan) *   7 * 24 * 360;
	else if (period == 'months') 	zIndex = parseInt(rowspan) *  30 * 24 * 360;
	else if (period == 'years') 	zIndex = parseInt(rowspan) * 365 * 24 * 360;
	return zIndex;
}



//#################################################
//#################################################
function Chart__Update() {
	Debug1('*****************************************');
	Debug1('1.Chart.Update.', '-', get_Function_Name(), '-', [...arguments]);

	const USED_DATA = V_USED_DATA;
	const INTERVAL_DATA = V_INTERVAL_DATA || {}; //only RESULTS

	if (V_RESULTS_PAGE == 'FORMS_RESULTS') {
		if (V_DATE__FROM_TO__Last != V_DATE_FROM + '_' + V_DATE_TO) {
			//calculate -- click all formula_refresh buttons
			$("[id^=formula_refresh_").trigger('click');

			V_DATE__FROM_TO__Last = V_DATE_FROM + '_' + V_DATE_TO;
		}
	}


	//reset chart
	// chart ############################################
	V_Chart.destroy();
	// chart ############################################
	
	
	//init chart
	// chart ############################################
	$('#container_graph').highcharts({});
	V_Chart = $('#container_graph').highcharts();
	// chart ############################################
	

	//change Loading to NoData - set options
	Highcharts.setOptions({
		lang: { noData: LANG.DIAGRAM.NO_DATA_SELECTED },
		noData: { style: { fontSize: '15px' } }
	});


	//if Diagram panel is closed -> open it
	if (!$('#C_Diagramm.collapse').hasClass('in')) {
		//open diagram panel
		$('#C_Diagramm.collapse').collapse('show');
	}

	//if Athletes Data panel is open -> close it
	if ($('#C_Athletes_Data.collapse').hasClass('in')) {
		//close data panel
		$('#C_Athletes_Data.collapse').collapse('hide');
	}

	if (V_RESULTS_PAGE == 'RESULTS') {
		//if intervals panel is open -> close it
		if ($('#C_Intervals_Data.collapse').hasClass('in')) {
			//close data panel
			$('#C_Intervals_Data.collapse').collapse('hide');
		}
	}

	//if Axis panel is open -> close it
	if ($('#C_Axis.collapse').hasClass('in')) {
		//close data panel
		$('#C_Axis.collapse').collapse('hide');
	}

	setTimeout(function(){ //for collapse to end its job
		$("html,body").animate({
			scrollTop: $('#accordion_diagram').offset().top - 3
		}, "slow");
	}, 500);
	

	//#######################################################################
	//DATA ##################################################################

	//Data  --we get data first  and then axis bcz we need data_num for axis loop
	const data_diagram_show = $("#Athletes_Data_Fieldsets input[name='data_diagram_show[]']");
	const data_select_val 	= $("#Athletes_Data_Fieldsets input[name='data_select_val[]']");
	const data_athlete_id 	= $("#Athletes_Data_Fieldsets input[name='data_athlete_id[]']"); //
	const data_form_id 		= $("#Athletes_Data_Fieldsets input[name='data_form_id[]']");
	const data_form_name 	= $("#Athletes_Data_Fieldsets input[name='data_form_name[]']");
	const data_graph_name 	= $("#Athletes_Data_Fieldsets input[name='data_graph_name[]']");
	const data_field_name	= $("#Athletes_Data_Fieldsets input[name='data_field_name[]']");
	const data_field_type	= $("#Athletes_Data_Fieldsets input[name='data_field_type[]']");
	const data_color 		= $("#Athletes_Data_Fieldsets input[name='data_color[]']");
	const data_type 		= $("#Athletes_Data_Fieldsets select[name='data_type_sel[]']");
	const data_line 		= $("#Athletes_Data_Fieldsets select[name='data_line_sel[]']");
	const data_p_range		= $("#Athletes_Data_Fieldsets select[name='data_p_range_sel[]']");
	const data_markers		= $("#Athletes_Data_Fieldsets select[name='data_markers_sel[]']");
	const data_labels 		= $("#Athletes_Data_Fieldsets select[name='data_labels_sel[]']");
	const data_axis 		= $("#Athletes_Data_Fieldsets select[name='data_axis_sel[]']");
	const data_num 			= data_graph_name.length;
	
	//DATA ##################################################################
	//#######################################################################
	

	if (V_RESULTS_PAGE == 'RESULTS') {
		//@@@@@ use var --const not work outside of brackets {}

		//#######################################################################
		//INTERVALS #############################################################

		var int_diagram_show 	= $("#Intervals_Data_Fieldsets input[name='data_diagram_show[]']");
		var int_ath_name_show 	= $("#Intervals_Data_Fieldsets input[name='data_diagram_ath_name_show[]']"); //
		var int_select_val 		= $("#Intervals_Data_Fieldsets input[name='data_select_val[]']");
		var int_data_id 		= $("#Intervals_Data_Fieldsets input[name='data_int_id[]']"); //
		var int_field_id 		= $("#Intervals_Data_Fieldsets input[name='data_field_id[]']"); //
		//var int_form_id 		= $("#Intervals_Data_Fieldsets input[name='data_form_id[]']");
		//var int_form_name 	= $("#Intervals_Data_Fieldsets input[name='data_form_name[]']");
		var int_graph_name 		= $("#Intervals_Data_Fieldsets input[name='data_graph_name[]']");
		var int_field_name		= $("#Intervals_Data_Fieldsets input[name='data_field_name[]']");
		//var int_field_type	= $("#Intervals_Data_Fieldsets input[name='data_field_type[]']");
		var int_color 			= $("#Intervals_Data_Fieldsets input[name='data_color[]']");
		var int_type 			= $("#Intervals_Data_Fieldsets select[name='data_type_sel[]']");
		var int_line 			= $("#Intervals_Data_Fieldsets select[name='data_line_sel[]']");
		var int_p_range			= $("#Intervals_Data_Fieldsets select[name='data_p_range_sel[]']");
		var int_markers			= $("#Intervals_Data_Fieldsets select[name='data_markers_sel[]']");
		var int_labels 			= $("#Intervals_Data_Fieldsets select[name='data_labels_sel[]']");
		var int_axis 			= $("#Intervals_Data_Fieldsets select[name='data_axis_sel[]']");
		var int_num 			= int_graph_name.length;

		//INTERVALS #############################################################
		//#######################################################################

	} //RESULTS

	

	//#######################################################################
	//AXIS ##################################################################
	
	// X AXIS ##################################################################
	//we have YYYY-MM-DD HH:mm in t_date_from, t_date_to 
	let t_date_from = moment($('#t_date_from').val() + ':00', 'YYYY-MM-DD HH:mm:ss');
	let t_date_to = moment($('#t_date_to').val() + ':59', 'YYYY-MM-DD HH:mm:ss');

	if ($('#t_date_from').val().indexOf('.') !== -1) { //german date
		t_date_from = moment($('#t_date_from').val() + ':00', 'DD.MM.YYYY HH:mm:ss');
		t_date_to = moment($('#t_date_to').val() + ':00', 'DD.MM.YYYY HH:mm:ss');
	}

	const x_min = t_date_from.format("YYYY MM DD HH mm ss").split(' ');
	const x_max = t_date_to.format("YYYY MM DD HH mm ss").split(' ');

	//set the X-AXIS DATETIME  start-end
	V_Chart.xAxis[0].update({
		min: Date.UTC(x_min[0], (x_min[1] - 1), x_min[2], x_min[3], x_min[4], x_min[5]),
		max: Date.UTC(x_max[0], (x_max[1] - 1), x_max[2], x_max[3], x_max[4], x_max[5])
	});
	// X AXIS ##################################################################
	

	//count axis
	var axis_arr = [];
	var axis_X_arr = {};

	
	//####DATA axis //get all visible axis from DATA fields ###############################
	for (let i = 0; i < data_num; i++) {
		//only for diagram visible fields
		if ($(data_diagram_show[i]).val() == 1) {
			axis_arr.push([
				$(data_axis[i]).val(),
				$(data_color[i]).val()
			]);
			
			//Extra X Axis //check if we have an extra X axis
			const ath_id = $(data_athlete_id[i]).val();
			const form_id = $(data_form_id[i]).val();
			const field_name = $(data_field_name[i]).val();
			const formula_X_axis_show = USED_DATA[ath_id][form_id][field_name].formula_X_axis_show;

			//only checked Extra X Axis fields
			if (formula_X_axis_show == 1) {
				const rowspan = USED_DATA[ath_id][form_id][field_name].rowspan;
				const rowspan_period = USED_DATA[ath_id][form_id][field_name].rowspan_period;
				const rowspan_after = USED_DATA[ath_id][form_id][field_name].rowspan_after;
				const axis_arr = [
					parseInt(rowspan),
					rowspan_period,
					parseInt(rowspan_after),
					formula_X_axis_show
				];
				if (V_RESULTS_PAGE == 'FORMS_RESULTS') {
					//axis_X_arr.push(axis_arr);
					axis_X_arr['RAW_' + ath_id + '_' + form_id + '_' + field_name] = axis_arr;
				}
				else { //RESULTS
					axis_X_arr['RAW_' + ath_id + '_' + form_id + '_' + field_name] = axis_arr;
				}
			}
		}
	}


	if (V_RESULTS_PAGE == 'RESULTS') {
		//####INT axis //get all visible axis from INT fields ################################
		for (let i = 0; i < int_num; i++) {
			//only for diagram visible fields
			if ($(int_diagram_show[i]).val() == 1) {
				axis_arr.push([
					$(int_axis[i]).val(),
					$(int_color[i]).val()
				]);
				
				//Extra X Axis //check if we have an extra X axis
				const interval_id = $(int_data_id[i]).val();
				const formula_X_axis_show = INTERVAL_DATA[interval_id].formula_X_axis_show;

				//only checked Extra X Axis fields
				if (formula_X_axis_show == 1) {
					const rowspan = INTERVAL_DATA[interval_id].rowspan;
					const rowspan_period = INTERVAL_DATA[interval_id].rowspan_period;
					const rowspan_after = 0;
					if (!axis_X_arr[interval_id]) {
						axis_X_arr[interval_id] = [
							parseInt(rowspan),
							rowspan_period,
							parseInt(rowspan_after),
							formula_X_axis_show
						];
					}
				}
			}
		}
	}
	

	//get the count of data for each axis --unique axis array
	const axis_count = {}; 
	axis_arr.forEach(function(i) { 
		axis_count[i[0]] = [((axis_count[i[0]] ? axis_count[i[0]][0] : 0) + 1), i[1] || '']; 
	});
	//console.log(axis_arr, axis_count);
	

	//load Axis from Data inputs to Y-Axis region
	Object.keys(axis_count).forEach(function(axis__id) { 
		//we select it
		$("#Select_Axis_container .Select_Axis").val( axis__id.split('_')[1] );
		//load selected axis --do nothing if already exist
		Axis__Load();
	});
	

	//get Axis options from Y-Axis region inputs
	const axis_id 		= $("#C_Axis input[name='axis_id[]']");
	const axis_name 	= $("#C_Axis input[name='axis_name[]']");
	const axis_color 	= $("#C_Axis input[name='axis_color[]']");
	const axis_min 		= $("#C_Axis input[name='axis_min[]']");
	const axis_max 		= $("#C_Axis input[name='axis_max[]']");
	const axis_pos_sel	= $("#C_Axis select[name='axis_pos_sel[]']");
	const axis_grid_sel	= $("#C_Axis select[name='axis_grid_sel[]']");
	const axis_num 		= axis_name.length;
	

	//loop axis
	for (let i = 0; i < axis_num; i++) {
		
		//color ##############################
		let ax_color = $(axis_color[i]).val();
		
		//there is a request for this and the opposite request for the data
		//if the axis have 1 data and the data has color -> give axis the data color
		if (axis_count[$(axis_id[i]).val()] &&
			axis_count[$(axis_id[i]).val()][0] == 1 &&
			axis_count[$(axis_id[i]).val()][1] != "")
		{
			ax_color = axis_count[$(axis_id[i]).val()][1]; //give data color
		}
		//if axis have more than 1 data then the axis is always black
		else if (axis_count[$(axis_id[i]).val()] &&
				 axis_count[$(axis_id[i]).val()][0] > 1)
		{
			ax_color = '#000000';
		}

		//make sure that there is no transparency in axis color
		if (ax_color.indexOf('rgba') != -1) { //if is rgba
			//replace the number with dot '.' and parenthesis with '1)' to remove transparency 
			//https://stackoverflow.com/questions/16065998/replacing-changing-alpha-in-rgba-javascript
			ax_color = ax_color.replace(/[\d\.]+\)$/g, '1)'); 
		}
		//color ##############################
		

		//axis_options #######################
		let axis_options = {
			id: $(axis_id[i]).val(),
			title: {
				text: $(axis_name[i]).val(),
				style: { color: ax_color }
			},
			labels: {
				style: { color: ax_color }
			},
			min: ($(axis_min[i]).val() != '') ? $(axis_min[i]).val() : null,
			max: ($(axis_max[i]).val() != '') ? $(axis_max[i]).val() : null,
			gridLineWidth: parseInt($(axis_grid_sel[i]).val()),
			//gridLineColor: ax_color,
			//tickInterval: 1,
			opposite: (($(axis_pos_sel[i]).val() == 'true') ? true : false)
		};
		
		if ($(axis_color[i]).val() != '') {
			axis_options.gridLineColor = ax_color;
		}

		if (V_RESULTS_PAGE == 'RESULTS') {
			//override labels
			axis_options.labels = {
				//https://api.highcharts.com/highcharts/xAxis.labels.autoRotation
				//step overwrite this - so should be disabled
				autoRotation: [-10, -20, -30, -40, -50, -60, -70, -80, -90],
				style: { color: ax_color }
			};

			//not display axis if not have data
			axis_options.showEmpty = false;
		}
		//axis_options #######################
		//console.log(axis_options);

		
		// chart ############################################
		V_Chart.addAxis(axis_options);
		// chart ############################################
	}


	//Extra X Axis
	//axis_X_arr.forEach(function (i, index) {
	Object.keys(axis_X_arr).forEach(function(axis_X_id, index) { 
		let axis_X = axis_X_arr[axis_X_id];
		//console.log(axis_X, index);

		const rowspan = axis_X[0];
		const rowspan_period = axis_X[1];
		const rowspan_after = axis_X[2];
		const is_for = 'for_X_axis';

		const tickInterval = Chart__Rowspan_Period__2__Milliseconds__Get(rowspan, rowspan_period);
		const all_interval_rows_array = DATE__Interval_Rowspans_All__Get_Array(rowspan, rowspan_period, rowspan_after, is_for); 
		//console.log(axis_X, tickInterval, all_interval_rows_array);

		
		//axis_options #######################
		let axis_X_options = {
			linkedTo: 0,
			id: index + 1,
			type: 'datetime',
			tickInterval: tickInterval,
			tickPositions: all_interval_rows_array, //[1,4,7,10]
			labels: {
				step: 1, //this helps somehow for the problem with not visible tick labels at zoom
				format: ((rowspan_period == 'hours' || rowspan_period == 'minutes') ? '{value:%e %b %H:%M}' : '{value:%e %b}') //'{value:%Y-%m-%d %H:%M}'
			},
			lineWidth: 0, //not work with css
			//lineColor: '#ffffff', //make line white so it is not visible
			gridLineWidth: 1,
			gridLineColor: "#dddddd",

			endOnTick: true, //def:false
			showFirstLabel: true, //def:true
			showLastLabel: true, //def:true
			startOnTick: true, //def:false

			min: Date.UTC(x_min[0], (x_min[1] - 1), x_min[2], x_min[3], x_min[4], x_min[5]),
			max: Date.UTC(x_max[0], (x_max[1] - 1), x_max[2], x_max[3], x_max[4], x_max[5])
			//gridLineColor: ax_color,
			//tickInterval: 1,
			//showEmpty: false //def:true
		};

		if (V_RESULTS_PAGE == 'RESULTS') {
			//override labels
			axis_X_options.labels = {
				//https://api.highcharts.com/highcharts/xAxis.labels.autoRotation
				//step overwrite this - so should be disabled
				autoRotation: [-10, -20, -30, -40, -50, -60, -70, -80, -90], 
				//step: 1, //should be disabled for autoRotation to work
				format: ((rowspan_period == 'hours' || rowspan_period == 'minutes') ? '{value:%e %b %H:%M}' : '{value:%e %b}') //'{value:%Y-%m-%d %H:%M}'
			};
		}
		//axis_options #######################

		// chart ############################################
		V_Chart.addAxis(axis_X_options, true); //true = X axis
		// chart ############################################
		

		//second X Axis for the period label
		let tick_points_on_intervals_arr = Chart__Extra_X_Axis__Interval_Column_Points__Get(rowspan, rowspan_period, rowspan_after, tickInterval, all_interval_rows_array, false);
		let tick_points_on_intervals = tick_points_on_intervals_arr[0];
		let text_intervals = tick_points_on_intervals_arr[1];

		//axis_options #######################
		let axis_X_options2 = {
			linkedTo: 0,
			id: index + 2,
			type: 'datetime',
			tickInterval: tickInterval,
			tickPositions: tick_points_on_intervals, //[1,4,7,10]
			labels: {
				y: 0,
				step: 1,
				formatter: function () {
					let this_interval = '';
					for (let i = 0; i < text_intervals.length; i++) {
						if (this.value == text_intervals[i][0]) {
							this_interval = text_intervals[i][1];
							break;
						}
					}
					return this_interval;
				}
			},
			lineWidth: 0,
			tickWidth: 0,
			min: Date.UTC(x_min[0], (x_min[1] - 1), x_min[2], x_min[3], x_min[4], x_min[5]),
			max: Date.UTC(x_max[0], (x_max[1] - 1), x_max[2], x_max[3], x_max[4], x_max[5])
		};

		if (V_RESULTS_PAGE == 'RESULTS') {
			//override labels
			axis_X_options2.labels = {
				y: 0, //-1
				//step overwrite this - so should be disabled
				autoRotation: [-10, -20, -30, -40, -50, -60, -70, -80, -90], 
				//step: 1, //should be disabled for autoRotation to work
				formatter: function () {
					let this_interval = '';
					for (let i = 0; i < text_intervals.length; i++) {
						if (this.value == text_intervals[i][0]) {
							this_interval = text_intervals[i][1];
							break;
						}
					}
					return this_interval;
				}
			};
		}
		//axis_options #######################

		// chart ############################################
		V_Chart.addAxis(axis_X_options2, true); //true = X axis
		// chart ############################################
	}); //end Extra X Axis



	//Notes ##########################################################3
	let plotBands = [];
	for (let i = 0; i < data_num; i++) {
		if ($(data_form_id[i]).val() == 'note' &&
			$(data_field_type[i]).val() == '_Text')
		{
			let ath_id = $(data_athlete_id[i]).val();
			let form_id = $(data_form_id[i]).val();
			let field_name = $(data_field_name[i]).val();
			let n_color = $(data_color[i]).val();
			let ii = 0;

			USED_DATA[ath_id][form_id][field_name].data.forEach(function(row, index){
				let timestamp_start = (row[0]+'').slice(0, -3);
				let timestamp_end = (row[2]+'').slice(0, -3);
				//check if it is in visible date
				if (DATE__is_in_Range__Get_Visible_Hidden(timestamp_start) == 'vis' &&
					DATE__is_in_Range__Get_Visible_Hidden(timestamp_end) == 'vis')
				{ 
					plotBands.push({
						color: (n_color ? n_color : (row[3] ? row[3] : 'rgba(238,238,238,0.5)')), //note color
						borderColor: 'rgba(221, 221, 221, 0.5)',
						borderWidth: 1,
						from: row[0],
						to: row[2],
						label: {
							text: row[1],
							align: 'center',
							textAlign: 'center',
							verticalAlign: 'top',
							y: (20 + (ii * 10)),
							style: {
								color: '#888888',
								fontWeight: 'bold'
							}
						}
					});
					ii++;
				}
			});
		}
	}

	//https://github.com/highcharts/highcharts/issues/5573
	//Plotbands and plotlines added dynamically using addPlotBandOrLine() are not exported
	// chart ############################################
	V_Chart.xAxis[0].update({ plotBands: plotBands });
	// chart ############################################

	//Notes end ###########################################################
	
	//AXIS ##################################################################
	//#######################################################################
	
	
	//#######################################################################
	//DATA ##################################################################
	

	let colorIndex = 0; //_colorIndex
	let symbolIndex = 0; //_symbolIndex
	let x_axis_index = 1;
	

	if (V_RESULTS_PAGE == 'RESULTS') {

		let x_axis_int = [];
		for (let i = 0; i < int_num; i++) {
			if ($(int_diagram_show[i]).val() != 1) {
				//not show to diagram checkbox
				continue;
			}
			const interval_id = $(int_data_id[i]).val();
			x_axis_int[interval_id] = false;
		}

		//#######################################################################
		//INTERVALS #############################################################
		
		//Int series
		//for each field
		for (let i = 0; i < int_num; i++) {
			if ($(int_diagram_show[i]).val() != 1) {
				//not show to diagram checkbox
				continue;
			}
			
			let interval_id = $(int_data_id[i]).val();
			let field_id = $(int_field_id[i]).val();
			

			let col_ATH = [];
			
			let rowspan = INTERVAL_DATA[interval_id].rowspan;
			let rowspan_period = INTERVAL_DATA[interval_id].rowspan_period;
			
			//data Name
			col_ATH.name = $(int_graph_name[i]).val();
			//console.log(interval_id, field_id, col_ATH.name);		
			
			//col_ATH Color
			if (colorIndex > 9) {
				colorIndex = 0;
			}
			col_ATH._colorIndex = colorIndex;
			
			//col_ATH Symbol
			if (symbolIndex > 4) {
				symbolIndex = 0;
			}
			col_ATH._symbolIndex = symbolIndex;

			//priority have the color of the data and then the axis color
			//if axis have 1 data series
			if (axis_count[$(int_axis[i]).val()][0] == 1) { 
				//if data have color
				if ($(int_color[i]).val()!='') {
					//give the color --and we put the same color to the axis too
					col_ATH.color = $(int_color[i]).val();
				}
				//if axis have color
				else if ($('#'+$(int_axis[i]).val()+' .cpA').val() != '') {
					//we give data the color of the axis
					col_ATH.color = $('#'+$(int_axis[i]).val()+' .cpA').val();
				}
			}
			//if axis have more than 1 data series
			else {
				//if it have color 
				if ($(int_color[i]).val()!='') {
					//give the color
					col_ATH.color = $(int_color[i]).val();
				}
			}
			

			//data Y-Axis
			col_ATH.yAxis = $(int_axis[i]).val();

			//data Labels
			col_ATH.dataLabels = {
				enabled: (($(int_labels[i]).val() == 'true') ? true : false)
			};

			//data Markers
			col_ATH.marker = {
				enabled: (($(int_markers[i]).val() == 'null') ? null : ($(int_markers[i]).val() == 'true') ? true : false)
			};
			
			//chart Type
			const type = $(int_type[i]).val();
			col_ATH.type = type;
			
			if (type == 'column') {
				col_ATH.zIndex = -1;
				col_ATH.pointPadding = 0.1;
				col_ATH.groupPadding = 0;
				col_ATH.dataLabels = { enabled: ($(int_labels[i]).val() == 'true') ? true : false, inside: true };
				
				//if they set point_range
				if ($(int_p_range[i]).val() != 0) {
					col_ATH.pointRange = $(int_p_range[i]).val() * 60 * 1000;
					//col_ATH.groupPadding = 0.05;
				}
				//if "auto"
				else {
					const pointRange = Chart__Rowspan_Period__2__Milliseconds__Get(rowspan, rowspan_period);

					//zIndex
					col_ATH.zIndex = -Chart__Rowspan_Period__2__zIndex__Get(rowspan, rowspan_period);
					
					/*if (rowspan_period != 'lines') {*/
						//console.log(rowspan, rowspan_period, pointRange);
						col_ATH.pointRange = pointRange;
						col_ATH.pointInterval = pointRange;
						//col_ATH.pointPlacement = 'between';
						col_ATH.grouping = true; //here we want it true //the RAW should be false --not matter in which xAxis is
						//col_ATH.pointPadding = 0.2; //def=0.1 
						//grouping: false,
						//plotOptions: {column: {crisp: false}}
						//col_ATH.pointStart: Date.UTC(x_min[0],(x_min[1]-1),x_min[2], x_min[3],x_min[4],x_min[5]); //Date.UTC(2009, 0, 1)
						//col_ath X-Axis
						
						//extra x-axis enabled
						if (INTERVAL_DATA[interval_id].formula_X_axis_show == 1) {
							//if we not tie the column with the extra x-axis the normal x-axis will have problems with zooming
							col_ATH.xAxis = x_axis_index - (x_axis_int[interval_id] === false ? 0 : 2); //sub -2 for the same interval bcz we add them first
							//add only the first time for each interval
							if (x_axis_int[interval_id] === false) {
								x_axis_int[interval_id] = true;
								x_axis_index++;
								x_axis_index++; //2 times bcz we have the extra labels "Interval n"
							}
						}
					/*}*/
				}
			}
			else if (type == 'scatter') {
				col_ATH.marker = {enabled:true};
			}
			else {
				col_ATH.dashStyle = $(int_line[i]).val();
			}
			//console.log(col_ATH, interval_id, field_id);
			

			let show_athlete_name = false;
			//add athlete name if checked
			if ($(int_ath_name_show[i]).val() == '1') {
				show_athlete_name = true;
			}
			

			//until here we have the basic options for the field -- from here and after we want to put the data each athlete separately

			const temp_name = col_ATH.name;
			//for each athlete data
			Object.keys(INTERVAL_DATA[interval_id][field_id]).forEach(function(row, index){
				//if it is a number then it is athlete data --else it is option
				if (parseInt(row) == row) {
					const ath_id = INTERVAL_DATA[interval_id][field_id][row].ath_id;
					if (show_athlete_name) {
						if (ath_id == 'INT' || ath_id == 'INTSC') {
							col_ATH.name = temp_name + ' (' + Object.values(V_Selected__Athletes__ID_Name).join(', ') + ')'; //add athletes names
						} else {
							col_ATH.name = temp_name + ' (' + V_Selected__Athletes__ID_Name[ath_id] + ')'; //add ath name
						}
					}
					col_ATH.data = $.extend(true, [], INTERVAL_DATA[interval_id][field_id][row].data); //clone
					col_ATH.stack = ath_id;

					//INTERVAL_DATA Color
					if (colorIndex > 9) {
						colorIndex = 0;
					}
					col_ATH._colorIndex = colorIndex;

					//INTERVAL_DATA Symbols
					if (symbolIndex > 4) {
						symbolIndex = 0;
					}
					col_ATH._symbolIndex = symbolIndex;

					//increase indexes
					colorIndex++;
					symbolIndex++;

					//add series INTERVAL_DATA to chart
					// chart ############################################
					V_Chart.addSeries(col_ATH, false); //false=not render each added series --too slow
					// chart ############################################
					//console.log(interval_id, field_id, row, col_ATH, col_ATH.data);
				}
			});
		}
		//INTERVALS end #########################################################
		//#######################################################################
	}


	
	//#######################################################################
	//FORMS ##################################################################
	
	//Data series
	for (let i = 0; i < data_num; i++)
	{
		//only number fields
		if (V_Not_Show_in_Diagram_Types_arr.indexOf($(data_field_type[i]).val()) > -1) {
			continue;
		}
		//unchecked 'show to diagram' checkbox
		if ($(data_diagram_show[i]).val() != 1) {
			continue;
		}

		const ath_id 		= $(data_athlete_id[i]).val();
		const form_id 		= $(data_form_id[i]).val();
		const field_name 	= $(data_field_name[i]).val();
		
		//delete some options --bcz if it get them from column then it keeps them 
		delete USED_DATA[ath_id][form_id][field_name].zIndex; 
		delete USED_DATA[ath_id][form_id][field_name].pointRange; 
		delete USED_DATA[ath_id][form_id][field_name].pointInterval; 
		delete USED_DATA[ath_id][form_id][field_name].pointPadding; 
		delete USED_DATA[ath_id][form_id][field_name].groupPadding; 
		delete USED_DATA[ath_id][form_id][field_name].pointPlacement; 
		delete USED_DATA[ath_id][form_id][field_name].dataLabels; //delete bcz in column we give something extra
		delete USED_DATA[ath_id][form_id][field_name].marker; 
		delete USED_DATA[ath_id][form_id][field_name].color; //delete color in case is null now
		delete USED_DATA[ath_id][form_id][field_name].xAxis; 

	
		const rowspan 			= USED_DATA[ath_id][form_id][field_name].rowspan;
		const rowspan_period 	= USED_DATA[ath_id][form_id][field_name].rowspan_period;
		const rowspan_after 	= USED_DATA[ath_id][form_id][field_name].rowspan_after;
		

		//data Name
		let data_graph_name_val = $(data_graph_name[i]).val();
		//add athlete name if checked
		if ($('#Diagram__Athlete_Name__Show_' + ath_id).val() == '1') {
			data_graph_name_val += ' (' + $('#Diagram__Athlete_Name_' + ath_id).val() + ')';
		}
		//add form name if checked
		if ($('#Diagram__Form_Name__Show_' + ath_id + '_' + form_id).val() == '1') {
			data_graph_name_val += ' [' + $('#Diagram__Form_Name_' + ath_id + '_' + form_id).val() + ']';
		}
		//set data Name	
		USED_DATA[ath_id][form_id][field_name].name = data_graph_name_val;
		

		//data Color
		if (colorIndex > 9) {
			colorIndex = 0; //reset color
		}
		//set data Color
		USED_DATA[ath_id][form_id][field_name]._colorIndex = colorIndex;
		

		//data Symbol
		if (symbolIndex > 4) {
			symbolIndex = 0; //reset symbol
		}
		//set data Symbol
		USED_DATA[ath_id][form_id][field_name]._symbolIndex = symbolIndex;


		//priority have the color of the data and then the axis color
		//if axis have 1 data series
		if (axis_count[$(data_axis[i]).val()][0] == 1) { 
			//if data have color
			if ($(data_color[i]).val() != '') {
				//give the color --and we put the same color to the axis too
				USED_DATA[ath_id][form_id][field_name].color = $(data_color[i]).val();
			}
			//if axis have color
			else if ($('#' + $(data_axis[i]).val() + ' .cpA').val() != '') {
				//we give data the color of the axis
				USED_DATA[ath_id][form_id][field_name].color = $('#' + $(data_axis[i]).val() + ' .cpA').val();
			}
		}
		//if axis have more than 1 data series
		else {
			//if it have color 
			if ($(data_color[i]).val() != '') {
				//give the color
				USED_DATA[ath_id][form_id][field_name].color = $(data_color[i]).val();
			}
		}
		

		//data Y-Axis
		USED_DATA[ath_id][form_id][field_name].yAxis = $(data_axis[i]).val();

		//data Labels
		USED_DATA[ath_id][form_id][field_name].dataLabels = {
			enabled: (($(data_labels[i]).val() == 'true') ? true : false)
		};
		
		//data Markers
		USED_DATA[ath_id][form_id][field_name].marker = {
			enabled: (($(data_markers[i]).val() == 'null') ? null : ($(data_markers[i]).val() == 'true') ? true : false)
		};
		
		
		//chart Type
		const type = $(data_type[i]).val();
		USED_DATA[ath_id][form_id][field_name].type = type;
		
		if (type == 'column') {
			USED_DATA[ath_id][form_id][field_name].zIndex = -1;
			USED_DATA[ath_id][form_id][field_name].pointPadding = 0.1;
			USED_DATA[ath_id][form_id][field_name].groupPadding = 0;
			USED_DATA[ath_id][form_id][field_name].dataLabels = {
				enabled: ($(data_labels[i]).val() == 'true') ? true : false,
				inside: true
			};

			//zIndex
			USED_DATA[ath_id][form_id][field_name].zIndex = -Chart__Rowspan_Period__2__zIndex__Get(rowspan, rowspan_period);
			USED_DATA[ath_id][form_id][field_name].xAxis = 0;


			if (V_RESULTS_PAGE == 'RESULTS') {
				//always false here so it doesn't make group with the Intervals
				USED_DATA[ath_id][form_id][field_name].grouping = false;
			}


			//if they set point_range
			if ($(data_p_range[i]).val() != 0) {
				USED_DATA[ath_id][form_id][field_name].pointRange = $(data_p_range[i]).val() * 60 * 1000;
				USED_DATA[ath_id][form_id][field_name].pointInterval = $(data_p_range[i]).val() * 60 * 1000;
				//USED_DATA[ath_id][form_id][field_name].groupPadding = 0.05;
			}
			//if "auto"
			else {
				
				//pointRange
				const pointRange = Chart__Rowspan_Period__2__Milliseconds__Get(rowspan, rowspan_period);
				
				if (rowspan_period != 'lines') {
					//console.log(rowspan, rowspan_period, pointRange);
					USED_DATA[ath_id][form_id][field_name].pointRange = pointRange;
					USED_DATA[ath_id][form_id][field_name].pointInterval = pointRange;
					//USED_DATA[ath_id][form_id][field_name].pointPlacement = 'between';
					//USED_DATA[ath_id][form_id][field_name].pointStart: Date.UTC(x_min[0],(x_min[1]-1),x_min[2], x_min[3],x_min[4],x_min[5]);


					if (V_RESULTS_PAGE == 'FORMS_RESULTS') {
						USED_DATA[ath_id][form_id][field_name].grouping = false;
					}
				}
			}

			if (rowspan_period != 'lines') {
				//extra X-Axis enabled
				if (USED_DATA[ath_id][form_id][field_name].formula_X_axis_show == 1) {
					//if we not tie the column with the extra x-axis the normal x-axis will have problems with zooming
					USED_DATA[ath_id][form_id][field_name].xAxis = x_axis_index;
					x_axis_index++;
					x_axis_index++; //2 times bcz we have the extra labels "Interval n"
				}
			}
		}
		else if (type == 'scatter') {
			USED_DATA[ath_id][form_id][field_name].marker = { enabled: true };
		}
		else {
			USED_DATA[ath_id][form_id][field_name].dashStyle = $(data_line[i]).val();
		}
		

		//add data series to chart
		//false for not redraw, we do that at the end for performance
		// chart ############################################
		V_Chart.addSeries(USED_DATA[ath_id][form_id][field_name], false);
		// chart ############################################


		//increase indexes
		colorIndex++;
		symbolIndex++;
	}

	//FORMS ##################################################################
	//#######################################################################

	//DATA ##################################################################
	//#######################################################################

	//https://www.highcharts.com/blog/news/175-highcharts-performance-boost/
	// chart ############################################
	V_Chart.redraw();
	// chart ############################################



	//Export Filename --YYYYMMDD_REGmon Export_type_first name last name
	let filename = '';
	const today = moment().format('YYYYMMDD');

	if (V_RESULTS_PAGE == 'FORMS_RESULTS') {
		filename = LANG.DIAGRAM.EXPORT_FORMS_FILE_NAME.replace('{TODAY}', today).replace('{ATHLETES_NAMES}', V_Athlete_Name);
	}
	else { //RESULTS
		const athletes_names = Object.values(V_Selected__Athletes__ID_Name).join(', ');
		filename = LANG.DIAGRAM.EXPORT_RESULTS_FILE_NAME.replace('{TODAY}', today).replace('{ATHLETES_NAMES}', athletes_names);
	}

	//set the filename for exporting
	V_Chart.options.exporting.filename = filename;

	
	//auto graph_fullscreen in mobile
	//not work --require user input
	/*if ($('.touchevents').length && V_OPEN_TEMPLATE != '0') { //if mobile
		setTimeout(function(){
			$('.graph_fullscreen span.expand').trigger("click");
		}, 3000);
	}*/

	//V_OPEN_TEMPLATE = '0'; //reset for dashboard link

	Debug1('1.Chart.Update.--', '-', get_Function_Name(), '-', '--END--', '-USED_DATA:', [USED_DATA]);
	Debug1('*****************************************');
} //end Chart__Update
//#################################################
//#################################################



// HIGHCHARTS #################################################

Highcharts_Options = {
    time: {
        useUTC: false,
        //timezone: 'Europe/Berlin'
    },
	/*global: {
		canvasToolsURL: 'js/Highcharts/js/modules/canvas-tools.js'
	},*/
	lang: {
		loading: LANG.DIAGRAM.LOADING,
		noData: LANG.DIAGRAM.NO_DATA_SELECTED, //def:'No data to display'
		contextButtonTitle: LANG.DIAGRAM.EXPORT_MENU_TITLE, 
		downloadPNG: LANG.DIAGRAM.DOWNLOAD_PNG_IMAGE, 
		downloadJPEG: LANG.DIAGRAM.DOWNLOAD_JPEG_IMAGE, 
		downloadPDF: LANG.DIAGRAM.DOWNLOAD_PDF_DOC, 
		downloadSVG: LANG.DIAGRAM.DOWNLOAD_SVG_IMAGE, 
		downloadCSV: LANG.DIAGRAM.DOWNLOAD_CSV_DOC,
		downloadXLS: LANG.DIAGRAM.DOWNLOAD_XLSX_DOC,
	   //viewData: 'View data table'
		invalidDate: '',
		printChart: LANG.DIAGRAM.PRINT_DIAGRAM, 
		resetZoom: LANG.DIAGRAM.RESET_ZOOM,
		resetZoomTitle: LANG.DIAGRAM.RESET_ZOOM_LEVEL_1_1,
		months: LANG.DIAGRAM.MONTHS_ARRAY,
		weekdays: LANG.DIAGRAM.WEEKDAYS_ARRAY,
		shortMonths: LANG.DIAGRAM.MONTHS_ARRAY,
		shortWeekdays: LANG.DIAGRAM.WEEKDAYS_SHORT_ARRAY
	},
	credits: {
		enabled: false,
		text: 'REGman.org',
		href: 'https://www.regman.org'
	},
	noData: {
		style: {
			fontSize: '15px',
			color: '#303030'
		}
	},
    loading: { //not used
		labelStyle: {
			fontSize: '30px',
			top: '35%'
		}
    },
	chart: {
		zoomType: 'xy',
		alignTicks: false,
		showAxes: true,
		//ignoreHiddenSeries: false //def:true  If true, the axes will scale to the remaining visible series once one series is hidden. If false, hiding and showing a series will not affect the axes or the other series
	},
	title: { text: '' },
	subtitle: { text: '' },
	xAxis: {
		type: 'datetime',
		crosshair: true,
		//minPadding:0,
		//maxPadding:0,
		minTickInterval: 60 * 1000, //per minute the minimum
		//gridLineWidth: 0,
		//gridLineColor: "#cccccc",
		dateTimeLabelFormats: { //defs
			millisecond: '%H:%M:%S.%L',
			second: '%H:%M:%S',
			minute: '%H:%M',
			hour: '%H:%M',
			day: '%e. %b',
			week: '%e. %b',
			month: '%b \'%y',
			//month: '%d.%m.%y %H:%M',
			year: '%Y'
		},
		startOnTick: true, //def:false
		//endOnTick: true, //def:false
		//showFirstLabel: true, //def:true
		//showLastLabel: true, //def:true
	},
	yAxis: {
		title:'',
		//show the axis line and title when the axis has no data. Defaults to true
		showEmpty: false //we want false
		//min: 0,
		//max: 200,
		//endOnTick: true, //def:true
		//showFirstLabel: true, //def:true
		//showLastLabel: true, //def:true
		//startOnTick: true //def:true
	},
	tooltip: {
		//http://jsfiddle.net/gh/get/jquery/1.7.2/highcharts/highcharts/tree/master/samples/highcharts/studies/tooltip-split/
		shared: true,
		dateTimeLabelFormats: { //defs
			/*millisecond:"%A, %b %e, %H:%M:%S.%L",
			second:"%A, %b %e, %H:%M:%S",
			minute:"%A, %b %e, %H:%M",
			hour:"%A, %b %e, %H:%M",
			day:"%A, %b %e, %Y",
			week:"Week from %A, %b %e, %Y",
			month:"%B %Y",
			year:"%Y"*/
			millisecond:"%A, %e. %b %Y, %H:%M:%S",
			second:"%A, %e. %b %Y, %H:%M:%S",
			minute:"%A, %e. %b %Y, %H:%M",
			hour:"%A, %e. %b %Y, %H:%M",
			day:"%A, %e. %b %Y, %H:%M",
			week:"%A, %e. %b %Y, %H:%M",
			month:"%A, %e. %b %Y, %H:%M",
			year:"%A, %e. %b %Y, %H:%M"
		}
	},
	plotOptions: {
		series: {
			connectNulls: true,
			marker: {
				enabled: null
			},
			states: {
				hover: {
					enabled: true,
					lineWidth: 4
				}
			}
		},
		scatter: {
			tooltip: {
				pointFormat: 'x: {point.x:%A, %e. %b %Y, %H:%M}<br> y: <b>{point.y}</b>'
			}
        }
        /*column: {
			grouping: false
            //pointPlacement: 'between'
        }*/
	},
	exporting: {
		/* // specific options for the exported image
		chartOptions: {
			plotOptions: {
				series: {
					dataLabels: {
						enabled: true
					}
				}
			}
		},*/
		filename: 'Diagramm',
		//scale: 3, //def=2
		sourceWidth: 1120,
		sourceHeight: 500,
		fallbackToExportServer: false,
		url: "https://regman.org/",
		csv: {
			dateFormat: '%d.%m.%Y %H:%M:%S'
		}
	}
};	

if (V_RESULTS_PAGE == 'RESULTS') {
	Highcharts_Options.xAxis.labels = {
		autoRotation: [-10, -20, -30, -40, -50, -60, -70, -80, -90]
	};
}

if (V_RESULTS_PAGE == 'AXIS') {
	Highcharts_Options.xAxis = {
		categories: ['Wert']
	};
	Highcharts_Options.yAxis = {
		title: ''
	};
}

Highcharts.setOptions(Highcharts_Options);


//showEmpty: Whether to show the axis line and title when the axis has no data. Defaults to true.
//showEmpty not work if axis have min and max --workaround this problem
Highcharts.Axis.prototype.hasData = function () {
	return this.hasVisibleSeries;
};

// HIGHCHARTS #################################################


// DIAGRAM ####################################################
//#############################################################

