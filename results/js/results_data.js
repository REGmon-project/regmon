if (!Production_Mode) "use strict"; //remove on production so "const" works on iOS Safari 

var V_Selected__Groups = [];
var V_Selected__Athletes = [];
var V_Selected__Athletes__With_Data = []; //used also from results_data_intervals.js
var V_Selected__Athletes__ID_Name = {}; //used also from results_data_intervals.js + diagram_functions.js
var V_Selected__Athletes__Diagram__Show_Name = {};
var V_Selected__Forms = [];
var V_Selected__Data = [];
var V_Selected__Data__Forms__Changes = {};
var V_Selected__Data__Intervals__Changes = {};
var V_Selected__Data__Time__Last = 0;

var V_Results_Template_AUTO_Load = false;
var V_Results_Template_id = 0;


jQuery(function() {
	//init Results Templates Select
	Select__Results_Templates__Init(false);

	//init Groups Select
	Select__Groups__Init(); //start selecting
});



//###############################################
// DATE #########################################

function DATE__Submit() {
	Debug1('  2.Submit.Click.', '-', get_Function_Name(), '-', [...arguments]);

	DATA__Changes__Save('Button__DATE__Submit');

	Select__Groups__Init();

	//if all objects are empty
	if (JSON.stringify(V_Selected__Groups) +
		JSON.stringify(V_Selected__Athletes) +
		JSON.stringify(V_Selected__Forms) +
		JSON.stringify(V_Selected__Data) == '[][][][]')
	{
		//click Button__Select__Groups__Submit
		$("#Button__Select__Groups__Submit").trigger("click");
	}
}

// DATE #########################################
//###############################################



//####################################################
// SELECT DATA #######################################

//init multiselect
function Select__Multiselect__Init(select_el, select_box, title, disabledText, rebuild_Select) {
	Debug2('  2.Select.Init.', '-', get_Function_Name(), '-', [...arguments]);

	//http://davidstutz.github.io/bootstrap-multiselect/
	$(select_el).multiselect({
		maxHeight: 300,
		buttonContainer: '<div class="btn-group" id="' + select_box + '" />',
		buttonWidth: '100%',
		enableFiltering: true,
		enableCaseInsensitiveFiltering: true,
		filterPlaceholder: LANG.SELECT_SEARCH,
		enableCollapsibleOptGroups: true,
		enableClickableOptGroups: true,
		nonSelectedText: title + LANG.SELECT_PLACEHOLDER,
		numberDisplayed: 1,
		nSelectedText: ' - ' + title + LANG.SELECTED,
		includeSelectAllOption: true,
		selectAllText: LANG.SELECT_ALL,
		allSelectedText: LANG.ALL_SELECTED,
		disableIfEmpty: true,
		disabledText: disabledText
		//onChange: function(option, checked) {alert(option.length + ' options ' + (checked ? 'selected' : 'deselected'));}
	});

	if (rebuild_Select) {
		$(select_el).multiselect('rebuild');
	}
}


//init Groups Select
function Select__Groups__Init() {
	Debug1('1.Select.Init.1. -----', '-', get_Function_Name(), '-', [...arguments]);

	if (V_Selected__Groups.length) {
		V_Selected__Groups.forEach(function (val) {
			$('#Select__Groups option[value="' + val + '"]').prop('selected', true);
		});
	}
	

	const rebuild_Select = (V_Selected__Groups.length ? true : false);
	Select__Multiselect__Init('#Select__Groups', 'Select__Groups__Box', LANG.GROUP, LANG.GROUPS_SELECT_NO_GROUP, rebuild_Select);
	

	$("#Button__Select__Groups__Submit").off('click').on('click',function(){
		Debug1('  2.Submit.Click.1. Button__Select__Groups__Submit click');

		loading.show();

		let selected_groups = [];
		$('#Select__Groups option:selected').each(function() {
			selected_groups.push($(this).val());
		});
		V_Selected__Groups = selected_groups;

		DATA__Changes__Save('Button__Select__Groups__Submit');
		
		const load_data = {
			group_ids: selected_groups.join(',')
		};
		$("#Select__Athletes__Div").load("results/ajax.athletes_select_in_groups.php", load_data, function(data, result) {
			loading.hide();
			Select__Athletes__Init();
		});

		$("#Select__Forms__Div").html(''); //reset forms
		$("#Select__Forms_Fields__Div").html(''); //reset forms fields
		$("#DATA__Div").html(''); //reset DATA div

		//reset V_FORMS_DATA, V_USED_DATA, V_INTERVAL_DATA
		DATA__All__Reset();
	});
	
	if (V_Selected__Groups.length) {
		$("#Button__Select__Groups__Submit").trigger("click");
	}
}


