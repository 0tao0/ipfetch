<?php
/**
 * Created by PhpStorm.
 * User: zhongjian
 * Date: 16/3/23
 * Time: 下午4:43
 */

class Db{
    private static $config = array();

    private static $connection = null;

    function __construct()
    {
        self::$config = array(
            "host" => "10.106.4.86",
            "dbname" => "ip",
            "username" => "ip_list",
            "password" => "2RZH7Q2QVnM7uwqL",
        );
    }

    /**
     * @param array $config
     */
    public static function connection(array $config = array())
    {
        if($config && self::$config != $config) self::$config = $config;

        if(self::$connection) return true;

        $dsn = 'mysql:host='.self::$config['host'].';dbname='.self::$config['dbname'];
        $pdo = new PDO($dsn,self::$config['username'],self::$config['password']);

        if($pdo)
        {
            $pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
            $pdo->query('set names utf8');
            self::$connection = $pdo;
            return true;
        }else{
            return false;
        }
    }

    /**
     * @param array $data
     */
    public static function insert(array $data)
    {
        if(!$data || !self::$connection) return false;

        $sql = "insert into ip_list ";
        $sth = self::$connection->prepare($sql);
        $sth->bindValue(':calories', $calories, PDO::PARAM_INT);
        $sth->bindValue(':colour', $colour, PDO::PARAM_STR);
        $sth->execute();

        self::$connection->bindValue();
    }

}