<?php
namespace app\modules\dashboard\widgets;

use app\common\base\InitTrait;
use app\common\helpers\ClassHelper;
use app\common\web\AssetBundle;
use app\common\widgets\Widget;
use app\modules\dashboard\dashlets\UnitSummaryDashlet\UnitSummaryDashlet;
use app\modules\dashboard\widgets\DashletInterface;

/**
 * Trait DashletTrait
 *
 * @mixin DashletInterface
 * @mixin Widget
 * @mixin InitTrait
 *
 * @package Module\Dashboard
 * @copyright 2012-2019 Medkey
 */
trait DashletTrait
{
    /**
     * @var string
     */
    public $_assetClass;
    /**
     * @var integer
     */
    public $dashboardItemId;


    /**
     * @see InitTrait
     */
    public function initDashletTrait()
    {
        if (class_exists($this->assetClass)) {
            $this->registerClientScript();
        }

        
        $this->options['data-dashlet-id'] = $this->dashboardItemId;
    }

    /**
     * @return void
     */
    public function registerClientScript()
    {
        /** @var AssetBundle $assetClass */
        $assetClass = $this->assetClass;
        $this->getView()->registerAssetBundle($assetClass::className());
    }

    /**
     * @return string
     */
    public function getAssetClass()
    {
        if (!$this->_assetClass) {
            $this->_assetClass = ClassHelper::getNamespace(static::className())
                . '\assets\\' . ClassHelper::getShortName(static::className()) . 'Asset';
        }

        return $this->_assetClass;
    }

    /**
     * @return string
     */
    public function setAssetClass($val)
    {
        $this->_assetClass = $val;
    }
}
