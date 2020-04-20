<?php
session_start();
include_once 'conn.php';
$ndate =date("Y-m-d");
$addnew=$_POST["addnew"];
if ($addnew=="1" )
{
	$bianhao=$_POST["bianhao"];$mingcheng=$_POST["mingcheng"];$chufadi=$_POST["chufadi"];$mudedi=$_POST["mudedi"];$chuxingshijian=$_POST["chuxingshijian"];$jiage=$_POST["jiage"];$chuxingshichang=$_POST["chuxingshichang"];$jiaotonggongju=$_POST["jiaotonggongju"];$beizhu=$_POST["beizhu"];
	$sql="insert into lvyouxianlu(bianhao,mingcheng,chufadi,mudedi,chuxingshijian,jiage,chuxingshichang,jiaotonggongju,beizhu) values('$bianhao','$mingcheng','$chufadi','$mudedi','$chuxingshijian','$jiage','$chuxingshichang','$jiaotonggongju','$beizhu') ";
	mysqli_query('set names utf8');
	mysqli_query($sql);
	echo "<script>javascript:alert('娣诲姞鎴愬姛!');location.href='lvyouxianlu_add.php';</script>";
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>鏃呮父绾胯矾</title>
<link rel="stylesheet" type="text/css" href="css/jquery-ui.css">
<script language="javascript" src="js/Calendar.js"></script><link rel="stylesheet" href="css.css" type="text/css">
</head>
<script language="javascript">
	
	
	function OpenScript(url,width,height)
{
  var win = window.open(url,"SelectToSort",'width=' + width + ',height=' + height + ',resizable=1,scrollbars=yes,menubar=no,status=yes' );
}
	function OpenDialog(sURL, iWidth, iHeight)
{
   var oDialog = window.open(sURL, "_EditorDialog", "width=" + iWidth.toString() + ",height=" + iHeight.toString() + ",resizable=no,left=0,top=0,scrollbars=no,status=no,titlebar=no,toolbar=no,menubar=no,location=no");
   oDialog.focus();
}
</script>
<body>
<p>娣诲姞鏃呮父绾胯矾锛