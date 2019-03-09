<?php
namespace app\common\base;

/**
 * Class Module
 * @package Common\Base
 * @copyright 2012-2019 Medkey
 */
class Module extends \yii\base\Module
{
    use ModuleTrait;

    /**
     * @inheritdoc
     */
    public function init()
    {
        if ($this->controllerNamespace === null) {
            $class = get_class($this);
            if (($pos = strrpos($class, '\\')) !== false) {
                // меняем controllers->port и используем prefix
                if (php_sapi_name() === 'cli') {
                    $this->controllerNamespace = substr($class, 0, $pos) . '\\commands';
                }
                $this->controllerNamespace = substr($class, 0, $pos) . '\\port';
            }
        }
        if ($this->_widgetNamespace === null) {
            $class = get_class($this);
            if (($pos = strrpos($class, '\\')) !== false) {
                $this->_widgetNamespace = substr($class, 0, $pos) . '\\widgets';
            }
        }
        if ($this->_ormNamespace === null) {
            $class = get_class($this);
            if (($pos = strrpos($class, '\\')) !== false) {
                $this->_ormNamespace = substr($class, 0, $pos) . '\\models\orm';
            }
        }
        if ($this->_workflowNamespace === null) {
            $class = get_class($this);
            if (($pos = strrpos($class, '\\')) !== false) {
                $this->_workflowNamespace = substr($class, 0, $pos) . '\\workflow';
            }
        }
        if ($this->_applicationNamespace === null) {
            $class = get_class($this);
            if (($pos = strrpos($class, '\\')) !== false) {
                $this->_applicationNamespace = substr($class, 0, $pos) . '\\application';
            }
        }
        if ($this->_workflowHandlerNamespace === null) {
            $class = get_class($this);
            if (($pos = strrpos($class, '\\')) !== false) {
                $this->_workflowHandlerNamespace = substr($class, 0, $pos) . '\\handlers';
            }
        }
        parent::init();
    }
}
