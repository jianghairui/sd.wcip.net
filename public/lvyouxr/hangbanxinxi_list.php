<?php 
include_once 'conn.php';

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>������Ϣ</title><script language="javascript" src="js/Calendar.js"></script><link rel="stylesheet" href="css.css" type="text/css">
</head>

<body>

<p>���к�����Ϣ�б�</p>
<form id="form1" name="form1" method="post" action="">
  ����: ����ţ�<input name="banjihao" type="text" id="banjihao" /> ʼ���أ�<input name="shifadi" type="text" id="shifadi" /> Ŀ�ĵأ�<input name="mudedi" type="text" id="mudedi" />
  <input type="submit" name="Submit" value="����" />
</form>
<table width="100%" border="1" align="center" cellpadding="3" cellspacing="1" bordercolor="#00FFFF" style="border-collapse:collapse">  
  <tr>
    <td width="25" bgcolor="#CCFFFF">���</td>
    <td bgcolor='#CCFFFF'>�����</td><td bgcolor='#CCFFFF'>ʼ����</td><td bgcolor='#CCFFFF'>Ŀ�ĵ�</td><td bgcolor='#CCFFFF'>Ʊ��</td><td bgcolor='#CCFFFF'>���ʱ��</td><td bgcolor='#CCFFFF'>��ע</td>
    <td width="120" align="center" bgcolor="#CCFFFF">���ʱ��</td>
    <td width="70" align="center" bgcolor="#CCFFFF">����</td>
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
    <td><?php echo mysqli_result($query,$i,banjihao);?></td><td><?php echo mysqli_result($query,$i,shifadi);?></td><td><?php echo mysqli_result($query,$i,mudedi);?></td><td><?php echo mysqli_result($query,$i,piaojia);?></td><td><?php echo mysqli_result($query,$i,qifeishijian);?></td><td><?php echo mysqli_result($query,$i,beizhu);?></td>
    <td width="120" align="center"><?php
echo mysqli_result($query,$i,"addtime");
?></td>
    <td width="70" align="center"><a href="del.php?id=<?php
		echo mysqli_result($query,$i,"id");
	?>&tablename=hangbanxinxi" onclick="return confirm('���Ҫɾ����')">ɾ��</a> <a href="hangbanxinxi_updt.php?id=<?php
		echo mysqli_result($query,$i,"id");
	?>">�޸�</a></td>
  </tr>
  	<?php
	}
}
?>
</table>
<p>�������ݹ�<?php
		echo $rowscount;
	?>��,
  <input type="button" name="Submit2" onclick="javascript:window.print();" value="��ӡ��ҳ" />
</p>
<p align="center"><a href="hangbanxinxi_list.php?pagecurrent=1">��ҳ</a>, <a href="hangbanxinxi_list.php?pagecurrent=<?php echo $pagecurrent-1;?>">ǰһҳ</a> ,<a href="hangbanxinxi_list.php?pagecurrent=<?php echo $pagecurrent+1;?>">��һҳ</a>, <a href="hangbanxinxi_list.php?pagecurrent=<?php echo $pagecount;?>">ĩҳ</a>, ��ǰ��<?php echo $pagecurrent;?>ҳ,��<?php echo $pagecount;?>ҳ </p>

<p>&nbsp; </p>

</body>
</html>

