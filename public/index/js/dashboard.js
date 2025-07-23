"use strict";

// Dashboard

jQuery(function() 
{
	initDashboard();

	//open_dashboard_onlogin
	$('#open_dashboard_onlogin').off('change').on('change', function(){
		const post_data = {
			ath_id: V_UID,
			dashboard: ($(this).is(':checked') ? '1' : '0')
		};
		$.post('php/ajax.php?i=users&oper=dash_onlogin', post_data, function(data, result){
		});
	});

	$('#dashboard_link, #dashboard_link2').on('click',function() { 
		if ($("#dashboard_div").css('width') == '0px')
			openDashboard();
		else closeDashboard();
	});

	if ($.cookie('DASH_ON_LOGIN') == '1') {
		openDashboard();
		$.removeCookie('DASH_ON_LOGIN', { path: '/'+V_REGmon_Folder, SameSite:'Lax' });
	}
	else if ($.cookie('DASHBOARD') == '1') {
		openDashboard();
	}
});


function openDashboard() {
	$.cookie('DASHBOARD', '1', { path: '/' + V_REGmon_Folder, SameSite:'Lax' });
	
	stickyNavBar.remove();

	$("#dashboard").css('width', $(window).width());
	$('body').css('overflow', 'hidden');
	$("#dashboard_div").css('width', '100%');
	$("#dashboard_div").css('height', '100%');

	setTimeout(function(){
		$("#dashboard").css('width', '');
	},450);
}

function closeDashboard() {
	$.cookie('DASHBOARD', '0', { path: '/' + V_REGmon_Folder, SameSite:'Lax' });
	
	$("#dashboard").css('width', $(window).width());
	$('body').css('overflow', '');
	$("#dashboard_div").css('width', '0px');
	$("#dashboard_div").css('height', '0px');

	setTimeout(function(){
		$("#dashboard").css('width', '');
	},450);
}

function color_field_dash(el) {
	$(el).colorpicker({ //https://mjolnic.com/bootstrap-colorpicker/
		//format:'hex',
		template: ''+
		  '<div class="colorpicker dropdown-menu">' +
			'<div class="colorpicker-selectors" style="float:right; margin:0 0 0 10px; width:80px;"></div>' +
			'<div class="colorpicker-saturation"><i><b></b></i></div>' +
			'<div class="colorpicker-hue"><i></i></div>' +
			'<div class="colorpicker-alpha"><i></i></div>' +
			'<div class="colorpicker-color"></div>' +
		  '</div>',
		customClass: 'dashboard_color', // custom class added to the colorpicker element
		colorSelectors: {
			'#ea921b': '#ea921b',
			'#d06504': '#d06504',
			'#176ba3': '#176ba3',
			'#1790be': '#1790be',
			'#02b11e': '#02b11e',
			'#008b01': '#008b01',
			'#aaaaaa': '#aaaaaa',
			'#bbbbbb': '#bbbbbb',
			'#cccccc': '#cccccc',
			'#000000': '#000000',
			'#993300': '#993300',
			'#333300': '#333300',
			'#000080': '#000080',
			'#333399': '#333399',
			'#333333': '#333333',
			'#800000': '#800000',
			'#ff6600': '#ff6600',
			'#808000': '#808000',
			'#008000': '#008000',
			'#008080': '#008080',
			'#0000ff': '#0000ff',
			'#666699': '#666699',
			'#808080': '#808080',
			'#ff0000': '#ff0000',
			'#ff9900': '#ff9900',
			'#99cc00': '#99cc00',
			'#339966': '#339966',
			'#33cccc': '#33cccc',
			'#3366ff': '#3366ff',
			'#800080': '#800080',
			'#999999': '#999999',
			'#ff00ff': '#ff00ff',
			'#ffcc00': '#ffcc00',
			'#ffff00': '#ffff00',
			'#00ff00': '#00ff00',
			'#00ffff': '#00ffff',
			'#00ccff': '#00ccff',
			'#993366': '#993366',
			'#c0c0c0': '#c0c0c0',
			'#ff99cc': '#ff99cc',
			'#ffcc99': '#ffcc99',
			'#ffff99': '#ffff99',
			'#ccffff': '#ccffff',
			'#99ccff': '#99ccff',
			'#ffffff': '#ffffff'
		}
	}).on('changeColor', function(e) {
		//console.log(e.color.toHex(), $(this).val());
		if ($(this).val() != '') {
			$(this).css('background', e.color); //e.color.toHex()
		} else {
			$(this).css('background', '');
		}
	});
}

