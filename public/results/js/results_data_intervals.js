if (!Production_Mode) "use strict"; //remove on production so "const" works on iOS Safari


jQuery(function() {
	//add interval data
	$('#DATA__Interval__Add').on('click', function() {
		if (!Object.keys(V_FORMS_DATA).length) {
			//if not data return false
			return false;
		}

		loading.show();
		setTimeout(function () { //for loading
			const formula_cells = 1;
			const formula_period = 'days';
			const formula_X_axis_show = 0;

			Fieldset__Interval__Init(formula_cells, formula_period, formula_X_axis_show);
		}, 0);
	});
	
	//open Template from Dashboard link
	if (V_OPEN_TEMPLATE != '0') {
		const temp_arr = (V_OPEN_TEMPLATE+'').split('__'); //189__0__26.11.2019__02.12.2019
		const save_id = temp_arr[0];
		
		//select template
		$('#Select__Results_Templates').val(save_id);

		//load template
		$('#Results_Template__Load').trigger("click");
	}
});



//###############################################
// FORMULA ######################################

function Formula__Interval__Get_Max_Number() {
	Debug2('      4.Calc.Num.', '-', get_Function_Name(), '-', [...arguments]);

	let max = 0;
	$('.interval_group').each(function() {
		const interval_id = parseInt($(this).attr('id').split('_')[1]);
		max = (interval_id > max) ? interval_id : max;
	});
	return max + 1;
}


function Formula__Interval_Form_Field__Get_Max_Number(interval_id) {
	Debug2('      4.Calc.Num.', '-', get_Function_Name(), '-', [...arguments]);

	let max = 0;
	$('#div-INTERVAL-FORM_'+interval_id+' .Calculation_Raw, '+
	  '#div-INTERVAL-FORM_'+interval_id+' .Calculation_Interval, '+
	  '#div-INTERVAL-FORM_'+interval_id+' .Calculation_Interval_SingleColumn').each(function() 
	{
		const calc_id = parseInt($(this).attr('id').split('_')[2]);
		max = (calc_id > max) ? calc_id : max;
	});
	return max + 1;
}


function Formula__Interval__Get_ALPHA_id(num, second_pass) {
	Debug2('      4.Calc.Num.', '-', get_Function_Name(), '-', [...arguments]);

	let prefix = '';
	if (!second_pass) {
		num = num - 1; //adjust num only on first pass
		prefix = 'B'; //put B only on first pass
	}
	
	//in case ALPHA_id is bigger than 26 letters (English Alphabet)
	const extra_ALPHA_id = (num >= 26 ? Formula__Interval__Get_ALPHA_id(((num / 26 >> 0) - 1), true) : '');
	const ALPHA_id = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'[num % 26 >> 0];

	return prefix + extra_ALPHA_id + ALPHA_id;
}


function Formula__Interval_Form_Field__Get_Empty_Data_Rows(interval_id, cell_id) {
	Debug1('      4.Calc.Num.', '-', get_Function_Name(), '-', [...arguments]);

	let data_rows = [];
	V_INTERVAL_DATA[interval_id].data.forEach(function (row, index) {
		//we get row[int_start, int_end-1, int_middle]
		data_rows[index] = [];
		data_rows[index][0] = row[2];
		data_rows[index][1] = 0;
		data_rows[index][2] = cell_id + (index + 1);
		//data_rows[index][2] = [cell_id + (index + 1), 0]; //or maybe an array
	});
	

	return data_rows;
}

// FORMULA ######################################
//###############################################


	
//################################################
// INTERVAL Json #################################

function INTERVAL_DATA__Interval__Init(interval_id, rowspan, rowspan_period) {
	Debug1('    3.Json.Init.', '-', get_Function_Name(), '-', [...arguments]);

	//init V_INTERVAL_DATA
	V_INTERVAL_DATA[interval_id] = {};
	V_INTERVAL_DATA[interval_id].data_date = V_DATE_FROM + '|' + V_DATE_TO;
	V_INTERVAL_DATA[interval_id].rowspan = rowspan;
	V_INTERVAL_DATA[interval_id].rowspan_period = rowspan_period;
	V_INTERVAL_DATA[interval_id].formula_X_axis_show = parseInt($('#formula_X_axis_show_INT_' + interval_id).val());

	//make V_INTERVAL_DATA data
	V_INTERVAL_DATA[interval_id].data = INTERVAL_DATA__Interval_Rows__Get_All(rowspan, rowspan_period);
} //end INTERVAL_DATA__Interval__Init


function INTERVAL_DATA__Interval__Remove(interval_id) {
	Debug1('    3.Json.Remove.Interval.', '-', get_Function_Name(), '-', [...arguments]);

	delete V_INTERVAL_DATA[interval_id];
}


function INTERVAL_DATA__Interval_Field__Add(interval_id, form_id, col_id, col_field_name, col_field_data, cell_id, table_column, u_col_id, ath_id, is_single_column) {
	Debug1('    3.Json.Add.Field.', '-', get_Function_Name(), '-', [...arguments]);

	//init interval_id if not already
	if (!V_INTERVAL_DATA[interval_id]) {
		V_INTERVAL_DATA[interval_id] = {};
	}
	
	//init interval field
	if (!V_INTERVAL_DATA[interval_id][col_id]) {
		V_INTERVAL_DATA[interval_id][col_id] = {};
	}

	V_INTERVAL_DATA[interval_id][col_id].form_id = form_id;
	V_INTERVAL_DATA[interval_id][col_id].name = col_field_name.replace('Formula', LANG.RESULTS.CALCULATION);
	V_INTERVAL_DATA[interval_id][col_id].is_single_column = is_single_column;

	//TODO: check why
	//each athlete 2 objects
	V_INTERVAL_DATA[interval_id][col_id][u_col_id] = {};
	V_INTERVAL_DATA[interval_id][col_id][u_col_id].ath_id = ath_id;
	V_INTERVAL_DATA[interval_id][col_id][u_col_id].cell_id = cell_id;
	V_INTERVAL_DATA[interval_id][col_id][u_col_id].table_column = table_column;
	V_INTERVAL_DATA[interval_id][col_id][u_col_id].data = col_field_data;
}


function INTERVAL_DATA__Interval_Form_Field__Remove(interval_id, col_id) {
	Debug1('    3.Json.Remove.Field.', '-', get_Function_Name(), '-', [...arguments]);

	delete V_INTERVAL_DATA[interval_id][col_id];
}


function INTERVAL_DATA__Table_Column_Num__Update(interval_id) {
	Debug1('    3.Json.Update.ColumnNum.', '-', get_Function_Name(), '-', [...arguments]);

	let table_column = 1;
	$('table#Table__Interval_Form_' + interval_id + ' thead tr.thead_row3 th').each(function (index, th) {
		//7 sticky columns
		if (index > 6) {
			const col_id = $(this).attr('data-colnum');
			const u_col_id = $(this).attr('data-u_colnum');
			
			V_INTERVAL_DATA[interval_id][col_id][u_col_id].table_column = table_column;

			table_column++;
		}
	});
}


