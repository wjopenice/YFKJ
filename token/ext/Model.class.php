<?php
namespace ext;
use \PDO;
abstract class Model
{
    public $pdo;
    private $sql;
    public function __construct($db_config = array())
    {
        if(empty($db_config)){
            $dsn = TYPE.":dbname=".Dbname.";host=".Host.";charset=".Charset.";port=".Port;
            $this->pdo = new PDO($dsn,User,Pass);
        }else{
            $dsn = "{$db_config["driver"]}:dbname={$db_config["database"]};host={$db_config["hostname"]};charset={$db_config["charset"]};port={$db_config["port"]}";
            $this->pdo = new PDO($dsn,$db_config["username"],$db_config["password"]);
        }
    }

    public function  deleteSql($tbname = null,$where= null){
        $sql = "DELETE FROM ".Prefix.$tbname." WHERE {$where}";
        return  $sql;
    }

    public function insertSql($tbname = null,array $data = []){
        $strkey = "";
        $strval = "";
        foreach ($data as $key=>$value){
            $strkey .= "`$key`,";
            if(is_int($value)){
                $strval .= "$value,";
            }else if(is_null($value)){
                $strval .= "null,";
            }else{
                $strval .= "'$value',";
            }
        }
        $sql = "INSERT INTO ".Prefix.$tbname." (".substr($strkey,0,-1)." ) VALUES (".substr($strval,0,-1).")";
        return  $sql;
    }
    
    public function updateSql($tbname = null,array $data=[],$where=null){
        $strData = "";
        foreach ($data as $key=>$value){
            if(is_int($value)){
                $strData .= $key."=".$value.",";
            }else{
                $strData .= $key."='".$value."',";
            }
        }
        $sql = "UPDATE ".Prefix.$tbname." SET ".substr($strData,0,-1)." WHERE {$where} ";
        return  $sql;
    }
    //执行SQL语句
    //$ftype = 2：返回一个索引为结果集列名的数组
    //$ftype = 4：返回一个索引为结果集列名和以0开始的列号的数组
    //$ftype = 6：返回 TRUE ，并分配结果集中的列值给 PDOStatement::bindColumn() 方法绑定的 PHP 变量。
    //$ftype = 8：返回一个请求类的新实例，映射结果集中的列名到类中对应的属性名。如果 fetch_style 包含 PDO::FETCH_CLASSTYPE（例如：PDO::FETCH_CLASS |PDO::FETCH_CLASSTYPE），则类名由第一列的值决定
    //$ftype = 9：更新一个被请求类已存在的实例，映射结果集中的列到类中命名的属性
    //$ftype = 1：结合使用 PDO::FETCH_BOTH 和 PDO::FETCH_OBJ，创建供用来访问的对象变量名
    //$ftype = 3：返回一个索引为以0开始的结果集列号的数组
    //$ftype = 5：返回一个属性名对应结果集列名的匿名对象
    //（注意：查询结果为多维数据，修改/增加/删除结果为布尔值）

    public function action($sql,$ftype = 2){
         if(stripos($sql,"SELECT") !==  false){
             try {
                 $result = $this->pdo->query($sql);
                 $data = $result->fetchAll($ftype);
                 return $data;
             } catch (PDOException $e) {
                 echo 'Connection failed: ' . $e->getMessage();
             }
         }else{
              $bool = $this->pdo->exec($sql);
              return $bool;
         }
    }

    public function beginTransaction(){
        $this->pdo->beginTransaction();
    }
    public function rollback(){
        $this->pdo->rollback();
    }
    public function commit(){
        $this->pdo->commit();
    }
    //获取写入ID
    public function getInsertId(){
        $getId = $this->pdo->lastInsertId();
        return  $getId;
    }

    //单表求和
    public function zssum($table,$field,$as = "num",$where = null){
        $str = "";
        if($where == null){
            $str = "SELECT sum({$field}) AS {$as} FROM ".Prefix.$table;
        }else{
            $str = "SELECT sum({$field}) AS {$as} FROM ".Prefix.$table." WHERE {$where}";
        }
        $result = $this->pdo->query($str);
        $data = $result->fetch(2);
        unset($str);
        if(!is_null($data[$as])){
            return $data[$as];
        }else{
            return 0;
        }
    }

