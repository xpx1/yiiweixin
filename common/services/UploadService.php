<?php


namespace app\common\services;

use app\models\images\Images;
/*
 * 上传服务
 */
class UploadService extends BaseService
{
    public static function uploadByFile($file_name,$file_path,$bucket='')
    {
        if(!$file_name)
        {
            return self::_err('参数文件名是必需的');
        }
        if( !$file_path || !file_exists($file_path) ){
            return self::_err("请传入合法的参数文件路径~~");
        }

        //在每个篮子下面按照日期放图片 ，为了方便管理图片的路径我们可以把图片路径配置在params.php文件中
        $upload_config = \Yii::$app->params['upload'];
        if( !isset( $upload_config[ $bucket ] ) ){
            return self::_err("指定的bucket不存在或者没有配置~~");
        }
        $date_now = date("Y-m-d H:i:s");
        $tmp_file_extend = explode(".", $file_name);
        $file_type = strtolower( end($tmp_file_extend) );
        $hash_key = md5( file_get_contents( $file_path ) );
        $upload_dir_path=UtilService::getRootPath()."/web".$upload_config[ $bucket ]."/";
        $folder_name = date( "Ymd",strtotime($date_now) );
        $upload_dir = $upload_dir_path.$folder_name; //文件的完整路径
        //如果该文件夹不存在就创建该文件夹
        if( !file_exists($upload_dir) ){
            mkdir($upload_dir,0777);
            chmod($upload_dir,0777);//在服务器上用mkdir没用所以再用chmod改变文件夹的权限
        }
        $upload_file_name = "{$folder_name}/{$hash_key}.".$file_type;//篮子下面的文件夹及文件
        if( is_uploaded_file($file_path) ){
            if(!move_uploaded_file($file_path,$upload_dir_path.$upload_file_name) ){//将文件的临时路径移动到指定路径
                return self::_err("上传失败！！系统繁忙请稍后再试~~");
            }
        }else{
            file_put_contents( $upload_dir_path.$upload_file_name,file_get_contents($file_path) );//创建文件
        }

        $image_url=\Yii::$app->params['domain']['www'].$upload_config[ $bucket ]."/";//图片路径前缀

        //图片路径入库操作
        $images=new Images();
        $images->bucket=$bucket;
        $images->file_key=$image_url.$upload_file_name;
        $images->created_time=$date_now;
        $images->save(0);

        return[
            'code' => 200,
            'path' => $upload_file_name,
            'prefix' => $upload_config[ $bucket ]."/"
        ];
    }
}