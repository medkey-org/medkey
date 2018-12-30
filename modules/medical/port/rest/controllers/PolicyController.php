<?php
namespace app\modules\medical\port\rest\controllers;

use app\common\rest\Controller;
use app\common\widgets\ActiveForm;
use app\modules\medical\application\PolicyServiceInterface;
use app\modules\medical\models\form\Policy;
use yii\base\Module;

/**
 * Class PolicyController
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
class PolicyController extends Controller
{
    /**
     * @var PolicyServiceInterface
     */
    public $policyService;


    /**
     * PolicyController constructor.
     * @param string $id
     * @param Module $module
     * @param PolicyServiceInterface $policyService
     * @param array $config
     */
    public function __construct($id, Module $module, PolicyServiceInterface $policyService, array $config = [])
    {
        $this->policyService = $policyService;
        parent::__construct($id, $module, $config);
    }

    public function actionValidateCreate()
    {
        $form = new Policy([
            'scenario' => 'create',
        ]);
        $form->load(\Yii::$app->request->getBodyParams());
        return $this->asJson(ActiveForm::validate($form));
    }

    public function actionCreate()
    {
        $form = new Policy([
            'scenario' => 'create',
        ]);
        $form->load(\Yii::$app->request->getBodyParams());
        $this->policyService->addPolicy($form);
    }

    public function actionValidateUpdate()
    {
        $form = new Policy([
            'scenario' => 'update',
        ]);
        $form->load(\Yii::$app->request->getBodyParams());
        return $this->asJson(ActiveForm::validate($form));
    }

    public function actionUpdate($id)
    {
        $form = new Policy([
            'scenario' => 'create',
        ]);
        $form->load(\Yii::$app->request->getBodyParams());
        $this->policyService->updatePolicy($id, $form);
    }
}
