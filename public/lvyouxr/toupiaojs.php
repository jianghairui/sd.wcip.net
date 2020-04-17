<?php
header('Content-Type:application/json');
include_once 'conn.php';
$username = $_POST['username'];
$id = $_POST['id'];

if($username == ''){
	exit(json_encode('请先登录!'));
}
$sql = "SELECT * FROM toupiaojilu WHERE username='$username' AND lid='$id'";
mysqli_query("set names utf8");
$result = mysqli_query($sql);
$isExist = mysqli_fetch_array($result);
if($isExist){
	exit(json_encode('已投票'));
}else{
	$sql = "INSERT INTO toupiaojilu (`username`,`lid`) VALUES ('$username','$id')";
	$result = mysqli_query($sql);
	if($result){
		exit(json_encode('投票成功'));
	}else{
		exit(json_encode('投票失败'));
	}
}




?>