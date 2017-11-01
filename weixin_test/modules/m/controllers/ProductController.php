<?php

namespace app\modules\m\controllers;

use app\modules\m\controllers\common\BaseController;
use yii\web\Controller;

class ProductController extends BaseController
{	
	/*
		指定公共布局
	 */
	// public $layout='main';
	/*
		商品列表
	 */
    public function actionIndex()
    {
        return $this->render('index');
    }
    /*
    	商品详情
     */
    public function actionInfo()
    {   
        $this->layout=false;
        return $this->render('info');
    }
    /*
    	下单页面
     */
    public function actionOrder()
    {
        return $this->render('order');
    }
}
