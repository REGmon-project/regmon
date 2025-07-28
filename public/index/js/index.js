if (!Production_Mode) "use strict"; //remove on production so "const" works on iOS Safari 

var V_SELECTED_DATE;
var V_ANIMATE_RUN = false;
var V_requestsCount = 0;

//fancybox defaults

window.addEventListener('online', function(e) {
	V_ONLINE = true;
    // Resync data with server.
    console.log("You are online");
    //Page.hideOfflineWarning();
}, false);
window.addEventListener('offline', function(e) {
	V_ONLINE = false;
    // Queue up events for server.
    console.log("You are offline");
    //Page.showOfflineWarning();
}, false);
// Check if the user is connected.
if (navigator.onLine) {
	V_ONLINE = true;
    console.log("Start online");
} else {
	V_ONLINE = false;
    console.log("Start offline");
    // Show offline message
    //Page.showOfflineWarning();
}


jQuery(function() 
{
	//nav button
	$('.nav_link').on('click',function() {
		loading.show();
	});
	
	//button Export
	$("button.export").on('click',function() {
		loading.show();
		window.location.href = 'export.php';
	});
	//button Import
	$("button.import").on('click',function() {
		loading.show();
		window.location.href = 'import.php';
	});
	//button Profile
	$("a.nav_profile").fancybox(fancyBoxDefaults);
	
	//tooltip --gone at the end
	//$('[data-toggle="tooltip"]').tooltip({ trigger: "hover" }); //if not give hover it not close after click


	
	//Group Select ###############################################################
	$("#Select_Group").chosen({width:'100%', placeholder_text_single: LANG.SELECT_OPTION, no_results_text: LANG.NO_RESULTS, search_contains: true, disable_search_threshold: 10});
	

	$('#Select_Group').on('chosen:showing_dropdown', function() {
		$('#Select_Group_chosen .chosen-results li').each(function() {
			//get v_value class --it has the group_id
			let val = $(this).attr("class").match(/v_[\w]*\b/);
			if (val) {
				val = val[0].split('_')[1];
				$(this).append(
					'<span title="' + $('#Select_Group option[value="' + val + '"]').attr('data-status') + '">&nbsp;</span>'
				);
			}
		});
	});	
	

	V_GROUP = $("#Select_Group").val();
	

	//init icon and submit buttons
	group_icons_buttons();
	

	//private
	$("#private_submit").on('click',function() {
		const p_val = encodeURIComponent( $('#private_key').val() ); //support for special characters
		if (p_val == '') {
			return false;
		}

		const location_id = (V_Group_2_Location[V_GROUP] &&
							V_Group_2_Location[V_GROUP][0]) ? V_Group_2_Location[V_GROUP][0] 
															: V_LOCATION;

		$.ajax({
			url: "login/ajax.check_private_key.php?private_key=" + p_val + '&location_id=' + location_id,
			success: function (data_res)
			{
				if (data_res != 'false' && data_res != '') {
					let action = 'group_user_request_access';

					//if user where in this group before
					if (V_User_2_Groups[data_res]) {
						if (V_User_2_Groups[data_res].status == 0) {
							action = 'group_user_request_access_AN';
						}
						if (V_User_2_Groups[data_res].status == 5) {
							action = 'group_user_request_access_AL_user';
						}
						if (V_User_2_Groups[data_res].status == 15) {
							action = 'group_user_request_access_AL_groupadmin';
						}
					}

					const data = {
						request: 'user2group',
						action: action,
						group_id: data_res,
						location_id: location_id
					};
					$.post('index/ajax.request.php', data, function(data, result){
						V_GRID_SAVE = true; //for continue loading
						window.location.reload();
					});
				}
				else {
					alert(LANG.GROUPS.PRIVATE_KEY_ERROR);
				}
			}
		});
	});

	$("#private_close").on('click',function() {
		$('#private_group').hide();
		$('#Select_Group_chosen').show();
	});
	
	
	//Group Select on Change
	$("#Select_Group").on('change', function () 
	{
		if ($(this).val() == '') {
			return false;
		}

		if ($(this).val() == 'Private') {
			$('#Select_Group_chosen').hide();
			$('#private_group').css('display', 'inline-block');
			
			$('#Select_Group').val(V_GROUP);
			$("#Select_Group").trigger("chosen:updated");
		}
		else {
			V_GROUP = $(this).val();

			$.cookie('ATHLETE', V_UID, { path: '/'+V_REGmon_Folder, SameSite:'Lax' });
			
			const post_data = {
				group_id: V_GROUP,
				location_id: V_Group_2_Location[V_GROUP][0],
				u_id: V_UID
			};
			$.post('index/ajax.user_group_update.php', post_data, function(data, result){
				V_GRID_SAVE = true; //for continue loading
				window.location.reload();
			});
		}
	});
	

	//Calendar / Options Buttons
	$('input[name="options_calendar"]').on('change', function () {
		if (this.id == 'view_calendar') {
			$("#group_data").hide();
			$("#group_calendar").show();
			Select_Athletes_enable();
		}
		else {
			$("#group_data").show();
			$("#group_calendar").hide();
			Select_Athletes_disable();
		}

		$(window).trigger('resize'); //it need this bcz calendar loses the scrollbar
	});


	$("#group_data").hide();
	$("#view_calendar").trigger("click"); //init calendar view

	
	//button submit_group
	$('button.submit_group').confirmation({
		href: 'javascript:void(0)',
		title: LANG.ARE_YOU_SURE, placement: 'top',
		btnOkLabel: LANG.YES, btnOkClass: 'btn btn-sm btn-success mr10',
		btnCancelLabel: LANG.NO, btnCancelClass: 'btn btn-sm btn-danger',
		onConfirm: function(e, button) {
			if ($("#Select_Group").val() == '') {
				return false;
			}
			const submit_group_id = $(button).prop('id');
			if (submit_group_id == 'group_user_cancel_access') {
				//double check
				if (!confirm("\n\n"+LANG.REQUEST.USER_LEAVE_GROUP+"\n\n"+LANG.ARE_YOU_SURE+"\n\n")) {
					return false;
				}
			}

			const post_data = {
				request: 'user2group',
				action: submit_group_id,
				group_id: V_GROUP,
				location_id: V_Group_2_Location[V_GROUP][0]
			};
			$.post('index/ajax.request.php', post_data, function (data, result)
			{
				$("#group_buttons_message").html(data).show();

				V_GRID_SAVE = true; //for continue loading

				$.cookie('ATHLETE', V_UID, { path: '/' + V_REGmon_Folder, SameSite:'Lax' });
				
				window.location.reload();
			});
		}
	});
	
	
	//#################################################
	//#################################################
	//Calendar ########################################
	$('#calendar').fullCalendar({
		header: {
			left: 'prev,next today',
			center: 'title',
			right: 'month,agendaWeek,agendaDay'
		},
		defaultView: 'agendaWeek',
		allDaySlot: true,
		allDayText: '',
		lang: LANG.LANG_CURRENT,
		navLinks: true,
		displayEventTime: false,	
		height: 550,
		contentHeight: 500,
		//forceEventDuration:true,
		//minTime:'06:00:00',
		//maxTime:'22:00:00',
		//defaultDate: '2016-02-12',
		//editable: true,
		//eventLimit: true, // allow "more" link when too many events
		events: {
			url: getCalendarUrl(),
			cache: true, //this will stop calendar adding a timestamp at the end of each url
			error: function() {
			}
			//color: 'yellow',   // for all
			//textColor: 'green' // for all
		},
		eventRender: function (event, element, view) {
			//console.log(event, event.start._d.getUTCHours());
			element.popover({
				//container: view.name=='month' ? '.fc-body' : '.fc-time-grid',
				container: (view.name=='month' || event.allDay) ? '#calendar' : '.fc-time-grid',
				viewport: {selector:  (view.name=='month' || event.allDay) ? '#calendar' : '.fc-time-grid', padding: 2},
				title: "<b>"+event.title+"</b>",
				//placement: 'auto',
				placement: function (context, source) {
					if (view.name=='month' || event.allDay) {
						//return 'auto';
						const popoverHeight = 220; //190;
						const container = $(window).height();
						const scrollTop = $(window).scrollTop();
						const offset = $(source).offset();				
						if ((container + scrollTop - offset.top - popoverHeight) > 0) {
							return 'bottom';
						}
						return 'top';
					}
					else {
						const popoverHeight = 290; //190;
						const container = $('.fc-time-grid').height();
						//const scrollTop = $('.fc-scroller').scrollTop();
						const offset = $(source).offset();				
						if (container - (offset.top+popoverHeight) > 150) {
							return 'bottom';
						}
						return 'top';
					}
				},				
				html:true,
				//trigger:"click focus",
				trigger:"manual",
				content: replaceAll(event.msg, "\n", '<br>')
			});
			
			if (event.status == '0') {
				$(element).addClass('event_deactivated');
			}
		},
		//eventAfterAllRender: function (view) { //put it in source
			//$('.fc-center h2').text($('.fc-center h2').text().replace(' —','. —'));
		//},
		eventClick: function(event, jsEvent, view) {
			//close other popups
			const this_pop = $(this).attr('aria-describedby');
			$('.popover.in').each(function(i, el) {
				if (el.id != this_pop) {
					$("a[aria-describedby=" + el.id + "]").popover('toggle');
				}
			});

			//show this popup
			$(this).popover('toggle');
			
			//allDay popups need reset
			if (event.allDay) {
				$(this).data('bs.popover').tip().css({ 'margin-top': '0px', 'margin-left': '0px' });
			}
			
			const event_el = this;
			$("button#Cal_Edit_" + event.id).fancybox(fancyBoxDefaults_iframe);
			$("button#Cal_Res_" + event.id).fancybox(fancyBoxDefaults_iframe);
			$("button#Cal_Res_Sub_" + event.id).fancybox(fancyBoxDefaults_iframe);
			
			if (event.status == '1') {
				$("button#forms_data_deactivate_" + event.id).show();
				$("button#forms_data_activate_" + event.id).hide();
				$("button#Cal_Res_Sub_" + event.id).show();
			} else {
				$("button#forms_data_deactivate_" + event.id).hide();
				$("button#forms_data_activate_" + event.id).show();
				$("button#Cal_Res_Sub_" + event.id).hide();
			}

			$("button#forms_data_deactivate_" + event.id).on('click', function () {
				$.get('php/ajax.php?i=forms_data&oper=status&ID=' + event.id + '&status=0', function (data, result)
				{
					event.status = '0';
					$('.popover').popover('hide'); //hide all popovers

					$("button#forms_data_deactivate_" + event.id).hide();
					$("button#forms_data_activate_" + event.id).show();
					$("button#Cal_Res_Sub_" + event.id).hide();

					$(event_el).addClass('event_deactivated');

					$('#calendar').fullCalendar('updateEvent', event);
				});
			});

			$("button#forms_data_activate_" + event.id).on('click', function () {
				$.get('php/ajax.php?i=forms_data&oper=status&ID=' + event.id + '&status=1', function (data, result)
				{
					event.status = '1';
					$('.popover').popover('hide'); //hide all popovers

					$("button#forms_data_deactivate_" + event.id).show();
					$("button#forms_data_activate_" + event.id).hide();
					$("button#Cal_Res_Sub_" + event.id).show();

					$(event_el).removeClass('event_deactivated');

					$('#calendar').fullCalendar('updateEvent', event);
				});
			});

			$("button#Cal_Res_Del_" + event.id).confirmation({
				href: 'javascript:void(0)',
				title: LANG.CONFIRM_DELETE_ENTRY, placement: 'top',
				btnOkLabel: LANG.YES, btnOkClass: 'btn btn-sm btn-success mr10',
				btnCancelLabel: LANG.NO, btnCancelClass: 'btn btn-sm btn-danger',
				onConfirm: function (e, button) {
					$.get('php/ajax.php?i=forms_data&oper=status&ID=' + event.id + '&status=-1', function (data, result)
					{
						$('.popover').popover('hide'); //hide all popovers
						$(event_el).hide();

						parent.Swal({
							type: 'success',
							title: LANG.ENTRY_DELETE_SUCCESS,
							showConfirmButton: false,
							timer: 2000
						});
					});
				}
			});

			//notes
			$("button#note_delete").on('click',function() {
				const post_data = {
					group_id: V_GROUP,
					athlete_id: V_ATHLETE,
					ID: event.id
				};
				$.post('index/ajax.note_delete.php', post_data, function(data, result){
					$('.popover').popover('hide'); //hide all popovers
					$('#calendar').fullCalendar( 'refetchEvents' );
				});
			});

			$("button#note_edit").on('click', function () {
				$('.popover').popover('hide'); //hide all popovers
				$.fancybox($("#create_Note"), $.extend({}, fancyBoxDefaults, { minWidth: 300 }));
				//console.log(event);

				init_Notes_Edit(event.id, event.allDay, event.showInGraph, event.start._i, event.end._i, event.title, event.text, event.color2);
			});
		},
		dayClick: function(date, jsEvent, view) {
			V_SELECTED_DATE = date.format();
			//console.log(V_SELECTED_DATE, jsEvent, jsEvent.target, $(jsEvent.target).hasClass('fc-day'), view.name);
			$.cookie('SELECTED_DATE', V_SELECTED_DATE, { path: '/' + V_REGmon_Folder, SameSite:'Lax' });
			
			if (hasWriteAccess()) {
				if (jsEvent.target.tagName == "TD") { //bcz it opens with a click to popover
					if (view.name != 'month' && V_SELECTED_DATE.indexOf('T') == -1) { //all-day Note
						if (V_TRAINER_W_PERMS.indexOf('All') != -1 ||
							V_TRAINER_W_PERMS.indexOf('Note_n') != -1)
						{
							$.fancybox($("#create_Note"), $.extend({}, fancyBoxDefaults, { minWidth: 300 }));
							
							init_Notes_Create('Cal_' + view.name);
						}
						else {
							$.fancybox('<div class="empty_message">' + LANG.NOT_HAVE_ACCESS_RIGHTS + '</div>', $.extend({}, fancyBoxDefaults, { minWidth: 300, minHeight: 60 }));
						}
					}
					else {
						jsEvent.preventDefault();
						jsEvent.stopPropagation(); //not click the behind header

						setTimeout(function () {
							$.fancybox($("#A_Box_Forms_Menu"), fancyBoxDefaults);
							setTimeout(function () {
								$("#A_Box_Forms_Menu").parent('.fancybox-inner').css('height', 'auto');
							}, 300);
						}, 300);
					}
				}
			}
			else {
				const p_text = ''+
					'<div style="font-size:17px; padding:25px 10px; text-align:center; font-weight:bold;">' +
						LANG.WRITE_ACCESS_PROBLEM +
						'<div class="not_display" style="width:520px;"></div>'+
					'</div>';
				
				$.fancybox($.extend({},fancyBoxDefaults,{minWidth: 300, content:p_text, beforeShow:function(){} }));
			}
		},
		eventMouseover: function(calEvent, jsEvent, view) {
			$(this).css('opacity', '0.5');
		},
		eventMouseout: function(event, jsEvent, view) {
			if (event.status == '0') {
				$(this).css('opacity', '0.8');
			} else {
				$(this).css('opacity', '1');
			}
		},
		loading: function(bool) {
			$('.popover').popover('hide'); //hide all popovers
		},
		viewRender: function( view, element) {
			$('.popover.in').popover('hide'); //hide all popovers
		}
	});	
	

	//add Note Button
	//on mozila have float:left from .fc .fc-toolbar > * > *
	$("#calendar .fc-toolbar .fc-center h2").after(
		'<br style="float:none;">' +
		//Go to date button
		'<div class="input-group" id="datetimepicker_hiddenDate" style="float:left; margin-left:-3px;">'+
			'<input type="hidden" id="hiddenDate"/>'+
			'<button id="go_to_date" type="button" title="'+LANG.BUTTON_DATUM_TOOLTIP+'" data-toggle="tooltip" data-placement="top" data-container="body" class="input-group-addon fc-button fc-state-default fc-corner-left fc-corner-right" style="height:25px; width:auto; padding:0 5px; border-left:1px solid rgba(0, 0, 0, 0.1);"><i class="fa fa-calendar"></i> '+LANG.BUTTON_DATUM+'</button>'+
		'</div>' +
		//border-left: 1px solid rgba(0, 0, 0, 0.1); bcz the .input-group-addon which needed it, zero it
		//addNote button
		'<button id="addNote" type="button" title="'+LANG.BUTTON_NOTE_TOOLTIP+'" data-toggle="tooltip" data-placement="top" data-container="body" class="fc-button fc-state-default fc-corner-left fc-corner-right" style="float:right; height:25px; padding:0 5px;"><i class="fa fa-commenting"></i> '+LANG.BUTTON_NOTE+'</button>'
	);

	$('#addNote').hover(function() {
		$(this).addClass('fc-state-hover');
	}, function() {
		$(this).removeClass('fc-state-hover');
	});

	$('#addNote').on('click',function() {
		if (V_TRAINER_W_PERMS.indexOf('All') != -1 ||
			V_TRAINER_W_PERMS.indexOf('Note_n') != -1)
		{
			$.fancybox($("#create_Note"), $.extend({},fancyBoxDefaults,{minWidth: 300}));
			init_Notes_Create('Cal_Button');
		}
		else {
			$.fancybox(
				'<div class="empty_message">' + LANG.NOT_HAVE_ACCESS_RIGHTS + '</div>',
				$.extend({}, fancyBoxDefaults, { minWidth: 300, minHeight: 60 })
			);
		}
	});
	
	$('#go_to_date').hover(function() {
		$(this).addClass('fc-state-hover');
	}, function() {
		$(this).removeClass('fc-state-hover');
	});
	

	// https://eonasdan.github.io/bootstrap-datetimepicker/
	$('#datetimepicker_hiddenDate').datetimepicker({
		locale: LANG.LANG_CURRENT,
		format: 'YYYY-MM-DD', //for calendar need english date format
		showTodayButton: true,
		showClose: true,
		allowInputToggle: true,
		widgetPositioning: {horizontal: 'auto', vertical: 'bottom'},
		//debug: true, //Will cause the date picker to stay open after a blur event.
		//icons: { date: "fa fa-calendar" },
		tooltips: {
			today: LANG.DATE_TODAY,
			clear: LANG.DATE_CLEAR,
			close: LANG.DATE_CLOSE,
			selectTime: LANG.DATE_SELECT_TIME,
			selectMonth: LANG.DATE_MONTH_SELECT,
			prevMonth: LANG.DATE_MONTH_PREV,
			nextMonth: LANG.DATE_MONTH_NEXT,
			selectYear: LANG.DATE_YEAR_SELECT,
			prevYear: LANG.DATE_YEAR_PREV,
			nextYear: LANG.DATE_YEAR_NEXT,
			selectDecade: LANG.DATE_DECADE_SELECT,
			prevDecade: LANG.DATE_DECADE_PREV,
			nextDecade: LANG.DATE_DECADE_NEXT,
			prevCentury: LANG.DATE_CENTURY_PREV,
			nextCentury: LANG.DATE_CENTURY_NEXT
		}
	});
	$("#datetimepicker_hiddenDate").on("dp.change", function (e) {
		$('#calendar').fullCalendar('gotoDate', e.date);
	});

	//if User not have any data --> message to click on Calendar
	$.get('php/ajax.php?i=forms_data&oper=cal_count&ID=' + V_ATHLETE + '&group_id=' + V_GROUP, function (data, result) {
		if (data == '0') { //user not have data in calendar
			$('#calendar').addClass('no_calendar_data');
		}
	});


	load_New_Info(true);
	setInterval(function(){ 
		load_New_Info(false);
	}, 300000); // 5 mins
	

	//tooltip
	$('[data-toggle="tooltip"]').tooltip({ trigger: "hover" }); //if not give hover it not close after click


	//load forms menu -> from form.menu.js
	load_Box_Forms_Menu();

}); //jQuery(function()



