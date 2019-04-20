<?php
namespace app\modules\medical\models\orm;

use app\common\db\ActiveRecord;
use app\modules\medical\MedicalModule;

/**
 * Insurance ORM
 *
 * @property string $code
 * @property string $title
 * @property string $short_title
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
            'code' => MedicalModule::t('insurance', 'Code'),
            'title' => MedicalModule::t('insurance','Title'),
            'short_title' => MedicalModule::t('insurance','Short title'),
        ];
    }
}
