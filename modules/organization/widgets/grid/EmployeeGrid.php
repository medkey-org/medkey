<?php
namespace app\modules\organization\widgets\grid;

use app\common\button\LinkActionButton;
use app\common\grid\GridView;
use app\common\helpers\CommonHelper;
use app\common\helpers\Html;
use app\common\helpers\Url;
use app\modules\organization\models\finders\EmployeeFinder;
use app\modules\organization\models\orm\Employee;
use app\modules\organization\application\EmployeeServiceInterface;
use app\modules\security\application\UserServiceInterface;
use yii\widgets\MaskedInput;

/**
 * Class EmployeeGrid
 * @package Module\Organization
 * @copyright 2012-2019 Medkey
 */
class EmployeeGrid extends GridView
{
    /**
     * @var EmployeeFinder
     */
    public $filterModel;
    /**
     * @var int
     */
    public $userId;
    /**
     * @var EmployeeServiceInterface
     */
    public $employeeService;
    /**
     * @var UserServiceInterface
     */
    public $userService;
    /**
     * @var bool
     */
    public $visibleFilterRow = false;

    public function __construct(EmployeeServiceInterface $employeeService, UserServiceInterface $userService, array $config = [])
    {
        $this->userService = $userService;
        $this->employeeService = $employeeService;
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->filterModel = EmployeeFinder::ensure($this->filterModel, 'search', $this->formData);
        $this->filterModel->userId = $this->userId;
        $user = $this->userService->getUserById($this->userId);
        $this->dataProvider = $this->employeeService->getEmployeeList($this->filterModel);
        if (empty($user->employees)) {
            $this->actionButtons['create'] = [
                'class' => LinkActionButton::class,
                'url' => ['/organization/ui/employee/view', 'userId' => $this->userId, 'scenario' => 'create'],
                'isDynamicModel' => false,
                'isAjax' => false,
                'disabled' => false,
                'value' => '',
                'options' => [
                    'class' => 'btn btn-primary btn-xs',
                    'icon' => 'plus',
                ],
            ];
        }
        $this->actionButtons['update'] = [
            'class' => LinkActionButton::class,
            'url' => ['/organization/ui/employee/view', 'scenario' => 'update'],
            'isDynamicModel' => true,
            'isAjax' => false,
            'disabled' => true,
            'value' => '',
            'options' => [
                'class' => 'btn btn-primary btn-xs',
                'icon' => 'edit',
            ],
        ];
        // todo своего сотрудника запрещено удалять. возможно добавить бизнес-правила для кнопок (rules) или просто на уровне экшна accessControl
//        $this->actionButtons['delete'] = [
//            'class' => LinkActionButton::class,
//            'url' => ['/organization/rest/employee/delete'],
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
                'attribute' => 'fullName',
                'value' => function(Employee $model) {
                    return Html::a(Html::encode($model->getFullName()), Url::to(['/organization/ui/employee/view', 'id' => $model->id]));
                },
                'format' => 'html',
                'filter' => function () {
                    return Html::activeTextInput($this->filterModel, 'fullName', ['class' => 'form-control']);
                },
                'options' => [
                    'class' => 'col-xs-4',
                ],
            ],
            [
                'attribute' => 'birthday',
                'value' => function ($model) {
                    return \Yii::$app->formatter->asDate($model->birthday, CommonHelper::FORMAT_DATE_UI);
                },
                'filter' => function () {
                    return Html::activeDateInput($this->filterModel, 'birthday');
                },
                'options' => [
                    'class' => 'col-xs-2',
                ],
            ],
            [
                'attribute' => 'sex',
                'value' => function (Employee $model) {
                    return $model->getSexName();
                },
                'filter' => function () {
                    return Html::activeSelect2Input($this->filterModel, 'sex', Employee::sexListData());
                },
                'options' => [
                    'class' => 'col-xs-3',
                ],
            ],
            [
                'attribute' => 'phone.phone',
                'format' => 'text',
                'value' => function (Employee $model) {
                    $phones = array();
                    foreach ($model->phones as $phone) {
                        $phones[] = $phone->phone . ' (' . $phone->getTypeName() . ')';
                    }
                    return implode(PHP_EOL, $phones);
                },
                'options' => [
                    'class' => 'col-xs-3',
                ],
                'filter' => function () {
                    return MaskedInput::widget([
                        'model' => $this->filterModel,
                        'attribute' => 'phone',
                        'mask' => CommonHelper::PHONE_MASK
                    ]);
                }
            ],
        ];
        parent::init();
    }
}
