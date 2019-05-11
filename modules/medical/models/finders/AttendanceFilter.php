<?php
namespace app\modules\medical\models\finders;

use app\common\base\Model;
use app\common\validators\ForeignKeyValidator;

/**
 * Class AttendanceFinder
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
class AttendanceFilter extends Model
{
    public $ehrId;
    public $status;
    public $datetime;
    public $type;
    public $updatedAt;
    public $referralId;
    public $patientId;
    public $employeeId;
    public $cabinetNumber;
    public $employeeFullName;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [ ['ehrId', 'referralId', 'patientId', 'employeeId', ], ForeignKeyValidator::class ],
            [ ['datetime', 'updatedAt', 'cabinetNumber', 'employeeFullName'], 'string', 'on' => 'search' ],
            [ ['status', 'type'], 'integer', 'on' => 'search' ],
        ];
    }
}
