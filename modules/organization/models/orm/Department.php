<?php
namespace app\modules\organization\models\orm;

use app\common\db\ActiveRecord;
use app\common\validators\ForeignKeyValidator;
use app\modules\organization\OrganizationModule;

/**
 * Department ORM
 *
 * @property int $organization_id
 * @property string $title
 * @property string $short_title
 * @property string $description
 *
 * @package Module\Organization
 * @copyright 2012-2019 Medkey
 */
class Department extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [ ['organization_id',], ForeignKeyValidator::class, ],
            [ ['organization_id', 'title'], 'required', 'on' => [ActiveRecord::SCENARIO_CREATE, ActiveRecord::SCENARIO_UPDATE] ],
            [ ['title', 'short_title', 'description'], 'string', 'on' => [ActiveRecord::SCENARIO_CREATE, ActiveRecord::SCENARIO_UPDATE] ]
        ];
    }

    /**
     * @return \yii\db\ActiveQueryInterface
     */
    public function getOrganization()
    {
        return $this->hasOne(Organization::className(), ['id' => 'organization_id']);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabelsOverride()
    {
        return [
            'organization_id' => 'Организация',
            'department_id' => 'Подразделение',
            'title' => 'Название',
            'short_title' => 'Короткое название',
            'description' => 'Описание',
        ];
    }
}
