<?php
namespace app\common\rest;

use app\common\db\ActiveRecord;

/**
 * Class ActiveController
 * @package Common\REST
 * @copyright 2012-2019 Medkey
 */
class ActiveController extends \yii\rest\ActiveController
{
    /**
     * @var string the scenario used for creating a model.
     * @see \yii\base\Model::scenarios()
     */
    public $createScenario = ActiveRecord::SCENARIO_CREATE;
    /**
     * @var string the scenario used for updating a model.
     * @see \yii\base\Model::scenarios()
     */
    public $updateScenario = ActiveRecord::SCENARIO_UPDATE;
}
