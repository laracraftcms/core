$(document).ready(function () {
	var $forms = $('#section_entry_types, #entry_types');
	var $section_entry_types = $('#section_entry_types');
	var $entry_types = $('#entry_types');
	var check_empty = function(){
		$entry_types.find('.empty_row').prop('hidden', $entry_types.find('tbody tr').not('.empty_row').length !== 0);
		$section_entry_types.find('.empty_row').prop('hidden', $section_entry_types.find('tbody tr').not('.empty_row').length !== 0);
	};
	$forms.on('click', '.toggle-plus', function (e) {
		var $tr = $(this).closest('tr');
		$tr.find('input').prop('disabled', false);
		$tr.appendTo($section_entry_types.find('tbody'));
		$(this).prop('hidden', true);
		$tr.find('.toggle-minus').prop('hidden', false);
		check_empty();
	});
	$forms.on('click', '.toggle-minus', function (e) {
		var $tr = $(this).closest('tr');
		$tr.find('input').prop('disabled', true);
		$tr.appendTo($entry_types.find('tbody'));
		$(this).prop('hidden', true);
		$tr.find('.toggle-plus').prop('hidden', false);
		check_empty();
	});
});

