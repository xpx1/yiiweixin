<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/23
 * Time: 16:31
 */

namespace app\modules\weixin\controllers;


use app\common\components\BaseWebController;

class MsgController extends BaseWebController
{
    public function actionIndex( )
    {
        if (!$this->checkSignature()) {

            return 'error signature ~~';
        }

        if (array_key_exists('echostr', $_GET) && $_GET['echostr']) {//用于微信第一次认证的（因为当微信通过后就不会再发这信息了）
            return $_GET['echostr'];
        }
    }
    //验证基本配置（别的方法也能调用该方法所以这里设为public）
    public function checkSignature(){
        $signature = trim( $this->get("signature","") );
        $timestamp = trim( $this->get("timestamp","") );
        $nonce = trim( $this->get("nonce","") );
        $tmpArr = array( \Yii::$app->params['weixin']['token'],$timestamp,$nonce );
        sort( $tmpArr,SORT_STRING );
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );
        if( $tmpStr ==  $signature ){
            return true;
        }else{
            return false;
        }
    }

}