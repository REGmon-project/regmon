"use strict";

// Groups Grid

var $groups = false;

jQuery(function() 
{

const grid_width_Max = 1000;
const LG = LANG.GROUPS;

const idPrefix = "g_";
const pager = '#Gpager';
$groups = $("#groups");
if ($groups)
{

//Groups ###############################
$groups.jqGrid({
	url: 'php/ajax.php?i=groups',
	editurl: "php/ajax.php?i=groups",
	datatype: "json",
	idPrefix: idPrefix,
	hiddengrid: true, //to start closed without loading data
	loadonce: true,
	pager: pager,
	pgtext: '',
	caption: LG.HEADER,
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
		{name:'location_id',width:100, align:"center",
				formatter:"select", edittype:"select", stype:'select',
				searchoptions: {sopt:['cn','eq','ne'], value:V_LOCATIONS_OPTIONS},	
				editoptions:{
					value:V_LOCATIONS_OPTIONS, size:1, dataUrl:'php/ajax.php?i=locations&oper=locations_select', 
					selectFilled:function(options) {
						$(options.elem).chosen({width:'100%', placeholder_text: ' ', no_results_text: LANG.NO_RESULTS, disable_search_threshold: 10});
						let disabled = false; //addForm
						if (options.mode == 'editForm') {
							disabled = true;
						}
						$(options.elem).prop('disabled', disabled).trigger("chosen:updated");
					}
				}
		},
		{name:'name', 		width:250},
		{name:'status', 	width:50, template: aktivInaktivPrivateTemplate },
		{name:'private_key',width:80},
		{name:'admins_id', 	width:150, align:"center",
				formatter:"select", edittype:"select", stype:'select',
				searchoptions: {sopt:['cn','eq','ne'], value:V_GROUPS_ADMINS_OPTIONS},	
				editoptions:{
					value:V_GROUPS_ADMINS_OPTIONS, multiple:true, size:1, dataUrl:'php/ajax.php?i=groups&oper=groups_admins_select', 
					selectFilled:function(options) {
						$(options.elem).chosen({width:'100%', placeholder_text_multiple: ' ', no_results_text: LANG.NO_RESULTS});
					}
				}
		},
		{name:'stop_date',	width:64, align:"right", template: stopDateTemplate },
		{name:'created', 	width:64, editoptions:{readonly:'readonly'} },
		{name:'modified', 	width:64, editoptions:{readonly:'readonly'} }
	],
	subGrid: true,
	subGridOptions: { 
		selectOnExpand: true,
	},
	
	//FormsSelect #############################
	subGridRowExpanded: function(subgrid_id, row_id)
	{
		$("#loading").show();
		const group_id = $groups.jqGrid('getRowData', row_id)['id'];
	
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
		
		$("#"+subgrid_table_id).load("forms/ajax.forms_menu.php", {group_id: group_id, edit: true}, function() {
			$('input.check_box').iCheck({checkboxClass: 'icheckbox_flat-yellow2s'});
			$('input.standard').iCheck({checkboxClass: 'icheckbox_polaris2'});
			$("#loading").hide();
		});
		
		//button Save FormsSelect Selection
		$("button#forms_selection_save_g_"+group_id).on('click',function() {
			$("#loading").show();
			const data = {
				admin: true, group_id: group_id,
				forms_select: $('input[name^="sel_g_' + group_id + '_"]').serialize(),
				forms_standard: $('input[name^="std_g_' + group_id + '_"]').serialize()
			};
			$.post('forms/ajax.forms_selection_save.php', data, function(data, result){ $("#loading").hide();/*console.log(data, result);*/});
		});

	} //end subGridRowExpanded FormsSelect
	
}) //end $groups.jqGrid({
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
	del: false, 	deltext: LANG.BUTTON_DELETE,
	search: false, 	searchtext: LANG.BUTTON_SEARCH,
	view: true, 	viewtext: LANG.BUTTON_VIEW,
	refresh: true, 	refreshtext: LANG.BUTTON_RELOAD,
	reloadGridOptions: { fromServer: true }
}); //pager


//set Caption from table title/alt
$groups.jqGrid('setCaption', $groups.attr('alt'))
//center Caption and change font-size
.closest("div.ui-jqgrid-view")
	.children("div.ui-jqgrid-titlebar").css({"text-align":"center", "cursor":"pointer"})
	.children("span.ui-jqgrid-title").css({"float":"none", "font-size": "17px"});

//Expand/Collapse grid from Caption click
$($groups[0].grid.cDiv).on('click',function() {
	if ($(pager).is(':hidden')) 
		$(this).removeClass('ui-corner-all');
	else $(this).addClass('ui-corner-all');
	$(".ui-jqgrid-titlebar-close",this).trigger("click");	
}).addClass('ui-corner-all');

$(pager).children().children().css('table-layout', 'auto'); //fix pager width
	
	
//on window resize -> resize grids
$(window).on('resize', function() {
	//main grid
	if (grid_width_Max > $(window).width()) {
		$groups.jqGrid('setGridWidth', $(window).width()-30);
	} else {
		$groups.jqGrid('setGridWidth', grid_width_Max);
	}
}).trigger('resize');


} //end if (groups)

}); //end jQuery(document).ready