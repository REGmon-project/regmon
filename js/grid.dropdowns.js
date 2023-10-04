"use strict";

// Dropdowns Grid

var $dropdowns = false;

jQuery(function() 
{

const grid_width_Max = 700;
const LD = LANG.DROPDOWN;
const idPrefix = 'd_';
const pager = '#Dpager';
const dropdowns_url = 'php/ajax.php?i=dropdowns';
$dropdowns = $("#dropdowns");
if ($dropdowns) 
{

//dropdowns ###############################
$dropdowns.jqGrid({  
	url: dropdowns_url,
	editurl: dropdowns_url,
	idPrefix: idPrefix,
	loadonce: true,
	hiddengrid: true,
	caption: LD.HEADER,
	pager: pager,
	pgtext: '',
	navOptions: { reloadGridOptions: { fromServer: true } },
	colNames:['', LANG.ID, LD.NAME, LANG.STATUS, LANG.CREATED, LANG.MODIFIED],
	colModel:[
		{ //inline editing buttons and options
			name: 'acc', hidden:(V_GROUP_ADMIN_2?true:false), width:22, fixed:true, sortable:false, editable:false, search: false, resizable:false, formatter:'actions', 
			formatoptions:{
				keys:true,
				delbutton:false,
				editformbutton: true
			}
		},
		{name:'id',key:true,width:30, sorttype:"int", align:"center", editoptions:{readonly:'readonly'} },
		{name:'name', 		width:250, editrules:{required:true} },
		{name:'status', 	width:50, template: aktivInaktivTemplate },
		{name:'created', 	width:45, hidden:V_is_Index_Options, editoptions:{readonly:'readonly'} },
		{name:'modified', 	width:45, hidden:V_is_Index_Options, editoptions:{readonly:'readonly'} }
	],
	subGrid: true,
    subGridOptions: { 
        selectOnExpand: true,
	},
	
	//options #############################
	subGridRowExpanded: function(subgrid_id, row_id) {
		const subgrid_table_id = subgrid_id + "_t";
		const sub_pager = "p_" + subgrid_table_id;
		
		$("#" + subgrid_id).html(
			'<table id="' + subgrid_table_id + '" class="scroll"></table>'+
			'<div id="' + sub_pager + '" class="scroll"></div>'
		);

		const dropdown_id = $dropdowns.jqGrid('getRowData', row_id)['id'];
		const dropdowns_options_url = "php/ajax.php?i=dropdowns&oper=options&ID=" + dropdown_id;
		const sub_idPrefix = "op_";
		
		$("#"+subgrid_table_id).jqGrid({
			url: dropdowns_options_url,
			editurl: dropdowns_options_url,
			idPrefix: sub_idPrefix,
			pager: sub_pager,
			pgtext: '',
			colNames:['', LANG.ID, LD.PARENT_ID, LD.OPTIONS, LANG.STATUS, LANG.CREATED, LANG.MODIFIED],
			colModel:[
				{ //inline editing buttons and options
					name: 'acc', hidden:(V_GROUP_ADMIN_2?true:false), width:22, fixed:true, sortable:false, editable:false, search: false, resizable:false, formatter:'actions', 
					formatoptions:{
						keys:true,
						delbutton:false,
						editformbutton: true
					}
				},
				{name:'id',key:true,width:30,  align:"center", sorttype:'int', editoptions:{readonly:'readonly'} },
				{name:'parent_id',	width:100, align:"center", hidden:true, editrules:{edithidden:true}, editoptions:{readonly:'readonly'}},
				{name:'options', 	width:225, editoptions:{placeholder:LD.OPTION_PLACEHOLDER} },
				{name:'status', 	width:50, template: aktivInaktivTemplate },
				{name:'created', 	width:45, hidden:V_is_Index_Options, editoptions:{readonly:'readonly'} },
				{name:'modified', 	width:45, hidden:V_is_Index_Options, editoptions:{readonly:'readonly'} }
			]
	
		}) //$("#"+subgrid_table_id).jqGrid({ Options
		.jqGrid('navGrid',"#"+sub_pager,{
			iconsOverText: V_is_Index_Options,
			edit:false, 	edittext: LANG.BUTTON_EDIT,
			add:!V_GROUP_ADMIN_2, addtext: LANG.BUTTON_ADD,
			del:!V_GROUP_ADMIN_2, deltext: LANG.BUTTON_DELETE,
			search:false, 	searchtext: LANG.BUTTON_SEARCH,
			view:true, 		viewtext: LANG.BUTTON_VIEW,
			refresh:true, 	refreshtext: LANG.BUTTON_RELOAD
		});
		
		$('#gbox_' + subgrid_table_id).removeClass('ui-corner-all');
		$('#' + sub_pager).removeClass('ui-corner-bottom');
		
		//set subgrid width
		$('#' + subgrid_table_id).jqGrid('setGridWidth', $dropdowns.width() - 27); //30
		
	} //end subGridRowExpanded options
	
}) //end $dropdowns.jqGrid({  
//bottom bar
.jqGrid('navGrid',pager,{
	iconsOverText: V_is_Index_Options,
	edit: false,	edittext: LANG.BUTTON_EDIT,
	add: !V_GROUP_ADMIN_2, addtext: LANG.BUTTON_ADD,
	del: !V_GROUP_ADMIN_2, deltext: LANG.BUTTON_DELETE,
	search: false, 	searchtext: LANG.BUTTON_SEARCH,
	view: true, 	viewtext: LANG.BUTTON_VIEW,
	refresh: true, 	refreshtext: LANG.BUTTON_RELOAD,
	reloadGridOptions: { fromServer: true }
});


$(pager).children().children().css('table-layout', 'auto'); //fix pager width

//set Caption from table title/alt
$dropdowns.jqGrid('setCaption', $dropdowns.attr('alt'))
//center Caption and change font-size
.closest("div.ui-jqgrid-view")
	.children("div.ui-jqgrid-titlebar").css({"text-align":"center", "cursor":"pointer"})
	.children("span.ui-jqgrid-title").css({"float":"none", "font-size": "17px"});

//Expand/Collapse grid from Caption click
$($dropdowns[0].grid.cDiv).on('click',function() {
	if ($(pager).is(':hidden')) 
		$(this).removeClass('ui-corner-all');
	else $(this).addClass('ui-corner-all');
	$(".ui-jqgrid-titlebar-close",this).trigger("click");	
}).addClass('ui-corner-all');


function Responsive_Dropdown() { 
	if (V_is_Index_Options) {
		let p_width = $('#C_Sports_Dropdowns_link').prop('clientWidth');// Get width of parent container
		if (p_width == null || p_width < 1){
			p_width = $('#C_Sports_Dropdowns_link').prop('offsetWidth'); // For IE, revert to offsetWidth if necessary
		}
		p_width = p_width - 2; //prevent horizontal scrollbars
		
		//set here anyway to avoid empty grid after (calendar, options) change
		$dropdowns.jqGrid('setGridWidth', p_width);
		if (p_width != $dropdowns.width()) {
			//$dropdowns.jqGrid('setGridWidth', p_width);
		
			//if have subs opened
			if ($("div[id^=dropdowns_d_]").length > 0) { 
				$("div[id^=dropdowns_d_]").each(function(){
					$('#'+this.id+'_t').jqGrid('setGridWidth', $dropdowns.width()-27); //30
				});
			}
		}
	}
	else {
		if (grid_width_Max > $(window).width()) {
			$dropdowns.jqGrid('setGridWidth', $(window).width()-30);
		} else {
			$dropdowns.jqGrid('setGridWidth', grid_width_Max);
		}
		if ($("div[id^=dropdowns_d_]").length > 0) { 
			$("div[id^=dropdowns_d_]").each(function(){
				$('#'+this.id+'_t').jqGrid('setGridWidth', $dropdowns.width()-30);
			});
		}
	}
}
Responsive_Dropdown();


//on window resize -> resize grids
$(window).on('resize', function() {
	Responsive_Dropdown();
}).trigger('resize');


} //end if (dropdowns)

}); //end jQuery(document).ready