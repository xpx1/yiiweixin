<?php

namespace app\modules\web\controllers;

use app\modules\web\controllers\common\BaseController;
use yii\web\Controller;

class DashboardController extends BaseController
{
	/*
        指定公共布局
     */
    public $layout='main';
 	/*
 		仪表盘页面
 	 */  
    public function actionIndex()
    {
        return $this->render('index');
    }
}
