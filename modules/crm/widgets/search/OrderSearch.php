<?php
namespace app\modules\crm\widgets\search;

use app\common\widgets\SearchWidget;
use app\modules\crm\models\finders\OrderFinder;
use app\modules\crm\widgets\grid\OrderGrid;

/**
 * Class OrderSearch
 * @package Module\CRM
 * @copyright 2012-2019 Medkey
 * @deprecated
 */
class OrderSearch extends SearchWidget
{
    /**
     * @var bool
     */
    public $renderResetAfterForm = false;


    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->model = OrderFinder::ensure($this->model, 'search');
        $this->list = [
            'class' => OrderGrid::class
        ];
        parent::init();
    }
}
