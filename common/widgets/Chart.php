<?php
namespace app\common\widgets;

use app\common\base\Finder;
use app\common\helpers\Html;
use app\common\helpers\Json;
use app\modules\schedule\widgets\NoDataPlaceholderWidget;

/**
 * Class Chart
 * @package Common\Widgets
 * @copyright 2012-2019 Medkey
 *
 */
class Chart extends Widget
{
    /**
     * @var array
     */
    public $chartData;
    /**
     * @var array
     */
    public $chartConfig;
    /**
     * @var float
     */
    public $aspectRatio = 0.6;
    /**
     * @var bool
     */
    public $clientWrapperContainer = true;
    /**
     * @var bool
     */
    public $formatData = true;
    /**
     * @var string
     */
    public $callbackSuffix = '_CALLBACK';
    /**
     * @var string
     */
    public $parentSelector = '.chart-container';
    /**
     * @var int
     */
    public $widthOffset = 40;
    /**
     * @var Finder
     */
    public $filterModel;
    /**
     * @var array
     */
    public $options = ['class' => 'chart-container'];
    /**
     * @var array|string[]|null
     */
    public $colors = ['#a9856a', '#509526', '#f3d690', '#339275', '#7334c2', '#3e2d8f', '#f90357', '#855051', '#2927d2',
        '#cde130', '#ef0150', '#ef4c59', '#f06a49', '#49908c', '#681e3e', '#de970b', '#b337fd', '#4691eb', '#593270',
        '#7a8b65', '#5c9608', '#0aa815', '#9aa595', '#1f1b1e', '#373d46', '#6c16a8', '#7dd5a3', '#2c2ba1', '#178b9a',
        '#d62ec0', '#f4117f', '#a42fab', '#37d195', '#2c3685', '#69dbcb', '#db5715', '#7e7182', '#fbd855', '#844658',
        '#c4aa88', '#195c02', '#849a0c', '#c36fd0', '#f449c9', '#76300f', '#7563a7', '#00cbf3', '#4765b3', '#e9b9b3',
        '#7cf766'];

    /**
     * @inheritdoc
     */
    public function init()
    {
        if (is_null($this->chartData) && $this->filterModel) {
            if (!$this->filterModel instanceof Finder) {
                throw new \Exception('Invalid filterModel given: must be Finder');
            }

            $this->chartData = $this->filterModel->search()->getModels();
        }
        if (!is_null($this->chartData) && $this->formatData) {
            $this->chartData = $this->chartDataFormat($this->chartData);
        }

        $this->clientViewOptions = [
            'chartConfig' => Json::encode($this->chartConfig),
            'chartData' => $this->chartData,
            'aspectRatio' => $this->aspectRatio,
            'callbackSuffix' => $this->callbackSuffix,
            'parentSelector' => $this->parentSelector,
            'widthOffset' => $this->widthOffset
        ];

        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $content = Html::beginTag('div', ['class' => 'dashboard-widget']);

        if (!empty($this->chartData)) {
            $content .=  Html::tag('div', '', [
                'class' => 'chart',
            ]);
        } else {
            $content .= 'No data';
        }
        $content .=   Html::endTag('div');

        return $content;
    }

    /**
     * @param array $data
     * @return array
     */
    public function chartDataFormat(array $data)
    {
        return array_map([$this, 'chartDataFormatItem'], $data);
    }

    /**
     * @param array $item
     * @return array
     */
    public function chartDataFormatItem(array $item)
    {
        $item['color'] = ( isset($item['color']) ? $item['color'] : $this->nextColor() );

        return $item;
    }

    /**
     * @return string|null
     */
    public function nextColor()
    {
        if (!is_array($this->colors)) {
            return null;
        }
        $k = key($this->colors);

        if (!array_key_exists($k, $this->colors)) {
            return reset($this->colors);
        }
        return next($this->colors);
    }
}
