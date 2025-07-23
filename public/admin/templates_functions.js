"use strict";

// Templates Functions

var Perms_elems = ['#Global', '#Location', '#Group', '#Trainer'];

function init_Permissions() {
	$(Perms_elems).each(function(i,el){
		checkViewFrom(el+'View', el+'Edit'); //init
		checkEditFrom(el+'Edit'); //init
		
		$(el+'View').on('change', function(){ 
			let gview = $(this).is(':checked');
			if(el == '#Trainer' && gview) {
				checkViewFrom(el+'View', el+'Edit');
				checkEditFrom(el+'Edit');
			}
			else {
				$(Perms_elems).each(function(i2,el2){
					if(gview) {
						if(i2 > i) { //check after el -if unchecked
							if (!$(el2+'View').is(':checked')) $(el2+'View').trigger("click");
						}
					} else {
						if(i2 <= i) { //uncheck before el -if checked
							if ($(el2+'View').is(':checked')) $(el2+'View').trigger("click");
						} 
					}
				});
			}
			checkViewFrom(el+'View', el+'Edit');
		});
		$(el+'Edit').on('change', function(){ 
			let gedit = $(this).is(':checked');
			$(Perms_elems).each(function(i2,el2){
				if(gedit) {
					if(i2 > i) { //check after el -if unchecked
						if (!$(el2+'Edit').is(':checked')) $(el2+'Edit').trigger("click");
					}
				} else {
					if(i2 <= i) { //uncheck before el -if checked
						if ($(el2+'Edit').is(':checked')) $(el2+'Edit').trigger("click");
					}
				}
			});
			checkEditFrom(el+'Edit');
		});
	});
	$('#Private').on('change', function(){ checkPrivate('#Private'); });
	checkPrivate('#Private');
}

function checkViewFrom(t_view, t_change) {
	if($(t_view).is(':checked')) {
		$(t_view).parent().addClass('checked');
		$(t_change).prop('disabled', false);
		$(t_change).parent().removeClass('disabled');
	} else {
		$(t_view).parent().removeClass('checked');
		$(t_change).prop('disabled', true);
		$(t_change).parent().addClass('disabled');
		
		$(t_change).prop('checked', false);
		$(t_change).parent().removeClass('checked');
	}
}

function checkEditFrom(t_change) {
	if($(t_change).is(':checked')) {
		$(t_change).parent().addClass('checked');
	} else {
		$(t_change).parent().removeClass('checked');
	}
}

function checkPrivate(t_view) {
	if($(t_view).is(':checked')) {
		$(t_view).parent().addClass('checked');
		$(Perms_elems).each(function(i,el){
			//uncheck
			if ($(el+'Edit').is(':checked')) $(el+'Edit').trigger("click");
			if ($(el+'View').is(':checked')) $(el+'View').trigger("click");
			
			$(el+'View').prop('disabled', true).trigger('change');
			$(el+'Edit').prop('disabled', true).trigger('change');
			$(el+'View').parent().addClass('private');
			$(el+'Edit').parent().addClass('private');
		});
	} else {
		$(t_view).parent().removeClass('checked');
		$(Perms_elems).each(function(i,el){
			$(el+'View').prop('disabled', false).trigger('change');
			//$(el+'Edit').prop('disabled', true); //no need
			$(el+'View').parent().removeClass('private');
			$(el+'Edit').parent().removeClass('private');
		});
	}
}