//init Athletes Select
function Select__Athletes__Init() {
	Debug1('1.Select.Init.2. -----', '-', get_Function_Name(), '-', [...arguments]);

	if (V_Selected__Athletes.length) {
		V_Selected__Athletes.forEach(function (val) {
			$('#Select__Athletes option[value="' + val + '"]').prop('selected', true);
		});
	}
	

	const rebuild_Select = (V_Selected__Athletes.length ? true : false);
	Select__Multiselect__Init('#Select__Athletes', 'Select__Athletes__Box', LANG.ATHLETE, LANG.ATHLETES_SELECT_NO_ATHLETE, rebuild_Select);	
	

	$("#Button__Select__Athletes__Submit").off('click').on('click',function(){
		Debug1('  2.Submit.Click.2. Button__Select__Athletes__Submit click');

		loading.show();

		let selected_athletes = [];
		V_Selected__Athletes__ID_Name = {};

		$('#Select__Athletes option:selected').each(function() {
			selected_athletes.push($(this).val());
			//get only ath_id      and      take only lastname firstname, not sport
			V_Selected__Athletes__ID_Name[$(this).val().split('_')[1]] = $(this).text().split(' - ')[0];
		});
		V_Selected__Athletes = selected_athletes;

		DATA__Changes__Save('Button__Select__Athletes__Submit');

		const post_data = {
			date_from: V_DATE_FROM_moment.format('YYYY-MM-DD HH:mm:ss'),
			date_to: V_DATE_TO_moment.format('YYYY-MM-DD HH:mm:ss'),
			athletes_ids: "'" + selected_athletes.join("','") + "'"
		};
		$("#Select__Forms__Div").load("results/ajax.forms_select_in_categories.php", post_data, function (data, result) {
			loading.hide();
			V_Selected__Athletes = selected_athletes;
			Select__Forms__Init();
		});

		$("#Select__Forms_Fields__Div").html(''); //reset forms fields
		$("#DATA__Div").html(''); //reset DATA div

		//reset V_FORMS_DATA, V_USED_DATA, V_INTERVAL_DATA
		DATA__All__Reset();
	});
	
	if (V_Selected__Athletes.length) {
		$("#Button__Select__Athletes__Submit").trigger("click");
	}
}


//init Forms Select
function Select__Forms__Init() {
	Debug1('1.Select.Init.3. -----', '-', get_Function_Name(), '-', [...arguments]);

	if (V_Selected__Forms.length) {
		V_Selected__Forms.forEach(function (val) {
			$('#Select__Forms option[value="' + val + '"]').prop('selected', true);
		});
	}
	

	const rebuild_Select = (V_Selected__Forms.length ? true : false);
	Select__Multiselect__Init('#Select__Forms', 'Select__Forms__Box', LANG.FORM, LANG.FORMS_SELECT_NO_DATA, rebuild_Select);	
	

	$("#Button__Select__Forms__Submit").off('click').on('click',function(){
		Debug1('  2.Submit.Click.3. Button__Select__Forms__Submit click');

		loading.show();

		let selected_forms = [];
		$('#Select__Forms option:selected').each(function() {
			selected_forms.push($(this).val());
		});
		V_Selected__Forms = selected_forms;

		DATA__Changes__Save('Button__Select__Forms__Submit');

		const load_data = {
			date_from: V_DATE_FROM_moment.format('YYYY-MM-DD HH:mm:ss'),
			date_to: V_DATE_TO_moment.format('YYYY-MM-DD HH:mm:ss'),
			athletes_ids: "'" + V_Selected__Athletes.join("','") + "'",
			forms_ids: selected_forms.join(',')
		};
		$("#Select__Forms_Fields__Div").load("results/ajax.forms_fields_select_in_forms.php", load_data, function (data, result) {
			loading.hide();
			Select__Forms_Fields__Init();
		});
		$("#DATA__Div").html(''); //reset DATA div

		//reset V_FORMS_DATA, V_USED_DATA, V_INTERVAL_DATA
		DATA__All__Reset();
	});
	
	if (V_Selected__Forms.length) {
		$("#Button__Select__Forms__Submit").trigger("click");
	}
}


//init Forms Fields Select
function Select__Forms_Fields__Init() {
	Debug1('1.Select.Init.4. -----', '-', get_Function_Name(), '-', [...arguments]);

	if (V_Selected__Data.length) {
		V_Selected__Data.forEach(function(val){
			$('#Select__Forms_Fields option[value="' + val + '"]').prop('selected', true);
		});
	}
	

	const rebuild_Select = (V_Selected__Data.length ? true : false);
	Select__Multiselect__Init('#Select__Forms_Fields', 'Select__Forms_Fields__Box', LANG.DATA, LANG.DATA_SELECT_NO_DATA, rebuild_Select);	
	

	$("#Button__Select__Forms_Fields__Submit").off('click').on('click',function(){
		Debug1('  2.Submit.Click.4. Button__Select__Forms_Fields__Submit click');

		loading.show();

		let selected_data = [];
		$('#Select__Forms_Fields option:selected').each(function() {
			selected_data.push($(this).val());
		});
		V_Selected__Data = selected_data;

		DATA__Changes__Save('Button__Select__Forms_Fields__Submit');

		//reset V_FORMS_DATA, V_USED_DATA, V_INTERVAL_DATA
		DATA__All__Reset();

		const load_data = {
			data_ids: selected_data.join(','),
			athletes_ids: "'" + V_Selected__Athletes.join("','") + "'",
			date_from: V_DATE_FROM_moment.format('YYYY-MM-DD HH:mm:ss'),
			date_to: V_DATE_TO_moment.format('YYYY-MM-DD HH:mm:ss')
		};

		$("#DATA__Div").load("results/ajax.selected_data_load.php", load_data, function(data, result) {
			
			//we get Update to objects V_FORMS_DO, V_FORMS_N_FIELDS, V_FORMS_DATA

			loading.hide();
			

			if (Object.keys(V_FORMS_DATA).length) {
				Selected__DATA__init();

				//enable DATA__Interval__Add button
				$('#DATA__Interval__Add').prop('disabled',false);
			}
			else {
				//disable DATA__Interval__Add button
				$('#DATA__Interval__Add').prop('disabled',true);
			}
		});
	});
	
	if (V_Selected__Data.length) {
		$("#Button__Select__Forms_Fields__Submit").trigger("click");
	}
}


