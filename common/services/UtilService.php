<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/8
 * Time: 11:25
 */

namespace app\common\services;

use yii\helpers\Html;
class UtilService
{
    /*
     * 获取客服端的ip
     * 获取ip,但当服务器做了反向代理与正向代理时则获取不正确 $_SERVER['REMOTE_ADDR'];
     * 如果设置了反向代理则用$_SERVER['HTTP_X_FORWARDED_FOR'];获取
     */
    public static function getIP()
    {
        if (!empty($_SERVER["HTTP_X_FORWARDED_FOR"]))
            return $_SERVER["HTTP_X_FORWARDED_FOR"];
        return isset($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"]:'';
    }
    /*
     * yii防止xss攻击
     */
    public static function encode($display)
    {
        return Html::encode($display);
    }
    //获取项目根路径
    public static function getRootPath(){
        $vendor_path = \Yii::$app->vendorPath; //表示的是 vendor文件夹的绝对路径
        return dirname($vendor_path);
    }
    public static  function isWechat(){
        $ug= isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:'';
        if( stripos($ug,'micromessenger') !== false ){
            return true;
        }
        return false;
    }
}