function get_dash_data(get_data) {
	const id = $('#dash_id').val();
	const name = $('#dash_name').val();
	const type = $('#dash_type_select').val();
	let options = $('.dash_option:visible').val();
	const sort = $('#dash_sort').val();
	const color = $('#dash_color').val();
	let extra_opt_empty = false;
	
	if (type == 'calendar') {
		var date_cal = $('#dash_date_calendar').val();
		var date_cal_today = $('#dash_date_calendar_today').is(':checked');
		if (date_cal == '' && !date_cal_today) {
			extra_opt_empty = true;
		}
	}
	else if (type == 'forms_results' || type == 'results') {
		var date_type = $('#dash_date_type_select').val();
		if (date_type == '0') {
			var date_from = $('#dash_date_from').val();
			var date_to = $('#dash_date_to').val();
			if (date_from == '' || date_to == '') {
				extra_opt_empty = true;
			}

			if (LANG.LANG_CURRENT == 'de') {
				const german_date_regex = /^\d{2}\.\d{2}\.\d{4}$/;
				if (german_date_regex.test(date_from)) {
					var parts = date_from.split('.');
					date_from = parts[2] + '-' + parts[1] + '-' + parts[0];
				}
				if (german_date_regex.test(date_to)) {
					var parts = date_to.split('.');
					date_to = parts[2] + '-' + parts[1] + '-' + parts[0];
				}
			}
		}
		else if (date_type == '1') {
			var date_last = $('#dash_date_last').val();
			var date_range = $('#dash_date_range_select').val();
			if (date_last == '' || date_range == '') {
				extra_opt_empty = true;
			}
		}
		else if (date_type == '2') {
		}
	}

	//console.log(name, type, options, extra_opt_empty, (name=='' || type=='' || options=='' || extra_opt_empty));
	if (name=='' || type=='' || options=='' || extra_opt_empty) {
		$('#dashboard_save').attr('disabled', true);
	}
	else {
		$('#dashboard_save').attr('disabled', false);
	}

	if (type == 'calendar') {
		options += '__'+ (date_cal_today?'1':date_cal);
	}
	else if (type == 'forms_results' || type == 'results') {
		if (date_type == '0') {
			options += '__'+ date_type +'__'+ date_from +'__'+ date_to;
		}
		else if (date_type == '1') {
			options += '__'+ date_type +'__'+ date_last +'__'+ date_range;
		}
		else if (date_type == '2') {
			options += '__'+ date_type +'__0__0';
		}
	}
	if (get_data) {
		return [id, name, type, options, sort, color];
	}
}


