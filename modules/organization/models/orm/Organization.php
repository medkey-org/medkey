<?php
namespace app\modules\organization\models\orm;

use app\common\db\ActiveRecord;
use app\modules\organization\OrganizationModule;

/**
 * Organization ORM
 *
 * @property string $title
 * @property string $short_title
 * @property string $description
 *
 * @package Module\Organization
 * @copyright 2012-2019 Medkey
 */
class Organization extends ActiveRecord
{
    public static function modelIdentity()
    {
        return [
            'title',
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [ ['title'], 'required', 'on' => [ActiveRecord::SCENARIO_CREATE, ActiveRecord::SCENARIO_UPDATE] ],
            [ ['title', 'short_title', 'description'], 'string', 'on' => [ActiveRecord::SCENARIO_CREATE, ActiveRecord::SCENARIO_UPDATE] ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabelsOverride()
    {
        return [
            'title' => OrganizationModule::t('common', 'Title'),
            'short_title' => OrganizationModule::t('common', 'Short title'),
            'description' => OrganizationModule::t('common', 'Description'),
        ];
    }
}
