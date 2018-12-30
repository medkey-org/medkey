<?php
namespace app\modules\dashboard\dashlets\OrderChartDashlet\widgets;

use app\common\data\ActiveDataProvider;
use app\common\db\ActiveRecord;
use app\common\widgets\Widget;
use app\modules\dashboard\dashlets\IncidentsDashlet\models\IncidentsTypesFinder;

/**
 * Class Order
 * @package Module\Dashboard
 * @copyright 2012-2019 Medkey
 */
class Order extends Widget
{
    /**
     * @var ActiveRecord
     */
    public $filterModel;
    /**
     * @var array
     */
    public $pageData = [];
    /**
     * @var array
     */
    public $colors = ['#a9856a', '#509526', '#f3d690', '#339275', '#7334c2', '#3e2d8f', '#f90357', '#855051', '#2927d2', '#cde130', '#ef0150', '#ef4c59', '#f06a49', '#49908c', '#681e3e', '#de970b', '#b337fd', '#4691eb', '#593270', '#7a8b65', '#5c9608', '#0aa815', '#9aa595', '#1f1b1e', '#373d46', '#6c16a8', '#7dd5a3', '#2c2ba1', '#178b9a', '#d62ec0', '#f4117f', '#a42fab', '#37d195', '#2c3685', '#69dbcb', '#db5715', '#7e7182', '#fbd855', '#844658', '#c4aa88', '#195c02', '#849a0c', '#c36fd0', '#f449c9', '#76300f', '#7563a7', '#00cbf3', '#4765b3', '#e9b9b3', '#7cf766'];
    /**
     * @var bool
     */
    public $clientWrapperContainer = true;

    /**
     * @inheritdoc
     */
    public function init() {
        parent::init();

        $this->filterModel = IncidentsTypesFinder::ensure($this->filterModel);

        $dataProvider = $this->filterModel->search();

        if ($dataProvider) {
            $dataProvider->pagination = false;
            /** @var ActiveDataProvider $dataProvider */
            $this->pageData = $dataProvider->getModels();
        }
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $incidentTypes = $this->getIncidentsTypes();

        return OrderChart::widget([
                'chartData' => $incidentTypes
            ]);
    }

    /**
     * @return array
     */
    public function getIncidentsTypes()
    {
        $index = [];

        $total = 0;

        $others = ['label' => 'Другие < 2%', 'data' => 0, 'color' => '#6c7a89'];

        array_map(function($item) use (&$total) {
            $total += $item['count'];
        }, $this->pageData);

        array_map(function($item) use (&$index, $total, &$others) {
            if (($item['count'] / ($total / 100)) > ceil(($total / 100) * 2)) { // TODO: Config percentage
                $index[] = [
                    'label' => $item['title'],
                    'data' => (int)$item['count'],
                    'color' => $this->colors[$item['type_id'] % sizeof($this->colors)],
                ];
            } else {
                $others['data'] += (int)$item['count'];
            }

        }, $this->pageData);

        if ($others['data']) {
            $index[] = $others;
        }

        if (empty($index)) { // Prevent error on empty
            $index = [[]];
        }

        return $index;
    }
}
