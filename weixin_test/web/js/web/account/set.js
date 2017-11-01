;
var account_set_ops = {
    init:function(){
        this.eventBind();
    },
    eventBind:function(){
        $('.save').click(function () {
            var nickname=$("input[name=nickname]").val();
            if(nickname.length<1){
                common_ops.tip('请输入用户名',$("input[name=nickname]"));
                return false;
            }
            var mobile=$("input[name=mobile]").val();
            if(nickname.length<1){
                common_ops.tip('请输入手机号并且手机号的位数不少于11位',$("input[name=mobile]"));
                return false;
            }
            if(!(/^1[34578]\d{9}$/.test(mobile))){
                common_ops.tip("手机号码有误，请重填",$("input[name=mobile]"));
                return false;
            }
            var email=$("input[name=email]").val();
            if(email.length<1){
                common_ops.tip('请输入邮箱',$("input[name=email]"));
                return false;
            }
            if(!(/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/.test(email))) {
                common_ops.tip('邮箱格式错误',$("input[name=email]"));
                return false;
            }
            var login_name=$("input[name=login_name]").val();
            if(login_name.length<1){
                common_ops.tip('请输入登录名',$("input[name=login_name]"));
                return false;
            }
            var login_pwd=$("input[name=login_pwd]").val();
            if(login_pwd.length<1){
                common_ops.tip('请输入密码',$("input[name=login_pwd]"));
                return false;
            }
            if(login_pwd.length<6){
                common_ops.tip('密码不得小于六位',$("input[name=login_pwd]"));
                return false;
            }
            var data={
            'nickname':nickname,
            'mobile':mobile,
            'email':email,
            'login_name':login_name,
            'login_pwd':login_pwd
            };

            $.ajax({
                type: 'POST',
                url: '/web/account/set',
                data: {'data':data},
                dataType:'json',
                success: function(res){
                    var callback=null;
                    if(res.code==200){
                        callback=window.location.href = '/web/account/index';
                    }
                    common_ops.alert( res.msg ,callback);
                }
            });
        })
    }
};

$(document).ready( function(){
    account_set_ops.init();
});
