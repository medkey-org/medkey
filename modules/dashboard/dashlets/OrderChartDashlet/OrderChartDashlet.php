<?php
namespace app\modules\dashboard\dashlets\OrderChartDashlet;

use app\modules\crm\application\OrderServiceInterface;
use app\modules\dashboard\DashboardModule;
use app\modules\dashboard\widgets\Dashlet;
use machour\flot\Chart as Chart;

/**
 * Class OrderListDashlet
 * @package Module\Dashboard
 * @copyright 2012-2019 Medkey
 */
class OrderChartDashlet extends Dashlet
{
    /**
     * @var OrderServiceInterface
     */
    private $orderService;


    /**
     * @param OrderServiceInterface $orderService
     * @param array $config
     */
    public function __construct(OrderServiceInterface $orderService, array $config = [])
    {
        $this->orderService = $orderService;
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $orders = $this->orderService->getOrderCountForWeek();

        $ticksCollection = [];
        $i = 0;

        $ordersCollection = array_map(function ($order) use (&$ticksCollection, &$i) {
            $ticksCollection[$i] = [$i, $order['created_at_day']];
            $data = [$i, $order['count']];
            $i++;
            return $data;
        }, $orders);

        return Chart::widget([
            'data' => [
                [
                    'label' => DashboardModule::t('dashboard', 'Order count'),
                    'data'  => $ordersCollection,
                    'lines'  => ['show' => true],
                    'points' => ['show' => true],
                ],

            ],
            'options' => [
                'xaxis' => [
                    'ticks' => $ticksCollection,
                ],
                'yaxis' => [
                    'tickDecimals' => 0,
                ]
            ],
            'htmlOptions' => [
                'style' => 'width:100%;height:400px;'
            ]
        ]);
    }
}
