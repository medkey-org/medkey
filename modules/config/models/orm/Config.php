<?php
namespace app\modules\config\models\orm;

use app\common\db\ActiveRecord;
use app\modules\config\ConfigModule;

/**
 * Class Config
 * @package Module\Config
 * @copyright 2012-2019 Medkey
 */
class Config extends ActiveRecord
{
    const LANG_NOT_SET = ' ';
    const LANG_RU = 'ru-RU';
    const LANG_EN = 'en-US';

    public static function listLanguage(): array
    {
        return [
            self::LANG_EN => ConfigModule::t('common','English'),
            self::LANG_RU => ConfigModule::t('common','Russian'),
        ];
    }

    public static function listLanguageWithNotSet(): array
    {
        return array_merge([
            Config::LANG_NOT_SET => ConfigModule::t('common','Language not set (system default)'),
        ], self::listLanguage());
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [ 'key', 'unique' ],
            [ ['key', 'value', ], 'string' ],
        ];
    }
}
