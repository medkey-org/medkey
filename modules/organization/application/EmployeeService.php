<?php
namespace app\modules\organization\application;

use app\common\base\Model;
use app\common\mail\EmailSenderServiceInterface;
use app\common\data\ActiveDataProvider;
use app\common\db\ActiveRecord;
use app\common\helpers\ArrayHelper;
use app\common\helpers\CommonHelper;
use app\common\helpers\Json;
use app\common\logic\orm\Address;
use app\common\logic\orm\Email;
use app\common\logic\orm\Phone;
use app\common\service\ApplicationService;
use app\common\service\exception\AccessApplicationServiceException;
use app\common\service\exception\ApplicationServiceException;
use app\modules\crm\models\orm\EmployeeToSpeciality;
use app\modules\organization\models\finders\EmployeeFinder;
use app\modules\organization\models\orm\Employee;
use app\modules\organization\OrganizationModule;
use yii\base\InvalidValueException;
use app\modules\organization\models\form\Employee as EmployeeForm;

/**
 * Class EmployeeService
 * @package Module\Organization
 * @copyright 2012-2019 Medkey
 */
class EmployeeService extends ApplicationService implements EmployeeServiceInterface
{
    public $emailSenderService;

    public function __construct(
        array $config = []
    ) {
        $this->emailSenderService = \Yii::$container->get(EmailSenderServiceInterface::class);
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function aclAlias()
    {
        return OrganizationModule::t('employee', 'Employee');
    }

    /**
     * @inheritdoc
     */
    public function getPrivileges()
    {
        return [
            'addEmployee' => OrganizationModule::t('employee', 'Add employee record'),
            'updateEmployee' => OrganizationModule::t('employee','Update employee record'),
            'getEmployeeById' => OrganizationModule::t('employee','Get employee record'),
            'getEmployeeList' => OrganizationModule::t('employee','Get employees list'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function getEmployeeList(Model $form)
    {
        /** @var $form EmployeeFinder */
        if (!$this->isAllowed('getEmployeeList')) {
            throw new AccessApplicationServiceException(OrganizationModule::t('employee', 'Access to the employees list restricted'));
        }
        $query = Employee::find();
        $query
            ->joinWith('phones')
            ->andFilterWhere([
                Employee::tableColumns('user_id') => $form->userId
            ])
            ->andFilterWhere([Employee::tableColumns('id') => $form->ids])
            ->andFilterWhere([
//                'phone' => $form->phone
                'like',
                'phone.phone',
                $form->phone
            ])
            ->andFilterWhere([
                'cast("employee"."updated_at" as date)' =>
                    empty($form->updatedAt) ? null : \Yii::$app->formatter->asDate($form->updatedAt, CommonHelper::FORMAT_DATE_DB)
            ])
            ->andFilterWhere([
                'sex' => $form->sex
            ])
            ->andFilterWhere([
                'or',
                ['ilike', 'last_name', $form->fullName],
                ['ilike', 'first_name', $form->fullName],
                ['ilike', 'middle_name', $form->fullName],
            ])
            ->andFilterWhere([
                'birthday' => empty($form->birthday) ? null : \Yii::$app->formatter->asDate($form->birthday, CommonHelper::FORMAT_DATE_DB)
            ]);
        return new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes' => [
                    'phone.phone' => [
                        'asc' => [
                            'phone.phone' => SORT_ASC,
                        ],
                        'desc' => [
                            'phone.phone' => SORT_DESC,
                        ],
                    ],
                    'birthday',
                    'sex',
                    'updated_at',
                    'fullName' => [
                        'asc' =>
                            ['last_name' => SORT_ASC, 'first_name' => SORT_ASC, 'middle_name' => SORT_ASC],
                        'desc' =>
                            ['last_name' => SORT_DESC, 'first_name' => SORT_DESC, 'middle_name' => SORT_DESC],
                    ]
                ],
            ],
            'pagination' => [
                'pageSize' => 20
            ],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getEmployeeById($id)
    {
        return Employee::findOne($id);
    }

    public function addEmployee(EmployeeForm $employeeForm)
    {
        if (!($employeeForm instanceof EmployeeForm)) {
            throw new InvalidValueException('Form is not instance ' . EmployeeForm::class . ' class');
        }
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $model = new Employee([
                'scenario' => ActiveRecord::SCENARIO_CREATE
            ]);
            $model->loadForm($employeeForm);
//            $model->skype_code = $this->createSkypeCode();
            if (!$model->save()) {
                throw new ApplicationServiceException(OrganizationModule::t('employee', 'Can\'t save employee record'));
            }
            $employeeForm->id = $model->id;
            $this->savePhones($model->id, $employeeForm->phones);
            $this->saveEmails($model->id, $employeeForm->emails);
            $this->saveAddresses($model->id, $employeeForm->addresses);
            $this->saveSpecialities($model->id, $employeeForm->specialities);

            if (is_array($employeeForm->emails)) {
                foreach ($employeeForm->emails as $email) {
                    if (empty($email['type']) || empty($email['address'])) {
                        continue;
                    }
//                    $this->sendSkypeCodeByEmail($email['address'], $model->skype_code);
                }
            }
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
        return $model;
    }

    public function updateEmployee($id, EmployeeForm $employeeForm)
    {
        if (!($employeeForm instanceof EmployeeForm)) {
            throw new InvalidValueException('Form is not instance ' . EmployeeForm::class . ' class');
        }
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            /** @var ActiveRecord $model */
            $model = Employee::findOneEx($id);
            $model->setScenario(ActiveRecord::SCENARIO_UPDATE);
            $model->loadForm($employeeForm);
            if (!$model->save()) {
                throw new ApplicationServiceException(OrganizationModule::t('employee', 'Can\'t save employee record'));
            }
            $employeeForm->id = $model->id;
            $this->savePhones($model->id, $employeeForm->phones);
            $this->saveEmails($model->id, $employeeForm->emails);
            $this->saveAddresses($model->id, $employeeForm->addresses);
            $this->saveSpecialities($model->id, $employeeForm->specialities);
            if (is_array($employeeForm->emails)) {
                foreach ($employeeForm->emails as $email) {
                    if (empty($email['type']) || empty($email['address'])) {
                        continue;
                    }
//                    $this->sendSkypeCodeByEmail($email['address'], $model->skype_code);
                }
            }
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
        return $model;
    }

    private function saveEmails($employeeId, $emails)
    {
        $employee = Employee::findOneEx($employeeId);
        $q = Email::find()
            ->where([
                'entity' => Employee::getTableSchema()->name,
                'entity_id' => $employee->id
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
            $model->entity = Employee::getTableSchema()->name;
            $model->entity_id = $employee->id;
            if (!$model->save()) {
                throw new ApplicationServiceException(OrganizationModule::t('employee', 'Can\'t save employee\'s E-mail(s)') . ': ' . Json::encode($model->getErrors()));
            }
        }
    }

    private function savePhones($employeeId, $phones)
    {
        $employee = Employee::findOneEx($employeeId);
        $q = Phone::find()
            ->where([
                'entity' => Employee::getTableSchema()->name,
                'entity_id' => $employee->id
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
            $model->entity = Employee::getTableSchema()->name;
            $model->entity_id = $employee->id;
            if (!$model->save()) {
                throw new ApplicationServiceException(OrganizationModule::t('employee', 'Can\'t save employee\'s phone(s)') . ': ' . Json::encode($model->getErrors()));
            }
        }
    }

    private function saveAddresses($employeeId, $addresses)
    {
        $employee = Employee::findOneEx($employeeId);
        $q = Address::find()
            ->where([
                'entity' => Employee::getTableSchema()->name,
                'entity_id' => $employee->id
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
            if (empty($address['type']) || empty($address['city'])) {
                continue;
            }
            $model = new Address();
            $model->setAttributes($address);
            $model->entity = Employee::getTableSchema()->name;
            $model->entity_id = $employee->id;
            if (!$model->save()) {
                throw new ApplicationServiceException(OrganizationModule::t('employee', 'Can\'t save employee\'s address(es)') . ': ' . Json::encode($model->getErrors()));
            }
        }
    }

    private function saveSpecialities($employeeId, $specialities)
    {
        $employee = Employee::findOneEx($employeeId);
        $q = EmployeeToSpeciality::find()
            ->where([
                'employee_id' => $employee->id
            ])
            ->notDeleted();
        $exists = $q->all();
        foreach ($exists as $p) {
            $p->delete();
        }
        if (!is_array($specialities)) {
            return null;
        }
        foreach ($specialities as $specialityId) {
            $model = new EmployeeToSpeciality();
            $model->speciality_id = $specialityId;
            $model->employee_id = $employee->id;
            if (!$model->save()) {
                throw new ApplicationServiceException(OrganizationModule::t('employee', 'Can\'t save employee\'s specialities') . ': ' . Json::encode($model->getErrors()));
            }
        }
    }

    /**
     * @param string $raw
     * @return EmployeeForm
     */
    public function getEmployeeForm($raw)
    {
        $model = Employee::ensureWeak($raw);
        $employeeForm = new EmployeeForm();
        if ($model->isNewRecord) {
            $employeeForm->setScenario('create');
        }
        $employeeForm->loadAr($model);
        $employeeForm->id = $model->id;
        $employeeForm->phones = ArrayHelper::toArray($model->phones);
        $employeeForm->emails = ArrayHelper::toArray($model->emails);
        $employeeForm->addresses = ArrayHelper::toArray($model->addresses);
        $employeeForm->user = ArrayHelper::toArray(!$model->user ? [] : $model->user);
        $employeeForm->specialities = ArrayHelper::getColumn($model->specialities, 'id');

        return $employeeForm;
    }

    private function createSkypeCode()
    {
        do {
            $code = substr(md5(\Yii::$app->security->generateRandomString()), 0, 6);
        } while (!is_null($this->getEmployeeBySkypeCode($code)));

        return $code;
    }

    /**
     * @param $text
     * @return array|bool|null|\yii\db\ActiveRecord
     */
    public function getEmployeeBySkypeCode($text)
    {
        $employee = Employee::find()
            ->where([
                'skype_code' => $text,
            ])
            ->notDeleted()
            ->one();
        if (isset($employee)) {
            return $employee;
        }

        return null;
    }

    public function getEmployeeBySkypeId($id)
    {
        $employee = Employee::find()
            ->where([
                'skype_bot_id' => $id,
            ])
            ->notDeleted()
            ->one();
        return $employee;
    }

    /**
     * @param string $date
     * @return array
     */
    public function getEmployeesWithAttendanceByDate(string $date)
    {
        $employees = Employee::find()
            ->joinWith([
                'attendances.ehr.patient',
                'speciality'
            ])
//            ->where([
//                'cast(datetime as DATE)' => $date, // todo CAST субд
//            ])
            ->all();
        return $employees;
    }
}
