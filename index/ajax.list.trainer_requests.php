<?php // ajax Trainer Requests List
$PATH_2_ROOT = '../';
require_once($PATH_2_ROOT.'_settings.regmon.php');
require($PATH_2_ROOT.'login/validate.php');

$group_id = (int)($_POST['group_id'] ?? false);
if (!$group_id) exit;

$req_text = array(
	'G_no' 		=> $LANG->STATUS_REQUEST_REJECTED, //0
	'G_yes' 	=> $LANG->STATUS_REQUEST_ACCEPTED, //1
	'G_leaveR' 	=> $LANG->STATUS_CANCELED_ACCESS_TRAINER, //5
	'G_leaveA' 	=> $LANG->STATUS_CANCELED_ACCESS_ATHLETE, //15
	'G_waitLR' 	=> $LANG->STATUS_REQ_WAIT_CANCELED_TRAINER, //7
	'G_waitLA' 	=> $LANG->STATUS_REQ_WAIT_CANCELED_ATHLETE, //17
	'G_waitN' 	=> $LANG->STATUS_REQ_WAIT_REJECTED_ATHLETE, //8
	'G_wait' 	=> $LANG->STATUS_REQUEST_WAIT, //9
	
	'G_no_trainer' 	=> $LANG->REQUEST_ATHLETE_LEAVE_TRAINER, //0
	'G_no_ans' 		=> $LANG->REQUEST_REJECT, //0
	'G_yes_ans' 	=> $LANG->REQUEST_ACCEPT //1
);

$html = '';


