window.laracraft = window.laracraft || {};

window.laracraft.settings = window.laracraft.settings || {};

$.fn.bootstrapToggle.Constructor.DEFAULTS = {
	on: '',
	off: '',
	onstyle: 'success',
	offstyle: 'danger',
	size: 'normal',
	style: '',
	width: 40,
	height: null
};

$(document).ready(function(){
	$('[data-toggle="tooltip"]').tooltip();
});
