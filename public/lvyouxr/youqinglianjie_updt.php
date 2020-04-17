<?php 
$id=$_GET["id"];
include_once 'conn.php';
$ndate =date("Y-m-d");
$addnew=$_POST["addnew"];
if ($addnew=="1" )
{

	$wangzhanmingcheng=$_POST["wangzhanmingcheng"];$wangzhi=$_POST["wangzhi"];
	$sql="update youqinglianjie set wangzhanmingcheng='$wangzhanmingcheng',wangzhi='$wangzhi' where id= ".$id;
	mysqli_query($sql);
	echo "<script>javascript:alert('�޸ĳɹ�!');location.href='youqinglianjie_list.php';</script>";
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>�޸���������</title><link rel="stylesheet" href="css.css" type="text/css"><script language="javascript" src="js/Calendar.js"></script>
</head>

<body>
<p>�޸��������ӣ� ��ǰ���ڣ� <?php echo $ndate; ?></p>
<?php
$sql="select * from youqinglianjie where id=".$id;
$query=mysqli_query($sql);
$rowscount=mysqli_num_rows($query);
if($rowscount>0)
{
?>
<form id="form1" name="form1" method="post" action="">
<table width="100%" border="1" align="center" cellpadding="3" cellspacing="1" bordercolor="#00FFFF" style="border-collapse:collapse"> 

      <tr><td>��վ���ƣ�</td><td><input name='wangzhanmingcheng' type='text' id='wangzhanmingcheng' size='50' value='<?php echo mysqli_result($query,$i,wangzhanmingcheng);?>' /></td></tr><tr><td>��ַ��</td><td><input name='wangzhi' type='text' id='wangzhi' size='50' value='<?php echo mysqli_result($query,$i,wangzhi);?>' /></td></tr>
   
   
    <tr>
      <td>&nbsp;</td>
      <td><input name="addnew" type="hidden" id="addnew" value="1" />
      <input type="submit" name="Submit" value="�޸�" />
      <input type="reset" name="Submit2" value="����" /></td>
    </tr>
  </table>
</form>
<?php
	}
?>
<p>&nbsp;</p>
</body>
</html>

