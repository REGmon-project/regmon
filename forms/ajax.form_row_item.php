<?php // ajax Form Row Item
$PATH_2_ROOT = '../';
require_once($PATH_2_ROOT.'_settings.regmon.php');
require($PATH_2_ROOT.'login/validate.php');

$EDIT = true; 
require("inc.form_functions.php");


$row_item = $_POST['row_item'] ?? '0_0_0'; 
$row_item_type = $_POST['row_item_type'] ?? "text"; 


$row_item_arr = explode('_', $row_item);
$row = (int)$row_item_arr[0]; //row num
$item = (int)$row_item_arr[1]; //item num

$html = '';

$basic_Items = array(
	'_Empty',
	'_Space',
	'_Line',
	'_Label',
	'_Html',
	'_Text',
	'_Textarea',
	'_Date',
	'_Time',
	'_Period'
);

//Basic Items
if (in_array($row_item_type, $basic_Items)) {
	$html .= get_Form_Row_Item($row_item_type, $row, array(
		"no" => $item
	));
}
//Items with extra options
elseif ($row_item_type == "_Number") {
	$html .= get_Form_Row_Item('_Number', $row, array(
		"no" => $item,
		"min" => "",
		"max" => "",
		"decimal" => "0"
	));
}
elseif ($row_item_type == "_Dropdown") {
	$html .= get_Form_Row_Item('_Dropdown', $row, array(
		"no" => $item,
		"dd" => "",
		"opt" => $LANG->FORM_ITEM_SELECT_OPTION
	));
}
elseif ($row_item_type == "_Dropdown_Select_Only") {
	$dd 		= $_POST['dd'] ?? 0; 
	$opt 		= $_POST['opt'] ?? ''; 
	$has_color 	= $_POST['has_color'] ?? 0; 
	$color 		= $_POST['color'] ?? '120|0'; //def=Red-Yellow-Green - 120|0

	$html .= get_Form_Row_Item('_Dropdown_Select_Only', $row, array(
		"no" => $item,
		"dd" => $dd,
		"opt" => $opt,
		"has_color" => $has_color,
		"color" => $color
	));
}
elseif ($row_item_type == "_RadioButtons" OR $row_item_type == "_Radio_Buttons_Select_Only") { 
	$rdd 		= $_POST['rdd'] ?? 0; 
	$has_title 	= $_POST['has_title'] ?? 0; 
	$title 		= $_POST['title'] ?? ''; 
	$talign 	= $_POST['talign'] ?? 'left'; 
	$has_color 	= $_POST['has_color'] ?? 0; 
	$color 		= $_POST['color'] ?? '120|0'; //def=Red-Yellow-Green - 120|0

	$html .= get_Form_Row_Item($row_item_type, $row, array(
		"no" => $item,
		"has_title" => $has_title,
		"title" => $title,
		"talign" => $talign,
		"has_color" => $has_color,
		"color" => $color,
		"rdd" => $rdd
	));
}
elseif ($row_item_type == "_Accordion" OR $row_item_type == "_Accordion_Panel") {
	$acc_item 	= $_POST['acc_item'] ?? 1; 
	
	$acc 		= $_POST['acc'] ?? 0; 
	$has_title 	= $_POST['has_title'] ?? 0; 
	$title 		= $_POST['title'] ?? ''; 
	$talign 	= $_POST['talign'] ?? 'left'; 
	$has_color 	= $_POST['has_color'] ?? 0; 
	$color 		= $_POST['color'] ?? '120|0'; //def=Red-Yellow-Green - 120|0

	$html .= get_Form_Row_Item($row_item_type, $row, array(
		"no" => $item,
		"acc_no" => $acc_item
	));
}

echo $html;
?>