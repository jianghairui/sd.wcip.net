<?php
session_start();
include_once 'conn.php';
$lb=$_GET["lb"];
?>
<html>
<head>
<title>â��������վ</title>
<link href="qtimages/StyleSheet.css" rel="stylesheet" type="text/css">
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<style type="text/css">
<!--
.STYLE2 {color: #FFFFFF}
.STYLE4 {color: #FFFFFF; font-weight: bold; }
.STYLE6 {color: #198A95; font-weight: bold; }
-->
</style>
<style type="text/css">
<!--
.STYLE8 {
	color: #000099;
	font-weight: bold;
	font-size: 14px;
}
-->
</style>
</head>
<body bgcolor="#FFFFFF" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">

<table width="900" height="964" border="0" align="center" cellpadding="0" cellspacing="0" id="__01">
	<tr>
		<td>
			<style type="text/css">
<!--
.STYLE5 {	color: #72AC27;
	font-size: 26pt;
}
.STYLE6 {color: #FFFFFF}
-->
</style>
<?php include_once 'qttop.php';?></td>
	</tr>
	<tr>
		<td height="541"><table id="__01" width="900" height="532" border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td height="532" valign="top"><table id="__01" width="220" height="532" border="0" cellpadding="0" cellspacing="0">
              <tr>
                <td colspan="2"><img src="qtimages/img_02_01_01.gif" width="220" height="3" alt=""></td>
              </tr>
              <tr>
                <td width="9" height="100%" rowspan="3" background="qtimages/img_02_01_02.gif">&nbsp;</td>
                <td width="211" height="150" valign="middle" background="qtimages/img_02_01_03.gif">
                  <p>&nbsp;</p>
                  
<?php include_once 'userlog.php';?>
				                  </td>
              </tr>
              <tr>
                <td height="183"><?php include_once 'left1.php';?></td>
              </tr>
              <tr>
                <td height="183"><?php include_once 'left2.php';?></td>
              </tr>
              
              <tr>
                <td height="10" colspan="2" background="qtimages/img_02_01_07.gif">&nbsp;</td>
              </tr>
            </table></td>
            <td width="4" background="qtimages/img_02_02.gif">&nbsp;</td>
            <td valign="top"><table id="__01" width="676" height="506" border="0" cellpadding="0" cellspacing="0">
              
              <tr>
                <td height="136" valign="top"><table id="__01" width="676" height="136" border="0" cellpadding="0" cellspacing="0">
                  <tr>
                    <td width="676" height="34" background="qtimages/img_02_03_02_01.gif"><table width="100%" border="0" cellpadding="0" cellspacing="0">
                      <tr>
                        <td width="28%" align="center"><strong><font color="#198A95">��ǰλ�ã�</font><a href="index.php"><font color="#198A95">��ҳ</font></a> <font color="#198A95">&gt;&gt; ��Ʒչʾ </font></strong></td>
                        <td width="72%">&nbsp;</td>
                      </tr>
                    </table></td>
                  </tr>
                  <tr>
                    <td width="676" height="101" align="left" background="qtimages/img_02_03_02_02.gif">
				
					<table width="96%" border="1" align="left" cellpadding="3" cellspacing="1" bordercolor="#00FFFF" style="border-collapse:collapse">  

                      <tr>
                        <td height="104"><form id="form1" name="form1" method="post" action="">
                          ����:���:
                          <input name="bh" type="text" id="bh" />
                          ����:
  <input name="mc" type="text" id="mc" />
  <input type="submit" name="Submit" value="����" />
                        </form>
                          <table width="100%" border="1" align="center" cellpadding="3" cellspacing="1" bordercolor="#00FFFF" style="border-collapse:collapse">
                            <tr>
                              <td bgcolor='#EBE2FE'>���</td>
                              <td bgcolor='#EBE2FE'>����</td>
                              <td bgcolor='#EBE2FE'>���</td>
                              <td bgcolor='#EBE2FE'>ͼƬ</td>
                              <td bgcolor='#EBE2FE'>�۸�</td>
                              <td width="70" align="center" bgcolor="#EBE2FE">����</td>
                            </tr>
                            <?php 
    $sql="select * from shangpinxinxi where 1=1";
  if ($_POST["bh"]!="")
{
  	$nreqbh=$_POST["bh"];
  	$sql=$sql." and bianhao like '%$nreqbh%'";
}
     if ($_POST["mc"]!="")
{
  	$nreqmc=$_POST["mc"];
  	$sql=$sql." and mingcheng like '%$nreqmc%'";
}
  $sql=$sql." order by id desc";
  
$query=mysqli_query($sql);
  $rowscount=mysqli_num_rows($query);
  if($rowscount==0)
  {}
  else
  {
  $pagelarge=4;//ÿҳ������
  $pagecurrent=$_GET["pagecurrent"];
  if($rowscount%$pagelarge==0)
  {
		$pagecount=$rowscount/$pagelarge;
  }
  else
  {
   		$pagecount=intval($rowscount/$pagelarge)+1;
  }
  if($pagecurrent=="" || $pagecurrent<=0)
{
	$pagecurrent=1;
}
 
if($pagecurrent>$pagecount)
{
	$pagecurrent=$pagecount;
}
		$ddddd=$pagecurrent*$pagelarge;
	if($pagecurrent==$pagecount)
	{
		if($rowscount%$pagelarge==0)
		{
		$ddddd=$pagecurrent*$pagelarge;
		}
		else
		{
		$ddddd=$pagecurrent*$pagelarge-$pagelarge+$rowscount%$pagelarge;
		}
	}

	for($i=$pagecurrent*$pagelarge-$pagelarge;$i<$ddddd;$i++)
{
  ?>
                            <tr>
                              <td><?php echo mysqli_result($query,$i,bianhao);?></td>
                              <td><?php echo mysqli_result($query,$i,mingcheng);?></td>
                              <td><?php echo mysqli_result($query,$i,leibie);?></td>
                              <td width='80'><a href="<?php echo mysqli_result($query,$i,tupian) ?>" target='_blank'><img src='<?php echo mysqli_result($query,$i,tupian) ?>' width='80' height='88' border='0'></a></td>
                              <td><?php echo mysqli_result($query,$i,jiage);?></td>
                              <td width="70" align="center"><a href="pro_detail.php?id=<?php
		echo mysqli_result($query,$i,"id");
	?>">��ϸ</a></td>
                            </tr>
                            <?php
	}
}
?>
                          </table>
                          <p>�������ݹ�
                              <?php
		echo $rowscount;
	?>
                            ��,
                            <input type="button" name="Submit2" onclick="javascript:window.print();" value="��ӡ��ҳ" />
                          </p>
                          <p align="center"><a href="prolist.php?pagecurrent=1">��ҳ</a>, <a href="prolist.php?pagecurrent=<?php echo $pagecurrent-1;?>">ǰһҳ</a> ,<a href="prolist.php?pagecurrent=<?php echo $pagecurrent+1;?>">��һҳ</a>, <a href="prolist.php?pagecurrent=<?php echo $pagecount;?>">ĩҳ</a>, ��ǰ��<?php echo $pagecurrent;?>ҳ,��<?php echo $pagecount;?>ҳ </p></td>
                      </tr>
                      <tr>
                        <td align="right"><a href="#" onClick="javascript:history.back();">����</a></td>
                      </tr>
                    </table>
					
					</td>
                  </tr>
                  <tr>
                    <td><img src="qtimages/img_02_03_02_03.gif" width="676" height="1" alt=""></td>
                  </tr>
                </table></td>
              </tr>
              

              
            </table></td>
          </tr>
      </table></td>
	</tr>
	<tr>
		<td><?php include_once 'qtdown.php';?></td>
	</tr>
</table>
<!-- End ImageReady Slices -->
</body>
</html>