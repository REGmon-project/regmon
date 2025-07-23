if (!Production_Mode) "use strict"; //remove on production so "const" works on iOS Safari


jQuery(function () {
	
	//Select_Athlete show/hide
	$("#Select__Athlete__Toggle").toggle();	


	$('#Button__Athlete__Add').on('click', function() {
		$("#Select__Athlete__Toggle").toggle();		
	});


	//Select__Athlete init
	$("#Select__Athlete").chosen({
		width: '100%',
		placeholder_text_single: LANG.SELECT_ATHLETE,
		no_results_text: LANG.NO_RESULTS,
		search_contains: true,
		disable_search_threshold: 10,
		allow_single_deselect: true
	});


	$("#Select__Athlete__Submit").on('click', function() {
		Debug1('0.JS.Click. Select__Athlete__Submit click');

		const athlete_id = $("#Select__Athlete").val();
		const athlete_name = $("#Select__Athlete option:selected").text();

		if (athlete_id) {
			loading.show();

			//disable option
			$("#Select__Athlete option:selected").prop("disabled", true);
			$("#Select__Athlete option:selected").prop("selected", false);
			$("#Select__Athlete").trigger("chosen:updated");
			
			Fieldset__Athlete__Init(athlete_id, athlete_name);

			//Select_Athlete hide
			$("#Select__Athlete__Toggle").toggle();
		}
	});
	

	// if we not have default template and have form_id and form saves then show the swal here
	// and after save default template continue with click on init first athlete
	if (V_IS_IFRAME &&
		V_Template_id == '0' &&
		V_Form_id != '0' &&
		V_FORMS_TEMPLATES[V_Form_id])
	{
		Select__Default_Template__Init();
	}
	else {
		$("#Select__Athlete__Submit").trigger('click'); //init first athlete
	}

}); //end jQuery(function() {



function Select__Default_Template__Init() { //#####	 --only in FORMS_RESULTS
	Debug1('1.Select.Template.Default.Init.', '-', get_Function_Name(), '-', [...arguments]);

	const Forms_Templates_Saves_obj = {};
	Object.keys( V_FORMS_TEMPLATES[V_Form_id] ).forEach(function(save_id){
		Forms_Templates_Saves_obj[save_id] = V_FORMS_TEMPLATES[V_Form_id][save_id].name;
	});

	Swal({
		title: LANG.FORMS.DEFAULT_TEMPLATE_SELECT_TITLE+':',
		input: 'select',
		inputOptions: Forms_Templates_Saves_obj,
		allowOutsideClick: false,
		inputPlaceholder: LANG.FORMS.DEFAULT_TEMPLATE_SELECT_PLACEHOLDER,
		showCancelButton: true,
		confirmButtonText: LANG.BUTTON_SAVE,
		cancelButtonText: LANG.BUTTON_CANCEL,
		showLoaderOnConfirm: true,
		inputValidator: function(value) {
			if (!value) {
				return LANG.FORMS.DEFAULT_TEMPLATE_SELECT_PLACEHOLDER;
			}
			V_Template_id = value;
		},
		preConfirm: function(value) {
			//save value to DB
			const post_data = {
				group_id: V_Group_id,
				ath_id: V_Athlete_id,
				cat_id: V_Category_id,
				form_id: V_Form_id,
				template_id: value
			};
			$.post('forms/ajax.form_default_template_save.php', post_data, function(data, result) {
				return true; //return false for not close the popup
			});
		}
	}).then((result) => {
		if (result.value) {
			//console.log('ok',result.value);
		}
		else if (result.dismiss === parent.Swal.DismissReason.cancel) {
			//console.log('cancel',result.dismiss);
		}

		//anyway
		$("#Select__Athlete__Submit").trigger('click'); //init first athlete
	});

} //end Select__Default_Template__Init


