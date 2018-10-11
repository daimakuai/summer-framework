<?php
defined('IN_FRAME') or exit('Access Denied');

class loader{
    private static $myclass = array();

    public static function _load($name, $type=CLASS_NAME, $path=""){

        if(!empty($path)){
            $file = $path . $name ;
        }else{
            $file = LIB_ROOT . '/'.$type.'/' . $name ;
        }

        if(file_exists($file.'.'.$type.'.'.PHP_FILE_NAME)){
            require_once $file.'.'.$type.'.'.PHP_FILE_NAME;
        } else {
            echo str_replace(SYS_ROOT, '', $path).$name.'.'.$type.'.'.PHP_FILE_NAME.' is not exists';
            exit;
        }

    }


    public static function _class($name, $path=""){

        if(!empty($path)){
            $file = $path . $name ;
        }else{
            $file = LIB_ROOT . '/'.CLASS_NAME.'/' . $name ;
        }

        if(file_exists($file.'.'.CLASS_NAME.'.'.PHP_FILE_NAME)){
            require_once $file.'.'.CLASS_NAME.'.'.PHP_FILE_NAME;
        } else {
            echo str_replace(SYS_ROOT, '', $path).$name.'.class.'.PHP_FILE_NAME.' is not exists';
            exit;
        }

    }


    public static function _fun($name, $path=""){

        if(!empty($path)){
            $file = $path . $name ;
        }else{
            $file = LIB_ROOT . '/'.FUNCTION_NAME.'/' . $name ;
        }

        if(file_exists($file.'.'.FUNCTION_NAME.'.'.PHP_FILE_NAME)){
            require_once $file.'.'.FUNCTION_NAME.'.'.PHP_FILE_NAME;
        } else {
            echo str_replace(SYS_ROOT, '', $path).$name.'.'.FUNCTION_NAME.'.'.PHP_FILE_NAME.' is not exists';
            exit;
        }

    }

    public static function _run($module, $action, $do){

        $file = APP_ROOT . $module.'/' . $action ;

        if(file_exists($file.'.'.PHP_FILE_NAME)){
            require_once $file.'.'.PHP_FILE_NAME;
        } else {
            echo $file.'.'.PHP_FILE_NAME.' is not exists!';
            exit;
        }

        if (!class_exists($action)) {
            die($action . ' ' . $do . ' class\'s file is not exists!!!');
        }

        if(method_exists($action, $do)){
            $newclass = new $action;
            if(isset(self::$myclass[$action])){
                $newclass = self::$myclass[$action];
            }else{
                self::$myclass[$action] = $newclass;
            }
            call_user_func(array($newclass, $do));

        }else{
            die($do.' function is not exists!');
        }

    }

}