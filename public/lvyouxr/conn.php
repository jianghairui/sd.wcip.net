<?php
//数据库链接文件
error_reporting(E_ALL &~ E_NOTICE);
date_default_timezone_set('PRC');
$host='127.0.0.1';//数据库服务器
$user='root';//数据库用户名
$password='blackpearl';//数据库密码
$database='lvyouxr';//数据库名
$conn=@mysqli_connect($host,$user,$password) or die('数据库连接失败！');
@mysqli_select_db($conn,$database) or die('没有找到数据库！');
mysqli_query($conn,"set names 'utf-8'");
function getoption($ntable,$nzd,$link)
{
		$sql="select ".$nzd." from ".$ntable." order by id desc";
		$query=mysqli_query($link,$sql);
		$rowscount=mysqli_num_rows($query);
		if($rowscount>0)
		{
			for ($fi=0;$fi<$rowscount;$fi++)
			{
				?>
				<option value="<?php echo mysql_result($query,$fi,0);?>"><?php echo mysql_result($query,$fi,0);?></option>
				<?php
			}
		}
}
function getoption2($ntable,$nzd,$link)
{
	?>
	<option value="">请选择</option>
	<?php
		$sql="select ".$nzd." from ".$ntable." order by id desc";
		$query=mysqli_query($link,$sql);
		$rowscount=mysqli_num_rows($query);
		if($rowscount>0)
		{
			for ($fi=0;$fi<$rowscount;$fi++)
			{
				?>
				<option value="<?php echo mysql_result($query,$fi,0);?>" <?php 
				
				if($_GET[$nzd]==mysql_result($query,$fi,0))
				{
					echo "selected";
				}
				?>><?php echo mysql_result($query,$fi,0);?></option>
				<?php
			}
		}
}
function getitem($ntable,$nzd,$tjzd,$ntj)
{
	if($_GET[$tjzd]!="")
	{
		$sql="select ".$nzd." from ".$ntable." where ".$tjzd."='".$ntj."'";
		$query=mysqli_query($sql);
		$rowscount=mysql_num_rows($query);
		if($rowscount>0)
		{
			
				echo "value='".mysql_result($query,0,0)."'";
			
		}
	}
}
?>