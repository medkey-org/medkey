<?php
namespace app\modules\dashboard\application;

use app\common\acl\resource\ResourceInterface;
use app\common\dto\Dto;
use app\common\service\ApplicationService;
use app\modules\dashboard\DashboardModule;
use app\modules\dashboard\models\finders\DashboardFinder;
use app\modules\dashboard\models\orm\Dashboard;
use yii\data\ArrayDataProvider;
use app\common\helpers\ArrayHelper;

/**
 * Class DashboardService
 * @package Module\Dashboard
 * @copyright 2012-2019 Medkey
 */
class DashboardService extends ApplicationService implements DashboardServiceInterface
{
    /**
     * Get user's dashboards collection by filterModel
     * @param DashboardFinder $filterModel
     * @return \app\common\base\Model|mixed
     */
    public function getCollectionForUserByFilterModel($filterModel = null)
    {
        return DashboardFinder::ensure($filterModel, 'search');
    }

    /**
     * Get user's dashboards collection
     * @return \app\common\base\Model|mixed
     */
    public function getCollectionForUser()
    {
        return DashboardFinder::ensure(null, 'search')->search();

        if (!Dashboard::gate()->can('view')) {
            return new ArrayDataProvider([
                'allModels' => ArrayHelper::filterBy(DashboardFinder::ensure(null, 'search')->search()->getModels(), function (Dashboard $model) {
                    // через гейт, чтоб избежать повторных проверок на уровне класса
                    return $model->gate->can('view');
                }),
            ]);
        }
    }

    /**
     * @inheritdoc
     */
    public function aclAlias()
    {
        return DashboardModule::t('dashboard', 'Dashboard');
    }

    /**
     * @inheritdoc
     */
    public function getPrivileges()
    {
        return [
            'getAllCollectionByFilterModel' => DashboardModule::t('dashboard', 'View all dashboards (administrative feature)'),
            'getCollectionForUserByFilterModel' => DashboardModule::t('dashboard', 'View user\'s dashboards'),
        ];
    }

    /**
     * Get all dashboards collection
     * @param null $filterModel
     * @return \app\common\base\Model|mixed
     */
    public function getAllCollectionByFilterModel($filterModel = null)
    {
        return DashboardFinder::ensure($filterModel, 'search')->search();
    }

    /**
     * @param Dto $dashboardDto
    * @return Dashboard
        */
    public function getOneForUser($dashboardDto)
    {
        if (!($dashboardDto instanceof Dto)) {
            throw new InvalidValueException('Object is not instance Dto class (OrderDto)'); // todo normalize text
        }

        return Dashboard::ensureWeak($dashboardDto->id);
    }

    /**
     * @param $dashboardDto
     */
    public function addNewItem($dashboardDto)
    {
    }

}
