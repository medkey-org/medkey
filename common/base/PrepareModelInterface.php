<?php
namespace app\common\base;

/**
 * Interface PrepareModelInterface
 * @package Common\Base
 * @copyright 2012-2019 Medkey
 */
interface PrepareModelInterface
{
    /**
     * Prepare model of data
     * @param mixed $model
     * @param string $scenario
     * @param array $data
     * @return mixed
     */
    public static function ensure($model, $scenario, $data = []);
}
