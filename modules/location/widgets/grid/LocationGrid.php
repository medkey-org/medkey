<?php
namespace app\modules\location\widgets\grid;

use app\common\grid\GridView;
use app\modules\location\models\finders\LocationFinder;
use app\common\button\LinkActionButton;
use app\common\db\ActiveRecord;
use app\common\helpers\Html;
use app\common\helpers\Url;
use app\modules\location\models\orm\Location;
use app\common\helpers\CommonHelper;
use app\modules\location\application\LocationServiceInterface;

/**
 * Class LocationGrid
 * @package Module\Location
 * @copyright 2012-2019 Medkey
 */
class LocationGrid extends GridView
{
    /**
     * @var bool
     */
    public $visibleFilterRow = false;
    /**
     * @var LocationFinder
     */
    public $filterModel;
    /**
     * @var LocationServiceInterface
     */
    public $locationService;


    /**
     * LocationGrid constructor.
     * @param LocationServiceInterface $locationService
     * @param array $config
     */
    public function __construct(LocationServiceInterface $locationService, array $config = [])
    {
        $this->locationService = $locationService;
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->filterModel = LocationFinder::ensure($this->filterModel, 'search');
        $this->dataProvider = $this->locationService->getLocationList($this->filterModel);
        $this->actionButtons['create'] = [
            'class' => LinkActionButton::class,
            'url' => ['/location/ui/location/view', 'scenario' => ActiveRecord::SCENARIO_CREATE],
            'isDynamicModel' => false,
            'isAjax' => false,
            'disabled' => false,
            'value' => '',
            'options' => [
                'class' => 'btn btn-primary btn-xs',
                'icon' => 'plus',
            ],
        ];
//        $this->actionButtons['delete'] = [
//            'class' => LinkActionButton::class,
//            'url' => ['/location/ui/location/delete'],
//            'isDynamicModel' => true,
//            'isAjax' => true,
//            'disabled' => true,
//            'isConfirm' => true,
//            'afterUpdateBlock' => $this,
//            'value' => '',
//            'options' => [
//                'class' => 'btn btn-danger btn-xs',
//                'icon' => 'remove',
//            ],
//        ];
        $this->columns = [
            [
                'attribute' => 'code',
                'value' => function(Location $model) {
                    return Html::a(Html::encode($model->code), Url::to(['/location/ui/location/view', 'id' => $model->id]));
                },
                'format' => 'html',
                'options' => [
                    'class' => 'col-xs-3'
                ],
            ],
            [
                'attribute' => 'description',
                'options' => [
                    'class' => 'col-xs-3',
                ],
            ],
            [
                'attribute' => 'status',
                'value' => function (Location $model) {
                    $checked = $model->status == 1 ? true : false;
                    return Html::activeCheckbox($model, 'status', [
                        'disabled' => true,
                        'checked' => $checked,
                        'label' => false,
                    ]);
                },
                'format' => 'raw',
                'filter' => function () {
                    return Html::activeDropDownList($this->filterModel, 'status', Location::statuses(), ['class' => 'form-control']);
                },
                'options' => [
                    'class' => 'col-xs-1',
                ],
            ],
            [
                'attribute' => 'start_date',
                'value' => function (Location $model) {
                    return \Yii::$app->formatter->asDate($model->start_date, CommonHelper::FORMAT_DATE_UI);
                },
                'filter' => function () {
                    return Html::activeDateInput($this->filterModel, 'startDate');
                },
                'options' => [
                    'class' => 'col-xs-3',
                ],
            ],
            [
                'attribute' => 'end_date',
                'value' => function (Location $model) {
                    return \Yii::$app->formatter->asDate($model->end_date, CommonHelper::FORMAT_DATE_UI);
                },
                'filter' => function () {
                    return Html::activeDateInput($this->filterModel, 'endDate');
                },
                'options' => [
                    'class' => 'col-xs-3',
                ],
            ],
        ];
        parent::init();
    }
}
