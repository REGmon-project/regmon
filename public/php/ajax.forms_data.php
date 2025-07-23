<?php // ajax Forms Data

//ajax.php
/** @var string $action */
/** @var int $ID */
/** @var string $where */

if ($SEC_check != $CONFIG['SEC_Page_Secret']) exit;

switch ($action) {
	/*
	case 'add': // INSERT
	case 'edit': // UPDATE 
	  break;
	*/

	case 'del': // DELETE 
		
		if ($ADMIN) { //delete all forms_data data
			$result = $db->delete("forms_data", "form_id=?", array($ID));
			echo check_delete_result($result);
		}
		
	  break;

	case 'status': // UPDATE 
		
		$status = (int)($_REQUEST['status'] ?? 0);
		
		$values['status'] = $status;
		
		$result = $db->update($values, "forms_data", "id=?", array($ID));

		//echo check_update_result($result);
		
	  break;

	case 'cal_count': //get count
	case 'cal': // SELECT 
		
		$response = array();
		
		$athlete_id = $ID;
		$group_id = (int)($_REQUEST['group_id'] ?? 0);
		$start = isset($_REQUEST['start']) ? date("Y-m-d 00:00:00", strtotime($_REQUEST['start'])) : date("Y-m-d", strtotime("-1 week"));
		$end = isset($_REQUEST['end']) ?  date("Y-m-d 23:59:59", strtotime($_REQUEST['end'])) : date("Y-m-d");
		
		$trainer_view = false;
		$where_trainer = '';
		$trainer_read_arr = array();
		$trainer_write_arr = array();
		
		if ($TRAINER) {
			if ($athlete_id == '-1') {
				$athlete_id = $UID; //self as athlete-trainer
			}
			if ($ID == '-1' OR $athlete_id != $UID) { //athlete-trainer
				$trainer_id = $UID;
				$trainer_view = true;
				
				//Users2Trainers //get trainer selected Forms
				$trainer_forms_selected_read_str = '';
				$trainer_forms_selected_write_str = '';
				$row = $db->fetchRow("SELECT forms_select_read, forms_select_write FROM users2trainers WHERE user_id = ? AND group_id = ? AND trainer_id = ?", array($athlete_id, $group_id, $trainer_id)); 
				if ($db->numberRows() > 0) {
					if ($row['forms_select_read'] != '') {
						$trainer_read_arr = explode(',', $row['forms_select_read']??'');
						//we have 13_1 so need to make it '13_1'
						$trainer_forms_selected_read_str = "'".implode("','", $trainer_read_arr)."'";
					}
					if ($row['forms_select_write'] != '') {
						$trainer_write_arr = explode(',', $row['forms_select_write']??'');
						$trainer_forms_selected_write_str = "'".implode("','", $trainer_write_arr)."'";
					}
				}
				if ($trainer_forms_selected_read_str == '') $trainer_forms_selected_read_str = '0';
				if ($trainer_forms_selected_write_str == '') $trainer_forms_selected_write_str = '0';
				$where_trainer = " AND CONCAT(category_id,'_',form_id) IN (".$trainer_forms_selected_read_str.") ";
			}
		}
		
		if ($action != 'cal_count') {
			$categories_colors = $db->fetchAllwithKey("SELECT id, color FROM categories WHERE status = 1 ORDER BY id", array(), 'id');
			$forms_names = $db->fetchAllwithKey("SELECT id, name FROM forms WHERE status = 1 ORDER BY id", array(), 'id'); 

			
			//$where = " WHERE user_id = '$ID' AND group_id = '$group_id' AND timestamp_start BETWEEN '$start' AND '$end'";
			//we need the selected Athlete and not the trainer or admin
			$where = " WHERE user_id = '$athlete_id' AND group_id = '$group_id' AND form_id > 0 AND timestamp_start BETWEEN '$start' AND '$end'";

			//forms_data
			$rows = $db->fetch("SELECT * FROM forms_data $where $where_trainer AND status = 1 ORDER BY timestamp_start", array()); 
			$i=0;
			if ($db->numberRows() > 0)  {
				foreach ($rows as $row) {
					$sec = '';
					if ($trainer_view) {
						$sec = '&sec='.MD5($CONFIG['SEC_Encrypt_Secret'] . $row['form_id'] . $athlete_id . $group_id . $UID);
					}
					//new +60mins
					$date_time_end = date("Y-m-d H:i:s", (strtotime($row['timestamp_start']) + (60*60)));
					//if we have timestamp_end
					if ($row['timestamp_end'] != '' AND $row['timestamp_start'] != $row['timestamp_end']) {
						$date_time_end = $row['timestamp_end'];
					}

					$has_write_permissions = false;
					if ($athlete_id == $UID OR 
						($trainer_view AND in_array($row['category_id'].'_'.$row['form_id'], $trainer_write_arr))) 
					{
						$has_write_permissions = true;
					}

					$response[$i] = $response[$i]['cell'] = array(
						"id" => $row['id'],
						"title" => $forms_names[$row['form_id']]['name'],
						"start" => $row['timestamp_start'],
						"end" => $date_time_end,
						"status" => $row['status'],
						"color" => $categories_colors[$row['category_id']]['color'],
						"msg" => ''.
						'<div style="text-align:center; font-size:0.9em; margin:0 5px;">'.
							//edit form + deactivate/activate form_data entry --if can
							($has_write_permissions?
								//deactivate form_data entry
								'<button id="forms_data_deactivate_'.$row['id'].'" type="button" class="bttn" style="padding:3px 10px; width:190px;">'.$LANG->INDEX_DEACTIVATE_RECORD.' &nbsp; <i class="fa fa-eye-slash" style="font-size:16px;"></i></button>'.
								//activate form_data entry
								'<button id="forms_data_activate_'.$row['id'].'" type="button" class="bttn" style="padding:3px 10px; width:190px;">'.$LANG->INDEX_ACTIVATE_RECORD.' &nbsp; <i class="fa fa-eye" style="font-size:16px;"></i></button>'.
								//edit form 
								'<br>'.
								'<button id="Cal_Edit_'.$row['id'].'" type="button" class="bttn fancybox fancybox.iframe" href="form.php?change=true&id='.$row['form_id'].'&cat_id='.$row['category_id'].'&from_data_id='.$row['id'].'&group_id='.$row['group_id'].'&athlete_id='.$athlete_id.'" style="margin-top:10px; padding:3px 10px; width:190px;">'.$LANG->INDEX_EDIT_RECORD.'&nbsp; &nbsp;<i class="fa fa-edit" style="font-size:16px;"></i></button>'.
								'<br>'
							:'').
							//view form
							'<button id="Cal_Res_'.$row['id'].'" type="button" class="bttn fancybox fancybox.iframe" href="form.php?view=true&id='.$row['form_id'].'&cat_id='.$row['category_id'].'&from_data_id='.$row['id'].'&group_id='.$row['group_id'].'&athlete_id='.$athlete_id.'" style="margin-top:10px; padding:3px 10px; width:190px;">'.$LANG->INDEX_VIEW_RECORD.' &nbsp; &nbsp;<i class="fa fa-bar-chart fa-rotate-90" style="font-size:14px;"></i></button>'.
							//view results
							'<br>'.
							'<button id="Cal_Res_Sub_'.$row['id'].'" type="button" class="bttn fancybox fancybox.iframe" href="forms_results.php?athlete_id='.$athlete_id.'&id='.$row['form_id'].'&cat_id='.$row['category_id'].'&timestamp='.(strtotime($row['timestamp_start']) + 30*60).'&is_iframe'.$sec.'" style="margin-top:10px; padding:3px 10px; width:190px;">'.$LANG->INDEX_VIEW_RESULTS.' &nbsp; &nbsp;<i class="fa fa-bar-chart" style="font-size:16px;"></i></button>'.
							//delete form.save --if can
							($has_write_permissions?
								'<br>'.
								'<button id="Cal_Res_Del_'.$row['id'].'" type="button" class="bttnR" style="margin-top:10px; padding:3px 10px; width:190px;">'.$LANG->INDEX_DELETE_RECORD.' &nbsp; <i class="fa fa-trash-o" style="font-size:16px;"></i></button>'
							:'').
						'</div>'
						//'<div style="text-align:center; margin-top:10px;">'.
						// 	$LANG->CREATED.': <b>'.$row['timestamp_start'].'</b><br>'.
						// 	$LANG->MODIFIED.': <b>'.$row['modified'].'</b>'.
						//'</div>'
					);
					$i++;
				}
			}

			//notes in calendar
			if ($athlete_id == $UID OR $ADMIN OR ($trainer_view AND (in_array('Note_n', $trainer_read_arr)))) {
				$rows = $db->fetch("SELECT * FROM notes WHERE user_id = ? AND group_id = ? ORDER BY timestamp_start", array($athlete_id, $group_id));
				if ($db->numberRows() > 0)  {
					foreach ($rows as $row) {
						//FIXES //////////////////////////////////////////////////
						$start = get_date_time_SQL($row['timestamp_start']);
						$end = get_date_time_SQL($row['timestamp_end']);
						if ($row['isAllDay']=='1') {
							//calendar want a full day if allDay:true 
							//if same date and different time calendar not show it
							//end + 1sec bcz is 23:59:59
							$end = date("Y-m-d H:i:s", strtotime($row['timestamp_end']) + 1);
						}
						//FIXES //////////////////////////////////////////////////
						
						$response[$i] = $response[$i]['cell'] = array(
							//notes need to have different id than normal events so no conflict
							"id" => -$row['id'],
							"title" => $row['name'],
							"start" => $start,
							"end" => $end,
							"color" => (($row['color']!='' AND str_replace(' ', '', $row['color'])!='rgba(238,238,238,0.5)')?$row['color']:'#aaaaaa'),
							"color2" => ($row['color']!=''?$row['color']:'#aaaaaa'),
							"allDay"=> $row['isAllDay']=='1'?true:false,
							"showInGraph"=> $row['showInGraph']=='1'?true:false,
							"text" => $row['notes'],
							"msg" => '<span>'.$row['notes'].'</span>'.
								(($athlete_id == $UID OR ($trainer_view AND in_array('Note_n', $trainer_write_arr)))?
									'<div class="clearfix"></div>'.
									'<div style="text-align:center; margin-top:10px;">'.
										'<button type="button" id="note_delete" class="delete" style="margin:5px; padding:5px 30px 5px 10px; font-size:12px;">'.$LANG->DELETE.'</button>'.
										'<button type="button" id="note_edit" class="edit" style="margin:5px; padding:5px 30px 5px 10px; font-size:12px;">'.$LANG->EDIT.'</button>'.
									'</div>'
								:'')
						);
						$i++;
					}
				}
			}
			
			$response = json_encode($response);
			
			if ($response == '""') //if empty
				echo '[]';
			else 
				echo $response;
		
		} //$action != 'cal_count' end
		else { //cal_count
			$where = " WHERE user_id = '".((int)$athlete_id)."' AND group_id = '".((int)$group_id)."' AND form_id > 0";
			//forms_data
			$row_forms = $db->fetchRow("SELECT COUNT(*) AS data_num FROM forms_data $where $where_trainer AND status = 1 ORDER BY timestamp_start", array()); 
			//notes in calendar
			$row_notes = $db->fetchRow("SELECT COUNT(*) AS notes_num FROM notes WHERE user_id = ? AND group_id = ? ORDER BY timestamp_start", array($athlete_id, $group_id));
			echo ($row_forms['data_num'] + $row_notes['notes_num']);
		}
		
	  break;

	case 'view': // SELECT 
	default: //view
		
		$response = new stdClass();
		$sidx = $sidx ?? '';
		$sord = $sord ?? '';
		$group_id = (int)($_REQUEST['group_id'] ?? 0);
		
		$wher = "WHERE user_id = '".$ID."' AND group_id = '".$group_id."' AND form_id > 0 ";

		$where = $wher . $where;
		//$sidx = str_replace('pos', 'pos*1', $sidx);
		$rows = $db->fetch("SELECT * FROM forms_data $where AND status = 1 ORDER BY $sidx $sord", array()); 
		$i=0;
		if ($db->numberRows() > 0)  {
			foreach ($rows as $row) {
				$response->rows[$i] = $response->rows[$i]['cell'] = array(
					//'',
					$row['id'],
					$row['user_id'],
					$row['type'],
					$row['group_id'],
					//$row['results_json'],
					get_date_time_SQL($row['timestamp_start'].''),
					get_date_time_SQL($row['modified'].''),
					''
				);
				$i++;
			}
		}
		
		$response = json_encode($response);
		
		if ($response == '""') //if empty
			echo '{"rows":[]}';
		else 
			echo $response;
			
	  break;
}
?>