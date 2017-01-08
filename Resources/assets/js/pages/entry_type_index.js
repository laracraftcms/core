$(document).ready(function () {
	$('#entry_types').not('.empty').DataTable({
		'columnDefs': [
			{
				'targets':    [3],
				'searchable': false,
				'sortable':   false
			}
		]
	});
});
