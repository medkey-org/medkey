<?php
namespace app\modules\medical\models\form;

use app\common\base\Model;
use app\common\helpers\CommonHelper;
use app\common\validators\ForeignKeyValidator;

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
            [ ['ehr_id', 'employee_id'], 'required',],
            [ ['ehr_id', 'employee_id'], ForeignKeyValidator::class ],
            [ ['template', 'conclusion', 'name', 'complaints', 'diagnosis', 'recommendations', 'preliminary'], 'string' ],
            [ ['type'], 'integer' ],
            [ ['revist', 'datetime'],
                'datetime',
                'format' => CommonHelper::FORMAT_DATETIME_UI,
            ],
        ];
    }
}
