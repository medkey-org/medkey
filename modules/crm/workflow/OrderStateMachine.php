<?php
namespace app\modules\crm\workflow;

use app\common\workflow\StateMachine;
use app\modules\crm\models\orm\Order;
use app\modules\crm\workflow\subscribers\OrderStateSubscriber;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Workflow\Definition;
use Symfony\Component\Workflow\MarkingStore\MarkingStoreInterface;
use Symfony\Component\Workflow\MarkingStore\SingleStateMarkingStore;
use Symfony\Component\Workflow\Transition;

/**
 * Class OrderStateMachine
 * @package Module\CRM
 * @copyright 2012-2019 Medkey
 * @deprecated
 */
class OrderStateMachine extends StateMachine
{
    /**
     * @inheritdoc
     */
    public function aclAlias()
    {
        return 'Статусная модель заказа';
    }

    /**
     *
     * ProcessingOrderStateMachine constructor.
     * @param Definition|null $definition
     * @param MarkingStoreInterface|null $markingStore
     * @param EventDispatcherInterface|null $dispatcher
     * @param string $name
     */
    public function __construct(Definition $definition = null, MarkingStoreInterface $markingStore = null, EventDispatcherInterface $dispatcher = null, $name = 'paid_order')
    {
        $places = [
            Order::STATUS_NEW,
            Order::STATUS_PAID,
            ORDER::STATUS_ERROR,
        ];
        $transitions[] = new Transition('paid', Order::STATUS_NEW, Order::STATUS_PAID);
//        $transitions[] = new Transition('error', Order::STATUS_NEW, Order::STATUS_ERROR);
//        $transitions[] = new Transition('error', Order::STATUS_PAID, Order::STATUS_ERROR);
        $definition = new Definition($places, $transitions);
        $dispatcher = new EventDispatcher();
        $subscriber = new OrderStateSubscriber();
        $dispatcher->addSubscriber($subscriber);
        parent::__construct($definition, new SingleStateMarkingStore('status'), $dispatcher, $name);
    }
}
