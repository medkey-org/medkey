<?php
namespace app\modules\crm\assets;

use app\assets\AppAsset;
use app\common\web\AssetBundle;

/**
 * Class BillingAsset
 * @package Module\CRM
 * @copyright 2012-2019 Medkey
 */
class OrderAsset extends AssetBundle
{
    public $sourcePath = '@app/modules/crm/resources';
    public $js = [
        'js/order.js',
    ];
    public $depends = [
        AppAsset::class,
    ];
}
