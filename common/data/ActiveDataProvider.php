<?php
namespace app\common\data;

use app\common\db\ResponsibilityEntityInterface;
use app\common\helpers\ClassHelper;
use app\modules\security\domain\services\ResponsibilityServiceInterface;
use app\modules\security\models\orm\Acl;

/**
 * Class ActiveDataProvider
 * @package Common\Data
 * @copyright 2012-2019 Medkey
 */
class ActiveDataProvider extends \yii\data\ActiveDataProvider
{
    public $aclRule = [];
    public $hideDeleted = true;
    public $aclService;

    /**
     * @inheritdoc
     */
    public function init()
    {
        if (!empty($this->query) && method_exists($this->query, 'notDeleted')) {
            $this->query->notDeleted();
        }
        $sort = $this->getSort();
        if ($sort && !$sort->defaultOrder && $this->query) {
            // Default sorting
            $sort->defaultOrder = ['updated_at' => SORT_DESC];
        }

        $isResp = false;
        if (ClassHelper::implementsInterface($this->query->modelClass, ResponsibilityEntityInterface::class)) {
            $isResp = true;
        }

        if ($isResp) {
            $this->query
                ->joinWith(['responsibilities as responsibilities'], true, 'INNER JOIN');
        }
        if (!empty($this->aclRule)) {
            $resTable = \Yii::$app->db->schema->getRawTableName(
                \Yii::$container->get(ResponsibilityServiceInterface::class)->getResponsibilityTable($this->query->modelClass::tableName())
            );

            foreach ($this->aclRule as $rule) {
                if ($rule & Acl::RULE_RESPONSIBILITY) {
                    // Responsible employee case
                    if ($isResp) {
                        $this->query
                            ->andWhere([
                                $resTable . '.employee_id' => \Yii::$app->user->getIdentity()->employee ? \Yii::$app->user->getIdentity()->employee->id : -1,
                            ]);
                    }
                } elseif ($rule & Acl::RULE_AUTHOR) {
                    // Author case
                    // Not implemented
                } elseif ($rule & Acl::RULE_DEPARTMENT) {
                    // Equal department case
                    // Not implemented
                } elseif ($rule & Acl::RULE_POSITION) {
                    // Equal position case
                    // Not implemented
                } elseif ($rule & Acl::RULE_USER_ORGANIZATION) {
                    // Equal organization case
                    $this->query
                        ->where([
                            'organization_id' => (
                                \Yii::$app->user->getIdentity()->organization_id
                                    ? \Yii::$app->user->getIdentity()->organization_id
                                    : -1
                            )
                        ]);
                }
            }
        }
        parent::init();
    }
}
