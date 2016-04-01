<?php
/**
 * Created by PhpStorm.
 * User: zhongjian
 * Date: 16/3/23
 * Time: 下午4:43
 */

class Db{
    private $config = array();

    private $connection = null;

    private $columns = array();

    function __construct()
    {
        $this->config = array(
            "host" => "10.141.4.86",
            "dbname" => "ip",
            "username" => "ip_list",
            "password" => "2RZH7Q2QVnM7uwqL",
        );
        if(!$this->connection) $this->connection();
    }

    /**
     * @param array $config
     */
    public function connection(array $config = array())
    {
        if($config && self::$config != $config) $this->config = $config;

        if($this->connection) return true;

        $dsn = 'mysql:host='.$this->config['host'].';dbname='.$this->config['dbname'];
        $pdo = new PDO($dsn,$this->config['username'],$this->config['password']);

        if($pdo)
        {
            $pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
            $pdo->query('set names utf8');
            $this->connection = $pdo;
            return true;
        }else{
            return false;
        }
    }

    /**
     * @param array $data
     */
    public function insert($data,$table)
    {
        $values = $params  =  $fields    = array();
        if(!$table || !$data || !$this->connection) return false;

        $find_sql = "SELECT id,check_time FROM ip_list WHERE ip2long = :ip2long";
        $sth = $this->connection->prepare($find_sql);
        $sth->bindValue(':ip2long', $data['ip2long'], PDO::PARAM_INT);
        $sth->execute();
        $id =$sth->fetchAll(PDO::FETCH_NUM);
        if($id)
        {
            if($id[0][1] != $data['check_time'])
            {
                return $this->update($data['check_time'],$id[0][0],$table);
            }

            return true;
        }
        if(!$this->columns)
        {
            $get_columns_sql = "select COLUMN_NAME from information_schema.COLUMNS where table_name = :table_name";
            $sth = $this->connection->prepare($get_columns_sql);
            $sth->bindValue(':table_name', $table, PDO::PARAM_STR);
            $sth->execute();
            $this->columns =$sth->fetchAll(PDO::FETCH_COLUMN);
            if(!$this->columns) return false;
        }

        $columns = array_flip($this->columns);

        $data = array_intersect_key($data,$columns);

        foreach($data as $k=>$v)
        {
            $key = ":".$k;
            $fields[] = $k;
            $values[] = $key;
            $params[$key] = $v;
        }



        $sql   =  'INSERT INTO '.$table." (".implode(',', $fields).") VALUES (".implode(',', $values).")";

        $sth = $this->connection->prepare($sql);
        $result = $sth->execute($params);
        if($result){
            $id =  $this->connection->lastInsertId();
            return $id;
        }else{
            var_dump($sql,$params);
            return false;
        }
    }

    public function update($check_time,$id,$table)
    {
        if(!$check_time || !$table || !$id) return false;


        $sql = "UPDATE `ip_list` SET `check_time` = :check_time WHERE `id` = :id";

        $sth = $this->connection->prepare($sql);
        $sth->bindValue(':check_time', $table, PDO::PARAM_INT);
        $sth->bindValue(':id', $table, PDO::PARAM_INT);
        return $sth->execute();
    }

}