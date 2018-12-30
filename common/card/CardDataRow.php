<?php
namespace app\common\card;

use app\common\db\ActiveRecord;
use yii\base\BaseObject;
use app\common\helpers\Html;
use yii\base\InvalidConfigException;
use yii\base\Model;

/**
 * Card Data Row
 * @package Common\Card
 * @copyright 2012-2019 Medkey
 */
class CardDataRow extends BaseObject
{
    const DEFAULT_COLS = 12;
    /**
     * @var ActiveRecord
     */
    public $model;
    /**
     * @var string
     */
    public $class = 'app\common\card\CardDataAttribute';
    /**
     * @var array
     */
    public $items;
	/**
	 * @var \app\common\widgets\ActiveForm
	 */
	public $form;
    /**
     * @var int
     */
    protected $cols = self::DEFAULT_COLS;
    /**
     * @var int
     */
    protected $customSizeCount = 0;


    /**
     * @return false|string
     */
    public function render()
    {
        ob_start();
        ob_implicit_flush(false);
        echo Html::beginTag('div', ['class' => 'row']);
        echo $this->renderItems();
        echo Html::endTag('div');
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }

    /**
     * @return string
     */
    public function renderItems()
    {
        return implode('', array_map(function ($value) {

            $content = '';
            echo Html::beginTag('div', [
                'class' => 'col-md-' . $this->getColSize($value),
            ]);
            $content .= $this->renderItem($value);
            echo Html::endTag('div');
            return $content;
        }, $this->items));
    }

    /**
     * @param $value
     * @return int
     */
    protected function getColSize($value)
    {
        $this->cols = self::DEFAULT_COLS;;
        $i = 0;
        foreach ($this->items as $item) {
            if(isset($item['colSize'])) {
                $i++;
                $this->customSizeCount += (int)$item['colSize'];
                $this->cols -= (int)$item['colSize'];
            }
        }

        if ($this->customSizeCount > 0 && $this->customSizeCount % 2 == 0 && $this->cols >= 0 && $i == count($this->items)) {
            $size = $value['colSize'];
        } else {
            $size = (int)($this->cols / count($this->items));
        }
        return $size;
    }

    /**
     * @param $config
     * @return mixed
     * @throws InvalidConfigException
     */
    protected function renderItem($config)
    {
        if (!is_array($config)) {
            $config = [
                'attribute' => $config,
            ];
        }

        $config['model'] = $this->model;

        if (!isset($config['class'])) {
            if ($this->model->scenario === Model::SCENARIO_DEFAULT) {
                $config['class'] = CardDataViewAttribute::class;
            } else {
                $config['class'] = CardDataEditAttribute::class;
            }
        }

        if (isset($config['scenarios']) && isset($config['scenarios'][$this->model->scenario]) && is_array($config['scenarios'])) {
            $config = array_merge($config, $config['scenarios'][$this->model->scenario]);
        }

        if (isset($config['scenarios'])) {
            unset($config['scenarios']);
        }

        $config['count'] = count($this->items);
        $config['form'] = $this->form;

        return \Yii::createObject($config)->render();
    }
}
