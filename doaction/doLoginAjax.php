<?php
/**
 * Created by PhpStorm.
 * User: tao
 * Date: 2016/11/16
 * Time: 下午5:26
 */
require_once 'Session.class.php';
require_once "mysqlConfig.php";
header("Content-type:text/html;charset=utf-8");
$name = trim($_POST['adminName']);
$pwd = trim($_POST['adminPwd']);
if($name && $pwd){
    $mysqli = createDbObj();
    $password = doCheckAdmin($mysqli,$name);
    if($password == null){
        $out = array(
            'info'=>'用户不存在,请重新登录'
        );
    }else{
        if($pwd == $password){
            Session::set($name);
            $out = array(
                'status'=>1,
                'info'=>'登录成功,即将自动跳转',
            );
        }else{
            $out = array
            (
                'status'=>0,
                'info'=>'密码错误,请重新登录'
            );
        }
    }
}
echo json_encode($out);


