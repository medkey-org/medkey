<?php
namespace app\modules\security\assets;

use app\common\web\AssetBundle;

/**
 * Class SecurityAsset
 * @package Module\Security
 * @copyright 2012-2019 Medkey
 */
class SecurityAsset extends AssetBundle
{
    public $sourcePath = '@app/modules/security/resources';
    public $js = [
        'js/security.js',
    ];
    public $css = [
        'css/security.css',
    ];
    public $depends = [
        'app\assets\AppAsset',
    ];
}
