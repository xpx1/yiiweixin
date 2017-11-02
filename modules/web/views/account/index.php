<!-- 以上引入公共布局部分 -->
<?php
use \app\common\services\UrlService;
use\app\common\services\ConstantMapService;
use \app\common\services\StaticService;
use yii\widgets\LinkPager;
StaticService::includeAppJsStatic( "js/web/account/index.js",\app\assets\WebAsset::className() );
?>
<?php echo \Yii::$app->view->renderFile("@app/modules/web/views/common/tab_account.php",['current'=>'index']); ?>
<div class="row">
    <div class="col-lg-12">
        <form class="form-inline wrap_search">
            <div class="row m-t p-w-m">
                <div class="form-group">
                    <select name="status" class="form-control inline">
                        <option value="<?=ConstantMapService::$status_default ;?>">请选择状态</option>
                        <?php foreach ($status_mapping as $k=>$v):?>
                                                    <option value="<?= $k ;?>" <?php if($k==$status):?> selected="selected"<?php endif;?>><?= $v ;?></option>
                        <?php endforeach;?>
                                            </select>
                </div>

                <div class="form-group">
                    <div class="input-group">
                        <input type="text" name="mix_kw" placeholder="请输入姓名或者手机号码" class="form-control" value="<?php ($mix_kw)?$mix_kw:"";echo $mix_kw?>">
                        <input type="hidden" name="p" value="1">
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-primary search">
                                <i class="fa fa-search"></i>搜索
                            </button>
                        </span>
                    </div>
                </div>
            </div>
            <hr/>
            <div class="row">
                <div class="col-lg-12">
                    <a class="btn btn-w-m btn-outline btn-primary pull-right" href="/web/account/set">
                        <i class="fa fa-plus"></i>账号
                    </a>
                </div>
            </div>
        </form>
        <table class="table table-bordered m-t">
            <thead>
            <tr>
                <th>序号</th>
                <th>姓名</th>
                <th>手机</th>
                <th>邮箱</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach( $info as $value ):?>
                <tr>
                    <td><?php echo $value['uid'];?></td>
                    <td><?php echo $value['nickname'];?></td>
                    <td><?php echo $value['mobile'];?></td>
                    <td><?php echo $value['email'];?></td>
                    <td>
                        <a  href="<?php echo UrlService::buildWebUrl('/account/info',['uid'=>$value['uid']])?>">
                            <i class="fa fa-eye fa-lg"></i>
                        </a>
                <?php if( $value['status'] ):?>
                                                <a class="m-l" href="<?php echo UrlService::buildWebUrl('/account/set',['uid'=>$value['uid']])?>">
                            <i class="fa fa-edit fa-lg"></i>
                        </a>

                        <a class="m-l remove" href="javascript:void(0);" data="<?= $value['uid'];?>">
                            <i class="fa fa-trash fa-lg"></i>
                        </a>
                    <?php else:?>
                    <a class="m-l recover" href="<?=UrlService::buildNullUrl();?>" data="<?=$value['uid'];?>">
                        <i class="fa fa-rotate-left fa-lg"></i>
                    </a>
                <?php endif;?>
                                            </td>
                </tr>
            <?php endforeach; ;?>
                </tbody>
        </table>
        <div class="row">
    <div class="col-lg-12">
        <span class="pagination_count" style="line-height: 40px;">共<?= $pages->totalCount;?>条记录 | 每页<?= $pages->pageSize;?>条</span>
                <div style="margin-left: 900px;">
            <?php echo LinkPager::widget([    //显示分页
                'pagination' => $pages,
            ]); ?>
                </div>
<!--        <ul class="pagination pagination-lg pull-right" style="margin: 0 0 ;">-->
<!--                                                            <li class="active"><a href="javascript:void(0);">1</a></li>-->
<!--                                                </ul>-->
    </div>
</div>  </div>
</div>
<!-- 以下引入公共布局部分 -->
