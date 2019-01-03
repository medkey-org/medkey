<?php
namespace app\assets;

use app\common\web\AssetBundle;

/**
 * Class UnderscoreAsset
 * @deprecated use webpack
 */
class UnderscoreAsset extends AssetBundle
{
    public $sourcePath = '@npm/underscore';
    public $js = [
        'underscore.js'
    ];
}
