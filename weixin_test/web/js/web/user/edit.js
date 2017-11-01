;
var user_edit_ops= {
    init:function () {
        this.eventBind();
    },
    eventBind:function(){
        $('.user_edit_wrap .save').click( function () {
            var btn_target=$(this); //当前对象
            if(btn_target.hasClass('disabled')){
                common_ops.alert('请不要重复点击');
                return;
            }
            var nickname_target=$(".user_edit_wrap input[name=nickname]");
              var nickname=nickname_target.val();
            var email_target=$(".user_edit_wrap input[name=email]");
                var email=email_target.val();
            if(nickname.length<1){
                common_ops.tip('请输入合法的用户名',nickname_target);
                return false; //return false 有阻止表单提交即阻止代码往下执行的作用
            }
            if(email.length<1){
                common_ops.tip('请输入合法的邮箱',email_target);
                return false;
            }
            btn_target.addClass('disabled');//使之不能点击因为程序还没运行完
            $.ajax({
                type: "POST",
                url: '/web/user/edit',
                dataType: "json",
                data: {nickname:nickname,email:email},
                success: function(res){
                    btn_target.removeClass('disabled');//程序运行完后去掉禁止点击属性
                    alert(res.msg);
                    if(res.code==200){
                        window.location.href=window.location.href;
                    }
                }
            });
        });
    }
};

$(document).ready(function(){
    user_edit_ops.init();
});