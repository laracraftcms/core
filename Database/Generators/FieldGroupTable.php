<?php

namespace Laracraft\Core\Database\Generators;

use Laracraft\Core\Entities\Field;
use Laracraft\Core\Entities\FieldGroup;
use Illuminate\Filesystem\Filesystem;

class FieldGroupTable{

    protected $fieldGroup;
    protected $files;
    protected $name;
    protected $action;
    protected $fields;
	protected $old_name;
	protected $time;

    public function __construct(Filesystem $files)
    {
        $this->files = $files;
    }

    /**
     * Get the path to where we should store the migration.
     *
     * @param  string $name
     * @return string
     */
    protected function getPath($name)
    {
		$this->time = time();
        return base_path() . '/database/migrations/' . date('Y_m_d_His') . '_' . $name . '_' . $this->time . '.php';
    }

    protected function makeDirectory($path){
        if (!$this->files->isDirectory(dirname($path))) {
            $this->files->makeDirectory(dirname($path), 0777, true, true);
        }
    }

    /**
     * Compile the migration stub.
     *
     * @return string
     */
    protected function compileMigrationStub()
    {
        $stub = $this->files->get( __DIR__ . '/../Stubs/migration.stub');

        $this->replaceClassName($stub)
            ->replaceSchema($stub)
            ->replaceTableName($stub);

        return $stub;
    }

    /**
     * Replace the class name in the stub.
     *
     * @param  string $stub
     * @return $this
     */
    protected function replaceClassName(&$stub)
    {
        $className = ucwords(camel_case($this->name));

        $stub = str_replace('{{class}}', $className . time(), $stub);

        return $this;
    }

    /**
     * Replace the schema for the stub.
     *
     * @param  string $stub
     * @return $this
     */
    protected function replaceSchema(&$stub)
    {
        $base =  $this->files->get( __DIR__ . '/../Stubs/schema-' .($this->action === 'create'?'create':'change') .'.stub');

        switch($this->action){
            case 'create':
                $up = str_replace('{{schema}}',$this->constructSchema($this->fields),$base);
                $down = sprintf('$schema->drop("%s");', $this->fieldGroup->table_name . '_content');
                break;
			case 'rename':
				$up = sprintf('$schema->rename("%s","%s");', $this->old_name, $this->fieldGroup->table_name . '_content');
				$down = sprintf('$schema->rename("%s","%s");', $this->fieldGroup->table_name . '_content', $this->old_name);
				break;
            case 'addColumn':
                $up = str_replace('{{schema}}',$this->constructSchema($this->fields,'add'),$base);
                $down = str_replace('{{schema}}',$this->constructSchema($this->fields,'drop'),$base);
                break;
            case 'dropColumn':
                $up = str_replace('{{schema}}',$this->constructSchema($this->fields,'drop'),$base);
                $down = str_replace('{{schema}}',$this->constructSchema($this->fields,'add'),$base);
                break;
            case 'renameColumn':
                $up = str_replace('{{schema}}',$this->constructSchema($this->fields,'rename'),$base);
                $rev = array_map('array_reverse',$this->fields);
                $down = str_replace('{{schema}}',$this->constructSchema($rev,'rename'),$base);
                break;
			case 'modifyColumn':
				$up = str_replace('{{schema}}',$this->constructSchema($this->fields,'modifyUp'),$base);
				$down = str_replace('{{schema}}',$this->constructSchema($this->fields,'modifyDown'),$base);
				break;
            default:
                $up = str_replace('{{schema}}','',$base);
                $down = str_replace('{{schema}}','',$base);
        }

        $stub = str_replace(['{{schema_up}}', '{{schema_down}}'], [$up, $down], $stub);

        return $this;
    }

    /**
     * Construct the schema fields.
     *
     * @param  array $fields
     * @param  string $action
     * @return array
     */
    private function constructSchema($fields, $action = 'add')
    {
        if (!$fields) return '';

		if($action == 'modifyUp'){
			$schema = array_map(function ($field) use ($action) {
				return $this->addColumnSyntax($field, true, false);
			}, $fields);
		}else if($action == 'modifyDown'){
			$schema = array_map(function ($field) use ($action) {
				return $this->addColumnSyntax($field, true, true);
			}, $fields);
		}else {
			$schema = array_map(function ($field) use ($action) {
				$method = "{$action}ColumnSyntax";

				return $this->$method($field);
			},
				$fields);
		}
        return implode("\n" . str_repeat(' ', 12), $schema);
    }

