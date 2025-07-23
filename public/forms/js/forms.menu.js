"use strict";

//load Forms Menu 
function load_Box_Forms_Menu() {
	if (hasAccess()) {
		const post_data = {
			group_id: V_GROUP,
			athlete_id: V_ATHLETE,
			box: true
		};
		$("#A_Box_Forms_Menu").load("forms/ajax.forms_menu.php", post_data);
	}
}


//init Form Selection Menu --for Group_Admin and Athlete
function init_Forms_Selection_Menu(elem, type) {
	//append -demo checkboxes -open/close button -save buttons
	const demo_checkbox = ''+
		LANG.FORMS.AVAILABLE+': <span class="icheckbox_flat-yellow2s checked"></span>'+
		' &nbsp; &nbsp; '+
		LANG.FORMS.STANDARD+': <span class="icheckbox_polaris2 checked"></span>'+
		'<span style="position:absolute; left:16px;" title="'+LANG.FORMS.OPEN_CLOSE_ALL+'">'+
			'<i class="fa fa-plus-square-o open_all"></i>'+
			'<i class="fa fa-minus-square-o close_all"></i>'+
		'</span><br>';
		
	const save1 = '<button type="button" class="save" style="margin:5px;">'+LANG.BUTTON_SAVE+' &nbsp; </button>';
	const save2 = '<button type="button" class="save" style="margin:5px;">'+LANG.BUTTON_SAVE+' &nbsp; </button>';
	
	$(elem).before(demo_checkbox);
	$(elem).before(save1);
	$(elem).after(save2);
	
	//Group_Forms_Select
	if ($(elem).attr('id')=='A_Group_Forms_Select') {
		//search box for admins
		const search_input = ''+
			'<div class="forms_search form-group has-feedback" style="margin:0 5px 8px;">' +
				'<span class="glyphicon glyphicon-search form-control-feedback" style="left:0; color:#ccc;"></span>'+
				'<input type="text" class="form-control" placeholder="'+LANG.SEARCH+'" style="padding-left:35px;">'+
				'<span class="form-control-clear glyphicon glyphicon-remove form-control-feedback" style="z-index:10; pointer-events:auto; cursor:pointer;"></span>'+
			'</div>';
		$(elem).before(search_input);
		

		const categories = $('#C_Group_Forms_Select .cat_name');
		const forms = $('#C_Group_Forms_Select .form_name');


		$('#C_Group_Forms_Select .forms_search input').on('input keyup change', function() {
			const search = $(this).val().toLowerCase();
			const visible = Boolean(search);

			//show clear button if we have search
			$(this).siblings('.form-control-clear').toggleClass('hidden', !visible);
			
			if (search == "") { //show all
				forms.each(function() {
					$(this).parents('li,tr').show();
				});
			}
			else {
				//show/hide forms based on search
				forms.each(function() {
					const el = $(this);
					const title = el.attr('title').toLowerCase();
					if (title.indexOf(search) > -1) {
						el.parents('li,tr').show(); // show all li parents up the ancestor tree
					} else {
						el.parents('tr').hide(); // hide current tr as it doesn't match
					}
				});
				//hide categories if empty
				categories.each(function() {
					const el = $(this);
					if (el.parent().find('.form_name:visible').length < 1) {
						el.parent().hide();
					}
				});
			};
		});

		//clear search button
		$('#C_Group_Forms_Select .forms_search .form-control-clear').on('click',function() {
			$(this).siblings('input[type="text"]').val('').trigger('change').trigger('focus');
		});		
	}

	//Athlete_Forms_Select_Menu
	else if ($(elem).attr('id') == 'A_Athlete_Forms_Select_Menu') {
		//form options --form template select
		$('.form_options').off('click').popover({
			placement: 'bottom',
			html: true,
			content: function () {
				const tmp = $(this).attr('data-ath_group_cat_form').split('|');
				const ath = tmp[0];
				const group = tmp[1];
				const cat = tmp[2];
				const form = tmp[3];
				const id = group + '_' + ath + '_' + cat + '_' + form;
				
				const post_data = {
					group_id: group,
					ath_id: ath,
					cat_id: cat,
					form_id: form
				};
				$.post('forms/ajax.form_default_template_select.php', post_data, function (data, result) {
					const save_bt = '<hr><button type="button" id="save_default_template_' + id + '" class="save" style="margin:5px;">' + LANG.BUTTON_SAVE + ' &nbsp; </button>';
					$('#' + id).html(data + save_bt);

					$('#save_default_template_' + id).off('click').on('click', function () {
						//const temp_id = $('#select_template_'+id+' option:selected').val(); //not work
						const temp_id = $(this).parent().find('.select_template').val()
						const post_data2 = {
							group_id: group,
							ath_id: ath,
							cat_id: cat,
							form_id: form,
							template_id: temp_id
						};
						$.post('forms/ajax.form_default_template_save.php', post_data2, function (data, result) {
						});
					});
				});
				
				return '<span id="' + id + '" class="popover_nowrap" style="text-align:center;"></span>';
			}
		});
	}
	
	//init checkboxes
	$(elem).find('input.check_box').iCheck({checkboxClass: 'icheckbox_flat-yellow2s'});
	$(elem).find('input.standard').iCheck({checkboxClass: 'icheckbox_polaris2'});
	

	//open/close button functionality
	$(elem).parent().find('.open_all').toggle();
	$(elem).parent().find('.open_all, .close_all').off('click').on('click',function() {
		const is_open = !$(elem).parent().find('.open_all').is(':hidden');
		$(elem).parent().find('.open_all').toggle();
		$(elem).find('.collapse').each(function(i, el) {
				 if (is_open && !$(el).hasClass('in')) $(el).prev().trigger("click"); //close
			else if (!is_open && $(el).hasClass('in')) $(el).prev().trigger("click"); //open
		});
	});
	

	//button Save Forms Selection
	$(elem).parent().find('.save').off('click').on('click',function() {
		let post_data = {
			group_id: V_GROUP,
			forms_select: $(elem).find('input[name^="sel_g_"]').serialize()
		};

		if ($(elem).attr('id')=='A_Group_Forms_Select') {
			//if admin add standard selects to data
			post_data.admin = true;
			post_data.forms_standard = $(elem).find('input[name^="std_g_"]').serialize();
		}

		$.post('forms/ajax.forms_selection_save.php', post_data, function ()
		{
			reload_Opened_Forms_Selection_Menus(elem);

			if ($(elem).attr('id')=='A_Group_Forms_Select') { //Admin
				Swal({
					type: 'success',
					title: LANG.FORMS.GROUP_SELECT_SAVE + V_GROUP_NAME,
					showConfirmButton: false,
					timer: 3000
				});
			}
			else { //Athlete
				Swal({
					type: 'success',
					title: LANG.FORMS.ATHLETE_SELECT_SAVE,
					showConfirmButton: false,
					timer: 3000
				});
			}
		});
	});
}


