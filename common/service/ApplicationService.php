<?php
namespace app\common\service;

use app\common\acl\ApplicationResourceTrait;
use app\common\acl\resource\ApplicationResourceInterface;
use app\common\db\ActiveRecord;
use yii\base\BaseObject;

/**
 * Class Service
 * @package Common\Service
 * @copyright 2012-2019 Medkey
 */
class ApplicationService extends BaseObject implements ApplicationServiceInterface, ApplicationResourceInterface
{
    use ApplicationResourceTrait;

    /**
     * @var ActiveRecord
     * @deprecated
     */
    public $modelClass;
}
