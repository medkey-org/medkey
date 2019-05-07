<?php
namespace app\modules\crm\widgets\form;

use app\common\widgets\FormWidget;
use app\common\wrappers\DynamicModal;
use app\modules\crm\CrmModule;

/**
 * Class OrderItemUpdateForm
 * @package Module\CRM
 * @copyright 2012-2019 Medkey
 */
class OrderItemUpdateForm extends OrderItemCreateForm
{
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->model = $this->orderService->getOrderItemForm($this->model, 'update');
        $this->action = ['/crm/rest/order-item/update', 'id' => $this->model->id];
        $this->validationUrl = ['/crm/rest/order-item/validate-update', 'id' => $this->model->id];
        FormWidget::init();
    }

    /**
     * {@inheritdoc}
     */
    public function wrapperOptions()
    {
        return [
            'wrapperClass' => DynamicModal::class,
            'header' => CrmModule::t('order', 'Update order position'),
        ];
    }
}
