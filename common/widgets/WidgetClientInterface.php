<?php
namespace app\common\widgets;

/**
 * Interface WidgetClientInterface
 * @package Common\Widgets
 * @copyright 2012-2019 Medkey
 */
interface WidgetClientInterface
{
    /**
     * @param string $id
     *
     * @return void
    */
    public function registerClient($id);

    /**
     * @param string $serverClassName
     * @param string $serverModule
     * @param array $clientOptions
     *
     * @return mixed
     */
    public function prepareClientOptions($serverClassName, $serverModule, $clientOptions = []);

    /**
     * @return array
     */
    public function prepareClientModel();

    /**
     * @param string $serverClassName
     * @param string $serverModule
     *
     * @return mixed
     */
    public function prepareClientClassName($serverClassName, $serverModule);

    /**
     * @param string $clientClassName
     * @param string $el
     * @param array $clientOptions
     *
     * @return mixed
     */
    public function createClientWidget($clientClassName, $el, $clientOptions);

    /**
     * @param array $config
     *
     * @return void
     */
    public function setConfig($config);

    /**
     * @param bool $denied
     *
     * @return array
     */
    public function getConfig($denied = false);

    /**
     * @return array
     */
    public function deniedConfig();

    /**
     * @param string $content
     * @param array $config
     * @param array $options
     * @param bool $wrapper
     * @param bool $modelFromAttr
     *
     * @return string
     */
    public static function loaderButton($content, array $config = [], array $options = ['class' => 'btn btn-primary'], $modelFromAttr = true, $wrapper = true);

    /**
     * @param string $name
     * @param string $content
     * @param array $config
     * @param array $options
     * @param bool $wrapper
     *
     * @return string
     */
    public static function loaderTag($name, $content, array $config = [], array $options = ['href' => '#'], $wrapper = true);

    /**
     * @return mixed
     */
    public static function widget();

    /**
     * @return mixed
     */
    public static function begin();

    /**
     * @return mixed
     */
    public static function end();
}
