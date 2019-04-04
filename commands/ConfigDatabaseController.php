<?php
namespace app\commands;

use app\common\console\Controller;
use app\common\db\Connection;
use yii\di\Instance;

/**
 * Class ConfigDatabaseController
 * @package Common\CLI
 * @copyright 2012-2019 Medkey
 */
class ConfigDatabaseController extends Controller
{
    /**
     * @var Connection
     */
    public $db;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->db = Instance::ensure('db');
    }

    public function actionPgUuidActivate()
    {
        $this->line('begin execute command.');
        if ($this->db->driverName === 'pgsql') {
            $sql = <<<SQL
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";
SQL;
            $this->db->createCommand($sql)->execute();
            $this->line('done.');
        }
    }

    public function actionCHARACTERMysql()
    {
        if ($this->db->driverName === 'mysql') {
        $this->db
            ->createCommand('ALTER DATABASE CHARACTER SET utf8 COLLATE utf8_general_ci;')
            ->execute();
        }
    }
}
