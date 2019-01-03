<?php
namespace app\assets;

use app\common\web\AssetBundle;

/**
 * Class AppAsset
 * @deprecated use webpack
 */
class AppAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@app/resources';
    /**
     * @var array
     */
    public $js = [
        'js/core/Component.js',
        'js/core/Request.js',
        'js/core/HtmlHelper.js',
        'js/core/Module.js',
        'js/core/WidgetLoader.js',
        'js/core/MessageFactory.js',
        'js/core/View.js',
        'js/core/Block.js',
        'js/core/DynamicModal.js',
        'js/core/DynamicPopover.js',
        'js/core/ConfirmModal.js',
        'js/core/CardView.js',
        'js/core/GridView.js',
        'js/core/TabView.js',
        'js/core/RelatedWidget.js',
        'js/core/ListView.js',
        'js/core/FormWidget.js',
        'js/core/SearchWidget.js',
        'js/core/Application.js',
        'js/core/Panel.js',
        'js/core/Chart.js',
        'js/plugins/jquery.loading.js',
        'js/app.js',
    ];
    /**
     * @var array
     */
    public $css = [
        'css/core/main.css',
    ];
    /**
     * @var array
     */
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\BootstrapPluginAsset',
        'app\assets\BackboneAsset',
        'yii\jui\JuiAsset',
        'kartik\datetime\DateTimePickerAsset',
        'kartik\date\DatePickerAsset',
    ];
}
