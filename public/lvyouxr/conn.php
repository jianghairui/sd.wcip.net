<?php
header('Content-type:text/html;charset=utf-8');
error_reporting(E_ALL &~ E_NOTICE);
date_default_timezone_set('PRC');
$host='127.0.0.1';
$user='root';
$password='blackpearl';
$database='lvyouxr';
$conn=@mysqli_connect($host,$user,$password) or die('');
@mysqli_select_db($conn,$database) or die('û���ҵ����ݿ⣡');
mysqli_query($conn,"set names 'gb2312'");

function getoption($ntable,$nzd,$conn)
{
		$sql="select ".$nzd." from ".$ntable." order by id desc";
		$query=mysqli_query($conn,$sql);
		$rowscount=mysqli_num_rows($query);
		if($rowscount>0)
		{
			for ($fi=0;$fi<$rowscount;$fi++)
			{
				?>
				<option value="<?php echo mysqli_result($query,$fi,0);?>"><?php echo mysqli_result($query,$fi,0);?></option>
				<?php
			}
		}
}
function getoption2($ntable,$nzd)
{
	?>
	<option value="">��ѡ��</option>
	<?php
		$sql="select ".$nzd." from ".$ntable." order by id desc";
		$query=mysqli_query($sql);
		$rowscount=mysqli_num_rows($query);
		if($rowscount>0)
		{
			for ($fi=0;$fi<$rowscount;$fi++)
			{
				?>
				<option value="<?php echo mysqli_result($query,$fi,0);?>" <?php
				
				if($_GET[$nzd]==mysqli_result($query,$fi,0))
				{
					echo "selected";
				}
				?>><?php echo mysqli_result($query,$fi,0);?></option>
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
		$rowscount=mysqli_num_rows($query);
		if($rowscount>0)
		{
			
				echo "value='".mysqli_result($query,0,0)."'";
			
		}
	}
}
?>