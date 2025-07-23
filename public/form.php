<?php // Form main page

declare(strict_types=1);
require_once('_settings.regmon.php');
require('login/validate.php');


$EDIT 			= isset($_REQUEST['edit']) ? true : false; 
$PREVIEW 		= isset($_REQUEST['preview']) ? true : false; 
$PREVIEW_USER 	= isset($_REQUEST['preview_user']) ? true : false; 
$FORM_NAME2 	= isset($_REQUEST['form_name2']) ? true : false; 
$SAVE 			= isset($_REQUEST['save']) ? true : false; 
$CHANGE 		= isset($_REQUEST['change']) ? true : false;  //user edit form -> from calendar
$VIEW 			= isset($_REQUEST['view']) ? true : false; //show results -> from calendar
$is_iOS 		= isset($_REQUEST['is_iOS']) ? true : false; 

$group_id 		= $_REQUEST['group_id'] ?? $GROUP; 
$athlete_id 	= $_REQUEST['athlete_id'] ?? $UID; 
$category_id 	= $_REQUEST['cat_id'] ?? 0; 

$form_id 		= $_REQUEST['id'] ?? 0; 
$from_data_id 	= $_REQUEST['from_data_id'] ?? 0; 
$form_json 		= $_REQUEST['form_json'] ?? false; 
$form_json_names = $_REQUEST['form_json_names'] ?? false;


$selected_date = get_date_time_SQL('now');
if (isset($_COOKIE['SELECTED_DATE'])) {
	$selected_date = get_date_time_SQL($_COOKIE['SELECTED_DATE']);
}
if (!$form_id) {
	echo Exit_Message($LANG->FORM_PAGE_ERROR);
	exit;
}

$sec = isset($_REQUEST['sec']) ? $_REQUEST['sec'] : ''; 
$sec_OK = MD5($CONFIG['SEC_Encrypt_Secret'] . $form_id . $athlete_id . $group_id . $UID);
if ((isset($_REQUEST['sec']) AND $sec !== $sec_OK) OR ($ATHLETE AND $athlete_id != $UID)) {
	echo Exit_Message($LANG->NO_ACCESS_RIGHTS);
	exit;
}


//$debug = true;
//$debug = false;
//if ($PREVIEW) $debug = true;

$cat_form_id = $category_id.'_'.$form_id;

//#################################################################
if ($SAVE) {
	//echo "<pre>";print_r($form_json);echo "</pre>";
	$values = array();
	$values['data_json'] = $form_json;
	$values['data_names'] = $form_json_names;
	$values['modified'] = get_date_time_SQL('now');
	$values['modified_by'] = $USERNAME;

	$save = $db->update($values, "forms", "id=?", array($form_id));
	
	if ($save >= 1) {
		echo 'SAVE_OK';
		//echo '<script type="text/javascript">parent.loading.hide(); parent.Swal({type:"success", title:LANG.FORMS.FORM_SAVED, showConfirmButton:false, timer:5000});</script>';
	} else {
		echo 'SAVE_ERROR';
		//echo '<script type="text/javascript">parent.loading.hide(); parent.Swal({type:"error", title:"ERROR!<br>DB-Fehler. Aktualisierung fehlgeschlagen.", showConfirmButton:false, timer:5000});</script>';
		error_log('MySQL Error (update-save): ' . $db->myError);
	}
	exit;
}
//#################################################################



//#########################################
//if not SAVE #############################
//#########################################

//get Form data
$form = array();
$row = $db->fetchRow("SELECT id, name, name2, data_json, data_names FROM forms WHERE id=?", array($form_id)); 
//AND status = 1 --need to edit all forms -even disabled
if ($db->numberRows() > 0)  {
	$color = ''; //$row['color'];
	$forms_name = $row['name']; //external
	$forms_name2 = $row['name2']; //internal
	if ($PREVIEW) {
		$form = (array)json_decode(($form_json === false ? '{}': $form_json), true);
		$form_names = (array)json_decode(($form_json_names === false ? '{}' : $form_json_names), true);
	}
	else {
		$form = (array)json_decode(($row['data_json'] ?? '{}'), true);
		$form_names = (array)json_decode(($row['data_names'] ?? '{}'), true);
	}
	if ($PREVIEW_USER) $PREVIEW = true;
}
else {
	echo Exit_Message($LANG->FORM_PAGE_ERROR);
	exit;
}

require("forms/inc.form_functions.php");

if (!count($form)) {
	//empty page if new
	$form_json_str = '{
		"title":"", 
		"timer":{
			"has":0,
			"min":0,
			"period":"min"
		},
		"pages":[{
			"display_times":0,
			"title":"",
			"title_center":""
		}]
	}';
	$form = json_decode($form_json_str, true);
}