function DATA__All__Reset() {
	Debug1('1.Reset.', '-', get_Function_Name(), '-', [...arguments]);

	//reset CALX --first this bcz it needs the form to exist, to destroy
	V_Calx_Sheets.forEach(function(form){
		Debug1('  2.Calc.D. Destroy CALX ===============', [form]);
		//remove sheet object
		$(form).calx('destroy');
	});
	V_Calx_Sheets = [];


	//reset V_FORMS_DATA
	V_FORMS_DATA = {};

	//reset V_USED_DATA
	V_USED_DATA = {};

	//reset users fieldsets + forms + fields + tables
	$("#Athletes_Data_Fieldsets").html('');

	//reset V_INTERVAL_DATA
	V_INTERVAL_DATA = {};

	//reset intervals fieldsets + forms + fields + tables
	$("#Intervals_Data_Fieldsets").html('');


	//reset chart
	V_Chart.destroy();

	//init chart
	$('#container_graph').highcharts({});
	V_Chart = $('#container_graph').highcharts();
}



//it uses a lot of promises to handle the proper order of loading, in order to avoid a callback hell
//(async) init Athletes Data //#####################################################################
async function Selected__DATA__init() {
	Debug1('*********************');
	Debug1('1.', '-', get_Function_Name(), '-', [...arguments]);
	

	//reset
	V_Selected__Athletes__With_Data = [];
	
	//go through V_FORMS_DATA for every athlete
	await new Promise((resolve, reject) => {
		for (let ath_id in V_FORMS_DATA) {
			if (Object.prototype.hasOwnProperty.call(V_FORMS_DATA, ath_id)) {

				Fieldset__Athlete__Init(ath_id, V_Selected__Athletes__ID_Name[ath_id]);
			}
			V_Selected__Athletes__With_Data.push(ath_id);
		}

		Debug1('1.-- Fieldset__Athlete__Init ALL **************** --END--');
		resolve('Fieldset__Athlete__Init ALL Added');
	});
	
	await new Promise((resolve, reject) => setTimeout(resolve, 50)); //delay

	

	//load V_RESULTS_TEMPLATES
	if (V_Results_Template_AUTO_Load && V_Results_Template_id) {

		let RESULTS_TEMPLATE = V_RESULTS_TEMPLATES[V_Results_Template_id];
		
		//1. until here we have the Form Data with the changes of Form Data Template from V_FORMS_TEMPLATES
		
		await new Promise((resolve, reject) => {
			//2. here we apply the changes exist in Data_Forms save of V_RESULTS_TEMPLATES

			DATA__Forms__Load(RESULTS_TEMPLATE.Data_Forms);

			resolve('Load_FORMS_Objects Loaded');
		});
		

		//set athlete_name_show -- for each athlete
		Selected__Athletes__Diagram__Show_Name__Set(RESULTS_TEMPLATE.users_show_name);

		
		//3. here we load the INTERVAL Data and apply the changes exist in Data_Intervals save
		await DATA__Intervals__Load(RESULTS_TEMPLATE.Data_Intervals);
		

		//reset AUTO_Load
		V_Results_Template_AUTO_Load = false;
		V_Results_Template_id = 0;
		
		await new Promise((resolve, reject) => setTimeout(resolve, 50)); //delay
		

		await new Promise((resolve, reject) => {
			Chart__Update();

			resolve('Chart__Update Loaded');
		});
		
		//end of process at Chart__Update
	}
	//load selected data
	else {
		if (Object.keys(V_Selected__Data__Forms__Changes).length) {

			DATA__Forms__Load(V_Selected__Data__Forms__Changes);

			Debug1('2. DATA__Forms__Load Loaded');
		}
		if (Object.keys(V_Selected__Data__Intervals__Changes).length) {
			
			await DATA__Intervals__Load(V_Selected__Data__Intervals__Changes);

			Debug1('3. DATA__Intervals__Load Loaded');
		}

		Chart__Update();
	}

	Debug1('1.-- Selected__DATA__init --END--');
	Debug1('*******************************');
}

// SELECT DATA #######################################
//####################################################




//###############################################
// HTML #########################################


