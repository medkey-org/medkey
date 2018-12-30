<?php
namespace app\modules\security\domain\services;

/**
 * Class ResponsibilityServiceInterface
 * @package Module\Security
 * @copyright 2012-2019 Medkey
 */
interface ResponsibilityServiceInterface
{
    public function assignEmployeeByEntity($entityId, $employeeId, $entityTable);
    public function getResponsibilityTable($table);
}
