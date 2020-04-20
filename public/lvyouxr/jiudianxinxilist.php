<?php
session_start();
include_once 'conn.php';
?>
<html>
<head>
<title>芒果旅游网站</title>
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
                        <td width="12%" align="center" valign="bottom"><span class="STYLE4">酒店信息</span></td>
                        <td width="74%" valign="bottom">&nbsp;</td>
                        <td width="14%" valign="bottom" class="STYLE4"></td>
                      </tr>
                    </table></td>
                  </tr>
                  <tr>
                    <td height="760" valign="top"><table id="__01" width="785" height="100%" border="0" cellpadding="0" cellspacing="0">
                      <tr>
                        <td width="19" background="qtimages/1_02_02_02_02_01.jpg">&nbsp;</td>
                        <td width="737" height="176" valign="top"><form id="form1" name="form1" method="post" action="">
                          酒店名称：
                              <input name="jiudianmingcheng" type="text" id="jiudianmingcheng" size="15" />
                          星级：
  <select name='xingji' id='xingji'>
    <option value="">所有</option>
    <option value="五星级">五星级</option>
    <option value="四星级">四星级</option>
    <option value="三星级">三星级</option>
    <option value="二星级">二星级</option>
  </select>
                          电话：
  <input name="dianhua" type="text" id="dianhua" size="15" />
                          地址：
  <input name="dizhi" type="text" id="dizhi" size="15" />
  <input type="submit" name="Submit" value="查找" />
                        </form>
                          <table width="100%" border="1" align="center" cellpadding="3" cellspacing="1" bordercolor="#00FFFF" style="border-collapse:collapse">
                            <tr>
                              <td width="25" bgcolor="#CCFFFF">序号</td>
                              <td bgcolor='#CCFFFF'>酒店名称</td>
                              <td bgcolor='#CCFFFF'>星级</td>
                              <td bgcolor='#CCFFFF'>电话</td>
                              <td bgcolor='#CCFFFF'>地址</td>
                              <td bgcolor='#CCFFFF'>照片</td>
                              <td bgcolor='#CCFFFF'>备注</td>
                              
                              <td width="70" align="center" bgcolor="#CCFFFF">操作</td>
                            </tr>
                            <?php 
    $sql="select * from jiudianxinxi where 1=1";
  
if ($_POST["jiudianmingcheng"]!=""){$nreqjiudianmingcheng=$_POST["jiudianmingcheng"];$sql=$sql." and jiudianmingcheng like '%$nreqjiudianmingcheng%'";}
if ($_POST["xingji"]!=""){$nreqxingji=$_POST["xingji"];$sql=$sql." and xingji like '%$nreqxingji%'";}
if ($_POST["dianhua"]!=""){$nreqdianhua=$_POST["dianhua"];$sql=$sql." and dianhua like '%$nreqdianhua%'";}
if ($_POST["dizhi"]!=""){$nreqdizhi=$_POST["dizhi"];$sql=$sql." and dizhi like '%$nreqdizhi%'";}
  $sql=$sql." order by id desc";
  
$query=mysqli_query($sql);
  $rowscount=mysql_num_rows($query);
  if($rowscount==0)
  {}
  else
  {
  $pagelarge=10;//每页行数；
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
                              <td width="25"><?php
	echo $i+1;
?></td>
                              <td><?php echo mysql_result($query,$i,jiudianmingcheng);?></td>
                              <td><?php echo mysql_result($query,$i,xingji);?></td>
                              <td><?php echo mysql_result($query,$i,dianhua);?></td>
                              <td><?php echo mysql_result($query,$i,dizhi);?></td>
                              <td width='80'><a href="<?php echo mysql_result($query,$i,zhaopian) ?>" target='_blank'><img src='<?php echo mysql_result($query,$i,zhaopian) ?>' width='80' height='88' border='0'></a></td>
                              <td><?php echo mysql_result($query,$i,beizhu);?></td>
                             
                              <td width="70" align="center"><a href="jiudianyudingadd.php?id=<?php
		echo mysql_result($query,$i,"id");
	?>">预订</a></td>
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
                          <p align="center"><a href="jiudianxinxilist.php?pagecurrent=1">首页</a>, <a href="jiudianxinxilist.php?pagecurrent=<?php echo $pagecurrent-1;?>">前一页</a> ,<a href="jiudianxinxilist.php?pagecurrent=<?php echo $pagecurrent+1;?>">后一页</a>, <a href="jiudianxinxilist.php?pagecurrent=<?php echo $pagecount;?>">末页</a>, 当前第<?php echo $pagecurrent;?>页,共<?php echo $pagecount;?>页 </p>                          <p align="center">&nbsp;</p>                          
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