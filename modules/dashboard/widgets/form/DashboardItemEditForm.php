<?php

namespace app\modules\dashboard\widgets\form;

use app\common\helpers\Html;
use app\modules\dashboard\models\orm\DashboardItem;
use app\common\widgets\FormWidget;
use app\common\wrappers\DynamicModal;

/**
 * Class DashboardItemEditForm
 * @package Module\Dashboard
 * @copyright 2012-2019 Medkey
 */
class DashboardItemEditForm extends FormWidget
{
    /**
     * @inheritdoc
     */
    public function init()
    {

        $this->model = DashboardItem::ensureWeak($this->model, 'update');
        $this->action = ['/dashboard/ui/item/update', 'id' => $this->model->id];
        $this->validationUrl = ['/dashboard/ui/item/validate-update', 'id' => $this->model->id];

        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function renderForm($model,$form)
    {
        echo $form->field($model, 'title')->textInput();

        echo $form->field($model, 'widget')->dropDownList(DashboardItem::getDashletsTitles())->attributeInput(['disabled' => 'disabled']);

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
            'header' => 'Редактировать виджет',
            'wrapperClass' => DynamicModal::className(),
        ];
    }
}
