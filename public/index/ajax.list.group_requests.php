<?php // Group Requests List
$PATH_2_ROOT = '../';
require_once($PATH_2_ROOT.'_settings.regmon.php');
require($PATH_2_ROOT.'login/validate.php');

if (!$ADMIN AND !$LOCATION_ADMIN AND !$GROUP_ADMIN AND !$GROUP_ADMIN_2) exit;

$group_id = (int)($_POST['group_id'] ?? false);
if (!$group_id) exit;

$req_text = array(
	'G_no' => $LANG->STATUS_REQUEST_REJECTED, //0
	'G_yes' => $LANG->STATUS_REQUEST_ACCEPTED, //1
	'G_leaveR' => $LANG->STATUS_CANCELED_ACCESS_USER, //5
	'G_leaveA' => $LANG->STATUS_CANCELED_ACCESS_GROUPADMIN, //15
	'G_waitLR' => $LANG->STATUS_REQ_WAIT_CANCELED_USER, //7
	'G_waitLA' => $LANG->STATUS_REQ_WAIT_CANCELED_GROUPADMIN, //17
	'G_waitN' => $LANG->STATUS_REQ_WAIT_REJECTED_USER, //8
	'G_wait' => $LANG->STATUS_REQUEST_WAIT, //9
	'G_new' => $LANG->STATUS_REQ_WAIT_NEW_USER.' ('.$LANG->REQUEST_USER_INACTIVE.')', //10
	'G_newx' => $LANG->STATUS_REQ_WAIT_USER_INACTIVE, //11
	
	'G_no_group_user' => $LANG->REQUEST_GROUP_LEAVE_USER, //0
	'G_no_ans' => $LANG->REQUEST_REJECT, //0
	'G_yes_ans' => $LANG->REQUEST_ACCEPT //1
);

$html = '';

