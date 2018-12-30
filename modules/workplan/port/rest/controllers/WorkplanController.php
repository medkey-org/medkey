<?php
namespace app\modules\workplan\port\rest\controllers;

use app\common\rest\Controller;
use app\common\widgets\ActiveForm;
use app\modules\workplan\application\WorkplanServiceInterface;
use yii\base\Module;

/**
 * Class WorkplanController
 *
 * @package Module\Workplan
 * @copyright 2012-2019 Medkey
 */
class WorkplanController extends Controller
{
    /**
     * @var WorkplanServiceInterface
     */
    public $workplanService;


    /**
     * WorkplanController constructor.
     * @param string $id
     * @param Module $module
     * @param WorkplanServiceInterface $workplanService
     * @param array $config
     */
    public function __construct($id, Module $module, WorkplanServiceInterface $workplanService, array $config = [])
    {
        $this->workplanService = $workplanService;
        parent::__construct($id, $module, $config);
    }

    public function actionValidateCreate()
    {
        $form = $this->workplanService->getWorkplanForm(null, 'create');
        $form->load(\Yii::$app->request->getBodyParams());
        return $this->asJson(ActiveForm::validate($form));
    }

    public function actionCreate()
    {
        $form = $this->workplanService->getWorkplanForm(null, 'create');
        $form->load(\Yii::$app->request->getBodyParams());
        $this->workplanService->addWorkplan($form);
    }

    public function actionValidateUpdate()
    {
        $form = $this->workplanService->getWorkplanForm(null, 'update');
        $form->load(\Yii::$app->request->getBodyParams());
        return $this->asJson(ActiveForm::validate($form));
    }

    public function actionUpdate($id)
    {
        $form = $this->workplanService->getWorkplanForm(null, 'update');
        $form->load(\Yii::$app->request->getBodyParams());
        $this->workplanService->updateWorkplan($id, $form);
    }
}
