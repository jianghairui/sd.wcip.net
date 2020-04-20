<?php
error_reporting(E_ALL &~ E_NOTICE);
session_start();
include_once 'conn.php';
$tp=$_POST["tp"];
if ($tp=="1" )
{
  $xxa=$_POST["xxa"];
  $sql="insert into toupiaojilu(xx,addby) values('$xxa','".$_SESSION['username']."') ";
  mysqli_query($sql);
  echo "<script>javascript:alert('投票成功!');location.href='toupiao.php';</script>";
}
?>
<html>
<head>
<title>芒果旅游网站</title>
<LINK href="qtimages/style.css" type=text/css rel=stylesheet>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<style type="text/css">
<!--
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
                        <td width="12%" align="center" valign="bottom"><span class="STYLE4">在线投票</span></td>
                        <td width="74%" valign="bottom">&nbsp;</td>
                        <td width="14%" valign="bottom" class="STYLE4"></td>
                      </tr>
                    </table></td>
                  </tr>
                  <tr>
                    <td height="760" valign="top"><table id="__01" width="785" height="100%" border="0" cellpadding="0" cellspacing="0">
                      <tr>
                        <td width="19" background="qtimages/1_02_02_02_02_01.jpg">&nbsp;</td>
                        <td width="737" height="176" valign="top"><p align="center">&nbsp;</p>                          
                          
                            <table width="97%" border="1" align="center" cellpadding="3" cellspacing="1" bordercolor="#67B41A" style="border-collapse:collapse">
                              <tr>
                                <td height="33" align="center"><strong>为您喜欢的线路投一票吧</strong></td>
                              </tr>
                              <tr>
                                <td height="175"><table width="84%" border="1" align="center" cellpadding="3" cellspacing="1" bordercolor="#00FFFF" style="border-collapse:collapse">
                                    <tr bgcolor="#CCFFFF">
                                      <td>ID</td>
                                      <td>线路编号</td>
                                      <td>线路名称</td>
                                      <td>价格</td>
                                      <td></td>
                                    
                                    </tr>
                                    <?php
$sql = "SELECT l.id,l.bianhao,l.mingcheng,l.jiage,IFNULL(t.nums,0) AS num FROM lvyouxianlu l LEFT JOIN (SELECT *,COUNT(lid) AS nums FROM toupiaojilu GROUP BY lid) t ON l.id=t.lid;";
//mysqli_query("set names utf8");
$result = mysqli_query($sql);
while(list($id,$bianhao,$name,$price,$nums) = mysql_fetch_row($result)){
  echo "<tr>";
  echo "
  <td class='numid'>$id</td>
  <td>$bianhao</td>
  <td>$name</td>
  <td>$price</td>
  <td><button class='toupiao'>投票</button>&nbsp;&nbsp;&nbsp;&nbsp;<span>$nums<span></td>";
  echo "</tr>";
}                                   ?>
<script src="js/jquery.js"></script>
<script>
  
  $('.toupiao').click(function(){
      username = <?php echo "'".$_SESSION['username']."'"; ?>;
      id = $(this).parent().parent().find('.numid').text();
      var mythis = $(this);
      $.post('toupiaojs.php',{username:username,id:id},function(data){
          alert(data);        
          if(data=='投票成功') {
            val = mythis.parent().parent().find('span').text();
            val = parseInt(val);
            mythis.parent().parent().find('span').text(val+1);
          }
      })
  })

</script>                                
                                </table></td>
                              </tr>
                              <tr>
                                <td align="right"><a href="#" onClick="javascript:history.back();">返回</a></td>
                              </tr>
                            </table>
                        
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