<?php
error_reporting(E_ALL &~ E_NOTICE);
session_start();
include_once 'conn.php';
?>
<html>
<head>
<title>â��������վ</title>
<link rel="stylesheet" type="text/css" href="css/jquery-ui.css">
<link rel="stylesheet" media="all" type="text/css" href="css/jquery-ui-timepicker-addon.css" />
<LINK href="qtimages/style.css" type=text/css rel=stylesheet>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<script type="text/javascript" src="js/jquery.js"></script>
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
                        <td width="12%" align="center" valign="bottom"><span class="STYLE4">������·</span></td>
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
                           ��ţ�
                               <input name="bianhao" type="text" id="bianhao" size="12" />
                          ���ƣ�
  <input name="mingcheng" type="text" id="mingcheng" size="12" />
                          �����أ�
  <input name="chufadi" type="text" id="chufadi" size="12" />
                          Ŀ�ĵأ�
  <input name="mudedi" type="text" id="mudedi" size="12" />
                          <br>
                          ����ʱ�䣺
  <input name="chuxingshijian1" type="text" id="chuxingshijian1"  value=''/>
                          -
  <input name="chuxingshijian2" type="text" id="chuxingshijian2"  value=''/>
                          ��ͨ���ߣ�
  <select name='jiaotonggongju' id='jiaotonggongju'>
    <option value="">����</option>
    <option value="����">����</option>
    <option value="��">��</option>
    <option value="�ɻ�">�ɻ�</option>
    <option value="�ִ�">�ִ�</option>
  </select>
  <input type="submit" name="Submit" value="����" />
                        </form>
                          <table width="100%" border="1" align="center" cellpadding="3" cellspacing="1" bordercolor="#00FFFF" style="border-collapse:collapse">
                            <tr>
                              <td width="39" bgcolor="#CCFFFF">���</td>
                              <td width="46" bgcolor='#CCFFFF'>���</td>
                              <td width="59" bgcolor='#CCFFFF'>����</td>
                              <td width="54" align="center" bgcolor='#CCFFFF'>������</td>
                              <td width="67" align="center" bgcolor='#CCFFFF'>Ŀ�ĵ�</td>
                              <td width="87" align="center" bgcolor='#CCFFFF'>����ʱ��</td>
                              <td width="65" align="center" bgcolor='#CCFFFF'>�۸�</td>
                              <td width="68" align="center" bgcolor='#CCFFFF'>����ʱ��</td>
                              <td width="87" align="center" bgcolor='#CCFFFF'>��ͨ����</td>
                              <td width="72" align="center" bgcolor="#CCFFFF">����</td>
                            </tr>
                            <?php 
    $sql="select * from lvyouxianlu where 1=1";
  
if ($_POST["bianhao"]!=""){$nreqbianhao=$_POST["bianhao"];$sql=$sql." and bianhao like '%$nreqbianhao%'";}
if ($_POST["mingcheng"]!=""){$nreqmingcheng=$_POST["mingcheng"];$sql=$sql." and mingcheng like '%$nreqmingcheng%'";}
if ($_POST["chufadi"]!=""){$nreqchufadi=$_POST["chufadi"];$sql=$sql." and chufadi like '%$nreqchufadi%'";}
if ($_POST["mudedi"]!=""){$nreqmudedi=$_POST["mudedi"];$sql=$sql." and mudedi like '%$nreqmudedi%'";}
if ($_POST["chuxingshijian1"]!=""){$nreqchuxingshijian1=$_POST["chuxingshijian1"];$sql=$sql." and chuxingshijian >= '$nreqchuxingshijian1'";}
if ($_POST["chuxingshijian2"]!=""){$nreqchuxingshijian2=$_POST["chuxingshijian2"];$sql=$sql." and chuxingshijian <= '$nreqchuxingshijian2'";}
if ($_POST["jiaotonggongju"]!=""){$nreqjiaotonggongju=$_POST["jiaotonggongju"];$sql=$sql." and jiaotonggongju like '%$nreqjiaotonggongju%'";}
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
                              <td width="39"><?php
	echo $i+1;
?></td>
                              <td><?php echo mysqli_result($query,$i,bianhao);?></td>
                              <td class="mingcheng"><?php echo mysqli_result($query,$i,mingcheng);?></td>
                              <td align="center"><?php echo mysqli_result($query,$i,chufadi);?></td>
                              <td align="center"><?php echo mysqli_result($query,$i,mudedi);?></td>
                              <td align="center"><?php echo mysqli_result($query,$i,chuxingshijian);?></td>
                              <td class="jiage" align="center"><?php echo mysqli_result($query,$i,jiage);?></td>
                              <td align="center"><?php echo mysqli_result($query,$i,chuxingshichang);?></td>
                              <td align="center"><?php echo mysqli_result($query,$i,jiaotonggongju);?></td>
                              <td width="72" align="center"><a href="lvyouxianludetail.php?id=<?php echo mysqli_result($query,$i,"id");?>">��ϸ</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:void(0)" class="book">Ԥ��</a></td>
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
                          <p align="center"><a href="lvyouxianlulist.php?pagecurrent=1">��ҳ</a>, <a href="lvyouxianlulist.php?pagecurrent=<?php echo $pagecurrent-1;?>">ǰһҳ</a> ,<a href="lvyouxianlulist.php?pagecurrent=<?php echo $pagecurrent+1;?>">��һҳ</a>, <a href="lvyouxianlulist.php?pagecurrent=<?php echo $pagecount;?>">ĩҳ</a>, ��ǰ��<?php echo $pagecurrent;?>ҳ,��<?php echo $pagecount;?>ҳ </p>                          <p align="center">&nbsp;</p>                          
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
<script src="js/jquery_002.js"></script>
<script src="js/jquery-ui.js"></script>
<script type="text/javascript" src="js/jquery-ui-timepicker-addon.js"></script>
<script>

	$("#chuxingshijian1").datepicker({
        dateFormat: 'yy-mm-dd', inline: true,
    });
  $("#chuxingshijian2").datepicker({
        dateFormat: 'yy-mm-dd', inline: true,
    });

	$('.book').click(function(){
			linename = $(this).parent().parent().find('.mingcheng').text();
			price = $(this).parent().parent().find('.jiage').text();
			username = <?php echo "'".$_SESSION['username']."'"; ?>;
			name = <?php echo "'".$_SESSION['xm']."'"; ?>;

			$.post('booktour.php',{username:username,name:name,linename:linename,price:price},function(data){
				alert(data);
			});
			
			
		
	})
		

</script>