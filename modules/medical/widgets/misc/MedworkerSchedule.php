<?php
namespace app\modules\medical\widgets\misc;

use app\common\helpers\CommonHelper;
use app\common\helpers\Html;
use app\common\widgets\Widget;
use app\modules\medical\models\orm\Attendance;
use app\modules\medical\models\orm\Ehr;
use app\modules\medical\application\AttendanceServiceInterface;
use app\modules\workplan\application\WorkplanServiceInterface;
use yii\base\InvalidValueException;

/**
 * Class MedworkerSchedule
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
class MedworkerSchedule extends Widget
{
    /**
     * @var string
     */
    public $employeeId;
    /**
     * @var Ehr
     */
    public $ehrId;
    /**
     * @var string
     */
    public $referralId;
    /**
     * @var string
     */
    public $date;
    /**
     * @var WorkplanServiceInterface
     */
    private $workplanService;
    /**
     * @var AttendanceServiceInterface
     */
    private $attendanceService;

    /**
     * MedworkerSchedule constructor.
     * @param WorkplanServiceInterface $workplanService
     * @param AttendanceServiceInterface $attendanceService
     * @param array $config
     */
    public function __construct(
        WorkplanServiceInterface $workplanService,
        AttendanceServiceInterface $attendanceService,
        array $config = []
    )
    {
        $this->attendanceService = $attendanceService;
        $this->workplanService = $workplanService;
        parent::__construct($config);
    }

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        !empty($this->date) ? \Yii::$app->formatter->asDate($this->date, CommonHelper::FORMAT_DATE_DB) : $this->date = (new \DateTime())->format('Y-m-d');
        $this->options['class'] = 'medworker-schedule';
        parent::init();
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        echo '<h4>Выберите время приема</h4>';
        $this->renderWorktime();
    }

    private function renderWorktime()
    {
        $scheduleTimes = $this->workplanService->getScheduleMedworkerTimes($this->employeeId, $this->date);
        if (!is_array($scheduleTimes)) {
            throw new InvalidValueException('var is not array.');
        }
        if (empty($scheduleTimes)) {
            echo '<h5>Нет доступных записей.</h5>';
        }
        foreach ($scheduleTimes as $cabinetId => $workTimes) {
            foreach ($workTimes as $workTime) {
                $sinceTime = $workTime;
                $expireTime = (int)\Yii::$app->formatter->asTimestamp($workTime . date_default_timezone_get()) + Attendance::ATTENDANCE_DURATION;
                $expireTime = \Yii::$app->formatter->asTime($expireTime, CommonHelper::FORMAT_TIME_UI);
                $datetime = $this->date . ' ' . $workTime;
                /** @var Attendance $attendance */
                $attendance = $this->attendanceService->getAttendanceByEhrIdAndEmployeeIdAndDatetime($this->ehrId, $this->employeeId, $datetime);
                $opt = ['class' => 'employee-schedule-time'];
                empty($attendance) ?: $opt['data-attendance_id'] = $attendance->id;
                empty($attendance) ?: Html::addCssClass($opt, 'record');
                $opt['data-cabinet_id'] = $cabinetId;
                $opt['data-datetime'] = $datetime;
                echo Html::beginTag('div', $opt);
                echo $sinceTime . ' - ' . $expireTime;
                echo Html::endTag('div');
            }
        }
        echo '<br>';
        echo Html::beginDiv(['class' => 'attendance-record', 'style' => 'display: none;']);
        echo Html::endDiv();
    }
}
