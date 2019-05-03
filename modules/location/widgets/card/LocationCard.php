<?php
namespace app\modules\location\widgets\card;

use app\common\card\CardView;
use app\modules\location\models\orm\Location;
use app\common\helpers\Url;
use app\modules\location\LocationModule;
use app\common\widgets\ActiveForm;
use app\common\wrappers\Block;
use app\common\helpers\CommonHelper;
use app\common\helpers\Html;

/**
 * Class LocationCard
 * @package Module\Location
 * @copyright 2012-2019 Medkey
 */
class LocationCard extends CardView
{
    /**
     * @var Location
     */
    public $model;
    /**
     * @var bool
     */
    public $wrapper = true;


    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->formOptions = array_merge($this->formOptions, [
            'action' => Url::to(['/location/ui/location/' . $this->model->scenario, 'id' => $this->model->id]),
            'validationUrl' => Url::to(['/location/ui/location/validate-' . $this->model->scenario, 'id' => $this->model->id]),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function dataGroups()
    {
        return [
            'main' => [
                'title' => LocationModule::t('common', 'Location\'s data'),
                'items' => [
                    [
                        'items' => [
                            [
                                'attribute' => 'code',
                            ],
                            [
                                'attribute' => 'status',
                                'scenarios' => [
                                    'create' => [
                                        'value' => function (Location $model, ActiveForm $form) {
                                            return $form
                                                ->field($model, 'status')
                                                ->select2(Location::statuses())
                                                ->label(false);
                                        },
                                    ],
                                    'update' => [
                                        'value' => function (Location $model, ActiveForm $form) {
                                            return $form
                                                ->field($model, 'status')
                                                ->select2(Location::statuses())
                                                ->label(false);
                                        },
                                    ],
                                    'default' => [
                                        'value' => function (Location $model) {
                                            return $model->getStatusName();
                                        }
                                    ]
                                ],
                            ],
                        ],
                    ],
                    [
                        'items' => [
                            [
                                'attribute' => 'start_date',
                                'scenarios' => [
                                    'default' => [
                                        'value' => function (Location $model) {
                                            return \Yii::$app->formatter->asDate($model->start_date, CommonHelper::FORMAT_DATE_UI);
                                        },
                                    ],
                                    'update' => [
                                        'value' => function (Location $model, ActiveForm $form) {
                                            return $form->field($model, 'start_date')->dateInput()->label(false);
                                        }
                                    ],
                                    'create' => [
                                        'value' => function (Location $model, ActiveForm $form) {
                                            return $form->field($model, 'start_date')->dateInput()->label(false);
                                        }
                                    ],
                                ]
                            ],
                            [
                                'attribute' => 'end_date',
                                'scenarios' => [
                                    'default' => [
                                        'value' => function (Location $model) {
                                            return \Yii::$app->formatter->asDate($model->end_date, CommonHelper::FORMAT_DATE_UI);
                                        },
                                    ],
                                    'update' => [
                                        'value' => function (Location $model, ActiveForm $form) {
                                            return $form->field($model, 'end_date')->dateInput()->label(false);
                                        }
                                    ],
                                    'create' => [
                                        'value' => function (Location $model, ActiveForm $form) {
                                            return $form->field($model, 'end_date')->dateInput()->label(false);
                                        }
                                    ],
                                ]
                            ],
                        ]
                    ],
                    [
                        'items' => [
                            [
                                'attribute' => 'description',
                                'colSize' => 12,
                                'labelSize' => 2,
                                'valueSize' => 10,
                            ],
                        ]
                    ],
                ],
            ],
            'buttons' => [
                'title' => '',
                'showFrame' => false,
                'items' => [
                    [
                        'items' => [
                            [
                                'scenarios' => [
                                    'default' => [
                                        'value' => false,
                                        'label' => false,
                                    ],
                                    'update' => [
                                        'label' => false,
                                        'value' =>
                                            Html::submitButton(\Yii::t('app', 'Save'), [
                                                'class' => 'btn btn-primary',
                                                'icon'  => 'saved'
                                            ])
                                            . '&nbsp' . Html::button(\Yii::t('app', 'Cancel'), [
                                                'class' => 'btn btn-default',
                                                'data-card-switch' => 'default'
                                            ])
                                    ],
                                    'create' => [
                                        'label' => false,
                                        'value' => Html::submitButton(\Yii::t('app', 'Save'), [
                                                'class' => 'btn btn-primary',
                                                'icon'  => 'saved'
                                            ])
                                            . '&nbsp' . Html::button(\Yii::t('app', 'Cancel'), [
                                                'class' => 'btn btn-default',
                                                'data-card-switch' => 'default'
                                            ])
                                    ],
                                ],
                            ],
                        ]
                    ],
                ]
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function renderTitle()
    {
        return Html::encode($this->model->code);
    }

    /**
     * @inheritdoc
     */
    public function wrapperOptions()
    {
        return [
            'wrapperClass' => Block::className(),
            'header' => LocationModule::t('common', 'Location'),
        ];
    }
}
