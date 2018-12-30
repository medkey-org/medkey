<?php
namespace app\common\bots;

/**
 * Interface SkypeBotServiceInterface
 * @package Common\Bots
 * @copyright 2012-2019 Medkey
 */
interface SkypeBotServiceInterface
{
    public function sendMessageWithoutRequest($to, $content, $serviceUrl);

    /**
     * @return array
     */
    public function getSkypeToken();

    /**
     * @param array $authData
     * @param string $responseActivityRequestUrl
     * @param array $responseActivity
     */
    public function clientActivityRequest($authData, $responseActivityRequestUrl, $responseActivity);
}
