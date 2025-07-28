"use strict";

// Templates 2 Grid

jQuery(function() 
{

const LT = LANG.TEMPLATES;
const LP = LANG.PERMISSIONS;
const grid_width_Max = 900; //1200
const idPrefix = "t_";
const pager = '#TRpager';
let header = LANG.RESULTS.DASH_RESULTS_TEMPLATE_NAME;
let $templates_results = $("#templates_results");
if ($templates_results)
{

//templates_results ###############################
$templates_results.jqGrid({
	url: 'php/ajax.php?i=templates&oper=templates_results',
	editurl: "php/ajax.php?i=templates&oper=edit&template_type=results",
	datatype: "json",
	idPrefix: idPrefix,
	hiddengrid: true, //to start closed without loading data
	loadonce: true,
	caption: header,
	pager: pager,
	pgtext: '',
	altRows: true, //for zebra
	gridview: true,
	sortname: 'name',
	viewrecords: true, //view 1 - 1 of 10
	headertitles:true,
	cmTemplate: { editoptions:{size:22}, editable:true },
	colNames:['', LT.TEMPLATE_ID, LT.TEMPLATE_NAME, LT.USER_ID, LT.LOCATION_ID, LT.GROUP_ID, /*LP.GLOBAL_VIEW, LP.GLOBAL_EDIT, LP.LOCATION_VIEW, LP.GROUP_VIEW, LP.TRAINER_VIEW, LP.PRIVATE,*/ LANG.CREATED, LANG.CREATED_BY, LANG.MODIFIED, LANG.MODIFIED_BY],
	colModel:[
		{ //inline editing buttons and options
			name:'acc', hidden:(V_GROUP_ADMIN_2?true:false), width:22, fixed:true, sortable:false, editable:false, search: false, resizable:false, formatter:'actions', 
			formatoptions:{
				keys:true,
				delbutton:false,
				editformbutton:true,
				editOptions: {
					width: 500,
					beforeShowForm: function(form) {
						$('.ui-jqdialog-content .FormData td.CaptionTD').each(function(){
							$(this).html($(this).html().replace('<br>',' ')); //replace <br> with ' ' in CaptionTD
						});
						//Permissions Select
						init_Permissions();
					}
				}
			}
		},
		{name:'id',key:true, 	formoptions:{colpos:1, rowpos:1}, width:32, align:"center", sorttype:"int", editoptions:{readonly:'readonly'} },
		{name:'templatename', 	formoptions:{colpos:2, rowpos:1},width:250},
		{name:'user_id', 		formoptions:{colpos:1, rowpos:3}, width:50, align:"center"},
		{name:'location_id', 	formoptions:{colpos:1, rowpos:4}, width:50, align:"center"},
		{name:'group_id', 		formoptions:{colpos:1, rowpos:5}, width:50, align:"center"},
		// {name:"GlobalView", 		formoptions:{colpos:2, rowpos:2}, width:30, template: checkboxTemplate},
		// {name:"LocationView", 	formoptions:{colpos:2, rowpos:4}, width:30, template: checkboxTemplate},
		// {name:"GroupView", 		formoptions:{colpos:2, rowpos:6}, width:30, template: checkboxTemplate},
		// {name:"TrainerView", 	formoptions:{colpos:2, rowpos:8}, width:30, template: checkboxTemplate},
		// {name:"Private", 		formoptions:{colpos:2, rowpos:10}, width:30, template: checkboxTemplate},
		{name:'created', 		formoptions:{colpos:1, rowpos:7}, width:65, template: hiddenReadonlyTemplate},
		{name:'created_by', 	formoptions:{colpos:1, rowpos:8}, width:80, template: hiddenReadonlyTemplate, align:"center", hidden:false},
		{name:'modified', 		formoptions:{colpos:1, rowpos:9}, width:65, template: hiddenReadonlyTemplate},
		{name:'modified_by', 	formoptions:{colpos:1, rowpos:10}, width:80, template: hiddenReadonlyTemplate, align:"center", hidden:false},
	]
}) //end $templates_results.jqGrid({
//Column Search
.jqGrid('filterToolbar',{
	stringResult:true, //send as Json //filters
	searchOnEnter:false,
	defaultSearch: 'cn'
})
//bottom bar
.jqGrid('navGrid',pager,{
	iconsOverText: true,
	edit:false, edittext: LANG.BUTTON_EDIT,
	add:false/* !V_GROUP_ADMIN_2*/, addtext: LANG.BUTTON_ADD,
	del:false/*V_ADMIN*/, deltext: LANG.BUTTON_DELETE,
	search:false, searchtext: LANG.BUTTON_SEARCH,
	view:false, viewtext: LANG.BUTTON_VIEW,
	refresh:true, refreshtext: LANG.BUTTON_RELOAD,
	reloadGridOptions: { fromServer: true }
})
//duplicate button
.jqGrid("navButtonAdd", pager, {
	iconsOverText: true,
	buttonicon: "fa-copy",
	caption: LANG.BUTTON_DUPLICATE,
	title: LANG.BUTTON_DUPLICATE,
	onClickButton: function () {
		const self = $(this);
		const selRowId = self.jqGrid('getGridParam', 'selrow');
		if (selRowId != null) {
			const template_id = self.jqGrid('getCell', selRowId, 'id');
			$.ajax({url:'php/ajax.php?i=templates&oper=template_duplicate&ID='+template_id+'&template_type=results', success:function(data, result) {
				self.trigger("reloadGrid", { fromServer: true });
			}});
		}
		else {
			this.modalAlert();
		}
	}
});


$(pager).children().children().css('table-layout', 'auto'); //fix pager width

//Axis Groups
const templates_results_groups = '<select id="templates_results_Grouping" style="font-size:11px; float:left; margin:4px 0 0 21px; color:black; font-weight:normal; display:none;">\
<option value="">'+LANG.GROUPING_NO+'</option>\
<option value="user_id">'+LANG.GROUPING_BY+' '+LT.USER_ID+'</option>\
<option value="location_id">'+LANG.GROUPING_BY+' '+LT.LOCATION_ID+'</option>\
<option value="group_id">'+LANG.GROUPING_BY+' '+LT.GROUP_ID+'</option>\
<option value="created_by">'+LANG.GROUPING_BY+' '+LANG.CREATED_BY+'</option>\
<option value="modified_by">'+LANG.GROUPING_BY+' '+LANG.MODIFIED_BY+'</option></select>';


//set Caption from table title/alt
$templates_results.jqGrid('setCaption', $templates_results.attr('alt') +' '+ templates_results_groups)
.closest("div.ui-jqgrid-view") //center Caption and change font-size
	.children("div.ui-jqgrid-titlebar").css({"text-align":"center", "cursor":"pointer"})
	.children("span.ui-jqgrid-title").css({"float":"none", "font-size": "17px"});

//Expand/Collapse grid from Caption click
$($templates_results[0].grid.cDiv).on('click',function(e) {
	if (e.target.id == 'templates_results_Grouping') {
		//stop trigger caption click when click on UserGrouping select
		return false;
	}
	if ($(pager).is(':hidden')) {
		$(this).removeClass('ui-corner-all');
	} else {
		$(this).addClass('ui-corner-all');
	}
	$("#gview_templates_results .ui-jqgrid-titlebar-close").trigger("click");	
}).addClass('ui-corner-all');

$("#gview_templates_results .ui-jqgrid-titlebar-close").on('click',function() {
	$('#templates_results_Grouping').toggle();
});

//Templates Grouping
$("#templates_results_Grouping").on('change', function() {
	const groupingName = $(this).val();
	if (groupingName) {
		$templates_results.jqGrid('groupingGroupBy', groupingName, {
			groupText : [' {0} <b>( {1} )</b>'],
			groupOrder : ['asc'],
			groupColumnShow: [true],
			groupCollapse: true
		});
	} else {
		$templates_results.jqGrid('groupingRemove');
	}
	return false;
});


//on window resize -> resize grids
$(window).on('resize', function() {
	//main grid
	if (grid_width_Max > $(window).width()) {
		$templates_results.jqGrid('setGridWidth', $(window).width()-30);
	} else {
		$templates_results.jqGrid('setGridWidth', grid_width_Max);
	}
}).trigger('resize');


   
} //end if (templates_results)

}); //end jQuery(document).ready