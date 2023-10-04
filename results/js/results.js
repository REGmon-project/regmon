if (!Production_Mode) "use strict"; //remove on production so "const" works on iOS Safari


jQuery(function() {
	
	//init sidebar buttons
	$('#ssb-btn-1').on('click',function() { animateToDiv('#C_Templates_link', '#C_Templates'); });
	$('#ssb-btn-2').on('click',function() { animateToDiv('#C_Options_link', '#C_Options'); });
	$('#ssb-btn-3').on('click',function() { animateToDiv('#C_Athletes_Data_link', '#C_Athletes_Data'); });
	$('#ssb-btn-4').on('click',function() { animateToDiv('#C_Intervals_Data_link', '#C_Intervals_Data'); });
	$('#ssb-btn-5').on('click',function() { animateToDiv('#C_Diagramm_link', '#C_Diagramm'); });
	$('#ssb-btn-6').on('click',function() { Chart__Update(); });
	

	// close diagram panel --diagram start opened (bcz of a problem with start closed)
	$('#C_Diagramm.collapse.in').collapse('hide');
	

	//submit new DATE
	$("#Button__DATE__Submit").on('click', function () {
		loading.show();
		DATE__Submit();
	});


	//reset All Selections
	$('#resetSelect_div').confirmation({
		href: 'javascript:void(0)',
		title: LANG.ARE_YOU_SURE, placement: 'top',
		btnOkLabel: LANG.YES, btnOkClass: 'btn btn-sm btn-success mr10',
		btnCancelLabel: LANG.NO, btnCancelClass: 'btn btn-sm btn-danger',
		onConfirm: function(e, button) {
			loading.show();
			Selected__All__Reset();
		}
	});
	
	
	//########################################################
	// TEMPLATES #############################################

	//Save Template
	$('#Results_Template__Save').on('click',function() { 
		Results_Template__Save(); 
	});
	

	//Load selected Results_Template
	$('#Results_Template__Load').on('click', function() {
		Debug1('1.JS.Click. Results_Template__Load click');

		const save_id = $('#Select__Results_Templates').val();
		const save_name = $('#Select__Results_Templates option:selected').text();
		if (!save_id) {
			return;
		}

		loading.show();
		setTimeout(function(){ //for loading
			Results_Template__Load(save_id, save_name);
		}, 100);
		
		//show Chart Loading...
		//V_Chart.showLoading();
		//change NoData to Loading
		Highcharts.setOptions({
			lang: { noData: LANG.DIAGRAM.LOADING },
			noData: { style: { fontSize: '50px' } }
		});
		

		//open diagram panel
		setTimeout(function () {
			$('#C_Diagramm.collapse').collapse('show');
		}, 500);

		//if data panel is open -> close it
		if ($('#C_Athletes_Data.collapse').hasClass('in')) {
			$('#C_Athletes_Data.collapse').collapse('hide');
		}
		//if intervals panel is open -> close it
		if ($('#C_Intervals_Data.collapse').hasClass('in')) {
			$('#C_Intervals_Data.collapse').collapse('hide');
		}
		//if axis panel is open -> close it
		if ($('#C_Axis.collapse').hasClass('in')) {
			$('#C_Axis.collapse').collapse('hide');
		}

		//scroll to Diagram
		setTimeout(function(){ //delay for collapse to finish 
			$("html,body").animate({
				scrollTop: $('#accordion_diagram').offset().top - 3
			}, "slow");
		}, 700);
	});
	

	//Add selected Results_Template to Dashboard
	$('#Results_Template_2_Dashboard').on('click', function () {
		const save_id = $('#Select__Results_Templates').val();
		const save_name = $('#Select__Results_Templates option:selected').text();
		if (!save_id) {
			return;
		}

		const post_data = {
			group_id: V_GROUP,
			ath_id: V_UID,
			dash_id: 0,
			name: LANG.RESULTS.DASH_RESULTS_TEMPLATE_NAME + save_name,
			type: 'results',
			options: save_id + '__2__0__0',
			sort: 'max',
			color: '#cccccc'
		};
		Debug1('1.Template.Save.Dash. Results_Template_2_Dashboard', [post_data]);
		
		$.post('index/ajax.dashboard_save.php', post_data, function(data, result){
			if (data != 'ERROR') {
				parent.Swal({
					type: 'success',
					title: LANG.RESULTS.TEMPLATE_ADDED_TO_DASH.replace('{TEMPLATE_NAME}', save_name),
					showConfirmButton: false,
					timer: 2000
				});
			} else {
				parent.Swal({
					type: 'error',
					title: LANG.ERROR+'!',
					showConfirmButton: false,
					timer: 2000
				});
			}
		});
	});
	

	//Delete selected Results Template
	$('#Results_Template_Delete').confirmation({
		href: 'javascript:void(0)',
		title: function () {
			const save_name = $('#Select__Results_Templates option:selected').text();
			return LANG.RESULTS.TEMPLATE_CONFIRM_DELETE.replace('{TEMPLATE_NAME}', save_name)
		}, 
		placement: 'top',
		btnOkLabel: LANG.YES, btnOkClass: 'btn btn-sm btn-success mr10',
		btnCancelLabel: LANG.NO, btnCancelClass: 'btn btn-sm btn-danger',
		onConfirm: function(e, button) {
			const id = $('#Select__Results_Templates').val();
			if (!id) {
				return;
			}
			
			const post_data = {
				group_id		: V_Group_id,
				athlete_id		: V_Athlete_id,
				template_type	: 'results',
				id				: id
			};
			Debug1('1.Template.Delete. Results_Template_Delete confirmation', [post_data]);

			$.post('results/ajax.template_delete.php', post_data, function(data, result) {
				if (data != 'ERROR' && data.indexOf('.....') == -1) {
					const save_name = $('#Select__Results_Templates option:selected').text();
					parent.Swal({
						type: 'success',
						title: LANG.RESULTS.TEMPLATE_DELETE_SUCCESS.replace('{TEMPLATE_NAME}', save_name),
						showConfirmButton: false,
						timer: 2000
					});

					//we get new V_RESULTS_TEMPLATES[form_id] in data
					$('#add_data').html(data);

					//init Results_Templates Select
					Select__Results_Templates__Init(false);
				}
				else {
					parent.Swal({
						type: 'error',
						title: data,
						showConfirmButton: false,
						timer: 2000
					});
				}
			});
		}
	});

	// TEMPLATES #############################################
	//########################################################

}); //end jQuery(function() {
