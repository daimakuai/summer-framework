<?php
defined('IN_FRAME') or exit('Access Denied');

/**
 * 数据库操作类
 */
class mysql {
    public static $querynum = 0;
    public static $link;

    /**
     * 数据库连接函数
     * @param  string  $con_db_host  主机地址
     * @param  string  $con_db_user    用户名
     * @param  string  $con_db_pass  密码
     * @param  string  $con_db_name  数据库名
     * @param  string  $db_charset   字符编码
     * @param  string  $pconnect     是否打开永久链接
     */
    public static function  dbconn($con_db_host,$con_db_user,$con_db_pass, $con_db_name = '', $con_db_port = '3306', $db_charset='utf8',$pconnect = 0) {
        self::$link = @ new mysqli($con_db_host, $con_db_user, $con_db_pass, $con_db_name, $con_db_port);
        if(self::$link->connect_error){
            self::halt($con_db_host);
        }


        if(self::version() > '4.1') {
            if(!$db_charset){
                $db_charset = 'utf8';
            }
            if($db_charset!='latin1') {
                self::$link->query("SET character_set_connection=$db_charset, character_set_results=$db_charset, character_set_client=binary");
            }

            if(self::version() > '5.0.1') {
                self::$link->query("SET sql_mode=''");
            }
        }

        if($con_db_name) {
            @self::$link->select_db($con_db_name);
        }

    }

    /**
     * 选择数据库
     * @param   string  $dbname     选择的数据库名
     * @return  bool                是否成功
     */
    public static function select_db($con_db_name) {
        return self::$link->select_db($con_db_name);
    }

    /**
     * 选择数据库
     *  $result  mysqli_result对象
     *	MYSQLI_ASSOC - 默认。关联数组
     *	MYSQLI_NUM - 数字数组
     *	MYSQLI_BOTH - 同时产生关联和数字数组
     *  @return  array  出巡结果数组
     */
    public static function fetch_array($result, $result_type = MYSQLI_ASSOC) {
        if ($result instanceof mysqli_result) {
            return $result->fetch_array($result_type);
        }else{
            self::errno();
        }
        #return mysql_fetch_array($query,$result_type);
    }

    /**
     * 获取一条数据
     * @param   string  $sql      select sql语句
     * @param   string  $type     为UNBUFFERED时，不获取缓存结果
     * @return  array             返回执行sql语句后查询到的数据
     */
    public static function get_one($sql, $type = ''){
        $result = self::query($sql, $type);
        $rs = self::fetch_array($result);
        //如果是前台可视化编辑模式
        if(IN_ADMIN !== true && $_GET['pageset'] == 1){
            load::sys_class('view/ui_compile');
            $ui_compile = new ui_compile();
            $rs = $ui_compile->replace_sql_one($sql,$rs);
        }
        self::free_result($result);
        return $rs ;
    }

