<?php

namespace Laracraft\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use DB;
use Carbon\Carbon;
use Laracraft\Core\Entities\Field;

class CoreDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();


		$adminUser = DB::table('users')->where('email', 'demo@laracraft.org')->first(['id']);
		$now = Carbon::now()->format('Y-m-d H:i:s');

        DB::table('field_groups')->updateOrInsert(['id' => 'd51b1e44-fd23-4f39-ad43-61d3b826f931'],[
			'name' => 'Default',
			'table_name' => 'default',
		  	'created_at' => $now,
			'updated_at' => $now,
			'created_by' => is_null($adminUser) ? null : $adminUser->id,
			'updated_by' => is_null($adminUser) ? null : $adminUser->id
		]);

		DB::table('fields')->updateOrInsert(['id' => '26cf2ea5-b615-4b7c-aaf7-4e17f8a6e5ef'],[
			'name' => 'Title',
			'handle' => 'title',
			'help' => '',
			'type' => Field::PLAINTEXT_TYPE,
			'settings' => '{}',
			'created_at' => $now,
			'updated_at' => $now,
			'created_by' => is_null($adminUser) ? null : $adminUser->id,
			'updated_by' => is_null($adminUser) ? null : $adminUser->id
		]);

		DB::table('fields')->updateOrInsert(['id' => '9bb58701-ef6c-4463-b444-8a2c43540adb'],[
			'name' => 'Slug',
			'handle' => 'slug',
			'help' => '',
			'type' => Field::PLAINTEXT_TYPE,
			'settings' => '{}',
			'created_at' => $now,
			'updated_at' => $now,
			'created_by' => is_null($adminUser) ? null : $adminUser->id,
			'updated_by' => is_null($adminUser) ? null : $adminUser->id
		]);
    }
}
