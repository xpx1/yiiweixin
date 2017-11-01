<!-- 以上引入公共布局部分 -->
<?php
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use app\common\services\StaticService;
use \app\common\services\UrlService;
StaticService::includeAppJsStatic('js/web/brand/set.js',\app\assets\WebAsset::className());
?>
<?php echo \Yii::$app->view->renderFile("@app/modules/web/views/common/tab_brand.php",['current'=>'info']); ?>
<div class="row m-t  wrap_brand_set">
	<div class="col-lg-12">
		<h2 class="text-center">品牌设置</h2>
		<div class="form-horizontal m-t m-b">
			<div class="form-group">
				<label class="col-lg-2 control-label">品牌名称:</label>
				<div class="col-lg-10">
					<input type="text" name="name" class="form-control" placeholder="请输入品牌名称~~" value="">
				</div>
			</div>
			<div class="hr-line-dashed"></div>
			<div class="form-group">
				<label class="col-lg-2 control-label">品牌Logo:</label>
				<div class="col-lg-10">
                    <form class="upload_pic_wrap" target="upload_file" enctype="multipart/form-data" method="POST" action="<?=UrlService::buildWebUrl('/upload/pic');?>">
                        <div class="upload_wrap pull-left">
                            <i class="fa fa-upload fa-2x"></i>
                           <input type="hidden" name="bucket" value="brand" /><!-- 把篮子的值传到后台，根据篮子的值可以跟好的对图片进行管理-->
                            <input type="file" name="pic" accept="image/png, image/jpeg, image/jpg,image/gif">
                        </div>
                        <?php if ($info&&$info['logo']):?>
						    <span class="pic-each">
							<img src="<?= UrlService::buildPicUrl('brand',$info['logo']) ;?>">
							<span class="fa fa-times-circle del del_image" data="<?=$info['logo'];?>"><i></i></span>
						</span>
                        <?php endif; ?>
                    </form>
                </div>
			</div>
			<div class="hr-line-dashed"></div>
			<div class="form-group">
				<label class="col-lg-2 control-label">电话:</label>
				<div class="col-lg-10">
					<input type="text" name="mobile" class="form-control" placeholder="请输入联系电话~~"  value="">
				</div>
			</div>
			<div class="hr-line-dashed"></div>
			<div class="form-group">
				<label class="col-lg-2 control-label">地址:</label>
				<div class="col-lg-10">
					<input type="text" name="address" class="form-control" placeholder="请输入联系地址~~"  value="">
				</div>
			</div>
			<div class="hr-line-dashed"></div>
			<div class="form-group">
				<label class="col-lg-2 control-label">品牌介绍:</label>
				<div class="col-lg-10">
					<textarea  name="description" class="form-control" rows="4"></textarea>
				</div>
			</div>
			<div class="hr-line-dashed"></div>
			<div class="form-group">
				<div class="col-lg-4 col-lg-offset-2">
                    <button class="btn btn-w-m btn-outline btn-primary save">保存</button>
				</div>
			</div>
		</div>
	</div>
</div>
<iframe name="upload_file" align="right" class="hide"></iframe>
<!-- 以下引入公共布局部分 -->

