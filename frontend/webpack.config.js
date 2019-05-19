/**
 * @copyright 2012-2019 Medkey
 */

const path = require('path');
const fs = require('fs');
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const MergeIntoSingleFilePlugin = require('webpack-merge-and-include-globally');
const UglifyJsPlugin = require('uglifyjs-webpack-plugin');
const vendorPath = '../vendor';
const appPath = '../';
const assetPath = '../common/assets';
const webRoot = path.join(__dirname, "../web/bundles/");
const uglifyJS = require("uglify-js");

let entryPaths = {};
let buffer = fs.readdirSync(__dirname + '/modules');
for (let i = 0; i < buffer.length; i++) {
    let p = '/modules/' + buffer[i] + '/entry.js';
    if (fs.existsSync(__dirname + p)) {
        entryPaths['bundle_' + buffer[i]] = path.resolve(__dirname, './modules/' + buffer[i] + '/entry.js');
    }
}
entryPaths['bundle'] = path.resolve(__dirname, './entry.js');
module.exports = {
    mode: process.argv.mode,
    // devtool: "source-map",
    entry: entryPaths,
    output: {
        filename: '[name].js',
        path: webRoot
    },
    optimization: {
        minimizer: [
            new UglifyJsPlugin()
        ]
    },
    module: {
        rules: [
            {
                test: /\.js|jsx$/,
                exclude: /(node_modules|bower_components)/,
                use: {
                    loader: 'babel-loader',
                    options: {
                        presets: ['env', 'react'],
                        plugins: ['transform-class-properties']
                    }
                }
            },
            {
                test: /\.css$/,
                use: [
                    "style-loader",
                    MiniCssExtractPlugin.loader,
                    "css-loader"
                ]
            },
            {
                test: /\.(jpg|jpeg|png|gif|woff|woff2|eot|ttf|svg)$/,
                use: [
                    {
                        loader: 'url-loader',
                        options: {
                            limit: 100000
                        }
                    }
                ]
            }
        ]
    },
    plugins: [
        new MiniCssExtractPlugin({
            filename: "style.[name].css",
        }),
        // for legacy JS
        new MergeIntoSingleFilePlugin({
            files: {
                "app-bundle.js" : [
                    // bower/npm
                    // todo Заменить на NPM frontend !!! replace composer.json в приложении и подключение import этих ресурсов во фронте
                    vendorPath + '/bower-asset/jquery/dist/jquery.js',
                    vendorPath + '/bower-asset/jquery-ui/jquery-ui.min.js',
                    vendorPath + '/bower-asset/bootstrap/dist/js/bootstrap.min.js',
                    vendorPath + '/bower-asset/inputmask/dist/jquery.inputmask.bundle.js',
                    vendorPath + '/npm-asset/underscore/underscore-min.js',
                    vendorPath + '/npm-asset/backbone/backbone-min.js',

                    // yii2 framework
                    vendorPath + '/yiisoft/yii2/assets/yii.js',
                    vendorPath + '/yiisoft/yii2/assets/yii.activeForm.js',
                    vendorPath + '/yiisoft/yii2/assets/yii.validation.js',

                    // kartik widgets
                    vendorPath + '/kartik-v/yii2-krajee-base/src/assets/js/kv-widgets.js',
                    vendorPath + '/kartik-v/yii2-widget-datetimepicker/assets/js/bootstrap-datetimepicker.js',
                    vendorPath + '/kartik-v/yii2-widget-datepicker/assets/js/bootstrap-datepicker.js',
                    vendorPath + '/kartik-v/yii2-widget-datepicker/assets/js/datepicker-kv.js',
                    vendorPath + '/kartik-v/yii2-widget-datepicker/assets/js/locales/bootstrap-datepicker.ru.min.js',
                    vendorPath + '/kartik-v/yii2-widget-datetimepicker/assets/js/locales/bootstrap-datetimepicker.ru.js',
                    vendorPath + '/kartik-v/yii2-widget-select2/assets/js/select2.full.js',
                    vendorPath + '/kartik-v/yii2-widget-select2/assets/js/select2-krajee.js',
                    vendorPath + '/kartik-v/yii2-widget-select2/assets/js/i18n/ru.js',
                    vendorPath + '/kartik-v/yii2-widget-timepicker/assets/js/bootstrap-timepicker.min.js',

                    // flot
                    vendorPath + '/machour/yii2-jquery-flot/assets/jquery.flot.js',

                    // application
                    assetPath + '/js/core/Component.js',
                    assetPath + '/js/core/Request.js',
                    assetPath + '/js/core/HtmlHelper.js',
                    assetPath + '/js/core/Module.js',
                    assetPath + '/js/core/WidgetLoader.js',
                    assetPath + '/js/core/MessageFactory.js',
                    assetPath + '/js/core/View.js',
                    assetPath + '/js/core/Block.js',
                    assetPath + '/js/core/DynamicModal.js',
                    assetPath + '/js/core/DynamicPopover.js',
                    assetPath + '/js/core/ConfirmModal.js',
                    assetPath + '/js/core/CardView.js',
                    assetPath + '/js/core/GridView.js',
                    assetPath + '/js/core/TabView.js',
                    assetPath + '/js/core/RelatedWidget.js',
                    assetPath + '/js/core/ListView.js',
                    assetPath + '/js/core/FormWidget.js',
                    assetPath + '/js/core/SearchWidget.js',
                    assetPath + '/js/core/Application.js',
                    assetPath + '/js/core/Panel.js',
                    assetPath + '/js/core/Chart.js',
                    assetPath + '/js/plugins/jquery.loading.js',
                    assetPath + '/js/app.js',

                    // modules
                    appPath + 'modules/config/resources/js/directory.js',
                    appPath + 'modules/config/resources/js/workflow.js',
                    appPath + 'modules/crm/resources/js/order.js',
                    appPath + 'modules/medical/resources/js/medical.js',
                    appPath + 'modules/organization/resources/js/organization.js',
                    appPath + 'modules/security/resources/js/security.js',
                    appPath + 'modules/workplan/resources/js/workplan.js'

                ]
            },
            // transform: {
            //     'app-bundle.js': code => uglifyJS.minify(code).code
            // }
        })
    ]
};