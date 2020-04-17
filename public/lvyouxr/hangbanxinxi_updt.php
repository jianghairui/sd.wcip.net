<?php 
$id=$_GET["id"];
include_once 'conn.php';
$ndate =date("Y-m-d");
$addnew=$_POST["addnew"];
if ($addnew=="1" )
{

  $banjihao=$_POST["banjihao"];$shifadi=$_POST["shifadi"];$mudedi=$_POST["mudedi"];$piaojia=$_POST["piaojia"];$qifeishijian=$_POST["qifeishijian"];$beizhu=$_POST["beizhu"];
  $sql="update hangbanxinxi set banjihao='$banjihao',shifadi='$shifadi',mudedi='$mudedi',piaojia='$piaojia',qifeishijian='$qifeishijian',beizhu='$beizhu' where id= ".$id;
  mysqli_query('set names utf8');
  mysqli_query($sql);
  echo "<script>javascript:alert('修改成功!');location.href='hangbanxinxi_list.php';</script>";
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>修改航班信息</title>
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
<p>修改航班信息： 当前日期： <?php echo $ndate; ?></p>
<?php
$sql="select * from hangbanxinxi where id=".$id;
mysqli_query('set names utf8');
$query=mysqli_query($sql);
$rowscount=mysqli_num_rows($query);
if($rowscount>0)
{
?>
<form id="form1" name="form1" method="post" action="">
<table width="100%" border="1" align="center" cellpadding="3" cellspacing="1" bordercolor="#00FFFF" style="border-collapse:collapse"> 

      <tr><td>班机号：</td><td><input name='banjihao' type='text' id='banjihao' value='<?php echo mysqli_result($query,$i,banjihao);?>' /></td></tr><tr><td>始发地：</td><td><input name='shifadi' type='text' id='shifadi' value='<?php echo mysqli_result($query,$i,shifadi);?>' /></td></tr><tr><td>目的地：</td><td><input name='mudedi' type='text' id='mudedi' value='<?php echo mysqli_result($query,$i,mudedi);?>' /></td></tr><tr><td>票价：</td><td><input name='piaojia' type='text' id='piaojia' value='<?php echo mysqli_result($query,$i,piaojia);?>' /></td></tr><tr><td>起飞时间：</td><td><input name='qifeishijian' type='text' id='qifeishijian' value='<?php echo mysqli_result($query,$i,qifeishijian);?>' /></td></tr><tr><td>备注：</td><td><textarea name='beizhu' cols='50' rows='8' id='beizhu'><?php echo mysqli_result($query,$i,beizhu);?></textarea></td></tr>
   
   
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
  $("#qifeishijian").datetimepicker({
        dateFormat: 'yy-mm-dd', inline: true,
        monthNames: ["1月", "2月", "3月", "4月", "5月", "6月", "7月", "8月", "9月", "10月", "11月", "12月"],
        dayNamesMin: ["日", "一", "二", "三", "四", "五", "六"],
        // onSelect: function (dateText, inst) {
        //     var theDate = new Date(Date.parse($(this).datepicker('getDate')));
        //     var dateFormatted = $.datepicker.formatDate('yy-mm-dd', theDate);
        // }
    });
</script>

