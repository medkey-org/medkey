<?php
namespace app\modules\medical\application;

use app\common\data\ActiveDataProvider;
use app\common\db\ActiveRecord;
use app\common\db\Exception;
use app\common\helpers\ArrayHelper;
use app\common\helpers\CommonHelper;
use app\common\helpers\Json;
use app\common\logic\orm\Address;
use app\common\logic\orm\Email;
use app\common\logic\orm\Passport;
use app\common\logic\orm\Phone;
use app\common\service\ApplicationService;
use app\common\service\exception\AccessApplicationServiceException;
use app\common\service\exception\ApplicationServiceException;
use app\modules\medical\MedicalModule;
use app\modules\medical\models\finders\PatientFilter;
use app\modules\medical\models\orm\Patient;
use yii\base\InvalidConfigException;
use yii\base\InvalidValueException;
use yii\base\Model;
use app\modules\medical\models\form\Patient as PatientForm;
use yii\data\DataProviderInterface;
use yii\db\ActiveRecordInterface;

/**
 * Class PatientService
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
class PatientService extends ApplicationService implements PatientServiceInterface
{
    /**
     * @inheritdoc
     */
    public function getPrivileges()
    {
        return [
            'getPatientList' => MedicalModule::t('patient', 'Patient registry'),
            'getPatientById' => MedicalModule::t('patient', 'Patient'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function aclAlias()
    {
        return MedicalModule::t('patient', 'Patient');
    }

    protected function saveEmails($patientId, $emails)
    {
        $patient = Patient::findOneEx($patientId);
        $q = Email::find()
            ->where([
                'entity' => Patient::getTableSchema()->name,
                'entity_id' => $patient->id
            ])
            ->notDeleted();
        $exists = $q->all();
        foreach ($exists as $e) {
            $e->delete();
        }
        if (!is_array($emails)) {
            return null;
        }
        foreach ($emails as $email) {
            if (empty($email['type']) || empty($email['address'])) {
                continue;
            }
            $model = new Email();
            $model->setAttributes($email);
            $model->entity = Patient::getTableSchema()->name;
            $model->entity_id = $patient->id;
            if (!$model->save()) {
                throw new ApplicationServiceException(MedicalModule::t('patient', 'Can\'t save email. Reason:') . Json::encode($model->getErrors()));
            }
        }
    }

    protected function savePhones($patientId, $phones)
    {
        $patient = Patient::findOneEx($patientId);
        $q = Phone::find()
            ->where([
                'entity' => Patient::getTableSchema()->name,
                'entity_id' => $patient->id
            ])
            ->notDeleted();
        $exists = $q->all();
        foreach ($exists as $p) {
            $p->delete();
        }
        if (!is_array($phones)) {
            return null;
        }
        foreach ($phones as $phone) {
            if (empty($phone['type']) || empty($phone['phone'])) {
                continue;
            }
            $model = new Phone();
            $model->setAttributes($phone);
            $model->entity = Patient::getTableSchema()->name;
            $model->entity_id = $patient->id;
            if (!$model->save()) {
                throw new ApplicationServiceException(MedicalModule::t('patient', 'Can\'t save phone. Reason:'). Json::encode($model->getErrors()));
            }
        }
    }

    protected function saveAddresses($patientId, $addresses)
    {
        $patient = Patient::findOneEx($patientId);
        $q = Address::find()
            ->where([
                'entity' => Patient::getTableSchema()->name,
                'entity_id' => $patient->id
            ])
            ->notDeleted();
        $exists = $q->all();
        foreach ($exists as $p) {
            $p->delete();
        }
        if (!is_array($addresses)) {
            return null;
        }
        foreach ($addresses as $address) {
            if (!isset($address['type'])
                || !isset($address['city'])
                || empty($address['type'])
                || empty($address['city'])
            ) {
                continue;
            }
            $model = new Address();
            $model->setAttributes($address);
            $model->entity = Patient::getTableSchema()->name;
            $model->entity_id = $patient->id;
            $model->save();
        }
    }

    /**
     * @inheritdoc
     */
    public function addPatient(PatientForm $patientForm): ActiveRecordInterface
    {
        if (!($patientForm instanceof PatientForm)) {
            throw new InvalidValueException(MedicalModule::t('patient', 'Form is not an instance of class ') . PatientForm::class);
        }
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $model = new Patient([
                'scenario' => ActiveRecord::SCENARIO_CREATE
            ]);
            $model->loadForm($patientForm);
            if (!$model->save()) {
                throw new ApplicationServiceException(MedicalModule::t('patient', 'Can\'t save patient record'));
            }
            $patientForm->id = $model->id;
            $this->savePhones($model->id, $patientForm->phones);
            $this->saveEmails($model->id, $patientForm->emails);
            $this->saveAddresses($model->id, $patientForm->addresses);
            $this->savePassport($model->id, $patientForm->passportSeries, $patientForm->passportNumber);
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
        return $model;
    }

    /**
     * @param string|integer $patientId
     * @param string $series
     * @param string $number
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function savePassport($patientId, $series, $number)
    {
        $patient = Patient::findOneEx($patientId);
        $patient->unlinkAll('passport', true);
        $passport = new Passport();
        $passport->series = $series;
        $passport->number = $number;
        $passport->entity_id = $patientId;
        $passport->entity = Patient::getTableSchema()->name;
        $passport->save();
    }

    /**
     * @inheritdoc
     */
    public function updatePatient(string $id, PatientForm $patientForm): ActiveRecordInterface
    {
        if (!($patientForm instanceof PatientForm)) {
            throw new InvalidValueException(MedicalModule::t('patient', 'Form is not an instance of class ') . PatientForm::class);
        }
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            /** @var ActiveRecord $model */
            $model = Patient::findOneEx($id);
            $model->setScenario(ActiveRecord::SCENARIO_UPDATE);
            $model->loadForm($patientForm);
            if (!$model->save()) {
                throw new ApplicationServiceException(MedicalModule::t('patient', 'Can\'t save patient record'));
            }
            $patientForm->id = $model->id;
            $this->savePhones($model->id, $patientForm->phones);
            $this->saveEmails($model->id, $patientForm->emails);
            $this->saveAddresses($model->id, $patientForm->addresses);
            $this->savePassport($model->id, $patientForm->passportSeries, $patientForm->passportNumber);
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
        return $model;
    }

    /**
     * @inheritdoc
     */
    public function getPatientList(Model $form): DataProviderInterface
    {
        /** @var $form PatientFilter */
        if (!$this->isAllowed('getPatientList')) {
            throw new AccessApplicationServiceException(MedicalModule::t('patient', 'Access restricted'));
        }
        $query = Patient::find();
        $query
            ->andFilterWhere([
                'birthday' => empty($form->birthday) ? null : \Yii::$app->formatter->asDate($form->birthday, CommonHelper::FORMAT_DATE_DB),
                'cast(updated_at as date)' =>
                    empty($form->updatedAt) ? null : \Yii::$app->formatter->asDate($form->updatedAt, CommonHelper::FORMAT_DATE_DB),
            ])
            ->andFilterWhere([
                'like',
                'last_name',
                $form->fullName,
            ])
            ->orFilterWhere([
                'like',
                'first_name',
                $form->fullName,
            ])
            ->orFilterWhere([
                'like',
                'middle_name',
                $form->fullName,
            ]);
        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10
            ],
        ]);
    }

    public function getPatientById($id): ActiveRecordInterface
    {
        if (!$this->isAllowed('getPatientById')) {
            throw new AccessApplicationServiceException(MedicalModule::t('patient', 'Access restricted'));
        }
        return Patient::find()
            ->notDeleted()
            ->joinWith(['policies.insurance'])
            ->where([
                Patient::tableColumns('id') => $id,
            ])
            ->one();
    }

    /**
     * @param string $raw
     * @return PatientForm
     */
    public function getPatientForm($raw)
    {
        $model = Patient::ensureWeak($raw);
        $phones = ArrayHelper::toArray($model->phones);
        $emails = ArrayHelper::toArray($model->emails);
        $addresses = ArrayHelper::toArray($model->addresses);
        $ehr = ArrayHelper::toArray($model->ehr);
        $patientForm = new PatientForm();
        if ($model->isNewRecord) {
            $patientForm->setScenario('create');
        }
        $patientForm->loadAr($model);
        $patientForm->id = $model->id;
        $patientForm->phones = $phones;
        $patientForm->emails = $emails;
        $patientForm->addresses = $addresses;
        $patientForm->ehr = $ehr;
        if (isset($model->passport)) {
            $patientForm->passportSeries = $model->passport->series;
            $patientForm->passportNumber = $model->passport->number;
        }
        return $patientForm;
    }
}
