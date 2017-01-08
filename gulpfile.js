const elixir = require('laravel-elixir');

process.env.DISABLE_NOTIFIER = true;

require('laravel-elixir-eslint');

var del = require('del'); // execute: $ npm install --save-dev del

elixir.extend("remove", function(path) {
	new elixir.Task('del', function() {
		return del(path);
	});
});

elixir.config.publicPath = 'Assets';

elixir(function(mix) {

	mix.copy('node_modules/font-awesome/fonts','Assets/fonts');

	mix.scripts([
		'node_modules/jquery/dist/jquery.js',
		'node_modules/tether/dist/js/tether.js',
		'node_modules/bootstrap/js/dist/util.js',
		'node_modules/bootstrap/js/dist/alert.js',
		'node_modules/bootstrap/js/dist/button.js',
		//'node_modules/bootstrap/js/dist/carousel.js',
		'node_modules/bootstrap/js/dist/collapse.js',
		'node_modules/bootstrap/js/dist/dropdown.js',
		'node_modules/bootstrap/js/dist/modal.js',
		'node_modules/bootstrap/js/dist/scrollspy.js',
		'node_modules/bootstrap/js/dist/tab.js',
		'node_modules/bootstrap/js/dist/tooltip.js',
		'node_modules/bootstrap/js/dist/popover.js',
		'node_modules/bootstrap-toggle/js/bootstrap-toggle.js',
		'node_modules/js-cookie/src/js.cookie.js',
		'node_modules/moment/moment.js',
		'node_modules/datatables.net/js/jquery.dataTables.js',
		'node_modules/datatables.net-select/js/dataTables.select.js',
		'resources/assets/js/vendor/**/*.js'
	], 'resources/assets/js/tmp/vendor.js', './');

	mix.eslint([
		'resources/assets/js/**/*.js',
		'!resources/assets/js/tmp/*.js',
		'!resources/assets/js/vendor/*.js'
	]);

	mix.scripts( [
		'laracraft.js',
		'components/editlocks.js'
	], 'resources/assets/js/tmp/laracraft.js');

	mix.combine([
		'resources/assets/js/tmp/vendor.js',
		'resources/assets/js/tmp/laracraft.js'
	], 'Assets/js/laracraft.js');

	mix.remove([
		'resources/assets/js/tmp'
	]);

	mix.copy('resources/assets/js/pages','Assets/js/pages');

    mix.sass('laracraft.scss', 'Assets/css/laracraft.css');
});
