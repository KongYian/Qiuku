<?php
/**
 * Created by PhpStorm.
 * User: tao
 * Date: 2016/11/20
 * Time: 下午7:18
 */
echo phpinfo();

function getInfo()
{
    //获取服务器80端口请求数目
    $connect_num = shell_exec('netstat -nat|grep -i "80"|wc -l');
    //所有连接到本机80端口的IP地址和其连接数。80端口一般是用来处理HTTP网页请求
    $fp = popen('netstat -plan|grep :80|awk {\'print $5\'}|cut -d: -f 1|sort|uniq -c|sort -nk 1', "r");
    $online_num = 0;
//    $ip_count = 0;
    while (!feof($fp)) {
        $rs = fgets($fp, 1024);
//        preg_match("/\d/",$rs,$hi);
//        $online_num += $hi[0];
        if(!feof($fp)){
            $online_num += 1;
        }
    }
    pclose($fp);

    //获取某一时刻系统cpu和内存使用情况
    $fp = popen('top -b -n 2 | grep -E "^(%Cpu|KiB Mem|Tasks)"', "r");
    $rs = "";
    while (!feof($fp)) {
        $rs .= fread($fp, 1024);
    }
    pclose($fp);
    $sys_info = explode("\n", $rs);
    $tast_info = explode(",", $sys_info[3]);//进程 数组
    $cpu_info = explode(",", $sys_info[4]);  //CPU占有量  数组
    $mem_info = explode(",", $sys_info[5]); //内存占有量 数组

    //正在运行的进程数
    $tast_running = trim(trim($tast_info[1], 'running'));

    //CPU占有量
    $cpu_usage = trim(trim($cpu_info[3], '%Cpu(s): '), '%id');  //百分比
    $cpu_usage = 100 - $cpu_usage;

    //内存占有量
    $mem_total = trim(trim($mem_info[0], 'KiB Mem: '), 'k total');
    $mem_used = trim($mem_info[2], 'used');
    $mem_usage = round(100 * intval($mem_used) / intval($mem_total), 2);  //百分比

    /*硬盘使用率 begin*/
    $fp = popen('df -lh | grep -E "^(/)"', "r");
    $rs = fread($fp, 1024);
    pclose($fp);
    $rs = preg_replace("/\s{2,}/", ' ', $rs);  //把多个空格换成 “_”
    $hd = explode(" ", $rs);
    $hd_avail = trim($hd[3], 'G'); //磁盘可用空间大小 单位G
    $hd_usage = trim($hd[4], '%'); //挂载点 百分比
    /*硬盘使用率 end*/

    //检测时间 服务器时间
    $fp = popen("date +\"%Y-%m-%d %H:%M:%S\"", "r");
    $rs = fread($fp, 1024);
    pclose($fp);
    $detection_time = trim($rs);

    $res = array(
        'cpu_usage' => $cpu_usage,//CPU占用率
        'mem_usage' => $mem_usage,//内存占用率
        'hd_avail' => $hd_avail, //磁盘可用空间,单位G
        'hd_usage' => $hd_usage, //磁盘挂载点 百分比
        'tast_running' => $tast_running,   //正在运行的进程数
        'detection_time' => $detection_time,//服务器时间
        'connect_num' => $connect_num,//80端口请求数目
        'online_num' => $online_num, //连接到80端口的独立ip总数,在线人数
    );
    return $res;
}