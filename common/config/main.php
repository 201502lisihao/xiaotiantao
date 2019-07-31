<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
    ],
    'modules' => [
        'gii' => [
            'class' => 'yii\gii\Module',
            // 配置访问IP地址
            'allowedIPs' => ['127.0.0.1', '::1', '111.204.113.197']
        ],
        'debug' => [
            'class' => 'yii\debug\Module',
            // 配置访问IP地址
            'allowedIPs' => ['127.0.0.1', '::1', '111.204.113.197']
        ],
    ]
];
