<?php
namespace app\modules\dashboard\widgets;

use app\common\helpers\Html;
use app\common\widgets\Widget;

/**
 * Class Dashlet
 * @package Module\Dashboard
 * @copyright 2012-2019 Medkey
 */
abstract class Dashlet extends Widget implements DashletInterface
{
    /**
     * Добавлен метод для перехвата исключений, возникающих внутри дашлетов
     * @param array $config
     * @return string
     * @throws \Exception
    */
    public static function widget($config = [])
    {
        try {
            $response = parent::widget($config);
        }
        catch (\Exception $e) {
            if (\Yii::$app->getRequest()->getIsAjax()) {
                throw $e;
            }
            $response = Html::alert($e->getMessage());
        }
        return $response;
    }
}
