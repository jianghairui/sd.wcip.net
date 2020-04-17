<?php
//��֤��½��Ϣ
session_start();
include_once 'conn.php';
//if($_POST['submit']){
	$login=$_POST["login"];
	$username=$_POST['username'];
	$pwd=$_POST['pwd'];
	//$userpass=md5($userpass);
	if($login=="1")
	{
		if ($username!="" && $pwd!="")
		{
		$sql="select * from allusers where username='$username' and pwd='$pwd'";
		
		$query=mysqli_query($sql);
		$rowscount=mysqli_num_rows($query);
			if($rowscount>0)
			{
					$_SESSION['username']=$username;
					$_SESSION['cx']=mysqli_result($query,0,"cx");
					//$row = mysqli_fetch_row($query)
					//echo $_SESSION['cx'];
					echo "<script language='javascript'>alert('��½�ɹ���');location='main.php';</script>";
			}
			else
			{
					echo "<script language='javascript'>alert('�û������������');history.back();</script>";
			}
		}
		else
		{
				echo "<script language='javascript'>alert('������������');history.back();</script>";
		}
	}
	
//}
?>