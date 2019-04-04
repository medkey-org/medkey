<?php
namespace app\port\rest\controllers;

use app\common\bots\SkypeBotServiceInterface;
use app\common\rest\Controller;
use app\modules\organization\application\EmployeeService;

/**
 * Class SkypeBotController
 * @package Common\REST
 * @copyright 2012-2019 Medkey
 * @deprecated
 */
class SkypeBotController extends Controller
{
    public $skypeBotService;
    public $employeeService;

    public function __construct(
        $id,
        $module,
        SkypeBotServiceInterface $skypeBotService,
        EmployeeService $employeeService,
        array $config = []
    ) {
        $this->skypeBotService = $skypeBotService;
        $this->employeeService = $employeeService;
        parent::__construct($id, $module, $config);
    }

    public function actionBot()
    {
        $authData = $this->skypeBotService->getSkypeToken();

        $requestActivity = \Yii::$app->request->getBodyParams();
        $serviceUrl = rtrim($requestActivity['serviceUrl'], '/');

        switch ((string)$requestActivity['type']) {
            case 'message':
                $text = trim((string)$requestActivity['text']);
                $id = (string)$requestActivity['from']['id'];
                $maskCode = "/\b[A-Za-z0-9_-]{6}\b/";
                if (preg_match($maskCode, $text)) {
                    $employee1 = $this->employeeService->getEmployeeBySkypeId($id);
                    if (is_null($employee1)) {
                        $employee = $this->employeeService->getEmployeeBySkypeCode($text);
                        if (!is_null($employee)) {
                            $employee->skype_bot_id = $id;
                            $employee->skype_service_url = $serviceUrl;
                            $employee->save();
                            $message = $employee->fullName . ",verification completed. Remove " . getenv('SKYPE_BOT_NAME') . " from your contact list.";
                        } else {
                            $message = "Not found. Try once again.";
                        }
                    } else {
                        $message = "Block";
                    }
                } else {
                    $message = "Block";
                }
                break;
            case 'contactRelationUpdate':
                if ((string)$requestActivity['action'] === 'add') {
                    $message = 'Welcome. Enter verification code.';
                } elseif ((string)$requestActivity['action'] === 'remove') {
                    $id = (string)$requestActivity['from']['id'];
                    $employee1 = $this->employeeService->getEmployeeBySkypeId($id);
                    if (!is_null($employee1)) {
                        $employee1->skype_bot_id = null;
                        $employee1->skype_service_url = null;
                        $employee1->save();
                    }
                    $message = 'Block';
                } else {
                    $message = 'Block';
                }
                break;
            default:
                $message = 'Block';
                break;
        }

        if ($message !== 'Block') {
            $responseActivity = [
                'type' => 'message',
                'text' => $message,
                'textFormat' => 'plain',
                'locale' => 'ru-RU',
                'replyToId' => (string)$requestActivity['id'],
                'from' => [
                    'id' => (string)$requestActivity['recipient']['id'],
                    'name' => (string)$requestActivity['recipient']['name']
                ],
                'recipient' => [
                    'id' => (string)$requestActivity['from']['id'],
                    'name' => (string)$requestActivity['from']['name']
                ],
                'conversation' => [
                    'id' => (string)$requestActivity['conversation']['id']
                ]
            ];
            $responseActivityRequestUrl = $serviceUrl
                . '/v3/conversations/' . $responseActivity['conversation']['id']
                . '/activities/' . urlencode($responseActivity['replyToId']);

            $this->skypeBotService->clientActivityRequest($authData, $responseActivityRequestUrl, $responseActivity);
        }
    }
}
