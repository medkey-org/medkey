<?php
namespace app\modules\dashboard;

use app\common\base\Module;

/**
 * Class DashboardModule
 * @package Module\Dashboard
 * @copyright 2012-2019 Medkey
 */
class DashboardModule extends Module
{
    /**
     * @var array
     */
    public $layouts;
    /**
     * @var array
     */
    public $dashlets;


    /**
     * Initialize
     */
    public function init()
    {
        parent::init();

        \Yii::configure($this, require(__DIR__ . '/config/main.php'));

        foreach (\Yii::$app->modules as $key => $value) {

            $dir = implode('/', ['@app/modules', $key]);
            $file = \Yii::getAlias(implode('/', [$dir, 'config', 'dashlets.php']));

            if (is_file($file)) {
                $data = require($file);
                foreach ($data as $name => $dashlet) {
                    $dashlet['module'] = $key;
                    $this->dashlets[$name] = $dashlet;
                }
            }
        }
    }

    /**
     * @inheritdoc
     */
    public static function translationList()
    {
        return [
            'dashboard',
        ];
    }
}
