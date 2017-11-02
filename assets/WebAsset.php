<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class WebAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public function registerAssetFiles($view)
    {
        $VERSION=defined("VERSION")?VERSION:time();  //defined — 检查某个名称的常量是否存在
       $this->css = [
        'css/web/bootstrap.min.css',
        'font-awesome/css/font-awesome.css',
        'css/web/style.css?ver='.$VERSION
        ];
        $this->js = [
            'plugins/jquery-2.1.1.js',
            'js/web/bootstrap.min.js',
            'plugins/layer/layer.js',
            'js/web/common.js?ver='.$VERSION
        ];
        parent::registerAssetFiles($view);  //采用覆盖基类的方式
    }
    // public $depends = [
    //     'yii\web\YiiAsset',
    //     'yii\bootstrap\BootstrapAsset',
    // ];
}
