<?php
namespace app\modules\dashboard\models\models;

use app\common\ddd\EntityInterface;

/**
 * Dashboard Item
 * @package Module\Dashboard
 * @copyright 2012-2019 Medkey
 */
class DashboardItem implements EntityInterface
{
    public $title;
    public $dashboardId;
    public $widget;
    public $position;
    public $order;
}
