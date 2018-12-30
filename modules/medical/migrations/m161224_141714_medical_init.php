<?php

/**
 * Class m161224_141714_medical_init
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
class m161224_141714_medical_init extends \app\common\db\Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createBothTables('{{%patient}}', [
            'first_name' => $this->string()->notNull(),
            'middle_name' => $this->string(),
            'last_name' => $this->string()->notNull(),
            'birthday' => $this->date()->notNull(),
            'snils' => $this->string(),
            'inn' => $this->string(),
            'birthplace' => $this->text(),
            'race_type' => $this->smallInteger(), // TODO
            'children_count' => $this->smallInteger(), // TODO
            'education_type' => $this->smallInteger(), // TODO
            'citizenship' => $this->integer(), // TODO
            'sex' => $this->smallInteger(),
            'status' => $this->smallInteger(),
        ]);
        $this->createBothTables('{{%ehr}}', [
            'number' => $this->string()->notNull(),
            'type' => $this->smallInteger()->notNull(),
            'status' => $this->smallInteger()->notNull(),
            'patient_id' => $this->foreignKeyId(),
        ]);
        $this->createBothTables('{{%ehr_record}}', [
            'ehr_id' => $this->foreignKeyId(),
            'employee_id' => $this->foreignKeyId(), // @todo добавить ehr_record_to_employee
            'template' => $this->text(), // @todo заменить на настоящие шаблоны
            'conclusion' => $this->text(), // @todo продумать заключение
            'datetime' => $this->timestamp(),
            'type' => $this->integer()
        ]);
//        $this->createResponsibilityTable('{{%ehr_record}}');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropBothTables('{{%patient}}');
        $this->dropBothTables('{{%ehr}}');
        $this->dropBothTables('{{%ehr_record}}');
    }
}
