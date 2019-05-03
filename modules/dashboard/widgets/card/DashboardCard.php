<?php
namespace app\modules\dashboard\widgets\card;

use app\common\button\LinkActionButton;
use app\common\button\WidgetLoaderButton;
use app\common\card\CardView;
use app\common\helpers\Html;
use app\modules\dashboard\models\orm\Dashboard;
use app\modules\dashboard\widgets\DashboardDetail;
use app\modules\dashboard\widgets\form\DashboardItemCreateForm;
use app\modules\dashboard\widgets\form\DashboardItemEditForm;
use app\modules\dashboard\widgets\grid\DashboardItemGrid;
use app\modules\security\models\orm\User;
use app\common\widgets\ActiveForm;

/**
 * Class DashboardCard
 * @package Module\Dashboard
 * @copyright 2012-2019 Medkey
 */
class DashboardCard extends CardView
{
    /**
     * @var Dashboard
     */
    public $model;
	/**
	 * @var int
	 */
    public $bodySize = 6;
	/**
	 * @var int
	 */
    public $subpanelsSize = 6;


    /**
     * @inheritdoc
     */
    public function init()
    {
        if ($this->model) {
            $model = Dashboard::ensure($this->model);

            $this->setModel($model);
        }

        parent::init();

        if ($this->model->isNewRecord) {
            $this->formOptions = array_merge($this->formOptions, [
                'action' => ['/dashboard/ui/dashboard/create'],
                'validationUrl' => ['/dashboard/ui/dashboard/validate-create'],
                'clientClassName' => 'Dashboard_DashboardCreateForm',
            ]);
        } else {
            $this->formOptions = array_merge($this->formOptions, [
                'action' => ['/dashboard/ui/dashboard/update', 'id' => $this->model->id],
                'validationUrl' => ['/dashboard/ui/dashboard/validate-update', 'id' => $this->model->id],
                'clientClassName' => 'Dashboard_DashboardEditForm',
            ]);
        }
    }

    /**
     * Вернуть группы с наборами данных
     */
    public function dataGroups()
    {
        return [
            'general' => [
                'title' => 'Рабочий стол',
                'items' => [
                    [
                        'items' => [[
                            'attribute' => 'title',
                        ]],
                    ], [
                        'items' => [[
                            'attribute' => 'id',
                        ], [
                            'attribute' => 'created_at',
                        ]],
                    ],[
                        'items' => [[
                            'attribute' => 'key',
                        ]],
                    ], [
                        'items' => [[
                            'attribute' => 'description',
                        ], [
                            'attribute' => 'layout',
                            'scenarios' => [
                                'default' => [
                                    'value' => function (Dashboard $model) {
                                        return $model->layoutTitle;
                                    }
                                ],
                                'update' => [
                                    'value' => function (Dashboard $model, ActiveForm $form) {
                                        return $form->field($model, 'layout')->dropDownList(Dashboard::getLayoutsTitles())->label(false);
                                    }
                                ],
                                'create' => [
                                    'value' => function (Dashboard $model, ActiveForm $form) {
                                        return $form->field($model, 'layout')->dropDownList(Dashboard::getLayoutsTitles())->label(false);
                                    }
                                ],
                            ],
                        ]],
                    ], [
                        'items' => [[
                            'attribute' => 'type',
                            'scenarios' => [
                                'default' => [
                                    'value' => function (Dashboard $model) {
                                        return $model->getTypeLabel();
                                    }
                                ],
                            ]
                        ], [
                            'attribute' => 'owner_id',
                            'scenarios' => [
                                'default' => [
                                    'value' => function (Dashboard $model) {
                                        return Html::encode($model->getOwnerFullName());
                                    }
                                ],
                                'update' => [
                                    'value' => function (Dashboard $model, ActiveForm $form) {
                                        return $form->field($model, 'owner_id')->dropDownList(User::listAll(null, 'login', 'id'), [
                                            'disabled' => $model->isTypeTemplate(),
                                        ])->label(false);
                                    }
                                ],
                                'create' => [
                                    'value' => function (Dashboard $model, ActiveForm $form) {
                                        return $form->field($model, 'owner_id')->dropDownList(User::listAll(null, 'login', 'id'), [
                                            'disabled' => $model->isTypeTemplate(),
                                        ])->label(false);
                                    }
                                ]
                            ]
                        ]],
                    ], [
                        'items' => [[
                            'scenarios' => [
                                'default' => [
                                    'value' => '',
                                ],
                                'update' => [
                                    'value' =>
                                        Html::submitButton('Сохранить', [
                                            'class' => 'btn btn-primary',
                                            'icon'  => 'glyphicon glyphicon-saved',
                                        ])
                                        .' '. Html::button('Отмена', [
                                            'class' => 'btn btn-default',
                                            'data-card-switch' => 'default'
                                        ])
                                ],
                                'create' => [
                                    'value' => Html::submitButton('Сохранить', ['class' => 'btn btn-primary']),
                                ],
                            ],
                        ]],
                    ],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function subpanels()
    {
        return [
            'dashlets' => [
                'header' => "Список виджетов",
                'collapsible' => false,
                'value' => function () {
                    return DashboardItemGrid::widget([
                        'imitation' => $this->model->isNewRecord,
                        'model' => $this->model ?: $this->model,
                        'fixedCaption' => false,
                        'dashboardId' => $this->getId(),
                        'actionButtonTemplate' => '{add}{edit}{delete}',
                        'actionButtons' => [
                            'add' => [
                                'class' => WidgetLoaderButton::className(),
                                'widgetClass' => DashboardItemCreateForm::className(),
                                'widgetConfig' => [
                                    'dashboard_id' => $this->model->id,
                                ],
                                'options' => [
                                    'class' => 'btn btn-default btn-xs',
                                    'icon' => 'plus',
                                ],
                                'value' => 'Добавить',
//                                'transferModel' => false,
                                'disabled' => false
                            ],
                            'edit' => [
                                'class' => WidgetLoaderButton::className(),
                                'widgetClass' => DashboardItemEditForm::className(),
                                'widgetConfig' => [
                                    'ajaxSubmit' => true,
                                ],
                                'options' => [
                                    'class' => 'btn btn-default btn-xs',
                                    'icon' => 'pencil',
                                ],
                                'value' => 'Изменить',
                            ],
                            'delete' => [
                                'disabled' => true,
                                'class' => LinkActionButton::className(),
                                'value' => 'Удалить',
                                'url' => '/dashboard/ui/item/delete',
                                'options' => [
                                    'class' => 'btn btn-xs btn-danger',
                                    'icon'  => 'trash',
                                    'module' => 'dashboard',
                                ],
                                'isConfirm' => true,
                            ],
                        ],
                    ]);
                },
            ],
            'detail' => [
                'header' => "Просмотр",
                'collapsible' => false,
                'value' => function () {
                    return DashboardDetail::widget([
                        'imitation' => $this->model->isNewRecord,
                        'model' => $this->model
                    ]);
                }
            ]
        ];
    }
}
