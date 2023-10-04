"use strict";

// Sports Grid

var $sports = false;

jQuery(function() 
{

const grid_width_Max = 700;
const LS = LANG.SPORTS;
const idPrefix = 'sp_';
const pager = '#SPpager';
const sports_url = 'php/ajax.php?i=sports';
$sports = $("#sports");
if ($sports) 
{

//sports ###############################
$sports.jqGrid({  
	url: sports_url,
	editurl: sports_url,
	idPrefix: idPrefix,
	loadonce: true,
	hiddengrid: true,
	caption: LS.HEADER,
	pager: pager,
	pgtext: '',
	navOptions: { reloadGridOptions: { fromServer: true } },
	colNames:['', LANG.ID, LS.OPTIONGROUP, LANG.STATUS, LANG.CREATED, LANG.MODIFIED],
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
		const subgrid_table_id = subgrid_id+"_t"; 
		const sub_pager = "p_"+subgrid_table_id;
		$("#"+subgrid_id).html("<table id='"+subgrid_table_id+"' class='scroll'></table><div id='"+sub_pager+"' class='scroll'></div>");
		const sports_group_id = $sports.jqGrid('getRowData', row_id)['id'];
		const sports_options_url = "php/ajax.php?i=sports&oper=options&ID="+sports_group_id;
		const sub_idPrefix = "sop_";
		
		$("#"+subgrid_table_id).jqGrid({
			url: sports_options_url,
			editurl: sports_options_url,
			idPrefix: sub_idPrefix,
			pager: sub_pager,
			pgtext: '',
			colNames:['', LANG.ID, LS.PARENT_ID, LS.OPTIONS, LANG.STATUS, LANG.CREATED, LANG.MODIFIED],
			colModel:[
				{ //inline editing buttons and options
					name: 'acc', width:22, fixed:true, sortable:false, editable:false, search: false, resizable:false, formatter:'actions', 
					formatoptions:{
						keys:true,
						delbutton:false,
						editformbutton: true
					}
				},
				{name:'id',key:true,width:30, sorttype:'int',  align:"center", editoptions:{readonly:'readonly'} },
				{name:'parent_id',width:100, align:"center", hidden:true, editrules:{edithidden:true},
						formatter:"select", edittype:"select", stype:'select',
						searchoptions: {sopt:['cn','eq','ne'], value:V_SPORTS_GROUPS_OPTIONS},	
						editoptions:{value:V_SPORTS_GROUPS_OPTIONS, size:1, dataUrl:'php/ajax.php?i=sports&oper=sports_groups_select'},
						selectFilled:function(options) {
							$(options.elem).chosen({width:'100%', placeholder_text_multiple: ' ', no_results_text: LANG.NO_RESULTS});
						}
				},
				{name:'options', 	width:225},
				{name:'status', 	width:50, template: aktivInaktivTemplate },
				{name:'created', 	width:45, hidden:V_is_Index_Options, editoptions:{readonly:'readonly'} },
				{name:'modified', 	width:45, hidden:V_is_Index_Options, editoptions:{readonly:'readonly'} }
			]
	
		})
		.jqGrid('navGrid',"#"+sub_pager,{
			iconsOverText: V_is_Index_Options,
			edit:false, 	edittext: LANG.BUTTON_EDIT,
			add:!V_GROUP_ADMIN_2, addtext: LANG.BUTTON_ADD,
			del:!V_GROUP_ADMIN_2, deltext: LANG.BUTTON_DELETE,
			search:false, 	searchtext: LANG.BUTTON_SEARCH,
			view:true, 		viewtext: LANG.BUTTON_VIEW,
			refresh:true, 	refreshtext: LANG.BUTTON_RELOAD
		});
		
		$('#gbox_'+subgrid_table_id).removeClass('ui-corner-all');
		$('#'+sub_pager).removeClass('ui-corner-bottom');
		//set subgrid width
		$('#'+subgrid_table_id).jqGrid('setGridWidth', $sports.width()-27); //30
		
	} //end subGridRowExpanded options
	
}) //end $sports.jqGrid({  
//bottom bar
.jqGrid('navGrid',pager,{
	iconsOverText: V_is_Index_Options,
	edit: false,	edittext: LANG.BUTTON_EDIT,
	add: !V_GROUP_ADMIN_2, addtext: LANG.BUTTON_ADD,
	del: !V_GROUP_ADMIN_2, deltext: LANG.BUTTON_DELETE,
	search: false, 	searchtext: LANG.BUTTON_SEARCH,
	view: true, 	viewtext: LANG.BUTTON_VIEW,
	refresh: true, 	refreshtext: LANG.BUTTON_RELOAD
});


$(pager).children().children().css('table-layout', 'auto'); //fix pager width

//set Caption from table title/alt
$sports.jqGrid('setCaption', $sports.attr('alt'))
//center Caption and change font-size
.closest("div.ui-jqgrid-view")
	.children("div.ui-jqgrid-titlebar").css({"text-align":"center", "cursor":"pointer"})
	.children("span.ui-jqgrid-title").css({"float":"none", "font-size": "17px"});

//Expand/Collapse grid from Caption click
$($sports[0].grid.cDiv).on('click',function() {
	if ($(pager).is(':hidden')) 
		$(this).removeClass('ui-corner-all');
	else $(this).addClass('ui-corner-all');
	$(".ui-jqgrid-titlebar-close",this).trigger("click");	
}).addClass('ui-corner-all');


function Responsive_Sports() { 
	if (V_is_Index_Options) {
		let p_width = $('#C_Sports_Dropdowns_link').prop('clientWidth');// Get width of parent container
		if (p_width == null || p_width < 1){
			p_width = $('#C_Sports_Dropdowns_link').prop('offsetWidth'); // For IE, revert to offsetWidth if necessary
		}
		p_width = p_width - 2; //prevent horizontal scrollbars
		
		//set here anyway to avoid empty grid after (calendar, options) change
		$sports.jqGrid('setGridWidth', p_width);
		if (p_width != $sports.width()) {
			//$sports.jqGrid('setGridWidth', p_width);
		
			//if have subs opened
			if ($("div[id^=sports_sp_]").length > 0) { 
				$("div[id^=sports_sp_]").each(function(){
					$('#'+this.id+'_t').jqGrid('setGridWidth', $sports.width()-27); //30
				});
			}
		}
	}
	else {
		if (grid_width_Max > $(window).width()) {
			$sports.jqGrid('setGridWidth', $(window).width()-30);
		} else {
			$sports.jqGrid('setGridWidth', grid_width_Max);
		}
		if ($("div[id^=sports_sp_]").length > 0) { 
			$("div[id^=sports_sp_]").each(function(){
				$('#'+this.id+'_t').jqGrid('setGridWidth', $sports.width()-27); //30
			});
		}

	}
}
Responsive_Sports();


//on window resize -> resize grids
$(window).on('resize', function() {
	Responsive_Sports();
}).trigger('resize');


} //end if (sports)

}); //end jQuery(document).ready
