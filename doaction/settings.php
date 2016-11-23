<?php
require_once 'Session.class.php';
require_once 'mysqlConfig.php';
Session::checkSession('../index.php');
?>
<!doctype html>
<html xmlns="http://www.w3.org/1999/html">
<head>
    <meta charset="utf-8">
    <title>监控信息配置</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="../layui/css/layui.css"  media="all">
    <link rel="stylesheet" href="../layui/css/style.css">
    <script src="../layui/jquery-3.1.1.min.js"></script>
    <script src="../layui/layui.js"></script>
    <script src="../layui/verificationNumbers.js"></script>
    <script src="../layui/Particleground.js"></script>
    <script>
      $(function () {
            $('body').particleground({
             dotColor: '#5cbdaa',
               lineColor: '#5cbdaa'
           });
       })
    </script>
    <style>
        body{height:100%;background:#16a085;overflow:hidden;}
        canvas{z-index:-1;position:absolute;}
    </style>
</head>

<body class="layui-main">

<fieldset class="layui-elem-field layui-field-title ">
    <legend>监控信息配置</legend>
</fieldset>

<form class="layui-form layui-main" action="">
    <div class="layui-form-item">
        <label class="layui-form-label">服务器名称</label>
        <div class="layui-input-inline">
            <input type="text" name="serverName" lay-verify="required" placeholder="请输入" autocomplete="off" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">服务器IP</label>
        <div class="layui-input-inline">
            <input type="text" name="serverIp" lay-verify="required" placeholder="请输入" autocomplete="off" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">服务器OS</label>
        <div class="layui-input-inline">
            <input type="text" name="serverOs" lay-verify="required" placeholder="请输入" autocomplete="off" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">监控项目</label>
        <div class="layui-input-block monitor">
            <input type="checkbox" name="monitor[cpu]" title="CPU使用率" value="1">
            <input type="checkbox" name="monitor[mem]" title="内存占用率" value="1">
            <input type="checkbox" name="monitor[dis]" title="磁盘可用空间" value="1">
            <input type="checkbox" name="monitor[task]" title="正在运行的进程数" value="1">
            <input type="checkbox" name="monitor[online]" title="在线人数" value="1">
            <input type="checkbox" name="monitor[port]" title="80端口连接数" value="1">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">CPU占用率报警值</label>
        <div class="layui-input-inline">
            <input type="text" name="cpuCaution" lay-verify="required" placeholder="请输入" autocomplete="off" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">内存占用率报警值</label>
        <div class="layui-input-inline">
            <input type="text" name="memCaution" lay-verify="required" placeholder="请输入" autocomplete="off" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">记录频率</label>
        <div class="layui-input-inline">
            <select name="recordFrequency">
                <option value="1"selected="">一分钟</option>
                <option value="2">两分钟</option>
                <option value="5">五分钟</option>
                <option value="10">十分钟</option>
                <option value="20">二十分钟</option>
            </select>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">记录模式</label>
        <div class="layui-input-block">
            <input type="checkbox" name="recordMode" lay-skin="switch" title="开关">
        </div>
    </div>
    <div><input type="hidden" name="createTime" value="<?php echo @date('Y-m-d H:m:s',time());?>"></div>
    <div class="layui-form-item">
        <div class="layui-input-block">
            <button class="layui-btn layui-btn-warm" id="subsettings">保存配置</button>
            <button type="reset" class="layui-btn layui-btn-primary">重置</button>
        </div>
    </div>
</form>

<fieldset class="layui-elem-field">
    <legend>我的配置</legend>
    <div class="layui-field-box">
<!--丢失-->
<!--分页显示-->
<!--修改功能-->
            <button ></button>

<!--当选择某一个配置的时候，将status改为1，并在ob.php界面读取配置-->
        <?php
        $myslqi = createDbObj();
        $pr = doSettingSelect($myslqi,Session::get());
        while($res = $pr->fetch_assoc()){
            echo "<button class=\"layui-btn layui-btn-normal recordtype\"  data-id=".$res['id'].">";
            echo $res['serverName'];
            echo "</button>";
        }
        closeMysql($myslqi);
        ?>
    </div>
</fieldset>


<button class="layui-btn layui-btn-warm confirm" >确认选择</button>

<script>
    layui.use(['form','element','layer','layedit'], function(){
        var $id;

        $('#subsettings').click(function () {
            var $url = 'doSettingAjax.php';
            //如何将空值以0的数值传过来。
            var $param = $('form').serialize();
            //do something 判断非法值无法提交
            alert($param);
            $.ajax({
                url:$url,
                data:$param,
                dataType:'json',
                type:'post',
                success:function (data) {
                    alert(data.info);
                },
                error:function () {
                    alert('操作失败');
                }
            })
         })


        $('.confirm').click(function () {
            //获取被选中的按钮的 data-id的id值 将本条数据的status改为1
            //在ob.php界面读取status=1的记录，并且在记录停止的时候讲status的状态变为0
            if($id){
                var $url = 'doConfirmAjax.php';
                var $param = {'id':$id};
                $.ajax({
                    url:$url,
                    data:$param,
                    dataType:'json',
                    type:'post',
                    success:function (data) {
                        alert(data.info);
                        window.location.href='../ob.php';
                    },
                    error:function () {
                        alert('操作失败');
                    }
                })
            }else{
                alert('请先选择一种监测模式');
            }



        })

        $('.recordtype').click(function () {
            $id = $(this).data('id');
            console.log($id);
        })

});
</script>


</body>
</html>