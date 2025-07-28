"use strict";

var V_CONTINUE_LOADING = false,
	item_Time_init,
	item_Time_Period_init,
	Main_Time_From_To_init,
	item_Time_From_To_Period_Calc_init,
	item_Date_init,
	HTML_Editor;

var fancyBoxDefaults_iframe = {
	padding:0, //def:15
	margin:10, //def:20
	modal:true,
	live:false,
	tpl:{error:'<p class="fancybox-error">'+LANG.PROBLEM_LOADING_PAGE+'</p>'},
	maxWidth:'100%',
	width:'100%',
	//fitToView : false,
	autoSize:false, height:'100%', //full height
	beforeLoad:function() {	loading.show();	},
	afterLoad:function() { loading.hide(); },
	afterClose:function() {
		//location.reload();
		return;
	},
	afterShow:function() {
		const close = 'javascript:jQuery.fancybox.close();';
		//2 close buttons
		$('.fancybox-skin').append('<a title="'+LANG.CLOSE+'" class="fancybox-item fancybox-back" href="'+close+'"></a>');
		$('.fancybox-skin').append('<a title="'+LANG.CLOSE+'" class="fancybox-item fancybox-close" href="'+close+'"></a>');
	}
};

function stop_beforeunload_IE(){
	$(window).off('beforeunload'); 
}



