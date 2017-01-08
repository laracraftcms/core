<?php

namespace Laracraft\Core\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use DB;
use Laracraft\Core\Entities\Helpers\UrlFormatter;
use Laracraft\Core\Entities\Section;

class LaracraftDemoSeeder extends Seeder
{

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {

		DB::table('users')->insert([
									   'name' => 'Laracraft Admin',
									   'email' => 'demo@laracraft.org',
									   'password' => bcrypt('secret'),
								   ]);

		$adminUser = DB::table('users')->where('email', 'demo@laracraft.org')->first(['id']);
		$now = Carbon::now()->format('Y-m-d H:i:s');

		DB::table('field_layouts')->updateOrInsert(['id' => '774b6681-4038-4ab9-b3a7-d5a3c5b69fa8'],[
											   'name' => 'Homepage Field Layout',
											   'handle' => 'homepage_field_layout',
											   'created_at' => $now,
											   'updated_at' => $now,
											   'created_by' => $adminUser->id,
											   'updated_by' => $adminUser->id
										   ]);

		DB::table('entry_types')->updateOrInsert(['id' => 'c0269ded-6916-4196-8c11-7dfdb931cae5'],[
											 'field_layout_id' => '774b6681-4038-4ab9-b3a7-d5a3c5b69fa8',
											 'name' => 'Homepage Entry',
											 'handle' => 'homepage_entry',
											 'has_title_field' => 1,
											 'title_config' => null,
											 'created_at' => $now,
											 'updated_at' => $now,
											 'created_by' => $adminUser->id,
											 'updated_by' => $adminUser->id
										 ]);
		DB::table('sections')->updateOrInsert(['id' => 'cfeee385-866b-4428-8404-68498e719f01'],[
											 'name' => 'Homepage',
											 'handle' => 'homepage',
											 'type' => Section::SINGLE_TYPE,
										     'has_urls' => 1,
											 'view' => 'core::demo.home',
										     'has_versions' => 1,
											 'default_enabled' => 1,
											 'url_format' => UrlFormatter::HOME_PLACEHOLDER,
											 'enable' => 1,
											 'created_at' => $now,
											 'updated_at' => $now,
											 'created_by' => $adminUser->id,
											 'updated_by' => $adminUser->id
										 ]);

	}

}