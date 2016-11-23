<?php
/**
 * Session控制类
 */

session_start();
class Session{
    const NAME = 'admininfo';
    //用数学表达式在线上会报错 60 * min 60*60*24
    const EXPIRE = 86400;
    /**
     * 设置session
     * @param String $name   session name
     * @param Mixed  $data   session data
     * @param Int    $expire 超时时间(秒)
     */
    public static function set($data,$name=Session::NAME, $expire=Session::EXPIRE){
        $session_data = array();
        $session_data['data'] = $data;
        $session_data['expire'] = time()+$expire;
        $_SESSION[$name] = $session_data;
    }

    /**
     * 读取session
     * @param  String $name  session name
     * @return Mixed
     */
    public static function get($name=Session::NAME){
        if(isset($_SESSION[$name])){
            if($_SESSION[$name]['expire']>time()){
                return $_SESSION[$name]['data'];
            }else{
                self::clear($name);
            }
        }
        return false;
    }

    /**
     * @param string $name
     */
    public static function checkSession($path='index.php',$name=Session::NAME){
        if(!($_SESSION[$name])){
            echo "<script>
                 alert('请先登录');
                 window.location.href='$path';
                </script>";
        }elseif(time() - $_SESSION[$name]['expire'] > Session::EXPIRE){
            echo "<script>
                  alert('网页超时，请重新登录');
                  window.location.href='$path';
                </script>";
            self::clear();
        }
    }

    /**
     * 清除session
     * @param  String  $name  session name
     */
    private static function clear($name=Session::NAME){
        unset($_SESSION[$name]);
    }

    public static function logout(){
        self::clear();
    }


}

