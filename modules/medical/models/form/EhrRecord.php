<?php
namespace app\modules\medical\models\form;

use app\common\base\Model;

class EhrRecord extends Model
{
    public $id;
    public $ehr_id;
    public $employee_id;
    public $template;
    public $conclusion;
    public $type;
    public $datetime;
    public $name;
    public $complaints;
    public $diagnosis;
    public $recommendations;
    public $preliminary;
    public $revist;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [

        ];
    }
}