    /**
     * 获取多条数据
     * @param   string  $sql      select sql语句
     * @param   string  $type     为UNBUFFERED时，不获取缓存结果
     * @return  array             返回执行sql语句后查询到的数据
     *	MYSQLI_ASSOC - 默认。关联数组
     *	MYSQLI_NUM - 数字数组
     *	MYSQLI_BOTH - 同时产生关联和数字数组
     */
    /*public static func get_all($sql, $type = ''){
        $result = self::query($sql, $type);
        # 	MYSQLI_ASSOC - 默认。关联数组
        #	MYSQLI_NUM - 数字数组
        #	MYSQLI_BOTH - 同时产生关联和数字数组

            if ($result instanceof mysqli_result) {
            $rs = $result->fetch_all(MYSQLI_ASSOC);
        }else{
            self::error();
        }
        //如果是前台可视化编辑模式
        if(IN_ADMIN !== true && $_GET['pageset'] == 1){
            load::sys_class('view/ui_compile');
            $ui_compile = new ui_compile();
            $rs = $ui_compile->replace_sql_all($sql,$rs);
        }
        return $rs ;
    }*/
    public static function get_all($sql, $type = ''){
        $result = self::query($sql, $type);
        # 	MYSQLI_ASSOC - 默认。关联数组
        #	MYSQLI_NUM - 数字数组
        #	MYSQLI_BOTH - 同时产生关联和数字数组
        if ($result instanceof mysqli_result) {
            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                $rs[]=$row;
            }
        }else{
            self::error();
        }
        //如果是前台可视化编辑模式
        if(IN_ADMIN !== true && $_GET['pageset'] == 1){
            load::sys_class('view/ui_compile');
            $ui_compile = new ui_compile();
            $rs = $ui_compile->replace_sql_all($sql,$rs);
        }
        return $rs ;
    }

    /**
     * 执行数据库语句
     * @param   string  $sql      insert、update等 sql语句
     * @param   string  $type     为UNBUFFERED时，不获取缓存结果
     * @return                    返回执行sql执行后的结果
     */
    /*public static func queryold($sql, $type = '') {
       $func = $type == 'UNBUFFERED' && @function_exists('mysql_unbuffered_query') ?
            'mysql_unbuffered_query' : 'mysql_query';
        if(!($query = $func($sql, self::$link))) {
            if(in_array(self::errno(), array(2006, 2013)) && substr($type, 0, 5) != 'RETRY') {
                self::close();
                $db_settings = parse_ini_file(PATH_WEB.'config/config_db.php');
                @extract($db_settings);
                self::dbconn($con_db_host,$con_db_id,$con_db_pass, $con_db_name = '',$pconnect);
                self::query($sql, 'RETRY'.$type);
            }
        }
        self::$querynum++;
        return $query;
    }*/

    /**
     * @param string $sql sql语句
     * @return mixed mysqli_result对象
     */
    public static function query($sql) {
        #$sql1  = "SELECT * FROM met_lang ORDER BY no_order";
        if( !$result = self::$link->query($sql)){
            self::errno();
        }
        return $result;
    }

    /**
     * 获取指定条数数据
     * @param   string  $table       表名称
     * @param   string  $where       where条件
     * @param   string  $order       order条件
     * @param   string  $limit_start 开始条数
     * @param   string  $limit_num   取条数数量
     * @param   string  $field_name  获取的字段
     * @return  array                查询得到的数据
     */
    function get_data($table, $where , $order, $limit_start = 0, $limit_num = 20, $field_name = '*')
    {
        if($limit_start < 0){
            return false;
        }
        $limit_start = $limit_start ? $limit_start : 0;
        $where = str_ireplace("WHERE", "", $where);
        $order = str_ireplace("ORDER BY", "", $order);
        if($where){
            $conds .= " WHERE {$where} ";
        }
        if($order){
            $conds .= " ORDER BY {$order} ";
        }

        $conds .= " LIMIT {$limit_start},{$limit_num}";
        $query = "SELECT {$field_name} FROM {$table} {$conds}";
        $data = DB::get_all($query);
        if($data){
            return $data;
        }else{
            if($limit_start == 0){
                return $data;
            }else{
                return false;
            }
        }

    }

    /**
     * 统计条数
     * @param   string  $table_name      insert、update等 sql语句
     * @param   string  $where_str       where条件,建议添加上WEHER
     * @param   string  $field_name      统计的字段
     * @return  int                      统计条数
     */
    public static function counter($table_name,$where_str="", $field_name="*"){
        $where_str = trim($where_str);
        if(strtolower(substr($where_str,0,5))!='where' && $where_str){
            $where_str = "WHERE ".$where_str;
        }
        $query = " SELECT COUNT($field_name) FROM $table_name $where_str ";
        $result = self::query($query);
        if ($result instanceof mysqli_result) {
            $fetch_row = $result->fetch_row();
        }else{
            self::error();
        }
        return $fetch_row[0];

        /*$fetch_row = mysql_fetch_row($result);
        return $fetch_row[0];*/
    }

    /**
     * 返回前一次 MySQL 操作所影响的记录行数。
     * @param   string  $dbname     选择的数据库名
     * @return  int                 执行成功，则返回受影响的行的数目，如果最近一次查询失败的话，函数返回 -1。
     */
    public static function affected_rows() {
        return self::$link->affected_rows;
        #return mysql_affected_rows(self::$link);
    }

    /**
     * 返回上一个 MySQLI 操作产生的文本错误信息
     * @return  string                    错误信息
     */
    public static function error() {
        return self::$link->error;
    }

    /**
     * 返回上一个 MySQLI 操作中的错误信息的数字编码
     * @return  string  错误信息的数字编码
     */
    public static function errno() {
        return self::$link->errno;
    }

    /**
     * 返回上一个 MySQLI 操作中的错误信息的数字编码
     * @return  array  错误信息列表
     */
    public static function errorlist()
    {
        return self::$link->error_list;
    }

    /**
     * 返回结果集中一个字段的值
     * @param        $query 规定要使用的结果标识符。该标识符是 mysql_query() 函数返回的。
     * @param    int $row   规定行号。行号从 0 开始。
     * @return              结果集中一个字段的值
     */
    public static function result($query, $row) {
        die("method disable");
    }

    /**
     * 返回查询的结果中行的数目
     * @param        $result 规定要使用的结果标识符。该标识符是 mysqli_query() 函数返回的。
     * @return       int    行数
     */
    public static function num_rows($result) {
        if ($result instanceof mysqli_result) {
            return $result->num_rows;
        } else {
            self::errno();
        }
    }

    /**
     * 返回查询的结果中字段的信息
     * @param $result 规定要使用的结果标识符。该标识符是 mysqli_query() 函数返回的。
     * @return mixed  字段数组
     */
    public static function fields($result) {
        if ($result instanceof mysqli_result) {
            return $result->fetch_fields();
        } else {
            self::errno();
        }
    }


    /**
     * 返回查询的结果中字段的数目
     * @param        $result 规定要使用的结果标识符。该标识符是 mysqli_query() 函数返回的。
     * @return       int    字段数
     */
    public static function num_fields($result) {
        if ($result instanceof mysqli_result) {
            return $result->field_count;
        } else {
            self::errno();
        }
    }

    /**
     * 释放结果内存
     * @param        $result 规定要使用的结果标识符。该标识符是 mysqli_query() 函数返回的。
     */
    public static function free_result($result) {
        if ($result instanceof mysqli_result) {
            return $result->free();
        } else {
            self::errno();
        }
    }

    /**
     * 返回上一步 INSERT 操作产生的 ID
     * @return       int    id号
     */
    public static function insert_id() {
        return self::$link->insert_id;
    }

    /**
     * 从结果集中取得一行作为数字数组
     * @param        $result myslqi_result对象。
     * @return       array    结果集一行数组
     */
    public static function fetch_row($result) {
        if ($result instanceof mysqli_result) {
            return $result->fetch_row ();
        }else{
            self::errno();
        }
    }

    /**
     * 返回mysql服务器信息
     */
    public static function version() {
        return @self::$link->server_info;
    }

    /**
     * 关闭连接
     */
    public static function close() {
        return @self::$link->close ();
    }

    /**
     * 无法连接数据库报错
     */
    public static function halt($dbhost) {
        $sqlerror = self::$link->error;
        $sqlerrno = self::$link->connect_error;
        $sqlerror = str_replace($dbhost,'dbhost',$sqlerror);

        header('HTTP/1.1 500 Internal Server Error');
        echo"<html><head><title>MyFrame</title><style type='text/css'>P,BODY{FONT-FAMILY:tahoma,arial,sans-serif;FONT-SIZE:10px;}A { TEXT-DECORATION: none;}a:hover{ text-decoration: underline;}TD { BORDER-RIGHT: 1px; BORDER-TOP: 0px; FONT-SIZE: 16pt; COLOR: #000000;}</style><body>\n\n";
        echo"<table style='TABLE-LAYOUT:fixed;WORD-WRAP: break-word'><tr><td>";
        echo"<br><br><b>The URL Is</b>:<br>http://$_SERVER[HTTP_HOST]$REQUEST_URI";
        echo"<br><br><b>Can not connect to MySQL server</b>:<br>$sqlerror  ( $sqlerrno )";
        echo"<br><br><b>You Can Get Help In</b>:<br><b>http://www.MetInfo.cn</b>";
        echo"</td></tr></table>";
        exit;
    }



}

?>