<?php
namespace app\modules\organization\widgets\search;

use app\common\widgets\SearchWidget;
use app\modules\organization\models\finders\EmployeeFinder;
use app\modules\organization\widgets\grid\EmployeeGrid;

/**
 * Class EmployeeSearch
 * @package Module\Organization
 * @copyright 2012-2019 Medkey
 */
class EmployeeSearch extends SearchWidget
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
        $this->model = EmployeeFinder::ensure($this->model, 'search');
        $this->list = [
            'class' => EmployeeGrid::className()
        ];
        parent::init();
    }
}
