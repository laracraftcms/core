<?php
namespace Laracraft\Core\Database;

class Blueprint extends \Illuminate\Database\Schema\Blueprint
{


    /**
     * Add created_by field to the table.
     *
     * @return void
     */
    public function trackCreate($index= false)
    {
        $this->integer('created_by')->nullable();

        if((bool) $index){
            $this->index('create_by');
        }
    }

    /**
     * Add updated_by field to the table.
     *
     * @return void
     */
    public function trackUpdate($index= false)
    {
        $this->integer('updated_by')->nullable();

        if((bool) $index){
            $this->index('updated_by');
        }
    }


    /**
     * Add created_by and updated_by fields to the table.
     *
     * @return void
     */
    public function tracked($index= false)
    {
        $this->trackCreate($index);
        $this->trackUpdate($index);
    }

    /**
     * Add creation and update timestamps to the table.
     *
     * @return void
     */
    public function indexedTimestamps()
    {
        $this->timestamp('created_at')->index();
        $this->timestamp('updated_at')->index();
    }

    public function publishable(){

        $this->schedulable();
        $this->expireable();
    }

    public function schedulable(){

        $this->timestamp('published_at')->nullable()->index();
    }

    public function expireable(){

        $this->timestamp('expired_at')->nullable()->index();
    }

    public function enableable($default=false){

        $this->boolean('enabled')->default($default)->index();
    }

    public function baumNode(){

        $this->integer('parent_id')->nullable()->index();
        $this->integer('lft')->nullable()->index();
        $this->integer('rgt')->nullable()->index();
        $this->integer('depth')->nullable()->index();

    }

	public function morphsWithUUID($name, $indexName = null)
	{
		$this->uuid("{$name}_id");
		$this->string("{$name}_type");
		$this->index(["{$name}_id", "{$name}_type"], $indexName);
	}

}