    //单表求长度SELECT count(*) as total FROM zs_card
    public function zscount($table,$field = "*",$as = 'total',$where = null){
        if($where == null){
            $str = "SELECT count({$field}) AS {$as} FROM ".Prefix.$table;
        }else{
            $str = "SELECT count({$field}) AS {$as} FROM ".Prefix.$table." WHERE {$where}";
        }
        $result = $this->pdo->query($str);
        $data = $result->fetch(2);
        return $data[$as];
    }
    //多表求长度SELECT count(*) as total FROM zs_card
    public function zsoddcount($table,$from,$join,$field = "*",$as = 'total',$joinwhere = null){
        if($joinwhere == null){
            $str = "SELECT count({$field}) AS {$as} FROM ".Prefix.$table." INNER JOIN ".Prefix.$from." ON {$join}";
        }else{
            $str = "SELECT count({$field}) AS {$as} FROM ".Prefix.$table." INNER JOIN ".Prefix.$from." ON {$join} WHERE {$joinwhere}";
        }
        $result = $this->pdo->query($str);
        $data = $result->fetch(2);
        return $data[$as];
    }
    public function from($from=null){
        $exp = "/[\da-zA-Z]+_/";
        preg_match($exp,$from,$data);
        if(!empty($data)){
            $this->sql .= " FROM ".$from." ";
        }else{
            $this->sql .= " FROM ".Prefix.$from." ";
        }
        return $this;
    }

    //SQL链式操作显示的字段
    public function field($field = "*"){
        $this->sql .= " SELECT {$field} ";
        return $this;
    }
    //SQL链式操作的表名
    public function table($table=null){
        $this->sql .= " FROM {$table} ";
        return $this;
    }
    public function where($where=null){
        $this->sql .= " WHERE {$where} ";
        return $this;
    }
    public function order($order=null){
        $this->sql .= " ORDER BY {$order} ";
        return $this;
    }
    public function limit($start=null,$num=null){
        $this->sql .= " LIMIT {$start},{$num} ";
        return $this;
    }
    public function like($like=null){
        $this->sql .= " LIKE '%{$like}%' ";
        return $this;
    }
    public function join($table=null,$join=null){
        $exp = "/[\da-zA-Z]+_/";
        preg_match($exp,$table,$data);
        if(!empty($data)){
            $this->sql .= " INNER JOIN ".$table." ON {$join} ";
        }else{
            $this->sql .= " INNER JOIN ".Prefix.$table." ON {$join} ";
        }
        return $this;
    }
    public function  group($group=null){
        $this->sql .= " GROUP BY {$group} ";
        return $this;
    }
    public function regexp($regexp=null){
        $this->sql .= " REGEXP '{$regexp}'";
        return $this;
    }

    //查询结果多维数组|SQL链式操作查询结果多维数组
    public function select(){
        $data = $this->action($this->sql);
        $this->sql = "";
        return $data;
    }
    //查询结果多维对象|SQL链式操作查询结果多维对象
    public function selectobj(){
        $data = $this->action($this->sql,5);
        $this->sql = "";
        return $data;
    }
    //查询结果一维数组|SQL链式操作查询结果一维数组
    public function find(){
        $result = $this->pdo->query($this->sql);
        $data = $result->fetch(2);
        $this->sql = "";
        return $data;
    }
    //查询结果一维对象|SQL链式操作查询结果一维对象
    public function findobj(){
        $result = $this->pdo->query($this->sql);
        //$data = $result->fetch(\PDO::FETCH_OBJ);
        $data = $result->fetch(5);
        $this->sql = "";
        return $data;
    }
    public function getSql() {
        return $this->sql;
        exit;
    }
    public function getDbError() {
        return $this->pdo->getError();
    }
}