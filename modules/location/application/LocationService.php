<?php
namespace app\modules\location\application;

use app\common\data\ActiveDataProvider;
use app\common\helpers\CommonHelper;
use app\common\service\ApplicationService;
use app\common\service\exception\AccessApplicationServiceException;
use app\modules\location\LocationModule;
use app\modules\location\models\finders\LocationFinder;
use app\modules\location\models\orm\Location;

/**
 * Class LocationService
 * @package Module\Location
 * @copyright 2012-2019 Medkey
 */
class LocationService extends ApplicationService implements LocationServiceInterface
{
    /**
     * @inheritdoc
     */
    public function aclAlias()
    {
        return LocationModule::t('location', 'Location');
    }

    /**
     * @inheritdoc
     */
    public function getPrivileges()
    {
        return [
            'getLocationList' => LocationModule::t('location', 'Get locations list'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function getLocationList(LocationFinder $form)
    {
        if (!$this->isAllowed('getLocationList')) {
            throw new AccessApplicationServiceException('Доступ к списку локаций запрещен.');
        }
        $query = Location::find();
        if (!empty($form->startDate)) {
            $form->startDate = \Yii::$app->formatter->asDate($this->startDate, CommonHelper::FORMAT_DATE_DB);
        }
        if (!empty($this->endDate)) {
            $form->endDate = \Yii::$app->formatter->asDate($this->endDate, CommonHelper::FORMAT_DATE_DB);
        }
        $query
            ->andFilterWhere([
                'status' => $form->status
            ])
            ->andFilterWhere([
                'code' => $form->code
            ])
            ->andFilterWhere([
                'end_date' => $form->endDate
            ])
            ->andFilterWhere([
                'start_date' => $form->startDate
            ])
            ->andFilterWhere([
                'like',
                'description',
                $form->description
            ]);
        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10
            ],
        ]);
    }
}
