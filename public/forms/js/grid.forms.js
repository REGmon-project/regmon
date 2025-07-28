"use strict";

// Forms Grid

jQuery(function() 
{

const grid_width_Max = 1400;
const LF = LANG.FORMS;
const idPrefix = "f_";
const pager = '#Fpager';
let $forms = $("#forms");
let start_hidden = true;
let header = 'Forms';
if (V_is_Index_Options) { //for index options
	start_hidden = false;
	header = '';
}
if ($forms)
{

//Forms ###############################
$forms.jqGrid({
	url: 'php/ajax.php?i=forms',
	editurl: "php/ajax.php?i=forms",
	datatype: "json",
	idPrefix: idPrefix,
	iconSet: 'fontAwesome',
	hiddengrid: start_hidden, //to start Hidden and without ajax loading
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
	colNames:['', LANG.ID, LF.NAME, LF.NAME2, LF.TAGS, LF.CATEGORIES, LANG.STATUS, LF.DETAILS, LANG.CREATED, LANG.CREATED_BY, LANG.MODIFIED, LANG.MODIFIED_BY],
	colModel:[
		{ //inline editing buttons and options
			name:'acc', hidden:(V_GROUP_ADMIN_2?true:false), width:22, fixed:true, sortable:false, editable:false, search: false, resizable:false, formatter:'actions', 
			formatoptions:{
				keys:true,
				delbutton:false,
				editformbutton:true
			}
		},
		{name:'id',key:true, width:32, align:"center", sorttype:"int", editoptions:{readonly:'readonly'} },
		{name:'name', 		 width:150},
		{name:'name2',		 width:100},
		{name:'tags',		 width:100,
			formatter:"select", edittype:"select",
			editoptions: {
				multiple: true, size: 1, dataUrl: 'php/ajax.php?i=tags&oper=get_tags_select',
				selectFilled:function(options) {
					$(options.elem).chosen({
						width:'100%', placeholder_text_multiple: ' ', no_results_text: LANG.NO_RESULTS,
						multiple: true,
						create_option: true,
						create_option_text: LANG.NEW_OPTION,
						search_contains: true
					});
				}
			}
		},
		{name:'categories',	 width:30, hidden:V_is_Index_Options, editrules:{edithidden:true},
			formatter:"select", edittype:"select", 
			editoptions: {
				multiple: true, disabled: true, size: 1, dataUrl: 'php/ajax.php?i=categories&oper=get_Categories_Select',
				selectFilled:function(options) {
					$(options.elem).chosen({width:'100%', placeholder_text_multiple: ' ', no_results_text: LANG.NO_RESULTS});
				}
			}
		},
		{name:'status',	 	 width:45, template: aktivInaktivTemplate },
		{name:'details',	 width:32, fixed:true, hidden:(V_GROUP_ADMIN_2?true:false), 
			editable:false, search:false,
			formatter: function (cellValue, option, rowObject) {
				return ''+
				'<div title="'+LANG.FORMS.FORM_EDIT+'" style="text-align:center; cursor:pointer;"'+
					' onmouseover="$(this).addClass(\'ui-state-hover\');"'+
					' onmouseout="$(this).removeClass(\'ui-state-hover\');">'+
					'<a href="form.php?id='+option.rowId+'&edit" target="_blank" style="display:inline-block;">'+
						'<span class="fa fa-gear" style="color:#2e6e9e;"></span>'+
						'<span class="fa fa-file-text-o" style="margin-left:-5px; color:#2e6e9e;"></span>'+
					'</a>'+
				'</div>';
			}
		},
		{name:'created',	 width:40, hidden:V_is_Index_Options, editrules:{edithidden:true}, editoptions:{readonly:'readonly'} },
		{name:'created_by',  width:50, hidden:V_is_Index_Options, editrules:{edithidden:true}, editoptions:{readonly:'readonly'}, align:"center" },
		{name:'modified', 	 width:40, hidden:V_is_Index_Options, editrules:{edithidden:true}, editoptions:{readonly:'readonly'} },
		{name:'modified_by', width:50, hidden:V_is_Index_Options, editrules:{edithidden:true}, editoptions:{readonly:'readonly'}, align:"center" }
	]
}) //end $forms.jqGrid({
//Column Search
.jqGrid('filterToolbar',{
	stringResult:true,
	searchOnEnter:false,
	defaultSearch: 'cn'
})
//bottom bar
.jqGrid('navGrid',pager,{
	iconsOverText: true,
	edit:false, 			edittext: LANG.BUTTON_EDIT,
	add:!V_GROUP_ADMIN_2, 	addtext: LANG.BUTTON_ADD,
	del:V_ADMIN, 			deltext: LANG.BUTTON_DELETE,
	search:false, 			searchtext: LANG.BUTTON_SEARCH,
	view:false, 			viewtext: LANG.BUTTON_VIEW,
	refresh:true, 			refreshtext: LANG.BUTTON_RELOAD,
	reloadGridOptions: { fromServer: true }
},
{},
{ //add
	afterShowForm: function(form) {
		//gray out not used fields
		$('input[readonly], select[readonly]').css({'background-color':'#eee','background-image':'none'});
	}
});

if (!V_GROUP_ADMIN_2) {
	//duplicate button
	$forms.jqGrid("navButtonAdd", pager, {
		iconsOverText: true,
		buttonicon: "fa-copy",
		caption: LANG.BUTTON_DUPLICATE,
		title: LANG.BUTTON_DUPLICATE,
		onClickButton: function () {
			const self = $(this);
			const selRowId = self.jqGrid('getGridParam', 'selrow');
			if (selRowId != null) {
				const form_id = self.jqGrid('getCell', selRowId, 'id');
				$.ajax({url:'php/ajax.php?i=forms&oper=form_duplicate&ID='+form_id, success:function(data, result) {
					self.trigger("reloadGrid", { fromServer: true });
				}});
			}
			else {
				this.modalAlert();
			}
		}
	});
}


$(pager).children().children().css('table-layout', 'auto'); //fix pager width

if (V_is_Index_Options) { //for index options
	$('#gbox_forms').removeClass('ui-corner-all').addClass('ui-corner-bottom')
}
else { //admin page
	//set Caption from table title/alt
	$forms.jqGrid('setCaption', $forms.attr('alt'))
	.closest("div.ui-jqgrid-view") //center Caption and change font-size
		.children("div.ui-jqgrid-titlebar").css({"text-align":"center", "cursor":"pointer"})
		.children("span.ui-jqgrid-title").css({"float":"none", "font-size": "17px"});

	//Expand/Collapse grid from Caption click
	$($forms[0].grid.cDiv).on('click',function() {
		if ($(pager).is(':hidden')) 
			$(this).removeClass('ui-corner-all');
		else $(this).addClass('ui-corner-all');
		$(".ui-jqgrid-titlebar-close",this).trigger("click");	
	}).addClass('ui-corner-all');
}


function Responsive_Forms() { 
	if (V_is_Index_Options) { //for index options
		let p_width = $('#C_Forms_link').prop('clientWidth');// Get width of parent container
		if (p_width == null || p_width < 1){
			p_width = $('#C_Forms_link').prop('offsetWidth'); // For IE, revert to offsetWidth if necessary
		}
		p_width = p_width - 3; //prevent horizontal scrollbars
		
		//set here anyway to avoid empty grid after (calendar, options) change
		$forms.jqGrid('setGridWidth', p_width);
		if (p_width != $forms.width()) {
			//$forms.jqGrid('setGridWidth', p_width);
		}
	}
	else { //admin page
		if (grid_width_Max > $(window).width()) {
			$forms.jqGrid('setGridWidth', $(window).width()-30);
		} else {
			$forms.jqGrid('setGridWidth', grid_width_Max);
		}
	}
}
Responsive_Forms();


//on window resize -> resize grids
$(window).on('resize', function() {
	Responsive_Forms();
}).trigger('resize');

   
} //end if (forms)

}); //end jQuery(document).ready