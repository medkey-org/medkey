<?php
namespace app\modules\medical\widgets\grid;

use app\common\button\LinkActionButton;
use app\common\button\WidgetLoaderButton;
use app\common\grid\GridView;
use app\common\helpers\CommonHelper;
use app\common\helpers\Html;
use app\common\helpers\Url;
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
     * {@inheritdoc}
     */
    public function init()
    {
        $this->filterModel = EhrRecordFinder::ensure($this->filterModel);
        $this->filterModel->ehrId = $this->ehrId;
        $this->actionButtons['create'] = [
            'class' => LinkActionButton::class,
            'isAjax' => false,
            'url' => Url::to(['/medical/ui/ehr-record/view', 'ehrId' => $this->ehrId]),
            'disabled' => false,
            'isDynamicModel' => false,
            'value' => '',
            'options' => [
                'class' => 'btn btn-primary btn-xs',
                'icon' => 'plus'
            ]
        ];
        $this->actionButtons['update'] = [
            'class' => LinkActionButton::class,
            'isAjax' => false,
            'url' => Url::to(['/medical/ui/ehr-record/view', 'ehrId' => $this->ehrId]),
            'disabled' => true,
            'isDynamicModel' => true,
            'value' => '',
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
                'attribute' => 'name',
                'value' => function (EhrRecord $model) {
                    return Html::a($model->name, Url::to(['/medical/ui/ehr-record/view', 'scenario'=> 'default', 'id' => $model->id, 'ehrId' => $model->ehr_id]));
                },
                'options' => [
                    'class' => 'col-xs-2',
                ],
                'format' => 'raw',
            ],
            [
                'attribute' => 'employee_id',
                'value' => function (EhrRecord $model) {
                    if ($model->employee instanceof Employee) {
                        return $model->employee->fullName;
                    }
                    return '';
                },
                'options' => [
                    'class' => 'col-xs-3',
                ],
            ],
            [
                'attribute' => 'datetime',
                'value' => function (EhrRecord $model) {
                    return \Yii::$app->formatter->asDatetime($model->datetime, CommonHelper::FORMAT_DATETIME_UI);
                },
                'options' => [
                    'class' => 'col-xs-1',
                ],
            ],
            [
                'attribute' => 'revisit',
                'value' => function (EhrRecord $model) {
                    return \Yii::$app->formatter->asDatetime($model->revisit, CommonHelper::FORMAT_DATETIME_UI);
                },
                'options' => [
                    'class' => 'col-xs-1',
                ],
            ],
        ];
        parent::init();
    }
}
