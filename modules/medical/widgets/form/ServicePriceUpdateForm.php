<?php
namespace app\modules\medical\widgets\form;

use app\common\widgets\FormWidget;

/**
 * Class ServicePriceUpdateForm
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
class ServicePriceUpdateForm extends ServicePriceCreateForm
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->model = $this->servicePriceService->getServicePriceForm($this->model, 'update');
        $this->action = ['/medical/rest/service-price/update', 'id' => $this->model->id];
        $this->validationUrl = ['/medical/rest/service-price/validate-update', 'id' => $this->model->id];
        FormWidget::init();
    }
}
