<?php 
include_once 'conn.php';
if(isset($_GET['id'])){
  $sql = "DELETE FROM booktourline WHERE id='".$_GET['id']."'";
  mysqli_query($sql);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>酒店预订</title>
<link rel="stylesheet" type="text/css" href="css/jquery-ui.css">
<script language="javascript" src="js/Calendar.js"></script><link rel="stylesheet" href="css.css" type="text/css">
</head>

<body>

<p>已有酒店预订列表：</p>
<form id="form1" name="form1" method="post" action="">
  搜索: 酒店名称：<input name="jiudianmingcheng" type="text" id="jiudianmingcheng" /> 预订人：<input name="yudingren" type="text" id="yudingren" /> 预订时间：<input name="yudingshijian1" type="text" id="yudingshijian1"  value=''/>-<input name="yudingshijian2" type="text" id="yudingshijian2"  value=''/>
  <input type="submit" name="Submit" value="查找" />
</form>
<table width="100%" border="1" align="center" cellpadding="3" cellspacing="1" bordercolor="#00FFFF" style="border-collapse:collapse">  
  <tr>
    <td width="25" bgcolor="#CCFFFF">序号</td>
    <td bgcolor='#CCFFFF'>酒店名称</td><td bgcolor='#CCFFFF'>星级</td><td bgcolor='#CCFFFF'>电话</td><td bgcolor='#CCFFFF'>地址</td><td bgcolor='#CCFFFF'>预订人</td><td bgcolor='#CCFFFF'>预订时间</td><td bgcolor='#CCFFFF'>预订人数</td><td bgcolor='#CCFFFF'>备注</td><td bgcolor='#CCFFFF' width='80' align='center'>是否审核</td>
    <td width="70" align="center" bgcolor="#CCFFFF">操作</td>
  </tr>
  <?php 
    $sql="select * from jiudianyuding where 1=1";
  
if ($_POST["jiudianmingcheng"]!=""){$nreqjiudianmingcheng=$_POST["jiudianmingcheng"];$sql=$sql." and jiudianmingcheng like '%$nreqjiudianmingcheng%'";}
if ($_POST["yudingren"]!=""){$nreqyudingren=$_POST["yudingren"];$sql=$sql." and yudingren like '%$nreqyudingren%'";}
if ($_POST["yudingshijian1"]!=""){$nreqyudingshijian1=$_POST["yudingshijian1"];$sql=$sql." and yudingshijian >= '$nreqyudingshijian1'";}
if ($_POST["yudingshijian2"]!=""){$nreqyudingshijian2=$_POST["yudingshijian2"];$sql=$sql." and yudingshijian <= '$nreqyudingshijian2'";}
  $sql=$sql." order by id desc";
mysqli_query("set names utf8");
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
    <td><?php echo mysqli_result($query,$i,jiudianmingcheng);?></td><td><?php echo mysqli_result($query,$i,xingji);?></td><td><?php echo mysqli_result($query,$i,dianhua);?></td><td><?php echo mysqli_result($query,$i,dizhi);?></td><td><?php echo mysqli_result($query,$i,yudingren);?></td><td><?php echo mysqli_result($query,$i,yudingshijian);?></td><td><?php echo mysqli_result($query,$i,yudingrenshu);?></td><td><?php echo mysqli_result($query,$i,beizhu);?></td><td width='80' align='center'><a href="sh.php?id=<?php echo mysqli_result($query,$i,"id") ?>&yuan=<?php echo mysqli_result($query,$i,"issh")?>&tablename=jiudianyuding" onclick="return confirm('您确定要执行此操作？')"><?php echo mysqli_result($query,$i,"issh")?></a></td>
    <td width="70" align="center"><a href="del.php?id=<?php
    echo mysqli_result($query,$i,"id");
  ?>&tablename=jiudianyuding" onclick="return confirm('真的要删除？')">删除</a> <a href="jiudianyuding_updt.php?id=<?php
    echo mysqli_result($query,$i,"id");
  ?>">修改</a></td>
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
<p align="center"><a href="jiudianyuding_list.php?pagecurrent=1">首页</a>, <a href="jiudianyuding_list.php?pagecurrent=<?php echo $pagecurrent-1;?>">前一页</a> ,<a href="jiudianyuding_list.php?pagecurrent=<?php echo $pagecurrent+1;?>">后一页</a>, <a href="jiudianyuding_list.php?pagecurrent=<?php echo $pagecount;?>">末页</a>, 当前第<?php echo $pagecurrent;?>页,共<?php echo $pagecount;?>页 </p>

<p>&nbsp; </p>
<p>已有路线预订列表：</p>
<table width="1000" border="1"  cellpadding="3" cellspacing="1" bordercolor="#00FFFF" style="border-collapse:collapse">
  <thead>
    <tr  bgcolor="#CCFFFF">
      <th>订单编号</th>
      <th>用户账号</th>
      <th>用户姓名</th>
      <th>路线名</th>
      <th>价格</th>
      <th>预定时间</th>
      <th>操作</th>
    </tr>
  </thead>
  <tbody>
  <?php
$sql = "SELECT * FROM booktourline";
$result = mysqli_query($sql);
while(list($id,$username,$name,$linename,$price,$booktime) = mysqli_fetch_row($result)) {
  echo "<tr>
      <td>$id</td>
      <td>$username</td>
      <td>$name</td>
      <td>$linename</td>
      <td>$price</td>
      <td>$booktime</td>
      <td><a href='?id=$id' onclick='return oper()'>删除</a></td>
    </tr>";
  }
  ?>
  </tbody>
</table>


</body>
</html>
<script type="text/javascript">
  
  function oper() {
    if(confirm('确定删除吗')) {
      return true;
    }else{
      return false;
    }
  }

</script>
<script src="js/jquery_002.js"></script>
<script src="js/jquery-ui.js"></script>
<script>
  $("#yudingshijian1").datepicker({
        dateFormat: 'yy-mm-dd', inline: true,
        monthNames: ["1月", "2月", "3月", "4月", "5月", "6月", "7月", "8月", "9月", "10月", "11月", "12月"],
        dayNamesMin: ["日", "一", "二", "三", "四", "五", "六"],
        onSelect: function (dateText, inst) {
            var theDate = new Date(Date.parse($(this).datepicker('getDate')));
            var dateFormatted = $.datepicker.formatDate('yy-mm-dd', theDate);
        }
    });
  $("#yudingshijian2").datepicker({
        dateFormat: 'yy-mm-dd', inline: true,
        monthNames: ["1月", "2月", "3月", "4月", "5月", "6月", "7月", "8月", "9月", "10月", "11月", "12月"],
        dayNamesMin: ["日", "一", "二", "三", "四", "五", "六"],
        onSelect: function (dateText, inst) {
            var theDate = new Date(Date.parse($(this).datepicker('getDate')));
            var dateFormatted = $.datepicker.formatDate('yy-mm-dd', theDate);
        }
    });
</script>