function get_dash_content(dash) {
	const dash_id = dash[0];
	const dash_name = dash[1];
	const dash_type = dash[2];
	let dash_options = dash[3];
	let dash_sort = dash[4];
	const dash_color = dash[5];

	if (dash_id == '0') { //new
		let max = 0;
		V_DASHBOARD.forEach(function(ds){
			max = (ds[4] > max) ? ds[4] : max;
		});
		dash_sort = max + 1;
	}
	
	let dash_date_calendar = moment().format('YYYY-MM-DD');
	let dash_date_from = moment().add(-1 ,'week').add(1 ,'day').format('YYYY-MM-DD');
	let dash_date_to = moment().format('YYYY-MM-DD');
	let dash_date_last = '';
	let dash_date_range = '';
	let dash_date_type = '';


	
	if (dash_type=='calendar') {
		const dd = dash_options.split('__');
		dash_options = dd[0];
		if (dd[1]) {
			dash_date_calendar = dd[1];
		}
	}
	else if (dash_type=='forms_results') {
		const dd = (dash_options+'__0__2__0__0').split('__'); //avoid errors from old data
		dash_date_type = dd[2];
		if (dash_date_type == '0') {
			dash_date_from = dd[3];
			dash_date_to = dd[4];
		}
		else if (dash_date_type == '1') {
			dash_date_last = dd[3];
			dash_date_range = dd[4];
		}
		else if (dash_date_type == '2') {}
		else {
			dash_date_type = '0';
		}
	}
	else if (dash_type=='results') {
		const dd = (dash_options+'__2__0__0').split('__'); //avoid errors from old data
		dash_date_type = dd[1];
		if (dash_date_type == '0') {
			dash_date_from = dd[2];
			dash_date_to = dd[3];
		}
		else if (dash_date_type == '1') {
			dash_date_last = dd[2];
			dash_date_range = dd[3];
		}
		else if (dash_date_type == '2') {}
		else {
			dash_date_type = '2';
		}
	}

	if (LANG.LANG_CURRENT == 'de') {
		const us_date_regex = /^\d{4}\-\d{2}\-\d{2}$/;
		if (typeof dash_date_from === 'string' && us_date_regex.test(dash_date_from)) {
			var parts = dash_date_from.split('-');
			dash_date_from = parts[2] + '.' + parts[1] + '.' + parts[0];
		}
		if (typeof dash_date_from === 'string' && us_date_regex.test(dash_date_to)) {
			var parts = dash_date_to.split('-');
			dash_date_to = parts[2] + '.' + parts[1] + '.' + parts[0];
		}
	}
	
	//hide all popovers
	//$('.popover').popover('hide'); //not work - we need one more click
	$('.popover.in').each(function(i, el) {
		//$("span[aria-describedby="+el.id+"]").popover('toggle'); //not work
		$("[aria-describedby="+el.id+"]").trigger("click");
		if ($("[aria-describedby="+el.id+"]").attr('id') != 'add_dashboard') {
			$("[aria-describedby="+el.id+"]").hide(); //hide bcz we keep it visible if the popover is open
		}
	});

	$('.colorpicker').remove(); //remove all colorpicker
	

	const html = ''+
		'<div id="dash_pop_div" class="popover_nowrap" style="color:black; width:255px;">'+
			'<div id="dash_name_div" style="margin-bottom:5px;">'+
				'<label>'+LANG.DASH.NAME+' :&nbsp;</label>'+
				'<input type="hidden" id="dash_id" value="'+dash_id+'">'+
				'<input type="text" id="dash_name" value="'+dash_name+'" style="width:194px;">'+
			'</div>'+
			'<div id="dash_color_div">'+
				'<label>'+LANG.DASH.COLOR+' :&nbsp;</label>'+
				'<input id="dash_color" type="text" value="'+dash_color+'" class="cpD" style="display:inline-block; width:194px; color:white; text-shadow:black 1px 1px;"/>'+
			'</div>'+
			'<div id="dash_sort_div">'+
				'<label>'+LANG.DASH.POSITION+' :&nbsp;</label>'+
				'<input id="dash_sort" type="number" value="'+dash_sort+'" style="width:177px; margin-top:5px;">'+
			'</div>'+
			'<hr style="margin:8px 10px 10px 0;">'+
			'<div id="dash_type_select_div" style="margin-bottom:5px;">'+
				'<label>'+LANG.DASH.TYPE+' :&nbsp;</label>'+
				'<select id="dash_type_select" style="width:195px;">'+
					'<option value="">'+LANG.DASH.SELECT_TYPE+'</option>'+
					'<option value="calendar"'+(dash_type=='calendar'?' selected':'')+'>'+LANG.DASH.TYPE_CALENDAR+'</option>'+
					'<option value="link"'+(dash_type=='link'?' selected':'')+'>'+LANG.DASH.TYPE_OPTIONS+'</option>'+
					'<option value="form"'+(dash_type=='form'?' selected':'')+'>'+LANG.DASH.TYPE_FORMS+'</option>'+
					'<option value="forms_results"'+(dash_type=='forms_results'?' selected':'')+'>'+LANG.DASH.TYPE_RESULTS+'</option>'+
					'<option value="results"'+(dash_type=='results'?' selected':'')+'>'+LANG.DASH.TYPE_RESULTS_GRP+'</option>'+
					// (!V_IS_ATHLETE ? '<option value="results"'+(dash_type=='results'?' selected':'')+'>'+LANG.DASH.TYPE_RESULTS_GRP+'</option>':'')+
				'</select>'+
			'</div>'+
			
			//Calendar
			'<div id="dash_Calendar_select_div" style="margin-bottom:5px;'+(dash_type=='calendar'?'':' display:none;')+'">'+
				'<label>'+LANG.DASH.VIEW+' :&nbsp;</label>'+
				'<select id="dash_Calendar_select" class="dash_option" style="width:181px;">'+
					'<option value="">'+LANG.DASH.VIEW_SELECT+'</option>'+
					'<option value="month">'+LANG.DASH.VIEW_MONTH+'</option>'+
					'<option value="week">'+LANG.DASH.VIEW_WEEK+'</option>'+
					'<option value="day">'+LANG.DASH.VIEW_DAY+'</option>'+
				'</select>'+
			'</div>'+
			'<div id="dash_Calendar_date_div"'+(dash_type=='calendar'?'':' style="display:none;"')+'>'+
				'<label>'+LANG.DASH.DATE+' :&nbsp;</label>'+
				'<input id="dash_date_calendar" type="text" value="'+(dash_date_calendar!='1'?dash_date_calendar:'')+'" class="datepicker" style="width:189px;"'+(dash_date_calendar=='1'?' disabled':'')+'><br>'+
				'<label style="float:right; font-weight:normal; margin-right:20px;">'+
					'<input id="dash_date_calendar_today" type="checkbox" style="height:20px; vertical-align:bottom;"'+(dash_date_calendar=='1'?' checked':'')+'>&nbsp;'+LANG.DASH.DATE_CURRENT+
				'</label><br>'+
			'</div>'+
			
			//Options
			'<div id="dash_Options_select_div"'+(dash_type=='link'?'':' style="display:none;"')+'>'+
				'<label>'+LANG.DASH.OPTION+' :&nbsp;</label>'+
				'<select id="dash_Options_select" class="dash_option" style="width:187px;">'+
					'<option value="">'+LANG.DASH.OPTION_SELECT+'</option>'+
					'<option value="1">'+LANG.DASH.OPTION_1+'</option>'+ // OPTION_1: 'My Forms Selection'
					// '<option value="2">'+LANG.DASH.OPTION_2+'</option>'+ // OPTION_2: 'Data Management (Import, Export)'
					// '<option value="15">'+LANG.DASH.OPTION_15+'</option>'+ //window.location.href = 'import.php'; // OPTION_15: 'Import'
					'<option value="16">'+LANG.DASH.OPTION_16+'</option>'+ //window.location.href = 'export.php'; // OPTION_16: 'Export'
					((V_ADMIN || V_LOCATION_ADMIN || V_GROUP_ADMIN || V_GROUP_ADMIN_2) ? //only this Group Admins
						'<option value="3">'+LANG.DASH.OPTION_3+'</option>'+ // OPTION_3: 'Group Requests'
						'<option value="4">'+LANG.DASH.OPTION_4+'</option>' // OPTION_4: 'Group Users'
					: '')+
					//'<option value="5">'+LANG.DASH.OPTION_5+'</option>'+ // OPTION_5: 'Leave Group'
					((V_GROUP_TRAINER) ? //only Group Trainers
						'<option value="6">'+LANG.DASH.OPTION_6+'</option>'+ // OPTION_6: 'Request Access from Athlete(s)'
						'<option value="7">'+LANG.DASH.OPTION_7+'</option>' // OPTION_7: 'Form Permissions of Athlete(s)'
					: '')+
					(V_IS_ATHLETE ? //only Athletes
						'<option value="8">'+LANG.DASH.OPTION_8+'</option>'+ // OPTION_8: 'Requested Access by Trainers(s)'
						'<option value="9">'+LANG.DASH.OPTION_9+'</option>' // OPTION_9: 'Form Permissions for Trainer(s)'
					: '')+
					((V_ADMIN || V_LOCATION_ADMIN || V_GROUP_ADMIN || V_GROUP_ADMIN_2) ? //only this Group Admins
						'<option value="10">'+LANG.DASH.OPTION_10+'</option>'+ // OPTION_10: 'Group Forms Selection'
						'<option value="11">'+LANG.DASH.OPTION_11+'</option>'+ // OPTION_11: 'Forms'
						'<option value="12">'+LANG.DASH.OPTION_12+'</option>'+ // OPTION_12: 'Categories'
						'<option value="13">'+LANG.DASH.OPTION_13+'</option>' // OPTION_13: 'Lists (Sports, Dropdowns, etc.)'
					: '')+
					(V_IS_ADMIN || V_LOCATION_ADMIN ? //only admin and location admins
						'<option value="14">'+LANG.DASH.OPTION_14+'</option>' // OPTION_14: 'Groups of this Location'
					: '')+
				'</select>'+
			'</div>'+
			
			//Forms
			'<div id="dash_Forms_select_div"'+(dash_type=='form'?'':' style="display:none;"')+'>'+
				'<label>'+LANG.DASH.FORM+' :&nbsp;</label>'+
				'<select id="dash_Forms_select" class="dash_option" style="width:171px;">'+
					'<option value="" style="background:white;">'+LANG.DASH.FORM_SELECT+'</option>'+
					V_CATEGORIES_FORMS_OPTIONS+
				'</select><br>'+
				'<button type="button" id="dash_Forms_select_color" class="btn btn-default" style="float:right; margin:10px 25px 10px 10px; padding:0 10px; text-shadow:0 0 black;">'+LANG.DASH.FORM_COLOR_USE+'</button><br><br>'+
			'</div>'+
			
			//Results
			'<div id="dash_Results_select_div" style="margin-bottom:5px;'+(dash_type=='forms_results'?'':'display:none;')+'">'+
				'<label>'+LANG.DASH.TEMPLATE+' :&nbsp;</label>'+
				'<select id="dash_Results_select" class="dash_option" style="width:182px;">'+
					'<option value="">'+LANG.DASH.TEMPLATE_SELECT+'</option>'+
					V_FORMS_TEMPLATES_OPTIONS+
				'</select>'+
			'</div>'+
			
			//Results Group
			'<div id="dash_ResultsGroup_select_div" style="margin-bottom:5px;'+(dash_type=='results'?'':'display:none;')+'">'+
				'<label>'+LANG.DASH.TEMPLATE+' :&nbsp;</label>'+
				'<select id="dash_ResultsGroup_select" class="dash_option" style="width:182px;">'+
					'<option value="">'+LANG.DASH.TEMPLATE_SELECT+'</option>'+
					V_RESULTS_TEMPLATES_OPTIONS+
				'</select>'+
			'</div>'+
			
			//Results + Results Group
			'<div id="dash_date_type_select_div" style="margin-bottom:5px;'+((dash_type=='forms_results'||dash_type=='results')?'':' display:none;')+'">'+
				'<label>'+LANG.DASH.PERIOD+' :&nbsp;</label>'+
				'<select id="dash_date_type_select" style="width:173px;">'+
					'<option value="2" id="dash_date_type_2"'+(dash_date_type=='2'?' selected':'')+(dash_type=='results'?'':' style="display:none;"')+'>'+LANG.DASH.PERIOD_TEMPLATE+'</option>'+
					'<option value="0"'+(['1','2'].indexOf(dash_date_type)==-1?' selected':'')+'>'+LANG.DASH.PERIOD_STATIC+'</option>'+
					'<option value="1"'+(dash_date_type=='1'?' selected':'')+'>'+LANG.DASH.PERIOD_DYNAMIC+'</option>'+
				'</select>'+
			'</div>'+
			
			'<div id="dash_date_from_to_div" style="margin-bottom:5px;'+((['1','2'].indexOf(dash_date_type)==-1&&(dash_type=='forms_results'||dash_type=='results'))?'':' display:none;')+'">'+
				'<label>'+LANG.DASH.PERIOD_FROM+' :&nbsp;</label>'+
				'<input id="dash_date_from" type="text" value="'+dash_date_from+'" class="datepicker" style="width:80px;">'+
				'<label>&nbsp;&nbsp;&nbsp;'+LANG.DASH.PERIOD_TO+' :&nbsp;</label>'+
				'<input id="dash_date_to" type="text" value="'+dash_date_to+'" class="datepicker" style="width:81px;">'+
			'</div>'+
			'<div id="dash_date_range_div" style="margin-bottom:5px;'+((dash_date_type=='1'&&(dash_type=='forms_results'||dash_type=='results'))?'':' display:none;')+'">'+
				'<label>'+LANG.DASH.PERIOD_LAST+' :&nbsp;</label>'+
				'<input id="dash_date_last" type="number" min="1" value="'+dash_date_last+'" style="width:40px;">'+
				'<select id="dash_date_range_select" style="width:112px; margin-left:10px;">'+
					'<option value="min"'+(dash_date_range=='min'?' selected':'')+'>'+LANG.DASH.PERIOD_LAST_MINS+'</option>'+
					'<option value="hour"'+(dash_date_range=='hour'?' selected':'')+'>'+LANG.DASH.PERIOD_LAST_HOURS+'</option>'+
					'<option value="day"'+(dash_date_range=='day'?' selected':'')+'>'+LANG.DASH.PERIOD_LAST_DAYS+'</option>'+
					'<option value="week"'+(dash_date_range=='week'?' selected':'')+'>'+LANG.DASH.PERIOD_LAST_WEEKS+'</option>'+
					'<option value="month"'+(dash_date_range=='month'?' selected':'')+'>'+LANG.DASH.PERIOD_LAST_MONTHS+'</option>'+
					'<option value="year"'+(dash_date_range=='year'?' selected':'')+'>'+LANG.DASH.PERIOD_LAST_YEARS+'</option>'+
				'</select>'+
			'</div>'+
		'</div>';
	return html;
}


