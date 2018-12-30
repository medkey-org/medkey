<?php
namespace app\common\workflow;

use app\common\acl\ApplicationResourceTrait;
use app\common\acl\resource\ApplicationResourceInterface;
use app\common\service\ApplicationServiceInterface;
use Symfony\Component\Workflow\Transition;

/**
 * Class StateMachine
 * @package Common\Workflow
 * @copyright 2012-2019 Medkey
 */
class StateMachine extends \Symfony\Component\Workflow\StateMachine implements ApplicationServiceInterface, ApplicationResourceInterface
{
    use ApplicationResourceTrait;

    /**
     * @inheritdoc
     */
    public function getPrivileges()
    {
        $privileges = [];
        $transitions = $this->getDefinition()->getTransitions();
        if (empty($transitions)) {
            return $privileges;
        }
        foreach ($transitions as $transition) {
            if (!$transition instanceof Transition) {
                continue; // or ERROR
            }
            $privileges[$transition->getName()] = \Yii::t('app', $transition->getName());
        }
        return $privileges;
    }
}
