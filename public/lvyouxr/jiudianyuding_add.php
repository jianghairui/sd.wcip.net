<?php
session_start();
include_once 'conn.php';
$ndate =date("Y-m-d");
$addnew=$_POST["addnew"];
if ($addnew=="1" )
{
	$jiudianmingcheng=$_POST["jiudianmingcheng"];$xingji=$_POST["xingji"];$dianhua=$_POST["dianhua"];$dizhi=$_POST["dizhi"];$yudingren=$_POST["yudingren"];$yudingshijian=$_POST["yudingshijian"];$yudingrenshu=$_POST["yudingrenshu"];$beizhu=$_POST["beizhu"];
	$sql="insert into jiudianyuding(jiudianmingcheng,xingji,dianhua,dizhi,yudingren,yudingshijian,yudingrenshu,beizhu) values('$jiudianmingcheng','$xingji','$dianhua','$dizhi','$yudingren','$yudingshijian','$yudingrenshu','$beizhu') ";
	mysqli_query($sql);
	echo "<script>javascript:alert('��ӳɹ�!');location.href='jiudianyuding_add.php';</script>";
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>�Ƶ�Ԥ��</title><script language="javascript" src="js/Calendar.js"></script><link rel="stylesheet" href="css.css" type="text/css">
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
<p>��ӾƵ�Ԥ���� ��ǰ���ڣ� <?php echo $ndate; ?></p>
<script language="javascript">
	function check()
{
	if(document.form1.jiudianmingcheng.value==""){alert("������Ƶ�����");document.form1.jiudianmingcheng.focus();return false;}if(document.form1.xingji.value==""){alert("�������Ǽ�");document.form1.xingji.focus();return false;}if(document.form1.yudingren.value==""){alert("������Ԥ����");document.form1.yudingren.focus();return false;}if(document.form1.yudingshijian.value==""){alert("������Ԥ��ʱ��");document.form1.yudingshijian.focus();return false;}
}
	function gow()
	{
		location.href='peixunccccailiao_add.php?jihuabifffanhao='+document.form1.jihuabifffanhao.value;
	}
</script>
<form id="form1" name="form1" method="post" action="">
<table width="100%" border="1" align="center" cellpadding="3" cellspacing="1" bordercolor="#00FFFF" style="border-collapse:collapse">    
	<tr><td>�Ƶ����ƣ�</td><td><input name='jiudianmingcheng' type='text' id='jiudianmingcheng' value='' size='50'  />&nbsp;*</td></tr><tr><td>�Ǽ���</td><td><input name='xingji' type='text' id='xingji' value='' />&nbsp;*</td></tr><tr><td>�绰��</td><td><input name='dianhua' type='text' id='dianhua' value='' /></td></tr><tr><td>��ַ��</td><td><input name='dizhi' type='text' id='dizhi' value='' size='50'  /></td></tr><tr><td>Ԥ���ˣ�</td><td><input name='yudingren' type='text' id='yudingren' value='<?php echo $_SESSION['username'];?>' />&nbsp;*</td></tr><tr><td>Ԥ��ʱ�䣺</td><td><input name='yudingshijian' type='text' id='yudingshijian' value='' onclick="getDate(form1.yudingshijian,'2')" need="1" />&nbsp;*</td></tr><tr><td>Ԥ��������</td><td><input name='yudingrenshu' type='text' id='yudingrenshu' value='' /></td></tr><tr><td>��ע��</td><td><textarea name='beizhu' cols='50' rows='8' id='beizhu'></textarea></td></tr>

    <tr>
      <td>&nbsp;</td>
      <td><input type="hidden" name="addnew" value="1" />
        <input type="submit" name="Submit" value="���" onclick="return check();" />
      <input type="reset" name="Submit2" value="����" /></td>
    </tr>
  </table>
</form>
<p>&nbsp;</p>
</body>
</html>

