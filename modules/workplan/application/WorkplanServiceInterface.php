<?php
namespace app\modules\workplan\application;

use app\common\base\Model;
use app\common\db\ActiveRecord;
use app\modules\workplan\models\form\Workplan;
use yii\data\DataProviderInterface;
use app\modules\workplan\models\form\Workplan as WorkplanForm;

/**
 * Interface WorkplanApplicationServiceInterface
 * @package Module\Workplan
 * @copyright 2012-2019 Medkey
 */
interface WorkplanServiceInterface
{
    /**
     * @param WorkplanForm $form
     * @param string $scenario
     * @return ActiveRecord
     */
    public function addWorkplan(WorkplanForm $form, $scenario = ActiveRecord::SCENARIO_CREATE);

    /**
     * @param string $id
     * @param WorkplanForm $form
     * @param string $scenario
     * @return ActiveRecord
     */
    public function updateWorkplan($id, WorkplanForm $form, $scenario = ActiveRecord::SCENARIO_UPDATE);

    /**
     * @param Model $form
     * @return DataProviderInterface
     */
    public function getWorkplanList(Model $form);

    /**
     * @param string $employeeId
     * @param string $date
     * @return mixed // todo
     */
    public function getWorkplansByExistsRules($employeeId, $date);

    /**
     * @param string $employeeId
     * @param string $date
     * @return mixed // todo
     */
    public function getScheduleMedworkerTimes($employeeId, $date);

    /**
     * @param mixed $raw
     * @param string $scenario
     * @return Workplan
     */
    public function getWorkplanForm($raw, $scenario = 'create');
    public function getScheduleMedworkerTimesWithAttendances($employeeId, $date);
}
