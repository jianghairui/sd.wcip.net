<table id="__01" width="211" height="183" border="0" cellpadding="0" cellspacing="0">
                  <tr>
                    <td width="211" height="29" background="qtimages/img_02_01_04_01.gif"><table width="88%" border="0" align="center" cellspacing="0">
                        <tr>
                          <td width="71%"><span class="unnamed2_14 STYLE2" style="font-size: 10pt; color: #ffffff"><strong>友情连接</strong></span></td>
                          <td width="29%">&nbsp;</td>
                        </tr>
                    </table></td>
                  </tr>
                  <tr>
                    <td width="211" height="154" background="qtimages/img_02_01_04_02.gif"><table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
                      <?php 
					  $sql="select * from youqinglianjie order by id desc";
					  $query=mysqli_query($sql);
					  $rowscount=mysql_num_rows($query);
					  if($rowscount>0)
					  {
					  	for($i=0;$i<$rowscount;$i++)
						{
						?>
						<tr>
                        <td width="12%" align="center"><img src="qtimages/3.gif" width="5" height="7"></td>
                        <td width="88%"><a href="<?php echo mysql_result($query,$i,"wangzhi");?>" target="_blank"><?php echo mysql_result($query,$i,"wangzhanmingcheng");?></a></td>
                      </tr>
						<?php
						}
					  }
					  ?>
							
							
						
							
					  
                     
                    </table></td>
                  </tr>
                </table>