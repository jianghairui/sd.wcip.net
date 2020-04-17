<?php
session_start();
include_once 'conn.php';
include_once 'sinaEditor.class.php';
extract($_POST);
extract($_GET);
$editor=new sinaEditor('neirong');
$editor->Value='';
$editor->BasePath='.';
$editor->Height=460;
$editor->Width=650;
$editor->AutoSave=false;
$ndate =date("Y-m-d");
$addnew=$_POST["addnew"];
$lb=$_GET["lb"];
if ($addnew=="1" )
{
	$biaoti=$_POST["biaoti"];$leibie=$_POST["leibie"];$neirong=$_POST["neirong"];$shouyetupian=$_POST["shouyetupian"];$dianjilv=$_POST["dianjilv"];$tianjiaren=$_POST["tianjiaren"];
	$sql="insert into xinwentongzhi(biaoti,leibie,neirong,shouyetupian,dianjilv,tianjiaren) values('$biaoti','$leibie','$neirong','$shouyetupian','$dianjilv','$tianjiaren') ";
	mysqli_query($sql);
	echo "<script>javascript:alert('��ӳɹ�!');location.href='xinwentongzhi_add.php?lb=$lb';</script>";
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>����֪ͨ</title><script language="javascript" src="js/Calendar.js"></script><link rel="stylesheet" href="css.css" type="text/css">
<script type="text/javascript" charset="utf-8" src="ueditor/ueditor.config.js"></script>
    <script type="text/javascript" charset="utf-8" src="ueditor/ueditor.all.min.js"> </script>
    <script type="text/javascript" charset="utf-8" src="ueditor/lang/zh-cn/zh-cn.js"></script>
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
<p>�������֪ͨ�� ��ǰ���ڣ� <?php echo $ndate; ?></p>
<script language="javascript">
	function check()
{
	if(document.form1.biaoti.value==""){alert("���������");document.form1.biaoti.focus();return false;}if(document.form1.leibie.value==""){alert("���������");document.form1.leibie.focus();return false;}if(document.form1.tianjiaren.value==""){alert("�����������");document.form1.tianjiaren.focus();return false;}
}
	function gow()
	{
		location.href='peixunccccailiao_add.php?jihuabifffanhao='+document.form1.jihuabifffanhao.value;
	}
</script>
<form id="form1" name="form1" method="post" action="">
<table width="100%" border="1" align="center" cellpadding="3" cellspacing="1" bordercolor="#00FFFF" style="border-collapse:collapse">    
	<tr><td>���⣺</td><td><input name='biaoti' type='text' id='biaoti' value='' size='50'  />&nbsp;*</td></tr><tr><td>���</td><td><input name='leibie' type='text' id='leibie' value='<?php echo $lb;?>' />&nbsp;*</td></tr>
	<tr>
		<td>���ݣ�</td>
		<td><textarea id="content" name="neirong" style="width:800px;min-height:300px;"></textarea></td>
	</tr>
	<tr><td>��ҳͼƬ��</td><td><input name='shouyetupian' type='text' id='shouyetupian' value='' size='50'  />&nbsp;<a href="javaScript:OpenScript('upfile.php?Result=shouyetupian',460,180)"><img src="Images/Upload.gif" width="30" height="16" border="0" align="absmiddle" /></a></td></tr><tr><td>����ʣ�</td><td><input name='dianjilv' type='text' id='dianjilv' value='1' /></td></tr><tr><td>����ˣ�</td><td><input name='tianjiaren' type='text' id='tianjiaren' value='<?php echo $_SESSION["username"]; ?>' />&nbsp;*</td></tr>

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
<script type="text/javascript">
    var ue = UE.getEditor('content');
</script>