	/**
	 * Construct the syntax to add a column.
	 *
	 * @param  Field $field
	 * @param bool $change
	 * @param bool $original
	 *
	 * @return string
	 */
    private function addColumnSyntax(Field $field, $change = false, $original = false)
    {

		$params = "'" . $field->handle . "'";

        // If there are arguments for the schema type, like decimal('amount', 5, 2)
        // then we have to remember to work those in.
        if (!empty($field->getDatabaseTypeParams($original))) {
            $params .= ',' . implode(', ', $field->getDatabaseTypeParams($original));
        }

		$syntax = sprintf("\$table->%s(%s)->nullable()", $field->getDatabaseType($original), $params);

        if (!empty($field->getDatabaseTypeOptions($original)) && is_array($field->getDatabaseTypeOptions($original))) {
            foreach ($field->getDatabaseTypeOptions($original) as $method => $value) {
                $syntax .= sprintf("->%s(%s)", $method, $value === true ? '' : $value);
            }
        }
		if($change){
			$syntax .= '->change()';
		}
        $syntax .= ';';

		return $syntax;
    }

    /**
     * Construct the syntax to drop a column.
     *
     * @param  Field $field
     * @return string
     */
    private function dropColumnSyntax(Field $field)
    {
        return sprintf("\$table->dropColumn('%s');", $field->handle);
    }

    /**
     * Construct the syntax to drop a column.
     *
     * @param  array $data
     * @return string
     */
    private function renameColumnSyntax($data)
    {
        return sprintf("\$table->renameColumn('%s','%s');", $data[0], $data[1]);
    }

    /**
     * Replace the table name in the stub.
     *
     * @param  string $stub
     * @return $this
     */
    protected function replaceTableName(&$stub)
    {
        $table = $this->fieldGroup->table_name . '_content';

        $stub = str_replace('{{table}}', $table, $stub);

        return $this;
    }

    public function create(FieldGroup $fieldGroup){

        $this->fieldGroup = $fieldGroup;

        $this->action = 'create';

        $this->name = 'create_' . $this->fieldGroup->table_name . '_content_table';

        $this->fields = [];

        $path = $this->getPath($this->name);

        $this->makeDirectory($path);

        $this->files->put($path, $this->compileMigrationStub());
    }

	public function rename(FieldGroup $fieldGroup, $old_name){

		$this->fieldGroup = $fieldGroup;

		$this->action = 'rename';

		$this->name = 'rename_' . $old_name . '_to_' . $this->fieldGroup->table_name . '_content';

		$this->old_name = $old_name;

		$this->fields = [];

		$path = $this->getPath($this->name);

		$this->makeDirectory($path);

		$this->files->put($path, $this->compileMigrationStub());
	}



    public function addField(FieldGroup $fieldGroup, Field $field){

        $this->fieldGroup = $fieldGroup;

        $this->action = 'addColumn';

        $this->name = 'add_' . $field->handle .'_column_to_' . $this->fieldGroup->table_name . '_content_table';

        $this->fields = [$field];

        $path = $this->getPath($this->name);

        $this->makeDirectory($path);

        $this->files->put($path, $this->compileMigrationStub());

    }

    public function dropField(FieldGroup $fieldGroup, Field $field){

        $this->fieldGroup = $fieldGroup;

        $this->action = 'dropColumn';

        $this->name = 'drop_' . $field->handle .'_column_from_' . $this->fieldGroup->table_name . '_content_table';

        $this->fields = [$field];

        $path = $this->getPath($this->name);

        $this->makeDirectory($path);

        $this->files->put($path, $this->compileMigrationStub());

    }

    public function renameField(FieldGroup $fieldGroup, Field $field, $from){

        $this->fieldGroup = $fieldGroup;

        $to = $field->handle;

        $this->action = 'renameColumn';

        $this->name = 'rename_' . $from . '_column_to_' . $to .'_column_in_' . $this->fieldGroup->table_name . '_content_table';

        $this->fields = [[$from,$to]];

        $path = $this->getPath($this->name);

        $this->makeDirectory($path);

        $this->files->put($path, $this->compileMigrationStub());

    }

	public function modifyField(FieldGroup $fieldGroup, Field $field){

		$this->fieldGroup = $fieldGroup;

		$this->action = 'modifyColumn';

		$this->name = 'modify_' . $field->handle .'_column_in_' . $this->fieldGroup->table_name . '_content_table';

		$this->fields = [$field];

		$path = $this->getPath($this->name);

		$this->makeDirectory($path);

		$this->files->put($path, $this->compileMigrationStub());

	}
}