<?php
namespace app\common\components;
use yii\web\Controller;
use Yii;
use yii\web\HttpException;
/**
 * 集成常用公用方法，提供给所有Controller使用
 * get，post，setCookie，getCookie，removeCookie，renderJson
 */

class BaseWebController extends Controller
{
    public $enableCsrfValidation = false;//关闭Csrf
    /*
     * 获取http的get参数
     */
    public function get( $key,$default_val="" )
    {
        return \Yii::$app->request->get($key,$default_val);//yii自带获取get的方法
    }
    /*
     * 获取http的post参数
     */
    public function post( $key,$default_val="" )
    {
        return \Yii::$app->request->post($key,$default_val);//yii自带获取get的方法
    }
    /*
     * 设置cookie值
     * //过期时间设置为0则永不过期
     */
    public function setCookie( $name,$value,$expire=0 )
    {
        $cookies = \Yii::$app->response->cookies;//yii自带设置cookie的方法
        $cookies->add(new \yii\web\Cookie([
            'name' =>  $name,
            'value' => $value,
            'expire'=> $expire
        ]));
    }
    /*
     * 获取cookie值
     */
    public function getCookie($name,$default_value='')
    {
        $cookies = \Yii::$app->request->cookies;//yii自带获取cookie的方法
        return $cookies->getValue($name,$default_value);//获取cookie的值
    }
    /*
     * 删除cookie值
     */
    public function removeCookie( $name )
    {
        $cookies=\Yii::$app->response->cookies;
        $cookies->remove($name);
    }
    /**
     * [renderJson 统一返回json的方法]
     * @return [type] [$data:json数据、$msg:json信息、$code:状态码,req_id:返回的id]
     */
    public function renderJson($data=[],$msg="ok",$code=200)
    {
        header('Content-type: application/json');
       echo json_encode([
        "code"=>$code,
        "msg"=>$msg,
        "data"=>$data,
        "req_id"=>uniqid()
        ]);
    }
    /*
     * 统一js跳转设置
     * $msg：提示信息 $url：跳转地址
     */
    public function redenJs($msg,$url)
    {
        return $this->renderPartial("@app/views/common/js",['msg'=>$msg,'url'=>$url]);
    }
}