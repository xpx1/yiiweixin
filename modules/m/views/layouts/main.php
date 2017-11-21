<?php
use app\assets\MAsset;
MAsset::register($this);
use\app\models\brand\BrandSetting;
use \app\common\services\UtilService;
use app\common\services\UrlService;
$brand=BrandSetting::find()->asArray()->all();
$brand_name=$brand[0]['name'];
?>
<?php $this->beginPage();?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <!-- Set render engine for 360 browser -->
    <meta name="renderer" content="webkit">
    <title><?= $brand_name?UtilService::encode($brand_name):''?></title>
<!--    <link href="/font-awesome/css/font-awesome.css" rel="stylesheet">-->
<!--<link href="/css/m/css_style.css" rel="stylesheet">-->
<!--<link href="/css/m/app.css?ver=20170401" rel="stylesheet">-->
    <?php $this->head();?>
</head>
<body>
<?php $this->beginBody()?>
<!-- 不同的部分开始 -->
<?=$content;?>
<!-- 不同的部分结束 -->

<div class="copyright clearfix">
    <?php if (isset($this->params['current_user'])): ?>
            <p class="name">欢迎您，<?= UtilService::encode($this->params['current_user']['nickname']) ?></p>
    <?php endif;?>
        <p class="copyright">由<a href="<?=UrlService::buildWwwUrl('/');?>" target="_blank">编程浪子</a>提供技术支持</p>
</div>
<?php if( !\Yii::$app->view->params['menu_hide'] ):?>
<div class="footer_fixed clearfix">
    <span><a href="<?=UrlService::buildMUrl("/default/index");?>" class="default"><i class="home_icon"></i><b>首页</b></a></span>
    <span><a href="<?=UrlService::buildMUrl("/product/index");?>" class="product"><i class="store_icon"></i><b>图书</b></a></span>
    <span><a href="<?=UrlService::buildMUrl("/user/index");?>" class="user"><i class="member_icon"></i><b>我的</b></a></span>
</div>
<?php endif;?>
<div class="layout_hide_wrap hidden">
    <input type="hidden" id="share_info" value='<?=Yii::$app->getView()->params['share_info'];?>'> <!--取出分享所需的变量，是之使用main.php的文件都可使用该变量-->
</div>
<?php $this->endBody()?>
</body>
</html>
<?php $this->endPage();?>