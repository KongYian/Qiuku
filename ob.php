<?php
require_once 'doaction/Session.class.php';
require_once 'doaction/mysqlConfig.php';
Session::checkSession();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>OB信息监测</title>
    <link rel="stylesheet" href="layui/css/mystyle.css">
    <link rel="stylesheet" href="layui/css/layui.css">
    <script src="layui/jquery-3.1.1.min.js"></script>
    <script src="layui/layui.js"></script>
</head>
<body>
<div id="content">
    <a class="back" href=""></a>
    <span class="scroll"></span>
    <h1>信息监测</h1>
    <div id="admininfo">
        <ul class="layui-nav layui-nav-tree">
            <li class="layui-nav-item">
                <a href="javascript:;"><?php echo "hello,".Session::get();?>
                </a>
                <dl class="layui-nav-child">
                    <dd><a href="doaction/settings.php">监测配置</a></dd>
<!--                    <dd><a href="doaction/logout.php">退出登录</a></dd>-->
                    <dd><a href="doaction/logout.php" id="logout">退出登录</a></dd>
                </dl>
            </li>
        </ul>
    </div>
    <p class="head">
    </p>
    <div id="controller">
        <div id="btn">
            <button id="start" class="layui-btn layui-btn-normal">开始监测</button>
            <button id="stop" class="layui-btn layui-btn-warm">停止监测</button>
        </div>
    </div>
    <p class="head">
        <fieldset class="layui-elem-field">
            <p>dddd</p>
        </fieldset>
    <p class="head">
    </p>
    <table class="table1">
        <thead>
        <tr>
           <!-- <th scope="col" abbr="Starter">
                请求总数
            </th>
            <th scope="col" abbr="Starter">
                CPU占用率
            </th>
            <th scope="col" abbr="Medium">
                内存占用率
            </th>
            <th scope="col" abbr="Business">
                在线人数
            </th>
            <th scope="col" abbr="Deluxe">
                时间
            </th>-->

            <?php
                $res = doSettingRead(Session::get());
                var_dump($res);
                if(@$res['watchCpu']){
                    echo " <th scope=\"col\" abbr=\"Deluxe\">Cpu占用率</th>";
                }
                if(@$res['watchMem']){
                    echo " <th scope=\"col\" abbr=\"Deluxe\">内存占用率</th>";
                }
                if(@$res['watchDis']){
                    echo " <th scope=\"col\" abbr=\"Deluxe\">磁盘占用率</th>";
                }
                if(@$res['watchTask']) {
                    echo " <th scope=\"col\" abbr=\"Deluxe\">进程数目</th>";
                }
                if(@$res['watchOnline']){
                    echo " <th scope=\"col\" abbr=\"Deluxe\">在线人数</th>";
                }
                if(@$res['watchPort80']){
                    echo " <th scope=\"col\" abbr=\"Deluxe\">80端口连接数</th>";
                }
                    echo " <th scope=\"col\" abbr=\"Deluxe\">时间</th>";
                echo $res['serverIp'];
            ?>
        </tr>
        </thead>
        <tbody class="showdata">
        </tbody>
    </table>
    <p class="head">
    </p>
</div>
</body>
<script>
    layui.use(['layer','element'], function(){
        //监测配置的选择
        var $sid=<?php echo @$res['id']=$res['id']?$res['id']:0; ?>;
        //用户的标示
        var $uid=<?php echo @$res['uid']=$res['uid']?$res['uid']:0 ?>;
        //是否开启监测
        var $recordFrequency = <?php echo @$res['recordFrequency']=$res['recordFrequency']?$res['recordFrequency']:0 ?>;
        var $recordMode = <?php echo @$res['recordMode']=$res['recordMode']?$res['recordMode']:0 ?>;
        var $serverIp ="<?php echo @$res['serverIp']=$res['serverIp']?$res['serverIp']:0 ?>";
        //控制重复开始
        var $start = 1;
        var timer;

        //启动监测
        $('#start').click(function () {
            if ($start == 1) {
                $start = 0;
                showData(1);
                layer.msg('监测开始', {icon: 6});
                setTimeout(timer,5000);
                timer = setInterval(function () {
                    showData(2);
                }, 5000);
            } else {
                layer.alert('请先终止监测，再重新开始',{icon:2});
            }
        })
        //停止监测
        $('#stop').click(function () {
            if($start == 0){
                $start = 1;
                layer.msg('正在停止...',{icon:6});
                clearInterval(timer);
                setTimeout(function () {
                    showData(3)
                }, 4000);
            } else {
                setTimeout(function(){
                    layer.msg('请先开始，再终止',{icon:2})
                },1000);
            }
        })

        //ajax具体实现异步提交
        function showData(swtichflag) {
            var $url = 'doaction/doObAjax.php';
            //将有可能的数字型字符串转换成数字
            var $param = {  'uid': $uid*1,
                            'sid': $sid*1,
                            'rFrequency':$recordFrequency*1,
                            'rMode':$recordMode*1,
                            'sflag':swtichflag,
                            'serverIp':$serverIp};
            console.log($param);
              $.ajax({
                type: 'post',
                data: $param,
                dataType: 'json',
                url: $url,
                success: function (data) {
//                    alert(data);
                    console.log(data);
                    if(swtichflag==2){
//                            console.log(data);
                            var $count = $('.showdata tr').length;
                            var str =
                                "<tr>"
                                + "<td class='cpu'>" + data.cpu_usage + "%" + "</td>"
                                + "<td class='cpu'>" + data.mem_usage + "%" + "</td>"
                                + "<td class='cpu'>" + data.hd_avail + "%" + "</td>"
                                + "<td class='cpu'>" + data.tast_running + "</td>"
                                + "<td class='cpu'>" + data.connect_num + "</td>"
                                + "<td class='cpu'>" + data.online_num + "</td>"
                                + "<td class='cpu'>" + data.detection_time + "</td>"
                                + "</tr>";
                            if ($count > 4) {
                                $('.showdata tr:last').remove();
                                $('.showdata').prepend(str);
                            } else {
                                $('.showdata').prepend(str);
                        }
                    }else if(swtichflag == 3) {
                        layer.msg(data.info, {icon: 6});
                    }
                },
                error: function () {
                    //出现任何异常的时候，强制停止程序
                    clearInterval(timer);
                    showData(3)
                    layer.alert('页面出错啦',{icon:1});
                }
            })
        }
    });
</script>
</html>