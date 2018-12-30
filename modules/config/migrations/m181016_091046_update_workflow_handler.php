<?php

use app\common\db\Migration;

/**
 * Class m181016_091046_update_workflow_handler
 * @package Module\Config
 * @copyright 2012-2019 Medkey
 */
class m181016_091046_update_workflow_handler extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addBothColumns('{{%workflow_transition}}', 'handler_type', $this->string());
        $this->addBothColumns('{{%workflow_transition}}', 'handler_method', $this->string());
        $this->addBothColumns('{{%workflow_transition}}', 'async', $this->smallInteger());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropBothColumns('{{%workflow_transition}}', 'handler_type');
        $this->dropBothColumns('{{%workflow_transition}}', 'handler_method');
        $this->dropBothColumns('{{%workflow_transition}}', 'async');
    }
}
