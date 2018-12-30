<?php
namespace app\modules\medical\port\rest\controllers;

use app\common\base\Module;
use app\common\data\ActiveDataProvider;
use app\common\db\ActiveRecord;
use app\common\rest\ActiveController;
use app\common\widgets\ActiveForm;
use app\modules\medical\application\EhrServiceInterface;
use app\modules\medical\models\orm\Ehr;
use yii\db\ActiveRecordInterface;

/**
 * Class EhrController
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
class EhrController extends ActiveController
{
    /**
     * @var string the model class name. This property must be set.
     */
    public $modelClass = Ehr::class;
    public $ehrService;

    public function __construct($id, Module $module, EhrServiceInterface $ehrService, array $config = [])
    {
        $this->ehrService = $ehrService;
        parent::__construct($id, $module, $config);
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

    /**
     * @param string $q
     * @return ActiveDataProvider
     */
    public function actionIndex($q)
    {
        /* @var $modelClass ActiveRecordInterface */
        $modelClass = $this->modelClass;
        $query = $modelClass::find()
            ->where([
                'like',
                'number',
                $q
            ]);
        /** @var ActiveDataProvider $provider */
        $provider = \Yii::createObject([
            'class' => ActiveDataProvider::className(),
            'query' => $query
        ]);
        return $provider;
    }

    public function actionGetEhrById($id)
    {
        return $this->asJson($this->ehrService->getEhrById($id)->toArray([], ['patient.policies.insurance']));
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
}
