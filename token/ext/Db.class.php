<?php
namespace ext;
class Db
{
     private $sql;

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

     public function select($select="*"){
         $this->sql .= " SELECT {$select} ";
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
    public function getSql(){
        return $this->sql;
    }
    public function delSql(){
        $this->sql = "";
    }

}

