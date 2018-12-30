<?php
namespace app\modules\security\widgets\grid;

use app\common\button\LinkActionButton;
use app\common\button\WidgetLoaderButton;
use app\common\grid\GridView;
use app\modules\security\models\finders\AclRoleFinder;
use app\modules\security\application\AclServiceInterface;
use app\modules\security\widgets\form\AccessRoleCreateForm;
use app\modules\security\widgets\form\AccessRoleUpdateForm;

/**
 * Class AccessRoleGrid
 * @package Module\Security
 * @copyright 2012-2019 Medkey
 */
class AclRoleGrid extends GridView
{
    /**
     * @var AclRoleFinder
     */
    public $filterModel;
    /**
     * @var AclServiceInterface
     */
    public $aclService;

    /**
     * AclRoleGrid constructor.
     * @param AclServiceInterface $aclService
     * @param array $config
     */
    public function __construct(AclServiceInterface $aclService, array $config = [])
    {
        $this->aclService = $aclService;
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->filterModel = AclRoleFinder::ensure($this->filterModel);
        $this->dataProvider = $this->aclService->getAclRoleList($this->filterModel);
//        $this->actionButtons['link'] = [
//            'class' => WidgetLoaderButton::class,
//            'widgetClass' => RelatedWidget::class,
//            'widgetConfig' => [
//                'list' => [
//                    'class' => AclRoleGrid::class,
//                ],
//                'modelClass' => User::class,
//                'modelPk' => $this->user->id,
//                'relationClass' => AclRole::class,
//                'relationName' => 'accessRoles',
//                'afterUpdateBlockId' => $this->getId()
//            ],
//            'disabled' => false,
//            'isDynamicModel' => false,
//            'value' => '',
//            'options' => [
//                'class' => 'btn btn-primary btn-xs',
//                'icon' => 'link'
//            ]
//        ];
        $this->actionButtons['create'] = [
            'class' => WidgetLoaderButton::class,
            'widgetClass' => AccessRoleCreateForm::class,
            'disabled' => false,
            'isDynamicModel' => false,
            'value' => '',
            'widgetConfig' => [
                'afterUpdateBlockId' => $this->getId()
            ],
            'options' => [
                'class' => 'btn btn-primary btn-xs',
                'icon' => 'plus'
            ]
        ];
        $this->actionButtons['update'] = [
            'class' => WidgetLoaderButton::class,
            'widgetClass' => AccessRoleUpdateForm::class,
            'disabled' => true,
            'isDynamicModel' => true,
            'value' => '',
            'widgetConfig' => [
                'afterUpdateBlockId' => $this->getId()
            ],
            'options' => [
                'class' => 'btn btn-primary btn-xs',
                'icon' => 'edit'
            ]
        ];
//        $this->actionButtons['delete'] = [
//            'class' => LinkActionButton::class,
//            'url' => ['/security/ui/acl-role/delete'],
//            'isDynamicModel' => true,
//            'isAjax' => true,
//            'disabled' => true,
//            'isConfirm' => true,
//            'value' => '',
//            'options' => [
//                'class' => 'btn btn-danger btn-xs',
//                'icon' => 'remove',
//            ],
//        ];
        $this->columns = [
            'name',
            'short_name',
            'description'
        ];
        parent::init();
    }
}
