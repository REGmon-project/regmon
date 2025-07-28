<?php // ajax Forms Select (in Categories optgroup) for selected Athletes

$PATH_2_ROOT = '../';
require_once($PATH_2_ROOT.'_settings.regmon.php');
require($PATH_2_ROOT.'login/validate.php');

require($PATH_2_ROOT.'results/inc.results_functions.php');


$athletes_ids = $_POST['athletes_ids'] ?? false;
$date_from = $_POST['date_from'] ?? false;
$date_to = $_POST['date_to'] ?? false;

if (!$athletes_ids OR $athletes_ids == "''" OR !$date_from OR !$date_to) {
	echo get_No_Data_Error();
	exit;
}


// get forms_data counts
$where_forms = '';
$Forms_with_Forms_Data_arr = array();
$forms_data = $db->fetch("SELECT COUNT(*) AS count, form_id, category_id 
FROM forms_data 
WHERE CONCAT(group_id,'_',user_id) IN ($athletes_ids) AND form_id > 0 AND status = 1 
AND timestamp_start >= ? AND timestamp_start <= ? 
GROUP BY form_id, category_id 
ORDER BY category_id, form_id", array($date_from, $date_to));
if ($db->numberRows() > 0)  {
	foreach ($forms_data as $form_data_columns) {
		if ($form_data_columns['count'] != '0') {
			$Forms_with_Forms_Data_arr[$form_data_columns['form_id']] = $form_data_columns['count'];
			if ($where_forms != '') {
				$where_forms .= ',';
			}
			$where_forms .= "'".$form_data_columns['category_id'].'_'.$form_data_columns['form_id']."'";
		}
	}
} else {
	$where_forms .= "'0'";
}




// get available forms
$Forms_select_options = '';
$forms = array();
$rows = $db->fetch("SELECT c.id AS category_id, c.name AS category_name, f.id AS form_id, f.name AS form_name
FROM forms f 
LEFT JOIN forms2categories f2c ON f2c.form_id = f.id 
LEFT JOIN categories c ON c.id = f2c.category_id 
WHERE f.status = 1 AND c.status = 1 AND CONCAT(f2c.category_id,'_',f2c.form_id) IN ($where_forms) 
ORDER BY c.parent_id, c.sort, c.id, f.name", array());
if ($db->numberRows() > 0)  {
	//html support only one level optgroup
	$open_group = false;
	$group = '';
	$group_tmp = '';
	foreach ($rows as $row) {
		$group = $row['category_name'];
		//Group
		if ($group <> $group_tmp) {
			if ($open_group) {
				$Forms_select_options .= '</optgroup>';
			}
			$Forms_select_options .= '<optgroup label="'.$group.'">';
			$open_group = true;
		}

		//option
		$Forms_select_options .= '<option value="'.$row['category_id'].'_'.$row['form_id'].'">'.$row['form_name'].' ('.$Forms_with_Forms_Data_arr[$row['form_id']].')</option>';

		$group_tmp = $group;
	}
	if ($open_group) {
		$Forms_select_options .= '</optgroup>';
	}
}
?>

<span id="Select__Forms__Row__Span">
	<hr style="margin:10px;">
	<div id="Select__Forms__Row">
		<span class="wiz-title"><?=$LANG->RESULTS_SELECT_FORMS;?> : &nbsp; </span>
		<select id="Select__Forms" name="Select__Forms" multiple="multiple" style="display:none;">
			<?=$Forms_select_options;?>
		</select> &nbsp; 
<?php if ($Forms_select_options != '') { ?>
	 	<button id="Button__Select__Forms__Submit" class="forward" title="<?= $LANG->RESULTS_BUTTON_APPLY_CHANGES;?>"></button>
<?php } else { //change Loading to NoData ?>
	 	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	 	<?=get_No_Data_Error();?>
<?php } ?>
	</div>
</span>
