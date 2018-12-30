<?php
namespace app\modules\dashboard\dashlets\EhrListDashlet;

use app\modules\medical\widgets\grid\EhrGrid;
use app\modules\dashboard\widgets\Dashlet;

/**
 * Class EhrListDashlet
 * @package Module\Dashboard
 * @copyright 2012-2019 Medkey
 */
class EhrListDashlet extends Dashlet
{
    /**
     * @inheritdoc
     */
    public function run()
    {
        return EhrGrid::widget();
    }
}
