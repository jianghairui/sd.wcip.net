<?php 
$id=$_GET["id"];
include_once 'conn.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>������ϸ</title><link rel="stylesheet" href="css.css" type="text/css">
</head>
<body>
<p>������ϸ�� ��ǰ���ڣ� <?php echo $ndate; ?></p>
<?php
$sql="select * from lvyouxianlu where id=".$id;
$query=mysqli_query($sql);
$rowscount=mysqli_num_rows($query);
if($rowscount>0)
{
?>

<table width="100%" border="1" align="center" cellpadding="3" cellspacing="1" bordercolor="#00FFFF" style="border-collapse:collapse"> 
      <td width='11%'>��ţ�</td><td width='39%'><?php echo mysqli_result($query,0,bianhao);?></td><td width='11%'>���ƣ�</td><td width='39%'><?php echo mysqli_result($query,0,mingcheng);?></td></tr><tr><td width='11%'>�����أ�</td><td width='39%'><?php echo mysqli_result($query,0,chufadi);?></td><td width='11%'>Ŀ�ĵأ�</td><td width='39%'><?php echo mysqli_result($query,0,mudedi);?></td></tr><tr><td width='11%'>����ʱ�䣺</td><td width='39%'><?php echo mysqli_result($query,0,chuxingshijian);?></td><td width='11%'>�۸�</td><td width='39%'><?php echo mysqli_result($query,0,jiage);?></td></tr><tr><td width='11%'>����ʱ����</td><td width='39%'><?php echo mysqli_result($query,0,chuxingshichang);?></td><td width='11%'>��ͨ���ߣ�</td><td width='39%'><?php echo mysqli_result($query,0,jiaotonggongju);?></td></tr><tr><td width='11%'>��ע��</td><td width='39%'><?php echo mysqli_result($query,0,beizhu);?></td><td>&nbsp;</td><td>&nbsp;</td>
    <tr>
      <td colspan="4" align="center"><input type="button" name="Submit" value="����" onclick="javascript:history.back()" /></td>
    </tr>
  </table>

<?php
	}
?>
<p>&nbsp;</p>
</body>
</html>

