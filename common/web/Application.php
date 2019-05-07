<?php
namespace app\common\web;

use app\common\base\ModuleTrait;
use app\modules\config\models\orm\Config;

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
        $this->language = $this->sourceLanguage;
        $userLang = $this->getUser()->getCurrentLang();
        $conf = Config::find() // todo in service
            ->where(['key' => 'language'])
            ->one();
        if (!empty($userLang)) {
            $this->language = $userLang;
        } elseif (!empty($conf)) {
            $this->language = $conf->value;
        } elseif (locale_accept_from_http($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $this->language = locale_accept_from_http($_SERVER['HTTP_ACCEPT_LANGUAGE']);
        }
    }
}
