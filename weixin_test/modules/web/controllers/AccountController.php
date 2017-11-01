<?php

namespace app\modules\web\controllers;

use app\common\services\ConstantMapService;
use app\models\User;
use app\modules\web\controllers\common\BaseController;
use yii\web\Controller;
use yii\data\Pagination;
class AccountController extends BaseController
{
	/*
        指定公共布局
     */
    public $layout='main';
    /*
    	账户管理页面
     */
    public function actionIndex()
    {
        $mix_kw = trim( $this->get("mix_kw","" ) );
        $status = intval( $this->get("status",ConstantMapService::$status_default ) );
        $page = trim( $this->get("page","" ) );
        $query=User::find();
        if($mix_kw)
        {
            //后面false参数为禁止转义
            $where_nickname = [ 'LIKE','nickname','%'.$mix_kw.'%', false ];
            $where_mobile = [ 'LIKE','mobile','%'.$mix_kw.'%', false ];
            $query->andWhere(['OR',$where_nickname,$where_mobile ]);
        }
        if($status>ConstantMapService::$status_default)
        {
            $query->andWhere(['status'=>$status]);
        }
//      $info=$query->orderBy(['uid' => SORT_DESC])->asArray()->all();

        $pages = new Pagination([
            'totalCount' => $query->count(),
            'pageSize'   => 3   //每页显示条数
        ]);
        $info = $query->orderBy(['uid' => SORT_DESC])->asArray()->offset($pages->offset)
            ->limit($pages->limit)
            ->all();
        return $this->render('index', [
            'info' => $info,
            'pages' => $pages,
            'status_mapping'=>ConstantMapService::$status_mapping,
            'mix_kw'=>$mix_kw,
            'status'=>$status
        ]);

//        return $this->render('index',['info'=>$info,'status_mapping'=>ConstantMapService::$status_mapping]);
    }
    /*
    	账户编辑页面
     */
    public function actionSet()
    {
        if( \Yii::$app->request->isGet ){
            $id = intval( $this->get("uid",0) );
            $info = [];
            if( $id ){
                $info = User::find()->where([ 'uid' => $id ])->one(  );
            }
            return $this->render("set",[
                'info' => $info
            ]);
        }
        $data=$this->post('data','');
        $id = intval( $this->get("uid",0) );
        $nickname=trim($data['nickname'],'');
        $mobile=trim($data['mobile'],'');
        $email=trim($data['email'],'');
        $login_name=trim($data['login_name'],'');
        $login_pwd=trim($data['login_pwd'],'');
        $date_now  = date("Y-m-d H:i:s");
        if( mb_strlen( $nickname,"utf-8" ) < 1 ){
            return $this->renderJSON( [] , "请输入符合规范的姓名~~" ,-1);
        }

        if( mb_strlen( $mobile,"utf-8" ) < 1 ){
            return $this->renderJSON( [] , "请输入符合规范的手机号码~~" ,-1);
        }

        if( mb_strlen( $email,"utf-8" ) < 1 ){
            return $this->renderJSON( [] , "请输入符合规范的邮箱地址~~" ,-1);
        }

        if( mb_strlen( $login_name,"utf-8" ) < 1 ){
            return $this->renderJSON( [] , "请输入符合规范的登录名~~" ,-1);
        }

        if( mb_strlen( $login_pwd,"utf-8" ) < 1 ){
            return $this->renderJSON( [] , "请输入符合规范的登录密码~~" ,-1);
        }

        $has_in = User::find()->where([ 'login_name' => $login_name ])->andWhere([ '!=','uid',$id ])->count();
        if( $has_in ){
            return $this->renderJSON( [] , "该登录名已存在，请换一个试试~~" ,-1);
        }

        $info = User::find()->where([ 'uid' => $id ])->one(  );
        if( $info ){
            $model_user = $info;
        }else{
            $model_user = new User();
            $model_user->setSalt();
            $model_user->created_time = $date_now;
        }
        $model_user->nickname = $nickname;
        $model_user->mobile = $mobile;
        $model_user->email = $email;
        $model_user->avatar = ConstantMapService::$default_avatar;
        $model_user->login_name = $login_name;
        if( $login_pwd !=  ConstantMapService::$default_password ){
            $model_user->setPassword( $login_pwd)  ;
        }
        $model_user->updated_time = $date_now;
        $model_user->save( 0 );

        return $this->renderJSON( [],"操作成功~~" );



    }
    /*
    	账户详情页面
     */
    public function actionInfo()
    {
    	return $this->render('info');
    }
    /*
     * 删除与恢复方面
     */
    public function actionOps(){
        if( !\Yii::$app->request->isPost ){
            return $this->renderJSON( [],ConstantMapService::$default_syserror,-1 );
        }

        $id = $this->post('id',[]);
        $act = trim($this->post('act',''));
        if( !$id ){
            return $this->renderJSON([],"请选择要操作的账号~~",-1);
        }

        if( !in_array( $act,['remove','recover' ])){
            return $this->renderJSON([],"操作有误，请重试~~",-1);
        }

        $info = User::find()->where([ 'uid' => $id ])->one();
        if( !$info ){
            return $this->renderJSON([],"指定账号不存在~~",-1);
        }

        switch ( $act ){
            case "remove":
                $info->status = 0;
                break;
            case "recover":
                $info->status = 1;
                break;
        }
        $info->updated_time = date("Y-m-d H:i:s");
        $info->update( 0 );
        return $this->renderJSON( [],"操作成功~~" );
    }

}
