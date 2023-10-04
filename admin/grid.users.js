"use strict";

// Users Grid

var $users = false;

jQuery(function() 
{

const grid_width_Max = 1400;
const LU = LANG.USERS;
const USER_LVL_OPTIONS = "10:"+LU.LVL_ATHLETE+";30:"+LU.LVL_TRAINER+";40:"+LU.LVL_GROUP_ADMIN_2+";45:"+LU.LVL_GROUP_ADMIN+";50:"+LU.LVL_LOCATION+";99:"+LU.LVL_ADMIN;

const idPrefix = "u_";
const pager = "#Upager";
$users = $("#users");
if ($users) 
{

//Users ###############################
$users.jqGrid({  
	url: 'php/ajax.php?i=users',
	editurl: "php/ajax.php?i=users",
	datatype: "json",
	idPrefix: idPrefix,
	hiddengrid: true, //to start closed without loading data
	loadonce: true,
	pager: pager,
	caption: LU.HEADER,
	pgbuttons: true,
	rowNum: 10,
	rowList:[10,25,50,100,200,500,'999999:Alle'],
	viewrecords: true, //view 1 - 1 of 10
	headertitles:true,
	cmTemplate: { editoptions:{size:22}, editable:true },
	colNames:['', LANG.ID, LU.USERNAME, LU.PASSWORD, LU.PASS_CONFIRM, LU.LOCATION, LU.GROUP, LU.FIRSTNAME, LU.LASTNAME, LU.BIRTH_DATE, LU.SPORT, LU.SEX, LU.BODY_HEIGHT, LU.EMAIL, LU.TELEPHONE, LANG.STATUS, LU.LEVEL, LU.LAST_LOGIN, LU.LOGIN_COUNT, LU.LAST_IP, LANG.CREATED, LANG.MODIFIED, LU.ERGEBNISSE],
	colModel:[
		{ //inline editing buttons and options
			name: 'acc', width:22, fixed:true, sortable:false, editable:false, search: false, resizable:false, formatter:'actions', 
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
		{name:'id',key:true,width:35, sorttype:"int", align:"center", editoptions:{readonly:'readonly'} },
		{name:'uname', 		width:80, 	/*editoptions:{readonly:'readonly'}*/ },
		{name:'passwd', 	width:50, 	edittype:'password', hidden:true, editrules:{edithidden:true} },
		{name:'pass_confirm',width:50, 	edittype:'password', hidden:true, editrules:{edithidden:true} },
		{name:'location_id', width:100, 	hidden:true, editrules:{edithidden:true},
				formatter:"select", edittype:"select", stype:'select',
				searchoptions: {sopt:['cn','eq','ne'], value:V_LOCATIONS_OPTIONS},	
				editoptions:{value:V_LOCATIONS_OPTIONS, disabled:true, size:1, dataUrl:'php/ajax.php?i=locations&oper=locations_select'}
		},
		{name:'group_id', 	width:230, 	hidden:true, editrules:{edithidden:true},
				formatter:"select", edittype:"select", stype:'select',
				searchoptions: {sopt:['cn','eq','ne'], value:V_GROUPS_OPTIONS},	
				editoptions:{value:V_GROUPS_OPTIONS, disabled:true, size:1, dataUrl:'php/ajax.php?i=groups&oper=groups_select'}
		},
		{name:'firstname', 	width:100},
		{name:'lastname', 	width:100},
		{name:'birth_date',	width:70, align:"right", 
			sorttype:"date", formatter:"date", formatoptions:{srcformat:"Y-m-d", newformat:LANG.GRID.DATE},
			editoptions:{ dataInit:initDate, style:"width:70%" },
			searchoptions: { sopt: ['cn', 'eq', 'ne', 'lt', 'le', 'gt', 'ge'] }
		},
		{name:'sport',		width:80,
				formatter:"select", edittype:"select", stype:'select',
				searchoptions: {sopt:['cn','eq','ne'], value:V_SPORTS_OPTIONS},	
				editoptions:{
					value:V_SPORTS_OPTIONS, multiple:true, size:1, dataUrl:'php/ajax.php?i=sports&oper=sports_select', 
					selectFilled:function(options) {
						$(options.elem).chosen({width:'100%', placeholder_text_multiple: ' ', no_results_text: LANG.NO_RESULTS});
					}
				}
		},
		{name:'sex', 		width:60, template: sexTemplate},
		{name:'body_height',width:60,
				formatter:"select", edittype:"select", align:"center",
				editoptions:{value: V_BODY_HEIGHT_OPTIONS, size:1}
		},
		{name:'email', 		width:130},
		{name:'telephone', 	width:80},
		{name:'status', 	width:50, template: aktivInaktivTemplate },
		{name:'level', 		width:85, align:"center",
				formatter:"select", edittype:"select",
				editoptions:{value: USER_LVL_OPTIONS, defaultValue:LU.LVL_ATHLETE, size:1},
				stype:'select', searchoptions: {sopt:['eq', 'ne'], value:":;"+USER_LVL_OPTIONS},
		},
		{name:'lastlogin', 	width:64, editoptions:{readonly:'readonly'},
				sorttype:"date", formatter:"date", formatoptions:{srcformat:"Y-m-d H:i:s", newformat:LANG.GRID.DATE_TIME}
		},
		{name:'logincount', width:40, editoptions:{readonly:'readonly'}, align:"right" },
		{name:'last_ip', 	width:80, hidden:true, editoptions:{readonly:'readonly'}, align:"right" },
		{name:'created', 	width:64, hidden:true, editrules:{edithidden:true}, editoptions:{readonly:'readonly'} },
		{name:'modified', 	width:64, hidden:true, editrules:{edithidden:true}, editoptions:{readonly:'readonly'} },
		{name:'results', 	width:50, fixed:true, editable:false}
	]
}) //$users.jqGrid({ end
//Column Search
.jqGrid('filterToolbar',{
	stringResult:true, //send as Json //filters
	searchOnEnter:false,
	defaultSearch: 'cn'
})
//bottom bar
.jqGrid('navGrid',pager,{
	edit: false,	edittext: LANG.BUTTON_EDIT,
	add: true, 		addtext: LANG.BUTTON_ADD,
	del: true, 		deltext: LANG.BUTTON_DELETE,
	search: false, 	searchtext: LANG.BUTTON_SEARCH,
	view: true, 	viewtext: LANG.BUTTON_VIEW,
	refresh: true, 	refreshtext: LANG.BUTTON_RELOAD
	//reloadGridOptions: { fromServer: true }
}); //pager


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


//User Groups
const users_Grouping = '<select id="users_Grouping" style="font-size:11px; float:left; margin:4px 0 0 21px; color:black; font-weight:normal; display:none;">\
<option value="">'+LANG.GROUPING_NO+'</option>\
<option value="location_id">'+LANG.GROUPING_BY+' '+LU.LOCATION+'</option>\
<option value="group_id">'+LANG.GROUPING_BY+' '+LU.GROUP+'</option>\
<option value="status">'+LANG.GROUPING_BY+' '+LANG.STATUS+'</option>\
<option value="sport">'+LANG.GROUPING_BY+' '+LU.SPORT+'</option>\
<option value="sex">'+LANG.GROUPING_BY+' '+LU.SEX+'</option>\
<option value="level">'+LANG.GROUPING_BY+' '+LU.LEVEL+'</option></select>';


//set Caption from table title/alt
$users.jqGrid('setCaption', $users.attr('alt') +' '+ users_Grouping)
//center Caption and change font-size
.closest("div.ui-jqgrid-view")
	.children("div.ui-jqgrid-titlebar").css({"text-align":"center", "cursor":"pointer"})
	.children("span.ui-jqgrid-title").css({"float":"none", "font-size": "17px"});

//Expand/Collapse grid from Caption click
$($users[0].grid.cDiv).on('click',function(e) {
	if (e.target.id == 'UserGrouping') return false; //stop trigger caption click when click on UserGrouping select
	if ($(pager).is(':hidden')) 
		$(this).removeClass('ui-corner-all');
	else $(this).addClass('ui-corner-all');
	$("#gview_users .ui-jqgrid-titlebar-close").trigger("click");	
}).addClass('ui-corner-all');

$("#gview_users .ui-jqgrid-titlebar-close").on('click',function() {
	$('#UserGrouping').toggle();
});

//User Grouping
$("#UserGrouping").on('change', function() {
	const groupingName = $(this).val();
	if (groupingName) {
		$users.jqGrid('groupingGroupBy', groupingName, {
			groupText : [' {0} <b>( {1} )</b>'],
			groupOrder : ['asc'],
			groupColumnShow: [true],
			groupCollapse: true
		});
	} else {
		$users.jqGrid('groupingRemove');
	}
	$users.jqGrid('hideCol',["location_id","group_id"]); //this hidden columns became visible after the grouping
	return false;
});

$(pager).children().children().css('table-layout', 'auto'); //fix pager width


function Responsive_Users() { 
	if (grid_width_Max > $(window).width()) {
		$users.jqGrid('setGridWidth', $(window).width()-30);
	} else {
		$users.jqGrid('setGridWidth', grid_width_Max);
	}
}
Responsive_Users();

//on window resize -> resize grids
$(window).on('resize', function() {
	Responsive_Users();
}).trigger('resize');


} //end if (users)

}); //end jQuery(document).ready