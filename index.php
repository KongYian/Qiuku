<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <title>监测后台登录</title>
    <link rel="stylesheet" type="text/css" href="layui/css/style.css" tppabs="css/style.css"/>
    <link rel="stylesheet" href="layui/css/layui.css">
    <style>
        body{height:100%;background:#16a085;overflow:hidden;}
        canvas{z-index:-1;position:absolute;}
    </style>
    <script src="layui/jquery-3.1.1.min.js"></script>
    <script src="layui/verificationNumbers.js" tppabs="js/verificationNumbers.js"></script>
    <script src="layui/Particleground.js" tppabs="js/Particleground.js"></script>
    <script src="layui/layui.js"></script>
    <script>
        layui.use(['layer', 'element'], function() {
            $('body').particleground({
                dotColor: '#5cbdaa',
                lineColor: '#5cbdaa'
            });
            $('.submit_btn').click(function() {
                var $name = $('.user_icon input').val();
                var $pwd = $('.pwd_icon input').val();
                if (!$name || !$pwd) {
                    layer.alert('密码或者用户名为空！请重新填写', {
                        icon: 2
                    })
                } else {
                    var $param = {
                        'adminName': $name,
                        'adminPwd': $pwd
                    }
                    console.log($param);
                    $.ajax({
                        url: 'doaction/doLoginAjax.php',
                        data: $param,
                        dataType: 'json',
                        type: 'post',
                        success: function(data) {
                            if (data.status == 1) {
                                layer.msg(data.info, {
                                    icon: 1
                                });
                                setTimeout(function() {
                                    window.location.href = 'ob.php'
                                }, 2000)
                            } else {
                                layer.alert(data.info, {
                                    icon: 2
                                })
                            }
                        },
                        error: function() {
                            layer.alert('页面出错啦', {
                                icon: 2
                            })
                        }
                    })
                }
            })
        })
    </script>
</head>
<body>
<dl class="admin_login">
    <dt>
        <strong>监测程序登录</strong>
        <em>Login System</em>
    </dt>
    <dd class="user_icon">
        <input type="text" placeholder="账号" class="login_txtbx" name="adminname"/>
    </dd>
    <dd class="pwd_icon">
        <input type="password" placeholder="密码" class="login_txtbx" name="adminpwd">
    </dd>
    <dd>
        <input type="button" value="立即登陆" class="submit_btn"/>
    </dd>
    <dd>
        <p>
            michael
        </p>
        <p>
            All rights reserved
        </p>
    </dd>
</dl>
</body>
</html>