//if ($debug) { echo "<br><br><pre>"; print_r($form); echo "</pre>"; }
//if ($debug) { echo "<br><br><pre>"; print_r($form_names); echo "</pre>"; }


//Form Title
//$form_title = $form['title']; //not want this any more
$form_title = $forms_name.' ('.$forms_name2.')'; //extern (intern)
if ($EDIT) {
	$form_title = $forms_name . ' (' . $forms_name2 . ')'; //extern (intern)
}
if ($FORM_NAME2) {
	$form_title = $forms_name; //extern only
}

//page title - Browsers title
$title = $form_title . ' - ' . $LANG->APP_NAME; //page title


//days available
$days_has = isset($form['days']['has'])?$form['days']['has']:0;
$days_arr = isset($form['days']['arr'])?$form['days']['arr']:array(1,2,3,4,5,6,7);


//timer
$timer = $form['timer']['has'];
$timer_time_min = $form['timer']['min'];
if (isset($form['timer']['period'])) {
	$timer_time_period = $form['timer']['period'];
} else {
	$timer_time_period = 'min';
}
$timer_time_sec = 0;
$timer_time_step = 0;
$answers_step = 0;
if ($timer AND $timer_time_min) {
	$timer_time_sec = $timer_time_min; //sec
	
	if ($timer_time_period == 'min') {
		$timer_time_sec = 60 * $timer_time_min;
	}
	elseif($timer_time_period == 'hour') {
		$timer_time_sec = 60 * 60 * $timer_time_min;
	}
	$timer_time_step = 100 / $timer_time_sec;
}


//HAVE_DATA + HAVE_DATA_NUM - if + the number of times this form has filled 
$HAVE_DATA = false;
$HAVE_DATA_NUM = 0;
$forms_data_count = $db->fetchRow("SELECT COUNT(*) AS forms_data_count FROM forms_data WHERE form_id = ?", array($form_id)); 
if ($forms_data_count['forms_data_count'] > 0)  {
	$HAVE_DATA = true;
	$HAVE_DATA_NUM = (int)$forms_data_count['forms_data_count'];
}


//USER_HAVE_FILL_FORM_NUM - number of times the user have filled this form
$USER_HAVE_FILL_FORM_NUM = 0;
$forms_data_count = $db->fetchRow("SELECT COUNT(*) AS forms_data_count FROM forms_data WHERE user_id = ? AND form_id = ? AND status = 1", array($athlete_id, $form_id)); 
if ($forms_data_count['forms_data_count'] > 0)  {
	$USER_HAVE_FILL_FORM_NUM = $forms_data_count['forms_data_count'];
}
	

//get Form Data
$FORM_DATA = false;
if ($CHANGE OR $VIEW) {
	$forms_data_row = $db->fetchRow("SELECT * FROM forms_data WHERE user_id = ? AND form_id = ? AND id = ? AND status = 1", array($athlete_id, $form_id, $from_data_id)); 
	if ($db->numberRows() > 0)  {
		$FORM_DATA = json_decode($forms_data_row['res_json'], true);
		$FORM_DATA['id'] = $forms_data_row['id'];
		$FORM_DATA['timestamp_start'] = $forms_data_row['timestamp_start'];
		$FORM_DATA['timestamp_end'] = $forms_data_row['timestamp_end'];
		$FORM_DATA['modified'] = $forms_data_row['modified'];

		//if ($debug) { echo "<br><br><pre>"; print_r($FORM_DATA);echo "</pre>"; }
	}
}

//make page
$html = '';
$items_num = 0;
//$pages_num = count($form['pages']); //not used --use of pages_num_visible
$pages_num_visible = 0;
$G_ORDER = 0;

