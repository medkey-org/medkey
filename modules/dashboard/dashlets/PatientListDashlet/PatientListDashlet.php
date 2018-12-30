<?php
namespace app\modules\dashboard\dashlets\PatientListDashlet;

use app\modules\medical\widgets\grid\PatientGrid;
use app\modules\dashboard\widgets\Dashlet;

/**
 * Class PatientListDashlet
 * @package Module\Dashboard
 * @copyright 2012-2019 Medkey
 */
class PatientListDashlet extends Dashlet
{
    /**
     * @inheritdoc
     */
    public function run()
    {
        return PatientGrid::widget();
    }
}