function Fieldset__Athlete__Init(athlete_id, athlete_name) { //#####	 --RESULTS version
	Debug1('************************************************');
	Debug1('1.Fieldset.Athlete.Init.', '-', get_Function_Name(), '-', [...arguments]);


	//add template to HTML
	$('#Athletes_Data_Fieldsets').append(
		HTML_Template__Fieldset__Athlete(athlete_id, athlete_name)
	);
	

	//init Athlete Fieldset ##########################################

	//fieldset collapsible
	collapse_set('#fs-ATHLETE_' + athlete_id);
	

	//init fieldset expand/collapse
	Fieldset__Expand_Collapse__Init('#fs-ATHLETE_' + athlete_id);

	//init Athlete Fieldset ##########################################
	

	//go through V_FORMS_DATA for this athlete forms

	//####### key_form_id may be base_form_id or save_form_id
	
	//first the Notes
	for (let key_form_id in V_FORMS_DATA[athlete_id]) { //forms
		if (Object.prototype.hasOwnProperty.call(V_FORMS_DATA[athlete_id], key_form_id)) {

			const base_form_id = key_form_id.split('_S')[0];
			const t_save_id = key_form_id.split('_S')[1] || false;
			const t_save_name = V_FORMS_N_FIELDS[key_form_id][0] || false;

			//only note forms
			if (key_form_id == 'note') { 
				for (const key_index in V_FORMS_DATA[athlete_id][key_form_id]) { //fields
					if (Object.prototype.hasOwnProperty.call(V_FORMS_DATA[athlete_id][key_form_id], key_index)) {
						const data_line = V_FORMS_DATA[athlete_id][key_form_id][key_index];
						const form_id = key_form_id;
						const field_name = data_line.name;
						const field_num = key_index.substring(1); //cut the '_' from the beginning
						if (V_FORMS_DO[form_id] &&
							V_FORMS_DO[form_id].indexOf(field_num) != -1) //if form field is selected
						{
							Athlete_Form_Field_DATA__Add(athlete_id, form_id, field_num, field_name, t_save_id, t_save_name);
						}
					}
				}
			}
		}
	}
	
	//then the rest forms
	for (let key_form_id in V_FORMS_DATA[athlete_id]) { //forms
		if (Object.prototype.hasOwnProperty.call(V_FORMS_DATA[athlete_id], key_form_id)) {

			const base_form_id = key_form_id.split('_S')[0];
			const t_save_id = key_form_id.split('_S')[1] || false;
			const t_save_name = V_FORMS_N_FIELDS[key_form_id][0] || false;

			//rest of the forms --except notes
			if (key_form_id != 'note') {
				for (const key_index in V_FORMS_DATA[athlete_id][key_form_id]) { //fields
					if (Object.prototype.hasOwnProperty.call(V_FORMS_DATA[athlete_id][key_form_id], key_index)) {
						//key_form_id = 3_S18
						const data_line = V_FORMS_DATA[athlete_id][key_form_id][key_index];
						const field_name = data_line.name;
						//cut the '_' from the beginning
						const field_num = key_index.substring(1);

						if (V_FORMS_DO[base_form_id] &&
							V_FORMS_DO[base_form_id].indexOf(field_num) != -1) //if form field is selected
						{
							Athlete_Form_Field_DATA__Add(athlete_id, base_form_id, field_num, field_name, t_save_id, t_save_name);
						}
					}
				}
				

				Debug1('  2.Calc.UC. Update/Calculate CALX ===============', [athlete_id + '_' + key_form_id, '#F' + athlete_id + '_' + key_form_id]);
				//we do that here so not called for every field but for all at the end for each form
				//Update CALX
				$('#F' + athlete_id + '_' + key_form_id).calx('update');
				//calculate CALX
				$('#F' + athlete_id + '_' + key_form_id).calx('calculate');
				

				//SAVES for this form
				if (V_FORMS_DO['saves'] &&
					V_FORMS_DO['saves'][base_form_id])
				{
					//each save_id
					V_FORMS_DO['saves'][base_form_id].forEach(function (save_id)
					{
						const form_group_id = athlete_id + '_' + base_form_id + '_S' + save_id;
						const Forms_Template_Data = V_FORMS_TEMPLATES[base_form_id][save_id].data;
						const save_name = V_FORMS_TEMPLATES[base_form_id][save_id].name;


						Forms_Template__Form_Fields__Load(athlete_id, base_form_id, Forms_Template_Data, save_id, save_name);
						

						Forms_Template__Form_Fields__Values__Set(athlete_id, base_form_id, save_id, Forms_Template_Data, 'Form');


						const athlete_name_show = V_FORMS_TEMPLATES[base_form_id][save_id].athlete_name_show;
						const form_name_show = V_FORMS_TEMPLATES[base_form_id][save_id].form_name_show;
						
						if (athlete_name_show != $("#Diagram__Athlete_Name__Show_" + athlete_id).val()) {
							$("#Diagram__Athlete_Name__Show_" + athlete_id).prev().trigger("click");
						}
						if (form_name_show != $("#Diagram__Form_Name__Show_" + form_group_id).val()) {
							$("#Diagram__Form_Name__Show_" + form_group_id).prev().trigger("click");
						}


						Debug1('  2.Calc.UC. Update/Calculate CALX ===============', [form_group_id, '#F' + form_group_id]);
						//Update CALX
						$('#F' + form_group_id).calx('update');
						//calculate CALX
						$('#F' + form_group_id).calx('calculate');
					});
				}
			}
		}
	}
	
	Debug1('1.Fieldset.Init.', '-', get_Function_Name(), '-', '--END--');
	Debug1('**************************************************');
} //Fieldset__Athlete__Init(athlete_id, athlete_name)


// HTML #########################################
//###############################################




//####################################################
// TEMPLATES #########################################


//make RESULTS_TEMPLATE Saves Select from V_RESULTS_TEMPLATES
function Select__Results_Templates__Init(selected) {
	Debug1('1.Html.Select.', '-', get_Function_Name(), '-', [...arguments]);

	let select_options = '';
	Object.keys(V_RESULTS_TEMPLATES).forEach(function (id) {
		const selected_attr = ((selected && selected == id) ? ' selected' : '');
		select_options += '<option value="' + id + '"' + selected_attr + '>' + V_RESULTS_TEMPLATES[id].name + '</option>';
	});

	//add Select__Results_Templates
	$('#Select__Results_Templates__Div').html(
		'<select id="Select__Results_Templates" class="form-control">' +
			select_options +
		'</select>'
	);
	
	//if not have a selected we give the last saved
	if (selected !== false && !selected) {
		$('#Select__Results_Templates option:contains("'+ $('#Results_Template__Name').val() +'")').attr('selected',true);
	}
	
	//Template Select - init chosen
	$("#Select__Results_Templates").chosen({
		width: '100%; max-width:400px; margin:5px; text-align:left',
		no_results_text: LANG.NO_RESULTS,
		search_contains: true,
		disable_search_threshold: 10,
		allow_single_deselect: true
	});
}


