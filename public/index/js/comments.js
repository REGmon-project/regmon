"use strict";

// Comments functions

//initialize Comments
function init_Comments(id) {

	function on_isAllDay_check() {
		if ($("#isAllDay").is(':checked')) {
			$('#comment_date_end').prop('disabled','');
			$('#comment_time_div').hide();
		}
		else {
			$('#comment_date_end').val( $('#comment_date_start').val() );
			$('#comment_date_end').prop('disabled','disabled');
			$('#comment_time_div').show();
		}
	}

	function on_showInGraph_check() {
		if ($("#showInGraph").is(':checked')) {
			$('#comment_color_div').show();
		}
		else {
			$('#comment_color_div').hide();
		}
	}

	//console.log('init_Comments', id);
	$('label.error:visible').remove();
	
	$('#comment_error').hide();
	
	//checkbox isAllDay
	$("#isAllDay").on('change', function() {
		on_isAllDay_check();
	});

	on_isAllDay_check();
	
	
	//checkbox showInGraph
	$("#showInGraph").on('change', function() {
		on_showInGraph_check();
	});

	on_showInGraph_check();
	

	color_field_dash('.cpC');
	$('#comment_color').trigger('change'); //to enable the background

	item_Comment_Date_init('#datetimepicker_comment_start', 'left');
	item_Comment_Date_init('#datetimepicker_comment_end', 'right');

	item_Comment_Date_From_To_Calc_init('#comment_date_start', '#comment_date_end');

	item_Comment_Time_init('#clockpicker_comment_time_start');
	item_Comment_Time_init('#clockpicker_comment_time_end');

	item_Comment_Time_From_To_Calc_init('#comment_time_start', '#comment_time_end');

	$("#comment_time_now").off('click').on('click',function() { //button Now
		$('#comment_time_start').val( moment().format("HH:mm") ).trigger('change'); 
	});


	//button Save Comment
	$("button#comment_save").off('click').on('click', function () {
		
		$('form#create_comment').validate();

		const inputs = $('form#create_comment').find(':input');
		if (!inputs.valid() && $('label.error:visible').length != 0) {
			//console.log(inputs, inputs.valid(), $('label.error:visible').length);
		}
		else {
			const t_isAllDay = $("#isAllDay").is(':checked');
			const t_date_start = get_date_SQL($('#comment_date_start').val());
			const t_date_end = get_date_SQL($('#comment_date_end').val());
			const t_time_start = $('#comment_time_start').val();
			const t_time_end = $('#comment_time_end').val();
			const t_title = $('#comment_title').val();
			const t_comment = $('#comment_text').val();
			const t_showInGraph = $("#showInGraph").is(':checked');
			const t_color = $('#comment_color').val();

			const data = {
				group_id: V_GROUP,
				athlete_id: V_ATHLETE,
				t_isAllDay: t_isAllDay,
				t_date_start: t_date_start,
				t_date_end: t_date_end,
				t_time_start: t_time_start,
				t_time_end: t_time_end,
				t_title: t_title,
				t_comment: t_comment,
				t_showInGraph: t_showInGraph,
				t_color: t_color
			};

			if (id) {
				data['ID'] = id;
			}
			$.post('index/ajax.comment_save.php', data, function(data, result){
				if (data == 'ERROR-MAX3') {
					$('#comment_error').show();
				} else {
					//close and reload calendar
					$('#comment_error').hide();

					$.fancybox.close();

					$('.popover').popover('hide'); //hide all popovers
					$('#calendar').fullCalendar('refetchEvents');
					$('#calendar').removeClass('no_calendar_data');
				}
			});
		}
	});
} //init_Comments()


//initialize Comments Create
function init_Comments_Create(from) {
	/*from (
		Cal_Button false
		Dash_Button false
		Menu_Button false
		Cal_agendaDay true
		Cal_agendaWeek true
	)*/
	//console.log('init_Comments_Create', from);

	let date = $.cookie('SELECTED_DATE') || ''; //2019-10-02 or 2019-10-02T06%3A30%3A00
	let tt1 = moment().format("HH:mm"); //now
	let in_cal = false;

	if (date.indexOf('T') != -1) {
		in_cal = true;
		tt1 = date.split('T')[1].substring(0,5);
		date = date.split('T')[0];
	}

	let time_start = tt1;
	let time_end = moment(tt1, "HH:mm").add(1, 'hour').format("HH:mm");

	if (from == 'Cal_Button') { //current date
		date = moment().format("YYYY-MM-DD");
		time_start = moment().format("HH:mm"); //now
		time_end = moment().add(1, 'hour').format("HH:mm");
		//allday true
		$("#isAllDay").prop('checked', true).trigger('change');
	}
	else if (from == 'Dash_Button') { //current date-time
		date = moment().format("YYYY-MM-DD");
		time_start = moment().format("HH:mm"); //now
		time_end = moment().add(1, 'hour').format("HH:mm");
		//allday false
		$("#isAllDay").prop('checked', false).trigger('change');
	}
	else if (from == 'Menu_Button') { //click date or date-time
		if (in_cal) {
			//allday false for week-day click
			$("#isAllDay").prop('checked', false).trigger('change');
		} else {
			//allday true for month click
			$("#isAllDay").prop('checked', true).trigger('change');
		}
	}
	else if (from == 'Cal_agendaDay' || from == 'Cal_agendaWeek') { //click cal-allday bat --date 
		//allday false for week-day click
		if (in_cal) {
			$("#isAllDay").prop('checked', false).trigger('change');
		} else {
			//allday true for month click
			$("#isAllDay").prop('checked', true).trigger('change');
		}
	}
	else {
		if (in_cal) {
			$("#isAllDay").prop('checked', false).trigger('change');
		} else {
			$("#isAllDay").prop('checked', true).trigger('change');
		}
	}

	//console.log('init_Comments_Create', from, $.cookie('SELECTED_DATE'), date, time_start, time_end);
	$('#comment_date_start').val(get_date(date));
	$('#comment_date_end').val(get_date(date));
	$('#comment_time_start').val(time_start);
	$('#comment_time_end').val(time_end);
	$('#comment_title').val('');
	$('#comment_text').val('');
	$('#comment_color').val('rgba(238,238,238,0.5)');
	$("#showInGraph").prop('checked', '');

	init_Comments(false);
} //init_Comments_Create()