function INTERVAL_DATA__Cells_Count__Update(interval_id, field_num, ath_index, ath_id, formula_sub_period = false, formula_sub_period_type = false, formula_sub_period_start = false, formula_sub_period_end = false) {
	Debug1('    3.Json.Update.CellsCount.', '-', get_Function_Name(), '-', [...arguments]);

	const this_V_INTERVAL_DATA = V_INTERVAL_DATA[interval_id];
	const form_id = this_V_INTERVAL_DATA[field_num].form_id;
	const ath_V_INTERVAL_DATA = V_INTERVAL_DATA[interval_id][field_num][ath_index].data;

	//reset DATA Cells Count 
	ath_V_INTERVAL_DATA.forEach(function (interval_data_arr, interval_data_index) {
		//cut from index 3, 3 items
		interval_data_arr.splice(3, 3);
	});
	
	let athletes_index_arr = [];

	this_V_INTERVAL_DATA.data.forEach(function(interval_data_arr, interval_data_index) {

		if (!athletes_index_arr[ath_id]) {
			//init ath index
			athletes_index_arr[ath_id] = 0;
		}
		
		//get the first data we found, we care only about timestamps
		const ath_data = (V_USED_DATA[ath_id] && V_USED_DATA[ath_id][form_id]) ? Object.entries(V_USED_DATA[ath_id][form_id])[0][1].data : [];
		const ath_data_len = ath_data.length;
		
		for (let i = athletes_index_arr[ath_id]; i < ath_data_len; i++) {
			//in main interval
			if (ath_data[i][0] >= interval_data_arr[0] &&
				ath_data[i][0] <= interval_data_arr[1])
			{
				//console.log('main', moment(ath_data[i][0]).format('YYYY-MM-DD HH:mm:ss') +'>='+ moment(interval_data_arr[0]).format('YYYY-MM-DD HH:mm:ss') +'&&'+ moment(ath_data[i][0]).format('YYYY-MM-DD HH:mm:ss') +'<='+ moment(interval_data_arr[1]).format('YYYY-MM-DD HH:mm:ss'),  ath_data[i][0] +'>='+ interval_data_arr[0] +'&&'+ ath_data[i][0] +'<='+ interval_data_arr[1],  (ath_data[i][0] >= interval_data_arr[0] && ath_data[i][0] <= interval_data_arr[1]));

				//we have sub period
				if (formula_sub_period) {
					//if not in sub period
					if (!DATE__is_in_Sub_Period(ath_data[i][0], formula_sub_period_type, formula_sub_period_start, formula_sub_period_end)) {
						continue;
					}
				}


				if (!ath_V_INTERVAL_DATA[interval_data_index][3]) {
					//first cell
					ath_V_INTERVAL_DATA[interval_data_index][3] = i + 1;
				}

				//last cell
				ath_V_INTERVAL_DATA[interval_data_index][4] = i + 1;

				//count cells included
				const cells_count = (ath_V_INTERVAL_DATA[interval_data_index][4] - ath_V_INTERVAL_DATA[interval_data_index][3]) + 1;
				ath_V_INTERVAL_DATA[interval_data_index][5] = cells_count;
			}
			//after interval end
			else if (ath_data[i][0] >= interval_data_arr[1]) {
				//we keep the last index so we can start from that in next loop
				athletes_index_arr[ath_id] = i;
				break;
			}
		}
	});
} //end INTERVAL_DATA__Cells_Count__Update


function INTERVAL_DATA__Interval_Rows__Get_All(rowspan, rowspan_period) {
	Debug1('    3.Array.IntervalRows.', '-', get_Function_Name(), '-', [...arguments]);

	let next_time_moment = V_DATE_FROM_moment.clone();
	let all_interval_rows_array = [];

	//make virtual array with all time interval rows
	while (true) {
		const int_start = next_time_moment.valueOf(); //start of interval
		const int_end = next_time_moment.clone().add(rowspan, rowspan_period).valueOf(); //end of interval  -1
		let int_middle = next_time_moment.clone().add(rowspan / 2, rowspan_period).valueOf();//middle of interval
		
		//middle of interval --not work for days/weeks/months
		if (rowspan_period == 'days' || rowspan_period == 'weeks' || rowspan_period == 'months') {
			int_middle = int_start + ((int_end - int_start) / 2); //middle of interval
		}

		/*console.log(
			'1----', next_time_moment.format('YYYY-MM-DD HH:mm:ss'), //start of interval
			'2----', rowspan, rowspan_period, next_time_moment.clone().add(rowspan, rowspan_period).format('YYYY-MM-DD HH:mm:ss'), //end of interval  -1
			'3----', rowspan/2, rowspan_period, next_time_moment.clone().add(rowspan/2, rowspan_period).format('YYYY-MM-DD HH:mm:ss') //middle of interval
		);*/
		
		all_interval_rows_array.push([
			int_start,
			int_end-1,
			int_middle
		]);
		
		next_time_moment = next_time_moment.add(rowspan, rowspan_period); //add 1 period each time
		
		if (next_time_moment > V_DATE_TO_moment) {
			//console.log('out of loop', next_time_moment.format('YYYY-MM-DD HH:mm') +'>'+ V_DATE_TO_moment.format('YYYY-MM-DD HH:mm'));
			break;
		}
	}
	//console.log('INTERVAL_DATA__Interval_Rows__Get_All', all_interval_rows_array);

	return all_interval_rows_array;
} //end INTERVAL_DATA__Interval_Rows__Get_All


//copy formula results to V_INTERVAL_DATA 
function INTERVAL_DATA__Formula__Update_After_Calculate(calx, interval_id) {
	Debug1('  2.Calc.Json.', '-', get_Function_Name(), '-', [...arguments]);

	if (!calx.affectedCell.length || calx.affectedCell[0] == 'NN1') {
		//we not need the rest when we 'update' calc .. only when 'calculate'
		return;
	}
	
	const effected_data = {};

	//get effected field names
	calx.affectedCell.forEach(function (cell, index) //cell = BA1
	{
		if (cell[0] == 'B') {
			const calx_cell = calx.getCell(cell); //Obj
			const num = cell.replace(/^\D+/g, ''); //1
			const column = cell.replace(num, ''); //BA
			const field_value = calx_cell.getValue(); //calculation result
			//console.log(cell, field_value, column, num); //BA1 11 BA 1
			
			//BABC-> A=col_num/calc, B or BC=col_num_idx/user
			let int_col = Formula__Number_from_Alpha__Get(column.substring(1, 2)); //for B[A]A we pass A and get 1 col_num
			let user_idx = Formula__Number_from_Alpha__Get(column.substring(2)); //for BA[A] we pass A and get 1 idx

			//when we have more than 26 users - BAAA
			if (column.length == 4) {
				user_idx = Formula__Number_from_Alpha__Get(column.substring(2, 4)); //for BA[AA] we pass AA and get 27
			}

			if (!effected_data[int_col]) {
				effected_data[int_col] = {};
			}

			if (!effected_data[int_col][user_idx]) {
				effected_data[int_col][user_idx] = [];
			}

			effected_data[int_col][user_idx].push([cell, field_value]);
		}
	});
	//console.log('effected_data', effected_data);


	//set null to all effected_data lines value field
	Object.keys(effected_data).forEach(function (int_col) {
		Object.keys(effected_data[int_col]).forEach(function (user_idx)
		{
			if (V_INTERVAL_DATA[interval_id] &&
				V_INTERVAL_DATA[interval_id][int_col] &&
				V_INTERVAL_DATA[interval_id][int_col][user_idx])
			{
				//console.log(int_col, user_idx, effected_data[int_col][user_idx]);
				V_INTERVAL_DATA[interval_id][int_col][user_idx].data.forEach(function (line, index) {
					line[1] = null;
				});
			}
		});
	});
	
	
	//set values from effected_data to V_INTERVAL_DATA
	Object.keys(effected_data).forEach(function (int_col) {
		Object.keys(effected_data[int_col]).forEach(function (user_idx)
		{
			let new_data = effected_data[int_col][user_idx];
			let new_data_len = new_data.length;
			//console.log('effected', int_col, user_idx, new_data);

			if (V_INTERVAL_DATA[interval_id] &&
				V_INTERVAL_DATA[interval_id][int_col] &&
				V_INTERVAL_DATA[interval_id][int_col][user_idx])
			{
				let i_row = 0;
				let this_DATA = V_INTERVAL_DATA[interval_id][int_col][user_idx];

				this_DATA.data.forEach(function (data_arr, data_index) {
					for (let i=i_row; i<new_data_len; i++) {
						const cell_id = new_data[i][0];
						const field_value = new_data[i][1];
						if (cell_id == data_arr[2]) {
							//round number + parseFloat for diagram
							data_arr[1] = numRound(parseFloat(field_value));
							i_row++;
							break;
						}
						else {
							//if not find it in first pass then it is later, 
							//not need to continue the loop, go to the next each
							return;
						}
					};
				});
			}
		});
	});
} //end INTERVAL_DATA__Formula__Update_After_Calculate


// INTERVAL Json #################################
//################################################




//###############################################
// DATE #########################################

