$(document).ready(function () {
	$('#entry_type').change(function(e){
		var id = $('#id').val();
		var params = {
			'section' : $('#section_handle').val(),
			'entry_type' : $(this).val()
		};
		if (id.length > 0){
			params.id = id;
		}
		window.location = window.laracraft.routes.route(window.laracraft.settings.cp_root + '.section.entry_type.entry.' + (id.length > 0 ? 'edit' : 'create'), params);
	});
});