function Fieldset__Athlete__Init(athlete_id, athlete_name) { //#####	 --FORMS_RESULTS version
	Debug1('***********************************************');
	Debug1('1.Fieldset.Init.', '-', get_Function_Name(), '-', [...arguments]);


	//add template to HTML
	$('#Athletes_Data_Fieldsets').append(
		HTML_Template__Fieldset__Athlete(athlete_id, athlete_name)
	);

	
	//init Athlete Group ##########################################

	//fieldset collapsible init
	collapse_set('#fs-ATHLETE_' + athlete_id);
	

	//init fieldset expand/collapse
	Fieldset__Expand_Collapse__Init('#fs-ATHLETE_' + athlete_id);
	

	//remove Athlete Fieldset
	$(".Button__Fieldset_Athlete__Remove").on('click', function () {
		Debug1(' 1.Fieldset.Remove. Button__Fieldset_Athlete__Remove');

		const ath_id = $(this).attr('data-val');

		//click on each remove form inside athlete to proper remove forms from CALX
		$('#fs-ATHLETE_' + ath_id + ' .remove_form').trigger("click");

		//enable select fields
		$('#Select__Athlete option[value="' + ath_id + '"]').attr("disabled", false);
		$("#Select__Athlete").trigger("chosen:updated");

		//remove fieldset
		$(this).next().remove();

		//remove close
		$(this).remove();

		USED_DATA__Athlete__Remove(ath_id);
		
		//Update CALX
		//$('#F'+ath_id).calx('refresh'); //Rebuild cell registry from the scratch.
		//$('#F'+ath_id).calx('calculate'); //Calculate the whole sheet and display the result in each cell.
	});

	//init Athlete Group ##########################################
	
	
	//load all Athlete Data #######################################

	//load ajax.get_athlete_data.php --we get data json, select data dropdown, 2 buttons
	let post_data = {
		id: V_Form_id,
		group_id: V_Group_id,
		athlete_id: athlete_id,
		is_iframe: V_IS_IFRAME
	};
	$("#Select__Athlete_Data__Div_" + athlete_id).load("results/ajax.get_athlete_data.php", post_data, function(data, result) {
		Debug1(' 1.Json.Ajax.Load. load ajax.get_athlete_data', [athlete_id]);

		//here we get update to V_FORM_id_2_name and V_FORMS_DATA

		loading.hide();
		if (data.indexOf('Error_No_Data') != -1) {
			//do nothing
		} 
		else {
			//make Athlete data SELECT
			Select__Athlete_Data__Init(athlete_id);

			//add data button init
			$('#Button__Athlete_Data__Add_' + athlete_id).on('click', function() {
				Debug1(' 1.JS.Click. Button__Athlete_Data__Add_ button click', [athlete_id]);

				loading.show();
				setTimeout(function () { //for loading
					//for each selected field 
					$('#Select__Athlete_Data_' + athlete_id + ' option:selected').each(function () {
						//disable options so cannot selected again
						$(this).prop('selected', false);
						$(this).prop('disabled', true);

						const val_arr = $(this).val().split('|');
						const field_name = $(this).text(); //selected text
						const ath_id = val_arr[0];
						const form_id = val_arr[1];
						const form_field_num = val_arr[2];

						//saved data
						if (val_arr[3] && val_arr[3] == 'save') {
							const save_id = val_arr[2];
							const save_name = field_name;

							Load_Saved_Form_Fields(ath_id, form_id, save_id, save_name);
						}
						//normal data
						else {
							Athlete_Form_Field_DATA__Add(ath_id, form_id, form_field_num, field_name, false, false);
						}
					});

					if (V_AUTO_UPDATE_CHART) { 
						setTimeout(function () {
							//Update Chart
							Chart__Update();
						}, 200);

						//reset
						V_AUTO_UPDATE_CHART = false;
					}

					$("#Select__Athlete_Data_" + athlete_id).multiselect('refresh');
					loading.hide();
				}, 10);
			});
			

			//Auto Init - from dashboard and calendar
			if (V_Auto_Init || V_OPEN_TEMPLATE != '0') {
				V_AUTO_UPDATE_CHART = true;
				setTimeout(function(){
					V_Auto_Init = false;
					if (V_OPEN_TEMPLATE != '0') {
						let date_from = '';
						let date_to = '';
						const forms_template = (V_OPEN_TEMPLATE + '__0__2__0__0').split('__'); //for backward compatibility

						if (forms_template[2] == '2') {
							//189__2__0__0 //do nothing, we keep the save dates --not used here
						}
						else if (forms_template[2] == '0') {
							//1_189__0__26.11.2019__02.12.2019
							date_from = forms_template[3] + ' 00:00';
							date_to = forms_template[4] + ' 23:59';

							if (V_LANG_CURRENT == 'de') {
								date_from = moment(forms_template[3] + ' 00:00:00', 'DD.MM.YYYY HH:mm:ss').format('DD.MM.YYYY HH:mm');
								date_to = moment(forms_template[4] + ' 23:59:59', 'DD.MM.YYYY HH:mm:ss').format('DD.MM.YYYY HH:mm');
							}
						}
						else if (forms_template[2] == '1') {
							//1_175__1__2__week
							date_from = moment().subtract(forms_template[3], forms_template[4]).format('YYYY-MM-DD') + ' 00:00';
							date_to = moment().format('YYYY-MM-DD') + ' 23:59';

							if (V_LANG_CURRENT == 'de') {
								date_from = moment().subtract(forms_template[3], forms_template[4]).format('DD.MM.YYYY') + ' 00:00';
								date_to = moment().format('DD.MM.YYYY') + ' 23:59';
							}
						}

						//init multiselect
						$('#Select__Athlete_Data_'+athlete_id).multiselect('select', athlete_id+'|'+forms_template[0]+'|'+forms_template[1]+'|save');
						
						//set saved date if exist
						if (date_from != '' && date_to != '') {
							$('#datetimepicker_from').data("DateTimePicker").date(date_from);
							$('#datetimepicker_to').data("DateTimePicker").date(date_to);
						}

						//reset
						V_OPEN_TEMPLATE = '0';
					}

					//add data click trigger
					$('#Button__Athlete_Data__Add_' + athlete_id).trigger('click');
				}, 0);
			}
		}
		
		Debug1('1.Fieldset.Init.', '-', get_Function_Name(), '-', '--END--');
		Debug1('**************************************************');
	}); //end load("results/ajax.get_athlete_data.php"
	
	//load all Athlete Data #######################################

} //end Fieldset__Athlete__Init(athlete_id, athlete_name)


