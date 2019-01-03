<?php
namespace app\assets;

use app\common\web\AssetBundle;

/**
 * Class ReactJs
 * @deprecated use webpack
 */
class ReactJs extends AssetBundle
{
    public $js = [
        'https://unpkg.com/react@16/umd/react.development.js',
        'https://unpkg.com/react-dom@16/umd/react-dom.development.js',
        'https://unpkg.com/babel-standalone@6.15.0/babel.min.js',
    ];
}
