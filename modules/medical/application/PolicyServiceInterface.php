<?php
namespace app\modules\medical\application;

use app\common\data\ActiveDataProvider;
use app\modules\medical\models\form\Policy as PolicyForm;
use app\modules\medical\models\finders\PolicyFilter;
use app\modules\medical\models\orm\Policy;
use yii\data\DataProviderInterface;

/**
 * Interface PolicyServiceInterface
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
interface PolicyServiceInterface
{
    /**
     * @param PolicyFilter $form
     * @return DataProviderInterface
     */
    public function getPolicyList(PolicyFilter $form): DataProviderInterface;
    public function addPolicy(PolicyForm $form) : Policy;
    public function updatePolicy(string $id, PolicyForm $form) : Policy;
    public function getPolicyForm($raw);
}
