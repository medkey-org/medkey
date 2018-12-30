<?php
namespace app\modules\location\widgets\search;

use app\common\widgets\SearchWidget;
use app\modules\location\models\finders\LocationFinder;
use app\modules\location\widgets\grid\LocationGrid;

/**
 * Class LocationSearch
 * @package Module\Location
 * @copyright 2012-2019 Medkey
 */
class LocationSearch extends SearchWidget
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
        $this->model = LocationFinder::ensure($this->model, 'search');
        $this->list = [
            'class' => LocationGrid::className()
        ];
        parent::init();
    }
}
