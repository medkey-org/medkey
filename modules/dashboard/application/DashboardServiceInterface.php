<?php
namespace app\modules\dashboard\application;

use app\common\dto\Dto;
use app\modules\dashboard\models\finders\DashboardFinder;

/**
 * Interface DashboardServiceInterface
 * @package Module\Dashboard
 * @copyright 2012-2019 Medkey
 */
interface DashboardServiceInterface
{
    /**
     * Get user's dashboards collection
     * @param DashboardFinder $filterModel
     * @return mixed
     */
    public function getCollectionForUserByFilterModel($filterModel = null);

    /**
     * @param Dto $dashboardDto
     * @return mixed
     */
    public function getOneForUser($dashboardDto);

    /**
     * @param null $filterModel
     * @return mixed
     */
    public function getAllCollectionByFilterModel($filterModel = null);

}
