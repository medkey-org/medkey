<?php
namespace app\common\web;

/**
 * Class AssetBundle
 * @package Common\Web
 * @copyright 2012-2019 Medkey
 */
class AssetBundle extends \yii\web\AssetBundle
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->publishOptions['forceCopy'] = YII_DEBUG;
        parent::init();
    }
}
