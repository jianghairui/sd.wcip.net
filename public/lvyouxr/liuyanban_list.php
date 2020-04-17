<?php 
include_once 'conn.php';

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>���԰�</title><link rel="stylesheet" href="css.css" type="text/css">
</head>

<body>

<p>�������԰��б�</p>
<form id="form1" name="form1" method="post" action="">
  ����:�˺�:
  <input name="bh" type="text" id="bh" />
  ����:
  <input name="mc" type="text" id="mc" />
  <input type="submit" name="Submit" value="����" />
</form>
<table width="100%" border="1" align="center" cellpadding="3" cellspacing="1" bordercolor="#00FFFF" style="border-collapse:collapse">  
  <tr>
    <td width="25" bgcolor="#EBE2FE">���</td>
    <td width="152" bgcolor='#EBE2FE'>�˺�</td>
    <td width="80" bgcolor='#EBE2FE'>��Ƭ</td>
    <td width="92" bgcolor='#EBE2FE'>����</td>
    <td width="497" bgcolor='#EBE2FE'>����</td>
    <td width="123" align="center" bgcolor="#EBE2FE">�ظ�</td>
    <td width="123" align="center" bgcolor="#EBE2FE">���ʱ��</td>
    <td width="106" align="center" bgcolor="#EBE2FE">����</td>
  </tr>
  <?php 
    $sql="select * from liuyanban where 1=1";
  if ($_POST["bh"]!="")
{
  	$nreqbh=$_POST["bh"];
  	$sql=$sql." and zhanghao like '%$nreqbh%'";
}
     if ($_POST["mc"]!="")
{
  	$nreqmc=$_POST["mc"];
  	$sql=$sql." and xingming like '%$nreqmc%'";
}
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
    <td><?php echo mysqli_result($query,$i,zhanghao);?></td><td><img src="<?php echo mysqli_result($query,$i,zhaopian);?>" width="75" height="82" /></td>
    <td><?php echo mysqli_result($query,$i,xingming);?></td><td><?php echo mysqli_result($query,$i,liuyan);?></td>
    <td width="123" align="center"><?php
echo mysqli_result($query,$i,"huifu");
?></td>
    <td width="123" align="center"><?php
echo mysqli_result($query,$i,"addtime");
?></td>
    <td width="106" align="center"><a href="liuyanban_huifu.php?id=<?php echo mysqli_result($query,$i,id);?>">�ظ�</a> <a href="del.php?id=<?php
		echo mysqli_result($query,$i,"id");
	?>&tablename=liuyanban" onclick="return confirm('���Ҫɾ����')">ɾ��</a> <a href="liuyanban_updt.php?id=<?php
		echo mysqli_result($query,$i,"id");
	?>"></a></td>
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
<p align="center"><a href="liuyanban_list.php?pagecurrent=1">��ҳ</a>, <a href="liuyanban_list.php?pagecurrent=<?php echo $pagecurrent-1;?>">ǰһҳ</a> ,<a href="liuyanban_list.php?pagecurrent=<?php echo $pagecurrent+1;?>">��һҳ</a>, <a href="liuyanban_list.php?pagecurrent=<?php echo $pagecount;?>">ĩҳ</a>, ��ǰ��<?php echo $pagecurrent;?>ҳ,��<?php echo $pagecount;?>ҳ </p>

<p>&nbsp; </p>

</body>
</html>

