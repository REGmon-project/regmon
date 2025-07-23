<?php // ajax Forms Menu
$PATH_2_ROOT = '../';
require_once($PATH_2_ROOT.'_settings.regmon.php');
require($PATH_2_ROOT.'login/validate.php');

$group_id 	= (int)($_POST['group_id'] ?? 0);
$trainer_id = (int)($_POST['trainer_id'] ?? 0);
$athlete_id = (int)($_POST['athlete_id'] ?? 0);

if (!$group_id) exit;

$edit 		= ((isset($_POST['edit']) AND $_POST['edit'] == 'true') ? true : false);
$select 	= ((isset($_POST['select']) AND $_POST['select'] == 'true') ? true : false);
$trainer 	= ((isset($_POST['trainer']) AND $_POST['trainer'] == 'true') ? true : false);
$box 		= ((isset($_POST['box']) AND $_POST['box'] == 'true') ? true : false);


if ($edit) {
	if (!$ADMIN) {
		//Location Admin
		$st_admin = $db->fetchRow("SELECT u.id FROM users u 
LEFT JOIN locations l ON u.id = l.admin_id 
LEFT JOIN `groups` g ON g.location_id = l.id 
WHERE g.id = ? AND u.level = 50 AND u.id = ?", array($group_id, $UID)); 
		if (!$db->numberRows() > 0)  {
			//Group Admin
			$gr_admin = $db->fetchRow("SELECT u.id FROM users u 
LEFT JOIN `groups` gr ON gr.id = ? 
WHERE FIND_IN_SET( u.id, gr.admins_id ) 
AND (u.level = 40 OR u.level = 45) AND u.id = ?", array($group_id, $UID));
			if ($db->numberRows() > 0)  {}
			else {
				echo '<script>'.
						'V_TRAINER_R_PERMS=[]; '.
						'V_TRAINER_W_PERMS=[];'.
					'</script>'.
					'<div class="empty_message">'.$LANG->NEED_ADMIN_RIGHTS.'</div>';
				exit; //no admin user
			}
		}
	}
}



//########################################
// Trainer View of an Athlete forms_select
$trainer_view = false;
$trainer_view_id = false;

if (!$select AND !$trainer AND !$edit) { //normal view
	if ($athlete_id != $UID) { //not an athlete
		if ($athlete_id == '-1') $athlete_id = $UID; //self as athlete-trainer
		if (!$athlete_id) $athlete_id = $UID;
		//Select Athletes in Group with Trainer this User-$UID
		$row = $db->fetchRow("SELECT u.id, u.lastname, u.firstname 
FROM users2groups u2g 
JOIN users u ON (u.id = u2g.user_id AND (u.level = 10 OR u.id = ?) AND u.status = 1) 
JOIN users2trainers u2t ON (u.id = u2t.user_id AND u2g.group_id = u2t.group_id AND u2t.status = 1 AND u2t.trainer_id = ?) 
WHERE u2g.group_id = ? AND u2g.status = 1 AND u.id = ? 
ORDER BY u.id", array($UID, $UID, $group_id, $athlete_id)); 
		if (!$db->numberRows() > 0) {
			echo '<script>'.
					'V_TRAINER_R_PERMS=[]; '.
					'V_TRAINER_W_PERMS=[];'.
				'</script>'.
				'<div class="empty_message">'.$LANG->NO_ACCESS_RIGHTS.'</div>';
			exit;
		} else {
			$trainer_view = true;
			$trainer_view_id = $UID;
		}
	}
}
else {
	$athlete_id = $UID;
}




//get Group selected forms_select
$group_forms_selected_arr = array();
$group_forms_standard_arr = array();
$group_forms_selected_str = '0';
//$group_forms_standard_str = '0'; //not used
$location_id = 0;
$group_forms = $db->fetchRow("SELECT location_id, forms_select, forms_standard FROM `groups` WHERE id=?", array($group_id)); 
if ($db->numberRows() > 0)  {
	$location_id = $group_forms['location_id'];
	if ($group_forms['forms_select'] != '') {
		//we have 3_1 it need to be '3_1'
		$group_forms_selected_arr = explode(',', $group_forms['forms_select']??'');
		$group_forms_selected_str = "'".implode("','", $group_forms_selected_arr)."'";
		//we have 3_1 it need to be '3_1'
		$group_forms_standard_arr = explode(',', $group_forms['forms_standard']??'');
		//$group_forms_standard_str = "'".implode("','", $group_forms_standard_arr)."'";
	}
}
if ($group_forms_selected_str == "''") $group_forms_selected_str = '0'; //for use with IN ()



//Users2Groups - get athlete selected forms_select
$athletes_forms_selected_arr = array();
$athletes_forms_selected_str = '0';
$row = $db->fetchRow("SELECT forms_select FROM users2groups WHERE user_id = ? AND group_id = ? ", array($athlete_id, $group_id)); 
if ($db->numberRows() > 0)  {
	//we have 3_1 it need to be '3_1'
	$athletes_forms_selected_arr = explode(',', $row['forms_select']??'');
	$athletes_forms_selected_str = "'".implode("','", $athletes_forms_selected_arr)."'";
}
if ($athletes_forms_selected_str == "''") $athletes_forms_selected_str = '0'; //for use with IN ()



//if is not an Admin edit then is from athlete
if (!$edit) {
	$group_forms_selected_arr = $athletes_forms_selected_arr;
}


//init values
$trainer_forms_selected_read_arr = array();
$trainer_forms_selected_read_str = '0';
$trainer_forms_selected_write_arr = array();
$trainer_forms_selected_write_str = '0';

//Users2Trainers - get Trainer Athletes selected forms_select
if (($trainer AND $trainer_id) OR ($trainer_view AND $trainer_view_id)) {
	$row = $db->fetchRow("SELECT forms_select_read, forms_select_write FROM users2trainers WHERE user_id = ? AND group_id = ? AND trainer_id = ?", array($athlete_id, $group_id, ($trainer_view_id ? $trainer_view_id : $trainer_id))); 
	if ($db->numberRows() > 0)  {
		if ($row['forms_select_read'] != '') {
			//we have 3_1 need to be '3_1'
			$trainer_forms_selected_read_arr = explode(',', $row['forms_select_read']??'');
			$trainer_forms_selected_read_str = "'".implode("','", $trainer_forms_selected_read_arr)."'";
		}
		if ($row['forms_select_write'] != '') {
			//we have 3_1 need to be '3_1'
			$trainer_forms_selected_write_arr = explode(',', $row['forms_select_write']??'');
			$trainer_forms_selected_write_str = "'".implode("','", $trainer_forms_selected_write_arr)."'";
		}
	}
	if ($trainer_forms_selected_read_str == "''") $trainer_forms_selected_read_str = '0'; //for use with IN ()
	if ($trainer_forms_selected_write_str == "''") $trainer_forms_selected_write_str = '0'; //for use with IN ()
}


/**
 * @param int $cat_id
 * @param string $space
 * @param bool $options
 * @return string
 */
function getCategoryForms_Html(int $cat_id, string $space = '', bool $options = false):string {
	global $forms, $box, $select, $edit, $trainer, $trainer_view, $group_forms_selected_arr, $group_forms_standard_arr, $trainer_forms_selected_read_arr, $trainer_forms_selected_write_arr, $athlete_id, $group_id, $UID, $CONFIG, $LANG;
	
	$html = '';
	foreach ($forms[$cat_id] as $row) {
		$id = $row['form_id'];
		$form_name = $row['name'];
		if ($edit) $form_name = $row['name'].' (interner Name: '.$row['name2'].')';
		$select_elem = 'sel_g_'.$group_id.'_c_'.$cat_id.'_'.$id;
		$standard_elem = 'std_g_'.$group_id.'_c_'.$cat_id.'_'.$id;
		
		//link_form format
		$link_form = ' href="form.php?id='.$id.'&cat_id='.$cat_id.''.(($select OR $trainer)?'&preview_user':'').($edit?'&preview_user&form_name2':'').'" class="calendar_menu_box fancybox fancybox.iframe"';
		if ($trainer_view) {
			$link_form = ' href="javascript:void(0)"';
			if ($box) {
				if (!in_array($cat_id.'_'.$id, $trainer_forms_selected_write_arr)) {
					$link_form = ' href="javascript:void(0)" class="no_access"';
				} else {
					$sec = MD5($CONFIG['SEC_Encrypt_Secret'] . $id . $athlete_id . $group_id . $UID);
					$link_form = ' href="form.php?id='.$id.'&cat_id='.$cat_id.'&group_id='.$group_id.'&athlete_id='.$athlete_id.'&sec='.$sec.'" class="calendar_menu_box fancybox fancybox.iframe"';
				}
			}
		}

		if ($options) $html .= '<option value="'.$cat_id.'_'.$id.'">'.$space.$form_name.'</option>';
		else $html .= '<tr>'.
					'<td><a'.$link_form.'>'.
						'<span class="form_name" '.($edit?' title="'.$form_name.' ['.$row['tags'].']"':'').'>'.
							//the "not_display" is to give some extra space to keep name from breaking on :hover/bold
							$form_name.($box?'<span class="not_display">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>':'').
						'</span>'.
					'</a></td>';
		
		if ($options) {
			
		}
		else {
			if ($select OR $edit) {
				$html .= '<td style="width:70px; padding-left:2px;"><a><span class="span_rs">'.
							'<input name="'.$select_elem.'" type="checkbox" class="check_box"'.(in_array($cat_id.'_'.$id, $group_forms_selected_arr)?' checked':'').'>'.
							'<input name="'.$standard_elem.'" type="checkbox" class="standard"'.(in_array($cat_id.'_'.$id, $group_forms_standard_arr)?' checked':'').($edit?'':' disabled').'>'.
						'</span></a></td>';
				if ($select) {
					$html .= '<td style="width:31px; padding-left:2px;">'.
								'<a class="form_options" href="javascript:void(0)" data-ath_group_cat_form="'.$athlete_id.'|'.$group_id.'|'.$cat_id.'|'.$id.'" data-toggle="popover" title="'.$LANG->FORM_OPTIONS.'"><span class="span_rs" style="padding:0 5px;"><i class="fa fa-cog" title="'.$LANG->FORM_OPTIONS.'"></i></span></a>'.
							'</td>';
				}
			}
			elseif ($trainer) {
				$html .= '<td style="width:70px; padding-left:2px;"><a><span class="span_rs">'.
							'<input name="'.$select_elem.'" type="checkbox" class="check_box"'.(in_array($cat_id.'_'.$id, $trainer_forms_selected_read_arr)?' checked':'').'>'.
							'<input name="'.$standard_elem.'" type="checkbox" class="standard"'.(in_array($cat_id.'_'.$id, $trainer_forms_selected_write_arr)?' checked':'').'>'.
						'</span></a></td>';
			}
			elseif ($trainer_view) {
				$html .= '<td style="width:70px; padding-left:2px;"><a><span class="span_rs" style="padding-left:5px;">'.
							'<span class="icheckbox_flat-yellow2s'.(in_array($cat_id.'_'.$id, $trainer_forms_selected_read_arr)?' checked':'').'" style="vertical-align:text-bottom;"></span>&nbsp; '.
							'<span class="icheckbox_flat-green2s'.(in_array($cat_id.'_'.$id, $trainer_forms_selected_write_arr)?' checked':'').'" style="vertical-align:text-bottom;"></span>'.
						'</span></a></td>';
			}
		}
		if ($options) $html .= '';
		else $html .= '</tr>';
	}
	return $html;
}


function buildCategory_Html(int $parent, string $collapse_id_prefix, int $level = 1, bool $select = false):string {
	global $categories, $group_id, $box;

	$html = '';
	$space = '';
	for ($i=1; $i<$level; $i++) $space .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	if (isset($categories['parent_categories'][$parent])) {
		if ($select) 
			$html .= '';
		else $html .= '<ul style="box-shadow:0px 0px '.($parent=='0'?'10':'5').'px 0px black;">';
		foreach ($categories['parent_categories'][$parent] as $cat_id) {
			$cat_name = $categories['categories'][$cat_id]['name'];
			$color = $categories['categories'][$cat_id]['color'];
			$collapse_id = 'cl_g_'.$group_id.'_c_'.$cat_id . $collapse_id_prefix;
			
			if ($categories['has_forms'][$cat_id]) {
				if ($select) 
					$html .= '<optgroup label="'.$space.$cat_name.'" style="background:'.$color.';">';
				else $html .= ''.
						'<li style="background:'.$color.';">'. //Color
							//Name + Collapse Handle
							'<span class="cat_name" data-toggle="collapse" data-target="#'.$collapse_id.'">'.
								//the "not_display" is to give some extra space to keep the ico from breaking
								$cat_name.($box?'<span class="not_display">&nbsp;&nbsp;</span>':'').
							'</span>'.
							'<div id="'.$collapse_id.'" class="collapse in">'; //Collapse Div

				if (isset($categories['forms'][$cat_id])) {
					if ($select) 
						$html .= getCategoryForms_Html($cat_id, $space, $select); //get forms
					else $html .= ''.
								'<div style="padding:0 5px 10px;">'.
									'<table style="width:100%;">'.
										getCategoryForms_Html($cat_id, $space, $select). //get forms
									'</table></div>';
				}

				// have childs
				if (isset($categories['parent_categories'][$cat_id])) {
					if ($select) {
						$html .= buildCategory_Html($cat_id, $collapse_id_prefix, $level + 1, $select); //get childs
					} else {
						$html .= buildCategory_Html($cat_id, $collapse_id_prefix, $level + 1, $select); //get childs
					}
				}
				
				if ($select)
					$html .= '</optgroup>';
				else $html .= ''.
							'</div>'.
						'</li>';
			}
		}
		if ($select) 
			$html .= '';
		else $html .= '</ul>';
	}
	return $html;
}


//here we make query for what each one can see
//get forms_select list for use or select
if ($edit) {
	$where_in = '';
}
elseif ($select) {
	$where_in = 'AND CONCAT(f2c.category_id,"_",f2c.form_id) IN (' . $group_forms_selected_str . ')';
}
//if trainer - get all of user to select 
elseif ($trainer_view) {
	$where_in = 'AND ('. //1st trainer that has less
					'CONCAT(f2c.category_id,"_",f2c.form_id) IN ('.$trainer_forms_selected_read_str.') OR '.
					'CONCAT(f2c.category_id,"_",f2c.form_id) IN ('.$trainer_forms_selected_write_str.')'.
				') AND '. //then user
				'CONCAT(f2c.category_id,"_",f2c.form_id) IN ('.$athletes_forms_selected_str.') AND '.
				'CONCAT(f2c.category_id,"_",f2c.form_id) IN ('.$group_forms_selected_str.')';
}
//if athlete - it should be selected from group too --in case of a group change in the middle
else $where_in = 'AND CONCAT(f2c.category_id,"_",f2c.form_id) IN ('.$athletes_forms_selected_str.') AND '.
					 'CONCAT(f2c.category_id,"_",f2c.form_id) IN ('.$group_forms_selected_str.')';


$forms = array();
$forms_rows = $db->fetch("SELECT f2c.form_id, f.name, f.name2, f.tags, f2c.category_id 
FROM forms2categories f2c 
LEFT JOIN forms f ON form_id = f.id 
WHERE f.status != 0 AND f2c.status != 0 AND ( f2c.stop_date IS NULL OR f2c.stop_date > NOW() ) 
$where_in 
ORDER BY f2c.category_id, f2c.sort, f.name", array()); 
if ($db->numberRows() > 0)  {
	foreach ($forms_rows as $row) {
		//forms array with cat_id as key
		$forms[$row['category_id']][] = $row;
	}
}


//add Note button
if ($box AND !$trainer_view) {
	echo '<button type="button" id="addNote_Menu" class="bttn" style="padding:5px 10px; width:100%; border:1px #ccc solid;">'.$LANG->FORM_MENU_NOTE_ADD.' &nbsp; &nbsp;<i class="fa fa-commenting" style="font-size:17px; vertical-align:bottom;"></i></button>';
}

//Trainers see/change Notes 
//echo 'trainer:'.$trainer.'view:'.$trainer_view;
if ($trainer OR $trainer_view) {
	$t_html = '';
	if ($trainer) { //athlete can see/change what forms trainer have access
		$select_elem = 'sel_g_'.$group_id.'_c_'.'Note_n';
		$standard_elem = 'std_g_'.$group_id.'_c_'.'Note_n';
		$t_html = '<input name="'.$select_elem.'" type="checkbox" class="check_box"'.(in_array('Note_n', $trainer_forms_selected_read_arr)?' checked':'').'>'.
				'<input name="'.$standard_elem.'" type="checkbox" class="standard"'.(in_array('Note_n', $trainer_forms_selected_write_arr)?' checked':'').'>';
	}
	elseif ($trainer_view) { //trainer see what forms have access given by athlete
		$t_html = '<span class="icheckbox_flat-yellow2s'.(in_array('Note_n', $trainer_forms_selected_read_arr)?' checked':'').'" style="vertical-align:text-bottom;"></span>&nbsp; '.
				'<span class="icheckbox_flat-green2s'.(in_array('Note_n', $trainer_forms_selected_write_arr)?' checked':'').'" style="vertical-align:text-bottom;"></span>';
	}
	$t_html = ''.
		'<ul style="box-shadow:0px 0px 5px 0px black;">'.
			'<li style="background:#446f91;">'.
				'<div style="padding:0 5px 10px;">'.
					'<table style="width:100%;"><tbody><tr>'.
						'<td>'.
							'<a href="javascript:void(0)"'.(($trainer_view && $box)?' id="addNote_Trainer"':'').'>'.
								'<span class="form_name">'.$LANG->FORM_MENU_NOTE.'</span>'.
							'</a>'.
						'</td>'.
						'<td style="width:70px; padding-left:2px;">'.
							'<a>'.
								'<span class="span_rs" style="padding-left:5px;">'.
									$t_html.
								'</span>'.
							'</a>'.
						'</td>'.
					'</tbody></tr></table>'.
				'</div>'.
			'</li>'.
		'</ul>';

	// if has read or write access
	if ($trainer OR in_array('Note_n', $trainer_forms_selected_read_arr) OR in_array('Note_n', $trainer_forms_selected_write_arr)) {
		echo $t_html;
	}
}


$html = '';
$collapse_id_prefix = '';
$categories_rows = $db->fetch("SELECT * FROM categories WHERE status = 1 ORDER BY parent_id, sort, name", array()); 
if ($db->numberRows() > 0)  {
	//make an array to hold categories info and parent/child keys 
	$categories = array(
		'categories' => array(), 
		'parent_categories' => array(), 
		'forms' => array(), 
		'has_forms' => array()
	);
	foreach ($categories_rows as $row) {
		//categories array with id as key
		$categories['categories'][$row['id']] = $row;
		//child categories with parent as key
		$categories['parent_categories'][$row['parent_id']][] = $row['id'];
		
		$categories['has_forms'][$row['id']] = false; //'no';
		if (isset($forms[$row['id']])) {
			//forms array with cat_id as key
			$categories['forms'][$row['id']] = $forms[$row['id']];
			$categories['has_forms'][$row['id']] = true; //'yes';
			$categories['has_forms'][$row['parent_id']] = true; //'yes1';
			if ($row['parent_id'] != '0' 
				AND isset($categories['categories'][$row['parent_id']]) 
					  AND $categories['categories'][$row['parent_id']] != '0' 
				AND isset($categories['categories'][$row['parent_id']]['parent_id']) 
					  AND $categories['categories'][$row['parent_id']]['parent_id'] != '0') 
			{
				$categories['has_forms'][ $categories['categories'][$row['parent_id']]['parent_id'] ] = true; //'yes2';
			}
		}
	}
	
	if ($box) 		$collapse_id_prefix .= '_box';
	if ($select) 	$collapse_id_prefix .= '_sel';
	if ($trainer) 	$collapse_id_prefix .= '_trn';
	if ($edit) 		$collapse_id_prefix .= '_edt';
	
	$html = buildCategory_Html(0, $collapse_id_prefix, 1, false); //global $categories

	if ($box AND $athlete_id == $UID) {
		$html .= '<button type="button" id="Go_2_Forms_Select_Menu" class="bttn" style="padding:3px 10px; width:100%; border:1px #ccc solid;">'.$LANG->FORM_MENU_GO_2_FORM_SELECT.' &nbsp; &nbsp;<i class="fa fa-list-alt" style="font-size:17px; vertical-align:bottom;"></i></button>';
	}
}

if ($html == '') {
	$html = '<div class="empty_message">'.(($select OR $trainer) ? $LANG->FORM_NO_FORMS_IN_GROUP : $LANG->FORM_NO_SELECTED_FORMS).'</div>';
	if ($box AND $athlete_id == $UID) {
		$html .= '<br><br><button type="button" id="Go_2_Forms_Select_Menu" class="bttn" style="padding:3px 10px; width:100%; border:1px #ccc solid;">'.$LANG->FORM_MENU_GO_2_FORM_SELECT.' &nbsp; &nbsp;<i class="fa fa-list-alt" style="font-size:17px; vertical-align:bottom;"></i></button>';
	}
}

echo $html;

//if have rights but not a trainer then its the athlete -> give perms=All
?>
<script>
<?php if ($trainer_view AND $trainer_view_id != $athlete_id AND $box) { //pass trainer perms to interface ?>
V_TRAINER_R_PERMS = [<?=$trainer_forms_selected_read_str;?>];
V_TRAINER_W_PERMS = [<?=$trainer_forms_selected_write_str;?>];
<?php } else { ?>
V_TRAINER_R_PERMS = ['All'];
V_TRAINER_W_PERMS = ['All'];
<?php } ?>


<?php if (!$select AND !$trainer AND !$edit) { 
//TODO: check - this changes in every Athlete change, but it only has to do with main user 
?>
V_CATEGORIES_FORMS_OPTIONS = '<option value="Note_n" style="background:#aaaaaa;"><?=$LANG->FORM_MENU_NOTE;?></option><?=buildCategory_Html(0, $collapse_id_prefix, 1, true);?>';
<?php } ?>

jQuery(function() {
	if (typeof fancyBoxDefaults_iframe !== "undefined") {
		$("a.calendar_menu_box").fancybox(fancyBoxDefaults_iframe);
		$('#addNote_Menu, #addNote_Trainer').on('click',function() {
			if (V_TRAINER_W_PERMS.indexOf('All') != -1 || V_TRAINER_W_PERMS.indexOf('Note_n') != -1) {
				$.fancybox($("#create_Note"), $.extend({},fancyBoxDefaults,{minWidth: 300}));
				init_Notes_Create('Menu_Button');
			}
		});
	<?php if ($box AND $athlete_id == $UID) { ?>
		$("button#Go_2_Forms_Select_Menu").on('click',function() {
			$.fancybox.close();
			$('#view_options').trigger('click');
			if (!$('#C_Athlete_Forms_Select').hasClass('in')) {
				$('#C_Athlete_Forms_Select_link').trigger('click');
			}
			setTimeout(function(){
				$("body").animate({ scrollTop: $('#C_Athlete_Forms_Select').prev().offset().top }, "slow");
			}, 500);
		});
	<?php } ?>
	}
});
</script>
