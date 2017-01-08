<?php
namespace Laracraft\Core\Database\Connections;

use \Illuminate\Database\Schema\Builder as SchemaBuilder;
use Laracraft\Core\Database\Blueprint;

class SQLiteConnection extends \Illuminate\Database\SQLiteConnection
{
	/**
	 * Get a schema builder instance for the connection.
	 *
	 * @return \Illuminate\Database\Schema\Builder
	 */
	public function getSchemaBuilder()
	{
		if (is_null($this->schemaGrammar)) {
			$this->useDefaultSchemaGrammar();
		}

		$builder =  new SchemaBuilder($this);
		$builder->blueprintResolver(function ($table, $callback) {
			return new Blueprint($table, $callback);
		});
		return $builder;
	}
}