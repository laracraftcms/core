<?php
return [
	config('laracraft-core.cp_root') => [
		'index' => [
			'title' => 'Dashboard',
			'menu' => 'Dashboard'
		],
		'entry'=> [
			'index' => [
				'title' => 'All Entries',
				'menu' => 'Entries',
				'breadcrumb' => 'Entries'
			]
		],
		'section' => [
			'entry' => [
				'index' => [
					'title' => ':section Entries',
					'breadcrumb' => ':name'
				]
			],
			'entry_type' => [
				'entry' => [
					'index' => [
						'title' => ':section Entries',
						'breadcrumb' => ':name'
					],
					'create' => [
						'title' => 'New Entry',
						'breadcrumb' => 'Create'
					],
					'edit'=> [
						'title' => 'Edit Entry',
						'breadcrumb' => ':title'
					]
				]
			]
		],
		'configure' => [

			'index' => [
				'title' => 'Configure',
				'menu' => 'Configure'
			],

			'section'=> [

				'index'     =>  [
					'title' => 'Manage Sections',
					'breadcrumb' => 'Sections',
					'menu' => 'Sections'
				],
				'edit'      =>  [
					'title' => 'Edit Section',
					'breadcrumb' => ':name'
				],
				'create'    =>  [
					'title' => 'New Section',
					'breadcrumb' => 'New'
				],
				'entry_type' => [

					'index' => [
						'title' => 'Manage Entry Types for :section',
						'breadcrumb' => 'Entry Types'
					]

				]
			],

			'entry_type'=> [

				'index' =>  [
					'title' => 'Manage Entry Types',
					'breadcrumb' => 'Entry Types',
					'menu' => 'Entry Types'
				],
				'edit'  =>  [
					'title' => 'Edit Entry Type',
					'breadcrumb' => ':name'
				],
				'create' =>  [
					'title' => 'Create Entry Type',
					'breadcrumb' => 'New'
				],

			],

			'field_layout'=> [

				'index' =>  [
					'title' => 'Manage Field Layouts',
					'breadcrumb' => 'Field Layouts',
					'menu' => 'Field Layouts'
				],
				'edit'  =>  [
					'title' => 'Edit Field Layout',
					'breadcrumb' => ':name'
				],
				'create' =>  [
					'title' => 'Create Field Layout',
					'breadcrumb' => 'New'
				],

			],

			'field_group'=> [

				'index' =>  [
					'title' => 'Manage Field Groups',
					'breadcrumb' => 'Field Groups',
					'menu' => 'Field Groups'
				],
				'edit'  =>  [
					'title' => 'Edit Field Group',
					'breadcrumb' => ':name'
				],
				'create' =>  [
					'title' => 'Create Field Group',
					'breadcrumb' => 'New'
				],

			],

			'field'=> [

				'index' =>  [
					'title' => 'Manage Fields',
					'breadcrumb' => 'Fields',
					'menu' => 'Fields'
				],
				'edit'  =>  [
					'title' => 'Edit Field',
					'breadcrumb' => ':name'
				],
				'create' =>  [
					'title' => 'Create Field',
					'breadcrumb' => 'New'
				],

			]

		]
	]
];