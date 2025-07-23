"use strict";

jQuery(function ()
{   
	
	$(".chosen-select").chosen({
		allow_single_deselect:true,
		disable_search_threshold:5,
		width:"100%",
		no_results_text:LANG.NO_RESULTS 
	});
	
	// https://eonasdan.github.io/bootstrap-datetimepicker/
	$('#datetimepicker_from, #datetimepicker_to').datetimepicker({
		locale: LANG.LANG_CURRENT,
		format: LANG.MOMENT.DATE,
		showTodayButton: true,
		showClose: true,
		allowInputToggle: true,
		//widgetPositioning:{horizontal: 'right'},
		//debug: true, //Will cause the date picker to stay open after a blur event.
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
	$("#datetimepicker_from").on("dp.change", function (e) {
		$('#datetimepicker_to').data("DateTimePicker").minDate(e.date);
	});
	$("#datetimepicker_to").on("dp.change", function (e) {
		$('#datetimepicker_from').data("DateTimePicker").maxDate(e.date);
	});
		
	//Download ###############################
	$('#download_XLS').on('click',function() {
		Download_Data("xls");
	});
	$('#download_XLSX').on('click',function() {
		Download_Data("xlsx");
	});
	$('#download_CSV').on('click',function() {
		Download_Data("csv");
	});
	//Download ###############################
});

//Download #################################
function Download_Data(xls_xlsx_csv) {
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
			a.click();
			a.remove();
		} 
		else console.log('Error! Fall back to server side handling');
	}

	function download_XLS_file() {
		const now = new Date();
		const day = ("0" + now.getDate()).slice(-2);
		const month = ("0" + (now.getMonth() + 1)).slice(-2);
		const today = (day) + "." + (month) + "." + now.getFullYear();
		const export_data = $('#export_data').html();
		const data = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>'+ today +'</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--><meta http-equiv="content-type" content="text/plain; charset=UTF-8"/></head><body><table>'+ export_data +'</table></body></html>';
        getFile(
            'data:text/html,\uFEFF' + data.replace(/\n/g, '%0A'), //data:application/vnd.ms-excel;base64,
            'xls',
            data,
			$('#filename').val()
        );
	}
	
	function download_XLSX_file() {
		export_table_to_excel('export_data', $('#filename').val() + '.xlsx');
	}

	function download_CSV_file() {
        const data = $("#export_data").table2CSV({delivery:'value'});
        getFile(
            'data:text/csv,\uFEFF' + data.replace(/\n/g, '%0A'),
            'csv',
            data,
			$('#filename').val()
        );
	}
	
	if (xls_xlsx_csv == 'xls') {
		download_XLS_file();
	}
	else if (xls_xlsx_csv == 'xlsx') {
		download_XLSX_file();
	}
	else if (xls_xlsx_csv == 'csv') {
		download_CSV_file();
	}
}
//Download #################################
