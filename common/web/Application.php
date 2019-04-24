<?php
namespace app\common\web;

use app\common\base\ModuleTrait;

/**
 * Class Application
 *
 *
 * @package Common\Web
 * @copyright 2012-2019 Medkey
 */
class Application extends \yii\web\Application
{
    use ModuleTrait;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->controllerNamespace = 'app\port';
        $this->_widgetNamespace = 'app\widgets';
        $this->_ormNamespace = 'app\models\orm';
        $this->_workflowNamespace = 'app\workflow';
        $this->_applicationNamespace = 'app\application';
        $this->_workflowHandlerNamespace = 'app\handlers';
        if ($this->dynamicModule) {
            $this->setDynamicModules($this->dynamicModuleDI);
        }
        parent::init();
        $this->language = $this->getUser()->getCurrentLang();
    }
}
