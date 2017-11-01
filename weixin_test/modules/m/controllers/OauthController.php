<?php
namespace app\modules\m\controllers;


use app\common\components\HttpClient;
use app\common\services\BaseService;
use app\common\services\UrlService;
use app\modules\m\controllers\common\BaseController;
use Yii;
/*
 * 该类用于网页授权
 */
class OauthController extends BaseController
{
    /*
     * 授权登录
     */
    public function actionLogin()
    {
        $appid=Yii::$app->params['weixin']['appid'];
        $scope=$this->get('scope','snsapi_base');
        $redirect_uri=urlencode(UrlService::buildMUrl('/oauth/callback'));
        $url="https://open.weixin.qq.com/connect/oauth2/authorize?appid={$appid}&redirect_uri={$redirect_uri}&response_type=code&scope={$scope}&state=#wechat_redirect";//state的参数不是必需的所以可以设为空
        return $this->redirect( $url );
    }
    /*
     * 登录之后的跳转页面
     */
    public function actionCallback()
    {
        //获取code参数用于换取网页授权access_token
        $code=$this->get('code');
        $appid=Yii::$app->params['weixin']['appid'];
        $secret=Yii::$app->params['weixin']['sk'];
        $url="https://api.weixin.qq.com/sns/oauth2/access_token?appid={$appid}&secret={$secret}&code={$code}&grant_type=authorization_code";
        $user=json_decode(HttpClient::get($url),true);
        $token=$user['access_token'];
        $openid=$user['openid'];
        $scope=$this->get('scope');
        $this->setCookie( $this->auth_cookie_current_openid,$openid );
        if($scope=='snsapi_userinfo')
        {
         $userurl="https://api.weixin.qq.com/sns/userinfo?access_token={$token}&openid={$openid}&lang=zh_CN";
//         var_dump(json_decode(HttpClient::get($userurl),true));
            $wechat_user_info = HttpClient::get( $userurl );
        }
        return $this->redirect( UrlService::buildMUrl( "/default/index" ) );

    }
}