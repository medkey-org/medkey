<?php
namespace app\modules\organization\assets;

use app\common\web\AssetBundle;

/**
 * Class OrganizationAsset
 * @package Module\Organization
 * @copyright 2012-2019 Medkey
 */
class OrganizationAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@app/modules/organization/resources';

    /**
     * @var array
     */
    public $js = [
        'js/organization.js',
    ];

    /**
     * @var array
     */
    public $css = [
        'css/organization.css',
    ];

    /**
     * @var array
     */
    public $depends = [
        'app\assets\AppAsset',
    ];
}
