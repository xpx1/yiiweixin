<?php
namespace app\common\services\applog;


use app\common\services\UtilService;
use app\models\log\AppLog;

class AppLogService
{
    /*
     * 记录错误日志
     */
    public function addErrorLog($appname,$content)
    {
        //yii自带获取错误的方法
       $error = \Yii::$app->errorHandler->exception;
        // 插入新客户的记录
        $model_app_logs = new AppLog();
        $model_app_logs->app_name = $appname;
        $model_app_logs->content = $content;
        $model_app_logs->ip=UtilService::getIP();

        if( !empty($_SERVER['HTTP_USER_AGENT']) ) {
            $model_app_logs ->ua = "[UA:{$_SERVER['HTTP_USER_AGENT']}]";
        }

        if ($error) {

            if(method_exists($error,'getName' )) {
                $model_app_logs->err_name = $error->getName();
            }

            if (isset($error->statusCode)) {
                $model_app_logs->http_code = $error->statusCode;
            }

            $model_app_logs->err_code = $error->getCode();
        }

        $model_app_logs->created_time = date("Y-m-d H:i:s");
        $model_app_logs->save(0);
    }

}