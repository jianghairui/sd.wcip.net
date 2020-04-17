<?php
//��֤��½��Ϣ

include_once 'conn.php';
//if($_POST['submit']){
	$id=$_GET["id"];
	$yuan=$_GET["yuan"];
	$tablename=$_GET["tablename"];
	if($yuan=="��")
	{
	$sql="update $tablename set issh='��' where id=$id";
	}
	else
	{
	$sql="update $tablename set issh='��' where id=$id";
	}
	 	mysqli_query($sql);
	

		$comewhere=$_SERVER['HTTP_REFERER'];
		echo "<script language='javascript'>alert('��˳ɹ���');location.href='$comewhere';</script>";
	
//}
?>