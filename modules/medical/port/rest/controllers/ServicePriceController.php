<?php
namespace app\modules\medical\port\rest\controllers;

use app\common\rest\Controller;
use app\common\widgets\ActiveForm;
use app\modules\medical\application\ServicePriceServiceInterface;
use app\modules\medical\models\form\ServicePrice as ServicePriceForm;
use yii\base\Module;

/**
 * Class ServicePriceController
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
class ServicePriceController extends Controller
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

    public function actionGetActivePriceByServiceId($id)
    {
        return $this->asJson($this->servicePriceService->getActivePriceByServiceId($id));
    }

    public function actionCreate()
    {
        $servicePriceForm = new ServicePriceForm([
            'scenario' => 'create',
        ]);
        $servicePriceForm->load(\Yii::$app->getRequest()->getBodyParams());
        return $this->asJson($this->servicePriceService->createServicePrice($servicePriceForm));
    }

    public function actionValidateCreate()
    {
        $servicePriceForm = new ServicePriceForm([
            'scenario' => 'create',
        ]);
        $servicePriceForm->load(\Yii::$app->getRequest()->getBodyParams());
        return $this->asJson(ActiveForm::validate($servicePriceForm));
    }

    public function actionUpdate($id)
    {
        $servicePriceForm = new ServicePriceForm([
            'scenario' => 'update',
        ]);
        $servicePriceForm->load(\Yii::$app->getRequest()->getBodyParams());
        return $this->asJson($this->servicePriceService->updateServicePrice($id, $servicePriceForm));
    }

    public function actionValidateUpdate()
    {
        $servicePriceForm = new ServicePriceForm([
            'scenario' => 'update',
        ]);
        $servicePriceForm->load(\Yii::$app->getRequest()->getBodyParams());
        return $this->asJson(ActiveForm::validate($servicePriceForm));
    }
}
