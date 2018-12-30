<?php
namespace app\modules\security\widgets\grid;

use app\common\button\LinkActionButton;
use app\common\grid\GridView;
use app\common\helpers\Html;
use app\common\helpers\Url;
use app\modules\security\models\finders\UserFinder;
use app\modules\security\models\orm\User;
use app\modules\security\application\UserServiceInterface;

/**
 * Class UserGrid
 * @package Module\Security
 * @copyright 2012-2019 Medkey
 */
class UserGrid extends GridView
{
    /**
     * @var UserFinder
     */
    public $filterModel;
    /**
     * @var UserServiceInterface
     */
    public $userService;
    /**
     * @var bool
     */
    public $visibleFilterRow = true;

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
        $this->filterModel = UserFinder::ensure($this->filterModel, 'search', $this->formData);
        $this->dataProvider = $this->userService->getUserList($this->filterModel);
        $this->actionButtons['create'] = [
            'class' => LinkActionButton::class,
            'url' => ['/security/ui/user/view', 'scenario' => 'create'],
            'isDynamicModel' => false,
            'isAjax' => false,
            'disabled' => false,
            'value' => '',
            'options' => [
                'class' => 'btn btn-xs btn-primary',
                'icon' => 'plus',
            ],
        ];
        $this->actionButtons['update'] = [
            'class' => LinkActionButton::class,
            'url' => ['/security/ui/user/view', 'scenario' => 'update'],
            'isDynamicModel' => true,
            'isAjax' => false,
            'disabled' => true,
            'value' => '',
            'options' => [
                'class' => 'btn btn-xs btn-primary',
                'icon' => 'edit',
            ],
        ];
        $this->columns = [
            [
                'attribute' => 'login',
                'format' => 'html',
                'value' => function (User $model) {
                    return Html::a(Html::encode($model->login), Url::to(['/security/ui/user/view', 'id' => $model->id]));
                },
                'options' => [
                    'class' => 'col-xs-2'
                ],
                'filter' => function () {
                    return Html::activeTextInput(
                        $this->filterModel,
                        'login',
                        ['class' => 'form-control', 'autocomplete' => 'off']
                    );
                },
            ],
            [
                'attribute' => 'aclRole.name',
                'format' => 'text',
                'options' => [
                    'class' => 'col-xs-2'
                ],
                'filter' => function () {
                    return Html::activeTextInput(
                        $this->filterModel,
                        'roleName',
                        ['class' => 'form-control', 'autocomplete' => 'off']
                    );
                }
            ],
            [
                'attribute' => 'statusName',
                'format' => 'text',
                'options' => [
                    'class' => 'col-xs-2'
                ],
                'filter' => function () {
                    return Html::activeSelect2Input($this->filterModel, 'status', User::statuses(), [
                        'empty' => false,

                    ]);
                },
            ],
        ];
        parent::init();
    }
}
