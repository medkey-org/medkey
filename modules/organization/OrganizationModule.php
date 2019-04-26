<?php
namespace app\modules\organization;

use app\common\base\Module;

/**
 * Class OrganizationModule
 * @package Module\Organization
 * @copyright 2012-2019 Medkey
 */
class OrganizationModule extends Module
{
    /**
     * @inheritdoc
     */
    public static function translationList()
    {
        return [
            'employee',
            'department',
            'cabinet',
        ];
    }
}
