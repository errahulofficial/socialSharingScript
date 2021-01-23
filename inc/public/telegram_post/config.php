<?php
return [
    'id' => 'telegram_post',
    'name' => 'Telegram Post',
    'author' => 'Stackcode',
    'author_uri' => 'https://stackposts.com',
    'version' => '1.0',
    'desc' => '',
    'icon' => 'fab fa-telegram',
    'color' => '#0088cc',
    'menu' => [
        'tab' => 2,
        'position' => 950,
        'name' => 'Telegram',
        'sub_menu' => [
        	'position' => 1000,
            'id' => 'telegram_post',
            'name' => 'Post'
        ]
    ],
    'css' => [
		'assets/css/telegram_post.css'
	]
];