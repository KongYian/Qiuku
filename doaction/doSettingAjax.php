<?php
/**
 * Created by PhpStorm.
 * User: tao
 * Date: 2016/11/18
 * Time: 下午2:25
 */

require_once "mysqlConfig.php";
require_once 'Session.class.php';

//阿里云...192.168.1.1...linux...Array...5...on

$mysqli = createDbObj();

$adminName = Session::get();
$admininfo = doSelectAdmin($mysqli,$adminName);

$monitor = $_POST['monitor'];
$setting = array();
$setting['uid'] = $admininfo['uid'];
$setting['serverName'] =$_POST['serverName'];
$setting['serverIp'] =$_POST['serverIp'];
$setting['serverOs'] =$_POST['serverOs'];
$setting['watchCpu'] = $monitor['cpu']?1:0;
$setting['watchMem'] = $monitor['mem']?1:0;
$setting['watchDis'] = $monitor['dis']?1:0;
$setting['watchTask'] = $monitor['task']?1:0;
$setting['watchOnline'] = $monitor['online']?1:0;
$setting['watchPort80'] = $monitor['port']?1:0;
$setting['recordFrequency'] =$_POST['recordFrequency'];
$setting['recordMode'] =$_POST['recordMode']?1:0;
$setting['createTime'] =$_POST['createTime'];
$setting['cpuCaution'] =$_POST['cpuCaution']?$_POST['cpuCaution']:0;
$setting['memCaution'] =$_POST['memCaution']?$_POST['memCaution']:0;

if(doSettingInsert($mysqli,$setting)){
    $out = array('info'=>'提交成功');
}
closeMysql($mysqli);
echo json_encode($out);



