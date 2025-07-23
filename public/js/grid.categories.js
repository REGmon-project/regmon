"use strict";

// Categories Grid

var V_CATEGORIES_FORMS = {};

jQuery(function() 
{

//Categories Forms data init
$.ajax({url:'php/ajax.php?i=forms2categories&oper=categories_forms_all', dataType:'json', success:function(data, result) {
	V_CATEGORIES_FORMS = data;
}});

const grid_width_Max = 900;
const LC = LANG.CATEGORIES;
const LF2C = LANG.FORMS2CATEGORIES;

const idPrefix = "c_";
const pager = '#Cpager';
const $categories = $("#categories");
let start_hidden = true;
let header = 'Categories';
if (V_is_Index_Options) { //for index options
	start_hidden = false;
	header = '';
}
if ($categories)
{

//Categories ###############################
$categories.jqGrid({
	url: 'php/ajax.php?i=categories',
	editurl: "php/ajax.php?i=categories",
	datatype: "json",
	idPrefix: idPrefix,
	iconSet: 'fontAwesome',
	hiddengrid: start_hidden,
	loadonce: true,
	caption: header, //error if not set
	pager: pager,
	pgtext: '',
	cmTemplate: { editoptions:{size:22}, editable:true, sortable:false },
	colNames:['', LANG.ID, LC.PARENT, LC.NAME, LC.ORDER, LC.COLOR, LANG.STATUS, LC.FORMS_COUNT, LC.FORMS, LANG.CREATED, LANG.CREATED_BY, LANG.MODIFIED, LANG.MODIFIED_BY],
	colModel:[
		{ //inline editing buttons and options
			name:'acc', hidden:(V_GROUP_ADMIN_2?true:false), width:22, fixed:true, sortable:false, editable:false, search: false, resizable:false, formatter:'actions', 
			formatoptions:{
				keys:true,
				delbutton:false,
				editformbutton:true,
				editOptions : {
					afterShowForm: function(form) {
						//gray out not used fields
						$('input[readonly], select[readonly]').css({'background-color':'#eee','background-image':'none'});
						//break long title
						$('#tr_forms_count td.CaptionTD').css('white-space', 'normal');
					}
				}
			}
		},
		{name:'id',key:true, width:25, align:"center", fixed:true, sorttype:'int', search:false, editoptions:{readonly:'readonly'} },
		{name:'parent_id', width:30, hidden:true, editrules:{edithidden:true},
			formatter:"select", edittype:"select", 
			editoptions:{size:1, dataUrl:'php/ajax.php?i=categories&oper=get_Categories_Select_Root'}
		},
		{name:'name', width:150/*, editrules:{required:true}*/},
		{name:'sort', width:20, align:"center", 
			editoptions: { type: "number", step: "1", min: "0", pattern: "[0-9]+([\.|,][0-9]+)?" },
			cellattr: function(rowId, val, rawObject, opts, row) {
				//console.log(rowId, val, rawObject, opts, row, row.parent_id);
				if (row.parent_id != '0') return ' style="padding-left:10px;"';
				else return ' style="padding-left:0px;"';
			}
		},
		{name:'color', width:45, fixed:true, search:false, editoptions:{dataInit:initColor, style:"width:65px"},
			cellattr: (rowId, val)  => { return ` style="background-color:${val}; color:${val}"`; }
		},
		{name:'status', width:45, template: aktivInaktivTemplate },
		{name:'forms_count', width:25, fixed:true, align:"center", editoptions:{readonly:'readonly'} },
		{name:'forms', width:100, hidden:true, editrules:{edithidden:true},
			formatter:"select", edittype:"select",
			editoptions: {
				multiple: true, disabled: true, size: 1, dataUrl: 'php/ajax.php?i=forms&oper=get_forms_select',
				selectFilled:function(options) {
					$(options.elem).chosen({width:'100%', placeholder_text_multiple: ' ', no_results_text: LANG.NO_RESULTS});
				}
			}
		},
		{name:'created',	width:40, hidden:V_is_Index_Options, editrules:{edithidden:true}, editoptions:{readonly:'readonly'} },
		{name:'created_by', width:50, hidden:V_is_Index_Options, editrules:{edithidden:true}, editoptions:{readonly:'readonly'}, align:"center" },
		{name:'modified', 	width:40, hidden:V_is_Index_Options, editrules:{edithidden:true}, editoptions:{readonly:'readonly'} },
		{name:'modified_by',width:50, hidden:V_is_Index_Options, editrules:{edithidden:true}, editoptions:{readonly:'readonly'}, align:"center" }
	],
	//loadComplete: function(data) {},
	//gridview: true, //no for treeGrid with subGrid
	treeGrid: true,
	treeGridModel: 'adjacency',
	ExpandColClick: true,
	ExpandColumn: 'name',
	/*treeIcons: { //define the icons in tree
		plus:'fa fa-fw fa-lg fa-plus',
		minus:'fa fa-fw fa-lg fa-minus',
		leaf:'fa fa-fw fa fa-file-text-o'
	},*/
	subGrid: true,
    subGridOptions: { 
        selectOnExpand: true,
	},
	
	//workaround for subGrids inside treeGrid
	onInitGrid: function () {
		$(this).jqGrid("getGridParam").subGrid = true;
	},
	//workaround for expand/collapse to hide the subGrid properly --when subGrids inside treeGrid
	treeGridBeforeCollapseRow: function (options) {
		const $self = $(this);
		const collapsingNodes = $self.jqGrid("getFullTreeNode", options.item);
		const p = $self.jqGrid("getGridParam");
		const idName = p.localReader.id;
		const iColSubgrid = p.iColByName.subgrid;

		collapsingNodes.forEach(function (item) {
			let tr = $self.jqGrid("getGridRowById", p.idPrefix + item[idName]);
			let	$td = $(tr.cells[iColSubgrid]);

			if ($td.hasClass("sgexpanded")) {
				$td.children(".sgbutton-div").children(".sgbutton").trigger("click");
			}
		});
	},
	
	//Forms subGrid #############################
	subGridRowExpanded: function(subgrid_id, row_id)
	{
		const sub_idPrefix = "cf_"; //categories/forms
		const subgrid_table_id = subgrid_id + "_t"; 
		const sub_pager = "p_" + subgrid_table_id;
		$("#" + subgrid_id).html(
			'<table id="' + subgrid_table_id + '" class="scroll"></table>' +
			'<div id="' + sub_pager + '" class="scroll"></div>'
		);
		const cat_id = $categories.jqGrid('getRowData', row_id)['id'];
		const forms_url = "php/ajax.php?i=forms2categories&ID=" + cat_id;
		//local data
		const grid_data = V_CATEGORIES_FORMS[cat_id];

		$("#"+subgrid_table_id).jqGrid({
			url: forms_url,
			editurl: forms_url,
			datatype: "local",
			data: grid_data,
			sortname: 'sort',
			forceClientSorting: true,
			gridview: true,
			idPrefix: sub_idPrefix,
			pager: '#'+sub_pager,
			pgtext: '',
			altRows: false, //for zebra rows
			pgbuttons: false,
			cmTemplate: { editoptions:{size:22}, editable:true },
			colNames:['', LANG.ID, LF2C.CATEGORY, LF2C.FORM_ID, LF2C.FORM_SELECT, LF2C.FORM_NAME, LF2C.ORDER, LANG.STATUS, LF2C.STOP_DATE, LANG.CREATED, LANG.CREATED_BY, LANG.MODIFIED, LANG.MODIFIED_BY],
			colModel:[
				{ //inline editing buttons and options
					name:'acc', hidden:(V_GROUP_ADMIN_2?true:false), width:22, fixed:true, sortable:false, editable:false, search: false, resizable:false, formatter:'actions', 
					formatoptions:{
						keys:true,
						delbutton:false,
						editformbutton:true, editOptions:{}
					}
				},
				{name:'id', width:32, key:true, search:false, hidden:true, sorttype:'int' },
				{name:'category_id', width:30, hidden:true, editrules:{edithidden:true}, editoptions:{readonly:'readonly'},
					formatter:"select", edittype:"select", 
					editoptions: {
						size: 1, dataUrl: 'php/ajax.php?i=categories&oper=get_Categories_Select',
						selectFilled:function(options) {
							$(options.elem).prop('disabled', 'disabled').css({'background-color':'#eee','background-image':'none'});
							//on add
							if (options.rowid === '_empty') {
								//select the right category
								const cat_id = row_id.split('_'); //c_12
								$('#category_id').val(cat_id[1]);
							}
						}
					}
				},
				{name:'form_id', width:20, sorttype:'int', editoptions:{readonly:'readonly'}},
				{name:'form_select', width:150, hidden:true, editrules:{edithidden:true},
					formatter:"select", edittype:"select", 
					editoptions: {
						size: 1, dataUrl: 'php/ajax.php?i=forms&oper=get_forms_select_empty',
						selectFilled:function(options) {
							// on add --show only on add
							if (options.rowid === '_empty') {
								//disable any existing form from the select list
								if (V_CATEGORIES_FORMS.hasOwnProperty(cat_id)) {
									let cat_have_forms = [];
									V_CATEGORIES_FORMS[cat_id].forEach(function(obj){
										cat_have_forms.push(obj.form_id);
									});
									$(options.elem).find('option').each(function(index,option){
										if (cat_have_forms.indexOf($(option).val()) !== -1) {
											$(option).prop('disabled', 'disabled');
										}
									});
								}
								//on select update form_name and form_id
								$(options.elem).on('change', function(){
									$('#form_id').val($(this).find("option:selected").val());
									$('#form_name').val($(this).find("option:selected").text());
								});
							} 
							else { // on edit
								$(options.elem).parents('tr').hide(); //hide on edit 
							}
						}
					}
				},
				{name:'form_name', width:250, editoptions:{readonly:'readonly'}},
				{name:'sort', width:30, align:"center", 
					editoptions: { type: "number", step: "1", min: "0", pattern: "[0-9]+([\.|,][0-9]+)?" }
				},
				{name:'status', width:45, template: aktivInaktivTemplate },
				{name:'stop_date', width:64, align:"right", template: stopDateTemplate},
				{name:'created', width:64, hidden:true, editrules:{edithidden:true}, editoptions:{readonly:'readonly'} },
				{name:'created_by', width:80, hidden:true, editrules:{edithidden:true}, editoptions:{readonly:'readonly'}, align:"center" },
				{name:'modified', width:64, hidden:true, editrules:{edithidden:true}, editoptions:{readonly:'readonly'} },
				{name:'modified_by', width:85, hidden:true, editrules:{edithidden:true}, editoptions:{readonly:'readonly'}, align:"center" }
			],
			loadComplete: function(data) {
				//Categories Forms data --load again to get fresh data
				if (this.p.datatype === 'json') { // only after add/edit/delete
					$.ajax({url:'php/ajax.php?i=forms2categories&oper=categories_forms_all', dataType:'json', success:function(data, result) {
						V_CATEGORIES_FORMS = data;
					}});
				}
			}
		}) //end of $("#"+subgrid_table_id).jqGrid({ //Forms
		.jqGrid('navGrid',"#"+sub_pager,{
			//iconsOverText: true,
			edit:false, edittext: LANG.BUTTON_EDIT,
			add:!V_GROUP_ADMIN_2, addtext: LANG.BUTTON_ADD,
			del:V_ADMIN, deltext: LANG.BUTTON_DELETE,
			search:false, searchtext: LANG.BUTTON_SEARCH,
			view:false, viewtext: LANG.BUTTON_VIEW,
			refresh:true, refreshtext: LANG.BUTTON_RELOAD,
			reloadGridOptions: { fromServer: true }
		},
		{},
		{ //add
			afterShowForm: function(form) {
				//gray out not used fields
				$('input[readonly], select[readonly]').css({'background-color':'#eee','background-image':'none'});
			}
		});
		
		//subgrid remove rounded corners and set background color
		$('#gbox_'+subgrid_table_id).removeClass('ui-corner-all');
		$('#'+sub_pager).removeClass('ui-corner-bottom');
		const parent_color = $categories.jqGrid('getRowData', row_id)['color'];
		$("#"+subgrid_id).parent().css('padding', '2px').css("background", parent_color).prev().css("background", parent_color);
		//set subgrid width
		$('#'+subgrid_table_id).jqGrid('setGridWidth', $categories.width()-30);
		
	} //subGridRowExpanded Forms end
	//end categories subGrid #############################
	
}) //$categories.jqGrid({ end
//Column Search --not work as treeGrid 
.jqGrid('navGrid',pager,{ //bottom bar
	//iconsOverText: true,
	edit:false, edittext: LANG.BUTTON_EDIT,
	add:!V_GROUP_ADMIN_2, addtext: LANG.BUTTON_ADD,
	del:V_ADMIN, deltext: LANG.BUTTON_DELETE,
	search:false, searchtext: LANG.BUTTON_SEARCH,
	view:false, viewtext: LANG.BUTTON_VIEW,
	refresh:true, refreshtext: LANG.BUTTON_RELOAD,
	reloadGridOptions: { fromServer: true }
},
{ //add
	afterShowForm: function(form) {
		//gray out not used fields
		$('input[readonly], select[readonly]').css({'background-color':'#eee','background-image':'none'});
		//break long title
		$('#tr_forms_count td.CaptionTD').css('white-space', 'normal');
	}
});


$(pager).children().children().css('table-layout', 'auto'); //fix pager width

if (V_is_Index_Options) { //for index options
	//remove rounded corners
	$('#gbox_categories').removeClass('ui-corner-all').addClass('ui-corner-bottom');
}
else { //admin page
	//set Caption from table title/alt
	$categories.jqGrid('setCaption', $categories.attr('alt'))
		.closest("div.ui-jqgrid-view") //center Caption and change font-size
			.children("div.ui-jqgrid-titlebar").css({"text-align":"center", "cursor":"pointer"})
			.children("span.ui-jqgrid-title").css({"float":"none", "font-size": "17px"});

	//Expand/Collapse grid from Caption click
	$($categories[0].grid.cDiv).on('click',function() {
		if ($(pager).is(':hidden')) 
			$(this).removeClass('ui-corner-all');
		else $(this).addClass('ui-corner-all');
		$(".ui-jqgrid-titlebar-close",this).trigger("click");	
	}).addClass('ui-corner-all');
}

function Responsive_Categories() { 
	if (V_is_Index_Options) { //for index options
		let p_width = $('#C_Categories_link').prop('clientWidth');// Get width of parent container
		if (p_width == null || p_width < 1){
			p_width = $('#C_Categories_link').prop('offsetWidth'); // For IE, revert to offsetWidth if necessary
		}
		p_width = p_width - 3; //prevent horizontal scrollbars
		
		//set here anyway to avoid empty grid after (calendar, options) change
		$categories.jqGrid('setGridWidth', p_width);
		if (p_width != $categories.width()) {
			//$categories.jqGrid('setGridWidth', p_width);
			//if have subs opened
			if ($("div[id^=categories_c_]").length > 0) { 
				$("div[id^=categories_c_]").each(function(){
					$('#'+this.id+'_t').jqGrid('setGridWidth', $categories.width()-30);
				});
			}
		}
	}
	else { //admin page
		if (grid_width_Max > $(window).width()) {
			$categories.jqGrid('setGridWidth', $(window).width()-30);
		} else {
			$categories.jqGrid('setGridWidth', grid_width_Max);
		}
		if ($("div[id^=categories_c_]").length > 0) { 
			$("div[id^=categories_c_]").each(function(){
				$('#'+this.id+'_t').jqGrid('setGridWidth', $categories.width()-30);
			});
		}
	}
}
Responsive_Categories();

//on window resize -> resize grids
$(window).on('resize', function() {
	Responsive_Categories();
}).trigger('resize');

   
} //end if (categories)

}); //end jQuery(document).ready