//##############################################################################
//form.menu--selection js -> go to form.menu.js
//##############################################################################


function hasAccess() {
	if ((V_GROUP in V_User_2_Groups) &&
		(V_User_2_Groups[V_GROUP].status == '1' || V_User_2_Groups[V_GROUP].status == '2'))
	{
		return true;
	}
	return false;
}


function hasWriteAccess() {
	if ((V_GROUP in V_User_2_Groups) &&
		(V_User_2_Groups[V_GROUP].status == '1'))
	{
		return true;
	}
	return false;
}


function getCalendarUrl() {
	return "php/ajax.php?i=forms_data&oper=cal&ID="+V_ATHLETE+"&group_id="+V_GROUP;
}


function Select__Athletes__Init() {
	//Athlete Select ###############################################################
	$("#Select_Athletes").chosen({
		width: '100%',
		no_results_text: LANG.NO_RESULTS,
		search_contains: true,
		disable_search_threshold: 10
	});

	Select_Athletes_enable();
	

	//Athlete Select Change
	$("#Select_Athletes").on('change', function () {
		$('.popover').popover('hide'); //hide all popovers
		$('#calendar').fullCalendar('removeEventSources');
		//$('#calendar').fullCalendar('removeEventSource', getCalendarUrl());

		V_ATHLETE = $(this).val();
		$.cookie('ATHLETE', V_ATHLETE, { path: '/' + V_REGmon_Folder, SameSite:'Lax' });
		
		//we not want anymore to change the options based on the selected user
		//options is always from the current logged in user - version > 1.911
		
		load_Box_Forms_Menu();

		$('.popover').popover('hide'); //hide all popovers

		$('#calendar').fullCalendar('addEventSource', getCalendarUrl());
		

		//User not have any data -- message to click on Calendar
		$.get('php/ajax.php?i=forms_data&oper=cal_count&ID=' + V_ATHLETE + '&group_id=' + V_GROUP, function (data, result) {
			if (data == '0') { //user not have data in calendar
				$('#calendar').addClass('no_calendar_data');
			} else {
				$('#calendar').removeClass('no_calendar_data');
			}
		});
	});
}


