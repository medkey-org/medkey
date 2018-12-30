<?php
namespace app\modules\dashboard\models\specifications;

use app\modules\dashboard\models\orm\Dashboard;

/**
 * Dashboard specificaton for search operations
 * @package Module\Dashboard
 * @copyright 2012-2019 Medkey
 */
class DashboardSpecification
{
    private $query;


    /**
     * DashboardSpecification constructor.
     */
    public function __construct()
    {
        $this->query = Dashboard::find();
    }
}
