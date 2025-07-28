jQuery(function($) {

	//SCROLL TO TOP
	function animateToTop(top) {
		if (top != 0) {
			top = 0;
			if (window.innerWidth >= 340) top = 60;
			if (window.innerWidth >= 550) top = 100;
			if (window.innerWidth >= 770) top = 115;
		}
		
		$("html, body").animate({ scrollTop: top }, "slow");
	}

	$(window).on('scroll',function() {
		if($(this).scrollTop() != 0) {
			$('#toTop').fadeIn();	
		} else {
			$('#toTop').fadeOut();
		}
	});
 
	$('#toTop').on('click',function() {
		animateToTop(0);
	});	


	//strong password validation method
	$.validator.addMethod("strong_password", function (value, element) {
		return (/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])/.test(value));
	}, LANG.USERS.PASSWORD_WEAK);

	
	// WIZARD  ###############################################
	// Basic wizard with validation
	$('form#wrapped').attr('action', 'login/registration.php');
	$('form#wrapped').wizard({
		stepsWrapper: "#wrapped",
		enableSubmit:true,
		submit: "#submit",
		beforeSelect: function( event, state ) {
			if (!state.isMovingForward) {
				animateToTop();
				return true;
			}

			const inputs = $(this).wizard('state').step.find(':input');
			
			if (!inputs.valid() && $('label.error:visible').length != 0) {
				$("html, body").animate({ scrollTop: $('label.error:visible').offset().top-50 }, "slow");
			}
			else {
				animateToTop();
				//$('form#wrapped').trigger('submit');
				//$('#submit').prop('disabled', false);
				$("#loading").show();

				setTimeout(function () {
					$('#submit').trigger("click");
				}, 1000);
			}
			return !inputs.length || !!inputs.valid();
		}
	});

	$('form#wrapped').validate({
		ignore: [":hidden:not(.chosen-select)"],
		//we not want check_user_exist to run on keyup but we cannot have onkeyup:false into field rules
		//onkeyup: false,
		onkeyup: function(element) { //workaround for onkeyup:false into field rules
			const element_id = jQuery(element).attr('id');
			if (this.settings.rules[element_id] && this.settings.rules[element_id].onkeyup !== false) {
				jQuery.validator.defaults.onkeyup.apply(this, arguments);
			}
		},
		rules: {
			uname: {
				required: true,
				minlength: 4,
				onkeyup: false,
				remote: "login/ajax.check_user_exist.php"
			},
			passwd: {
				required: true,
				minlength: 8,
				strong_password: true
			},
			pass_confirm: {
				required: true,
				equalTo: "#passwd"
			},
			private_key: {
				onkeyup: false,
				//only if private is selected
				required: function () {
                	return $('#Select_Group').val() == 'Private';
				},
				remote: "login/ajax.check_private_key.php"
			}
		},
		messages: {
			pass_confirm: {
				equalTo: LANG.USERS.PASSWORD_CONFIRM,
				minlength: LANG.USERS.PASSWORD_MIN_LENGTH,
			},
			uname: {
				remote: LANG.WARN_USERNAME_EXIST.replace(/<br>/g, ' ')
			},
			private_key: {
				remote: LANG.GROUPS.PRIVATE_KEY_ERROR
			}
		},
		errorPlacement: function(error, element) {
			if (element.is(':radio') || element.is(":checkbox")) {
				error.insertBefore( element.parent().next() );
			}
			else if (element.is('select')) {
				error.insertBefore(element.parent());
			}
			else { 
				if (element.attr('id') == 'private_key') {
					error.insertBefore(element.parent());
				} else {
					error.insertBefore( element );
				}
			}
		}
	});


	// Other Fields  ######################################

	//Check and radio input styles
	$('input.check_radio').iCheck({
		checkboxClass: 'icheckbox_square-aero',
		radioClass: 'iradio_square-aero'
	});
	
	//SPORTS select
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

	//telephone
	$("#telephone").intlTelInput({
		initialCountry: 'de', 
		//Specify the countries to appear at the top of the list.
		preferredCountries: ['de', 'gb', 'us'], 
		separateDialCode: true
	});
	$("#telephone").inputFilter(function(value) { //Floating point (use . or , as decimal separator):
		return /^-?\d*[ ]?\d*$/.test(value);
	});	
	$("#telephone").on('countrychange', function(a){
		$('#countryCode').val( $('.iti__selected-dial-code').text() );
	});

	//Group Select
	$("#Select_Group").on('change', function () {
		if ($(this).val() == 'Private') {
			$('#Select_Group').parent().hide();
			$('#private_group').show();
		}
	});
	$("#private_close").on('click',function() {
		$('#private_group').hide();
		$('#Select_Group').parent().show();
		$("#Select_Group").val('');
	});
	
});


// Filter Numbers #########################################################
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
