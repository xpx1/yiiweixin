<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/19
 * Time: 15:38
 */

namespace app\common\services;


class BaseService
{
    //封装基础服务用来规范返回信息
    protected static $_err_msg = null;
    protected static $_err_code = null;
    public static function _err($msg='',$code=-1)
    {
        self::$_err_msg=$msg;
        self::$_err_code=$code;
        return false;
    }
    public static function getLastErrorMsg()
    {
        self::$_err_msg;
    }
    public static function getLastErrorCode()
    {
        self::$_err_code;
    }
}