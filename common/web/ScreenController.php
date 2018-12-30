<?php
namespace app\common\web;

use app\common\db\ActiveRecord;
use app\common\db\ActiveRecordRegistry;
use app\common\helpers\ClassHelper;

/**
 * Class PageController
 * @package Common\Web
 * @copyright 2012-2019 Medkey
 *
 * @deprecated
 */
class ScreenController extends Controller
{
    /**
     * @var string
     */
    public $modelClass;


    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if (isset($this->modelClass)) {
            return ActiveRecordRegistry::getNamespace(ClassHelper::getMatchModule($this, false, '/'), str_replace('Controller', '', ClassHelper::getShortName(static::className())));
        }
    }

    /**
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * @param string|int $id
     * @return string
     */
    public function actionView($id = null)
    {
        $modelClass = $this->modelClass;
        /** @var $modelClass ActiveRecord */
        $model = $modelClass::ensureWeak($id);
        return $this->render('view', compact('model'));
    }
}
