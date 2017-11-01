<!-- 以上引入公共布局部分 -->
<?php
use \app\common\services\UtilService;
\app\common\services\StaticService::includeAppJsStatic('js/m/default/index.js',app\assets\MAsset::className())
?>
<div style="min-height: 500px;">
    <div class="shop_header">
    <i class="shop_icon"></i>
    <strong><?=$info?UtilService::encode($info['name']):'';?></strong>
</div>

<?php if($list): ?>
<div id="slideBox" class="slideBox">
    <div class="bd">
        <ul>
            <?php foreach ($list as  $image): ?>
                        <li><img style="max-height: 250px;" src="<?= \app\common\services\UrlService::buildPicUrl('brand',$image['image_key']);?>" /></li>
        <?php endforeach; ?>
        </ul>
    </div>
    <div class="hd">
        <ul>
        </ul></div>
</div>
    <?php endif; ?>
<div class="fastway_list_box">
    <ul class="fastway_list">
        <li><a href="javascript:void(0);" style="padding-left: 0.1rem;"><span>品牌名称：<?=$info?UtilService::encode($info['name']):'';?></span></a></li>
        <li><a href="javascript:void(0);" style="padding-left: 0.1rem;"><span>联系电话：<?=$info?UtilService::encode($info['mobile']):'';?></span></a></li>
        <li><a href="javascript:void(0);" style="padding-left: 0.1rem;"><span>联系地址：<?=$info?UtilService::encode($info['address']):'';?></span></a></li>
        <li><a href="javascript:void(0);" style="padding-left: 0.1rem;"><span>品牌介绍：<?=$info?UtilService::encode($info['description']):'';?></span></a></li>
    </ul>
</div></div>
<!-- 以下引入公共布局部分 -->