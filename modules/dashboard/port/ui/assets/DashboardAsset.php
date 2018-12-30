<?php
namespace app\modules\dashboard\port\ui\assets;

use app\common\web\AssetBundle;

/**
 * Class DashboardAsset
 * @package app\modules\dashboard\port\ui\assets
 * @package Module\Dashboard
 * @copyright 2012-2019 Medkey
 */
class DashboardAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@app/modules/dashboard/port/ui/resources';

    /**
     * @var array
     */
    public $js = [
        'js/module.js',
        'js/dashboard.js',
    ];

    /**
     * @var array
     */
    public $css = [
        'css/dashboard.css',
    ];

    /**
     * @var array
     */
    public $depends = [
        'app\assets\AppAsset',
        'app\modules\dashboard\dashlets\OrderChartDashlet\assets\OrderChartDashletAsset'
    ];
}
