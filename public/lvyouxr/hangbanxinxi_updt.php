<?php 
$id=$_GET["id"];
include_once 'conn.php';
$ndate =date("Y-m-d");
$addnew=$_POST["addnew"];
if ($addnew=="1" )
{

  $banjihao=$_POST["banjihao"];$shifadi=$_POST["shifadi"];$mudedi=$_POST["mudedi"];$piaojia=$_POST["piaojia"];$qifeishijian=$_POST["qifeishijian"];$beizhu=$_POST["beizhu"];
  $sql="update hangbanxinxi set banjihao='$banjihao',shifadi='$shifadi',mudedi='$mudedi',piaojia='$piaojia',qifeishijian='$qifeishijian',beizhu='$beizhu' where id= ".$id;
  mysqli_query('set names utf8');
  mysqli_query($sql);
  echo "<script>javascript:alert('淇