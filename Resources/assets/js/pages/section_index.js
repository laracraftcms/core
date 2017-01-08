$(document).ready(function () {
	$('#sections').not('.empty').DataTable({
		'columnDefs': [
			{
				'targets':    [2],
				'searchable': false
			},
			{
				'targets':    [4],
				'sortable':   false,
				'searchable': false
			}
		]
	});
});
