<?php
namespace app\modules\medical\application;

use app\common\data\ActiveDataProvider;
use app\common\helpers\CommonHelper;
use app\common\service\ApplicationService;
use app\common\service\exception\ApplicationServiceException;
use app\modules\medical\MedicalModule;
use app\modules\medical\models\finders\ServicePriceFinder;
use app\modules\medical\models\orm\ServicePrice;
use app\modules\medical\models\orm\ServicePriceList;
use app\modules\medical\models\form\ServicePriceList as ServicePriceListForm;
use app\modules\medical\models\form\ServicePrice as ServicePriceForm;
use yii\db\ActiveRecordInterface;

/**
 * Class ServicePriceService
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
class ServicePriceService extends ApplicationService implements ServicePriceServiceInterface
{
    /**
     * @param ServicePriceListForm $form
     * @throws ApplicationServiceException
     * @return ServicePriceList
     */
    public function createServicePriceList($form)
    {
        if (!$form instanceof ServicePriceListForm) {
            throw new ApplicationServiceException(MedicalModule::t('servicePrice', 'Can\'t create price list'));
        }
        $model = new ServicePriceList(['scenario' => 'create']);
        $model->loadForm($form);
        $model->save();
        return $model;
    }

    public function getServicePriceListById($id)
    {
        return ServicePriceList::findOneEx($id);
    }

    /**
     * @param string $id
     * @return ActiveRecordInterface
     */
    public function getActivePriceByServiceId($id)
    {
        $servicePrice = ServicePrice::find()
            ->joinWith('servicePriceList')
            ->where([
                'service_id' => $id,
                ServicePrice::tableColumns('status') => ServicePrice::STATUS_ACTIVE,
                ServicePriceList::tableColumns('status') => ServicePriceList::STATUS_ACTIVE,
            ])
            ->one();
        return $servicePrice;
    }

    /**
     * @param string $id
     * @param ServicePriceListForm $form
     * @return ServicePriceList
     * @throws ApplicationServiceException
     */
    public function updateServicePriceList($id, $form)
    {
        if (!$form instanceof ServicePriceListForm) {
            throw new ApplicationServiceException(MedicalModule::t('servicePrice', 'Can\'t create price list'));
        }
        $model = ServicePriceList::findOneEx($id);
        $model->setScenario('update');
        $model->loadForm($form);
        $model->save();
        return $model;
    }

    /**
     * @param ServicePriceForm $form
     * @return ServicePrice
     * @throws ApplicationServiceException
     */
    public function createServicePrice($form)
    {
        if (!$form instanceof ServicePriceForm) {
            throw new ApplicationServiceException(MedicalModule::t('servicePrice', 'Can\'t create price list'));
        }
        $model = new ServicePrice(['scenario' => 'create']);
        $model->loadForm($form);
        $model->save();
        return $model;
    }

    /**
     * @param string $id
     * @param ServicePriceForm $form
     * @return ServicePrice
     * @throws ApplicationServiceException
     */
    public function updateServicePrice($id, $form)
    {
        if (!$form instanceof ServicePriceForm) {
            throw new ApplicationServiceException(MedicalModule::t('servicePrice', 'Can\'t create price list'));
        }
        $model = ServicePrice::findOneEx($id);
        $model->setScenario('update');
        $model->loadForm($form);
        $model->save();
        return $model;
    }

    /**
     * @param ServicePriceFinder $form
     * @return ActiveDataProvider
     */
    public function getPriceList($form)
    {
        $query = ServicePrice::find();
        $query
            ->andFilterWhere([
                'service_price_list_id' => $form->service_price_list_id,
                'cast(updated_at as date)' =>
                    empty($form->updatedAt) ? null : \Yii::$app->formatter->asDate($form->updatedAt, CommonHelper::FORMAT_DATE_DB),
            ]);
        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10
            ],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getServicePriceList($form)
    {
        $query = ServicePriceList::find();
        $query
            ->andFilterWhere([
                'cast(updated_at as date)' =>
                    empty($form->updatedAt) ? null : \Yii::$app->formatter->asDate($form->updatedAt, CommonHelper::FORMAT_DATE_DB),
            ]);
        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10
            ],
        ]);
    }

    /**
     * @param mixed $raw
     * @param string $scenario
     * @return ServicePriceListForm
     */
    public function getServicePriceListForm($raw, $scenario = 'create')
    {
        $model = ServicePriceList::ensureWeak($raw);
        $servicePriceListForm = new ServicePriceListForm(['scenario' => $scenario]);
        $servicePriceListForm->loadAr($model);
        $servicePriceListForm->id = $model->id;
        return $servicePriceListForm;
    }

    /**
     * @param mixed $raw
     * @param string $scenario
     * @return ServicePriceForm
     */
    public function getServicePriceForm($raw, $scenario = 'create')
    {
        $model = ServicePrice::ensureWeak($raw);
        $servicePriceForm = new ServicePriceForm(['scenario' => $scenario]);
        $servicePriceForm->loadAr($model);
        $servicePriceForm->id = $model->id;
        return $servicePriceForm;
    }
}
