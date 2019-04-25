<?php
namespace app\modules\organization\application;

use app\common\base\Model;
use app\modules\organization\models\form\Employee as EmployeeForm;

/**
 * Interface EmployeeServiceInterface
 * @package Module\Organization
 * @copyright 2012-2019 Medkey
 */
interface EmployeeServiceInterface
{
    public function getEmployeeById($id);
    public function getEmployeeList(Model $form);
    public function getEmployeeForm($raw);
    public function addEmployee(EmployeeForm $employeeForm);
    public function updateEmployee($id, EmployeeForm $employeeForm);
    public function getEmployeeBySkypeCode($text);
    public function getEmployeeBySkypeId($id);
    public function getEmployeesWithAttendanceByDate(string $date);
}
