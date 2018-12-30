<?php
namespace app\modules\medical\application;

use app\common\db\ActiveRecord;
use app\common\dto\Dto;
use yii\base\Model;

/**
 * Interface ReferralServiceInterface
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
interface ReferralServiceInterface
{
    /**
     * @param Dto $orderDto
     * @return ActiveRecord
     */
    public function generateReferralByOrder(Dto $orderDto);

    /**
     * @param string $referralId
     * @return array
     */
    public function getEmployeesByReferral($referralId);

    /**
     * @param Model $form
     * @return mixed
     */
    public function getReferralList(Model $form);
    /**
     * @param string $id
     * @return ActiveRecord
     */
    public function getReferralById($id);
}
