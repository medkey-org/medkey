<?php
namespace app\common\card;

use app\common\base\UniqueKey;
use app\common\button\ButtonGroup;
use app\common\button\WidgetLoaderButton;
use app\common\db\ActiveRecord;
use app\common\db\ActiveRecordRegistry;
use app\common\helpers\ArrayHelper;
use app\common\widgets\ActiveForm;
use app\common\widgets\FormWidget;
use app\common\widgets\Widget;
use app\common\helpers\Html;
use app\common\workflow\StateMachine;
use app\common\workflow\Transition;
use app\common\workflow\WorkflowManagerInterface;
use app\modules\config\application\WorkflowServiceInterface;
use app\modules\organization\application\EmployeeServiceInterface;
use app\modules\organization\models\finders\EmployeeFinder;
use yii\base\Model;
use app\common\helpers\ClassHelper;
use yii\db\ActiveRecordInterface;

/**
 * CardView basic component
 * @package Common\Card
 * @copyright 2012-2019 Medkey
 */
class CardView extends Widget
{
    const MODE_FULL = 1;
    const MODE_STRIPPED = 2;

    public $mode = self::MODE_FULL;
    public $model;
    public $formOptions = [];
    public $visibleSubpanels = true;
    public $isResponsibility = false;
    public $pushState = true;
    public $redirectSubmit = false;
    public $headerTag = 'div';
    public $middlewareWidget;
    public $afterUpdateBlockId;
    protected $subpanelGroupClass = SubpanelGroup::class;
    protected $actionButtonGroupClass = ButtonGroup::class;
    protected $stateButtonGroupClass = ButtonGroup::class;
    protected $stateMachine;
    private $workflowService;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->workflowService = \Yii::$container->get(WorkflowServiceInterface::class);
        if (!isset($this->clientClassNameDefault)) {
            $this->clientClassNameDefault = ClassHelper::getShortName(__CLASS__);
        }
        $this->prepareModel();
        $this->prepareModelScenario();
        if ($this->model instanceof ActiveRecordInterface && $this->model->isNewRecord) {
            $this->visibleSubpanels = false;
        } elseif ($this->model instanceof Model && empty($this->model->id)) {
            $this->visibleSubpanels = false;
        } elseif (empty($this->subpanels())) {
            $this->visibleSubpanels = false;
        }
        if ($this->model instanceof ActiveRecordInterface && $this->model->isNewRecord) {
            $this->mode = self::MODE_STRIPPED;
        } elseif ($this->model instanceof Model && empty($this->model->id)) {
            $this->mode = self::MODE_STRIPPED;
        }
        $this->clientViewOptions['scenario'] = $this->model->scenario;
        $this->clientViewOptions['pushState'] = $this->pushState;
        $this->clientViewOptions['formOptions'] = $this->formOptions;
        $this->clientViewOptions['mode'] = $this->mode;
        $this->clientViewOptions['redirectSubmit'] = $this->redirectSubmit;
        $this->clientViewOptions['afterUpdateBlockId'] = $this->afterUpdateBlockId;
        if (!$this->stateMachine instanceof StateMachine) {
            $this->stateMachine = \Yii::$container->get(WorkflowManagerInterface::class)
                ->stateMachineFactory(
                    ClassHelper::getMatchModule($this, false),
                    ClassHelper::getShortName($this->model),
                    $this->model->id
                );
        }
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        echo Html::beginRow();
        echo Html::beginCol(['size' => ['xs' => 12]]);
        echo Html::beginDiv(['class' => 'b-card__title']);
        echo Html::beginTag($this->headerTag);
        echo $this->title();
        echo Html::endTag($this->headerTag);
        echo Html::endDiv();
        echo Html::endCol();
        echo Html::endRow();
        $this->mode !== self::MODE_FULL ?: $this->renderButtons();
        echo '<br />';
        $this->renderBody();
        $this->renderAfter();
    }

    /**
     * @return array
     */
    public function subpanels()
    {
        return [];
    }

    /**
     * @return array
     */
    public function dataGroups()
    {
        return [];
    }

    /**
     * Scenario list
     * @return array
     */
    public function visibleScenarioButtons()
    {
        return [
            'update',
            'default',
        ];
    }

    /**
     * @return array
     */
    public function configScenarioButtons()
    {
        return [
            'default' => [
                'class' => 'btn btn-default',
                'icon' => 'eye-open',
            ],
            'create' => [
                'class' => 'btn btn-primary',
                'icon' => 'plus',
            ],
            'update' => [
                'class' => 'btn btn-primary',
                'icon' => 'edit',
            ],
            'delete' => [
                'class' => 'btn btn-danger',
                'icon' => 'remove',
            ],
        ];
    }

    /**
     * @return array
     */
    public function scenarioButtons()
    {
        return [];
    }

    /**
     * @return void
     */
    public function renderAfter()
    {
    }

    /**
     * @return string HTML
     */
    public function title()
    {
        return '';
    }

    /**
     * @return array
     */
    public function extraButtons()
    {
        return [];
    }

    /**
     * @return array
     */
    public function dependencyAttributes()
    {
        return [
        ];
    }

    private function prepareModel()
    {
        if (!($this->model instanceof Model)) {
            $module = \Yii::$app->getModule($this->getModule());
            $modelClass = ActiveRecordRegistry::getNamespace($module->getUniqueId(), str_replace('Card', '', ClassHelper::getShortName(static::className())));
            $this->model = $modelClass::ensureWeak($this->model);
        }
    }

    /**
     * Prepare card model scenario value
     * @return string Scenario name
     */
    private function prepareModelScenario()
    {
        $scenario = \Yii::$app->request->getQueryParam('scenario');
        if (!empty($scenario)) {
            $this->model->setScenario($scenario);
        }
    }

    private function renderBody()
    {
        echo Html::beginTag('div', ['class' => 'row']);
        echo Html::beginTag('div', ['class' => 'col-md-12']);
        echo $this->renderData();
        echo Html::endTag('div'); // col-**
        echo Html::endTag('div'); // row
        if ($this->visibleSubpanels) {
            echo Html::beginTag('div', ['class' => 'row']);
            echo Html::beginTag('div', ['class' => 'col-md-12']);
            $this->renderSubpanels();
            echo Html::endTag('div'); // col-**
            echo Html::endTag('div'); // row
        }
    }

    private function renderButtons()
    {
        echo Html::beginTag('div', ['class' => 'row']);
        echo Html::beginTag('div', ['class' => 'col-md-6']);
        echo $this->renderStateButtons();
        echo Html::endTag('div');
        echo Html::beginTag('div', ['class' => 'col-md-6']);
        echo $this->renderActionButtons();
        echo Html::endTag('div');
        echo Html::endTag('div');
    }

    private function renderStateButtons()
    {
        if ($this->model instanceof ActiveRecordInterface && $this->model->isNewRecord) {
            return null;
        }elseif ($this->model instanceof Model && empty($this->model->id)) {
            return null;
        }
        $buttons = $this->configStateButtons();
        $config = [
            'class' => $this->stateButtonGroupClass,
            'buttonTemplate' => implode(' ', array_map(function ($action) {
                return '{' . $action . '}';
            }, array_keys($buttons))),
            'buttons' => $buttons,
            'group' => false,
            'groupOptions' => [
                'class' => 'btn-group pull-left',
            ],
        ];
        /** @var ButtonGroup $buttonGroup */
        $buttonGroup = \Yii::createObject($config);
        return $buttonGroup->render();
    }

    /**
     * @return array
     */
    private function configStateButtons()
    {
        $stateButtons = [];
        $stateMachine = $this->stateMachine;
        if (!$stateMachine instanceof StateMachine) {
            return $stateButtons;
        }
        $enable = $stateMachine->getEnabledTransitions($this->model);
        if (!$enable) {
            return $stateButtons;
        }
        foreach ($enable as $transition) {
            $buttonId = UniqueKey::generate('state-button');
            /** @var Transition $transition */
            if ($transition instanceof Transition && $transition->getMiddleware() && $this->middlewareWidget) {
                $stateButtons[$transition->getName()] = [
                    'class' => WidgetLoaderButton::class,
                    'widgetClass' => $this->middlewareWidget,
                    'disabled' => false,
                    'isDynamicModel' => false,
                    'value' => $transition->getName(),
                    'widgetConfig' => [
                        'workflowParams' => [
                            'ormModule' => ClassHelper::getMatchModule($this->model, false, '/'),
                            'ormClass' => ClassHelper::getShortName($this->model),
                            'ormId' => $this->model->id,
                            'transitionName' => $transition->getName()
                        ],
                        'buttonId' => $buttonId,
                        'afterUpdateBlockId' => $this->getId()
                    ],
                    'options' => [
                        'id' => $buttonId,
                        'data-orm_module' => ClassHelper::getMatchModule($this->model, false, '/'),
                        'data-transition_name' => $transition->getName(),
                        'data-orm_class' => ClassHelper::getShortName($this->model), // TODO необязательно форма совпадает по имени с ORM... баг
                        'data-orm_id' => $this->model->id,
                        'data-middleware' => $transition->getMiddleware(),
                        'class' => 'btn btn-primary state-transition',
                        'icon' => 'play',
                    ]
                ];
            } else {
                $config = [
                    'id' => $buttonId,
                    'data-orm_module' => ClassHelper::getMatchModule($this->model, false, '/'),
                    'data-transition_name' => $transition->getName(),
                    'data-orm_class' => ClassHelper::getShortName($this->model), // TODO необязательно форма совпадает по имени с ORM... баг
                    'data-orm_id' => $this->model->id,
//                    'data-middleware' => $transition->getMiddleware(),
                    'class' => 'btn btn-primary state-transition',
                    'icon' => 'play',
                ];
                $stateButtons[$transition->getName()] = [
                    'value' => function () use ($transition, $config) {
                        return Html::button($transition->getName(), $config);
                    }
                ];
            }
        }
        return $stateButtons;
    }

    private function renderActionButtons()
    {
        if ($this->model instanceof ActiveRecordInterface && $this->model->isNewRecord) {
            return null;
        } elseif ($this->model instanceof Model && empty($this->model->id)) {
            return null;
        }
        $scenarios = $this->model->scenarios();
        $visibleButtons = $this->visibleScenarioButtons();
        $configScenarioButtons = $this->configScenarioButtons();
        $scenarioButtons = $this->scenarioButtons();
        $buttons = [];
        foreach ($scenarios as $scenario => $value) {
            if ($this->model->scenario === $scenario) {
                continue;
            }
            if (!in_array($scenario, $visibleButtons)) {
                continue;
            }
            if (array_key_exists($scenario, $scenarioButtons)) { // заменяем сценарную кнопку на кастом
                $buttons[$scenario] = $scenarioButtons[$scenario];
            } else {
                $configScenarioButton = [];
                if (array_key_exists($scenario, $configScenarioButtons)) {
                    $configScenarioButton = $configScenarioButtons[$scenario];
                }
                $btn = [
                    'value' => function () use ($scenario, $configScenarioButton) {
                        return Html::button(\Yii::t('app', $scenario), array_merge([
                            'id' => 'scenario-switch',
                            'class' => 'btn btn-default',
                            'data-card-switch' => $scenario
                        ], $configScenarioButton));
                    }
                ];
                $buttons[$scenario] = $btn;
            }
        }
        $buttons = array_merge($buttons, $this->extraButtons());
        $config = [
            'class' => $this->actionButtonGroupClass,
            'buttonTemplate' => implode(' ', array_map(function ($action) {
                return '{' . $action . '}';
            }, array_keys($buttons))),
            'buttons' => $buttons,
            'group' => true,
            'groupOptions' => [
                'class' => 'btn-group pull-right',
            ],
        ];
        /** @var ButtonGroup $buttonGroup */
        $buttonGroup = \Yii::createObject($config);
        return $buttonGroup->render();
    }

    private function renderSubpanels()
    {
        $subpanels = $this->subpanels();
        $config = [
            'class' => $this->subpanelGroupClass,
            'subpanelTemplate' => implode(' ', array_map(function ($action) {
                return '{' . $action . '}';
            }, array_keys($subpanels))),
            'subpanels' => $subpanels,
            'model' => $this->model,
        ];

        /** @var SubpanelGroup $subpanelGroup */
        $subpanelGroup = \Yii::createObject($config);

        echo $subpanelGroup->render();
    }

    private function isFormScenario()
    {
        if ($this->model->scenario === ActiveRecord::SCENARIO_DEFAULT) {
            return false;
        }
        return true;
    }

    private function dependencyDataGroups()
    {
        $dependency = $this->dependencyAttributes();
        $data = [
            'dynamic-attribute' => [
                'title' => 'Additional attributes',
                'items' => [

                ]
            ]
        ];
        if (
            empty($dependency)
            || !is_array($dependency)
            || empty($dependency['attribute'])
            || empty($dependency['dependency'])
            || !is_array($dependency['dependency'])
            || !$this->model->hasProperty($dependency['attribute'])
            || !is_scalar(($this->model->hasProperty($dependency['attribute'])))
        ) {
            return [];
        }
        $items = [];
        $i = 1;
        foreach ($dependency['dependency'] as $value => $attributes) {
            $currentType = $this->model->{$dependency['attribute']};
            if ((string)$currentType !== (string)$value) {
                continue;
            }
            foreach ($attributes as $attribute) {
                if (count($attributes) === 1) {
                    $items[] = $attribute;
                    array_push($data['dynamic-attribute']['items'], [
                        'items' => $items
                    ]);
                    break;
                } elseif (count($attributes) <= 2 && $i < 2) {
                    $items[] = $attribute;
                } elseif ((count($attributes) <= 2 && $i === 2)) {
                    $items[] = $attribute;
                    array_push($data['dynamic-attribute']['items'], [
                        'items' => $items
                    ]);
                    break;
                } elseif (count($attributes) !== $i && $i % 2 === 0) {
                    $items[] = $attribute;
                    array_push($data['dynamic-attribute']['items'], [
                        'items' => $items
                    ]);
                    $items = [];
                } elseif (count($attributes) === $i) {
                    $items[] = $attribute;
                    array_push($data['dynamic-attribute']['items'], [
                        'items' => $items
                    ]);
                    break;
                } else {
                    $items[] = $attribute;
                }
                $i++;
            }
        }
        if (empty($data['dynamic-attribute']['items'])) {
            return [];
        }
        return $data;
    }

    private function responsibilityField()
    {
        return [
            'organization-fields' => [
                'title' => 'Organizational structure',
                'items' => [
                    [
                        'items' => [
                            [
                                'colSize' => 4,
                                'labelSize' => 4,
                                'scenarios' => [
                                    'create' => [
                                        'value' => function ($model, ActiveForm $form) {
                                            /** @var EmployeeServiceInterface $employeeService */
                                            $employeeList = \Yii::$container->get(EmployeeServiceInterface::class)->getEmployeeList(new EmployeeFinder())->getModels();
                                            return $form->field($model, 'responsibilityIds')
                                                ->select2(ArrayHelper::map(
                                                    $employeeList,
                                                    'id',
                                                    function ($row) {
                                                        return $row->last_name . ' ' . $row->first_name . ' ' . $row->middle_name;
                                                    }
                                                ))
                                                ->label(false);
                                        },
                                        'label' => function () {
                                            return 'Responsible';
                                        }
                                    ],
                                    'update' => [
                                        'value' => function ($model, ActiveForm $form) {
                                            /** @var EmployeeServiceInterface $employeeService */
                                            $employeeList = \Yii::$container->get(EmployeeServiceInterface::class)->getEmployeeList(new EmployeeFinder())->getModels();
                                            return $form->field($model, 'responsibilityIds')
                                                ->select2(ArrayHelper::map(
                                                    $employeeList,
                                                    'id',
                                                    function ($row) {
                                                        return $row->last_name . ' ' . $row->first_name . ' ' . $row->middle_name;
                                                    }
                                                ))
                                                ->label(false);
                                        },
                                        'label' => function () {
                                            return 'Responsible';
                                        }
                                    ],
                                    'default' => [
                                        'value' => function ($model) {
                                            if (empty($model->responsibilityIds)) {
                                                return '';
                                            }
                                            $employeeList = \Yii::$container->get(EmployeeServiceInterface::class)->getEmployeeList(new EmployeeFinder(['ids' => $model->responsibilityIds]))->getModels();
                                            $str = '';
                                            foreach ($employeeList as $employee) {
                                                $str .= $employee->fullName . PHP_EOL;
                                            }
                                            return $str;
                                        },
                                        'label' => function () {
                                            return 'Responsible';
                                        }
                                    ],
                                ],
                            ]
                        ],
                    ]
                ]
            ]
        ];
    }

    private function departmentField()
    {
    }

    private function organizationField()
    {
    }

    private function renderData()
    {
        if ($this->isFormScenario()) {
            // todo mini refactor
            $content = FormWidget::widget(array_merge($this->formOptions, [
                'model' => $this->model,
                'ajaxSubmit' => true,
                'callback' => function ($model, $form) {
                    return $this->renderDataGroups($model, $form);
                },
            ]));
        } else {
            $content = $this->renderDataGroups();
        }
        return $content;
    }

    /**
     * @param Model $model
     * @param ActiveForm $form
     * @return string
     */
    private function renderDataGroups($model = null, $form = null)
    {
        if (!isset($model)) {
            $model = $this->model;
        }
        $data = $this->dataGroups();
        $buttons = [];
        if (!empty($data['buttons'])) {
            $buttons = $data['buttons'];
            unset($data['buttons']);
        }
        $data = array_merge($data, $this->dependencyDataGroups());
        !$this->isResponsibility ?: $data = array_merge($data, $this->responsibilityField());
        $data = array_merge($data, ['buttons' => $buttons]);
        return implode('', array_map(function ($config) use ($form, $model) {
            if (!isset($config['title']) || !isset($config['items'])) {
                return '';
            }
            $config = array_merge($config, [
                'class' => CardDataGroup::class,
                'form' => $form,
                'model' => $model
            ]);
            /** @var CardDataGroup $cardDataGroup */
            $cardDataGroup = \Yii::createObject($config);
            return $cardDataGroup->render();
        }, $data));
    }
}
