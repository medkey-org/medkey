<?php
namespace app\modules\security;

use \app\common\base\Module;

/**
 * Class SecurityModule
 * @package Module\Security
 * @copyright 2012-2019 Medkey
 */
class SecurityModule extends Module
{
    /**
     * @inheritdoc
     */
    public static function translationList()
    {
        return [
            'role',
            'user',
            'acl',
        ];
    }
}