function Selected__Athletes__Diagram__Show_Name__Set(users_show_name_obj) {
	Debug1(' 1.Changes.Save.', '-', get_Function_Name(), '-', [...arguments]);

	//set athlete_name_show -- for each athlete
	Object.entries(users_show_name_obj).forEach(function(athlete){
		let ath_id = athlete[0];
		let athlete_name_show = athlete[1];
		if (athlete_name_show != $("#Diagram__Athlete_Name__Show_" + ath_id).val()) {
			
			$('#Diagram__Athlete_Name__Show_' + ath_id).prev().trigger("click");
		}
	});
}


function Selected__Athletes__Diagram__Show_Name__Get() {
	Debug1(' 1.Changes.Save.', '-', get_Function_Name(), '-', [...arguments]);

	let Diagram__Athletes_Names__Show__obj = {};
	V_Selected__Athletes__With_Data.forEach(function(ath_id, index) {
		Diagram__Athletes_Names__Show__obj[ath_id] = $("#Diagram__Athlete_Name__Show_" + ath_id).val();
	});
	return Diagram__Athletes_Names__Show__obj;
}


function Results_Template__Save() {
	Debug1('1.Template.Save.', '-', get_Function_Name(), '-', [...arguments]);
	
	let Templates_Names = [];
	Object.keys(V_RESULTS_TEMPLATES).forEach(function (save_id) {
		//exclude selected
		if (V_RESULTS_TEMPLATES[save_id].name != $('#Select__Results_Templates option:selected').text()) {
			Templates_Names.push(V_RESULTS_TEMPLATES[save_id].name);
		}
	});

	//Name validator
	jQuery.validator.addMethod("notExist", function(value, element, param) {
		return this.optional(element) || ($.inArray(value, Templates_Names) == -1);
	}, LANG.RESULTS.TEMPLATE_NAME_EXISTS); //Name exists
	

	//validate
	$("#Form__Results_Template").validate();
	
	
	//Save
	if ($('#Results_Template__Name').valid()) {
		const save_name = $('#Results_Template__Name').val();
		let id = 0;

		if ($('#Select__Results_Templates option:selected').text() == save_name) {
			//if the save_name is the same with the selected text then save with the same id else we make new
			id = $('#Select__Results_Templates').val();
		}

		let confirmSave = true;
		if (id) {
			if (!confirm(LANG.RESULTS.TEMPLATE_OVERWRITE_CONFIRM)) {
				confirmSave = false;
			}
		}
		if (confirmSave) {
			//save obj
			let save = {
				date_from		: V_DATE_FROM_moment.format('YYYY-MM-DD HH:mm:ss'),
				date_to			: V_DATE_TO_moment.format('YYYY-MM-DD HH:mm:ss'),
				groups			: V_Selected__Groups,
				athletes		: V_Selected__Athletes,
				forms			: V_Selected__Forms,
				fields			: V_Selected__Data,
				Data_Forms		: DATA__Forms__Get(),
				Data_Intervals	: DATA__Intervals__Get(),
				users_show_name	: Selected__Athletes__Diagram__Show_Name__Get()
			};
			//console.log(save);
	
			
			const post_data = {
				group_id		: V_Group_id,
				athlete_id		: V_Athlete_id,
				id				: id,
				title			: save_name,
				template_type	: 'results',
				data			: JSON.stringify(save)
				//+ to make them integer
				// GlobalView	: +$('#GlobalView').is(':checked'),
				// LocationView: +$('#LocationView').is(':checked'),
				// GroupView	: +$('#GroupView').is(':checked'),
				// TrainerView	: +$('#TrainerView').is(':checked'),
				// Private		: +$('#Private').is(':checked')
			};
			$.post('results/ajax.template_save.php', post_data, function(data, result) {
				if (data != 'ERROR') {
					parent.Swal({
						type: 'success',
						title: LANG.RESULTS.TEMPLATE_SAVE_SUCCESS.replace('{TEMPLATE_NAME}', save_name),
						showConfirmButton: false,
						timer: 2000
					});

					//update V_RESULTS_TEMPLATES
					$('#add_data').html(data);

					//make select
					Select__Results_Templates__Init(id);
				}
				else {
					parent.Swal({
						type: 'error',
						title: 'Error!',
						showConfirmButton: false,
						timer: 2000
					});
				}
			});
		}
	}
} //end Results_Template__Save


