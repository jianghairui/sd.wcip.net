<table id="__01" width="208" height="846" border="0" cellpadding="0" cellspacing="0">
              <tr>
                <td><table id="__01" width="208" height="230" border="0" cellpadding="0" cellspacing="0">
                  <tr>
                    <td width="208" height="37" background="qtimages/1_02_01_01_01.jpg"><table width="100%" height="23" border="0" cellpadding="0" cellspacing="0">
                      <tr>
                        <td width="70%" height="23" align="center" valign="bottom"><span class="STYLE1">ϵͳ����</span></td>
                        <td width="30%" valign="bottom">&nbsp;</td>
                        </tr>
                    </table></td>
                  </tr>
                  <tr>
                    <td><table id="__01" width="208" height="182" border="0" cellpadding="0" cellspacing="0">
                      <tr>
                        <td><img src="qtimages/1_02_01_01_02_01.jpg" width="22" height="182" alt=""></td>
                        <td width="169" height="182"><marquee border="0" direction="up" height="100%" onMouseOut="start()" onMouseOver="stop()"
                scrollamount="1" scrolldelay="50"><TABLE width="92%" height="100%" 
            border=0 align=center 
      cellPadding=0 cellSpacing=5>
                      <TBODY><TR><TD><?php 
					  $sql="select * from dx where leibie='ϵͳ����'";
					  $query=mysqli_query($sql);
					  $rowscount=mysqli_num_rows($query);
					  if($rowscount>0)
					  {
					  	echo mysqli_result($query,0,"content");
					  	}
						?></TD></TR></TBODY></TABLE></marquee></td>
                        <td><img src="qtimages/1_02_01_01_02_03.jpg" width="17" height="182" alt=""></td>
                      </tr>
                    </table></td>
                  </tr>
                  <tr>
                    <td><img src="qtimages/1_02_01_01_03.jpg" width="208" height="11" alt=""></td>
                  </tr>
                </table></td>
              </tr>
              <tr>
                <td><table id="__01" width="208" height="209" border="0" cellpadding="0" cellspacing="0">
                  <tr>
                    <td width="208" height="46" background="qtimages/1_02_01_02_01.jpg"><table width="100%" height="24" border="0" cellpadding="0" cellspacing="0">
                      <tr>
                        <td width="92%" height="16" align="center" valign="top"><span class="STYLE4">�û���½</span></td>
                        <td width="8%" valign="bottom">&nbsp;</td>
                      </tr>
                    </table></td>
                  </tr>
                  <tr>
                    <td><table id="__01" width="208" height="144" border="0" cellpadding="0" cellspacing="0">
                      <tr>
                        <td width="21" background="qtimages/1_02_01_02_02_01.jpg">&nbsp;</td>
                        <td width="170" height="154"><?php 
					if ($_SESSION['cx']=="" )
					{
				?>
                          <form action="userlog_post.php" method="post" name="userlog" id="userlog">
                            <table width="100%" height="68%" border="0" cellpadding="0" cellspacing="0">
                              <tr>
                                <td width="12" height="50">&nbsp;</td>
                                <td width="49">�û���:</td>
                                <td width="109"><input name="username" type="text" id="username" size="12" style="width:100px; height:16px; border:solid 1px #000000; color:#666666" /></td>
                              </tr>
                              <tr>
                                <td height="36">&nbsp;</td>
                                <td>����:</td>
                                <td><input name="pwd1" type="password" id="pwd1" size="12" style="width:100px; height:16px; border:solid 1px #000000; color:#666666" /></td>
                              </tr>
                              <tr>
                                <td height="38" colspan="3" align="center"><input type="submit" name="Submit" value="��½" style=" height:19px; border:solid 1px #000000; color:#666666" />
                                    <input type="reset" name="Submit2" value="����" style=" height:19px; border:solid 1px #000000; color:#666666" /></td>
                              </tr>
                            </table>
                          </form>
                          <?php 
							}
				  else
				  {
				 ?>
                          <table width="100%" height="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                            <tr>
                              <td align="left">��ǰ�û���<?php echo $_SESSION['username']?></td>
                            </tr>
                            <tr>
                              <td align="left">��ӭ���ĵ���!!!</td>
                            </tr>
                            <tr>
                              <td align="center"><input type="button" name="Submit3" value="�˳�" onclick="javascript:location.href='logout.php';" style=" height:19px; border:solid 1px #000000; color:#666666" />
                                  <input type="button" name="Submit22" value="�ҵĶ���" onclick="javascript:location.href='main.php';" style=" height:19px; border:solid 1px #000000; color:#666666" /></td>
                            </tr>
                          </table>
                        <?php } ?></td>
                        <td width="17" background="qtimages/1_02_01_02_02_03.jpg">&nbsp;</td>
                      </tr>
                    </table></td>
                  </tr>
                  <tr>
                    <td><img src="qtimages/1_02_01_02_03.jpg" width="208" height="9" alt=""></td>
                  </tr>
                </table></td>
              </tr>
              <tr>
                <td><table id="__01" width="208" height="200" border="0" cellpadding="0" cellspacing="0">
                  <tr>
                    <td width="208" height="41" background="qtimages/1_02_01_03_01.jpg"><table width="100%" height="24" border="0" cellpadding="0" cellspacing="0">
                      <tr>
                        <td width="92%" height="16" align="center" valign="middle"><span class="STYLE4">վ������</span></td>
                        <td width="8%" valign="bottom">&nbsp;</td>
                      </tr>
                    </table></td>
                  </tr>
                  <tr>
                    <td><table id="__01" width="208" height="145" border="0" cellpadding="0" cellspacing="0">
                      <tr>
                        <td width="26" background="qtimages/1_02_01_03_02_01.jpg">&nbsp;</td>
                        <td width="160" height="145"><table width="100%" height="100" border="0" cellpadding="0" cellspacing="0">
                          <form action="news.php" method="post" name="formsearch" id="formsearch">
                            <tr>
                              <td width="19%">����</td>
                              <td width="81%"><input name="biaoti" type="text" id="biaoti" size="20" style="width:130px; height:16px; border:solid 1px #000000; color:#666666" /></td>
                            </tr>
                            <tr>
                              <td>���</td>
                              <td><select name="lb" style="width:130px; height:16px; border:solid 1px #000000; color:#666666">
                                  <option value="վ������">վ������</option>
                                </select>
                              </td>
                            </tr>
                            <tr>
                              <td>&nbsp;</td>
                              <td><input type="submit" name="Submit4" value="�ύ" style=" height:19px; border:solid 1px #000000; color:#666666" /></td>
                            </tr>
                          </form>
                        </table></td>
                        <td width="22" background="qtimages/1_02_01_03_02_03.jpg">&nbsp;</td>
                      </tr>
                    </table></td>
                  </tr>
                  <tr>
                    <td><img src="qtimages/1_02_01_03_03.jpg" width="208" height="14" alt=""></td>
                  </tr>
                </table></td>
              </tr>
              <tr>
                <td><table id="__01" width="208" height="195" border="0" cellpadding="0" cellspacing="0">
                  <tr>
                    <td width="208" height="41" background="qtimages/1_02_01_03_01.jpg"><table width="100%" height="24" border="0" cellpadding="0" cellspacing="0">
                        <tr>
                          <td width="92%" height="16" align="center" valign="middle"><span class="STYLE4">վ������</span></td>
                          <td width="8%" valign="bottom">&nbsp;</td>
                        </tr>
                    </table></td>
                  </tr>
                  <tr>
                    <td height="140"><table width="208" height="100%" border="0" cellpadding="0" cellspacing="0" id="__01">
                        <tr>
                          <td width="26" background="qtimages/1_02_01_03_02_01.jpg">&nbsp;</td>
                          <td width="160"><table class="newsline" cellspacing="0" cellpadding="0" width="86%" 
                  align="center" border="0">
                            <tbody>
                              <?php 
					  $sql="select * from youqinglianjie order by id desc";
					  $query=mysqli_query($sql);
					  $rowscount=mysqli_num_rows($query);
					  if($rowscount>0)
					  {
					  	for($i=0;$i<$rowscount;$i++)
						{
						?>
                              <tr>
                                <td width="5%" height="25"><span class="STYLE2"><img src="qtimages/1.jpg" /></span></td>
                                <td height="25">&nbsp;</td>
                                <td width="92%" height="25"><a href="<?php echo mysqli_result($query,$i,"wangzhi");?>" target="_blank" ><?php echo mysqli_result($query,$i,"wangzhanmingcheng");?></a></td>
                              </tr>
                              <?php
						}
					  }
					  ?>
                            </tbody>
                          </table></td>
                          <td width="22" background="qtimages/1_02_01_03_02_03.jpg">&nbsp;</td>
                        </tr>
                    </table></td>
                  </tr>
                  <tr>
                    <td height="14"><img src="qtimages/1_02_01_03_03.jpg" width="208" height="14" alt=""></td>
                  </tr>
                </table></td>
              </tr>
              <tr>
                <td><img src="qtimages/1_02_01_05.jpg" width="208" height="12" alt=""></td>
              </tr>
            </table>