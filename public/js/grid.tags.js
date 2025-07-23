"use strict";

// Tags Grid

var $tags = false;

jQuery(function()
{

const grid_width_Max = 800;
const LT = LANG.TAGS;

const idPrefix = "t_";
const pager = '#Tpager';
const url = 'php/ajax.php?i=tags';
$tags = $("#tags");
if ($tags) 
{

//tags ###############################
$tags.jqGrid({  
	url: url,
	editurl: url,
	idPrefix: idPrefix,
	sortname: 'name',
	hiddengrid: true,
	loadonce: true,
	pager: pager,
	pgtext: '',
	caption: LT.HEADER,
	colNames:['', LANG.ID, LT.NAME, LANG.STATUS, LANG.CREATED, LANG.CREATED_BY, LANG.MODIFIED, LANG.MODIFIED_BY],
	colModel:[
		{ //inline editing buttons and options
			name: 'acc', width:22, fixed:true, sortable:false, editable:false, search: false, resizable:false, formatter:'actions', 
			formatoptions:{
				keys:true,
				delbutton:false,
				editformbutton: true
			}
		},
		{name:'id',key:true,width:30,  align:"center", sorttype:"int", editoptions:{readonly:'readonly'} },
		{name:'name', 		width:200},
		{name:'status', 	width:50, template: aktivInaktivTemplate },
		{name:'created',	width:40, hidden:V_is_Index_Options, editrules:{edithidden:true}, editoptions:{readonly:'readonly'} },
		{name:'created_by', width:50, hidden:V_is_Index_Options, editrules:{edithidden:true}, editoptions:{readonly:'readonly'}, align:"center" },
		{name:'modified', 	width:40, hidden:V_is_Index_Options, editrules:{edithidden:true}, editoptions:{readonly:'readonly'} },
		{name:'modified_by',width:50, hidden:V_is_Index_Options, editrules:{edithidden:true}, editoptions:{readonly:'readonly'}, align:"center" }
	],
}) //end $tags.jqGrid({ 
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
	refresh: true, 	refreshtext: LANG.BUTTON_RELOAD,
	reloadGridOptions: { fromServer: true }
});


//set Caption from table title/alt
$tags.jqGrid('setCaption', $tags.attr('alt'))
//center Caption and change font-size
.closest("div.ui-jqgrid-view")
	.children("div.ui-jqgrid-titlebar").css({"text-align":"center", "cursor":"pointer"})
	.children("span.ui-jqgrid-title").css({"float":"none", "font-size": "17px"});

//Expand/Collapse grid from Caption click
$($tags[0].grid.cDiv).on('click',function() {
	if ($(pager).is(':hidden')) 
		$(this).removeClass('ui-corner-all');
	else $(this).addClass('ui-corner-all');
	$(".ui-jqgrid-titlebar-close",this).trigger("click");	
}).addClass('ui-corner-all');

$(pager).children().children().css('table-layout', 'auto'); //fix pager width


//on window resize -> resize grids
$(window).on('resize', function() {
	if (grid_width_Max > $(window).width()) {
		$tags.jqGrid('setGridWidth', $(window).width()-30);
	} else {
		$tags.jqGrid('setGridWidth', grid_width_Max);
	}
}).trigger('resize');


} //end if (tags)

}); //end jQuery(document).ready