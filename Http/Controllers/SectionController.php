<?php

namespace Laracraft\Core\Http\Controllers;

use Laracraft\Core\Entities\EditLock;
use Laracraft\Core\Entities\Entry;
use Laracraft\Core\Entities\EntryType;
use Laracraft\Core\Entities\FieldLayout;
use Laracraft\Core\Entities\Section;
use Laracraft\Core\Repositories\Contracts\EntryTypeRepositoryContract;
use Laracraft\Core\Repositories\Contracts\FieldLayoutRepositoryContract;
use Laracraft\Core\Repositories\Contracts\SectionRepositoryContract;
use Request;
use Redirect;
use Cache;
use Script;
use Illuminate\Database\Eloquent\Collection;
use Laracraft\Core\Http\Requests\SectionPersistRequest;
use Laracraft\Core\Http\Requests\SectionUpdateEntryTypesRequest;
use DB;

class SectionController extends Controller
{

	/** @var  SectionRepositoryContract */
	protected $section_repository;


	public function __construct(SectionRepositoryContract $section_respository)
	{
		$this->middleware('lock_resource:section',['only'=>['edit','entryTypes']]);

		$this->section_repository  = $section_respository;

	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{

		$sections = $this->section_repository->tags(EntryType::class)->with('entryTypes')->all();

		$sections->load(['editLock', 'editLock.createdBy']);

		return view('core::cp.configure.section.index',compact('sections'));
	}

	public function entryTypes(EntryTypeRepositoryContract $entry_type_repository, Section $section)
	{
		$entry_types  = $entry_type_repository->all();

		/** @var Collection $entry_types */
		$entry_types = $entry_types->diff($section->entryTypes);
		return view('core::cp.configure.section.entry_type.index',compact('section','entry_types'));

	}

	public function updateEntryTypes(SectionUpdateEntryTypesRequest $request, Section $section){

		$section->entryTypes()->sync(Request::has('entry_types') ? Request::get('entry_types') : []);
		$section->save();

		$this->section_repository->flushCache();

		If (Request::has('and_edit')) {
			return Redirect::route(config('laracraft-core.cp_root') . '.configure.section.entry_type.index', [$section]);
		} else {
			return Redirect::route(config('laracraft-core.cp_root') . '.configure.section.edit',[$section]);
		}
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
		$section = $this->section_repository->instance([
			'has_urls' => (bool) config('laracraft-core.default_has_urls',true)
	   	]);

		return view('core::cp.configure.section.create', compact('section') );
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param SectionPersistRequest $request
	 * @param EntryTypeRepositoryContract $entry_type_repository
	 * @param FieldLayoutRepositoryContract $field_layout_repository
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function store(SectionPersistRequest $request, EntryTypeRepositoryContract $entry_type_repository, FieldLayoutRepositoryContract $field_layout_repository)
	{

		$section_repository = $this->section_repository;
		$section = null;

		DB::transaction(function() use( &$section, $section_repository, $entry_type_repository, $field_layout_repository) {

			$section = $section_repository->create(Request::all());

			$field_layout = $field_layout_repository->create([
											'name' => $section->name . ' Entry Fields',
											'handle' => $section->handle . '_entry_fields'
										]);

			$entry_type = $entry_type_repository->create([
											'name' => $section->name . ' Entry',
											'handle' => $section->handle . '_entry',
											'field_layout_id' => $field_layout->id
										]);

			$section->entryTypes()->save($entry_type);
		});

		If (Request::has('and_edit')) {
			return Redirect::route(config('laracraft-core.cp_root') . '.configure.section.edit', [$section]);
		} else {
			return Redirect::route(config('laracraft-core.cp_root') . '.configure.section.index');
		}

	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param Section $section
	 * @return \Illuminate\Http\Response
	 */
	public function edit(Section $section)
	{
		return view('core::cp.configure.section.edit', compact('section') );
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param SectionPersistRequest|Request $request
	 * @param Section $section
	 * @return \Illuminate\Http\Response
	 */
	public function update(SectionPersistRequest $request, Section $section)
	{

		$this->section_repository->update(Request::all(),$section);

		If (Request::has('and_edit')) {
			return Redirect::route(config('laracraft-core.cp_root') . '.configure.section.edit', [$section]);
		} else {
			return Redirect::route(config('laracraft-core.cp_root') . '.configure.section.index');
		}
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param Section $section
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Section $section)
	{
		$this->section_repository->delete($section);
	}
}
