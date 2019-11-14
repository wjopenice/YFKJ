<!DOCTYPE html>
<html>
<head>
    <title></title>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="<?=ROOT?>public/Admin/Css/bootstrap.css" />
    <link rel="stylesheet" type="text/css" href="<?=ROOT?>public/Admin/Css/bootstrap-responsive.css" />
    <link rel="stylesheet" type="text/css" href="<?=ROOT?>public/Admin/Css/style.css" />
    <script type="text/javascript" src="<?=ROOT?>public/Admin/Js/jquery.js"></script>
    <script type="text/javascript" src="<?=ROOT?>public/Admin/Js/jquery.sorted.js"></script>
    <script type="text/javascript" src="<?=ROOT?>public/Admin/Js/bootstrap.js"></script>
    <script type="text/javascript" src="<?=ROOT?>public/Admin/Js/ckform.js"></script>
    <script type="text/javascript" src="<?=ROOT?>public/Admin/Js/common.js"></script>

    <style type="text/css">
        body {
            padding-bottom: 40px;
        }
        .sidebar-nav {
            padding: 9px 0;
        }

        @media (max-width: 980px) {
            /* Enable use of floated navbar text */
            .navbar-text.pull-right {
                float: none;
                padding-left: 5px;
                padding-right: 5px;
            }
        }


    </style>
</head>
<body>
<form class="form-inline definewidth m20" action="index.html" method="get">
    菜单名称：
    <input type="text" name="menuname" id="menuname"class="abc input-default" placeholder="" value="">&nbsp;&nbsp; 
    <button type="submit" class="btn btn-primary"><span class="icon-search"></span></button>&nbsp;&nbsp; <button type="button" class="btn btn-success" id="addnew"><span class="icon-plus"></span></button>
</form>
<table class="table table-bordered table-hover definewidth m10">
    <thead>
    <tr>
        <th><input type="checkbox" name="" value=""></th>
        <th>标题</th>
        <th>图片</th>
        <th>价格</th>
        <th>类型</th>
        <th>管理操作</th>
    </tr>
    </thead>
            <?php foreach ($arrData as $key=>$value):?>
            <tr>
                <td><input type="checkbox" name="id" value="<?=$value['id']?>"></td>
                <td><?=$value['name']?></td>
                <td><img width="50" height="50" src="<?=ROOT?>public/images/<?=$value['pic']?>" /></td>
                <td><?=$value['price']?></td>
                <td><?=$value['title']?></td>
                <td><a href="<?=BASE_URL?>/Admin/Index/menu_edit?id=<?=$value['id']?>" class="	btn btn-warning"><span class="icon-pencil"></span></a>&nbsp;<a href="<?=BASE_URL?>/Admin/Index/menu_del?id=<?=$value['id']?>&page=<?php echo isset($_GET['page'])?$_GET['page'] : 1;  ?>" class="btn btn-danger"><span class="icon-trash"></span></a></td>
            </tr>
            <?php endforeach;?>
            <tr>
                <td  colspan="6">
                <?=$show?>
                </td>
            </tr>
      </table>
</body>
</html>
<script>
    $(function () {
		$('#addnew').click(function(){

				window.location.href="<?=BASE_URL?>/Admin/Index/menu_add";
		 });
    });
	
</script>