
//fix bfcach problem when browser back button is pressed and page not load properly
window.addEventListener( "pageshow", function (event) {
	const historyTraversal = event.persisted || (typeof window.performance != "undefined" && window.performance.navigation.type === 2);
	if (historyTraversal) {
		 // page was restored from the bfcach
	  window.location.reload(); //reload page
	}
});

jQuery(function ()
{
	loading.hide();
	
	$(".new_reg").on('click',function() {
		loading.show();
		window.location.href = 'register.php';
	});

	$(".log_in").on('click',function() {
		//loading.show();
	});
});
