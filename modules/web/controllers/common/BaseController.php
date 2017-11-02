<?php
namespace app\modules\web\controllers\common;


use app\common\components\BaseWebController;
use app\common\services\UrlService;
use app\models\User;

//1.指定特定的布局文件
//2.进行统一登录验证
class BaseController extends BaseWebController
{
    protected $page_size = 50;//定义总页数
    public $user_info=null;
    //定义cookie的名字
    protected $auth_token_name='mooc';
    //指定不需验证登录态的方法
    //静态变量用self引用其他的用$this
    public static $allowAction=[
        'web/user/login'
    ];
    public $layout='main';
    /*
     * 登录统一验证
     */
    public function beforeAction($action)
    {
        /*
         * 布尔类型（boolean）：只有两个值，一个是TRUE，另一个FALSE，
         * 可以理解为是或否。它不区分大小写，也就是说”TRUE”和“true”效果是一样的。
         * 主要用在条件结构（条件结构在后面部分会介绍）中，
         * 例如判断是否满足条件的时候，是用“true”表示满足，用“false”表示不满足
         */
        $is_login=$this->checkLoginStatus();
        //如果返回值为false;则跳转到登录页面
        // in_array($action->getUniqueId(),self::$allowAction)
        //不需验证登录态的方法在该$action->getUniqueId()数组里
        if(in_array($action->getUniqueId(),self::$allowAction))
        {
            return true;
        }
        if(!$is_login){
            if(\Yii::$app->request->isAjax)
            {
                $this->renderJson([],"未登录，请先登录","-302");
            }else{
                $this->redirect(UrlService::buildWebUrl("/user/login"));
            }
            return false; //作为条件结构的判断
        }
        return true;
    }
    /*
     * 验证登录状态
     */
    private function checkLoginStatus()
    {
        $auth_cookie=$this->getCookie($this->auth_token_name);
        if(!$auth_cookie)
        {
            return false;
        }
        //将cookie值分割成 含auth_token,uid的数组并将该值赋给$auth_token,$uid变量
        list($auth_token,$uid)=explode('#',$auth_cookie);
        if(!$auth_token||!$uid)
        {
            return false;
        }
        //验证uid是否是数字
        if(!preg_match("/^\d+$/",$uid))
        {
            return false;
        }
        //根据uid查询用户信息是否存在
        $userinfo=User::find()->where(['uid'=>$uid])->one();
        if(!$userinfo)
        {
            return false;
        }
        //验证加密字符串是否正确，预防cookie被篡改 !=:不等于
        $auth_token_md5=$this->geneAuth_token($userinfo);
        if($auth_token!=$auth_token_md5)
        {
            return false;
        }
        $this->user_info=$userinfo;
        return true;
    }
    /*
     * 设置统一登录态
     */
    public function setLoginStatus($userinfo)
    {
         $this->setCookie($this->auth_token_name,$this->geneAuth_token($userinfo)."#".$userinfo['uid']);
    }
    /*
     * 移除统一登录态
     */
    public function removeLoginStatus()
    {
        $this->removeCookie($this->auth_token_name);
    }
    /*
     * 生成统一加密字段
     * 加密字符串=md5(用户成名+密码+密码加密秘钥)
     */
    public function geneAuth_token($userinfo)
    {
        return md5($userinfo['login_name'].$userinfo['login_pwd'].$userinfo['login_salt']);
    }
}