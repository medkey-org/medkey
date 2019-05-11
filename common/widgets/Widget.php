<?php
namespace app\common\widgets;

use app\common\helpers\ClassHelper;
use app\common\wrappers\DynamicModal;

/**
 * Class Widget
 *
 * @property-read string $module
 *
 * @property mixed $model
 *
 * @package Common\Widgets
 * @copyright 2012-2019 Medkey
 */
class Widget extends \yii\bootstrap\Widget implements WidgetClientInterface, WrapperAbleInterface
{
    use IdWidgetTrait;
    use WidgetClientTrait;
    use WrapperAbleTrait;

    /**
     * @var array
     */
    public $wrapperOptions;
    /**
     * @var string
     */
    private $_module;

    /**
     * Widget constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->_module = ClassHelper::getMatchModule($this, false, '/');
        if (is_array($config)) {
            $this->setConfig($config);
            parent::__construct($this->getConfig());
        } else {
            parent::__construct();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        if (empty($this->wrapperOptions)) {
            $this->wrapperOptions = $this->wrapperOptions();
        }
        $this->options['id'] = $this->getId();
        if ($this->clientView) {
             $this->registerClient($this->getId());
        }
    }

    /**
     * @return string
     */
    public function getModule()
    {
        return $this->_module;
    }

    /**
     * {@inheritdoc}
     */
    public function wrapperOptions()
    {
        return [
            'wrapperClass' => DynamicModal::class,
        ];
    }
}
