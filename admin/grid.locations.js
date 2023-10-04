"use strict";

// Locations Grid

var $locations = false;

jQuery(function() 
{

const grid_width_Max = 1050;
const LL = LANG.LOCATIONS;
const LG = LANG.GROUPS;

const idPrefix = "loc_";
const pager = '#Lpager';
$locations = $("#locations");
if ($locations)
{

//Locations ###############################
$locations.jqGrid({
	url: 'php/ajax.php?i=locations',
	editurl: "php/ajax.php?i=locations",
	datatype: "json",
	idPrefix: idPrefix,
	iconSet: 'fontAwesome',
	hiddengrid: true, //to start closed without loading data
	loadonce: true,
	pager: pager,
	pgtext: '',
	caption: LL.HEADER,
	colNames:['', LANG.ID, LL.NAME, LANG.STATUS, LL.ADMIN, LANG.CREATED, LANG.MODIFIED],
	colModel:[
		{ //inline editing buttons and options
			name: 'acc', width:22, fixed:true, sortable:false, editable:false, search: false, resizable:false, formatter:'actions', 
			formatoptions:{
				keys:true,
				delbutton:false,
				editformbutton:true
			}
		},
		{name:'id',key:true,width:30,  align:"center", sorttype:"int", editoptions:{readonly:'readonly'} },
		{name:'name', 		width:250},
		{name:'status', 	width:50, template: aktivInaktivTemplate },
		{name:'admin_id', 	width:150, align:"center",
				formatter:"select", edittype:"select", stype:'select',
				searchoptions: {sopt:['cn','eq','ne'], value:V_LOCATIONS_ADMINS_OPTIONS},	
				editoptions:{
					value:V_LOCATIONS_ADMINS_OPTIONS, size:1, dataUrl:'php/ajax.php?i=locations&oper=locations_admins_select',
					selectFilled:function(options) {
						$(options.elem).chosen({width:'100%', placeholder_text_multiple: ' ', no_results_text: LANG.NO_RESULTS, disable_search_threshold: 10});
					}
				}
		},
		{name:'created', 	formoptions:{colpos:1, rowpos:5}, width:64, editoptions:{readonly:'readonly'} },
		{name:'modified', 	formoptions:{colpos:1, rowpos:6}, width:64, editoptions:{readonly:'readonly'} }
	],
	subGrid: true,
    subGridOptions: { 
        selectOnExpand: true,
	},
	
	//Groups #############################
	subGridRowExpanded: function(subgrid_id, row_id)
	{
		const subgrid_table_id = subgrid_id+"_t"; 
		const sub_pager = "p_"+subgrid_table_id;
		const sub_idPrefix = "lg_";
		$("#" + subgrid_id).html(
			'<table id="' + subgrid_table_id + '" class="scroll"></table>'+
			'<div id="' + sub_pager + '" class="scroll"></div>'
		);
		const location_id = $locations.jqGrid('getRowData', row_id)['id'];
		const location_groups_url = "php/ajax.php?i=groups&ID="+location_id;
		
		$("#"+subgrid_table_id).jqGrid({
			url: location_groups_url,
			editurl: location_groups_url,
			idPrefix: sub_idPrefix,
			iconSet: 'fontAwesome',
			loadonce: true,
			pager: '#'+sub_pager,
			pgtext: '',
			colNames:['', LANG.ID, LG.LOCATION, LG.NAME, LANG.STATUS, LG.PRIVATE_KEY, LG.ADMIN, LG.STOP_DATE, LANG.CREATED, LANG.MODIFIED],
			colModel:[
				{ //inline editing buttons and options
					name: 'acc', width:22, fixed:true, sortable:false, editable:false, search: false, resizable:false, formatter:'actions', 
					formatoptions:{
						keys:true,
						delbutton:false,
						editformbutton:true
					}
				},
				{name:'id',key:true,width:30,  align:"center", sorttype:"int", editoptions:{readonly:'readonly'} },
				{name:'location_id',width:80, align:"center", hidden:true, editrules:{edithidden:true},
						formatter:"select", edittype:"select", stype:'select',
						searchoptions: {sopt:['cn','eq','ne'], value:V_LOCATIONS_OPTIONS},	
						editoptions:{
							value:V_LOCATIONS_OPTIONS, size:1, disabled:true, dataUrl:'php/ajax.php?i=locations&oper=locations_select', 
							selectFilled:function(options) {
								$(options.elem).chosen({width:'100%', placeholder_text_multiple: ' ', no_results_text: LANG.NO_RESULTS, disable_search_threshold: 10});
							}
						}
				},
				{name:'name', 		width:190},
				{name:'status', 	width:50, template: aktivInaktivPrivateTemplate},
				{name:'private_key',width:80},
				{name:'admins_id', 	width:130, align:"center", 
						formatter:"select", edittype:"select", stype:'select',
						searchoptions: {sopt:['cn','eq','ne'], value:V_GROUPS_ADMINS_OPTIONS},	
						editoptions:{
							value:V_GROUPS_ADMINS_OPTIONS, multiple:true, size:1, dataUrl:'php/ajax.php?i=groups&oper=groups_admins_select', 
							selectFilled:function(options) {
								$(options.elem).chosen({width:'100%', placeholder_text_multiple: ' ', no_results_text: LANG.NO_RESULTS});
							}
						}
				},
				{name:'stop_date',	width:64, align:"right", template: stopDateTemplate},
				{name:'created', 	width:64, editrules:{edithidden:true}, editoptions:{readonly:'readonly'} },
				{name:'modified', 	width:64, editrules:{edithidden:true}, editoptions:{readonly:'readonly'} }
			],
			subGrid: true,
			subGridOptions: { 
				selectOnExpand: true,
			},
			
			//FormsSelect #############################
			subGridRowExpanded: function(subgrid_id, row_id)
			{
				$("#loading").show();
				const group_grid = $("#"+subgrid_id.substring(0, subgrid_id.indexOf("_t")+2));
				const group_id = group_grid.jqGrid('getRowData', row_id)['id'];
				const subgrid_width = $("#"+subgrid_id).width();
				const subgrid_table_id = subgrid_id+"_t"; 
				$("#"+subgrid_id).html(
					'<div id="'+subgrid_table_id+'" class="anav" style="width:'+(subgrid_width-200)+'px; display:table-cell;"></div>'+
					'<div style="width:200px; display:table-cell; vertical-align:middle; text-align:center;">'+
						'<div style="width:200px; margin-bottom:50px;">'+
							LANG.FORMS.AVAILABLE+': <input type="checkbox" class="check_box" checked>'+
							'<br><br>'+
							LANG.FORMS.STANDARD+': <input type="checkbox" class="standard" checked>'+
						'</div>'+
						'<button type="button" id="forms_selection_save_g_'+group_id+'" class="save">'+
							LANG.BUTTON_SAVE+' &nbsp; '+
						'</button>'+
					'</div>'
				).css('display', 'table');
				

				$("#" + subgrid_table_id).load("forms/ajax.forms_menu.php", { group_id: group_id, edit: true }, function () {
					$('input.check_box').iCheck({ checkboxClass: 'icheckbox_flat-yellow2s' });
					$('input.standard').iCheck({ checkboxClass: 'icheckbox_polaris2' });
					$("#loading").hide();
				});
				
				//button Save FormsSelect Selection
				$("button#forms_selection_save_g_" + group_id).on('click', function () {
					$("#loading").show();
					const data = {
						admin: true, group_id: group_id,
						forms_select: $('input[name^="sel_g_' + group_id + '_"]').serialize(),
						forms_standard: $('input[name^="std_g_' + group_id + '_"]').serialize()
					};
					$.post('forms/ajax.forms_selection_save.php', data, function (data, result) {
						$("#loading").hide();
					});
				});

			} //end subGridRowExpanded FormsSelect

		}) //Groups end $("#"+subgrid_table_id).jqGrid({
		//Column Search
		.jqGrid('filterToolbar',{
			stringResult:true, //send as Json //filters
			searchOnEnter:false,
			defaultSearch: 'cn'
		})
		//bottom bar
		.jqGrid('navGrid','#'+sub_pager,{ 
			edit: false,	edittext: LANG.BUTTON_EDIT,
			add: true, 		addtext: LANG.BUTTON_ADD,
			del: false, 	deltext: LANG.BUTTON_DELETE,
			search: false, 	searchtext: LANG.BUTTON_SEARCH,
			view: true, 	viewtext: LANG.BUTTON_VIEW,
			refresh: true, 	refreshtext: LANG.BUTTON_RELOAD,
			reloadGridOptions: { fromServer: true }
		});
		
		$("#"+subgrid_id+" td.ui-search-clear").attr('style', 'width:11px;'); //show 'clear search' hidden in subgrid
		$('#gbox_'+subgrid_table_id).removeClass('ui-corner-all');
		$('#'+sub_pager).removeClass('ui-corner-bottom');
		$("#"+subgrid_id).parent().css('padding', '3px');
		
		if ($locations.width() < grid_width_Max) { //small screens
			$("#"+subgrid_table_id).jqGrid('hideCol',["created","modified"]); //this hidden columns became visible after the grouping
			$("#"+subgrid_table_id).jqGrid('setGridWidth', $locations.width()-30); //groups width
		} else {
			$("#"+subgrid_table_id).jqGrid('setGridWidth', (grid_width_Max-30)); //groups width
			$locations.jqGrid('setGridWidth', grid_width_Max); //locations width
		}
	} //end subGridRowExpanded

}) //end $locations.jqGrid({
//Column Search
.jqGrid('filterToolbar',{
	stringResult:true, //send as Json //filters
	searchOnEnter:false,
	defaultSearch: 'cn'
})
//bottom bar
.jqGrid('navGrid',pager,{
	edit: false,	edittext: LANG.BUTTON_EDIT,
	add: true, 	addtext: LANG.BUTTON_ADD,
	del: false, 	deltext: LANG.BUTTON_DELETE,
	search: false, 	searchtext: LANG.BUTTON_SEARCH,
	view: true, 	viewtext: LANG.BUTTON_VIEW,
	refresh: true, 	refreshtext: LANG.BUTTON_RELOAD,
	reloadGridOptions: { fromServer: true }
});


//set Caption from table title/alt
$locations.jqGrid('setCaption', $locations.attr('alt'))
//center Caption and change font-size
.closest("div.ui-jqgrid-view")
	.children("div.ui-jqgrid-titlebar").css({"text-align":"center", "cursor":"pointer"})
	.children("span.ui-jqgrid-title").css({"float":"none", "font-size": "17px"});

//Expand/Collapse grid from Caption click
$($locations[0].grid.cDiv).on('click',function() {
	if ($(pager).is(':hidden')) 
		$(this).removeClass('ui-corner-all');
	else $(this).addClass('ui-corner-all');
	$(".ui-jqgrid-titlebar-close",this).trigger("click");	
}).addClass('ui-corner-all');

$(pager).children().children().css('table-layout', 'auto'); //fix pager width
	
	
//on window resize -> resize grids
$(window).on('resize', function() {
	//main grid
	if (grid_width_Max > $(window).width() || (grid_width_Max+30) > $(window).width()) {
		$locations.jqGrid('setGridWidth', $(window).width()-30);
	}
	else {
		if ($("div[id^=locations_loc_]").length > 0) { //haven't close the last yet here so 1 instead of 0 
			$locations.jqGrid('setGridWidth', (grid_width_Max+30));
		} else {
			$locations.jqGrid('setGridWidth', grid_width_Max);
		}
	}
}).trigger('resize');

	
} //end if (locations)

}); //end jQuery(document).ready