<?php
//引入配置文件
try{
    require '../../app/Core/lib/config.php';
    //加载函数库
    include '../../app/Core/lib/functionLib.php';
    //加载函数库
    include '../../app/Core/lib/Loader.class.php';
    //  加载PHP插件
    include '../../app/Core/lib/PHPExcel/PHPExcel.php';
    include '../../app/Core/lib/PHPExcel/PHPExcel/IOFactory.php';
    include '../../app/Core/lib/PHPExcel/PHPExcel/Writer/Excel5.php';
    include '../../app/Core/lib/qrcode/phpqrcode.php';
    include '../../app/Core/lib/phpthumb/ThumbLib.inc.php';

    spl_autoload_register(array('Loader', 'autoload'));

//从url获取信息    验证登录
    $urlString = $_SERVER['PATH_INFO'];
//if (validationData('HTTP_LOGINCODE', $_SERVER)){
//    if ($_SERVER['HTTP_LOGINCODE'] != 'NotLoggedIn'){
//        $login = new ServiceDLoginTime();
//        $result = $login ->verifyLogin();
//        if (!is_bool($result)){
//            echo $result;
//            exit;
//        }
//    }
//}else{
//    echo jsonEncode(array('success'=>'loginOut', 'msg'=>'缺少登录标识'));
//    exit;
//}
    if(substr_count($urlString, '/') == 3 ){
        $urlArr = explode('/', substr($urlString,1));
        $module = $urlArr[0]; //模块
        $className = ucfirst($urlArr[1]); //控制器
        $className = $module."\\".$className;//模块/类名 eg:addressBook/CtrlSysPosition
        $action = $urlArr[2];// 方法
    } else if(substr_count($urlString, '/') == 2 ){
        $urlArr = explode('/', substr($urlString,1));
        $className = ucfirst($urlArr[0]); //控制器
        $action = $urlArr[1];// 方法
    } else {
        echo '';
        exit;
    }

    //判断控制器是否存在，控制器对应的文件是否存在
    $cPath = APP_PATH . "controller/" . $className . ".class.php";
    if (!is_file($cPath)) {
        echo $cPath;
        exit;
    }

    //实例化控制器类
    $className = "\\app\\controller\\" . $className;
    $refl = new ReflectionClass($className);
    $instance = $refl->newInstance();

    if (!method_exists($instance, $action)) {
        \app\Core\lib\MyLog::writeLog('action不存在', $action);
        $action = DEFAULT_ACTION;
    }
    $instance->$action();
}
catch (Throwable  $error) {
    if (function_exists('writePHPErrorLog')){
        writePHPErrorLog("", $error->getCode(), $error->getMessage(), $error->getFile(), $error->getLine());
    }else{
        http_response_code(500);
    }
}


?>