function Fieldset__Athlete_Form__Extra_Buttons__Init(ath_id, base_form_id, form_group_id, save_id, save_name, save_form_id) { //#####	 --only in FORMS_RESULTS
	Debug1('   2.Html.Fieldset.init', '-', get_Function_Name(), '-', [...arguments]);

	//remove_form click
	$('#Button__Fieldset_Athlete_Form__Remove' + form_group_id).on('click', function () {
		Debug1('  2.Fieldset.Form.Remove. Button__Fieldset_Athlete_Form__Remove click', [form_group_id]);
		
		
		Debug1('  2.Calc.D. Destroy CALX ===============', [form_group_id]);
		//remove sheet object
		$('#F' + form_group_id).calx('destroy');

		//remove fieldset
		$(this).next().remove();
		//remove close
		$(this).remove();

		//get sel_val
		let sel_val = ath_id + '|' + base_form_id + '|';
		if (save_id) {
			sel_val = ath_id + '|' + base_form_id + '|' + save_id + '|save';
		}

		//enable disabled fields from data select
		$('#Select__Athlete_Data_' + ath_id + ' option[value^="' + sel_val + '"]').prop("disabled", false);
		$("#Select__Athlete_Data_" + ath_id).multiselect('refresh');

		USED_DATA__Athlete_Form__Remove(ath_id, save_form_id);
		//Axis__With_No_Data__Remove();

		//Update CALX
		//Debug1('  2.Calc.RC. Refresh/Calculate CALX', [form_group_id]);
		//$('#F'+form_group_id).calx('refresh');
		//$('#F'+form_group_id).calx('calculate'); //calculate CALX
	});
	
	//add calculation button init
	$('#Button__Athlete_Form_Field_CALC__Add' + form_group_id).on('click', function () {
		Athlete_Form_Field_CALC__Add(ath_id, base_form_id, save_form_id, form_group_id, save_id, '');
	});
	
	//Save selected Forms_Template data
	$('#Button__Forms_Template__Save' + form_group_id).on('click', function () {
		Forms_Template__Save(ath_id, base_form_id, save_id, save_name);
	});
	
	//Add selected Forms_Template to Dashboard
	$('#Button__Forms_Template__2_Dashboard_' + form_group_id).on('click', function () {
		let date_from = moment(V_DATE_FROM + ':00', 'YYYY-MM-DD HH:mm:ss').format('YYYY-MM-DD');
		let date_to = moment(V_DATE_TO + ':59', 'YYYY-MM-DD HH:mm:ss').format('YYYY-MM-DD');
		
		if (V_LANG_CURRENT == 'de') {
			date_from = moment(V_DATE_FROM + ':00', 'DD.MM.YYYY HH:mm:ss').format('YYYY-MM-DD');
			date_to = moment(V_DATE_TO + ':59', 'DD.MM.YYYY HH:mm:ss').format('YYYY-MM-DD');
		}
	
		const options = base_form_id + '__' +
			save_id + '__' +
			'0' + '__' +
			date_from + '__' + date_to;
		
		const data = {
			group_id: V_Group_id,
			ath_id: V_UID,
			dash_id: 0,
			name: LANG.RESULTS.DASH_FORMS_TEMPLATE_NAME + ':' + save_name,
			type: 'forms_results',
			options: options,
			sort: 'max',
			color: '#cccccc'
		};
		$.post('index/ajax.dashboard_save.php', data, function (data, result) {
			if (data != 'ERROR') {
				parent.Swal({
					type: 'success',
					title: LANG.RESULTS.TEMPLATE_ADDED_TO_DASH.replace('{TEMPLATE_NAME}', save_name),
					showConfirmButton: false,
					timer: 2000
				});
			} else {
				parent.Swal({
					type: 'error',
					title: 'Error!',
					showConfirmButton: false,
					timer: 2000
				});
			}
		});
	});
	
	//Delete Template
	$('#Button__Forms_Template__Delete' + form_group_id).confirmation({
		href: 'javascript:void(0)',
		title: function () {
			return LANG.RESULTS.TEMPLATE_CONFIRM_DELETE.replace('{TEMPLATE_NAME}', save_name);
		},
		placement: 'top',
		btnOkLabel: LANG.YES, btnOkClass: 'btn btn-sm btn-success mr10',
		btnCancelLabel: LANG.NO, btnCancelClass: 'btn btn-sm btn-danger',
		onConfirm: function (e, button) {
			if (!save_id) {
				return;
			}

			const post_data = {
				group_id	: V_Group_id,
				athlete_id	: ath_id,
				form_id		: base_form_id,
				id			: save_id
			};
			$.post('results/ajax.template_delete.php', post_data, function (data, result) {
				if (data != 'ERROR' && data.indexOf('.....') == -1) {
					
					//new V_FORMS_TEMPLATES[form_id] -- update V_FORMS_TEMPLATES
					$('#add_data').html(data);

					Select__Athlete_Data__Update(ath_id);
					
					$("#Button__Fieldset_Athlete_Form__Remove" + form_group_id).trigger("click");
					
					parent.Swal({
						type: 'success',
						title: LANG.RESULTS.TEMPLATE_DELETE_SUCCESS.replace('{TEMPLATE_NAME}', save_name),
						showConfirmButton: false,
						timer: 2000
					});
				}
				else {
					parent.Swal({
						type: 'error',
						title: data,
						showConfirmButton: false,
						timer: 2000
					});
				}
			});
		}
	}); //end Delete Template
} //end Fieldset__Athlete_Form__Extra_Buttons__Init


