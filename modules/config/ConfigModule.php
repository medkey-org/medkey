<?php
namespace app\modules\config;

use app\common\base\Module;

/**
 * Class ConfigModule
 * @package Module\Config
 * @copyright 2012-2019 Medkey
 */
class ConfigModule extends Module
{
    public static function translationList()
    {
        return [
            'workflow',
            'directory',
            'order',
        ];
    }
}
