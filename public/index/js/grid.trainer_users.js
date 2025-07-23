"use strict";

// Trainer Users Grid

var $trainer_users = false;
var trainer_users_data = false;

jQuery(function() 
{

const st_all = '<span class="req">.</span>'; //all
const st_null = '<span class="req" title="'+LANG.R_STATUS.NO_REQUEST+'">.</span>'; //null
const st_no = '<span class="req G_no" title="'+LANG.R_STATUS.REQUEST_REJECTED+'">0</span>'; //0
const st_yes = '<span class="req G_yes" title="'+LANG.R_STATUS.REQUEST_ACCEPTED+'">1</span>'; //1
const st_leaveR = '<span class="req G_leaveR" title="'+LANG.R_STATUS.CANCELED_ACCESS_TRAINER+'">5</span>'; //5
const st_leaveA = '<span class="req G_leaveA" title="'+LANG.R_STATUS.CANCELED_ACCESS_ATHLETE+'">15</span>'; //15
const st_waitLR = '<span class="req G_waitLR" title="'+LANG.R_STATUS.REQ_WAIT_CANCELED_TRAINER+'">7</span>'; //7
const st_waitLA = '<span class="req G_waitLA" title="'+LANG.R_STATUS.REQ_WAIT_CANCELED_ATHLETE+'">17</span>'; //17
const st_waitN = '<span class="req G_waitN" title="'+LANG.R_STATUS.REQ_WAIT_REJECTED_ATHLETE+'">8</span>'; //8
const st_wait = '<span class="req G_wait" title="'+LANG.R_STATUS.REQUEST_WAIT+'">9</span>'; //9
const request_status = ':X;null:'+st_null+';1:'+st_yes+';5:'+st_leaveR+';15:'+st_leaveA+';0:'+st_no+';7:'+st_waitLR+';17:'+st_waitLA+';8:'+st_waitN+';9:'+st_wait;

const LU = LANG.USERS;
let currentPage = 1;
const idPrefix = "ut_";
const pager = "#UTpager";

$trainer_users = $("#trainer_users");
if ($trainer_users) 
{

//Trainer Users ###############################
$trainer_users.jqGrid({  
	url: 'php/ajax.php?i=users&oper=trainer&group_id='+V_GROUP,
	editurl: 'php/ajax.php?i=users&oper=trainer&group_id='+V_GROUP,
	loadonce: true,
	iconSet: 'fontAwesome',
	idPrefix: idPrefix,
	pager: pager,
	sortname: 'u.id',
	//caption: LU.HEADER,
	pgbuttons: true,
	rowNum: 10,
	rowList:[5,10,20,30,50,100,200,500,'999999:Alle'],
	multiselect:true,
	colNames:[LANG.ID, LU.FIRSTNAME, LU.LASTNAME, LU.BIRTH_DATE, LU.SPORT, LU.SEX, LU.BODY_HEIGHT, LANG.CREATED, LANG.MODIFIED, 'Freigaben', LANG.STATUS],
	colModel:[
		{name:'u.id',key:true, 	width:35, align:"center", hidden:true, sorttype:'int'},
		{name:'u.firstname', 		width:80},
		{name:'u.lastname', 	 	width:80},
		{name:'u.birth_date',	width:85, align:"right", 
			sorttype:"date", formatter:"date", formatoptions:{srcformat:"Y-m-d", newformat:LANG.GRID.DATE},
			searchoptions: { sopt: ['cn', 'eq', 'ne', 'lt', 'le', 'gt', 'ge'] }
		},
		{name:'u.sport',		width:90, formatter:"select", edittype:"select", align:"center"},
		{name:'u.sex', 			width:65, template: sexTemplate},
		{name:'u.body_height',	width:45, formatter:"select", edittype:"select", align:"center"},
		{name:'u.created', 		width:64, hidden:true},
		{name:'u.modified', 	width:64, hidden:true},
		{name:'freigaben_link', width:24, fixed:true, sortable:false, editable:false, resizable:false, search:false, align:"center"},
		{name:'u2t.status',		width:30, formatter:"select", edittype:"select", align:"center",
			fixed:true, editable:false, resizable:false,
			editoptions:{value: request_status},
			stype:'select', searchoptions: {sopt:['eq', 'ne'], value: request_status}
		}
	],
	loadComplete: function(data) {
		//goto currentPage
		if (this.p.datatype === 'json' && currentPage !=1) {
			setTimeout(function() {
				$trainer_users.trigger("reloadGrid",[{page:currentPage}]);
			}, 50);
		}
		//color current user
		if (this.p.datatype === 'json') { trainer_users_data = data; }
		$.each(trainer_users_data.rows,function(i,item){
			if(trainer_users_data.rows[i][0]==V_UID) {
				$("#"+ idPrefix + trainer_users_data.rows[i][0]).css("color", "darkblue");
			}
		});
		
		$('.checklist').off('click').on('click',function() {
			const athlete_id = $(this).attr('data-id');

			Show_Athlete_2_Trainer_Forms__Load_Open(athlete_id);
		});
	}
})
.jqGrid('navGrid',pager,{ //bottom bar
	iconsOverText: true,
	edit: false,	edittext: LANG.BUTTON_EDIT,
	add: false, 	addtext: LANG.BUTTON_ADD,
	del: false, 	deltext: LANG.BUTTON_DELETE,
	search: false, 	searchtext: LANG.BUTTON_SEARCH,
	view: false, 	viewtext: LANG.BUTTON_VIEW,
	refresh: false, refreshtext: LANG.BUTTON_RELOAD
})
.jqGrid('filterToolbar',{ //Column Search
	stringResult:true, //send as Json //filters
	searchOnEnter:false,
	defaultSearch: 'cn'
});

$(pager).children().children().css('table-layout', 'auto'); //fix pager width
$('#gbox_trainer_users').removeClass('ui-corner-all').addClass('ui-corner-bottom')
$('#gbox_trainer_users .ui-jqgrid-hdiv').css('overflow', 'visible'); //fix pager

//Request Status Select ###############################################################
$('select[name="u2t.status"]').chosen({width:'26px', disable_search:true, disable_search_threshold:15});


var athletes_ids = [];
var Athletes_Confirm_Header = '';
function get_Athletes_IDS_Names(type) {
	athletes_ids = [];
	const ids = $trainer_users.jqGrid('getGridParam','selarrrow'); //selected rows
	let ids_uncheck = [];
	let header = LANG.REQUEST.NOBODY_SELECTED;

	var add_ID_name = function(id, status) {
		athletes_ids.push(id + '_' + status);
		names.push(
			'<li>' +
				$trainer_users.jqGrid('getCell', id, 'u.firstname') +
				' ' +
				$trainer_users.jqGrid('getCell', id, 'u.lastname')
		);
	};

	if (ids.length>0) {
		var names = [];
		for (let i = 0, i1 = ids.length; i < i1; i++) {
			const status = $('#'+ids[i]+' td:last').text();
			if (type == 'request_access_athlete' &&
				(status == '0' || status == '5' || status == '15' || status == '.'))
			{
				add_ID_name(ids[i], status);

				header = LANG.REQUEST.REQUEST_ACCESS_FROM;
			}
			else if (type == 'cancel_request_athlete' &&
				(status == '7' || status == '17' || status == '8' || status == '9'))
			{
				add_ID_name(ids[i], status);

				header = LANG.REQUEST.CANCEL_REQUEST_FROM;
			}
			else if (type == 'cancel_access_athlete' && status == '1') {
				add_ID_name(ids[i], status);
				
				header = LANG.REQUEST.CANCEL_ACCESS_TO;
			}
			else {
				ids_uncheck.push(ids[i]);
			}
		}

		for (let i = 0, i2 = ids_uncheck.length; i < i2; i++) {
			$trainer_users.jqGrid('setSelection', ids_uncheck[i], false);
		}

		Athletes_Confirm_Header = header;

		return '<b>' + header + '</b><br><hr style="margin:10px -10px;">' + names.join("<br> ");
	}
	else {
		Athletes_Confirm_Header = header;

		return '<b>' + header + '</b><br>';
	}
}

function init_request_athlete_buttons(el) {
	let el1, el2, bt, bt1, bt2;
	if (el == 'request_access_athlete') {
		el1 = 'cancel_request_athlete'; el2 = 'cancel_access_athlete';
	}
	else if (el == 'cancel_request_athlete') {
		el1 = 'request_access_athlete'; el2 = 'cancel_access_athlete';
	}
	else if (el == 'cancel_access_athlete') {
		el1 = 'request_access_athlete'; el2 = 'cancel_request_athlete';
	}
	

	bt =  $('#' + el)[0].outerHTML;  $('#' + el).remove();
	bt1 = $('#' + el1)[0].outerHTML; $('#' + el1).remove();
	bt2 = $('#' + el2)[0].outerHTML; $('#' + el2).remove();
	
	$('#req_athlete_action').prepend(bt);
	$('#req_athlete_action_1').prepend(bt1);
	$('#req_athlete_action_2').prepend(bt2);
	

	$("#"+el).off('click').confirmation({
		href: 'javascript:void(0)',
		html:true, placement: 'top',
		btnOkLabel: LANG.YES, btnOkClass: 'btn btn-sm btn-success mr10',
		btnCancelLabel: LANG.NO, btnCancelClass: 'btn btn-sm btn-danger',
		title: function () {
			return get_Athletes_IDS_Names(el);
		},
		onShow: function(event, element){ 
			if (Athletes_Confirm_Header == LANG.REQUEST.NOBODY_SELECTED) {
				$(element).parent().addClass('no_select');
			} else {
				$(element).parent().removeClass('no_select');
			}
		},
		onConfirm: function(e, button) {
			if (athletes_ids.length != 0) {
				const post_data = {
					request: 'user2trainer',
					action: el,
					group_id: V_GROUP,
					athletes_ids: athletes_ids
				};
				$.post('index/ajax.request.php', post_data, function (data, result)
				{
					$("#req_athletes_message").html(data);

					//reload grid
					currentPage = $trainer_users.jqGrid("getGridParam", "page"); //for loadComplete
					$trainer_users.trigger("reloadGrid", { fromServer: true }); //free-jqgrid only
					

					if ($('#A_Requests_From_Trainers').text().trim()!='') {
						load_Trainer_Requests();
					}

					load_New_Info(); //###########
				});
			}
		}
	});

	$('#'+el1+', #'+el2).off('click').on('click',function() {
		init_request_athlete_buttons(this.id);
	});
}
init_request_athlete_buttons('request_access_athlete');


	
function Responsive_Trainer_Users() { 
	// Get width of parent container
	let p_width = $('#C_Request_Access_From_Athletes_link').prop('clientWidth');
    if (p_width == null || p_width < 1){
		// For IE, revert to offsetWidth if necessary
		p_width = $('#C_Request_Access_From_Athletes_link').prop('offsetWidth');
    }
    p_width = p_width - 4; //prevent horizontal scrollbars
		
	//set here anyway to avoid empty grid after (calendar, options) change
	$trainer_users.jqGrid('setGridWidth', p_width);
	if (p_width != $trainer_users.width()) {
		//$trainer_users.jqGrid('setGridWidth', p_width);
	}
}
Responsive_Trainer_Users();

	
//on window resize -> resize grids
$(window).on('resize', function() {
	Responsive_Trainer_Users();
}).trigger('resize');


} //end if (trainer_users)

}); //end jQuery(document).ready