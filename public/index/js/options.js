

jQuery(function() 
{
	//#################################################
	//ACCORDION #######################################

	$('#C_Requests_From_Trainers_link').on('click',function() {
		if ($('#A_Requests_From_Trainers').text().trim()=='') {
			load_Trainer_Requests();
		}
	});

	$('#C_Request_Access_From_Athletes_link').on('click',function() {
		if ($('#A_Request_Access_From_Athletes').text().trim()=='') {
			load_Users_2_Trainers();
		}
	});
	
	$('#C_Group_Requests_link').on('click',function() {
		if ($('#A_Group_Requests').text().trim()=='') {
			load_Group_Requests();
		}
	});

	$('#C_Group_Users_link').on('click',function() {
		if ($('#A_Group_Users').text().trim()=='') {
			load_Users_2_Group();
		}
	});

	$('#C_Location_Groups_link').on('click',function() {
		if ($('#A_Location_Groups').text().trim()=='') {
			load_Location_Groups();
		}
	});
	

	//Edit choice of Group Forms - AJAX after click
	$('#C_Athlete_Forms_Select_link').on('click',function() {
		if ($('#C_Athlete_Forms_Select').text().trim()=='') {
			load_Forms_ATHLETE_Selection();
		}
	});

	//Edit choice of Trainers Access Forms - AJAX after click
	$('#C_Athlete_Give_Forms_Access_To_Trainers_link').on('click',function() {
		if ($('#C_Athlete_Give_Forms_Access_To_Trainers').text().trim()=='') {
			load_Athlete__Trainers_Select(-1);
		}
	});

	//Show choice of Trainers Access Forms - AJAX after click
	$('#C_Trainer_Access_To_Athletes_Forms_link').on('click',function() {
		if ($('#C_Trainer_Access_To_Athletes_Forms').text().trim()=='') {
			load_Trainer__Athletes_Select(-1);
		}
	});

	//Edit choice of Group Forms - AJAX after click
	$('#C_Group_Forms_Select_link').on('click',function() {
		if ($('#C_Group_Forms_Select').text().trim()=='') {
			load_Forms_ADMIN_Selection();
		}
	});

	$('#C_Forms_link').on('click',function() {
		if ($('#A_Forms').text().trim()=='') {
			load_Forms();
		}
	});

	$('#C_Categories_link').on('click',function() {
		if ($('#A_Categories').text().trim()=='') {
			load_Categories();
		}
	});

	$('#C_Sports_Dropdowns_link').on('click',function() {
		if ($('#A_Sports_Dropdowns').text().trim()=='') {
			load_Sports_Dropdowns();
		}
	});

}); //jQuery(function()



//##############################################################################
// Functions ###################################################################
//##############################################################################

function load_New_Info(show_loading) {
	if (!V_GROUP_TRAINER) {
		if (V_ONLINE) {
			$('#rC_loading').show();
			$('#requestsCount').hide();

			const post_data = {
				group_id: V_GROUP
			};
			$.post('index/ajax.requests_count.php', post_data, function(data, result){
				if (data == 'login') {
					window.location.reload();
				}
				else {
					if (V_ADMIN || V_LOCATION_ADMIN || V_GROUP_ADMIN || V_GROUP_ADMIN_2)
					{
						$('#GRP_requestsCount').hide();

						if (data > 0) {
							$('#requestsCount').show();
							$('#requestsCountValue').text(data);
							$('#GRP_requestsCount').show();
							$('#GRP_requestsCountValue').text(data);

							if (V_requestsCount != data) {
								if ($('#A_Group_Requests').text().trim()!='') {
									load_Group_Requests();
								}
							}
							V_requestsCount = data;
						}
					}
					else if (!V_GROUP_TRAINER) {
						$('#ATH_requestsCount').hide();

						if (data > 0) {
							$('#requestsCount').show();
							$('#requestsCountValue').text(data);
							$('#ATH_requestsCount').show();
							$('#ATH_requestsCountValue').text(data);

							if (V_requestsCount != data) {
								if ($('#A_Requests_From_Trainers').text().trim()!='') {
									load_Trainer_Requests();
								}
							}
							V_requestsCount = data;
						}
					}
				}
				$('#rC_loading').hide();
			});
		}
		if (!show_loading) {
			loading.hide();
		}
	}
}



//###################################
//Trainers Requests - Athletes Answer 
function load_Trainer_Requests() {
	if (hasAccess()) {
		const post_data = {
			group_id: V_GROUP
		};
		$("#A_Requests_From_Trainers").load("index/ajax.list.trainer_requests.php", post_data);
	}
}

