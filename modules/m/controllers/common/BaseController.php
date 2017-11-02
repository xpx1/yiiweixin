<?php
namespace app\modules\m\controllers\common;


use app\common\components\BaseWebController;

class BaseController extends BaseWebController
{
    protected $auth_cookie_current_openid = "shop_m_openid";
    /*
     * 指定基类中的公共布局文件
     */
    public function __construct($id,$module,array $config=[])
    {
        parent::__construct($id,$module,$config);
        $this->layout='main';
    }
    /*
     * 设置方法拦截器(可用来做登录验证)
     */
    public function beforeAction($action)
    {
        return true;
    }
}