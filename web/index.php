<?php
setlocale(LC_ALL, 'ru_RU.UTF-8');
require(__DIR__ . '/../vendor/autoload.php');

use Symfony\Component\Dotenv\Dotenv;

(new Dotenv())->load(__DIR__ . '/../.env');

// loading config of modules
$pathModules = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'modules';
$modules =  scandir($pathModules);
foreach ($modules as $module) {
    if ($module === '.' || $module === '..' ) {
        continue;
    }
    $p = $pathModules . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . '.env';
    if (file_exists($p)) {
        (new Dotenv())->load($p);
    }
}

defined('YII_DEBUG') or define('YII_DEBUG', getenv('APP_DEBUG') === 'true' ? true : false);
defined('YII_ENV') or define('YII_ENV', getenv('APP_ENV') === 'true' ? true : false);

require(__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');
require(__DIR__ . '/../override/web.php');

$config = require(__DIR__ . '/../config/web.php');
Yii::$container = new \app\common\di\Container();
(new app\common\web\Application($config))->run();
