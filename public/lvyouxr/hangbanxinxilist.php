<?php
session_start();
include_once 'conn.php';
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
                        <td width="12%" align="center" valign="bottom"><span class="STYLE4">������Ϣ</span></td>
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
                          ����ţ�
                              <input name="banjihao" type="text" id="banjihao" size="15" />
                          ʼ���أ�
  <input name="shifadi" type="text" id="shifadi" size="15" />
                          Ŀ�ĵأ�
  <input name="mudedi" type="text" id="mudedi" size="15" />
  <input type="submit" name="Submit" value="����" />
                        </form>
                          <table width="100%" border="1" align="center" cellpadding="3" cellspacing="1" bordercolor="#00FFFF" style="border-collapse:collapse">
                            <tr>
                              <td width="25" bgcolor="#CCFFFF">���</td>
                              <td bgcolor='#CCFFFF'>�����</td>
                              <td bgcolor='#CCFFFF'>ʼ����</td>
                              <td bgcolor='#CCFFFF'>Ŀ�ĵ�</td>
                              <td bgcolor='#CCFFFF'>Ʊ��</td>
                              <td bgcolor='#CCFFFF'>���ʱ��</td>
                              <td bgcolor='#CCFFFF'>��ע</td>
                              <!-- <td width="120" align="center" bgcolor="#CCFFFF">���ʱ��</td> -->
                              </tr>
                            <?php 
    $sql="select * from hangbanxinxi where 1=1";
  
if ($_POST["banjihao"]!=""){$nreqbanjihao=$_POST["banjihao"];$sql=$sql." and banjihao like '%$nreqbanjihao%'";}
if ($_POST["shifadi"]!=""){$nreqshifadi=$_POST["shifadi"];$sql=$sql." and shifadi like '%$nreqshifadi%'";}
if ($_POST["mudedi"]!=""){$nreqmudedi=$_POST["mudedi"];$sql=$sql." and mudedi like '%$nreqmudedi%'";}
  $sql=$sql." order by id desc";
  
$query=mysqli_query($sql);
  $rowscount=mysqli_num_rows($query);
  if($rowscount==0)
  {}
  else
  {
  $pagelarge=10;//ÿҳ������
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
                              <td><?php echo mysqli_result($query,$i,banjihao);?></td>
                              <td><?php echo mysqli_result($query,$i,shifadi);?></td>
                              <td><?php echo mysqli_result($query,$i,mudedi);?></td>
                              <td><?php echo mysqli_result($query,$i,piaojia);?></td>
                              <td><?php echo mysqli_result($query,$i,qifeishijian);?></td>
                              <td><?php echo mysqli_result($query,$i,beizhu);?></td>
                            
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
                          <p align="center"><a href="hangbanxinxilist.php?pagecurrent=1">��ҳ</a>, <a href="hangbanxinxilist.php?pagecurrent=<?php echo $pagecurrent-1;?>">ǰһҳ</a> ,<a href="hangbanxinxilist.php?pagecurrent=<?php echo $pagecurrent+1;?>">��һҳ</a>, <a href="hangbanxinxilist.php?pagecurrent=<?php echo $pagecount;?>">ĩҳ</a>, ��ǰ��<?php echo $pagecurrent;?>ҳ,��<?php echo $pagecount;?>ҳ </p>                          <p align="center">&nbsp;</p>                          
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