function DATE__is_in_Sub_Period(t_data, formula_sub_period_type, formula_sub_period_start, formula_sub_period_end) {
	Debug1('  2.Date.Check.', '-', get_Function_Name(), '-', [...arguments]);

	if (formula_sub_period_type == 'minutes') {
		data_time = moment(parseInt(moment(moment(t_data).format('mm') + ':00', 'mm:ss').unix() + '000'));
		sub_start = parseInt(moment(formula_sub_period_start, 'mm').unix() + '000');
		sub_end   = parseInt(moment(formula_sub_period_end, 'mm').unix() + 59 + '000');
		//console.log('DATE__is_in_Sub_Period', 'minutes', moment(data_time).format('mm:ss') +'>='+ moment(sub_start).format('mm:ss') +'&&'+ moment(data_time).format('mm:ss') +'<='+ moment(sub_end).format('mm:ss'),  (data_time >= sub_start && data_time <= sub_end));
	}
	else if (formula_sub_period_type == 'hours') {
		data_time = moment(parseInt(moment(moment(t_data).format('HH:mm') + ':00', 'HH:mm:ss').unix() + '000'));
		sub_start = parseInt(moment(formula_sub_period_start, 'HH:mm').unix() + '000');
		sub_end   = parseInt(moment(formula_sub_period_end, 'HH:mm').unix() + 59 + '000');
		//console.log('DATE__is_in_Sub_Period', 'hours', moment(data_time).format('HH:mm:ss') +'>='+ moment(sub_start).format('HH:mm:ss') +'&&'+ moment(data_time).format('HH:mm:ss') +'<='+ moment(sub_end).format('HH:mm:ss'), (data_time >= sub_start && data_time <= sub_end));
	}
	else if (formula_sub_period_type == 'days') {
		data_time = moment(parseInt(moment(moment(t_data).format('DD'), 'DD').unix() + '000'));
		sub_start = parseInt(moment(formula_sub_period_start, 'DD').unix() + '000');
		sub_end   = parseInt(moment(formula_sub_period_end, 'DD').unix() + '000');
		//console.log('DATE__is_in_Sub_Period', 'days', moment(data_time).format('DD') +'>='+ moment(sub_start).format('DD') +'&&'+ moment(data_time).format('DD') +'<='+ moment(sub_end).format('DD'), (data_time >= sub_start && data_time <= sub_end));
	}
	else { //if (formula_sub_period_type == 'months') {
		data_time = moment(parseInt(moment(moment(t_data).format('MM'), 'MM').unix() + '000'));
		sub_start = parseInt(moment(formula_sub_period_start, 'MM').unix() + '000');
		sub_end   = parseInt(moment(formula_sub_period_end, 'MM').unix() + '000');
		//console.log('DATE__is_in_Sub_Period', 'months', moment(data_time).format('MM') +'>='+ moment(sub_start).format('MM') +'&&'+ moment(data_time).format('MM') +'<='+ moment(sub_end).format('MM'), (data_time >= sub_start && data_time <= sub_end));
	}

	//is in sub period
	if (data_time >= sub_start && data_time <= sub_end) {
		return true;
	}

	return false;
} // end DATE__is_in_Sub_Period

// DATE #########################################
//###############################################




//################################################
// TABLE #########################################

//update interval table FORM name Colspan
function Table__Interval_Form__Colspan__Update(interval_id) {
	Debug1('    3.Table.Update.Colspan.', '-', get_Function_Name(), '-', [...arguments]);

	const $data_table = $('table#Table__Interval_Form_' + interval_id);
	const colSpan = $data_table.find('thead tr.thead_row3 th').length;

	//set colSpan
	$data_table.find('thead tr.thead_row0 th.th_colspan').attr('colSpan', colSpan);
}


//add RAW data FIELD to INTERVAL table
function Table__Interval_Form_Field__RAW__Add(interval_id, col_field_name, field_num) {
	Debug1('    3.Table.Add.RAW.', '-', get_Function_Name(), '-', [...arguments]);

	const ath_len = V_Selected__Athletes__With_Data.length;
	const form_id = $('#Interval_Form_' + interval_id + '_' + field_num).val();
	const form_name = $('#Interval_Form_' + interval_id + '_' + field_num + ' option:selected').text() + ' {' + Formula__Interval__Get_ALPHA_id(field_num, false) + '}';
	const $data_table = $('table#Table__Interval_Form_' + interval_id);
	
	// header form
	const row1 = '<th colspan="' + (ath_len * 2) + '" class="th_cn_' + field_num + ' brw6" data-colnum="' + field_num + '">' + form_name + '</th>';
	$data_table.find('thead tr.thead_row1 th').eq(-1).after(row1);
	
	// headers
	V_Selected__Athletes__With_Data.forEach(function (ath_id, index) {
		const table_column = $data_table.find('thead tr.thead_row3 th').length - 6 + 1; //7 sticky columns
		const ath_index = index + 1;
		const line_id = 'L' + ath_index;
		const data_id = 'B' + ath_index;
		const cell_id = Formula__Interval__Get_ALPHA_id(field_num, false) + Formula__Get_ALPHA_id_noPrefix(ath_index, false);
		const cell_line_id = 'L' + Formula__Get_ALPHA_id_noPrefix(field_num, false) + Formula__Get_ALPHA_id_noPrefix(ath_index, false);
		const lang_field_name = col_field_name.replace('Formula', LANG.RESULTS.CALCULATION);
		const col_field_data = Formula__Interval_Form_Field__Get_Empty_Data_Rows(interval_id, cell_id);
		

		//make INT data array for this field_num User
		INTERVAL_DATA__Interval_Field__Add(interval_id, form_id, field_num, col_field_name, col_field_data, cell_id, table_column, ath_index, ath_id, false);
		

		// put athletes Cells Count into intervals
		INTERVAL_DATA__Cells_Count__Update(interval_id, field_num, ath_index, ath_id);
		

		let sep = 'brw3';
		if (ath_len == (index + 1)) {
			sep = 'brw6';
		}

		const row2 = ''+
			'<th colspan="2" class="th_cn_'+field_num+' th_'+data_id+' '+sep+'">'+
				V_Selected__Athletes__ID_Name[ath_id] +
			'</th>';
					
		const row3 = ''+
			'<th class="th_cn_'+field_num+' th_'+line_id+'" data-colnum="'+field_num+'" data-u_colnum="'+ath_index+'">'+
				cell_line_id +
			'</th>' +
			'<th class="th_cn_'+field_num+' th_'+data_id+' '+sep+'" data-colnum="'+field_num+'" data-u_colnum="'+ath_index+'">'+ 
				cell_id + 
			'</th>';
					
		const row4 = ''+
			'<th class="th_cn_'+field_num+' th_'+line_id + '">'+
				'#n' +
			'</th>'+
			'<th class="th_cn_'+field_num+' th_'+data_id+' '+sep+'">'+
				'<span class="field_name" data-name="' + col_field_name + '">' + lang_field_name + '</span>'+
			'</th>';
		
		//put headers to table
		$data_table.find('thead tr.thead_row2 th').eq(-1).after(row2);
		$data_table.find('thead tr.thead_row3 th').eq(-1).after(row3);
		$data_table.find('thead tr.thead_row4 th').eq(-1).after(row4);
		

		//fix headers colspan
		Table__Interval_Form__Colspan__Update(interval_id);


		// data
		const $data_table_tbody = $data_table.find('tbody');
		$data_table_tbody.find('tr').each(function (index) {
			const line = index + 1;
			const line_cell = cell_line_id + line;
			const data_cell = cell_id + line;
			const timestamp = col_field_data[index][0];
			const value = col_field_data[index][1];

			const td = ''+
				'<td data-cell="'+line_cell+'" title="'+line_cell+'" class="num td_cn_'+field_num+' td_'+line_id+'">'+
					value + 
				'</td>' +
				'<td data-cell="'+data_cell+'" title="'+data_cell+'" class="num td_cn_'+field_num+' td_'+data_id+' '+sep+'">'+
					value + 
				'</td>';
			
			//if already have tds
			if ($(this).find('td').length) {
				$(this).find('td').eq(-1).after(td);
			}
			else {
				//put first one after header
				$(this).find('th').eq(-1).after(td);
			}
		});
	});
} //end Table__Interval_Form_Field__RAW__Add


