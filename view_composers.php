<?php

view()->composer(['core::cp.*'],function ($view) {
	$view->with('laracraft_breadcrumbs', app('laracraft-breadcrumbs'));
});

view()->composer(['core::fieldtypes.plaintext.settings'],function ($view) {
	$view->with('text_sizes', \Laracraft\Core\Database\DBHelper::TEXTUAL_COLUMN_SIZES);
});

view()->composer(['core::cp.configure.section.index'], function ($view) {
	Script::enqueue('laracraft-section-index','modules/core/js/pages/section_index.js');
});

view()->composer([
		'core::cp.configure.section.edit',
		'core::cp.configure.section.create'
	],function(){
		Script::enqueue('laracraft-section-form','modules/core/js/pages/section_form.js');
	});

view()->composer(['core::cp.configure.section.entry_type.index'], function ($view) {
	Script::enqueue('laracraft-section-index','modules/core/js/pages/section_entry_types.js');
});

view()->composer(['core::cp.configure.entry_type.index'], function ($view) {
	Script::enqueue('laracraft-entry-type-index','modules/core/js/pages/entry_type_index.js');
});

view()->composer([
					 'core::cp.configure.entry_type.edit',
					 'core::cp.configure.entry_type.create'
				 ],function(){
	Script::enqueue('laracraft-entry-type-form','modules/core/js/pages/entry_type_form.js');
});

view()->composer([
					 'core::cp.configure.field_layout.edit',
					 'core::cp.configure.field_layout.create'
				 ],function(){
	Script::enqueue('laracraft-field-layout-form','modules/core/js/pages/field_layout_form.js');
});

view()->composer([
					 'core::cp.configure.field.edit',
					 'core::cp.configure.field.create'
				 ],function(){
	Script::enqueue('laracraft-field-form','modules/core/js/pages/field_form.js');
});


view()->composer([
					 'core::cp.entry.edit',
					 'core::cp.entry.create'
				 ],function(){
	Script::enqueue('laracraft-entry-form','modules/core/js/pages/entry_form.js');
});