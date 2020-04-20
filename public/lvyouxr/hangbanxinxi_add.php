<?php
session_start();
include_once 'conn.php';
$ndate =date("Y-m-d");
$addnew=$_POST["addnew"];
if ($addnew=="1" )
{
	$banjihao=$_POST["banjihao"];$shifadi=$_POST["shifadi"];$mudedi=$_POST["mudedi"];$piaojia=$_POST["piaojia"];$qifeishijian=$_POST["qifeishijian"];$beizhu=$_POST["beizhu"];
	$sql="insert into hangbanxinxi(banjihao,shifadi,mudedi,piaojia,qifeishijian,beizhu) values('$banjihao','$shifadi','$mudedi','$piaojia','$qifeishijian','$beizhu') ";
  mysqli_query('set names utf8');
	mysqli_query($sql);
	echo "<script>javascript:alert('娣诲姞鎴愬姛!');location.href='hangbanxinxi_add.php';</script>";
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="css/jquery-ui.css">
<link rel="stylesheet" media="all" type="text/css" href="css/jquery-ui-timepicker-addon.css" />
<title>鑸