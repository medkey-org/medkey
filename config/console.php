<?php
/**
 * @copyright 2012-2019 Medkey
 */

Yii::setAlias('@tests', dirname(__DIR__) . '/tests/codeception');

$params = require(__DIR__ . '/params.php');
$db = require(__DIR__ . '/db.php');

$config = [
    'id' => 'medkey-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => [
        'log',
        app\common\Bootstrap::class,
    ],
    'version' => '1.0',
    'dynamicModule' => true,
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
//        'configManager' => [
//            'class' => \app\common\config\ConfigManager::class,
//        ],
        'acl' => [
            'class' => \app\common\acl\Acl::class,
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
                [
                    'class' => 'yii\log\DbTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'seeder' => [
        	'class' => 'app\common\seeds\Seeder',
        ],
        'dependencyFactory' => [
            'class' => 'app\common\dependencies\DependencyFactory',
        ],
    ],
    'params' => $params,
];

return $config;