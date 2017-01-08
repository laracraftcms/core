<?php

function laracraftGetBreadcrumb($route, $routeParams = [], $titleParams = []){
	$cp_root = config('laracraft-core.cp_root');
	$link = route($cp_root . '.' . $route,$routeParams);
	$trans = 'core::routes.' . config('laracraft-core.cp_root') . '.' . $route;
	$title = trans($trans.'.breadcrumb',$titleParams)!=$trans.'.breadcrumb' ? trans($trans.'.breadcrumb',$titleParams) : trans($trans.'.title',$titleParams);
	return [$title, $link];
}

$breadcrumbs = app('laracraft-breadcrumbs');

$cp_root = config('laracraft-core.cp_root');

$breadcrumbs->register($cp_root . '.index' ,function ($breadcrumbs)
{
	list($title, $link) = laracraftGetBreadcrumb('index');
	$breadcrumbs->push($title,$link);

});

$breadcrumbs->register($cp_root . '.configure.index' ,function ($breadcrumbs) use ($cp_root)
{

	$breadcrumbs->parent($cp_root . '.index');
	list($title, $link) = laracraftGetBreadcrumb('configure.index');
	$breadcrumbs->push($title,$link);

});


$breadcrumbs->register($cp_root . '.entry.index' ,function ($breadcrumbs) use ($cp_root)
{

	$breadcrumbs->parent($cp_root . '.index');
	list($title, $link) = laracraftGetBreadcrumb('entry.index');
	$breadcrumbs->push($title,$link);

});

$breadcrumbs->register($cp_root . '.section.entry.index' ,function ($breadcrumbs, $section) use ($cp_root)
{
	$breadcrumbs->parent($cp_root . '.entry.index');
	list($title, $link) = laracraftGetBreadcrumb('section.entry.index',[$section],['name' => $section->name]);
	$breadcrumbs->push($title,$link);

});

$breadcrumbs->register($cp_root . '.section.entry_type.entry.index' ,function ($breadcrumbs, $section, $entry_type) use ($cp_root)
{

	$breadcrumbs->parent($cp_root . '.section.entry.index', $section);
	list($title, $link) = laracraftGetBreadcrumb('section.entry_type.entry.index',[$section, $entry_type],['name' => $entry_type->name]);
	$breadcrumbs->push($title,$link);

});

$breadcrumbs->register($cp_root . '.section.entry_type.entry.create' ,function ($breadcrumbs, $section, $entry_type) use ($cp_root)
{

	$breadcrumbs->parent($cp_root . '.section.entry_type.entry.index', $section, $entry_type);
	list($title, $link) = laracraftGetBreadcrumb('section.entry_type.entry.create',[$section, $entry_type]);
	$breadcrumbs->push($title,$link);

});

$breadcrumbs->register($cp_root . '.section.entry_type.entry.edit' ,function ($breadcrumbs, $section, $entry_type, $entry) use ($cp_root)
{

	$breadcrumbs->parent($cp_root . '.section.entry_type.entry.index', $section, $entry_type);
	list($title, $link) = laracraftGetBreadcrumb('section.entry_type.entry.edit',[$section, $entry_type, $entry],['title'=>$entry->title]);
	$breadcrumbs->push($title,$link);

});


$breadcrumbs->register($cp_root . '.configure.section.index' ,function ($breadcrumbs) use ($cp_root)
{
	$breadcrumbs->parent($cp_root . '.configure.index');
	list($title, $link) = laracraftGetBreadcrumb('configure.section.index');
	$breadcrumbs->push($title,$link);

});

$breadcrumbs->register($cp_root . '.configure.section.create' ,function ($breadcrumbs) use ($cp_root)
{
	$breadcrumbs->parent($cp_root . '.configure.section.index');
	list($title, $link) = laracraftGetBreadcrumb('configure.section.create');
	$breadcrumbs->push($title,$link);

});

$breadcrumbs->register($cp_root . '.configure.section.edit' ,function ($breadcrumbs, $section) use ($cp_root)
{
	$breadcrumbs->parent($cp_root . '.configure.section.index');
	list($title, $link) = laracraftGetBreadcrumb('configure.section.edit',[$section],['name' => $section->name]);
	$breadcrumbs->push($title,$link);

});

$breadcrumbs->register($cp_root . '.configure.section.entry_type.index' ,function ($breadcrumbs, $section) use ($cp_root)
{
	$breadcrumbs->parent($cp_root . '.configure.section.index');

	list($title, $link) = laracraftGetBreadcrumb('configure.section.edit',[$section],['name' => $section->name]);
	$breadcrumbs->push($title,$link);

	list($title, $link) = laracraftGetBreadcrumb('configure.section.entry_type.index',[$section]);
	$breadcrumbs->push($title,$link);

});

