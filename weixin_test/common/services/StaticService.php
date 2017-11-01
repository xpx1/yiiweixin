<?php
namespace app\common\services;

use Yii;
class StaticService
{
    public static function includeAppStatic($type, $path,$depend)
    {
        $release_version = defined("VERSION")?VERSION:time();
        $path = $path."?ver={$release_version}";
        if ($type == "css") {
            Yii::$app->getView()->registerCssFile($path, ['depends' => $depend]);
        } else {
            Yii::$app->getView()->registerJsFile($path, ['depends' => $depend]);
        }
    }
    public static function includeAppJsStatic($path,$depend){
       return self::includeAppStatic("js",$path,$depend); //这里return
    }

    public static function includeAppCssStatic($path,$depend){
      return self::includeAppStatic("css",$path,$depend);
    }

}