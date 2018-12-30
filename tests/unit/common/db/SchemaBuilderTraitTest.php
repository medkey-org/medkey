<?php
namespace tests\codeception\unit\common\db;

use Codeception\Test\Unit;
use yii\db\Connection;
use yii\db\SchemaBuilderTrait as YiiSchemaBuilderTrait;
use app\common\db\SchemaBuilderTrait as AppSchemaBuilderTrait;

/**
 * Class SchemaBuilderTraitTest
 * @package Test/Unit/Common
 * @copyright 2012-2019 Medkey
 */
class SchemaBuilderTraitTest extends Unit
{
    use AppSchemaBuilderTrait;
    use YiiSchemaBuilderTrait;

    public function getDb()
    {
        return new Connection([
            'dsn' => 'pgsql:host=test;dbname=test',
        ]);
    }

    public function testForeignKeyId()
    {
        $this->assertEquals($this->string(36), $this->foreignKeyId());
        $this->assertEquals($this->string(), $this->foreignKeyId('string'));
        $this->assertEquals($this->integer(), $this->foreignKeyId('integer'));
        $this->assertEquals($this->bigInteger(), $this->foreignKeyId('bigInteger'));
    }
}
