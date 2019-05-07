<?php
namespace app\modules\config\models\orm;

use app\common\db\ActiveRecord;

/**
 * Class Config
 * @package Module\Config
 * @copyright 2012-2019 Medkey
 */
class Config extends ActiveRecord
{
    const LANG_RU = 'ru-RU';
    const LANG_EN = 'en-US';

    public static function listLanguage(): array
    {
        return [
            self::LANG_EN => 'English',
            self::LANG_RU => 'Russian',
        ];
    }
}
