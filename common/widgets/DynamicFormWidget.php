<?php
namespace app\common\widgets;

use yii\base\Exception;
use yii\base\Model;
use app\common\helpers\Html;

/**
 * Class DynamicFormWidget
 * @package Common\Widgets
 * @copyright 2012-2019 Medkey
 *
 */
class DynamicFormWidget extends Widget
{
    /**
     * @var bool
     */
    public $clientView = false;
    /**
     * @var bool
     */
    public $clientWrapperContainer = false;
    /**
     * @var array
     */
    public $attributes = [];
    /**
     * @var string
     */
    public $content;
    /**
     * @var ActiveForm
     */
    public $form;
    /**
     * @var Model
     */
    public $model;


    /**
     * @param Model $model
     * @param ActiveForm $form
     * @throws Exception
     */
    protected function renderElements($model, $form)
    {
        if (isset($this->content)) {
            echo $this->content;
        } elseif (is_array($this->attributes)) {
            foreach ($this->attributes as $attribute) {
	            /**
	             * @var ActiveField $item
	             */
                $item = $form->field($model, is_array($attribute) ? $attribute['attribute'] : $attribute);
                if (is_array($attribute)) {
                    if (!isset($attribute['attribute'])) {
                        throw new Exception('Неправильно передана конфигурация атрибута формы');
                    }
                    if (!isset($attribute['type'])) {
                        throw new Exception('Неправильно передана конфигурация атрибута формы');
                    }

                    switch ($attribute['type']) {
                        case 'email':
                            $item->input('email');
                            break;
                        case 'text':
                            $item->textarea();
                            break;
                        case 'dropdown': // @todo Переработать нормально

                            if ($attribute['value'] instanceof \Closure) {
                                $attribute['value'] = call_user_func($attribute['value'], $model, $form);
                            }

                            $item->dropDownList($attribute['value']);

                            break;
	                    case 'hidden':
		                    $item->hiddenInput($attribute);
		                    break;
                    }
                }
                echo $item;
            }
        }

        echo Html::submitButton(\Yii::t('app', 'Save'), [
            'class' => 'btn btn-primary',
            'icon' => 'glyphicon glyphicon-saved',
        ]);

        echo ' ' . Html::button(\Yii::t('app', 'Cancel'), [
                'class' => 'btn btn-default',
                'data' => ['dismiss' => 'modal'],
            ]);
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $this->renderElements($this->model, $this->form);
    }
}
