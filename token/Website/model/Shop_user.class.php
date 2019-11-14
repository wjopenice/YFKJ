<?php
/**
 * Created by PhpStorm.
 * User: redstart
 * Date: 2017/10/9
 * Time: 11:39
 */
namespace Website\model;
use \ext\Model;

class Shop_user extends Model
{

    public function db_find($table,$from = "*",$where=null){
        if(is_null($where)){
            $sql = "SELECT {$from} FROM {$table}";
        }else{
            $sql = "SELECT {$from} FROM {$table} WHERE {$where}";
        }
        $result = $this->pdo->query($sql);
        $data = $result->fetchAll(2);
        return $data;
    }

    public function db_findone($table,$from = "*",$where=null){
        if(is_null($where)){
            $sql = "SELECT {$from} FROM {$table}";
        }else{
            $sql = "SELECT {$from} FROM {$table} WHERE {$where}" ;
        }
        $result = $this->pdo->query($sql);
        $data = $result->fetch(2);
        return $data;
    }

}