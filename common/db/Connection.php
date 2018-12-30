<?php
namespace app\common\db;

use app\common\db\mysql\Schema;

/**
 * Class Connection
 * @package Common\DB
 * @copyright 2012-2019 Medkey
 */
class Connection extends \yii\db\Connection
{
    /**
     * @inheritdoc
     */
    public $commandClass = Command::class;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->schemaMap['mysql'] = Schema::class;
        $this->schemaMap['mysqli'] = Schema::class;
        parent::init();
    }
}