$rows = $db->fetch("SELECT u2t.status AS request_status, u2t.modified, u2t.created, u.id, u.lastname, u.firstname, u.sport 
FROM users2groups u2g 
JOIN users u ON (u.id = u2g.user_id AND u.level > 10 AND u.status = 1) 
JOIN users2trainers u2t ON (u.id = u2t.trainer_id AND u2g.group_id = u2t.group_id AND u2t.user_id = ?) 
WHERE u2g.group_id = ? AND u2g.status = 1 
ORDER BY u2t.id", array($UID, $group_id)); 
//print_r($rows); 
if ($db->numberRows() > 0)  {
	$html = '<ul><li><table style="width:100%">';
	foreach ($rows as $row) {
		$trainer_id = $row['id'];
		$lastname = $row['lastname'];
		$firstname = $row['firstname'];
		$sport = str_replace(',', ', ', $row['sport'].'');
		$request_status = $row['request_status'];
		$modified = get_date_time($row['modified'].'');
		$created = get_date_time($row['created'].'');
		$link = ' href="javascript:void(0);"';
		
		$answer = 0; //need_answer
		$ans = '';
		$status = '';
		$status1 = '';
		$status2 = '';
		$status3 = '';

		if ($request_status == '0') { 
			$status1 = 'G_no'; 		$status2 = ''; 	 		$status3 = ''; $status = ' no_access';
		}
		elseif ($request_status == '1') { 
			$status1 = 'G_yes'; 	$status2 = ''; 	 		$status3 = 'G_no_trainer';
		}
		elseif ($request_status == '5') { 
			$status1 = 'G_leaveR'; 	$status2 = ''; 	 		$status3 = ''; $status = ' no_access';
		}
		elseif ($request_status == '15'){ 
			$status1 = 'G_leaveA'; 	$status2 = ''; 	 		$status3 = ''; $status = ' no_access';
		}
		elseif ($request_status == '7') { 
			$status1 = 'G_waitLR'; 	$status2 = 'G_yes_ans'; $status3 = 'G_no_ans'; $answer = 1; 
		}
		elseif ($request_status == '17'){ 
			$status1 = 'G_waitLA'; 	$status2 = 'G_yes_ans'; $status3 = 'G_no_ans'; $answer = 1; 
		}
		elseif ($request_status == '8') { 
			$status1 = 'G_waitN'; 	$status2 = 'G_yes_ans'; $status3 = 'G_no_ans'; $answer = 1; 
		}
		elseif ($request_status == '9') { 
			$status1 = 'G_wait'; 	$status2 = 'G_yes_ans'; $status3 = 'G_no_ans'; $answer = 1; 
		}

		if ($answer) {
			$ans = ' answer';
		}

		$request = array(
			'G_no' 		=> '<span class="req G_no'.$status.'" title="'.$req_text['G_no'].'"></span>', //0
			'G_yes' 	=> '<span class="req G_yes" title="'.$req_text['G_yes'].'"></span>', //1
			'G_leaveR' 	=> '<span class="req G_leaveR'.$status.'" title="'.$req_text['G_leaveR'].'"></span>', //5
			'G_leaveA' 	=> '<span class="req G_leaveA'.$status.'" title="'.$req_text['G_leaveA'].'"></span>', //15
			'G_waitLR' 	=> '<span class="req G_waitLR'.$ans.'" title="'.$req_text['G_waitLR'].'"></span>', //7
			'G_waitLA' 	=> '<span class="req G_waitLA'.$ans.'" title="'.$req_text['G_waitLA'].'"></span>', //17
			'G_waitN' 	=> '<span class="req G_waitN'.$ans.'" title="'.$req_text['G_waitN'].'"></span>', //8
			'G_wait' 	=> '<span class="req G_wait'.$ans.'" title="'.$req_text['G_wait'].'"></span>', //9
			
			'G_no_trainer' => '<span class="req G_no" title="'.$req_text['G_no_trainer'].'"></span>', //0
			'G_no_ans' 	=> '<span class="req G_no'.$ans.'" title="'.$req_text['G_no_ans'].'"></span>', //0
			'G_yes_ans' => '<span class="req G_yes'.$ans.'" title="'.$req_text['G_yes_ans'].'"></span>' //1
		);

		$status11 = ($status1 == '' ? '' : $request[$status1]);
		$status2 = ($status2 == '' ? '' : $request[$status2]);
		$status3 = ($status3 == '' ? '' : $request[$status3]);

		if ($request_status == '1') {
			$link .= ' class="trainer_link" data-id="' . $trainer_id . '"';
		}

		$html .= ''.
			'<tr>'.
				'<td>'.
					'<a'.$link.'>'.
						'<span class="' . ($answer ? 'answer' : ($status ? $status : '')) . '">'.
							$firstname.' '.$lastname . 
							($request_status=='1'
								? '<i class="fa fa-list-alt" style="float:right; margin-top:6px;" title="Trainerfreigaben"></i>'
								: ''
							).
						'</span>'.
					'</a>'.
				'</td>';

		$link_attributes = ''.
			' class="trainer_status" href="javascript:void(0)"'.
			' data-toggle="popover" data-placement="bottom" data-html="true"'.
			' title="<b>'.$firstname.' '.$lastname.'</b>"'.
			' data-content="'.
				$LANG->STATUS.': <b>'.$req_text[$status1].'</b><br>'.
				$LANG->SPORT.': <b>'.$sport.'</b><br>'.
				'<span class=popover_nowrap>'.
					$LANG->CREATED.': <b>'.$created.'</b><br>'.
					$LANG->MODIFIED.': <b>'.$modified.'</b>'.
				'</span>'.
			'"';

		$link_accept_attr = ' class="trainer_accept" href="javascript:void(0)" data-id="'.$trainer_id.'"';
		$link_reject_attr = ' class="trainer_reject" href="javascript:void(0)" data-id="'.$trainer_id.'" data-status="'.$request_status.'"';


		$html .= '<td class="req_act" style="padding-right:10px"><a'.$link_attributes.'>'.$status11.'</a></td>';
		$html .= '<td class="req_act"><a'.$link_accept_attr.'>'.$status2.'</a></td>';
		$html .= '<td class="req_act"><a'.$link_reject_attr.'>'.$status3.'</a></td>';
		$html .= '</tr>';
	}
	$html .= '</table></li></ul>';
}
else {
	$html = '<div class="empty_message">'.$LANG->TRAINER_NOT_AVAILABLE_REQUESTS.'</div>';
}
$html .= '<div id="trainer_status_message" style="margin:2px 0; text-align:center;"></div>';

echo $html;
?>
<script>jQuery(function(){ init_Trainer_Requests(); });</script>
