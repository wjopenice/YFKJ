<?php
namespace ext;
class Page
{
    //分页类
    //总条数 = result->num
    //总页数 = ceil（总条数 /单页数）
    //单页数 = 3
    //当前页 =
    //起始位置 = （当前页-1）*单页数
    public $total;
    public $showPage;
    public $totalPage;
    public $page;
    public $start;
    public $limit;

    public function init($total,$showPage){
         $this->total = $total;
         $this->showPage = $showPage;
         $this->totalPage = ceil($this->total/$this->showPage);
         $this->page = empty($_GET['page']) ? 1 : $_GET['page'];
         $this->start = ($this->page -1) * $this->showPage;
         $this->limit = " limit {$this->start},{$this->showPage}";
    }

    public function show($searchData = ""){

        $str = "";

        $str .=  "<div class=\"pagination  pagination-large\"> ";
        $str .=  "<ul>";
        $str .= "<li><a href='?page=1".$searchData."' style=' padding: 5px 10px;margin:0 5px; ' class='btn'>首页</a></li>";
        $str .= "<li><a href='?page=".$this->prevPage($this->page).$searchData."' style=' padding: 5px 10px;margin:0 5px;' class='btn'>上一页</a></li>";
        for($j=1;$j<=$this->totalPage;$j++){
            if($j == $this->page){
                $str .= "<li><a href='?page=".$j.$searchData."' style='padding: 5px 10px;margin:0 5px; color:red;' class='btn'>".$j."</a></li>";
            }else{
                $str .= "<li><a href='?page=".$j.$searchData."' style='padding: 5px 10px;margin:0 5px;' class='btn'>".$j."</a></li>";
            }
        }
        $str .= "<li><a href='?page=".$this->totalPage.$searchData."' style=' padding: 5px 10px;margin:0 5px;' class='btn'>尾页</a></li>";
        $str .= "<li><a href='?page=".$this->nextPage($this->page,$this->totalPage).$searchData."' style=' padding: 5px 10px;margin:0 5px;' class='btn'>下一页</a></li>";
        $str .="  </ul>";
        $str .=" </div>";
         return $str;
    }

    public function prevPage($page){
        if($page <= 1){
            return  $x = 1;
        }else{
            return  $x = $page - 1;
        }
    }
    public function nextPage($page,$max){
        if($page >= $max){
            return  $x = $max;
        }else{
            return  $x = $page + 1;
        }
    }
}