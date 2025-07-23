
function init_HTML_Radio_Check_Buttons__On_Off(element_id) {
	$('#' + element_id + ' label').on('click', function () {
		if ($(this).hasClass('disabled')) {
			return false;
		}
		
		const value = $('#' + element_id + ' input:checked').val();
		if (value == '1' && $(this).find('input').val() == '1') {
			return false;
		}
		if (value == '0' && $(this).find('input').val() == '0') {
			return false;
		}

		$('#' + element_id + ' label').eq(0).toggleClass('btn-default btn-success');
		$('#' + element_id + ' label').eq(1).toggleClass('btn-default btn-danger');
	});
}
