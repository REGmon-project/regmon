if (!Production_Mode) "use strict"; //remove on production so "const" works on iOS Safari


function Export_Table_Data(csv_xlsx) {
	Debug1('X.Export.Table.Data', '-', get_Function_Name(), '-', [...arguments]);

	function getFile(href, extension, content, name) {
        const downloadAttrSupported = document.createElement('a').download !== undefined;
		let a, blobObject;

		// MS specific. Check this first because of bug with Edge (#76)
		if (window.Blob && window.navigator.msSaveOrOpenBlob) {
			// Falls to msSaveOrOpenBlob if download attribute is not supported
			blobObject = new Blob([content]);
			window.navigator.msSaveOrOpenBlob(blobObject, name + '.' + extension);
		} 
		// Download attribute supported
		else if (downloadAttrSupported) {
			a = document.createElement('a');
			a.href = href;
			a.target = '_blank';
			a.download = name + '.' + extension;
			document.body.appendChild(a);
			a.trigger("click");
			a.remove();
		} 
		else console.log('Error! Fall back to server side handling');
	}

	function set_val(val) {
		val = val.trim();
		if (val == '') {
			return '';
		}
		if (val == 'null') {
			return '';
		}
		if (typeof val === "string") {
			if (isNaN(val)) { //check if is not a number
				val = '"' + val + '"';
			}
		}
		if (typeof val === 'number') {
			let n = (1.1).toLocaleString()[1];
			if (n === ',') {
				val = val.toString().replace(".", ",");
			}
		}
		return val;
	}
	
	function get_CSV_data() {
		Debug1('X.Export. Export_Table_Data', '-', get_Function_Name());

		const data = V_USED_DATA;
		let csv = '';
		let athletes = '';

		//each Athlete
		Object.keys(data).forEach(function (ath_id) {
			if (athletes != '') {
				athletes += ',';
			}
			athletes += $('#Diagram__Athlete_Name_' + ath_id).val();		
			
			//each Form
			Object.keys(data[ath_id]).forEach(function (form_id) {
				const form_group_id = ath_id + '_' + form_id;
				const raw_headers = [];
				const raw_rows = [];
				const int_headers = [];
				const int_rows = [];
				let int_index_rowspan = 0;
				
				//build rows
				$('table#Table__Athlete_Form_' + form_group_id + ' tbody tr').not('.hidden_Grey, .hidden_Grey2').each(function (tr_index, tr) {
					let t_date_time = $($(tr).find('th')[1]).text();
					$(tr).find('td').each(function(td_index, td){
						const is_int = $(td).attr('data-is_int');
						const has_int = $(td).hasClass('int');
						const has_int_rowspan = $(td).hasClass('hidden_rowspan');
						const has_raw = $(td).hasClass('raw');
						//INT
						if ((has_int && is_int) || (has_int && has_int_rowspan)) {
							if (has_int_rowspan) {
								int_index_rowspan++;
								return true; //continue
							}
							let t_date_time_int = $(td).attr('data-datetime') + ':00';
							if (!int_rows[tr_index - int_index_rowspan]) {
								int_rows[tr_index - int_index_rowspan] = (tr_index + 1 - int_index_rowspan) + ';' + set_val(t_date_time_int);
							}
							int_rows[tr_index - int_index_rowspan] += ';' + set_val($(td).text());
						}
						//RAW
						else {
							if (!raw_rows[tr_index]) {
								raw_rows[tr_index] = (tr_index + 1) + ';' + set_val(t_date_time + ' ');
							}
							raw_rows[tr_index] += ';' + set_val($(td).text());
						}
						
						//get headers --only in first tr
						if (tr_index == 0) {
							//INT
							if (has_int && is_int) {
								int_headers.push(td_index);
							}
							//RAW
							else {
								raw_headers.push(td_index);
							}
						}
					});
				});
				

				//build headers
				let form_title = '';
				let raw_header1 = '';
				let raw_header2 = '';
				let int_header1 = '';
				let int_header2 = '';
				let add = 3; //cells to add from beginning

				$('table#Table__Athlete_Form_'+form_group_id+' thead tr').each(function(tr_index, tr){
					if (tr_index == '0') {
						if (V_RESULTS_PAGE == 'FORMS_RESULTS') {
							//here we have Diagram__Form_Name_ and Diagram__Form_Name__Full_ --we use Diagram__Form_Name__Full_
							form_title = $('#Diagram__Form_Name__Full_' + form_group_id).val();
						}
						else { //RESULTS
							form_title = $('#Diagram__Form_Name_' + form_group_id).val();
						}

						//if more than 1 athletes
						if (Object.keys(data).length > 1) {
							form_title += ' [' + $('#Diagram__Athlete_Name_' + ath_id).val() + ']';
						}
					}
					/* //cell nums not needed
					else if (tr_index == '1') {
						raw_headers.forEach(function (th, th_index) {
							if (raw_header1 != '') {
								raw_header1 += ';';
							}
							raw_header1 += $($(tr).find('th').not('.dz_hidden')[th + add]).text();
						});
						int_headers.forEach(function (th, th_index) {
							if (int_header1 != '') {
								int_header1 += ';';
							}
							int_header1 += set_val($($(tr).find('th').not('.dz_hidden')[th + add]).text());
						});
					}*/
					else if (tr_index == '2') {
						raw_headers.forEach(function(th, th_index) {
							if (raw_header2 == '') {
								raw_header2 += '"NN";"' + LANG.RESULTS.TABLE.DATE_TIME + '"';
							}
							if (raw_header2 != '') {
								raw_header2 += ';';
							}
							raw_header2 += set_val($($(tr).find('th').not('.dz_hidden')[th + add]).text());
						});

						int_headers.forEach(function(th, th_index) {
							if (int_header2 == '') {
								int_header2 += '"NN";"' + LANG.RESULTS.TABLE.DATE_TIME + '"';
							}
							if (int_header2 != '') {
								int_header2 += ';';
							}
							int_header2 += set_val($($(tr).find('th').not('.dz_hidden')[th + add]).text());
						});
					}
				});
				//console.log('raw', form_title, raw_headers, raw_header2, raw_rows);
				//console.log('int', form_title, int_headers, int_header2, int_rows);

				//raw
				csv += form_title + '\n' + raw_header2 + '\n' + raw_rows.join('\n');
				//int
				if (int_header2 != '') {
					csv += '\n\n';
					csv += int_header2 + '\n' + int_rows.join('\n');
				}
				csv += '\n\n\n\n';
			});
		});
		//console.log(athletes, csv);

		return [athletes, csv];
	}
	
	function get_CSV_int() {
		Debug1('X.Export. Export_Table_Data', '-', get_Function_Name());

		const data = V_INTERVAL_DATA;
		let headers = [];
		let rows = [];

		//each Interval
		Object.keys(data).forEach(function (interval_id) {
			//build headers
			$('table#Table__Interval_Form_' + interval_id + ' thead tr').each(function (tr_index, tr) {
				//not want cell ids
				if (tr_index != 3) {
					$(tr).find('th').each(function(th_index, th){
						if (!headers[tr_index]) {
							headers[tr_index] = set_val($(th).text(), true);
							if ($(th).attr('colspan') && tr_index != 0) {
								let times = parseInt($(th).attr('colspan')) - 1;
								headers[tr_index] += ';'.repeat(times);
							}
						}
						else {
							headers[tr_index] += ';' + set_val($(th).text(), true);
							if ($(th).attr('colspan')) {
								let times = parseInt($(th).attr('colspan')) - 1;
								headers[tr_index] += ';'.repeat(times);
							}
						}
					});
				}
			});
			//build rows
			$('table#Table__Interval_Form_' + interval_id + ' tbody tr').each(function (tr_index, tr) {
				$(tr).find('th').each(function (th_index, th) {
					if (!rows[tr_index]) {
						rows[tr_index] = set_val($(th).text());
					}
					else rows[tr_index] += ';' + set_val($(th).text());
				});
				$(tr).find('td').each(function (td_index, td) {
					rows[tr_index] += ';' + set_val($(td).text());
				});
			});
		});

		//fix array index to exclude the header no 3
		headers = headers.filter(function (val) { return val });
		//console.log(headers);
		//console.log(rows);
		return headers.join('\n') + '\n' + rows.join('\n');
	}
	
	function get_ARRAY_data() {
		Debug1('X.Export. Export_Table_Data', '-', get_Function_Name());

		const data = V_USED_DATA;
		const aths = {};
		const arr = [];
		const arr2 = [];
		let athletes = '';

		//each Athlete
		Object.keys(data).forEach(function(ath_id) {
			const ath_name = $('#Diagram__Athlete_Name_' + ath_id).val();			
			let forms = {};

			if (athletes != '') {
				athletes += ',';
			}
			athletes += ath_name;			

			//each Form
			Object.keys(data[ath_id]).forEach(function(form_id) {
				const form_group_id = ath_id + '_' + form_id;
				let form_name = '';

				t_form_id = form_id;
				
				if (V_RESULTS_PAGE == 'FORMS_RESULTS') {
					//here we have Diagram__Form_Name_ and Diagram__Form_Name__Full_ --we use Diagram__Form_Name__Full_
					form_name = $('#Diagram__Form_Name__Full_' + form_group_id).val();
				}
				else { //RESULTS
					form_name = $('#Diagram__Form_Name_' + form_group_id).val();
				}

				const raw_headers = [];
				const raw_rows = [];
				const int_headers = [];
				const int_rows = [];
				let int_index_rowspan = 0;
				
				//build rows
				$('table#Table__Athlete_Form_' + form_group_id + ' tbody tr').not('.hidden_Grey, .hidden_Grey2').each(function (tr_index, tr)
				{
					let t_date_time = $($(tr).find('th')[1]).text().trim();

					$(tr).find('td').each(function (td_index, td)
					{
						const is_int = $(td).attr('data-is_int');
						const has_int = $(td).hasClass('int');
						const has_int_rowspan = $(td).hasClass('hidden_rowspan');
						const has_raw = $(td).hasClass('raw');

						//INT
						if ((has_int && is_int) || (has_int && has_int_rowspan)) {
							if (has_int_rowspan) {
								int_index_rowspan++;
								return true; //continue
							}

							let t_date_time_int = $(td).attr('data-datetime') + ':00';
							if (!int_rows[tr_index - int_index_rowspan]) {
								int_rows[tr_index - int_index_rowspan] = [(tr_index + 1 - int_index_rowspan), t_date_time_int];
							}
							int_rows[tr_index - int_index_rowspan].push($(td).text().trim());
						}
						//RAW
						else {
							if (!raw_rows[tr_index]) {
								raw_rows[tr_index] = [(tr_index + 1), t_date_time + ' '];
							}
							raw_rows[tr_index].push($(td).text().trim());
						}
						
						//get headers --only in first tr
						if (tr_index == 0) {
							//INT
							if (has_int && is_int) {
								int_headers.push(td_index);
							}
							//RAW
							else {
								raw_headers.push(td_index);
							}
						}
					});
				});
				

				//build headers
				let form_title = [];
				let raw_header2 = [];
				let int_header2 = '';
				let add = 3; //cells to add from beginning

				$('table#Table__Athlete_Form_' + form_group_id + ' thead tr').each(function (tr_index, tr)
				{
					if (tr_index == '0') {
						let form_title_tmp = '';

						if (V_RESULTS_PAGE == 'FORMS_RESULTS') {
							//here we have Diagram__Form_Name_ and Diagram__Form_Name__Full_ --we use Diagram__Form_Name__Full_
							form_title_tmp = $('#Diagram__Form_Name__Full_' + form_group_id).val();
						}
						else { //RESULTS
							form_title_tmp = $('#Diagram__Form_Name_' + form_group_id).val();
						}

						//if more than 1 athletes
						if (Object.keys(data).length > 1) {
							form_title_tmp += ' [' + $('#Diagram__Athlete_Name_' + ath_id).val() + ']';
						}
						form_title = [form_title_tmp];
					}
					else if (tr_index == '2') {
						raw_headers.forEach(function(th, th_index) {
							if (!raw_header2.length) {
								raw_header2 = ["NN", LANG.RESULTS.TABLE.DATE_TIME];
							}
							raw_header2.push( $($(tr).find('th').not('.dz_hidden')[th + add]).text().trim() );
						});

						int_headers.forEach(function(th, th_index) {
							if (!int_header2.length) {
								int_header2 = ["NN", LANG.RESULTS.TABLE.DATE_TIME];
							}
							int_header2.push( $($(tr).find('th').not('.dz_hidden')[th + add]).text().trim() );
						});
					}
				});
				//console.log('raw', form_title, raw_headers, raw_header2, raw_rows);
				//console.log('int', form_title, int_headers, int_header2, int_rows);

				arr = raw_rows;
				arr.unshift(raw_header2);
				arr.unshift(form_title);

				//int
				if (int_header2 != '') {
					arr.push([]);
					arr.push([]);
					arr.push([]);
					arr.push(int_header2);
					arr.push(...int_rows);
				}
				forms[form_id] = [form_name, arr]; //sheet_name, data
			});
			aths[ath_id] = [ath_name, forms];
		});
		//console.log(aths);
		
		return [athletes, aths];
	}
	
	function get_ARRAY_int() {
		Debug1('X.Export. Export_Table_Data', '-', get_Function_Name());

		const data = V_INTERVAL_DATA;
		const intervals = {};

		Object.keys(data).forEach(function(interval_id) { //each Interval
			let headers = [];
			let rows = [];

			//build headers
			$('table#Table__Interval_Form_' + interval_id + ' thead tr').each(function (tr_index, tr) {
				//not want cell ids
				if (tr_index != 3) {
					$(tr).find('th').each(function (th_index, th) {
						if (!headers[tr_index]) {
							headers[tr_index] = [$(th).text().trim()];
							if ($(th).attr('colspan') && tr_index != 0) {
								let times = parseInt($(th).attr('colspan')) - 1;
								for (let i = 0; i < times; i++) {
									headers[tr_index].push('');
								}
							}
						}
						else {
							headers[tr_index].push($(th).text().trim());
							if ($(th).attr('colspan')) {
								let times = parseInt($(th).attr('colspan')) - 1;
								for (let i = 0; i < times; i++) {
									headers[tr_index].push('');
								}
							}
						}
					});
				}
			});


			//build rows
			$('table#Table__Interval_Form_' + interval_id + ' tbody tr').each(function (tr_index, tr) {
				$(tr).find('th').each(function (th_index, th) {
					if (!rows[tr_index]) {
						rows[tr_index] = [ $(th).text().trim() ];
					}
					else rows[tr_index].push( $(th).text().trim() );
				});
				$(tr).find('td').each(function (td_index, td) {
					rows[tr_index].push( $(td).text().trim() );
				});
			});


			//fix array index to exclude the header no 3
			headers = headers.filter(function (val) { return val });
			intervals[interval_id] = headers;
			intervals[interval_id].push(...rows);
		});
		//console.log(intervals);

		return intervals;
	}
	
	function get_Filename(athletes) {
		const today = moment().format('YYYYMMDD');
		let filename = '';

		if (V_RESULTS_PAGE == 'FORMS_RESULTS') {
			filename = LANG.DIAGRAM.EXPORT_FORMS_FILE_NAME.replace('{TODAY}', today).replace('{ATHLETES_NAMES}', athletes);
		}
		else { //RESULTS
			filename = LANG.DIAGRAM.EXPORT_RESULTS_FILE_NAME.replace('{TODAY}', today).replace('{ATHLETE_NAME}', athletes);
		}

		return filename;
	}

	function download_CSV_file() {
		Debug1('X.Export. Export_Table_Data', '-', get_Function_Name());

		const data = get_CSV_data();
        const athletes = data[0];
		const filename = get_Filename(athletes);
		let csv = '';
		

		if (V_RESULTS_PAGE == 'FORMS_RESULTS') {
			csv = data[1];
		}
		else { //RESULTS
			const intervals = get_CSV_int();
			csv = data[1] + '\n\n\n' + intervals;
		}
		

		getFile(
			//replace lineBreak and '#' --csv breaks with this char
			'data:text/csv,\uFEFF' + csv.replace(/\n/g, '%0A').replace(/#/g, '%23'),
            'csv',
			csv,
			filename
        );
	}

	function download_XLSX_file() {
		Debug1('X.Export. Export_Table_Data', '-', get_Function_Name());

		const data = get_ARRAY_data();
        const athletes = data[0];
        const xlsx = data[1];
		const filename = get_Filename(athletes) + '.xlsx';


		if (V_RESULTS_PAGE == 'FORMS_RESULTS') {
			export_array_to_excel(xlsx, filename);
		}
		else { //RESULTS
			const intervals = get_ARRAY_int();
			export_array_to_excel(xlsx, filename, intervals);
		}
	}
	
	if (csv_xlsx == 'csv') {
		download_CSV_file();
	}
	else if (csv_xlsx == 'xlsx') {
		download_XLSX_file();
	}
} //end Export_Table_Data
