<?php
namespace app\common\widgets;

use app\common\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * Class WidgetLoader
 * @package Common\Widgets
 * @copyright 2012-2019 Medkey
 *
 */
class WidgetLoader extends Widget
{
    /**
     * @var bool
     */
    public $isDynamicModel = true;
    /**
     * @var string
     */
    public $queryParams;
    /**
     * @var array
     */
    public $options = [];
    /**
     * @var array
     */
    public $clientOptions = [];
    /**
     * @var string
     */
    public $insertBlockId;


    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->clientView = false;
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $id = $this->options['id'];
        $clientOptions = Json::encode(ArrayHelper::intToStringRecursive($this->clientOptions), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_FORCE_OBJECT | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS);
        $isDynamicModel = Json::encode($this->isDynamicModel, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_FORCE_OBJECT | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS);
        $queryParams = Json::encode($this->queryParams, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_FORCE_OBJECT | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS);
        $insertBlockId = Json::encode($this->insertBlockId, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_FORCE_OBJECT | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS);
        \Yii::$app->view->registerJs(<<<JS
$('#{$id}').on('click', function (e) {
    var target = $(e.currentTarget);
    if (target.data('loader') === false) {
        return false;
    }
    var queryParams = {$queryParams};
    var clientOptions = {$clientOptions};
    var isDynamicModel = {$isDynamicModel};
    var insertBlockId = {$insertBlockId};
    if (isDynamicModel) {
         var model = target.data('dynamicModel');
        if (!clientOptions['config']['model'] && model) {
            clientOptions['config']['model'] = model;
        }
    }
    target.loading('loadingIcon');
    if (insertBlockId) {
        var elem = $('#' + insertBlockId);
        if (elem.length) {
            elem.loading('start');
        }
     }
    application.getComponent('widgetLoader').load(clientOptions, queryParams)
        .done(function (response) {
            if (!response.html) {
                throw new Error('Response does not contain client content for insertion.');
            }
            var content = application.getComponent('htmlHelper').movementResources(response.html);
            if (insertBlockId) {
                var elem = $('#' + insertBlockId);
                $('#' + insertBlockId).html(content);
                elem.loading('stop');
            } else {
                $('body').append(content);
            }
        }).always(function () {
            target.loading('stopIcon');
        }).fail(function () {
            console.log('fail loader');
        });
});
JS
        );
    }
}
