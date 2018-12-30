<?php
namespace app\modules\dashboard\port\ui\controllers;

use Yii;
use app\common\base\Module;
use app\modules\dashboard\application\DashboardServiceInterface;
use app\common\web\CrudController;
use app\common\dto\Dto;

/**
 * Class DashboardController
 * @package Module\Dashboard
 * @copyright 2012-2019 Medkey
 */
class DashboardController extends CrudController
{
    /**
     * @var DashboardServiceInterface
     */
    private $dashboardService;


    /**
     * @param string $id
     * @param DashboardServiceInterface $dashboardService
     * @param Module $module
     * @param array $config
     */
    public function __construct($id, Module $module, DashboardServiceInterface $dashboardService, array $config = [])
    {
        $this->dashboardService = $dashboardService;
        parent::__construct($id, $module, $config);
    }

    /**
     * View user dashboards screen
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * View dashbaord
     * @param null $id
     * @return string
     * @throws
     */
    public function actionView($id = null)
    {
        $dto = new Dto([
            'id' => $id,
        ]);

        $model = $this->dashboardService->getOneForUser($dto);

        return $this->render('view', compact('model'));
    }

    /**
     * @return string
     */
    public function actionList()
    {
        return $this->render('list', [
            'dataProvider' => $this->dashboardService->getAllCollectionByFilterModel(),
        ]);
    }
}
