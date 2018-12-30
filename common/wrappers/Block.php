<?php
namespace app\common\wrappers;

use app\common\helpers\ClassHelper;
use app\common\helpers\Html;
use app\common\widgets\Widget;

/**
 * Class Block
 * @package Common\Wrappers
 * @copyright 2012-2019 Medkey
 */
class Block extends Widget implements WrapperInterface
{
    use WrapperTrait;

    /**
     * @var bool
     */
    public $prevButton = false;
    /**
     * @var string
     */
    public $header = '';
    /**
     * @var string
     */
    public $headerTag = 'h4';
    /**
     * @var bool
     */
    public $backdrop = true;
    /**
     * @var bool
     */
    public $clientView = false;


    /**
     * @inheritdoc
     */
    public function init()
    {
        if (!isset($this->clientClassNameDefault)) {
            $this->clientClassNameDefault = ClassHelper::getShortName(__CLASS__);
        }
        Html::addCssClass($this->options, 'b-block');
        if ($this->backdrop) {
            Html::addCssClass($this->options, 'b-block__backdrop');
        }
        if (!empty($this->header)) {
            echo Html::tag($this->headerTag, $this->header, [
                'class' => 'b-block__title',
            ]);
        }
        $this->injectWrapperOptions();
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        if ($this->prevButton) {
            echo Html::a('Go back', \Yii::$app->request->referrer, [
                'class' => 'btn btn-default',
                'id' => 'prev-button',
            ]);
        }
        echo $this->renderContent();
    }
}