//add INT data FIELD to INTERVAL table
function Table__Interval_Form_Field__INT__Add(interval_id, col_field_name, field_num) {
	Debug1('    3.Table.Add.INT.', '-', get_Function_Name(), '-', [...arguments]);

	const ath_len = V_Selected__Athletes__With_Data.length;
	const form_id = 'INT';
	const form_name = LANG.RESULTS.INTERVAL + ' ' + interval_id + ' {' + Formula__Interval__Get_ALPHA_id(field_num, false) + '}';
	const $data_table = $('table#Table__Interval_Form_' + interval_id);

	// header form
	const row1 = '<th colspan="'+(ath_len*2)+'" class="th_cn_'+field_num+' brw6" data-colnum="'+field_num+'">'+form_name+'</th>';
	$data_table.find('thead tr.thead_row1 th').eq(-1).after(row1);
	
	// headers
	V_Selected__Athletes__With_Data.forEach(function(ath_id, index) {
		const table_column = $data_table.find('thead tr.thead_row3 th').length - 6 + 1; //7 sticky columns
		const ath_index = index + 1;
		const line_id = 'L' + ath_index;
		const data_id = 'B' + ath_index;
		const cell_id = Formula__Interval__Get_ALPHA_id(field_num, false) + Formula__Get_ALPHA_id_noPrefix(ath_index, false);
		const cell_line_id = 'L' + Formula__Get_ALPHA_id_noPrefix(field_num, false) + Formula__Get_ALPHA_id_noPrefix(ath_index, false);
		const lang_field_name = col_field_name.replace('Formula', LANG.RESULTS.CALCULATION);
		const col_field_data = Formula__Interval_Form_Field__Get_Empty_Data_Rows(interval_id, cell_id);
		

		//make INT data array for this field_num User
		INTERVAL_DATA__Interval_Field__Add(interval_id, form_id, field_num, col_field_name, col_field_data, cell_id, table_column, ath_index, ath_id, false);
		

		// put athletes Cells Count into intervals
		INTERVAL_DATA__Cells_Count__Update(interval_id, field_num, ath_index, ath_id);
		

		let sep = 'brw3';
		if (ath_len == (index + 1)) {
			sep = 'brw6';
		}

		const row2 = ''+
			'<th colspan="2" class="th_cn_'+field_num+' th_'+data_id+' '+sep+'">'+
				V_Selected__Athletes__ID_Name[ath_id] +
			'</th>';
		
		const row3 = ''+
			'<th class="th_cn_'+field_num+' th_'+line_id+'" data-colnum="'+field_num+'" data-u_colnum="'+ath_index+'">'+
				cell_line_id+
			'</th>' + 
			'<th class="th_cn_'+field_num+' th_'+data_id+' '+sep+'" data-colnum="'+field_num+'" data-u_colnum="'+ath_index+'">'+
				cell_id+
			'</th>';
		
		const row4 = ''+
			'<th class="th_cn_'+field_num+' th_'+line_id+'">'+
				'#n'+
			'</th>'+ 
			'<th class="th_cn_'+field_num+' th_'+data_id+' '+sep+'">'+
				'<span class="field_name" data-name="'+col_field_name+'">'+	lang_field_name +'</span>'+
			'</th>';

		//put headers to table
		$data_table.find('thead tr.thead_row2 th').eq(-1).after(row2);
		$data_table.find('thead tr.thead_row3 th').eq(-1).after(row3);
		$data_table.find('thead tr.thead_row4 th').eq(-1).after(row4);
		

		//fix headers colspan
		Table__Interval_Form__Colspan__Update(interval_id);


		// data
		const $data_table_tbody = $data_table.find('tbody');
		$data_table_tbody.find('tr').each(function(index){
			const line = index + 1;
			const line_cell = cell_line_id+line;
			const data_cell = cell_id+line;
			const timestamp = col_field_data[index][0];
			const value = col_field_data[index][1];

			const td = ''+
				'<td data-cell="'+line_cell+'" title="'+line_cell+'" class="num td_cn_'+field_num+' td_'+line_id+'">'+
					value +
				'</td>'+
				'<td data-cell="'+data_cell+'" title="'+data_cell+'" class="num td_cn_'+field_num+' td_'+data_id+' '+sep+'">'+
					value +
				'</td>';
			
			//if already have tds
			if ($(this).find('td').length) {
				$(this).find('td').eq(-1).after(td);
			}
			else {
				//put first one after header
				$(this).find('th').eq(-1).after(td);
			}
		});
	});
} //end Table__Interval_Form_Field__INT__Add


//add INTSC data FIELD to INTERVAL table
function Table__Interval_Form_Field__INTSC__Add(interval_id, col_field_name, field_num) {
	Debug1('    3.Table.Add.INTSC.', '-', get_Function_Name(), '-', [...arguments]);

	const form_id = 'INTSC';
	const form_name = LANG.RESULTS.INTERVAL + ' ' + interval_id + ' {' + Formula__Interval__Get_ALPHA_id(field_num, false) + '}';
	const $data_table = $('table#Table__Interval_Form_' + interval_id);
	
	// header form
	const row1 = '<th colspan="2" class="th_cn_' + field_num + ' brw6" data-colnum="' + field_num + '">' + form_name + '</th>';
	$data_table.find('thead tr.thead_row1 th').eq(-1).after(row1);
	
	// headers
	const table_column = $data_table.find('thead tr.thead_row3 th').length - 6 + 1; //7 sticky columns
	const ath_index = 1;
	const line_id = 'L' + ath_index;
	const data_id = 'B' + ath_index;
	const cell_id = Formula__Interval__Get_ALPHA_id(field_num, false) + Formula__Get_ALPHA_id_noPrefix(ath_index, false);
	const cell_line_id = 'L' + Formula__Get_ALPHA_id_noPrefix(field_num, false) + Formula__Get_ALPHA_id_noPrefix(ath_index, false);
	const lang_field_name = col_field_name.replace('Formula', LANG.RESULTS.CALCULATION);
	const col_field_data = Formula__Interval_Form_Field__Get_Empty_Data_Rows(interval_id, cell_id);
	

	//make INT data array for this field_num User
	INTERVAL_DATA__Interval_Field__Add(interval_id, form_id, field_num, col_field_name, col_field_data, cell_id, table_column, ath_index, 'INTSC', true);
	

	// put athletes Cells Count into intervals
	//check 'INTSC' should be ath_id   ==>   we not have ath_id so we give 'INTSC'
	INTERVAL_DATA__Cells_Count__Update(interval_id, field_num, ath_index, 'INTSC');
	

	const sep = 'brw6';
	const row2 = ''+
		'<th colspan="2" class="th_cn_'+field_num+' th_'+data_id+' '+sep+'">'+
			Object.values(V_Selected__Athletes__ID_Name).join(', ') +
		'</th>';
	
	const row3 = ''+
		'<th class="th_cn_'+field_num+' th_'+line_id+'" data-colnum="'+field_num+'" data-u_colnum="'+ath_index+'">'+
			cell_line_id +
		'</th>' + 
		'<th class="th_cn_'+field_num+' th_'+data_id+' '+sep+'" data-colnum="'+field_num+'" data-u_colnum="'+ath_index+'">'+
			cell_id+
		'</th>';
	
	const row4 = ''+
		'<th class="th_cn_'+field_num+' th_'+line_id+'">'+
			'#n' +
		'</th>'+ 
		'<th class="th_cn_'+field_num+' th_'+data_id+' '+sep+'">'+
			'<span class="field_name" data-name="'+col_field_name+'">'+lang_field_name+'</span>'+
		'</th>';
				
	
	//put headers to table
	$data_table.find('thead tr.thead_row2 th').eq(-1).after(row2);
	$data_table.find('thead tr.thead_row3 th').eq(-1).after(row3);
	$data_table.find('thead tr.thead_row4 th').eq(-1).after(row4);
	

	//fix headers colspan
	Table__Interval_Form__Colspan__Update(interval_id);


	// data
	let $data_table_tbody = $data_table.find('tbody');
	$data_table_tbody.find('tr').each(function(index){
		const line = index + 1;
		const line_cell = cell_line_id + line;
		const data_cell = cell_id + line;
		const timestamp = col_field_data[index][0];
		const value = col_field_data[index][1];

		const td = ''+
			'<td data-cell="'+line_cell+'" title="'+line_cell+'" class="num td_cn_'+field_num+' td_'+line_id+'">'+
				value +
			'</td>'+
			'<td data-cell="'+data_cell+'" title="'+data_cell+'" class="num td_cn_'+field_num+' td_'+data_id+' '+sep+'">'+
				value +
			'</td>';
		
		//if already have tds
		if ($(this).find('td').length) {
			$(this).find('td').eq(-1).after(td);
		}
		else {
			//put first one after header
			$(this).find('th').eq(-1).after(td);
		}
	});
} //end Table__Interval_Form_Field__INTSC__Add


function Table__Interval_Form_Field__Remove(val) {
	Debug1('    3.Table.Remove.Field.', '-', get_Function_Name(), '-', [...arguments]);

	//TODO: check if interval field is in use by an interval calculation and not let the deletion
	
	const val_arr = val.split('|');
	const interval_id = val_arr[0];
	const field_num = val_arr[1];
	
	//remove table column
	$('#FI' + interval_id + ' .td_cn_' + field_num).remove();
	$('#FI' + interval_id + ' .th_cn_' + field_num).remove();

	//remove FIELD from V_INTERVAL_DATA Json
	INTERVAL_DATA__Interval_Form_Field__Remove(interval_id, field_num);

	Table__Interval_Form__Colspan__Update(interval_id);

	INTERVAL_DATA__Table_Column_Num__Update(interval_id);

	//if empty table -- 7 sticky row3 columns
	if ($('#FI' + interval_id + ' .thead_row3 th').length == 7) {
		//enable interval cells, period and form change
		$("#formula_cells_INT_" + interval_id).prop('disabled', false);
		$("#formula_period_INT_" + interval_id).prop('disabled', false);
	}
} //end Table__Interval_Form_Field__Remove

// TABLE #########################################
//################################################




//################################################
// HTML ##########################################

