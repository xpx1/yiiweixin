<?php

namespace app\modules\m\controllers;

use app\models\brand\BrandImages;
use app\models\brand\BrandSetting;
use app\modules\m\controllers\common\BaseController;
use yii\web\Controller;

class DefaultController extends BaseController
{
    //首页控制器
	/*
		品牌首页
	 */
    public function actionIndex()
    {
        $info=BrandSetting::find()->one();
        $list=BrandImages::find()->orderBy(['id'=>SORT_DESC])->all();
        return $this->render('index',[
            'info'=>$info,
            'list'=>$list
        ]);
    }
}
