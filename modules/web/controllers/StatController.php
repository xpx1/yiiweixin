<?php

namespace app\modules\web\controllers;
use app\modules\web\controllers\common\BaseController;
use yii\web\Controller;

class StatController extends BaseController
{
    /*
        指定公共布局
     */
    public $layout='main';
    /*
        财务统计
     */
    public function actionIndex()
    {
        return $this->render('index');
    }
    /*
        商品售卖统计
     */
    public function actionProduct()
    {
        return $this->render('index');
    }
    /*
        会员消费统计
     */
    public function actionMember()
    {
        return $this->render('index');
    }
    /*
        分享统计
     */
    public function actionShare()
    {
        return $this->render('index');
    }
}
