<?php
namespace app\common\port\cli\controllers;

use app\common\console\Controller;
use app\modules\incident\application\FileServiceInterface;

/**
 * Class FileController
 * @package Common\CLI
 * @copyright 2012-2019 Medkey
 *
 */
class FileController extends Controller
{
    /**
     * @var FileServiceInterface
     */
    public $fileService;

    public function __construct($id, $module, FileServiceInterface $fileService, array $config = [])
    {
        $this->fileService = $fileService;
        parent::__construct($id, $module, $config);
    }

    /**
     * Generates and push files to a bait server
     * @param int $virtualUserId id of the virtual user to generate emails for
     * @param string $fileSet A file set to start with
     */
    public function actionUpload(int $virtualUserId, string $fileSet)
    {
        $this->fileService->upload($virtualUserId, $fileSet);
    }

    /**
     * Lists available file sets
     */
    public function actionListFileSets() {
        foreach ($this->fileService->listFileSets() as $fileSet) {
            echo $fileSet . PHP_EOL;
        }
    }
}
