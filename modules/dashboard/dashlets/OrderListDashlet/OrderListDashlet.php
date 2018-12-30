<?php
namespace app\modules\dashboard\dashlets\OrderListDashlet;

use app\modules\crm\widgets\grid\OrderGrid;
use app\modules\dashboard\widgets\Dashlet;

/**
 * Class OrderListDashlet
 * @package Module\Dashboard
 * @copyright 2012-2019 Medkey
 */
class OrderListDashlet extends Dashlet
{
    /**
     * @inheritdoc
     */
    public function run()
    {
        return OrderGrid::widget();
    }
}
