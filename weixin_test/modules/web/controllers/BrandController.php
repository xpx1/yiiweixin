<?php

namespace app\modules\web\controllers;
use app\common\services\ImageService;
use app\common\services\UtilService;
use app\models\brand\BrandSetting;
use app\models\brand\BrandImages;
use app\models\UploadForm;
use app\modules\web\controllers\common\BaseController;
use yii\web\Controller;
use yii\web\UploadedFile;
class BrandController extends BaseController
{
	/*
        指定公共布局
     */
    public $layout='main';
	/*
		品牌详情
	 */
    public function actionInfo()
    {
       $info = BrandSetting::find()->asArray()->one();
        return $this->render('info',['info'=>$info]);
    }
    /*
		品牌编辑
	 */
    public function actionSet()
    {
        if (\Yii::$app->request->isGet) {
            $info = BrandSetting::find()->one(  );
            return $this->render("set",[
                'info' => $info
            ]);
        }
        $name = trim( $this->post("name","") );
        $logo = trim( $this->post("logo","") );
        $mobile = trim( $this->post("mobile","") );
        $address = trim( $this->post("address","") );
        $description = trim( $this->post("description","") );
        $date_now  = date("Y-m-d H:i:s");

        if( mb_strlen( $name,"utf-8" ) < 1 ){
            return $this->renderJSON( [] , "请输入符合规范的品牌名称~~" ,-1);
        }

        if( mb_strlen( $logo,"utf-8" ) < 1 ){
            return $this->renderJSON( [] , "请上传品牌Logo~~" ,-1);
        }

        if( mb_strlen( $mobile,"utf-8" ) < 1 ){
            return $this->renderJSON( [] , "请输入符合规范的手机号码~~" ,-1);
        }

        if( mb_strlen( $address,"utf-8" ) < 1 ){
            return $this->renderJSON( [] , "请输入符合规范的地址~~" ,-1);
        }

        if( mb_strlen( $description,"utf-8" ) < 1 ){
            return $this->renderJSON( [] , "请输入符合规范的品牌介绍~~" ,-1);
        }

        $info = BrandSetting::find()->one(  );//从数数据库中取出的数据是对象形式的，->asArray()数组形式的
        if( $info ){
            $model_brand = $info;
        }else{
            $model_brand = new BrandSetting();
            $model_brand->created_time = $date_now;
        }
        $model_brand->name = $name;
        $model_brand->logo = $logo;
        $model_brand->mobile = $mobile;
        $model_brand->address = $address;
        $model_brand->description = $description;
        $model_brand->updated_time = $date_now;
        $model_brand->save( 0 );   //为0则不指定该数据的属性，默认为null

        return $this->renderJSON( [],"操作成功~~" );
    }


    /*
    	品牌相册
     */
    public function actionImages()
    {
        $list = BrandImages::find()->orderBy([ 'id' => SORT_DESC ])->all();
        return $this->render("images",[
            'list' => $list
        ]);
    }
    public function actionRoot()
    {
        $upload_dir_path=UtilService::getRootPath();
        echo $upload_dir_path;
    }
        public function actionSetImage(){

        $image_key = trim( $this->post("image_key","") );
        if( !$image_key ){
            return $this->renderJSON([],"请上传图片之后在提交~~",-1);
        }

        $total_count = BrandImages::find()->count();
        if( $total_count >= 5 ){
            return $this->renderJSON([],"最多上传五张~~",-1);
        }

        $model = new BrandImages();
        $model->image_key = $image_key;
        $model->created_time = date("Y-m-d H:i:s");
        $model->save( 0 );
        return $this->renderJSON([],"操作成功~~");
    }

    public function actionImageOps(){
        if( !\Yii::$app->request->isPost ){
            return $this->renderJSON( [],ConstantMapService::$default_syserror,-1 );
        }

        $id = $this->post('id',[]);
        if( !$id ){
            return $this->renderJSON([],"请选择要删除的图片~~",-1);
        }

        $info = BrandImages::find()->where([ 'id' => $id ])->one();
        if( !$info ){
            return $this->renderJSON([],"指定图片不存在~~",-1);
        }
        $info->delete();
        return $this->renderJSON( [],"操作成功~~" );
    }
}
