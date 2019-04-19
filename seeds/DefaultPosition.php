<?php
namespace app\seeds;

use app\common\helpers\ArrayHelper;
use app\common\seeds\Seed;
use app\modules\organization\models\orm\Position;

class DefaultPosition extends Seed
{
    public function run()
    {
        $departments = $this->call('default_department')->models;
        $this->model = Position::class;
        $this->data = [
            [
                'department_id' => ArrayHelper::findBy($departments, ['title' => 'Default Department'])->id,
                'title' => 'Default Title Position',
                'short_title' => 'Default Short Title',
                'description' => 'Desc',
            ]
        ];
    }
}