//group requests
$rows = $db->fetch("SELECT u2g.status AS request_status, u2g.modified, u2g.created, u.id, u.uname, u.lastname, u.firstname, u.sport, u.body_height, u.sex, u.birth_date, u.email, u.telephone, u.level 
FROM users2groups u2g 
JOIN users u ON u.id = u2g.user_id
WHERE u2g.group_id = ? AND u2g.status > 5 AND u2g.status != 15 AND u2g.status != 11 
ORDER BY u2g.id", array($group_id)); 
//print_r($rows); 

//if ($db->numberRows() > 0)  {
if (count($rows)) {
	$html = '<ul><li><table style="width:100%">';
	foreach ($rows as $row) {
		$group_user_id = $row['id'];
		$uname = $row['uname'];
		$lastname = $row['lastname'];
		$firstname = $row['firstname'];
		$sport = str_replace(',', ', ', $row['sport']);
		$request_status = $row['request_status'];

		$profil = ($row['level'] == '10' ? $LANG->LVL_ATHLETE
					: ($row['level'] == '30' ? $LANG->LVL_TRAINER
						: ($row['level'] == '40' ? $LANG->LVL_GROUP_ADMIN_2
							: ($row['level'] == '45' ? $LANG->LVL_GROUP_ADMIN
								: ($row['level'] == '50' ? $LANG->LVL_LOCATION
									: ($row['level'] == '99' ? $LANG->LVL_ADMIN
										: '')
								)
							)
						)
					)
				);

		$link = ' href="javascript:void(0);"';
		

		$answer = 0; //need_answer
		$ans = '';
		$status = '';
		$status1 = '';
		$status2 = '';
		$status3 = '';

		if ($request_status == '0') { 
			$status1 = 'G_no'; 		$status2 = ''; 	 		$status3 = ''; 	$status = ' no_access';
		}
		elseif ($request_status == '1') { 
			$status1 = 'G_yes'; 	$status2 = ''; 	 		$status3 = 'G_no_group_user'; 
		}
		elseif ($request_status == '5') { 
			$status1 = 'G_leaveR'; 	$status2 = ''; 	 		$status3 = ''; 	$status = ' no_access';
		}
		elseif ($request_status == '15'){ 
			$status1 = 'G_leaveA'; 	$status2 = ''; 	 		$status3 = ''; 	$status = ' no_access';
		}
		elseif ($request_status == '7') { 
			$status1 = 'G_waitLR'; 	$status2 = 'G_yes_ans'; $status3 = 'G_no_ans'; $answer = 1; 
		}
		elseif ($request_status == '17'){ 
			$status1 = 'G_waitLA'; 	$status2 = 'G_yes_ans'; $status3 = 'G_no_ans'; $answer = 1; 
		}
		elseif ($request_status == '8') { 
			$status1 = 'G_waitN';  	$status2 = 'G_yes_ans'; $status3 = 'G_no_ans'; $answer = 1; 
		}
		elseif ($request_status == '9') { 
			$status1 = 'G_wait'; 	$status2 = 'G_yes_ans'; $status3 = 'G_no_ans'; $answer = 1; 
		}
		elseif ($request_status == '10'){ 
			$status1 = 'G_new'; 	$status2 = 'G_yes_ans'; $status3 = 'G_no_ans'; $answer = 1; 
		}
		// elseif ($request_status == '11'){ 
		// 	$status1 = 'G_newx'; 	$status2 = ''; 		 	$status3 = ''; 		   $answer = 1; 
		// }

		if ($answer) $ans = ' answer';
		$request = array(
			'G_no' 		=> '<span class="req G_no'.$status.'" title="'.$req_text['G_no'].'"></span>', //0
			'G_yes' 	=> '<span class="req G_yes" title="'.$req_text['G_yes'].'"></span>', //1
			'G_leaveR' 	=> '<span class="req G_leaveR'.$status.'" title="'.$req_text['G_leaveR'].'"></span>', //5
			'G_leaveA' 	=> '<span class="req G_leaveA'.$status.'" title="'.$req_text['G_leaveA'].'"></span>', //15
			'G_waitLR' 	=> '<span class="req G_waitLR'.$ans.'" title="'.$req_text['G_waitLR'].'"></span>', //7
			'G_waitLA' 	=> '<span class="req G_waitLA'.$ans.'" title="'.$req_text['G_waitLA'].'"></span>', //17
			'G_waitN' 	=> '<span class="req G_waitN'.$ans.'" title="'.$req_text['G_waitN'].'"></span>', //8
			'G_wait' 	=> '<span class="req G_wait'.$ans.'" title="'.$req_text['G_wait'].'"></span>', //9
			'G_new' 	=> '<span class="req G_new'.$ans.'" title="'.$req_text['G_new'].'"></span>', //10
			
			'G_no_group_user' => '<span class="req G_no" title="'.$req_text['G_no_group_user'].'"></span>', //0
			'G_no_ans' 	=> '<span class="req G_no'.$ans.'" title="'.$req_text['G_no_ans'].'"></span>', //0
			'G_yes_ans' => '<span class="req G_yes'.$ans.'" title="'.$req_text['G_yes_ans'].'"></span>' //1
		);

		$status11 = ($status1 == '' ? '' : $request[$status1]);
		$status2 = ($status2 == '' ? '' : $request[$status2]);
		$status3 = ($status3 == '' ? '' : $request[$status3]);

		if ($request_status == '1') {
			$link .= ' class="group_user_link" data-id="' . $group_user_id . '"';
		}

		$html .= '<tr><td><a'.$link.'><span class="'.($answer?'answer':($status?$status:'')).'">('.$uname.') '.$firstname.' '.$lastname.'</span></a></td>';

		$link_attributes = ''.
			' class="group_user_status" href="javascript:void(0)"'.
			' data-toggle="popover" data-placement="bottom" data-html="true"'.
			' title="'.'Profil'.': <b>'.$profil.'</b><br>'.'"'.
			' data-content="'.
				$LANG->STATUS.': <b>'.$req_text[$status1].'</b><br>'.
				'<span class=popover_nowrap>'.
					'<hr>'.
					$LANG->REGISTER_USERNAME.': <b>'.$row['uname'].'</b><br>'.
					$LANG->REGISTER_FIRST_NAME.': <b>'.$firstname.'</b><br>'.
					$LANG->REGISTER_LAST_NAME.': <b>'.$lastname.'</b><br>'.
					$LANG->SPORT.': <b>'.$sport.'</b><br>'.
					$LANG->REGISTER_BODY_HEIGHT.': <b>'.$row['body_height'].'</b><br>'.
					$LANG->REGISTER_SEX.': <b>'.($row['sex']=='0'?$LANG->REGISTER_MALE:($row['sex']=='1'?$LANG->REGISTER_FEMALE:$LANG->REGISTER_OTHER)).'</b><br>'.
					$LANG->REGISTER_BIRTH_DATE.': <b>'.get_date($row['birth_date'].'').'</b><br>'.
					$LANG->REGISTER_EMAIL.': <b>'.$row['email'].'</b><br>'.
					$LANG->REGISTER_TELEPHONE.': <b>'.$row['telephone'].'</b><br><hr>'.
					$LANG->CREATED.': <b>'.get_date_time($row['created'].'').'</b><br>'.
					$LANG->MODIFIED.': <b>'.get_date_time($row['modified'].'').'</b>'.
				'</span>'.
			'"';
			
		$link_accept_attr = ' class="group_user_accept" href="javascript:void(0)" data-id="'.$group_user_id.'" data-status="'.$request_status.'"';
		$link_reject_attr = ' class="group_user_reject" href="javascript:void(0)" data-id="'.$group_user_id.'" data-status="'.$request_status.'"';


		$html .= '<td class="req_act" style="padding-right:10px"><a'.$link_attributes.'>'.$status11.'</a></td>';
		$html .= '<td class="req_act"><a'.$link_accept_attr.'>'.$status2.'</a></td>';
		$html .= '<td class="req_act"><a'.$link_reject_attr.'>'.$status3.'</a></td>';
		$html .= '</tr>';
	}
	$html .= '</table></li></ul>';
}
else {
	$html = '<div class="empty_message">'.$LANG->GROUP_USERS_NO_REQUESTS.'</div>';
}
$html .= '<div id="group_user_status_message" style="margin:2px 0; text-align:center;"></div>';

echo $html;
?>
<script>jQuery(function(){ init_Group_Requests(); });</script>
