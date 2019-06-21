<?php
namespace app\modules\medical\widgets\misc;

use app\common\helpers\Html;
use app\common\web\View;
use app\common\widgets\Widget;
use app\common\wrappers\DynamicModal;
use app\modules\medical\MedicalModule;

class AttendanceSchedule extends Widget
{
    public $ehrId;

    /**
     * {@inheritDoc}
     */
    public function run()
    {
//        $specialities = Speciality::find()->all();
//        foreach ($specialities as $speciality) {
//            echo Html::div($speciality->title, [
//                'class' => 'speciality-title',
//                'data-id' => $speciality->id,
//            ]);
//        }
        echo Html::beginDiv(['id' => 'schedule']);
        echo Html::endDiv();
        echo \Yii::$app->view->registerJs(<<<JS
        registerAttendanceSchedule($this->ehrId);
JS
, View::POS_END);
    }

    /**
     * {@inheritDoc}
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
