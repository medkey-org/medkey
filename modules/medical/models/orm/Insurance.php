<?php
namespace app\modules\medical\models\orm;

use app\common\db\ActiveRecord;

/**
 * Class Insurance
 *
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
class Insurance extends ActiveRecord
{
    public static function modelIdentity()
    {
        return [
            'title',
            'code',
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [ ['code', 'title'], 'required', ],
            [ ['title', 'short_title'], 'string'],
            [ ['code'], 'integer', ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'code' => 'Код',
            'title' => 'Название',
            'short_title' => 'Короткое название',
        ];
    }
}
