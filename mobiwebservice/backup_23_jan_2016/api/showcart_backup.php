<?php
include("../common/config.php");
include("../include/functions.php"); 
include "../dine/dineclassbk.php";
$Dine_Obj = new Dine();
if($_REQUEST['device_id']){
    $Cart=$Dine_Obj->ShowCart();
    echo "<pre>";
    print_r($Cart);
    		   if($Cart[1]>0) {
			foreach ($Cart[0] as $ind => $itemx) {
			$item = $itemx['itemName'];
			$menu=$Dine_Obj->GetMenuFromItem($itemx['Cart_ServiceID']);
			$Cus_Pizza=$Dine_Obj->GetCus_Pizza($itemx['Cart_ServiceID']);
			$Free_Toppings=$Dine_Obj->GetFree_Toppings($itemx['Cart_ServiceID']);
			?>
			<div class="resPriceBg">
			
				<div class="resPriceArea">
				
					<div style="width: 150px; float: left;">
					<?php
					$subitemprice=0;
					if($itemx['Cus_Pizza']==1)
					{
						$Csql="SELECT tbl_cartsubitems.subgroup_id,tbl_cartsubitems.subgroup_value,subitems.subitemName,subitems.subitemPrice,subitems.subgroup_id AS SID FROM tbl_cartsubitems INNER JOIN subitems ON subitems.id=tbl_cartsubitems.subgroup_id WHERE Cart_ID=".$itemx['Cart_ID']." GROUP BY subitems.subgroup_id";
						$Cexe=mysql_query($Csql);
						$Cnum=mysql_num_rows($Cexe);
						$subitemprice=0;
						if($Cnum>0)
						{
						$SubCnt=1;
						while($CRes=mysql_fetch_array($Cexe))
						{
							
							$SCsql="SELECT tbl_cartsubitems.subgroup_x2,tbl_cartsubitems.subgroup_id,tbl_cartsubitems.subgroup_value,subitems.subitemName,subitems.subitemPrice,subitems.subgroup_id AS SID,tbl_cartsubitems.Free_Toppings AS FreeToppings FROM tbl_cartsubitems INNER JOIN subitems ON subitems.id=tbl_cartsubitems.subgroup_id WHERE Cart_ID=".$itemx['Cart_ID']." AND subitems.subgroup_id=".$CRes['SID'];
							$SCexe=mysql_query($SCsql);
							$Left_value="";$Whole_value="";$Right_value="";
							$Left_valueX="";$Whole_valueX="";$Right_valueX="";
							$Left_Price=0;$Whole_Price=0;$Right_Price=0;
							while($SCRes=mysql_fetch_array($SCexe))
							{
								if($SCRes['subgroup_value']=='Left Side') {
									$Left_value=$SCRes['subitemName'];
									$Left_Price=$SCRes['subitemPrice'];
									$Left_Price=$Left_Price/2;
									if($SCRes['subgroup_x2']==1) {
									$Left_valueX="(2X)";
									$Left_Price=$Left_Price*2;
									}
									if($SCRes['FreeToppings']!=1)
									$subitemprice=$subitemprice+$Left_Price;
								}
								if($SCRes['subgroup_value']=='Whole') {
									$Whole_value=$SCRes['subitemName'];
									$Whole_Price=$SCRes['subitemPrice'];
									if($SCRes['subgroup_x2']==1) {
									$Whole_valueX="(2X)";
									$Whole_Price=$Whole_Price*2;
									}
									if($SCRes['FreeToppings']!=1)
									$subitemprice=$subitemprice+$Whole_Price;
								}
								if($SCRes['subgroup_value']=='Right Side') {
									$Right_value=$SCRes['subitemName'];
									$Right_Price=$SCRes['subitemPrice'];
									$Right_Price=$Right_Price/2;
									if($SCRes['subgroup_x2']==1) {
									$Right_valueX="(2X)";
									$Right_Price=$Right_Price*2;
									}
									if($SCRes['FreeToppings']!=1)
									$subitemprice=$subitemprice+$Right_Price;
								}
								$SubCnt++;
							}
							
						}
						?>
						<?php
						}
						$subitemprice=$subitemprice*$itemx['Cart_Quantity'];
						$subtotal += ($itemx['Amount'] * $itemx['Cart_Quantity'])+$subitemprice;
					}
					else {
						$Csql="SELECT tbl_cartsubitems.subgroup_id,tbl_cartsubitems.subgroup_value,subitems.subitemName,subitems.subitemPrice,tbl_cartsubitems.Free_Toppings AS FreeToppings,tbl_cartsubitems.price_index FROM tbl_cartsubitems INNER JOIN subitems ON subitems.id=tbl_cartsubitems.subgroup_id WHERE Cart_ID=".$itemx['Cart_ID'];
						$Cexe=mysql_query($Csql);
						$Cnum=mysql_num_rows($Cexe);
						$subitemprice=0;
						if($Cnum>0)
						{
							while($CRes=mysql_fetch_array($Cexe))
							{
								if($SCRes['FreeToppings']!=1)
								{
									$price_index=$CRes['price_index'];
									$subitemPrice1=explode(",",$CRes['subitemPrice']);
									
									if(COUNT($subitemPrice1)>1)
										$subitemprice=$subitemPrice1[$price_index-1];
									else
										$subitemprice=$CRes['subitemPrice'];
								}
									//$subitemprice=$subitemprice+$CRes['subitemPrice'];
									
							}
						}
						$subitemprice=$subitemprice*$itemx['Cart_Quantity'];
						$subtotal += ($itemx['Amount'] * $itemx['Cart_Quantity'])+$subitemprice;
					}
						?>
						<p><span class="itemBlack"><?php
									echo $itemx['itemName'];
								if($itemx['Size']<>'' && $itemx['Size']<>'NORMAL' && $itemx['Size']<>'normal' && $itemx['Size']<>'Normal' && $itemx['Size']<>'1')
									echo " (".$itemx['Size'].")";
								 echo " (".$itemx['Cart_Quantity'].")";
								?> </span> <br/>
								<table width="100%" cellpadding="0" cellspacing="0">
							<?php
							if($itemx['Cus_Pizza']==1)
							{
								echo '<tr><td></td><td class="content14"><a class="tooltip" href="#">Pizza Details<span>';
							    $Csql="SELECT tbl_cartsubitems.subgroup_id,tbl_cartsubitems.subgroup_value,subitems.subitemName,subitems.subitemPrice,subitems.subgroup_id AS SID FROM tbl_cartsubitems INNER JOIN subitems ON subitems.id=tbl_cartsubitems.subgroup_id WHERE Cart_ID=".$itemx['Cart_ID']." GROUP BY subitems.subgroup_id";
								$Cexe=mysql_query($Csql);
								$Cnum=mysql_num_rows($Cexe);
								
								if($Cnum>0)
								{
									?>
								<table bgcolor="#cce4ff" style=" border: 1px solid #62a8d9; border-collapse: collapse;" width="100%" cellspacing="0" cellpadding="0">
                                  <tr>
								  <td style="border: 1px solid #62a8d9;">&nbsp;</td>
                                  <td style="border: 1px solid #62a8d9;"><div align="center"><img src="<?=$Host_Path;?>dine/images/img_halfcircleleft.png" width="32" height="32" border="0"  alt="Left Side" title="Left Side"/></div></td>
                                  <td style="border: 1px solid #62a8d9;"><div align="center" alt="Whole" title="Whole"><img src="<?=$Host_Path;?>dine/images/img_fullcircle.png" width="32" height="32" border="0" alt="Whole"  title="Whole"/></div></td>
								  <td style="border: 1px solid #62a8d9;"><div align="center"><img src="<?=$Host_Path;?>dine/images/img_halfcircleright.png" width="32" height="32" border="0"  alt="Left Side" title="Right Side"/></div></td>
                                  </tr>
								  <?php
								while($CRes=mysql_fetch_array($Cexe))
								{
									echo '<tr><td style="border: 1px solid #62a8d9;" class="guidelines"><div align="center" class="accountinfo">'.getSubgroupname($CRes['SID']);
									
									echo '</div></td>';
									$SCsql="SELECT tbl_cartsubitems.subgroup_x2,tbl_cartsubitems.subgroup_id,tbl_cartsubitems.subgroup_value,subitems.subitemName,subitems.subitemPrice,subitems.subgroup_id AS SID,tbl_cartsubitems.Free_Toppings AS FreeToppings FROM tbl_cartsubitems INNER JOIN subitems ON subitems.id=tbl_cartsubitems.subgroup_id WHERE Cart_ID=".$itemx['Cart_ID']." AND subitems.subgroup_id=".$CRes['SID']." ORDER BY subgroup_value";
									$SCexe=mysql_query($SCsql);
									$Left_value="";$Whole_value="";$Right_value="";
									$Left_valueX="";$Whole_valueX="";$Right_valueX="";
									$Left_Price=0;$Whole_Price=0;$Right_Price=0;
									$SubCnt=1;
									while($SCRes=mysql_fetch_array($SCexe))
									{
										if($SCRes['subgroup_value']=='Left Side') {
											$LeftPrice=$SCRes['subitemPrice']/2;
											if($SCRes['subgroup_x2']==1)
												$LeftPrice=$LeftPrice*2;
											if($SCRes['FreeToppings']==1)
												$LeftPrice="Free";
											else
												$LeftPrice="$".number_format($LeftPrice,2);
											if($Left_value=='')
											$Left_value=$SCRes['subitemName']."<br>(".$LeftPrice.")";
											else
												$Left_value=$Left_value."<br>".$SCRes['subitemName']."<br>(".$LeftPrice.")";
											if($SCRes['subgroup_x2']==1)
												$Left_value=$Left_value." (2X)";
										}
										if($SCRes['subgroup_value']=='Whole') {
											$WholePrice=$SCRes['subitemPrice'];
											if($SCRes['subgroup_x2']==1)
												$WholePrice=$WholePrice*2;
											if($SCRes['FreeToppings']==1)
												$WholePrice="Free";
											else
												$WholePrice="$".number_format($WholePrice,2);
											if($Whole_value=='')
											$Whole_value=$SCRes['subitemName']."<br>(".$WholePrice.")";
											else
												$Whole_value=$Whole_value."<br>".$SCRes['subitemName']."<br>(".$WholePrice.")";
											if($SCRes['subgroup_x2']==1) {
												$Whole_value=$Whole_value." (2X)";
												$SubCnt++;
												}
										}
										if($SCRes['subgroup_value']=='Right Side') {
											$RightPrice=$SCRes['subitemPrice']/2;
											if($SCRes['subgroup_x2']==1)
												$RightPrice=$RightPrice*2;
											if($SCRes['FreeToppings']==1)
												$RightPrice="Free";
											else
												$RightPrice="$".number_format($RightPrice,2);
											if($Right_value=='')
											$Right_value=$SCRes['subitemName']."<br>(".$RightPrice.")";
											else
												$Right_value=$Right_value."<br>".$SCRes['subitemName']."<br>(".$RightPrice.")";
											
											if($SCRes['subgroup_x2']==1)
												$Right_value=$Right_value." (2X)";
										}
										$SubCnt++;
									}
									if($Left_value<>'') {
										echo '<td style="border: 1px solid #62a8d9;"><div align="center" class="accountinfo1">'.$Left_value;
										//if($Left_valueX<>'') 
											//echo ' (2X)';
										if($Left_Price>0) 
											echo ' $'.number_format($Left_Price,2);
										echo '</div></td>'; 
									}
									else {
										echo '<td style="border: 1px solid #62a8d9;"><div align="center" class="accountinfo1">&nbsp;</div></td>';
										}
									if($Whole_value<>'') {
										echo '<td style="border: 1px solid #62a8d9;"><div align="center" class="accountinfo1">'.$Whole_value;
										//if($Whole_valueX<>'')
											//echo ' (2X)';
										if($Whole_Price>0) 
											echo ' $'.number_format($Whole_Price,2);
										echo '</div></td>';
									}
									else {
										echo '<td style="border: 1px solid #62a8d9;"><div align="center" class="accountinfo1">&nbsp;</div></td>';
									}
									if($Right_value<>'') {
										echo '<td style="border: 1px solid #62a8d9;"><div align="center" class="accountinfo1">'.$Right_value;
										//if($Right_valueX<>'') 
											//echo ' (2X)'; 
										if($Right_Price>0) 
											echo ' $'.number_format($Right_Price,2);
										echo '</div></td>';
									}
									else {
										echo '<td style="border: 1px solid #62a8d9;"><div align="center" class="accountinfo1">&nbsp;</div></td>';
									}
									echo '</tr>';
								}
								echo '<tr><td class="accountinfo1" style="border: 1px solid #62a8d9;">Crust Type:</td><td colspan="3" class="accountinfo1" style="padding-left:10px;" align="left">'.$itemx['crust'].'</td></tr>';
								?>
								
								</table>
								<?php
								}
								else
								{
									echo '<table><tr><td colspan="3" class="content14">No Specification Found.</td></tr></table>';
								}
								echo '</span></a></td></tr>';
							}
							else
							{
								$Csql="SELECT tbl_cartsubitems.subgroup_id,tbl_cartsubitems.subgroup_value,subitems.subitemName,subitems.subitemPrice,subgroups.subgroup_name,tbl_cartsubitems.price_index FROM tbl_cartsubitems INNER JOIN subitems ON subitems.id=tbl_cartsubitems.subgroup_id
								INNER JOIN subgroups ON subgroups.id=subitems.subgroup_id
								WHERE Cart_ID=".$itemx['Cart_ID']." ORDER BY subgroups.id";
								$Cexe=mysql_query($Csql);
								$Cnum=mysql_num_rows($Cexe);
								
								if($Cnum>0)
								{
									echo '<tr><td class="content14"><div align="left" class="guidelines">';
									$cnt=1;
									$subgroup_name="";
									$TotalsubitemPrice=0;
									while($CRes=mysql_fetch_array($Cexe))
									{
										$price_index=$CRes['price_index'];
										$subitemPrice1=explode(",",$CRes['subitemPrice']);
										
										if(COUNT($subitemPrice1)>1)
											$subitemPrice=$subitemPrice1[$price_index-1];
										else
											$subitemPrice=$CRes['subitemPrice'];
										if($subgroup_name<>$CRes['subgroup_name']) {
											if($cnt>1)
												echo '<br>';
											echo "<b>".ucfirst($CRes['subgroup_name'])." :</b> ";
											}
										$subgroup_name=$CRes['subgroup_name'];
										echo $CRes['subitemName'];
										if($CRes['subgroup_value']<>'')
											echo $CRes['subgroup_value'];
										else if($subitemPrice>0)
											echo ' ($ '.number_format($subitemPrice,2).')';
										if($Cnum<>$cnt)
											echo ', ';
										$cnt++;
										$TotalsubitemPrice=$TotalsubitemPrice+$subitemPrice;
									}
									echo '</div></td></tr>';
									//echo '</table>';
								}
								
								if($itemx['Notes']<>'')
								{
								echo '<tr><td></td><td class="content14" colspan="2"><b>Instructions:</b>  '.$itemx['Notes'].'</td></tr>';
								}
							}
								?>
							  </table>
								<br>
								<div class="padding04"></div><span class="itemBlue">
								 <?php
								 if($Cus_Pizza==1) {
								 ?>
								 <a href="<?=$Host_Path;?>dine/item_pizza.php?id=<?=$merchantID;?>&Menu=<?=$menu;?>&Item=<?=$itemx['Cart_ServiceID'];?>&Cart_ID=<?=$itemx['Cart_ID'];?>&orderfor=<?=$_REQUEST['orderfor'];?>&coupon=" class="colorbox h6_grey2">edit</a>
								 <?php
								 }
								 else {
								  ?>
								  <a href="<?=$Host_Path;?>dine/item.php?id=<?=$merchantID;?>&Menu=<?=$menu;?>&Item=<?=$itemx['Cart_ServiceID'];?>&Cart_ID=<?=$itemx['Cart_ID'];?>&orderfor=<?=$_REQUEST['orderfor'];?>&coupon=" class="colorbox h6_grey2">edit</a>
								 <?php
								 }
								 ?>
								| 
								<a href="javascript:;" onclick="removeitem(<?=$itemx['Cart_ID']?>);">remove</a>
								</span></p>
					
					</div>
					
					<div class="clear"></div>
					
					<div style="width: 60px; float: left; position: relative; left: 170px; top: -35px;">
					
						<p class="itemRed">$<?=number_format((($itemx['Amount'] * $itemx['Cart_Quantity'])+$TotalsubitemPrice), 2)?></p>
					
					</div>
				
				</div>
			
			</div>
			<?php } }
}
else{
    $content=array("status"=>0,"message"=>PARAMETER_MSG);
    echo json_encode($content);
    exit;
}

?>