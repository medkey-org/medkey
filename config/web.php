<?php
/**
 * @copyright 2012-2019 Medkey
 */

$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'medkey-web',
    'basePath' => dirname(__DIR__),
    'bootstrap' => [
        'log',
        \app\common\Bootstrap::class
    ],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'dynamicModule' => true,
    'defaultRoute' => '/dashboard/ui/dashboard/index',
    'homeUrl' => ['/dashboard/ui/dashboard/index'],
    'version' => @file_get_contents(__DIR__ . '/../version'),
    'sourceLanguage' => 'en-US',
    'components' => [
        'request' => [
            'class' => \app\common\web\Request::class,
            'cookieValidationKey' => '1@3$5^qWeRvBnMj!K#l%Oi',
            'parsers' => [
                'application/json' => \app\common\web\JsonParser::class,
            ],
        ],
        'i18n' => [
            'class' => \app\common\i18n\I18N::class,
            'translations' => [
                'app*' => [
                    'class' => 'app\common\i18n\PhpMessageSource',
                    'basePath' => '@app/messages',
                    'fileMap' => [
                        'app' => 'app.php',
                        'app/error' => 'error.php',
                        'app/attributes/user' => 'attributes/user.php',
                    ],
                ],
                'yii' => [
                    'class' => 'app\common\i18n\PhpMessageSource',
                    'basePath' => '@app/messages',
                ],
            ],
        ],
	    'response' => [
	    	'class' => 'app\common\web\Response',
	    ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'class' => \app\common\web\User::class,
            'identityClass' => \app\modules\security\models\orm\User::class,
            'enableAutoLogin' => false,
            'enableSession' => true,
        ],
        'eventDispatcher' => [
            'class' => \Symfony\Component\EventDispatcher\EventDispatcher::class,
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                [
                    'pattern' => '<module>/<controller>/<action>',
                    'route' => '<module>/<controller>/<action>',
                    'suffix' => ''
                ],
            ]
        ],
        'assetManager' => [
            'class' => \yii\web\AssetManager::class,
            'appendTimestamp' => true,
            'bundles' => false,
        ],
        'acl' => [
            'class' => \app\common\acl\Acl::class,
        ],
//        'configManager' => [
//            'class' => \app\common\config\ConfigManager::class,
//        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => require(__DIR__ . '/db.php'),
        'formatter' => [
            'class' => \app\common\base\Formatter::class,
        ],
        'view' => [
            'class' => \app\common\web\View::class,
	        'themeName' => 'basic',
        ],
    ],
    'params' => $params,
];
if (YII_DEBUG) {
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => ['127.0.0.1', '127.0.0.1:100', '::1', '10.0.2.2'],
    ];
}

return $config;
