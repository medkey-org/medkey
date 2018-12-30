<?php
namespace app\common\config;

use app\common\helpers\ArrayHelper;
use app\modules\config\models\orm\Config;
use yii\base\Component;
use yii\base\InvalidValueException;
use yii\base\Module;

/**
 * Class ConfigManager
 *
 * @property-read array $configuration
 *
 * @package Common\Config
 * @copyright 2012-2019 Medkey
 * @deprecated
 */
class ConfigManager extends Component
{
    const SOURCE_DB = '__db';

    /**
     * @var ConfigRepository
     */
    private $repository;
    /**
     * @var array
     */
    private $_configuration = [];


    /**
     * ConfigManager constructor.
     * @param ConfigRepository $repository
     * @param array $config
     */
    public function __construct(ConfigRepository $repository, array $config = [])
    {
        $this->repository = $repository;
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->initConfiguration();
        parent::init();
    }

    /**
     * @param string $key
     * @param string $entity
     * @param string $source
     * @return array
     */
    public function findAll($key = null, $entity = null, $source = null)
    {
        return $this->repository->findAll($key, $entity, $source);
    }

    /**
     * @param string $key
     * @param string $entity
     * @param string $source
     * @return array
     */
    public function findOne($key = null, $entity = null, $source = null)
    {
        return $this->repository->findOne($key, $entity, $source);
    }

    /**
     * @param string $key
     * @param string $value
     * @param string $source
     * @param string $entity
     */
    public function add($key, $value, $source = self::SOURCE_DB, $entity = null)
    {

    }

    /**
     * @param string $key
     * @param string $value
     * @param string $source
     * @param string $entity
     */
    public function update($key, $value, $source = self::SOURCE_DB, $entity = null)
    {

    }

    /**
     * @param string $key
     * @param string $value
     * @param string $source
     * @param string $entity
     */
    public function remove($key, $value, $source = '', $entity = null)
    {

    }

    /**
     * @return void
     */
    private function initConfiguration()
    {
        $this->loadFileConfiguration();
        $this->loadDbConfiguration();
        $this->repository->configuration = $this->_configuration;
        $this->_configuration = null;
    }

    /**
     * @param array $row
     * @return bool
     */
    private function checkFormat($row)
    {
        if (is_array($row) && isset($row['key']) && isset($row['value'])) {
            return true;
        }
        return false;
    }

    /**
     * @return void
     */
    private function loadDbConfiguration()
    {
        $table = \Yii::$app->db->getTableSchema(Config::tableName());
        $config = [];
        if ($table) {
            $config = Config::find()
                ->notDeleted()
                ->setAccess(false)
                ->all();
            $config = ArrayHelper::toArray($config);
            if (!is_array($config)) {
                throw new InvalidValueException(\Yii::t('app', 'Error parse in configManager')); // normalize text
            }
        }
        $_config = [];
        foreach ($config as $row) {
            $c = ['key' => $row['key'], 'value' => $row['value'], 'entity' => $row['entity'], 'source' => self::SOURCE_DB];
            array_push($_config, $c);
        }
        $this->_configuration = array_merge($this->_configuration, $_config);
    }

    /**
     * @param string $dir
     * @return void
     */
    private function parseFileConfig($dir)
    {
        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file === '.' || $file === '..' ) {
                continue;
            }
            $f = $dir . DIRECTORY_SEPARATOR . $file;
            $type = new \SplFileInfo($f);
            if (!is_file($f) || $type->getExtension() !== 'php') { // php 5.3.6+
                continue;
            }
            $config = require($f);
            if (!is_array($config)) {
                continue;
            }
            $_config = [];
            if ($this->checkFormat($config)) {
                $c = ['key' => $config['key'], 'value' => $config['value']];
                if (!empty($config['entity'])) { // todo что-то тут может измениться в будущем.
                    $c['entity'] = $config['entity'];
                }
                $c['source'] = $f;
                array_push($_config, $c);
            } else {
                foreach ($config as $row) {
                    if (!$this->checkFormat($row)) {
                        continue;
                    }
                    $c = ['key' => $row['key'], 'value' => $row['value']];
                    if (!empty($row['entity'])) { // todo что-то тут может измениться в будущем.
                        $c['entity'] = $row['entity'];
                    }
                    $c['source'] = $f;
                    array_push($_config, $c);
                }
            }
            $this->_configuration = array_merge($this->_configuration, $_config);
        }
    }

    /**
     * @param null $modules
     * @throws \ReflectionException
     */
    private function loadFileConfiguration($modules = null)
    {
        $modules = !empty($modules) ? $modules : \Yii::$app->getModules();
        foreach ($modules as $id => $module) {
            $path = '';
            if ($module instanceof Module) {
                $path = $module->getBasePath();
            } elseif (is_array($module)) {
                $class = $module['class'];
                $reflector = new \ReflectionClass($class);
                $path = dirname($reflector->getFileName());
            }
            if (empty($path)) {
                continue;
            }
            $files = scandir($path);
            foreach ($files as $file) {
                if ($file === '.' || $file === '..') {
                    continue;
                }
                $p = $path . DIRECTORY_SEPARATOR . $file;
                if (!is_dir($p) || $file !== 'config') {
                    continue;
                }
                $this->parseFileConfig($path . DIRECTORY_SEPARATOR . 'config');
            }
            // next recursive
            $subModules = [];
            if ($module instanceof Module) {
                $subModules = $module->getModules();
            } elseif (is_array($module) && isset($module['modules'])) {
                $subModules = $module['modules'];
            }
            if (!empty($subModules)) {
                $this->loadFileConfiguration($subModules);
            }
        }
    }
}
