<?php
namespace app\modules\organization\models\orm;

use app\common\db\ActiveRecord;
use app\common\validators\ForeignKeyValidator;
use app\modules\organization\OrganizationModule;

/**
 * Class Cabinet
 * @package Module\Organization
 * @copyright 2012-2019 Medkey
 */
class Cabinet extends ActiveRecord
{
    public function rules()
    {
        // todo возможно required organization_id и department_id
        return [
            [ ['number'], 'unique', 'targetAttribute' => ['number', 'department_id'] ],
            [ ['number'], 'string' ], // todo возможно не только int
            [ ['number'], 'required' ],
            [ ['description'], 'string' ],
            [ ['organization_id', 'department_id'], ForeignKeyValidator::class, ],
        ];
    }

    public function getOrganization()
    {
        return $this->hasOne(Organization::className(), ['id' => 'organization_id']);
    }

    public function getDepartment()
    {
        return $this->hasOne(Department::className(), ['id' => 'department_id']);
    }

    public function attributeLabelsOverride()
    {
        return [
            'number' => OrganizationModule::t('cabinet', 'Number'),
            'description' =>  OrganizationModule::t('cabinet', 'Description'),
            'organization_id' =>  OrganizationModule::t('cabinet', 'Organization'),
            'department_id' =>  OrganizationModule::t('cabinet', 'Department'),
        ];
    }
}