//reload Form Selection Menu --for Group_Admin and Athlete
function reload_Opened_Forms_Selection_Menus(elem) {
	load_Box_Forms_Menu(); //calendar forms menu --always reload
	
	//load current Menu if not already loaded
	
	//Athlete_Forms_Select
	const current_Menu = $(elem).parent().parent().attr('id');

	if (current_Menu != 'C_Athlete_Forms_Select' &&
		$('#C_Athlete_Forms_Select').length &&
		$('#C_Athlete_Forms_Select').text().trim() != '')
	{
		load_Forms_ATHLETE_Selection();
	}

	//Athlete_Give_Forms_Access_To_Trainers
	if (current_Menu != 'C_Athlete_Give_Forms_Access_To_Trainers' &&
		$('#C_Athlete_Give_Forms_Access_To_Trainers').length &&
		$('#C_Athlete_Give_Forms_Access_To_Trainers').text().trim() != '')
	{
		load_Athlete__Trainers_Select(-1);
	}
}


//Group_Admin Forms Selection
function load_Forms_ADMIN_Selection() {
	if (hasAccess()) {
		//clear menu div
		$("#A_Group_Forms_Select").parent().html(
			'<nav id="A_Group_Forms_Select" class="nav shadow1"></nav>'
		);

		//load Group forms menu
		const post_data = {
			group_id: V_GROUP,
			edit: true
		};
		$("#A_Group_Forms_Select").load("forms/ajax.forms_menu.php", post_data, function (data, result)
		{
			if (!(data.indexOf('empty_message') > -1)) {
				init_Forms_Selection_Menu(this, 'edit');
			}
		});
	}
}



