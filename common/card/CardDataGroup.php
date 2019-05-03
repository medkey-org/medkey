<?php
namespace app\common\card;

use app\common\db\ActiveRecord;
use app\common\widgets\ActiveForm;
use yii\base\BaseObject;
use app\common\helpers\Html;

/**
 * Class CardDataGroup
 * @package Common\Card
 * @copyright 2012-2019 Medkey
 */
class CardDataGroup extends BaseObject
{
    /**
     * @var ActiveRecord
     */
    public $model;
    /**
     * @var string Класс для отрисовки строки
     */
    public $class = 'app\common\card\CardDataRow';
    /**
     * @var array Коллекция групп
     */
    public $items;
    /**
     * @var string Заголовок группы
     */
    public $title;
	/**
	 * @var ActiveForm|null
	 */
	public $form;
    /**
     * @var bool
     */
	public $showFrame = true;

    /**
     * Отрисовать группу
     */
    public function render()
    {
        ob_start();
        ob_implicit_flush(false);
        if ($this->showFrame) {
            echo Html::beginTag('div', ['class' => 'panel panel-frame']);
            echo Html::beginTag('div', ['class' => 'panel-heading']);
            echo $this->renderTitle();
            echo Html::endTag('div');
            echo Html::beginTag('div', ['class' => 'panel-body']);
        }
        echo $this->renderItems($this->form);
        if ($this->showFrame) {
            echo Html::endTag('div');
            echo Html::endTag('div');
        }
        $content = ob_get_contents();
        ob_end_clean();

        return $content;
    }

    /**
     * Отрисовать заголовок
     */
    public function renderTitle()
    {
        return Html::encode($this->title);
    }

	/**
	 * Отрисовать строки
	 *
	 * @param ActiveForm|null $form
	 * @return string
	 */
    public function renderItems($form = null)
    {
        return implode('', array_map(function ($value) use ($form) {
            if (!isset($value['items'])) {
                return '';
            }
            $config = [
                'class' => !isset($value['class']) ? $this->class : $value['class'],
                'items' => !isset($value['items']) ? [] : $value['items'],
                'model' => $this->model,
	            'form' => $form,
            ];
            /** @var CardDataRow $item */
            $item = \Yii::createObject($config);
            return $item->render();
        }, $this->items));
    }
}
