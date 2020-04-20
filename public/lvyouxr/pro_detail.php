<?php
session_start();
include_once 'conn.php';
$id=$_GET["id"];
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
                        <td height="104"><?php
$sql="select * from shangpinxinxi where id=".$id;
$query=mysqli_query($sql);
$rowscount=mysql_num_rows($query);
if($rowscount>0)
{
?>
                          <form id="form1" name="form1" method="post" action="">
                            <table width="100%" border="1" align="center" cellpadding="3" cellspacing="1" bordercolor="#00FFFF" style="border-collapse:collapse">
                              <tr>
                                <td width="13%" height="37">编号：</td>
                                <td width="40%"><?php echo mysql_result($query,0,bianhao);?></td>
                                <td width="47%" rowspan="5" align="center"><a href="<?php echo mysql_result($query,0,tupian);?>" target="_blank"><img src="<?php echo mysql_result($query,0,tupian);?>" width="223" height="197" border="0"></a></td>
                              </tr>
                              <tr>
                                <td height="41">名称：</td>
                                <td><?php echo mysql_result($query,0,mingcheng);?></td>
                                </tr>
                              <tr>
                                <td height="42">类别：</td>
                                <td><?php echo mysql_result($query,0,leibie);?></td>
                                </tr>
                              
                              <tr>
                                <td height="43">发布人：</td>
                                <td><?php echo mysql_result($query,0,faburen);?></td>
                                </tr>
                              <tr>
                                <td height="36">价格：</td>
                                <td><?php echo mysql_result($query,0,jiage);?></td>
                                </tr>
                              <tr>
                                <td height="154">简介：</td>
                                <td colspan="2"><?php echo mysql_result($query,0,jianjie);?></td>
                                </tr>
                            </table>
                          </form>
</td>
                      </tr>
                      <tr>
                        <td height="34" align="center"><form name="form3" method="post" action="gwc.php?bh=<?php echo mysql_result($query,0,bianhao);?>&mc=<?php echo mysql_result($query,0,mingcheng);?>&jg=<?php echo mysql_result($query,0,jiage);?>">
                          我要订餐                          
 数量
                          <input name="shuliang" type="text" id="shuliang" value="1" size="5">
                          <input type="submit" name="Submit" value="确认订餐">
                          <a href="#" onClick="javascript:history.back();">返回</a>
                                                </form>
                        </td>
                        <form name="form2" method="post" action="">
                        </form>
                      </tr>
                    </table>
			                          <?php
	}
?>		
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