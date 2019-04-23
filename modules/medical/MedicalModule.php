<?php
namespace app\modules\medical;

use app\common\base\Module;

/**
 * Class MedicalModule
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
class MedicalModule extends Module
{
    public static function translationList()
    {
        return [
            'attendance',
            'common',
            'ehr',
            'insurance',
            'patient',
            'policy',
            'referral',
            'schedule',
            'service',
            'servicePrice',
            'servicePriceList',
            'speciality',
            'workplace',
        ];
    }
}
