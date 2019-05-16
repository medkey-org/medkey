<?php
namespace app\modules\security\widgets\search;

use app\modules\security\models\finders\UserFinder;
use app\common\widgets\SearchWidget;
use app\modules\security\widgets\grid\UserGrid;

/**
 * Class UserSearch
 * @package Module\Security
 * @copyright 2012-2019 Medkey
 */
class UserSearch extends SearchWidget
{
    /**
     * @var bool
     */
    public $renderResetAfterForm = false;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->model = UserFinder::ensure($this->model, 'search');
        $this->list = [
            'class' => UserGrid::className()
        ];
        parent::init();
    }
}
