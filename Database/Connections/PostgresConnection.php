<?php
namespace Laracraft\Core\Database\Connections;

use Illuminate\Database\Schema\PostgresBuilder;
use Laracraft\Core\Database\Blueprint;

class PostgresConnection extends \Illuminate\Database\PostgresConnection
{
	/**
	 * Get a schema builder instance for the connection.
	 *
	 * @return \Illuminate\Database\Schema\PostgresBuilder
	 */
	public function getSchemaBuilder()
	{
		if (is_null($this->schemaGrammar)) {
			$this->useDefaultSchemaGrammar();
		}

		$builder =  new PostgresBuilder($this);
		$builder->blueprintResolver(function ($table, $callback) {
			return new Blueprint($table, $callback);
		});
		return $builder;
	}
}