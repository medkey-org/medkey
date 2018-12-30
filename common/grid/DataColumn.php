<?php
namespace app\common\grid;

use app\common\base\Model;
use app\common\helpers\Html;

/**
 * Class DataColumn
 * @package Common\Grid
 * @copyright 2012-2019 Medkey
 */
class DataColumn extends \yii\grid\DataColumn
{
    /**
     * @inheritdoc
     */
    protected function renderFilterCellContent()
    {
        $model = $this->grid->filterModel;
        if (is_string($this->filter)) {
            return $this->filter;
        }
        if ($this->filter instanceof \Closure) {
            return call_user_func($this->filter);
        }
        if ($this->filter !== false && $model instanceof Model && $this->attribute !== null && $model->isAttributeActive($this->attribute)) {
            if ($model->hasErrors($this->attribute)) {
                Html::addCssClass($this->filterOptions, 'has-error');
                $error = ' ' . Html::error($model, $this->attribute, $this->grid->filterErrorOptions);
            } else {
                $error = '';
            }
            if (is_array($this->filter)) {
                $options = array_merge(['prompt' => ''], $this->filterInputOptions);
                return Html::activeDropDownList($model, $this->attribute, $this->filter, $options) . $error;
            } else {
                return Html::activeTextInput($model, $this->attribute, $this->filterInputOptions) . $error;
            }
        } else {
            return parent::renderFilterCellContent();
        }
    }
}
