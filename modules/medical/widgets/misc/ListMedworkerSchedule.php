<?php
namespace app\modules\medical\widgets\misc;

use app\common\base\UniqueKey;
use app\common\helpers\CommonHelper;
use app\common\helpers\Html;
use app\common\widgets\DatePicker;
use app\common\widgets\Widget;
use app\common\wrappers\DynamicModal;
use app\modules\medical\assets\ScheduleMedworkerAsset;
use app\modules\medical\MedicalModule;
use app\modules\medical\models\orm\Referral;
use app\modules\medical\application\ReferralServiceInterface;
use app\modules\organization\application\EmployeeServiceInterface;

/**
 * Class ScheduleMedworker
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
class ListMedworkerSchedule extends Widget
{
    /**
     * @var ReferralServiceInterface
     */
    public $referralService;
    /**
     * @var Referral
     */
    public $model;
    /**
     * @var string[]
     */
    private $employeeIds = [];
    /**
     * @var EmployeeServiceInterface
     */
    private $employeeService;

    /**
     * ListMedworkerSchedule constructor.
     * @param EmployeeServiceInterface $employeeService
     * @param ReferralServiceInterface $referralService
     * @param array $config
     */
    public function __construct(EmployeeServiceInterface $employeeService, ReferralServiceInterface $referralService, array $config = [])
    {
        $this->employeeService = $employeeService;
        $this->referralService = $referralService;
        parent::__construct($config);
    }

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        ScheduleMedworkerAsset::register($this->view);
        $this->model = Referral::ensure($this->model); // todo proxy ReferralService
        $this->employeeIds = $this->referralService->getEmployeesByReferral($this->model->id);
        parent::init();
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        if (empty($this->employeeIds)) {
            return MedicalModule::t('schedule', 'Specialists or schedule items not found');
        }
        foreach ($this->employeeIds as $employeeId) {
            $this->renderRow($employeeId);
        }
    }

    private function renderRow($employeeId)
    {
        $employee = $this->employeeService->getEmployeeById($employeeId);
        echo Html::beginTag('div', ['class' => 'row-employee', 'key' => $employee->id]);
        echo Html::tag('h5', Html::tag('span', $employee->fullName, ['class' => 'employee-name']));
        echo Html::beginDiv(['class' => 'datetime']);
        echo Html::beginRow();
        echo Html::beginCol(['size' => '3']);
        echo DatePicker::widget([
            'name' => 'employee-date',
            'layout' => '{picker}',
            'id' => UniqueKey::generate('employee-date'),
            'type' => DatePicker::TYPE_INLINE,
            'pluginOptions' => [
                'format' => CommonHelper::FORMAT_DATEPICKER_UI,
                'todayHighlight' => true
            ]
        ]);
        echo Html::endCol();
        echo Html::beginCol(['size' => '9', 'class' => 'employee-schedule']);
        echo MedworkerSchedule::widget([
            'employeeId' => $employeeId,
            'referralId' => $this->model->id,
            'ehrId' => $this->model->ehr_id
        ]);
        echo Html::endCol();
        echo Html::endRow();
        echo Html::endDiv();
        echo Html::endTag('div');
    }

    /**
     * {@inheritdoc}
     */
    public function wrapperOptions()
    {
        return [
            'wrapperClass' => DynamicModal::class,
            'header' => MedicalModule::t('schedule', 'Specialist\'s schedule'),
            'size' => 'modal-lg'
        ];
    }
}