function Select__Athlete_Data__Init(ath_id) { //#####  --only in FORMS_RESULTS
	Debug1(' 1.Html.4.', '-', get_Function_Name(), '-', [...arguments]);

	//we get V_FORMS_DATA[ath_id] = {} from ajax.get_athlete_data.php

	//make select data options 
	let data_select = '';
	let data_select_Notes = '';

	//loop forms
	for (const form_id in V_FORMS_DATA[ath_id]) {
		if (Object.prototype.hasOwnProperty.call(V_FORMS_DATA[ath_id], form_id)) {
			let form_data_length = 0;
			let data_select_options = '';
			const form_data = V_FORMS_DATA[ath_id][form_id];
			//loop form_data
			for (const key_index in form_data) {
				if (Object.prototype.hasOwnProperty.call(form_data, key_index)) {
					form_data_length = form_data[key_index].data.length;
					const data_line = form_data[key_index];
					//remove the first '_'
					let data_line_val = ath_id + '|' + form_id + '|' + key_index.substring(1);
					if (form_id == 'note') {
						data_line_val += '|note';
					}
					let this_selected = false;
					// data line option
					data_select_options += '<option value="' + data_line_val + '"' + (this_selected ? ' selected' : '') + '>' + data_line.name + '</option>';
				}
			}
			if (form_id == 'note') {
				data_select_Notes += '<optgroup label="' + V_FORM_id_2_name[form_id] + ' (' + form_data_length + ')">' + data_select_options + '</optgroup>';
			}
			else {
				data_select += '<optgroup label="' + V_FORM_id_2_name[form_id] + ' (' + form_data_length + ')">' + data_select_options + '</optgroup>';
			}
			
			//Saved Form Templates
			if (V_FORMS_TEMPLATES[form_id]) {
				let saved_data_select_options = '';
				for (let saved_form_id in V_FORMS_TEMPLATES[form_id]) {
					if (Object.prototype.hasOwnProperty.call(V_FORMS_TEMPLATES[form_id], saved_form_id)) {
						let this_selected = false;
						//we put that in templates
						if (V_Auto_Init && V_Template_id == saved_form_id) {
							this_selected = true;
						}
						const option_text = V_FORMS_TEMPLATES[form_id][saved_form_id];
						const option_value = ath_id + '|' + form_id + '|' + saved_form_id + '|save';
						//option
						saved_data_select_options += '<option value="' + option_value + '"' + (this_selected ? ' selected' : '') + '>' + option_text.name + '</option>';
					}
				}
				//optgroup 
				data_select += '<optgroup label="' + V_FORM_id_2_name[form_id] + ' - (' + LANG.RESULTS.TEMPLATES + ')">' + saved_data_select_options + '</optgroup>';
			}
		}
	} //end loop forms


	//data select init
	$('#Select__Athlete_Data_' + ath_id).html(data_select_Notes + data_select);
	//http://davidstutz.github.io/bootstrap-multiselect/
	$("#Select__Athlete_Data_" + ath_id).multiselect({
		maxHeight: 300,
		buttonContainer: '<div class="btn-group" id="Select__Athlete_Data_box_' + ath_id + '" />',
		buttonWidth: '250px',
		enableFiltering: true,
		enableCaseInsensitiveFiltering: true,
		filterPlaceholder: LANG.SELECT_SEARCH,
		enableCollapsibleOptGroups: true,
		enableClickableOptGroups: true,
		nonSelectedText: LANG.DATA_SELECT_PLACEHOLDER,
		numberDisplayed: 1,
		nSelectedText: ' - ' + LANG.DATA_SELECTED,
		includeSelectAllOption: true,
		selectAllText: LANG.SELECT_ALL,
		allSelectedText: LANG.ALL_SELECTED
	});
} //end Select__Athlete_Data__Init


