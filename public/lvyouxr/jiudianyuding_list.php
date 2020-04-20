<?php 
include_once 'conn.php';
if(isset($_GET['id'])){
  $sql = "DELETE FROM booktourline WHERE id='".$_GET['id']."'";
  mysqli_query($sql);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>閰掑簵棰勮