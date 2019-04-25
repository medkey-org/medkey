<?php
namespace app\modules\config\widgets\misc;

use app\common\helpers\Html;
use app\common\widgets\Widget;
use app\common\wrappers\Block;
use app\modules\config\ConfigModule;
use app\modules\config\widgets\grid\WorkflowTransitionGrid;

/**
 * Class WorkflowTransition
 * @package Module\Config
 * @copyright 2012-2019 Medkey
 */
class WorkflowTransitionWidget extends Widget
{
    public $wrapper = true;
    public $workflowId;

    /**
     * @inheritdoc
     */
    public function run()
    {
        echo Html::beginRow();
        echo Html::beginCol(['size' => ['xs' => 6]]);
        echo WorkflowTransitionGrid::widget([
            'workflowId' => $this->workflowId,
            'wrapperId' => $this->getId()
        ]);
        echo Html::endCol();
        echo Html::beginCol(['size' => ['xs' => 6]]);
        echo $this->printWorkflow($this->workflowId);
        echo Html::endCol();
        echo Html::endRow();
    }

    public function printWorkflow($id)
    {
        $basePath = \Yii::$app->basePath;
        $command = 'php ' .
            $basePath .
            DIRECTORY_SEPARATOR .
            'bin print-workflow/print ' .
            $id;
        $image = shell_exec($command);
        if (empty($image)) {
            return ConfigModule::t('workflow', 'No any transitions');
        }
        if (!file_exists($image)) {
            return Html::alert(ConfigModule::t('workflow', 'Can\'t draw workflow image, check logs'));
        }
        $content = base64_encode(file_get_contents($image));
        echo Html::img("data:image/gif;base64," . $content);
    }

    /**
     * @inheritdoc
     */
    public function wrapperOptions()
    {
        return [
            'wrapperClass' => Block::class,
            'header' => ConfigModule::t('workflow', 'Workflow transitions'),
        ];
    }
}
