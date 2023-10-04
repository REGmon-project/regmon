if (!Production_Mode) "use strict"; //remove on production so "const" works on iOS Safari 

jQuery(function() {
	
	//set global variables
	V_DATE_FROM = $('#t_date_from').val();
	V_DATE_TO = $('#t_date_to').val();

	V_DATE_FROM_moment = moment(V_DATE_FROM + ':00', 'YYYY-MM-DD HH:mm:ss');
	V_DATE_TO_moment = moment(V_DATE_TO + ':00', 'YYYY-MM-DD HH:mm:ss');

	if (V_LANG_CURRENT == 'de') {
		V_DATE_FROM_moment = moment(V_DATE_FROM + ':00', 'DD.MM.YYYY HH:mm:ss');
		V_DATE_TO_moment = moment(V_DATE_TO + ':00', 'DD.MM.YYYY HH:mm:ss');
	}


	//init collapsible
	$(".coolfieldset").collapsible();
	

	//init tooltips
	$(".help_colors, .help_lines, .help_formula").tooltip({
		html: true,
		animated: 'fade',
		placement: 'left'
	});


	//AXIS fieldset expand/collapse
	Fieldset__Expand_Collapse__Init('#fieldset_AXIS');


	//Date Picker
	// https://eonasdan.github.io/bootstrap-datetimepicker/
	$('#datetimepicker_from, #datetimepicker_to').datetimepicker({
		locale: LANG.LANG_CURRENT,
		format: LANG.MOMENT.DATE_TIME_NOSECS,
		showTodayButton: true,
		showClose: true,
		allowInputToggle: true,
		//widgetPositioning:{horizontal: 'right'},
		debug: false, //true Will cause the date picker to stay open after a blur event.
		//icons: { date: "fa fa-calendar" },
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
	});

	//datetimepicker_from on change
	$("#datetimepicker_from").on("dp.change", function (e) {
		$('#datetimepicker_to').data("DateTimePicker").minDate(e.date);
		V_DATE_FROM = $('#t_date_from').val();
		V_DATE_FROM_moment = moment(V_DATE_FROM + ':00', 'YYYY-MM-DD HH:mm:ss');
		if (V_LANG_CURRENT == 'de') {
			V_DATE_FROM_moment = moment(V_DATE_FROM + ':00', 'DD.MM.YYYY HH:mm:ss');
		}

		if (V_RESULTS_PAGE == 'FORMS_RESULTS') {
			Table__DATA__Filter_by_DATE();
		}
	});

	//datetimepicker_to on change
	$("#datetimepicker_to").on("dp.change", function (e) {
		$('#datetimepicker_from').data("DateTimePicker").maxDate(e.date);
		V_DATE_TO = $('#t_date_to').val();

		V_DATE_TO_moment = moment(V_DATE_TO + ':00', 'YYYY-MM-DD HH:mm:ss');
		if (V_LANG_CURRENT == 'de') {
			V_DATE_TO_moment = moment(V_DATE_TO + ':00', 'DD.MM.YYYY HH:mm:ss');
		}
		
		if (V_RESULTS_PAGE == 'FORMS_RESULTS') {
			Table__DATA__Filter_by_DATE();
		}
	});
	
	
	//buttons New Axis
	$("button#New_YAxis").fancybox({
		padding:0, //def:15
		margin:10, //def:20
		modal:true,
		live:false,
		tpl: { error: '<p class="fancybox-error">' + LANG.PROBLEM_LOADING_PAGE + '</p>' },
		maxWidth:'100%',
		width:'100%',
		beforeLoad: function() {
			loading.show();
		},
		afterLoad: function() {
			loading.hide();
		},
		afterClose: function() {
			//location.reload();
			return;
		},
		afterShow: function() {
			$('.fancybox-skin').append('<a title="'+LANG.BACK+'" class="fancybox-item fancybox-back" href="javascript:jQuery.fancybox.close();"></a>'); //close button
			$('.fancybox-skin').append('<a title="'+LANG.CLOSE+'" class="fancybox-item fancybox-close" href="javascript:jQuery.fancybox.close();"></a>'); //close button
		}
	});


	//Load selected axis
	$('#Axis__Load').on('click', function() {
		Axis__Load();
	});
	

	//Button__Chart__Update --Button__Chart__Update_2 -only in RESULTS
	$('#Button__Chart__Update, #Button__Chart__Update_2').on('click', function() {
		Chart__Update();
	});


	//export_data
	$('#export_data').on('click', function() {
		Swal({
			title: LANG.RESULTS.DATA_EXPORT,
			html: '' +
				'<button id="export_data_csv" type="button" class="btn btn-success btn-md">'+
					'<i style="font-size:17px;" class="fa fa-download"></i>'+
					'&nbsp;&nbsp;<b>'+LANG.RESULTS.DATA_EXPORT_CSV+'</b>'+
				'</button>'+
				'<br><br>'+
				'<button id="export_data_xlsx" type="button" class="btn btn-success btn-md">'+
					'<i style="font-size:17px;" class="fa fa-download"></i>'+
					'&nbsp;&nbsp;<b>'+LANG.RESULTS.DATA_EXPORT_XLSX+'</b>'+
				'</button>',
			//allowOutsideClick: false,
			showConfirmButton: false,
			showCancelButton: true,
			cancelButtonText: LANG.BUTTON_CANCEL
		});
	});

	//export_data CSV
    $(document).on('click', '#export_data_csv', function() {
        Swal.clickConfirm();
		Export_Table_Data('csv');
	});
	
	//export_data XLSX
    $(document).on('click', '#export_data_xlsx', function() {
        Swal.clickConfirm();
		Export_Table_Data('xlsx');
    });


	//init Diagram
	$('#container_graph').highcharts({});
	V_Chart = $('#container_graph').highcharts();	

}); //end jQuery(function() {
