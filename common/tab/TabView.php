<?php
namespace app\common\tab;

use app\common\base\Model;
use app\common\button\ActionButton;
use app\common\button\ButtonGroup;
use app\common\db\ActiveRecord;
use app\common\helpers\ArrayHelper;
use app\common\helpers\ClassHelper;
use app\common\helpers\Html;
use app\common\widgets\ListView;
use app\common\widgets\Widget;
use yii\data\ArrayDataProvider;

/**
 * Class TabView
 *
 * @property-read Model|ActiveRecord|array $detailModel
 *
 * @package Common\Tab
 * @copyright 2012-2019 Medkey
 */
class TabView extends ListView
{
    /**
     * @var string
     */
    public $layout = "{items}\n{pager}";
    /**
     * @var string
     */
    public $titleColumn;
    /**
     * @var string
     */
    public $detailClass = '\app\common\widgets\DetailView';
    /**
     * @var array
     */
    public $detailOptions = [];
    /**
     * @var int
     */
    public $activeModelId;
    /**
     * @var bool
     */
    public $clientWrapperContainer = true;
    /**
     * @var string
     */
    public $actionButtonTemplate = '{refresh}';
    /**
     * @var array
     */
    public $actionButtons = [];
    /**
     * @var Model[]|ActiveRecord[]|array[]
     */
    public $items = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        if (!isset($this->clientClassNameDefault)) {
            $this->clientClassNameDefault = ClassHelper::getShortName(__CLASS__);
        }

	    if (!$this->titleColumn) {
            $this->titleColumn = 'title'; // default on array items mode
            $this->dataProvider = new ArrayDataProvider([
                'allModels' => $this->items,
            ]);
	    }
        parent::init();

        $this->dataProvider->pagination = false;

        $this->getId();

        $this->actionButtons = array_merge([
            'refresh' => [
                'value' => function ($disabled) {
                    return Html::button(\Yii::t('app','Refresh'), [
                        'class' => 'btn btn-default tab-update',
                        'icon' => 'refresh',
                        'disabled' => $disabled,
                    ]);
                },
                'disabled' => false,
            ]
        ], $this->actionButtons);
    }

    /**
     * @return string
     */
    public function renderItems()
    {
        $models = $this->dataProvider->getModels();

        $buttonContent = $this->renderButtons();

        if (!$this->activeModelId && !empty($models)) {
            $this->activeModelId = ArrayHelper::getValue($models[0], 'id');
        }
        $tabs = [];

        foreach ($models as $index => $model) {
            $tabs[] = $this->renderTab($model);
        }

        $tabNavigationContent = Html::beginTag('div',['class' => 'sttabs tabs-style-linebox']) .
                        Html::beginTag('nav') .
                            Html::tag('ul', implode("\n", $tabs), ['class' => 'navbar-right', 'role' => 'tablist']) .
                        Html::endTag('nav') .
                    Html::endTag('div');

        $tabContent = $this->renderModel();

        $content =
            Html::beginDiv(['class' => 'row'])
                    . Html::beginDiv(['class' => 'col-md-6']) . $buttonContent . Html::endDiv()
                    . Html::beginDiv(['class' => 'col-md-6']) . $tabNavigationContent . Html::endDiv()
                . Html::endDiv() .
            $tabContent;

        return $content;
    }

    /**
     * @return Model|ActiveRecord|array
     */
    public function getDetailModel()
    {
        return ArrayHelper::findBy($this->dataProvider->getModels(), [
            'id' => $this->activeModelId,
        ]);
    }

	/**
	 * @param Model|ActiveRecord|array $model
	 * @return string
	 */
    public function renderTab($model)
    {
    	$id = ArrayHelper::getValue($model, 'id');
    	$title = ArrayHelper::getValue($model, $this->titleColumn);

        return Html::beginTag('li', [
                'role' => 'presentation',
                'class' => $id == $this->activeModelId ? ' active tab-current' : ''
            ]) .
            Html::tag('a', $title, ['data-tab-id' => $id, 'data-tab-view' => $this->getId()]) .
            Html::endTag('li');
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function renderModel()
    {
        if (!$this->detailModel) {
            return '';
        }
        if (!class_exists($this->detailClass)) {
            throw new \Exception('Detail Widget not found.');
        }
        /** @var Widget $detailClass */
        $detailClass = $this->detailClass;

        return $detailClass::widget(array_merge($this->detailOptions, [
            'model' => $this->detailModel,
        ]));
    }

    /**
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function renderButtons()
    {
        $config = [
            'class' => ButtonGroup::className(),
            'buttonTemplate' => $this->actionButtonTemplate,
            'buttons' => $this->actionButtons,
            'defaultButtonClass' => ActionButton::className(),
            'buttonConfig' => [
                'afterUpdateBlock' => $this,
            ],
        ];
        $buttonGroup = \Yii::createObject($config);

        return Html::tag('div', $buttonGroup->render(), ['class' => 'tab-buttons']);
    }
}