$breadcrumbs->register($cp_root . '.configure.entry_type.index' ,function ($breadcrumbs) use ($cp_root)
{
	$breadcrumbs->parent($cp_root . '.configure.index');
	list($title, $link) = laracraftGetBreadcrumb('configure.entry_type.index');
	$breadcrumbs->push($title,$link);

});

$breadcrumbs->register($cp_root . '.configure.entry_type.create' ,function ($breadcrumbs) use ($cp_root)
{
	$breadcrumbs->parent($cp_root . '.configure.entry_type.index');
	list($title, $link) = laracraftGetBreadcrumb('configure.entry_type.create');
	$breadcrumbs->push($title,$link);

});

$breadcrumbs->register($cp_root . '.configure.entry_type.edit' ,function ($breadcrumbs, $entry_type) use ($cp_root)
{
	$breadcrumbs->parent($cp_root . '.configure.entry_type.index');
	list($title, $link) = laracraftGetBreadcrumb('configure.entry_type.edit',[$entry_type],['name' => $entry_type->name]);
	$breadcrumbs->push($title,$link);

});

$breadcrumbs->register($cp_root . '.configure.field_layout.index' ,function ($breadcrumbs) use ($cp_root)
{
	$breadcrumbs->parent($cp_root . '.configure.index');
	list($title, $link) = laracraftGetBreadcrumb('configure.field_layout.index');
	$breadcrumbs->push($title,$link);

});

$breadcrumbs->register($cp_root . '.configure.field_layout.create' ,function ($breadcrumbs) use ($cp_root)
{
	$breadcrumbs->parent($cp_root . '.configure.field_layout.index');
	list($title, $link) = laracraftGetBreadcrumb('configure.field_layout.create');
	$breadcrumbs->push($title,$link);

});

$breadcrumbs->register($cp_root . '.configure.field_layout.edit' ,function ($breadcrumbs, $field_layout) use ($cp_root)
{
	$breadcrumbs->parent($cp_root . '.configure.field_layout.index');
	list($title, $link) = laracraftGetBreadcrumb('configure.field_layout.edit',[$field_layout],['name' => $field_layout->name]);
	$breadcrumbs->push($title,$link);

});

$breadcrumbs->register($cp_root . '.configure.field_group.index' ,function ($breadcrumbs) use ($cp_root)
{
	$breadcrumbs->parent($cp_root . '.configure.index');
	list($title, $link) = laracraftGetBreadcrumb('configure.field_group.index');
	$breadcrumbs->push($title,$link);

});

$breadcrumbs->register($cp_root . '.configure.field_group.create' ,function ($breadcrumbs) use ($cp_root)
{
	$breadcrumbs->parent($cp_root . '.configure.field_group.index');
	list($title, $link) = laracraftGetBreadcrumb('configure.field_group.create');
	$breadcrumbs->push($title,$link);

});

$breadcrumbs->register($cp_root . '.configure.field_group.edit' ,function ($breadcrumbs, $field_group) use ($cp_root)
{
	$breadcrumbs->parent($cp_root . '.configure.field_group.index');
	list($title, $link) = laracraftGetBreadcrumb('configure.field_group.edit',[$field_group],['name' => $field_group->name]);
	$breadcrumbs->push($title,$link);

});

$breadcrumbs->register($cp_root . '.configure.field.index' ,function ($breadcrumbs) use ($cp_root)
{
	$breadcrumbs->parent($cp_root . '.configure.index');
	list($title, $link) = laracraftGetBreadcrumb('configure.field.index');
	$breadcrumbs->push($title,$link);

});

$breadcrumbs->register($cp_root . '.configure.field.create' ,function ($breadcrumbs) use ($cp_root)
{
	$breadcrumbs->parent($cp_root . '.configure.field.index');
	list($title, $link) = laracraftGetBreadcrumb('configure.field.create');
	$breadcrumbs->push($title,$link);

});

$breadcrumbs->register($cp_root . '.configure.field.edit' ,function ($breadcrumbs, $field) use ($cp_root)
{
	$breadcrumbs->parent($cp_root . '.configure.field.index');
	list($title, $link) = laracraftGetBreadcrumb('configure.field.edit',[$field],['name' => $field->name]);
	$breadcrumbs->push($title,$link);

});