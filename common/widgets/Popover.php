<?php
namespace app\common\widgets;
use app\common\helpers\Json;
use yii\bootstrap\BootstrapPluginAsset;

/**
 * Class Popover
 * @package Common\Widgets
 * @copyright 2012-2019 Medkey
 *
 */
class Popover extends Widget
{
    /**
     * @var string
     */
    public $pluginName = 'popover';
    /**
     * @var string
     */
    public $selector;
    /**
     * @var bool
     */
    public $clientView = false;


    public function init()
    {
        ob_start();
        ob_implicit_flush(false);
        parent::init();
        $this->clientOptions = [
            'trigger' => 'click',
        ];
    }

    public function run()
    {
        $content = ob_get_clean();
        $this->clientOptions['container'] = 'body';
        $this->clientOptions['trigger'] = 'click';
        $this->clientOptions['html'] = true;
        $this->clientOptions['content'] = $content;
        $this->registerPlugin($this->pluginName);
    }

    protected function registerPlugin($name)
    {
        $view = $this->getView();
        BootstrapPluginAsset::register($view);
        if ($this->selector) {
            $id = '#' . $this->selector;
        } else {
            $id = '#' . $this->id;
        }
        if ($this->clientOptions !== false) {
            $event = $this->clientOptions['trigger'];
            $options = empty($this->clientOptions) ? '' : Json::htmlEncode($this->clientOptions);
            $js = "jQuery('$id').$name($options);";
            $view->registerJs($js);
            $js = "jQuery('$id').trigger('click')";
            $view->registerJs($js);
        }
        $this->registerClientEvents();
    }
}
