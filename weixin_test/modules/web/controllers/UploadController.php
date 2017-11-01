<?php
namespace app\modules\web\controllers;


use app\common\services\UploadService;
use app\modules\web\controllers\common\BaseController;

class UploadController extends BaseController
{
    private $allow_file_type=['jpg','gif','png','jpeg'];
    /*
     * 上传接口
     *  $bucket:[avatar:头像/brand:品牌/book:图书;
     */
    public function actionPic()
    {

        $bucket =trim( $this->post("bucket","") );
        $type = $this->post('type');//表示文件的类型
        $call_back_target = 'window.parent.upload';//表示ifame页面调用set.php页面
        if(!$_FILES || !isset($_FILES['pic']))
        {
            return "<script>{$call_back_target}.error('请选择文件之后再提交~~')</script>";
        }
        //关于文件后缀的验证
        $file_name=$_FILES['pic']['name'];
        //以点切割文件名(文件名后带文件后缀名)
        $tmo_file_extend=explode(".",$file_name);

        if(!in_array(strtolower(end($tmo_file_extend)),$this->allow_file_type))//end获取数组的最后一个元素
        {
            return "<script>{$call_back_target}.error('请上传指定类型的图片，类型允许png,jpg,jpeg,gif')</script>";
        }

        //上传图片业务逻辑 todo
        $ret=UploadService::uploadByFile($file_name,$_FILES['pic']['name'],$bucket);//$_FILES['pic']['name']表示上传文件的临时路径
        $ret = UploadService::uploadByFile( $_FILES['pic']['name'],$_FILES['pic']['tmp_name'],$bucket );
        if( !$ret ){
            return "<script type='text/javascript'>{$call_back_target}.error('".UploadService::getLastErrorMsg()."');</script>";
        }
        return "<script type='text/javascript'>{$call_back_target}.success('{$ret['path']}','$type');</script>";
    }
}