<?php
// This file is generated. Do not modify it manually.
return array(
	'gas-stations-list-block' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'create-block/gas-stations-list-block',
		'version' => '0.1.0',
		'title' => 'Gas Stations List Block',
		'category' => 'widgets',
		'icon' => 'smiley',
		'description' => 'Test assignment for Scopevisio.',
		'keywords' => array(
			'gas',
			'station'
		),
		'supports' => array(
			'html' => false,
			'spacing' => array(
				'padding' => true
			),
			'align' => array(
				'wide',
				'full'
			)
		),
		'attributes' => array(
			'columns' => array(
				'type' => 'string',
				'default' => 'grid-cols-3'
			),
			'showMap' => array(
				'type' => 'boolean',
				'default' => false
			),
			'colorTheme' => array(
				'type' => 'string',
				'default' => 'light'
			)
		),
		'textdomain' => 'gas-stations-list-block',
		'editorScript' => 'file:./index.js',
		'editorStyle' => 'file:./index.css',
		'style' => 'file:./style-index.css',
		'viewScript' => 'file:./view.js'
	)
);
