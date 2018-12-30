<?php
namespace app\modules\dashboard\widgets\misc;

use app\modules\dashboard\widgets\misc\DashboardTab;

/**
 * Class DashboardTab
 * @package Module\Dashboard
 * @copyright 2012-2019 Medkey
 */
class UserDashboardTab extends DashboardTab
{
    /**
     * @return mixed
     */
    protected function getCollectionForUser()
    {
        return $this->dashboardService->getCollectionForUser();
    }
}
