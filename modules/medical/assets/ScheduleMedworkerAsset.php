<?php
namespace app\modules\medical\assets;

use app\common\web\AssetBundle;

/**
 * Class ScheduleAsset
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
class ScheduleMedworkerAsset extends AssetBundle
{
    public $sourcePath = '@app/modules/medical/resources';
    public $js = [
        'js/medical.js',
    ];
    public $css = [
        'css/medical.css',
    ];
}
