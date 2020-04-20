<?php
header('Content-Type:application/json');
include_once 'conn.php';
$username = $_POST['username'];
$id = $_POST['id'];

if($username == ''){
	exit(json_encode('璇峰厛鐧诲綍!'));
}
$sql = "SELECT * FROM toupiaojilu WHERE username='$username' AND lid='$id'";
mysqli_query("set names utf8");
$result = mysqli_query($sql);
$isExist = mysql_fetch_array($result);
if($isExist){
	exit(json_encode('宸叉姇绁