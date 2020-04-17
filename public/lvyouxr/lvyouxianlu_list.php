<?php 
include_once 'conn.php';

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>旅游线路</title>
<link rel="stylesheet" type="text/css" href="css/jquery-ui.css">
<script language="javascript" src="js/Calendar.js"></script><link rel="stylesheet" href="css.css" type="text/css">
</head>

<body>

<p>已有旅游线路列表：</p>
<form id="form1" name="form1" method="post" action="">
  搜索: 编号：<input name="bianhao" type="text" id="bianhao" size="12" /> 
  名称：<input name="mingcheng" type="text" id="mingcheng" size="12" /> 
  出发地：<input name="chufadi" type="text" id="chufadi" size="12" /> 
  目的地：<input name="mudedi" type="text" id="mudedi" size="12" /> 
  出行时间：<input name="chuxingshijian1" type="text" id="chuxingshijian1"  value=''/>-<input name="chuxingshijian2" type="text" id="chuxingshijian2"  value=''/> 交通工具：
  <select name='jiaotonggongju' id='jiaotonggongju'>
    <option value="">所有</option>
    <option value="汽车">汽车</option>
    <option value="火车">火车</option>
    <option value="飞机">飞机</option>
    <option value="轮船">轮船</option>
  </select>
  <input type="submit" name="Submit" value="查找" />
</form>
<table width="100%" border="1" align="center" cellpadding="3" cellspacing="1" bordercolor="#00FFFF" style="border-collapse:collapse">  
  <tr>
    <td width="25" bgcolor="#CCFFFF">序号</td>
    <td bgcolor='#CCFFFF'>编号</td><td bgcolor='#CCFFFF'>名称</td><td bgcolor='#CCFFFF'>出发地</td><td bgcolor='#CCFFFF'>目的地</td><td bgcolor='#CCFFFF'>出行时间</td><td bgcolor='#CCFFFF'>价格</td><td bgcolor='#CCFFFF'>出行时长</td><td bgcolor='#CCFFFF'>交通工具</td><td bgcolor='#CCFFFF'>备注</td>
    <td width="120" align="center" bgcolor="#CCFFFF">添加时间</td>
    <td width="70" align="center" bgcolor="#CCFFFF">操作</td>
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
  mysqli_query('set names utf8');
$query=mysqli_query($sql);
  $rowscount=mysqli_num_rows($query);
  if($rowscount==0)
  {}
  else
  {
  $pagelarge=10;//每页行数；
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
    <td><?php echo mysqli_result($query,$i,bianhao);?></td><td><?php echo mysqli_result($query,$i,mingcheng);?></td><td><?php echo mysqli_result($query,$i,chufadi);?></td><td><?php echo mysqli_result($query,$i,mudedi);?></td><td><?php echo mysqli_result($query,$i,chuxingshijian);?></td><td><?php echo mysqli_result($query,$i,jiage);?></td><td><?php echo mysqli_result($query,$i,chuxingshichang);?></td><td><?php echo mysqli_result($query,$i,jiaotonggongju);?></td><td><?php echo mysqli_result($query,$i,beizhu);?></td>
    <td width="120" align="center"><?php
echo mysqli_result($query,$i,"addtime");
?></td>
    <td width="90" align="center"><a href="del.php?id=<?php echo mysqli_result($query,$i,"id");?>&tablename=lvyouxianlu" onclick="return confirm('真的要删除？')">删除</a> <a href="lvyouxianlu_updt.php?id=<?php echo mysqli_result($query,$i,"id");?>">修改</a> <a href="lvyouxianlu_detail.php?id=<?php echo mysqli_result($query,$i,"id");?>" target="_blank">详细</a></td>
  </tr>
    <?php
  }
}
?>
</table>
<p>以上数据共<?php
    echo $rowscount;
  ?>条,
  <input type="button" name="Submit2" onclick="javascript:window.print();" value="打印本页" />
</p>
<p align="center"><a href="lvyouxianlu_list.php?pagecurrent=1">首页</a>, <a href="lvyouxianlu_list.php?pagecurrent=<?php echo $pagecurrent-1;?>">前一页</a> ,<a href="lvyouxianlu_list.php?pagecurrent=<?php echo $pagecurrent+1;?>">后一页</a>, <a href="lvyouxianlu_list.php?pagecurrent=<?php echo $pagecount;?>">末页</a>, 当前第<?php echo $pagecurrent;?>页,共<?php echo $pagecount;?>页 </p>

<p>&nbsp; </p>

</body>
</html>
<script src="js/jquery_002.js"></script>
<script src="js/jquery-ui.js"></script>
<script>
  $("#chuxingshijian1").datepicker({
        dateFormat: 'yy-mm-dd', inline: true,
        monthNames: ["1月", "2月", "3月", "4月", "5月", "6月", "7月", "8月", "9月", "10月", "11月", "12月"],
        dayNamesMin: ["日", "一", "二", "三", "四", "五", "六"],
        onSelect: function (dateText, inst) {
            var theDate = new Date(Date.parse($(this).datepicker('getDate')));
            var dateFormatted = $.datepicker.formatDate('yy-mm-dd', theDate);
        }
    });
  $("#chuxingshijian2").datepicker({
        dateFormat: 'yy-mm-dd', inline: true,
        monthNames: ["1月", "2月", "3月", "4月", "5月", "6月", "7月", "8月", "9月", "10月", "11月", "12月"],
        dayNamesMin: ["日", "一", "二", "三", "四", "五", "六"],
        onSelect: function (dateText, inst) {
            var theDate = new Date(Date.parse($(this).datepicker('getDate')));
            var dateFormatted = $.datepicker.formatDate('yy-mm-dd', theDate);
        }
    });
</script>

