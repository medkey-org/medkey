<?php
namespace app\common\helpers;

use yii\base\InvalidParamException;

/**
 * Class Json
 * @package Common\Helpers
 * @copyright 2012-2019 Medkey
 */
class Json extends \yii\helpers\Json
{
    /**
     * @param string $json
     * @param bool $asArray
     * @param int $depth
     * @param int $options
     * @return mixed|null
     */
    public static function decode($json, $asArray = true, $depth = 512, $options = 0)
    {
        if (is_array($json)) {
            throw new InvalidParamException('Invalid JSON data.');
        } elseif ($json === null || $json === '') {
            return null;
        }
        $decode = json_decode((string) $json, $asArray, $depth, $options);
        static::handleJsonError(json_last_error());

        return $decode;
    }
}