function Select_Athletes_enable() {
	$("#Athlete_Name_div").hide();
	$("#Select_Athletes_div").show();
}

function Select_Athletes_disable() {
	$("#Athlete_Name_div").show();
	$("#Select_Athletes_div").hide();
}


//############################################################
//User Profile ###############################################
function init_Profile_Edit() {
	$("#SPORTS_select").chosen({
		width: '100%',
		multiple: true,
		create_option: true,
		create_option_text: LANG.NEW_OPTION,
		no_results_text: LANG.NO_RESULTS,
		search_contains: true
	}).on('change', function () {
		$(this).parent('div').find('label.error').remove(); //remove required error if select something
	});

	$("#telephone").intlTelInput({
		initialCountry: 'de', 
		//Specify the countries to appear at the top of the list.
		preferredCountries: ['de', 'gb', 'us'], 
		separateDialCode: true
	});
	$("#telephone").inputFilter(function(value) { //Floating point (use . or , as decimal separator):
		return /^-?\d*[ ]?\d*$/.test(value);
	});	


	//strong password validation method
	$.validator.addMethod("strong_password", function (value, element) {
		if ($('#passwd').val() == '') return true; //only if password not empty
		return (/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])/.test(value));
	}, LANG.USERS.PASSWORD_WEAK);
	//$.validator.setDefaults({ ignore: ":hidden:not(.chosen-select)" }) //for all select having class .chosen-select


	//validate form
	$('form#profile_edit').validate({
		ignore: [":hidden:not(.chosen-select)"],
		rules: {
			uname: {
				required: true,
				minlength: 4,
				//onkeyup: false,
				//remote: "login/ajax.check_user_exist.php"
			},
			passwd: {
				//required: true,
				minlength: 8,
				strong_password: true,
			},
			pass_confirm: {
				//required: true,
				equalTo: "#passwd"
			}
		},
		messages: {
			pass_confirm: {
				equalTo: LANG.USERS.PASSWORD_CONFIRM,
				minlength: LANG.USERS.PASSWORD_MIN_LENGTH,
			},
			// uname: {
			// 	remote: LANG.WARN_USERNAME_EXIST
			// }
		},
		// errorPlacement: function(error, element) {
		// 	error.insertBefore( element );
		// }
	});

	
	$("button#profile_save").off('click').on('click', function () {
		  
		const inputs = $('form#profile_edit').find(':input');
		if (!inputs.valid() && $('label.error:visible').length != 0) {
			$(".fancybox-inner").animate({
				scrollTop: $('label.error:visible:first').offset().top - $(".fancybox-inner").offset().top + $(".fancybox-inner").scrollTop()
			}, "slow");
		}
		else {
			const uid 			= $('form#profile_edit input[name=uid]').val();
			const uname 		= $('form#profile_edit input[name=uname]').val();
			const passwd 		= $('form#profile_edit input[name=passwd]').val();
			const pass_confirm	= $('form#profile_edit input[name=pass_confirm]').val();

			//we not want check_user_exist to run on every keyup in validator
			$.ajax({
				url: "login/ajax.check_user_exist.php?uname=" + uname + '&uid=' + uid,
				success: function(data) {
					if (data == 'OK') {
						const post_data = {
							uname		: uname,
							passwd		: passwd,
							pass_confirm: pass_confirm,
							lastname	: $('form#profile_edit input[name=lastname]').val(),
							firstname	: $('form#profile_edit input[name=firstname]').val(),
							email		: $('form#profile_edit input[name=email]').val(),
							telephone	: $("form#profile_edit .iti__selected-dial-code").text()+' '+$("#telephone").val(),
							sport		: $('form#profile_edit select[name=sport]').val(),
							body_height	: $('form#profile_edit select[name=body_height]').val(),
							sex			: $('form#profile_edit input[name=sex]:checked').val(),
							birth_year	: $('form#profile_edit select[name=birth_year]').val(),
							birth_month	: $('form#profile_edit select[name=birth_month]').val(),
							birth_day	: $('form#profile_edit select[name=birth_day]').val(),
							dashboard	: $('form#profile_edit input[name=dashboard]:checked').val(),
							location_name: $('form#profile_edit input[name=location_name]').val(),
							group_id	: $('form#profile_edit input[name=group_id]').val(),
							group_name	: $('form#profile_edit input[name=group_name]').val(),
							level_id	: $('form#profile_edit input[name=level_id]').val(),
							profile		: $('form#profile_edit input[name=profile]').val()
						};
						$.post('login/ajax.profile_save.php', post_data, function(data, result){
							$("#profile_alerts").html(data);
						});
					}
					else {
						alert(data.replace(/<br>/g, '\n'));
					}
				}
			});
		}
	});
}


