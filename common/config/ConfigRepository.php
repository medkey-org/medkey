<?php
namespace app\common\config;

/**
 * Class ConfigRepository
 *
 * @property array $configuration
 *
 * @package Common\Config
 * @copyright 2012-2019 Medkey
 * @deprecated
 */
class ConfigRepository
{
    const PRIORITY_DB = 1;
    const PRIORITY_FILE = 2;

    /**
     * @var array
     */
    private $_configuration = [];


    /**
     * Setter
     * @param array $configuration
     * @return void
     */
    public function setConfiguration(array $configuration)
    {
        $this->_configuration = $configuration;
    }

    /**
     * Getter
     * @return array
     * @return array
     */
    public function getConfiguration()
    {
        return $this->_configuration;
    }

    /**
     * @param string $key
     * @param string $entity
     * @param string $source
     * @return array
     */
    public function findAll($key = null, $entity = null, $source = null)
    {
        $config = $this->configuration;
        if (!is_array($config)) {
            return [];
        }
        $result = [];
        foreach ($config as $row) {
            $check = true;
            if (isset($key) && $row['key'] !== $key) {
                $check = false;
            }
            if (isset($entity) && $row['entity'] !== $entity) {
                $check = false;
            }
            if (isset($source) && $row['source'] !== $source) {
                $check = false;
            }
            if ($check) {
                array_push($result, $row);
            }
        }

        return $result;
    }

    /**
     * @param string $key
     * @param string $entity
     * @param string $source
     * @return array
     */
    public function findOne($key = null, $entity = null, $source = null)
    {
        // todo при поиске учитывать приоритет (если это вообще нужно)
        $config = $this->configuration;
        if (!is_array($config)) {
            return null;
        }
        foreach ($config as $row) {
            $check = true;
            if (isset($key) && $row['key'] !== $key) {
                $check = false;
            }
            if (isset($entity) && $row['entity'] !== $entity) {
                $check = false;
            }
            if (isset($source) && $row['source'] !== $source) {
                $check = false;
            }
            if ($check) {
                return $row;
            }
        }

        return null;
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
}
