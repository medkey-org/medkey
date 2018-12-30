<?php
namespace app\assets;

use app\common\web\AssetBundle;

/**
 * Class BackboneAsset
 */
class BackboneAsset extends AssetBundle
{
    public $sourcePath = '@npm/backbone';
    public $js = [
        'backbone.js',
    ];
    public $depends = [
        'app\assets\UnderscoreAsset',
    ];
}
