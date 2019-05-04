<?php
namespace app\modules\medical\port\rest\controllers;

use app\common\db\ActiveRecord;
use app\common\web\Controller;
use app\common\widgets\ActiveForm;
use app\modules\medical\application\EhrServiceInterface;
use app\modules\medical\models\orm\EhrRecord;
use app\modules\medical\models\form\EhrRecord as EhrRecordForm;

/**
 * Class EhrRecordController
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
class EhrRecordController extends Controller
{
    /**
     * @var EhrServiceInterface
     */
    public $ehrService;

    /**
     * EhrRecordController constructor.
     * @param $id
     * @param $module
     * @param EhrServiceInterface $ehrService
     * @param array $config
     */
    public function __construct($id, $module, EhrServiceInterface $ehrService, array $config = [])
    {
        $this->ehrService = $ehrService;
        parent::__construct($id, $module, $config);
    }

    public function actionValidateCreate()
    {
        $form = new EhrRecordForm();
        $form->load(\Yii::$app->getRequest()->getBodyParams());
        return $this->asJson(ActiveForm::validate($form));
    }

//    public function actionValidateUpdate($id)
//    {
//        $modelClass = $this->modelClass;
//        /** @var $modelClass ActiveRecord */
//        $model = $modelClass::ensure($id, 'update', \Yii::$app->getRequest()->getBodyParams());
//        return $this->asJson(ActiveForm::validate($model));
//    }

    public function actionCreate()
    {
        $form = new EhrRecordForm();
        $form->load(\Yii::$app->getRequest()->getBodyParams());
        return $this->asJson($this->ehrService->createEhrRecord($form));
    }

//    public function actionUpdate($id)
//    {
//        $modelClass = $this->modelClass;
//        /** @var $modelClass ActiveRecord */
//        $model = $modelClass::ensure($id, 'update', \Yii::$app->getRequest()->getBodyParams());
//        $model->save();
//
//        return $this->asJson($model);
//    }

    public function actionDelete($id)
    {
        // TODO in service
        $model = EhrRecord::ensure($id);
        $model->setScenario(ActiveRecord::SCENARIO_DELETE);
        $model->delete();
        return $this->asJson($model);
    }
}
