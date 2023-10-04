"use strict";

// FancyBox Defaults


var confirm_Close_Form_iframe = function(){
	if (!confirm("\n\n" + LANG.LEAVE_PAGE_WARNING + "\n\n")) {
		//return false;	
	}
	else {
		window.frames[0].frameElement.contentWindow.stop_beforeunload_IE();

		setTimeout($.fancybox.close, 200);
	}
}


//http://fancyapps.com/fancybox/#docs
//if some difference use -> $.extend(fancyBoxDefaults,{maxWidth: 800})

var fancyBoxDefaults = {
	padding:0, //def:15
	margin:10, //def:20
	maxWidth:540,
	minWidth:250,
	modal:true,
	live:false,
	tpl:{error:'<p class="fancybox-error">'+LANG.PROBLEM_LOADING_PAGE+'</p>'},
	afterClose:function() {
		//location.reload();
		return;
	},
	beforeShow:function() {
		$('.not_display').show(); //show spacer
	},
	afterShow:function() {
		$('.fancybox-skin').append(
			//close button
			'<a title="' + LANG.CLOSE + '" class="fancybox-item fancybox-close" href="javascript:jQuery.fancybox.close();"></a>'
		);

		setTimeout(function(){
			//hide spacer //give space to link so that fit when is bold on hover
			$('.not_display').hide();
		}, 0);
		
		setTimeout(function () {
			//if new page has fancy_placeholder - hide it
			$("#fancy_placeholder").hide();
		}, 0);
	}
};


var fancyBoxDefaults_iframe = {
	padding:0, //def:15
	margin:10, //def:20
	modal:true,
	live:false,
	tpl:{error:'<p class="fancybox-error">'+LANG.PROBLEM_LOADING_PAGE+'</p>'},
	maxWidth:'100%',
	width:'100%',
	//fitToView : false,
	autoSize:false, height:'100%', //full height
	beforeLoad:function() {	
		loading.show();
		//open in new page if is iphone/ipad-safari
		if (is_iOS()) {
			window.location.href = this.href+'&is_iOS';
			return false;
		}
	},
	afterLoad:function() { loading.hide(); },
	afterClose:function() {
		//location.reload();
		return;
	},
	afterShow:function() {
		let close = '';
		//console.log(this.href);
		if (this.href.indexOf('form.php')!=-1) {
			close = 'javascript:confirm_Close_Form_iframe();';
			if (this.href.indexOf('view=true')!=-1) { //no confirm on View
				close = 'javascript:jQuery.fancybox.close();';
			}
		}
		else close = 'javascript:jQuery.fancybox.close();';
		$('.fancybox-skin').append('<a title="'+LANG.BACK+'" class="fancybox-item fancybox-back" href="'+close+'"></a>'); //close button
		$('.fancybox-skin').append('<a title="'+LANG.CLOSE+'" class="fancybox-item fancybox-close" href="'+close+'"></a>'); //close button
		
		//if new page has fancy_placeholder - hide it
		const _self = this;
		setTimeout(function () {
			const iframe = document.getElementById( $($(_self)[0].content).attr('id') );
			const element = iframe.contentWindow.document.getElementById("fancy_placeholder");
			$(element).hide();
		}, 0);
	}
};


//is iOS
function is_iOS() { //mono ta iphone/ipad 
	const iDevices = ['iPad Simulator', 'iPhone Simulator', 'iPod Simulator', 'iPad', 'iPhone', 'iPod'];
	if (!!navigator.platform) {
		while (iDevices.length) {
			if (navigator.platform === iDevices.pop()){ return true; }
		}
	}
	return false;
}
