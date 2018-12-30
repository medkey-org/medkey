<?php
namespace app\common\wrappers;

use app\common\widgets\WidgetClientInterface;
use app\common\helpers\Html;
use app\common\widgets\Modal;
use app\common\widgets\WidgetClientTrait;

/**
 * Class DynamicModal
 * @package Common\Wrappers
 * @copyright 2012-2019 Medkey
 *
 */
class DynamicModal extends Modal implements WidgetClientInterface, WrapperInterface
{
    use WidgetClientTrait;
    use WrapperTrait;

    /**
     * @var array
     */
    public $wrapperOptions = [];


    /**
     * Widget constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        if (is_array($config)) {
            $this->setConfig($config);
            parent::__construct($this->getConfig());
            $this->injectWrapperOptions();
        } else {
            parent::__construct();
        }
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->clientWrapperContainer = false;
        $this->clientParams = false;
        Html::addCssClass($this->headerOptions, 'b-header-modal');
        parent::init();
        $this->registerClient($this->getId());
        $this->clientOptions['show'] = true;
        $this->clientOptions['backdrop'] = 'static';
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        echo $this->renderContent();
        parent::run();
    }
}
