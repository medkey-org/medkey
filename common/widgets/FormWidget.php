<?php
namespace app\common\widgets;

use app\common\helpers\ClassHelper;
use app\common\helpers\CommonHelper;
use yii\base\Model;
use yii\helpers\Url;

/**
 * Class FormWidget
 * @package Common\Widgets
 * @copyright 2012-2019 Medkey
 */
class FormWidget extends Widget
{
    /**
     * @var bool
     */
    public $enableAjaxValidation = true;
    /**
     * @var bool
     */
    public $enableClientValidation = true;
    /**
     * @var bool
     */
    public $validateOnBlur = true;
    /**
     * @var bool
     */
    public $validateOnChange = true;
    /**
     * @var bool
     */
    public $animateLoading = true;
    /**
     * @var string
     */
    public $validationUrl;
    /**
     * @var array
     */
    public $formOptions = [];
    /**
     * @var string
     */
    public $action = '';
    /**
     * @var string
     */
    public $method = 'post';
    /**
     * @var bool
     */
    public $ajaxSubmit = true;
    /**
     * @var bool
     */
    public $afterCloseModal = true;
    /**
     * @var bool
     * @see Request::getIsRedirect()
     */
    public $afterRedirect = false;
    /**
     * @var Widget
     */
    public $afterUpdateBlockId;
    /**
     * @var string
     * @see Request::getRedirectUrl()
     */
    public $redirectUrl;
    /**
     * @var string
     */
    public $layout = 'default';
    /**
     * @var string Кастомный HTML, который должен быть выведен вместо renderForm
     */
    public $content;
    /**
     * @var null|callable
     */
    public $callback;
    /**
     * @var bool
     */
    public $collapsible = false;
    /**
     * @var array
     */
    public $collapsePanelOptions = [];
    /**
     * @var array
     */
    public $collapsePanelDefaultOptions = [
        'header' => 'Форма'
    ];

    /**
     * Template method
     * @param Model $model
     * @param ActiveForm $form
     */
    public function renderForm($model, $form)
    {
        echo CommonHelper::value($this->callback, $model, $form);
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        if (!isset($this->clientClassNameDefault)) {
            $this->clientClassNameDefault = ClassHelper::getShortName(__CLASS__);
        }
//        if (!isset($this->model)) {
//            throw new InvalidValueException(\Yii::t('app', 'The model is not initialized in FormWidget'));
//        }
        if (is_array($this->action)) {
            $this->action = Url::to($this->action);
        }
        if (is_array($this->validationUrl)) {
            $this->validationUrl = Url::to($this->validationUrl);
        }
        if (!isset($this->redirectUrl)) {
            $this->redirectUrl = Url::to(\Yii::$app->request->referrer);
        }
        $this->clientViewOptions['action'] = $this->action;
        $this->clientViewOptions['ajaxSubmit'] = $this->ajaxSubmit;
        $this->clientViewOptions['afterCloseModal'] = $this->afterCloseModal;
        $this->clientViewOptions['afterRedirect'] = $this->afterRedirect;
        $this->clientViewOptions['redirectUrl'] = $this->redirectUrl;
        $this->clientViewOptions['afterUpdateBlockId'] = $this->afterUpdateBlockId;
        $this->clientViewOptions['animateLoading'] = $this->animateLoading;
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $this->renderBeforeForm();
        $form = ActiveForm::begin([
            'action' => $this->action,
            'options' => $this->formOptions,
            'enableAjaxValidation' => $this->enableAjaxValidation,
            'enableClientValidation' => $this->enableClientValidation,
            'afterRedirect' => $this->afterRedirect,
            'redirectUrl' => $this->redirectUrl,
            'validationUrl'=> $this->validationUrl,
            'method' => $this->method,
            'validateOnBlur' => $this->validateOnBlur,
            'validateOnChange' => $this->validateOnChange
        ]);

        $this->renderInsideBeforeForm();
        $this->renderForm($this->model, $form);
        $this->renderInsideAfterForm();
        ActiveForm::end();
        $this->renderAfterForm();
    }

    /**
     * Выводить что-то перед выводом формы поиска
     * @return void
     */
    public function renderBeforeForm()
    {
        if (!$this->collapsible) {
            return;
        }
        Panel::begin(array_merge($this->collapsePanelDefaultOptions, $this->collapsePanelOptions, [
            'collapsible' => $this->collapsible,
        ]));
    }

    /**
     * Выводить что-то после вывода формы поиска
     * @return void
     */
    public function renderAfterForm()
    {
        if (!$this->collapsible) {
            return;
        }
        Panel::end();
    }

    /**
     * Выводить что-то внутри формы перед началом её рендера
     * @return void
     */
    public function renderInsideBeforeForm()
    {

    }

    /**
     * Выводить что-то внутри формы после началом её рендера
     * @return void
     */
    public function renderInsideAfterForm()
    {

    }
}
