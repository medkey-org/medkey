<?php
namespace app\modules\medical\widgets\form;

use app\common\helpers\Html;
use app\common\widgets\FormWidget;
use app\common\wrappers\DynamicModal;
use app\modules\medical\application\ServicePriceServiceInterface;
use app\modules\medical\MedicalModule;
use app\modules\medical\models\form\ServicePrice;
use app\modules\medical\models\orm\Service;

/**
 * Class ServicePriceCreateForm
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
class ServicePriceCreateForm extends FormWidget
{
    /**
     * @var ServicePrice
     */
    public $model;
    /**
     * @var string
     */
    public $servicePriceListId;
    /**
     * @var ServicePriceServiceInterface
     */
    public $servicePriceService;


    /**
     * ServicePriceCreateForm constructor.
     * @param ServicePriceServiceInterface $servicePriceService
     * @param array $config
     */
    public function __construct(ServicePriceServiceInterface $servicePriceService, array $config = [])
    {
        $this->servicePriceService = $servicePriceService;
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->model = $this->servicePriceService->getServicePriceForm($this->model, 'create');
        $this->model->service_price_list_id = $this->servicePriceListId;
        $this->action = ['/medical/rest/service-price/create'];
        $this->validationUrl = ['/medical/rest/service-price/validate-create'];
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function renderForm($model, $form)
    {
        echo $form->field($model, 'service_price_list_id')
            ->hiddenInput();
        echo $form->field($model, 'cost')
            ->moneyInput();
        echo $form->field($model, 'service_id')
            ->select2(Service::listAll()); // todo proxy service
        echo Html::submitButton(\Yii::t('app', 'Save'), [
            'class' => 'btn btn-primary',
            'icon' => 'plus',
        ]);
        echo '&nbsp';
        echo Html::button(\Yii::t('app', 'Cancel'), [
            'class' => 'btn btn-default',
            'data-dismiss' => 'modal'
        ]);
    }

    public function wrapperOptions()
    {
        return [
            'wrapperClass' => DynamicModal::class,
            'header' => MedicalModule::t('servicePrice', 'Add pricelist position'),
        ];
    }
}