function Fieldset__Interval__Init(formula_cells, formula_period, formula_X_axis_show) {
	Debug1('************************************************');
	Debug1('1.Fieldset.Interval.Init.', '-', get_Function_Name(), '-', [...arguments]);


	let interval_id = Formula__Interval__Get_Max_Number();
	$('#Intervals_Data_Fieldsets').append(
		HTML_Template__Fieldset__Interval(interval_id, formula_cells, formula_period, formula_X_axis_show)
	);
	

	//init Interval Fieldset #########################################

	//fieldset collapsible
	collapse_set('#fs-INTERVAL_' + interval_id);


	//init fieldset expand/collapse
	Fieldset__Expand_Collapse__Init("#fs-INTERVAL_" + interval_id);


	//remove Interval Fieldset
	$(".Button__Fieldset__Interval__Remove").on('click',function() {
		const interval_id = $(this).attr('data-val');


		Debug1('  2.Calc.D. Destroy CALX ===============', [interval_id]);
		//remove sheet object
		$('#FI' + interval_id).calx('destroy');


		//remove fieldset
		$(this).next().remove();

		//remove close
		$(this).remove();


		//remove data
		INTERVAL_DATA__Interval__Remove(interval_id);
		
		
		Debug1('  2.Calc.R. Refresh CALX ===============', [interval_id]);
		//Refresh CALX
		$('#F' + interval_id).calx('refresh'); //Rebuild cell registry from the scratch.
		//$('#F'+ath_id).calx('reset'); //Reset the form inside sheet element to its original state.
		//$('#F'+ath_id).calx('update'); //Update cell registry against any change in the sheet element.
		//calculate CALX
		//$('#F'+interval_id).calx('calculate'); //Calculate the whole sheet and display the result in each cell. //no need
	});


	//on Interval values change -> init again

	$("#formula_cells_INT_" + interval_id).on('change', function () {
		INTERVAL_DATA__Interval__Init(interval_id, $(this).val(), $("#formula_period_INT_" + interval_id).val());

		//add interval table
		HTML_Template__Table__Interval__Add(interval_id);
	});

	$("#formula_period_INT_" + interval_id).on('change', function () {
		INTERVAL_DATA__Interval__Init(interval_id, $("#formula_cells_INT_" + interval_id).val(), $(this).val());

		//add interval table
		HTML_Template__Table__Interval__Add(interval_id);
	});

	$("#formula_X_axis_show_ck_INT_" + interval_id).on('change', function () {
		V_INTERVAL_DATA[interval_id].formula_X_axis_show = parseInt($('#formula_X_axis_show_INT_' + interval_id).val());
	});
	
	
	//button init
	$('#Button__Interval_Form_Field__for_RAW_Data__Add_' + interval_id).on('click', function () {
		loading.show();
		setTimeout(function () { //for loading
			Interval_Form_Field__for_RAW_Data__Add(interval_id);
			loading.hide();
		}, 0);
	});
	
	//button init
	$('#Button__Interval_Form_Field__for_INTERVAL_Data__Add_' + interval_id).on('click', function () {
		loading.show();
		setTimeout(function () { //for loading
			Interval_Form_Field__for_INTERVAL_Data__Add(interval_id);
			loading.hide();
		}, 0);
	});
	
	//button init
	$('#Button__Interval_Form_Field__for_INTERVAL_SingleColumn_Data__Add' + interval_id).on('click', function () {
		loading.show();
		setTimeout(function () { //for loading
			Interval_Form_Field__for_INTERVAL_SingleColumn_Data__Add(interval_id);
			loading.hide();
		}, 0);
	});
	
	//init Interval Fieldset end #########################################
	
	
	//init Interval Data 
	const INT_formula_cells = $("#formula_cells_INT_" + interval_id).val();
	const INT_formula_period = $("#formula_period_INT_" + interval_id).val();
	INTERVAL_DATA__Interval__Init(interval_id, INT_formula_cells, INT_formula_period);
	
	//add interval table
	HTML_Template__Table__Interval__Add(interval_id);
	
	
	//add Interval to V_Calx_Sheets
	if (V_Calx_Sheets.indexOf('#FI' + interval_id) == -1) {
		V_Calx_Sheets.push('#FI' + interval_id);
	}

	Debug1('  2int. Init CALX ===============', [interval_id]);
	//init CALX
	$('#FI' + interval_id).calx({
		onAfterCalculate : function() {
			INTERVAL_DATA__Formula__Update_After_Calculate(this, interval_id);
		}
		//autoCalculate: false,
		//checkCircularReference: true,
		//onAfterRender : function() {}
	});
	
	loading.hide();

	Debug1('1.********', '-', get_Function_Name(), '-', [...arguments], '--END--');
} //end Fieldset__Interval__Init


function Fieldset__Interval_Form_Field__Init(interval_id, field_name, field_num, is_interval_form, is_single_column) {
	Debug1('    3.Html.', '-', get_Function_Name(), '-', [...arguments]);

	const field_id = interval_id + '_' + field_num;


	//add fieldset
	$('#div-INTERVAL-FORM_' + interval_id).append(
		HTML_Template__Fieldset__Interval_Form_Field(interval_id, field_name, field_num, is_interval_form, is_single_column)
	);
	
	
	//only for RAW data calculation
	if (!is_interval_form) {

		//interval_form select on change
		$('#Interval_Form_' + field_id).on('change', function () {

			$('#Interval_Form_Field_' + field_id).html(
				Select_Options__RAW_Form_Fields__Get($(this).val())
			);

			if ($(this).val()) {
				//remove error if we have val
				$('#fs-INTERVAL-FORM-FIELD_' + field_id + ' legend span.Error_Form_Not_Found').remove();
			}

			if (V_INTERVAL_DATA[interval_id][field_num])
			{
				V_INTERVAL_DATA[interval_id][field_num].form_id = $(this).val();

				//get data for the new form
				V_Selected__Athletes__With_Data.forEach(function (ath_id, ath_index) {
					const cell_id = V_INTERVAL_DATA[interval_id][field_num][ath_index + 1].cell_id;
					const col_field_data = Formula__Interval_Form_Field__Get_Empty_Data_Rows(interval_id, cell_id);

					V_INTERVAL_DATA[interval_id][field_num][ath_index + 1].data = col_field_data;

					// put athletes Cells Count into intervals
					INTERVAL_DATA__Cells_Count__Update(interval_id, field_num, (ath_index + 1), ath_id);
				});
			}


			//change Table FIELDs after form select change
			const $data_table = $('table#Table__Interval_Form_' + interval_id);

			// header form
			const form_name = $('#Interval_Form_' + interval_id + '_' + field_num + ' option:selected').text() + ' {' + Formula__Interval__Get_ALPHA_id(field_num, false) + '}';

			$data_table.find('thead tr.thead_row1 .th_cn_' + field_num).text(form_name);
			
			// data
			const $data_table_tbody = $data_table.find('tbody');
			$data_table_tbody.find('tr td.td_cn_' + field_num).text('');
		});


		//init form field select
		$('#Interval_Form_' + field_id).trigger('change');

		//interval_form_field select on change
		$('#Interval_Form_Field_' + field_id).on('change', function () {
			$('#Interval_Form_Field_txt_' + field_id).val('{' + $('option:selected', this).attr('data-cell_id') + '}');
		});

		//init form field txt
		$('#Interval_Form_Field_' + field_id).trigger('change');
	}


	//init formula #######################################################
	Formula__Beautifier('formula_input_' + field_id).update();
	
	$("#formula_beautify_" + field_id).on('click', function () {
		$("#formula_beautify_open_" + field_id).toggle();
	});

	$("#formula_refresh_" + field_id).on('click', function () {
		try {
			Formula__Interval__Update_N_Calculate(interval_id, field_num);
		}
		catch (ex) {
			$(this).parent().next().removeClass('hidden').parent().addClass('has-error');
			console.log("caught " + ex);
		}
	});
	//init formula #######################################################

	
	//init FIELDs #######################################################
	collapse_set('#fs-INTERVAL-FORM-FIELD_'+field_id);
	collapse_set('#fs-INTERVAL-FORM-FIELD_'+field_id+'_period');
	

	$('#fs-INTERVAL-FORM-FIELD_'+field_id+'_period_label').on('click',function(e){
		$(this).parent().find('.formula_sub_period_INT_ck').trigger("click");
		return false;
	});


	color_field('#fs-INTERVAL-FORM-FIELD_'+field_id+' .cp');
	color_remove('#fs-INTERVAL-FORM-FIELD_'+field_id+' .color_remove');
	

	//Show in Diagram checkbox
	$('#fs-INTERVAL-FORM-FIELD_' + field_id + ' .data_diagram_show_ck').on('change', function () {
		if (!this.checked) {
			$(this).parent().parent().parent().parent().find('.diagram_options').addClass('hidden');
		} else {
			$(this).parent().parent().parent().parent().find('.diagram_options').removeClass('hidden');
		}
	});


	//Data Name change
	$('#fs-INTERVAL-FORM-FIELD_' + field_id + " input[name='data_graph_name[]']").on('change keyup', function () {
		const new_name = $(this).val();

		$('#fs-INTERVAL-FORM-FIELD_' + field_id + ' span.field_name').text(new_name);

		$('#Table__Interval_Form_' + interval_id + ' span.field_name[data-name="' + field_name + '"]').text(new_name);

		V_INTERVAL_DATA[interval_id][field_num].name = new_name;
	});
	

	//Diagram type select on change
	$('#fs-INTERVAL-FORM-FIELD_' + field_id + " select[name='data_type_sel[]']").on('change', function () {
		if ($(this).val() == 'column') {
			$('#fs-INTERVAL-FORM-FIELD_' + field_id + " select[name='data_line_sel[]']").parent().hide();
			$('#fs-INTERVAL-FORM-FIELD_' + field_id + " select[name='data_p_range_sel[]']").parent().show();
			$('#fs-INTERVAL-FORM-FIELD_' + field_id + " select[name='data_markers_sel[]']").parent().hide();
		}
		else if ($(this).val() == 'scatter') {
			$('#fs-INTERVAL-FORM-FIELD_' + field_id + " select[name='data_line_sel[]']").parent().hide();
			$('#fs-INTERVAL-FORM-FIELD_' + field_id + " select[name='data_p_range_sel[]']").parent().hide();
			$('#fs-INTERVAL-FORM-FIELD_' + field_id + " select[name='data_markers_sel[]']").parent().hide();
		}
		else {
			$('#fs-INTERVAL-FORM-FIELD_' + field_id + " select[name='data_line_sel[]']").parent().show();
			$('#fs-INTERVAL-FORM-FIELD_' + field_id + " select[name='data_p_range_sel[]']").parent().hide();
			$('#fs-INTERVAL-FORM-FIELD_' + field_id + " select[name='data_markers_sel[]']").parent().show();
		}
	});
	

	//remove_INT_form_field click
	$('#Button__Interval_Form_Field__Remove_'+field_id).on('click',function() {
		//TODO: check if this field included in any interval calculation and if yes not remove

		loading.show();

		const data_val = $(this).attr('data-val');

		$(this).next().remove(); //remove fieldset
		$(this).remove(); //remove close
		
		setTimeout(function () { //for loading
			
			Table__Interval_Form_Field__Remove(data_val);

			Button__Interval_Form_Field__Enable_Disable(interval_id);

			Debug1('  2.Calc.RC. Refresh/Calculate CALX ===============', [interval_id]);
			//Update CALX
			$('#FI' + interval_id).calx('refresh');
			//calculate CALX
			$('#FI' + interval_id).calx('calculate');

			loading.hide();
		}, 0);
	});
	

	Debug1('  2.Calc.U. Update CALX ===============', [interval_id]);
	//Update CALX
	$('#FI'+interval_id).calx('update');
	//calculate CALX
	//$('#FI'+interval_id).calx('calculate'); //no need to calculate when we add new field 
	
} //Fieldset__Interval_Form_Field__Init


