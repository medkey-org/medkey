<?php

namespace app\modules\dashboard\widgets\form;

use app\common\helpers\Html;
use app\modules\dashboard\models\orm\DashboardItem;
use app\common\widgets\FormWidget;
use app\common\wrappers\DynamicModal;

/**
 * Class DashboardItemCreateForm
 * @package Module\Dashboard
 * @copyright 2012-2019 Medkey
 */
class DashboardItemCreateForm extends FormWidget
{
    /**
     * @var int
     */
    public $dashboard_id;


    /**
     * @inheritdoc
     */
    public function init()
    {

        $this->model = DashboardItem::ensureWeak($this->model, 'create');
        $this->model->dashboard_id = $this->dashboard_id;
        $this->action = ['/dashboard/ui/item/create'];
        $this->validationUrl = ['/dashboard/ui/item/validate-create'];

        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function renderForm($model, $form)
    {
        echo $form->field($model, 'dashboard_id')->hiddenInput();

        echo $form->field($model, 'title')->textInput();

        echo $form->field($model, 'widget')->dropDownList(DashboardItem::getDashletsTitles());

        echo $form->field($model, 'position')->textInput();

        echo $form->field($model, 'order')->textInput();

        echo Html::submitButton('Сохранить', ['class' => 'btn btn-primary']);
        echo Html::button('Отмена', ['class' => 'btn btn-default', 'data' => ['dismiss' => 'modal']]);
    }

    /**
     * @inheritdoc
     */
    public function wrapperOptions()
    {
        return [
            'header' => 'Добавить виджет',
            'wrapperClass' => DynamicModal::className(),
        ];
    }
}
