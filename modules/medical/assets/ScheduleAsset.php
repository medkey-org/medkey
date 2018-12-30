<?php
namespace app\modules\medical\assets;

use app\assets\ReactJs;
use app\common\web\AssetBundle;

/**
 * Class ScheduleAsset
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
class ScheduleAsset extends AssetBundle
{
    public $sourcePath = '@app/modules/medical/resources';
    public $css = [
        'css/react/schedule.css'
    ];
    public $js = [
        'js/react/Schedule.js'
    ];
    public $jsOptions = [
        'type' => 'text/babel'
    ];
    public $depends = [
        ReactJs::class,
    ];
}