//jQuery(function($) {
jQuery(function()
{


function Check_Tables_Width() {
	$('.box2_scroll').each(function() {
		$(this).removeClass('box2_scroll');
	});
	
	const box1_ww = $('.box1').width();
	$('.box2').each(function() {
		const box2_ww = $(this).width();
		if (box1_ww < box2_ww) {
			$(this).addClass('box2_scroll');
		}
		//else $(this).removeClass('box2_scroll');
		//console.log(this, box1_ww, box2_ww);
	});
}
Check_Tables_Width();

	
$(window).on('resize', function() { 
	Check_Tables_Width();
}).trigger('resize');


// SCROLL TO TOP
function animateToTop(top) {
	if (top != 0) {
		//top = $('#middle-wizard').position().top;
		top = 0;
		if (window.innerWidth >= 340) top = 80;
		if (window.innerWidth >= 520) top = 90;
		if (window.innerWidth >= 770) top = 100;
	}
	$("html, body").animate({ scrollTop: top }, "slow");
}

	
$('#toTop').on('click',function() {
	animateToTop(0);
});	

	
$(window).on('scroll',function() {
	if($(this).scrollTop() != 0) {
		$('#toTop').fadeIn();	
	} else {
		$('#toTop').fadeOut();
	}
	$('#progress_cont').css('top', window.pageYOffset + 'px');
});


//Warning when leaving page
$(window).on('beforeunload', function(){
	if (!V_PREVIEW && !V_VIEW) {
		//Do you really want to leave the page? \n All changes will be lost
		return "\n\n"+LANG.LEAVE_PAGE_WARNING+"\n\n"; 
	}
});

	
	
//tooltip
$("body").tooltip({ //for new created elements too
	selector: '[data-toggle="tooltip"]'
});


// Validation Rules ###################################################
//$.validator.setDefaults({ ignore: [] }); //not ignore hidden inputs  --def=':hidden'
$.validator.methods.range = function (value, element, param) {
	const globalizedValue = value.replace(",", ".");
	$(element).val(globalizedValue); //change input value too
	return this.optional(element) || (globalizedValue >= param[0] && globalizedValue <= param[1]);
}
$.validator.methods.min = function (value, element, param) {
	const globalizedValue = value.replace(",", ".");
	$(element).val(globalizedValue); //change input value too
	return this.optional(element) || globalizedValue >= param;
}
$.validator.methods.max = function (value, element, param) {
	const globalizedValue = value.replace(",", ".");
	$(element).val(globalizedValue); //change input value too
	return this.optional(element) || globalizedValue <= param;
}
$.validator.methods.number = function (value, element) {
	const globalizedValue = value.replace(",", ".");
	$(element).val(globalizedValue); //change input value too
	return this.optional(element) || /^(?:-?\d+|-?\d{1,3}(?:,\d{3})+)?(?:\.\d+)?$/.test(globalizedValue); //org
	//return this.optional(element) || /^-?(?:\d+|\d{1,3}(?:[\s\.,]\d{3})+)(?:[\.,]\d+)?$/.test(globalizedValue); //new with comma
}

$.validator.addMethod("time", function(value, element) {
	return this.optional(element) || /^([01]\d|2[0-3]|[0-9])(:[0-5]\d){1,2}$/.test(value);
}, LANG.FORMS.TIME_BETWEEN_ERROR); //"Please enter a valid time, between 00:00 and 23:59");

$.validator.addMethod("results_parenthesis", function(value, element) {
	return this.optional(element) || isParenthesisOK(value);
}, LANG.RESULTS.PARENTHESES_ERROR);

$.validator.addMethod("results_numbers", function(value, element) {
	if (value.indexOf(',') != -1) {
		value = value.replace(",", ".");
		$(element).val(value); //change input value too
	}
	return this.optional(element) || isResultOK(value);
}, LANG.RESULTS.ALLOWED_CHARS_ERROR);



// WIZARD ###################################################
// Basic wizard with validation
$('form#wrapped').prop('action', 'forms/ajax.form_data_save.php');
$('form#wrapped').wizard({
	stepsWrapper: "#wrapped",
	submit: ".saveSubmit", //.backward .forward //def buttons
	beforeSelect: function( event, state ) {
		//console.log('beforeSelect');
		if (!state.isMovingForward) {
			animateToTop();
			return true;
		}

		const inputs = $(this).wizard('state').step.find(':input');
		if (!inputs.valid() && $('label.error:visible').length != 0) {
			$("html, body").animate({ scrollTop: $('label.error:visible').offset().top-50 }, "slow");
		}
		else {
			animateToTop();
		}

		return !inputs.length || !!inputs.valid();
	},
	afterSelect: function( event, state ) {
		//console.log('afterSelect');
		$("#location").text("(" + state.stepsComplete + "/" + state.stepsPossible + ")");
		if (V_PREVIEW) {
			//disable submit button
			$('button[type=submit]').prop('disabled', 'disabled');
		}
		if (V_PREVIEW || V_CHANGE) {
			//hide keep_form_open check
			$('#keep_form_open_div').hide();
		}
	}
}).validate({
	ignore: [] //not ignore hidden inputs  --def=':hidden'
});


//disable submit button after click + disable warn when leaving page after submit
$('form#wrapped').on('submit', function(e){
	if (!V_EDIT) {
		const inputs = $('form#wrapped').wizard('state').step.find(':input');
		const inputs_top = $('form#wrapped #top-wizard').find(':input');

		if (!inputs.valid() && $('label.error:visible').length != 0) {
			$("html, body").animate({
				scrollTop: $('label.error:visible').offset().top - 50
			}, "slow");
			return false;
		}
		else if (!inputs_top.valid() && $('label.error:visible').length != 0) {
			$("html, body").animate({
				scrollTop: $('label.error:visible').offset().top - 50
			}, "slow");
			return false;
		}
		//for hidden items inside accordions
		else if (!inputs.valid() && $('label.error:hidden').length != 0) {
			//open panels
			$('label.error:hidden').parents('.panel').find('>.panel-heading>.panel-title>a.trigger.collapsed').trigger("click");

			$("html, body").animate({
				scrollTop: $('label.error:visible').offset().top - 50
			}, "slow");
			return false;
		}
	
		//post with ajax
		let post_data = $('form#wrapped').serialize();
		post_data += '&form_id=' + $('#form_id').val();
		post_data += '&category_id=' + $('#category_id').val();
		post_data += '&group_id=' + $('#group_id').val();
		post_data += '&athlete_id=' + $('#athlete_id').val();
		post_data += '&change=' + $('#change').val();
		post_data += '&change_id=' + $('#change_id').val();
		
		$.post('forms/ajax.form_data_save.php', post_data, function (data, result) {
			
			if ($('#keep_form_open').val() != '1') {
				$(window).off('beforeunload'); //disable unload warning
				if (is_iOS()) {
					window.location.href = '.';
				}
				else {
					if (typeof(parent.$) !== 'undefined') {
						parent.$.fancybox.close();
					}
				}
			}

			if (typeof(parent.$) !== 'undefined') {
				parent.Swal({
					type: 'success',
					title: LANG.FORMS.FORM_DATA_SAVED,
					showConfirmButton: false,
					timer: 3000
				});

				parent.$('.popover').popover('hide'); //hide all popovers

				if (parent.$('#calendar').length) {
					parent.$('#calendar').fullCalendar('refetchEvents');
					parent.$('#calendar').removeClass('no_calendar_data');
				}
			}
		});
		e.preventDefault();
		return false;
	}
});



if (V_PREVIEW) {
	//disable submit button
	$('button[type=submit]').prop('disabled', 'disabled');
}

if (V_PREVIEW || V_CHANGE) {
	//hide keep_form_open check
	$('#keep_form_open_div').hide();
}



//button Now
$("#form_time_now").on('click',function() {
	$('#form_time').val( moment().format("HH:mm") ); 
	$('#form_time_end').val( moment().add(1, 'hour').format("HH:mm") ); 
});


//Group Select
setTimeout(() => {
	$('#Form_Select_Groups').multiselect({ //http://davidstutz.github.io/bootstrap-multiselect/
		maxHeight: 300,
		buttonContainer: '<div class="btn-group" id="Form_Select_Groups_box" />',
		buttonWidth: '100%',
		enableFiltering: true,
		enableCaseInsensitiveFiltering: true,
		filterPlaceholder: LANG.SELECT_SEARCH,
		enableCollapsibleOptGroups: true,
		enableClickableOptGroups: true,
		nonSelectedText: LANG.GROUPS_SELECT_PLACEHOLDER,
		numberDisplayed: 1,
		nSelectedText: ' - ' + LANG.GROUPS_SELECTED,
		includeSelectAllOption: true,
		selectAllText: LANG.SELECT_ALL,
		allSelectedText: LANG.ALL_SELECTED,
		disableIfEmpty: true,
		disabledText: LANG.GROUPS_SELECT_NO_GROUP
	});
}, 0);
//$('#Form_Select_Groups').multiselect('rebuild');
if (V_CHANGE) {
	$('#Form_Select_Groups').multiselect('disable');
}

//Athlete Select
setTimeout(() => {
	$('#Form_Select_Athlete').multiselect({ //http://davidstutz.github.io/bootstrap-multiselect/
		maxHeight: 300,
		buttonContainer: '<div class="btn-group" id="Form_Select_Athlete_box" />',
		buttonWidth: '100%',
		enableFiltering: true,
		enableCaseInsensitiveFiltering: true,
		filterPlaceholder: LANG.SELECT_SEARCH,
		enableCollapsibleOptGroups: true,
		enableClickableOptGroups: true,
		nonSelectedText: LANG.ATHLETES_SELECT_PLACEHOLDER,
		numberDisplayed: 1,
		nSelectedText: ' - ' + LANG.ATHLETES_SELECTED,
		includeSelectAllOption: true,
		selectAllText: LANG.SELECT_ALL,
		allSelectedText: LANG.ALL_SELECTED,
		disableIfEmpty: true,
		disabledText: LANG.ATHLETES_SELECT_NO_ATHLETE
	});
}, 0);
if (V_CHANGE) {
	$('#Form_Select_Athlete').multiselect('disable');
}
$('#Form_Select_Athlete').on('change', function() {
	const ath_id = $(this).val();
	$("#athlete_id").val( ath_id );
	let options = '';
	V_Athletes_2_Groups[ath_id].forEach(function(group_id) {
		options += '<option value="'+group_id+'"'+(group_id==V_GROUP?' selected':'')+'>'+V_GroupsNames[group_id]+'</option>';
	});
	$('#Form_Select_Groups').html(options);
	$('#Form_Select_Groups').multiselect('rebuild');
});
$("#athlete_id").val( $("#Form_Select_Athlete").val() );



//#######################################################
//Progress bars #########################################
$("#progress").progressbar();
$("#time_limit").progressbar();

$('input, textarea').on('change', function(event){
	const t_name = $(this).attr('name');
	if (t_name == 'form_date' || t_name == 'form_time' || t_name == 'form_time_end') {
		return false;
	}
	if (t_name == '' || t_name == undefined) {
		return false; //Form_Select_Groups, Form_Select_Athlete checkboxes
	}
	
	on_Change_Update_ProcessBar(this);
});

	
$('select').on('change', function(event){
	on_Change_Select(this);
});
	
//init selects
$('select').each(function(i,el){
	on_Change_Select(this);
})

	
//Check and radio input styles
$('input.check_radio, input.check_radio2').iCheck({
	checkboxClass: 'icheckbox_square-aero',
	radioClass: 'iradio_square-aero'
});

//on radio check --progress update + remove error
$('input.check_radio').on('ifChecked', function(event){
	if (!V_EDIT) {
		$(this).closest('td').find('label.error').each(function(){ 
			$(this).remove(); //remove required error if checked
		});
	}
});


//on radio click --progress update
$('input.check_radio').on('ifClicked', function(event){
	if (!V_EDIT) {
		const name = $(this).attr('name');
		const radio = $('input[name="' + name + '"]');
		
		if (radio.filter(':checked').val() == $(this).val()) {
			setTimeout(function(){ 
				radio.iCheck('uncheck');

				update_ProgressBar(name, '');
			}, 1);
		}
		else {
			update_ProgressBar(name, radio.filter(':checked').val());
		}
	}
});

	
//#################################################
//ProgressBar functions ###########################
function update_ProgressBar(id, val) {
	if (val != '') {
		if (V_ANSWERED.indexOf(id) == -1) {
			V_ANSWERED.push(id);
			if (!V_CHANGE) {
				$("#progress").progressbar("value", V_ANSWERED.length * V_ANSWERS_STEP);
			}
		}
	}
	else {
		if (V_ANSWERED.indexOf(id) != -1) {
			const index = V_ANSWERED.indexOf(id);
			if (index > -1) {
				V_ANSWERED.splice(index, 1);
			}
			if (!V_CHANGE) {
				$("#progress").progressbar("value", V_ANSWERED.length * V_ANSWERS_STEP);
			}
		}
	}
	//console.log(V_ANSWERED, V_ANSWERED.length, V_ANSWERS_STEP, V_ANSWERED.length * V_ANSWERS_STEP, id, val);
}


//on change--progress update + remove error
function on_Change_Update_ProcessBar(self) {
	if (!V_EDIT) {
		$(self).parents('td').find('label.error').each(function(){ 
			$(this).remove(); //remove required error if checked
		});
		update_ProgressBar($(self).attr('name'), $(self).val());
	}
}


//on change--progress update + remove error
function on_Change_Select(self) {
	const t_name = $(self).attr('name');
	if (t_name == undefined) return false;
	else if (t_name == 'timer_period') return false;
	else if (t_name == 'Form_Select_Groups[]') return false;
	else if (t_name == 'Form_Select_Athlete') return false;

	on_Change_Update_ProcessBar(self);

	//let this_bg = $(self).find(":selected").css('background-color'); //not work in mozilla
	const this_bg = $(self).find(":selected").attr('data-color');

	if (this_bg != 'none' && this_bg != undefined) {
		$(self).attr('style', 'background:'+ this_bg + ' url(img/down_arrow_select.png) no-repeat 98% center !important');
	}
	else {
		$(self).css('background', 'none');
	}
}

	
//time_limit ############################################
var count = 0;
var counter = (V_COUNTER ? setInterval(time_limit, 1000) : false);

function time_limit() {
	count++;
	if (count > V_COUNT_ALL) {
		clearInterval(counter);

		$(window).off('beforeunload'); //disable unload warning

		if (confirm(LANG.FORMS.TIME_LIMIT_EXCEEDED)) {
			window.location.href = window.location.href; //refresh to form.php
		}
		else {
			parent.$.fancybox.close(); //fancybox close
		}

		return;
	}	
	
	const secs_left = V_COUNT_ALL - count;
	const t_time = parseInt(secs_left / 60) + ':' + ((secs_left % 60 < 10) ? '0' : '') + secs_left % 60;
	
	$('#t_time').text(t_time);

	$("#time_limit").progressbar("value", V_COUNT_STEP * count);
}



//#######################################################################
//items inputs init #####################################################
//#######################################################################

//Time
item_Time_init = function (element) {
	$(element).clockpicker({
		donetext: LANG.OK
	});
}
//Time init
item_Time_init('.clockpicker.time');


//Time Period
item_Time_Period_init = function (element) {
	$(element).clockpicker({
		donetext: LANG.OK
	});
}
//Time Period init
item_Time_Period_init('.clockpicker.period');


//Main Time From -> To
Main_Time_From_To_init = function (el_From, el_To) {
	//Time functions
	$(el_From).on('change', function () { //'#t_time_from'
		const start = $(this).val();
		let end = $(el_To).val();
		if (end < start) {
			end = moment(start, 'HH:mm').add(1, 'hours').format("HH:mm");
			$(el_To).val(end);
		}
	});
	$(el_To).on('change', function () { //'#t_time_to'
		let start =  $(el_From).val();
		const end = $(this).val();
		if (end < start) {
			start = moment(end, 'HH:mm').subtract(1, 'hours').format("HH:mm");
			$(el_From).val(start);
		}
	});
}
//Main Time From -> To
Main_Time_From_To_init('#form_time', '#form_time_end');


//Time From -> To = Period auto calculate
item_Time_From_To_Period_Calc_init = function (el_From, el_To, el_Period) {
	//Time functions
	$(el_From).on('change', function () { //'#t_time_from'
		const start = $(this).val();
		let end =  $(el_To).val();
		let diff = $(el_Period).val();

		if (end == '') {
			if (diff == '') {
				diff = '01:00';
				end = moment(start, 'HH:mm').add(1, 'hours').format("HH:mm");
			} else {
				end = moment(start, 'HH:mm').add(moment.duration(diff).asMinutes(), 'minutes').format("HH:mm");
			}
		}
		else {
			if (diff == '') {
				const st = moment(start, 'HH:mm');
				const ed = moment(end, 'HH:mm');
				const df = moment.duration(ed.diff(st)).asMinutes();

				diff = moment('00:00', 'HH:mm').add(df, 'minutes').format("HH:mm");
			} else {
				end = moment(start, 'HH:mm').add(moment.duration(diff).asMinutes(), 'minutes').format("HH:mm");
			}
		}

		$(el_Period).val(diff);
		$(el_To).val(end!='Invalid date'?end:'');
	});

	$(el_To).on('change', function () { //'#t_time_to'
		const start =  $(el_From).val();
		const end = $(this).val();
		let diff = $(el_Period).val();
		if (start != '') {
			const st = moment(start, 'HH:mm');
			const ed = moment(end, 'HH:mm');
			const df = moment.duration(ed.diff(st)).asMinutes();

			diff = moment('00:00', 'HH:mm').add(df, 'minutes').format("HH:mm");

			$(el_Period).val(diff);
		}
	});

	$(el_Period).on('change', function () { //'#t_period'
		const start =  $(el_From).val();
		let end = $(el_To).val();
		const diff = $(this).val();
		if (start != '') {
			end = moment(start, 'HH:mm').add(moment.duration(diff).asMinutes(), 'minutes').format("HH:mm");
			$(el_To).val(end);
		}
	});
}

//Time From -> To = Period auto calculate
$('.clockpicker.period').each(function(){
	const id = $(this).attr('data-id');

	item_Time_From_To_Period_Calc_init('#c_'+id+'_PRDfrom', '#c_'+id+'_PRDto', '#c_'+id+'_PRDperiod');
});

	
//Date
item_Date_init = function (element) {
	$(element).datetimepicker({ //'#datetimepicker'
		locale: LANG.LANG_CURRENT,
		format: LANG.MOMENT.DATE,
		showTodayButton: true,
		showClose: true,
		allowInputToggle: true,
		//widgetPositioning:{horizontal: 'auto', vertical: 'auto'},
		//widgetPositioning:{horizontal: 'right'},
		//debug: true, //Will cause the date picker to stay open after a blur event.
		icons: { date: "fa fa-calendar" },
		tooltips: {
			today: LANG.DATE_TODAY,
			clear: LANG.DATE_CLEAR,
			close: LANG.DATE_CLOSE,
			selectTime: LANG.DATE_SELECT_TIME,
			selectMonth: LANG.DATE_MONTH_SELECT,
			prevMonth: LANG.DATE_MONTH_PREV,
			nextMonth: LANG.DATE_MONTH_NEXT,
			selectYear: LANG.DATE_YEAR_SELECT,
			prevYear: LANG.DATE_YEAR_PREV,
			nextYear: LANG.DATE_YEAR_NEXT,
			selectDecade: LANG.DATE_DECADE_SELECT,
			prevDecade: LANG.DATE_DECADE_PREV,
			nextDecade: LANG.DATE_DECADE_NEXT,
			prevCentury: LANG.DATE_CENTURY_PREV,
			nextCentury: LANG.DATE_CENTURY_NEXT
		}
	}).on("dp.change", function (e) {
		$(this).find('input').trigger('change'); //this=div
	});
}
//Date init
item_Date_init('.date');

}); //jQuery(function() 
