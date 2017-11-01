<?php

namespace app\controllers;

use app\common\components\BaseWebController;
use app\common\services\captcha\ValidateCode;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use \app\models\sms\SmsCaptcha;
use app\common\services\UtilService;
use app\common\services\ConstantMapService;
class DefaultController extends BaseWebController
{
    public $layout = 'main';

    public function actionIndex()
    {
        return $this->render('index');
    }

    //验证码cookie名
    private $captcha_cookie_name = "validate_code";

    /*
     * 生成图片验证码
     */
    public function actionImg_captcha()
    {
        $font = Yii::$app->getBasePath() . '/web/fonts/captcha.ttf';
        $captcha = new ValidateCode($font);
        $captcha->doimg();
        $this->setCookie( $this->captcha_cookie_name,$captcha->getCode() );
    }
    /*
     * 验证图片验证码
     */
    public function actionGet_captcha()
    {
        $mobile = $this->post("mobile", "");
        $img_captcha = $this->post("img_captcha", "");;
        if (!$mobile || !preg_match('/^1[0-9]{10}$/', $mobile)) {
            $this->removeCookie($this->captcha_cookie_name);
            return $this->renderJson([], "请输入符合要求的手机号码~~", -1);
        }

        $captcha_code = $this->getCookie($this->captcha_cookie_name);
        if (strtolower($img_captcha) != $captcha_code) {
            $this->removeCookie($this->captcha_cookie_name);
            return $this->renderJson([], "请输入正确图形校验码\r\n你输入的图形验证码是{$img_captcha},正确的是{$captcha_code}~~", -1);
        }
        //发送手机验证码，能发验证码，能验证

        $model_sms = new SmsCaptcha();
        $model_sms->geneCustomCaptcha( $mobile ,UtilService::getIP() );
//        var_dump($model_sms);
        $this->removeCookie( $this->captcha_cookie_name );
        if( $model_sms ){
//            echo 1232432435465767;
            return $this->renderJson( [],"发送成功~~，手机验证码是".$model_sms->captcha );
        }

        return $this->renderJson( [],ConstantMapService::$default_syserror,-1 );
    }
}
