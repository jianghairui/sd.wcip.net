<?php 
include_once 'conn.php';

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>�Ƶ���Ϣ</title><script language="javascript" src="js/Calendar.js"></script><link rel="stylesheet" href="css.css" type="text/css">
</head>

<body>

<p>���оƵ���Ϣ�б�</p>
<form id="form1" name="form1" method="post" action="">
  ����: �Ƶ����ƣ�<input name="jiudianmingcheng" type="text" id="jiudianmingcheng" /> �Ǽ���
  <select name='xingji' id='xingji'>
    <option value="">����</option>
    <option value="���Ǽ�">���Ǽ�</option>
    <option value="���Ǽ�">���Ǽ�</option>
    <option value="���Ǽ�">���Ǽ�</option>
    <option value="���Ǽ�">���Ǽ�</option>
  </select> 
  �绰��
  <input name="dianhua" type="text" id="dianhua" /> ��ַ��<input name="dizhi" type="text" id="dizhi" />
  <input type="submit" name="Submit" value="����" />
</form>
<table width="100%" border="1" align="center" cellpadding="3" cellspacing="1" bordercolor="#00FFFF" style="border-collapse:collapse">  
  <tr>
    <td width="25" bgcolor="#CCFFFF">���</td>
    <td bgcolor='#CCFFFF'>�Ƶ�����</td><td bgcolor='#CCFFFF'>�Ǽ�</td><td bgcolor='#CCFFFF'>�绰</td><td bgcolor='#CCFFFF'>��ַ</td><td bgcolor='#CCFFFF'>��Ƭ</td><td bgcolor='#CCFFFF'>��ע</td>
    <td width="120" align="center" bgcolor="#CCFFFF">���ʱ��</td>
    <td width="70" align="center" bgcolor="#CCFFFF">����</td>
  </tr>
  <?php 
    $sql="select * from jiudianxinxi where 1=1";
  
if ($_POST["jiudianmingcheng"]!=""){$nreqjiudianmingcheng=$_POST["jiudianmingcheng"];$sql=$sql." and jiudianmingcheng like '%$nreqjiudianmingcheng%'";}
if ($_POST["xingji"]!=""){$nreqxingji=$_POST["xingji"];$sql=$sql." and xingji like '%$nreqxingji%'";}
if ($_POST["dianhua"]!=""){$nreqdianhua=$_POST["dianhua"];$sql=$sql." and dianhua like '%$nreqdianhua%'";}
if ($_POST["dizhi"]!=""){$nreqdizhi=$_POST["dizhi"];$sql=$sql." and dizhi like '%$nreqdizhi%'";}
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
    <td><?php echo mysqli_result($query,$i,jiudianmingcheng);?></td><td><?php echo mysqli_result($query,$i,xingji);?></td><td><?php echo mysqli_result($query,$i,dianhua);?></td><td><?php echo mysqli_result($query,$i,dizhi);?></td><td width='80'><a href="<?php echo mysqli_result($query,$i,zhaopian) ?>" target='_blank'><img src='<?php echo mysqli_result($query,$i,zhaopian) ?>' width='80' height='88' border='0'></a></td><td><?php echo mysqli_result($query,$i,beizhu);?></td>
    <td width="120" align="center"><?php
echo mysqli_result($query,$i,"addtime");
?></td>
    <td width="70" align="center"><a href="del.php?id=<?php
		echo mysqli_result($query,$i,"id");
	?>&tablename=jiudianxinxi" onclick="return confirm('���Ҫɾ����')">ɾ��</a> <a href="jiudianxinxi_updt.php?id=<?php
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
<p align="center"><a href="jiudianxinxi_list.php?pagecurrent=1">��ҳ</a>, <a href="jiudianxinxi_list.php?pagecurrent=<?php echo $pagecurrent-1;?>">ǰһҳ</a> ,<a href="jiudianxinxi_list.php?pagecurrent=<?php echo $pagecurrent+1;?>">��һҳ</a>, <a href="jiudianxinxi_list.php?pagecurrent=<?php echo $pagecount;?>">ĩҳ</a>, ��ǰ��<?php echo $pagecurrent;?>ҳ,��<?php echo $pagecount;?>ҳ </p>

<p>&nbsp; </p>

</body>
</html>

