<?php
namespace app\modules\medical\port\rest\controllers;

use app\common\web\Response;
use app\common\widgets\ActiveForm;
use app\common\db\ActiveRecord;
use app\modules\medical\models\orm\Insurance;
use yii\filters\VerbFilter;
use app\common\rest\Controller;

/**
 * Class InsuranceController
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
class InsuranceController extends Controller
{
    public $modelClass = Insurance::class;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbFilter' => [
                'class' => VerbFilter::class,
                'actions' => $this->verbs(),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    protected function verbs()
    {
        return [
//            'index' => ['GET'],
//            'view' => ['GET'],
//            'validate-create' => ['POST'],
//            'validate-update' => ['POST'],
//            'create' => ['POST'],
//            'update' => ['POST'],
//            'delete' => ['GET'],
        ];
    }

    /**
     * @return mixed json
     */
    public function actionValidateCreate()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $modelClass = $this->modelClass;
        /** @var $modelClass ActiveRecord */
        $model = $modelClass::ensureWeak(null, ActiveRecord::SCENARIO_CREATE, \Yii::$app->getRequest()->getBodyParams());

        return ActiveForm::validate($model);
    }

    /**
     * @param mixed $id
     *
     * @return mixed json
     */
    public function actionValidateUpdate($id)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $modelClass = $this->modelClass;
        /** @var $modelClass ActiveRecord */
        $model = $modelClass::ensure($id, ActiveRecord::SCENARIO_UPDATE, \Yii::$app->getRequest()->getBodyParams());

        return ActiveForm::validate($model);
    }

    /**
     * @return ActiveRecord json
     */
    public function actionCreate()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $modelClass = $this->modelClass;
        /** @var $modelClass ActiveRecord */
        $model = $modelClass::ensureWeak(null, ActiveRecord::SCENARIO_CREATE, \Yii::$app->request->getBodyParams());
        $model->save();

        return $model;
    }

    /**
     * @param mixed $id
     *
     * @return ActiveRecord json
     */
    public function actionUpdate($id)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $modelClass = $this->modelClass;
        /** @var $modelClass ActiveRecord */
        $model = $modelClass::ensure($id, ActiveRecord::SCENARIO_UPDATE, \Yii::$app->getRequest()->getBodyParams());
        $model->save();

        return $model;
    }

    /**
     * @param mixed $id
     *
     * @return void
     */
    public function actionDelete($id)
    {
        $modelClass = $this->modelClass;
        /** @var $modelClass ActiveRecord */
        $model = $modelClass::ensure($id);
        $model->setScenario(ActiveRecord::SCENARIO_DELETE);
        $model->delete();
    }
}
