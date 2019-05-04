<?php
namespace app\modules\medical\widgets\card;

use app\common\card\CardView;
use app\common\helpers\CommonHelper;
use app\common\helpers\Html;
use app\common\helpers\Url;
use app\common\logic\orm\Address;
use app\common\logic\orm\Email;
use app\common\logic\orm\Phone;
use app\common\widgets\ActiveForm;
use app\common\widgets\MultipleInput;
use app\common\wrappers\Block;
use app\modules\medical\application\PatientServiceInterface;
use app\modules\medical\MedicalModule;
use app\modules\medical\models\form\Patient;
use app\modules\medical\widgets\grid\EhrGrid;
use app\modules\medical\widgets\grid\PolicyGrid;
use yii\helpers\HtmlPurifier;
use yii\widgets\MaskedInput;

/**
 * Class PatientCard
 *
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
class PatientCard extends CardView
{
    /**
     * @var Patient
     */
    public $model;
    /**
     * @var bool
     */
    public $wrapper = true;
    /**
     * @var PatientServiceInterface
     */
    public $patientService;

    /**
     * PatientCard constructor.
     * @param PatientServiceInterface $patientService
     * @param array $config
     */
    public function __construct(PatientServiceInterface $patientService, array $config = [])
    {
        $this->patientService = $patientService;
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->model = $this->patientService->getPatientForm($this->model);
        parent::init();
        $this->formOptions = array_merge($this->formOptions, [
            'action' => Url::to(['/medical/rest/patient/' . $this->model->scenario, 'id' => $this->model->id]),
            'validationUrl' => Url::to(['/medical/rest/patient/validate-' . $this->model->scenario, 'id' => $this->model->id]),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function title()
    {
        return Html::encode($this->model->fullName);
    }

    /**
     * @inheritdoc
     */
    public function dataGroups()
    {
        return [
            'personal' => [
                'title' => MedicalModule::t('patient', 'Patient data'),
                'items' => [
                    [
                        'items' => [
                            'last_name',
                            'first_name',
                            'middle_name',
                        ],
                    ],
                    [
                        'items' => [
                            [
                                'colSize' => 4,
                                'attribute' => 'sex',
                                'scenarios' => [
                                    'default' => [
                                        'value' => function ($model) {
                                            /**
                                             * @var Patient $model
                                             */
                                            return Html::encode($model->getSexName());
                                        }
                                    ],
                                    'create' => [
                                        'value' => function ($model, ActiveForm $form) {
                                            /**
                                             * @var Patient $model
                                             */
                                            return $form
                                                ->field($model, 'sex')
                                                ->select2(Patient::sexListData())
                                                ->label(false);
                                        }
                                    ],
                                    'update' => [
                                        'value' => function ($model, ActiveForm $form) {
                                            /**
                                             * @var Patient $model
                                             */
                                            return $form
                                                ->field($model, 'sex')
                                                ->select2(Patient::sexListData())
                                                ->label(false);
                                        }
                                    ]
                                ],
                            ],
                            [
                                'colSize' => 4,
                                'attribute' => 'birthday',
                                'scenarios' => [
                                    'default' => [
                                        'value' => function ($model) {
                                            /**
                                             * @var Patient $model
                                             */
                                            return Html::encode(\Yii::$app->formatter->asDate($model->birthday, CommonHelper::FORMAT_DATE_UI));
                                        }
                                    ],
                                    'create' => [
                                        'value' => function ($model, ActiveForm $form) {
                                            /**
                                             * @var Patient $model
                                             */
                                            return $form->field($model, 'birthday')
                                                ->dateInput([
                                                    'startAfterNow' => false,
                                                    'pluginOptions' => [
                                                        'todayHighlight' => false,
                                                    ],
                                                ])
                                                ->label(false);
                                        }
                                    ],
                                    'update' => [
                                        'value' => function ($model, ActiveForm $form) {
                                            /**
                                             * @var Patient $model
                                             */
                                            return $form->field($model, 'birthday')
                                                ->dateInput()
                                                ->label(false);
                                        }
                                    ],
                                ],
                            ],
                        ]
                    ],
                    [
                        'items' => [
                            [
                                'colSize' => 4,
                                'attribute' => 'snils',
                                'scenarios' => [
                                    'create' => [
                                        'value' => function (Patient $model, ActiveForm $form) {
                                            /**
                                             * @var Patient $model
                                             */
                                            return $form
                                                ->field($model, 'snils')
                                                ->inputMask(['mask' => '999-999-999-99'])
                                                ->label(false);
                                        }
                                    ],
                                    'update' => [
                                        'value' => function (Patient $model, ActiveForm $form) {
                                            /**
                                             * @var Patient $model
                                             */
                                            return $form
                                                ->field($model, 'snils')
                                                ->inputMask(['mask' => '999-999-999-99'])
                                                ->label(false);
                                        }
                                    ],
                                ],
                            ],
                            [
                                'colSize' => 4,
                                'attribute' => 'inn',
                                'scenarios' => [
                                    'create' => [
                                        'value' => function (Patient $model, ActiveForm $form) {
                                            /**
                                             * @var Patient $model
                                             */
                                            return $form
                                                ->field($model, 'inn')
                                                ->inputMask(['mask' => '999999999999'])
                                                ->label(false);
                                        }
                                    ],
                                    'update' => [
                                        'value' => function (Patient $model, ActiveForm $form) {
                                            /**
                                             * @var Patient $model
                                             */
                                            return $form
                                                ->field($model, 'inn')
                                                ->inputMask(['mask' => '999999999999'])
                                                ->label(false);
                                        }
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'contacts' => [
                'title' => MedicalModule::t('patient', 'Patient\'s contact data'),
                'items' => [
                    [
                        'items' => [
                            [
                                'attribute' => 'phones',
                                'scenarios' => [
                                    'default' => [
                                        'value' => function ($model) {
                                            $phones = $model->phones;
                                            if (empty($phones) || !is_array($phones)) {
                                                return MedicalModule::t('patient', 'Phone numbers not set');
                                            }
                                            $content = '';
                                            $count = count($phones);
                                            $i = 0;
                                            foreach ($phones as $phone) {
                                                $content .= $phone['phone'];
                                                if ($count !== $i + 1) {
                                                    $content .= '<br>';
                                                }
                                                $i++;
                                            }
                                            return HtmlPurifier::process($content);
                                        }
                                    ],
                                    'update' => [
                                        'value' => function ($model, ActiveForm $form) {
                                            return $form->field($model, 'phones')->widget(MultipleInput::class, [
                                                'columns' => [
                                                    [
                                                        'name' => 'type',
                                                        'type' => 'dropDownList',
                                                        'title' => MedicalModule::t('common', 'Type'),
                                                        'items' => Phone::typeListData(),
                                                        'enableError' => true,

                                                    ],
                                                    [
                                                        'type' => MaskedInput::class,
                                                        'name'  => 'phone',
                                                        'title' => MedicalModule::t('common', 'Phone number'),
                                                        'options' => [
                                                            'options' => [
                                                                'class' => 'form-control',
                                                            ],
                                                            'mask' => '+7 (999) 999-99-99',
                                                        ],
                                                        'enableError' => true,
                                                    ],
                                                ],
                                            ])->label(false);
                                        },
                                    ],
                                    'create' => [
                                        'value' => function ($model, ActiveForm $form) {
                                            return $form->field($model, 'phones')->widget(MultipleInput::class, [
                                                'columns' => [
                                                    [
                                                        'name' => 'type',
                                                        'type' => 'dropDownList',
                                                        'title' => MedicalModule::t('common', 'Type'),
                                                        'items' => Phone::typeListData(),
                                                        'enableError' => true,

                                                    ],
                                                    [
                                                        'type' => MaskedInput::class,
                                                        'name'  => 'phone',
                                                        'title' =>  MedicalModule::t('common', 'Phone number'),
                                                        'options' => [
                                                            'options' => [
                                                                'class' => 'form-control',
                                                            ],
                                                            'mask' => '+7 (999) 999-99-99',
                                                        ],
                                                        'enableError' => true,
                                                    ],
                                                ],
                                            ])->label(false);
                                        },
                                    ],
                                ],
                            ],
                            [
                                'attribute' => 'emails',
                                'multiline' => true,
                                'scenarios' => [
                                    'default' => [
                                        'value' => function ($model) {
                                            $emails = $model->emails;
                                            if (empty($emails) || !is_array($emails)) {
                                                return  MedicalModule::t('patient', 'E-mails not set');
                                            }
                                            $content = '';
                                            $count = count($emails);
                                            $i = 0;
                                            foreach ($emails as $email) {
                                                $content .= $email['address'];
                                                if ($count !== $i + 1) {
                                                    $content .= '<br>';
                                                }
                                                $i++;
                                            }
                                            return HTMLPurifier::process($content);
                                        }
                                    ],
                                    'update' => [
                                        'value' => function ($model, ActiveForm $form) {
                                            return $form->field($model, 'emails')->widget(MultipleInput::class, [
                                                'columns' => [
                                                    [
                                                        'name' => 'type',
                                                        'type' => 'dropDownList',
                                                        'title' => MedicalModule::t('common', 'Type'),
                                                        'items' => Email::typeListData(),
                                                        'enableError' => true,
                                                    ],
                                                    [
                                                        'name'  => 'address',
                                                        'title' => MedicalModule::t('common', 'Address'),
                                                        'enableError' => true,
                                                    ],
                                                ],
                                            ])->label(false);
                                        }
                                    ],
                                    'create' => [
                                        'value' => function ($model, ActiveForm $form) {
                                            return $form->field($model, 'emails')->widget(MultipleInput::class, [
                                                'columns' => [
                                                    [
                                                        'name' => 'type',
                                                        'type' => 'dropDownList',
                                                        'title' => MedicalModule::t('common', 'Type'),
                                                        'items' => Email::typeListData(),
                                                        'enableError' => true,
                                                    ],
                                                    [
                                                        'name'  => 'address',
                                                        'title' => MedicalModule::t('common', 'Address'),
                                                        'enableError' => true,
                                                    ],
                                                ],
                                            ])->label(false);
                                        }
                                    ],
                                ],
                            ],
                        ],
                    ],
                    [
                        'items' => [
                            [
                                'colSize' => 12,
                                'labelSize' => 2,
                                'valueSize' => 10,
                                'attribute' => 'addresses',
                                'scenarios' => [
                                    'default' => [
                                        'value' => function ($model) {
                                            $addresses = $model->addresses;
                                            if (empty($addresses) || !is_array($addresses)) {
                                                return MedicalModule::t('patient','Addresses not set');
                                            }
                                            $content = '';
                                            $count = count($addresses);
                                            $i = 0;
                                            foreach ($addresses as $address) {
                                                $content .= $address['type']
                                                    . ' '
                                                    . $address['region']
                                                    . ' '
                                                    . $address['area']
                                                    . ' '
                                                    . $address['settlement']
                                                    . ' '
                                                    . $address['street']
                                                    . ' '
                                                    . $address['house']
                                                    . ' '
                                                    . $address['building']
                                                    . ' '
                                                    . $address['room'];
                                                if ($count !== $i + 1) {
                                                    $content .= ', ';
                                                }
                                                $i++;
                                            }
                                            return Html::encode($content);
                                        }
                                    ],
                                    'create' => [
                                        'value' => function ($model, ActiveForm $form) {
                                            return $form->field($model, 'addresses')->widget(MultipleInput::class, [
                                                'columns' => [
                                                    [
                                                        'name' => 'type',
                                                        'type' => 'dropDownList',
                                                        'title' => \Yii::t('app', 'Type address'),
                                                        'items' => Address::typeListData(),
                                                        'enableError' => true,
                                                    ],
                                                    [
                                                        'name'  => 'region',
                                                        'title' => \Yii::t('app', 'Region'),
                                                        'enableError' => true,
                                                    ],
                                                    [
                                                        'name'  => 'area',
                                                        'title' => \Yii::t('app', 'Area'),
                                                        'enableError' => true,
                                                    ],
                                                    [
                                                        'name'  => 'city',
                                                        'title' => \Yii::t('app', 'City'),
                                                        'enableError' => true,
                                                    ],
                                                    [
                                                        'name'  => 'settlement',
                                                        'title' => \Yii::t('app', 'Settlement'),
                                                        'enableError' => true,
                                                    ],
                                                    [
                                                        'name'  => 'street',
                                                        'title' => \Yii::t('app', 'Street'),
                                                        'enableError' => true,
                                                    ],
                                                    [
                                                        'name'  => 'house',
                                                        'title' => \Yii::t('app', 'House'),
                                                        'enableError' => true,
                                                    ],
                                                    [
                                                        'name'  => 'building',
                                                        'title' => \Yii::t('app', 'Building'),
                                                        'enableError' => true,
                                                    ],
                                                    [
                                                        'name'  => 'room',
                                                        'title' => \Yii::t('app', 'Room'),
                                                        'enableError' => true,
                                                    ],
                                                ],
                                            ])->label(false);
                                        }
                                    ],
                                    'update' => [
                                        'value' => function ($model, ActiveForm $form) {
                                            return $form->field($model, 'addresses')->widget(MultipleInput::class, [
                                                'columns' => [
                                                    [
                                                        'name' => 'type',
                                                        'type' => 'dropDownList',
                                                        'title' => \Yii::t('app', 'Type address'),
                                                        'items' => Address::typeListData(),
                                                        'enableError' => true,
                                                    ],
                                                    [
                                                        'name'  => 'region',
                                                        'title' => \Yii::t('app', 'Region'),
                                                        'enableError' => true,
                                                    ],
                                                    [
                                                        'name'  => 'area',
                                                        'title' => \Yii::t('app', 'Area'),
                                                        'enableError' => true,
                                                    ],
                                                    [
                                                        'name'  => 'city',
                                                        'title' => \Yii::t('app', 'City'),
                                                        'enableError' => true,
                                                    ],
                                                    [
                                                        'name'  => 'settlement',
                                                        'title' => \Yii::t('app', 'Settlement'),
                                                        'enableError' => true,
                                                    ],
                                                    [
                                                        'name'  => 'street',
                                                        'title' => \Yii::t('app', 'Street'),
                                                        'enableError' => true,
                                                    ],
                                                    [
                                                        'name'  => 'house',
                                                        'title' => \Yii::t('app', 'House'),
                                                        'enableError' => true,
                                                    ],
                                                    [
                                                        'name'  => 'building',
                                                        'title' => \Yii::t('app', 'Building'),
                                                        'enableError' => true,
                                                    ],
                                                    [
                                                        'name'  => 'room',
                                                        'title' => \Yii::t('app', 'Room'),
                                                        'enableError' => true,
                                                    ],
                                                ],
                                            ])->label(false);
                                        }
                                    ],
                                ],
                            ],
                        ]
                    ],
                ],
            ],
            'documents' => [
                'title' => MedicalModule::t('patient', 'Patient documents'),
                'items' => [
                    [
                        'items' => [
                            [
                                'attribute' => 'passportSeries',
                                'scenarios' => [
                                    'create' => [
                                        'value' => function (Patient $model, ActiveForm $form) {
                                            return $form
                                                ->field($model, 'passportSeries')
                                                ->inputMask(['mask' => '9999'])
                                                ->label(false);
                                        }
                                    ],
                                    'update' => [
                                        'value' => function (Patient $model, ActiveForm $form) {
                                            return $form
                                                ->field($model, 'passportSeries')
                                                ->inputMask(['mask' => '9999'])
                                                ->label(false);
                                        }
                                    ],
                                ],
                            ],
                            [
                                'attribute' => 'passportNumber',
                                'scenarios' => [
                                    'create' => [
                                        'value' => function (Patient $model, ActiveForm $form) {
                                            return $form
                                                ->field($model, 'passportNumber')
                                                ->inputMask(['mask' => '999999'])
                                                ->label(false);
                                        }
                                    ],
                                    'update' => [
                                        'value' => function (Patient $model, ActiveForm $form) {
                                            return $form
                                                ->field($model, 'passportNumber')
                                                ->inputMask(['mask' => '999999'])
                                                ->label(false);
                                        }
                                    ],
                                ],
                            ],
                        ],
                    ],
                ]
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
            'ehrs' => [
                'value' => function ($model) {
                    return EhrGrid::widget([
                        'patientId' => $model->id
                    ]);
                },
                'header' => MedicalModule::t('common', 'EHRs'),
            ],
            'policy' => [
                'value' => function ($model) {
                    return PolicyGrid::widget([
                        'patientId' => $model->id,
                    ]);
                },
                'header' => MedicalModule::t('common', 'Policies'),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function wrapperOptions()
    {
        return [
            'wrapperClass' => Block::class,
            'header' => \Yii::t('app', 'Patient'),
        ];
    }
}
