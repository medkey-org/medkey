<?php
namespace app\modules\organization\widgets\card;

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
use app\modules\organization\application\EmployeeServiceInterface;
use app\modules\organization\models\orm\Employee;
use app\modules\organization\OrganizationModule;
use app\modules\security\models\orm\User;
use yii\widgets\MaskedInput;
use app\modules\organization\models\form\Employee as EmployeeForm;

/**
 * Class EmployeeCard
 * @package Module\Organization
 * @copyright 2012-2019 Medkey
 */
class EmployeeCard extends CardView
{
    /**
     * @var EmployeeForm
     */
    public $model;
    /**
     * @var int
     */
    public $userId;
    /**
     * @inheritdoc
     */
    public $wrapper = true;
    /**
     * @var EmployeeServiceInterface
     */
    public $employeeService;

    /**
     * EmployeeCard constructor.
     * @param EmployeeServiceInterface $employeeService
     * @param array $config
     */
    public function __construct(EmployeeServiceInterface $employeeService, array $config = [])
    {
        $this->employeeService = $employeeService;
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->model = $this->employeeService->getEmployeeForm($this->model);
        $this->model->user_id = $this->userId;
        parent::init();
        $this->formOptions = array_merge($this->formOptions, [
            'action' => Url::to(['/organization/rest/employee/' . $this->model->scenario, 'id' => $this->model->id]),
            'validationUrl' => Url::to(['/organization/rest/employee/validate-' . $this->model->scenario, 'id' => $this->model->id]),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function renderTitle()
    {
        // todo in AR add field title
        echo $this->model->fullName;
    }

    /**
     * @inheritdoc
     */
    public function dataGroups()
    {
        return [
            'personal' => [
                'title' => OrganizationModule::t('common', 'Employee\'s data'),
                'items' => [
                    [
                        'items' => [
                            [
                                'attribute' => 'last_name'
                            ],
                            [
                                'attribute' => 'sex',
                                'scenarios' => [
                                    'default' => [
                                        'value' => function (EmployeeForm $model) {
                                            return $model->getSexName();
                                        }
                                    ],
                                    'create' => [
                                        'value' => function (EmployeeForm $model, ActiveForm $form) {
                                            return $form
                                                ->field($model, 'sex')
                                                ->select2(Employee::sexListData())
                                                ->label(false);
                                        }
                                    ],
                                    'update' => [
                                        'value' => function (EmployeeForm $model, ActiveForm $form) {
                                            return $form
                                                ->field($model, 'sex')
                                                ->select2(Employee::sexListData())
                                                ->label(false);
                                        }
                                    ]
                                ],
                            ],
                        ],
                    ],
                    [
                        'items' => [
                            [
                                'attribute' => 'first_name'
                            ],
                            [
//                                'colSize' => 4,
                                'attribute' => 'birthday',
                                'scenarios' => [
                                    'default' => [
                                        'value' => function ($model) {
                                            return \Yii::$app->formatter->asDate($model->birthday, CommonHelper::FORMAT_DATE_UI);
                                        }
                                    ],
                                    'create' => [
                                        'value' => function ($model, ActiveForm $form) {
                                            return $form->field($model, 'birthday')
                                                ->dateInput([
                                                    'startAfterNow' => false,
                                                    'startBeforeNow' => true
                                                ])
                                                ->label(false);
                                        }
                                    ],
                                    'update' => [
                                        'value' => function ($model, ActiveForm $form) {
                                            return $form->field($model, 'birthday')
                                                ->dateInput([
                                                    'startAfterNow' => false,
                                                    'startBeforeNow' => true
                                                ])
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
                                'attribute' => 'middle_name',
                            ],
                            [
                                'attribute' => 'user_id',
                                'scenarios' => [
                                    'default' => [
                                        'value' => false,
                                        'label' => false,
                                    ],
                                    'create' => [
                                        'value' => function (EmployeeForm $model, ActiveForm $form) {
                                            return $form->field($model, 'user_id')
                                                ->hiddenInput()
                                                ->label(false);
                                        },
                                        'label' => false,
                                    ],
                                    'update' => [
                                        'value' => function (EmployeeForm $model, ActiveForm $form) {
                                            return $form->field($model, 'user_id')
                                                ->hiddenInput()
                                                ->label(false);
                                        },
                                        'label' => false,
                                    ]
                                ],
                            ],
                        ]
                    ],
                ],
            ],
            'contacts' => [
                'title' => 'Контактные данные сотрудника',
                'items' => [
                    [
                        'items' => [
                            [
                                'attribute' => 'phones',
//                                    'colSize' => 4,
//                                    'labelSize' => 2,
//                                    'valueSize' => 10,
                                'scenarios' => [
                                    'default' => [
                                        'value' => function ($model) {
                                            $phones = $model->phones;
                                            if (empty($phones) || !is_array($phones)) {
                                                return 'Телефоны не найдены'; // todo normalize text
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
                                            return $content;
                                        }
                                    ],
                                    'update' => [
                                        'value' => function ($model, ActiveForm $form) {
                                            return $form->field($model, 'phones')->widget(MultipleInput::class, [
                                                'columns' => [
                                                    [
                                                        'name' => 'type',
                                                        'type' => 'dropDownList',
                                                        'title' => 'Тип',
                                                        'items' => Phone::typeListData(),
                                                        'enableError' => true,

                                                    ],
                                                    [
                                                        'type' => MaskedInput::class,
                                                        'name'  => 'phone',
                                                        'title' => 'Телефон',
                                                        'options' => [
                                                            'options' => [
                                                                'class' => 'form-control',
                                                            ],
                                                            'mask' => '+7 (999) 999-99-99', // todo more country
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
                                                        'title' => 'Тип',
                                                        'items' => Phone::typeListData(),
                                                        'enableError' => true,

                                                    ],
                                                    [
                                                        'type' => MaskedInput::class,
                                                        'name'  => 'phone',
                                                        'title' => 'Телефон',
                                                        'options' => [
                                                            'options' => [
                                                                'class' => 'form-control',
                                                            ],
                                                            'mask' => '+7 (999) 999-99-99', // todo more country
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
//                                'colSize' => 4,
//                                'labelSize' => 2,
//                                'valueSize' => 10,
                                'scenarios' => [
                                    'default' => [
                                        'value' => function ($model) {
                                            $emails = $model->emails;
                                            if (empty($emails) || !is_array($emails)) {
                                                return 'Email не найден(ы)'; // todo normalize text
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
                                            return $content;
                                        }
                                    ],
                                    'update' => [
                                        'value' => function ($model, ActiveForm $form) {
                                            return $form->field($model, 'emails')->widget(MultipleInput::class, [
                                                'columns' => [
                                                    [
                                                        'name' => 'type',
                                                        'type' => 'dropDownList', // @todo проработать, возможно можно удалить нафиг
                                                        'title' => 'Тип',
                                                        'items' => Email::typeListData(),
                                                        'enableError' => true,
                                                    ],
                                                    [
                                                        'name'  => 'address',
                                                        'title' => 'Адрес',
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
                                                        'type' => 'dropDownList', // @todo проработать, возможно можно удалить нафиг
                                                        'title' => 'Тип',
                                                        'items' => Email::typeListData(),
                                                        'enableError' => true,
                                                    ],
                                                    [
                                                        'name'  => 'address',
                                                        'title' => 'Адрес',
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
//                    [
//                        'items' => [
//                            [
//                                'colSize' => 12,
//                                'labelSize' => 2,
//                                'valueSize' => 10,
//                                'attribute' => 'addresses',
//                                'scenarios' => [
//                                    'default' => [
//                                        'value' => function ($model) {
//                                            $addresses = $model->addresses;
//                                            if (empty($addresses) || !is_array($addresses)) {
//                                                return 'Адреса не найдены'; // todo normalize text
//                                            }
//                                            $content = '';
//                                            $count = count($addresses);
//                                            $i = 0;
//                                            foreach ($addresses as $address) {
//                                                $content .= $address['type']
//                                                    . ' '
//                                                    . $address['region']
//                                                    . ' '
//                                                    . $address['area']
//                                                    . ' '
//                                                    . $address['settlement']
//                                                    . ' '
//                                                    . $address['street']
//                                                    . ' '
//                                                    . $address['house']
//                                                    . ' '
//                                                    . $address['building']
//                                                    . ' '
//                                                    . $address['room'];
//                                                if ($count !== $i + 1) {
//                                                    $content .= ', ';
//                                                }
//                                                $i++;
//                                            }
//                                            return $content;
//                                        }
//                                    ],
//                                    'create' => [
//                                        'value' => function ($model, ActiveForm $form) {
//                                            return $form->field($model, 'addresses')->widget(MultipleInput::class, [
//                                                'columns' => [
//                                                    [
//                                                        'name' => 'type',
//                                                        'type' => 'dropDownList', // @todo проработать, возможно можно удалить нафиг
//                                                        'title' => \Yii::t('app', 'Type address'),
//                                                        'items' => Address::typeListData(),
//                                                        'enableError' => true,
//                                                    ],
//                                                    [
//                                                        'name'  => 'region',
//                                                        'title' => \Yii::t('app', 'Region'),
//                                                        'enableError' => true,
//                                                    ],
//                                                    [
//                                                        'name'  => 'area',
//                                                        'title' => \Yii::t('app', 'Area'),
//                                                        'enableError' => true,
//                                                    ],
//                                                    [
//                                                        'name'  => 'city',
//                                                        'title' => \Yii::t('app', 'City'),
//                                                        'enableError' => true,
//                                                    ],
//                                                    [
//                                                        'name'  => 'settlement',
//                                                        'title' => \Yii::t('app', 'Settlement'),
//                                                        'enableError' => true,
//                                                    ],
//                                                    [
//                                                        'name'  => 'street',
//                                                        'title' => \Yii::t('app', 'Street'),
//                                                        'enableError' => true,
//                                                    ],
//                                                    [
//                                                        'name'  => 'house',
//                                                        'title' => \Yii::t('app', 'House'),
//                                                        'enableError' => true,
//                                                    ],
//                                                    [
//                                                        'name'  => 'building',
//                                                        'title' => \Yii::t('app', 'Building'),
//                                                        'enableError' => true,
//                                                    ],
//                                                    [
//                                                        'name'  => 'room',
//                                                        'title' => \Yii::t('app', 'Room'),
//                                                        'enableError' => true,
//                                                    ],
//                                                ],
//                                            ])->label(false);
//                                        }
//                                    ],
//                                    'update' => [
//                                        'value' => function ($model, ActiveForm $form) {
//                                            return $form->field($model, 'addresses')->widget(MultipleInput::class, [
//                                                'columns' => [
//                                                    [
//                                                        'name' => 'type',
//                                                        'type' => 'dropDownList', // @todo проработать, возможно можно удалить нафиг
//                                                        'title' => \Yii::t('app', 'Type address'),
//                                                        'items' => Address::typeListData(),
//                                                        'enableError' => true,
//                                                    ],
//                                                    [
//                                                        'name'  => 'region',
//                                                        'title' => \Yii::t('app', 'Region'),
//                                                        'enableError' => true,
//                                                    ],
//                                                    [
//                                                        'name'  => 'area',
//                                                        'title' => \Yii::t('app', 'Area'),
//                                                        'enableError' => true,
//                                                    ],
//                                                    [
//                                                        'name'  => 'city',
//                                                        'title' => \Yii::t('app', 'City'),
//                                                        'enableError' => true,
//                                                    ],
//                                                    [
//                                                        'name'  => 'settlement',
//                                                        'title' => \Yii::t('app', 'Settlement'),
//                                                        'enableError' => true,
//                                                    ],
//                                                    [
//                                                        'name'  => 'street',
//                                                        'title' => \Yii::t('app', 'Street'),
//                                                        'enableError' => true,
//                                                    ],
//                                                    [
//                                                        'name'  => 'house',
//                                                        'title' => \Yii::t('app', 'House'),
//                                                        'enableError' => true,
//                                                    ],
//                                                    [
//                                                        'name'  => 'building',
//                                                        'title' => \Yii::t('app', 'Building'),
//                                                        'enableError' => true,
//                                                    ],
//                                                    [
//                                                        'name'  => 'room',
//                                                        'title' => \Yii::t('app', 'Room'),
//                                                        'enableError' => true,
//                                                    ],
//                                                ],
//                                            ])->label(false);
//                                        }
//                                    ],
//                                ],
//                            ],
//                        ]
//                    ],
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
                                        'value' => Html::submitButton(\Yii::t('app', 'save'), [
                                                'class' => 'btn btn-primary',
                                                'icon'  => 'saved'
                                            ])
                                            . '&nbsp' . Html::button(\Yii::t('app', 'cancel'), [
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
        ];
    }

    /**
     * @inheritdoc
     */
    public function wrapperOptions()
    {
        return [
            'wrapperClass' => Block::class,
            'header' => 'Сотрудник', // todo от сценария менять хедер
        ];
    }
}
