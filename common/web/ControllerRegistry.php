<?php
namespace app\common\web;

use app\common\base\Module;
use yii\base\BaseObject;

/**
 * Class ControllerRegistry
 * @package Common\Web
 * @copyright 2012-2019 Medkey
 * @deprecated
 */
class ControllerRegistry extends BaseObject
{
    /**
     * @param string $module
     * @param string $port
     * @return array
     */
    public static function registry($module, $port = 'ui')
    {
        $classes = [];
        /** @var Module $m */
        $m = \Yii::$app->getModule($module);
        if ($m === null) {
            return $classes;
        }
        $dir = $m->getControllerPath() . DIRECTORY_SEPARATOR . $port . DIRECTORY_SEPARATOR . 'controllers';
        $parentModule = $m->getUniqueId();
        $subModules = $m->getModules();
        if (!empty($subModules)) {
            // recursive
            foreach ($subModules as $subModule => $val) {
                $classes = array_merge($classes, ControllerRegistry::registry($parentModule . '/' . $subModule));
            }
        }
        if (!file_exists($dir)) {
            return $classes;
        }
        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file === '.' || $file === '..' || !is_file($dir . DIRECTORY_SEPARATOR . $file)) {
                continue;
            }
            $f = new \SplFileInfo($dir . DIRECTORY_SEPARATOR . $file);
            if ($f->getExtension() !== 'php') { // php 5.3.6+
                continue;
            }
            $basename = $f->getBasename('.php');
            $classes[] = $basename;
        }
        return $classes;
    }
}
