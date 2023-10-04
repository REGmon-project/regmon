<?php // ajax Request
$PATH_2_ROOT = '../';
require_once($PATH_2_ROOT.'_settings.regmon.php');
require_once($PATH_2_ROOT."login/validate.php");


$request = $_POST['request'] ?? false;
if (!$request) {
	echo $LANG->REQUEST_SENDING_ERROR;
	exit;
}


$message = '';
$values = array();

switch ($request) {

	// Users 2 groups ####################################
	case 'user2group':  //User ask for access to the Group
		$location_id = (int)($_POST['location_id'] ?? false);
		$group_id = (int)($_POST['group_id'] ?? false);

		if ($group_id) {
			$action = $_POST['action'] ?? false;
			//status
			//==========================
			//0: rejected (7 17 8 9)
			//1: ok (9)
			//5: leave user (1)
			//15: leave group (1)
			//7: request after leave user (5)
			//17: request after leave group (15)
			//8: request after rejected (0)
			//9: new request
			//10:new request (private)
			//11:rejected (private)
			//==========================
			if ($action == 'group_user_request_access') //.
			{ //new request
				$values['user_id'] = $UID;
				$values['group_id'] = $group_id;
				$values['status'] = '9';
				if ($ADMIN or $THIS_LOCATION_ADMIN) {
					$values['status'] = '1';
				}
				$values['created'] = get_date_time_SQL('now');
				$values['created_by'] = $USERNAME;
				$values['modified'] = get_date_time_SQL('now');
				$values['modified_by'] = $USERNAME;

				$save = $db->insert($values, "users2groups");
			}
			elseif ($action == 'group_user_request_access_AN') //0
			{ //update - send again - rejected before
				$values['status'] = '8';
				if ($ADMIN or $THIS_LOCATION_ADMIN) {
					$values['status'] = '1';
				}
				$values['modified'] = get_date_time_SQL('now');
				$values['modified_by'] = $USERNAME;

				$save = $db->update($values, "users2groups", "user_id=? AND group_id=?", array($UID, $group_id));
			}
			elseif ($action == 'group_user_request_access_AL_user') //5  //after Requested (User) Leave
			{ //update - send again - leave before
				$values['status'] = '7';
				if ($ADMIN or $THIS_LOCATION_ADMIN) {
					$values['status'] = '1';
				}
				$values['modified'] = get_date_time_SQL('now');
				$values['modified_by'] = $USERNAME;

				$save = $db->update($values, "users2groups", "user_id=? AND group_id=?", array($UID, $group_id));
			}
			elseif ($action == 'group_user_request_access_AL_groupadmin') //15  //after Answerer (Groupadmin) Leave
			{ //update - send again - leave by groupadmin before
				$values['status'] = '17';
				if ($ADMIN or $THIS_LOCATION_ADMIN) {
					$values['status'] = '1';
				}
				$values['modified'] = get_date_time_SQL('now');
				$values['modified_by'] = $USERNAME;

				$save = $db->update($values, "users2groups", "user_id=? AND group_id=?", array($UID, $group_id));
			}

			//if cancel - revert the previous values
			elseif ($action == 'group_user_cancel_request_user') //7,17,8,9
			{ //update - cancel wait
				$row = $db->fetchRow("SELECT status FROM users2groups WHERE user_id = ? AND group_id = ?", array($UID, $group_id)); 
				if ($db->numberRows() > 0)  {
					$values['status'] = '0';

						if ($row['status'] == '7')  $values['status'] = '5';
					elseif ($row['status'] == '17') $values['status'] = '15';
					elseif ($row['status'] == '8')  $values['status'] = '0';
					elseif ($row['status'] == '9')  $values['status'] = '-1';
					
					if ($values['status'] != '-1') {
						$values['modified'] = get_date_time_SQL('now');
						$values['modified_by'] = $USERNAME;

						$save = $db->update($values, "users2groups", "user_id=? AND group_id=?", array($UID, $group_id));
					}
					else {
						$message = $LANG->REQUEST_FOR_GROUP_CANCELED;

						$delete = $db->delete("users2groups", "user_id=? AND group_id=?", array($UID, $group_id));
					}
				}
				else $message = $LANG->REQUEST_SENDING_ERROR;
			}
			
			//user want to leave the group
			elseif ($action == 'group_user_cancel_access') //1
			{ //update - leave - ok before
				$values['status'] = '5';
				$values['modified'] = get_date_time_SQL('now');
				$values['modified_by'] = $USERNAME;

				$save = $db->update($values, "users2groups", "user_id=? AND group_id=?", array($UID, $group_id));
			}
			else $message = $LANG->REQUEST_SENDING_ERROR;
		}
		else $message = $LANG->REQUEST_SENDING_ERROR;
		
		//message
		if ($message == '') {
			$message = str_replace('{DATE_TIME}', '<b>'.get_date_time('now').'</b>', $LANG->REQUEST_WAS_SENT_AT);
		}
		
		echo $message;
		
	  break;


	// Users 2 Group Answer #######################################
	//the group (groupadmin) answer to the User request for access to the Group
	case 'user2group_Answer': 
		$group_id = (int)($_POST['group_id'] ?? false);
		$user_id = (int)($_POST['user_id'] ?? false);
		$action = $_POST['action'] ?? false;
		if ($action AND $group_id) 
		{
			//accepted
			if ($action == 'group_user_accept' AND $user_id) {
				$request_status = $_POST['request_status'] ?? false;
				$values['status'] = '1';
				$values['modified'] = get_date_time_SQL('now');
				$values['modified_by'] = $USERNAME;

				$save = $db->update($values, "users2groups", "user_id=? AND group_id=?", array($user_id, $group_id));

				if (!$save) {
					$message = $LANG->REQUEST_ANSWER_ERROR;
				}

				if ($request_status == '10') {
					$values2 = array();			
					$values2['status'] = '1';
					$values2['modified'] = get_date_time_SQL('now');

					$save = $db->update($values2, "users", "id=?", array($user_id));
					

					//send activation OK email
					$user = $db->fetchRow("SELECT uname, email FROM users WHERE id = ?", array($user_id)); 
					if ($db->numberRows() > 0)  {
						// Email
						require($PATH_2_ROOT.'php/inc.email.php');

						$Subject = str_replace('{Username}', $user['uname'], $LANG->EMAIL_ACCOUNT_ACTIVATE_SUBJECT);
						/** @var string $Message */
						$Message = str_replace('{Username}', $user['uname'], $LANG->EMAIL_ACCOUNT_ACTIVATE_MESSSAGE);
						$Message = str_replace('{HTTP}', $CONFIG['HTTP'], $Message);
						$Message = str_replace('{DOMAIN}', $CONFIG['DOMAIN'], $Message);
						$Message = str_replace('{REGmon_Folder}', $CONFIG['REGmon_Folder'], $Message);
									
						if (SendEmail($user['email'], $Subject, $Message) == 'OK') {}
						else error_log($user['email'].', '. $Subject.', Activate User Email Not Send');
					}
				}
			}
			//rejected
			elseif ($action == 'group_user_reject' AND $user_id) {
				$request_status = $_POST['request_status'] ?? false;
				
				$status = '0';
					if ($request_status == '7') $status = '0';
				elseif ($request_status == '17')$status = '0';
				elseif ($request_status == '8') $status = '0';
				elseif ($request_status == '9') $status = '0';
				elseif ($request_status == '10')$status = '11';

				$values['status'] = $status;
				$values['modified'] = get_date_time_SQL('now');
				$values['modified_by'] = $USERNAME;

				$save = $db->update($values, "users2groups", "user_id=? AND group_id=?", array($user_id, $group_id));

				if (!$save) {
					$message = $LANG->REQUEST_ANSWER_ERROR;
				}
			}
			
			//reject after accept  //groupadmin want the user out of the group
			elseif ($action == 'group_user_cancel_access_groupadmin') //1
			{ //update - leave by Groupadmin - ok before
				$group_users_ids = $_POST['group_users_ids'] ?? array();
				
				foreach ($group_users_ids as $group_users_arr) 
				{
					$user = explode('_', $group_users_arr);
					$user_id = $user[1];
					$user_status = $user[2];

					$row = $db->fetchRow("SELECT status FROM users2groups WHERE user_id = ? AND group_id = ?", array($user_id, $group_id)); 
					if ($db->numberRows() > 0)  {
						if ($row['status'] == $user_status) { //if not already changed from another request
							$values['status'] = '15';
							$values['modified'] = get_date_time_SQL('now');
							$values['modified_by'] = $USERNAME;

							$save = $db->update($values, "users2groups", "user_id=? AND group_id=?", array($user_id, $group_id));
						}
						else $message = $LANG->REQUEST_SENDING_ERROR;
					}
					else $message = $LANG->REQUEST_SENDING_ERROR;
				}
			}
			else $message = $LANG->REQUEST_SENDING_ERROR;
		}
		else $message = $LANG->REQUEST_ANSWER_ERROR;
		

		//message
		if ($message == $LANG->REQUEST_ANSWER_ERROR OR $message == $LANG->REQUEST_SENDING_ERROR) {
			echo ''. //error
			'<div class="alert alert-danger alert-dismissible" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<strong>'.$LANG->ERROR.'!</strong> '.$message.'
			</div>';
		}
		elseif ($action == 'group_user_cancel_access_groupadmin') {
			echo ''. //success
			'<div class="alert alert-success alert-dismissible" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<strong>'.$LANG->SUCCESS.'!</strong> '
				.str_replace('{DATE_TIME}', '<b>'.get_date_time('now').'</b>', $LANG->REQUEST_WAS_SENT_AT)
			.'</div>';
		}
		
	  break;


	//################################################################
	//################################################################
	//################################################################

	// Users 2 Trainer 
	//a Trainer ask an Athlete for Access
	case 'user2trainer':
		$group_id = (int)($_POST['group_id'] ?? false);
		$action = $_POST['action'] ?? false;
		$athletes_ids = $_POST['athletes_ids'] ?? array();

		if ($action AND $group_id AND $athletes_ids) 
		{
			foreach ($athletes_ids as $athlete_arr) 
			{
				$athlete = explode('_', $athlete_arr);
				$athlete_id = (int)$athlete[1];
				$athlete_status = $athlete[2];
				$trainer_id = $UID;

				//status
				//==========================
				//0: rejected (7 17 8 9)
				//1: ok (9)
				//5: leave trainer (1)
				//15: leave athlete (1)
				//7: request after leave trainer (5)
				//17: request after leave athlete (15)
				//8: request after rejected (0)
				//9: new request
				//==========================
				if ($action == 'request_access_athlete') //0, 5, .=null ( ->9, 0->8, 5->7, 15->17)
				{
					if ($athlete_status == '.')
					{ //new request
						$values['user_id'] = $athlete_id;
						$values['group_id'] = $group_id;
						$values['trainer_id'] = $trainer_id;
						$values['status'] = '9';
						$values['modified'] = get_date_time_SQL('now');
						$values['modified_by'] = $USERNAME;
						$values['created'] = get_date_time_SQL('now');
						$values['created_by'] = $USERNAME;

						$save = $db->insert($values, "users2trainers");
					}
					else {
						if ($athlete_status == '0')
						{ //update - send again - rejected before
							$values['status'] = '8';
						}
						elseif ($athlete_status == '5') //after he(trainer) leave 
						{ //update - send again - leave before
							$values['status'] = '7';
						}
						elseif ($athlete_status == '15') //after user leave
						{ //update - send again - leave before
							$values['status'] = '17';
						}

						$values['modified'] = get_date_time_SQL('now');
						$values['modified_by'] = $USERNAME;

						$save = $db->update($values, "users2trainers", "user_id=? AND group_id=? AND trainer_id=?", array($athlete_id, $group_id, $trainer_id));
					}
				}
				//if cancel -revert the preveus values
				elseif ($action == 'cancel_request_athlete') //7, 8, 9  (7->5, 17-15, 8->0, 9->delete)
				{ //update - cancel wait
					$row = $db->fetchRow("SELECT status FROM users2trainers WHERE user_id = ? AND group_id = ? AND trainer_id=?", array($athlete_id, $group_id, $trainer_id)); 
					if ($db->numberRows() > 0)  {
						if ($row['status'] == $athlete_status) { //if not already changed from another request
							$values['status'] = '0';
					
								if ($athlete_status == '7') $values['status'] = '5';
							elseif ($athlete_status == '17')$values['status'] = '15';
							elseif ($athlete_status == '8') $values['status'] = '0';
							elseif ($athlete_status == '9') $values['status'] = '-1';
							
							if ($values['status'] != '-1') {
								$values['modified'] = get_date_time_SQL('now');
								$values['modified_by'] = $USERNAME;

								$save = $db->update($values, "users2trainers", "user_id=? AND group_id=? AND trainer_id=?", array($athlete_id, $group_id, $trainer_id));
							}
							else {
								$message = $LANG->REQUEST_FOR_ACCESS_CANCEL;

								$delete = $db->delete("users2trainers", "user_id=? AND group_id=? AND trainer_id=?", array($athlete_id, $group_id, $trainer_id));
							}
						}
						else $message = $LANG->REQUEST_SENDING_ERROR;
					}
					else $message = $LANG->REQUEST_SENDING_ERROR;
				}
				
				//Trainer have access to Athlete but not want it any more - leaves Athlete (1->5)
				elseif ($action == 'cancel_access_athlete') //1
				{ //update - leave - ok before
					$row = $db->fetchRow("SELECT status FROM users2trainers WHERE user_id = ? AND group_id = ? AND trainer_id=?", array($athlete_id, $group_id, $trainer_id)); 
					if ($db->numberRows() > 0)  {
						if ($row['status'] == $athlete_status) { //an den exei alaksei endiamesa sta request
							$values['status'] = '5';
							$values['modified'] = get_date_time_SQL('now');
							$values['modified_by'] = $USERNAME;

							$save = $db->update($values, "users2trainers", "user_id=? AND group_id=? AND trainer_id=?", array($athlete_id, $group_id, $trainer_id));
						}
						else $message = $LANG->REQUEST_SENDING_ERROR;
					}
					else $message = $LANG->REQUEST_SENDING_ERROR;
				}
			} //foreach $athletes_ids
		}
		else $message = $LANG->REQUEST_SENDING_ERROR;
		

		//message
		if ($message == $LANG->REQUEST_SENDING_ERROR) {
			echo ''. //error
			'<div class="alert alert-danger alert-dismissible" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<strong>'.$LANG->ERROR.'!</strong> '.$LANG->REQUEST_SENDING_ERROR.'
			</div>';
		}
		else {
			echo ''. //success
			'<div class="alert alert-success alert-dismissible" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<strong>'.$LANG->SUCCESS.'!</strong> '
				.($message == $LANG->REQUEST_FOR_ACCESS_CANCEL ? $LANG->REQUEST_FOR_ACCESS_CANCEL : str_replace('{DATE_TIME}', '<b>'.get_date_time('now').'</b>', $LANG->REQUEST_WAS_SENT_AT))
			.'</div>';
		}
		
	  break;
	  

	// User 2 Trainer Answer /////////////////////////////////////////////////////////////////
	//User answer to Trainer request for access 
	case 'user2trainer_Answer': 
		$action = $_POST['action'] ?? false;
		$group_id = (int)($_POST['group_id'] ?? false);
		$trainer_id = (int)($_POST['trainer_id'] ?? false);

		if ($action AND $group_id AND $trainer_id) 
		{
			$athlete_id = $UID;
			$status = '0';

			//accepted
			if ($action == 'trainer_accept') $status = '1'; //(7,17,8,9->1)
			//rejected
			elseif ($action == 'trainer_reject') {
				$request_status = $_POST['request_status'] ?? false;

					if ($request_status == '1') $status = '15';
				elseif ($request_status == '7') $status = '0';
				elseif ($request_status == '17')$status = '0';
				elseif ($request_status == '8') $status = '0';
				elseif ($request_status == '9') $status = '0';
			}

			$values['status'] = $status;
			$values['modified'] = get_date_time_SQL('now');
			$values['modified_by'] = $USERNAME;

			$save = $db->update($values, "users2trainers", "user_id=? AND group_id=? AND trainer_id=?", array($athlete_id, $group_id, $trainer_id));

			if (!$save) {
				$message = $LANG->REQUEST_ANSWER_ERROR;
			}
		}
		else $message = $LANG->REQUEST_ANSWER_ERROR;
		
		//message
		if ($message == $LANG->REQUEST_ANSWER_ERROR) {
			echo ''.//error
			'<div class="alert alert-danger alert-dismissible" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<strong>'.$LANG->ERROR.'!</strong> '.$LANG->REQUEST_ANSWER_ERROR.'
			</div>';
		}
		
	  break;
	  
	// New 
	case 'new':
	
	  break;
}

?>