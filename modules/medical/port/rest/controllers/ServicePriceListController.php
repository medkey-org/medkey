<?php
namespace app\modules\medical\port\rest\controllers;

use app\common\rest\Controller;
use app\common\widgets\ActiveForm;
use app\modules\medical\application\ServicePriceServiceInterface;
use app\modules\medical\models\form\ServicePriceList as ServicePriceListForm;
use yii\base\Module;

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

    public function actionCreate()
    {
        $servicePriceListForm = new ServicePriceListForm([
            'scenario' => 'create',
        ]);
        $servicePriceListForm->load(\Yii::$app->getRequest()->getBodyParams());
        return $this->asJson($this->servicePriceService->createServicePriceList($servicePriceListForm));
    }

    public function actionUpdate($id)
    {
        $servicePriceListForm = new ServicePriceListForm([
            'scenario' => 'create',
        ]);
        $servicePriceListForm->load(\Yii::$app->getRequest()->getBodyParams());
        return $this->asJson($this->servicePriceService->updateServicePriceList($id, $servicePriceListForm));
    }

    public function actionValidateCreate()
    {
        $servicePriceListForm = new ServicePriceListForm([
            'scenario' => 'create',
        ]);
        $servicePriceListForm->load(\Yii::$app->getRequest()->getBodyParams());
        return $this->asJson(ActiveForm::validate($servicePriceListForm));
    }

    public function actionValidateUpdate()
    {
        $servicePriceListForm = new ServicePriceListForm([
            'scenario' => 'create',
        ]);
        $servicePriceListForm->load(\Yii::$app->getRequest()->getBodyParams());
        return $this->asJson(ActiveForm::validate($servicePriceListForm));
    }
}