function Results_Template__Load(save_id, save_name) {
	Debug1('1.Template.Load.', '-', get_Function_Name(), '-', [...arguments]);

	//put saved values
	const save = V_RESULTS_TEMPLATES[save_id];

	let date_from = moment(save.date_from + ':00', 'YYYY-MM-DD HH:mm:ss').format('YYYY-MM-DD HH:mm');
	let date_to = moment(save.date_to + ':59', 'YYYY-MM-DD HH:mm:ss').format('YYYY-MM-DD HH:mm');
	
	if (V_LANG_CURRENT == 'de') {
		date_from = moment(save.date_from + ':00', 'YYYY-MM-DD HH:mm:ss').format('DD.MM.YYYY HH:mm');
		date_to = moment(save.date_to + ':59', 'YYYY-MM-DD HH:mm:ss').format('DD.MM.YYYY HH:mm');
	}


	if (V_OPEN_TEMPLATE != '0') {
		const template_arr = V_OPEN_TEMPLATE.split('__');
		
		if (template_arr[1] == '2') {		 
			//189__2__0__0 //do nothing, we keep the save dates
		}
		else if (template_arr[1] == '0') {
			//189__0__26.11.2019__02.12.2019
			date_from = template_arr[2] + ' 00:00';
			date_to = template_arr[3] + ' 23:59';

			if (V_LANG_CURRENT == 'de') {
				date_from = moment(template_arr[2] + ' 00:00:00', 'YYYY-MM-DD HH:mm:ss').format('DD.MM.YYYY HH:mm');
				date_to = moment(template_arr[3] + ' 23:59:59', 'YYYY-MM-DD HH:mm:ss').format('DD.MM.YYYY HH:mm');
			}
		}
		else if (template_arr[1] == '1') {
			//175__1__2__week
			date_from = moment().subtract(template_arr[2], template_arr[3]).format('YYYY-MM-DD') + ' 00:00';
			date_to = moment().format('YYYY-MM-DD') + ' 23:59';

			if (V_LANG_CURRENT == 'de') {
				date_from = moment().subtract(template_arr[2], template_arr[3]).format('DD.MM.YYYY') + ' 00:00';
				date_to = moment().format('DD.MM.YYYY') + ' 23:59';
			}
		}

		V_OPEN_TEMPLATE = '0';
	}
	
	
	$('#datetimepicker_from').data("DateTimePicker").date(date_from);
	$('#datetimepicker_to').data("DateTimePicker").date(date_to);


	//reset All Selections
	Selected__All__Reset();
	
	//set save selected
	V_Selected__Groups 	= save.groups;
	V_Selected__Athletes = save.athletes;
	V_Selected__Forms 	= save.forms;
	V_Selected__Data 	= save.fields;
	//console.log(V_Selected__Groups, V_Selected__Athletes, V_Selected__Forms, V_Selected__Data, V_INTERVAL_DATA);
		

	//with this continue to Selected__DATA__init --after Select__Groups__Init
	V_Results_Template_AUTO_Load = true;
	V_Results_Template_id = save_id;
	

	//put save name in input field, so we can save/update on it
	$('#Results_Template__Name').val(save_name);
	
	setTimeout(function(){
		//init
		Select__Groups__Init();
	}, 0);

} //end Results Template Load


function Selected__All__Reset() {
	Debug1('1.Reset.', '-', get_Function_Name(), '-', [...arguments]);

	//reset V_FORMS_DATA, V_USED_DATA, V_INTERVAL_DATA
	DATA__All__Reset();
	
	V_Selected__Groups = [];
	V_Selected__Athletes = [];
	V_Selected__Forms = [];
	V_Selected__Data = [];
	
	//console.log(V_Selected__Groups, V_Selected__Athletes, V_Selected__Forms, V_Selected__Data, V_INTERVAL_DATA);
	
	//clean up -- reset all first
	$('#Select__Groups').multiselect('deselectAll', false);	
	$('#Select__Groups').multiselect('updateButtonText');
	$("#Select__Athletes__Div").html(''); //reset athletes
	$("#Select__Forms__Div").html(''); //reset forms
	$("#Select__Forms_Fields__Div").html(''); //reset forms fields
	$("#DATA__Div").html(''); //reset data
	
	loading.hide();
}

//save FORMS and INTERVAL changes
function DATA__Changes__Save(elem) {
	Debug1('1.Data.Changes.Save.', '-', get_Function_Name(), '-', [...arguments]);

	let Time_now = Date.now() / 1000 | 0; //timestamp in seconds
	let DATA__Forms = {};
	let DATA__Intervals = {};

	//if longer than 15 seconds save it (10 sec on local with some data to load a save)
	if ((Time_now - V_Selected__Data__Time__Last) > 15) {
		DATA__Forms = DATA__Forms__Get();
		DATA__Intervals = DATA__Intervals__Get();

		//set V_Selected__Athletes__Diagram__Show_Name
		V_Selected__Athletes__Diagram__Show_Name = Selected__Athletes__Diagram__Show_Name__Get();


		if (Object.keys(DATA__Forms).length ||
			Object.keys(DATA__Intervals).length)
		{
			if (Object.keys(DATA__Forms).length) {
				V_Selected__Data__Forms__Changes = DATA__Forms;
			}
			if (Object.keys(DATA__Intervals).length) {
				V_Selected__Data__Intervals__Changes = DATA__Intervals;
			}
			V_Selected__Data__Time__Last = Time_now;
		}
	}
}


function DATA__Forms__Get() {
	Debug1(' 1.Data.Changes.Get.', '-', get_Function_Name(), '-', [...arguments]);

	const DATA__Forms = {};

	//for each #Form  --each form start with "F" -- form#F2_1_S116
	$('#C_Athletes_Data form[id^=F]').each(function (i, el) {
		const form_group_id = (el.id).replace('F', '');
		const DATA__Athlete_Form = DATA__Athlete_Form__Get(form_group_id);

		//set form_name_show
		const form_name_show = $("#Diagram__Form_Name__Show_" + form_group_id).val();
		DATA__Athlete_Form['form_name_show'] = form_name_show;

		DATA__Forms[form_group_id] = DATA__Athlete_Form;
	});
	
	return DATA__Forms;
}


function DATA__Intervals__Get() {
	Debug1(' 1.Data.Changes.Get.', '-', get_Function_Name(), '-', [...arguments]);

	const DATA__Intervals = {};

	 //for each INT  --each form start with "FI" -- form#FI1
	$('#C_Intervals_Data form[id^=FI]').each(function (i, el) {
		const interval_id = (el.id).replace('FI', '');
		DATA__Intervals[interval_id] = DATA__Interval_Form__Get(interval_id);
	});

	return DATA__Intervals;
}