//#####################################################
//for ATHLETES ########################################
//#####################################################

//Athlete Forms Selection
function load_Forms_ATHLETE_Selection() {
	if (hasAccess()) {
		//clear menu div
		$("#A_Athlete_Forms_Select_Menu").parent().html(
			'<nav id="A_Athlete_Forms_Select_Menu" class="nav shadow1"></nav>'
		);

		//load Athlete forms menu
		const post_data = {
			group_id: V_GROUP,
			select: true
		};
		$("#A_Athlete_Forms_Select_Menu").load("forms/ajax.forms_menu.php", post_data, function(data, result) {
			if (!(data.indexOf('empty_message') > -1)) {
				init_Forms_Selection_Menu(this, 'select');
			}
		});
	}
}


//for Athlete 2 Trainer Forms Selection
//first load Athlete -> Trainers Select Dropdown
function load_Athlete__Trainers_Select(trainer_id) {
	if (hasAccess()) {
		//clear div
		$("#A_Athlete_Give_Forms_Access_To_Trainers").parent().html(
			'<div id="TRN_select_div" style="padding:10px"></div>' +
			'<nav id="A_Athlete_Give_Forms_Access_To_Trainers" class="nav shadow1"></nav>'
		);
		
		//Trainers Select Dropdown
		const post_data = {
			group_id: V_GROUP,
			trainer_id: trainer_id
		};
		$("#TRN_select_div").load("index/ajax.athlete_trainers_select.php", post_data, function(data, result) {
			if (!(data.indexOf('empty_message') > -1))
			{
				init_Athlete__Trainers_Select();
				
				if (trainer_id != '-1') { //if a trainer selected
					load_Forms_Athlete_2_Trainer_Selection(trainer_id);
				}
			}
		});
	}
}


//init Athlete -> Trainers Select
function init_Athlete__Trainers_Select() {

	//Athlete Trainers Select
	$("#TRN_select").chosen({
		placeholder_text_single: LANG.SELECT_TRAINER,
		no_results_text: LANG.NO_RESULTS,
		search_contains: true,
		disable_search_threshold: 10
	});

	//Athlete Trainers Select Change - load active Trainer FormsSelect
	$("#TRN_select").on('change', function () {
		load_Forms_Athlete_2_Trainer_Selection($(this).val());
	});
}

//###################################### 
//load Athlete 2 Trainer Forms Selection 
function load_Forms_Athlete_2_Trainer_Selection(trainer_id) {
	//load Trainer forms menu
	const post_data = {
		group_id: V_GROUP,
		trainer: true,
		trainer_id: trainer_id
	};
	$("#A_Athlete_Give_Forms_Access_To_Trainers").load("forms/ajax.forms_menu.php", post_data, function(data, result) {
		if (!(data.indexOf('empty_message') > -1)) {
			if ($("#C_Athlete_Give_Forms_Access_To_Trainers .save").length) { //reload
				init_Forms_Athlete_2_Trainer_Selection__Checkbox_N_Toggle();
			} else { //first time
				init_Forms_Athlete_2_Trainer_Selection();
			}
			$("body").animate({
				scrollTop: $('#C_Athlete_Give_Forms_Access_To_Trainers').offset().top
			}, "slow");
		}
	});
}


