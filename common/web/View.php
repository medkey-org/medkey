<?php
namespace app\common\web;

use yii\helpers\FileHelper;

/**
 * Class View
 * @package Common\Web
 * @copyright 2012-2019 Medkey
 */
class View extends \yii\web\View
{
    public $themeName = 'basic';

    public function init()
    {
        $this->title = getenv('COMMON_WEB_TITLE');
        $this->theme = [];
        $this->theme['basePath'] = '@app/themes/' . $this->themeName;
        $this->theme['baseUrl'] = '@web/themes/' . $this->themeName;
        $path = \Yii::$app->getBasePath() . DIRECTORY_SEPARATOR . 'modules';
        $modules = array_map(function ($m) {
            return basename($m);
        }, FileHelper::findDirectories($path, [
                'recursive' => false,
            ])
        ); // Yii2 submodules not supported
        $this->theme['pathMap'] = [];
        $this->theme['pathMap']['@app/views'] = '@app/themes/' . $this->themeName;
        foreach ($modules as $m) {
            $this->theme['pathMap']['@app/modules/' . $m] = '@app/modules/' . $m . '/themes/' . $this->themeName;
        }
        parent::init();
    }
}
