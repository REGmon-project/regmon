"use strict";

// Grid -extend default grid options

var V_TIME_OUT_AFTER_SAVE = 2000;
var V_GRID_SAVE = false;
var V_ADMIN_VIEW = false;


var initColor = function (el) {
	setTimeout(function () {
		$(el).colorPicker();
	}, 100);
};


var initDate = function (el) {
	setTimeout(function () {
		$(el).after(' <i class="fa fa-calendar" style="font-size:14px;"></i>').next().on('click', function (e) {
			$(el).datepicker('show');
			return false;
		});
		$(el).datepicker({
			changeMonth: true,
			changeYear: true,
			//yearRange: "-85:-5",
			//showButtonPanel: true,
			dateFormat: LANG.DATEPICKER.DATE
		});
		$('.ui-datepicker').css({ 'font-size': '75%' });
	}, 100);
};


var numberTemplate = {
	formatter: "number", align: "right", sorttype: "number",
	editrules: { number: true, required: true },
	searchoptions: { sopt: ["eq", "ne", "lt", "le", "gt", "ge", "nu", "nn", "in", "ni"] }
};

var checkboxTemplate = {
	formatter: "checkbox", /*formatoptions: { disabled: false},*/ align: "center",
	edittype: "checkbox", editoptions: { value: LANG.YES + ":" + LANG.NO, defaultValue: LANG.YES },
	stype: "select", searchoptions: { sopt: ["eq", "ne"], value: ":;1:" + LANG.YES + ";0:" + LANG.NO }
};

var aktivInaktivTemplate = {
	fixed: true, align: "center",
	formatter: "select", edittype: "select", stype: 'select',
	searchoptions: { sopt: ['eq', 'ne'], value: ":;1:" + LANG.ST_ACTIVE + ";0:" + LANG.ST_INACTIVE },
	editoptions: { value: "1:" + LANG.ST_ACTIVE + ";0:" + LANG.ST_INACTIVE, defaultValue: '1', size: 1 },
	cellattr: function (rowId, val) {
		if (val == 1) return " style='color:green;'"; //active
		else return " style='color:red;'"; //inactive
	}
};

var aktivInaktivPrivateTemplate = {
	fixed: true, align: "center",
	formatter: "select", edittype: "select", stype: 'select',
	searchoptions: { sopt: ['eq', 'ne'], value: ":;1:" + LANG.ST_ACTIVE + ";3:" + LANG.ST_PRIVATE + ";0:" + LANG.ST_INACTIVE },
	editoptions: { value: "1:" + LANG.ST_ACTIVE + ";3:" + LANG.ST_PRIVATE + ";0:" + LANG.ST_INACTIVE, defaultValue: LANG.ST_ACTIVE, size: 1 },
	cellattr: function (rowId, val) {
		if (val == 1) return " style='color:green;'"; //active
		else if (val == 3) return " style='color:blue;'"; //private
		else return " style='color:red;'"; //inactive
	}
};

var stopDateTemplate = {
	formatter: "date", sorttype: "date", formatoptions: { srcformat: "Y-m-d", newformat: LANG.GRID.DATE },
	editoptions: { dataInit: initDate, style: "width:70%" },
	cellattr: function (rowId, val) {
		if (moment(val, 'YYYY-MM-DD').isSame(moment(), 'day')) return ' class="orange"';
		else if (moment(val, 'YYYY-MM-DD').isAfter()) return ' class="green"';
		else if (moment(val, 'YYYY-MM-DD').isBefore()) return ' class="red"';
	}
};

var sexTemplate = {
	formatter: "select", edittype: "select", align: "center",
	editoptions: { value: ": ;0:" + LANG.USERS.SEX_MALE + ";1:" + LANG.USERS.SEX_FEMALE + ";2:" + LANG.USERS.SEX_OTHER, defaultValue: LANG.USERS.SEX_MALE, size: 1 },
	stype: 'select', searchoptions: { sopt: ['eq', 'ne'], value: ": ;0:" + LANG.USERS.SEX_MALE + ";1:" + LANG.USERS.SEX_FEMALE + ";2:" + LANG.USERS.SEX_OTHER }
};

var hiddenReadonlyTemplate = { hidden:true, editrules:{edithidden:true}, editoptions:{readonly:'readonly'}};