//init Athlete 2 Trainer Forms Selection - first time
function init_Forms_Athlete_2_Trainer_Selection() {
	const elem_sel = $('#TRN_select_div');
	const elem = $("#A_Athlete_Give_Forms_Access_To_Trainers");
	const parent_elem = $("#C_Athlete_Give_Forms_Access_To_Trainers");

	elem_sel.before(
		LANG.FORMS.READ + ': <span class="icheckbox_flat-yellow2s checked"></span> &nbsp; &nbsp; ' +
		LANG.FORMS.WRITE+': <span class="icheckbox_flat-green2s checked"></span>'+
		'<span style="position:absolute; left:16px;" title="' + LANG.FORMS.OPEN_CLOSE_ALL + '">'+
			'<i class="fa fa-plus-square-o open_all"></i>'+
			'<i class="fa fa-minus-square-o close_all"></i>'+
		'</span><br>'
	);
	elem_sel.before(
		'<button type="button" class="save" style="margin:5px;">' + LANG.BUTTON_SAVE + ' &nbsp; </button>'
	);
	elem.after(
		'<button type="button" class="save" style="margin:5px;">' + LANG.BUTTON_SAVE + ' &nbsp; </button>'
	);
	
	init_Forms_Athlete_2_Trainer_Selection__Checkbox_N_Toggle();

	//button Save Forms Trainer
	parent_elem.find('.save').off('click').on('click',function() {
		const disabled = elem.find('input[name^="sel_g_"]:input:disabled').removeAttr('disabled');
		$("#TRN_select option:selected").text().trim().replace('   ', '')
		const forms_select = elem.find('input[name^="sel_g_"]').serialize();
		disabled.attr('disabled','disabled');
		const forms_standard = elem.find('input[name^="std_g_"]').serialize();

		const trainer_id = $("#TRN_select").val();
		const trainer_name = $("#TRN_select option:selected").text().trim().replace(' ', '').replace(' ', '');
		
		const post_data = {
			group_id: V_GROUP,
			trainer: true,
			trainer_id: trainer_id,
			forms_select: forms_select,
			forms_standard: forms_standard
		};
		$.post('forms/ajax.forms_selection_save.php', post_data, function(){
			Swal({
				type: 'success',
				title: LANG.FORMS.ATHLETE_2_TRAINER_SELECT_SAVE + trainer_name,
				showConfirmButton: false,
				width: 'auto',
				timer: 3000
			});
		});
	});
}


//init Athlete 2 Trainer Forms Selection - reload
function init_Forms_Athlete_2_Trainer_Selection__Checkbox_N_Toggle() {
	const parent_elem = $('#C_Athlete_Give_Forms_Access_To_Trainers');
	const elem = $('#A_Athlete_Give_Forms_Access_To_Trainers');

	elem.find('input.check_box').iCheck({checkboxClass: 'icheckbox_flat-yellow2s'});
	elem.find('input.standard').iCheck({checkboxClass: 'icheckbox_flat-green2s'});
	
	parent_elem.find('input.standard').on('ifChecked', function () {
		$(this).parent().prev().iCheck('check').iCheck('disable');
	});

	parent_elem.find('input.standard').on('ifUnchecked', function () {
		$(this).parent().prev().iCheck('enable');
	});

	parent_elem.find('input.standard').each(function () {
		if ($(this).iCheck('update')[0].checked) {
			$(this).parent().prev().iCheck('check').iCheck('disable');
		}
	});
	
	parent_elem.find('.open_all').toggle();

	parent_elem.find('.open_all, .close_all').off('click').on('click',function() {
		const is_open = !parent_elem.find('.open_all').is(':hidden');
		parent_elem.find('.open_all').toggle();
		elem.find('.collapse').each(function(i, el) {
				 if (is_open && !$(el).hasClass('in')) $(el).prev().trigger("click"); //close
			else if (!is_open && $(el).hasClass('in')) $(el).prev().trigger("click"); //open
		});
	});
}



//#####################################################
//for TRAINERS ########################################
//#####################################################

//for Athlete_2_Trainer Forms Show
//first load Trainer -> Athletes Select Dropdown
function load_Trainer__Athletes_Select(athlete_id) {
	if (hasAccess()) {
		//clear div
		$("#A_Trainer_Access_To_Athletes_Forms").parent().html(
			'<div id="Select_Trainer_Athletes_div" style="padding:10px"></div>' +
			'<nav id="A_Trainer_Access_To_Athletes_Forms" class="nav shadow1"></nav>'
		);

		//Athletes Select Dropdown
		const post_data = {
			group_id: V_GROUP,
			athlete_id: athlete_id
		};
		$("#Select_Trainer_Athletes_div").load("index/ajax.trainer_athletes_select.php", post_data, function(data, result) {
			if (!(data.indexOf('empty_message') > -1)) {

				init_Trainer__Athletes_Select();

				if (athlete_id != '-1') { //if an athlete selected
					load_Forms_Athlete_2_Trainer_Show(athlete_id);
				}
			}
		});
	}
}


