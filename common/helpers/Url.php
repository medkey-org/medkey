<?php
namespace app\common\helpers;

/**
 * Class Url
 * @package Common\Helpers
 * @copyright 2012-2019 Medkey
 */
class Url extends \yii\helpers\Url
{
    public static $fakeLogin = 'fake';

    /**
     * @param null $url
     * @return string
     */
    public static function basicLogout($url = null)
    {
        $url = is_null($url) ? \Yii::$app->getHomeUrl() : $url;
        return static::ensureUser($url, static::$fakeLogin);
    }

    /**
     * @param array|string $url
     * @param string|null  $user
     * @param string|null  $pass
     * @return string
     * @throws \Exception
     */
    public static function ensureUser($url, $user = null, $pass = null)
    {
        $url = static::to($url, true);
        $secure = \Yii::$app->request->getIsSecureConnection();
        $scheme = $secure ? 'https' : 'http';

        if (substr($url, 0, 2) === '//') {
            // e.g. //example.com/path/to/resource
            $url = static::ensureScheme($url, $scheme);
        }
        if (($pos = strpos($url, '://')) === false) {
            throw new \Exception('Invalid url given: must be absolute');
        }
        $user = is_null($user) ? (\Yii::$app->user->identity ? \Yii::$app->user->identity->login : static::$fakeLogin) : $user;

        if ($pass) {
            $user = "$user:$pass";
        }
        $url = $scheme . '://' . "$user@" . substr($url, $pos + 3);

        return $url;
    }
}