function init_Trainer_Requests() {
	$('.trainer_link').off('click').on('click',function() {
		const trainer_id = $(this).attr('data-id');
		Edit_Athlete_2_Trainer_Forms__Load_Open(trainer_id);
	});

	$('.trainer_status').off('click').popover();

	$('.trainer_accept').off('click').on('click',function() {
		const trainer_id = $(this).attr('data-id');
		const post_data = {
			request: 'user2trainer_Answer',
			action: 'trainer_accept',
			group_id: V_GROUP,
			trainer_id: trainer_id
		};
		$.post('index/ajax.request.php', post_data, function(data, result){
			if (data) {
				$("#trainer_status_message").html(data); //.show();
			}

			load_Trainer_Requests();

			Edit_Athlete_2_Trainer_Forms__Load_Open(trainer_id);

			load_New_Info(false);
		});
	});

	$('.trainer_reject').confirmation({
		href: 'javascript:void(0)',
		title: LANG.ARE_YOU_SURE, placement: 'top',
		btnOkLabel: LANG.YES, btnOkClass: 'btn btn-sm btn-success mr10',
		btnCancelLabel: LANG.NO, btnCancelClass: 'btn btn-sm btn-danger',
		onConfirm: function(e, button) {
			const trainer_id = $(button).attr('data-id');
			const request_status = $(button).attr('data-status');
			const post_data = {
				request: 'user2trainer_Answer',
				action: 'trainer_reject',
				group_id: V_GROUP,
				trainer_id: trainer_id,
				request_status: request_status
			};
			$.post('index/ajax.request.php', post_data, function(data, result){
				if (data) {
					$("#trainer_status_message").html(data); //.show();
				}

				load_Trainer_Requests();

				Edit_Athlete_2_Trainer_Forms__Load_Open(trainer_id);

				load_New_Info(false);
			});
		}
	});
}


//##################################
//Group Requests - GroupAdmin Answer Requests here
function load_Group_Requests() {
	if (hasAccess()) {
		$("#A_Group_Requests").load("index/ajax.list.group_requests.php", {group_id: V_GROUP});
	}
}
function init_Group_Requests() {
	$('.group_user_status').off('click').popover();

	$('.group_user_accept').off('click').on('click',function() {
		loading.show();

		const user_id = $(this).attr('data-id');
		const request_status = $(this).attr('data-status');
		const post_data = {
			request: 'user2group_Answer',
			action: 'group_user_accept',
			group_id: V_GROUP,
			user_id: user_id,
			request_status: request_status
		};
		$.post('index/ajax.request.php', post_data, function(data, result){
			if (data) {
				$("#group_user_status_message").html(data); //.show();
			}

			load_Group_Requests();

			$("#group_users").trigger("reloadGrid", { fromServer: true });

			load_New_Info(false);

			loading.hide();
		});
	});

	$('.group_user_reject').off('click').confirmation({
		href: 'javascript:void(0)',
		title: LANG.ARE_YOU_SURE, placement: 'top',
		btnOkLabel: LANG.YES, btnOkClass: 'btn btn-sm btn-success mr10',
		btnCancelLabel: LANG.NO, btnCancelClass: 'btn btn-sm btn-danger',
		onConfirm: function(e, button) {
			const user_id = $(button).attr('data-id');
			const request_status = $(button).attr('data-status');
			const post_data = {
				request: 'user2group_Answer',
				action: 'group_user_reject',
				group_id: V_GROUP,
				user_id: user_id,
				request_status: request_status
			};
			$.post('index/ajax.request.php', post_data, function(data, result){
				if (data) {
					$("#group_user_status_message").html(data); //.show();
				}

				load_Group_Requests();

				$("#group_users").trigger("reloadGrid", { fromServer: true });

				load_New_Info(false);
			});
		}
	});
}


//Request_Access by Athletes from Trainers ######################
function load_Users_2_Trainers() {
	if (hasAccess()) {
		const post_data = {
			group_id: V_GROUP
		};
		$("#A_Request_Access_From_Athletes").load("index/ajax.grid.trainer_users.php", post_data);
	}
}


//Group Users ###################################################
function load_Users_2_Group() {
	if (hasAccess()) {
		const post_data = {
			group_id: V_GROUP,
			location_id: V_Group_2_Location[V_GROUP][0]
		};
		$("#A_Group_Users").load("index/ajax.grid.group_users.php", post_data);
	}
}


//Location Groups ###############################################
function load_Location_Groups() {
	if (hasAccess()) {
		const post_data = {
			location_id: V_Group_2_Location[V_GROUP][0]
		};
		$("#A_Location_Groups").load("index/ajax.grid.location_groups.php", post_data);
	}
}


//Forms #########################################################
function load_Forms() {
	if (hasAccess()) {
		$("#A_Forms").load("index/ajax.grid.forms.php");
	}
}


//Categories ####################################################
function load_Categories() {
	if (hasAccess()) {
		$("#A_Categories").load("index/ajax.grid.categories.php");
	}
}


//Sports Dropdowns ##############################################
function load_Sports_Dropdowns() {
	if (hasAccess()) {
		$("#A_Sports_Dropdowns").load("index/ajax.grid.dropdowns_n_sports.php");
	}
}

