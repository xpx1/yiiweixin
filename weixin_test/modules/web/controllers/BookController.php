<?php

namespace app\modules\web\controllers;
use app\modules\web\controllers\common\BaseController;
use yii\web\Controller;

class BookController extends BaseController
{
	/*
        指定公共布局
     */
    public $layout='main';
	/*
		图书列表
	 */
    public function actionIndex()
    {
        return $this->render('index');
    }
    /*
		图书编辑或添加
	 */
    public function actionSet()
    {
        return $this->render('set');
    }
    /*
        图书详情
     */
    public function actionInfo()
    {
        return $this->render('info');
    }
    /*
    	图书图片
     */
    public function actionImages()
    {
        return $this->render('images');
    }
    /*
        图书分类列表
     */
    public function actionCat()
    {
        return $this->render('cat');
    }
    /*
        图书编辑或添加
     */
    public function actionCat_set()
    {
        return $this->render('cat_set');
    }
}
