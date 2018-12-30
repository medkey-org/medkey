<?php
namespace app\common\bots;

use app\common\service\ApplicationService;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7;

/**
 * Class SkypeBotService
 * @package Common\Bots
 * @copyright 2012-2019 Medkey
 */
class SkypeBotService extends ApplicationService implements SkypeBotServiceInterface
{
    public function sendMessageWithoutRequest($to, $content, $serviceUrl)
    {
        $authData = $this->getSkypeToken();

        $responseActivity = [
            'type' => 'message',
            'text' => $content,
            'textFormat' => 'plain',
            'locale' => 'ru-RU',
            'from' => [
                'id' => getenv('SKYPE_CLIENT_ID'),
                'name' => getenv('SKYPE_BOT_NAME')//
            ],
            'recipient' => [
                'id' => $to,
            ],
            'conversation' => [
                'id' => $to
            ]
        ];
        $responseActivityRequestUrl = $serviceUrl . "/v3/conversations/"
            . $responseActivity['conversation']['id'] . "/activities";

        $this->clientActivityRequest($authData, $responseActivityRequestUrl, $responseActivity);
    }

    public function getSkypeToken()
    {
        $authRequestUrl = 'https://login.microsoftonline.com/botframework.com/oauth2/v2.0/token';
        $params = [
            'grant_type' => 'client_credentials',
            'client_id' => getenv('SKYPE_CLIENT_ID'),
            'client_secret' => getenv('SKYPE_CLIENT_SECRET'),
            'scope' => 'https://api.botframework.com/.default'
        ];
        $client = new Client();
        $authResult = $client->request('POST', $authRequestUrl, ['form_params' => $params]);
        $body = Psr7\stream_for($authResult->getBody());
        return json_decode($body, true);
    }

    public function clientActivityRequest($authData, $responseActivityRequestUrl, $responseActivity)
    {
        $headers = [
            'Authorization' => $authData['token_type'] . ' ' . $authData['access_token'],
        ];
        $client = new Client();
        $authResult = $client->request('POST', $responseActivityRequestUrl, [
            'headers' => $headers,
            'json' => $responseActivity
        ]);
    }
}