//here we update form_name + athlete_name Diagram Show -for Selections made after Loading Data
function DATA__Forms__Load(DATA__Forms) {
	Debug1('1.Template.Load.Forms.', '-', get_Function_Name(), '-', [...arguments]);
	
	//DATA__Forms can be V_Selected__Data__Forms__Changes or V_RESULTS_TEMPLATES[save_id].Data_Forms
	Object.keys(DATA__Forms).forEach(function(ath__form__saveid){
		const Saved_Data = DATA__Forms[ath__form__saveid];
		const tmp = ath__form__saveid.split('_');
		const ath_id = tmp[0].replace('F', '');
		const form_id = tmp[1];
		let save_id = 0;
		if (tmp[2]) {
			save_id = tmp[2].replace('S', '');
		}

		let form_group_id = ath_id + '_' + form_id;
			
		if (save_id) {
			form_group_id = ath_id + '_' + form_id + '_S' + save_id;


			//set values back to form --no need that
			//Forms_Template__Form_Fields__Values__Set(ath_id, form_id, save_id, Saved_Data, form_type);


			//const athlete_name_show = V_FORMS_TEMPLATES[form_id][save_id].athlete_name_show;
			//const form_name_show = V_FORMS_TEMPLATES[form_id][save_id].form_name_show;
			const athlete_name_show = V_Selected__Athletes__Diagram__Show_Name[ath_id] || 0;
			const form_name_show = Saved_Data.form_name_show || 0;
			//console.log(athlete_name_show, form_name_show);
			
			if (athlete_name_show != $("#Diagram__Athlete_Name__Show_" + ath_id).val()) {
				$("#Diagram__Athlete_Name__Show_" + ath_id).prev().trigger("click");
			}
			if (form_name_show != $("#Diagram__Form_Name__Show_" + form_group_id).val()) {
				$("#Diagram__Form_Name__Show_" + form_group_id).prev().trigger("click");
			}
		}

	});

	Debug1('1.Template.Load.Forms.--', '-', get_Function_Name(), '-', '--END--');
} //end DATA__Forms__Load



//#########################################################################################
//it uses promises to handle the proper order of loading, in order to avoid a callback hell
async function DATA__Intervals__Load(DATA__Intervals) {
	Debug1('1.Template.Load.INT. async', '-', get_Function_Name(), '-', [...arguments]);
	Debug2('1.Template.Load.INT. async', '-', get_Function_Name(), '-', [JSON.stringify(V_INTERVAL_DATA), JSON.stringify(DATA__Intervals)]);


	//add interval
	await new Promise((resolve, reject) => {
		Object.keys(DATA__Intervals).forEach(function(interval_id){
			//add new interval first
			$('#DATA__Interval__Add').trigger("click");
		});

		Debug1('  2.Template.Load.INT.1. add INTERVAL to DOM --End--');
		resolve('add INTERVAL to DOM');
	});
	
	//delay
	await new Promise((resolve, reject) => setTimeout(resolve, 50));
	

	//loop DATA__Intervals
	await new Promise((resolve, reject) => {
		Object.keys(DATA__Intervals).forEach(function(interval_id){
			const this_interval = DATA__Intervals[interval_id];
			const this_interval_data = this_interval.data;
			
			//put basic interval values
			$('#formula_cells_INT_' + interval_id).val(this_interval.formula_cells).trigger('change');
			$('#formula_period_INT_' + interval_id).val(this_interval.formula_period).trigger('change');

			//always start 0
			if (this_interval.formula_X_axis_show == '1') {
				if (!$('#formula_X_axis_show_ck_INT_' + interval_id).prop('checked')) {
					$('#formula_X_axis_show_ck_INT_' + interval_id).trigger('click');
				}
			}
			
		
			//add data
			$.each(this_interval_data, function (i, interval_data) {
				//interval with Raw data
				if (interval_data.is_interval_form == 'false') {
					Interval_Form_Field__for_RAW_Data__Add(interval_id);
				}
				//interval with Interval data SingleColumn
				else if (interval_data.is_single_column == 'true') { 
					Interval_Form_Field__for_INTERVAL_SingleColumn_Data__Add(interval_id);
				}
				//interval with Interval data
				else {
					Interval_Form_Field__for_INTERVAL_Data__Add(interval_id);
				}
			});
			
			//await new Promise((resolve, reject) => setTimeout(resolve, 50)); //delay
			

			//await new Promise((resolve, reject) => {
				//here we apply the changes in DATA__Intervals save
				DATA__Interval_Form_Fields__Values__Set(this_interval_data, interval_id);
			//});
		});

		Debug1('  2.Template.Load.INT.2. add INTERVAL-Values to Fields --End--');
		resolve('add INTERVAL-Values to Fields End');
	});

	Debug1('1.Template.Load.INT.-- async', '-', get_Function_Name(), '-', '--END--');
} //end async DATA__Intervals__Load ##########################################