//initialize Comments Edit
function init_Comments_Edit(id, isAllDay, showInGraph, date_start, date_end, title, text, color) {
	//console.log('init_Comments_Edit', id, isAllDay, showInGraph, date_start, date_end, title, text, color);
	let dt1 = date_start.split(' ');
	let dt2 = date_end.split(' ');
	let date__start = dt1[0];
	let date__end = dt2[0];
	let time__start = dt1[1].substring(0,5);
	let time__end = dt2[1].substring(0,5);
	if (isAllDay) {
		$("#isAllDay").prop('checked', 'checked').trigger('change');
		dt2 = moment(date_end, "YYYY-MM-DD HH:mm:ss").subtract(1, 'second').format("YYYY-MM-DD HH:mm").split(' ');
		date__end = dt2[0];
		time__end = dt2[1];
	}
	else $("#isAllDay").prop('checked', '').trigger('change');
	
	$('#comment_date_start').val(get_date(date__start));
	$('#comment_date_end').val(get_date(date__end));
	$('#comment_time_start').val(time__start);
	$('#comment_time_end').val(time__end);
	$('#comment_title').val(title);
	$('#comment_text').val(text);
	$('#comment_color').val(color);
	
	init_Comments(id);
	
	//we need ot after init_Comments bcz there it reset the check
	if (showInGraph) {
		$("#showInGraph").prop('checked', 'checked').trigger('change');
	} else {
		$("#showInGraph").prop('checked', '').trigger('change');
	}
} //init_Comments_Edit()


//#####################################################

//initialize Comment Date
function item_Comment_Date_init(element, pos) {
	$(element).datetimepicker({ //'#datetimepicker'
		locale: LANG.LANG_CURRENT,
		format: LANG.MOMENT.DATE,
		showTodayButton: true,
		showClose: true,
		allowInputToggle: true,
		//widgetPositioning:{horizontal: 'auto', vertical: 'auto'},
		widgetPositioning: {horizontal: pos, vertical: 'bottom'},
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

	$(element).data("DateTimePicker").widgetPositioning({horizontal: pos, vertical: 'bottom'}); //don't know why needed again here to work
}


//initialize Comment Time
function item_Comment_Time_init(element) {
	$(element).clockpicker({
		donetext: LANG.OK
	});
}


//initialize Date From -> To = auto calculate
function item_Comment_Date_From_To_Calc_init(el_From, el_To) {
	//'#t_time_from'
	$(el_From).off('change').on('change', function () {
		const start = $(this).val();
		const end =  $(el_To).val();
		if (end == '' || get_date_obj(start) > get_date_obj(end)) {
			$(el_To).val(start);
		}
		if (!$("#isAllDay").is(':checked')) {
			$(el_To).val(start);
		}
	});

	//'#t_time_to'
	$(el_To).off('change').on('change', function () {
		const start =  $(el_From).val();
		const end = $(this).val();
		if (start == '' || get_date_obj(start) > get_date_obj(end)) {
			$(el_From).val(end);
		}
	});
}


//initialize Time From -> To = auto calculate
function item_Comment_Time_From_To_Calc_init(el_From, el_To) {
	//'#t_time_from'
	$(el_From).on('change', function () {
		const start = $(this).val();
		let end =  $(el_To).val();
		if (end == '' || start >= end) {
			end = moment(start, 'HH:mm').add(1, 'hours').format("HH:mm");
			$(el_To).val(end != 'Invalid date' ? end : '');
		}
	});
	
	//'#t_time_to'
	$(el_To).on('change', function () {
		let start =  $(el_From).val();
		const end = $(this).val();
		if (start == '' || start >= end) {
			start = moment(end, 'HH:mm').subtract(1, 'hours').format("HH:mm");
			$(el_From).val(start!='Invalid date'?start:'');
		}
	});
}

