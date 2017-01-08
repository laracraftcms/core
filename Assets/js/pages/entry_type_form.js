$(document).ready(function () {
	$('#has_title_field').change(function(){
		if ($(this).is(':checked')){
			$('.has_title_field_on').prop('hidden', false);
			$('.has_title_field_off').prop('hidden', true);
		} else {
			$('.has_title_field_off').prop('hidden', false);
			$('.has_title_field_on').prop('hidden', true);
		}
	});
});
