<?php
namespace app\modules\medical\application;

use app\common\data\ActiveDataProvider;
use app\common\db\ActiveRecord;
use app\common\helpers\Json;
use app\common\service\ApplicationService;
use app\common\service\exception\ApplicationServiceException;
use app\modules\medical\models\finders\PolicyFilter;
use app\modules\medical\models\orm\Policy;
use app\modules\medical\models\form\Policy as PolicyForm;

/**
 * Class PolicyService
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
class PolicyService extends ApplicationService implements PolicyServiceInterface
{
    /**
     * @inheritdoc
     */
    public function addPolicy(PolicyForm $form) : Policy
    {
        $policy = new Policy([
            'scenario' => ActiveRecord::SCENARIO_CREATE,
        ]);
        $policy->loadForm($form);
        if (!$policy->save()) {
            throw new ApplicationServiceException('Не удалось сохранить полис, причина: ' . Json::encode($policy->getErrors()));
        }
        return $policy;
    }

    /**
     * @inheritdoc
     */
    public function updatePolicy(string $id, PolicyForm $form) : Policy
    {
        $policy = Policy::findOneEx($id);
        $policy->loadForm($form);
        if (!$policy->save()) {
            throw new ApplicationServiceException('Не удалось сохранить полис, причина: ' . Json::encode($policy->getErrors()));
        }
        return $policy;
    }

    /**
     * @inheritdoc
     */
    public function getPolicyForm($raw)
    {
        $orm = Policy::ensureWeak($raw);
        $form = new PolicyForm();
        if ($orm->isNewRecord) {
            $form->setScenario('create');
        }
        $form->loadAr($orm);
        $form->id = $orm->id;
        return $form;
    }

    /**
     * @inheritdoc
     */
    public function getPolicyList(PolicyFilter $form)
    {
//        if (!$this->isAllowed('getPolicyList')) {
//            throw new AccessApplicationServiceException('Доступ запрещен.');
//        }
        $query = Policy::find()
            ->where([
                'patient_id' => $form->patientId,
            ]);
        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10
            ],
        ]);
    }
}
