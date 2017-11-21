<?php

namespace app\modules\m\controllers;

use app\modules\m\controllers\common\BaseController;
use yii\web\Controller;
use Yii;
use \app\models\member\Member;
use \app\models\sms\SmsCaptcha;
use app\common\services\ConstantMapService;
use app\common\services\UtilService;
use app\common\services\UrlService;
use app\models\member\Oauth_member_bind;
class UserController extends BaseController
{	
	/*
        指定公共布局
     */
	public $layout=false;
	/*
		账号绑定页面
	 */
    public function actionBind()
    {
        $this->layout = 'main';
        if (Yii::$app->request->isGet) {
            return $this->render('bind');
        }
        $mobile = trim($this->post("mobile"));
        $img_captcha = trim($this->post("img_captcha"));
        $captcha_code = trim($this->post("captcha_code"));
        $openid = $this->getCookie($this->auth_cookie_current_openid);
        $date_now = date("Y-m-d H:i:s");
        if (mb_strlen($mobile, "utf-8") < 1 || !preg_match("/^[1-9]\d{10}$/", $mobile)) {
            return $this->renderJSON([], "请输入符合要求的手机号码~~", -1);
        }

        if (mb_strlen($img_captcha, "utf-8") < 1) {
            return $this->renderJSON([], "请输入符合要求的图像校验码~~", -1);
        }

        if (mb_strlen($captcha_code, "utf-8") < 1) {
            return $this->renderJSON([], "请输入符合要求的手机验证码~~", -1);
        }


        if (!SmsCaptcha::checkCaptcha($mobile, $captcha_code)) {
            return $this->renderJSON([], "请输入正确的手机验证码~~", -1);
        }

        $member_info = Member::find()->where(['mobile' => $mobile, 'status' => 1])->one();//member表里有该字段就直接登录跳转，没有就插入一条数据

        if ($member_info&&$member_info['status']==0) {
            return $this->renderJSON([], "您的账号已被禁止，请联系客服解决~~", -1);
        }

        if (!$member_info) {
            if (Member::findOne(['mobile' => $mobile])) {
               return $this->renderJSON([], "手机号码已注册，请直接使用手机号码登录~~", -1);
            }

            $model_member = new Member();
            $model_member->nickname = $mobile;
            $model_member->mobile = $mobile;
            $model_member->setSalt();
            $model_member->avatar = ConstantMapService::$default_avatar;
            $model_member->reg_ip = sprintf("%u", ip2long(UtilService::getIP()));
            $model_member->status = 1;
            $model_member->created_time = $model_member->updated_time = date("Y-m-d H:i:s");
            $model_member->save(0);
            $member_info = $model_member;
        }
        if (!$member_info||!$member_info['status']) {
            return $this->renderJSON([], "您的账号已被禁止，请联系客服解决~~", -1);
        }
        if ($openid)
        {
            $bind_info = Oauth_member_bind::find()->where([ 'member_id' => $member_info['id'],'openid' => $openid,'type' => ConstantMapService::$client_type_wechat  ])->one();
            if (!$bind_info)
            {
                $model_bind=new Oauth_member_bind();
                $model_bind->member_id=$member_info['id'];
                $model_bind->client_type='weixin';
                $model_bind->type=ConstantMapService::$client_type_wechat;
                $model_bind->openid=$openid;
                $model_bind->unionid='';//这里没有只有当绑定多个公众号时才有，保存方法与openid一样
                $model_bind->extra='';
                $model_bind->updated_time=$date_now;
                $model_bind->created_time=$date_now;
                $model_bind->save(0);
            }
        }
        if( UtilService::isWechat() && $member_info['nickname']  == $member_info['mobile'] ){
           return $this->renderJSON([ 'url' => UrlService::buildMUrl( "/oauth/login",[ 'scope' => 'snsapi_userinfo' ] )  ],"绑定成功~~");
        }
        //todo设置登录态
        $this->setLoginStatus( $member_info );  //即保存cookie状态
        return $this->renderJSON([ 'url' => UrlService::buildMUrl( "/default/index" )  ],"绑定成功~~");

    }
    /*
        购物车
     */
    public function actionCart()
    {
        $this->layout=false;
        return $this->render('cart');
    }
    /*
        我的订单
     */
    public function actionOrder()
    {
        $this->layout='main';
        return $this->render('order');
    }
    /*
        我的页面
     */
    public function actionIndex()
    {
        $this->layout='main';
        return $this->render('index');
    }
    /*
        我的地址列表
     */
    public function actionAddress()
    {
        $this->layout='main';
        return $this->render('address');
    }
    /*
        收货地址的添加或编辑
     */
    public function actionAddress_set()
    {
        $this->layout='main';
        return $this->render('address_set');
    }
    /*
        我的收藏
     */
    public function actionFav()
    {
        $this->layout='main';
        return $this->render('fav');
    }
    /*
        我的评论列表
     */
    public function actionComment()
    {
        $this->layout='main';
        return $this->render('comment');
    }
    /*
        评论打分
     */
    public function actionComment_set()
    {
        $this->layout='main';
        return $this->render('comment_set');
    }
}
