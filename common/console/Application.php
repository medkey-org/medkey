<?php
namespace app\common\console;

use app\common\base\ModuleTrait;
use yii\helpers\FileHelper;

/**
 * Class Application
 * @package Common\Console
 * @copyright 2012-2019 Medkey
 */
class Application extends \yii\console\Application
{
    use ModuleTrait;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->controllerNamespace = 'app\commands';
        $this->_widgetNamespace = 'app\widgets';
        $this->_ormNamespace = 'app\models\orm';
        $this->_workflowNamespace = 'app\workflow';
        $this->_applicationNamespace = 'app\application';
        $this->_workflowHandlerNamespace = 'app\handlers';
        if ($this->dynamicModule) {
            $this->setDynamicModules($this->dynamicModuleDI);
        }
        $path = \Yii::$app->getBasePath() . DIRECTORY_SEPARATOR . 'modules';
        $modules = array_map(function ($m) {
            return basename($m);
        }, FileHelper::findDirectories($path, [
            'recursive' => false,
        ]));
        $nsModules = [];
        $pathModules = [];
        foreach ($modules as $m) {
            $nsModules[] = 'app\modules\\' . $m;
            $pathModules[] = $path . DIRECTORY_SEPARATOR . $m . DIRECTORY_SEPARATOR . 'migrations';
        }
        $this->controllerMap = array_merge($this->controllerMap, [
            'migrate' => [
                'class' => MigrateController::class,
                'migrationNamespaces' => $nsModules,
                'migrationPath' => array_merge(['@app/migrations'], $pathModules),
            ]
        ]);
        parent::init();
    }
}
