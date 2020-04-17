<?php
session_start();
include_once 'conn.php';
$lb=$_GET["lb"];
$biaoti=$_POST["biaoti"];
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
                        <td width="12%" align="center" valign="bottom"><span class="STYLE4">վ������</span></td>
                        <td width="74%" valign="bottom">&nbsp;</td>
                        <td width="14%" valign="bottom" class="STYLE4"></td>
                      </tr>
                    </table></td>
                  </tr>
                  <tr>
                    <td height="760" valign="top"><table id="__01" width="785" height="100%" border="0" cellpadding="0" cellspacing="0">
                      <tr>
                        <td width="19" background="qtimages/1_02_02_02_02_01.jpg">&nbsp;</td>
                        <td width="737" height="176" valign="top"><table width="97%" border="0" align="center" cellpadding="0" cellspacing="0" class="newsline">
                          <?php 
    $sql="select * from xinwentongzhi where 1=1";
  
if ($biaoti!=""){$sql=$sql." and biaoti like '%$biaoti%'";}
if($lb!=""){$sql=$sql." and leibie='$lb'";}
  $sql=$sql." order by id desc";
  
$query=mysqli_query($sql);
  $rowscount=mysqli_num_rows($query);
  if($rowscount==0)
  {}
  else
  {
  $pagelarge=20;//ÿҳ������
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
                          <tr height="25">
                            <td width="2%" align="right" class="newsline">��</td>
                            <td width="65%" class="newsline"><a href="gg_detail.php?id=<?php echo mysqli_result($query,$i,"id");?>"><?php echo mysqli_result($query,$i,"biaoti");?></a></td>
                            <td width="11%" class="newsline">�����<?php echo mysqli_result($query,$i,"dianjilv");?>��</td>
                            <td width="22%" class="newsline"><?php echo mysqli_result($query,$i,"addtime");?></td>
                          </tr>
                          <?php
						}
					  }
					  ?>
                        </table>
                          <p align="center"><a href="news.php?pagecurrent=1&lb=<?php echo $lb;?>">��ҳ</a>, <a href="news.php?pagecurrent=<?php echo $pagecurrent-1;?>&lb=<?php echo $lb;?>">ǰһҳ</a> ,<a href="news.php?pagecurrent=<?php echo $pagecurrent+1;?>&lb=<?php echo $lb;?>">��һҳ</a>, <a href="news.php?pagecurrent=<?php echo $pagecount;?>&lb=<?php echo $lb;?>">ĩҳ</a>, ��ǰ��<?php echo $pagecurrent;?>ҳ,��<?php echo $pagecount;?>ҳ �������ݹ�
                            <?php
		echo $rowscount;
	?>
                            ��,
  <input type="button" name="Submit22" onClick="javascript:window.print();" value="��ӡ��ҳ" style=" height:19px; border:solid 1px #000000; color:#666666" />
                          </p>                          <p align="center">&nbsp;</p>
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