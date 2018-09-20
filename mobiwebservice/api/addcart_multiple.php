<?php
include ('config.php');
include ('function.php');
include "../dine/dineclassbk.php";
	
$Dine_Obj	=	new Dine();
$device_id=$_REQUEST['device_id'];
$sizevalue=explode(":",$_REQUEST['sizeval']);
$subgroup_ids=$_REQUEST['subgroup_ids'];
$subitems_values=$_REQUEST['subitems'];
		
	if($_REQUEST['Item']<>'')
	$Free_Toppings=$Dine_Obj->GetFree_Toppings($_REQUEST['Item']);
	$tabindex=0;
	$sq="SELECT menus.* FROM items INNER JOIN menus on menus.id=items.menuID WHERE items.id=".$_REQUEST['Item'];
	$ex=mysqli_query($con,$sq);
	$re=mysqli_fetch_array($ex);
	if($re['menuSize1']==$sizevalue[0])
		$tabindex=1;
	else if($re['menuSize2']==$sizevalue[0])
		$tabindex=2;
	else if($re['menuSize3']==$sizevalue[0])
		$tabindex=3;
	else if($re['menuSize4']==$sizevalue[0])
		$tabindex=4;
	else if($re['menuSize5']==$sizevalue[0])
		$tabindex=5;
	else if($re['menuSize6']==$sizevalue[0])
		$tabindex=6;
	else if($re['menuSize7']==$sizevalue[0])
		$tabindex=7;
	else if($re['menuSize8']==$sizevalue[0])
		$tabindex=8;
	else if($re['menuSize9']==$sizevalue[0])
		$tabindex=9;
	else if($re['menuSize10']==$sizevalue[0])
		$tabindex=10;

	if($_REQUEST['User_ID']=='')
		$User_ID=0;
	else
		$User_ID=$_REQUEST['User_ID'];
	$subgroup_ids=explode(",",$subgroup_ids);
	$subgroup_ids=array_unique($subgroup_ids);
	$fromDate1="0000-00-00 00:00:00";
	if($_REQUEST['Cart_ID']>0)
	{
		$query="UPDATE tbl_cart SET Notes='".mysqli_real_escape_string($con,$_REQUEST['notes'])."',Cart_Quantity='".$_REQUEST['quantity']."',Size='".mysqli_real_escape_string($con,$sizevalue[0])."',crust='".$_REQUEST['crust']."',Amount='".$sizevalue[1]."',TotalAmount='".($_REQUEST['quantity']*$sizevalue[1])."' WHERE Cart_ID=".mysqli_real_escape_string($con,$_REQUEST['Cart_ID']);
		$res = mysqli_query($con,$query);
		$Cart_ID=$_REQUEST['Cart_ID'];
		$sql="DELETE FROM tbl_cartsubitems WHERE Cart_ID=".$Cart_ID;
		mysqli_query($con,$sql);
		
		if($_REQUEST['cus']==1)
		{
		if($_REQUEST['Multiple_Toppings']==1)
		{
		   // Multi Toppings Pizza selection
		   $FT=0;
		   for($i=0;$i<$_REQUEST['Topping_Count'];$i++)
		   {
			if($_REQUEST['subitems'.$i]<>'')
			{
				$subitems_value1=explode(":",$_REQUEST['subitems'.$i]);
				$subitemsX2_value1=$_REQUEST['subitemsX2'.$i];
				$exist=$subitemsX2_value1;
				if($Free_Toppings>0 && $FT<$Free_Toppings) {
					$S_Free_Toppings=1;
					if($exist==1 && $subitems_value1[1]=='Whole')
					$FT++;
				}
				else
					$S_Free_Toppings=0;
				if($exist<>1)
					$exist=0;
				if(COUNT($subitems_value1)>1)
					$sql="INSERT INTO tbl_cartsubitems(Cart_ID,subgroup_id,subgroup_value,subgroup_x2,Free_Toppings,price_index) VALUES(".$Cart_ID.",".mysqli_real_escape_string($con,$subitems_value1[0]).",'".mysqli_real_escape_string($con,$subitems_value1[1])."',".$exist.",".$S_Free_Toppings.",".$tabindex.")";
				else
					$sql="INSERT INTO tbl_cartsubitems(Cart_ID,subgroup_id,subgroup_x2,Free_Toppings,price_index) VALUES(".$Cart_ID.",".mysqli_real_escape_string($con,$subitems_value).",".$exist.",".$S_Free_Toppings.",".$S_Free_Toppings.",".$tabindex.")";
				mysqli_query($con,$sql);
			}
			$FT++;
		   }
		}
		else
		{
		$pi=0;
		$FT=0;
		foreach($subgroup_ids as $subgroup)
		{
			if($Free_Toppings>0 && $FT<$Free_Toppings) {
				$S_Free_Toppings=1;
				if($exist==1 && $subitems[1]=='Whole')
				$FT++;
			}
			else
				$S_Free_Toppings=0;
			$CLeft=$_REQUEST['R'.$subgroup.'_comboleft'];
			
			$CX2 = implode(",",$_REQUEST['R'.$subgroup.'_comboX2']); 
			$CX2= explode(",",$CX2);
			$CX2 = implode(":",$_REQUEST['R'.$subgroup.'_comboX2']); 
			$CX2= explode(":",$CX2);
			$exist=0;
			if(count($CLeft)>0)
			{
				$subitems= explode(":",$CLeft);
				if(COUNT($subitems)>1)
					$exist=in_array($subitems[0],$CX2);
				else
					$exist=in_array($subitem,$CX2);
				if($exist<>1)
					$exist=0;
				if(COUNT($subitems)>1) 
					$sql="INSERT INTO tbl_cartsubitems(Cart_ID,subgroup_id,subgroup_value,subgroup_x2,Free_Toppings,price_index) VALUES(".$Cart_ID.",".mysqli_real_escape_string($con,$subitems[0]).",'".mysqli_real_escape_string($con,$subitems[1])."',".$exist.",".$S_Free_Toppings.",".$tabindex.")";
				else
					$sql="INSERT INTO tbl_cartsubitems(Cart_ID,subgroup_id,subgroup_x2,Free_Toppings,price_index) VALUES(".$Cart_ID.",".mysqli_real_escape_string($con,$subitem).",".$exist.",".$S_Free_Toppings.",".$tabindex.")";
				mysqli_query($con,$sql);
			}
			$CWhole=$_REQUEST['R'.$subgroup.'_combowhole'];
			if(count($CWhole)>0)
			{
				$subitems=explode(":",$CWhole);
				if(COUNT($subitems)>1)
					$exist=in_array($subitems[0],$CX2);
				else
					$exist=in_array($subitem,$CX2);
				if($exist<>1)
					$exist=0;
				if(COUNT($subitems)>1)
					$sql="INSERT INTO tbl_cartsubitems(Cart_ID,subgroup_id,subgroup_value,subgroup_x2,Free_Toppings,price_index) VALUES(".$Cart_ID.",".mysqli_real_escape_string($con,$subitems[0]).",'".mysqli_real_escape_string($con,$subitems[1])."',".$exist.",".$S_Free_Toppings.",".$tabindex.")";
				else
					$sql="INSERT INTO tbl_cartsubitems(Cart_ID,subgroup_id,subgroup_x2,Free_Toppings,price_index) VALUES(".$Cart_ID.",".mysqli_real_escape_string($con,$subitem).",".$exist.",".$S_Free_Toppings.",".$tabindex.")";
				mysqli_query($con,$sql);
			}
			$CRight=$_REQUEST['R'.$subgroup.'_comboright'];
			if(count($CRight)>0)
			{
				$subitems=explode(":",$CRight);
				if(COUNT($subitems)>1)
					$exist=in_array($subitems[0],$CX2);
				else
					$exist=in_array($subitem,$CX2);
				if($exist<>1)
					$exist=0;
				if(COUNT($subitems)>1)
					$sql="INSERT INTO tbl_cartsubitems(Cart_ID,subgroup_id,subgroup_value,subgroup_x2,Free_Toppings,price_index) VALUES(".$Cart_ID.",".mysqli_real_escape_string($con,$subitems[0]).",'".mysqli_real_escape_string($con,$subitems[1])."',".$exist.",".$S_Free_Toppings.",".$tabindex.")";
				else
					$sql="INSERT INTO tbl_cartsubitems(Cart_ID,subgroup_id,subgroup_x2,Free_Toppings,price_index) VALUES(".$Cart_ID.",".mysqli_real_escape_string($con,$subitem).",".$exist.",".$S_Free_Toppings.",".$tabindex.")";
				mysqli_query($con,$sql);
			}
			$pi++;
			$FT++;
		}
		}
		}
		else
		{
			$pi=0;
			$FT=0;
			foreach($subitems_values as $subitems_value)
			{
				$subitems_value1=explode(":",$subitems_value);
				if($Free_Toppings>0 && $FT<$Free_Toppings) {
					$S_Free_Toppings=1;
					if($exist==1 && $subitems_value1[1]=='Whole')
					$FT++;
				}
				else
					$S_Free_Toppings=0;
				if(COUNT($subitems_value1)>1)
					$sql="INSERT INTO tbl_cartsubitems(Cart_ID,subgroup_id,subgroup_value,Free_Toppings,price_index) VALUES(".$Cart_ID.",".mysqli_real_escape_string($con,$subitems_value1[1]).",'".mysqli_real_escape_string($con,$subitems_value1[1])."',".$S_Free_Toppings.",".$tabindex.")";
				else
					$sql="INSERT INTO tbl_cartsubitems(Cart_ID,subgroup_id,Free_Toppings,price_index) VALUES(".$Cart_ID.",".mysqli_real_escape_string($con,$subitems_value).",".$S_Free_Toppings.",".$tabindex.")";
				mysqli_query($con,$sql);
				$pi++;
				$FT++;
			}
		}
	}
	else
	{
		$query="INSERT INTO  tbl_cart(Sess_ID,Cart_UserID,Cart_ServiceID,Owner_ID,Owner_Restaurant,crust,Notes,Cart_Quantity,Cart_Type,Size,Amount,charges,TotalAmount,from_date,Ticket_Type,Ticket_Quantity,Cart_Created)VALUES('".$device_id."','".$User_ID."','".mysqli_real_escape_string($con,$_REQUEST['Item'])."','".$_REQUEST['Owner']."','".$_REQUEST['Restaurant']."','".$_REQUEST['crust']."','".mysqli_real_escape_string($con,$_REQUEST['notes'])."','".mysqli_real_escape_string($con,$_REQUEST['quantity'])."','Dine','".mysqli_real_escape_string($con,$sizevalue[0])."','".mysqli_real_escape_string($con,$sizevalue[1])."','".mysqli_real_escape_string($con,$extfees)."','".mysqli_real_escape_string($con,($_REQUEST['quantity']*$sizevalue[1]))."','".mysqli_real_escape_string($con,$fromDate1)."','".mysqli_real_escape_string($con,$Ticket_Type)."','".mysqli_real_escape_string($con,$Ticket_Quantity)."',Now());";	
			
		$res = mysqli_query($con,$query);
	
		$Cart_ID=mysqli_insert_id($con);
		if($_REQUEST['cus']==1)
		{
		if($_REQUEST['Multiple_Toppings']==1)
		{
		   // Multi Toppings Pizza selection
		   $FT=0;
		   for($i=0;$i<$_REQUEST['Topping_Count'];$i++)
		   {
			if($_REQUEST['subitems'.$i]<>'')
			{
				$subitems_value1=explode(":",$_REQUEST['subitems'.$i]);
				$subitemsX2_value1=$_REQUEST['subitemsX2'.$i];
				$exist=$subitemsX2_value1;
				if($Free_Toppings>0 && $FT<$Free_Toppings) {
					$S_Free_Toppings=1;
					if($exist==1 && $subitems_value1[1]=='Whole')
					$FT++;
				}
				else
					$S_Free_Toppings=0;
				if($exist<>1)
					$exist=0;
				if(COUNT($subitems_value1)>1)
					$sql="INSERT INTO tbl_cartsubitems(Cart_ID,subgroup_id,subgroup_value,subgroup_x2,Free_Toppings,price_index) VALUES(".$Cart_ID.",".mysqli_real_escape_string($con,$subitems_value1[0]).",'".mysqli_real_escape_string($con,$subitems_value1[1])."','".$exist."',".$S_Free_Toppings.",".$tabindex.")";
				else
					$sql="INSERT INTO tbl_cartsubitems(Cart_ID,subgroup_id,subgroup_x2,Free_Toppings,price_index) VALUES(".$Cart_ID.",".mysqli_real_escape_string($con,$subitems_value).",'".$exist."',".$S_Free_Toppings.",".$tabindex.")";
				mysqli_query($con,$sql);
			}
			$FT++;
		   }
		}
		else
		{
		$exist=0;
		$pi=0;
		$FT=0;
		foreach($subgroup_ids as $subgroup)
		{
			
			$CX2 = implode(",",$_REQUEST['R'.$subgroup.'_comboX2']); 
			$CX2=explode(",",$CX2);
			$CX2 = implode(":",$_REQUEST['R'.$subgroup.'_comboX2']); 
			$CX2=explode(":",$CX2);
			$exist=0;
			$CLeft=$_REQUEST['R'.$subgroup.'_comboleft'];
			if(count($CLeft)>0)
			{
				$subitems=explode(":",$CLeft);
				if(COUNT($subitems)>1)
					$exist=@in_array($subitems[0],$CX2);
				else
					$exist=in_array($subitem,$CX2);
				if($Free_Toppings>0 && $FT<$Free_Toppings) {
				$S_Free_Toppings=1;
				if($exist==1 && $subitems[1]=='Whole')
				$FT++;
				}
				else
					$S_Free_Toppings=0;
				if($exist<>1)
					$exist=0;
				if(COUNT($subitems)>1)
					$sql="INSERT INTO tbl_cartsubitems(Cart_ID,subgroup_id,subgroup_value,subgroup_x2,Free_Toppings,price_index) VALUES(".$Cart_ID.",".mysqli_real_escape_string($con,$subitems[0]).",'".mysqli_real_escape_string($con,$subitems[1])."',".$exist.",".$S_Free_Toppings.",".$tabindex.")";
				else
					$sql="INSERT INTO tbl_cartsubitems(Cart_ID,subgroup_id,subgroup_x2,Free_Toppings,price_index) VALUES(".$Cart_ID.",".mysqli_real_escape_string($con,$subitem).",".$exist.",".$S_Free_Toppings.",".$tabindex.")";
				mysqli_query($con,$sql);
			}
			$CWhole=$_REQUEST['R'.$subgroup.'_combowhole'];
			if(count($CWhole)>0)
			{
				$subitems=explode(":",$CWhole);
				if(COUNT($subitems)>1)
					$exist=in_array($subitems[0],$CX2);
				else
					$exist=in_array($subitem,$CX2);
				if($Free_Toppings>0 && $FT<$Free_Toppings) {
				$S_Free_Toppings=1;
				if($exist==1 && $subitems[1]=='Whole')
				$FT++;
				}
				else
					$S_Free_Toppings=0;
				if($exist<>1)
					$exist=0;
				if(COUNT($subitems)>1)
					$sql="INSERT INTO tbl_cartsubitems(Cart_ID,subgroup_id,subgroup_value,subgroup_x2,Free_Toppings,price_index) VALUES(".$Cart_ID.",".mysqli_real_escape_string($con,$subitems[0]).",'".mysqli_real_escape_string($con,$subitems[1])."',".$exist.",".$S_Free_Toppings.",".$tabindex.")";
				else
					$sql="INSERT INTO tbl_cartsubitems(Cart_ID,subgroup_id,subgroup_x2,Free_Toppings,price_index) VALUES(".$Cart_ID.",".mysqli_real_escape_string($con,$subitem).",".$exist.",".$S_Free_Toppings.",".$tabindex.")";
				mysqli_query($con,$sql);
			}
			$CRight=$_REQUEST['R'.$subgroup.'_comboright'];
			if(count($CRight)>0)
			{
				$subitems=explode(":",$CRight);
				if(COUNT($subitems)>1)
					$exist=in_array($subitems[0],$CX2);
				else
					$exist=in_array($subitem,$CX2);
				if($Free_Toppings>0 && $FT<$Free_Toppings) {
				$S_Free_Toppings=1;
				if($exist==1 && $subitems[1]=='Whole')
				$FT++;
				}
				else
					$S_Free_Toppings=0;
				if($exist<>1)
					$exist=0;
				if(COUNT($subitems)>1)
					$sql="INSERT INTO tbl_cartsubitems(Cart_ID,subgroup_id,subgroup_value,subgroup_x2,Free_Toppings,price_index) VALUES(".$Cart_ID.",".mysqli_real_escape_string($con,$subitems[0]).",'".mysqli_real_escape_string($con,$subitems[1])."',".$exist.",".$S_Free_Toppings.",".$tabindex.")";
				else
					$sql="INSERT INTO tbl_cartsubitems(Cart_ID,subgroup_id,subgroup_x2,Free_Toppings,price_index) VALUES(".$Cart_ID.",".mysqli_real_escape_string($con,$subitem).",".$exist.",".$S_Free_Toppings.",".$tabindex.")";
				mysqli_query($con,$sql);
			}
			$pi++;
			$FT++;
		}
		}
		}
		else
		{
			$pi=0;
			$FT=0;
			if($subitems_values!='') 
			{ 
				foreach($subitems_values as $subitems_value)
				{
					$subitems_value1=explode(":",$subitems_value);
					if($Free_Toppings>0 && $FT<$Free_Toppings) {
						$S_Free_Toppings=1;
						if($exist==1 && $subitems_value1[1]=='Whole')
						$FT++;
					}
					else
						$S_Free_Toppings=0;
					if(COUNT($subitems_value1)>1)
						$sql="INSERT INTO tbl_cartsubitems(Cart_ID,subgroup_id,subgroup_value,Free_Toppings,price_index) VALUES(".$Cart_ID.",".mysqli_real_escape_string($con,$subitems_value1[0]).",'".mysqli_real_escape_string($con,$subitems_value1[1])."',".$S_Free_Toppings.",".$tabindex.")";
					else
						$sql="INSERT INTO tbl_cartsubitems(Cart_ID,subgroup_id,Free_Toppings,price_index) VALUES(".$Cart_ID.",".mysqli_real_escape_string($con,$subitems_value).",".$S_Free_Toppings.",".$tabindex.")";
					
					mysqli_query($con,$sql);
					$pi++;
					$FT++;
				}
			}
		}
        $output = array("status" => 1, "cart_id" => $Cart_ID);
        echo json_encode($output);
        exit;
	}
?>