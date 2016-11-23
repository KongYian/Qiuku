<?php
/**
 * Created by PhpStorm.
 * User: tao
 * Date: 2016/11/17
 * Time: 下午4:54
 */


require_once 'Session.class.php';
Session::logout();
header("Location: ../index.php");
