<?php
namespace app\modules\security\port\ui\controllers;

use app\common\dto\Dto;
use app\common\web\CrudController;
use app\modules\security\application\AclService;
use yii\base\Module;

/**
 * Class AccessAclController
 * @package Module\Security
 * @copyright 2012-2019 Medkey
 */
class AclController extends CrudController
{
    /**
     * @var AclService
     */
    public $aclService;

    /**
     * AccessAclController constructor.
     * @param string $id
     * @param Module $module
     * @param AclService $aclManager
     * @param array $config
     */
    public function __construct($id, Module $module, AclService $aclManager, array $config = [])
    {
        $this->aclService = $aclManager;
        parent::__construct($id, $module, $config);
    }

    public function actionCreate()
    {
        $acl = \Yii::$app->request->post('Acl');
        $aclDto = Dto::make($acl);
        $this->aclService->add($aclDto);
        return $this->redirect(['/security/ui/acl/index']); // todo так надо, но разобраться почему
    }

    public function actionUpdate($id)
    {
        $acl = \Yii::$app->request->post('Acl');
        $aclDto = Dto::make($acl);
        $this->aclService->update($id, $aclDto);
        return $this->redirect(['/security/ui/acl/index']); // todo так надо, но разобраться почему
    }

    public function actionDelete($id)
    {
        $this->aclService->deleteAcl($id);
//        return $this->redirect(['/security/ui/acl/index']); // todo так надо, но разобраться почему
    }

    /**
     * @inheritdoc
     */
    public function actionIndex()
    {
        return $this->render('index');
    }
}
