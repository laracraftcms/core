<?php

namespace Laracraft\Core\Http\Controllers;

use Laracraft\Core\Entities\EntryType;
use Laracraft\Core\Entities\Section;
use Laracraft\Core\Http\Requests\EntryTypePersistRequest;
use Laracraft\Core\Repositories\Contracts\EntryTypeRepositoryContract;
use Laracraft\Core\Repositories\Contracts\FieldLayoutRepositoryContract;
use Laracraft\Core\Repositories\Contracts\SectionRepositoryContract;
use Request;
use Cache;
use Script;
use Redirect;
use Laracraft\Core\Entities\FieldLayout;

class EntryTypeController extends Controller
{

	/** @var EntryTypeRepositoryContract */
	protected $entry_type_repository;

    public function __construct(EntryTypeRepositoryContract $entry_type_repository)
    {
        $this->middleware('lock_resource:entry_type',['only'=>['edit']]);

		$this->entry_type_repository = $entry_type_repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $entry_types = $this->entry_type_repository->tags(Section::class, FieldLayout::class)->with(['sections','fieldLayout'])->all();

        $entry_types->load(['editLock', 'editLock.createdBy']);

        return view('core::cp.configure.entry_type.index',compact('entry_types'));
    }

	/**
	 * Show the form for creating a new resource.
	 *
	 * @param FieldLayoutRepositoryContract $field_layout_repository
	 *
	 * @return \Illuminate\Http\Response
	 */
    public function create(FieldLayoutRepositoryContract $field_layout_repository)
    {

        $type = $this->entry_type_repository->instance([]);

        $field_layouts = $field_layout_repository->all();

        return view('core::cp.configure.entry_type.create',compact('type','field_layouts'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  EntryTypePersistRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(EntryTypePersistRequest $request)
    {
        $type = $this->entry_type_repository->create(Request::all());

        If (Request::has('and_edit')) {
            return Redirect::route(config('laracraft-core.cp_root') . '.configure.entry_type.edit', [$type]);
        } else {
            return Redirect::route(config('laracraft-core.cp_root') . '.configure.entry_type.index');
        }
    }

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param EntryType $entry_type
	 * @param FieldLayoutRepositoryContract $field_layout_repository
	 *
	 * @return \Illuminate\Http\Response
	 */
    public function edit(EntryType $entry_type, FieldLayoutRepositoryContract $field_layout_repository)
    {
		$entry_type->load('sections');

        $field_layouts = $field_layout_repository->all();

        return view('core::cp.configure.entry_type.edit',compact('entry_type','field_layouts'));
    }

	/**
	 * Update the specified resource in storage.
	 *
	 * @param EntryTypePersistRequest $request
	 * @param EntryType $entry_type
	 *
	 * @return \Illuminate\Http\Response
	 */
    public function update(EntryTypePersistRequest $request, EntryType $entry_type)
    {
		$this->entry_type_repository->update(Request::all(),$entry_type);

        If (Request::has('and_edit')) {
            return Redirect::route(config('laracraft-core.cp_root') . '.configure.entry_type.edit', [$entry_type]);
        } else {
            return Redirect::route(config('laracraft-core.cp_root') . '.configure.entry_type.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param EntryType $entry_type
     * @return \Illuminate\Http\Response
     */
    public function destroy(EntryType $entry_type)
    {
        $this->entry_type_repository->delete($entry_type);
    }
}