function Select__Interval_Form_Field__Sub_Period(field_id, main_period) {
	Debug1('    3.Html.2.', '-', get_Function_Name(), '-', [...arguments]);

	function get_options(period, start, end, is_end) {
		let _options = '';
		for (let i = start; i <= end; i++) {
			if (period == 'hours' || period == 'minutes') {
				const value = (i < 10 ? '0' : '') + i + (is_end ? ':59' : ':00');
				_options += '<option value="' + value + '">' + value + '</option>';
			} else {
				_options += '<option value="' + i + '">' + i + '</option>';
			}
		}
		return _options;
	} //end get_options


	let options = '';
	let options2 = '';

	//main period             //sub period
	if (main_period == 'years') { //months
		options  = get_options('months', 1, 12, false);
		options2 = get_options('months', 1, 12, true);
	}
	else if (main_period == 'months') { //weeks --we give days
		options  = get_options('days', 1, 31, false);
		options2 = get_options('days', 1, 31, true);
	}
	else if (main_period == 'weeks') { //days
		options  = get_options('days', 1, 31, false);
		options2 = get_options('days', 1, 31, true);
	}
	else if (main_period == 'days') { //hours
		options  = get_options('hours', 0, 23, false);
		options2 = get_options('hours', 0, 23, true);
	}
	else if (main_period == 'hours') { //minutes
		options  = get_options('minutes', 0, 59, false);
		options2 = get_options('minutes', 0, 59, true);
	}
	else if (main_period == 'minutes') { //seconds --too short --not give any options
		options  = '<option value=""></option>';
		options2 = '<option value=""></option>';
	}
	else {
		options  = '<option value=""></option>';
		options2 = '<option value=""></option>';
	}

	return ''+
		'<label>' + LANG.RESULTS.INTERVAL_PERIOD_START + ': </label> '+
		'<select id="formula_sub_period_Start_INT_'+field_id+'" name="formula_sub_period_start[]" class="form-control">'+
			options +
		'</select>'+
		' &nbsp; '+
		'<label>' + LANG.RESULTS.INTERVAL_PERIOD_END + ': </label> '+
		'<select id="formula_sub_period_End_INT_'+field_id+'" name="formula_sub_period_end[]" class="form-control">'+
			options2 +
		'</select>';
} //end Select__Interval_Form_Field__Sub_Period


//get Forms select options
function Select_Options__RAW_Forms__Get() {
	Debug1('    3.Html.1.', '-', get_Function_Name(), '-', [...arguments]);

	let forms_select = [];
	//go through V_FORMS_N_FIELDS for every form
	for (let form_id in V_FORMS_N_FIELDS) {
		if (Object.prototype.hasOwnProperty.call(V_FORMS_N_FIELDS, form_id)) {
			const form_name = V_FORMS_N_FIELDS[form_id][0];
			//is selected
			if (V_FORMS_DO[form_id]) {
				forms_select.push('<option value="' + form_id + '">' + form_name + '</option>');
			}
			else {
				//is save
				if (form_id.indexOf('_S') != -1) {
					//is selected
					if (V_FORMS_DO['saves'] &&
						V_FORMS_DO['saves'][form_id.split('_S')[0]] &&
						V_FORMS_DO['saves'][form_id.split('_S')[0]].indexOf(form_id.split('_S')[1]) != -1)
					{
						forms_select.push('<option value="' + form_id + '">' + form_name + '</option>');
					}
				}	
			}
		}
	}
	
	return forms_select.join('');
} //end Select_Options__RAW_Forms__Get


//get form fields select options
function Select_Options__RAW_Form_Fields__Get(form_id) {
	Debug1('    3.Html.5.', '-', get_Function_Name(), '-', [...arguments]);

	if (form_id == null) {
		return "";
	}


	//templates have form_id like '53_S187'
	const is_Forms_Template = (form_id.indexOf('_S') != -1);
	const select_options = [];


	if (is_Forms_Template) {
		const save_id = form_id.split('_S')[1];

		form_id = form_id.split('_S')[0];

		if (V_FORMS_TEMPLATES[form_id] &&
			V_FORMS_TEMPLATES[form_id][save_id])
		{
			const form_fields = V_FORMS_TEMPLATES[form_id][save_id].data;

			for (const field_id in form_fields) {
				if (Object.prototype.hasOwnProperty.call(form_fields, field_id)) {
					const field_name = form_fields[field_id].name;
					const cell_id = form_fields[field_id].cell_id;

					select_options.push(
						'<option value="' + field_id + '" data-cell_id="' + cell_id + '">' +
							field_name +
						'</option>'
					);
				}
			}
		}
	}
	//number
	else {
		if (Object.prototype.hasOwnProperty.call(V_FORMS_N_FIELDS, form_id)) {
			const form_fields = V_FORMS_N_FIELDS[form_id][1];

			for (const field_id in form_fields) {
				if (Object.prototype.hasOwnProperty.call(form_fields, field_id)) {
					const field_name = form_fields[field_id][0];
					const cell_id = form_fields[field_id][2];

					select_options.push(
						'<option value="' + field_id + '" data-cell_id="' + cell_id + '">' +
						field_name +
						'</option>'
					);
				}
			}
		}
	}

	return select_options.join('');
} //end Select_Options__RAW_Form_Fields__Get


