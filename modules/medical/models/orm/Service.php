<?php
namespace app\modules\medical\models\orm;

use app\common\db\ActiveRecord;
use app\common\validators\ForeignKeyValidator;
use app\modules\medical\MedicalModule;

/**
 * Class Service
 *
 * @property string $speciality_id
 * @property string $description
 * @property string $short_title
 * @property string $title
 * @property string $code
 *
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
class Service extends ActiveRecord
{
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 2;


    public function getSpeciality()
    {
        return $this->hasOne(Speciality::className(), ['id' => 'speciality_id']);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [ ['speciality_id'], ForeignKeyValidator::class, ],
            [ ['code', 'title', 'short_title', 'speciality_id'], 'required', ],
            [ ['code', 'title', 'short_title', 'description'], 'string' ],
            [ ['status'], 'default', 'value' => self::STATUS_ACTIVE ],
            [ ['status'], 'integer' ],
            [ ['program'], 'integer' ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabelsOverride()
    {
        return [
            'code' => MedicalModule::t('service', 'Code'),
            'title' => MedicalModule::t('service', 'Title'),
            'program' => MedicalModule::t('service', 'Program'),
            'short_title' => MedicalModule::t('service', 'Short title'),
            'description' => MedicalModule::t('service', 'Description'),
            'speciality_id' => MedicalModule::t('service', 'Speciality'),
            'status' => MedicalModule::t('service', 'Status'),
        ];
    }
}