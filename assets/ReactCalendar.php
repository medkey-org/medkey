<?php
namespace app\assets;

use app\common\web\AssetBundle;

/**
 * Class ReactCalendar
 * @deprecated use webpack
 */
class ReactCalendar extends AssetBundle
{
    public $js = [
        'https://unpkg.com/react-calendar@2.14.0/dist/entry.js',
    ];
}
