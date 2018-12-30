<?php
namespace app\common\widgets;

use app\common\base\Module;
use app\common\db\ActiveRecord;
use app\common\helpers\ArrayHelper;
use app\common\helpers\ClassHelper;
use app\common\helpers\Html;
use app\common\db\ResponsibilityEntityInterface;
use app\common\wrappers\DynamicModal;
use app\modules\organization\models\orm\Employee;
use yii\base\InvalidValueException;

/**
 * Class ResponsibilityWidget
 * @package Common\Widgets
 * @copyright 2012-2019 Medkey
 *
 */
class ResponsibilityWidget extends FormWidget
{
    /**
     * @var bool
     */
    public $enableAjaxValidation = false;
    /**
     * @var bool
     */
    public $enableClientValidation = false;
    /**
     * @var array
     */
    public $action = ['/rest/responsibility/relation'];
    /**
     * @var string
     */
    public $module;
    /**
     * @var string
     */
    public $entity;
    /**
     * @var ActiveRecord|string
     */
    public $model;


    /**
     * @inheritdoc
     */
    public function init()
    {
        /** @var Module $module */
        $module = \Yii::$app->getModule($this->module);
        $ns = $module->getOrmNamespace();
        /** @var ActiveRecord $modelClass */
        $modelClass = $ns . '\\' . $this->entity;
        if (!class_exists($modelClass) || !ClassHelper::implementsInterface($modelClass, ResponsibilityEntityInterface::class)) {
            throw new InvalidValueException('Class not found.');
        }
        $this->model = $modelClass::ensure($this->model);
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function renderForm($model, $form)
    {
        $employees = ArrayHelper::map(Employee::find()->notDeleted()->all(), 'id', function ($row) {
            return $row->last_name
                . ' '
                . $row->first_name
                . ' '
                . $row->middle_name;
        });
        echo Html::hiddenInput('module', $this->module, ['id' => 'module']);
        echo Html::hiddenInput('entity', $this->entity, ['id' => 'entity']);
        echo Html::hiddenInput('entity_id', $this->model->id, ['id' => 'entity_id']);
        echo Html::select2Input(
            'responsibilities',
            ArrayHelper::getColumn($model->responsibilities, 'employee_id'),
            $employees,
            [
                'multiple' => true,
                'placeholder' => 'Select value',
                'empty' => false,
                'id' => 'responsibilities'
            ]
        );
        echo '<br>';
        echo Html::submitButton('Save', [
            'class' => 'btn btn-primary',
            'icon' => 'save'
        ]);
        echo '&nbsp';
        echo Html::button('Cancel', [
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
            'wrapperClass' => DynamicModal::className(),
            'header' => 'Set responsible',
        ];
    }
}
