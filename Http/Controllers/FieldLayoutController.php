<?php

namespace Laracraft\Core\Http\Controllers;

use Laracraft\Core\Entities\EntryType;
use Laracraft\Core\Http\Requests\FieldLayoutPersistRequest;
use Laracraft\Core\Entities\FieldLayout;
use Laracraft\Core\Entities\FieldLayoutTab;
use Laracraft\Core\Repositories\Contracts\FieldGroupRepositoryContract;
use Laracraft\Core\Repositories\Contracts\FieldLayoutRepositoryContract;
use Laracraft\Core\Repositories\Contracts\FieldRepositoryContract;
use Request;
use DB;
use Laracraft\Core\Http\Requests;
use Redirect;
use Illuminate\Database\Eloquent\Collection;
use Laracraft\Core\Entities\Field;

class FieldLayoutController extends Controller
{

	/** @var FieldLayoutRepositoryContract */
	protected $field_layout_repository;

    public function __construct(FieldLayoutRepositoryContract $field_layout_repository)
    {
        $this->middleware('lock_resource:field_layout',['only'=>['edit']]);

		$this->field_layout_repository = $field_layout_repository;
	}

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $field_layouts = $this->field_layout_repository->tags(Field::class,EntryType::class)->with(['fieldCount','fieldLayoutTabCount','entryTypes'])->all();

        $field_layouts->load(['editLock', 'editLock.createdBy']);

        return view('core::cp.configure.field_layout.index',compact('field_layouts'));
    }

	/**
	 * Show the form for creating a new resource.
	 *
	 * @param FieldGroupRepositoryContract $field_group_repository
	 *
	 * @return \Illuminate\Http\Response
	 */
    public function create(FieldGroupRepositoryContract $field_group_repository)
    {

        $field_layout = $this->field_layout_repository->instance();

		$groups = $field_group_repository->with('fields')->tags(Field::class)->all();

		return view('core::cp.configure.field_layout.create',compact('field_layout','groups'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param FieldLayoutPersistRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(FieldLayoutPersistRequest $request)
    {
		$field_layout = $this->field_layout_repository->create(Request::all());

		If (Request::has('and_edit')) {
			return Redirect::route(config('laracraft-core.cp_root') . '.configure.field_layout.edit', [$field_layout]);
		} else {
			return Redirect::route(config('laracraft-core.cp_root') . '.configure.field_layout.index');
		}
    }

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param FieldLayout $field_layout
	 * @param FieldGroupRepositoryContract $field_group_repository
	 *
	 * @return \Illuminate\Http\Response
	 */
    public function edit(FieldLayout $field_layout, FieldGroupRepositoryContract $field_group_repository)
    {

        $groups = $field_group_repository->with('fields')->tags(Field::class)->all();

		/** @var Collection $fields */
		$fields = $field_layout->fields;

		foreach($field_layout->tabs as &$tab){
			$tab->setRelation('fields',$fields->where('pivot.field_layout_tab_id',$tab->id)->sortBy('pivot.sequence'));
		}

        /** @var Collection $groups */
        foreach($groups as &$group){
            $group->setRelation('fields',$group->fields->diff($field_layout->fields));
        }

        return view('core::cp.configure.field_layout.edit',compact('field_layout','groups'));
    }

	/**
	 * Update the specified resource in storage.
	 *
	 * @param FieldLayoutPersistRequest $request
	 * @param FieldLayout $field_layout
	 * @param FieldLayoutTab $field_layout_tab
	 * @param FieldRepositoryContract $field_repository
	 *
	 * @return \Illuminate\Http\Response
	 */
    public function update(FieldLayoutPersistRequest $request, FieldLayout $field_layout, FieldLayoutTab $field_layout_tab, FieldRepositoryContract $field_repository)
    {

       $field_layout_repository = $this->field_layout_repository;

		DB::transaction(function () use($field_layout, $field_layout_repository, $field_layout_tab, $field_repository) {
			$tab_names = Request::get('tabs',[]);
			$field_names = Request::get('names',[]);
			$required = Request::get('required',[]);

			$field_names_rev = array_flip($field_names);

			//update existing tabs
			foreach($field_layout->tabs as $tab){
				if(array_key_exists($tab->id,$tab_names)) {
					$tab->name = $tab_names[$tab->id];
					$tab->sequence = array_search($tab->id, array_keys($tab_names));
					$tab->save();
				}
			}

			//create new tabs
			$tabs = [];
			foreach($tab_names as $tabID => $tabName){
				if(preg_match('/^~~(new|group)~~/',$tabID)) {
					/** @var FieldLayoutTab $tab */
					$tab = $field_layout_tab->newInstance([
						'name' => $tabName,
						'field_layout_id' => $field_layout->id,
						'sequence' => array_search($tabName,array_values($tab_names))
					]);
					$tabs[$tab->name] = $tab->id;
				}else{
					$tabs[$tabName] = $tabID;
				}
			}

			//remove old tabs
			$removedTabs = array_diff($field_layout->tabs->pluck('id')->toArray(), array_values($tabs));
			$field_layout_tab->newQuery()->whereIn('id',$removedTabs)->delete();

			$usedFields = $field_repository->findWhereIn('id',$field_names_rev);

			//detect field name changes
			foreach($usedFields as $field){
				if($field_names[$field->id] === $field->name){
					unset($field_names[$field->id]);
				}
			}

			$layoutTabs = Request::get('layout',[]);
			$newlayout = [];

			// update fields
			foreach($layoutTabs as $tab =>$fields){
				foreach($fields as $seq => $field){
					$newlayout[$field] = ['sequence'=>$seq,'field_layout_tab_id'=>$tabs[$tab],'required'=>intval(in_array($field,$required))];
					if(array_key_exists($field,$field_names)){
						$newlayout[$field]['name_override'] = $field_names[$field];
					}
				}
			}

			// persist field changes
			$field_layout->fields()->sync($newlayout);

			$field_layout_repository->update(Request::all(), $field_layout);

		});


        If (Request::has('and_edit')) {
            return Redirect::route(config('laracraft-core.cp_root') . '.configure.field_layout.edit', [$field_layout]);
        } else {
            return Redirect::route(config('laracraft-core.cp_root') . '.configure.field_layout.index');
        }

    }

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param FieldLayout $field_layout
	 *
	 * @return \Illuminate\Http\Response
	 */
    public function destroy(FieldLayout $field_layout)
    {
        $this->field_layout_repository->delete($field_layout);
    }

}
