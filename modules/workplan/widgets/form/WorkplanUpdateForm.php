<?php
namespace app\modules\workplan\widgets\form;

use app\common\helpers\ArrayHelper;
use app\common\helpers\Html;
use app\common\helpers\Url;
use app\common\widgets\DatePicker;
use app\common\widgets\FormWidget;
use app\common\wrappers\DynamicModal;
use app\modules\organization\models\orm\Cabinet;
use app\modules\organization\models\orm\Department;
use app\modules\workplan\application\WorkplanServiceInterface;
use app\modules\workplan\models\form\Workplan as WorkplanForm;
use app\modules\workplan\WorkplanModule;
use yii\web\JsExpression;

/**
 * Class WorkplanUpdateForm
 * @package Module\Workplan
 * @copyright 2012-2019 Medkey
 */
class WorkplanUpdateForm extends FormWidget
{
    /**
     * @var WorkplanForm
     */
    public $model;
    /**
     * @var WorkplanServiceInterface
     */
    public $workplanService;


    /**
     * WorkplanUpdateForm constructor.
     * @param WorkplanServiceInterface $workplanService
     * @param array $config
     */
    public function __construct(WorkplanServiceInterface $workplanService, array $config = [])
    {
        $this->workplanService = $workplanService;
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->model = $this->workplanService->getWorkplanForm($this->model);
        $this->action = ['/workplan/rest/workplan/update', 'id' => $this->model->id];
        $this->validationUrl = ['/workplan/rest/workplan/validate-update'];
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function renderForm($model, $form)
    {
        echo Html::beginTag('div', [
            'class' => 'row',
        ]);
        echo Html::beginTag('div', [
            'class' => 'col-xs-12 col-sm-12 col-md-12 col-lg-12'
        ]);
        echo Html::beginTag('div', [
            'class' => 'row',
        ]);
        echo Html::beginTag('div', [
            'class' => 'col-xs-6 col-sm-6 col-md-6 col-lg-6'
        ]);
        echo $form->field($model, 'since_date')->dateInput([
            'type' => DatePicker::TYPE_INLINE,
        ]);
        echo Html::endTag('div'); // col-xs-6 ...
        echo Html::beginTag('div', [
            'class' => 'col-xs-6 col-sm-6 col-md-6 col-lg-6'
        ]);
        echo $form->field($model, 'expire_date')->dateInput([
            'type' => DatePicker::TYPE_INLINE,
        ]);
        echo Html::endTag('div'); // col-xs-6 ...
        echo Html::endTag('div'); // row
        echo Html::beginTag('div', [
            'class' => 'row',
        ]);
        echo Html::beginTag('div', [
            'class' => 'col-xs-6 col-sm-6 col-md-6 col-lg-6'
        ]);
        echo $form->field($model, 'since_time')->timeInput();
        echo Html::endTag('div');
        echo Html::beginTag('div', [
            'class' => 'col-xs-6 col-sm-6 col-md-6 col-lg-6'
        ]);
        echo $form->field($model, 'expire_time')->timeInput();
        echo Html::endTag('div'); // col-xs-6 col-sm-6 ...
        echo Html::endTag('div'); // row
        echo Html::beginTag('div', [
            'class' => 'row',
        ]);
        echo Html::beginTag('div', [
            'class' => 'col-xs-6 col-sm-6 col-md-6 col-lg-6',
        ]);
        echo $form->field($model, 'department_id')->dropDownList(Department::listAll(), [
            'empty' => 'Не выбрано',
        ]);
        echo Html::endTag('div');
        echo Html::beginTag('div', [
            'class' => 'col-xs-6 col-sm-6 col-md-6 col-lg-6'
        ]);
        echo $form->field($model, 'cabinet_id')->dropDownList(Cabinet::listAll(null, 'number', 'id'), [
            'empty' => false,
        ]);
        // @todo выбирать кабинеты ТОЛЬКО из выбранного подразделения
        echo Html::endTag('div'); // col-xs-6 col-sm-6 col-md-6 col-lg-6
        echo Html::endTag('div'); // row
        echo Html::beginTag('div', [
            'class' => 'row',
        ]);
        echo Html::beginTag('div', [
            'class' => 'col-xs-6 col-sm-6 col-md-6 col-lg-6'
        ]);
        echo $form->field($model, 'weekIds')->select2(WorkplanForm::listWeek(), [
           'multiple' => true
        ]);
        echo Html::endTag('div'); // col-xs-6 col-sm-6 col-md-6 col-lg-6
        echo Html::endTag('div'); // row
//        $model->employee; // lazy
        echo $form->field($model, 'employee_id')
            ->select2(ArrayHelper::map([$model->employee], 'id', function ($row) {
                return empty($row) ?: $row['last_name'] . ' ' . $row['first_name'] . ' ' . $row['middle_name'];
            }), [], [
                'allowClear' => true,
                'minimumInputLength' => 1,
                'language' => [
                    'errorLoading' => new JsExpression("function () { return 'Ничего не найдено.'; }"),
                ],
                'ajax' => [
                    'url' => Url::to(['/organization/rest/employee/index']),
                    'dataType' => 'json',
                    'delay' => 1000,
                    'data' => new JsExpression('function (params) { return {q:params.term, page: params.page || 1}; }'),
                    'processResults' => new JsExpression('function (data, params) { return { results: data, pagination: {more: (params.page * 10) < data.count_filtered}}; }')
                ],
                'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                'templateResult' => new JsExpression('function (employee) { if (employee.loading) { return employee.text; } else {return employee.last_name + " " + employee.first_name + " " + employee.middle_name;} }'),
                'templateSelection' => new JsExpression('function (employee) { if (employee.last_name) {return employee.last_name + " " + employee.first_name + " " + employee.middle_name;} else { return employee.text;} }'),
            ]);
        echo Html::endTag('div'); // col-xs-12 col-sm-12 col-md-12 col-lg-12
        echo Html::endTag('div'); // row
        echo Html::submitButton(\Yii::t('app', 'Save'), [
            'class' => 'btn btn-primary',
            'icon' => 'edit',
        ]);
        echo '&nbsp';
        echo Html::button(\Yii::t('app', 'Cancel'), [
            'class' => 'btn btn-default',
            'data-dismiss' => 'modal'
        ]);
    }

    /**
     * @inheritdoc
     */
    public function wrapperOptions()
    {
        return [
            'header' => WorkplanModule::t('workplan', 'Edit workplan'),
            'wrapperClass' => DynamicModal::class,
        ];
    }
}
