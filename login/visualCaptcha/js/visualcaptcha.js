( function( window, $ ) {
    $( function() {
	
        var captchaEl = $( '#login-captcha' ).visualCaptcha({
            imgPath: 'login/visualCaptcha/img/',
            captcha: {
                numberOfImages: 5, //def=5 hardcoded for security
				routes : {
					start : "/login/visualCaptcha/start",
					image : "/login/visualCaptcha/image",
					audio : "/login/visualCaptcha/audio"
				}
            },
			language: {
				accessibilityAlt: LANG.LOGIN.ACCESSIBILITY_ALT,
				accessibilityTitle: LANG.LOGIN.ACCESSIBILITY_TITLE,
				accessibilityDescription: LANG.LOGIN.ACCESSIBILITY_DESCRIPTION,
				explanation: LANG.LOGIN.CAPTCHA_EXPLANATION,
				refreshAlt: LANG.LOGIN.CAPTCHA_REFRESH_ALT,
				refreshTitle: LANG.LOGIN.CAPTCHA_REFRESH_TITLE
			}
		} );
        var captcha = captchaEl.data( 'captcha' );
    } );
}( window, jQuery ) );