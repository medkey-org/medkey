<?php
namespace app\modules\medical\port\rest\controllers;

use app\common\db\ActiveRecord;
use app\common\filters\QueryParamAuth;
use app\common\rest\Controller;
use app\common\widgets\ActiveForm;
use app\modules\medical\models\orm\Speciality;
use yii\filters\AccessControl;
use yii\web\ForbiddenHttpException;

/**
 * Class SpecialityController
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
class SpecialityController extends Controller
{
    /**
     * @var string the scenario used for creating a model.
     * @see \yii\base\Model::scenarios()
     */
    public $createScenario = ActiveRecord::SCENARIO_CREATE;
    /**
     * @var string the scenario used for updating a model.
     * @see \yii\base\Model::scenarios()
     */
    public $updateScenario = ActiveRecord::SCENARIO_UPDATE;

    public $modelClass = Speciality::class;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'authenticator' => [
                'class' => QueryParamAuth::class,
                'isSession' => false,
                'optional' => [
                    '*',
                ],
            ],
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
                'denyCallback' => function () {
                    throw new ForbiddenHttpException(\Yii::t('yii', 'You are not allowed to perform this action.'));
                }
            ],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function verbs()
    {
        return [];
    }

    public function actionValidateCreate()
    {
        $modelClass = $this->modelClass;
        /** @var $modelClass ActiveRecord */
        $model = $modelClass::ensureWeak(null, $this->createScenario, \Yii::$app->getRequest()->getBodyParams());
        return $this->asJson(ActiveForm::validate($model));
    }

    public function actionValidateUpdate($id)
    {
        $modelClass = $this->modelClass;
        /** @var $modelClass ActiveRecord */
        $model = $modelClass::ensure($id, $this->updateScenario, \Yii::$app->getRequest()->getBodyParams());
        return $this->asJson(ActiveForm::validate($model));
    }

    public function actionCreate()
    {
        $modelClass = $this->modelClass;
        /** @var $modelClass ActiveRecord */
        $model = $modelClass::ensureWeak(null, $this->createScenario, \Yii::$app->request->getBodyParams());
        $model->save();

        return $this->asJson($model);
    }

    public function actionUpdate($id)
    {
        $modelClass = $this->modelClass;
        /** @var $modelClass ActiveRecord */
        $model = $modelClass::ensure($id, $this->updateScenario, \Yii::$app->getRequest()->getBodyParams());
        $model->save();

        return $this->asJson($model);
    }

    public function actionDelete($id)
    {
        $modelClass = $this->modelClass;
        /** @var $modelClass ActiveRecord */
        $model = $modelClass::ensure($id);
        $model->setScenario(ActiveRecord::SCENARIO_DELETE);
        $model->delete();
        return $this->asJson($model);
    }

    public function actionSpecialityList()
    {
        $specialities = Speciality::find()
            ->notDeleted()
            ->all();
        $result = [];
        foreach ($specialities as $speciality) {
            $result[] = $speciality->toArray();
        }
        return $this->asJson($result);
    }
}