//init Trainer -> Athletes Select
function init_Trainer__Athletes_Select() {
	//Trainer Athletes Select
	$("#Select_Trainer_Athletes").chosen({
		placeholder_text_single: LANG.SELECT_ATHLETE,
		no_results_text: LANG.NO_RESULTS,
		search_contains: true,
		disable_search_threshold: 10
	});

	//Trainer Athletes Select Change - load active Trainer FormsSelect
	$("#Select_Trainer_Athletes").on('change', function () {
		load_Forms_Athlete_2_Trainer_Show($(this).val());
	});
}


//###################################### 
//load Athlete 2 Trainer Forms Show 
function load_Forms_Athlete_2_Trainer_Show(athlete_id) {
	//load Forms Trainer Selection
	const post_data = {
		group_id: V_GROUP,
		athlete_id: athlete_id
	};
	$("#A_Trainer_Access_To_Athletes_Forms").load("forms/ajax.forms_menu.php", post_data, function(data, result) {
		if (!(data.indexOf('empty_message') > -1)) {
			if ($('#C_Trainer_Access_To_Athletes_Forms .chk_show').length) { //reload
				init_Forms_Athlete_2_Trainer_Show__Checkbox_N_Toggle();
			} else { //first time
				init_Forms_Athlete_2_Trainer_Show();
			}
			$("body").animate({
				scrollTop: $('#C_Trainer_Access_To_Athletes_Forms').offset().top
			}, "slow");
		}
	});
}


//init Trainer Forms Show - first time
function init_Forms_Athlete_2_Trainer_Show() {
	const elem_sel = $('#Select_Trainer_Athletes_div');
	const parent_elem = $("#C_Trainer_Access_To_Athletes_Forms");

	elem_sel.before(
		LANG.FORMS.READ + ': <span class="icheckbox_flat-yellow2s checked chk_show"></span>'+
		' &nbsp; &nbsp; ' +
		LANG.FORMS.WRITE+': <span class="icheckbox_flat-green2s checked"></span>'+
		'<span style="position:absolute; left:16px;" title="' + LANG.FORMS.OPEN_CLOSE_ALL + '">'+
			'<i class="fa fa-plus-square-o open_all"></i>'+
			'<i class="fa fa-minus-square-o close_all"></i>'+
		'</span>'+
		'<br>'
	);
	
	parent_elem.find('.open_all').toggle();

	init_Forms_Athlete_2_Trainer_Show__Checkbox_N_Toggle();
}


//init Trainer Forms Show - reload
function init_Forms_Athlete_2_Trainer_Show__Checkbox_N_Toggle() {
	const elem = $("#A_Trainer_Access_To_Athletes_Forms");
	const parent_elem = $("#C_Trainer_Access_To_Athletes_Forms");

	parent_elem.find('.open_all, .close_all').off('click').on('click',function() {
		const is_open = !parent_elem.find('.open_all').is(':hidden');
		parent_elem.find('.open_all').toggle();
		elem.find('.collapse').each(function(i, el) {
				 if (is_open && !$(el).hasClass('in')) $(el).prev().trigger("click"); //close
			else if (!is_open && $(el).hasClass('in')) $(el).prev().trigger("click"); //open
		});
	});
}


//############################################################


//used from index.js to Open Athlete_2_Trainer Forms Selection - Athlete
function Edit_Athlete_2_Trainer_Forms__Load_Open(trainer_id) {

	load_Athlete__Trainers_Select(trainer_id);

	//close any right open panel and open the trainers one
	$('#accordion2 .collapse.in').collapse('hide');

	$('#C_Athlete_Give_Forms_Access_To_Trainers').collapse('show');

	setTimeout(function(){
		$('#C_Athlete_Give_Forms_Access_To_Trainers').collapse('show');
	}, 500);
}


//used from index.js to Open Athlete_2_Trainer Forms Show - Trainer
function Show_Athlete_2_Trainer_Forms__Load_Open(athlete_id) {

	load_Trainer__Athletes_Select(athlete_id);

	//close any right open panel and open the trainers one
	$('#accordion2 .collapse.in').collapse('hide');

	$('#C_Trainer_Access_To_Athletes_Forms').collapse('show');

	setTimeout(function(){
		$('#C_Trainer_Access_To_Athletes_Forms').collapse('show');
	}, 500);
}
