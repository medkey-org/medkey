<?php
namespace app\modules\medical\application;

use app\common\db\ActiveRecord;
use yii\base\Model;

/**
 * Class EhrApplicationServiceInterface
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
interface EhrServiceInterface
{
    /**
     * @param string|integer $id
     * @return ActiveRecord
     */
    public function getEhrById($id);

    /**
     * @param Model $form
     * @return \app\modules\medical\models\orm\Ehr[]
     */
    public function getEhrList(Model $form);
}