function Select__Athlete_Data__Update(ath_id) { //#####  --only in FORMS_RESULTS
	Debug1('X.Html.', '-', get_Function_Name(), '-', [...arguments]);

	const dis_arr = []; //get disabled options
	$('#Select__Athlete_Data_' + ath_id + ' option:disabled').each(function (i, el) {
		dis_arr.push($(el).val());
	});
	
	Select__Athlete_Data__Init(ath_id); //remake select
	
	//put back disabled options
	$(dis_arr).each(function (i, el) {
		$('#Select__Athlete_Data_' + ath_id + ' option[value="' + el + '"]').attr('disabled', true);
	});
	
	$("#Select__Athlete_Data_" + ath_id).multiselect('rebuild'); //init select
} //end Select__Athlete_Data__Update


function Table__DATA__Filter_by_DATE() { //#####  --only in FORMS_RESULTS
	Debug1('1.Table.Filter.Date.', '-', get_Function_Name(), '-', [...arguments]);

	//each table
	$('table[id^=Table__Athlete_Form_]').each(function(index, table){
		const $data_table_tbody = $(this).find('tbody');
		//each tr row
		$data_table_tbody.find('tr').each(function(index2, tr){
			const row = $(tr);
			const date_time = moment.unix(row.attr('data-timestamp'));
			const is_after_start_date = (date_time.format('YYYY-MM-DD HH:mm:ss') >= V_DATE_FROM_moment.format('YYYY-MM-DD HH:mm:ss'));
			//we have date_time<=V_DATE_TO_moment bcz it is 23:59
			const is_before_end_date = (date_time.format('YYYY-MM-DD HH:mm:ss') <= V_DATE_TO_moment.format('YYYY-MM-DD HH:mm:ss'));
			const vis = (is_after_start_date && is_before_end_date);
			const is_visible = row.hasClass('vis');
			let hidden_Grey = 'hidden_Grey';

			if (is_after_start_date && !is_before_end_date) {
				hidden_Grey = 'hidden_Grey2';
			}
			if (!vis && is_visible) {
				//hide it
				row.removeClass('vis').addClass(hidden_Grey);
				row.find('th,td').removeClass('vis').addClass(hidden_Grey);
			}
			if (vis && !is_visible) {
				//show it
				row.removeClass('hidden_Grey hidden_Grey2').addClass('vis');
				row.find('th,td').removeClass('hidden_Grey hidden_Grey2').addClass('vis');
			}
		});
	});
} //end Table__DATA__Filter_by_DATE



