<?php

namespace app\modules\web\controllers;

use app\common\components\BaseWebController;
use app\common\services\UrlService;
use app\modules\web\controllers\common\BaseController;
use yii\web\Controller;
use Yii;
use app\models\User;
class UserController extends BaseController
{
	/*
		登录方法
	 */
	public function actionLogin()
	{
	    if(Yii::$app->request->isGet){
            $this->layout=false;
            return $this->render('login');
        }
        $username=trim($this->post("login_name",""));
        $login_pwd=trim($this->post("login_pwd",""));
        if(!$username||!$login_pwd){
            return $this->redenJs('请输入用户名和密码-1',UrlService::buildWebUrl('/user/login'));
        }
        //验证用户名
        $userinfo=User::find()->where(['login_name'=>$username])->asArray()->one();
        if(!$userinfo){
            return $this->redenJs('请输入正确的用户名和密码-2',UrlService::buildWebUrl('/user/login'));
        }
        //验证密码
        $login_salt=$userinfo['login_salt'];
        $auth_pwd=md5($login_pwd.md5($login_salt));
        $pwd=User::find()->where(['login_pwd'=>$auth_pwd])->one();
        if(!$pwd){
            return $this->redenJs('请输入正确的用户名和密码-3',UrlService::buildWebUrl('/user/login'));
        }
        //保存用户的登录状态
        $this->setLoginStatus($userinfo);
        return $this->redirect(UrlService::buildWebUrl('/dashboard/index'));
	}
    /*
    	编辑登录人信息
     */
    public function actionEdit()
    {
        $this->layout='main';
        if(Yii::$app->request->isGet){
        $userinfo=$this->user_info;
        return $this->render('edit',['userinfo'=>$userinfo]);
        }
        $nickname=trim($this->post('nickname',''));
        $email=trim($this->post('email',''));

        if( mb_strlen( $nickname,"utf-8" ) < 1 ){
            return $this->renderJSON( [],"请输入符合规范的姓名~~",-1 );
        }

        if( mb_strlen( $email,"utf-8" ) < 1 ){
            return $this->renderJSON( [],"请输入符合规范的邮箱地址~~",-1 );
        }
        $save_data=$this->user_info;
        $save_data->nickname=$nickname;
        $save_data->email=$email;
       $result = $save_data->save(0);
       if($result){
           return $this->renderJSON([],"操作成功~~");
       }else{
           return $this->renderJSON([],"操作失败~~",-1);
       }
    }
    /*
    	重置密码
     */
    public function actionResetPwd()
    {
        $this->layout='main';
        if(Yii::$app->request->isGet){
            $userinfo=$this->user_info;
            return $this->render('reset_pwd',['userinfo'=>$userinfo]);
        }


        $old_password = trim($this->post('old_password',''));
        $new_password = trim($this->post('new_password',''));
        if(!$old_password){
            return $this->renderJSON([],"请输入原密码！",-1);
        }

        if( mb_strlen($new_password,"utf-8") < 6 ){
            return $this->renderJSON([],"请输入不少于6位的新密码！",-1);
        }

        if($old_password == $new_password){
            return $this->renderJSON([],"请重新输入一个吧，新密码和原密码不能相同哦！",-1);
        }

        $current_user = $this->user_info;
        if (!$current_user->verifyPassword($old_password)) {
            return $this->renderJSON([],"请检查原密码是否正确~~",-1);
        }

        if( $current_user['uid'] == 2 ){
            return $this->renderJSON([],"该账号为测试账号，请不要修改密码~~",-1);
        }
        $current_user->setPassword($new_password);
        $current_user->updated_time = date("Y-m-d H:i:s");
        $current_user->update(0);

        $this->setLoginStatus( $current_user );

        return $this->renderJSON([],"修改成功~~");

    }
    /*
     * 退出功能
     */
    public function actionLogout()
    {
        $this->removeLoginStatus();
        return $this->redirect(UrlService::buildWebUrl('/user/login'));
    }
}
