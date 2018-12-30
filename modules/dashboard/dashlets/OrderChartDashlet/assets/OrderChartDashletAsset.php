<?php
namespace app\modules\dashboard\dashlets\OrderChartDashlet\assets;

use app\common\web\AssetBundle;

/**
 * Class OrderChartDashletAsset
 * @package Module\Dashboard
 * @copyright 2012-2019 Medkey
 */
class OrderChartDashletAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@app/modules/dashboard/dashlets/OrderChartDashlet/resources';

    /**
     * @var array
     */
    public $js = [
        'js/dashlet.js',
    ];

    /**
     * @var array
     */
    public $css = [
    ];

    /**
     * @var array
     */
    public $depends = [
        'app\assets\AppAsset',
    ];
}
