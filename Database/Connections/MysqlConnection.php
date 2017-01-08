<?php

namespace Laracraft\Core\Database\Connections;

use Illuminate\Database\Schema\MySqlBuilder;
use Laracraft\Core\Database\Blueprint;

/**
 * Class MySqlConnection
 */
class MysqlConnection extends \Illuminate\Database\MySqlConnection
{
    /**
     * Get a schema builder instance for the connection.
     *
     * @return \Illuminate\Database\Schema\MySqlBuilder
     */
    public function getSchemaBuilder()
    {
        if (is_null($this->schemaGrammar)) {
            $this->useDefaultSchemaGrammar();
        }
        $builder = new MySqlBuilder($this);

        $builder->blueprintResolver(function ($table, $callback) {
            return new Blueprint($table, $callback);
        });
        return $builder;
    }
}