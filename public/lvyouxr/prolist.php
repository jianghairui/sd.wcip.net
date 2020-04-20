<?php
session_start();
include_once 'conn.php';
$lb=$_GET["lb"];
?>
<html>
<head>
<title>芒果旅游网站</title>
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
                        <td width="28%" align="center"><strong><font color="#198A95">当前位置：</font><a href="index.php"><font color="#198A95">首页</font></a> <font color="#198A95">&gt;&gt; 菜品展示 </font></strong></td>
                        <td width="72%">&nbsp;</td>
                      </tr>
                    </table></td>
                  </tr>
                  <tr>
                    <td width="676" height="101" align="left" background="qtimages/img_02_03_02_02.gif">
				
					<table width="96%" border="1" align="left" cellpadding="3" cellspacing="1" bordercolor="#00FFFF" style="border-collapse:collapse">  

                      <tr>
                        <td height="104"><form id="form1" name="form1" method="post" action="">
                          搜索:编号:
                          <input name="bh" type="text" id="bh" />
                          名称:
  <input name="mc" type="text" id="mc" />
  <input type="submit" name="Submit" value="查找" />
                        </form>
                          <table width="100%" border="1" align="center" cellpadding="3" cellspacing="1" bordercolor="#00FFFF" style="border-collapse:collapse">
                            <tr>
                              <td bgcolor='#EBE2FE'>编号</td>
                              <td bgcolor='#EBE2FE'>名称</td>
                              <td bgcolor='#EBE2FE'>类别</td>
                              <td bgcolor='#EBE2FE'>图片</td>
                              <td bgcolor='#EBE2FE'>价格</td>
                              <td width="70" align="center" bgcolor="#EBE2FE">操作</td>
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
  $rowscount=mysql_num_rows($query);
  if($rowscount==0)
  {}
  else
  {
  $pagelarge=4;//每页行数；
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
                              <td><?php echo mysql_result($query,$i,bianhao);?></td>
                              <td><?php echo mysql_result($query,$i,mingcheng);?></td>
                              <td><?php echo mysql_result($query,$i,leibie);?></td>
                              <td width='80'><a href="<?php echo mysql_result($query,$i,tupian) ?>" target='_blank'><img src='<?php echo mysql_result($query,$i,tupian) ?>' width='80' height='88' border='0'></a></td>
                              <td><?php echo mysql_result($query,$i,jiage);?></td>
                              <td width="70" align="center"><a href="pro_detail.php?id=<?php
		echo mysql_result($query,$i,"id");
	?>">详细</a></td>
                            </tr>
                            <?php
	}
}
?>
                          </table>
                          <p>以上数据共
                              <?php
		echo $rowscount;
	?>
                            条,
                            <input type="button" name="Submit2" onclick="javascript:window.print();" value="打印本页" />
                          </p>
                          <p align="center"><a href="prolist.php?pagecurrent=1">首页</a>, <a href="prolist.php?pagecurrent=<?php echo $pagecurrent-1;?>">前一页</a> ,<a href="prolist.php?pagecurrent=<?php echo $pagecurrent+1;?>">后一页</a>, <a href="prolist.php?pagecurrent=<?php echo $pagecount;?>">末页</a>, 当前第<?php echo $pagecurrent;?>页,共<?php echo $pagecount;?>页 </p></td>
                      </tr>
                      <tr>
                        <td align="right"><a href="#" onClick="javascript:history.back();">返回</a></td>
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