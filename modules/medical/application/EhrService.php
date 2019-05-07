<?php
namespace app\modules\medical\application;

use app\common\data\ActiveDataProvider;
use app\common\helpers\CommonHelper;
use app\common\helpers\Json;
use app\common\service\ApplicationService;
use app\common\service\exception\AccessApplicationServiceException;
use app\common\service\exception\ApplicationServiceException;
use app\modules\medical\MedicalModule;
use app\modules\medical\models\finders\EhrFilter;
use app\modules\medical\models\orm\Ehr;
use app\modules\medical\models\orm\EhrRecord;
use app\modules\medical\models\form\EhrRecord as EhrRecordForm;
use yii\base\Model;

/**
 * Class EhrApplicationService
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
class EhrService extends ApplicationService implements EhrServiceInterface
{
    /**
     * {@inheritdoc}
     */
    public function getEhrById($id): Ehr
    {
        return Ehr::find()
            ->notDeleted()
            ->joinWith(['patient.policies.insurance'])
            ->where([
                Ehr::tableColumns('id') => $id,
            ])
            ->one();
    }

    /**
     * {@inheritdoc}
     */
    public function createEhrRecord($form)
    {
        $model = new EhrRecord();
        $model->loadForm($form);
        if (!empty($model->revisit)) {
            $model->revisit = \Yii::$app->formatter->asDatetime($model->revisit, CommonHelper::FORMAT_DATETIME_DB);
        }
        if (!empty($model->datetime)) {
            $model->datetime = \Yii::$app->formatter->asDatetime($model->datetime, CommonHelper::FORMAT_DATETIME_DB);
        }
        if (!$model->save()) {
            throw new ApplicationServiceException(MedicalModule::t('ehr', 'Can\'t create ehr record' + Json::encode($model->getErrors())));
        }
        return $model;
    }

    /**
     * {@inheritdoc}
     */
    public function updateEhrRecord($id, $form)
    {
        $model = EhrRecord::findOneEx($id);
        $model->loadForm($form);
        if (!empty($model->revisit)) {
            $model->revisit = \Yii::$app->formatter->asDatetime($model->revisit, CommonHelper::FORMAT_DATETIME_DB);
        }
        if (!empty($model->datetime)) {
            $model->datetime = \Yii::$app->formatter->asDatetime($model->datetime, CommonHelper::FORMAT_DATETIME_DB);
        }
        if (!$model->save()) {
            throw new ApplicationServiceException(MedicalModule::t('ehr', 'Can\'t update ehr record' + Json::encode($model->getErrors())));
        }
        return $model;
    }

    /**
     * {@inheritdoc}
     */
    public function getEhrRecordFormByRaw($raw, $ehrId)
    {
        $model = EhrRecord::ensureWeak($raw);
        $form = new EhrRecordForm();
        if ($model->isNewRecord) {
            $form->setScenario('create');
        } else {
            $form->setScenario('update');
        }
        $form->loadAr($model);
        $form->id = $model->id;
        $form->ehr_id = $ehrId;
        if (!empty($form->revisit)) {
            $form->revisit = \Yii::$app->formatter->asDatetime($form->revisit, CommonHelper::FORMAT_DATETIME_UI);
        }
        if (!empty($model->datetime)) {
            $form->datetime = \Yii::$app->formatter->asDatetime($form->datetime, CommonHelper::FORMAT_DATETIME_UI);
        }
        if (!\Yii::$app->user->getIdentity()->employee) {
            throw new ApplicationServiceException(\Yii::t('app','Employee not found.'));
        }
        $form->employee_id = \Yii::$app->user->getIdentity()->employee->id;
        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function getEhrList(Model $form): ActiveDataProvider
    {
        /** @var $form EhrFilter */
        if (!$this->isAllowed('getEhrList')) {
            throw new AccessApplicationServiceException(MedicalModule::t('ehr', 'Access restricted'));
        }
        $query = Ehr::find()
            ->andFilterWhere([
                'patient_id' => $form->patientId,
                'cast(updated_at as date)' =>
                    empty($form->updatedAt) ? null : \Yii::$app->formatter->asDate($form->updatedAt, CommonHelper::FORMAT_DATE_DB),
                'type' => $form->type,
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
    public function getPrivileges()
    {
        return [
            'getEhrList' => MedicalModule::t('ehr', 'EHR list'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function aclAlias()
    {
        return MedicalModule::t('ehr', 'EHR');
    }
}
