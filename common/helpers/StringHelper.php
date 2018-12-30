<?php
namespace app\common\helpers;

/**
 * Class StringHelper
 * @package Common\Helpers
 * @copyright 2012-2019 Medkey
 */
class StringHelper extends \yii\helpers\StringHelper
{
    /**
     * Transliterate cyrillic string
     * @param string $st
     * @return string
     */
    public static function transliterate($st)
    {
        $st = strtr(($st),
            "абвгдежзийклмнопрстуфхыэ",
            "abvgdegziyklmnoprstufhie"
        );
        $st = strtr($st, [
            'ё' => "yo",      'ц' => "ts",  'ч' => "ch",  'ш' => "sh",
            'щ' => "shch",    'ъ' => "",    'ь' => "",    'ю' => "yu",
            'я' => "ya",
        ]);

        return $st;
    }

    /**
     * Converts bytes count, returns string
     * For example: 9.54 MB
     * @param $bytes
     * @param int $precision
     * @return string
     */
    public static function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
