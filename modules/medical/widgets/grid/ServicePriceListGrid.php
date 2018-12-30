<?php
namespace app\modules\medical\widgets\grid;

use app\common\button\LinkActionButton;
use app\common\grid\GridView;
use app\common\helpers\CommonHelper;
use app\common\helpers\Html;
use app\modules\medical\application\ServicePriceServiceInterface;
use app\modules\medical\models\finders\ServicePriceListFinder;
use app\modules\medical\models\orm\ServicePriceList;

/**
 * Class ServicePriceListGrid
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
class ServicePriceListGrid extends GridView
{
    /**
     * @var ServicePriceListFinder
     */
    public $filterModel;
    /**
     * @var ServicePriceServiceInterface
     */
    public $servicePriceService;


    /**
     * ServicePriceListGrid constructor.
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
        $this->filterModel = ServicePriceListFinder::ensure($this->filterModel, 'search');
        $this->dataProvider = $this->servicePriceService->getServicePriceList($this->filterModel);
        $this->actionButtons['create'] = [
            'class' => LinkActionButton::class,
            'url' => ['/medical/ui/service-price-list/view', 'scenario' => 'create'],
            'isDynamicModel' => false,
            'isAjax' => false,
            'disabled' => false,
            'value' => '',
            'options' => [
                'class' => 'btn btn-primary btn-xs',
                'icon' => 'plus',
            ],
        ];
        // todo своего сотрудника запрещено удалять. возможно добавить бизнес-правила для кнопок (rules) или просто на уровне экшна accessControl
        $this->actionButtons['delete'] = [
            'class' => LinkActionButton::class,
            'url' => ['/medical/rest/service-price-list/delete'],
            'isDynamicModel' => true,
            'isAjax' => true,
            'disabled' => true,
            'isConfirm' => true,
            'afterUpdateBlock' => $this,
            'value' => '',
            'options' => [
                'class' => 'btn btn-danger btn-xs',
                'icon' => 'remove',
            ],
        ];
        $this->columns = [
            [
                'attribute' => 'name',
                'format' => 'html',
                'value' => function (ServicePriceList $model) {
                    return Html::a($model->name, ['/medical/ui/service-price-list/view', 'id' => $model->id]);
                }
            ],
            'status',
            [
                'attribute' => 'start_date',
                'value' => function (ServicePriceList $model) {
                    return \Yii::$app->formatter->asDate($model->start_date, CommonHelper::FORMAT_DATE_UI);
                }
            ],
            [
                'attribute' => 'end_date',
                'value' => function (ServicePriceList $model) {
                    return \Yii::$app->formatter->asDate($model->end_date, CommonHelper::FORMAT_DATE_UI);
                }
            ],
        ];
        parent::init();
    }
}
