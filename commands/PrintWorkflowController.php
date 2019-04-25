<?php
namespace app\commands;

use app\common\console\Controller;
use app\common\workflow\WorkflowManagerInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Workflow\Dumper\GraphvizDumper;
use yii\helpers\FileHelper;

/**
 * Class PrintWorkflowController
 * @package Common\CLI
 * @copyright 2012-2019 Medkey
 */
class PrintWorkflowController extends Controller
{
    /**
     * @var WorkflowManagerInterface
     */
    public $workflowManager;

    /**
     * PrintWorkflowController constructor.
     * @param $id
     * @param $module
     * @param WorkflowManagerInterface $workflowManager
     * @param array $config
     */
    public function __construct($id, $module, WorkflowManagerInterface $workflowManager, array $config = [])
    {
        $this->workflowManager = $workflowManager;
        parent::__construct($id, $module, $config);
    }

    /**
     * @param $workflowId
     * @return null
     * @throws \yii\base\Exception
     */
    public function actionPrint($workflowId)
    {
        $definition = $this->workflowManager->definitionFactory($workflowId);
        if (!isset($definition)) {
            return null;
        }
        $dumper = new GraphvizDumper();
        $dotRaw = $dumper->dump($definition);
        $pathDir = \Yii::getAlias('@runtime/temp');
        if (!is_dir($pathDir)) {
            FileHelper::createDirectory($pathDir, 0777);
        }
        $pathImage = $pathDir . DIRECTORY_SEPARATOR . $workflowId . '-workflow.png';
        $process = new Process('dot -Tpng -o ' . $pathImage);
        $process->setInput($dotRaw);
        $process->run();
        if (!$process->isSuccessful()) {
            \Yii::error('Can\'t draw graph, check for graphviz on your system: ' . $process->getErrorOutput());
        }
        echo $pathImage;
    }
}
