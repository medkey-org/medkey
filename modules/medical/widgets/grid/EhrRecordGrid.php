<?php
namespace app\modules\medical\widgets\grid;

use app\common\button\LinkActionButton;
use app\common\button\WidgetLoaderButton;
use app\common\grid\GridView;
use app\modules\medical\models\finders\EhrRecordFinder;
use app\modules\medical\models\orm\Ehr;
use app\modules\medical\models\orm\EhrRecord;
use app\modules\medical\widgets\card\EhrRecordCard;
use app\modules\medical\widgets\form\EhrRecordCreateForm;
use app\modules\medical\widgets\form\EhrRecordUpdateForm;
use app\modules\organization\models\orm\Employee;

/**
 * Class EhrRecord
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
class EhrRecordGrid extends GridView
{
    /**
     * @var EhrRecordFinder
     */
    public $filterModel;
    /**
     * @var Ehr
     */
    public $ehrId;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->filterModel = EhrRecordFinder::ensure($this->filterModel);
        $this->filterModel->ehrId = $this->ehrId;
        $this->actionButtons['create'] = [
            'class' => WidgetLoaderButton::class,
            'widgetClass' => EhrRecordCard::class,
            'disabled' => false,
            'isDynamicModel' => false,
            'value' => '',
            'widgetConfig' => [
                'ehrId' => $this->ehrId,
                'afterUpdateBlockId' => $this->getId(),
            ],
            'options' => [
                'class' => 'btn btn-primary btn-xs',
                'icon' => 'plus'
            ]
        ];
        $this->actionButtons['update'] = [
            'class' => WidgetLoaderButton::class,
            'widgetClass' => EhrRecordUpdateForm::class,
            'disabled' => true,
            'isDynamicModel' => true,
            'value' => '',
            'widgetConfig' => [
                'ehrId' => $this->ehrId,
                'afterUpdateBlockId' => $this->getId()
            ],
            'options' => [
                'class' => 'btn btn-primary btn-xs',
                'icon' => 'edit'
            ]
        ];
//        $this->actionButtons['delete'] = [
//            'class' => LinkActionButton::class,
//            'url' => ['/medical/rest/ehr-record/delete'],
//            'isDynamicModel' => true,
//            'isAjax' => true,
//            'disabled' => true,
//            'isConfirm' => true,
//            'value' => '',
//            'options' => [
//                'class' => 'btn btn-danger btn-xs',
//                'icon' => 'remove',
//            ],
//        ];
        $this->columns = [
            [
                'attribute' => 'employee_id',
                'value' => function (EhrRecord $model) {
                    if ($model->employee instanceof Employee) {
                        return $model->employee->fullName;
                    }
                    return '';
                }
            ],
            [
                'attribute' => 'conclusion',
                'value' => function (EhrRecord $model) {
                    return $model->conclusion;
                }
            ],
        ];
        parent::init();
    }
}
