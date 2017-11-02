<?php
namespace app\common\services\weixin;
use app\common\components\HttpClient;
use app\models\member\Oauth_access_token;
use \app\common\services\BaseService;

class RequestService extends  BaseService {
	private  static $app_token = "";
	private  static $appid = "";
	private  static $app_secret = "";

	private static $url = "https://api.weixin.qq.com/cgi-bin/"; //设置请求地址的相同部分
    /*
     * 因为access_token在调取接口时会经常用到所以可以将获取access_token封装起来方便调用
     */
	//获取access_token
	public static function getAccessToken(){
		$date_now = date("Y-m-d H:i:s");
		$access_token_info = Oauth_access_token::find()->where([ '>','expired_time' , $date_now ])->limit(1)->one();
		if( $access_token_info ){
			return $access_token_info['access_token']; //如果数据库中有access_token且数据库中的过期时间大于当前时间，则说明access_token有效，则返回access_token
		}

		$path = 'token?grant_type=client_credential&appid='.self::getAppId().'&secret='.self::getAppSecret(); //拼上请求的路径
		$res = self::send($path);
		if( !$res ){
			return self::_err( self::getLastErrorMsg() ); //返回错误信息
		}

		$model_access_token = new Oauth_access_token();
		$model_access_token->access_token = $res['access_token'];
		$model_access_token->expired_time = date("Y-m-d H:i:s",$res['expires_in'] + time() - 200 ); //当前时间加上7200s-200s
		$model_access_token->created_time = $date_now;
		$model_access_token->save( 0 );
		return $res['access_token'];
	}

	public static function send($path,$data=[],$method = "GET"){

		$request_url = self::$url.$path;

		if( $method == "POST"){
			$res = HttpClient::post($request_url,$data);
		}else{
			$res = HttpClient::get($request_url,[]);
		}

		$ret = @json_decode( $res,true );
		if( !$ret || ( isset( $res['errcode'] ) && $res['errcode'] )  ){
			return self::_err( $res['errmsg'] );
		}
		return $ret;
	}

	public static function setConfig($appid ,$app_token,$app_secret){
		self::$appid = $appid; //设置appid 所以不需return appid可以存储在params.php中
		self::$app_token = $app_token;
		self::$app_secret = $app_secret;
	}

	public static function getAppId(){
		return self::$appid;
	}

	public static function getAppSecret(){
		return self::$app_secret;
	}

	public static function getAppToken(){
		return self::$app_token;
	}
}