<?php
session_start();
include_once 'conn.php';
$ndate =date("Y-m-d");
$addnew=$_POST["addnew"];
if ($addnew=="1" )
{
	$banjihao=$_POST["banjihao"];$shifadi=$_POST["shifadi"];$mudedi=$_POST["mudedi"];$piaojia=$_POST["piaojia"];$qifeishijian=$_POST["qifeishijian"];$beizhu=$_POST["beizhu"];
	$sql="insert into hangbanxinxi(banjihao,shifadi,mudedi,piaojia,qifeishijian,beizhu) values('$banjihao','$shifadi','$mudedi','$piaojia','$qifeishijian','$beizhu') ";
  mysqli_query('set names utf8');
	mysqli_query($sql);
	echo "<script>javascript:alert('添加成功!');location.href='hangbanxinxi_add.php';</script>";
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="css/jquery-ui.css">
<link rel="stylesheet" media="all" type="text/css" href="css/jquery-ui-timepicker-addon.css" />
<title>航班信息</title><script language="javascript" src="js/Calendar.js"></script><link rel="stylesheet" href="css.css" type="text/css">
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
<p>添加航班信息： 当前日期： <?php echo $ndate; ?></p>
<script language="javascript">
	function check()
{
	if(document.form1.banjihao.value==""){alert("请输入班机号");document.form1.banjihao.focus();return false;}if(document.form1.shifadi.value==""){alert("请输入始发地");document.form1.shifadi.focus();return false;}if(document.form1.mudedi.value==""){alert("请输入目的地");document.form1.mudedi.focus();return false;}if(document.form1.piaojia.value==""){alert("请输入票价");document.form1.piaojia.focus();return false;}if(document.form1.qifeishijian.value==""){alert("请输入起飞时间");document.form1.qifeishijian.focus();return false;}
}
	function gow()
	{
		location.href='peixunccccailiao_add.php?jihuabifffanhao='+document.form1.jihuabifffanhao.value;
	}
</script>
<form id="form1" name="form1" method="post" action="">
<table width="100%" border="1" align="center" cellpadding="3" cellspacing="1" bordercolor="#00FFFF" style="border-collapse:collapse">    
	<tr><td>班机号：</td><td><input name='banjihao' type='text' id='banjihao' value='' />&nbsp;*</td></tr><tr><td>始发地：</td><td><input name='shifadi' type='text' id='shifadi' value='' />&nbsp;*</td></tr><tr><td>目的地：</td><td><input name='mudedi' type='text' id='mudedi' value='' />&nbsp;*</td></tr><tr><td>票价：</td><td><input name='piaojia' type='text' id='piaojia' value='' />&nbsp;*</td></tr><tr><td>起飞时间：</td><td><input class="input" type="text" id="opentime" name="qifeishijian"  required>&nbsp;*</td></tr><tr><td>备注：</td><td><textarea name='beizhu' cols='50' rows='8' id='beizhu'></textarea></td></tr>

    <tr>
      <td>&nbsp;</td>
      <td><input type="hidden" name="addnew" value="1" />
        <input type="submit" name="Submit" value="添加" onclick="return check();" />
      <input type="reset" name="Submit2" value="重置" /></td>
    </tr>
  </table>
</form>
<p>&nbsp;</p>
</body>
</html>
<script src="js/jquery_002.js"></script>
<script src="js/jquery-ui.js"></script>
<script type="text/javascript" src="js/jquery-ui-timepicker-addon.js"></script>
<script>
  $("#opentime").datetimepicker({
        dateFormat: 'yy-mm-dd', inline: true,
        monthNames: ["1月", "2月", "3月", "4月", "5月", "6月", "7月", "8月", "9月", "10月", "11月", "12月"],
        dayNamesMin: ["日", "一", "二", "三", "四", "五", "六"],
        // onSelect: function (dateText, inst) {
        //     var theDate = new Date(Date.parse($(this).datepicker('getDate')));
        //     var dateFormatted = $.datepicker.formatDate('yy-mm-dd', theDate);
        // }
    });
</script>



