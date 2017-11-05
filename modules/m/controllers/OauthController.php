<?php
namespace app\modules\m\controllers;


use app\common\components\HttpClient;
use app\common\services\BaseService;
use app\common\services\UrlService;
use app\modules\m\controllers\common\BaseController;
use app\common\services\ConstantMapService;
use app\models\member\Member;
use app\models\member\Oauth_member_bind;
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
        $user=@json_decode(HttpClient::get($url),true);
        $token=$user['access_token'];
        $openid=$user['openid'];
        $scope=$user['scope'];
        $this->setCookie( $this->auth_cookie_current_openid,$openid );
        $reg_bind = Oauth_member_bind::find()->where([ 'openid' => $openid,'type' => ConstantMapService::$client_type_wechat ])->one();
        if( $reg_bind )
        {
            $member_info = Member::findOne(['id' => $reg_bind['member_id'], 'status' => 1]);
            if (!$member_info) {
                $reg_bind->delete();
                return $this->goHome();
            }

        if($scope=='snsapi_userinfo')
        {
         $userurl="https://api.weixin.qq.com/sns/userinfo?access_token={$token}&openid={$openid}&lang=zh_CN";
            $wechat_user_info = HttpClient::get( $userurl );
            $wechat_user_info = @json_decode( $wechat_user_info,true );
            if( $member_info['nickname'] == $member_info['mobile'] )
            {
                $member_info->nickname = isset( $wechat_user_info['nickname'] )?$wechat_user_info['nickname']:$member_info->nickname;
                $member_info->update( 0 );
                return $this->redirect( UrlService::buildMUrl( "/product/index" ) );
            }
            //设置登录态
            $this->setLoginStatus($member_info);
        }
        }
        return $this->redirect( UrlService::buildMUrl( "/default/index" ) );


    }
    /*
     * 退出操作
     */
    public function actionLoginOut()
    {
        $this->removeLoginStatus();//移除登录态
        $this->removeCookie($this->auth_cookie_current_openid);//移除cookie里的openID
    }
}