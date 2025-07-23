if (!Production_Mode) "use strict"; //remove on production so "const" works on iOS Safari


jQuery(function() {
	
	//init sidebar buttons
	$('#ssb-btn-1').on('click',function() { animateToDiv('#Edit_Date_link', false); });
	$('#ssb-btn-2').on('click',function() { animateToDiv('#C_Athletes_Data_link', '#C_Athletes_Data'); });
	$('#ssb-btn-3').on('click',function() { animateToDiv('#C_Diagramm_link', '#C_Diagramm'); }); //4
	$('#ssb-btn-6').on('click',function() { Chart__Update(); });
	
	
	//for iframe we want it open so it is bigger when the fancybox opens
	if (!V_IS_IFRAME) {
		// close diagram panel --diagram start opened (bcz of a problem with start closed)
		$('#accordion_diagram .collapse.in').collapse('hide');
	}
	

	//Select Group init
	$("#Select__Groups").chosen({
		width: '100%',
		no_results_text: LANG.NO_RESULTS,
		search_contains: true,
		disable_search_threshold: 10
	});

	//Select Group on change
	$("#Select__Groups").on('change', function () {
		$("#loading").show();
		//reload page with new group
		window.location.href = 'forms_results.php?group_id=' + $(this).val() + '&athlete_id=' + V_UID;
	});

}); //end jQuery(function() {

