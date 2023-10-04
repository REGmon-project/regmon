"use strict";

// Group Users Grid

var $group_users = false;

jQuery(function() 
{

const rs_no = LANG.R_STATUS.REQUEST_REJECTED; //0
const rs_yes = LANG.R_STATUS.REQUEST_ACCEPTED; //1
const rs_leaveR = LANG.R_STATUS.CANCELED_ACCESS_USER; //5
const rs_leaveA = LANG.R_STATUS.CANCELED_ACCESS_GROUP; //15
const rs_waitLR = LANG.R_STATUS.REQ_WAIT_CANCELED_USER; //7
const rs_waitLA = LANG.R_STATUS.REQ_WAIT_CANCELED_GROUP; //17
const rs_waitN = LANG.R_STATUS.REQ_WAIT_REJECTED_USER; //8
const rs_wait = LANG.R_STATUS.REQUEST_WAIT; //9
const rs_new = LANG.R_STATUS.REQ_WAIT_NEW_USER; //10
const rs_newx = LANG.R_STATUS.REQ_WAIT_USER_INACTIVE; //11
const request_status = ':;1:'+rs_yes+';5:'+rs_leaveR+';15:'+rs_leaveA+';0:'+rs_no+';7:'+rs_waitLR+';17:'+rs_waitLA+';8:'+rs_waitN+';9:'+rs_wait+';10:'+rs_new+';11:'+rs_newx;

const rs_H_all = '<span class="req">.</span>'; //all
const rs_H_no = '<span class="req G_no" title="'+rs_no+'">0</span>'; //0
const rs_H_yes = '<span class="req G_yes" title="'+rs_yes+'">1</span>'; //1
const rs_H_leaveR = '<span class="req G_leaveR" title="'+rs_leaveR+'">5</span>'; //5
const rs_H_leaveA = '<span class="req G_leaveA" title="'+rs_leaveA+'">15</span>'; //15
const rs_H_waitLR = '<span class="req G_waitLR" title="'+rs_waitLR+'">7</span>'; //7
const rs_H_waitLA = '<span class="req G_waitLA" title="'+rs_waitLA+'">17</span>'; //17
const rs_H_waitN = '<span class="req G_waitN" title="'+rs_waitN+'">8</span>'; //8
const rs_H_wait = '<span class="req G_wait" title="'+rs_wait+'">9</span>'; //9
const rs_H_new = '<span class="req G_new" title="'+rs_new+'">10</span>'; //10
const rs_H_newx = '<span class="req G_newx" title="'+rs_newx+'">11</span>'; //11
const request_status_Html = ':;1:'+rs_H_yes+';5:'+rs_H_leaveR+';15:'+rs_H_leaveA+';0:'+rs_H_no+';7:'+rs_H_waitLR+';17:'+rs_H_waitLA+';8:'+rs_H_waitN+';9:'+rs_H_wait+';10:'+rs_H_new+';11:'+rs_H_newx;

const LU = LANG.USERS;
let currentPage = 1;
const idPrefix = "ug_";
const pager = "#UGpager";
	
$group_users = $("#group_users");
if ($group_users) 
{

//Users Group ###############################
$group_users.jqGrid({  
	url: 'php/ajax.php?i=users&oper=group&group_id='+V_GROUP,
	editurl: 'php/ajax.php?i=users&act=group_add_edit&group_id='+V_GROUP+'&location_id='+V_Group_2_Location[V_GROUP][0],
	loadonce: true,
	iconSet: 'fontAwesome',
	idPrefix: idPrefix,
	pager: pager,
	sortname: 'id',
	//caption: LU.HEADER,
	pgbuttons: true,
	rowNum: 10,
	rowList:[5,10,20,30,50,100,200,500,'999999:Alle'],
	//loadonce: true,
	multiselect:true,
	colNames:['', LANG.ID, LU.USERNAME, LU.PASSWORD, LU.PASS_CONFIRM, LU.FIRSTNAME, LU.LASTNAME, LU.BIRTH_DATE, LU.SPORT, LU.SEX, LU.BODY_HEIGHT, LU.EMAIL, LU.TELEPHONE, LU.LEVEL, LU.LAST_LOGIN, LU.LOGIN_COUNT, LANG.CREATED, LANG.MODIFIED, LANG.STATUS],
	colModel:[
		{ //inline editing buttons and options
			name: 'acc', hidden:!(V_ADMIN||V_LOCATION_ADMIN||V_GROUP_ADMIN||V_GROUP_ADMIN_2), width:22, fixed:true, sortable:false, editable:false, search: false, resizable:false, formatter:'actions', 
			formatoptions:{
				keys:true, //[Enter]=save,[Esc]=cancel
				delbutton:false,
				afterSave: function () {
					$group_users.trigger("reloadGrid", [{ current: true }]);
				},
				//form dialog edit
				editformbutton: true, 
				editOptions : {
					recreateForm:true,
					width:350,
					closeOnEscape:true,
					afterShowForm: function (form) {
						//gray out not used fields
						$('input[readonly], select[readonly], textarea[readonly]').css({'background-color':'#eee','background-image':'none'});
						$('select[readonly]').attr("disabled", "disabled");
						$('.navButton').css({'display':'none'});

						//Pass check ####################################
						$('#passwd').after(''+
							'<div id="password_match" style="color:red;">'+LANG.USERS.PASSWORD_CONFIRM+'</div>'+
							'<div id="password_len" style="color:red;">'+LANG.USERS.PASSWORD_MIN_LENGTH+'</div>'+
							'<div id="password_strength" style="color:red;">'+LANG.USERS.PASSWORD_WEAK+'</div>'+
						'');
						$('#password_match, #password_len, #password_strength').hide();
						$('#passwd, #pass_confirm').on('keyup', function (event) {
							check_Password();
						});
						//Pass check ####################################
					}
				}
			}
		},
		{name:'id',key:true, width:35, align:"center", hidden:true, /*formatter:'number',*/ sorttype:'int'},
		{name:'uname',		 width:80, hidden:true, editrules:{edithidden:true}	/*editoptions:{readonly:'readonly'}*/ },
		{name:'passwd', 	 width:50, edittype:'password', hidden:true, editrules:{edithidden:true} },
		{name:'pass_confirm', width:50, edittype:'password', hidden:true, editrules:{edithidden:true} },
		{name:'firstname', 	width:90},
		{name:'lastname', 	width:90},
		{name:'birth_date', width:98, align:"right", 
			sorttype:"date", formatter:"date", formatoptions:{srcformat:"Y-m-d", newformat:LANG.GRID.DATE},
			editoptions:{ dataInit:initDate, style:"width:70%" },
			searchoptions: { sopt: ['cn', 'eq', 'ne', 'lt', 'le', 'gt', 'ge'] }
		},
		{name:'sport', width:80,
				formatter:"select", edittype:"select", stype:'select',
				searchoptions: {sopt:['cn','eq','ne'], value:V_SPORTS_OPTIONS},	
				editoptions:{
					value:V_SPORTS_OPTIONS, multiple:true, size:1, dataUrl:'php/ajax.php?i=sports&oper=sports_select', 
					selectFilled:function(options) {
						$(options.elem).chosen({width:'100%', placeholder_text_multiple: ' ', no_results_text: LANG.NO_RESULTS});
					}
				}
		},
		{name:'sex', width:60, template: sexTemplate},
		{name:'body_height', width:40,
				formatter:"select", edittype:"select", align:"center",
				editoptions:{value: V_BODY_HEIGHT_OPTIONS, defaultValue: '100 cm', size:1}
		},
		{name:'email', width:130, hidden:true, editrules:{edithidden:true} },
		{name:'telephone', width:130, hidden:true, editrules:{edithidden:true} },
		{name:'level', width:65,
				formatter:"select", edittype:"select", align:"center",
				editoptions:{value: V_USER_LVL_OPTIONS, defaultValue: '10', size:1, style:"width:100%"},
				stype:'select', searchoptions: {sopt:['eq', 'ne'], value:":;"+V_USER_LVL_OPTIONS},
		},
		{name:'lastlogin', 	width:64, hidden:true, editrules:{edithidden:true}, editoptions:{readonly:'readonly'} },
		{name:'logincount', width:40, hidden:true, editrules:{edithidden:true}, editoptions:{readonly:'readonly'}, align:"right" },
		{name:'created', 	width:64, hidden:true, editrules:{edithidden:true}, editoptions:{readonly:'readonly'} },
		{name:'modified', 	width:64, hidden:true, editrules:{edithidden:true}, editoptions:{readonly:'readonly'} },
		{name:'status',		width:30, formatter:"select", edittype:"select", align:"center",
			fixed:true, editable:false, resizable:false,
			editoptions:{value: request_status_Html},
			stype:'select', searchoptions: {sopt:['eq', 'ne'], value: request_status}
		}
	],
	loadComplete: function(data) {
		//goto currentPage
		if (this.p.datatype === 'json' && currentPage !=1) {
			setTimeout(function() {
				$group_users.trigger("reloadGrid",[{page:currentPage}]);
			}, 250);
		}
		else if (this.p.datatype === 'json') {
			setTimeout(function() {
				$group_users.trigger("reloadGrid",[{current:true}]);
			}, 250);
		}
	}
})
//Column Search
.jqGrid('filterToolbar',{
	stringResult:true, //send as Json //filters
	searchOnEnter:false,
	defaultSearch: 'cn'
})
//bottom bar
.jqGrid('navGrid',pager,{
	iconsOverText: true,
	edit: false,	edittext: LANG.BUTTON_EDIT,
	add: (V_ADMIN||V_LOCATION_ADMIN||V_GROUP_ADMIN||V_GROUP_ADMIN_2), addtext: LANG.BUTTON_ADD,
	del: false, 	deltext: LANG.BUTTON_DELETE,
	search: false, 	searchtext: LANG.BUTTON_SEARCH,
	view: true, 	viewtext: LANG.BUTTON_VIEW,
	refresh: true, refreshtext: LANG.BUTTON_RELOAD
});


$(pager).children().children().css('table-layout', 'auto'); //fix pager width
$('#gbox_group_users').removeClass('ui-corner-all').addClass('ui-corner-bottom')
$('#gbox_group_users .ui-jqgrid-hdiv').css('overflow', 'visible'); //fix header for chosen

	
function check_Password() {
	function check_Pass(password) { //([a-z]+[A-Z]+[0-9]) //--[$@#&!]
		let strength = 0;
		if (password.match(/[a-z]+/)) strength += 1;
		if (password.match(/[A-Z]+/)) strength += 1;
		if (password.match(/[0-9]+/)) strength += 1;
		//if (password.match(/[$@#&!]+/)) strength += 1;

		if (password.length === 0) return false;
		if (password.length < 8 && password.length !==0) return 'len<8';
		if (strength < 3) return strength;
		return 'OK';
	}

	let password = $('#passwd').val();
	let password2 = $('#pass_confirm').val();
	let checkPassword = check_Pass(password);
	
	$('#password_match, #password_len, #password_strength').hide();

	if (!checkPassword) {
		return false;
	}

	if (password != password2) {
		$('#password_match').show();
	}
	if (checkPassword == 'len<8') {
		$('#password_len').show();
	}
	if (checkPassword < 3) {
		$('#password_strength').show();
	}

	return false;
}


var group_users_ids = [];
function get_Group_Users_IDS_Names() {
	const ids = $group_users.jqGrid('getGridParam','selarrrow'); //selected rows
	let ids_uncheck = [];
	let header = LANG.REQUEST.NOBODY_SELECTED;
	group_users_ids = [];

	var add_ID_name = function(id, status) {
		group_users_ids.push(id + '_' + status);
		names.push(
			'<li>' +
			$group_users.jqGrid('getCell', id, 'firstname') +
			' ' +
			$group_users.jqGrid('getCell', id, 'lastname')
		);
	};

	if (ids.length > 0) {
		var names = [];
		for (let i = 0, i1 = ids.length; i < i1; i++) {
			const status = $('#' + ids[i] + ' td:last').text();
			if (status == '1' || status == '10') {
				add_ID_name(ids[i], status);

				header = LANG.REQUEST.CANCEL_ACCESS_TO;
			}
			else {
				ids_uncheck.push(ids[i]);
			}
		}

		for (let i = 0, i2 = ids_uncheck.length; i < i2; i++) {
			$group_users.jqGrid('setSelection', ids_uncheck[i], false);
		}

		return '<b>' + header + '</b><br><hr style="margin:10px -10px;">' + names.join("<br> ");
	}
	else {
		return '<b>' + header + '</b><br>';
	}
}

	
$("#group_user_cancel_access_groupadmin").confirmation({
	href: 'javascript:void(0)',
	html:true, placement: 'top',
	btnOkLabel: LANG.YES, btnOkClass: 'btn btn-sm btn-success mr10',
	btnCancelLabel: LANG.NO, btnCancelClass: 'btn btn-sm btn-danger',
	title: function () {
		return get_Group_Users_IDS_Names();
	},
	onConfirm: function(e, button) {
		if (group_users_ids.length != 0) {
			const post_data = {
				request: 'user2group_Answer',
				action: 'group_user_cancel_access_groupadmin',
				group_id: V_GROUP,
				group_users_ids: group_users_ids
			};
			$.post('index/ajax.request.php', post_data, function (data, result)
			{
				$("#req_group_users_message").html(data);

				//reload grid
				currentPage = $group_users.jqGrid("getGridParam", "page"); //for loadComplete
				$group_users.trigger("reloadGrid", { fromServer: true }); //free-jqgrid only
			});
		}
	}
});


function Responsive_Group_Users() { 
    let p_width = $('#C_Group_Users_link').prop('clientWidth');// Get width of parent container
    if (p_width == null || p_width < 1){
        p_width = $('#C_Group_Users_link').prop('offsetWidth'); // For IE, revert to offsetWidth if necessary
    }
    p_width = p_width - 2; //prevent horizontal scrollbars
		
	//set here anyway to avoid empty grid after (calendar, options) change
	$group_users.jqGrid('setGridWidth', p_width);
	if (p_width != $group_users.width()) {
		//$group_users.jqGrid('setGridWidth', p_width);
	}
}
Responsive_Group_Users();

//on window resize -> resize grids
$(window).on('resize', function() {
	Responsive_Group_Users();
}).trigger('resize');


} //end if (group_users)

}); //end jQuery(document).ready