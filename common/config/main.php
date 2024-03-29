<?php
return [
    'aliases'    => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'language'   => 'ru-RU',
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        /*
        'cache' => [
            'class'     => 'yii\caching\FileCache',
            'cachePath' => '@backend/cache',
        ],
        */
        'cache' => [
            'class'   => 'yii\caching\MemCache',
            'servers' => [
                [
                    'host'   => 'localhost',
                    'port'   => 11211,
                    'weight' => 100,
                ],
            ],
        ],

    ],
];
