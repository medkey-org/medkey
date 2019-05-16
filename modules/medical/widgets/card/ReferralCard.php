<?php
namespace app\modules\medical\widgets\card;

use app\common\button\WidgetLoaderButton;
use app\common\card\CardView;
use app\common\helpers\ArrayHelper;
use app\common\helpers\CommonHelper;
use app\common\helpers\Html;
use app\common\helpers\Url;
use app\common\widgets\ActiveForm;
use app\common\wrappers\Block;
use app\modules\medical\MedicalModule;
use app\modules\medical\models\orm\Ehr;
use app\modules\medical\models\orm\Referral;
use app\modules\medical\application\EhrServiceInterface;
use app\modules\medical\widgets\grid\AttendanceGrid;
use app\modules\medical\widgets\grid\ReferralItemGrid;
use app\modules\medical\widgets\misc\ListMedworkerSchedule;
use yii\web\JsExpression;

/**
 * Class ReferralCard
 *
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
class ReferralCard extends CardView
{
    /**
     * @var Referral
     */
    public $model;
    /**
     * @var bool
     */
    public $wrapper = true;
    /**
     * @var EhrServiceInterface
     */
    public $ehrService;


    /**
     * ReferralCard constructor.
     * @param EhrServiceInterface $ehrService
     * @param array $config
     */
    public function __construct(EhrServiceInterface $ehrService, array $config = [])
    {
        $this->ehrService = $ehrService;
        parent::__construct($config);
    }

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        $this->formOptions = array_merge($this->formOptions, [
            'action' => Url::to(['/medical/rest/referral/' . $this->model->scenario, 'id' => $this->model->id]),
            'validationUrl' => Url::to(['/medical/rest/referral/validate-' . $this->model->scenario, 'id' => $this->model->id]),
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function visibleScenarioButtons()
    {
        return [
            'update',
            'default'
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function extraButtons()
    {
        return [
            'record' => [
                'class' => WidgetLoaderButton::class,
                'widgetClass' => ListMedworkerSchedule::class,
                'widgetConfig' => [
                    'model' => $this->model,
                ],
                'disabled' => false,
                'value' => MedicalModule::t('referral', 'Create attendance'),
                'options' => [
                    'class' => 'btn btn-primary',
                    'icon' => 'time'
                ]
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function dataGroups()
    {
        return [
            'main' => [
                'title' => MedicalModule::t('referral', 'Referral details'),
                'items' => [
                    [
                        'items' => [
                            'number',
                            [
                                'attribute' => 'status',
                                'scenarios' => [
                                    'create' => [
                                        'value' => function (Referral $model, ActiveForm $form) {
                                            $model->status = Referral::STATUS_NEW;
                                            return $form
                                                ->field($model, 'status')
                                                ->select2(Referral::statuses())
                                                ->label(false);
                                        },
                                    ],
                                    'update' => [
                                        'value' => function (Referral $model, ActiveForm $form) {
                                            return $form
                                                ->field($model, 'status')
                                                ->select2(Referral::statuses())
                                                ->label(false);
                                        },
                                    ],
                                    'default' => [
                                        'value' => function (Referral $model) {
                                            return Html::encode($model->getStatusName());
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
                                        'value' => function (Referral $model) {
                                            return Html::encode(\Yii::$app->formatter->asDate($model->start_date, CommonHelper::FORMAT_DATE_UI));
                                        },
                                    ],
                                    'update' => [
                                        'value' => function (Referral $model, ActiveForm $form) {
                                            return $form->field($model, 'start_date')->dateInput()->label(false);
                                        }
                                    ],
                                    'create' => [
                                        'value' => function (Referral $model, ActiveForm $form) {
                                            return $form->field($model, 'start_date')->dateInput()->label(false);
                                        }
                                    ],
                                ]
                            ],
                            [
                                'attribute' => 'end_date',
                                'scenarios' => [
                                    'default' => [
                                        'value' => function (Referral $model) {
                                            return Html::encode(\Yii::$app->formatter->asDate($model->end_date, CommonHelper::FORMAT_DATE_UI));
                                        },
                                    ],
                                    'update' => [
                                        'value' => function (Referral $model, ActiveForm $form) {
                                            return $form->field($model, 'end_date')->dateInput()->label(false);
                                        }
                                    ],
                                    'create' => [
                                        'value' => function (Referral $model, ActiveForm $form) {
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
                                'attribute' => 'ehr_id',
                                'scenarios' => [
                                    'default' => [
                                        'value' => function (Referral $model) {
                                            if (!$model->ehr instanceof Ehr) {
                                                return '';
                                            }
                                            return Html::encode($model->ehr->number);
                                        }
                                    ],
                                    'create' => [
                                        'value' => function (Referral $model, ActiveForm $form) {
                                            if (!empty($model->ehr_id)) {
                                                $ehr = $this->ehrService->getEhrById($model->ehr_id);
                                            }
                                            return $form->field($model, 'ehr_id')
                                                ->select2(!isset($ehr) ? [] : ArrayHelper::map([$ehr], 'id', 'number'), [], [
                                                    'allowClear' => true,
                                                    'minimumInputLength' => 1,
                                                    'language' => [
                                                        'errorLoading' => new JsExpression("function () { return 'Ничего не найдено.'; }"),
                                                    ],
                                                    'ajax' => [
                                                        'url' => Url::to(['/medical/rest/ehr/index']),
                                                        'dataType' => 'json',
                                                        'delay' => 1000,
                                                        'data' => new JsExpression('function (params) { return {q:params.term, page: params.page || 1}; }'),
                                                        'processResults' => new JsExpression('function (data, params) { return { results: data, pagination: {more: (params.page * 10) < data.count_filtered}}; }')
                                                    ],
                                                    'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                                                    'templateResult' => new JsExpression('function (ehr) { if (ehr.loading) { return ehr.text; } else {return ehr.number;} }'),
                                                    'templateSelection' => new JsExpression('function (ehr) { if (ehr.number) {return ehr.number;} else { return ehr.text;} }'),
                                                ])
                                                ->label(false);
                                        }
                                    ],
                                    'update' => [
                                        'value' => function (Referral $model, ActiveForm $form) {
                                            $model->ehr;
                                            return $form->field($model, 'ehr_id')
                                                ->select2(ArrayHelper::map([$model->ehr], 'id', 'number'), [], [
                                                    'allowClear' => true,
                                                    'minimumInputLength' => 1,
                                                    'language' => [
                                                        'errorLoading' => new JsExpression("function () { return 'Ничего не найдено.'; }"),
                                                    ],
                                                    'ajax' => [
                                                        'url' => Url::to(['/medical/rest/ehr/index']),
                                                        'dataType' => 'json',
                                                        'delay' => 1000,
                                                        'data' => new JsExpression('function (params) { return {q:params.term, page: params.page || 1}; }'),
                                                        'processResults' => new JsExpression('function (data, params) { return { results: data, pagination: {more: (params.page * 10) < data.count_filtered}}; }')
                                                    ],
                                                    'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                                                    'templateResult' => new JsExpression('function (ehr) { if (ehr.loading) { return ehr.text; } else {return ehr.number;} }'),
                                                    'templateSelection' => new JsExpression('function (ehr) { if (ehr.number) {return ehr.number;} else { return ehr.text;} }'),
                                                ])
                                                ->label(false);
                                        }
                                    ],
                                ]
                            ],
                            'description',
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
    public function subpanels()
    {
        return [
            'referrals' => [
                'value' => function ($model) {
                    return ReferralItemGrid::widget([
                        'referralId' => $model->id
                    ]);
                },
                'header' => MedicalModule::t('referral', 'Referral positions'),
            ],
            'attendances' => [
                'value' => function ($model) {
                    return AttendanceGrid::widget([
                        'referralId' => $model->id,
                    ]);
                },
                'header' => MedicalModule::t('referral', 'Referral attendances'),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function renderTitle()
    {
        return Html::encode($this->model->number);
    }

    /**
     * @inheritdoc
     */
    public function wrapperOptions()
    {
        return [
            'wrapperClass' => Block::className(),
            'header' => MedicalModule::t('referral', 'Referral card')
        ];
    }
}
