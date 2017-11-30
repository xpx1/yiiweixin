<?php
namespace app\modules\weixin\controllers;

use app\common\components\BaseWebController;
use app\common\services\weixin\RequestService;

class JssdkController extends BaseWebController{
    public function actionIndex()
    {
        $noncestr=$this->createNoncestr();
        $jsapi_ticket = $this->getJssdk();
        $timestamp=time();
        $url=$this->get('url'); //要分享的url 从前端js传过来
        //使用URL键值对的格式（即key1=value1&key2=value2…）拼接成字符串string1。
        $string="jsapi_ticket={$jsapi_ticket}&noncestr={$noncestr}&timestamp={$timestamp}&url={$url}";
        $signature = sha1( $string ); //生成散列值
        $config = \Yii::$app->params['weixin'];
        $data = [
            'appId' => $config['appid'],
            'timestamp' => $timestamp,
            'nonceStr' => $noncestr,
            'signature' => $signature,
            'string' => $string
        ];
        return $this->renderJson($data );//将调取jsdk接口所需数据返回给前端js并以json形式返回
    }
    /*
     * 生成随机字符串即nocestr
     */
    private function createNoncestr( $length = 16 ){
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = '';
        for( $i =0 ;$i < $length ;$i++){
            $str .= substr( $chars,mt_rand( 0,strlen( $chars ) - 1 ),1 );
        }
        // substr截取字符串 参数：[字符串 位置 截取的长度] ，mt_rand随机数 ，strlen计算字符串长度 因为从下标为0开始所以减1
        return $str;
    }
    private function getJssdk()
    {
        $cache_key = "wx_jsticket";
        $cache = \Yii::$app->cache;
        $ticket = $cache->get( $cache_key ); //yii的自带数据缓存

        if(!$ticket)
        {
            $config = \Yii::$app->params['weixin'];
            RequestService::setConfig( $config['appid'],$config['token'],$config['sk'] );
            $access_token=RequestService::getAccessToken();
            $res=RequestService::send("ticket/getticket?access_token={$access_token}&type=jsapi"); //请求微信接口
            if( isset( $res['errcode'] ) && $res['errcode'] == 0  )
            {
                $cache->set( $cache_key,$res['ticket'],$res['expires_in'] - 200 ); //缓存ticket
                $ticket = $res['ticket'];
            }
        }
        return $ticket;
    }
}