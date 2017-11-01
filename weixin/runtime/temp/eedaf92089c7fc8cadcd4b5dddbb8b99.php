<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:77:"D:\WWW\weixinss\weixin\public/../application/admin\view\video\video_list.html";i:1492666454;}*/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>后台欢迎页</title>
<link rel="stylesheet" href="../../../../public/css/reset.css" />
<link rel="stylesheet" href="../../../../public/css/content.css" />
</head>
<body marginwidth="0" marginheight="0">
<div class="container">
    <div class="public-content">
        <div class="public-content-header">
            <!-- <h3>修改网站配置</h3> -->
        </div>
        <div class="public-content-cont">
            <table class="public-cont-table">
                <tr>
                    <th style="width:5%">直播名称</th>
                    <th style="width:5%">封面图</th>
                    <th style="width:5%">直播地址</th>
                    <th style="width:5%">开始时间</th>
                    <th style="width:5%">结束时间</th>
                    <th style="width:5%">操作</th>
                </tr>
                <tbody id="tbody">
                <?php foreach($video as $vo): ?>
                <tr>
                    <td><?php echo $vo['video_name']; ?></td>
                    <td><img src="../../../../public/uploads/zhibo/<?php echo $vo['video_img']; ?>" width="100" height="100"></td>
                    <td><?php echo $vo['video_url']; ?></td>
                    <td><?php echo date('H点i分',$vo['video_start']); ?></td>
                    <td><?php echo date('H点i分',$vo['video_end']); ?></td>
                    <td>
                        <div class="table-fun">
                            <button class="del" value="<?php echo $vo['video_id']; ?>">删除</button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
<script type="text/javascript" src="../../../../public/js/jquery.js"></script>
<script>
    $(".del").click(function(){
        $(this).parent().parent().parent().remove();
        var id = $(this).val();
        $.ajax({
            type: "POST",
            url: "roledel",
            data: "role_id="+id,
            success: function(msg){
                if(msg==1){

                    alert("删除成功！");

                }else {
                    alert("删除失败！");
                }
                // alert("删除成功！");
            }
        });
    })
</script>