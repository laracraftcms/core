$(document).ready(function () {

	$('#type').change(function () {
		$('div[class^="fieldset-"],div[class*=" fieldset-"]').removeClass('in');
		$('.fieldset-' + $(this).val()).addClass('in');
	});

	$('#single_has_urls').change(function(){
		$('#has_urls-fields-single').collapse('toggle');
	});
	$('#channel_has_urls').change(function(){
		$('#has_urls-fields-channel').collapse('toggle');
	});
	$('#structure_has_urls').change(function(){
		$('#has_urls-fields-structure').collapse('toggle');
	});
});
