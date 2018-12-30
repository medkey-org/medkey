<?php
namespace app\modules\medical\port\ui\controllers;

use yii\base\Module;
use app\common\web\Controller;
use app\modules\medical\application\ServicePriceServiceInterface;

/**
 * Class ServicePriceListController
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
class ServicePriceListController extends Controller
{
    /**
     * @var ServicePriceServiceInterface
     */
    public $servicePriceService;


    /**
     * ServicePriceListController constructor.
     * @param string $id
     * @param Module $module
     * @param ServicePriceServiceInterface $servicePriceService
     * @param array $config
     */
    public function __construct($id, Module $module, ServicePriceServiceInterface $servicePriceService, array $config = [])
    {
        $this->servicePriceService = $servicePriceService;
        parent::__construct($id, $module, $config);
    }

    /**
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * @param string|int $id
     * @return string
     */
    public function actionView($id = null)
    {
        $model = $id;
        return $this->render('view', compact('model'));
    }
}
