<?php

namespace app\modules\m\controllers;

use app\modules\m\controllers\common\BaseController;
use yii\web\Controller;

class PayController extends BaseController
{
	/*
		支付页面
	 */
    public function actionBuy()
    {
        return $this->render('buy');
    }
}
