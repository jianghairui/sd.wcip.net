<?php
session_start();
include_once 'conn.php';
$ndate =date("Y-m-d");
$addnew=$_POST["addnew"];
if ($addnew=="1" )
{
	$bianhao=$_POST["bianhao"];$mingcheng=$_POST["mingcheng"];$chufadi=$_POST["chufadi"];$mudedi=$_POST["mudedi"];$chuxingshijian=$_POST["chuxingshijian"];$jiage=$_POST["jiage"];$chuxingshichang=$_POST["chuxingshichang"];$jiaotonggongju=$_POST["jiaotonggongju"];$beizhu=$_POST["beizhu"];
	$sql="insert into lvyouxianlu(bianhao,mingcheng,chufadi,mudedi,chuxingshijian,jiage,chuxingshichang,jiaotonggongju,beizhu) values('$bianhao','$mingcheng','$chufadi','$mudedi','$chuxingshijian','$jiage','$chuxingshichang','$jiaotonggongju','$beizhu') ";
	mysqli_query('set names utf8');
	mysqli_query($sql);
	echo "<script>javascript:alert('添加成功!');location.href='lvyouxianlu_add.php';</script>";
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>旅游线路</title>
<link rel="stylesheet" type="text/css" href="css/jquery-ui.css">
<script language="javascript" src="js/Calendar.js"></script><link rel="stylesheet" href="css.css" type="text/css">
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
<p>添加旅游线路： 当前日期： <?php echo $ndate; ?></p>
<script language="javascript">
	function check()
{
	if(document.form1.bianhao.value==""){alert("请输入编号");document.form1.bianhao.focus();return false;}if(document.form1.mingcheng.value==""){alert("请输入名称");document.form1.mingcheng.focus();return false;}if(document.form1.chufadi.value==""){alert("请输入出发地");document.form1.chufadi.focus();return false;}if(document.form1.mudedi.value==""){alert("请输入目的地");document.form1.mudedi.focus();return false;}if(document.form1.chuxingshijian.value==""){alert("请输入出行时间");document.form1.chuxingshijian.focus();return false;}if(document.form1.jiage.value==""){alert("请输入价格");document.form1.jiage.focus();return false;}
}
	function gow()
	{
		location.href='peixunccccailiao_add.php?jihuabifffanhao='+document.form1.jihuabifffanhao.value;
	}
</script>
<form id="form1" name="form1" method="post" action="">
<table width="100%" border="1" align="center" cellpadding="3" cellspacing="1" bordercolor="#00FFFF" style="border-collapse:collapse">    
	<tr><td>编号：</td><td><input name='bianhao' type='text' id='bianhao' value='' />&nbsp;*</td></tr><tr><td>名称：</td><td><input name='mingcheng' type='text' id='mingcheng' value='' size='50'  />&nbsp;*</td></tr><tr><td>出发地：</td><td><input name='chufadi' type='text' id='chufadi' value='' />&nbsp;*</td></tr><tr><td>目的地：</td><td><input name='mudedi' type='text' id='mudedi' value='' />&nbsp;*</td></tr><tr><td>出行时间：</td><td><input class="input" type="text" id="opentime" name="chuxingshijian"  required>&nbsp;*</td></tr><tr><td>价格：</td><td><input name='jiage' type='text' id='jiage' value='' />&nbsp;*</td></tr><tr><td>出行时长：</td><td><input name='chuxingshichang' type='text' id='chuxingshichang' value='' /></td></tr><tr><td>交通工具：</td><td><select name='jiaotonggongju' id='jiaotonggongju'>
	  <option value="汽车">汽车</option>
	  <option value="火车">火车</option>
	  <option value="飞机">飞机</option>
	  <option value="轮船">轮船</option>
	</select></td></tr><tr><td>备注：</td><td><textarea name='beizhu' cols='50' rows='8' id='beizhu'></textarea></td></tr>

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
<script>
  $("#opentime").datepicker({
        dateFormat: 'yy-mm-dd', inline: true,
        monthNames: ["1月", "2月", "3月", "4月", "5月", "6月", "7月", "8月", "9月", "10月", "11月", "12月"],
        dayNamesMin: ["日", "一", "二", "三", "四", "五", "六"],
        onSelect: function (dateText, inst) {
            var theDate = new Date(Date.parse($(this).datepicker('getDate')));
            var dateFormatted = $.datepicker.formatDate('yy-mm-dd', theDate);
        }
    });
</script>

