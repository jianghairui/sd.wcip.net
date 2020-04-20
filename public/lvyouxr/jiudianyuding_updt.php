<?php 
$id=$_GET["id"];
include_once 'conn.php';
$ndate =date("Y-m-d");
$addnew=$_POST["addnew"];
if ($addnew=="1" )
{

	$jiudianmingcheng=$_POST["jiudianmingcheng"];$xingji=$_POST["xingji"];$dianhua=$_POST["dianhua"];$dizhi=$_POST["dizhi"];$yudingren=$_POST["yudingren"];$yudingshijian=$_POST["yudingshijian"];$yudingrenshu=$_POST["yudingrenshu"];$beizhu=$_POST["beizhu"];
	$sql="update jiudianyuding set jiudianmingcheng='$jiudianmingcheng',xingji='$xingji',dianhua='$dianhua',dizhi='$dizhi',yudingren='$yudingren',yudingshijian='$yudingshijian',yudingrenshu='$yudingrenshu',beizhu='$beizhu' where id= ".$id;
	mysqli_query($sql);
	echo "<script>javascript:alert('修改成功!');location.href='jiudianyuding_list.php';</script>";
  exit;
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>修改酒店预订</title>
<link rel="stylesheet" type="text/css" href="css/jquery-ui.css">
<link rel="stylesheet" media="all" type="text/css" href="css/jquery-ui-timepicker-addon.css" />
<link rel="stylesheet" href="css.css" type="text/css"><script language="javascript" src="js/Calendar.js"></script>
</head>
<script language="javascript">
	
	
	function OpenScript(url,width,height)
{
  var win = window.open(url,"SelectToSort",'width=' + width + ',height=' + height + ',resizable=1,scrollbars=yes,menubar=no,status=yes' );
}
	function OpenDialog(sURL, iWidth, iHeight)
{
   var oDialog = window.open(sURL, "_EditorDialog", "width=" + iWidth.toString() + ",height=" + iHeight.toString() + ",resizable=no,left=0,top=0,scrollbars=no,status=no,titlebar=no,toolbar=no,menubar=no,location=no");
   oDialog.focus();
}
</script>
<body>
<p>修改酒店预订： 当前日期： <?php echo $ndate; ?></p>
<?php
$sql="select * from jiudianyuding where id=".$id;
$query=mysqli_query($sql);
$rowscount=mysql_num_rows($query);
if($rowscount>0)
{
?>
<form id="form1" name="form1" method="post" action="">
<table width="100%" border="1" align="center" cellpadding="3" cellspacing="1" bordercolor="#00FFFF" style="border-collapse:collapse"> 

      <tr><td>酒店名称：</td><td><input name='jiudianmingcheng' type='text' id='jiudianmingcheng' size='50' value='<?php echo mysql_result($query,$i,jiudianmingcheng);?>' /></td></tr><tr><td>星级：</td><td><input name='xingji' type='text' id='xingji' value='<?php echo mysql_result($query,$i,xingji);?>' /></td></tr><tr><td>电话：</td><td><input name='dianhua' type='text' id='dianhua' value='<?php echo mysql_result($query,$i,dianhua);?>' /></td></tr><tr><td>地址：</td><td><input name='dizhi' type='text' id='dizhi' size='50' value='<?php echo mysql_result($query,$i,dizhi);?>' /></td></tr><tr><td>预订人：</td><td><input name='yudingren' type='text' id='yudingren' value='<?php echo mysql_result($query,$i,yudingren);?>' /></td></tr><tr><td>预订时间：</td><td><input name='yudingshijian' type='text' id='yudingshijian' value='<?php echo mysql_result($query,$i,yudingshijian);?>'/></td></tr><tr><td>预订人数：</td><td><input name='yudingrenshu' type='text' id='yudingrenshu' value='<?php echo mysql_result($query,$i,yudingrenshu);?>' /></td></tr><tr><td>备注：</td><td><textarea name='beizhu' cols='50' rows='8' id='beizhu'><?php echo mysql_result($query,$i,beizhu);?></textarea></td></tr>
   
   
    <tr>
      <td>&nbsp;</td>
      <td><input name="addnew" type="hidden" id="addnew" value="1" />
      <input type="submit" name="Submit" value="修改" />
      <input type="reset" name="Submit2" value="重置" /></td>
    </tr>
  </table>
</form>
<?php
	}
?>
<p>&nbsp;</p>
</body>
</html>
<script src="js/jquery_002.js"></script>
<script src="js/jquery-ui.js"></script>
<script type="text/javascript" src="js/jquery-ui-timepicker-addon.js"></script>
<script>

  $("#yudingshijian").datetimepicker({
        dateFormat: 'yy-mm-dd', inline: true,
    });
</script>

