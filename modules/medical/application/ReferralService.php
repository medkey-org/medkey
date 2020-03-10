<?php
namespace app\modules\medical\application;

use app\common\data\ActiveDataProvider;
use app\common\db\ActiveRecord;
use app\common\dto\Dto;
use app\common\helpers\CommonHelper;
use app\common\service\ApplicationService;
use app\common\service\exception\AccessApplicationServiceException;
use app\common\service\exception\ApplicationServiceException;
use app\common\service\StateMachineInterface;
use app\modules\medical\MedicalModule;
use app\modules\medical\models\finders\ReferralFilter;
use app\modules\medical\models\finders\ReferralItemFilter;
use app\modules\medical\models\orm\Patient;
use app\modules\medical\models\orm\Referral;
use app\modules\medical\models\orm\ReferralItem;
use app\modules\medical\models\orm\Service;
use app\modules\medical\models\orm\Speciality;
use yii\base\InvalidValueException;
use yii\base\Model;

/**
 * Class ReferralService
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
class ReferralService extends ApplicationService implements ReferralServiceInterface
{
    /**
     * @inheritdoc
     */
    public function getPrivileges()
    {
        return [
            'getReferralList' => MedicalModule::t('referral', 'Get referral list'),
            'getReferralById' => MedicalModule::t('referral', 'Get referral card'),
            'getReferralItemList' => MedicalModule::t('referral', 'Get referral items list'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function aclAlias()
    {
        return MedicalModule::t('referral', 'Referral');
    }

    /**
     * @inheritdoc
     */
    public function getReferralById($id)
    {
        if (!$this->isAllowed('getReferralById')) {
            throw new AccessApplicationServiceException(MedicalModule::t('referral', 'Access restricted'));
        }
        return Referral::findOne($id);
    }

    /**
     * @param ReferralItemFilter $form
     * @return ActiveDataProvider
     */
    public function getReferralItemList(ReferralItemFilter $form)
    {
        $query = ReferralItem::find();
        if (!empty($form->referralId)) {
            $query
                ->distinct(true)
                ->joinWith(['referral'])
                ->andFilterWhere([
                    Referral::tableColumns('id') => $form->referralId,
                ]);
        }
        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10
            ],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getReferralList(Model $form)
    {
        /** @var $form ReferralFilter */

        if (!$this->isAllowed('getReferralList')) {
            throw new AccessApplicationServiceException(MedicalModule::t('referral', 'Access restricted'));
        }

        $query = Referral::find();
        if (!empty($form->patientId)) {
            $query
                ->distinct(true)
                ->joinWith(['ehr.patient'])
                ->andFilterWhere([
                    Patient::tableColumns('id') => $form->patientId,
                ]);
        }

        if (!empty($form->orderId)) {
            $query->andWhere([
                'order_id' => $form->orderId,
            ]);
        }

        $query
            ->andFilterWhere([
                'ehr_id' => $form->ehrId,
                'status' => $form->status,
                'cast(updated_at as date)' =>
                    empty($form->updatedAt) ? null : \Yii::$app->formatter->asDate($form->updatedAt, CommonHelper::FORMAT_DATE_DB)
            ])
            ->andFilterWhere([
                'like',
                'number',
                $form->number,
            ]);
        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10
            ],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function generateReferralByOrder(Dto $orderDto)
    {
        $result = [];
        if (!$orderDto instanceof Dto || empty($orderDto->orderItems)) {
            throw new ApplicationServiceException(MedicalModule::t('referral', 'Given order is empty'));
        }
        $specialities = [];
        foreach ($orderDto->orderItems as $item) {
            $service = Service::findOneEx($item['service_id']);
            if (empty($specialities[$service->speciality_id])) {
                $specialities[$service->speciality_id][] = $service->id;
            }
        }
        if (!empty($specialities)) {
            foreach ($specialities as $specialityId => $services) {
                $referral = new Referral(['scenario' => ActiveRecord::SCENARIO_CREATE]);
                $ehrId = $orderDto->ehr_id;
                $orderId = $orderDto->id;
                $result[] = $referral->generateReferral($ehrId, $orderId, $services);
            }
        }
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function getEmployeesByReferral($referralId)
    {
        $referral = Referral::findOneEx($referralId);
        $result = [];
        foreach ($referral->referralItems as $referral) {
            $service = $referral->service;
            if (!$service instanceof Service) {
                throw new ApplicationServiceException(MedicalModule::t('referral', 'Can\'t find service for one or more of order\'s positions'));
            }
            $speciality = $service->speciality;
            if (!$speciality instanceof Speciality) {
                throw new ApplicationServiceException(MedicalModule::t('referral', 'Can\'t find speciality for service') . ' ' . $service->title);
            }
            $employees = $speciality->employees;
            foreach ($employees as $employee) {
                $result[] = $employee->id;
            }
        }
        return array_unique($result);
    }
}
