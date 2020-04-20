<?php 
$id=$_GET["id"];
include_once 'conn.php';
$ndate =date("Y-m-d");
$addnew=$_POST["addnew"];
if ($addnew=="1" )
{

  $bianhao=$_POST["bianhao"];$mingcheng=$_POST["mingcheng"];$chufadi=$_POST["chufadi"];$mudedi=$_POST["mudedi"];$chuxingshijian=$_POST["chuxingshijian"];$jiage=$_POST["jiage"];$chuxingshichang=$_POST["chuxingshichang"];$jiaotonggongju=$_POST["jiaotonggongju"];$beizhu=$_POST["beizhu"];
  $sql="update lvyouxianlu set bianhao='$bianhao',mingcheng='$mingcheng',chufadi='$chufadi',mudedi='$mudedi',chuxingshijian='$chuxingshijian',jiage='$jiage',chuxingshichang='$chuxingshichang',jiaotonggongju='$jiaotonggongju',beizhu='$beizhu' where id= ".$id;
  mysqli_query('set names utf8');
  mysqli_query($sql);
  echo "<script>javascript:alert('淇