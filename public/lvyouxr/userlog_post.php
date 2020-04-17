<?php
//��֤��½��Ϣ
session_start();
include_once 'conn.php';
//if($_POST['submit']){
	
	$username=$_POST['username'];
	$pwd=$_POST['pwd1'];
	//$userpass=md5($userpass);

		if ($username!="" && $pwd!="")
		{
		$sql="select * from yonghuzhuce where zhanghao='$username' and mima='$pwd' and issh='��'";
		
		$query=mysqli_query($sql);
		$rowscount=mysqli_num_rows($query);
			if($rowscount>0)
			{
					$_SESSION['username']=$username;
					$_SESSION['cx']="ע���û�";
					$_SESSION['xm']=mysqli_result($query,$i,xingming);
					$_SESSION['zp']=mysqli_result($query,$i,zhaopian);
					//$row = mysqli_fetch_row($query)
					//echo $_SESSION['cx'];
					echo "<script language='javascript'>alert('��½�ɹ���');location='index.php';</script>";
			}
			else
			{
					echo "<script language='javascript'>alert('�û�����������󣡻������ʺ�δ�����');history.back();</script>";
			}
		}
		else
		{
				echo "<script language='javascript'>alert('������������');history.back();</script>";
		}
	
	
//}
?>