<?php
namespace app\modules\security\domain\services;

/**
 * Class ResponsibilityService
 * @package Module\Security
 * @copyright 2012-2019 Medkey
 */
class ResponsibilityService implements ResponsibilityServiceInterface
{
    /**
     * @todo check db->getTableSchema(...
     * @inheritdoc
     */
    public function assignEmployeeByEntity($entityId, $employeeIds, $entityTable)
    {
        if (!is_array($employeeIds) || empty($employeeIds)) {
            return null;
        }
        $rawTable = \Yii::$app->db->schema->getRawTableName($entityTable);
        $resTable = $this->getResponsibilityTable($rawTable);
        $counts = 0;
        \Yii::$app
            ->db
            ->createCommand()
            ->delete($resTable,  [$rawTable . '_id' => $entityId])
            ->execute();
        foreach ($employeeIds as $employeeId) {
            $counts += \Yii::$app
                ->db
                ->createCommand()
                ->insert(
                    $resTable,
                    [$rawTable . '_id' => $entityId, 'employee_id' => $employeeId]
                )->execute();
        }
        return $counts;
    }

    public function getResponsibilityTable($table)
    {
        return '{{%res__' . \Yii::$app->db->schema->getRawTableName($table) . '}}';
    }
}
