"use strict";

// Location Groups Grid

var $location_groups = false;

jQuery(function() 
{

const LG = LANG.GROUPS;
const idPrefix = "g_";
const pager = '#SGpager';

$location_groups = $("#location_groups");
if ($location_groups)
{

//Location Groups ###############################
$location_groups.jqGrid({
	url: 'php/ajax.php?i=groups&ID='+V_Group_2_Location[V_GROUP][0],
	editurl: "php/ajax.php?i=groups&ID="+V_Group_2_Location[V_GROUP][0],
	idPrefix: idPrefix,
	iconSet: 'fontAwesome',
	pager: pager,
	pgtext: '',
	altRows: false, //for zebra
	cmTemplate: { editoptions:{size:22}, editable:true },
	colNames:['', LANG.ID, LG.LOCATION, LG.NAME, LANG.STATUS, LG.PRIVATE_KEY, LG.ADMIN, LG.STOP_DATE, LANG.CREATED, LANG.MODIFIED],
	colModel:[
		{ //inline editing buttons and options
			name:'acc', hidden:!(V_ADMIN||V_LOCATION_ADMIN), width:22, fixed:true, sortable:false, editable:false, search: false, resizable:false, formatter:'actions', 
			formatoptions:{
				keys:true,
				delbutton:false,
				editformbutton:true
			}
		},
		{name:'id',key:true,width:30,  align:"center", sorttype:'int', editoptions:{readonly:'readonly'} },
		{name:'location_id',width:100, hidden:true},
		{name:'name', 		width:250},
		{name:'status', 	width:50, template: aktivInaktivPrivateTemplate},
		{name:'private_key',width:80},
		{name:'admins_id', 	width:100, align:"center",
				formatter:"select", edittype:"select", stype:'select',
				searchoptions: {sopt:['cn','eq','ne'], value:V_GROUPS_ADMINS_OPTIONS},	
				editoptions:{
					value:V_GROUPS_ADMINS_OPTIONS, multiple:true, size:1, dataUrl:'php/ajax.php?i=groups&oper=groups_admins_select', 
					selectFilled:function(options) {
						$(options.elem).chosen({width:'100%', placeholder_text_multiple: ' ', no_results_text: LANG.NO_RESULTS});
					}
				}
		},
		{name:'stop_date',	width:85, align:"right", template: stopDateTemplate},
		{name:'created', 	width:64, hidden:true, editrules:{edithidden:true}, editoptions:{readonly:'readonly'} },
		{name:'modified', 	width:64, hidden:true, editrules:{edithidden:true}, editoptions:{readonly:'readonly'} }
	]
}) //end $location_groups.jqGrid({
.jqGrid('navGrid',pager,{ //bottom bar
	iconsOverText: true,
	edit: false,	edittext: LANG.BUTTON_EDIT,
	add: (V_ADMIN||V_LOCATION_ADMIN), addtext: LANG.BUTTON_ADD,
	del: false, 	deltext: LANG.BUTTON_DELETE,
	search: false, 	searchtext: LANG.BUTTON_SEARCH,
	view: true, 	viewtext: LANG.BUTTON_VIEW,
	refresh: true, 	refreshtext: LANG.BUTTON_RELOAD
}); //pager


$(pager).children().children().css('table-layout', 'auto'); //fix pager width
$('#gbox_location_groups').removeClass('ui-corner-all').addClass('ui-corner-bottom')


function Responsive_Location_Groups() { 
    let p_width = $('#C_Location_Groups_link').prop('clientWidth');// Get width of parent container
    if (p_width == null || p_width < 1){
        p_width = $('#C_Location_Groups_link').prop('offsetWidth'); // For IE, revert to offsetWidth if necessary
    }
	p_width = p_width -2; //prevent horizontal scrollbars
		
	//set here anyway to avoid empty grid after (calendar, options) change
	$location_groups.jqGrid('setGridWidth', p_width);
	if (p_width != $location_groups.width()) {
		//$location_groups.jqGrid('setGridWidth', p_width);
	}
}
Responsive_Location_Groups();


//on window resize -> resize grids
$(window).on('resize', function() {
	Responsive_Location_Groups();
}).trigger('resize');


} //end if (location_groups)

}); //end jQuery(document).ready