<?php
/**
 * Created by PhpStorm.
 * User: tao
 * Date: 2016/11/19
 * Time: 下午11:39
 */
require_once 'mysqlConfig.php';
$id = $_POST['id'];

if(doSettingConfirm($id)){
    $out = array('info'=>'选择成功');
    echo json_encode($out);
}


