<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/2
 * Time: 16:52
 */

namespace app\common\services;
use yii\helpers\Url;
use Yii;
//管理链接服务
class UrlService
{
    /**
     * 构建web端的链接
     * static function 静态方法不需要初始化即可使用
     * @param string $path 路径
     * @param array $params 参数
     * @return string $path 路径
     */
    public static function buildWebUrl( $path,$params=[] )
    {
        $domain_config=Yii::$app->params['domain'];
        $path = Url::toRoute( array_merge( [ $path ],$params ) );
        return $domain_config['web']. $path;   //从配置文件中取出路径
    }
    /*
     * 构建会员端的链接
     */
    public static function buildMUrl( $path,$params=[] )
    {
        $domain_config=Yii::$app->params['domain'];
        $path = Url::toRoute( array_merge( [ $path ],$params ) );
        return $domain_config['m']. $path;
    }
    /*
     * 构建官网的链接
     */
    public static function buildWwwUrl( $path,$params=[] )
    {
        $domain_config=Yii::$app->params['domain'];
        $path = Url::toRoute( array_merge( [ $path ],$params ) );
        return $domain_config['www'].$path;
    }
    /*
     * 构建空链接，可用于禁止a标签的跳转
     */
    public static function buildNullUrl()
    {
        return "javascript:void(0);";
    }
    /*
     * 图片路径的处理
     */
    public static function buildPicUrl($bucket,$file_key)
    {
        $domain_config=Yii::$app->params['domain'];//获取域名
        $upload_config=Yii::$app->params['upload'];//获取篮子路径(即放置图片的文件夹的路径)
        return $domain_config['www'].$upload_config[$bucket].'/'.$file_key;//返回图片的路径
    }
}