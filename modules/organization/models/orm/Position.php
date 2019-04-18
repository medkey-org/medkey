<?php
namespace app\modules\organization\models\orm;

use app\common\db\ActiveRecord;
use app\common\validators\ForeignKeyValidator;
use app\modules\organization\OrganizationModule;

/**
 * Class Position
 *
 * @property string $id
 * @property string $department_id
 * @property string $title
 * @property string $short_title
 * @property string $description
 *
 * @package Module\Organization
 * @copyright 2012-2019 Medkey
 */
class Position extends ActiveRecord
{
    public static function modelIdentity()
    {
        return [
            'department_id',
            'title',
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [ ['department_id'], ForeignKeyValidator::class, ],
            [ ['title', 'department_id'], 'required', 'on' => [ActiveRecord::SCENARIO_CREATE, ActiveRecord::SCENARIO_UPDATE] ],
            [ ['title', 'short_title', 'description'], 'string', 'on' => [ActiveRecord::SCENARIO_CREATE, ActiveRecord::SCENARIO_UPDATE] ]
        ];
    }

    /**
     * @return \yii\db\ActiveQueryInterface
     */
    public function getDepartment()
    {
        return $this->hasOne(Department::className(), ['id' => 'department_id']);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabelsOverride()
    {
        return [
            'department_id' => OrganizationModule::t('common', 'Department'),
            'title' => OrganizationModule::t('common', 'Title'),
            'short_title' => OrganizationModule::t('common', 'Short title'),
            'description' => OrganizationModule::t('common', 'Description'),
        ];
    }
}
