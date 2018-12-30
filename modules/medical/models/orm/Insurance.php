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
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [ ['code', 'title', 'short_title'], 'required', ],
            [ [ 'code', 'title', 'short_title' ], 'string' ]
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
