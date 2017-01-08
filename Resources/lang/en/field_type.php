<?php

return [
	'plaintext' => [
		'settings' => [
			'placeholder' => [
				'label' => 'Placeholder',
				'help'=> 'Placeholder text for input field in control panel.'
			],
			'max_length' => [
				'label' => 'Field Length',
				'help'=> 'The maximum length of this field. This fields database column will be sized appropriately.<br><span class="text-warning"><i class="fa fa-exclamation-circle"></i> WARNING: Changing this value may lead to data loss.</span>'
			],
			'multiline' => [
				'label' => 'Allow multiline?'
			],
			'initial_rows' =>[
				'label' => 'Initial Rows',
				'help' => 'Initial rows to display in textarea input.'
			]
		],
		'sizes'=> [
			\Laracraft\Core\Database\DBHelper::SIZE_STRING => 'Small (<=' . \Laracraft\Core\Database\DBHelper::SIZE_MAX_STRING .' chars)',
			\Laracraft\Core\Database\DBHelper::SIZE_TEXT => 'Standard (<=' . \Laracraft\Core\Database\DBHelper::SIZE_MAX_TEXT .' chars)',
			\Laracraft\Core\Database\DBHelper::SIZE_MEDIUMTEXT => 'Large (<=' . \Laracraft\Core\Database\DBHelper::SiZE_MAX_MEDIUMTEXT .' chars)',
			\Laracraft\Core\Database\DBHelper::SIZE_LONGTEXT => 'Very Large (up to ' . \Laracraft\Core\Database\DBHelper::SIZE_MAX_LONGTEXT .' chars)',
		]
	],
	'boolean' => [
		'settings' => [
			'default' => [
				'label' => 'Default value',
				'help' => 'This fields default value.'
			]
		]
	]
];
