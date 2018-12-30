<?php
namespace app\assets;

use app\common\web\AssetBundle;

/**
 * Class UnderscoreAsset
 * * @copyright 2012-2019 Medkey
 */
class UnderscoreAsset extends AssetBundle
{
    public $sourcePath = '@npm/underscore';
    public $js = [
        'underscore.js'
    ];
}
