<?php
namespace app\core;
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
        $str .= "<style>.pagination a{border: solid 1px #e6e6e6;color: #3b6391 ; border-right: none; display: inline-block; text-decoration: none; padding: 5px 14px;height: 20px;line-height: 20px;font-size: 12px;color: #666;}li{float: left;}</style>";
        $str .=  "<div class='pagination  pagination-large'> ";
        $str .=  "<ul style='list-style: none;'>";
        $str .= "<li><a href='?page=1".$searchData."' class='btn' style='border-right: none;'>首页</a></li>";
        $str .= "<li><a href='?page=".$this->prevPage($this->page).$searchData."'  class='btn'>上一页</a></li>";
        $start = ($this->page > 5)?($this->page - 5):1;
        $end = ( ($this->page + 4) >  $this->totalPage)?$this->totalPage:$this->page+4;
        for($j=$start;$j<=$end;$j++){
            if($j == $this->page){
                $str .= "<li><a href='?page=".$j.$searchData."' style=' background:#009688;border:1px solid #009688; color: white;' class='btn'>".$j."</a></li>";
            }else{
                $str .= "<li><a href='?page=".$j.$searchData."' class='btn'>".$j."</a></li>";
            }
        }
        $str .= "<li><a href='?page=".$this->nextPage($this->page,$this->totalPage).$searchData."'  class='btn'>下一页</a></li>";
        $str .= "<li><a href='?page=".$this->totalPage.$searchData."'  class='btn' style='border-right: 1px solid #e6e6e6;'>末页</a></li>";
        $str .="  </ul>";
        $str .=" </div>";
        return $str;
    }

    public function mediashow($searchData = ""){
        $str = "";
        $str .= "<style>.pagination a{}li{}</style>";
        $str .=  "<div class=''> ";
        $str .=  "<ul style=''>";
        $str .= "<li><a href='?page=1".$searchData."' class='btn' style='border-right: none;'>首页</a></li>";
        $str .= "<li><a href='?page=".$this->prevPage($this->page).$searchData."'  class='btn'>上一页</a></li>";
        $start = ($this->page > 5)?($this->page - 5):1;
        $end = ( ($this->page + 4) >  $this->totalPage)?$this->totalPage:$this->page+4;
        for($j=$start;$j<=$end;$j++){
            if($j == $this->page){
                $str .= "<li><a href='?page=".$j.$searchData."' style=' background:#091928;border:1px solid #091928; color: white;' class='btn'>".$j."</a></li>";
            }else{
                $str .= "<li><a href='?page=".$j.$searchData."' class='btn'>".$j."</a></li>";
            }
        }
        $str .= "<li><a href='?page=".$this->nextPage($this->page,$this->totalPage).$searchData."'  class='btn'>下一页</a></li>";
        $str .= "<li><a href='?page=".$this->totalPage.$searchData."'  class='btn' style='border-right: 1px solid #e6e6e6;'>末页</a></li>";
        $str .="  </ul>";
        $str .=" </div>";
        return $str;
    }

    public function wapshow($searchData = ""){
        $str = "";
        $str .=  "<div class='pagination  pagination-large'> ";
        $str .=  "<ul>";
        $str .= "<li class='li1'><a href='?page=1".$searchData."' class='btn' >首页</a></li>";
        $str .= "<li class='li2'><a href='?page=".$this->prevPage($this->page).$searchData."'  class='btn'>上一页</a></li>";
        $str .= "<li class='li3'><a href='?page=".$this->nextPage($this->page,$this->totalPage).$searchData."'  class='btn'>下一页</a></li>";
        $str .= "<li class='li4'><a href='?page=".$this->totalPage.$searchData."'  class='btn' >末页</a></li>";
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