//####################################################
// TEMPLATES #########################################

function Load_Saved_Form_Fields(ath_id, form_id, save_id, save_name) { //#####	 --only in FORMS_RESULTS
	Debug1(' 1.Template.Load.', '-', get_Function_Name(), '-', [...arguments]);

	const form_group_id = ath_id + '_' + form_id + '_S' + save_id;

	//Remove existing Fields from Form 
	$('#F' + form_group_id + ' .remove_form_field').each(function (i, el) {
		//close all previous data --make it remove_form_field
		$(el).trigger("click"); //close
	});
	

	//get Template data
	const Forms_Template_Data = V_FORMS_TEMPLATES[form_id][save_id].data;


	//add fields
	Forms_Template__Form_Fields__Load(ath_id, form_id, Forms_Template_Data, save_id, save_name);

	//set values back to form
	Forms_Template__Form_Fields__Values__Set(ath_id, form_id, save_id, Forms_Template_Data, 'form');


	//put save name in input field, so we can save/update again with it
	$('#Forms_Template__Save__Name_' + form_group_id).val(save_name);


	const athlete_name_show  = V_FORMS_TEMPLATES[form_id][save_id].athlete_name_show;
	const form_name_show = V_FORMS_TEMPLATES[form_id][save_id].form_name_show;

	//click checkbox if different
	if (athlete_name_show != $("#Diagram__Athlete_Name__Show_" + ath_id).val()) {
		$("#Diagram__Athlete_Name__Show_" + ath_id).prev().trigger("click");
	}
	if (form_name_show != $("#Diagram__Form_Name__Show_" + form_group_id).val()) {
		$("#Diagram__Form_Name__Show_" + form_group_id).prev().trigger("click");
	}

	
	loading.hide();

	Debug1(' 1.Template.Load.--', '-', get_Function_Name(), '--END--');
} //end Load_Saved_Form_Fields


