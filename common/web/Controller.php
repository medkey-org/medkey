<?php
namespace app\common\web;

use app\common\helpers\ClientHelper;
use yii\filters\AccessControl;
use yii\filters\ContentNegotiator;

/**
 * Class Controller
 * @package Common\Web
 * @copyright 2012-2019 Medkey
 */
class Controller extends \yii\web\Controller
{
    const TYPE_SUCCESS = 1;
    const TYPE_WARNING = 2;
    const TYPE_ERROR = 3;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'contentNegotiator' => [
                'class' => ContentNegotiator::class,
                'formats' => [
                    'text/html' => Response::FORMAT_HTML,
                    'application/json' => Response::FORMAT_JSON,
                    'application/xml' => Response::FORMAT_XML,
                ],
            ],
        ]);
    }

    public function init()
    {
        parent::init();
        if (!$this->getBehavior('access')) {
            $this->attachBehavior('access', [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ]
                ]
            ]);
        }
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * @param string $message
     * @param int $type
     * @return string
     */
    public function success($message, $type = self::TYPE_SUCCESS)
    {
        return $this->asJson(ClientHelper::messageFactory($message, $type));
    }

    /**
     * @param string $message
     * @param int $type
     * @return string
     */
    public function warning($message, $type = self::TYPE_WARNING)
    {
        return $this->asJson(ClientHelper::messageFactory($message, $type));
    }

    /**
     * @param string $message
     * @param int $type
     * @return string
     */
    public function error($message, $type = self::TYPE_ERROR)
    {
        return $this->asJson(ClientHelper::messageFactory($message, $type));
    }
}
