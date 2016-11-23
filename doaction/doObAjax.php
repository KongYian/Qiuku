<?php
/**
 * Created by PhpStorm.
 * User: tao
 * Date: 2016/11/10
 * Time: 下午4:33
 */
require_once 'mysqlConfig.php';
require_once 'getServerInfo.php';

//记录配置的id
$sid = $_POST['sid'];
//操作用户的id
$uid = $_POST['uid'];
//记录模式的频率
$rFrequency = $_POST['rFrequency'];
//记录模式是否开启的标志
$rMode = $_POST['rMode'];
//数据的状态 1，2，3
$sflag = $_POST['sflag'];
//目的服务器的ip地址
$serverip = $_POST['serverIp'];
//获取服务器的状态信息
$serverInfo = getInfo();
//组装发往前台的信息
$settingInfo = doObRead($sid,$uid);
$needInfo = array();
if($settingInfo['watchCpu']){
    $needInfo['cpu_usage'] = $serverInfo['cpu_usage'];
}
if($settingInfo['watchMem']){
    $needInfo['mem_usage'] = $serverInfo['mem_usage'];
}
if($settingInfo['watchDis']){
    $needInfo['hd_avail'] = $serverInfo['hd_avail'];
}
if($settingInfo['watchTask']){
    $needInfo['tast_running'] = $serverInfo['tast_running'];
}
if($settingInfo['watchOnline']){
    $needInfo['connect_num'] = $serverInfo['connect_num'];
}
if($settingInfo['watchPort80']){
    $needInfo['online_num'] = $serverInfo['online_num'];
}
$needInfo['detection_time'] = $serverInfo['detection_time'];

$record = doRecordSelect($sid,$uid);
//echo json_encode($record);exit;
if($rMode == 1){
    //开启了记录模式
//    $sql_status = $record['sql_status'];
    if($sflag == 1) {
        //数据开始记录的标志
        if ($record['sql_status'] == '' || $record['sql_status'] == 3) {
            //记录正常运转情况
            doRecordInsert($serverip, $serverInfo, $sid, $uid, 1, 0);
            //插入记录的第一条
    }else{
            //最后的一条记录为1，2
            //1: 为脏数据，将其删除，插入本次第一条数据
            //2: 将其改为3 并插入
            if($record['sql_status'] == 1){
                doRecordDelete($sid,$uid);
            }elseif($record['sql_status'] == 2){
                doRecordUpdate($sid,$uid);
            }
            doRecordInsert($serverip,$serverInfo,$sid,$uid,1);
        }
        //无论是哪一种模式、都需要涉及到信息的输出
        echo json_encode($needInfo);
    }
    elseif($sflag == 2){
        $lasttime = strtotime($record['detection_time']);//最新一条数据的时间戳
        $nowtime = strtotime($serverInfo['detection_time']);//本次服务器返回的时间戳
        if($record['sql_status'] == 1 || $record['sql_status'] == 2){
            if(($nowtime - $lasttime) > ($rFrequency*60)){
                doRecordInsert($serverip,$serverInfo,$sid,$uid,2);//插入正常数据的数据
            }
        }else{
            //3
            //do somethings
        }
        //无论是哪一种模式、都需要涉及到信息的输出
        echo json_encode($needInfo);
    }elseif($sflag == 3){
        //数据结束记录的标志
        //请求结束
        //意外结束
        if($record['sql_status'] == 1){
            doRecordDelete($sid,$uid);
            $out = array(
                'info' => '无效的记录，已停止监测'
            );
          }elseif ($record['sql_status'] == 2){
            doRecordUpdate($sid,$uid);
            $out = array(
                'info' => '记录成功，已停止监测'
            );
        }elseif ($record['sql_status'] == 3){
            //未知的结束标志
            $out = array(
                'info'=>'记录错误！',
            );
        }
        echo json_encode($out);
    }
}else{
    //浏览模式,也涉及到三种状态，可否进行省略操作。
    if($sflag == 1){
        //do somethings
        //无论是哪一种模式、都需要涉及到信息的输出
        echo json_encode($needInfo);
    }elseif ($sflag == 2){
        //do somethings
        //无论是哪一种模式、都需要涉及到信息的输出
        echo json_encode($needInfo);
    }elseif ($sflag == 3){
        $out = array(
            'info'=>'监测已停止',
        );
        echo json_encode($out);
    }
}