function get_popover_template(is_new) {
	return ''+
		'<div class="popover" role="tooltip">'+
			'<div class="arrow"></div>'+
			'<h3 class="popover-title"></h3>'+
			'<div class="popover-content"></div>'+
			'<div class="popover-footer">'+
				'<button type="button" id="dashboard_save" class="btn btn-primary"><i class="fa fa-floppy-o"></i> '+LANG.BUTTON_SAVE+' </button> &nbsp; '+
				(is_new ? '' :'<button type="button" id="dashboard_delete" class="btn btn-danger" title="'+LANG.BUTTON_DELETE+'"> &nbsp; <i class="fa fa-trash-o"></i> &nbsp; </button> &nbsp; ')+
				'<button type="button" id="dashboard_cancel" class="btn btn-default" title="'+LANG.BUTTON_CANCEL+'"> &nbsp; <i class="fa fa-times"></i> &nbsp; </button>'+
			'</div>'+
		'</div>';
}


function initDashboard() {
	V_DASHBOARD.sort(function(a, b) { //sort by sort, name
		if (a[4] == b[4]) {
			return (a[1] < b[1] ? -1 : (a[1] > b[1] ? 1 : 0)); //name-string
		}
		return a[4] > b[4] ? 1 : -1; //sort-num
	});

	let html = '';

	//make links
	V_DASHBOARD.forEach(function(dash){
		let name = dash[1];
		const type = dash[2];
		const option = dash[3];

		if (type == 'calendar') {
			name = '<i class="fa fa-calendar"></i><br>' + name;
		}
		else if (type == 'link') {
			name = '<i class="fa fa-gear"></i><br>' + name;
		}
		else if (type == 'form') {
			if (option == 'Note_n') {
				name = '<i class="fa fa-commenting"></i><br>' + name;
			} else {
				name = '<i class="fa fa-file-text-o"></i><br>' + name;
			}
		}
		else if (type == 'forms_results') {
			name = '<i class="fa fa-bar-chart"></i><br>' + name;
		}
		else if (type == 'results') {
			name = '<i class="fa fa-bar-chart"></i><i class="fa fa-users"></i><br>' + name;
		}
		
		//the link html
		html += ''+
			'<li id="dash_link_'+dash[0]+'" style="background:'+dash[5]+'">'+
				'<a class="dashboard_links">'+name+'</a>'+
				'<span class="das_set fa fa-pencil-square-o" data-dash="'+dash[0]+'|'+dash[1]+'|'+dash[2]+'|'+dash[3]+'|'+dash[4]+'|'+dash[5]+'" data-dash_options="'+dash[3]+'" data-toggle="popover" title="'+dash[1]+'" style="display:none;"></span>'+
			'</li>';
	});

	$('#dashboard').html('<ul>'+html+'</ul>');
	

	$('.dashboard_links').off('click').on('click', function() {
		loading.show();

		const dash = $(this).next().attr('data-dash').split('|');
		const dash_type = dash[2];
		const dash_options = dash[3];
		let page = '';
		let option = '';

		if (dash_type == 'calendar') {
			$('#view_calendar').trigger("click");
			const dd = dash_options.split('__');
			const range = dd[0];
			const date = dd[1];
				 if (range == 'month') 	$('.fc-month-button').trigger("click");
			else if (range == 'week') 	$('.fc-agendaWeek-button').trigger("click");
			else if (range == 'day') 	$('.fc-agendaDay-button').trigger("click");
			
			if (date == '1') { //today
				$('#calendar').fullCalendar('gotoDate', moment().format('YYYY-MM-DD'));
			}
			else {
				$('#calendar').fullCalendar('gotoDate', moment(date, 'YYYY-MM-DD').format('YYYY-MM-DD')); //date
			}


			V_ANIMATE_RUN = true;
			$('html, body').animate({
				scrollTop: $('#views').offset().top
			}, 1000, function() {
				loading.hide();
				setTimeout(function(){
					V_ANIMATE_RUN = false;
				}, 100);
			});
		}
		else if (dash_type == 'link') {
			$('#view_options').trigger("click");

			let el = '';
				 if (dash_options == '1') el = '#C_Athlete_Forms_Select_link';
			else if (dash_options == '2') el = '#C_Import_Export_Data_link';
			else if (dash_options == '3') el = '#C_Group_Requests_link'; 
			else if (dash_options == '4') el = '#C_Group_Users_link'; 
			//else if (dash_options == '5') el = '#C_Group_Leave_link';
			else if (dash_options == '6') el = '#C_Request_Access_From_Athletes_link';
			else if (dash_options == '7') el = '#C_Trainer_Access_To_Athletes_Forms_link';
			else if (dash_options == '8') el = '#C_Requests_From_Trainers_link'; 
			else if (dash_options == '9') el = '#C_Athlete_Give_Forms_Access_To_Trainers_link';
			else if (dash_options == '10') el = '#C_Group_Forms_Select_link';
			else if (dash_options == '11') el = '#C_Forms_link';
			else if (dash_options == '12') el = '#C_Categories_link';
			else if (dash_options == '13') el = '#C_Sports_Dropdowns_link';
			else if (dash_options == '14') el = '#C_Location_Groups_link';
			else if (dash_options == '15') {
				loading.show();
				window.location.href = 'import.php';
				return false;
			}
			else if (dash_options == '16') {
				loading.show();
				window.location.href = 'export.php';
				return false;
			}

			//if closed -> click
			if ($(el).hasClass('collapsed')) $(el).trigger("click");


			V_ANIMATE_RUN = true;
			$('html, body').animate({
				scrollTop: $(el).offset().top
			}, 1000, function() {
				setTimeout(function(){
					V_ANIMATE_RUN = false;
					loading.hide();
				}, 100);
			});
		}
		else if (dash_type == 'form') {
			const dd = dash_options.split('_');
			page = 'form.php';
			option = '?id=' + dd[1] + '&cat_id=' + dd[0];

			V_SELECTED_DATE = moment().format("YYYY-MM-DD HH:mm:ss");

			$.cookie('SELECTED_DATE', V_SELECTED_DATE, { path: '/' + V_REGmon_Folder, SameSite:'Lax' });
			
			if (dd[0] == 'Note') {
				$.fancybox($("#create_Note"), $.extend({},fancyBoxDefaults,{minWidth: 300}));
				init_Notes_Create('Dash_Button');
				loading.hide();
			}
			else {
				$.fancybox($.extend({},fancyBoxDefaults_iframe, {type:'iframe', href: page+''+option}));
			}
		}
		else if (dash_type == 'forms_results') {
			page = 'forms_results.php';
			option = '?vor=' + dash_options;
			loading.show();
			window.location.href = page + option;
		}
		else if (dash_type == 'results') {
			page = 'results.php';
			option = '?vor=' + dash_options;
			loading.show();
			window.location.href = page + option;
		}
		closeDashboard();
	});
	

	$('#dashboard.navv li').hover(function() {
		$(this).find('span.das_set').show();
	}, function(){
		let el = $(this).find('span.das_set');
		if (!el.next().hasClass('popover')) el.hide(); //not hide if popover exist
	});
	

	//new Dashboard
	$('#add_dashboard, #add_dashboard_notext').popover({
		placement: 'bottom',
		html: true,
		template: get_popover_template(true),
		content: function () {
			return get_dash_content([0, '', '', '__', 0, '#cccccc']);
		}
	});


	//edit Dashboard
	$('.das_set').popover({
		placement: 'left',
		html: true,
		template: get_popover_template(false),
		content: function () {
			let dash = $(this).attr('data-dash').split('|');
			return get_dash_content(dash);
		}
	});
	

	//on open init
	$('.das_set, #add_dashboard, #add_dashboard_notext').off('shown.bs.popover').on('shown.bs.popover', function () {
		const popup_link = this;
		color_field_dash('.cpD');
		$('#dash_color').trigger('change'); //to enable the background
		
		const dash_type = $(this).attr('data-dash').split('|')[2];
		let dash_options = $(this).attr('data-dash_options');
		const dd = dash_options.split('__');

		if (dash_type == 'forms_results') {
			dash_options = dd[0]+'__'+dd[1];
		}
		else if (dash_type == 'results') {
			dash_options = dd[0];
		}
		else if (dash_type=='calendar') { //(['month','week','day'].indexOf(dd[0]) != -1) {
			dash_options = dd[0];
		}

		//selected the right option
		$("#dash_pop_div option[value='" + dash_options + "']").prop("selected", true);
		
		setTimeout(function(){
			$('#dash_Forms_select').trigger('change'); //to enable the background
		}, 100);
		
		$("#dash_date_calendar, #dash_date_from, #dash_date_to").datepicker({
			changeMonth:true,
			changeYear:true,
			dateFormat: LANG.DATEPICKER.DATE,
			firstDay: 1,
			showOtherMonths: true,
			selectOtherMonths: true
		});
		
		get_dash_data(false); //init 
		
		$('#dash_Calendar_select_div, '+
			'#dash_Calendar_date_div, '+
			'#dash_Options_select_div, '+
			'#dash_Forms_select_div, '+
			'#dash_Results_select_div, '+
			'#dash_ResultsGroup_select_div, '+
			'#dash_Calendar_select, '+
			'#dash_date_calendar, '+
			'#dash_date_type_select_div, '+
			'#dash_date_from_to_div, '+
			'#dash_date_range_div, '+
			'#dash_date_from, '+
			'#dash_date_to'
		).off('change').on('change', function () {
			get_dash_data(false);
		});
		
		$('#dash_name').off('change').on('change', function () {
			get_dash_data(false);
		});

		$('#dash_date_calendar_today').off('change').on('change', function () {
			if ($(this).is(':checked')) {
				$('#dash_date_calendar').attr('disabled', true);
			} else {
				$('#dash_date_calendar').attr('disabled', false);
			}

			get_dash_data(false);
		});

		$('#dash_Forms_select').off('change').on('change', function () {
			if ($(this).val() == '') {
				$(this).css('background', 'white');
			}
			else if ($(this).val() == 'Note_n') {
				$(this).css('background', $(':selected', $(this)).css('background-color'));
			}
			else {
				$(this).css('background', $(':selected', $(this)).parent().css('background-color'));
			}

			get_dash_data(false);
			
		});


		$('#dash_type_select').off('change').on('change', function () {
			$('#dash_Calendar_select_div, '+
				'#dash_Calendar_date_div, '+
				'#dash_Options_select_div, '+
				'#dash_Forms_select_div, '+
				'#dash_Results_select_div, '+
				'#dash_ResultsGroup_select_div, '+
				'#dash_date_type_select_div, '+
				'#dash_date_from_to_div, '+
				'#dash_date_range_div'
			).hide(); //hide all
			

			if ($(this).val() == 'calendar') {
				$('#dash_Calendar_select_div').show();
				$('#dash_Calendar_date_div').show();
			}
			else if ($(this).val() == 'link') {
				$('#dash_Options_select_div').show();
			}
			else if ($(this).val() == 'form') {
				$('#dash_Forms_select_div').show();
			}
			else if ($(this).val() == 'forms_results') {
				$('#dash_Results_select_div').show();
				$('#dash_date_type_select_div').show();
				$('#dash_date_type_2').hide();
				$('#dash_date_type_select').val('0').trigger('change');
			}
			else if ($(this).val() == 'results') {
				$('#dash_ResultsGroup_select_div').show();
				$('#dash_date_type_select_div').show();
				$('#dash_date_type_2').show();
				$('#dash_date_type_select').val('0').trigger('change');
			}

			get_dash_data(false);
		});


		$('#dash_date_type_select').off('change').on('change', function () {
			$('#dash_date_from_to_div, #dash_date_range_div').hide(); //hide all

			if ($(this).val() == '1') {
				$('#dash_date_range_div').show();
			}
			else if ($(this).val() == '2' || $(this).val() == '') {
			}
			else {
				$('#dash_date_from_to_div').show();
			}

			get_dash_data(false);
		});


		$('#dash_Forms_select_color').off('click').on('click',function() {
			$('#dash_color').val( $('#dash_Forms_select').css('background-color') ).trigger('change');
		});
		

		$('#dashboard_cancel').off('click').on('click',function(e){
			$(popup_link).trigger("click");
			if ($(popup_link).attr('id') != 'add_dashboard') $(popup_link).hide(); //hide bcz we keep icon visible if the popover is open
		});


		$('#dashboard_delete').off('click').on('click',function(){
			const dash = get_dash_data(true);
			const dash_id = dash[0];
			const post_data = {
				group_id: V_GROUP,
				ath_id: V_ATHLETE,
				dash_id: dash_id
			};
			$.post('index/ajax.dashboard_delete.php', post_data, function(data, result){
				//update links
				$('#dashboard_script').html(data);
				initDashboard();
			});
			
			$(popup_link).trigger("click");
		});


		$('#dashboard_save').off('click').on('click',function(){
			const dash = get_dash_data(true);
			const dash_id = dash[0];
			const dash_name = dash[1];
			const dash_type = dash[2];
			const dash_options = dash[3];
			const dash_sort = dash[4];
			const dash_color = dash[5];
			const data = {
				group_id: V_GROUP,
				ath_id: V_ATHLETE,
				dash_id: dash_id,
				name: dash_name,
				type: dash_type,
				options: dash_options,
				sort: dash_sort,
				color: dash_color
			};
			$.post('index/ajax.dashboard_save.php', data, function(data, result){
				//update link
				/*$('#dash_link_'+dash_id+' .das_set').attr('data-dash', dash.join('|'));
				$('#dash_link_'+dash_id+' .das_set').attr('data-dash_options', dash_options);
				$('#dash_link_'+dash_id+' .das_set').attr('data-original-title', dash_name);
				$('#dash_link_'+dash_id+' a').html(dash_name);
				$('#dash_link_'+dash_id).css('background', dash_color);*/

				$('#dashboard_script').html(data);

				initDashboard();
			});
			$(popup_link).trigger("click");
		});
	});

	//adding title bcz popover remove it
	$('.das_set').attr('title', LANG.DASH.DETAILS);
}
