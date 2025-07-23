<?php // Date Functions

if ($SEC_check != $CONFIG['SEC_Page_Secret']) exit;

function get_date(string $date):string {
	global $LANG;
	if ($date == '') {
		return '';
	}
	elseif ($date == 'now') {
		$date = date("Y-m-d");	
	}
	if ($LANG->LANG_CURRENT == 'de') {
		return date("d.m.Y", (int)strtotime($date));
	} else {
		return date("Y-m-d", (int)strtotime($date));
	}
}
function get_date_time(string $date):string {
	global $LANG;
	if ($date == '') {
		return '';
	}
	elseif ($date == 'now') {
		$date = date("Y-m-d H:i:s");	
	}
	if ($LANG->LANG_CURRENT == 'de') {
		return date("d.m.Y H:i:s", (int)strtotime($date));	
	} else {
		return date("Y-m-d H:i:s", (int)strtotime($date));
	}
}
function get_date_time_noSecs(string $date):string {
	global $LANG;
	if ($date == '') {
		return '';
	}
	elseif ($date == 'now') {
		$date = date("Y-m-d H:i");	
	}
	if ($LANG->LANG_CURRENT == 'de') {
		return date("d.m.Y H:i", (int)strtotime($date));	
	} else {
		return date("Y-m-d H:i", (int)strtotime($date));
	}
}
function get_date_SQL(string $date):string {
	if ($date == '') {
		return '';
	} 
	elseif ($date == 'now') {
		$date = date("Y-m-d");	
	}
	return date("Y-m-d", (int)strtotime($date));
}
function get_date_time_SQL(string $date):string {
	if ($date == '') {
		return '';
	} 
	elseif ($date == 'now') {
		$date = date("Y-m-d H:i:s");	
	}
	return date("Y-m-d H:i:s", (int)strtotime($date));
}
?>