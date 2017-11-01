<?php

namespace app\modules\web\controllers;
use app\modules\web\controllers\common\BaseController;
use yii\web\Controller;

class QrcodeController extends BaseController
{
	/*
        指定公共布局
     */
    public $layout='main';
	/*
		营销渠道二维码列表
	 */   
    public function actionIndex()
    {
        return $this->render('index');
    }
    /*
    	营销渠道二维码的编辑与添加
     */
    public function actionSet()
    {
        return $this->render('set');
    }
}
