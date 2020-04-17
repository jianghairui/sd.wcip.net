<?php
session_start();
if($_SESSION["username"]=="")
{
	echo "<script>javascript:alert('�Բ��������ȵ�½��');location.href='index.php';</script>";
	exit;
}
include_once 'conn.php';
$id=$_GET["id"];
$addnew=$_POST["addnew"];
if ($addnew=="1" )
{
	
	$zhanghao=$_POST["zhanghao"];$zhaopian=$_POST["zhaopian"];$xingming=$_POST["xingming"];$liuyan=$_POST["liuyan"];
	$sql="insert into liuyanban(zhanghao,zhaopian,xingming,liuyan) values('$zhanghao','$zhaopian','$xingming','$liuyan') ";
	mysqli_query($sql);
	echo "<script>javascript:alert('���Գɹ�!');location.href='lyblist.php';</script>";
}
?>
<html>
<head>
<title>â��������վ</title>
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
	function check()
{
	if(document.form1.zhanghao.value==""){alert("�������˺�");document.form1.zhanghao.focus();return false;}if(document.form1.xingming.value==""){alert("����������");document.form1.xingming.focus();return false;}if(document.form1.liuyan.value==""){alert("����������");document.form1.liuyan.focus();return false;}
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
                        <td width="12%" align="center" valign="bottom"><span class="STYLE4">���԰�</span></td>
                        <td width="74%" valign="bottom">&nbsp;</td>
                        <td width="14%" valign="bottom" ><a href="lyblist.php"><font class="STYLE4">�鿴��������</a></a></td>
                      </tr>
                    </table></td>
                  </tr>
                  <tr>
                    <td height="760" valign="top"><table id="__01" width="785" height="100%" border="0" cellpadding="0" cellspacing="0">
                      <tr>
                        <td width="19" background="qtimages/1_02_02_02_02_01.jpg">&nbsp;</td>
                        <td width="737" height="176" valign="top"><form name="form1" method="post" action="">
                          <table width="96%" border="1" align="left" cellpadding="3" cellspacing="1" bordercolor="#67B41A" style="border-collapse:collapse">
                            <tr>
                              <td>�˺ţ�</td>
                              <td><input name='zhanghao' type='text' id='zhanghao' value='<?php echo $_SESSION["username"];?>' />
                                &nbsp;*</td>
                            </tr>
                            <tr>
                              <td>��Ƭ��</td>
                              <td><input name='zhaopian' type='hidden' id='zhaopian' value='<?php echo $_SESSION["zp"];?>' />
                                  <img src="<?php echo $_SESSION["zp"];?>" width="131" height="102"></td>
                            </tr>
                            <tr>
                              <td>������</td>
                              <td><input name='xingming' type='text' id='xingming' value='<?php echo $_SESSION["xm"];?>' />
                                &nbsp;*</td>
                            </tr>
                            <tr>
                              <td>���ԣ�</td>
                              <td><textarea name='liuyan' cols='50' rows='8' id='liuyan'></textarea>
                                &nbsp;*</td>
                            </tr>
                            <tr>
                              <td>&nbsp;</td>
                              <td><input type="hidden" name="addnew" value="1" />
                                  <input type="submit" name="Submit" value="���" onClick="return check();" style=" height:19px; border:solid 1px #000000; color:#666666"/>
                                  <input type="reset" name="Submit2" value="����" style=" height:19px; border:solid 1px #000000; color:#666666"/></td>
                            </tr>
                          </table>
                                                </form>
                        <p align="center">&nbsp;</p>                          
                          <p align="center">&nbsp;</p>                          
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