<?php
namespace app\modules\config\port\ui\controllers;

use app\common\base\Module;
use app\common\web\Controller;
use app\modules\config\entities\DirectoryEntity;

/**
 * Class DocumentController
 * @package Module\Config
 * @copyright 2012-2019 Medkey
 */
class DocumentTemplateController extends Controller
{
    public function __construct($id, Module $module, array $config = [])
    {
        parent::__construct($id, $module, $config);
    }

    public function init()
    {
        parent::init();
    }

    /**
     * @return string
     */
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
        return $this->render('view', [
            'id' => $id,
        ]);
    }
}
