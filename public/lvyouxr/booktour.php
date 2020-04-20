<?php
header('Content-Type:application/json');
include_once 'conn.php';
$username = $_POST['username'];
$name = $_POST['name'];
$linename = $_POST['linename'];
$price = $_POST['price'];

if($username == ''){
	exit(json_encode('璇峰厛鐧诲綍!'));
}
$sql = "INSERT INTO booktourline (`username`,`name`,`linename`,`price`) VALUES ('$username','$name','$linename','$price')";
mysqli_query("set names utf8");
$result = mysqli_query($sql);
if($result){
	exit(json_encode('棰勫畾鎴愬姛'));
}else{
	exit(json_encode('棰勫畾澶辫触'));
}

?>