$G_ROW = 0;
$pg = 0;
if (isset($form['pages'])) {
	if ($EDIT) {
		$html .= '<ul class="page_sortable">';
	}
	foreach ($form['pages'] as $page) {
		$pg++;
		$demo_page = false;
		$page_display_times = '0'; //not all forms have this, so we doing that
		if (isset($page['display_times'])) $page_display_times = $page['display_times'];
		
		//not show this page if displayed more times than $page_display_times
		if (!$CHANGE AND !$VIEW AND !$EDIT) {
			if ((int)$page_display_times > 0 AND $USER_HAVE_FILL_FORM_NUM >= (int)$page_display_times) {
				continue;
			}
		}
		$pages_num_visible++;
		
			
		//title #################################################
		if ($EDIT)
		{
			//
			$html.='<li class="page_sort'.($pg==1?' first_page':'').'">';
			$html .= '<span id="pageDrag_'.$pg.'" class="page-drag trans5 hid"><i class="fa fa-arrows"></i></span>';
			$html .= '<fieldset id="fieldset_'.$pg.'" class="coolfieldset" data-page-id="'.$pg.'">'.
						'<legend> '.$LANG->FORM_PAGE.': '.$pg.'&nbsp;</legend>'.
						'<input type="hidden" name="page[]" value="'.$pg.'">'.
						'<div class="edit_step">';
					
			if (!$HAVE_DATA) {
				//close button - except first page
				$html .= ($pg!=1?'<span id="p_close_page_'.$pg.'" class="close_page"></span>':'');
			}
			$html .= ''.	//limit the display times of this page
							'<div style="text-align:center;" title="'.$LANG->FORM_DISPLAY_TIMES_INFO.'">'. 
								'<label for="page_display_times_'.$pg.'">'.$LANG->FORM_DISPLAY_TIMES.' : &nbsp;</label>'.
								//display times select
								'<select id="page_display_times_'.$pg.'" class="page_display_times">'.
									'<option value="0"'.($page_display_times=='0'?' selected':'').'>'.$LANG->FORM_DISPLAY_TIMES_0.'</option>'.
									'<option value="1"'.($page_display_times=='1'?' selected':'').'>'.$LANG->FORM_DISPLAY_TIMES_1.'</option>'.
									'<option value="2"'.($page_display_times=='2'?' selected':'').'>'.$LANG->FORM_DISPLAY_TIMES_2.'</option>'.
									'<option value="3"'.($page_display_times=='3'?' selected':'').'>'.$LANG->FORM_DISPLAY_TIMES_3.'</option>'.
									'<option value="4"'.($page_display_times=='4'?' selected':'').'>'.$LANG->FORM_DISPLAY_TIMES_4.'</option>'.
									'<option value="5"'.($page_display_times=='5'?' selected':'').'>'.$LANG->FORM_DISPLAY_TIMES_5.'</option>'.
								'</select>'.
							'</div>'.
							'<h3 style="white-space:nowrap;">'.
								//title input
								'<input type="text" id="page_title_'.$pg.'" class="c_page_title"'.
									' placeholder="'.$LANG->FORM_PAGE_TITLE.'"'.
									' value="'.html_chars($page['title']).'"'.
									($page['title_center']=='1'?' style="text-align:center;"':'').'>'.
								//title align center checkbox
								'<span class="c_page_title_center trans20">'.
									'<label for="page_title_center_'.$pg.'">'.$LANG->FORM_TITLE_CENTER.'</label>'.
									'<input type="checkbox" id="page_title_center_'.$pg.'"'.($page['title_center']=='1'?' checked':'').'>'.
								'</span>'.
							'</h3>';
		} // if ($EDIT) end
		else {
			//enable submit button if it is the last page
			$html .= 	'<div class="step row box1 '.((count($form['pages'])==$pg)?'submit':'').'">'
							//wizard need at least one input to work 
							.'<input type="hidden">';

			//show page title
			if (isset($page['title'])) {
				$html .= 		'<h3 style="'.($page['title_center']?'text-align:center;':'').'">'.
									htmlspecialchars_decode($page['title']).
								'</h3>';
			}
		}
		//title end #############################################
		
		
		$html .= 			'<span class="main_font">';

		if ($EDIT) $html .= 	'<ul class="row_sortable">';
		
		//rows 
		if (isset($page['rows'])) {
			//form Rows ##########################
			$html .= get_Form_Rows($page['rows']);
		}
		
		if ($EDIT) $html .= 	'</ul>';
		
		if ($EDIT AND !$HAVE_DATA) { //new Row button
			$html .= 				'<div style="text-align:center; margin-top:10px;"><button type="button" id="page_'.$pg.'_newRow" class="newRow" data-page="'.$pg.'"> &nbsp; '.$LANG->FORM_ROW_ADD.'</button></div>';
		}
		$html .= 			'</span>'; //main_font end

		$html .= 		'</div>'; //step end

		if ($EDIT) {
			$html .= ''.
					'</fieldset><br><br>'.
				'</li>';
		}
	} //foreach ($form['pages'] end

	if ($EDIT) $html .= '</ul>'; //ul class="page_sortable" end
	
	if ($EDIT AND !$HAVE_DATA) {
		//new Page button
		$html .= '<button type="button" class="newPage"> &nbsp; '.$LANG->FORM_PAGE_ADD.'</button><br><br>';
	}
	
} //if (isset($form['pages'])) end


//items_num - changed in get_Form_Rows($rows)
//for page with no items --prevent Division by zero error
$items_num = ($items_num != 0 ? $items_num : 100);
$answers_step = 100 / $items_num;


require("forms/inc.form_header.php");


//echo the HTML
echo '<div id="middle-wizard"'.
	($EDIT?' style="padding-top:30px; padding-left:10px; padding-right:10px;"':'').
	($VIEW?' style="padding-top:20px;"':'').
'>';

	echo $html;
	
echo '</div>'; //middle-wizard end

require("forms/inc.form_footer.php");

?>