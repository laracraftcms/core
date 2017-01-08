<?php

namespace Laracraft\Core\Http\Controllers;

use Laracraft\Core\Entities\Field;
use Laracraft\Core\Entities\FieldGroup;
use Laracraft\Core\Http\Requests\FieldPersistRequest;
use Cache;
use Request;
use Redirect;
use Response;

class FieldController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $field_groups = Cache::tags(FieldGroup::class)->rememberForever('fields_manage', function() {
            return FieldGroup::with('fields') ->get();
        });
		$field_groups->load(['editLock', 'editLock.createdBy']);

        return view('core::cp.configure.field.index',compact('field_groups'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
		$field = new Field();

		$field_groups = Cache::tags(FieldGroup::class)->rememberForever('index', function() {
			return FieldGroup::all();
		});

		return view('core::cp.configure.field.create',compact('field','field_groups'));
    }

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param FieldPersistRequest $request
	 *
	 * @return \Illuminate\Http\Response
	 */
    public function store(FieldPersistRequest $request)
    {
		$field = new Field();

		$field->fill(Request::all());
		$field->save();

		//flush cache
		Cache::tags([Field::class, FieldGroup::class])->flush();

		If (Request::has('and_edit')) {
			return Redirect::route(config('laracraft-core.cp_root') . '.configure.field.edit', [$field]);
		} else {
			return Redirect::route(config('laracraft-core.cp_root') . '.configure.field.index');
		}
	}

    /**
     * Show the form for editing the specified resource.
     *
     * @param Field $field
     * @return \Illuminate\Http\Response
     */
    public function edit(Field $field)
    {
		$field->load(['editLock']);

		$field_groups = Cache::tags(FieldGroup::class)->rememberForever('index', function() {
			return FieldGroup::all();
		});

		return view('core::cp.configure.field.edit',compact('field','field_groups'));
    }

	/**
	 * Update the specified resource in storage.
	 *
	 * @param FieldPersistRequest $request
	 * @param Field $field
	 *
	 * @return \Illuminate\Http\Response
	 */
    public function update(FieldPersistRequest $request, Field $field)
    {

        $field->fill(Request::all());
		$field->save();

		//flush cache
		Cache::tags([Field::class, FieldGroup::class])->flush();

		If (Request::has('and_edit')) {
			return Redirect::route(config('laracraft-core.cp_root') . '.configure.field.edit', [$field]);
		} else {
			return Redirect::route(config('laracraft-core.cp_root') . '.configure.field.index');
		}
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Field $field
     * @return \Illuminate\Http\Response
     */
    public function destroy(Field $field)
    {
        //
    }

	/**
	 * @param $type
	 * @param Field $field
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function settingsHtml($type, Field $field){

		if(!array_key_exists($type,config('laracraft-core.field_types'))) {
			abort(404);
		}

		$field->type = $type;

		return Response::json(['html' => Field::getSettingsHtml($field)]);
	}
}
