<?php
/**
 * Created by PhpStorm.
 * User: tao
 * Date: 2016/11/10
 * Time: 上午10:51
 */

define("MONITORED_IP", "115.29.249.213");  //被监控的服务器IP地址  也就是本机地址
define("DB_SERVER", "qdm167381108.my3w.com");       //存放数据的服务器IP地址
define("DB_USER", "qdm167381108");
define("DB_PWD","kongwt1994");
define("DB_NMAE","qdm167381108_db");
date_default_timezone_set('Asia/Shanghai');


//define("MONITORED_IP", "115.29.249.213");  //被监控的服务器IP地址  也就是本机地址
//define("DB_SERVER", "127.0.0.1");       //存放数据的服务器IP地址
//define("DB_USER", "root");
//define("DB_PWD","123123");
//define("DB_NMAE","ob");

//$out = doRecordSelect();
//var_dump($out);


function createDbObj(){
    $mysqli = new mysqli(DB_SERVER,DB_USER,DB_PWD,DB_NMAE) or die('connect fail');
    $mysqli->select_db(DB_NMAE);
    $mysqli->set_charset('utf8');
    return $mysqli;
}


function doRecordInsert($serverip,$status,$sid,$uid,$sql_status=2){
    $mysqli = createDbObj();
    $sql = "insert into hi(ip,sid,uid,cpu_usage,mem_usage,hd_avail,hd_usage,tast_running,detection_time,connect_num,sql_status,online_num)";
    $sql .= " VALUES('".$serverip."','".$sid."','".$uid."','".$status['cpu_usage']."','".$status['mem_usage']."','".$status['hd_avail']."','".$status['hd_usage']."','".$status['tast_running']."','".$status['detection_time']."','".$status['connect_num']."','".$sql_status."','".$status['online_num']."')";
    $mysqli->query($sql);
    closeMysql($mysqli);
}

function doRecordSelect($sid,$uid){
    $mysqli = createDbObj();
    $sql = " SELECT * FROM `hi`  WHERE `sid`='".$sid."' AND `uid`='".$uid."' ORDER BY `id` DESC LIMIT 1";
    $pr =$mysqli->query($sql);
    $res = $pr->fetch_assoc();
    closeMysql($mysqli);
    return $res;
}

function doRecordUpdate($sid,$uid){
    $mysqli = createDbObj();
    $sql = "UPDATE `hi` SET `sql_status`= 3  WHERE `sid`='".$sid."' AND `uid`='".$uid."' ORDER BY `id` DESC LIMIT 1";
    $mysqli->query($sql);
    closeMysql($mysqli);
}

function doRecordDelete($sid,$uid){
    $mysqli = createDbObj();
    $sql = "DELETE FROM `hi` WHERE `sid`='{$sid}' AND `uid`='".$uid."' ORDER BY `id` DESC LIMIT 1";
    $mysqli->query($sql);
    closeMysql($mysqli);
}

function doCheckAdmin($mysqli,$adminname){
    $sql = "SELECT * FROM `adminInfo` WHERE `adminName` = '$adminname'";
    $pr = $mysqli->query($sql);
    if($pr){
        $res = $pr->fetch_assoc();
        $pwd = $res['adminPwd'];
    }else{
        $pwd = null;
    }
    return $pwd;
}

function doSelectAdmin($mysqli,$adminName){
    $sql = "SELECT * FROM `adminInfo` WHERE `adminName`='$adminName'";
    $pr = $mysqli->query($sql);
    $res= $pr->fetch_assoc();
    return $res;
}

function doSettingInsert($mysqli,$setting){
    $sql = "insert into `watchSettings`(`uid`,`serverName`,`serverIp`,`serverOs`,`watchCpu`,`watchMem`,`watchDis`,`watchTask`,`watchOnline`,`watchPort80`,`recordFrequency`,`recordMode`,`createTime`,`cpuCaution`,`memCaution`)";
    $sql .= " VALUES({$setting['uid']},'{$setting['serverName']}','{$setting['serverIp']}','{$setting['serverOs']}',{$setting['watchCpu']},{$setting['watchMem']},{$setting['watchDis']},{$setting['watchTask']},{$setting['watchOnline']},{$setting['watchPort80']},{$setting['recordFrequency']},{$setting['recordMode']},'{$setting['createTime']}',{$setting['cpuCaution']},{$setting['memCaution']})";
    return $mysqli->query($sql);
}

function doSettingSelect($mysqli,$adminName){
    $name = doSelectAdmin($mysqli,$adminName);
    $uid = $name['uid'];
    $sql = "SELECT * FROM `watchSettings` WHERE `uid` = '".$uid."' ORDER  BY `id` DESC ";
    $pr = $mysqli->query($sql);
//    $res = $pr->fetch_assoc();
    return $pr;
}

function doSettingConfirm($id){
    $mysqli = createDbObj();
    $sql_u = "UPDATE `watchSettings` SET `status`=0 WHERE `status`=1";
    $mysqli->query($sql_u);
    $sql = "UPDATE `watchSettings` SET `status`=1 WHERE `id`='".$id."' ";
    $res = $mysqli->query($sql);
    closeMysql($mysqli);
    return $res ;
}

function doSettingRead($adminName){
    $mysqli = createDbObj();
    $res = doSelectAdmin($mysqli,$adminName);
    $uid = $res['uid'];
    $sql = "SELECT `id`,`uid`,`serverIp`,`watchCpu`,`watchMem`,`watchDis`,`watchTask`,`watchOnline`,`watchPort80`,`cpuCaution`,`memCaution`,`recordFrequency`,`recordMode` FROM `watchSettings` WHERE `status`=1 AND `uid`='".$uid."' ";
    $pr = $mysqli->query($sql);
    $res = $pr->fetch_assoc();
    closeMysql($mysqli);
    return $res;
}

function doObRead($sid,$uid){
    $mysqli = createDbObj();
    $sql = "SELECT `watchCpu`,`watchMem`,`watchDis`,`watchTask`,`watchOnline`,`watchPort80` FROM `watchSettings` WHERE id='".$sid."' AND uid='".$uid."' ";
    $pr = $mysqli->query($sql);
    $res = $pr->fetch_assoc();
    closeMysql($mysqli);
    return $res;
}

function closeMysql($mysqli){
    $mysqli->close();
}

