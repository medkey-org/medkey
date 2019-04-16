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
    /**
     * @param string|integer $id
     * @return Ehr
     */
    public function getEhrById($id): Ehr;

    /**
     * @param Model $form
     * @return ActiveDataProvider
     */
    public function getEhrList(Model $form): ActiveDataProvider;
}