//function Forms_Template__Form_Fields__Load(ath_id, form_id, Forms_Template_Data, save_id, save_name) { } //in common_functions.js


function Forms_Template__Save(ath_id, form_id, save_id, save_name) { //#####	 --only in FORMS_RESULTS
	Debug1(' 1.Template.Save.', '-', get_Function_Name(), '-', [...arguments]);
	
	//loading.show();
	const base_form_id = form_id;
	let form_group_id = ath_id + '_' + form_id;

	if (save_id) { 
		form_group_id = ath_id + '_' + form_id + '_S' + save_id; 
	}

	//Data
	const remove_athlete_id = true;
	const data_arr = DATA__Athlete_Form__Get(form_group_id, remove_athlete_id);

	const data = {
		data: data_arr,
		athlete_name_show: $("#Diagram__Athlete_Name__Show_" + ath_id).val(),
		form_name_show: $("#Diagram__Form_Name__Show_" + form_group_id).val()
	};
	//console.log(data_arr, JSON.stringify(data_arr));
	
	
	let Templates_Names = [];
	if (V_FORMS_TEMPLATES[base_form_id]) {
		Object.keys(V_FORMS_TEMPLATES[base_form_id]).forEach(function(save_id){
			if (V_FORMS_TEMPLATES[base_form_id][save_id].name != save_name) {
				Templates_Names.push(
					V_FORMS_TEMPLATES[base_form_id][save_id].name
				);
			}
		});
	}
	

	//validation
	jQuery.validator.addMethod("notExist", function(value, element, param) {
		return this.optional(element) || ($.inArray(value, Templates_Names) == -1);
	}, LANG.RESULTS.TEMPLATE_NAME_EXISTS); //Name exists
	
	jQuery.validator.addMethod("noData", function(value, element, param) {
		return this.optional(element) || ($("#F"+form_group_id+" input[name='data_graph_name[]']").length != 0);
	}, LANG.DATA_SELECT_NO_DATA); //No Data
	

	//validate
	$("form #FORM__Forms_Template_Save_" + form_group_id).validate();
	

	//if valid
	if ($('#Forms_Template__Save__Name_' + form_group_id).valid()) {

		const save_name_new = $('#Forms_Template__Save__Name_' + form_group_id).val();
		//if the name is the same as the selected text then save with the same id, else save new
		const id = (save_name_new == save_name ? save_id : 0);
		const post_data = {
			group_id		: V_Group_id,
			athlete_id		: ath_id,
			id				: id,
			title			: save_name_new,
			form_id			: base_form_id,
			template_type	: 'forms',
			data			: JSON.stringify(data)
		};
		Debug2('1.Template.Save.Valid. Forms_Template__Save - template_save', [post_data]);

		$.post('results/ajax.template_save.php', post_data, function(data, result) {
			if (data != 'ERROR' && data.indexOf('empty_message') == -1) {
				$('#add_data').html(data);
				parent.Swal({
					type: 'success',
					title: LANG.RESULTS.TEMPLATE_SAVE_SUCCESS.replace('{TEMPLATE_NAME}', save_name_new),
					showConfirmButton: false,
					timer: 2000
				});
				Select__Athlete_Data__Update(ath_id);
			}
			else {
				parent.Swal({
					type: 'error',
					title: data,
					showConfirmButton: false,
					timer: 5000
				});
			}
		});
	}
} //end Forms_Template__Save

// TEMPLATES #########################################
//####################################################