//Set Saved INT Values -- here only put saved values back to form
//we give Saved_Data so we can use it for all the following
//1. V_Selected__Data__Intervals__Changes 
//2. V_RESULTS_TEMPLATES
function DATA__Interval_Form_Fields__Values__Set(Saved_Data, interval_id) {
	Debug1('  2.Template.Load.INT.', '-', get_Function_Name(), '-', [...arguments]);

	const form_int_id = 'FI' + interval_id;
	
	//Data fields
	const data_graph_name 	= $("#"+form_int_id+" input[name='data_graph_name[]']");
	const data_select_val 	= $("#"+form_int_id+" input[name='data_select_val[]']");
	//const data_athlete_id = $("#"+form_int_id+" input[name='data_athlete_id[]']");
	//const data_base_form_id = $("#"+form_int_id+" input[name='data_base_form_id[]']");
	//const data_form_id 	= $("#"+form_int_id+" input[name='data_form_id[]']");
	//const data_form_name 	= $("#"+form_int_id+" input[name='data_form_name[]']");
	const data_field_name 	= $("#"+form_int_id+" input[name='data_field_name[]']");
	//const data_field_type = $("#"+form_int_id+" input[name='data_field_type[]']");
	//const data_field_num 	= $("#"+form_int_id+" input[name='data_field_num[]']");
	//const data_cell_id 	= $("#"+form_int_id+" input[name='data_cell_id[]']");
	//const data_or_calc = $("#"+form_int_id+" input[name='data_or_calc[]']");
	const data_diagram_show = $("#"+form_int_id+" input[name='data_diagram_show[]']");
	const data_type 		= $("#"+form_int_id+" select[name='data_type_sel[]']");
	const data_line 		= $("#"+form_int_id+" select[name='data_line_sel[]']");
	const data_p_range		= $("#"+form_int_id+" select[name='data_p_range_sel[]']");
	const data_color 		= $("#"+form_int_id+" input[name='data_color[]']");
	const data_markers		= $("#"+form_int_id+" select[name='data_markers_sel[]']");
	const data_labels 		= $("#"+form_int_id+" select[name='data_labels_sel[]']");
	const data_axis 		= $("#"+form_int_id+" select[name='data_axis_sel[]']");
	//int extra
	const data_int_id 		= $("#"+form_int_id+" input[name='data_int_id[]']");
	const data_field_id 	= $("#"+form_int_id+" input[name='data_field_id[]']");
	const data_diagram_ath_name_show = $("#" + form_int_id + " input[name='data_diagram_ath_name_show[]']");
	
	const data_is_interval_form 	= $("#"+form_int_id+" input[name='is_interval_form[]']");
	const data_interval_form 		= $("#"+form_int_id+" [name='interval_form[]']");
	const data_formula_individual 	= $("#"+form_int_id+" input[name='formula_individual[]']");
	const data_formula_input 		= $("#"+form_int_id+" textarea[name='formula_input[]']");

	const data_formula_sub_period 		= $("#"+form_int_id+" input[name='formula_sub_period_INT[]']");
	const data_formula_sub_period_type 	= $("#"+form_int_id+" select[name='formula_sub_period_Type[]']");
	const data_formula_sub_period_start = $("#"+form_int_id+" select[name='formula_sub_period_start[]']");
	const data_formula_sub_period_end 	= $("#"+form_int_id+" select[name='formula_sub_period_end[]']");
		
	
	//info -- Saved_Data = V_RESULTS_TEMPLATES[save_id].Data_Intervals[interval_id].data;

	let i = 0;
	for (let field_id in Saved_Data) { //ES3
		if (Object.prototype.hasOwnProperty.call(Saved_Data, field_id)) {
			const Field_Data = Saved_Data[field_id];

			//if sel_val and field_id then we have the right field
			if ($(data_select_val[i]).val() == Field_Data.sel_val &&
				$(data_field_id[i]).val() == Field_Data.field_id)
			{
				$(data_graph_name[i]).val(Field_Data.name).trigger('change');

				//change if different
				if (Field_Data.show != $(data_diagram_show[i]).val()) {
					$(data_diagram_show[i]).prev().trigger("click");
				}
				//change if different
				if (Field_Data.show_name != $(data_diagram_ath_name_show[i]).val()) {
					$(data_diagram_ath_name_show[i]).prev().trigger("click");
				}

				$(data_type[i]).val(Field_Data.type).trigger('change');
				$(data_line[i]).val(Field_Data.line);
				$(data_p_range[i]).val(Field_Data.p_range);

				if (Field_Data.color != '') {
					//$(data_color[i]).val(Field_Data.color);
					$(data_color[i]).colorpicker('setValue', Field_Data.color);
				}

				$(data_markers[i]).val(Field_Data.markers);
				$(data_labels[i]).val(Field_Data.labels);
				$(data_axis[i]).val(Field_Data.axis);
				
				//change if different
				if (Field_Data.formula_sub_period != $(data_formula_sub_period[i]).val()) {
					$(data_formula_sub_period[i]).prev().trigger("click");
					//$(data_formula_sub_period_type[i]).val(Field_Data.formula_sub_period_type); //no need
					$(data_formula_sub_period_start[i]).val(Field_Data.formula_sub_period_start);
					$(data_formula_sub_period_end[i]).val(Field_Data.formula_sub_period_end);
				}
				
				//let calc_id = replaceAll(saved_sel_val, '|', '_');
				let calc_id = interval_id + '_' + Field_Data.field_id;
				if (Field_Data.is_interval_form == 'true' && Field_Data.formula_individual == '1') {
					$(data_formula_individual[i]).prev().trigger("click");
					//$('#formula_individual_ck_'+calc_id).prop('checked', true);
					//$('#formula_individual_'+calc_id).val('1');
				}
				else {
					$(data_interval_form[i]).val(Field_Data.interval_form).trigger('change');
					//if form not found
					if (!$(data_interval_form[i]).val()) { 
						$('#fs-INTERVAL-FORM-FIELD_' + calc_id + ' legend span.collapsible-indicator').after(
							'<span class="Error_Form_Not_Found">'+
								' &nbsp; '+ LANG.RESULTS.INTERVAL_FORM +' '+ //form
								'<b>"'+ (Field_Data.interval_form_name||'') +'"</b> '+ //form name
								LANG.RESULTS.INTERVAL_FORM_NOT_FOUND+' &nbsp; '+ //not found
							'</span>'
						);
					}
				}
				$('#formula_input_' + calc_id).val(Field_Data.formula_input);
				$("#formula_refresh_" + calc_id).trigger("click");
			}
			i++;
		}
	} //for each field
} //end DATA__Interval_Form_Fields__Values__Set


// TEMPLATES #########################################
//####################################################

