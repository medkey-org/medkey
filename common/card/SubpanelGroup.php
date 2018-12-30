<?php
namespace app\common\card;

use app\common\base\Model;
use app\common\widgets\Tabs;

/**
 * Class Subpanel group
 * @package Common\Card
 */
class SubpanelGroup
{
    /**
     * @var array
     */
    public $subpanels;
    /**
     * @var string
     */
    public $subpanelTemplate;
    /**
     * @var Model
     */
    public $model;


    /**
     * @return string|null
     */
    public function render()
    {
        if (empty($this->subpanels) || !is_array($this->subpanels)) {
            return null;
        }
        $items = [];
        foreach ($this->subpanels as $template => $subpanel) {
            if (!empty($subpanel['value']) && ($subpanel['value'] instanceof \Closure)) {
                $items[] = [
                    'label' => !empty($subpanel['header']) ? $subpanel['header'] : $template,
                    'content' => call_user_func($subpanel['value'], $this->model)
                ];
            } elseif (!empty($subpanel['value']) && is_scalar($subpanel['value'])) {
                $items[] = [
                    'label' => !empty($subpanel['header']) ? $subpanel['header'] : $template,
                    'content' => $subpanel['value']
                ];
            }
        }
        return Tabs::widget([
            'items' => $items
        ]);
    }
}