//############################################################
//Group Select/Actions #######################################
function group_icons_buttons() {
	//hide messages
	$("#group_buttons_message_in").hide();
	$("#group_buttons_message").hide();
	
	//add Icon
	let g_message = '';
	let g_class = '';
	let g_submit = 'group_user_request_access';

	if (V_GROUP in V_User_2_Groups) {
		let group_status = V_User_2_Groups[V_GROUP].status;
		 g_message = LANG.REQUEST.STATUS_UPDATED.replace('{DATE_TIME}', '<b>'+V_User_2_Groups[V_GROUP].modified+'</b>');
		if (group_status=='0') {
			g_class = 'G_no';
			g_submit = 'group_user_request_access_AN';
		} else if (group_status=='1') {
			g_class = 'G_yes';
			g_submit = 'group_user_cancel_access';
		} else if (group_status=='2') {
			g_class = 'G_yesStop';
			g_submit = 'group_user_cancel_access';
		} else if (group_status=='5') {
			g_class = 'G_leaveR';
			g_submit = 'group_user_request_access_AL_user';
		} else if (group_status=='15') {
			g_class = 'G_leaveA';
			g_submit = 'group_user_request_access_AL_groupadmin';
		} else if (group_status=='7' || group_status=='17' || group_status=='8' || group_status=='9') {
			g_class = 'G_wait';
			if (group_status=='7') g_class = 'G_waitLR';
			else if (group_status=='17') g_class = 'G_waitLA';
			else if (group_status=='8') g_class = 'G_waitN';
			else if (group_status=='9') g_class = 'G_wait';
			g_submit = 'group_user_cancel_request_user';
			g_message = LANG.REQUEST.WAS_SENT_AT.replace('{DATE_TIME}', '<b>'+V_User_2_Groups[V_GROUP].modified+'</b>');
		}
	}
	
	//selected group icon
	$("#Select_Group_chosen a span").removeClass('G_yes')
									.removeClass('G_yesStop')
									.removeClass('G_no')
									.removeClass('G_leaveR')
									.removeClass('G_leaveA')
									.removeClass('G_waitLR')
									.removeClass('G_waitLR')
									.removeClass('G_waitN')
									.removeClass('G_wait').addClass(g_class);
									
	$("#Select_Group_chosen a span").append(
		'<i title="' + $('#Select_Group option:selected').attr('data-status') + '">&nbsp;</i>'
	);
	

	//group_buttons
	if (g_submit != 'group_user_cancel_access') { //if not have access
		$("#views").hide();
		$("#view_radio").hide();
		$("#group_buttons").show();
		//$("#group_data").hide();
	}
	else { //if have access
		$("#views").show();
		$("#view_radio").show();
		$("#group_buttons").hide();
		//$("#group_data").show();
	}

	$(".submit_group").hide();
	$("#" + g_submit).show();
	

	//show messages
	if (g_submit == 'group_user_request_access') {
		//no message
	}
	else if (g_submit == 'group_user_cancel_access') {
		$("#group_buttons_message_in").html(g_message).show();
	}
	else {
		$("#group_buttons_message").html(g_message).show();
	}
} //end group_icons_buttons()



// Filter Numbers #####################################################
// Restricts input for each element in the set of matched elements to the given inputFilter.
(function($) {
	$.fn.inputFilter = function(inputFilter) {
		return this.on("input keydown keyup mousedown mouseup select contextmenu drop blur", function(e) {
			if (inputFilter(this.value)) {
				this.oldValue = this.value;
				this.oldSelectionStart = this.selectionStart;
				this.oldSelectionEnd = this.selectionEnd;
			} else if (this.hasOwnProperty("oldValue")) {
				this.value = this.oldValue;
				this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
			}
			if ((e.type == 'blur' || e.keyCode == 13) && this.value != '') $(this).trigger('change'); //bcz in EDGE we lose event after this point
		});
	};
}(jQuery));
// Filter Numbers #####################################################

