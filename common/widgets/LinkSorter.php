<?php
namespace app\common\widgets;

use app\common\helpers\Html;

/**
 * Class LinkSorter
 * @package Common\Widgets
 * @copyright 2012-2019 Medkey
 */
class LinkSorter extends \yii\widgets\LinkSorter
{
    /**
     * @inheritdoc
     */
    protected function renderSortLinks()
    {
        $attributes = empty($this->attributes) ? array_keys($this->sort->attributes) : $this->attributes;
        $links = [];
        foreach ($attributes as $name) {
            $links[] = $this->sort->link($name, $this->linkOptions);
        }

        return Html::ul($links, array_merge($this->options, ['encode' => false]));
    }
}
