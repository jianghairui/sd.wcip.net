<?php
error_reporting(E_ALL &~ E_NOTICE);
session_start();
if($_SESSION["username"]=="")
{
	echo "<script>javascript:alert('�Բ��������ȵ�½��');location.href='index.php';</script>";
	exit;
}
include_once 'conn.php';
$ndate =date("Y-m-d");
$addnew=$_POST["addnew"];
$id=$_GET["id"];
if ($addnew=="1" )
{
	$jiudianmingcheng=$_POST["jiudianmingcheng"];$xingji=$_POST["xingji"];$dianhua=$_POST["dianhua"];$dizhi=$_POST["dizhi"];$yudingren=$_POST["yudingren"];$yudingshijian=$_POST["yudingshijian"];$yudingrenshu=$_POST["yudingrenshu"];$beizhu=$_POST["beizhu"];
	$sql="insert into jiudianyuding(jiudianmingcheng,xingji,dianhua,dizhi,yudingren,yudingshijian,yudingrenshu,beizhu) values('$jiudianmingcheng','$xingji','$dianhua','$dizhi','$yudingren','$yudingshijian','$yudingrenshu','$beizhu') ";
	mysqli_query($sql);
	echo "<script>javascript:alert('�����ɹ�!');location.href='jiudianyudingadd.php?id=$id';</script>";
}
?>
<html>
<head>
<title>â��������վ</title><script language="javascript" src="js/Calendar.js"></script>
<link rel="stylesheet" type="text/css" href="css/jquery-ui.css">
<link rel="stylesheet" media="all" type="text/css" href="css/jquery-ui-timepicker-addon.css" />
<LINK href="qtimages/style.css" type=text/css rel=stylesheet>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<style type="text/css">
<!--
.STYLE1 {
	color: #529915;
	font-weight: bold;
}
.STYLE4 {color: #FFFFFF; font-weight: bold; }
.STYLE2 {color: #6D2E18; font-weight: bold; }
-->
</style>
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
<body bgcolor="#FFFFFF" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table width="993" height="1092" border="0" align="center" cellpadding="0" cellspacing="0" id="__01">
	<tr>
		<td><?php include_once 'qttop.php';?></td>
	</tr>
	<tr>
		<td><table id="__01" width="993" height="846" border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td valign="top"><?php include_once 'qtleft.php';?></td>
            <td height="830" valign="top"><table id="__01" width="785" height="830" border="0" cellpadding="0" cellspacing="0">
              
              <tr>
                <td valign="top"><table width="785" height="833" border="0" cellpadding="0" cellspacing="0" id="__01">
                  <tr>
                    <td width="785" height="40" background="qtimages/1_02_02_02_01.jpg"><table width="100%" height="19" border="0" cellpadding="0" cellspacing="0">
                      <tr>
                        <td width="12%" align="center" valign="bottom"><span class="STYLE4">�Ƶ�Ԥ��</span></td>
                        <td width="74%" valign="bottom">&nbsp;</td>
                        <td width="14%" valign="bottom" class="STYLE4"></td>
                      </tr>
                    </table></td>
                  </tr>
                  <tr>
                    <td height="760" valign="top"><table id="__01" width="785" height="100%" border="0" cellpadding="0" cellspacing="0">
                      <tr>
                        <td width="19" background="qtimages/1_02_02_02_02_01.jpg">&nbsp;</td>
                        <td width="737" height="176" valign="top"><form name="form1" method="post" action="">
						<?php
$sql="select * from jiudianxinxi where id=".$id;
$query=mysqli_query($sql);
$rowscount=mysqli_num_rows($query);
if($rowscount>0)
{
$i=0;
?>
                          <table width="100%" border="1" align="center" cellpadding="3" cellspacing="1" bordercolor="#00FFFF" style="border-collapse:collapse">
                            <tr>
                              <td>�Ƶ����ƣ�</td>
                              <td><input name='jiudianmingcheng' type='text' id='jiudianmingcheng' size='50' value='<?php echo mysqli_result($query,$i,jiudianmingcheng);?>' />                                &nbsp;*</td>
                            </tr>
                            <tr>
                              <td>�Ǽ���</td>
                              <td><input name='xingji' type='text' id='xingji' value='<?php echo mysqli_result($query,$i,xingji);?>' />
                                &nbsp;*</td>
                            </tr>
                            <tr>
                              <td>�绰��</td>
                              <td><input name='dianhua' type='text' id='dianhua' value='<?php echo mysqli_result($query,$i,dianhua);?>' /></td>
                            </tr>
                            <tr>
                              <td>��ַ��</td>
                              <td><input name='dizhi' type='text' id='dizhi' value='<?php echo mysqli_result($query,$i,dizhi);?>' size='50'  /></td>
                            </tr>
                            <tr>
                              <td>Ԥ���ˣ�</td>
                              <td><input name='yudingren' type='text' id='yudingren' value='<?php echo $_SESSION['username'];?>' />
                                &nbsp;*</td>
                            </tr>
                            <tr>
                              <td>Ԥ��ʱ�䣺</td>
                              <td><input name='yudingshijian' type='text' id='yudingshijian' value=''/>
                                &nbsp;*</td>
                            </tr>
                            <tr>
                              <td>Ԥ��������</td>
                              <td><input name='yudingrenshu' type='text' id='yudingrenshu' value='' /></td>
                            </tr>
                            <tr>
                              <td>��ע��</td>
                              <td><textarea name='beizhu' cols='50' rows='8' id='beizhu'></textarea></td>
                            </tr>
                            <tr>
                              <td>&nbsp;</td>
                              <td><input type="hidden" name="addnew" value="1" />
                                  <input type="submit" name="Submit" value="���" onclick="return check();" />
                                  <input type="reset" name="Submit2" value="����" /></td>
                            </tr>
                          </table><?php
	}
?>
                                                </form>                          <p align="center">&nbsp;</p>                          
                        </td>
                        <td width="29" background="qtimages/1_02_02_02_02_03.jpg">&nbsp;</td>
                      </tr>
                    </table></td>
                  </tr>
                  <tr>
                    <td height="12"><img src="qtimages/1_02_02_02_03.jpg" width="785" height="12" alt=""></td>
                  </tr>
                </table></td>
              </tr>
              
              <tr>
                <td height="13"><img src="qtimages/1_02_02_04.jpg" width="785" height="13" alt=""></td>
              </tr>
            </table></td>
          </tr>
        </table></td>
	</tr>
	<tr>
		<td><?php include_once 'qtdown.php';?></td>
	</tr>
</table>
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