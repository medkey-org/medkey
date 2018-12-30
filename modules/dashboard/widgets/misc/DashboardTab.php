<?php
namespace app\modules\dashboard\widgets\misc;

use app\common\tab\TabView;
use app\modules\dashboard\application\DashboardServiceInterface;

/**
 * Class DashboardTab
 * @package Module\Dashboard
 * @copyright 2012-2019 Medkey
 */
abstract class DashboardTab extends TabView
{
    /**
     * @var DashboardServiceInterface
     */
    protected $dashboardService;


    /**`
     * @param DashboardServiceInterface $dashboardService
     * @param array $config
     */
    public function __construct(DashboardServiceInterface $dashboardService, array $config = [])
    {
        $this->dashboardService = $dashboardService;
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->dataProvider = $this->getCollectionForUser();

        $this->actionButtonTemplate = '{refresh}';
        $this->detailClass = '\app\modules\dashboard\widgets\DashboardDetail';
        $this->titleColumn = 'title';

        parent::init();
    }

    /**
     * @return mixed
     */
    abstract protected function getCollectionForUser();
}
