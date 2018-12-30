<?php
namespace app\modules\medical\models\finders;

use app\common\db\OrmFinder;

/**
 * Class EhrRecordFinder
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
class EhrRecordFinder extends OrmFinder
{
    /**
     * @var string
     */
    public $ehrId;

    /**
     * @inheritdoc
     */
    public function initCondition()
    {
        $this->query
            ->andWhere([
                'ehr_id' => $this->ehrId
            ]);
    }
}
