<?php

namespace Laracraft\Core\Http\Controllers;

use DB;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Laracraft\Core\Entities\Url;
use Request;
use Laracraft\Core\Entities\Entry;
use Laracraft\Core\Entities\EntryType;
use Laracraft\Core\Entities\Section;
use Redirect;
use Laracraft\Core\Http\Requests\EntryPersistRequest;
use Session;
use Laracraft\Core\Repositories\Contracts\EntryRepositoryContract;

class EntryController extends Controller
{

	/** @var EntryRepositoryContract */
	protected $entry_repository;

	public function __construct(EntryRepositoryContract $entry_repository)
	{
		$this->entry_repository = $entry_repository;
	}

	public function index()
	{

		$entries = $this->entry_repository->setIncludeSlug(false)
			->setFieldsToLoad([])
			->tags([Section::class,Url::class,EntryType::class])
			->with(['section','url','entryType'])
			->all();

		/** @var Collection $entries */
		$entries->load('editLock', 'editLock.createdBy');

		return view('core::cp.entry.index',compact('entries'));
	}

	public function forSection(Section $section){

		$entries = $this->entry_repository->setIncludeSlug(false)
			->tags([Section::class,Url::class,EntryType::class])
			->with(['url','entryType'])
			->findWhere(['section_id' => $section->id]);

		/** @var Collection $entries */
		$entries->map(function($entry) use($section){
			$entry->setRelation('section',$section);
		});
		$entries->load('editLock', 'editLock.createdBy');

		return view('core::cp.entry.section.index',compact('entries','section'));
	}

	public function forSectionEntryType(Section $section, EntryType $entry_type){

		if(!$section->entryTypes->contains($entry_type)){
			abort(422);
		}

		$entries = $this->entry_repository->setIncludeSlug(false)
			->tags([Section::class,Url::class,EntryType::class])
			->with(['url'])
			->findWhere([
							'section_id' => $section->id,
							'entry_type_id' => $entry_type->id
						]);
		/** @var Collection $entries */
		$entries->map(function($entry) use($section, $entry_type){
			$entry->setRelation('section',$section);
			$entry->setRelation('entryType', $entry_type);
		});
		$entries->load('editLock', 'editLock.createdBy');

		return view('core::cp.entry.section.entry_type.index',compact('entries','section','entry_type'));
	}

	public function create(Section $section){

		if(!$this->assertCanCreate($section)){
			return Redirect::back();
		};

		$entry_type = $section->entryTypes->first();

		return Redirect::route(config('laracraft-core.cp_root') . '.section.entry_type.entry.create',[$section, $entry_type]);
	}

	public function createType(Section $section, EntryType $entry_type = null){

		if(!$this->assertCanCreate($section, $entry_type)){
			return Redirect::back();
		};

		if(!$section->entryTypes->contains($entry_type)){
			abort(422);
		}

		$entry = $this->entry_repository->instance([
			'section_id' => $section->id,
			'entry_type_id' => $entry_type->id
	   	]);
		//load relations
		$entry->setRelation('section',$section);
		$entry->setRelation('entryType',$entry_type);

		return view('core::cp.entry.create',compact('entry', 'entry_type', 'section'));
	}

	public function edit(Section $section, EntryType $entry_type, Entry $entry){

		$entry->section_id = $section->id;
		$entry->setRelation('section',$section);

		if(!$section->entryTypes->contains($entry_type)){
			abort(422);
		}

		$entry->entry_type_id = $entry_type->id;
		$entry->setRelation('entryType',$entry_type);

		$entry->populateFieldValues();


		return view('core::cp.entry.edit',compact('entry', 'entry_type', 'section'));
	}

	public function update(EntryPersistRequest $request, Section $section, EntryType $entry_type, Entry $entry){

		$entry->setRelation('entryType',$entry_type);
		$entry->setRelation('section', $section);

		DB::transaction(function () use(&$entry, $request) {
			$entry = $this->entry_repository->update($request->all(),$entry);
		});

		If (Request::has('and_edit')) {
			return Redirect::route(config('laracraft-core.cp_root') . '.section.entry_type.entry.edit', [$section, $entry_type, $entry]);
		} else {
			return Redirect::route(config('laracraft-core.cp_root') . '.section.entry.index', [$section]);
		}

	}

	public function store(EntryPersistRequest $request, Section $section, EntryType $entry_type){

		$entry = null;

		DB::transaction(function () use(&$entry, $request) {
			$entry = $this->entry_repository->create($request->all());
		});

		$entry->setRelation('entryType',$entry_type);
		$entry->setRelation('section', $section);

		If (Request::has('and_edit')) {
			return Redirect::route(config('laracraft-core.cp_root') . '.section.entry_type.entry.edit', [$section, $entry_type, $entry]);
		} else {
			return Redirect::route(config('laracraft-core.cp_root') . '.section.entry.index', [$section]);
		}
	}

	protected function assertCanCreate($section, $entry_type = null, $flash = true){

		//check we can create entries
		$this->authorize('create', [ Entry::class, $section, $entry_type ]);

		//Check if this section is a single whether we already have an entry
		if($section->type == Section::SINGLE_TYPE && $section->entries()->get()->count() > 0 ){

			if(!$flash) { return false; }

			Session::flash('alert',
						   [
							   'type' => 'info',
							   'message' => trans('core::errors.section.single_entry_exists')
						   ]);
			return false;
		}

		//if no entry type was defined check this section has at least one entry type.
		if(is_null($entry_type)){
			$entry_type = $section->entryTypes->first();
			if(!$entry_type){

				if(!$flash){ return false; }

				Session::flash('alert',
							   [
								   'type' => 'danger',
								   'message' => trans('core::errors.section.no_entry_types')
							   ]);
				return false;
			}
		}

		return true;
	}

	public function show(Entry $entry){

		$entry->populateFieldValues();
		\Debugbar::startMeasure('entry:render');
		$view = view($entry->getResolvedView(), ['entity' => $entry]);
		\Debugbar::stopMeasure('entry:render');
		return $view;
	}
}
