"use strict";

var button_New_Row_Click;
var New_Row_Events;
var button_New_AccordionPanel_Row_Click;
var New_Row_Item_init;
var button_New_Item_Click;
var button_Copy_Row_Click;
var Copy_Num = 0;
var get_Form_JSON;
var last_dd_opt;
var last_radio_has_title;
var last_radio_title;
var last_radio_talign;
var last_has_color;
var last_last_has_color;
var last_color_a;
var last_last_color_a;
var last_color_b;
var last_last_color_b;
var item_type_arr = {
	"_Empty" 	: LANG.FORMS.ITEM_EMPTY,
	"_Space" 	: LANG.FORMS.ITEM_SPACE,
	"_Line" 	: LANG.FORMS.ITEM_LINE,
	"_Label" 	: LANG.FORMS.ITEM_LABEL,
	"_Html" 	: LANG.FORMS.ITEM_TEXT_HTML,
	"_Text" 	: LANG.FORMS.ITEM_TEXT,
	"_Textarea" : LANG.FORMS.ITEM_TEXTAREA,
	"_Number" 	: LANG.FORMS.ITEM_NUMBER,
	"_Date" 	: LANG.FORMS.ITEM_DATE,
	"_Time" 	: LANG.FORMS.ITEM_TIME,
	"_Period" 	: LANG.FORMS.ITEM_PERIOD,
	"_Dropdown" : LANG.FORMS.ITEM_DROPDOWN,
	"_RadioButtons" 	: LANG.FORMS.ITEM_RADIO,
	"_Accordion" 		: LANG.FORMS.ITEM_ACCORDION,
	"_Accordion_Panel" 	: LANG.FORMS.ITEM_ACCORDION_PANEL
};