//enable/disable add interval calculation button
function Button__Interval_Form_Field__Enable_Disable(interval_id) {
	Debug1('  2.JS.H.', '-', get_Function_Name(), '-', [...arguments]);

	if ($('#div-INTERVAL-FORM_' + interval_id + ' .Calculation_Raw').length) {
		$("#Button__Interval_Form_Field__for_INTERVAL_Data__Add_" + interval_id).prop('disabled', false);
		$("#Button__Interval_Form_Field__for_INTERVAL_SingleColumn_Data__Add" + interval_id).prop('disabled', false);
	}
	else {
		$("#Button__Interval_Form_Field__for_INTERVAL_Data__Add_" + interval_id).prop('disabled', true);
		$("#Button__Interval_Form_Field__for_INTERVAL_SingleColumn_Data__Add" + interval_id).prop('disabled', true);
	}
}

// HTML ##########################################
//################################################



//################################################
// FORMULA #######################################

//convert Letter to Number
function Formula__Number_from_Alpha__Get(alpha) {
	Debug2('      4.Calc.Num.', '-', get_Function_Name(), '-', [...arguments]);

	let num = 0;
	const len = alpha.length;
	alpha = alpha.toUpperCase();

	for (pos = 0; pos < len; pos++) {
		num += (alpha.charCodeAt(pos) - 64) * Math.pow(26, len - pos - 1);
	}

	return num;
}


//get cell_ids
function Formula__Cell_ids_from_Formula__Get(formula) {
	Debug2('      4.Calc.Num.', '-', get_Function_Name(), '-', [...arguments]);

	var cell_ids = [];
	function replacer(match, p1, p2, p3, offset, string) {
		//console.log(0,match, 1,p1, 2,p2, 3,p3, 4,offset, 5,string);
		const column_name = p2;
		cell_ids.push(column_name);
	}
	formula.replace(/([\{])(.+?)([\}])/g, replacer);
	return cell_ids;
}


function Formula__Interval__Replace_Templates_with_ids(formula, id, ath_id, save_form_id, col_data, data, user_excel, type) {
	Debug2('      4.Calc.', '-', get_Function_Name(), '-', [...arguments]);

	function replacer(match, p1, p2, p3, offset, string) {
		//console.log('Formula__Interval__Replace_Templates_with_ids', 0,match, 1,p1, 2,p2, 3,p3, 4,offset, 5,string);
		let column_name = p2;
		//console.log('Formula__Interval__Replace_Templates_with_ids', 'column_name:', column_name+''+id, id, id-1, string);

		if (id == '[n]') {
			//beautifier
			return column_name + ':' + column_name +'[n]';
		}

		//INT data calculation by users ############################
		if (type == 'INT' || type == 'LINES') {
			if (type == 'LINES') {
				column_name = 'L' + column_name.substring(1);
			}

			return column_name + Formula__Get_ALPHA_id_noPrefix(ath_id, false) + id;
		}

		//INT data calculation single ##############################
		else if (type == 'INTSC' || type == 'LINES1') {
			if (type == 'LINES1') {
				column_name = 'L' + column_name.substring(1);
			}

			//start with { so it is not a range
			if (formula.substring(0, 1) == '{') {
				return column_name + 'A' + id;
			}
			else { //range
				return column_name + 'A' + id + ':' + column_name + Formula__Get_ALPHA_id_noPrefix(V_Selected__Athletes__With_Data.length, false) + id;
			}
		}
		
		//INT-individual data calculation single ###################
		else if (type == 'INTSC2' || type == 'LINES2') {
			if (type == 'LINES2') {
				column_name = 'L' + column_name.substring(1);
			}

			return column_name + id;
		}
		
		//RAW data calculation #####################################
		else {
			//console.log(ath_id, save_form_id, column_name);
			//calculation
			if (('|' + column_name).indexOf('|B') != -1) {
				const Calculation_Interval = Formula__Number_from_Alpha__Get(('|' + column_name).replace('|B', ''));
				//console.log(1, ath_id, save_form_id, 'Formula' + Calculation_Interval, Calculation_Interval);

				if (!V_USED_DATA[ath_id] ||
					!V_USED_DATA[ath_id][save_form_id] ||
					!V_USED_DATA[ath_id][save_form_id]['Formula' + Calculation_Interval])
				{
					return 'Out of Period';
				}

				const calc_data = V_USED_DATA[ath_id][save_form_id]['Formula' + Calculation_Interval].data;
				const calc_data_len = calc_data.length;

				let start_num = 0;
				let end_num = 0;

				for (let i=0; i<calc_data_len; i++) {
					//is in interval
					if (calc_data[i][0] >= data[0] && calc_data[i][0] <= data[1]) {
						//first cell
						if (start_num == 0) {
							start_num = calc_data[i][2];
						}
						//last cell
						end_num = calc_data[i][2];
					}
				}

				if (!start_num || !end_num) {
					return 'Out of Period';
				}

				return '#F' + ath_id + '_' + save_form_id + '!' + start_num + ':' + end_num;
			}
			else { //data
				//console.log(2, column_name, col_data[3], col_data[4]);
				return '#F' + ath_id + '_' + save_form_id + '!' + column_name + '' + col_data[3] + ':' + column_name + '' + col_data[4];
			}
		}
	}

	//make the replacements
	return formula.replace(/([\{])(.+?)([\}])/g, replacer);
} //Formula__Interval__Replace_Templates_with_ids


function Formula__Interval__Update_N_Calculate(interval_id, field_num) {
	Debug1('  2.Calc.Set.', '-', get_Function_Name(), '-', [...arguments]);

	loading.show();
	setTimeout(function(){ //for loading
		
		const this_FIELD = V_INTERVAL_DATA[interval_id][field_num];
		this_FIELD.formula = $('#formula_input_' + interval_id + '_' + field_num).val();

		if ($('#formula_sub_period_INT_' + interval_id + '_' + field_num).length) {
			let formula_sub_period = ($('#formula_sub_period_INT_' + interval_id + '_' + field_num).val() == '1' ? true : false);
			let formula_sub_period_type = $('#formula_sub_period_Type_INT_' + interval_id + '_' + field_num).val();
			let formula_sub_period_start = $('#formula_sub_period_Start_INT_' + interval_id + '_' + field_num).val();
			let formula_sub_period_end = $('#formula_sub_period_End_INT_' + interval_id + '_' + field_num).val();

			this_FIELD.formula_sub_period = formula_sub_period;
			this_FIELD.formula_sub_period_type = formula_sub_period_type;
			this_FIELD.formula_sub_period_start = formula_sub_period_start;
			this_FIELD.formula_sub_period_end = formula_sub_period_end;

			//get data for the new form -- with extra period in count
			V_Selected__Athletes__With_Data.forEach(function (ath_id, ath_index) {
				// put athletes Cells Count into intervals
				INTERVAL_DATA__Cells_Count__Update(interval_id, field_num, (ath_index + 1), ath_id, formula_sub_period, formula_sub_period_type, formula_sub_period_start, formula_sub_period_end);
			});
		}

		if ($('#formula_individual_' + interval_id + '_' + field_num).length) {
			this_FIELD.formula_individual = Number($('#formula_individual_' + interval_id + '_' + field_num).val());
		}


		Debug1('  2.Calc.U. Update CALX ===============', [interval_id]);
		//Update CALX
		$('#FI' + interval_id).calx('update');
		

		// copy formula to cells
		Formula__Interval__Table__Cells__Update(interval_id, field_num);


		Debug1('  2.Calc.C. Calculate CALX ===============', [interval_id]);
		//calculate CALX
		$('#FI' + interval_id).calx('calculate');
		

		loading.hide();
	}, 0);
} //end Formula__Interval__Update_N_Calculate


