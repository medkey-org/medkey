<?php
namespace app\modules\medical\widgets\grid;

use app\common\button\LinkActionButton;
use app\common\button\WidgetLoaderButton;
use app\common\grid\GridView;
use app\modules\medical\application\ReferralServiceInterface;
use app\modules\medical\models\finders\ReferralItemFilter;
use app\modules\medical\models\orm\Referral;
use app\modules\medical\models\orm\ReferralItem;
use app\modules\medical\models\orm\Service;
use app\modules\medical\widgets\form\ReferralItemCreateForm;
use app\modules\medical\widgets\form\ReferralItemUpdateForm;

/**
 * Class ReferralItemGrid
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
class ReferralItemGrid extends GridView
{
    /**
     * @var ReferralItemFilter
     */
    public $filterModel;
    /**
     * @var string
     */
    public $referralId;
    /**
     * @var ReferralServiceInterface
     */
    public $referralService;


    /**
     * ReferralItemGrid constructor.
     * @param ReferralServiceInterface $referralService
     * @param array $config
     */
    public function __construct(ReferralServiceInterface $referralService, array $config = [])
    {
        $this->referralService = $referralService;
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->filterModel = ReferralItemFilter::ensure($this->filterModel);
        $this->filterModel->referralId = $this->referralId;
        $this->dataProvider = $this->referralService->getReferralItemList($this->filterModel);
        /** @var Referral $referral */
        $referral = $this->referralService->getReferralById($this->referralId);
        if ($referral->isNew()) {
            $this->actionButtons['create'] = [
                'class' => WidgetLoaderButton::class,
                'widgetClass' => ReferralItemCreateForm::class,
                'disabled' => false,
                'isDynamicModel' => false,
                'value' => '',
                'widgetConfig' => [
                    'referral' => $this->referralId,
                    'afterUpdateBlockId' => $this->getId()
                ],
                'options' => [
                    'class' => 'btn btn-primary btn-xs',
                    'icon' => 'plus'
                ]
            ];
            $this->actionButtons['update'] = [
                'class' => WidgetLoaderButton::class,
                'widgetClass' => ReferralItemUpdateForm::class,
                'disabled' => true,
                'isDynamicModel' => true,
                'value' => '',
                'widgetConfig' => [
                    'afterUpdateBlockId' => $this->getId()
                ],
                'options' => [
                    'class' => 'btn btn-primary btn-xs',
                    'icon' => 'edit'
                ]
            ];
            $this->actionButtons['delete'] = [
                'class' => LinkActionButton::class,
                'url' => ['/medical/rest/referral-item/delete'],
                'isDynamicModel' => true,
                'isAjax' => true,
                'disabled' => true,
                'isConfirm' => true,
                'value' => '',
                'options' => [
                    'class' => 'btn btn-danger btn-xs',
                    'icon' => 'remove',
                ],
            ];
        }
        $this->columns = [
            [
                'attribute' => 'service_id',
                'value' => function (ReferralItem $model) {
                    if (!$model->service instanceof Service) {
                        return '';
                    }
                    return $model->service->title;
                }
            ],
        ];
        parent::init();
    }
}
