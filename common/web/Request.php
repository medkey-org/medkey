<?php
namespace app\common\web;

use app\common\helpers\Url;
use yii\base\ErrorException;

/**
 * Class Request
 * @package Common\Web
 * @copyright 2012-2019 Medkey
 */
class Request extends \yii\web\Request
{
    /**
     * @var string
     */
    private $_redirectUrl;

    /**
     * @return string
     */
    public function getRedirectUrl()
    {
        if (isset($_POST['_redirectUrl'])) {
            $this->_redirectUrl = $_POST['_redirectUrl'];

            return Url::toRoute($this->_redirectUrl);
        }

        return \Yii::$app->getHomeUrl();
    }

    public function getIsRedirect()
    {
        if (!isset($_POST['_redirectUrl'])) {
            return false;
        }

        return true;
    }

    /**
     * @param $ajaxParam
     * @return bool
     */
    public function getIsAjaxValidate($ajaxParam)
    {
        if ($this->post($ajaxParam) || $this->get($ajaxParam)) {
            return true;
        }
    }
}
