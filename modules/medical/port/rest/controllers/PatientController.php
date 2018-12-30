<?php
namespace app\modules\medical\port\rest\controllers;

use app\common\base\Module;
use app\common\data\ActiveDataProvider;
use app\common\db\ActiveRecord;
use app\common\rest\ActiveController;
use app\common\widgets\ActiveForm;
use app\modules\medical\models\orm\Patient;
use app\modules\medical\application\PatientServiceInterface;
use yii\db\ActiveRecordInterface;
use app\modules\medical\models\form\Patient as PatientForm;

/**
 * Class PatientController
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
class PatientController extends ActiveController
{
    /**
     * @var string the model class name. This property must be set.
     */
    public $modelClass = Patient::class;
    /**
     * @var PatientServiceInterface
     */
    public $patientService;


    /**
     * PatientController constructor.
     * @param string $id
     * @param PatientServiceInterface $patientService
     * @param Module $module
     * @param array $config
     */
    public function __construct($id, Module $module, PatientServiceInterface $patientService, array $config = [])
    {
        $this->patientService = $patientService;
        parent::__construct($id, $module, $config);
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
                'last_name',
                $q
            ])
            ->orWhere([
                'like',
                'first_name',
                $q
            ])
            ->orWhere([
                'like',
                'middle_name',
                $q
            ]);
        /** @var ActiveDataProvider $provider */
        $provider = \Yii::createObject([
            'class' => ActiveDataProvider::class,
            'query' => $query
        ]);
        return $provider;
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

    public function actionGetPatientById($id)
    {
//        $employees = $this->patientService->getPatientById($id);
//        $result = [];
//        foreach ($employees as $employee) {
//            /** @var Employee $employee */
//            $result[] = $employee->toArray([], ['policies']);
//        }
//        return $this->asJson($result);
        return $this->asJson($this->patientService->getPatientById($id)->toArray([], ['policies.insurance']));
    }

    /**
     * @todo getPatientForm
     * @return \yii\web\Response
     */
    public function actionValidateCreate()
    {
        $form = new PatientForm([
            'scenario' => 'create'
        ]);
        $form->load(\Yii::$app->getRequest()->getBodyParams());
        return $this->asJson(ActiveForm::validate($form));
    }

    public function actionValidateUpdate()
    {
        $form = new PatientForm([
            'scenario' => 'update'
        ]);
        $form->load(\Yii::$app->getRequest()->getBodyParams());
        return $this->asJson(ActiveForm::validate($form));
    }

    public function actionCreate()
    {
        $patientForm = new PatientForm([
            'scenario' => 'create',
        ]);
        $patientForm->load(\Yii::$app->getRequest()->getBodyParams());
        return $this->asJson($this->patientService->addPatient($patientForm));
    }

    public function actionUpdate($id)
    {
        $patientForm = new PatientForm([
            'scenario' => 'update'
        ]);
        $patientForm->load(\Yii::$app->getRequest()->getBodyParams());
        return $this->asJson($this->patientService->updatePatient($id, $patientForm));
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
