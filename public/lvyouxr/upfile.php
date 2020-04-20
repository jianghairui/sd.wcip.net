<?php
//error_reporting(E_ALL &~ E_NOTICE);
if(isset($_FILES['upfile']['name'])){
  $exp = explode('.',$_FILES['upfile']['name']);
  $exname=array_pop($exp);
  $uploadfile = getname($exname);
  $result = move_uploaded_file($_FILES['upfile']['tmp_name'], $uploadfile);
  if($result){
    echo "<font color=#ff0000>文件上传成功！</font>"; 
    echo "<input name='CopyPath' type='button' class='button' value='拷贝文件路径'  onclick=CopyPath('".$uploadfile."','".$_GET['Result']."')>";
  }
}

  function getname($exname){ 
     $dir = "uploadfile/"; 
     $i=1; 
     while(true){ 
       if(!is_file($dir.$i.".".$exname)){ 
          $name=$i.".".$exname; 
          break; 
        } 
       $i++; 

     } 
     return $dir.$name; 
  } 
?>
<html> 
<head> 
<meta http-equiv="Content-Type" content="text/html; charset=gb2312"> 
<title>文件上传</title>
<link rel="stylesheet" href="Images/CssAdmin.css">
</head> 
<body> 

<form enctype="multipart/form-data" action="upfile.php?Result=<?php echo $_GET['Result'];?>" method='post'>  

<input type="hidden" name="MAX_FILE_SIZE" value="2000000"> 

<input name='upfile' type='file' class="button"> 

<input type='submit' class="button" value='上传文件'>  

</form>  

</body> 

</html>
<script language="JavaScript"> 
function CopyPath(FilePath,FileSize)
{
							
	window.opener.document.form1.<?php echo $_GET["Result"];?>.value=FilePath;
	window.opener=null;
  window.close();
}
</script> 
