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
class MAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public function registerAssetFiles($view)
    {
        $VERSION=defined("VERSION")?VERSION:time();  //defined — 检查某个名称的常量是否存在
       $this->css = [
        'font-awesome/css/font-awesome.css',
        'css/m/css_style.css',
        'css/m/app.css?ver='.$VERSION
        ];
        $this->js = [
            'https://res.wx.qq.com/open/js/jweixin-1.2.0.js', //引入微信js接口文件 用电脑分享可以，手机不行
            'plugins/jquery-2.1.1.js',
            'js/m/TouchSlide.1.1.js',
            'js/m/common.js?ver='.$VERSION,
            'js/m/weixin.js?ver='.$VERSION //引入微信js接口文件
        ];
        parent::registerAssetFiles($view);  //采用覆盖基类的方式
    }
    // public $depends = [
    //     'yii\web\YiiAsset',
    //     'yii\bootstrap\BootstrapAsset',
    // ];
}