//https://alex-d.github.io/Trumbowyg/documentation/#button-pane
var trumbowyg_buttons = [
	['viewHTML'],
	['undo', 'redo'],
	['formatting'],
	['strong', 'em', 'underline', 'del'],
	['superscript', 'subscript'],
	['table'],
	['link'],
	['insertImage'],
	['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
	//['unorderedList', 'orderedList'],
	['horizontalRule'],
	['removeformat'],
	['fullscreen']
];

//Helpers #####################################
function replaceAll_Object_Array(str, obj) {
	Object.keys(obj).forEach(function(key,index) {
		str = replaceAll(str, obj[key][0], obj[key][1]);
	});
	return str;
}

function get_max_val(selector) {
	let max = null;
	$(selector).each(function() {
		const value = parseFloat($(this).val());
		max = (value > max) ? value : max;
	});
	return max;
}
//Helpers #####################################


jQuery(function()
{

//sticky buttons
$('#ssb-btn-1').on('click',function() { $("button.preview").trigger("click"); });
$('#ssb-btn-6').on('click',function() { $("button.save").trigger("click"); });

//Helpers #####################################
//firefox bug fix for disableSelection() that disable inputs 
$.fn.extend({
    preventDisableSelection: function(){
        return this.each(function(i) {
            $(this).bind('mousedown.ui-disableSelection selectstart.ui-disableSelection', function(e) {
                e.stopImmediatePropagation();
            });
        });
    }
});
//Helpers #####################################

//Timer
$("#timer_min").spinner({min:0});
$('#timer_has_label').on('click',function(e){
	$('#timer_has_ck').trigger("click");
	return false;
});
$('.timer_has_ck').on('click',function(e){
	$("#fs-form_timer").toggleClass('collapsed');
	$('#form_timer_div').slideToggle('slow');
});

$('#days_has_label').on('click',function(e){
	$('#days_has_ck').trigger("click");
	return false;
});
$('.days_has_ck').on('click',function(e){
	$("#fs-form_days").toggleClass('collapsed');
	$('#form_days_div').slideToggle('slow');
});


//#####################################
//TEMPLATES ###########################

const _Template_Page = ''+ //{PAGE_NUM}
	'<li class="page_sort">'+
		'<span id="pageDrag_{PAGE_NUM}" class="page-drag trans5 hid"><i class="fa fa-arrows"></i></span>'+
		'<fieldset id="fieldset_{PAGE_NUM}" class="coolfieldset" data-page-id="{PAGE_NUM}">'+
			'<legend> '+LANG.FORMS.FORM_TITLE+' {PAGE_NUM}&nbsp;</legend>'+
			'<input type="hidden" name="page[]" value="{PAGE_NUM}">'+
			'<div class="edit_step">'+
				'<span id="p_close_page_{PAGE_NUM}" class="close_page"></span>'+
				'<div style="text-align:center;" title="'+LANG.FORMS.DISPLAY_TIMES_INFO+'">'+ 
					'<label for="page_display_times_{PAGE_NUM}">'+LANG.FORMS.DISPLAY_TIMES+' : &nbsp;</label>'+
					'<select id="page_display_times_{PAGE_NUM}" class="page_display_times">'+
						'<option value="0">'+LANG.FORMS.DISPLAY_TIMES_0+'</option>'+
						'<option value="1">'+LANG.FORMS.DISPLAY_TIMES_1+'</option>'+
						'<option value="2">'+LANG.FORMS.DISPLAY_TIMES_2+'</option>'+
						'<option value="3">'+LANG.FORMS.DISPLAY_TIMES_3+'</option>'+
						'<option value="4">'+LANG.FORMS.DISPLAY_TIMES_4+'</option>'+
						'<option value="5">'+LANG.FORMS.DISPLAY_TIMES_5+'</option>'+
					'</select>'+
				'</div>'+
				'<h3 style="white-space:nowrap;">'+
					'<input type="text" id="page_title_{PAGE_NUM}" class="c_page_title" placeholder="'+LANG.FORMS.PAGE_TITLE+'" value="">'+
					'<span class="c_page_title_center trans20">'+
					'<label for="page_title_center_{PAGE_NUM}">'+LANG.FORMS.CENTER+'</label>'+
						'<input type="checkbox" id="page_title_center_{PAGE_NUM}">'+
					'</span>'+
				'</h3>'+
				'<span class="main_font">'+
					'<ul class="row_sortable"></ul>'+
					'<div style="text-align:center; margin-top:10px;"><button type="button" id="page_{PAGE_NUM}_newRow" class="newRow" data-page="{PAGE_NUM}"> &nbsp; '+LANG.FORMS.FORM_ROW_ADD+'</button></div>'+
				'</span>'+
			'</div>'+
		'</fieldset><br><br>'+
	'</li>';

const _Template_Row = ''+ //{ROW_NUM}
	'<li id="Row_{ROW_NUM}" class="row_sort" data-row="{ROW_NUM}">'+
		'<div class="row_drag">'+
			'<input type="hidden" name="row_no" value="{ROW_NUM}">'+
			'<span class="Row-drag trans5 hid"><i class="fa fa-arrows"></i></span>'+
		'</div>'+
		'<div class="row_div">'+
			'<table class="RowTable-edit text_inline box2 trans" border="0"><tbody>'+
				'<tr class="row_item_sort" data-row="{ROW_NUM}"><td class="rowItem-init" style="width:1%;"></td></tr>'+ // hidden--start visible
			'</tbody></table>'+
		'</div>'+
		'<div class="row_actions">'+
			'<span class="rowItem-add trans5 hid" data-row="{ROW_NUM}" title="'+LANG.FORMS.FORM_ITEM_ADD+'"><i class="fa fa-plus-circle"></i></span>'+
			'<span class="Row-copy trans5 hid" data-row="{ROW_NUM}" title="'+LANG.FORMS.FORM_ROW_DUPLICATE+'"><span class="fa-stack"><i class="fa fa-circle-thin fa-stack-2x"></i><i class="fa fa-copy fa-stack-1x" style="font-weight:bold;"></i></span></span>'+
			'<span class="Row-remove trans5 hid"><i class="fa fa-times-circle" title="'+LANG.FORMS.FORM_ROW_DELETE+'"></i></span>'+
		'</div>'+
	'</li>';

const _Template_Item__Main = ''+ //{ROW_ITEM},{ROW_ITEM_NUM},{ROW_ITEM_TYPE},{ROW_ITEM_TYPE_NAME}
		'<input type="hidden" name="rowItem_no" value="{ROW_ITEM_NUM}" class="c_no">'+
		'<input type="hidden" name="rowItem_type" value="{ROW_ITEM_TYPE}" class="c_type">'+
		'<div class="c_handler trans50">'+
			'<span class="rowItem_Drag trans10"><i class="fa fa-arrows"></i></span>'+
			'<a class="rowItem_EditLink trans30" data-id="{ROW_ITEM}" data-type="{ROW_ITEM_TYPE}" style="cursor:pointer;">'+
			'{ROW_ITEM_TYPE_NAME}</a>'+
		'</div>';

const _Template_Item__Empty = ''+ //{ROW_ITEM}
	'<td id="rowItem_{ROW_ITEM}" class="rowItem s_input cm_it empty" style="text-align: left; width: 100%;" data-row_item="{ROW_ITEM}">'+
		'<div class="rowItem_edit">'+
			_Template_Item__Main+
			'<div class="c_content hidden">'+
				'<select class="rowItem_Select" style="width:100%;">'+
					'<option value="_Space">'+LANG.FORMS.ITEM_SPACE+'</option>'+
					'<option value="_Line">'+LANG.FORMS.ITEM_LINE+'</option>'+
					'<option value="_Label">'+LANG.FORMS.ITEM_LABEL+'</option>'+
					'<option value="_Html">'+LANG.FORMS.ITEM_TEXT_HTML+'</option>'+
					'<option value="_Text">'+LANG.FORMS.ITEM_TEXT+'</option>'+
					'<option value="_Textarea">'+LANG.FORMS.ITEM_TEXTAREA+'</option>'+
					'<option value="_Number">'+LANG.FORMS.ITEM_NUMBER+'</option>'+
					'<option value="_Date">'+LANG.FORMS.ITEM_DATE+'</option>'+
					'<option value="_Time">'+LANG.FORMS.ITEM_TIME+'</option>'+
					'<option value="_Period">'+LANG.FORMS.ITEM_PERIOD+'</option>'+
					'<option value="_Dropdown">'+LANG.FORMS.ITEM_DROPDOWN+'</option>'+
					'<option value="_RadioButtons">'+LANG.FORMS.ITEM_RADIO+'</option>'+
					'<option value="_Accordion">'+LANG.FORMS.ITEM_ACCORDION+'</option>'+
				'</select>'+
			'</div>'+
		'</div>'+
		'<div class="rowItem_div">'+
			'<span class="c_empty"></span>'+
		'</div>'+
	'</td>';

//TEMPLATES ###########################
//#####################################





//#####################################
//PAGE ################################

function Page_Events_Init(page_num) {
	//fieldset
	$("#fieldset_"+page_num).collapsible();
	
	//page title center checkbox
	$("#page_title_center_"+page_num).on('click',function() {
		if ($(this).is(":checked") == true) {
			$("#page_title_"+page_num).css("text-align","center");
		}
		else {
			$("#page_title_"+page_num).css("text-align","left");
		}
	});
	
	//hover page title center checkbox
	$('#page_title_'+page_num).parent().hover(function() {
		$(this).find('.c_page_title_center').removeClass('trans20');
	}, function() {
		$(this).find('.c_page_title_center').addClass('trans20');
	});
	
	//New_Item button
	button_New_Row_Click("#page_"+page_num+"_newRow");
	
	//Page Remove
	$('#p_close_page_'+page_num).confirmation({
		href: 'javascript:void(0)',
		title: LANG.ARE_YOU_SURE, placement: 'top',
		btnOkLabel: LANG.YES, btnOkClass: 'btn btn-sm btn-success mr10',
		btnCancelLabel: LANG.NO, btnCancelClass: 'btn btn-sm btn-danger',
		onConfirm: function(e, button) {
			$(button).closest('li.page_sort').remove();
		}
	});
	
	//move page icon
	$("#fieldset_"+page_num).hover(function() {
		$(this).prev().removeClass('trans5');
	}, function() {
		$(this).prev().addClass('trans5');
	});
	$("#pageDrag_"+page_num).hover(function() {
		$(this).removeClass('trans5');
	}, function() {
		$(this).addClass('trans5');
	});

	//init Row dragging --need to called once in a Page
	if (!V_HAVE_DATA) {
		$("#fieldset_"+page_num+" > .edit_step > .main_font > ul.row_sortable").sortable({  
			items:"> li.row_sort",
			connectWith: "ul.row_sortable",
			//containment:"ul.page_sortable", //"ul.item_sortable", //moves in --I disabled that so rows can go everywhere
			handle: "> .row_drag > .Row-drag",
			revert: 300,
			opacity: '.5',
			tolerance: "intersect",
			placeholder:"ui-state-highlight placeholder-item",
			start: function (e, ui) { ui.placeholder.height(ui.helper.outerHeight()); ui.placeholder.height(ui.helper.outerHeight()); }
			//cancel:"a,input,textarea,button,select,option,ins, .s_radio, .iCheck-helper, .iradio_square-aero .close_page, .get_item, .active-result, .btn" --not needed bcz we do it with handle
		}).disableSelection(); //disable selection so not select text when we drag something
		//enable text select on inputs -firefox --may not needed bcz we do it with handle
		$(this).closest('.main_font').find("input,textarea,select").preventDisableSelection();
	}
}

//button newPage --only one button --run once
$("button.newPage").on('click',function() {
	const page_num = get_max_val('input[name="page[]"]') + 1;
	const Page_Template = replaceAll(_Template_Page, '{PAGE_NUM}', page_num);
	$(this).closest('#middle-wizard').find('ul.page_sortable').append(Page_Template);
	//init Page Events
	Page_Events_Init(page_num);
});

//make Pages Sortable Init --need to called once
if (!V_HAVE_DATA) {
	$("ul.page_sortable").sortable({
		items:"li.page_sort:not(.first_page)",
		//connectWith: "ul.item_sortable",
		//containment:"ul.page_sortable", //"ul.item_sortable", //moves in 
		handle: ".page-drag",
		revert: 300,
		opacity: '.5',
		tolerance: "pointer",
		placeholder:"ui-state-highlight placeholder-page",
		//containment:"parent",
		start: function (e, ui) { ui.placeholder.height(ui.helper.outerHeight()); ui.placeholder.height(ui.helper.outerHeight()); },
		//cancel:".edit_step, a,input,textarea,button,select,option,ins, .s_radio, .iCheck-helper, .iradio_square-aero .close_page, .get_item, .active-result, .btn"
	}).disableSelection(); //disable selection so not select text when we drag something
	$("ul.page_sortable").find("input,textarea,select").preventDisableSelection(); //enable text select on inputs -firefox
}
//PAGE ################################
//#####################################



//#####################################
//ROW #################################

//button newRow --called once in a page
button_New_Row_Click = function (element) {
	$(element).on('click',function() {
		const row_num = get_max_val('input[name="row_no"]') + 1;
		
		const Row_Template = replaceAll_Object(_Template_Row, {'{ROW_NUM}':row_num});
		
		$(this).parent().prev('ul.row_sortable').append(Row_Template);
		
		New_Row_Events(row_num);
	});
};

//called once for each Row
New_Row_Events = function (row) {
	$('#Row_'+row).hover(function() {
		$(this).find('> .row_drag > .hid').removeClass('trans5');
		$(this).find('> .row_actions > .hid').removeClass('trans5');
		$(this).find('> .row_div > table').removeClass('trans');
		$(this).find('> .row_div > table>tbody>tr>td > .rowItem_edit > .c_handler').removeClass('trans50');
		$(this).find('> .row_div > table>tbody>tr>td > .rowItem_edit > .c_handler').removeClass('trans50');
	}, function() {
		$(this).find('> .row_drag > .hid').addClass('trans5');
		$(this).find('> .row_actions > .hid').addClass('trans5');
		$(this).find('> .row_div > table').addClass('trans');
		$(this).find('> .row_div > table>tbody>tr>td > .rowItem_edit > .c_handler').addClass('trans50');
	});
	
	//New row item button init
	button_New_Item_Click('#Row_'+row+' > div.row_actions > .rowItem-add'); //#BTnewRowItem_'+row
	
	//Copy row item button init
	button_Copy_Row_Click('#Row_'+row+' > div.row_actions > .Row-copy'); //'#BTcopyRowItem_'+row
	
	//Row-remove click
	$('#Row_'+row+' > div.row_actions > .Row-remove').confirmation({
		href: 'javascript:void(0)',
		title: LANG.ARE_YOU_SURE, placement: 'top',
		btnOkLabel: LANG.YES, btnOkClass: 'btn btn-sm btn-success mr10',
		btnCancelLabel: LANG.NO, btnCancelClass: 'btn btn-sm btn-danger',
		onConfirm: function(e, button) {
			$(button).closest('li.row_sort').find('[aria-describedby^="popover"]').each(function(){
				$("#"+$(this).attr('aria-describedby')).popover('destroy'); //destroy all open popovers
			});
			$(button).closest('li.row_sort').remove(); //remove <li>
		}
	});
	
	//init Row-Item dragging --need to called once in a Row
	if (!V_HAVE_DATA) { //init dragging
		$('#Row_'+row+' > div.row_div > table > tbody > tr.row_item_sort').sortable({  
			items:"> td",
			connectWith: "tr.row_item_sort",
			//containment:"ul.page_sortable", //"ul.item_sortable", //moves in 
			handle: '.rowItem_edit > .c_handler > .rowItem_Drag', //needs one level after selector
			//helper: "clone",
			revert: 300,
			opacity: '.5',
			//"intersect": The item overlaps the other item by at least 50%. -- "pointer": The mouse pointer overlaps the other item.
			tolerance: "intersect", //have problem with pointer and nested acc items
			placeholder:"ui-state-highlight placeholder-item-td",
			stop: function( e, ui ) {
				const row = $(this).attr('data-row');

				Set_Items_Width(row); //set width
			},
			update: function( e, ui ) {
				const row = $(this).attr('data-row');

				Set_Items_Width(row); //set width
			}
		}).disableSelection();
	}
}

button_Copy_Row_Click = function (element) {
	function get_New_Row_num() {
		return get_max_val('input[name="row_no"]') + 1;
	}
	$(element).on('click',function() {
		//console.log('button_Copy_Row_Click');
		Copy_Num++;
		const old_row_num = $(this).attr('data-row');
		const new_row_num = get_New_Row_num();

		let new_row = $(this).parent().parent('li.row_sort').clone().html();

		if (new_row.indexOf('data-type="_Accordion"') == -1) { //one line
			//first Row only
			const a1 = ' name="row_no" value="'+old_row_num+'"'; //name="row_no" value="2"
			const a2 = ' name="row_no" value="'+new_row_num+'"';
			const b1 = ' id="rowItem_'+old_row_num+'_'; //rowItem_2_1
			const b2 = ' id="rowItem_'+new_row_num+'_';
			const c1 = ' data-row_item="'+old_row_num+'_'; //data-row_item="2_1"
			const c2 = ' data-row_item="'+new_row_num+'_';
			const d1 = ' data-id="'+old_row_num+'_'; //data-id="2_1"
			const d2 = ' data-id="'+new_row_num+'_';
			const e1 = ' data-row="'+old_row_num+'"'; //data-row="2"
			const e2 = ' data-row="'+new_row_num+'"';
			const f1 = ' name="c_'+old_row_num+'_'; //name="c_2_1_
			const f2 = ' name="c_'+new_row_num+'_';
			const g1 = 'true?1:0;" class="c_'+old_row_num+'_';
			const g2 = 'true?1:0;" class="c_'+new_row_num+'_';
			const h1 = 'c_has_title c_'+old_row_num+'_'; 
			const h2 = 'c_has_title c_'+new_row_num+'_';
			const i1 = ' class="c_title c_'+old_row_num+'_';
			const i2 = ' class="c_title c_'+new_row_num+'_';
			const j1 = 'function(){$(".c_'+old_row_num+'_';
			const j2 = 'function(){$(".c_'+new_row_num+'_';
			const k1 = 'btn_rd_align_'+old_row_num+'_';
			const k2 = 'btn_rd_align_'+new_row_num+'_';
			const l1 = ' class="c_has_color c_'+old_row_num+'_';
			const l2 = ' class="c_has_color c_'+new_row_num+'_';
			const m1 = '_color_a, .c_'+old_row_num+'_';
			const m2 = '_color_a, .c_'+new_row_num+'_';
			const n1 = ' class="c_color_a c_'+old_row_num+'_';
			const n2 = ' class="c_color_a c_'+new_row_num+'_';
			const o1 = ' class="c_color_b c_'+old_row_num+'_';
			const o2 = ' class="c_color_b c_'+new_row_num+'_';
			const p1 = ' id="datetimepicker_'+old_row_num+'_';
			const p2 = ' id="datetimepicker_'+new_row_num+'_';
			const q1 = 'time" id="clockpicker_'+old_row_num+'_';
			const q2 = 'time" id="clockpicker_'+new_row_num+'_';
			const r1 = ' id="clockpicker_from_'+old_row_num+'_';
			const r2 = ' id="clockpicker_from_'+new_row_num+'_';
			const s1 = ' id="clockpicker_to_'+old_row_num+'_';
			const s2 = ' id="clockpicker_to_'+new_row_num+'_';
			const t1 = ' id="clockpicker_period_'+old_row_num+'_';
			const t2 = ' id="clockpicker_period_'+new_row_num+'_';
			const u1 = ' id="c_'+old_row_num+'_';
			const u2 = ' id="c_'+new_row_num+'_';
			
			//replaces
			new_row = replaceAll_Object_Array(new_row, {a:[a1,a2], b:[b1,b2], c:[c1,c2], d:[d1,d2], e:[e1,e2], f:[f1,f2], g:[g1,g2], h:[h1,h2], i:[i1,i2], j:[j1,j2], k:[k1,k2], l:[l1,l2], m:[m1,m2], n:[n1,n2], o:[o1,o2], p:[p1,p2], q:[q1,q2], r:[r1,r2], s:[s1,s2], t:[t1,t2], u:[u1,u2]});
		}
		else { //accordion and many lines
			//nested Rows
			const a1 = ' href="#accordionPanel_';
			const a2 = ' href="#accordionPanel_C'+Copy_Num+'_';
			const b1 = ' data-parent="#accordion_';
			const b2 = ' data-parent="#accordion_C'+Copy_Num+'_';
			const c1 = ' id="Row_';
			const c2 = ' id="Row_C'+Copy_Num+'_';
			const d1 = ' id="rowItem_';
			const d2 = ' id="rowItem_C'+Copy_Num+'_';
			const e1 = ' aria-controls="accordionPanel_';
			const e2 = ' aria-controls="accordionPanel_C'+Copy_Num+'_';
			const f1 = ' name="row_no" value="';
			const f2 = ' name="row_no" value="C'+Copy_Num+'_';
			const g1 = ' data-row="';
			const g2 = ' data-row="C'+Copy_Num+'_';
			const h1 = ' data-row_item="';
			const h2 = ' data-row_item="C'+Copy_Num+'_';
			const i1 = ' data-id="';
			const i2 = ' data-id="C'+Copy_Num+'_';
			const j1 = ' name="c_';
			const j2 = ' name="c_C'+Copy_Num+'_';
			const k1 = ' id="accPanel_';
			const k2 = ' id="accPanel_C'+Copy_Num+'_';
			const l1 = ' id="AccPanel_';
			const l2 = ' id="AccPanel_C'+Copy_Num+'_';
			const m1 = ' id="accordionPanel_';
			const m2 = ' id="accordionPanel_C'+Copy_Num+'_';
			const n1 = ' id="accordion_';
			const n2 = ' id="accordion_C'+Copy_Num+'_';
			
			const ag1 = 'true?1:0;" class="c_';
			const ag2 = 'true?1:0;" class="c_C'+Copy_Num+'_';
			const ah1 = 'c_has_title c_'; 
			const ah2 = 'c_has_title c_C'+Copy_Num+'_';
			const ai1 = ' class="c_title c_';
			const ai2 = ' class="c_title c_C'+Copy_Num+'_';
			const aj1 = 'function(){$(".c_';
			const aj2 = 'function(){$(".c_C'+Copy_Num+'_';
			const ak1 = 'btn_rd_align_';
			const ak2 = 'btn_rd_align_C'+Copy_Num+'_';
			const al1 = ' class="c_has_color c_';
			const al2 = ' class="c_has_color c_C'+Copy_Num+'_';
			const am1 = '_color_a, .c_';
			const am2 = '_color_a, .c_C'+Copy_Num+'_';
			const an1 = ' class="c_color_a c_';
			const an2 = ' class="c_color_a c_C'+Copy_Num+'_';
			const ao1 = ' class="c_color_b c_';
			const ao2 = ' class="c_color_b c_C'+Copy_Num+'_';
			const ap1 = ' id="datetimepicker_';
			const ap2 = ' id="datetimepicker_C'+Copy_Num+'_';
			const aq1 = ' id="clockpicker_from_';
			const aq2 = ' id="clockpicker_from_C'+Copy_Num+'_';
			const ar1 = ' id="clockpicker_to_';
			const ar2 = ' id="clockpicker_to_C'+Copy_Num+'_';
			const as1 = ' id="clockpicker_period_';
			const as2 = ' id="clockpicker_period_C'+Copy_Num+'_';
			const at1 = 'time" id="clockpicker_';
			const at2 = 'time" id="clockpicker_C'+Copy_Num+'_';
			const au1 = ' id="c_';
			const au2 = ' id="c_C'+Copy_Num+'_';
			//replaces
			new_row = replaceAll_Object_Array(new_row, {a:[a1,a2], b:[b1,b2], c:[c1,c2], d:[d1,d2], e:[e1,e2], f:[f1,f2], g:[g1,g2], h:[h1,h2], i:[i1,i2], j:[j1,j2], k:[k1,k2], l:[l1,l2], m:[m1,m2], n:[n1,n2], 		ag:[ag1,ag2], ah:[ah1,ah2], ai:[ai1,ai2], aj:[aj1,aj2], ak:[ak1,ak2], al:[al1,al2], am:[am1,am2], an:[an1,an2], ao:[ao1,ao2], ap:[ap1,ap2], aq:[aq1,aq2], ar:[ar1,ar2], as:[as1,as2], at:[at1,at2], au:[au1,au2]});
		}
		//console.log(new_row);
		

		//replaces
		//put after original
		$(this).parent().parent('li.row_sort').after('<li id="Row_'+new_row_num+'" class="row_sort" data-row="'+new_row_num+'">'+ new_row +'</li>');
		

		//item unique name change
		//$('#Row_'+new_row_num+' .c_name').each(function(){
		$('#Row_'+new_row_num+' .row_div > table > tbody > tr > td > div.rowItem_edit > div.c_content .c_name').each(function(){
			const this_name = $(this).val();
			for (let ii=2; ii<50; ii++){
				//if name not exist
				if (!$("input[value='" + this_name + '-' + ii + "'].c_name").length) {
					//set this name
					$(this).val(this_name + '-' + ii);
					//set this name as attr('value') bcz has some problems with clone
					$(this).attr('value', this_name + '-' + ii);
					break;
				}
			}
		});


		//selects update
		//$('#Row_'+new_row_num+' .c_content select').each(function(){  --select:not(.rowItem_Select)
		$('#Row_'+new_row_num+' .row_div > table > tbody > tr > td > div.rowItem_edit > div.c_content select:not(.rowItem_Select)').each(function(){
			const new_name = this.name;
			const old_name = new_name.replace(new_row_num, old_row_num);
			$(this).val(
				$('select[name=' + old_name + ']').val()
			);
		});
		

		//html area remove
		//$('#Row_'+new_row_num+' .trumbowyg-box textarea').each(function(){
		$('#Row_'+new_row_num+' .row_div > table > tbody > tr > td > div.rowItem_div > div.trumbowyg-box textarea').each(function(){
			const new_name = this.name;
			const old_name = new_name.replace(new_row_num, old_row_num);
			//$(this).parent().after( $(this).val( $('textarea[name='+old_name+']').val() ) ).remove();
			$(this).parent().after(
				$(this).val(
					$('textarea[name=' + old_name + ']').val()
				)
			).remove();
		});


		//row_item buttons events init
		New_Row_Events(new_row_num);
		

		//items init
		//$('#Row_'+new_row_num+' td.cm_it').each(function(){
		$('#Row_'+new_row_num+' .row_div > table > tbody > tr > td.rowItem').each(function(){ //td.cm_it
			const row_item = $(this).attr('data-row_item');
			const type = $(this).find('> .rowItem_edit > input.c_type').val();

			Loaded_Item_init(type, row_item, false); //init item
		});
	});
}

//buttons New Item Init
//button_New_Row_Click("button.newRow"); //go to page init - down
//ROW #################################
//#####################################



//#####################################
//ITEMS ###############################

function Set_Items_Width(row_num) {
	//console.log('Set_Items_Width');
	const elems = $('#Row_'+row_num+' > div.row_div > table > tbody > tr > td.rowItem'); //td.cm_it
	const rowItems_num = elems.length;
	let width = 100;

	if (rowItems_num > 0) {
		width = parseInt(100/rowItems_num); //divide width/items
		$('#Row_'+row_num+' > div.row_div > table > tbody > tr > td.rowItem-init').addClass('hidden');
	}
	else {
		//make td.rowItem-init visible if no item exist
		$('#Row_'+row_num+' > div.row_div > table > tbody > tr > td.rowItem-init').removeClass('hidden');
	}

	elems.css('width',width+'%');
	elems.find('.c_content .c_width').val(width);

	const extra_width = 100 - (width*rowItems_num);
	if (extra_width) {
		$(elems[rowItems_num - 1]).css('width', width + extra_width + '%');
		$(elems.find('.c_content .c_width')[rowItems_num - 1]).val(width);
	}
}

	
button_New_Item_Click = function (element) {
	function get_New_Row_Item_num(row_num) {
		return get_max_val('#Row_'+row_num+' > .row_div > table>tbody>tr>td > .rowItem_edit > input[name="rowItem_no"]') + 1;
	}

	$(element).on('click',function() {
		//console.log('button_New_Item_Click', element);
		const row_num = $(this).attr('data-row');
		const item_num = get_New_Row_Item_num(row_num);
		const row_item = row_num+'_'+item_num;
		
		const Item__Empty__Template = replaceAll_Object(_Template_Item__Empty, {
			'{ROW_NUM}': row_num,
			'{ROW_ITEM}': row_item,
			'{ROW_ITEM_NUM}': item_num,
			'{ROW_ITEM_TYPE}': '_Empty',
			'{ROW_ITEM_TYPE_NAME}': item_type_arr['_Empty']
		});
		
		$('#Row_' + row_num + ' > div.row_div > table>tbody>tr > td.rowItem-init').before(
			Item__Empty__Template
		);
		
		Loaded_Item_init('_Empty', row_item, true); //init item
		
		Set_Items_Width(row_num); //set width
	});
}

	
//called once for each Row Item
New_Row_Item_init = function (row_num, item_num, item_type, is_new) { //for each item
	//console.log('New_Row_Item_init', row_num, item_num, item_type, is_new);
	const row_item = row_num+'_'+item_num;
	
	if (item_type == '_Html') {
		$('#rowItem_'+row_item+' > .rowItem_div > .textarea').trumbowyg({
			lang: LANG.LANG_CURRENT,
			autogrow: true,
			autogrowOnEnter: true,
			semantic: false,
			btns: trumbowyg_buttons
		}).prev().css({'min-height':'100px','height':'100px'}).parent().css('min-height', '100px');
	}
	else if (item_type == '_Date') {
		//datetimepicker
		item_Date_init('#rowItem_' + row_item + ' > .rowItem_div #datetimepicker_' + row_item);
	}
	else if (item_type == '_Time') {
		//clockpicker
		item_Time_init('#rowItem_' + row_item + ' > .rowItem_div #clockpicker_' + row_item);
	}
	else if (item_type == '_Period') {
		//clockpicker
		item_Time_Period_init('#rowItem_' + row_item + ' > .rowItem_div #clockpicker_from_' + row_item);
		//clockpicker
		item_Time_Period_init('#rowItem_' + row_item + ' > .rowItem_div #clockpicker_to_' + row_item);
		//clockpicker
		item_Time_Period_init('#rowItem_' + row_item + ' > .rowItem_div #clockpicker_period_' + row_item);

		//Time From -> To = Period auto calculate 
		item_Time_From_To_Period_Calc_init(
			'#c_' + row_item + '_PRDfrom',
			'#c_' + row_item + '_PRDto',
			'#c_' + row_item + '_PRDperiod'
		);
	}
	else if (item_type == '_RadioButtons' || item_type == '_Radio_Buttons_Select_Only') {
		//Check and radio input styles
		$('#rowItem_'+row_item+' > .rowItem_div input.check_radio').iCheck({
			checkboxClass: 'icheckbox_square-aero',
			radioClass: 'iradio_square-aero'
		});		
	}
	else if (item_type == '_Accordion') {
		Accordion_init(item_type, row_item);
	}


	//enable text select on inputs -firefox
	$('#Row_'+row_num+' > div.row_div > table > tbody > tr.row_item_sort').find('input,textarea,select,.trumbowyg-editor').preventDisableSelection();
	
	//console.log(row_num, item_num, item_type, is_new);
	//set width //this break saved widths and puts the default --so we run it only on new
	if (is_new) {
		Set_Items_Width(row_num);
	}
}


function Load_Item(type, item) {
	const post_data = {
		row_item_type: type,
		row_item: item
	};
	$.post("forms/ajax.form_row_item.php", post_data, function(data, result) {
		if (data!='') {
			//replace with new item --outerHTML
			$('#rowItem_' + item).replaceWith(data);

			Loaded_Item_init(type, item, true);
		}
	});
}

	
function Load_Item__Dropdown(type, item, dd, opt, has_color, color) {
	const post_data = {
		row_item: item,
		row_item_type: type,
		dd: dd,
		opt: opt,
		has_color: has_color,
		color: color
	};
	$.post("forms/ajax.form_row_item.php", post_data, function(data, result) {
		if (data!='') {
			//_Dropdown_Select_Only
			//replace with new item --outerHTML
			$('#rowItem_' + item + ' > .rowItem_div > div.styled-select > select').replaceWith(data);
		}
	});
}

	
function Load_Item__Radio(type, item, rdd, has_title, title, talign, has_color, color) {
	const post_data = {
		row_item: item,
		row_item_type: type,
		rdd: rdd,
		has_title: has_title,
		title: title,
		talign: talign,
		has_color: has_color,
		color: color
	};
	$.post("forms/ajax.form_row_item.php", post_data, function(data, result) {
		if (data!='') {
			//_Radio_Buttons_Select_Only
			//replace with new item --outerHTML
			$('#rowItem_' + item + ' > .rowItem_div > div.radio_div').replaceWith(data);

			const row_item = item.split('_');
			New_Row_Item_init(row_item[0], row_item[1], type, true); //init item
		}
	});
}

	
function Loaded_Item_init(type, item, is_new) {
	//console.log('Loaded_Item_init', type, item, is_new);
	$('#rowItem_'+item).hover(function() { //on hover efe
		$(this).find('> .rowItem_edit > .c_handler > .rowItem_Drag').removeClass('trans10');
		$(this).find('> .rowItem_edit > .c_handler > .rowItem_EditLink').removeClass('trans30');
		$(this).find('> .rowItem_div > .accordion_group > div > .accPanel-add').removeClass('trans10'); //if accordion
	}, function() {
		$(this).find('> .rowItem_edit > .c_handler > .rowItem_Drag').addClass('trans10');
		$(this).find('> .rowItem_edit > .c_handler > .rowItem_EditLink').addClass('trans30');
		$(this).find('> .rowItem_div > .accordion_group > div > .accPanel-add').addClass('trans10'); //if accordion
	});


	//init item
	const row_item = item.split('_');
	New_Row_Item_init(row_item[0], row_item[1], type, is_new);

	//init controls
	rowItem_EditLink_init($('#rowItem_'+item+' > .rowItem_edit > .c_handler > .rowItem_EditLink'), type, item);
}

/* --gone to the page init at the end
//row_item button init
$('.rowItem-add').each(function(){
	const row = $(this).attr('data-row');
	New_Row_Events(row);
	button_New_Item_Click(this);
});
//items init
$('td.rowItem').each(function(){ //td.cm_it
	const page_row_item = $(this).attr('data-row_item');
	const tp = $(this).find('.c_type').val();
	Loaded_Item_init(tp, page_row_item, false); //init item
});*/
//ITEMS ###############################
//#####################################



//#####################################
//Accordion Item ######################

//button Acc newRow --called once in a AccRow
button_New_AccordionPanel_Row_Click = function (element) {
	//console.log('button_New_AccordionPanel_Row_Click_init', element);
	$(element).on('click',function() {
		//console.log('button_New_AccordionPanel_Row_Click', $(this).attr('id'));

		const row_num = get_max_val('input[name="row_no"]') + 1;
		
		const Row_Template = replaceAll_Object(_Template_Row, {'{ROW_NUM}':row_num});
		
		$(this).parent().prev('ul.row_sortable').append(Row_Template);
		
		New_Row_Events(row_num);
	});
}

function Load_Item__Accordion_Panel(type, item, acc_item) {
	//console.log('Load_Item__Accordion_Panel', type, item, acc_item);
	const post_data = {
		row_item: item,
		row_item_type: type,
		acc_item: acc_item
	};
	$.post("forms/ajax.form_row_item.php", post_data, function(data, result) {
		if (data!='') {
			//_Accordion_Panel
			//place before button
			$('#accordion_' + item + ' > div > .accPanel-add').parent().before(data);

			Accordion_Panel_init(type, item, acc_item, true);
		}
	});
}

function Accordion_init(type, item) {
	//console.log('Accordion_init', type, item);
	$('#accordion_'+item+' > .AccPanel').hover(function() { //on hover efe
		$(this).find('> .rowItem_edit > .c_handler_acc > .rowItem_AccPanel_Drag').removeClass('trans10');
		$(this).find('> .rowItem_edit > .c_handler_acc > .rowItem_AccPanel_EditLink').removeClass('trans30');
		$(this).find('> .rowItem_edit > .c_handler_acc').removeClass('trans50');
		$(this).find('> .rowItem_div > .panel > .accordionPanel > .panel-body > div > .newRow').removeClass('trans10'); //newRow button
	}, function() {
		$(this).find('> .rowItem_edit > .c_handler_acc > .rowItem_AccPanel_Drag').addClass('trans10');
		$(this).find('> .rowItem_edit > .c_handler_acc > .rowItem_AccPanel_EditLink').addClass('trans30');
		$(this).find('> .rowItem_edit > .c_handler_acc').addClass('trans50');
		$(this).find('> .rowItem_div > .panel > .accordionPanel > .panel-body > div > .newRow').addClass('trans10'); //newRow button
	});
	

	$('#accordion_'+item+' > div > .accPanel-add').on('click',function() {
		//console.log('button_New_Accordion_Panel_Click');
		function get_New_Row_Item_num(row_item) {
			return get_max_val('#accordion_'+row_item+' input.c_acc_no') + 1; 
		}

		const row_num = $(this).attr('data-row');
		const item_num = $(this).attr('data-item');
		const row_item = row_num+'_'+item_num;
		const acc_num = get_New_Row_Item_num(row_item);

		//Loaded_Item_init('_Accordion_Panel', row_item, true); //init item
		Load_Item__Accordion_Panel('_Accordion_Panel', row_item, acc_num);
	});
	
	
	//init AccPanel dragging --need to called once in a Accordion
	if (!V_HAVE_DATA) {
		$("#accordion_"+item).sortable({  
			items:"> .AccPanel",
			//connectWith: "tr",
			//containment:"ul.page_sortable", //moves in 
			handle: "> .rowItem_edit > .c_handler_acc > .rowItem_AccPanel_Drag",
			revert: 300,
			opacity: '.5',
			tolerance: "intersect",
			placeholder:"ui-state-highlight placeholder-item-td",
		}).disableSelection();
	}


	//init AccPanel controls
	$('#accordion_'+item+' > .AccPanel').each(function(i,el) {
		const acc_item = $(this).find('> .rowItem_edit > input.c_acc_no').val();

		button_New_AccordionPanel_Row_Click('#accPanel_' + $(this).attr('data-id') + '_newRow');
		
		rowItem_AccordionPanel_EditLink_init($(this).find('> .rowItem_edit > .c_handler_acc > a.rowItem_AccPanel_EditLink'), type, item, acc_item);

		
		//init AccPanel Row dragging --need to called once in a AccPanel
		if (!V_HAVE_DATA) {
			$(this).find('> .rowItem_div > .panel  > .accordionPanel > .panel-body > ul.row_sortable').sortable({  
				items:"> li.row_sort",
				connectWith: "ul.row_sortable",
				containment:"ul.page_sortable", //moves in 
				handle: ".Row-drag",
				revert: 300,
				opacity: '.5',
				tolerance: "intersect",
				placeholder:"ui-state-highlight placeholder-item",
				start: function (e, ui) { ui.placeholder.height(ui.helper.outerHeight()); ui.placeholder.height(ui.helper.outerHeight()); }
			}).disableSelection();
		}
	});
}


function Accordion_Panel_init(type, item, acc_item, is_new) {
	//console.log('Accordion_Panel_init', type, item, acc_item, is_new);
	$('#AccPanel_'+item+'_'+acc_item).hover(function() { //on hover efe
		$(this).find('> .rowItem_edit > .c_handler_acc > .rowItem_AccPanel_Drag').removeClass('trans10');
		$(this).find('> .rowItem_edit > .c_handler_acc > .rowItem_AccPanel_EditLink').removeClass('trans30');
		$(this).find('> .rowItem_edit > .c_handler_acc').removeClass('trans50');
		$(this).find('> .rowItem_div > .panel > .accordionPanel > .panel-body > div > .newRow').removeClass('trans10'); //newRow button
	}, function() {
		$(this).find('> .rowItem_edit > .c_handler_acc > .rowItem_AccPanel_Drag').addClass('trans10');
		$(this).find('> .rowItem_edit > .c_handler_acc > .rowItem_AccPanel_EditLink').addClass('trans30');
		$(this).find('> .rowItem_edit > .c_handler_acc').addClass('trans50');
		$(this).find('> .rowItem_div > .panel > .accordionPanel > .panel-body > div > .newRow').addClass('trans10'); //newRow button
	});

	//init item
	//const row_item = item.split('_');
	//New_Row_Item_init(row_item[0], row_item[1], type, is_new);

	//init controls
	button_New_AccordionPanel_Row_Click('#accPanel_' + item + '_' + acc_item + '_newRow');
	
	rowItem_AccordionPanel_EditLink_init($('#AccPanel_' + item + '_' + acc_item + ' > .rowItem_edit > .c_handler_acc > a.rowItem_AccPanel_EditLink'), type, item, acc_item);
	
	
	//init AccPanel Row dragging --need to called once in a AccPanel
	if (!V_HAVE_DATA) {
		$('#AccPanel_'+item+'_'+acc_item).find('> .rowItem_div > .panel  > .accordionPanel > .panel-body > ul.row_sortable').sortable({  
			items:"> li.row_sort",
			connectWith: "ul.row_sortable",
			containment:"ul.page_sortable", //moves in 
			handle: ".Row-drag",
			revert: 300,
			opacity: '.5',
			tolerance: "intersect",
			placeholder:"ui-state-highlight placeholder-item",
			start: function (e, ui) { ui.placeholder.height(ui.helper.outerHeight()); ui.placeholder.height(ui.helper.outerHeight()); }
		}).disableSelection();
	}
}

function rowItem_AccordionPanel_EditLink_init($element, $type, $item, $acc_item) {
	//console.log('rowItem_AccordionPanel_EditLink_init', $element, $type, $item, $acc_item, item_type_arr[$type]);
	$item = $item + '_' + $acc_item;
	$element.off('click').on('click', function() {
		if (typeof $element.data('bs.popover') !== "undefined") {
			setTimeout(function () {
				$element.popover('destroy'); //stop blinking this way
			}, 0);
		} else {
			$element.popover({
				html: true,
				placement: 'top',
				container: $('body'),
				title: function () { return item_type_arr[$type]; },
				content: function () {
					return  '<form class="form_popup" id="form_popup_'+$item+'">'+
								$('#AccPanel_'+$item+' > .rowItem_edit > .c_content').html()+
							'</form>'; 
				},
				template: ''+
					'<div class="popover" role="tooltip">' +
						'<div class="arrow"></div>'+
						(V_HAVE_DATA ? '' :
							'<span id="accPanelRemove_'+$item+'" class="rowItem-remove">'+
								'<i class="fa fa-times-circle" title="'+LANG.FORMS.ITEM_DELETE+'"></i>'+
							'</span>'
						)+
						'<h3 class="popover-title"></h3>'+
						'<div class="popover-content"></div>'+
						'<div class="popover-footer">'+
							'<button type="button" class="btn btn-primary popover-save">'+
								'<i class="fa fa-floppy-o"></i> '+LANG.FORMS.ITEM_SAVE+' '+
							'</button> &nbsp; '+
							'<button type="button" class="btn btn-default popover-cancel" title="'+LANG.FORMS.ITEM_CANCEL+'">'+
								' &nbsp; <i class="fa fa-times"></i> &nbsp; '+
							'</button>'+
						'</div>'+
					'</div>'
			}).on('shown.bs.popover', function () {
				const popover = $element.data('bs.popover');
				if (typeof popover !== "undefined") {
					const $tip = popover.tip();
					
					//update form vals
					Get_Edit_Popup_Form($type, $item, $tip);
					
					$tip.find('.popover-cancel').on('click',function() {
						$element.popover('destroy'); //stop blinking this way
					});

					//width
					$tip.find('.c_width').spinner({
						min: 5,
						max: 100
					});
					
					//save
					$tip.find('.popover-save').on('click',function() {
						//popover.hide();
						if ($('form#form_popup_' + $item).valid())
						{
							Save_Popup_Form($type, $item, $tip);

							$element.popover('destroy'); //stop blinking this way
						}
					});

					//rowItemRemove_ click
					//$('#AccPanel_'+$item+' .accPanel-remove').confirmation({ //not work is on popup
					$("#accPanelRemove_"+$item).confirmation({
						href: 'javascript:void(0)',
						title: LANG.ARE_YOU_SURE, placement: 'bottom',
						btnOkLabel: LANG.YES, btnOkClass: 'btn btn-sm btn-success mr10',
						btnCancelLabel: LANG.NO, btnCancelClass: 'btn btn-sm btn-danger',
						onConfirm: function(e, button) {
							$element.popover('destroy'); //stop blinking this way

							$('#AccPanel_'+$item).remove(); //remove <td>
						}
					});
				}
			}).popover('show');
		}
	});
}
//Accordion Item ######################
//#####################################




//#####################################
//ITEM EDIT-SAVE  _CONTENT ############
	
//create of item-edit popups
function rowItem_EditLink_init($element, $type, $item) {
	//console.log('rowItem_EditLink_init', $element, $type, $item, item_type_arr[$type]);
	$element.off('click').on('click', function() {
		if (typeof $element.data('bs.popover') !== "undefined") {
			setTimeout(function(){
				$element.popover('destroy'); //stop blinking this way
			}, 0);
		} else {
			$element.popover({
				html: true,
				placement: 'auto',
				container: $('body'),
				title: function () {
					return item_type_arr[$type];
				},
				content: function () {
					return  '<form class="form_popup" id="form_popup_' + $item + '">' +
								$('#rowItem_' + $item + ' .c_content').html() + 
							'</form>';
				},
				template: ''+
					'<div class="popover" role="tooltip">'+
						'<div class="arrow"></div>'+
					(V_HAVE_DATA ? '' :
						'<span id="rowItemClear_'+$item+'" class="rowItem-clear">'+
							'<i class="fa fa-recycle" title="'+LANG.FORMS.ITEM_RESET+'"></i>'+
						'</span>'+
						'<span id="rowItemRemove_'+$item+'" class="rowItem-remove">'+
							'<i class="fa fa-times-circle" title="'+LANG.FORMS.ITEM_DELETE+'"></i>'+
						'</span>'
					)+
						'<h3 class="popover-title"></h3>'+
						'<div class="popover-content"></div>'+
						'<div class="popover-footer">'+
							'<button type="button" class="btn btn-primary popover-save">'+
								'<i class="fa fa-floppy-o"></i> '+LANG.FORMS.ITEM_SAVE+' '+
							'</button>'+
							' &nbsp; '+
							'<button type="button" class="btn btn-default popover-cancel" title="'+LANG.FORMS.ITEM_CANCEL+'">'+
								' &nbsp; <i class="fa fa-times"></i> &nbsp; '+
							'</button>'+
						'</div>'+
					'</div>'
			}).on('shown.bs.popover', function () {
				const popover = $element.data('bs.popover');
				if (typeof popover !== "undefined") {
					const $tip = popover.tip();
					
					//update form vals
					Get_Edit_Popup_Form($type, $item, $tip);
					
					//width
					$tip.find('.c_width').spinner({
						min: 5,
						max: 100 
					});

					//save
					$tip.find('.popover-save').on('click',function() {
						//popover.hide();
						//console.log($tip.find('.popover-content').html());
						if ($('form#form_popup_' + $item).valid())
						{
							Save_Popup_Form($type, $item, $tip);

							$element.popover('destroy');
						}
					});

					//cancel
					$tip.find('.popover-cancel').on('click',function() {
						$element.popover('destroy');
					});

					//rowItemClear_ click
					$("#rowItemClear_"+$item).confirmation({
						href: 'javascript:void(0)',
						title: LANG.FORMS.ITEM_RESET +' - '+ LANG.ARE_YOU_SURE, 
						placement: 'bottom',
						btnOkLabel: LANG.YES, btnOkClass: 'btn btn-sm btn-success mr10',
						btnCancelLabel: LANG.NO, btnCancelClass: 'btn btn-sm btn-danger',
						onConfirm: function(e, button) {
							$element.popover('destroy');

							Load_Item('_Empty', $item);
						}
					});

					//rowItemRemove_ click
					$("#rowItemRemove_"+$item).confirmation({
						href: 'javascript:void(0)',
						title: LANG.FORMS.ITEM_DELETE +' - '+ LANG.ARE_YOU_SURE, 
						placement: 'bottom',
						btnOkLabel: LANG.YES, btnOkClass: 'btn btn-sm btn-success mr10',
						btnCancelLabel: LANG.NO, btnCancelClass: 'btn btn-sm btn-danger',
						onConfirm: function(e, button) {
							$element.popover('destroy');
							$('#rowItem_' + $item).remove(); //remove <td>
							
							const row_item = $item.split('_');
							Set_Items_Width(row_item[0], row_item[1]);
						}
					});
				}
			}).popover('show');
		}
	});
}

//Copy to popup the item form values
function Get_Edit_Popup_Form(type, item, popup) {
	//console.log('Get_Edit_Popup_Form', type, item, popup);
	const html = popup.find('.popover-content input, .popover-content select');
	if (type == '_Empty') {
		//select init --not needed
		/*popup.find('.rowItem_Select').chosen({
			width: '100%;min-width:140px',
			no_results_text: LANG.NO_RESULTS,
			search_contains: false,
			disable_search_threshold: 10
		});*/
	}
	else {
		html.each(function () {
			const t_name = $(this).attr('name');
			const t_elems = $('input[name=' + t_name + '], select[name=' + t_name + ']');

			if (t_elems.length == 2 && t_elems[0] && t_elems[1]) {
				$(t_elems[1]).val($(t_elems[0]).val()); //src-val to edit-val
				$(t_elems[1]).attr('value', $(t_elems[0]).val()); //set attr(value) for sure
				
				if (t_name == 'c_' + item + '_required' && $(t_elems[0]).val() == 1) {
					if ($(t_elems[0]).val() == 1) {
						$(t_elems[1]).prev().attr('checked', true);
					} else {
						$(t_elems[1]).prev().attr('checked', false);
					}
				}
				else if (t_name == 'c_' + item + '_color' && $(t_elems[0]).val() == 1) {
					if ($(t_elems[0]).val() == 1) {
						$(t_elems[1]).prev().attr('checked', true);
					} else {
						$(t_elems[1]).prev().attr('checked', false);
					}
				}
				else if (t_name == 'c_' + item + '_has_title' && $(t_elems[0]).val() == 1) {
					if ($(t_elems[0]).val() == 1) {
						$(t_elems[1]).prev().attr('checked', true);
					} else {
						$(t_elems[1]).prev().attr('checked', false);
					}
				}
			}
			else {
				//t_elems.length = 4 //_numintdec
				if (t_name == 'c_' + item + '_numintdec') {
					if ($(t_elems[0]).is(':checked')) {
						$(t_elems[2]).parent().trigger("click"); //int
					} else {
						$(t_elems[3]).parent().trigger("click"); //dec
					}
				}
				//t_elems.length = 6 //_talign
				else if (t_name == 'c_' + item + '_talign') {
					if ($(t_elems[1]).is(':checked')) {
						$(t_elems[4]).parent().trigger("click"); //center
					}
					else if ($(t_elems[2]).is(':checked')) {
						$(t_elems[5]).parent().trigger("click"); //right
					}
					else {
						$(t_elems[3]).parent().trigger("click"); //left
					}
				}
				//t_elems.length = 6 //_align
				else if (t_name == 'c_' + item + '_align') {
					if ($(t_elems[1]).is(':checked')) {
						$(t_elems[4]).parent().trigger("click"); //center
					}
					else if ($(t_elems[2]).is(':checked')) {
						$(t_elems[5]).parent().trigger("click"); //right
					}
					else {
						$(t_elems[3]).parent().trigger("click"); //left
					}
				}
				//t_elems.length = 6 //_bold
				else if (t_name == 'c_' + item + '_bold') {
					if ($(t_elems[1]).is(':checked')) {
						$(t_elems[4]).parent().trigger("click"); //semibold
					}
					else if ($(t_elems[2]).is(':checked')) {
						$(t_elems[5]).parent().trigger("click"); //bold
					}
					else {
						$(t_elems[3]).parent().trigger("click"); //normal
					}
				}
				//t_elems.length = 4 //_accType
				else if (t_name == 'c_' + item + '_accType') {
					if ($(t_elems[0]).is(':checked')) {
						$(t_elems[2]).parent().trigger("click"); //basic
					} else {
						$(t_elems[3]).parent().trigger("click"); //multiple
					}
				}
				//t_elems.length = 4 //_accOpen
				else if (t_name == 'c_' + item + '_accOpen') {
					if ($(t_elems[0]).is(':checked')) {
						$(t_elems[2]).parent().trigger("click"); //opened
					} else {
						$(t_elems[3]).parent().trigger("click"); //closed
					}
				}
				//t_elems.length = 6 //_accAlign
				else if (t_name == 'c_' + item + '_accAlign') {
					if ($(t_elems[1]).is(':checked')) {
						$(t_elems[4]).parent().trigger("click"); //center
					}
					else if ($(t_elems[2]).is(':checked')) {
						$(t_elems[5]).parent().trigger("click"); //right
					}
					else {
						$(t_elems[3]).parent().trigger("click"); //left
					}
				}
				//t_elems.length = 6 //_accBold
				else if (t_name == 'c_' + item + '_accBold') {
					if ($(t_elems[1]).is(':checked')) {
						$(t_elems[4]).parent().trigger("click"); //semibold
					}
					else if ($(t_elems[2]).is(':checked')) {
						$(t_elems[5]).parent().trigger("click"); //bold
					}
					else {
						$(t_elems[3]).parent().trigger("click"); //normal
					}
				}
			}
		});
		

		//focus on input
		if ($('.form_popup .c_name')[0] != undefined) {
			$('.form_popup .c_name')[0].focus();
		}
		else if ($('.form_popup .c_label')[0] != undefined) {
			$('.form_popup .c_label')[0].focus();
		}
	}
}

	
//Copy the changed popup values Back to the item form values
function Save_Popup_Form(type, item, popup) {
	//console.log('Save_Popup_Form', type, item, popup);
	const html = popup.find('.popover-content input, .popover-content select');
	if (type == '_Empty') {
		const new_item_type = popup.find('.rowItem_Select').val();
		Load_Item(new_item_type, item);
	}
	else {
		html.each(function () {
			//console.log(item, type, $(this).attr('name'), $(this).val());
			//put the input vals from the popup form [1] to item content form [0]
			const t_name = $(this).attr('name');
			const t_elems = $('input[name=' + t_name + '], select[name=' + t_name + ']');
			
			const old_val = $(t_elems[0]).val();
			const new_val = $(t_elems[1]).val();

			if (t_elems.length==2 && t_elems[0] && t_elems[1]) {
				//copy val to element c_content store
				if (t_name == 'c_' + item + '_talign') {
					//Radio talign --> never comes here --only catch input,select
				}
				else if (t_name == 'c_' + item + '_rdd' || t_name == 'c_' + item + '_dd') { //Select		
					$(t_elems[0]).val(new_val); //edit-val to src-val
				}
				else {
					$(t_elems[0]).val(new_val); //edit-val to src-val
					$(t_elems[0]).attr('value', new_val); //set attr(value) for sure
				}
				
				//update element with the changes
				if (t_name == 'c_' + item + '_width') {
					$('#rowItem_' + item).css('width', new_val + '%');
				}
				else if (t_name == 'c_' + item + '_label') {
					$('#rowItem_' + item + ' > .rowItem_div > .label_txt').html(new_val);
				}
				else if (t_name == 'c_' + item + '_accLabel') {
					$('#AccPanel_' + item + ' > .rowItem_div > .panel > .panel-heading > .panel-title > a.accLabel').html(' ' + new_val);
				}
				else if (t_name == 'c_' + item + '_placeholder') {
					if ($('#rowItem_' + item + ' > .rowItem_div > input').length) { //normal
						$('#rowItem_' + item + ' > .rowItem_div > input').attr('placeholder', new_val);
					}
					else { //with div -> Period -only period
						if ($('#rowItem_' + item + ' > .rowItem_div > div.period > input.period').length) { //period
							$('#rowItem_' + item + ' > .rowItem_div > div.period').next('div.period').next('div.period').find('input.period').attr('placeholder', new_val); //3rd div
						}
						else { //with div -> Date, Time
							$('#rowItem_' + item + ' > .rowItem_div > div > input').attr('placeholder', new_val);
						}
					}
				}
				else if (t_name == 'c_' + item + '_placeholder_from') { //period
					$('#rowItem_' + item + ' > .rowItem_div > div.period > input.period').attr('placeholder', new_val); //1st div
				}
				else if (t_name == 'c_' + item + '_placeholder_to') { //period
					$('#rowItem_' + item + ' > .rowItem_div > div.period').next('div.period').find('input.period').attr('placeholder', new_val); //2nd div
				}
				else if (t_name == 'c_' + item + '_required') { //required
					$(t_elems[0]).prev().attr('checked', new_val == 1 ? true : false);
					if (new_val == 1) {
						$(t_elems[0]).closest('td').addClass('required');
					}
					else {
						$(t_elems[0]).closest('td').removeClass('required');
					}
				}
				else if (t_name == 'c_' + item + '_has_color') { //radio has color
					last_last_has_color = old_val;
					last_has_color = new_val;
					$(t_elems[0]).prev().attr('checked', new_val == 1 ? true : false);
				}
				else if (t_name == 'c_' + item + '_color_a') { //color start
					last_last_color_a = old_val;
					last_color_a = new_val;
					$(t_elems[0]).prev().attr('checked', new_val == 1 ? true : false);
				}
				else if (t_name == 'c_' + item + '_color_b') { //color end
					last_last_color_b = old_val;
					last_color_b = new_val;
					$(t_elems[0]).prev().attr('checked', new_val == 1 ? true : false);
				}
				else if (t_name == 'c_' + item + '_has_title') {//radio has title
					last_radio_has_title = new_val;
					$(t_elems[0]).prev().attr('checked', new_val == 1 ? true : false);

					if (new_val == 1) {
						$('#rowItem_' + item + ' > .rowItem_div > .radio_div .tr_radio_title').removeClass('hidden');
					}
					else {
						$('#rowItem_' + item + ' > .rowItem_div > .radio_div .tr_radio_title').addClass('hidden');
					}
				}
				else if (t_name == 'c_' + item + '_title') { //radio title
					last_radio_title = new_val;
					$('#rowItem_' + item + ' > .rowItem_div > .radio_div .tr_radio_title > .s_radio_title').html(new_val);
				}
				else if (t_name == 'c_' + item + '_opt') { //dropdown
					last_dd_opt = new_val;
					$('#rowItem_' + item + ' > .rowItem_div > .styled-select > select option:first').text(last_dd_opt);
				}
				else if (t_name == 'c_' + item + '_dd') { //dropdown
					$('#rowItem_' + item + ' > .rowItem_div > .styled-select > select option:first').text(last_dd_opt);
					if (old_val != new_val ||
						last_last_has_color != last_has_color ||
						last_last_color_a != last_color_a ||
						last_last_color_b != last_color_b)
					{
						Load_Item__Dropdown('_Dropdown_Select_Only', item, new_val, last_dd_opt, last_has_color, last_color_a + '|' + last_color_b);
					}
				}
				else if (t_name == 'c_'+item+'_rdd') { //radio
					if (old_val != new_val ||
						last_last_has_color != last_has_color || 
						last_last_color_a != last_color_a ||
						last_last_color_b != last_color_b)
					{
						//console.log('_Radio_Buttons_Select_Only', item, new_val, last_radio_has_title, last_radio_title, last_radio_talign);
						Load_Item__Radio('_Radio_Buttons_Select_Only', item, new_val, last_radio_has_title, last_radio_title, last_radio_talign, last_has_color, last_color_a+'|'+last_color_b);
					}
				}
				else if (t_name == 'c_'+item+'_acc') { //accordion
					if (old_val != new_val ||
						last_last_has_color != last_has_color || 
						last_last_color_a != last_color_a ||
						last_last_color_b != last_color_b)
					{
						//console.log('_Accordion_Panel', item, new_val, last_radio_has_title, last_radio_title, last_radio_talign);
						Load_Item__Accordion_Panel('_Accordion_Panel', item, new_val, last_radio_has_title, last_radio_title, last_radio_talign, last_has_color, last_color_a+'|'+last_color_b);
					}
				}
			}
			else {
				if (t_name == 'c_' + item + '_numintdec') { //t_elems.length=4 //_numintdec
					//select radio by click on label
					if ($(t_elems[2]).is(':checked')) {
						$(t_elems[0]).parent().trigger("click"); //int
					} else {
						$(t_elems[1]).parent().trigger("click"); //dec
					}
				}
				else if (t_name == 'c_' + item + '_talign') { //t_elems.length=6 //_talign
					//select radio by click on label
					let align = 'left';
					if ($(t_elems[4]).is(':checked')) {
						$(t_elems[1]).parent().trigger("click"); //center
						align = 'center';
					}
					else if ($(t_elems[5]).is(':checked')) {
						$(t_elems[2]).parent().trigger("click"); //right
						align = 'right';
					}
					else {
						$(t_elems[0]).parent().trigger("click"); //left
					}
					
					last_radio_talign = align;
					//set saved talign
					$('#rowItem_' + item + ' > .rowItem_div > .radio_div .tr_radio_title > .s_radio_title').css('text-align', align);
				}
				else if (t_name == 'c_' + item + '_align') { //t_elems.length=6 //_align
					//select radio by click on label
					let align = 'left';
					if ($(t_elems[4]).is(':checked')) {
						$(t_elems[1]).parent().trigger("click"); //center
						align = 'center';
					}
					else if ($(t_elems[5]).is(':checked')) {
						$(t_elems[2]).parent().trigger("click"); //right
						align = 'right';
					}
					else $(t_elems[0]).parent().trigger("click"); //left

					//set saved align
					$('#rowItem_' + item).css('text-align', align);
				}
				else if (t_name == 'c_' + item + '_bold') {
					//select radio by click on label
					let bold = '2';
					if ($(t_elems[4]).is(':checked')) {
						$(t_elems[1]).parent().trigger("click");
						bold = '1';
					}
					else if ($(t_elems[5]).is(':checked')) {
						$(t_elems[2]).parent().trigger("click");
						bold = '0';
					}
					else {
						$(t_elems[0]).parent().trigger("click"); //2
					}

					//set saved bold
					$('#rowItem_' + item).css('font-weight', (bold == '0' ? '700' : (bold == '1' ? '600' : '500')));
				}
				else if (t_name == 'c_' + item + '_accType') { //t_elems.length=4 //_accType
					//select radio by click on label
					if ($(t_elems[2]).is(':checked')) {
						$(t_elems[0]).parent().trigger("click"); //basic
					} else {
						$(t_elems[1]).parent().trigger("click"); //multiple
					}
				}
				else if (t_name == 'c_' + item + '_accOpen') { //t_elems.length=4 //_accOpen
					//select radio by click on label
					if ($(t_elems[2]).is(':checked')) {
						$(t_elems[0]).parent().trigger("click"); //opened
					} else {
						$(t_elems[1]).parent().trigger("click"); //closed
					}
				}
				else if (t_name == 'c_' + item + '_accAlign') { //t_elems.length=6 //_accAlign
					//select radio by click on label
					let align = 'left';
					if ($(t_elems[4]).is(':checked')) {
						$(t_elems[1]).parent().trigger("click"); //center
						align = 'center';
					}
					else if ($(t_elems[5]).is(':checked')) {
						$(t_elems[2]).parent().trigger("click"); //right
						align = 'right';
					}
					else $(t_elems[0]).parent().trigger("click"); //left

					//set saved align
					$('#AccPanel_' + item + ' > .rowItem_div > .panel > .panel-heading > .panel-title > a.accLabel').css('text-align', align);
				}
				else if (t_name == 'c_'+item+'_accBold') {
					//select radio by click on label
					let bold = '2';
					if ($(t_elems[4]).is(':checked')) {
						$(t_elems[1]).parent().trigger("click");
						bold = '1';
					}
					else if ($(t_elems[5]).is(':checked')) {
						$(t_elems[2]).parent().trigger("click");
						bold = '0';
					}
					else {
						$(t_elems[0]).parent().trigger("click"); //2
					}

					//set saved bold
					$('#AccPanel_' + item + ' > .rowItem_div > .panel > .panel-heading > .panel-title > a.accLabel').css('font-weight', (bold == '0' ? '700' : (bold == '1' ? '600' : '500')));
				}
			}
		});
	}
}
//ITEM EDIT-SAVE ######################
//#####################################



//#####################################
//GET FORM JSON #######################
get_Form_JSON = function() 
{
	function parse_Empty(c_content, it) {
		const elems = $(c_content);
		const obj = {type:'_Empty'};
		obj.no = it+1;
		return obj;
	}
	function parse_Space(c_content, it) {
		const elems = $(c_content);
		const obj = {type:'_Space'};
		obj.no = it+1;
		obj.width = elems.find('.c_width').val();
		return obj;
	}
	function parse_Line(c_content, it) {
		const elems = $(c_content);
		const obj = {type:'_Line'};
		obj.no = it+1;
		obj.width = elems.find('.c_width').val();
		return obj;
	}
	function parse_Label(c_content, it) {
		const elems = $(c_content);
		const obj = {type:'_Label'};
		obj.no = it+1;
		obj.label = elems.find('.c_label').val();
		obj.align = elems.find('input[name$=_align]:checked').val(); //$=ends with
		obj.bold = elems.find('input[name$=_bold]:checked').val(); //$=ends with
		obj.width = elems.find('.c_width').val();
		return obj;
	}
	function parse_Html(c_content, it) {
		const elems = $(c_content);
		const obj = {type:'_Html'};
		obj.no = it+1;
		obj.text = elems.parent().next().find('.c_text').val();
		obj.width = elems.find('.c_width').val();
		return obj;
	}
	function parse_Text(c_content, it) {
		const elems = $(c_content);
		const obj = {type:'_Text'};
		obj.no = it+1;
		obj.unid = get_unique_id();
		obj.name = elems.find('.c_name').val();
		obj.placeholder = elems.find('.c_placeholder').val();
		obj.required = elems.find('.c_required').val();
		obj.width = elems.find('.c_width').val();
		json_names[obj.unid] = [obj.name, obj.type];
		return obj;
	}
	function parse_Textarea(c_content, it) {
		const elems = $(c_content);
		const obj = {type:'_Textarea'};
		obj.no = it+1;
		obj.unid = get_unique_id();
		obj.name = elems.find('.c_name').val();
		obj.placeholder = elems.find('.c_placeholder').val();
		obj.required = elems.find('.c_required').val();
		obj.width = elems.find('.c_width').val();
		json_names[obj.unid] = [obj.name, obj.type];
		return obj;
	}
	function parse_Number(c_content, it) {
		const elems = $(c_content);
		const obj = {type:'_Number'};
		obj.no = it+1;
		obj.unid = get_unique_id();
		//obj.unid = elems.find('.c_unid').val();
		obj.name = elems.find('.c_name').val();
		obj.placeholder = elems.find('.c_placeholder').val();
		obj.required = elems.find('.c_required').val();
		obj.min = elems.find('.c_min').val();
		obj.max = elems.find('.c_max').val();
		obj.decimal = $(elems.find('input[name$=_numintdec]')[1]).is(':checked'); //$=ends with
		obj.width = elems.find('.c_width').val();
		json_names[obj.unid] = [obj.name, obj.type];
		return obj;
	}
	function parse_Date(c_content, it) {
		const elems = $(c_content);
		const obj = {type:'_Date'};
		obj.no = it+1;
		obj.unid = get_unique_id();
		obj.name = elems.find('.c_name').val();
		obj.placeholder = elems.find('.c_placeholder').val();
		obj.required = elems.find('.c_required').val();
		obj.width = elems.find('.c_width').val();
		json_names[obj.unid] = [obj.name, obj.type];
		return obj;
	}
	function parse_Time(c_content, it) {
		const elems = $(c_content);
		const obj = {type:'_Time'};
		obj.no = it+1;
		obj.unid = get_unique_id();
		obj.name = elems.find('.c_name').val();
		obj.placeholder = elems.find('.c_placeholder').val();
		obj.required = elems.find('.c_required').val();
		obj.width = elems.find('.c_width').val();
		json_names[obj.unid] = [obj.name, obj.type];
		return obj;
	}
	function parse_Period_Feld(c_content, it) {
		const elems = $(c_content);
		const obj = {type:'_Period'};
		obj.no = it+1;
		obj.unid = get_unique_id();
		obj.name = elems.find('.c_name').val();
		obj.placeholder_from = elems.find('.c_placeholder_from').val();
		obj.placeholder_to = elems.find('.c_placeholder_to').val();
		obj.placeholder = elems.find('.c_placeholder').val();
		obj.required = elems.find('.c_required').val();
		obj.width = elems.find('.c_width').val();
		json_names[obj.unid] = [obj.name, obj.type];
		return obj;
	}
	function parse_Dropdown(c_content, it) {
		const elems = $(c_content);
		const obj = {type:'_Dropdown'};
		obj.no = it+1;
		obj.unid = get_unique_id();
		obj.name = elems.find('.c_name').val();
		obj.opt = elems.find('.c_opt').val();
		obj.dd = elems.find('.c_dd').val();
		obj.has_color = elems.find('.c_has_color').val();
		obj.color = elems.find('.c_color_a').val()+'|'+elems.find('.c_color_b').val();
		obj.required = elems.find('.c_required').val();
		obj.width = elems.find('.c_width').val();
		json_names[obj.unid] = [obj.name, obj.type];
		return obj;
	}
	function parse_RadioButtons(c_content, it) {
		const elems = $(c_content);
		const obj = {type:'_RadioButtons'};
		obj.no = it+1;
		obj.unid = get_unique_id();
		obj.name = elems.find('.c_name').val();
		obj.has_title = elems.find('.c_has_title').val();
		obj.title = elems.find('.c_title').val();
		obj.talign = elems.find('input[name$=_talign]:checked').val(); //$=ends with
		obj.rdd = elems.find('.c_rdd').val();
		obj.has_color = elems.find('.c_has_color').val();
		obj.color = elems.find('.c_color_a').val()+'|'+elems.find('.c_color_b').val();
		obj.required = elems.find('.c_required').val();
		obj.width = elems.find('.c_width').val();
		json_names[obj.unid] = [obj.name, obj.type];
		return obj;
	}
	function parse_Accordion(c_content, it, Panels_obj) {
		const elems = $(c_content);
		const obj = {type:'_Accordion'};
		obj.no = it+1;
		obj.accType = $(elems.find('input[name$=_accType]')[1]).is(':checked'); //$=ends with
		obj.width = elems.find('.c_width').val();
		obj.Panels = Panels_obj;
		return obj;
	}
	//items in accordion Obj
	function parse_Accordion_Panel(c_content, it, acc_it, Rows_obj) {
		const elems = $(c_content);
		const obj = {type:'_Accordion_Panel'};
		obj.no = it+1;
		obj.acc_no = acc_it+1;
		obj.label = elems.find('.c_accLabel').val();
		obj.align = elems.find('input[name$=_accAlign]:checked').val(); //$=ends with
		obj.bold = elems.find('input[name$=_accBold]:checked').val(); //$=ends with
		obj.open = $(elems.find('input[name$=_accOpen]')[0]).is(':checked'); //$=ends with
		//obj.width = elems.find('.c_width').val(); //is always 100%
		obj.Rows = Rows_obj;
		return obj;
	}
	
	function parse_Row(_this, selector) {
		//console.log(_this, selector);
		let row_num = 0;
		const rowsArr = [];
		$(_this).find(selector).each(function(){ //Rows
			//const row = $(this).attr('data-row');
			//console.log(row);
			
			//init row
			rowsArr[row_num] = {};
			rowsArr[row_num].no = row_num+1;
			rowsArr[row_num].items = [];
		
			let it = 0;
			$(this).find('> .row_div > table>tbody>tr > td.rowItem').each(function(){ //rowItems
				const c_type = $(this).find('> .rowItem_edit > input.c_type').val();
				//console.log(row, it, c_type);
				const elems = $( $(this).find('> .rowItem_edit > .c_content') );
				
				let item_obj;
					 //if (c_type == '_Empty') 		item_obj = parse_Empty(elems, it);
					 if (c_type == '_Space') 		item_obj = parse_Space(elems, it);
				else if (c_type == '_Line') 		item_obj = parse_Line(elems, it);
				else if (c_type == '_Label') 		item_obj = parse_Label(elems, it);
				else if (c_type == '_Html') 		item_obj = parse_Html(elems, it);
				else if (c_type == '_Text') 		item_obj = parse_Text(elems, it);
				else if (c_type == '_Textarea') 	item_obj = parse_Textarea(elems, it);
				else if (c_type == '_Number') 		item_obj = parse_Number(elems, it);
				else if (c_type == '_Date') 		item_obj = parse_Date(elems, it);
				else if (c_type == '_Time') 		item_obj = parse_Time(elems, it);
				else if (c_type == '_Period') 		item_obj = parse_Period_Feld(elems, it);
				else if (c_type == '_Dropdown') 	item_obj = parse_Dropdown(elems, it);
				else if (c_type == '_RadioButtons') item_obj = parse_RadioButtons(elems, it);
				else if (c_type == '_Accordion') {
					//_Accordion_Panel
					let acc_it = 0;
					const Panels_obj = [];
					
					//Accordion Panels
					$(this).find('> .rowItem_div > .accordion_group > .AccPanel').each(function(){
						const acc_elems = $( $(this).find('> .rowItem_edit > .c_content') );
						const Rows_obj = parse_Row(this, '> .rowItem_div > .panel > .accordionPanel > .panel-body > ul.row_sortable > li.row_sort', false) || [];

						Panels_obj.push( parse_Accordion_Panel(acc_elems, it, acc_it, Rows_obj) );
						acc_it++;
					});
					
					item_obj = parse_Accordion(elems, it, Panels_obj);
				}
				

				if (c_type != '_Empty') { //we not want to save empty 
					rowsArr[row_num].items.push(item_obj);
					it++;
				}
			});

			row_num++;
		});

		return rowsArr;
	}

	function get_unique_id() {
		return unid++;
	}

	const json = {
		"title":"",
		"timer": {
			"has": 0,
			"min": 0,
			"period": 'min'
		},
		"days": {
			"has": 0,
			"arr": [],
		},
		"pages": []
	};

	let json_names = {};
	let unid = 1;
	
	json.title = $('input[name=form_title]').val();
	json.timer.has = $('#timer_has').val();
	json.timer.min = $('#timer_min').val();
	json.timer.period = $('#timer_period').val();
	json.days.has = $('#days_has').val();

	let days_arr = [];
	$('input[name=days_arr]').each(function(i,el){
		if ($(el).val()=='1') days_arr.push(i+1);
	});

	json.days.arr = days_arr;
	json.pages = [];
	

	let pg = 0; //0 for the object and 0+1 for the .no
	$('li.page_sort > fieldset').each(function () { //Page
		const page = $(this).attr('data-page-id');

		json.pages[pg] = {};
		json.pages[pg].no = pg + 1;
		json.pages[pg].display_times = $(this).find('#page_display_times_' + page).val() || '0';
		json.pages[pg].title = $(this).find('#page_title_' + page).val() || '';
		json.pages[pg].title_center = $(this).find('#page_title_center_' + page).is(':checked');
		
		json.pages[pg].rows = parse_Row(this, '> .edit_step > .main_font > ul.row_sortable > li.row_sort', true) //Row on page
		pg++;
	});

	//console.log(json, json_names);
	$('input[name=form_json]').val(
		JSON.stringify(json)
	);
	$('input[name=form_json_names]').val(
		JSON.stringify(json_names)
	);
}
//GET FORM JSON #######################
//#####################################

	
//#####################################
//Preview, Save, Delete Entries #######
	
	
//button Preview
$("button.preview").on('click',function() {
	if ($('form#wrapped').valid()) {

		get_Form_JSON();

		//in order to send the data with POST we open an iframe and then we POST the data there
		$.fancybox($.extend(fancyBoxDefaults_iframe, {
			type:'iframe', 
			href: 'forms/ajax.form_data_preview.php', 
			afterLoad:function() {
				$('form#form_data').prop('action', 'form.php?id=' + V_SRV_ID + '&preview&form_name2').prop('target', $('.fancybox-iframe').attr('name'));
				
				$('form#form_data').trigger('submit');
				loading.hide();
			}
		}));
	}
	else {
		//check if we have any empty c_names and point the user to it
		$('.c_content :input.c_name.required').each(function(){
			if ($(this).val() == '') {
				$(this).parents('td').find('.c_handler a').trigger("click");

				setTimeout(function () {
					$('.popover .popover-save').trigger("click");

					setTimeout(function () {
						if ($('label.error:visible').length) {
							$("html, body").animate({
								scrollTop: $('label.error:visible').offset().top - 50
							}, "slow");
						}
					}, 100);
				}, 300);
			}
		});
	}
	return false;
});

	
//button Save Form Creator
$("button.save").on('click',function() {
	loading.show();
	let c_name_missing = false;
	let page_display_times_conflict = false;
	let page_display_times_conflict2 = false;

	$(".popover").popover('destroy'); //first remove all popups

	setTimeout(function () { //bcz of problems with popovers we need setTimeout
		//check if we have any required field in a show times-limited page 
		$('.c_content :input.c_required').each(function(){
			if ($(this).val() == '1') {
				const page_times = $(this).parents('.page_sort').find('.page_display_times').val();
				if (page_times != '0') {
					page_display_times_conflict = true;

					parent.Swal({
						type: 'error',
						title: LANG.FORMS.DISPLAY_TIMES_FIELD_ERROR,
						showConfirmButton: true,
						width: '420px'
						//timer: 5000
					});
				}
			}
		});

		//check if we have a show times-limited page with no normal page
		let normal_pages = 0;
		$('.page_display_times').each(function(){
			if ($(this).val() == '0') {
				normal_pages++;
			}
		});

		if (normal_pages == '0') {
			page_display_times_conflict2 = true;
			parent.Swal({
				type: 'error',
				title: DISPLAY_TIMES_INFO_ERROR,
				showConfirmButton: true,
				width: '420px'
				//timer: 5000
			});
		}
		
		//check if we have any empty c_names and point the user to it
		$('.c_content :input.c_name.required').each(function(){
			if ($(this).val() == '') {
				c_name_missing = true;
				$(this).parents('td').find('.c_handler a').trigger("click");

				setTimeout(function () {
					$('.popover .popover-save').trigger("click");

					setTimeout(function () {
						if ($('label.error:visible').length) {
							$("html, body").animate({
								scrollTop: $('label.error:visible').offset().top - 50
							}, "slow");
						}
					}, 100);
				}, 300);
			}
		});
		

		loading.hide();


		if (!c_name_missing &&
			!page_display_times_conflict &&
			!page_display_times_conflict2)
		{
			get_Form_JSON();

			if (!$(':input').not('.c_content :input').not('.popover :input').valid() &&
				$('label.error:visible').length != 0)
			{
				$("html, body").animate({
					scrollTop: $('label.error:visible').offset().top - 50
				}, "slow");
			}
			else {
				//post with ajax
				let data = {
					group_id	: $('input[name="group_id"]').val(),
					athlete_id	: $('input[name="athlete_id"]').val(),
					form_id		: $('input[name="form_id"]').val(),
					category_id	: $('input[name="category_id"]').val(),
					form_json	: $('input[name="form_json"]').val(),
					form_json_names: $('input[name="form_json_names"]').val()
				};
				$.post('form.php?id=' + V_SRV_ID + '&save', data, function (data, result) {
					if (data == 'SAVE_OK') {
						Swal({
							type: 'success',
							title: LANG.FORMS.FORM_SAVED,
							showConfirmButton: false,
							timer: 3000
						});
					}
					else {
						Swal({
							type: 'error',
							title: LANG.FORMS.FORM_DB_ERROR,
							showConfirmButton: false,
							timer: 5000
						});
					}
				});
			}
		}
	}, 200);
});


//button delete form data --on edit
$('button.delete_forms_data_2_edit').confirmation({
	href: 'javascript:void(0)',
	title: LANG.ARE_YOU_SURE, placement: 'bottom',
	btnOkLabel: LANG.YES, btnOkClass: 'btn btn-sm btn-success mr10',
	btnCancelLabel: LANG.NO, btnCancelClass: 'btn btn-sm btn-danger',
	onConfirm: function(e, button) {
		const form_id = $(button).attr('data-form-id');
		V_CONTINUE_LOADING = true;
		$.ajax({
			url: "php/ajax.php?i=forms_data&oper=del&ID=" + form_id, success: function (data, result) {
				if (data == "OK_delete") {
					$(window).off('beforeunload'); //disable unload warning
					window.location.reload();
				}
				else {
					loading.hide();
					$(".delete_forms_data_2_edit")/*.hide()*/.after("<br><b><u>" + data + "</u></b>");
				}
			}
		});
	}
});
//Preview, Save, Delete Entries #######
//#####################################

	
//INIT
//#####################################
//Pages Events Init
$('li.page_sort > fieldset').each(function(){ //Page
	const page_num = $(this).attr('data-page-id');
	Page_Events_Init(page_num);
	
	//row_item button init
	$('#fieldset_' + page_num + ' .rowItem-add').each(function () {
		const row = $(this).attr('data-row');
		New_Row_Events(row);
	});

	//items init
	$('#fieldset_' + page_num + ' td.rowItem').each(function () {
		const row_item = $(this).attr('data-row_item');
		const type = $(this).find('> .rowItem_edit > input.c_type').val();
		Loaded_Item_init(type, row_item, false); //init item
	});
});
//#####################################


}); //jQuery(function()