function Formula__Interval__Table__Cells__Update(interval_id, field_num) {
	Debug1('  2.Calc.Copy.', '-', get_Function_Name(), '-', [...arguments]);

	const this_DATA = V_INTERVAL_DATA[interval_id].data;
	const this_FIELD = V_INTERVAL_DATA[interval_id][field_num];

	const save_form_id = this_FIELD.form_id;
	const formula = this_FIELD.formula;
	const is_individual = this_FIELD.formula_individual;

	const cell_ids = Formula__Cell_ids_from_Formula__Get(formula);
	const cell_id = cell_ids[0];

	let is_calc = false;
	//we have calculation? --bcz calculation may have rowspan and not be a line
	if (('|' + cell_id).indexOf('|B') != -1) {
		is_calc = true;
	}
	
	//INTSC data form is_single_column
	if (save_form_id == 'INTSC') {
		const int_V_INTERVAL_DATA = this_FIELD[1].data;
		const table_elem = 'table#Table__Interval_Form_' + interval_id + ' tbody .td_cn_' + field_num + '.td_B1';

		Formula__Interval__Table__Cells__Update_by_Type('', '', save_form_id, interval_id, this_DATA, int_V_INTERVAL_DATA, table_elem, is_calc, formula, 'INTSC', is_individual);
	}
	//INT data form
	else if (save_form_id == 'INT') {
		//loop selected athletes
		V_Selected__Athletes__With_Data.forEach(function(ath_id, ath_index) {
			const int_V_INTERVAL_DATA = this_FIELD[ath_index + 1].data;
			const table_elem = 'table#Table__Interval_Form_' + interval_id + ' tbody .td_cn_' + field_num + '.td_B' + (ath_index + 1);

			Formula__Interval__Table__Cells__Update_by_Type(ath_id, ath_index, save_form_id, interval_id, this_DATA, int_V_INTERVAL_DATA, table_elem, is_calc, formula, 'INT');
		});
	}
	//RAW data form
	else { //save_form_id = "3_S15"
		//loop selected athletes
		V_Selected__Athletes__With_Data.forEach(function(ath_id, ath_index) {
			const ath_V_INTERVAL_DATA = this_FIELD[ath_index + 1].data;
			const table_elem = 'table#Table__Interval_Form_' + interval_id + ' tbody .td_cn_' + field_num + '.td_B' + (ath_index + 1);

			Formula__Interval__Table__Cells__Update_by_Type(ath_id, ath_index, save_form_id, interval_id, this_DATA, ath_V_INTERVAL_DATA, table_elem, is_calc, formula, 'RAW');
		});
	}

	Debug1('  2.Calc.R. Refresh CALX ===============', [interval_id]);
	//Refresh CALX
	$('#FI' + interval_id).calx('refresh');

} //end Formula__Interval__Table__Cells__Update


function Formula__Interval__Table__Cells__Update_by_Type(ath_id, ath_index, save_form_id, interval_id, this_DATA, type_V_INTERVAL_DATA, table_elem, is_calc, formula, type, is_individual = false) {
	Debug1('  2.Calc.Copy.', '-', get_Function_Name(), '-', [...arguments]);

	const cell_ids = Formula__Cell_ids_from_Formula__Get(formula);
	const cell_id = cell_ids[0];

	let line_num = 1;
	
	$(table_elem).each(function(index, td){
		const data_cell = $(td).attr('data-cell');
		if (type == 'INT' || type == 'INTSC') {
			var data_cell_lines = $(td).prev().attr('data-cell');
		}

		//reset
		let lines_count = '';
		let new_formula = '';
		let new_formula_excel = '';

		//INTSC
		if (type == 'INTSC') {
			lines_count = Formula__Interval__Replace_Templates_with_ids('SUM({' + cell_id + '})', line_num, '', save_form_id, type_V_INTERVAL_DATA[line_num - 1], this_DATA[line_num - 1], 'user', (is_individual ? 'LINES2' : 'LINES1'));

			new_formula = Formula__Interval__Replace_Templates_with_ids(formula, line_num, '', save_form_id, type_V_INTERVAL_DATA[line_num - 1], this_DATA[line_num - 1], 'user', (is_individual ? 'INTSC2' : 'INTSC'));						
		}
		//INT
		else if (type == 'INT') {
			//filter for unique values + map to add +{}
			//let lines_plus = cell_ids/*.filter(function(value, index, self) {return self.indexOf(value) === index;})*/.map(function (cell_id) { return '+{' + cell_id + '}'; }).join('').substring(1);
			//substring to remove the first +
			let lines_plus = cell_ids.map(function (cell_id) { return '+{' + cell_id + '}'; }).join('').substring(1);
			
			lines_count = Formula__Interval__Replace_Templates_with_ids(lines_plus, line_num, ath_index + 1, save_form_id, type_V_INTERVAL_DATA[line_num - 1], this_DATA[line_num - 1], 'user', 'LINES');
			
			new_formula = Formula__Interval__Replace_Templates_with_ids(formula, line_num, ath_index + 1, save_form_id, type_V_INTERVAL_DATA[line_num - 1], this_DATA[line_num - 1], 'user', 'INT');
		}
		//RAW
		else if (type == 'RAW') {
			//if we have available cell or is_calc
			if (type_V_INTERVAL_DATA[line_num - 1][3] || is_calc) {
				lines_count = type_V_INTERVAL_DATA[line_num - 1][5];

				new_formula = Formula__Interval__Replace_Templates_with_ids(formula, line_num, ath_id, save_form_id, type_V_INTERVAL_DATA[line_num - 1], this_DATA[line_num - 1], 'user', 'RAW');

			}
		}
		

		//new_formula_excel = Formula__Interval__Replace_Templates_with_ids(formula, line_num, save_form_id, this_DATA[line_num-1], 'excel', type);
		//no need --its the same at the moment
		new_formula_excel = new_formula;


		if (new_formula.indexOf('Out of Period') != -1) {
			//reset
			lines_count = '';
			new_formula = '';
			new_formula_excel = '';
		}
		
		//increase for the next pass
		line_num++;
		
		//format data
		$(td).attr('data-format', "0[.]00");

		//cell formula + line formula
		if (new_formula_excel != '') {
			//formula
			$('#FI' + interval_id).calx('getCell', data_cell).setFormula(new_formula_excel);
			
			//lines
			if (type == 'INT' || type == 'INTSC') {
				//lines --as formula
				$('#FI' + interval_id).calx('getCell', data_cell_lines).setFormula(lines_count);
			}
			else if (type == 'RAW') {
				//lines --just copy to cell
				$(td).prev().text( lines_count );
			}
		}
		else {
			//need to clear formula in case it has old data
			$('#FI' + interval_id).calx('getCell', data_cell).setFormula('');

			//if no formula set cell to '' empty
			$(td).text('');

			//lines
			$(td).prev().text('');
		}


		//cell formula in title
		$(td).attr('title', data_cell + ' -> ' + new_formula);

		if (type != 'RAW') {
			//cell lines in title
			$(td).prev().attr('title', data_cell_lines + ' -> ' + lines_count);
		}
	});
} //end Formula__Interval__Table__Cells__Update_by_Type

// FORMULA #########################################
//##################################################
	


//########################################################
// ADD INTERVAL CALCULATION  #############################

//add INTERVAL calculation from RAW data
function Interval_Form_Field__for_RAW_Data__Add(interval_id) {
	Debug1('  2.Calc.Add.RAW.', '-', get_Function_Name(), '-', [...arguments]);

	//disable interval cells, period and form change
	$("#formula_cells_INT_" + interval_id).prop('disabled', true);
	$("#formula_period_INT_" + interval_id).prop('disabled', true);

	const calc_num = Formula__Interval_Form_Field__Get_Max_Number(interval_id);
	const field_name = 'Formula' + calc_num;

	//add FIELD data html 
	Fieldset__Interval_Form_Field__Init(interval_id, field_name, calc_num, false, false);

	Table__Interval_Form_Field__RAW__Add(interval_id, field_name, calc_num);

	Button__Interval_Form_Field__Enable_Disable(interval_id);

	Debug1('  2.Calc.Add.RAW.--', '--END--');
}


//add INTERVAL calculation in INTERVAL data
function Interval_Form_Field__for_INTERVAL_Data__Add(interval_id) {
	Debug1('  2.Calc.Add.INT.', '-', get_Function_Name(), '-', [...arguments]);

	const calc_num = Formula__Interval_Form_Field__Get_Max_Number(interval_id);
	const field_name = 'Formula' + calc_num;

	//add FIELD data html 
	Fieldset__Interval_Form_Field__Init(interval_id, field_name, calc_num, true, false);

	Table__Interval_Form_Field__INT__Add(interval_id, field_name, calc_num);

	Debug1('  2.Calc.Add.INT.--', '--END--');
}


//add INTERVAL calculation in INTERVAL_SC data (single column)
function Interval_Form_Field__for_INTERVAL_SingleColumn_Data__Add(interval_id) {
	Debug1('  2.Calc.Add.INTSC.--', '-', get_Function_Name(), '-', [...arguments], '--END--');

	const calc_num = Formula__Interval_Form_Field__Get_Max_Number(interval_id);
	const field_name = 'Formula' + calc_num;
	
	//add FIELD data html 
	Fieldset__Interval_Form_Field__Init(interval_id, field_name, calc_num, true, true);

	Table__Interval_Form_Field__INTSC__Add(interval_id, field_name, calc_num);
	
	Debug1('  2.Calc.Add.INTSC.--', '--END--');
}

// ADD INTERVAL CALCULATION  #############################
//########################################################
