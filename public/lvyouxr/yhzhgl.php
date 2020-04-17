<?php

session_start();
if($_SESSION['cx']!="��������Ա")
{
	echo "<script>javascript:alert('�Բ�����û�и�Ȩ��');history.back();</script>";
	exit;
}


include_once 'conn.php';

	 
	$addnew=$_POST["addnew"];
	if($addnew=="1")
	{
	$username=$_POST['username'];
	$pwd=$_POST['pwd1'];
	$cx=$_POST['cx'];
	
	$sql="select * from allusers where username='$username' and pwd='$pwd'";
		
		$query=mysqli_query($sql);
		$rowscount=mysqli_num_rows($query);
		if($rowscount>0)
			{
					
					echo "<script language='javascript'>alert('���û����Ѿ�����,�뻻�����û�����');history.back();</script>";
			}
			else
			{
				//date_default_timezone_set("PRC");
				
				$ndate =date("Y-m-d H:i:s");

					$sql="insert into allusers(username,pwd,cx) values('$username','$pwd','$cx')";
					mysqli_query($sql);
					echo "<script language='javascript'>alert('�����ɹ���');location.href='yhzhgl.php';</script>";
			}
	 }
	 
	 

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>�ޱ����ĵ�</title>
</head>

<body>
<p>����¹���Ա��</p>
<script language="javascript">
	function check()
	{
		if(document.form1.username.value=="")
		{
			alert("�������û���");
			document.form1.username.focus();
			return false;
		}
		if(document.form1.pwd1.value=="")
		{
			alert("����������");
			document.form1.pwd1.focus();
			return false;
		}
		if(document.form1.pwd2.value=="")
		{
			alert("������ȷ������");
			document.form1.pwd2.focus();
			return false;
		}
		if(document.form1.pwd1.value!=document.form1.pwd2.value)
		{
			alert("�������벻һ�£�������");
			document.form1.pwd1.value="";
			document.form1.pwd2.value="";
			document.form1.pwd1.focus();
			return false;
		}
	}
</script>
<form id="form1" name="form1" method="post" action="">
<table width="100%" border="1" align="center" cellpadding="3" cellspacing="1" bordercolor="#00FFFF" style="border-collapse:collapse">    <tr>
      <td>�û�����</td>
      <td><input name="username" type="text" id="username" />
      *
      <input name="addnew" type="hidden" id="addnew" value="1" /></td>
    </tr>
    <tr>
      <td>���룺</td>
      <td><input name="pwd1" type="password" id="pwd1" />
      *</td>
    </tr>
    <tr>
      <td>ȷ�����룺</td>
      <td><input name="pwd2" type="password" id="pwd2" />
      *</td>
    </tr>
    
    <tr>
      <td>Ȩ��:</td>
      <td><input name="cx" type="radio" value="��ͨ����Ա" checked="checked" />
      ��ͨ����Ա
        <input type="radio" name="cx" value="��������Ա" />
      ��������Ա</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td><input type="submit" name="Submit" value="�ύ" onClick="return check();" />
      <input type="reset" name="Submit2" value="����" /></td>
    </tr>
  </table>
</form>
<p>���й���Ա�б�</p>
<table width="100%" border="1" align="center" cellpadding="3" cellspacing="1" bordercolor="#00FFFF" style="border-collapse:collapse">  
  <tr>
    <td bgcolor="A4B6D7">���</td>
    <td bgcolor="A4B6D7">�û���</td>
    <td bgcolor="A4B6D7">����</td>
    <td bgcolor="A4B6D7">Ȩ��</td>
    <td bgcolor="A4B6D7">���ʱ��</td>
    <td bgcolor="A4B6D7">����</td>
  </tr>
  <?php
	  $sql="select * from allusers order by id desc";
	  $query=mysqli_query($sql);
	  $rowscount=mysqli_num_rows($query);
	 for($i=0;$i<$rowscount;$i++)
	 {
  ?>
  <tr>
    <td><?php
		echo $i+1;
	?></td>
    <td><?php
		echo mysqli_result($query,$i,"username");
	?></td>
    <td><?php
		echo mysqli_result($query,$i,"pwd");
	?></td>
    <td><?php
		echo mysqli_result($query,$i,"cx");
	?></td>
    <td><?php
		echo mysqli_result($query,$i,"addtime");
	?></td>
    <td><a href="del.php?id=<?php
		echo mysqli_result($query,$i,"id");
	?>&tablename=allusers" onClick="return confirm('���Ҫɾ����')">ɾ��</a> </td>
  </tr>
  <?php
  	}
  ?>
</table>
<p>&nbsp; </p>
</body>
</html>
