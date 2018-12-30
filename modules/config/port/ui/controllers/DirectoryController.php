<?php
namespace app\modules\config\port\ui\controllers;

use app\common\base\Module;
use app\common\web\Controller;
use app\modules\config\entities\DirectoryEntity;

/**
 * Class DirectoryController
 * @package Module\Config
 * @copyright 2012-2019 Medkey
 */
class DirectoryController extends Controller
{
    /**
     * @var DirectoryEntity
     */
    private $entity;


    /**
     * DirectoryController constructor.
     * @param string $id
     * @param Module $module
     * @param \app\modules\config\entities\DirectoryEntity $entity
     * @param array $config
     */
    public function __construct($id, Module $module, DirectoryEntity $entity, array $config = [])
    {
        $this->entity = $entity;
        parent::__construct($id, $module, $config);
    }

    public function init()
    {
        parent::init();
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * @param string $id
     * @return string
     */
    public function actionView($id)
    {
        $directory = $this->entity->findDirectory($id);
        return $this->render('view', [
            'directory' => $directory,
            'key' => $id,
        ]);
    }
}
