<?php
namespace app\modules\dashboard\widgets\grid;

use app\common\grid\GridView;
use app\modules\dashboard\models\finders\DashboardItemFinder;
use app\modules\dashboard\models\orm\Dashboard;

/**
 * Class DashboardItemGrid
 * @package Module\Dashboard
 * @copyright 2012-2019 Medkey
 */
class DashboardItemGrid extends GridView
{
    /**
     * @var Dashboard
     */
    public $model;
    /**
     * @var string
     */
    public $dashboardId;
    /**
     * @var array
     */
    public $actionButtonPermissions = [
        'add' => true,
        'delete' => true
    ];


    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->filterModel = DashboardItemFinder::ensure($this->filterModel, 'search');
        $this->model = Dashboard::ensure($this->model);
        $this->filterModel->dashboardId = $this->model->id;

        $this->columns = [
            [
                'attribute' => 'title',
            ],
            [
                'attribute' => 'position',
            ],
            [
                'attribute' => 'order',
            ],
            [
                'attribute' => 'widget'
            ]

        ];

        parent::init();
    }
}
