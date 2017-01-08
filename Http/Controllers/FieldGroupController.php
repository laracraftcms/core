<?php

namespace Laracraft\Core\Http\Controllers;

use Laracraft\Core\Entities\FieldGroup;
use Laracraft\Core\Http\Requests\FieldGroupPersistRequest;
use Cache;
use Request;
use Redirect;
use Laracraft\Core\Repositories\Contracts\FieldGroupRepositoryContract;

class FieldGroupController extends Controller
{
	/** @var FieldGroupRepositoryContract */
	protected $field_group_repository;

	public function __construct(FieldGroupRepositoryContract $field_group_repository)
	{
		$this->middleware('lock_resource:field_group',['only'=>['edit']]);

		$this->field_group_repository = $field_group_repository;
	}

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $field_groups = $this->field_group_repository->with(['fieldCount'])->all();

        $field_groups->load(['editLock', 'editLock.createdBy']);

        return view('core::cp.configure.field_group.index',compact('field_groups'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
		$field_group = $this->field_group_repository->instance();

		return view('core::cp.configure.field_group.create',compact('field_group'));
    }

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param FieldGroupPersistRequest $request
	 *
	 * @return \Illuminate\Http\Response
	 */
    public function store(FieldGroupPersistRequest $request)
    {
		$field_group = $this->field_group_repository->create(Request::all());

		If (Request::has('and_edit')) {
			return Redirect::route(config('laracraft-core.cp_root') . '.configure.field_group.edit', [$field_group]);
		} else {
			return Redirect::route(config('laracraft-core.cp_root') . '.configure.field_group.index');
		}
	}

    /**
     * Show the form for editing the specified resource.
     *
     * @param FieldGroup $field_group
     * @return \Illuminate\Http\Response
     */
    public function edit(FieldGroup $field_group)
    {
		return view('core::cp.configure.field_group.edit', compact('field_group'));
    }

	/**
	 * Update the specified resource in storage.
	 *
	 * @param FieldGroupPersistRequest $request
	 * @param FieldGroup $field_group
	 *
	 * @return \Illuminate\Http\Response
	 */
    public function update(FieldGroupPersistRequest $request, FieldGroup $field_group)
    {
        $field_group->fill(Request::all());
		$field_group->save();

		//flush cache
		Cache::tags(FieldGroup::class)->flush();

		If (Request::has('and_edit')) {
			return Redirect::route(config('laracraft-core.cp_root') . '.configure.field_group.edit', [$field_group]);
		} else {
			return Redirect::route(config('laracraft-core.cp_root') . '.configure.field_group.index');
		}
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param FieldGroup $field_group
     * @return \Illuminate\Http\Response
     */
    public function destroy(FieldGroup $field_group)
    {
        //
    }
}
