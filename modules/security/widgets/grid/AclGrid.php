<?php
namespace app\modules\security\widgets\grid;

use app\common\acl\resource\ApplicationResourceInterface;
use app\common\base\Module;
use app\common\button\LinkActionButton;
use app\common\button\WidgetLoaderButton;
use app\common\grid\GridView;
use app\common\helpers\ClassHelper;
use app\modules\security\models\finders\AclFinder;
use app\modules\security\models\orm\Acl;
use app\modules\security\models\orm\AclRole;
use app\modules\security\application\AclService;
use app\modules\security\application\AclServiceInterface;
use app\modules\security\SecurityModule;
use app\modules\security\widgets\form\AclCreateForm;
use app\modules\security\widgets\form\AclUpdateForm;
use yii\base\InvalidValueException;

/**
 * Class AccessAclGrid
 * @package Module\Security
 * @copyright 2012-2019 Medkey
 */
class AclGrid extends GridView
{
    /**
     * @var AclFinder
     */
    public $filterModel;
    /**
     * @var int
     */
    public $type = Acl::TYPE_SERVICE;
    /**
     * @var AclServiceInterface
     */
    public $aclService;

    /**
     * AclGrid constructor.
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
        $this->filterModel = AclFinder::ensure($this->filterModel);
        $this->dataProvider = $this->aclService->getAclList($this->filterModel);
        $this->filterModel->type = $this->type;
        $this->actionButtons['create'] = [
            'class' => WidgetLoaderButton::class,
            'widgetClass' => AclCreateForm::class,
            'disabled' => false,
            'isDynamicModel' => false,
            'value' => '',
            'widgetConfig' => [
                'afterUpdateBlockId' => $this->getId(),
            ],
            'options' => [
                'class' => 'btn btn-primary btn-xs',
                'icon' => 'plus'
            ]
        ];
        $this->actionButtons['update'] = [
            'class' => WidgetLoaderButton::class,
            'widgetClass' => AclUpdateForm::class,
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
        $this->actionButtons['delete'] = [
            'class' => LinkActionButton::class,
            'url' => ['/security/ui/acl/delete'],
            'isDynamicModel' => true,
            'isAjax' => true,
            'disabled' => true,
            'isConfirm' => true,
            'afterUpdateBlock' => $this,
            'value' => '',
            'options' => [
                'class' => 'btn btn-danger btn-xs',
                'icon' => 'remove',
            ],
        ];
        $this->columns = [
            [
                'attribute' => 'type',
                'value' => function (Acl $model) {
                    return $model->getTypeName();
                }
            ],
            [
                'attribute' => 'aclRole.name',
                'format' => 'text',
                'value' => function (Acl $model) {
                    if (!$model->aclRole instanceof AclRole) {
                        return '';
                    }
                    return $model->aclRole->name;
                }
            ],
            [
                'attribute' => 'module',
                'value' => function (Acl $model) {
                    $m = \Yii::$app->getModule($model->module);
                    if ($m instanceof Module && !empty($m->aliasId)) {
                        return $m->aliasId;
                    }
                    return $model->module;
                }
            ],
            [
                'attribute' => 'entity_type',
                'value' => function (Acl $model) {
                    /** @var ApplicationResourceInterface $service */
                    $service = \Yii::$app->acl->getResourceClass($model->module, $model->entity_type, $model->type);
                    if (!ClassHelper::implementsInterface($service, ApplicationResourceInterface::class)) {
                        throw new InvalidValueException(SecurityModule::t('acl', 'Given incorrect interface'));
                    }
                    return \Yii::createObject($service)->aclAlias();
                }
            ],
            [
                'attribute' => 'action',
                'value' => function (Acl $model) {
                    /** @var ApplicationResourceInterface $service */
                    $service = \Yii::$app->acl->getResourceClass($model->module, $model->entity_type, $model->type);
                    if (!ClassHelper::implementsInterface($service, ApplicationResourceInterface::class)) {
                        throw new InvalidValueException(SecurityModule::t('acl', 'Given incorrect interface'));
                    }
                    $priv = \Yii::createObject($service)->getPrivileges();
                    return !empty($priv[$model->action]) ? $priv[$model->action] : '';
                }
            ],
        ];
        parent::init();
    }
}
