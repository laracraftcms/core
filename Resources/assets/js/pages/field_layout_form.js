window.laracraft = window.laracraft || {};
window.laracraft.field = window.laracraft.field || {};

$(document).ready(function () {

	$('.tab-fields').sortable(tab_fields_conf).disableSelection();

	$('.group-fields').sortable(group_fields_conf).disableSelection();

	$('.field-groups').sortable({
		connectWith: '.layout-tabs, #available_fields',
		handle:      '.card-header'
	}).disableSelection();

	$('.layout-tabs').sortable({
		handle:  '.card-header',
		receive: function (event, ui) {
			var $clone = $(ui.item).clone().removeAttr('style');
			$clone.find('li.field').remove();
			$(ui.item).removeClass('group-tab');
			var $group = $(ui.item).find('.group-fields');
			$group.sortable('destroy');
			$group.removeClass('group-fields').addClass('tab-fields');
			$group.sortable(tab_fields_conf).disableSelection();
			$(ui.item).find('input').prop('disabled', false);
			$(ui.item).find('.config-toggle').prop('hidden', false);
			ui.sender.append($clone);
			$clone.prop('hidden', true).find('.group-fields').sortable(group_fields_conf).disableSelection();
			laracraft_field_layout_refresh();
		}
	}).disableSelection();

	var $layout = $('#layout');

	$layout.on('click', '[data-toggle="required"]', function () {
		var $field = $(this).closest('li.field');
		var $req = $field.find('.required_field');
		if ($req.prop('disabled')) {
			$req.prop('disabled', false);
			$(this).text('Make not required');
		} else {
			$req.prop('disabled', true);
			$(this).text('Make required');
		}
		$field.find('.field_name_display').toggleClass('required');
	});

	$layout.on('click', '[data-toggle="relabel"]', function () {
		var $field = $(this).closest('li.field');
		$field.find('.field_name_display').hide();
		var $field_input = $field.find('.field_name');
		$field_input.prop('type', 'text');
		$field_input.focus();
		$field_input.keydown(function (e) {
			if (e.keyCode === 13) {
				$(this).blur();
				e.preventDefault();
				e.stopPropagation();
				return false;
			}
			if (e.keyCode === 27) {
				$(this).val($field.find('.field_name_display').text());
				$(this).prop('type', 'hidden');
				e.preventDefault();
				e.stopPropagation();
				return false;
			}
		});
		$field_input.blur(function () {
			var $val = $(this).val();
			var $orig = $(this).data('original');
			$field.find('.field_name_display').text($val).show();
			if ($val !== $orig) {
				if ($field.find('.field_name_display_original').length === 0) {
					$field.find('.field_name_display').before($('<div class="field_name_display_original">' + window.laracraft.lang.forms.original + ' ' + $orig + '</div>'));
				}
			} else {
				$field.find('.field_name_display_original').remove();
			}
			$(this).prop('type', 'hidden');
		});
	});

	$layout.on('dblclick', '.tab-name', laracraft_tab_rename);

	$layout.on('click', '[data-toggle="rename"]', laracraft_tab_rename);

	$layout.on('click', '[data-toggle="delete"]', function () {
		var $tab = $(this).closest('.tab');
		$tab.find('li.field').each(function () {
			laracraft_remove_field($(this));
		});
		$tab.remove();
	});

	$('#available_fields').droppable({
		accept: 'li.field, .tab',
		drop:   function (event, ui) {
			if ($(ui.draggable).is('li.field')) {
				laracraft_remove_field($(ui.draggable));
			} else {
				if (!$(ui.draggable).is('.group-tab')) {
					$(ui.draggable).find('li.field').each(function () {
						laracraft_remove_field($(this));
					});
					$(ui.draggable).remove();
					laracraft_field_layout_refresh();
				}
			}
		}
	});

	$('#addTab').click(function (e) {
		e.preventDefault();
		var $tt = $('#tabTemplate');
		var $clone = $tt.children().first().clone();
		var $layout_tabs = $('.layout-tabs').first();
		var $n = $tt.data('count') + 1;
		$tt.data('count', $n);
		var $name = 'Tab ' + $n;
		$clone.attr('data-tab-name', $name);
		$clone.find('.tab-name-field').attr('name', 'tabs[~~new~~' + $n + ']').val($name).prop('disabled', false);
		$clone.find('.tab-name').text($name);
		$layout_tabs.append($clone);
		$clone.find('.tab-fields').sortable(tab_fields_conf).disableSelection();
		laracraft_field_layout_refresh();
	});

});

var tab_fields_conf = {
	connectWith: '.tab-fields',
	receive:     function (event, ui) {
		var $tabname = $(this).closest('.tab').find('.tab-name-field').first().val();
		var $item = $(ui.item);
		$item.find('input').not('.required_field').prop('disabled', false);
		$item.find('.field_id').attr('name', 'layout[' + $tabname + '][]');
		$item.find('.config-toggle').prop('hidden', false);
	}
};

var group_fields_conf = {
	connectWith: '.tab-fields',
	remove:      function () {
		if ($(this).children().length === 0) {
			$(this).closest('.tab').prop('hidden', true);
		}
	}
};

function laracraft_field_layout_refresh() {

	$('.field-groups, .layout-tabs, .group-fields, .field-groups').sortable('refresh');

}
function laracraft_remove_field($item) {
	var $group = $item.data('field-group');
	var $clone = $item.clone().removeAttr('style');
	$clone.find('.config-toggle').prop('hidden', true);
	$clone.find('input').prop('disabled', true);
	var $name = $clone.find('.field_name');
	$name.val($name.data('original'));
	$clone.find('.field_name_display').removeClass('required').text($name.data('original'));
	$clone.find('.field_name_display_original').remove();
	$clone.find('.field_id').attr('name', 'layout[' + $group + '][]');
	$('[data-tab-name="' + $group + '"]', '#available_fields')
		.prop('hidden', false)
		.find('.group-fields').append($clone);
	$item.remove();
}

function laracraft_tab_name_blur(){
	var $header = $(this).closest('.card-header');
	var $tab_name_input = $header.find('.tab-name-field');
	var $val = $tab_name_input.val();
	$header.find('.tab-name').text($val).show();
	$tab_name_input.prop('type', 'hidden');
	$tab_name_input.closest('.tab').find('.field_id').attr('name', 'layout[' + $val + '][]');
}
function laracraft_tab_rename() {
	var $header = $(this).closest('.card-header');
	var $tab_name = $header.find('.tab-name').hide();
	var $tab_name_input = $header.find('.tab-name-field');
	$tab_name_input.prop('type', 'text');
	$tab_name_input.focus();
	$tab_name_input.keydown(function (e) {
		if (e.keyCode === 13) {
			e.preventDefault();
			e.stopPropagation();
			$tab_name_input.trigger('blur');
			return false;
		}
		if (e.keyCode === 27) {
			$tab_name.show();
			$(this).val($tab_name.text());
			$(this).prop('type', 'hidden');
			e.preventDefault();
			e.stopPropagation();
			return false;
		}
	});
	$tab_name_input.blur(laracraft_tab_name_blur);
}
