<?php
namespace app\modules\medical\application;

use app\common\data\ActiveDataProvider;
use app\modules\medical\models\orm\Ehr;
use yii\base\Model;

/**
 * Class EhrApplicationServiceInterface
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
interface EhrServiceInterface
{
    public function getEhrById($id): Ehr;
    public function getEhrList(Model $form): ActiveDataProvider;
    public function getEhrRecordFormByRaw($raw, $ehrId);
    public function createEhrRecord($form);
    public function updateEhrRecord($id, $form);
}
