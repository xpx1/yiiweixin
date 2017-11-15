<?php
use \app\common\services\UrlService;
use \app\common\services\UtilService;
use \app\common\services\StaticService;
StaticService::includeAppJsStatic( "/js/m/product/info.js",\app\assets\MAsset::className() );
?>
<div style="min-height: 500px;">
	<div class="pro_tab clearfix">
    <span>图书详情</span>
</div>
<div class="proban">
    <div id="slideBox" class="slideBox">
        <div class="bd">
            <ul>
                <li><img src="<?=UrlService::buildPicUrl("book",$list['main_image'] );?>"/></li>
            </ul>
        </div>
    </div>
</div>
<div class="pro_header">
    <div class="pro_tips">
        <h2><?=$list['name']?UtilService::encode($list['name']):''?></h2>
        <h3><b><?=$list['price']?UtilService::encode($list['price']):''?></b><font>库存量：<?=$list['price']?UtilService::encode($list['stock']):''?></font></h3>
    </div>
    <span class="share_span"><i class="share_icon"></i><b>分享商品</b></span>
</div>
<div class="pro_express">月销量：<?=UtilService::encode($list['month_count'])?><b>累计评价：<?=UtilService::encode($list['comment_count'])?></b></div>
<div class="pro_virtue">
    <div class="pro_vlist">
        <b>数量</b>
        <div class="quantity-form">
            <a class="icon_lower"></a>
            <input type="text" name="quantity" class="input_quantity" value="1" readonly="readonly" max="<?=$list["stock"];?>"/>
            <a class="icon_plus"></a>
        </div>
    </div>
</div>
<div class="pro_warp">
    <?=nl2br($list['summary']);?>
</div>
<div class="pro_fixed clearfix">
    <a href="<?= UrlService::buildMUrl("/"); ?>"><i class="sto_icon"></i><span>首页</span></a>
    <?php if( $has_faved ):?>
        <a class="fav has_faved" href="<?= UrlService::buildNullUrl( ); ?>"><i class="keep_icon"></i><span>已收藏</span></a>
    <?php else:?>
        <a class="fav" href="<?= UrlService::buildNullUrl( ); ?>" data="<?=$list['id'];?>"><i class="keep_icon"></i><span>收藏</span></a>
    <?php endif;?>
        <input type="button" value="立即订购" class="order_now_btn" data="<?=$list['id'];?>"/>
    <input type="button" value="加入购物车" class="add_cart_btn" data="<?=$list['id'];?>"/>
    <input type="hidden" name="id" value="<?=$list['id'];?>">
</div>
</div>