jQuery(function() {
	if (typeof $.blockUI == 'function') V_ADMIN_VIEW = true;
	if (!V_ADMIN_VIEW) V_TIME_OUT_AFTER_SAVE = 1000;
	
	//JQGRID EXTEND #####################
	//options
	$.extend($.jgrid.defaults, {
		datatype: "json",
		gridview: true,
		sortname: 'id',
		sortorder: "asc",
		iconSet: 'fontAwesome',
		pgbuttons: false, 
		//pgtext: '',
		viewrecords: true, //view 1 - 1 of 10
		rowNum: 999,
		//rowList:[20,40,60,80,100],
		multiselect: false,
		altRows: true,
		//height: '100%', //def
		//forceClientSorting: true,
		headertitles:true,
		sortable: true, //reorder columns
		sortIconsBeforeText: true,
		searching: { searchOnEnter: false, searchOperators: false },
		ignoreCase: true,
		autoencode: false,
		loadError: function(xhr,st,err) { 
			if (xhr.responseText == 'session expired') window.location.href = ".";
		},
		cmTemplate: {/*sortable:false,*/ editoptions:{size:25}, editable:true, searchoptions: {
			sopt: ['cn', 'nc', 'eq', 'ne', 'lt', 'le', 'gt', 'ge', 'bw', 'bn', 'ew', 'en', 'nu', 'nn', 'in', 'ni']
		}}
	});
	

	//edit + add -- inline + nav
	$.extend($.jgrid.edit, {
		width:330, 
		recreateForm:true,
		closeOnEscape:true,
		//closeAfterAdd: true,
		beforeShowForm: function(form) {
			$('.ui-jqdialog-content .FormData td.CaptionTD').each(function(){
				$(this).html($(this).html().replace('<br>',' ')); //replace <br> with ' ' in CaptionTD
			});
		},
		afterShowForm: function(form) {
			//gray out not used fields
			$('input[readonly], select[readonly], textarea[readonly]').css({'background-color':'#eee','background-image':'none'});
			$('select[readonly]').attr("disabled", "disabled");
		},
		beforeSubmit : function(postdata, formid) {
			if (V_ADMIN_VIEW) $.blockUI({ message: '' }); 
			else {
				V_GRID_SAVE = true;
			}
			return [true, ''];
		},
		afterSubmit: function (jqXHR) {
			const this_id = this.id;
			const myInfo = ''+
				'<div class="ui-state-highlight ui-corner-all">' +
					'<span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>' +
					'<span>' + (jqXHR.responseText == "OK_insert" ? LANG.INSERT_OK : LANG.UPDATE_OK) + '</span>'+
				'</div>';

			if (jqXHR.responseText !== "OK_insert" &&
				jqXHR.responseText !== "OK_update")
			{
				V_GRID_SAVE = false;
				if (V_ADMIN_VIEW) {
					$.unblockUI();
				}
				else {
					loading.hide();
				}

				const errorMessage = '' +
					'<span class="ui-icon ui-icon-alert" style="float:left;"></span>' +
					'<span style="display:block; padding:1px 0px 0px 20px;">' + jqXHR.responseText + '</span>';
				
				return [false, errorMessage];
			}


			const $infoTr = $("table#TblGrid_"+ this_id +">tbody>tr.tinfo");
			const $infoTd = $infoTr.children("td.topinfo");
			$infoTd.html(myInfo);
			$infoTr.show();

			// hide the info after 2 sec timeout
			setTimeout(function () {
				$infoTr.fadeOut("slow", function () {
					$infoTr.hide();

					$('#edithd' + this_id + ' > a').trigger("click"); //close after add is ok
					
					V_GRID_SAVE = false;
					if (V_ADMIN_VIEW) {
						$.unblockUI();
					}
					else {
						loading.hide();
					}
				});
			}, V_TIME_OUT_AFTER_SAVE);

			//if loadonce:true need this to reload grid
			$(this).jqGrid('setGridParam', { datatype: 'json' });


			//response should be interpreted as successful
			return [true, "", ""];
		}
	});
	

	//delete
	$.extend($.jgrid.del, {
		closeOnEscape:true,
		//recreateForm: true,
		//reloadAfterSubmit: true,
		beforeSubmit : function(postdata, formid) {
			if (V_ADMIN_VIEW) {
				$.blockUI({ message: '' });
			}
			else {
				loading.show();
			}

			return [true, ''];
		},
		afterSubmit: function (jqXHR) {
			if (V_ADMIN_VIEW) {
				$.unblockUI();
			}
			else loading.hide();
			
			if (jqXHR.responseText !== "OK_delete") {
				const errorMessage = '' +
					'<span class="ui-icon ui-icon-alert" style="float:left;"></span>' +
					'<span style="display:block; padding:1px 0px 0px 20px;">' + jqXHR.responseText + '</span>';
				
				return [false, errorMessage];
			}

			//if loadonce:true need this to reload grid
			$(this).jqGrid('setGridParam', { datatype: 'json' });
			
			//response should be interpreted as successful
			return [true, "", ""];
		}
	});
	

	//view
	$.extend($.jgrid.view, {
		width:300,
		beforeShowForm: function(form) {
			//hide the edit icon inside id value
			setTimeout(function () {
				//$('#v_id').find('span:first').css('display','none');
				$('span#acc').hide();
			}, 100);
		}
	});

}); //end jQuery(document).ready