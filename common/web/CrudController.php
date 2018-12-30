<?php
namespace app\common\web;

use app\common\helpers\ClassHelper;
use app\common\widgets\ActiveForm;
use app\common\db\ActiveRecord;
use yii\base\InvalidValueException;
use yii\filters\VerbFilter;

/**
 * Class CrudController
 * @package Common\Web
 * @copyright 2012-2019 Medkey
 *
 * @deprecated
 */
abstract class CrudController extends Controller
{
    /**
     * @var string the model class name. This property must be set.
     */
    public $modelClass;
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
        ];
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if (!isset($this->modelClass)) {
            $module = $this->module;
            $ns = ClassHelper::getNamespace($module);
            /** @var ActiveRecord $modelClass */
            $this->modelClass = $ns . '\models\orm\\' . str_replace('Controller', '', ClassHelper::getShortName($this));
            if (!class_exists($this->modelClass)) {
                /** @var ActiveRecord $modelClass */
                $this->modelClass = 'app\common\logic\orm\\' . str_replace('Controller', '', ClassHelper::getShortName($this));
                if (!class_exists($this->modelClass)) {
                    throw new InvalidValueException("Orm `{$this->modelClass}` is not found");
                }
            }
        }
    }

    /**
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * @param mixed $id
     *
     * @return string
     */
    public function actionView($id = null)
    {
        $modelClass = $this->modelClass;
        /** @var $modelClass ActiveRecord */
        $model = $modelClass::ensureWeak($id);

        return $this->render('view', compact('model'));
    }

    /**
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function actionValidateCreate()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $modelClass = $this->modelClass;
        /** @var $modelClass ActiveRecord */
        $model = $modelClass::ensureWeak(null, $this->createScenario, \Yii::$app->getRequest()->getBodyParams());

        return ActiveForm::validate($model);
    }

    /**
     * @param $id
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function actionValidateUpdate($id)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $modelClass = $this->modelClass;
        /** @var $modelClass ActiveRecord */
        $model = $modelClass::ensure($id, $this->updateScenario, \Yii::$app->getRequest()->getBodyParams());

        return ActiveForm::validate($model);
    }

    /**
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function actionCreate()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $modelClass = $this->modelClass;
        /** @var $modelClass ActiveRecord */
        $model = $modelClass::ensureWeak(null, $this->createScenario, \Yii::$app->request->getBodyParams());
        $model->save();

        return $model;
    }

    /**
     * @param $id
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function actionUpdate($id)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $modelClass = $this->modelClass;
        /** @var $modelClass ActiveRecord */
        $model = $modelClass::ensure($id, $this->updateScenario, \Yii::$app->getRequest()->getBodyParams());
        $model->save();

        return $model;
    }

    /**
     * @param mixed $id
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
