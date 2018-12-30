<?php
namespace app\modules\medical\widgets\grid;

use app\common\button\WidgetLoaderButton;
use app\common\grid\GridView;
use app\modules\medical\application\ServicePriceServiceInterface;
use app\modules\medical\models\finders\ServicePriceFinder;
use app\modules\medical\models\orm\Service;
use app\modules\medical\models\orm\ServicePrice;
use app\modules\medical\widgets\form\ServicePriceCreateForm;
use app\modules\medical\widgets\form\ServicePriceUpdateForm;

/**
 * Class ServicePriceGrid
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
class ServicePriceGrid extends GridView
{
    /**
     * @var ServicePriceFinder
     */
    public $filterModel;
    /**
     * @var string
     */
    public $servicePriceListId;
    /**
     * @var ServicePriceServiceInterface
     */
    public $servicePriceService;


    /**
     * ServicePriceGrid constructor.
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
        $this->filterModel = ServicePriceFinder::ensure($this->filterModel, 'search');
        $this->filterModel->service_price_list_id = $this->servicePriceListId;
        $this->dataProvider = $this->servicePriceService->getPriceList($this->filterModel);
        $this->actionButtons['create'] = [
            'disabled' => false,
            'class' => WidgetLoaderButton::class,
            'widgetClass' => ServicePriceCreateForm::class,
            'widgetConfig' => [
                'servicePriceListId' => $this->servicePriceListId,
                'afterUpdateBlockId' => $this->getId(),
            ],
            'isDynamicModel' => false,
            'value' => '',
            'options' => [
                'class' => 'btn btn-xs btn-primary',
                'icon' => 'plus',
            ],
        ];
        $this->actionButtons['update'] = [
            'disabled' => true,
            'class' => WidgetLoaderButton::class,
            'widgetClass' => ServicePriceUpdateForm::class,
            'widgetConfig' => [
                'afterUpdateBlockId' => $this->getId(),
            ],
            'isDynamicModel' => true,
            'value' => '',
            'options' => [
                'class' => 'btn btn-xs btn-primary',
                'icon' => 'edit',
            ],
        ];
        $this->columns = [
            'cost',
            [
                'attribute' => 'service_id',
                'value' => function (ServicePrice $model) {
                    if (!$model->service instanceof Service) {
                        return '';
                    }
                    return $model->service->title;
                }
            ],
        ];
        parent::init();
    }
}
