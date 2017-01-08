<?php
use Laracraft\Core\Entities\Section;
use Laracraft\Core\Entities\Field;

return [
    'entry_type' => [
		'heading' =>'Entry Type(s)',
		'label' => 'Entry Type|Entry Types',
		'create' => 'Add new entry type',
		'add' => 'Associate entry type',
		'remove'=> 'Disassociate entry type',
		'empty' => 'No entry types configured',
		'none_to_add' => 'No entry types available',
		'none_to_remove' => 'No entry types associated',
		'change' => '<span class="text-warning"><i class="fa fa-exclamation-circle"></i> WARNING: Changing this value may lead to data loss.</span>',
		'fields' => [
			'name' => [
				'heading' =>'Name',
				'label' =>'Name',
				'help' =>'What this entry type will be called in the Control Panel.'
			],
			'handle' => [
				'heading' => 'Handle',
				'label' =>'Handle',
				'help' => 'How you’ll refer to this entry type in the views.'
			],
			'has_title_field' => [
				'heading' => "Has title field",
				'label' => "Has title field",
				'help' => "Does this entry type have an editable title field?"
			],
			'title_config' => [
				'heading' => 'Title Config',
				'label' => [
					'true' => 'Title Field Label',
					'false' => 'Title format'
				],
				'help' => [
					'true' => 'The label for this entry type\'s title field. (if left blank will default to "Title")',
					'false' => 'What the auto-generated entry titles should look like. You can include tags that output entry properties, such as {myCustomField}.'
				]
			],
			'field_layout' => [
				'heading' => 'Field Layout',
				'label' => 'Field Layout',
				'help' => 'The Field Layout that this entry type will use.',
				'placeholder' => 'Select Field Layout'
			]
		]
	],
    'section' => [
		'heading' => 'Section(s)',
		'label' => 'Section|Sections',
		'create' => 'Add new section',
		'empty' => 'No sections configured',
		'fields' => [
			'name' => [
				'heading' => 'Name',
				'label' =>'Name',
				'help' =>'What this section will be called in the Control Panel.'
			],
			'handle' => [
				'heading' => 'Handle',
				'label' => 'Handle',
				'help' => 'How you’ll refer to this section in the views.'
			],
			'type' => [
				'heading' => 'Type',
				'label' => 'Section Type',
				'help' =>'What type of section is this?',
				'placeholder' => 'Select type'
			],
			'has_urls' => [
				'heading' => 'Has URL(s)',
				'label' => [
					Section::SINGLE_TYPE    =>  'Should this entry have a URL and be routed?',
					Section::CHANNEL_TYPE   =>  'Should this entries in this channel have URL\'s and be routed?',
					Section::STRUCTURE_TYPE =>  'Should this entries in this structure have URL\'s and be routed?',
				],
			],
			'url_format' => [
				'heading' => 'URL Format',
				'label' => [
					Section::SINGLE_TYPE    =>  'URL',
					Section::CHANNEL_TYPE   =>  'Entry URL Format',
					Section::STRUCTURE_TYPE =>  'Top-level URL Format'
				],
				'help' => [
					Section::SINGLE_TYPE	=>	'What this entry\'s URL should be.',
					Section::CHANNEL_TYPE   =>  'Define the URL format for entries in this channel. You can include tags that output entry properties, such as {slug} or {createdAt|date("Y")}.',
					Section::STRUCTURE_TYPE =>  'Define the URL format for top-level entries in this structure. You can include tags that output entry properties, such as {slug} or {createdAt|date("Y")}.'
				]
			],
			'view' => [
				'heading' => 'View',
				'label' => [
					Section::SINGLE_TYPE    =>  'View',
					Section::CHANNEL_TYPE   =>  'View',
					Section::STRUCTURE_TYPE =>  'View'
				],
				'help' => [
					Section::SINGLE_TYPE	=>	'The view used to render this entry.',
					Section::CHANNEL_TYPE   =>  'The view used to render entries in this channel.',
					Section::STRUCTURE_TYPE =>  'The view used to render entries in this structure.'
				]
			],
			'nested_url_format' => [
				'heading' => 'Nested URL Format',
				'label' => 'Nested URL Format',
				'help' => 'Define the URL format for nested entries in this structure. To form nested URL\'s begin with {parent.url}. You can include tags that output entry properties, such as {slug} or {createdAt|date("Y")}.'
			],
			'max_levels' => [
				'heading' => 'Max Levels',
				'label' => 'The maximum number of levels allowed in this structure.',
				'help' => 'Enter 0 or leave empty for unlimited.'
			],
			'default_enabled' => [
				'heading' => 'Default Status',
				'label' => 'Default Status:'
			],
			'has_versions' => [
				'heading' => 'Has Versions',
				'label' => 'Enable versioning for entries in this section?'
			],
			'enabled' => [
				'heading' => 'Status',
				'label' => 'Status:'
			]
		],
		'types' =>  [
			Section::SINGLE_TYPE    =>  'Single',
			Section::CHANNEL_TYPE   =>  'Channel',
			Section::STRUCTURE_TYPE =>  'Structure'
		],
	],
    'field_layout' => [
		'heading' => 'Field Layout|Field Layout(s)',
		'label' => 'Field Layout|Field Layouts',
		'create' => 'Add new field layout',
		'empty' => 'No field layouts configured',
		'layout' => 'This Layout',
		'fields' => [
			'name' => [
				'heading' => 'Name',
				'label' =>'Name',
				'help' =>'What this field layout will be called in the Control Panel.'
			],
			'handle' => [
				'heading' => 'Handle',
				'label' =>'Handle',
				'help' =>'How you’ll refer to this field layout in the views.'
			],
			'tabs' => [
				'count' => 'Tab Count'
			]
		]
	],
	'field_layout_tab' => [
		'create' =>'New Tab',
		'destroy'=> 'Delete Tab',
		'rename' => 'Rename Tab'
	],
	'field_group' => [
		'heading' => 'Field Group(s)',
		'label' => 'Field Group|Field Groups',
		'create' => 'Add new field group',
		'empty' => 'No field groups configured',
		'fields' => [
			'name' => [
				'heading' => 'Name',
				'label' =>'Name',
				'help' =>'What this field group will be called in the Control Panel.'
			],
			'table_name' => [
				'heading' => 'Table Name',
				'label' => 'Table Name',
				'help' => 'What this countent groups table will be called in the database. Values will have `_content` appended automatically.'
			],
			'fields' => [
				'heading' => 'Fields in this group'
			]
		]
	],
	'field' => [
		'heading' => 'Field(s)',
		'label' => 'Field|Fields',
		'create' => 'Add new field',
		'empty' => 'No fields configured',
		'relabel' => 'Relabel Field',
		'count' => 'Field Count',
		'available' => 'Available Fields',
		'fields' => [
			'name' => [
				'heading' => 'Name',
				'label' =>'Name',
				'help' =>'What this field will be called in the Control Panel, and its default label when used in a field layout.'
			],
			'handle' => [
				'heading' => 'Handle',
				'label' => 'Handle',
				'help' => 'How you’ll refer to this field in the views.'
			],
			'field_group'=> [
				'heading' => 'Field Group',
				'label' => 'Field Group',
				'help' => 'The field group this field will belong to. Fields used together often should be grouped to improve performance.'
			],
			'help' => [
				'heading' => 'Help text',
				'label' => 'Help text',
				'help' => 'The default help text to display with this field when used in a field layout.'
			],
			'type' => [
				'heading' => 'Field Type',
				'label' => 'Field Type',
				'help' => 'This fields type. <span class="text-warning"><i class="fa fa-exclamation-circle"></i> WARNING: Changing this value may lead to data loss.</span>'
			]
		],
		'types' => [
			Field::PLAINTEXT_TYPE => 'Plain Text',
			Field::BOOLEAN_TYPE => 'Boolean (true / false)'
		]
	],
    'entry' => [
		'heading'=> 'Entries',
		'label'=> 'Entry|Entries',
		'fields' => [
			'title' => [
				'heading' => 'Title',
				'label' => 'Title',
				'help' =>''
			],
			'enabled' => [
				'heading' => 'Status',
				'label' => 'Status:'
			],
			'published_at' => [
				'heading' => 'Published at',
				'label' => 'Published at',
				'help' => 'When this entry was/will-be published. Set to a future date to schedule publishing at that time. Unpublished entries are not visible publicly.'
			],
			'expired_at' => [
				'heading' => 'Expired at',
				'label' => 'Expired at',
				'help' => 'When this entry expired/will expire. Set to a past date to expire this entry. Expired entries are not visible publicly.'
			],
			'created_at' => [
				'heading' => 'Created at',
				'label' => 'Created at',
			],
			'created_by' => [
				'heading' => 'Created by',
				'label' => 'Created by',
			],
			'url' => [
				'heading' => 'URL',
				'label' => 'URL',
			]
		]
	]
];