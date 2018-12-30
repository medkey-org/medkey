<?php
namespace app\modules\dashboard\widgets\grid;

use app\modules\dashboard\application\DashboardServiceInterface;
use app\common\button\LinkActionButton;
use app\common\grid\GridView;
use app\common\helpers\Html;
use app\modules\dashboard\models\orm\Dashboard;
use app\common\helpers\Url;
use app\modules\security\models\orm\User;

/**
 * Class DashboardGrid
 * @package Module\Dashboard
 * @copyright 2012-2019 Medkey
 */
class DashboardGrid extends GridView
{
    public $dashboardService;


    /**
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
//        $this->filterModel = $this->dashboardService->getAllCollectionByFilterModel($this->filterModel); // DashboardFinder::ensure($this->filterModel, 'search');

        $buttonTemplate = '{refresh} {create} {view} {delete}';

        $this->actionButtonTemplate = $buttonTemplate . '{close}';

        $this->actionButtons = array_merge($this->actionButtons, [
            'create' => [
                'class' => LinkActionButton::className(),
                'url' => ['/dashboard/ui/dashboard/view', 'scenario' => Dashboard::SCENARIO_CREATE],
                'isAjax' => false,
                'disabled' => false,
                'value' => '',
                'options' => [
                    'class' => 'btn btn-primary btn-xs',
                    'icon' => 'plus',
                ],
            ],
            'delete' => [
                'class' => LinkActionButton::className(),
                'value' => '',
                'url' => '/dashboard/ui/dashboard/delete',
                'options' => [
                    'class' => 'btn btn-danger btn-xs',
                    'icon'  => 'trash',
                ],
                'isConfirm' => true,
            ],
            'view' => [
                'class' => LinkActionButton::className(),
                'url' => ['/dashboard/ui/dashboard/view'],
                'options' => [
                    'class' => 'btn btn-default btn-xs',
                    'icon' => 'eye-open',
                ],
                'value' => '',
                'isAjax' => false,
            ],
        ]);
        $this->columns = [
            [
                'attribute' => 'title',
                'value' => function ($model) {
                    return Html::a(Html::encode($model->title), Url::to(['/dashboard/ui/dashboard/view', 'id' => $model->id]));
                },
                'format' => 'raw',
            ],
            'key',
            'typeLabel',
            [
                'attribute' => 'ownerName',
                'value' => function($model) {
                    if ($model->owner instanceof User) {
                        return Html::encode($model->owner->login);
//                        return UserPopoverWidget::loaderTag('a', $model->ownerFullName, ['model' => $model->owner], ['href' => Url::to(['/security/ui/user/view', 'id' => $model->owner->id])], false, false, 'hover', true, ['title' => $model->ownerFullName]);
                    }
                },
                'format' => 'raw'
            ]
        ];
        parent::init();
    }
}
