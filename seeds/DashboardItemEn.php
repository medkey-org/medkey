<?php
namespace app\seeds;

use app\common\seeds\Seed;
use app\common\helpers\ArrayHelper;

class DashboardItemEn extends Seed
{
    public function run()
    {
        $dashboards = $this->call('dashboardEn')->models;
        $this->model = \app\modules\dashboard\models\orm\DashboardItem::class;

        $this->data = [
            [
                'title' => 'Order list',
                'dashboard_id' => ArrayHelper::findBy($dashboards, ['key' => 'default'])->id,
                'widget' => 'OrderListDashlet',
                'position' => 0,
                'order' => 0,
            ],
            [
                'title' => 'Patient list',
                'dashboard_id' => ArrayHelper::findBy($dashboards, ['key' => 'default'])->id,
                'widget' => 'PatientListDashlet',
                'position' => 0,
                'order' => 1,
            ],
            [
                'title' => 'EHR list',
                'dashboard_id' => ArrayHelper::findBy($dashboards, ['key' => 'default'])->id,
                'widget' => 'EhrListDashlet',
                'position' => 0,
                'order' => 2,
            ],
            [
                'title' => 'Order graph',
                'dashboard_id' => ArrayHelper::findBy($dashboards, ['key' => 'default'])->id,
                'widget' => 'OrderChartDashlet',
                'position' => 1,
                'order' => 0,
            ],
        ];
    }
}
