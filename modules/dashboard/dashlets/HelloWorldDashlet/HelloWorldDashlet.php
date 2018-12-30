<?php
namespace app\modules\dashboard\dashlets\HelloWorldDashlet;

use \app\modules\dashboard\widgets\Dashlet;

/**
 * Class HelloWorldDashlet
 * @package Module\Dashboard
 * @copyright 2012-2019 Medkey
 */
class HelloWorldDashlet extends Dashlet
{
    /**
     * @inheritdoc
     */
    public function run() {
        return '<h2>Hello World</h2>';
    }
}
