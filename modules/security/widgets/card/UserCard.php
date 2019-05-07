<?php
namespace app\modules\security\widgets\card;

use app\common\button\LinkActionButton;
use app\common\button\WidgetLoaderButton;
use app\common\card\CardView;
use app\common\helpers\Html;
use app\common\helpers\Url;
use app\common\widgets\ActiveForm;
use app\modules\config\models\orm\Config;
use app\modules\organization\widgets\grid\EmployeeGrid;
use app\modules\security\application\UserServiceInterface;
use app\modules\security\models\orm\AclRole;
use app\modules\security\models\form\User as UserForm;
use app\modules\security\models\orm\User;
use app\modules\security\SecurityModule;
use app\modules\security\widgets\form\UserPasswordChangeForm;

/**
 * Class UserCard
 * @package Module\Security
 * @copyright 2012-2019 Medkey
 */
class UserCard extends CardView
{
    public $model;
    public $userService;

    public function __construct(UserServiceInterface $userService, array $config = [])
    {
        $this->userService = $userService;
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->model = $this->userService->getUserForm($this->model);
        !\Yii::$app->user->getIsGuest() ?: $this->redirectSubmit = true;
        parent::init();
        $this->formOptions = array_merge($this->formOptions, [
            'action' => Url::to(['/security/rest/user/' . $this->model->scenario, 'id' => $this->model->id]),
            'validationUrl' => Url::to(['/security/rest/user/validate-' . $this->model->scenario, 'id' => $this->model->id]),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function extraButtons()
    {
        $config = [];
        if (!empty($this->model->id)) {
            $config['change-password'] = [
                'class' => WidgetLoaderButton::class,
                'widgetClass' => UserPasswordChangeForm::class,
                'widgetConfig' => [
                    'userId' => $this->model->id
                ],
                'afterUpdateBlock' => $this,
                'disabled' => false,
                'value' => SecurityModule::t('user', 'Change password'),
                'options' => [
                    'class' => 'btn btn-default',
                    'icon' => 'user',
                ],
            ];
//            $config['create-employee'] = [
//                'class' => LinkActionButton::class,
//                'url' => ['/security/ui/user/view', 'scenario' => 'update'],
//                'isDynamicModel' => true,
//                'isAjax' => false,
//                'disabled' => true,
//                'value' => 'Создать сотрудника',
//                'options' => [
//                    'class' => 'btn btn-default',
//                    'icon' => 'plus',
//                ],
//            ];
        }
        return $config;
    }

    /**
     * @inheritdoc
     */
    public function renderTitle()
    {
        echo Html::encode($this->model->login);
    }

    /**
     * @inheritdoc
     */
    public function dataGroups()
    {
        return [
            'main' => [
                'title' => SecurityModule::t('user', 'User details'),
                'items' => [
                    [
                        'items' => [
                            'login',
                            [
                                'attribute' => 'password_hash',
                                'scenarios' => [
                                    'create' => [
                                        'value' => function (UserForm $model, ActiveForm $form) {
                                            return $form
                                                ->field($model, 'password_hash')
                                                ->passwordInput()
                                                ->label(false);
                                        },
                                    ],
                                    'update' => [
                                        'value' => function (UserForm $model, ActiveForm $form) {
                                            return '*******';
                                        },
//                                        'label' => false,
                                    ],
                                    'default' => [
                                        'value' => function (UserForm $model) {
                                            return '********';
                                        },
//                                        'label' => false,
                                    ],
                                ]
                            ]
                        ],
                    ],
                    [
                        'items' => [
                            [
                                'attribute' => 'acl_role_id',
                                'scenarios' => [
                                    'create' => [
                                        'value' => function (UserForm $model, ActiveForm $form) {
                                            return $form->field($model, 'acl_role_id')
                                                ->select2(AclRole::listAll(null, 'description', 'id'))
                                                ->label(false);
                                        }
                                    ],
                                    'update' => [
                                        'value' => function (UserForm $model, ActiveForm $form) {
                                            return $form->field($model, 'acl_role_id')
                                                ->select2(AclRole::listAll(null, 'description', 'id'))
                                                ->label(false);
                                        }
                                    ],
                                    'default' => [
                                        'value' => function (UserForm $model) {
                                            if (
                                                !empty($model['aclRole'])
                                                && !empty($model['aclRole']['description'])
                                            ) {
                                                return Html::encode($model['aclRole']['description']);
                                            }
                                            return '';
                                        }
                                    ]
                                ]
                            ],
                            [
                                'attribute' => 'language',
                                'scenarios' => [
                                    'create' => [
                                        'value' => function (UserForm $model, ActiveForm $form) {
                                            return $form
                                                ->field($model, 'language')
                                                ->select2(Config::listLanguageWithNotSet())
                                                ->label(false);
                                        }
                                    ],
                                    'update' => [
                                        'value' => function (UserForm $model, ActiveForm $form) {
                                            return $form
                                                ->field($model, 'language')
                                                ->select2(Config::listLanguageWithNotSet())
                                                ->label(false);
                                        }
                                    ],
                                    'default' => [
                                        'value' => function (UserForm $form) {
                                            return Html::encode($form->getLanguageLabel());
                                        }
                                    ],
                                ],
                            ],
                        ],
                    ],
                    [
                        'items' => [
                            [
                                'attribute' => 'status',
                                'colSize' => 6,
                                'scenarios' => [
                                    'create' => [
                                        'value' => function (UserForm $model, ActiveForm $form) {
                                            return $form->field($model, 'status')
                                                ->select2(User::statuses())
                                                ->label(false);
                                        }
                                    ],
                                    'update' => [
                                        'value' => function (UserForm $model, ActiveForm $form) {
                                            return $form->field($model, 'status')
                                                ->select2(User::statuses())
                                                ->label(false);
                                        }
                                    ],
                                    'default' => [
                                        'value' => function (UserForm $model) {
                                            return Html::encode($model->statusName);
                                        }
                                    ]
                                ]
                            ]
                        ],
                    ]
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
                        ],
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
            'employee' => [
                'value' => EmployeeGrid::widget([
                    'userId' => $this->model->id,
                ]),
                'header' => SecurityModule::t('user', 'Linked employees'),
            ],
        ];
    }
}
