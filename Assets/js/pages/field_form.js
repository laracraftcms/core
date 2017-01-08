window.laracraft = window.laracraft || {};
window.laracraft.field = window.laracraft.field || {};
window.laracraft.field.types = window.laracraft.field.types || {};

window.laracraft.field.fieldSettings = $('#fieldSettings').first();
window.laracraft.field.exists = $('#exists').first().val();

window.laracraft.field.getSettingsHtml = function($type, $field){

	var params =  {'type' : $type};

	if (typeof $field !== 'undefined'){
		params.field = $field;
	}

	$.ajax(window.laracraft.routes.route(window.laracraft.settings.cp_root + '.configure.field.settings' + (window.laracraft.field.exists ? '.for':''), params), {
		success:function($data){
			window.laracraft.field.fieldSettings.html($data.html);
			window.laracraft.field.initSettings($data.type);
		}
	});
};

window.laracraft.field.initSettings = function($type){
	window.laracraft.field.fieldSettings.find('[data-toggle="toggle"]').bootstrapToggle();
	if (typeof window.laracraft.field.types[$type] !== 'undefined') {
		window.laracraft.field.types[$type].init();
	}
};


window.laracraft.field.types.plaintext = {
	init: function(){
		$('#max_length_advanced').click(function(e){
			e.preventDefault();
			$('#settings\\[max_length\\], [for="settings[max_length]"]').prop('hidden', true).prop('disabled', true);
			$('#settings\\[max_length\\]exact, [for="settings[max_length]exact"]').prop('hidden', false).prop('disabled', false);
		});
		$('#max_length_simple').click(function(e){
			e.preventDefault();
			$('#settings\\[max_length\\], [for="settings[max_length]"]').prop('hidden', false).prop('disabled', false);
			$('#settings\\[max_length\\]exact, [for="settings[max_length]exact"]').prop('hidden', true).prop('disabled', true);
		});
	}
};

$(document).ready(function () {

	var $type = $('#type');
	$type.change(function (e) {
		window.laracraft.field.getSettingsHtml(
			$(this).val(),
			(window.laracraft.field.exists ? $('#fieldRouteKey').val() : null )
		);
	});

	if (typeof window.laracraft.field.types[$type.val()] !== 'undefined') {
		window.laracraft.field.types[$('#type').val()].init();
	}

});

