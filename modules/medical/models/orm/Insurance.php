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
            'code' => MedicalModule::t('insurance', 'Code'),
            'title' => MedicalModule::t('insurance','Title'),
            'short_title' => MedicalModule::t('insurance','Short title'),
        ];
    }
}
