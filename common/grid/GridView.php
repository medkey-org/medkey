<?php
namespace app\common\grid;

use app\common\button\ActionButton;
use app\common\db\BaseFinder;
use app\common\helpers\ClassHelper;
use app\common\helpers\CommonHelper;
use app\common\widgets\DatePicker;
use app\common\widgets\WidgetClientTrait;
use app\common\widgets\WrapperAbleTrait;
use app\common\base\Model;
use app\common\widgets\WrapperAbleInterface;
use app\common\widgets\WidgetClientInterface;
use app\common\widgets\LinkSorter;
use app\common\helpers\Html;
use app\common\widgets\LinkPager;
use app\common\widgets\IdWidgetTrait;
use app\common\wrappers\DynamicModal;
use yii\grid\Column;
use yii\helpers\ArrayHelper;
use yii\widgets\BaseListView;
use app\common\button\ButtonGroup;

/**
 * Class GridView
 * @package Common\Grid
 * @copyright 2012-2019 Medkey
 */
class GridView extends \yii\grid\GridView implements WidgetClientInterface, WrapperAbleInterface
{
    use IdWidgetTrait;
    use WidgetClientTrait;
    use WrapperAbleTrait;

    /**
     * @var string
     */
    public $dataColumnClass = DataColumn::class;
    /**
     * @var BaseFinder
     */
    public $filterModel;
    /**
     * @var bool
     */
    public $visibleFilterRow = false;
    /**
     * @var array
     */
    public $tableOptions = ['class' => 'table table-bordered table-striped'];
    /**
     * @var array
     */
    public $wrapperOptions;
    /**
     * @var array|string
     */
    public $formData = [];
    /**
     * @var string
     */
    public $layout = "{items}\n{pager}";
    /**
     * @var string
     */
    public $actionButtonClass;
    /**
     * @var string
     */
    public $actionButtonTemplate = '{refresh}{view}{create}{update}{delete}{upload}{download}';
    /**
     * @var bool
     */
    public $visibleActionButtons = true;
    /**
     * @var bool
     */
    public $clickable = true;
    /**
     * @var array
     */
    public $actionButtons = [];
    /**
     * @var array
     */
    public $result;
    /**
     * @var bool
     */
    public $columnUpdatedAt = true;

    /**
     * GridView constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        if (is_array($config)) {
            $this->setConfig($config);
            $config = $this->getConfig();
            if (!empty($config['formData']) && is_string($config['formData'])) {
                parse_str($config['formData'], $config['formData']);
            }
            parent::__construct($config);
        } else {
            parent::__construct();
        }
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        if (!isset($this->clientClassNameDefault)) {
            $this->clientClassNameDefault = ClassHelper::getShortName(__CLASS__);
        }
        $this->clientWrapperContainer = false;
        if (empty($this->wrapperOptions)) {
            $this->wrapperOptions = $this->wrapperOptions();
        }
        if (($this->filterModel instanceof BaseFinder) && !isset($this->dataProvider)) {
            $params = [];
            if (!empty($this->formData) && is_string($this->formData)) {
                parse_str($this->formData, $params);
            } else {
                $params = $this->formData;
            }
            $this->dataProvider = $this->filterModel->search($params);
            $this->result = array_values($this->dataProvider->getModels());
        }
        if ($this->columnUpdatedAt) {
            array_push($this->columns, [
                'attribute' => 'updated_at',
                'label' => \Yii::t('app', 'Updated At'),
                'value' => function ($model) {
                    if (!empty($model['updated_at'])) {
                        return \Yii::$app->formatter->asDatetime($model['updated_at'] . date_default_timezone_get(), CommonHelper::FORMAT_DATETIME_UI);
                    }
                    return '';
                },
                'filter' => function () {
                    return Html::activeDateInput(
                        $this->filterModel,
                        'updatedAt',
                        [
                            'startAfterNow' => false,
                            'type' => DatePicker::TYPE_INPUT,
                        ]
                    );
                },
                'options' => [
                    'class' => 'col-xs-1'
                ],
            ]);
        }

        parent::init();
        $page = \Yii::$app->request->getQueryParam('page');
        $sort = \Yii::$app->request->getQueryParam('sort');
        empty($page) ?: $this->clientViewOptions['page'] = $page;
        empty($sort) ?: $this->clientViewOptions['sort'] = $sort;
        $this->clientViewOptions['clickable'] = $this->clickable;
        if ($this->clientView) {
            $this->registerClient($this->getId());
        }
    }

	/**
     * @inheritdoc
     */
    public function renderTableBody()
    {
        $models = $this->result ? $this->result : array_values($this->dataProvider->getModels());
        $keys = $this->dataProvider->getKeys();
        $rows = [];
        foreach ($models as $index => $model) {
            $key = $keys[$index];
            if ($this->beforeRow !== null) {
                $row = call_user_func($this->beforeRow, $model, $key, $index, $this);
                if (!empty($row)) {
                    $rows[] = $row;
                }
            }

            $rows[] = $this->renderTableRow($model, $key, $index);

            if ($this->afterRow !== null) {
                $row = call_user_func($this->afterRow, $model, $key, $index, $this);
                if (!empty($row)) {
                    $rows[] = $row;
                }
            }
        }

        if (empty($rows)) {
            $colspan = count($this->columns);

            return "<tbody class=\"empty\">\n<tr><td colspan=\"$colspan\">" . $this->renderEmpty() . "</td></tr>\n</tbody>";
        } else {
            return "<tbody>\n" . implode("\n", $rows) . "\n</tbody>";
        }
    }

