<?php

session_start();



include_once 'conn.php';

	 
	$addnew=$_POST["addnew"];
	if($addnew=="1")
	{
	$username=$_POST['username'];
	$pwd=$_POST['xmm1'];
	$pwdy=$_POST['ymm'];
	//$cx=$_POST['cx'];
	
	$sql="select * from allusers where username='".$_SESSION['username']."'";
		
		$query=mysqli_query($sql);
		$rowscount=mysqli_num_rows($query);
		if($rowscount>0)
			{
					if(mysqli_result($query,0,"pwd")==$pwdy)
					{
					$sql="update allusers set pwd='$pwd' where username='".$_SESSION['username']."'";
					$query=mysqli_query($sql);
					echo "<script language='javascript'>alert('�޸ĳɹ���');history.back();</script>";
					}
					else
					{
					echo "<script language='javascript'>alert('�Բ���,ԭ���벻��ȷ��');history.back();</script>";
					}
			}
			else
			{
		
					echo "<script language='javascript'>alert('�Բ���,ԭ���벻��ȷ��');history.back();</script>";
			}
	 }
	 
	 

?>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>�޸�����</title>
</head>
<script>
function check()
{
	if(document.form1.ymm.value=="")
	{
		alert("������ԭ����");
		document.form1.ymm.focus();
		return false;
	}
	if(document.form1.xmm1.value=="")
	{
		alert("������������");
		document.form1.xmm1.focus();
		return false;
	}
	if(document.form1.xmm2.value=="")
	{
		alert("������ȷ������");
		document.form1.xmm2.focus();
		return false;
	}
	if (document.form1.xmm1.value!=document.form1.xmm2.value)
	{
		alert("�Բ����������벻һ��������������");
		document.form1.xmm1.value="";
		document.form1.xmm2.value="";
		document.form1.xmm1.focus();
		return false;
	}
}

</script>
<body>

<form id="form1" name="form1" method="post" action="mod.php">
  <table width="41%" height="126" border="1" align="center" cellpadding="0" cellspacing="0" bordercolor="9DC9FF" style="border-collapse:collapse">
    <tr>
      <td colspan="2"><div align="center">�޸�����</div></td>
    </tr>
    <tr>
      <td>ԭ���룺</td>
      <td><input name="ymm" type="text" id="ymm" />
      <input name="addnew" type="hidden" id="addnew" value="1"></td>
    </tr>
    <tr>
      <td>�����룺</td>
      <td><input name="xmm1" type="password" id="xmm1" /></td>
    </tr>
    <tr>
      <td>ȷ�����룺</td>
      <td><input name="xmm2" type="password" id="xmm2" /></td>
    </tr>
    <tr>
      <td><input type="submit" name="Submit" value="ȷ��" onClick="return check()" /></td>
      <td><input type="reset" name="Submit2" value="����" /></td>
    </tr>
  </table>
</form>
</body>
</html>

