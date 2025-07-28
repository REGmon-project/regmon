<?php

if ($SEC_check != $CONFIG['SEC_Page_Secret']) exit;

function Exit_Message(string $message):string {
	return ''.
		'<div style="text-align:center; font-size:20px;">'.
			$message.
		'</div>';
}


function html_chars(mixed $string):string {
	//return htmlspecialchars(utf8_encode($string));
	//http://stackoverflow.com/questions/307623/utf-8-and-htmlentities-in-rss-feeds
	//return utf8_encode(htmlentities($string,ENT_COMPAT,'utf-8'));
	//return htmlspecialchars(utf8_encode($string), ENT_QUOTES); // if input encoding is ISO 8859-1

	$string = $string . ''; //make string in case is null
	return htmlspecialchars($string, ENT_QUOTES, 'UTF-8', false); // if input encoding is UTF-8
}