	/**
	 * @param Model $model
	 * @param mixed $key
	 * @param int   $index
	 * @return string
	 */
	public function renderTableRow($model, $key, $index)
	{
        $cells = [];
        /* @var $column Column */
        foreach ($this->columns as $column) {
            $cells[] = $column->renderDataCell($model, $key, $index);
        }
        if ($this->rowOptions instanceof \Closure) {
            $options = call_user_func($this->rowOptions, $model, $key, $index, $this);
        } else {
            $options = $this->rowOptions;
        }
        $options['data-key'] = is_array($key) ? json_encode($key) : (string) $key;

        return Html::tag('tr', implode('', $cells), $options);
	}

    /**
     * @inheritdoc
     */
    public function renderCaption()
    {
        $this->captionOptions = array_merge([
            'class' => 'grid-view__caption'
        ], $this->captionOptions);
        if (!$this->actionButtons) {
            $this->actionButtons = [];
        }
        if (!$this->visibleActionButtons) {
            $this->actionButtons = [];
        }
        $this->actionButtons = array_merge([
            'refresh' => [
                'value' => function () {
                    return Html::button('', [
                        'class' => 'btn btn-default btn-xs grid-update',
                        'icon' => 'refresh'
                    ]);
                }
            ]
        ], $this->actionButtons);
        $config = [
            'class' => ButtonGroup::class,
            'buttonTemplate' => $this->actionButtonTemplate,
            'buttons' => $this->actionButtons,
            'defaultButtonClass' => ActionButton::class,
            'buttonConfig' => [
                'afterUpdateBlock' => $this,
            ],
        ];
        $buttonGroup = \Yii::createObject($config);
        $this->caption = $buttonGroup->render();

        return parent::renderCaption();
    }

    /**
     * Remove js yii2
     */
    public function run()
    {
        BaseListView::run();
    }

    /**
     * @inheritdoc
     */
    public function renderTableHeader()
    {
        $cells = [];
        foreach ($this->columns as $column) {
            /* @var $column Column */
            $cells[] = $column->renderHeaderCell();
        }
        $content = Html::tag('tr', implode('', $cells), $this->headerRowOptions);
        if ($this->visibleFilterRow && $this->filterPosition === self::FILTER_POS_HEADER) {
            $content = $this->renderFilters() . $content;
        } elseif ($this->visibleFilterRow && $this->filterPosition === self::FILTER_POS_BODY) {
            $content .= $this->renderFilters();
        }

        return "<thead>\n" . $content . "\n</thead>";
    }

    /**
     * @inheritdoc
     */
    public function renderTableFooter()
    {
        $cells = [];
        foreach ($this->columns as $column) {
            /* @var $column Column */
            $cells[] = $column->renderFooterCell();
        }
        $content = Html::tag('tr', implode('', $cells), $this->footerRowOptions);
        if ($this->visibleFilterRow && $this->filterPosition === self::FILTER_POS_FOOTER) {
            $content .= $this->renderFilters();
        }

        return "<tfoot>\n" . $content . "\n</tfoot>";
    }

    /**
     * @inheritdoc
     */
    public function renderPager()
    {
        $pagination = $this->dataProvider->getPagination();
        if ($pagination === false || $this->dataProvider->getCount() <= 0) {
            return '';
        }
        /* @var $class LinkPager */
        $pager = $this->pager;
        $class = ArrayHelper::remove($pager, 'class', LinkPager::className());
        $pager['pagination'] = $pagination;
        $pager['view'] = $this->getView();

        return $class::widget($pager);
    }

    /**
     * @inheritdoc
     */
    public function renderSorter()
    {
        $sort = $this->dataProvider->getSort();
        if ($sort === false || empty($sort->attributes) || $this->dataProvider->getCount() <= 0) {
            return '';
        }
        /* @var $class LinkSorter */
        $sorter = $this->sorter;
        $class = ArrayHelper::remove($sorter, 'class', LinkSorter::className());
        $sorter['sort'] = $sort;
        $sorter['view'] = $this->getView();

        return $class::widget($sorter);
    }

    /**
     * @inheritdoc
     */
    public function wrapperOptions()
    {
        return [
            'wrapperClass' => DynamicModal::className(),
            'header' => 'List',
        ];
    }
}
