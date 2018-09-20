<?php
class Dine
{
	function RequestApps($id,$type)
	{
		GLOBAL $con;
		$up = "UPDATE tbl_registeration SET ".$type."_control=2 WHERE id=".$id; 
		$res = mysqli_query($con, $up);
		
		print "<script language=javascript>window.location='index.php';</script>";
		exit;
	}
	function view_Restaurants() {
			GLOBAL $con;
			$sql="Select * from merchant WHERE Deleted=0 AND (Res_UserID=".$_SESSION['User_ID']." OR Res_AssignUserID=".$_SESSION['User_ID'].")";
			if($_REQUEST['Keyword']<>'')
			$sql.=" AND (merchantName LIKE '%".mysqli_real_escape_string($con,$_REQUEST['Keyword'])."%' OR merchantName LIKE '%".mysqli_real_escape_string($con,strtoupper($_REQUEST['Keyword']))."%' OR merchantName LIKE '%".mysqli_real_escape_string($con,strtolower($_REQUEST['Keyword']))."%' OR merchantName LIKE '%".mysqli_real_escape_string($con,ucwords($_REQUEST['Keyword']))."%' OR merchantName LIKE '%".mysqli_real_escape_string($con,ucfirst($_REQUEST['Keyword']))."%' OR contactName LIKE '%".mysqli_real_escape_string($con,$_REQUEST['Keyword'])."%' OR contactAddress LIKE '%".mysqli_real_escape_string($con,$_REQUEST['Keyword'])."%' OR crossStreet LIKE '%".mysqli_real_escape_string($con,$_REQUEST['Keyword'])."%')";
			$sql.=" order by id desc";
			$result = mysqli_query($con, $sql);
			$TotalRecordCount=mysqli_num_rows($result);
			$Totalpages 		= mysqli_query($con, "SELECT * FROM `tbl_control`");
			$exec_paging		= mysqli_fetch_array($Totalpages);
			$end_count 			= $exec_paging['no_of_pages'];
			$Limits				= $exec_paging['results_per_page'];
			$Limits = $Limits;
			$Page = $_REQUEST['page'];
			if($Page==""){
				$Page=1;
			}
			$pageCount = ceil($TotalRecordCount/$Limits); 
			$StartLimit = (($Page-1)*$Limits);
			if($TotalRecordCount > ($Page*$Limits)){
				$EndLimit = $Page*$Limits;
			}else{
				$EndLimit = $TotalRecordCount;
			}
			$sql1=" LIMIT ".$StartLimit.",".$Limits;
			$query=$sql.$sql1;
			$Records=array();
			if($Page==1)
				$k=1;
			else
				$k=(($Page-1)*$Limits)+1;
			$Res=mysqli_query($con, $query);
			$Record	=	array();
			$i			=	0;
			while($res=mysqli_fetch_array($Res)) {
				$Record[$i]['S_NO']		=$k;
				$Record[$i]['id']		=$res['id'];
				$Record[$i]['Res_AssignUserID']		=$res['Res_AssignUserID'];
				$Record[$i]['contactName']=$res['contactName'];
				$Record[$i]['merchantName']=$res['merchantName'];
				$Record[$i]['contactAddress']=$res['contactAddress'];
				$Record[$i]['city']=$res['city'];
				$Record[$i]['state']=$res['state'];
				$Record[$i]['status']=$res['status'];
				$i++;
				$k++;
			}
		return array($Record,$TotalRecordCount);
	}
	function Add_Restaurant()
	{
		GLOBAL $Map_API;
		GLOBAL $con;
		$State=mysqli_real_escape_string($con,$_REQUEST['State']);
		$City=mysqli_real_escape_string($con,$_REQUEST['City']);
		$cuisines=implode(",",$_REQUEST['cuisine']);
		$loc=$City.",".$State;
		$where = stripslashes($_REQUEST['Zip_Code']);
		$whereurl = urlencode($where);
		$geocode=file_get_contents('http://maps.google.com/maps/api/geocode/json?address='.$whereurl.'&sensor=false');

		$output= json_decode($geocode);

		$lat = $output->results[0]->geometry->location->lat;
		$long = $output->results[0]->geometry->location->lng;
		
		if($_REQUEST['ID']>0)
		{
			$sql="UPDATE merchant SET merchantName='".mysqli_real_escape_string($con,$_REQUEST['merchantname'])."',contactName='".mysqli_real_escape_string($con,$_REQUEST['contactname'])."',cuisine='".$cuisines."',state='".trim($State)."',city='".$City."',contactAddress='".mysqli_real_escape_string($con,$_REQUEST['streetaddress'])."',crossStreet='".mysqli_real_escape_string($con,$_REQUEST['crossstreets'])."',postalCode='".mysqli_real_escape_string($con,$_REQUEST['Zip_Code'])."',telephone='".mysqli_real_escape_string($con,$_REQUEST['phonenumber'])."',faxNumber='".mysqli_real_escape_string($con,$_REQUEST['faxnumber'])."',gsmNumber='".mysqli_real_escape_string($con,$_REQUEST['gsmnumber'])."',email='".mysqli_real_escape_string($con,$_REQUEST['email'])."',delivery='".mysqli_real_escape_string($con,$_REQUEST['deliveryavailable'])."',takeout='".mysqli_real_escape_string($con,$_REQUEST['takeoutavailable'])."',tablebooking='".mysqli_real_escape_string($con,$_REQUEST['tablebooking'])."',phoneorderonly='".mysqli_real_escape_string($con,$_REQUEST['phoneorderonly'])."',payment_cash='".mysqli_real_escape_string($con,$_REQUEST['payment_cash'])."',payment_cc='".mysqli_real_escape_string($con,$_REQUEST['payment_cc'])."',Neighborhood='".mysqli_real_escape_string($con,$_REQUEST['Neighborhood'])."',executive_chef='".mysqli_real_escape_string($con,$_REQUEST['executive_chef'])."',dress_code='".mysqli_real_escape_string($con,$_REQUEST['dress_code'])."',additional_service='".mysqli_real_escape_string($con,$_REQUEST['additional_service'])."',public_transit='".mysqli_real_escape_string($con,$_REQUEST['public_transit'])."',parking='".mysqli_real_escape_string($con,$_REQUEST['parking'])."',private_party_facility='".mysqli_real_escape_string($con,$_REQUEST['private_party_facility'])."',private_party_contact='".mysqli_real_escape_string($con,$_REQUEST['private_party_contact'])."',catering='".mysqli_real_escape_string($con,$_REQUEST['catering'])."',aboutUs='".mysqli_real_escape_string($con,$_REQUEST['aboutus'])."',dining_style='".mysqli_real_escape_string($con,$_REQUEST['dining_style'])."',promotions='".mysqli_real_escape_string($con,$_REQUEST['promotions'])."',entertainment='".mysqli_real_escape_string($con,$_REQUEST['entertainment'])."',timezone='".mysqli_real_escape_string($con,$_REQUEST['timezone'])."',geoLong='".$long."',geoLat='".$lat."' WHERE id=".mysqli_real_escape_string($con,$_REQUEST['ID']);
			$rep=2;
			$rec=mysqli_query($con, $sql); 
			$Merchant_ID=$_REQUEST['ID'];
		}
		else
		{
			$sql="INSERT INTO merchant(Res_UserID,merchantName,contactName,state,city,contactAddress,cuisine,crossStreet,postalCode,telephone,faxNumber,gsmNumber,email,status,delivery,takeout,tablebooking,phoneorderonly,payment_cash,payment_cc,createdOn,Neighborhood,executive_chef,dress_code,additional_service,public_transit,parking,private_party_facility,private_party_contact,catering,aboutUs,dining_style,promotions,entertainment,timezone,geoLong,geoLat) VALUES('".$_SESSION['User_ID']."','".mysqli_real_escape_string($con,$_REQUEST['merchantname'])."','".mysqli_real_escape_string($con,$_REQUEST['contactname'])."','".trim($State)."','".$City."','".mysqli_real_escape_string($con,$_REQUEST['streetaddress'])."','".$cuisines."','".mysqli_real_escape_string($con,$_REQUEST['crossstreets'])."','".mysqli_real_escape_string($con,$_REQUEST['Zip_Code'])."','".mysqli_real_escape_string($con,$_REQUEST['phonenumber'])."','".mysqli_real_escape_string($con,$_REQUEST['faxnumber'])."','".mysqli_real_escape_string($con,$_REQUEST['gsmnumber'])."','".mysqli_real_escape_string($con,$_REQUEST['email'])."','1','".mysqli_real_escape_string($con,$_REQUEST['deliveryavailable'])."','".mysqli_real_escape_string($con,$_REQUEST['takeoutavailable'])."','".mysqli_real_escape_string($con,$_REQUEST['tablebooking'])."','".mysqli_real_escape_string($con,$_REQUEST['phoneorderonly'])."','".mysqli_real_escape_string($con,$_REQUEST['payment_cash'])."','".mysqli_real_escape_string($con,$_REQUEST['payment_cc'])."',Now(),'".mysqli_real_escape_string($con,$_REQUEST['Neighborhood'])."','".mysqli_real_escape_string($con,$_REQUEST['executive_chef'])."','".mysqli_real_escape_string($con,$_REQUEST['dress_code'])."','".mysqli_real_escape_string($con,$_REQUEST['additional_service'])."','".mysqli_real_escape_string($con,$_REQUEST['public_transit'])."','".mysqli_real_escape_string($con,$_REQUEST['parking'])."','".mysqli_real_escape_string($con,$_REQUEST['private_party_facility'])."','".mysqli_real_escape_string($con,$_REQUEST['private_party_contact'])."','".mysqli_real_escape_string($con,$_REQUEST['catering'])."','".mysqli_real_escape_string($con,$_REQUEST['aboutus'])."','".mysqli_real_escape_string($con,$_REQUEST['dining_style'])."','".mysqli_real_escape_string($con,$_REQUEST['promotions'])."','".mysqli_real_escape_string($con,$_REQUEST['entertainment'])."','".mysqli_real_escape_string($con,$_REQUEST['timezone'])."','".$long."','".$lat."')";
			$rep=1;
			$rec=mysqli_query($con, $sql); 
			$Merchant_ID=mysqli_insert_id($con,$rec);
		}
		//echo $sql;exit;
		$sql="DELETE FROM merchant_cuisine WHERE merchantID=".$Merchant_ID;
		mysqli_query($con, $sql);
		
		$Cuisines=explode(",",$cuisines);
		$cuisinecnt=0;
		foreach($Cuisines as $u=>$v)
		{
			if($v<>'')
			{
			$sql="INSERT INTO merchant_cuisine(merchantID,cuisineID) VALUES(".$Merchant_ID.",".$v.")";
			mysqli_query($con, $sql);
			}
		}
		if($cuisinecnt==0)
		{
		$sql="INSERT INTO merchant_cuisine(merchantID,cuisineID) VALUES(".$Merchant_ID.",0)";
			mysqli_query($con, $sql);
		}
		
			echo '<script language="javascript">location.href="dine.php?act=my_restaurants&rep='.$rep.'";</script>'; 
		exit;
	}
	
	function add_item()
	{
		GLOBAL $con;
		$cuisines=implode(",",$_REQUEST['cuisine']);
		// image Upload
		if ($_FILES['image_file']['tmp_name']!= "") 
		{
			$validation_type = 2;

		if($validation_type == 1)
		{
			$mime = array('image/gif' => 'gif',
						  'image/jpeg' => 'jpeg',
						  'image/png' => 'png',
						  'application/x-shockwave-flash' => 'swf',
						  'image/psd' => 'psd',
						  'image/bmp' => 'bmp',
						  'image/tiff' => 'tiff',
						  'image/tiff' => 'tiff',
						  'image/jp2' => 'jp2',
						  'image/iff' => 'iff',
						  'image/vnd.wap.wbmp' => 'bmp',
						  'image/xbm' => 'xbm',
						  'image/vnd.microsoft.icon' => 'ico');
		}
		else if($validation_type == 2) // Second choice? Set the extensions
		{
			$image_extensions_allowed = array('jpg', 'jpeg', 'png', 'gif');
		}

		$upload_image_to_folder = '../admin/upload/users/';
		$file = $_FILES['image_file'];

		$file_name = $file['name'];
		$file_name=preg_replace("/[^a-zA-Z0-9.s]/", "", $file_name);

		$error = ''; // Empty

		// Get File Extension (if any)

		$ext = strtolower(substr(strrchr($file_name, "."), 1));

		// Check for a correct extension. The image file hasn't an extension? Add one

		   if($validation_type == 1)
		   {
			 $file_info = getimagesize($_FILES['image_file']['tmp_name']);

			  if(empty($file_info)) // No Image?
			  {
			  $error .= "The uploaded file doesn't seem to be an image.";
			  }
			  else // An Image?
			  {
			  $file_mime = $file_info['mime'];

				 if($ext == 'jpc' || $ext == 'jpx' || $ext == 'jb2')
				 {
				 $extension = $ext;
				 }
				 else
				 {
				 $extension = ($mime[$file_mime] == 'jpeg') ? 'jpg' : $mime[$file_mime];
				 }

				 if(!$extension)
				 {
				 $extension = '';  
				 $file_name = str_replace('.', '', $file_name); 
				 }
			  }
		   }
		   else if($validation_type == 2)
		   {
			  if(!in_array($ext, $image_extensions_allowed))
			  {
			  $exts = implode(', ',$image_extensions_allowed);
			  $error .= "You must upload a file with one of the following extensions: ".$exts;
			  }

			  $extension = $ext;
		   }
			if($error == "") // No errors were found?
		   {
		   $new_file_name = strtolower($file_name);
		   $new_file_name = str_replace(' ', '-', $new_file_name);
		   $new_file_name = substr($new_file_name, 0, -(strlen($ext)+1));
		   $new_file_name .= strtotime(date("F j, Y, g:i a")).".".$extension;
		   
		   // File Name
		   $move_file = move_uploaded_file($file['tmp_name'], $upload_image_to_folder.$new_file_name);

			 if($move_file)
			  {
			  $done = 'The image has been uploaded.';
			  }
		   }
		   else
		   {
		   @unlink($file['tmp_name']);
		   }
	
		   $file_uploaded = true;
	}
	else
	{
		$file_uploaded='';$done='';$error='';
		$new_file_name=$_REQUEST['logo'];
	}
		if($_REQUEST['popular']=='')
			$popular=0;
		else
			$popular=$_REQUEST['popular'];
		if($_REQUEST['spicy']=='')
			$spicy=0;
		else
			$spicy=$_REQUEST['spicy'];
		if($_REQUEST['veggie']=='')
			$veggie=0;
		else
			$veggie=$_REQUEST['veggie'];
		if($_REQUEST['chef_special']=='')
			$chef_special=0;
		else
			$chef_special=$_REQUEST['chef_special'];
		
		if($_REQUEST['price1']=='')
			$price1=0;
		else
			$price1=$_REQUEST['price1'];
		if($_REQUEST['price2']=='')
			$price2=0;
		else
			$price2=$_REQUEST['price2'];
		if($_REQUEST['price3']=='')
			$price3=0;
		else
			$price3=$_REQUEST['price3'];
		if($_REQUEST['price4']=='')
			$price4=0;
		else
			$price4=$_REQUEST['price4'];
		if($_REQUEST['price5']=='')
			$price5=0;
		else
			$price5=$_REQUEST['price5'];
		if($_REQUEST['price6']=='')
			$price6=0;
		else
			$price6=$_REQUEST['price6'];
		if($_REQUEST['price7']=='')
			$price7=0;
		else
			$price7=$_REQUEST['price7'];
		if($_REQUEST['price8']=='')
			$price8=0;
		else
			$price8=$_REQUEST['price8'];
		if($_REQUEST['price9']=='')
			$price9=0;
		else
			$price9=$_REQUEST['price9'];
		if($_REQUEST['price10']=='')
			$price10=0;
		else
			$price10=$_REQUEST['price10'];
			
		if($_REQUEST['price11']=='')
			$price11=0;
		else
			$price11=$_REQUEST['price11'];
		if($_REQUEST['price12']=='')
			$price12=0;
		else
			$price12=$_REQUEST['price12'];
		if($_REQUEST['price13']=='')
			$price13=0;
		else
			$price13=$_REQUEST['price13'];
		if($_REQUEST['price14']=='')
			$price14=0;
		else
			$price14=$_REQUEST['price14'];
		if($_REQUEST['price15']=='')
			$price15=0;
		else
			$price15=$_REQUEST['price15'];
		if($_REQUEST['price16']=='')
			$price16=0;
		else
			$price16=$_REQUEST['price16'];
		if($_REQUEST['price17']=='')
			$price17=0;
		else
			$price17=$_REQUEST['price17'];
		if($_REQUEST['price18']=='')
			$price18=0;
		else
			$price18=$_REQUEST['price18'];
		if($_REQUEST['price19']=='')
			$price19=0;
		else
			$price19=$_REQUEST['price19'];
		if($_REQUEST['price20']=='')
			$price20=0;
		else
			$price20=$_REQUEST['price20'];
		
		if($_REQUEST['Cus_Pizza']=='')
			$Cus_Pizza=0;
		else
			$Cus_Pizza=$_REQUEST['Cus_Pizza'];
		if($_REQUEST['Multiple_Toppings']=='')
			$Multiple_Toppings=0;
		else
			$Multiple_Toppings=$_REQUEST['Multiple_Toppings'];
		if($_REQUEST['Free_Toppings']=='')
			$Free_Toppings=0;
		else
			$Free_Toppings=$_REQUEST['Free_Toppings'];
		if($_REQUEST['Max_Toppings']=='')
			$Max_Toppings=0;
		else
			$Max_Toppings=$_REQUEST['Max_Toppings'];
			
		if($_REQUEST['Item_ID']>0)
		{
			$sql="UPDATE items SET itemName='".mysqli_real_escape_string($con,$_REQUEST['itemname'])."',itemImage='".$new_file_name."',
			itemDescription='".mysqli_real_escape_string($con,$_REQUEST['description'])."',
			itemStatus='".$_REQUEST['status']."',popular='".$popular."',spicy='".$spicy."',
			veggie='".$veggie."',chef_special='".$chef_special."',itemPrice='".mysqli_real_escape_string($con,$price1)."',
			itemPrice1='".mysqli_real_escape_string($con,$price2)."',itemPrice2='".mysqli_real_escape_string($con,$price3)."',
			itemPrice3='".mysqli_real_escape_string($con,$price4)."',itemPrice4='".mysqli_real_escape_string($con,$price5)."',
			itemPrice5='".mysqli_real_escape_string($con,$price6)."',itemPrice6='".mysqli_real_escape_string($con,$price7)."',
			itemPrice7='".mysqli_real_escape_string($con,$price8)."',itemPrice8='".mysqli_real_escape_string($con,$price9)."',
			itemPrice9='".mysqli_real_escape_string($con,$price10)."',
			itemPrice10='".mysqli_real_escape_string($con,$price11)."',
			itemPrice11='".mysqli_real_escape_string($con,$price12)."',
			itemPrice12='".mysqli_real_escape_string($con,$price13)."',
			itemPrice13='".mysqli_real_escape_string($con,$price14)."',
			itemPrice14='".mysqli_real_escape_string($con,$price15)."',
			itemPrice15='".mysqli_real_escape_string($con,$price16)."',
			itemPrice16='".mysqli_real_escape_string($con,$price17)."',
			itemPrice17='".mysqli_real_escape_string($con,$price18)."',
			itemPrice18='".mysqli_real_escape_string($con,$price19)."',
			itemPrice19='".mysqli_real_escape_string($con,$price20)."',
			Cus_Pizza='".mysqli_real_escape_string($con,$Cus_Pizza)."',
			Multiple_Toppings='".mysqli_real_escape_string($con,$Multiple_Toppings)."',
			Free_Toppings='".mysqli_real_escape_string($con,$Free_Toppings)."',
			Max_Toppings='".mysqli_real_escape_string($con,$Max_Toppings)."',lastUpdatedOn=Now() WHERE id=".mysqli_real_escape_string($con,$_REQUEST['Item_ID']);
			
			$rec=mysqli_query($con, $sql); 
			$Item_ID=$_REQUEST['Item_ID'];
		}
		else
		{
			$sql="INSERT INTO items(menuID,itemName,itemImage,itemDescription,itemStatus,popular,spicy,veggie,chef_special,itemPrice,itemPrice1,itemPrice2,itemPrice3,itemPrice4,itemPrice5,itemPrice6,itemPrice7,itemPrice8,itemPrice9,Cus_Pizza,Multiple_Toppings,Free_Toppings,Max_Toppings,lastUpdatedOn) VALUES('".mysqli_real_escape_string($con,$_REQUEST['Menu'])."','".mysqli_real_escape_string($con,$_REQUEST['itemname'])."','".$new_file_name."','".mysqli_real_escape_string($con,$_REQUEST['description'])."','".$_REQUEST['status']."','".$popular."','".$spicy."','".$veggie."','".$chef_special."','".mysqli_real_escape_string($con,$price1)."','".mysqli_real_escape_string($con,$price2)."','".mysqli_real_escape_string($con,$price3)."','".mysqli_real_escape_string($con,$price4)."','".mysqli_real_escape_string($con,$price5)."','".mysqli_real_escape_string($con,$price6)."','".mysqli_real_escape_string($con,$price7)."','".mysqli_real_escape_string($con,$price8)."','".mysqli_real_escape_string($con,$price9)."','".mysqli_real_escape_string($con,$price10)."','".$Cus_Pizza."','".$Multiple_Toppings."','".$Max_Toppings."','".$Free_Toppings."',Now())";
			$rec=mysqli_query($con, $sql); 
			$Item_ID=mysqli_insert_id($con);
		}
		//echo $sql;exit;
		
		$sql="DELETE FROM subitems WHERE itemID=".$Item_ID;
		mysqli_query($con, $sql);
		$suboption=$_REQUEST['suboption'];
		$subgroup=$_REQUEST['subgroup'];
		$suboptionprice=$_REQUEST['suboptionprice'];
		$pp=1;
		for($i=0;$i<COUNT($suboption);$i+=1)
		{
			//str_replace("\"", "&quot;",$_REQUEST['size5'])
			$orderid=2;
			if($suboption[$i]=='Pizza sauce')
				$orderid=1;
			if($suboption[$i]<>'') {
			$sql="INSERT INTO subitems(itemID,subgroup_id,subitemName,subitemPrice,Sel,status,lastUpdatedOn,sortOrder) VALUES(".$Item_ID.",'".mysqli_real_escape_string($con,$subgroup[$i])."','".mysqli_real_escape_string($con,str_replace("\"", "&quot;",$suboption[$i]))."','".mysqli_real_escape_string($con,$suboptionprice[$i])."',".$_REQUEST['suboptionsel'.$pp].",'Active',Now(),".$orderid.")";
			mysqli_query($con, $sql);
			}
			$pp++;
		}
		
		// Update MAX & MIN Price for Merchant Table
		$sql="SELECT DISTINCT(id) AS MENU FROM menus WHERE merchantID=".$_REQUEST['Res_ID'];
		$exe=mysqli_query($con, $sql);
		$menus="";
		while($res=mysqli_fetch_array($exe))
		{
			$menus.=",".$res['MENU'];
		}
		$menus=trim($menus,',');
		$sql="SELECT 
		MIN(itemPrice) AS MINPRICE1,
		MIN(itemPrice1) AS MINPRICE2,
		MIN(itemPrice2) AS MINPRICE3,
		MIN(itemPrice3) AS MINPRICE4,
		MIN(itemPrice4) AS MINPRICE5,
		MIN(itemPrice5) AS MINPRICE6,
		MIN(itemPrice6) AS MINPRICE7,
		MIN(itemPrice7) AS MINPRICE8,
		MIN(itemPrice8) AS MINPRICE9,
		MIN(itemPrice9) AS MINPRICE10,
		MIN(itemPrice10) AS MINPRICE11,
		MIN(itemPrice11) AS MINPRICE12,
		MIN(itemPrice12) AS MINPRICE13,
		MIN(itemPrice13) AS MINPRICE14,
		MIN(itemPrice14) AS MINPRICE15,
		MIN(itemPrice15) AS MINPRICE16,
		MIN(itemPrice16) AS MINPRICE17,
		MIN(itemPrice17) AS MINPRICE18,
		MIN(itemPrice18) AS MINPRICE18,
		MIN(itemPrice19) AS MINPRICE20,
		MAX(itemPrice) AS MAXPRICE1, 
		MAX(itemPrice1) AS MAXPRICE2,
		MAX(itemPrice2) AS MAXPRICE3,
		MAX(itemPrice3) AS MAXPRICE4,
		MAX(itemPrice4) AS MAXPRICE5,
		MAX(itemPrice5) AS MAXPRICE6,
		MAX(itemPrice6) AS MAXPRICE7,
		MAX(itemPrice7) AS MAXPRICE8,
		MAX(itemPrice8) AS MAXPRICE9,
		MAX(itemPrice9) AS MAXPRICE10,
		MAX(itemPrice10) AS MAXPRICE11,
		MAX(itemPrice11) AS MAXPRICE12,
		MAX(itemPrice12) AS MAXPRICE13,
		MAX(itemPrice13) AS MAXPRICE14,
		MAX(itemPrice14) AS MAXPRICE15,
		MAX(itemPrice15) AS MAXPRICE16,
		MAX(itemPrice16) AS MAXPRICE17,
		MAX(itemPrice17) AS MAXPRICE18,
		MAX(itemPrice18) AS MAXPRICE18,
		MAX(itemPrice19) AS MAXPRICE20
		FROM items WHERE menuID IN(".$menus.")";
		$exe=mysqli_query($con, $sql);
		$res=mysqli_fetch_array($exe);
		for($i=1;$i<=20;$i++)
		{
			if($res['MINPRICE'.$i]>0)
				$MINPRICES.=",".$res['MINPRICE'.$i];
			if($res['MAXPRICE'.$i]>0)
				$MAXPRICES.=",".$res['MAXPRICE'.$i];
		}
		$MINPRICES=trim($MINPRICES,',');
		$MAXPRICES=trim($MAXPRICES,',');
		$MINPRICES=explode(",",$MINPRICES);
		$MAXPRICES=explode(",",$MAXPRICES);
		$MINPRICE=MIN($MINPRICES);
		$MAXPRICE=MAX($MAXPRICES);
		if($MINPRICE=='')
			$MINPRICE=0;
		if($MAXPRICE=='')
			$MAXPRICE=0;
		
		$sql="UPDATE merchant SET Min_price=".$MINPRICE.", Max_price=".$MAXPRICE." WHERE id=".$_REQUEST['Res_ID'];
		mysqli_query($con, $sql);
		
		// Groups
		echo '<script language="javascript">location.href="dine.php?act=menu_item&Res_ID='.$_REQUEST['Res_ID'].'&Menu='.$_REQUEST['Menu'].'&rep=1";</script>'; 
		exit;
	}
	function duplicate_item()
	{
		GLOBAL $con;
		$cuisines=implode(",",$_REQUEST['cuisine']);
		// image Upload
			if ($_FILES['image_file']['tmp_name']!= "") 
			{
				$validation_type = 2;

			if($validation_type == 1)
			{
				$mime = array('image/gif' => 'gif',
							  'image/jpeg' => 'jpeg',
							  'image/png' => 'png',
							  'application/x-shockwave-flash' => 'swf',
							  'image/psd' => 'psd',
							  'image/bmp' => 'bmp',
							  'image/tiff' => 'tiff',
							  'image/tiff' => 'tiff',
							  'image/jp2' => 'jp2',
							  'image/iff' => 'iff',
							  'image/vnd.wap.wbmp' => 'bmp',
							  'image/xbm' => 'xbm',
							  'image/vnd.microsoft.icon' => 'ico');
			}
			else if($validation_type == 2) // Second choice? Set the extensions
			{
				$image_extensions_allowed = array('jpg', 'jpeg', 'png', 'gif');
			}

			$upload_image_to_folder = '../admin/upload/users/';
			$file = $_FILES['image_file'];

			$file_name = $file['name'];
			$file_name=preg_replace("/[^a-zA-Z0-9.s]/", "", $file_name);

			$error = ''; // Empty

			// Get File Extension (if any)

			$ext = strtolower(substr(strrchr($file_name, "."), 1));

			// Check for a correct extension. The image file hasn't an extension? Add one

			   if($validation_type == 1)
			   {
				 $file_info = getimagesize($_FILES['image_file']['tmp_name']);

				  if(empty($file_info)) // No Image?
				  {
				  $error .= "The uploaded file doesn't seem to be an image.";
				  }
				  else // An Image?
				  {
				  $file_mime = $file_info['mime'];

					 if($ext == 'jpc' || $ext == 'jpx' || $ext == 'jb2')
					 {
					 $extension = $ext;
					 }
					 else
					 {
					 $extension = ($mime[$file_mime] == 'jpeg') ? 'jpg' : $mime[$file_mime];
					 }

					 if(!$extension)
					 {
					 $extension = '';  
					 $file_name = str_replace('.', '', $file_name); 
					 }
				  }
			   }
			   else if($validation_type == 2)
			   {
				  if(!in_array($ext, $image_extensions_allowed))
				  {
				  $exts = implode(', ',$image_extensions_allowed);
				  $error .= "You must upload a file with one of the following extensions: ".$exts;
				  }

				  $extension = $ext;
			   }
				if($error == "") // No errors were found?
			   {
			   $new_file_name = strtolower($file_name);
			   $new_file_name = str_replace(' ', '-', $new_file_name);
			   $new_file_name = substr($new_file_name, 0, -(strlen($ext)+1));
			   $new_file_name .= strtotime(date("F j, Y, g:i a")).".".$extension;
			   
			   // File Name
			   $move_file = move_uploaded_file($file['tmp_name'], $upload_image_to_folder.$new_file_name);

				 if($move_file)
				  {
				  $done = 'The image has been uploaded.';
				  }
			   }
			   else
			   {
			   @unlink($file['tmp_name']);
			   }
		
			   $file_uploaded = true;
		}
		else
		{
			$file_uploaded='';$done='';$error='';
			$new_file_name=$_REQUEST['logo'];
		}
		
		if($_REQUEST['popular']=='')
			$popular=0;
		else
			$popular=$_REQUEST['popular'];
		if($_REQUEST['spicy']=='')
			$spicy=0;
		else
			$spicy=$_REQUEST['spicy'];
		if($_REQUEST['veggie']=='')
			$veggie=0;
		else
			$veggie=$_REQUEST['veggie'];
		if($_REQUEST['chef_special']=='')
			$chef_special=0;
		else
			$chef_special=$_REQUEST['chef_special'];
		
		if($_REQUEST['price1']=='')
			$price1=0;
		else
			$price1=$_REQUEST['price1'];
		if($_REQUEST['price2']=='')
			$price2=0;
		else
			$price2=$_REQUEST['price2'];
		if($_REQUEST['price3']=='')
			$price3=0;
		else
			$price3=$_REQUEST['price3'];
		if($_REQUEST['price4']=='')
			$price4=0;
		else
			$price4=$_REQUEST['price4'];
		if($_REQUEST['price5']=='')
			$price5=0;
		else
			$price5=$_REQUEST['price5'];
		if($_REQUEST['price6']=='')
			$price6=0;
		else
			$price6=$_REQUEST['price6'];
		if($_REQUEST['price7']=='')
			$price7=0;
		else
			$price7=$_REQUEST['price7'];
		if($_REQUEST['price8']=='')
			$price8=0;
		else
			$price8=$_REQUEST['price8'];
		if($_REQUEST['price9']=='')
			$price9=0;
		else
			$price9=$_REQUEST['price9'];
		if($_REQUEST['price10']=='')
			$price10=0;
		else
			$price10=$_REQUEST['price10'];
			
		if($_REQUEST['price11']=='')
			$price11=0;
		else
			$price11=$_REQUEST['price11'];
		if($_REQUEST['price12']=='')
			$price12=0;
		else
			$price12=$_REQUEST['price12'];
		if($_REQUEST['price13']=='')
			$price13=0;
		else
			$price13=$_REQUEST['price13'];
		if($_REQUEST['price14']=='')
			$price14=0;
		else
			$price14=$_REQUEST['price14'];
		if($_REQUEST['price15']=='')
			$price15=0;
		else
			$price15=$_REQUEST['price15'];
		if($_REQUEST['price16']=='')
			$price16=0;
		else
			$price16=$_REQUEST['price16'];
		if($_REQUEST['price17']=='')
			$price17=0;
		else
			$price17=$_REQUEST['price17'];
		if($_REQUEST['price18']=='')
			$price18=0;
		else
			$price18=$_REQUEST['price18'];
		if($_REQUEST['price19']=='')
			$price19=0;
		else
			$price19=$_REQUEST['price19'];
		if($_REQUEST['price20']=='')
			$price20=0;
		else
			$price20=$_REQUEST['price20'];
		
		if($_REQUEST['Cus_Pizza']=='')
			$Cus_Pizza=0;
		else
			$Cus_Pizza=$_REQUEST['Cus_Pizza'];
		if($_REQUEST['Multiple_Toppings']=='')
			$Multiple_Toppings=0;
		else
			$Multiple_Toppings=$_REQUEST['Multiple_Toppings'];
		if($_REQUEST['Free_Toppings']=='')
			$Free_Toppings=0;
		else
			$Free_Toppings=$_REQUEST['Free_Toppings'];
		if($_REQUEST['Max_Toppings']=='')
			$Max_Toppings=0;
		else
			$Max_Toppings=$_REQUEST['Max_Toppings'];
			
			
		
			$sql="INSERT INTO items(menuID,itemName,itemImage,itemDescription,itemStatus,popular,spicy,veggie,chef_special,itemPrice,itemPrice1,itemPrice2,itemPrice3,itemPrice4,itemPrice5,itemPrice6,itemPrice7,itemPrice8,itemPrice9,itemPrice10,itemPrice11,itemPrice12,itemPrice13,itemPrice14,itemPrice15,itemPrice16,itemPrice17,itemPrice18,itemPrice19,Cus_Pizza,Multiple_Toppings,Free_Toppings,Max_Toppings,lastUpdatedOn) VALUES('".mysqli_real_escape_string($con,$_REQUEST['Menu'])."','".mysqli_real_escape_string($con,$_REQUEST['itemname'])."','".$new_file_name."','".mysqli_real_escape_string($con,$_REQUEST['description'])."','".$_REQUEST['status']."','".$popular."','".$spicy."','".$veggie."','".$chef_special."','".mysqli_real_escape_string($con,$price1)."','".mysqli_real_escape_string($con,$price2)."','".mysqli_real_escape_string($con,$price3)."','".mysqli_real_escape_string($con,$price4)."','".mysqli_real_escape_string($con,$price5)."','".mysqli_real_escape_string($con,$price6)."','".mysqli_real_escape_string($con,$price7)."','".mysqli_real_escape_string($con,$price8)."','".mysqli_real_escape_string($con,$price9)."','".mysqli_real_escape_string($con,$price10)."','".mysqli_real_escape_string($con,$price11)."','".mysqli_real_escape_string($con,$price12)."','".mysqli_real_escape_string($con,$price13)."','".mysqli_real_escape_string($con,$price14)."','".mysqli_real_escape_string($con,$price15)."','".mysqli_real_escape_string($con,$price16)."','".mysqli_real_escape_string($con,$price17)."','".mysqli_real_escape_string($con,$price18)."','".mysqli_real_escape_string($con,$price19)."','".mysqli_real_escape_string($con,$price20)."','".$Cus_Pizza."','".$Multiple_Toppings."','".$Max_Toppings."','".$Free_Toppings."',Now())";
			
			$rec=mysqli_query($con, $sql); 
			$Item_ID=mysqli_insert_id($con);
		
		//echo $sql;exit;
		$suboption=$_REQUEST['suboption'];
		$subgroup=$_REQUEST['subgroup'];
		$suboptionprice=$_REQUEST['suboptionprice'];
		$pp=1;
		for($i=0;$i<COUNT($suboption);$i+=1)
		{
			//str_replace("\"", "&quot;",$_REQUEST['size5'])
			$orderid=2;
			if($suboption[$i]=='Pizza sauce')
				$orderid=1;
			if($suboption[$i]<>'') {
			$sql="INSERT INTO subitems(itemID,subgroup_id,subitemName,subitemPrice,Sel,status,lastUpdatedOn,sortOrder) VALUES(".$Item_ID.",'".mysqli_real_escape_string($con,$subgroup[$i])."','".mysqli_real_escape_string($con,str_replace("\"", "&quot;",$suboption[$i]))."','".mysqli_real_escape_string($con,$suboptionprice[$i])."',".$_REQUEST['suboptionsel'.$pp].",'Active',Now(),".$orderid.")";
			mysqli_query($con, $sql);
			}
			$pp++;
		}
		//exit;
		// Groups
			echo '<script language="javascript">location.href="dine.php?act=menu_item&Res_ID='.$_REQUEST['Res_ID'].'&Menu='.$_REQUEST['Menu'].'&rep=1";</script>'; 
		exit;
	}
	function view_menus() {
			GLOBAL $con;
			$sql="SELECT * from menus WHERE Deleted=0 AND merchantID=".mysqli_real_escape_string($con,$_REQUEST['Res_ID']);
			if($_REQUEST['Keyword']<>'')
			$sql.=" AND (menuName LIKE '%".mysqli_real_escape_string($con,$_REQUEST['Keyword'])."%' OR menuDescription LIKE '%".mysqli_real_escape_string($con,$_REQUEST['Keyword'])."%')";
			$sql.=" ORDER BY menuName ASC";
			$result = mysqli_query($con, $sql);
			$TotalRecordCount=mysqli_num_rows($result);
			$Totalpages 		= mysqli_query($con, "SELECT * FROM `tbl_control`");
			$exec_paging		= mysqli_fetch_array($Totalpages);
			$end_count 			= $exec_paging['no_of_pages'];
			$Limits				= $exec_paging['results_per_page'];
			$Limits = $Limits;
			$Page = $_REQUEST['page'];
			if($Page==""){
				$Page=1;
			}
			$pageCount = ceil($TotalRecordCount/$Limits); 
			$StartLimit = (($Page-1)*$Limits);
			if($TotalRecordCount > ($Page*$Limits)){
				$EndLimit = $Page*$Limits;
			}else{
				$EndLimit = $TotalRecordCount;
			}
			$sql1=" LIMIT ".$StartLimit.",".$Limits;
			$query=$sql.$sql1;
			$Records=array();
			if($Page==1)
				$k=1;
			else
				$k=(($Page-1)*$Limits)+1;
			$Res=mysqli_query($con, $query);
			$Record	=	array();
			$i			=	0;
			while($res=mysqli_fetch_array($Res)) {
				$Record[$i]['id']		=$res['id'];
				$Record[$i]['S_NO']		=$k;
				$Record[$i]['menuName']=$res['menuName'];
				$Record[$i]['menuStatus']=$res['menuStatus'];
				$Record[$i]['Main_Menu']=$res['Main_Menu'];
				$i++;
				$k++;
			}
		return array($Record,$TotalRecordCount);
	}
	function GetMenuDetails($menu) {
			GLOBAL $con;
			$sql="Select * from menus WHERE Deleted=0 AND id=".$menu." order by menuName ASC";
			$result = mysqli_query($con, $sql);
			$TotalRecordCount=mysqli_num_rows($result);
			$Totalpages 		= mysqli_query($con, "SELECT * FROM `tbl_control`");
			$exec_paging		= mysqli_fetch_array($Totalpages);
			$end_count 			= $exec_paging['no_of_pages'];
			$Limits				= $exec_paging['results_per_page'];
			$Limits = $Limits;
			$Page = $_REQUEST['page'];
			if($Page==""){
				$Page=1;
			}
			$pageCount = ceil($TotalRecordCount/$Limits); 
			$StartLimit = (($Page-1)*$Limits);
			if($TotalRecordCount > ($Page*$Limits)){
				$EndLimit = $Page*$Limits;
			}else{
				$EndLimit = $TotalRecordCount;
			}
			$sql1=" LIMIT ".$StartLimit.",".$Limits;
			$query=$sql.$sql1;
			$Records=array();
			$i=1;
			$Res=mysqli_query($con, $query);
			$Record	=	array();
			$i			=	0;
			while($res=mysqli_fetch_array($Res)) {
				$Record[$i]['id']		=$res['id'];
				$Record[$i]['menuName']=$res['menuName'];
				$Record[$i]['menuStatus']=$res['menuStatus'];
				$Record[$i]['menuSize1']=$res['menuSize1'];
				$Record[$i]['menuSize2']=$res['menuSize2'];
				$Record[$i]['menuSize3']=$res['menuSize3'];
				$Record[$i]['menuSize4']=$res['menuSize4'];
				$Record[$i]['menuSize5']=$res['menuSize5'];
				$Record[$i]['menuSize6']=$res['menuSize6'];
				$Record[$i]['menuSize7']=$res['menuSize7'];
				$Record[$i]['menuSize8']=$res['menuSize8'];
				$Record[$i]['menuSize9']=$res['menuSize9'];
				$Record[$i]['menuSize10']=$res['menuSize10'];
				$Record[$i]['menuSize11']=$res['menuSize11'];
				$Record[$i]['menuSize12']=$res['menuSize12'];
				$Record[$i]['menuSize13']=$res['menuSize13'];
				$Record[$i]['menuSize14']=$res['menuSize14'];
				$Record[$i]['menuSize15']=$res['menuSize15'];
				$Record[$i]['menuSize16']=$res['menuSize16'];
				$Record[$i]['menuSize17']=$res['menuSize17'];
				$Record[$i]['menuSize18']=$res['menuSize18'];
				$Record[$i]['menuSize19']=$res['menuSize19'];
				$Record[$i]['menuSize20']=$res['menuSize20'];
				$i++;
			}
		return array($Record,$TotalRecordCount);
	}

	function view_items() {
			GLOBAL $con;
			$sql="Select * from items WHERE Deleted=0 AND menuID=".mysqli_real_escape_string($con,$_REQUEST['Menu']);
			if($_REQUEST['Keyword']<>'')
			$sql.=" AND (itemName LIKE '%".mysqli_real_escape_string($con,$_REQUEST['Keyword'])."%' OR itemDescription LIKE '%".mysqli_real_escape_string($con,$_REQUEST['Keyword'])."%')";
			$sql.=" order by itemName ASC";
			$result = mysqli_query($con, $sql);
			$TotalRecordCount=mysqli_num_rows($result);
			$Totalpages 		= mysqli_query($con, "SELECT * FROM `tbl_control`");
			$exec_paging		= mysqli_fetch_array($Totalpages);
			$end_count 			= $exec_paging['no_of_pages'];
			$Limits				= $exec_paging['results_per_page'];
			$Limits = $Limits;
			$Page = $_REQUEST['page'];
			if($Page==""){
				$Page=1;
			}
			$pageCount = ceil($TotalRecordCount/$Limits); 
			$StartLimit = (($Page-1)*$Limits);
			if($TotalRecordCount > ($Page*$Limits)){
				$EndLimit = $Page*$Limits;
			}else{
				$EndLimit = $TotalRecordCount;
			}
			$sql1=" LIMIT ".$StartLimit.",".$Limits;
			$query=$sql.$sql1;
			$Records=array();
			if($Page==1)
				$k=1;
			else
				$k=(($Page-1)*$Limits)+1;
			$Res=mysqli_query($con, $query);
			$Record	=	array();
			$i			=	0;
			while($res=mysqli_fetch_array($Res)) {
				$Record[$i]['S_NO']		=$k;
				$Record[$i]['id']		=$res['id'];
				$Record[$i]['itemName']=$res['itemName'];
				$Record[$i]['itemStatus']=$res['itemStatus'];
				$Record[$i]['itemPrice']=$res['itemPrice'];	
				$Record[$i]['itemPrice1']=$res['itemPrice1'];	
				$Record[$i]['itemPrice2']=$res['itemPrice2'];	
				$Record[$i]['itemPrice3']=$res['itemPrice3'];	
				$Record[$i]['itemPrice4']=$res['itemPrice4'];	
				$Record[$i]['itemPrice5']=$res['itemPrice5'];	
				$Record[$i]['itemPrice6']=$res['itemPrice6'];	
				$Record[$i]['itemPrice7']=$res['itemPrice7'];	
				$Record[$i]['itemPrice8']=$res['itemPrice8'];	
				$Record[$i]['itemPrice9']=$res['itemPrice9'];	
				$Record[$i]['itemPrice10']=$res['itemPrice10'];					
				$i++;
				$k++;
			}
		return array($Record,$TotalRecordCount);
	}
	function view_Allmenus($id) {
			GLOBAL $con;
			$sql="Select * from menus WHERE Deleted=0 AND id=".$id." order by menuName ASC";
			$result = mysqli_query($con, $sql);
			$TotalRecordCount=mysqli_num_rows($result);
			$i=0;
			$Record	=	array();
			while($res=mysqli_fetch_array($result)) {
				$Record[$i]['id']		=$res['id'];
				$Record[$i]['menuName']=$res['menuName'];
				$Record[$i]['menuDescription']=$res['menuDescription'];
				$i++;
			}
		return array($Record,$TotalRecordCount);
	}
	function GetSubgroups($id) {
			GLOBAL $con;
			$sql="Select * from subgroups WHERE merchant_id=".$id." order by subgroup_name ASC";
			$result = mysqli_query($con, $sql);
			$TotalRecordCount=mysqli_num_rows($result);
			$i=0;
			$Record	=	array();
			while($res=mysqli_fetch_array($result)) {
				$Record[$i]['id']		=$res['id'];
				$Record[$i]['subgroup_name']=$res['subgroup_name'];
				$Record[$i]['required']=$res['required'];
				$Record[$i]['multiple']=$res['multiple'];
				$Record[$i]['sort_order']=$res['sort_order'];
				$i++;
			}
		return array($Record,$TotalRecordCount);
	}
	function GetSubItems($id) {
			GLOBAL $con;
			$sql="Select subitems.*,subgroups.subgroup_name,subgroups.id AS GID,subitems.id AS SID from subitems INNER JOIN subgroups ON subgroups.id=subitems.subgroup_id WHERE itemID=".$id." order by subgroup_id,subitemName ASC";
			$result = mysqli_query($con, $sql);
			$TotalRecordCount=mysqli_num_rows($result);
			$i=0;
			$Record	=	array();
			while($res=mysqli_fetch_array($result)) {
				$Record[$i]['id']		=$res['SID'];
				$Record[$i]['subitemName']=$res['subitemName'];
				$Record[$i]['subgroup_id']=$res['subgroup_id'];
				$Record[$i]['subitemPrice']=$res['subitemPrice'];
				$Record[$i]['GID']=$res['GID'];
				$Record[$i]['Sel']=$res['Sel'];
				$Record[$i]['subgroup_name']=$res['subgroup_name'];
				
				$i++;
			}
		return array($Record,$TotalRecordCount);
	}
	function GetCartSubItems($id) {
			GLOBAL $con;
			$sql="Select subgroup_id,subgroup_value from tbl_cartsubitems WHERE Cart_ID=".$id;
			$result = mysqli_query($con, $sql);
			$TotalRecordCount=mysqli_num_rows($result);
			$i=0;
			$Record	=	array();
			while($res=mysqli_fetch_array($result)) {
				$Record[$i]['subgroup_id']		=$res['subgroup_id'];
				$Record[$i]['subgroup_value']		=$res['subgroup_value'];
				$i++;
			}
		return array($Record,$TotalRecordCount);
	}
	function GetSubItemsGroupname($id) {
			GLOBAL $con;
			$sql="Select subitems.*,subgroups.subgroup_name,subgroups.id AS GID,subitems.id AS SID,subgroups.* from subitems INNER JOIN subgroups ON subgroups.id=subitems.subgroup_id WHERE itemID=".$id." GROUP BY subgroup_id order by subgroup_id ASC";
			$result = mysqli_query($con, $sql);
			$TotalRecordCount=mysqli_num_rows($result);
			$i=0;
			$Record	=	array();
			while($res=mysqli_fetch_array($result)) {
				$Record[$i]['id']		=$res['SID'];
                $Record[$i]['item_id']		=$res['itemID'];
				$Record[$i]['subitemName']=$res['subitemName'];
				$Record[$i]['subgroup_id']=$res['subgroup_id'];
				$Record[$i]['subitemPrice']=$res['subitemPrice'];
				$Record[$i]['GID']=$res['GID'];
				$Record[$i]['subgroup_name']=$res['subgroup_name'];
				$Record[$i]['multiple']=$res['multiple'];
				$i++;
			}
		return array($Record,$TotalRecordCount);
	}
	function GetSubItemsGroupnameItems($id,$groupid) {
			GLOBAL $con;
			$sql="Select subitems.*,subgroups.subgroup_name,subgroups.id AS GID,subitems.id AS SID from subitems INNER JOIN subgroups ON subgroups.id=subitems.subgroup_id WHERE itemID=".$id." AND subgroup_id=".$groupid." order by subitemName ASC";
			$result = mysqli_query($con, $sql);
			$TotalRecordCount=mysqli_num_rows($result);
			$i=0;
			$Record	=	array();
			while($res=mysqli_fetch_array($result)) {
				$Record[$i]['id']		=$res['SID'];
				$Record[$i]['subitemName']=$res['subitemName'];
				$Record[$i]['subgroup_id']=$res['subgroup_id'];
				$Record[$i]['subitemPrice']=$res['subitemPrice'];
				$Record[$i]['GID']=$res['GID'];
				$Record[$i]['subgroup_name']=$res['subgroup_name'];
				
				$i++;
			}
		return array($Record,$TotalRecordCount);
	}
	function Getdeliveryfees()
	{
		GLOBAL $con;
		$sql="Select * from delivery_fees WHERE merchantID=".mysqli_real_escape_string($con,$_REQUEST['ID'])." order by postalCode ASC";
		$result = mysqli_query($con, $sql);
		$TotalRecordCount=mysqli_num_rows($result);
		$i=0;
		$Record	=array();
		while($res=mysqli_fetch_array($result)) {
			$Record[$i]['id']=$res['id'];
			$Record[$i]['postalCode']=$res['postalCode'];
			$Record[$i]['fees']=$res['fees'];
			$Record[$i]['minFee']=$res['minFee'];
			$i++;
		}
		return array($Record,$TotalRecordCount);
	}
	function Getdeliveryzipfees()
	{
		GLOBAL $con;
		$sql="Select * from deliveryzipcode_fees WHERE merchantID=".mysqli_real_escape_string($con,$_REQUEST['ID'])." order by postalCode ASC";
		$result = mysqli_query($con, $sql);
		$TotalRecordCount=mysqli_num_rows($result);
		$i=0;
		$Record	=array();
		while($res=mysqli_fetch_array($result)) {
			$Record[$i]['id']=$res['id'];
			$Record[$i]['postalCode']=$res['postalCode'];
			$Record[$i]['fees']=$res['fees'];
			$Record[$i]['minFee']=$res['minFee'];
			$i++;
		}
		return array($Record,$TotalRecordCount);
	}
	function GeGetOpenCloseHours() {
			GLOBAL $con;
			 $sql="Select * from merchant_hours WHERE merchantID=".mysqli_real_escape_string($con,$_REQUEST['ID'])." order by id ASC";
			$result = mysqli_query($con, $sql);
			$TotalRecordCount=mysqli_num_rows($result);
			$i=0;
			$Record	=	array();
			while($res=mysqli_fetch_array($result)) {
				$Record[$i]['id']		=$res['id'];
				$Record[$i]['closed']		=$res['closed'];
				$Record[$i]['deliveryClosed']		=$res['deliveryClosed'];
				$Record[$i]['pickupClosed']		=$res['pickupClosed'];
				$Record[$i]['weekDay']=$res['weekDay'];
				$Record[$i]['startTime']=$res['startTime'];
				if($res['endTime']=='23:59:59')
					$Record[$i]['endTime']="00:00:00";
				else
					$Record[$i]['endTime']=$res['endTime'];
				$Record[$i]['startTimeOther']=$res['startTimeOther'];
				$Record[$i]['endTimeOther']=$res['endTimeOther'];
				$Record[$i]['deliveryStartTimeOther']=$res['deliveryStartTimeOther'];
				$Record[$i]['deliveryEndTimeOther']=$res['deliveryEndTimeOther'];
				$Record[$i]['pickupStartTimeOther']=$res['pickupStartTimeOther'];
				$Record[$i]['pickupEndTimeOther']=$res['pickupEndTimeOther'];
				$Record[$i]['deliveryStartTime']=$res['deliveryStartTime'];
				$Record[$i]['deliveryEndTime']=$res['deliveryEndTime'];
				$Record[$i]['lastDeliveryTime']=$res['lastDeliveryTime'];
				$Record[$i]['pickupStartTime']=$res['pickupStartTime'];
				$Record[$i]['pickupEndTime']=$res['pickupEndTime'];
				
				$i++;
			}
		return array($Record,$TotalRecordCount);
	}
	
	function Add_Menu()
	{
		GLOBAL $con;
		if($_REQUEST['party']=='')
			$party=0;
		else
			$party=$_REQUEST['party'];
		if($_REQUEST['parentmenu']=='')
			$parentmenu=0;
		else
			$parentmenu=$_REQUEST['parentmenu'];
		if($_REQUEST['menuOrder']=='')
			$menuOrder=0;
		else
			$menuOrder=$_REQUEST['menuOrder'];
			
		if($_REQUEST['Menu_ID']>0)
		{
			$sql="UPDATE menus SET menuName='".mysqli_real_escape_string($con,$_REQUEST['menuname'])."',Main_Menu='".mysqli_real_escape_string($con,$_REQUEST['Main_Menu'])."',parentMenu='".mysqli_real_escape_string($con,$parentmenu)."',menuDescription='".mysqli_real_escape_string($con,$_REQUEST['description'])."',Start_Time='".$_REQUEST['ST1']."',End_Time='".$_REQUEST['ET1']."',menuStatus='".$_REQUEST['status']."',menuSize1='".mysqli_real_escape_string($con,$_REQUEST['size1'])."',menuSize2='".mysqli_real_escape_string($con,$_REQUEST['size2'])."',menuSize3='".mysqli_real_escape_string($con,$_REQUEST['size3'])."',menuSize4='".mysqli_real_escape_string($con,$_REQUEST['size4'])."',menuSize5='".mysqli_real_escape_string($con,$_REQUEST['size5'])."',menuSize6='".mysqli_real_escape_string($con,$_REQUEST['size6'])."',menuSize7='".mysqli_real_escape_string($con,$_REQUEST['size7'])."',menuSize8='".mysqli_real_escape_string($con,$_REQUEST['size8'])."',menuSize9='".mysqli_real_escape_string($con,$_REQUEST['size9'])."',
			menuSize10='".mysqli_real_escape_string($con,$_REQUEST['size10'])."',
			menuSize11='".mysqli_real_escape_string($con,$_REQUEST['size11'])."',
			menuSize12='".mysqli_real_escape_string($con,$_REQUEST['size12'])."',
			menuSize13='".mysqli_real_escape_string($con,$_REQUEST['size13'])."',
			menuSize14='".mysqli_real_escape_string($con,$_REQUEST['size14'])."',
			menuSize15='".mysqli_real_escape_string($con,$_REQUEST['size15'])."',
			menuSize16='".mysqli_real_escape_string($con,$_REQUEST['size16'])."',
			menuSize17='".mysqli_real_escape_string($con,$_REQUEST['size17'])."',
			menuSize18='".mysqli_real_escape_string($con,$_REQUEST['size18'])."',
			menuSize19='".mysqli_real_escape_string($con,$_REQUEST['size19'])."',
			menuSize20='".mysqli_real_escape_string($con,$_REQUEST['size20'])."',
			menuLang1='".mysqli_real_escape_string($con,$_REQUEST['lang1'])."',menuLang2='".mysqli_real_escape_string($con,$_REQUEST['lang2'])."',menuLang3='".mysqli_real_escape_string($con,$_REQUEST['lang3'])."',pizza='".$_REQUEST['pizza']."',menuOrder='".$menuOrder."',partyMenu='".$party."' WHERE id=".$_REQUEST['Menu_ID'];
			
		}
		else
		{
			$sql="INSERT INTO menus(merchantID,Main_Menu,parentMenu,menuName,menuDescription,Start_Time,End_Time,menuStatus,menuSize1,menuSize2,menuSize3,menuSize4,menuSize5,menuSize6,menuSize7,menuSize8,menuSize9,menuSize10,menuSize11,menuSize12,menuSize13,menuSize14,menuSize15,menuSize16,menuSize17,menuSize18,menuSize19,menuSize20,createdOn,menuLang1,menuLang2,menuLang3,pizza,partyMenu,menuOrder) VALUES('".$_REQUEST['Res_ID']."','".mysqli_real_escape_string($con,$_REQUEST['Main_Menu'])."','".mysqli_real_escape_string($con,$parentmenu)."','".mysqli_real_escape_string($con,$_REQUEST['menuname'])."','".mysqli_real_escape_string($con,$_REQUEST['description'])."','".$_REQUEST['ST1']."','".$_REQUEST['ET1']."','".$_REQUEST['status']."','".mysqli_real_escape_string($con,$_REQUEST['size1'])."','".mysqli_real_escape_string($con,$_REQUEST['size2'])."','".mysqli_real_escape_string($con,$_REQUEST['size3'])."','".mysqli_real_escape_string($con,$_REQUEST['size4'])."','".mysqli_real_escape_string($con,$_REQUEST['size5'])."','".mysqli_real_escape_string($con,$_REQUEST['size6'])."','".mysqli_real_escape_string($con,$_REQUEST['size7'])."',
			'".mysqli_real_escape_string($con,$_REQUEST['size8'])."',
			'".mysqli_real_escape_string($con,$_REQUEST['size9'])."',
			'".mysqli_real_escape_string($con,$_REQUEST['size10'])."',
			'".mysqli_real_escape_string($con,$_REQUEST['size11'])."',
			'".mysqli_real_escape_string($con,$_REQUEST['size12'])."',
			'".mysqli_real_escape_string($con,$_REQUEST['size13'])."',
			'".mysqli_real_escape_string($con,$_REQUEST['size14'])."',
			'".mysqli_real_escape_string($con,$_REQUEST['size15'])."',
			'".mysqli_real_escape_string($con,$_REQUEST['size16'])."',
			'".mysqli_real_escape_string($con,$_REQUEST['size17'])."',
			'".mysqli_real_escape_string($con,$_REQUEST['size18'])."',
			'".mysqli_real_escape_string($con,$_REQUEST['size19'])."',
			'".mysqli_real_escape_string($con,$_REQUEST['size20'])."',
			Now(),'".mysqli_real_escape_string($con,$_REQUEST['lang1'])."','".mysqli_real_escape_string($con,$_REQUEST['lang2'])."','".mysqli_real_escape_string($con,$_REQUEST['lang3'])."','".$_REQUEST['pizza']."','".$party."','".$menuOrder."')";
			
		}
		
		$rec=mysqli_query($con, $sql); 
		$menunames=$_REQUEST['menunames'];
		//echo $sql;exit;
		for($i=0;$i<COUNT($menunames);$i+=1)
		{
			
			if($menunames[$i]<>'') {
			$sql="INSERT INTO menus(merchantID,Main_Menu,parentMenu,menuName,menuDescription,Start_Time,End_Time,menuStatus,menuSize1,menuSize2,menuSize3,menuSize4,menuSize5,menuSize6,menuSize7,menuSize8,menuSize9,menuSize10,menuSize11,menuSize12,menuSize13,menuSize14,menuSize15,menuSize16,menuSize17,menuSize18,menuSize19,menuSize20,createdOn,menuLang1,menuLang2,menuLang3,pizza,partyMenu) VALUES('".$_REQUEST['Res_ID']."','".mysqli_real_escape_string($con,$_REQUEST['Main_Menu'])."','".mysqli_real_escape_string($con,$parentmenu)."','".mysqli_real_escape_string($con,$menunames[$i])."','".mysqli_real_escape_string($con,$_REQUEST['description'])."','".$_REQUEST['ST1']."','".$_REQUEST['ET1']."','".$_REQUEST['status']."','".mysqli_real_escape_string($con,$_REQUEST['size1'])."','".mysqli_real_escape_string($con,$_REQUEST['size2'])."','".mysqli_real_escape_string($con,$_REQUEST['size3'])."','".mysqli_real_escape_string($con,$_REQUEST['size4'])."','".mysqli_real_escape_string($con,$_REQUEST['size5'])."','".mysqli_real_escape_string($con,$_REQUEST['size6'])."','".mysqli_real_escape_string($con,$_REQUEST['size7'])."','".mysqli_real_escape_string($con,$_REQUEST['size8'])."','".mysqli_real_escape_string($con,$_REQUEST['size9'])."','".mysqli_real_escape_string($con,$_REQUEST['size10'])."',
			'".mysqli_real_escape_string($con,$_REQUEST['size8'])."',
			'".mysqli_real_escape_string($con,$_REQUEST['size9'])."',
			'".mysqli_real_escape_string($con,$_REQUEST['size10'])."',
			'".mysqli_real_escape_string($con,$_REQUEST['size11'])."',
			'".mysqli_real_escape_string($con,$_REQUEST['size12'])."',
			'".mysqli_real_escape_string($con,$_REQUEST['size13'])."',
			'".mysqli_real_escape_string($con,$_REQUEST['size14'])."',
			'".mysqli_real_escape_string($con,$_REQUEST['size15'])."',
			'".mysqli_real_escape_string($con,$_REQUEST['size16'])."',
			'".mysqli_real_escape_string($con,$_REQUEST['size17'])."',
			'".mysqli_real_escape_string($con,$_REQUEST['size18'])."',
			'".mysqli_real_escape_string($con,$_REQUEST['size19'])."',
			'".mysqli_real_escape_string($con,$_REQUEST['size20'])."',
			Now(),'".mysqli_real_escape_string($con,$_REQUEST['lang1'])."','".mysqli_real_escape_string($con,$_REQUEST['lang2'])."','".mysqli_real_escape_string($con,$_REQUEST['lang3'])."','".$_REQUEST['pizza']."','".$party."')";
			
			mysqli_query($con, $sql);
			}
		}
			echo '<script language="javascript">location.href="dine.php?act=my_menus&Res_ID='.$_REQUEST['Res_ID'].'&rep=1";</script>'; 
		exit;
	}
	function Settings_Restaurant()
	{
		GLOBAL $con;
		// image Upload
			if ($_FILES['image_file']['tmp_name']!= "") 
			{
				$validation_type = 2;

			if($validation_type == 1)
			{
				$mime = array('image/gif' => 'gif',
							  'image/jpeg' => 'jpeg',
							  'image/png' => 'png',
							  'application/x-shockwave-flash' => 'swf',
							  'image/psd' => 'psd',
							  'image/bmp' => 'bmp',
							  'image/tiff' => 'tiff',
							  'image/tiff' => 'tiff',
							  'image/jp2' => 'jp2',
							  'image/iff' => 'iff',
							  'image/vnd.wap.wbmp' => 'bmp',
							  'image/xbm' => 'xbm',
							  'image/vnd.microsoft.icon' => 'ico');
			}
			else if($validation_type == 2) // Second choice? Set the extensions
			{
				$image_extensions_allowed = array('jpg', 'jpeg', 'png', 'gif');
			}

			$upload_image_to_folder = '../admin/upload/users/';
			$file = $_FILES['image_file'];

			$file_name = $file['name'];
			$file_name=preg_replace("/[^a-zA-Z0-9.s]/", "", $file_name);

			$error = ''; // Empty

			// Get File Extension (if any)

			$ext = strtolower(substr(strrchr($file_name, "."), 1));

			// Check for a correct extension. The image file hasn't an extension? Add one

			   if($validation_type == 1)
			   {
				 $file_info = getimagesize($_FILES['image_file']['tmp_name']);

				  if(empty($file_info)) // No Image?
				  {
				  $error .= "The uploaded file doesn't seem to be an image.";
				  }
				  else // An Image?
				  {
				  $file_mime = $file_info['mime'];

					 if($ext == 'jpc' || $ext == 'jpx' || $ext == 'jb2')
					 {
					 $extension = $ext;
					 }
					 else
					 {
					 $extension = ($mime[$file_mime] == 'jpeg') ? 'jpg' : $mime[$file_mime];
					 }

					 if(!$extension)
					 {
					 $extension = '';  
					 $file_name = str_replace('.', '', $file_name); 
					 }
				  }
			   }
			   else if($validation_type == 2)
			   {
				  if(!in_array($ext, $image_extensions_allowed))
				  {
				  $exts = implode(', ',$image_extensions_allowed);
				  $error .= "You must upload a file with one of the following extensions: ".$exts;
				  }

				  $extension = $ext;
			   }
				if($error == "") // No errors were found?
			   {
			   $new_file_name = strtolower($file_name);
			   $new_file_name = str_replace(' ', '-', $new_file_name);
			   $new_file_name = substr($new_file_name, 0, -(strlen($ext)+1));
			   $new_file_name .= strtotime(date("F j, Y, g:i a")).".".$extension;
			   
			   // File Name
			   $move_file = move_uploaded_file($file['tmp_name'], $upload_image_to_folder.$new_file_name);

				 if($move_file)
				  {
				  $done = 'The image has been uploaded.';
				  }
			   }
			   else
			   {
			   @unlink($file['tmp_name']);
			   }
		
			   $file_uploaded = true;
		}
		else
		{
			$file_uploaded='';$done='';$error='';
			$new_file_name=$_REQUEST['logo'];
		}
		// Google Icon
		if ($_FILES['google_file']['tmp_name']!= "") 
			{
				$validation_type = 2;

			if($validation_type == 1)
			{
				$mime = array('image/gif' => 'gif',
							  'image/jpeg' => 'jpeg',
							  'image/png' => 'png',
							  'application/x-shockwave-flash' => 'swf',
							  'image/psd' => 'psd',
							  'image/bmp' => 'bmp',
							  'image/tiff' => 'tiff',
							  'image/tiff' => 'tiff',
							  'image/jp2' => 'jp2',
							  'image/iff' => 'iff',
							  'image/vnd.wap.wbmp' => 'bmp',
							  'image/xbm' => 'xbm',
							  'image/vnd.microsoft.icon' => 'ico');
			}
			else if($validation_type == 2) // Second choice? Set the extensions
			{
				$image_extensions_allowed = array('jpg', 'jpeg', 'png', 'gif');
			}

			$upload_image_to_folder = '../admin/upload/users/';
			$file = $_FILES['google_file'];

			$file_name = $file['name'];
			$file_name=preg_replace("/[^a-zA-Z0-9.s]/", "", $file_name);

			$mapiconerror = ''; // Empty

			// Get File Extension (if any)

			$ext = strtolower(substr(strrchr($file_name, "."), 1));

			// Check for a correct extension. The image file hasn't an extension? Add one

			   if($validation_type == 1)
			   {
				 $file_info = getimagesize($_FILES['google_file']['tmp_name']);

				  if(empty($file_info)) // No Image?
				  {
				  $mapiconerror .= "The uploaded file doesn't seem to be an image.";
				  }
				  else // An Image?
				  {
					
				  $file_mime = $file_info['mime'];

					 if($ext == 'jpc' || $ext == 'jpx' || $ext == 'jb2')
					 {
					 $extension = $ext;
					 }
					 else
					 {
					 $extension = ($mime[$file_mime] == 'jpeg') ? 'jpg' : $mime[$file_mime];
					 }

					 if(!$extension)
					 {
					 $extension = '';  
					 $file_name = str_replace('.', '', $file_name); 
					 }
				  }
			   }
			   else if($validation_type == 2)
			   {
				  if(!in_array($ext, $image_extensions_allowed))
				  {
				  $exts = implode(', ',$image_extensions_allowed);
				  $mapiconerror .= "You must upload a file with one of the following extensions: ".$exts;
				  }
					list($width, $height, $type, $attr) = getimagesize($_FILES['google_file']['tmp_name']);
				if($width>16 || $height>33)
				{
					$mapiconerror .= "You must upload a file Max 16px X 33px ";
				}
				
				  $extension = $ext;
			   }
				if($mapiconerror == "") // No errors were found?
			   {
			   $google_icon_file = strtolower($file_name);
			   $google_icon_file = str_replace(' ', '-', $google_icon_file);
			   $google_icon_file = substr($google_icon_file, 0, -(strlen($ext)+1));
			   $google_icon_file .= strtotime(date("F j, Y, g:i a")).".".$extension;
			   
			   // File Name
			   $move_file = move_uploaded_file($file['tmp_name'], $upload_image_to_folder.$google_icon_file);

				 if($move_file)
				  {
				  $done = 'The image has been uploaded.';
				  }
			   }
			   else
			   {
			   @unlink($file['tmp_name']);
			   }
			if($mapiconerror<>"")
				$google_icon_file="";
			   $mapicon_uploaded = true;
		}
		else
		{
			$mapicon_uploaded='';$mapicondone='';$mapiconerror='';
			$google_icon_file=$_REQUEST['google_icon'];
		}
		if($error=='') {
			$sql="UPDATE merchant SET logo='".$new_file_name."' WHERE id=".mysqli_real_escape_string($con,$_REQUEST['ID']);
			mysqli_query($con, $sql);
		}
		if($mapiconerror=='') {
			$sql="UPDATE merchant SET google_icon='".$google_icon_file."' WHERE id=".mysqli_real_escape_string($con,$_REQUEST['ID']);
			mysqli_query($con, $sql);
		}
		if($_REQUEST['showTax']=='')
			$showtax="No";
		else
			$showtax=mysqli_real_escape_string($con,$_REQUEST['showTax']);
		if($_REQUEST['tax']=='')
			$tax=0;
		else
			$tax=mysqli_real_escape_string($con,$_REQUEST['tax']);
		if($_REQUEST['minimumamount']=='')
			$minimumamount=0;
		else
			$minimumamount=mysqli_real_escape_string($con,$_REQUEST['minimumamount']);
		if($_REQUEST['deliveryfee']=='')
			$deliveryfee=0;
		else
			$deliveryfee=mysqli_real_escape_string($con,$_REQUEST['deliveryfee']);
		if($_REQUEST['deliverymiles']=='')
			$deliverymiles=0;
		else
			$deliverymiles=mysqli_real_escape_string($con,$_REQUEST['deliverymiles']);
		if($_REQUEST['deliverymiles']=='')
			$deliverymiles=0;
		else
			$deliverymiles=mysqli_real_escape_string($con,$_REQUEST['deliverymiles']);
		$sql="UPDATE merchant SET minimumDeliveryAmount='".$minimumamount."',deliveryFee='".$deliveryfee."',deliverymiles='".$deliverymiles."',deliveryareas='".mysqli_real_escape_string($con,$_REQUEST['deliveryareas'])."',deliveryzipcodes='".mysqli_real_escape_string($con,$_REQUEST['deliveryzipcodes'])."',delivery_basedon='".mysqli_real_escape_string($con,$_REQUEST['delivery_basedon'])."',deliveryWaitTime='".mysqli_real_escape_string($con,$_REQUEST['deliverywaittime'])."',takeout_time='".mysqli_real_escape_string($con,$_REQUEST['takeout_time'])."',showTax='".$showtax."',tax='".$tax."' WHERE id=".mysqli_real_escape_string($con,$_REQUEST['ID']);
		
		mysqli_query($con, $sql);
		// Sub Groups
		if($_REQUEST['SubGroup_Control']==1)
		{
		$subgroups=$_REQUEST['subgroups'];
		$sbids=$_REQUEST['sbids'];
		$subgroups_required=$_REQUEST['subgroups_required'];
		$subgroups_multiple=$_REQUEST['subgroups_multiple'];
		$subids="";
		for($i=0;$i<COUNT($subgroups);$i+=1)
		{
			if($subgroups[$i]<>'') {
				if($sbids[$i]>0) {
					$sql="UPDATE subgroups SET subgroup_name='".mysqli_real_escape_string($con,$subgroups[$i])."',required=".$subgroups_required[$i].",multiple=".$subgroups_multiple[$i]." WHERE id=".$sbids[$i];
					mysqli_query($con, $sql);
					$subids.=",".$sbids[$i];
				}
				else
				{
					$sql="INSERT INTO subgroups(merchant_id,subgroup_name,required,multiple) VALUES(".mysqli_real_escape_string($con,$_REQUEST['ID']).",'".mysqli_real_escape_string($con,$subgroups[$i])."',".$subgroups_required[$i].",".$subgroups_multiple[$i].")";
					mysqli_query($con, $sql);
					$insid=mysqli_insert_id($con);
					$subids.=",".$insid;
				}
			}
		}
		$subids=trim($subids,",");
		$sql="DELETE FROM subgroups WHERE merchant_id=".mysqli_real_escape_string($con,$_REQUEST['ID'])." AND id NOT IN (".$subids.")";
		mysqli_query($con, $sql);
		}
		
		//End Sub Groups
		
		// Fees based on Miles
		$sql="DELETE FROM delivery_fees WHERE merchantID=".mysqli_real_escape_string($con,$_REQUEST['ID']);
		mysqli_query($con, $sql);
	
		$zonalfees_postcode=$_REQUEST['zonalfees_postcode'];
		
		$zonalfees_charge=$_REQUEST['zonalfees_charge'];
		$zonalfees_minfee=$_REQUEST['zonalfees_minfee'];
		
		for($i=0;$i<COUNT($zonalfees_postcode);$i+=1)
		{
			//".$zonalfees_minfee[$i];
			if($zonalfees_postcode[$i]<>'') {
			$sql="INSERT INTO delivery_fees(merchantID,postalCode,fees,minFee) VALUES(".mysqli_real_escape_string($con,$_REQUEST['ID']).",'".$zonalfees_postcode[$i]."',".$zonalfees_charge[$i].",0)";
			mysqli_query($con, $sql);}
		}
		
		// Fees based on Zipcodes
		$sql="DELETE FROM deliveryzipcode_fees WHERE merchantID=".mysqli_real_escape_string($con,$_REQUEST['ID']);
		mysqli_query($con, $sql);
	
		$zonalfees_zipcode=$_REQUEST['zonalfees_zipcode'];
		$zonalfees_zipcodecharge=$_REQUEST['zonalfees_zipcodecharge'];
		
		for($i=0;$i<COUNT($zonalfees_zipcode);$i+=1)
		{
			if($zonalfees_zipcode[$i]<>'') {
			$sql="INSERT INTO deliveryzipcode_fees(merchantID,postalCode,fees) VALUES(".mysqli_real_escape_string($con,$_REQUEST['ID']).",'".$zonalfees_zipcode[$i]."',".$zonalfees_zipcodecharge[$i].")";
			mysqli_query($con, $sql);}
		}
		
		// Open close time
		$sql="DELETE FROM merchant_hours WHERE merchantID=".mysqli_real_escape_string($con,$_REQUEST['ID']);
		mysqli_query($con, $sql);
	
		$bussinessclose=$_REQUEST['bussinessclose'];
		$start=$_REQUEST['start'];
		$end=$_REQUEST['end'];
		$start1=$_REQUEST['start1'];
		$end1=$_REQUEST['end1'];
		$deliveryclosed=$_REQUEST['deliveryclosed'];
		$deliverystart=$_REQUEST['deliverystart'];
		$deliveryend=$_REQUEST['deliveryend'];
		$deliverystart1=$_REQUEST['deliverystart1'];
		$deliveryend1=$_REQUEST['deliveryend1'];
		$pickupclosed=$_REQUEST['pickupclosed'];
		$pickupstart=$_REQUEST['pickupstart'];
		$pickupend=$_REQUEST['pickupend'];
		$pickupstart1=$_REQUEST['pickupstart1'];
		$pickupend1=$_REQUEST['pickupend1'];
		
		for($i=0;$i<7;$i+=1)
		{
			if($bussinessclose[$i]==1)
				$close="Yes";
			else
				$close="No";
			if($deliveryclosed[$i]==1)
				$deliveryclose="Yes";
			else
				$deliveryclose="No";
				
			if($pickupclosed[$i]==1)
				$pickupclose="Yes";
			else
				$pickupclose="No";
			
			if($start[$i]=='') $startA="00:00:00";
			else $startA=date('H:i:s',strtotime($start[$i]));
			if($end[$i]=='') $endA="00:00:00";
			else if(date('H:i:s',strtotime($start[$i]))!='00:00:00' && date('H:i:s',strtotime($end[$i]))=='00:00:00')
				$endA='23:59:59';
			else $endA=date('H:i:s',strtotime($end[$i]));
			
			if($start1[$i]=='' || $start1[$i]==$end1[$i]) $start1A="00:00:00";
			else $start1A=date('H:i:s',strtotime($start1[$i]));
			if($end1[$i]=='' || $start1[$i]==$end1[$i]) $end1A="00:00:00";
			else $end1A=date('H:i:s',strtotime($end1[$i]));
			
			if($deliverystart[$i]=='') $deliverystartA="00:00:00";
			else $deliverystartA=date('H:i:s',strtotime($deliverystart[$i]));
			if($deliveryend[$i]=='') $deliveryendA="00:00:00";
			else $deliveryendA=date('H:i:s',strtotime($deliveryend[$i]));
			
			if($deliverystart1[$i]=='' || $deliverystart1[$i]==$deliveryend1[$i]) $deliverystart1A="00:00:00";
			else $deliverystart1A=date('H:i:s',strtotime($deliverystart1[$i]));
			if($deliveryend1[$i]=='' || $deliverystart1[$i]==$deliveryend1[$i]) $deliveryend1A="00:00:00";
			else $deliveryend1A=date('H:i:s',strtotime($deliveryend1[$i]));
			
			if($pickupstart[$i]=='') $pickupstartA="00:00:00";
			else $pickupstartA=date('H:i:s',strtotime($pickupstart[$i]));
			if($pickupend[$i]=='') $pickupendA="00:00:00";
			else $pickupendA=date('H:i:s',strtotime($pickupend[$i]));
			
			if($pickupstart1[$i]=='' || $pickupstart1[$i]==$pickupend1[$i]) $pickupstart1A="00:00:00";
			else $pickupstart1A=date('H:i:s',strtotime($pickupstart1[$i]));
			if($pickupend1[$i]=='') $pickupend1A="00:00:00";
			else $pickupend1A=date('H:i:s',strtotime($pickupend1[$i]));
			
			//echo $start[$i];
			$sql="INSERT INTO merchant_hours(merchantID,weekDay,closed,startTime,endTime,startTimeOther,endTimeOther,deliveryClosed,deliveryStartTime,deliveryEndTime,deliveryStartTimeOther,deliveryEndTimeOther,pickupClosed,pickupStartTime,pickupEndTime,pickupStartTimeOther,pickupEndTimeOther,lastUpdatedOn) VALUES(".$_REQUEST['ID'].",'".$i."','".$close."','".$startA."','".$endA."','".$start1A."','".$end1A."','".$deliveryclose."','".$deliverystartA."','".$deliveryendA."','".$deliverystart1A."','".$deliveryend1A."','".$pickupclose."','".$pickupstartA."','".$pickupendA."','".$pickupstart1A."','".$pickupend1A."',Now())";
			
			
			mysqli_query($con, $sql);
			
		}
		
		if($error<>"")
			echo '<script>window.location="dine.php?act=res_settings&err=1&ID='.$_REQUEST['ID'].'";</script>';
		else if($mapiconerror<>"")
			echo '<script>window.location="dine.php?act=res_settings&merr=1&ID='.$_REQUEST['ID'].'";</script>';
		else
			echo '<script>window.location="dine.php?act=my_restaurants&rep=2";</script>';
		exit;
	}
	function Delete_menu()
	{
		GLOBAL $con;
		//$qry="DELETE FROM menus WHERE id=".$_REQUEST['Menu_ID'];
		$qry="UPDATE menus SET Deleted=1,menuStatus='Inactive' WHERE id=".mysqli_real_escape_string($con,$_REQUEST['Menu_ID']);
		
		   mysqli_query($con, $qry);
		  // Delete Menu Items
		  //$sql="DELETE FROM items WHERE menuID=".$_REQUEST['Menu_ID'];
		  $sql="UPDATE items SET Deleted=1,itemStatus='Inactive' WHERE menuID=".mysqli_real_escape_string($con,$_REQUEST['Menu_ID']);
		  mysqli_query($con, $sql);
		   echo '<script>window.location="dine.php?act=my_menus&Res_ID='.$_REQUEST['Res_ID'].'";</script>';
		   exit;
	}
	function Delete_menuitem()
	{
			GLOBAL $con;
			// $sql="DELETE FROM items WHERE id=".$_REQUEST['Item_ID'];
			  $sql="UPDATE items SET Deleted=1,itemStatus='Inactive' WHERE id=".mysqli_real_escape_string($con,$_REQUEST['Item_ID']);
			mysqli_query($con, $sql);
		   echo '<script language="javascript">location.href="dine.php?act=menu_item&Res_ID='.$_REQUEST['Res_ID'].'&Menu='.$_REQUEST['Menu'].'";</script>'; 
		   exit;
	}
	function Delete_ItemImage()
	{
		GLOBAL $con;
		$sql="UPDATE items SET itemImage='' WHERE id=".mysqli_real_escape_string($con,$_REQUEST['Item_ID']);
		mysqli_query($con, $sql);
		unlink("../admin/upload/users/".$_REQUEST['img']);
		echo '<script language="javascript">location.href="dine.php?act=add_item&Res_ID='.$_REQUEST['Res_ID'].'&Menu='.$_REQUEST['Menu'].'&Item_ID='.$_REQUEST['Item_ID'].'";</script>'; 
		exit;
	}
	function Delete_Restaurant()
	{
			GLOBAL $con;
		   //$qry="DELETE FROM merchant WHERE id=".$_REQUEST['ID'];
		   $qry="UPDATE merchant SET Deleted=1,status='Inactive' WHERE id=".mysqli_real_escape_string($con,$_REQUEST['ID']);
		   mysqli_query($con, $qry);
		   echo '<script>window.location="dine.php?act=my_restaurants";</script>';
		   exit;
	}
	function BookATable()
	{
		GLOBAL $con;
		$id=mysqli_real_escape_string($con,$_REQUEST["id"]);
		/*foreach($_POST['layouts']  as  $value)  {
		$layouts .= "$value,";
		}
		
		$layouts=rtrim($layouts,",");*/
		
		$book_date=date("Y-m-d",strtotime($_REQUEST["book_date"]));
		$book_fulldate=date("Y-m-d G:i:s",strtotime($_REQUEST["book_date"]." ".$_REQUEST["book_time"]));
		
		$meal=mysqli_real_escape_string($con,$_REQUEST["meal"]);
		$Start_Time=date("G:i:s",strtotime($_REQUEST["book_time"]));
		// calculate End Time
		$End_Time=date("G:i:s",$_REQUEST["book_totime"]);
		$Look_Ahead=mysqli_real_escape_string($con,$_REQUEST["Look_Ahead"]);
		$Size=mysqli_real_escape_string($con,$_REQUEST["size"]);
		$Book_Name=mysqli_real_escape_string($con,$_REQUEST["Diner_Name"]);
		if($Book_Name=='First Name')
			$Book_Name="";
			
		$Book_LName=mysqli_real_escape_string($con,$_REQUEST["Diner_LName"]);
		if($Book_LName=='Last Name')
			$Book_LName="";
		if($Book_LName<>'')
			$Book_Name.=" ".$Book_LName;
		$Book_Email=mysqli_real_escape_string($con,$_REQUEST["Diner_Email"]);
		if($Book_Email=='For Reservation Confirmation')
			$Book_Email="";
		$Book_Phone=mysqli_real_escape_string($con,$_REQUEST["Diner_Phone"]);
		if($Book_Phone=='555-555-5555')
			$Book_Phone="";
		$Book_Phone_Contact=mysqli_real_escape_string($con,$_REQUEST["contact_principle"]);
		$Book_FirstTime=mysqli_real_escape_string($con,$_REQUEST["first_time"]);
		$Book_Notes=mysqli_real_escape_string($con,$_REQUEST["Special_Request"]);
		if($Book_Notes=='Please note that not all requests can be accommodated')
			$Book_Notes="";
		$special_events=$_REQUEST["special_events"];
		
		$User_ID=$_SESSION['User_ID'];
		if($_SESSION['User_ID']=='')
		{
			// Create an New User
			// get random values
			$query="SELECT email_add FROM tbl_registeration WHERE email_add='".$Book_Email."'";
			$qexe=mysqli_query($con, $query);
			$qnum=mysqli_num_rows($qexe);
			if($qnum>0)
			{
				$book_date1=date("m/d/Y",$_REQUEST["book_date"]);
				$book_time1=date("g:i a",$_REQUEST["book_time"]);
				echo "<script language='javascript'>location.href='table_confirm.php?id=".$_REQUEST['id']."&err=email&book_date=".$book_date1."&meal=".$_REQUEST['meal']."&size=".$_REQUEST['size']."&book_time=".$book_time1."&book_totime=".$_REQUEST['book_totime']."&Diner_Name=".$_REQUEST['Diner_Name']."&Diner_Phone=".$_REQUEST['Diner_Phone']."&Diner_Email=".$_REQUEST['Diner_Email']."&lastname=".$Book_LName."&Special_Request=".$Book_Notes."';</script>";
				exit;
			}
			$length = 30;
			$characters = "0123456789abcdefghijklmnopqrstuvwxyz";
			$string = "";    
			for ($p = 0; $p < $length; $p++) {
				$string .= $characters[mt_rand(0, strlen($characters))];
			}
			$checksum_register=$string;
			$password=get_rand_letters('6');
			$sql ="insert into tbl_registeration(email_add,password,firstname,display_name,mobile_phone,contact_principle,checksum_register,status,cdate) VALUES('".$Book_Email."','".$password."','".$_REQUEST['Diner_Name']."','".$_REQUEST['Diner_Name']."','".$_REQUEST['Diner_Phone']."','Mobile','".$checksum_register."',0,now())";
			
			$rec=mysqli_query($con, $sql);
			$id=mysqli_insert_id($con);
			$User_ID=$id;
			// Send Registeration details to email
			GLOBAL $adminmail,$Host_Path,$contactmail;
			$Subject="Welcome to Way.com, ".$_REQUEST['Diner_Name']."! ";
			$message='<table width="564" border="0" align="center" cellpadding="0" cellspacing="0" >
				  <tr>
					<td  valign="top" scope="col"><table width="100%" border="0" cellspacing="0" cellpadding="0">
					 <tr>
						<td height="5" scope="col">&nbsp;</td>
					  </tr>
					  <tr>
						<td scope="col" align="left"><font style="font-family:Arial, Helvetica, sans-serif; font-size:16px; font-weight:bold; color:#6a6a6a;">Thank You For Registering And Welcome To Way.com!</font></td>
					  </tr>
					  <tr>
						<td height="5" scope="col">&nbsp;</td>
					  </tr>
					  <tr>
						<td valign="top" scope="col"><table width="98%" border="0" align="center" cellpadding="0" cellspacing="0">
						<tr>
							<td height="26" scope="col"><div align="left"><font style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; color:#6a6a6a;">Hello</font><font style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; color:#be3917;">&nbsp;&nbsp;'.$_REQUEST['Diner_Name'].'</font>
							</div></td>
						  </tr>
						  <tr><td height="10" scope="col"></td></tr>
						 <tr>
							<td height="26" scope="col"><div align="left"><font style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; color:#6a6a6a;">Your Password:</font><font style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; color:#be3917;">&nbsp;&nbsp;'.$password.'</font>
							</div></td>
						  </tr>
						   <tr>
							<td height="19" scope="col" align="center">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; color:#6a6a6a;"><a href="'.$Host_Path.'confirm.php?id='.$User_ID.'&checksum_register='.$checksum_register.'" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; color:#0b9fd8;">Click here to activate account</font></td>
						  </tr>

						  <tr><td height="20" scope="col"></td></tr>
						  <tr>
							<td height="19" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#6a6a6a;">Connect with Way.com! Follow Way.com on Twitter, Facebook and the Way.com Blog.</font></td>
						  </tr>
						  <tr><td height="10" scope="col"></td></tr>
						  <tr>
							<td height="19" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#6a6a6a;"><b>Not you?</b></font></td>
						  </tr>
						  <tr><td height="10" scope="col"></td></tr>
						  <tr>
							<td height="19" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#6a6a6a;text-decoration:none">If you did not recently update your email address on <a href="'.$Host_Path.'">Way.com</a>, please let us know by forwarding this email to mail@way.com. </font></td>
						  </tr>
						  <tr><td height="20" scope="col"></td></tr>
						  <tr>
							<td height="19" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#6a6a6a;">This is an account-related message. Responses to this email will not go to a customer service representative.</font></td>
						  </tr>
						  
						  <tr>
							<td height="19" scope="col">&nbsp;<font style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#6a6a6a;">To contact our Customer Care team directly, please visit the help section of our website.</font></td>
						  </tr>
						 
						   <tr>
						<td height="19" scope="col">&nbsp;</td>
					  </tr>
						</table>
								 </td>
					  </tr>
					  <tr><td height="2" scope="col"></td></tr>
					</table></td>
				  </tr>
				</table>';
				$headers = "From: ".$adminmail."\r\n" .
						   'Reply-To: '.$adminmail."\r\n" .
						   'X-Mailer: PHP/' . phpversion();
				$headers .= 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
				include ("../template/email-template.php"); 
			$template1=str_replace('DETAILS',$message."",$template);
			$message=$template1;
			
			mail($Book_Email, $Subject, $message, $headers);
		}
		$rand=genRandomString();
		$code=$rand;
		$sql="INSERT INTO tbl_tablebooking_bookings(Book_Owner,Book_UserID,Book_Restaurant,Book_date,Book_datetime,Book_Meal,Book_Start_Time,Book_End_Time,Book_Size,Book_Name,Book_Email,Book_Phone,Book_Contact,Book_FirstTime,special_events,Book_Notes,code,Book_Created) 
		VALUES (".$_REQUEST['Res_UserID'].",'".$User_ID."',".$id.",'".$book_date."','".$book_fulldate."','".$meal."','".$Start_Time."','".$End_Time."','".$Size."','".$Book_Name."','".$Book_Email."','".$Book_Phone."','".$Book_Phone_Contact."','".$Book_FirstTime."','".$special_events."','".$Book_Notes."','".$code."',now())";
		
		mysqli_query($con, $sql);
		$Booking_ID=mysqli_insert_id($con);
		GLOBAL $adminmail,$Host_Path,$contactmail;
		
		$sql="SELECT email,contactName,merchantName,contactAddress,state,city,postalCode,telephone,faxNumber FROM merchant WHERE id=".$_REQUEST['id'];
		$exe=mysqli_query($con, $sql);
		$res=mysqli_fetch_array($exe);
		$Seller_Email =$res['email'];
		$faxNumber=$res['faxNumber'];
		
		// Send Confirmation mail to seller
			$Subject="An Table was booked through Way.com";
			$message='<table width="564" border="0" align="center" cellpadding="0" cellspacing="0" >
				  <tr>
					<td  valign="top" scope="col"><table width="100%" border="0" cellspacing="0" cellpadding="0">
					 <tr>
						<td height="5" scope="col"></td>
					  </tr>
					   <tr>
						<td scope="col" width="30%" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; color:#4e4e4e; line-height:18px;" colspan="2">Hi '.$res['contactName'].',</td>
					  </tr>
					  <tr>
						<td height="5" scope="col"></td>
					  </tr>
					   <tr>
						<td scope="col" width="30%" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; color:#4e4e4e; line-height:18px;" colspan="2">An table was Booked on '.$res['merchantName'].'. See details below:</td>
					  </tr>
					  <tr>
						<td height="5" scope="col">&nbsp;</td>
					  </tr>
					  <tr>
						<td scope="col"  width="30%" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;">Restaurant Name</td><td style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;" >: '.$res['merchantName'].'</td>
					  </tr>
					  <tr>
						<td scope="col" valign="top" width="30%" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;">Restaurant Address</td><td style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;" >: &nbsp;'.$res['contactAddress'].'<br>&nbsp;&nbsp;'.$res['city'].', '.$res['state'].'&nbsp;'.$res['postalCode'].'</td>
					  </tr>';
					  if($res['telephone']<>'')
					   $message.='<tr>
						<td scope="col" width="30%" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;">Restaurant Phone</td><td style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;" >: &nbsp;'.$res['telephone'].'</td>
					  </tr>';
					  $message.='<tr>
						<td scope="col" width="30%" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;">Booking ID</td><td style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;">: TB'.$Booking_ID.'</td>
					  </tr>
					  <tr>
						<td scope="col" width="30%" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;">Dinner Name</td><td style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;">: '.$Book_Name.'</td>
					  </tr>
					  <tr>
						<td scope="col" width="30%" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;">Dinner Phone</td><td style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;">: '.$Book_Phone.'</td>
					  </tr>
					  <tr>
						<td scope="col" width="30%" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;">Dinner Email</td><td style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;">: '.$Book_Email.'</td>
					  </tr>';
					  if($Book_Notes<>'')
					  $message.='<tr>
						<td scope="col" width="30%" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;" >Special Request: </td><td style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;" >: '.$Book_Notes.'</td>
					  </tr>';
					  
					  $message.='<tr>
						<td scope="col" width="30%" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;" >Booked On </td><td style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;" >: '.$book_date.'</td>
					  </tr>
					   <tr>
						<td scope="col" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;" >Start Time </td><td style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;" >: '.date("g:i A",strtotime($Start_Time)).'</td>
					  </tr>
					   <tr>
						<td scope="col" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;" >End Time </td><td style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;" >: '.date("g:i A",strtotime($End_Time)).'</td>
					  </tr>
					   <tr>
						<td scope="col" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;">Meal </td><td style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;" >: '.$meal.'</td>
					  </tr>
					   <tr>
						<td scope="col" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;" >Size </td><td style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;" >: '.$Size.'</td>
					  </tr>
					  
					  <tr>
						<td height="5" scope="col">&nbsp;</td>
					  </tr>
					  <tr>
						<td height="5" colspan="2" scope="col" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; color:#4e4e4e; line-height:18px;" >Please check your way.com Account for more details.</td>
					  </tr>
					  </table>
					 </td></tr>
					</table>';
			$faxmessage=$message;
			$headers = "From: ".$adminmail."\r\n" .
						   'Reply-To: '.$adminmail."\r\n" .
						   'X-Mailer: PHP/' . phpversion();
				$headers .= 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
				include ("../template/email-template.php"); 
			$template1=str_replace('DETAILS',$message."",$template);
			$message=$template1;
			
			mail($Seller_Email, $Subject, $message, $headers);
			// Fax using EMAIL with Provider Ringcentral
		$my_file = 'fax/D-8885194198-'.$Booking_ID.'.html';
		$my_file1 = 'D-8885194198-'.$Booking_ID.'.html';
		$handle = fopen($my_file, 'w');
		fwrite($handle, $faxmessage);
			
		$my_path = $_SERVER['DOCUMENT_ROOT']."/demo/dine/fax/";
		$my_name = "way";
		$my_mail = "wayfax@way.com";
		$my_replyto = "wayfax@way.com";
		$my_subject = "Way FAX";
		$my_message = "FAX";
		
		if($faxNumber=='')
			$faxNumber1='888-781-8138';
		else
			$faxNumber1=$faxNumber;
		if($faxNumber=='')
			$faxNumber='8887818138';
		
		$faxNumber=str_replace("-","",$faxNumber);
		$faxNumber=str_replace("(","",$faxNumber);
		$faxNumber=str_replace(")","",$faxNumber);
		$faxNumber=str_replace(" ","",$faxNumber);
		//$faxNumber='8887818138';
		$status=mail_attachmentnew($my_file1, $my_path, $faxNumber."@rcfax.com", $my_mail, $my_name, $my_replyto, $my_subject, $my_message);
		
		// Store in DB
		$sql="INSERT INTO tbl_fax(Fax_From,Fax_To,Fax_Restaurant,Fax_User,Fax_Content,Fax_Status,Fax_Created) VALUES('888-781-8138','".$faxNumber1."','".$id."',".$User_ID.",'".$my_file1."',".$status.",Now())";
		
		mysqli_query($con, $sql);
			// Send Confirmation mail to Buyer
			
				//$Customer_email=fetch_customers_email($_SESSION['User_ID']);
				//$Customer_Name=fetch_customers_name($_SESSION['User_ID']);
				
				$headers = "From: ".$adminmail."\r\n" .
						   'Reply-To: '.$adminmail."\r\n" .
						   'X-Mailer: PHP/' . phpversion();
				$headers .= 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
				$Subject="Your Table was booked through Way.com";
				$message='<table width="564" border="0" align="center" cellpadding="0" cellspacing="0" >
				  <tr>
					<td  valign="top" scope="col"><table width="100%" border="0" cellspacing="0" cellpadding="0">
					 <tr>
						<td height="5" scope="col"></td>
					  </tr>
					   <tr>
						<td scope="col" width="30%" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; color:#4e4e4e; line-height:18px;" colspan="2">Hi '.$Book_Name.',</td>
					  </tr>
					  <tr>
						<td height="5" scope="col"></td>
					  </tr>
					   <tr>
						<td scope="col" width="30%" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; color:#4e4e4e; line-height:18px;" colspan="2">An table was Booked on '.$res['merchantName'].'. See details below:</td>
					  </tr>
					  <tr>
						<td height="5" scope="col">&nbsp;</td>
					  </tr>
					  <tr>
						<td scope="col" width="30%" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;">Restaurant Name</td><td style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;" >: '.$res['merchantName'].'</td>
					  </tr>
					  <tr>
						<td scope="col" valign="top" width="30%" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;">Restaurant Address</td><td style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;" >: '.$res['contactAddress'].'<br>&nbsp;&nbsp;'.$res['city'].', '.$res['state'].'&nbsp;'.$res['postalCode'].'</td>
					  </tr>';
					  if($res['telephone']<>'')
					   $message.='<tr>
						<td scope="col" width="30%" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;">Restaurant Phone</td><td style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;" >: &nbsp;'.$res['telephone'].'</td>
					  </tr>';
					  $message.='<tr>
						<td scope="col" width="30%" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;">Booking ID</td><td style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;">: TB'.$Booking_ID.'</td>
					  </tr>
					  <tr>
						<td scope="col" width="30%" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;">Dinner Name</td><td style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;">: '.$Book_Name.'</td>
					  </tr>
					  <tr>
						<td scope="col" width="30%" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;">Dinner Phone</td><td style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;">: '.$Book_Phone.'</td>
					  </tr>
					  <tr>
						<td scope="col" width="30%" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;">Dinner Email</td><td style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;">: '.$Book_Email.'</td>
					  </tr>';
					  if($Book_Notes<>'')
					  $message.='<tr>
						<td scope="col" width="30%" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;" >Special Request: </td><td style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;" >: '.$Book_Notes.'</td>
					  </tr>';
					  
					  $message.='<tr>
						<td scope="col" width="30%" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;" >Booked On </td><td style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;" >: '.$book_date.'</td>
					  </tr>
					   <tr>
						<td scope="col" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;" >Start Time </td><td style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;" >: '.date("g:i A",strtotime($Start_Time)).'</td>
					  </tr>
					   <tr>
						<td scope="col" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;" >End Time </td><td style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;" >: '.date("g:i A",strtotime($End_Time)).'</td>
					  </tr>
					   <tr>
						<td scope="col" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;">Meal </td><td style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;" >: '.$meal.'</td>
					  </tr>
					   <tr>
						<td scope="col" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;" >Size </td><td style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;" >: '.$Size.'</td>
					  </tr>
					  
					  <tr>
						<td height="5" scope="col">&nbsp;</td>
					  </tr>
					  <tr>
						<td height="5" colspan="2" scope="col" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; color:#4e4e4e; line-height:18px;" >Please check your way.com Account for more details.</td>
					  </tr>
					  </table>
					 </td></tr>
					</table>';
					
				include ("../template/email-template.php"); 
				$template1=str_replace('DETAILS',$message."",$template);
				$message=$template1;
				
				mail($Book_Email, $Subject, $message, $headers);
				
	}
	function view_Reservations() {
			GLOBAL $con;
			$sql="Select tbl_tablebooking_bookings.*,tbl_registeration.* from tbl_tablebooking_bookings INNER JOIN tbl_registeration ON tbl_registeration.id=tbl_tablebooking_bookings.Book_UserID WHERE Book_Restaurant=".$_REQUEST['Res_ID']." order by Book_Created desc";
			$result = mysqli_query($con, $sql);
			$TotalRecordCount=mysqli_num_rows($result);
			$Totalpages 		= mysqli_query($con, "SELECT * FROM `tbl_control`");
			$exec_paging		= mysqli_fetch_array($Totalpages);
			$end_count 			= $exec_paging['no_of_pages'];
			$Limits				= $exec_paging['results_per_page'];
			$Limits = $Limits;
			$Page = $_REQUEST['page'];
			if($Page==""){
				$Page=1;
			}
			$pageCount = ceil($TotalRecordCount/$Limits); 
			$StartLimit = (($Page-1)*$Limits);
			if($TotalRecordCount > ($Page*$Limits)){
				$EndLimit = $Page*$Limits;
			}else{
				$EndLimit = $TotalRecordCount;
			}
			$sql1=" LIMIT ".$StartLimit.",".$Limits;
			$query=$sql.$sql1;
			$Records=array();
			$i=1;
			$Res=mysqli_query($con, $query);
			$Record	=	array();
			$i			=	0;
			while($res=mysqli_fetch_array($Res)) {
				$Record[$i]['Book_ID']		=$res['Book_ID'];
				$Record[$i]['Book_Restaurant']=$res['Book_Restaurant'];
				$Record[$i]['Book_Table']=$res['Book_Table'];
				
				$Record[$i]['Book_date']=$res['Book_date'];
				$Record[$i]['Book_Meal']		=$res['Book_Meal'];
				$Record[$i]['Book_Start_Time']	=$res['Book_Start_Time'];
				$Record[$i]['Book_Ahead']	=$res['Book_Ahead'];
				$Record[$i]['Book_Size']	=$res['Book_Size'];
				$Record[$i]['Book_Created']	=$res['Book_Created'];
				$Record[$i]['firstname']			=	$res['firstname'];
				$Record[$i]['lastname']			=	$res['lastname'];
				$Record[$i]['displayname']		=	$res['displayname'];
				$i++;
			}
		return array($Record,$TotalRecordCount);
	}
	function GetTableNames($Book_Table)
	{
		GLOBAL $con;
		$Book_Tables=explode(",",$Book_Table);
		foreach($Book_Tables as $p=>$q)
		{
			$exe 		= mysqli_query($con, "SELECT * FROM `tbl_tablebookinglayouts` WHERE TB_LID=".$q);
			$res		= mysqli_fetch_array($exe);
			echo "<br>".$res['TB_LName'];
		}
	}
	function EditSettings()
	{
		GLOBAL $con;
		$TBS_Restaurant=mysqli_real_escape_string($con,$_REQUEST["TBS_Restaurant"]);
		$TBS_Demo=mysqli_real_escape_string($con,$_REQUEST["TBS_Demo"]);
		$TBS_Seats=mysqli_real_escape_string($con,$_REQUEST["TBS_Seats"]);
		
		$TBS_Breakfast_Start=mysqli_real_escape_string($con,$_REQUEST["TBS_Breakfast_Start"]);
		$TBS_Breakfast_End=mysqli_real_escape_string($con,$_REQUEST["TBS_Breakfast_End"]);
		$TBS_Breakfast=mysqli_real_escape_string($con,$_REQUEST["TBS_Breakfast"]);
		$TBS_Lunch_Start=mysqli_real_escape_string($con,$_REQUEST["TBS_Lunch_Start"]);
		$TBS_Lunch_End=mysqli_real_escape_string($con,$_REQUEST["TBS_Lunch_End"]);
		$TBS_Lunch=mysqli_real_escape_string($con,$_REQUEST["TBS_Lunch"]);
		$TBS_Dinner_Start=mysqli_real_escape_string($con,$_REQUEST["TBS_Dinner_Start"]);
		$TBS_Dinner_End=mysqli_real_escape_string($con,$_REQUEST["TBS_Dinner_End"]);
		$TBS_Dinner=mysqli_real_escape_string($con,$_REQUEST["TBS_Dinner"]);
		$TBS_Before_Breakfast=mysqli_real_escape_string($con,$_REQUEST["TBS_Before_Breakfast"]);
		$TBS_Before_Lunch=mysqli_real_escape_string($con,$_REQUEST["TBS_Before_Lunch"]);
		$TBS_Before_Dinner=mysqli_real_escape_string($con,$_REQUEST["TBS_Before_Dinner"]);
		$TBS_Reserve_Buffer=mysqli_real_escape_string($con,$_REQUEST["TBS_Reserve_Buffer"]);
		$TBS_Desc=mysqli_real_escape_string($con,$_REQUEST["TBS_Desc"]);
		$TBS_Breakfast_Monday_Closed=0;
		$TBS_Breakfast_Tuesday_Closed=0;
		$TBS_Breakfast_Wednesday_Closed=0;
		$TBS_Breakfast_Thursday_Closed=0;
		$TBS_Breakfast_Friday_Closed=0;
		$TBS_Breakfast_Saturday_Closed=0;
		$TBS_Breakfast_Sunday_Closed=0;
		$TBS_Breakfast_Monday_Start=$_REQUEST['TBS_Breakfast_Monday_Start'];
		$TBS_Breakfast_Monday_End=$_REQUEST['TBS_Breakfast_Monday_End'];
		$TBS_Breakfast_Tuesday_Start=$_REQUEST['TBS_Breakfast_Tuesday_Start'];
		$TBS_Breakfast_Tuesday_End=$_REQUEST['TBS_Breakfast_Tuesday_End'];
		$TBS_Breakfast_Wednesday_Start=$_REQUEST['TBS_Breakfast_Wednesday_Start'];
		$TBS_Breakfast_Wednesday_End=$_REQUEST['TBS_Breakfast_Wednesday_End'];
		$TBS_Breakfast_Thursday_Start=$_REQUEST['TBS_Breakfast_Thursday_Start'];
		$TBS_Breakfast_Thursday_End=$_REQUEST['TBS_Breakfast_Thursday_End'];
		$TBS_Breakfast_Friday_Start=$_REQUEST['TBS_Breakfast_Friday_Start'];
		$TBS_Breakfast_Friday_End=$_REQUEST['TBS_Breakfast_Friday_End'];
		$TBS_Breakfast_Saturday_Start=$_REQUEST['TBS_Breakfast_Saturday_Start'];
		$TBS_Breakfast_Saturday_End=$_REQUEST['TBS_Breakfast_Saturday_End'];
		$TBS_Breakfast_Sunday_Start=$_REQUEST['TBS_Breakfast_Sunday_Start'];
		$TBS_Breakfast_Sunday_End=$_REQUEST['TBS_Breakfast_Sunday_End'];
				
		if($_REQUEST['TBS_Breakfast_Monday_Start']=='Closed' || $_REQUEST['TBS_Breakfast_Monday_End']=='Closed') {
				$TBS_Breakfast_Monday_Start="00:00:00";
				$TBS_Breakfast_Monday_End="00:00:00";
				$TBS_Breakfast_Monday_Closed=1;
		}
		if($_REQUEST['TBS_Breakfast_Tuesday_Start']=='Closed' || $_REQUEST['TBS_Breakfast_Tuesday_End']=='Closed') {
				$TBS_Breakfast_Tuesday_Start="00:00:00";
				$TBS_Breakfast_Tuesday_End="00:00:00";
				$TBS_Breakfast_Tuesday_Closed=1;
		}
		if($_REQUEST['TBS_Breakfast_Wednesday_Start']=='Closed' || $_REQUEST['TBS_Breakfast_Wednesday_End']=='Closed') {
				$TBS_Breakfast_Wednesday_Start="00:00:00";
				$TBS_Breakfast_Wednesday_End="00:00:00";
				$TBS_Breakfast_Wednesday_Closed=1;
		}
		if($_REQUEST['TBS_Breakfast_Thursday_Start']=='Closed' || $_REQUEST['TBS_Breakfast_Thursday_End']=='Closed') {
				$TBS_Breakfast_Thursday_Start="00:00:00";
				$TBS_Breakfast_Thursday_End="00:00:00";
				$TBS_Breakfast_Thursday_Closed=1;
		}
		if($_REQUEST['TBS_Breakfast_Friday_Start']=='Closed' || $_REQUEST['TBS_Breakfast_Friday_End']=='Closed') {
				$TBS_Breakfast_Friday_Start="00:00:00";
				$TBS_Breakfast_Friday_End="00:00:00";
				$TBS_Breakfast_Friday_Closed=1;
		}
		if($_REQUEST['TBS_Breakfast_Saturday_Start']=='Closed' || $_REQUEST['TBS_Breakfast_Saturday_End']=='Closed') {
				$TBS_Breakfast_Saturday_Start="00:00:00";
				$TBS_Breakfast_Saturday_End="00:00:00";
				$TBS_Breakfast_Saturday_Closed=1;
		}
		if($_REQUEST['TBS_Breakfast_Sunday_Start']=='Closed' || $_REQUEST['TBS_Breakfast_Sunday_End']=='Closed') {
				$TBS_Breakfast_Sunday_Start="00:00:00";
				$TBS_Breakfast_Sunday_End="00:00:00";
				$TBS_Breakfast_Sunday_Closed=1;
		}
		
		
		$sql="SELECT TBS_ID FROM tbl_tablebooking_settings WHERE merchant=".$_REQUEST['Res_ID'];
		$exe=mysqli_query($con, $sql);
		$num=mysqli_num_rows($exe);
		if($num>0)
		{
			//$sql="UPDATE tbl_tablebooking_settings SET TBS_Seats='".$_REQUEST['TBS_Seats']."',TBS_Breakfast_Monday_Start='".$_REQUEST['TBS_Breakfast_Monday_Start']."',TBS_Breakfast_Monday_End='".$_REQUEST['TBS_Breakfast_Monday_End']."',TBS_Breakfast_Tuesday_Start='".$_REQUEST['TBS_Breakfast_Tuesday_Start']."',TBS_Breakfast_Tuesday_End='".$_REQUEST['TBS_Breakfast_Tuesday_End']."',TBS_Breakfast_Wednesday_Start='".$_REQUEST['TBS_Breakfast_Wednesday_Start']."',TBS_Breakfast_Wednesday_End='".$_REQUEST['TBS_Breakfast_Wednesday_End']."',TBS_Breakfast_Thursday_Start='".$_REQUEST['TBS_Breakfast_Thursday_Start']."',TBS_Breakfast_Thursday_End='".$_REQUEST['TBS_Breakfast_Thursday_End']."',TBS_Breakfast_Friday_Start='".$_REQUEST['TBS_Breakfast_Friday_Start']."',TBS_Breakfast_Friday_End='".$_REQUEST['TBS_Breakfast_Friday_End']."',TBS_Breakfast_Saturday_Start='".$_REQUEST['TBS_Breakfast_Saturday_Start']."',TBS_Breakfast_Saturday_End='".$_REQUEST['TBS_Breakfast_Saturday_End']."',TBS_Breakfast_Sunday_Start='".$_REQUEST['TBS_Breakfast_Sunday_Start']."',TBS_Breakfast_Sunday_End='".$_REQUEST['TBS_Breakfast_Sunday_End']."',TBS_Breakfast='".$TBS_Breakfast."',TBS_Lunch_Monday_Start='".$_REQUEST['TBS_Lunch_Monday_Start']."',TBS_Lunch_Monday_End='".$_REQUEST['TBS_Lunch_Monday_End']."',TBS_Lunch_Tuesday_Start='".$_REQUEST['TBS_Lunch_Tuesday_Start']."',TBS_Lunch_Tuesday_End='".$_REQUEST['TBS_Lunch_Tuesday_End']."',TBS_Lunch_Wednesday_Start='".$_REQUEST['TBS_Lunch_Wednesday_Start']."',TBS_Lunch_Wednesday_End='".$_REQUEST['TBS_Lunch_Wednesday_End']."',TBS_Lunch_Thursday_Start='".$_REQUEST['TBS_Lunch_Thursday_Start']."',TBS_Lunch_Thursday_End='".$_REQUEST['TBS_Lunch_Thursday_End']."',TBS_Lunch_Friday_Start='".$_REQUEST['TBS_Lunch_Friday_Start']."',TBS_Lunch_Friday_End='".$_REQUEST['TBS_Lunch_Friday_End']."',TBS_Lunch_Saturday_Start='".$_REQUEST['TBS_Lunch_Saturday_Start']."',TBS_Lunch_Saturday_End='".$_REQUEST['TBS_Lunch_Saturday_End']."',TBS_Lunch_Sunday_Start='".$_REQUEST['TBS_Lunch_Sunday_Start']."',TBS_Lunch_Sunday_End='".$_REQUEST['TBS_Lunch_Sunday_End']."',TBS_Lunch='".$TBS_Lunch."',TBS_Dinner_Monday_Start='".$_REQUEST['TBS_Dinner_Monday_Start']."',TBS_Dinner_Monday_End='".$_REQUEST['TBS_Dinner_Monday_End']."',TBS_Dinner_Tuesday_Start='".$_REQUEST['TBS_Dinner_Tuesday_Start']."',TBS_Dinner_Tuesday_End='".$_REQUEST['TBS_Dinner_Tuesday_End']."',TBS_Dinner_Wednesday_Start='".$_REQUEST['TBS_Dinner_Wednesday_Start']."',TBS_Dinner_Wednesday_End='".$_REQUEST['TBS_Dinner_Wednesday_End']."',TBS_Dinner_Thursday_Start='".$_REQUEST['TBS_Dinner_Thursday_Start']."',TBS_Dinner_Thursday_End='".$_REQUEST['TBS_Dinner_Thursday_End']."',TBS_Dinner_Friday_Start='".$_REQUEST['TBS_Dinner_Friday_Start']."',TBS_Dinner_Friday_End='".$_REQUEST['TBS_Dinner_Friday_End']."',TBS_Dinner_Saturday_Start='".$_REQUEST['TBS_Dinner_Saturday_Start']."',TBS_Dinner_Saturday_End='".$_REQUEST['TBS_Dinner_Saturday_End']."',TBS_Dinner_Sunday_Start='".$_REQUEST['TBS_Dinner_Sunday_Start']."',TBS_Dinner_Sunday_End='".$_REQUEST['TBS_Dinner_Sunday_End']."',TBS_Dinner='".$TBS_Dinner."',TBS_Before_Breakfast='".$TBS_Before_Breakfast."',TBS_Before_Lunch='".$TBS_Before_Lunch."',TBS_Before_Dinner='".$TBS_Before_Dinner."',TBS_Reserve_Buffer='".$TBS_Reserve_Buffer."',TBS_Desc='".$TBS_Desc."' WHERE merchant=".$_REQUEST['Res_ID']; 
			$sql="UPDATE tbl_tablebooking_settings SET TBS_Breakfast_Monday_Closed='".$TBS_Breakfast_Monday_Closed."',TBS_Breakfast_Tuesday_Closed='".$TBS_Breakfast_Tuesday_Closed."',TBS_Breakfast_Wednesday_Closed='".$TBS_Breakfast_Wednesday_Closed."',TBS_Breakfast_Thursday_Closed='".$TBS_Breakfast_Thursday_Closed."',TBS_Breakfast_Friday_Closed='".$TBS_Breakfast_Friday_Closed."',TBS_Breakfast_Saturday_Closed='".$TBS_Breakfast_Saturday_Closed."',TBS_Breakfast_Sunday_Closed='".$TBS_Breakfast_Sunday_Closed."',TBS_Seats='".$_REQUEST['TBS_Seats']."',TBS_Breakfast_Monday_Start='".$TBS_Breakfast_Monday_Start."',TBS_Breakfast_Monday_End='".$TBS_Breakfast_Monday_End."',TBS_Breakfast_Tuesday_Start='".$TBS_Breakfast_Tuesday_Start."',TBS_Breakfast_Tuesday_End='".$TBS_Breakfast_Tuesday_End."',TBS_Breakfast_Wednesday_Start='".$TBS_Breakfast_Wednesday_Start."',TBS_Breakfast_Wednesday_End='".$TBS_Breakfast_Wednesday_End."',TBS_Breakfast_Thursday_Start='".$TBS_Breakfast_Thursday_Start."',TBS_Breakfast_Thursday_End='".$TBS_Breakfast_Thursday_End."',TBS_Breakfast_Friday_Start='".$TBS_Breakfast_Friday_Start."',TBS_Breakfast_Friday_End='".$TBS_Breakfast_Friday_End."',TBS_Breakfast_Saturday_Start='".$TBS_Breakfast_Saturday_Start."',TBS_Breakfast_Saturday_End='".$TBS_Breakfast_Saturday_End."',TBS_Breakfast_Sunday_Start='".$TBS_Breakfast_Sunday_Start."',TBS_Breakfast_Sunday_End='".$TBS_Breakfast_Sunday_End."',TBS_Breakfast='".$TBS_Breakfast."',TBS_Before_Breakfast='".$TBS_Before_Breakfast."',TBS_Before_Lunch='".$TBS_Before_Lunch."',TBS_Before_Dinner='".$TBS_Before_Dinner."',TBS_Reserve_Buffer='".$TBS_Reserve_Buffer."',TBS_Desc='".$TBS_Desc."' WHERE merchant=".$_REQUEST['Res_ID']; 
			$exe=mysqli_query($con, $sql);
			//exit;
		}
		else
		{
			/*$sql="INSERT INTO tbl_tablebooking_settings(merchant,TBS_Seats,TBS_UserID,TBS_Breakfast_Monday_Start,TBS_Breakfast_Monday_End,TBS_Breakfast_Tuesday_Start,TBS_Breakfast_Tuesday_End,TBS_Breakfast_Wednesday_Start,TBS_Breakfast_Wednesday_End,TBS_Breakfast_Thursday_Start,TBS_Breakfast_Thursday_End,TBS_Breakfast_Friday_Start,TBS_Breakfast_Friday_End,TBS_Breakfast_Saturday_Start,TBS_Breakfast_Saturday_End,TBS_Breakfast_Sunday_Start,TBS_Breakfast_Sunday_End,TBS_Lunch_Monday_Start,TBS_Lunch_Monday_End,TBS_Lunch_Tuesday_Start,TBS_Lunch_Tuesday_End,TBS_Lunch_Wednesday_Start,TBS_Lunch_Wednesday_End,TBS_Lunch_Thursday_Start,TBS_Lunch_Thursday_End,TBS_Lunch_Friday_Start,TBS_Lunch_Friday_End,TBS_Lunch_Saturday_Start,TBS_Lunch_Saturday_End,TBS_Lunch_Sunday_Start,TBS_Lunch_Sunday_End,TBS_Dinner_Monday_Start,TBS_Dinner_Monday_End,TBS_Dinner_Tuesday_Start,TBS_Dinner_Tuesday_End,TBS_Dinner_Wednesday_Start,TBS_Dinner_Wednesday_End,TBS_Dinner_Thursday_Start,TBS_Dinner_Thursday_End,TBS_Dinner_Friday_Start,TBS_Dinner_Friday_End,TBS_Dinner_Saturday_Start,TBS_Dinner_Saturday_End,TBS_Dinner_Sunday_Start,TBS_Dinner_Sunday_End,TBS_Breakfast,TBS_Dinner,TBS_Before_Breakfast,TBS_Before_Lunch,TBS_Before_Dinner,TBS_Reserve_Buffer,TBS_Desc,TBS_Created) 
			VALUES('".$_REQUEST['Res_ID']."','".$_REQUEST['TBS_Seats']."','".$_SESSION['User_ID']."','".$_REQUEST['TBS_Breakfast_Monday_Start']."','".$_REQUEST['TBS_Breakfast_Monday_End']."','".$_REQUEST['TBS_Breakfast_Tuesday_Start']."','".$_REQUEST['TBS_Breakfast_Tuesday_End']."','".$_REQUEST['TBS_Breakfast_Wednesday_Start']."','".$_REQUEST['TBS_Breakfast_Wednesday_End']."','".$_REQUEST['TBS_Breakfast_Thursday_Start']."','".$_REQUEST['TBS_Breakfast_Thursday_End']."','".$_REQUEST['TBS_Breakfast_Friday_Start']."','".$_REQUEST['TBS_Breakfast_Friday_End']."','".$_REQUEST['TBS_Breakfast_Saturday_Start']."','".$_REQUEST['TBS_Breakfast_Saturday_End']."','".$_REQUEST['TBS_Breakfast_Sunday_Start']."','".$_REQUEST['TBS_Breakfast_Sunday_End']."','".$_REQUEST['TBS_Lunch_Monday_Start']."','".$_REQUEST['TBS_Lunch_Monday_End']."','".$_REQUEST['TBS_Lunch_Tuesday_Start']."','".$_REQUEST['TBS_Lunch_Tuesday_End']."','".$_REQUEST['TBS_Lunch_Wednesday_Start']."','".$_REQUEST['TBS_Lunch_Wednesday_End']."','".$_REQUEST['TBS_Lunch_Thursday_Start']."','".$_REQUEST['TBS_Lunch_Thursday_End']."','".$_REQUEST['TBS_Lunch_Friday_Start']."','".$_REQUEST['TBS_Lunch_Friday_End']."','".$_REQUEST['TBS_Lunch_Saturday_Start']."','".$_REQUEST['TBS_Lunch_Saturday_End']."','".$_REQUEST['TBS_Lunch_Sunday_Start']."','".$_REQUEST['TBS_Lunch_Sunday_End']."','".$_REQUEST['TBS_Dinner_Monday_Start']."','".$_REQUEST['TBS_Dinner_Monday_End']."','".$_REQUEST['TBS_Dinner_Tuesday_Start']."','".$_REQUEST['TBS_Dinner_Tuesday_End']."','".$_REQUEST['TBS_Dinner_Wednesday_Start']."','".$_REQUEST['TBS_Dinner_Wednesday_End']."','".$_REQUEST['TBS_Dinner_Thursday_Start']."','".$_REQUEST['TBS_Dinner_Thursday_End']."','".$_REQUEST['TBS_Dinner_Friday_Start']."','".$_REQUEST['TBS_Dinner_Friday_End']."','".$_REQUEST['TBS_Dinner_Saturday_Start']."','".$_REQUEST['TBS_Dinner_Saturday_End']."','".$_REQUEST['TBS_Dinner_Sunday_Start']."','".$_REQUEST['TBS_Dinner_Sunday_End']."','".$TBS_Breakfast."','".$TBS_Dinner."','".$TBS_Before_Breakfast."','".$TBS_Before_Lunch."','".$TBS_Before_Dinner."','".$TBS_Reserve_Buffer."','".$TBS_Desc."',Now())";*/
			$sql="INSERT INTO tbl_tablebooking_settings(merchant,TBS_Seats,TBS_UserID,TBS_Breakfast_Monday_Closed,TBS_Breakfast_Tuesday_Closed,TBS_Breakfast_Wednesday_Closed,TBS_Breakfast_Thursday_Closed,TBS_Breakfast_Friday_Closed,TBS_Breakfast_Saturday_Closed,TBS_Breakfast_Sunday_Closed,TBS_Breakfast_Monday_Start,TBS_Breakfast_Monday_End,TBS_Breakfast_Tuesday_Start,TBS_Breakfast_Tuesday_End,TBS_Breakfast_Wednesday_Start,TBS_Breakfast_Wednesday_End,TBS_Breakfast_Thursday_Start,TBS_Breakfast_Thursday_End,TBS_Breakfast_Friday_Start,TBS_Breakfast_Friday_End,TBS_Breakfast_Saturday_Start,TBS_Breakfast_Saturday_End,TBS_Breakfast_Sunday_Start,TBS_Breakfast_Sunday_End,TBS_Breakfast,TBS_Before_Breakfast,TBS_Reserve_Buffer,TBS_Desc,TBS_Created) 
			VALUES('".$_REQUEST['Res_ID']."','".$_REQUEST['TBS_Seats']."','".$_SESSION['User_ID']."','".$TBS_Breakfast_Monday_Closed."','".$TBS_Breakfast_Tuesday_Closed."','".$TBS_Breakfast_Wednesday_Closed."','".$TBS_Breakfast_Thursday_Closed."','".$TBS_Breakfast_Friday_Closed."','".$TBS_Breakfast_Saturday_Closed."','".$TBS_Breakfast_Sunday_Closed."','".$TBS_Breakfast_Monday_Start."','".$TBS_Breakfast_Monday_End."','".$TBS_Breakfast_Tuesday_Start."','".$TBS_Breakfast_Tuesday_End."','".$TBS_Breakfast_Wednesday_Start."','".$TBS_Breakfast_Wednesday_End."','".$TBS_Breakfast_Thursday_Start."','".$TBS_Breakfast_Thursday_End."','".$TBS_Breakfast_Friday_Start."','".$TBS_Breakfast_Friday_End."','".$TBS_Breakfast_Saturday_Start."','".$TBS_Breakfast_Saturday_End."','".$TBS_Breakfast_Sunday_Start."','".$TBS_Breakfast_Sunday_End."','".$TBS_Breakfast."','".$TBS_Before_Breakfast."','".$TBS_Reserve_Buffer."','".$TBS_Desc."',Now())";
			$exe=mysqli_query($con, $sql);
		}
		//echo $sql;exit;
		echo '<script language="javascript">location.href="dine.php?act=settings&Res_ID='.$_REQUEST['Res_ID'].'";</script>'; 
		exit;
	}
	function EditNotification()
	{
		GLOBAL $con;
		$GS_Noti_frommail=mysqli_real_escape_string($con,$_REQUEST["GS_Noti_frommail"]);
		$GS_Noti_fromname=mysqli_real_escape_string($con,$_REQUEST["GS_Noti_fromname"]);
		$GS_Send_Confirmation=mysqli_real_escape_string($con,$_REQUEST["GS_Send_Confirmation"]);
		$GS_Cus_NotiSubject=mysqli_real_escape_string($con,$_REQUEST["GS_Cus_NotiSubject"]);
		$GS_Cus_NotiBody=mysqli_real_escape_string($con,$_REQUEST["GS_Cus_NotiBody"]);
		$GS_Cancel_Subject=mysqli_real_escape_string($con,$_REQUEST["GS_Cancel_Subject"]);
		$GS_Cancel_Body=mysqli_real_escape_string($con,$_REQUEST["GS_Cancel_Body"]);
		$GS_Noti_Email=mysqli_real_escape_string($con,$_REQUEST["GS_Noti_Email"]);
		
		$GS_Noti_CC=mysqli_real_escape_string($con,$_REQUEST["GS_Noti_CC"]);
		$GS_Attach_Report=mysqli_real_escape_string($con,$_REQUEST["GS_Attach_Report"]);
		$GS_Noti_Mail_Subject=mysqli_real_escape_string($con,$_REQUEST["GS_Noti_Mail_Subject"]);
		$GS_Noti_Body=mysqli_real_escape_string($con,$_REQUEST["GS_Noti_Body"]);
		$GS_Ext_Cancel_Subject=mysqli_real_escape_string($con,$_REQUEST["GS_Ext_Cancel_Subject"]);
		$GS_Ext_Cancel_Body=mysqli_real_escape_string($con,$_REQUEST["GS_Ext_Cancel_Body"]);
		
		/*$sql1="SELECT TBS_ID FROM tbl_tablebooking_settings WHERE TBS_UserID=".$_SESSION['User_ID'];
		$exe1=mysqli_query($con, $sql1);
		$num1=mysqli_num_rows($exe1);
		if($num1>0)
		{
			$res=mysqli_fetch_array($exe1);
			$id=$res['TBS_ID'];
		} else {
			$sql="INSERT INTO tbl_tablebooking_settings(merchant) VALUES(".$_SESSION['User_ID'].")";
			$exe=mysqli_query($con, $sql);
			$id=mysqli_insert_id($con,);
		}*/
		
		$sql="SELECT GS_ID FROM tbl_tablebooking_globalsettings WHERE GS_Restaurant=".$_REQUEST['Res_ID'];
		$exe=mysqli_query($con, $sql);
		$num=mysqli_num_rows($exe);
		if($num>0)
		{
			$sql="UPDATE tbl_tablebooking_globalsettings SET GS_Noti_frommail='".$GS_Noti_frommail."',GS_Noti_fromname='".$GS_Noti_fromname."',GS_Send_Confirmation='".$GS_Send_Confirmation."',GS_Cus_NotiSubject='".$GS_Cus_NotiSubject."',GS_Cus_NotiBody='".$GS_Cus_NotiBody."',GS_Cancel_Subject='".$GS_Cancel_Subject."',GS_Cancel_Body='".$GS_Cancel_Body."',GS_Noti_Email='".$GS_Noti_Email."',GS_Noti_CC='".$GS_Noti_CC."',GS_Attach_Report='".$GS_Attach_Report."',GS_Noti_Mail_Subject='".$GS_Noti_Mail_Subject."',GS_Noti_Body='".$GS_Noti_Body."',GS_Ext_Cancel_Subject='".$GS_Ext_Cancel_Subject."',GS_Ext_Cancel_Body='".$GS_Ext_Cancel_Body."' WHERE GS_Restaurant=".$_REQUEST['Res_ID']; 
			$exe=mysqli_query($con, $sql);
		}
		else
		{
			$sql="INSERT INTO tbl_tablebooking_globalsettings(GS_Restaurant,GS_UserID,GS_Noti_frommail,GS_Noti_fromname,GS_Send_Confirmation,GS_Cus_NotiSubject,GS_Cus_NotiBody,GS_Cancel_Subject,GS_Cancel_Body,GS_Noti_Email,GS_Noti_CC,GS_Attach_Report,GS_Noti_Mail_Subject,GS_Noti_Body,GS_Ext_Cancel_Subject,GS_Ext_Cancel_Body,GS_Created) 
			VALUES('".$_REQUEST['Res_ID']."','".$_SESSION['User_ID']."','".$GS_Noti_frommail."','".$GS_Noti_fromname."','".$GS_Send_Confirmation."','".$GS_Cus_NotiSubject."','".$GS_Cus_NotiBody."','".$GS_Cancel_Subject."','".$GS_Cancel_Body."','".$GS_Noti_Email."','".$GS_Noti_CC."','".$GS_Attach_Report."','".$GS_Noti_Mail_Subject."','".$GS_Noti_Body."','".$GS_Ext_Cancel_Subject."','".$GS_Ext_Cancel_Body."',Now())";
			$exe=mysqli_query($con, $sql);
		}
		//echo $sql;exit;
		echo '<script language="javascript">location.href="dine.php?act=notify&Res_ID='.$_REQUEST['Res_ID'].'";</script>'; 
		exit;
	}
	function view_review()
	  {
		  GLOBAL $con;
			$Record=array();
			$i=0;
			$sql="SELECT * FROM tbl_reviews WHERE Review_ID=".mysqli_real_escape_string($con,$_REQUEST['Review_ID'])." order by Date_Created desc";
			$rec=mysqli_query($con, $sql);
			$count=mysqli_num_rows($rec);
			while($res=mysqli_fetch_array($rec))
			{
				$Record[$i]['Review_ID']		=$res['Review_ID'];
				$Record[$i]['user_id']=$res['user_id'];
				$Record[$i]['txt1']=$res['txt1'];
				$Record[$i]['list_id']		=$res['list_id'];
				
				$Record[$i]['Review']	=$res['Review'];
				$Record[$i]['Date_Created']	=$res['Date_Created'];
				$Record[$i]['status']	=	$res['status'];
				$i++;
			}
			return array($Record,$count);
		}
	function view_Reviews1()
	{
		GLOBAL $con;
		//user_id=".$_SESSION['User_ID']." AND
		$sql="SELECT * FROM tbl_reviews WHERE R_Type='Dine' ";
		if($_REQUEST['Res_ID']!='')
		 $sql.=" AND list_id=".mysqli_real_escape_string($con,$_REQUEST['Res_ID']);
		$sql.=" order by Date_Created desc";
				$result = mysqli_query($con, $sql) or die(mysqli_error($con));
		$TotalRecordCount=mysqli_num_rows($result);
	
			$Totalpages 		= mysqli_query($con, "SELECT * FROM `tbl_control`");
			$exec_paging		= mysqli_fetch_array($Totalpages);
			$end_count 			= $exec_paging['no_of_pages'];
			$Limits				= $exec_paging['results_per_page'];
			$Limits = $Limits;
			$Page = $_REQUEST['page'];
			if($Page==""){
				$Page=1;
			}
			$pageCount = ceil($TotalRecordCount/$Limits); 
			$StartLimit = (($Page-1)*$Limits);
			if($TotalRecordCount > ($Page*$Limits)){
				$EndLimit = $Page*$Limits;
			}else{
				$EndLimit = $TotalRecordCount;
			}
			$sql1=" LIMIT ".$StartLimit.",".$Limits;
			$query=$sql.$sql1;
			$Records=array();
			$i=1;
			$Res=mysqli_query($con, $query) or die (mysqli_error($con));
			$Record	=	array();
			$i			=	0;
			while($res=mysqli_fetch_array($Res)) {
					$Record[$i]['Review_ID']		=$res['Review_ID'];
					$Record[$i]['user_id']=$res['user_id'];
					$Record[$i]['txt0']=$res['txt0'];
					$Record[$i]['txt1']=$res['txt1'];
					$Record[$i]['txt2']=$res['txt2'];
					$Record[$i]['txt3']=$res['txt3'];
					$Record[$i]['txt4']=$res['txt4'];
					$Record[$i]['txt5']=$res['txt5'];
					$Record[$i]['txt1_poor']=$res['txt1_poor'];
					$Record[$i]['txt1_average']=$res['txt1_average'];
					$Record[$i]['txt1_good']=$res['txt1_good'];
					$Record[$i]['txt1_vgood']=$res['txt1_vgood'];
					$Record[$i]['txt1_excellence']=$res['txt1_excellence'];
					$Record[$i]['list_id']		=$res['list_id'];
					$Record[$i]['Review']	=$res['Review'];
					$Record[$i]['Date_Created']	=$res['Date_Created'];
					$Record[$i]['status']	=	$res['status'];
				$i++;
			}
		return array($Record,$TotalRecordCount);
	}
	function DelReview()
	   {
		   GLOBAL $con;
		   $qry="DELETE FROM tbl_reviews WHERE Review_ID=".mysqli_real_escape_string($con,$_REQUEST[id]);
		   mysqli_query($con, $qry) or die(mysqli_error($con));
	  }
	  function Review_Status()
	   {
		   GLOBAL $con;
		   $qry="UPDATE tbl_reviews SET status=".mysqli_real_escape_string($con,$_GET[status])." WHERE Review_ID=".mysqli_real_escape_string($con,$_REQUEST['id']); 
		  mysqli_query($con, $qry) or die(mysqli_error($con));
	  }
	  function GetAverageReviews($model_id)
		{
			GLOBAL $con;
			$sql="SELECT txt1,txt2,txt3,txt4,txt5,txt6,txt7 FROM tbl_reviews WHERE model_id=".$model_id;
			$Exe=@mysqli_query($con, $sql);
			$Total=0;
			$i=0;
			while($res=@mysqli_fetch_array($Exe))
			{
				$Total=$Total+$res['txt1'];
				$Total=$Total+$res['txt2'];
				$Total=$Total+$res['txt3'];
				$Total=$Total+$res['txt4'];
				$Total=$Total+$res['txt5'];
				$Total=$Total+$res['txt6'];
				$Total=$Total+$res['txt7'];
				$i++;
			}
			$cnt=$i*7;
			$Avg=@round($Total/$cnt);
			return $Avg;
		}
		function getextreviews($Res_ID)
		{
			GLOBAL $con;
			$sql="select * from tbl_reviewsothersites where status=1 AND Review_Type='Dine' AND Res_ID=".$Res_ID." ORDER BY Date_Created DESC LIMIT 2";
			$result= mysqli_query($con, $sql);
			$Records=array();
			$totalrecords=mysqli_num_rows($result);
			$i = 0; 
			while ($aRow=mysqli_fetch_array($result))
			{                			
				$Records[$i]['Review_ID']=$aRow['Review_ID'];			
				$Records[$i]['user_id']=$aRow['user_id'];			
				$Records[$i]['Site_Name']=$aRow['Site_Name'];
				$Records[$i]['Title']=$aRow['Title'];
				$Records[$i]['Site_URL']=$aRow['Site_URL'];
				$Records[$i]['status']=$aRow['status'];
				$Records[$i]['Date_Created']=$aRow['Date_Created'];
				$i++;
			}
			return array($Records,$totalrecords);		  
		}
	function getAextreviews($ID)
	{
		GLOBAL $con;
		$sql="select * from tbl_reviewsothersites where Review_ID=".$ID;
		$result= mysqli_query($con, $sql);
		$Records=array();
		$totalrecords=mysqli_num_rows($result);
		$i = 0; 
		while ($aRow=mysqli_fetch_array($result))
		{                			
			$Records[$i]['Site_Name']=$aRow['Site_Name'];
			$Records[$i]['Title']=$aRow['Title'];
			$Records[$i]['Site_URL']=$aRow['Site_URL'];
			
			$i++;
		}
		return array($Records,$totalrecords);		  
	}
	 function view_Reviews()
	 {
		GLOBAL $con;
		  $sql="Select * from  tbl_activities WHERE Act_UserID='".$_SESSION[User_ID]."' order by Act_Created desc"; 
		  $result = mysqli_query($con, $sql) or die(mysqli_error($con));
		  $TotalRecordCount=mysqli_num_rows($result);
	
			$Totalpages 		= mysqli_query($con, "SELECT * FROM `tbl_control`");
			$exec_paging		= mysqli_fetch_array($Totalpages);
			$end_count 			= $exec_paging['no_of_pages'];
			$Limits				= $exec_paging['results_per_page'];
			$Limits = $Limits;
			$Page = $_REQUEST['page'];
			if($Page==""){
				$Page=1;
			}
			$pageCount = ceil($TotalRecordCount/$Limits); 
			$StartLimit = (($Page-1)*$Limits);
			if($TotalRecordCount > ($Page*$Limits)){
				$EndLimit = $Page*$Limits;
			}else{
				$EndLimit = $TotalRecordCount;
			}
			$sql1=" LIMIT ".$StartLimit.",".$Limits;
			$query=$sql.$sql1;
			$Records=array();
			$i=1;
			$Res=mysqli_query($con, $query) or die (mysqli_error($con));
			$Record	=	array();
			$i			=	0;
			while($res=mysqli_fetch_array($Res)) {
				$Record[$i]['id']		=$res['Act_ID'];
				$Record[$i]['P_UserID']=$res['Act_UserID'];
				$Record[$i]['views']	=$res['views'];
				$Record[$i]['description']	=$res['Act_Title'];
				$Record[$i]['cdate']	=$res['Act_Created'];
				$Record[$i]['clicks']	=$res['clicks'];
				$i++;
			}
		return array($Record,$TotalRecordCount);
	}
	function GetTabMenus()
	{
		GLOBAL $con;
		$sql="SELECT id,Main_Menu,Start_Time,End_Time FROM menus WHERE menuStatus='Active' AND merchantID=".mysqli_real_escape_string($con,$_REQUEST['id'])." GROUP BY Main_Menu LIMIT 3";
		$exe=mysqli_query($con, $sql) or die(mysqli_error($con));
		$i=0;
		$record=array();
		while($rec=mysqli_fetch_array($exe))
		{
			$record[$i]['id']				=	$rec['id'];
			$record[$i]['Main_Menu']			=	$rec['Main_Menu'];
			$record[$i]['Start_Time']			=	$rec['Start_Time'];
			$record[$i]['End_Time']			=	$rec['End_Time'];
			$i++;
		}
		return array($record);
	}
	function GetTabMenushtml($id)
	{
		GLOBAL $con;
		$sql="SELECT id,Main_Menu,Start_Time,End_Time FROM menus WHERE menuStatus='Active' AND merchantID=".$id." GROUP BY Main_Menu";
		$exe=mysqli_query($con, $sql);
		$i=0;
		$record=array();
		while($rec=mysqli_fetch_array($exe))
		{
			$record[$i]['id']				=	$rec['id'];
			$record[$i]['Main_Menu']			=	$rec['Main_Menu'];
			$record[$i]['Start_Time']			=	$rec['Start_Time'];
			$record[$i]['End_Time']			=	$rec['End_Time'];
			$i++;
		}
		return array($record);
	}
	function GetMenusHours($Menu)
	{
		GLOBAL $con;
		$sql="SELECT Start_Time,End_Time FROM menus WHERE Main_Menu='".mysqli_real_escape_string($con,$Menu)."' AND merchantID=".mysqli_real_escape_string($con,$_REQUEST['id']);
		$exe=mysqli_query($con, $sql) or die(mysqli_error($con));
		$rec=mysqli_fetch_array($exe);
		return array($rec['Start_Time'],$rec['End_Time']);
	}
	function GetMenuFromItem($item)
	{
		GLOBAL $con;
		$sql="SELECT menuID FROM items WHERE id=".$item;
		$exe=mysqli_query($con, $sql) or die(mysqli_error($con));
		$rec=mysqli_fetch_array($exe);
		return $rec['menuID'];
	}
	function GetCus_Pizza($item)
	{
		GLOBAL $con;
		$sql="SELECT Cus_Pizza FROM items WHERE id=".$item;
		$exe=mysqli_query($con, $sql) or die(mysqli_error($con));
		$rec=mysqli_fetch_array($exe);
		return $rec['Cus_Pizza'];
	}
	function GetFree_Toppings($item)
	{
		GLOBAL $con;	
		$sql="SELECT Free_Toppings FROM items WHERE id=".$item;
		$exe=mysqli_query($con, $sql) or die(mysqli_error($con));
		$rec=mysqli_fetch_array($exe);
		return $rec['Free_Toppings'];
	}
	
	function getMerchantOpenCloseStatus_Search($MID,$time,$orderType)
	{
		GLOBAL $Time_Zone;
		GLOBAL $con;
		if($time == "") {
		$day = date("w");
		$currTime = date("H:i:s");
		} else {
			$day = date("w", strtotime($time));
			$currTime = date("H:i:s",strtotime($time));
		}
		
		// get Merchant Info 
		$msql="SELECT * FROM merchant WHERE id=".$MID;
		$mexe=mysqli_query($con, $msql) or die(mysqli_error($con));
		$merchantInfo=mysqli_fetch_array($mexe);
		
		$NowTime = strtotime(date("Ymd"));
		$NowTime = 	toSeconds($NowTime);
		$diff = $merchantInfo['timezone'] - $Time_Zone;               
		$NowTime += ($diff * 60 * 60);
		
		$BookTime = strtotime($time);
		$BookTime = 	toSeconds($BookTime);
		$diff = $merchantInfo['timezone'] - $Time_Zone;               
		$BookTime += ($diff * 60 * 60);
		
		
		
		$sql="SELECT * FROM merchant_hours WHERE merchantID=".$MID." AND weekDay=".$day;
		$exe=mysqli_query($con, $sql) or die(mysqli_error($con));
		$num=mysqli_num_rows($exe);
		if($num>0)
		{
			$row=mysqli_fetch_array($exe);
			$currentTime = 	toSeconds($currTime);
			// mandatory for timezone diff calculation
			$diff = $merchantInfo['timezone'] - $Time_Zone;               
			//$currentTime += ($diff * 60 * 60);
			
			 
			if($orderType == "Delivery")
            {	
				if($row['deliveryClosed'] == "Yes")
					return "Close";
				else if( isset($row['deliveryStartTime']) && isset($row['deliveryEndTime']) && $row['deliveryStartTime'] != "00:00:00" && $row['deliveryEndTime'] != "00:00:00" && (($row['deliveryEndTime'] >= $row['deliveryStartTime'] && $currentTime >= toSeconds($row['deliveryStartTime']) && $currentTime<= toSeconds($row['deliveryEndTime'])) || ($row['deliveryEndTime'] < $row['deliveryStartTime'] && $currentTime >= toSeconds($row['deliveryStartTime']) && $currentTime<= (toSeconds($row['deliveryEndTime'])+24*60*60) ))) {
				return "Open";
                    } else if(isset($row['deliveryStartTimeOther']) && isset($row['deliveryEndTimeOther']) && $row['deliveryStartTimeOther'] != "00:00:00" && $row['deliveryEndTimeOther'] != "00:00:00" && (($row['deliveryEndTimeOther'] >= $row['deliveryStartTimeOther'] && $currentTime >= toSeconds($row['deliveryStartTimeOther']) && $currentTime<= toSeconds($row['deliveryEndTimeOther'])) || ($row['deliveryEndTimeOther'] < $row['deliveryStartTimeOther'] && $currentTime >= toSeconds($row['deliveryStartTimeOther']) && $currentTime<= (toSeconds($row['deliveryEndTimeOther'])+24*60*60))))  {
				return "Open";
                    } else {
				return "Close";
                   }
			}
			else if($orderType == "Pickup")
			{
				if($row['pickupClosed'] == "Yes")
				return "Close";
						else if( isset($row['pickupStartTime']) && isset($row['pickupEndTime']) && $row['pickupStartTime'] != "00:00:00" && $row['pickupEndTime'] != "00:00:00" && (($row['pickupEndTime'] >= $row->pickupStartTime && $currentTime >= toSeconds($row['pickupStartTime']) && $currentTime<= toSeconds($row['pickupEndTime'])) || ($row['pickupEndTime'] < $row['pickupStartTime'] && $currentTime >= toSeconds($row['pickupStartTime']) && $currentTime<= (toSeconds($row['pickupEndTime'])+24*60*60)))) {
				return "Open";
						} else if(isset($row['pickupStartTimeOther']) && isset($row['pickupEndTimeOther']) && $row['pickupStartTimeOther'] != "00:00:00" && $row['pickupEndTimeOther'] != "00:00:00" && (($row['pickupEndTimeOther'] >= $row['pickupStartTimeOther'] && $currentTime >= toSeconds($row['pickupStartTimeOther']) && $currentTime<= toSeconds($row['pickupEndTimeOther'])) || ($row['pickupEndTimeOther'] < $row['pickupStartTimeOther'] && $currentTime >= toSeconds($row['pickupStartTimeOther']) && $currentTime<= (toSeconds($row['pickupEndTimeOther'])+24*60*60)))) {
				return "Open";
						} else {
				return "Close";
						}
			} else {  // restaurant open time

                    if($row['closed'] == "Yes")
			return "Close";
                    else if( isset($row['startTime']) && isset($row['endTime']) && $row['startTime'] != "00:00:00" && $row['endTime'] != "00:00:00" && (($row['endTime'] >= $row['startTime'] && $currentTime >= toSeconds($row['startTime']) && $currentTime<= toSeconds($row['endTime'])) || ($row['endTime'] < $row['startTime'] && $currentTime >= toSeconds($row['startTime']) && $currentTime<= (toSeconds($row['endTime'])+24*60*60)))) {
			return "Open";
                    } else if(isset($row['startTimeOther']) && isset($row['endTimeOther']) && $row['startTimeOther'] != "00:00:00" && $row['endTimeOther'] != "00:00:00" && (($row['endTimeOther'] >= $row['startTimeOther'] && $currentTime >= toSeconds($row['startTimeOther']) && $currentTime<= toSeconds($row['endTimeOther'])) || ($row['endTimeOther'] < $row['startTimeOther'] && $currentTime >= toSeconds($row['startTimeOther']) && $currentTime<= (toSeconds($row['endTimeOther'])+24*60*60)))) {
			return "Open";
                    } else {
                        return "Close";
                    }
                }
			
			//return 'Open';
			return 'Close';
		}
		else
		{
			return 'Close';
		}
		
		return 'Close';
	}
	function RestaurantStatus($MID,$time,$orderType)
	{
		GLOBAL $Time_Zone;
		GLOBAL $con;
		if($time == "") {
		$day = date("w");
		$currTime = date("H:i:s");
		} else {
			$day = date("w", strtotime($time));
			$currTime = date("H:i:s",strtotime($time));
		}
		
		// get Merchant Info 
		$msql="SELECT * FROM merchant WHERE id=".$MID;
		$mexe=mysqli_query($con, $msql) or die(mysqli_error($con));
		$merchantInfo=mysqli_fetch_array($mexe);
		
		$currentTime = strtotime(date("F j, Y, g:i a"));

		$diff = $merchantInfo['timezone'] - $Time_Zone;               
		$currentTime += ($diff * 60 * 60);

		$sql="SELECT * FROM merchant_hours WHERE merchantID=".$MID." AND weekDay=".$day;
		$exe=mysqli_query($con, $sql) or die(mysqli_error($con));
		$num=mysqli_num_rows($exe);
		if($num>0)
		{
			$row=mysqli_fetch_array($exe);
			if($row['closed'] == "Yes")
			return "Close";
            else if( isset($row['startTime']) && isset($row['endTime']) && ((strtotime($row['endTime']) >= strtotime($row['startTime']) && $currentTime >= strtotime($row['startTime']) && $currentTime<= strtotime($row['endTime'])) || (strtotime($row['endTime']) < strtotime($row['startTime']) && $currentTime >= strtotime($row['startTime']) && $currentTime<= (strtotime($row['endTime']))))) {
				return "Open";
			} else if(isset($row['startTimeOther']) && isset($row['endTimeOther']) && (($row['endTimeOther'] >= $row['startTimeOther'] && $currentTime >= strtotime($row['startTimeOther']) && $currentTime<= strtotime($row['endTimeOther'])) || ($row['endTimeOther'] < $row['startTimeOther'] && $currentTime >= strtotime($row['startTimeOther']) && $currentTime<= (strtotime($row['endTimeOther']))))) {
				return "Open";
			}
			else {
				return 'Close';
			}
		}
		else
		{
			return 'Close';
		}
		
		return 'Close';
	}
	function getMerchantOpenCloseStatus($MID,$time,$orderType)
	{
		GLOBAL $Time_Zone;
		GLOBAL $con;
		if($time == "") {
		$day = date("w");
		$currTime = date("H:i:s");
		} else {
			$day = date("w", strtotime($time));
			$currTime = date("H:i:s",strtotime($time));
		}
		
		// get Merchant Info 
		$msql="SELECT * FROM merchant WHERE id=".$MID;
		$mexe=mysqli_query($con, $msql) or die(mysqli_error($con));
		$merchantInfo=mysqli_fetch_array($mexe);
		
		$NowTime = strtotime(date("F j, Y, g:i a"));
		//$NowTime = 	toSeconds($NowTime);
		$diff = $merchantInfo['timezone'] - $Time_Zone;               
		$NowTime += ($diff * 60 * 60);
		
		$BookTime = strtotime($time);
		//$BookTime = 	toSeconds($BookTime);
		$diff = $merchantInfo['timezone'] - $Time_Zone;               
		$BookTime += ($diff * 60 * 60);

		if($BookTime<$NowTime)
		{
			return "PAST";
		}
		
		$sql="SELECT * FROM merchant_hours WHERE merchantID=".$MID." AND weekDay=".$day;
		$exe=mysqli_query($con, $sql) or die(mysqli_error($con));
		$num=mysqli_num_rows($exe);
		if($num>0)
		{
			$row=mysqli_fetch_array($exe);
			//$currentTime = 	toSeconds($currTime);
			$currTime = strtotime($currentTime);
			// mandatory for timezone diff calculation
			$diff = $merchantInfo['timezone'] - $Time_Zone;               
			$currentTime += ($diff * 60 * 60);
			
			 
			if($orderType == "Delivery")
            {	
				if($row['deliveryClosed'] == "Yes")
					return "Close";
				else if( isset($row['deliveryStartTime']) && isset($row['deliveryEndTime']) && $row['deliveryStartTime'] != "00:00:00" && $row['deliveryEndTime'] != "00:00:00" && (($row['deliveryEndTime'] >= $row['deliveryStartTime'] && $currentTime >= strtotime($row['deliveryStartTime']) && $currentTime<= strtotime($row['deliveryEndTime'])) || ($row['deliveryEndTime'] < $row['deliveryStartTime'] && $currentTime >= strtotime($row['deliveryStartTime']) && $currentTime<= strtotime($row['deliveryEndTime']) ))) {
				return "Open";
                    } else if(isset($row['deliveryStartTimeOther']) && isset($row['deliveryEndTimeOther']) && $row['deliveryStartTimeOther'] != "00:00:00" && $row['deliveryEndTimeOther'] != "00:00:00" && (($row['deliveryEndTimeOther'] >= $row['deliveryStartTimeOther'] && $currentTime >= strtotime($row['deliveryStartTimeOther']) && $currentTime<= strtotime($row['deliveryEndTimeOther'])) || ($row['deliveryEndTimeOther'] < $row['deliveryStartTimeOther'] && $currentTime >= strtotime($row['deliveryStartTimeOther']) && $currentTime<= strtotime($row['deliveryEndTimeOther']))))  {
				return "Open";
                    } else {
				return "Close";
                   }
			}
			else if($orderType == "Pickup")
			{
				if($row['pickupClosed'] == "Yes")
				return "Close";
						else if( isset($row['pickupStartTime']) && isset($row['pickupEndTime']) && $row['pickupStartTime'] != "00:00:00" && $row['pickupEndTime'] != "00:00:00" && (($row['pickupEndTime'] >= $row->pickupStartTime && $currentTime >= strtotime($row['pickupStartTime']) && $currentTime<= strtotime($row['pickupEndTime'])) || ($row['pickupEndTime'] < $row['pickupStartTime'] && $currentTime >= strtotime($row['pickupStartTime']) && $currentTime<= (strtotime($row['pickupEndTime'])+24*60*60)))) {
				return "Open";
						} else if(isset($row['pickupStartTimeOther']) && isset($row['pickupEndTimeOther']) && $row['pickupStartTimeOther'] != "00:00:00" && $row['pickupEndTimeOther'] != "00:00:00" && (($row['pickupEndTimeOther'] >= $row['pickupStartTimeOther'] && $currentTime >= strtotime($row['pickupStartTimeOther']) && $currentTime<= strtotime($row['pickupEndTimeOther'])) || ($row['pickupEndTimeOther'] < $row['pickupStartTimeOther'] && $currentTime >= strtotime($row['pickupStartTimeOther']) && $currentTime<= (strtotime($row['pickupEndTimeOther'])+24*60*60)))) {
				return "Open";
						} else {
				return "Close";
						}
			} else {  // restaurant open time

                    if($row['closed'] == "Yes")
			return "Close";
                    else if( isset($row['startTime']) && isset($row['endTime']) && $row['startTime'] != "00:00:00" && $row['endTime'] != "00:00:00" && (($row['endTime'] >= $row['startTime'] && $currentTime >= strtotime($row['startTime']) && $currentTime<= strtotime($row['endTime'])) || ($row['endTime'] < $row['startTime'] && $currentTime >= strtotime($row['startTime']) && $currentTime<= (strtotime($row['endTime']))))) {
			return "Open";
                    } else if(isset($row['startTimeOther']) && isset($row['endTimeOther']) && $row['startTimeOther'] != "00:00:00" && $row['endTimeOther'] != "00:00:00" && (($row['endTimeOther'] >= $row['startTimeOther'] && $currentTime >= strtotime($row['startTimeOther']) && $currentTime<= strtotime($row['endTimeOther'])) || ($row['endTimeOther'] < $row['startTimeOther'] && $currentTime >= strtotime($row['startTimeOther']) && $currentTime<= (strtotime($row['endTimeOther']))))) {
			return "Open";
                    } else {
                        return "Close";
                    }
                }
			
			//return 'Open';
			return 'Close';
		}
		else
		{
			return 'Close';
		}
		
		return 'Close';
	}
	
	function GetMenus($MainMenu)
	{
		GLOBAL $con;
		$sql="SELECT id,menuName,menuDescription,Start_Time,End_Time,menuSize1,menuSize2,menuSize3,menuSize4,menuSize5,menuSize6,menuSize7,menuSize8,menuSize9,menuSize10,menuSize11,menuSize12,menuSize13,menuSize14,menuSize15,menuSize16,menuSize17,menuSize18,menuSize19,menuSize20 FROM menus WHERE menuStatus='Active' AND merchantID=".mysqli_real_escape_string($con,$_REQUEST['id'])." AND Main_Menu='".mysqli_real_escape_string($con,$MainMenu)."' ORDER BY menuOrder,menuName";
		$exe=mysqli_query($con, $sql) or die(mysqli_error($con));
		$num=mysqli_num_rows($exe);
		$i=0;
		$record=array();
		while($rec=mysqli_fetch_array($exe))
		{
			$record[$i]['id']				=	$rec['id'];
			$record[$i]['menuName']			=	$rec['menuName'];
			$record[$i]['menuDescription']			=	$rec['menuDescription'];
			$record[$i]['Start_Time']			=	$rec['Start_Time'];
			$record[$i]['End_Time']			=	$rec['End_Time'];
			$record[$i]['menuSize1']			=	$rec['menuSize1'];
			$record[$i]['menuSize2']			=	$rec['menuSize2'];
			$record[$i]['menuSize3']			=	$rec['menuSize3'];
			$record[$i]['menuSize4']			=	$rec['menuSize4'];
			$record[$i]['menuSize5']			=	$rec['menuSize5'];
			$record[$i]['menuSize6']			=	$rec['menuSize6'];
			$record[$i]['menuSize7']			=	$rec['menuSize7'];
			$record[$i]['menuSize8']			=	$rec['menuSize8'];
			$record[$i]['menuSize9']			=	$rec['menuSize9'];
			$record[$i]['menuSize10']			=	$rec['menuSize10'];
			$record[$i]['menuSize11']=$rec['menuSize11'];
			$record[$i]['menuSize12']=$rec['menuSize12'];
			$record[$i]['menuSize13']=$rec['menuSize13'];
			$record[$i]['menuSize14']=$rec['menuSize14'];
			$record[$i]['menuSize15']=$rec['menuSize15'];
			$record[$i]['menuSize16']=$rec['menuSize16'];
			$record[$i]['menuSize17']=$rec['menuSize17'];
			$record[$i]['menuSize18']=$rec['menuSize18'];
			$record[$i]['menuSize19']=$rec['menuSize19'];
			$record[$i]['menuSize20']=$rec['menuSize20'];
			$i++;
		}
		return array($record,$num);
	}
	function GetMenushtml($MainMenu,$id)
	{
		GLOBAL $con;
		$sql="SELECT id,menuName,menuDescription,Start_Time,End_Time,menuSize1,menuSize2,menuSize3,menuSize4,menuSize5,menuSize6,menuSize7,menuSize8,menuSize9,menuSize10,menuSize11,menuSize12,menuSize13,menuSize14,menuSize15,menuSize16,menuSize17,menuSize18,menuSize19,menuSize20 FROM menus WHERE menuStatus='Active' AND merchantID=".$id." AND Main_Menu='".mysqli_real_escape_string($con,$MainMenu)."' ORDER BY menuOrder,menuName";
		$exe=mysqli_query($con, $sql);
		$num=mysqli_num_rows($exe);
		$i=0;
		$record=array();
		while($rec=mysqli_fetch_array($exe))
		{
			$record[$i]['id']				=	$rec['id'];
			$record[$i]['menuName']			=	$rec['menuName'];
			$record[$i]['menuDescription']			=	$rec['menuDescription'];
			$record[$i]['Start_Time']			=	$rec['Start_Time'];
			$record[$i]['End_Time']			=	$rec['End_Time'];
			$record[$i]['menuSize1']			=	$rec['menuSize1'];
			$record[$i]['menuSize2']			=	$rec['menuSize2'];
			$record[$i]['menuSize3']			=	$rec['menuSize3'];
			$record[$i]['menuSize4']			=	$rec['menuSize4'];
			$record[$i]['menuSize5']			=	$rec['menuSize5'];
			$record[$i]['menuSize6']			=	$rec['menuSize6'];
			$record[$i]['menuSize7']			=	$rec['menuSize7'];
			$record[$i]['menuSize8']			=	$rec['menuSize8'];
			$record[$i]['menuSize9']			=	$rec['menuSize9'];
			$record[$i]['menuSize10']			=	$rec['menuSize10'];
			$record[$i]['menuSize11']=$rec['menuSize11'];
			$record[$i]['menuSize12']=$rec['menuSize12'];
			$record[$i]['menuSize13']=$rec['menuSize13'];
			$record[$i]['menuSize14']=$rec['menuSize14'];
			$record[$i]['menuSize15']=$rec['menuSize15'];
			$record[$i]['menuSize16']=$rec['menuSize16'];
			$record[$i]['menuSize17']=$rec['menuSize17'];
			$record[$i]['menuSize18']=$rec['menuSize18'];
			$record[$i]['menuSize19']=$rec['menuSize19'];
			$record[$i]['menuSize20']=$rec['menuSize20'];
			$i++;
		}
		return array($record,$num);
	}
	function GetMenustypecnt($tab,$type)
	{
		GLOBAL $con;
		$query="SELECT Count(*) AS CNT FROM items INNER JOIN menus ON menus.id=items.menuID WHERE items.itemStatus='Active' AND menus.Main_Menu='".mysqli_real_escape_string($con,$tab)."' AND menus.merchantID=".mysqli_real_escape_string($con,$_REQUEST['id']);
		if($type!='')
		{
			if($type=="Popular") 
				$query.=" AND items.popular=1";
			if($type=="Spicy") 
				$query.=" AND items.spicy=1";
			if($type=="Veggi") 
				$query.=" AND items.veggie=1";
			if($type=="Chef Special") 
				$query.=" AND items.chef_special=1";
		}
		//echo $query;
		$exe=mysqli_query($con, $query);
		$row=mysqli_fetch_array($exe);
		return $row['CNT'];
	}
	function GetMenuItems($Menu_ID)
	{
		GLOBAL $con;
		$sql="SELECT id,itemName,itemDescription,itemPrice,itemPrice1,itemPrice2,itemPrice3,itemPrice4,itemPrice5,itemPrice6,itemPrice7,itemPrice8,itemPrice9,itemPrice10,itemPrice11,itemPrice12,itemPrice13,itemPrice14,itemPrice15,itemPrice16,itemPrice17,itemPrice18,itemPrice19,popular,spicy,veggie,chef_special,itemImage,Cus_Pizza FROM items WHERE itemStatus='Active' AND menuID=".mysqli_real_escape_string($con,$Menu_ID);
		if($_SESSION['CT']<>'')
		{
			$CTT=0;
			$CTArray=explode(",",$_SESSION['CT']);
			$sql.=" AND (";
			if(in_array("Popular",$CTArray))  {
				if($CTT<>0)
					$sql.=" OR";
				$sql.=" popular=1";$CTT++; }
			if(in_array("Spicy",$CTArray)) {
				if($CTT<>0)
					$sql.=" OR";
				$sql.=" spicy=1"; $CTT++; }
			if(in_array("Veggi",$CTArray))  {
				if($CTT<>0)
					$sql.=" OR";
				$sql.=" veggie=1"; $CTT++; }
			if(in_array("Chef Special",$CTArray)) {
				if($CTT<>0)
					$sql.=" OR";
				$sql.=" chef_special=1"; $CTT++; }
			$sql.=")";
		}
		
		$exe=mysqli_query($con, $sql) or die(mysqli_error($con));
		$num=mysqli_num_rows($exe);
		$i=0;
		$record=array();
		while($rec=mysqli_fetch_array($exe))
		{
			$record[$i]['Item_id']					=	$rec['id'];
			$record[$i]['itemName']					=	$rec['itemName'];
			$record[$i]['itemDescription']			=	$rec['itemDescription'];
			$record[$i]['itemPrice']				=	$rec['itemPrice'];
			$record[$i]['itemPrice1']				=	$rec['itemPrice1'];
			$record[$i]['itemPrice2']				=	$rec['itemPrice2'];
			$record[$i]['itemPrice3']				=	$rec['itemPrice3'];
			$record[$i]['itemPrice4']				=	$rec['itemPrice4'];
			$record[$i]['itemPrice5']				=	$rec['itemPrice5'];
			$record[$i]['itemPrice6']				=	$rec['itemPrice6'];
			$record[$i]['itemPrice7']				=	$rec['itemPrice7'];
			$record[$i]['itemPrice8']				=	$rec['itemPrice8'];
			$record[$i]['itemPrice9']				=	$rec['itemPrice9'];
			$record[$i]['itemPrice10']=$rec['itemPrice10'];
			$record[$i]['itemPrice11']=$rec['itemPrice11'];
			$record[$i]['itemPrice12']=$rec['itemPrice12'];
			$record[$i]['itemPrice13']=$rec['itemPrice13'];
			$record[$i]['itemPrice14']=$rec['itemPrice14'];
			$record[$i]['itemPrice15']=$rec['itemPrice15'];
			$record[$i]['itemPrice16']=$rec['itemPrice16'];
			$record[$i]['itemPrice17']=$rec['itemPrice17'];
			$record[$i]['itemPrice18']=$rec['itemPrice18'];
			$record[$i]['itemPrice19']=$rec['itemPrice19'];
			$record[$i]['popular']				=	$rec['popular'];
			$record[$i]['spicy']				=	$rec['spicy'];
			$record[$i]['veggie']				=	$rec['veggie'];
			$record[$i]['chef_special']				=	$rec['chef_special'];
			$record[$i]['itemImage']				=	$rec['itemImage'];
			$record[$i]['Cus_Pizza']				=	$rec['Cus_Pizza'];
			$i++;
		}
		return array($record,$num);
	}
	function GetMerchantHours()
	{
		GLOBAL $con;
		$sql="SELECT * FROM merchant_hours WHERE merchantID=".mysqli_real_escape_string($con,$_REQUEST['id'])." ORDER BY weekDay ASC";
		$exe=mysqli_query($con, $sql) or die(mysqli_error($con));
		$i=0;
		$record=array();
		while($rec=mysqli_fetch_array($exe))
		{
			$record[$i]['id']				=	$rec['id'];
			$record[$i]['weekDay']			=	$rec['weekDay'];
			$record[$i]['closed']			=	$rec['closed'];
			$record[$i]['deliveryClosed']			=	$rec['deliveryClosed'];
			$record[$i]['pickupClosed']			=	$rec['pickupClosed'];
			$record[$i]['startTime']		=	$rec['startTime'];
			if($rec['endTime']=='23:59:59')
					$record[$i]['endTime']="00:00:00";
				else
					$record[$i]['endTime']=$rec['endTime'];
			$record[$i]['startTimeOther']	=	$rec['startTimeOther'];
			$record[$i]['endTimeOther']		=	$rec['endTimeOther'];
			$record[$i]['deliveryStartTimeOther']	=	$rec['deliveryStartTimeOther'];
			$record[$i]['deliveryEndTimeOther']		=	$rec['deliveryEndTimeOther'];
			$record[$i]['deliveryStartTime']		=	$rec['deliveryStartTime'];
			$record[$i]['deliveryEndTime']			=	$rec['deliveryEndTime'];
			$i++;
		}
		return array($record);
	}
	function GetMerchantOpenHours()
	{
		GLOBAL $con;
		$sql="SELECT * FROM merchant_hours WHERE merchantID=".mysqli_real_escape_string($con,$_REQUEST['id'])." ORDER BY weekDay ASC";
		$exe=mysqli_query($con, $sql) or die(mysqli_error($con));
		$i=0;
		$record=array();
		while($rec=mysqli_fetch_array($exe))
		{
			$record[$i]['id']				=	$rec['id'];
			$record[$i]['weekDay']			=	$rec['weekDay'];
			$record[$i]['closed']			=	$rec['closed'];
			$record[$i]['deliveryClosed']			=	$rec['deliveryClosed'];
			$record[$i]['pickupClosed']			=	$rec['pickupClosed'];
			$record[$i]['startTime']		=	$rec['startTime'];
			$record[$i]['endTime']			=	$rec['endTime'];
			$record[$i]['startTimeOther']	=	$rec['startTimeOther'];
			$record[$i]['endTimeOther']		=	$rec['endTimeOther'];
			$record[$i]['deliveryStartTimeOther']	=	$rec['deliveryStartTimeOther'];
			$record[$i]['deliveryEndTimeOther']		=	$rec['deliveryEndTimeOther'];
			$record[$i]['deliveryStartTime']		=	$rec['deliveryStartTime'];
			$record[$i]['deliveryEndTime']			=	$rec['deliveryEndTime'];
			$i++;
		}
		return array($record);
	}
	function GetPaymentOptions()
	{
		GLOBAL $con;
		$sql="SELECT payment_cc,payment_check,payment_cash,payment_ticket,payment_paypal FROM merchant WHERE id=".mysqli_real_escape_string($con,$_REQUEST['id']);
		$exe=mysqli_query($con, $sql) or die(mysqli_error($con));
		$payments="";
		$rec=mysqli_fetch_array($exe);
		/*{
			if($rec['payment_cc']=='Yes')
				$payments.=",Credit Card";
			if($rec['payment_check']=='Yes')
				$payments.=",Cheque";
			if($rec['payment_cash']=='Yes')
				$payments.=",Cash";
			if($rec['payment_ticket']=='Yes')
				$payments.=",Ticket";
			if($rec['payment_paypal']=='Yes')
				$payments.=",Paypal";
		}*/
		//$payments=trim($payments,",");
		return array($rec);
	}
	function GetDeliveryfee($id,$delivery_basedon)
	{
		GLOBAL $con;
		$Record	=	array();
		$i			=	0;
		if($delivery_basedon=='miles')
		{
			$sql="SELECT * FROM delivery_fees WHERE merchantID=".$id." GROUP BY fees";
			$exe=mysqli_query($con, $sql);
			$fee="";
			while($res=mysqli_fetch_array($exe))
			{
				$Record[$i]['postalCode']=$res['postalCode'];
				$Record[$i]['fees']=$res['fees'];
				$i++;
			}
		}
		else if($delivery_basedon=='zipcode')
		{
			$sql="SELECT * FROM deliveryzipcode_fees WHERE merchantID=".$id." GROUP BY fees";
			$exe=mysqli_query($con, $sql);
			$fee="";
			while($res=mysqli_fetch_array($exe))
			{
				$Record[$i]['postalCode']=$res['postalCode'];
				$Record[$i]['fees']=$res['fees'];
				$i++;
			}
		}
		
		return array($Record);
	}
	function view_ServiceReviews() {
		GLOBAL $con;
		 $sql="SELECT tbl_reviews.*,tbl_registeration.* FROM tbl_reviews INNER JOIN tbl_registeration On tbl_registeration.id=tbl_reviews.user_id WHERE tbl_reviews.list_id=".mysqli_real_escape_string($con,$_REQUEST['id'])." AND R_Type='Dine' order by tbl_reviews.Date_Created desc"; 
		$result = mysqli_query($con, $sql);
		$TotalRecordCount=mysqli_num_rows($result);
	
			$Record	=	array();
			$i			=	0;
			while($res=mysqli_fetch_array($result)) {
					$Record[$i]['Review_ID']		=$res['Review_ID'];
					$Record[$i]['user_id']=$res['user_id'];
					$Record[$i]['txt0']=$res['txt0'];
					$Record[$i]['txt1']=$res['txt1'];
					$Record[$i]['txt2']=$res['txt2'];
					$Record[$i]['txt3']=$res['txt3'];
					$Record[$i]['txt4']=$res['txt4'];
					$Record[$i]['txt5']=$res['txt5'];
					
					$Record[$i]['Average']		=$res['Average'];
					$Record[$i]['category']		=$res['category'];
					$Record[$i]['list_id']		=$res['list_id'];
					$Record[$i]['Review']	=$res['Review'];
					$Record[$i]['firstname']	=$res['firstname'];
					$Record[$i]['lastname']	=$res['lastname'];
					$Record[$i]['category']	=72;
					
					$Record[$i]['txt0_poor']	=$res['txt0_poor'];
					$Record[$i]['txt0_average']	=$res['txt0_average'];
					$Record[$i]['txt0_good']	=$res['txt0_good'];
					$Record[$i]['txt0_vgood']	=$res['txt0_vgood'];
					$Record[$i]['txt0_excellence']	=$res['txt0_excellence'];
					
					$Record[$i]['txt1_poor']	=$res['txt1_poor'];
					$Record[$i]['txt1_average']	=$res['txt1_average'];
					$Record[$i]['txt1_good']	=$res['txt1_good'];
					$Record[$i]['txt1_vgood']	=$res['txt1_vgood'];
					$Record[$i]['txt1_excellence']	=$res['txt1_excellence'];
					
					$Record[$i]['txt2_poor']	=$res['txt2_poor'];
					$Record[$i]['txt2_average']	=$res['txt2_average'];
					$Record[$i]['txt2_good']	=$res['txt2_good'];
					$Record[$i]['txt2_vgood']	=$res['txt2_vgood'];
					$Record[$i]['txt2_excellence']	=$res['txt2_excellence'];
					
					$Record[$i]['txt3_poor']	=$res['txt3_poor'];
					$Record[$i]['txt3_average']	=$res['txt3_average'];
					$Record[$i]['txt3_good']	=$res['txt3_good'];
					$Record[$i]['txt3_vgood']	=$res['txt3_vgood'];
					$Record[$i]['txt3_excellence']	=$res['txt3_excellence'];
					
					$Record[$i]['txt4_poor']	=$res['txt4_poor'];
					$Record[$i]['txt4_average']	=$res['txt4_average'];
					$Record[$i]['txt4_good']	=$res['txt4_good'];
					$Record[$i]['txt4_vgood']	=$res['txt4_vgood'];
					$Record[$i]['txt4_excellence']	=$res['txt4_excellence'];
					
					$Record[$i]['txt5_poor']	=$res['txt5_poor'];
					$Record[$i]['txt5_average']	=$res['txt5_average'];
					$Record[$i]['txt5_good']	=$res['txt5_good'];
					$Record[$i]['txt5_vgood']	=$res['txt5_vgood'];
					$Record[$i]['txt5_excellence']	=$res['txt5_excellence'];
					
					
					$Record[$i]['display_name']	=$res['display_name'];
					$Record[$i]['logo']	=$res['logo'];
					$Record[$i]['Date_Created']	=$res['Date_Created'];
					$Record[$i]['status']	=	$res['status'];
				$i++;
			}
		return array($Record,$TotalRecordCount);
	}
	function ShowCart()
	{		
		GLOBAL $con;
		//$Query="SELECT tbl_cart.*,items.itemName,items.Cus_Pizza from tbl_cart INNER JOIN items ON items.id=tbl_cart.Cart_ServiceID where Cart_Type='Dine' AND Sess_ID='".$_REQUEST['device_id']."'";
		if($_REQUEST['user_id'] != '')
		{
			$Query="SELECT tbl_cart.*,items.itemName,items.Cus_Pizza from tbl_cart INNER JOIN items ON items.id=tbl_cart.Cart_ServiceID where Cart_Type='Dine' AND Cart_UserID='".$_REQUEST['user_id']."'";
		}
		else
		{
			$Query="SELECT tbl_cart.*,items.itemName,items.Cus_Pizza from tbl_cart INNER JOIN items ON items.id=tbl_cart.Cart_ServiceID where Cart_Type='Dine' AND Sess_ID='".$_REQUEST['device_id']."'";	
		}
		$res = mysqli_query($con, $Query);
		$num=mysqli_num_rows($res);
		$cart = array();
		$i=0;
		while ($result=mysqli_fetch_array($res))
		{
			$cart[$i]['Cart_ID'] = $result['Cart_ID'];
			$cart[$i]['Cart_Type'] = $result['Cart_Type'];
			$cart[$i]['itemName'] = $result['itemName'];
			$cart[$i]['Owner_Restaurant'] = $result['Owner_Restaurant'];
			$cart[$i]['Cart_ServiceID'] = $result['Cart_ServiceID'];
			$cart[$i]['Amount'] = $result['Amount'];
			$cart[$i]['Cart_Quantity'] = $result['Cart_Quantity'];
			$cart[$i]['charges'] = $result['charges'];
			$cart[$i]['TotalAmount'] = $result['TotalAmount'];
			$cart[$i]['Ticket_Type'] = $result['Ticket_Type'];
			$cart[$i]['Cart_Created'] = $result['Cart_Created'];
			$cart[$i]['Notes'] = $result['Notes'];
			$cart[$i]['Cus_Pizza'] = $result['Cus_Pizza'];
			$cart[$i]['crust'] = $result['crust'];
			$cart[$i]['Size'] = $result['Size'];
			$i++;
		}
		return array($cart,$num);
		
	}
	function ClearCart()
	{
		GLOBAL $con;
		$Query="SELECT Owner_Restaurant from tbl_cart WHERE Cart_Type='Dine' AND Sess_ID='".$_REQUEST['device_id']."' LIMIT 1";
		$res = mysqli_query($con, $Query);
		$num=mysqli_num_rows($res);
		if($num>0)
		{
			$result=mysqli_fetch_array($res);
			if($result['Owner_Restaurant']<>$_REQUEST['id'])
			{
				$sql="DELETE FROM tbl_cart WHERE Cart_Type='Dine' AND Sess_ID='".$_REQUEST['device_id']."'";
				mysqli_query($con, $sql);
			}
		}
	}
	function getMinimunDeliveryCharges($rest)
	{
		GLOBAL $con;
		$rescity = @mysqli_fetch_array(mysqli_query($con, "select minimumDeliveryAmount from merchant where id=".$rest));
		return $rescity['minimumDeliveryAmount'];
	}
	function reservations()
	{
		GLOBAL $con;
		if($_REQUEST["page"]=="")
		{
			$page=1;
		}
		else
		{
			$page=$_REQUEST["page"];
		}	
		$fromDate = date('Y-m-d',strtotime($_REQUEST['fromDate']));
		$toDate = date('Y-m-d',strtotime($_REQUEST['toDate']));
			
		if($_REQUEST["TxnID"]!='')
		{
			$sq="SELECT orders.id AS Order_ID,merchant.logo,tbl_registeration.firstname,tbl_registeration.display_name,tbl_registeration.email_add,orders.*,merchant.merchantName,merchant.contactAddress,merchant.city AS RES_CITY,merchant.state AS RES_STATE,tbl_registeration.street,tbl_registeration.city AS C_CITY,tbl_registeration.state AS C_STATE FROM orders
			INNER JOIN tbl_registeration ON tbl_registeration.id=orders.customerID
			INNER JOIN merchant ON merchant.id=orders.merchantID 			
			 WHERE orders.id=".mysqli_real_escape_string($con,$_REQUEST["TxnID"])." AND Owner_ID=".$_SESSION["User_ID"]; 
			
		}
		else
		{
			$sq="SELECT orders.id AS Order_ID,merchant.logo,tbl_registeration.firstname,tbl_registeration.display_name,tbl_registeration.email_add,orders.*,merchant.merchantName,merchant.contactAddress,merchant.city AS RES_CITY,merchant.state AS RES_STATE,tbl_registeration.street,tbl_registeration.city AS C_CITY,tbl_registeration.state AS C_STATE,tbl_registeration.* FROM orders
			INNER JOIN tbl_registeration ON tbl_registeration.id=orders.customerID 
			INNER JOIN merchant ON merchant.id=orders.merchantID 
			 WHERE (Owner_ID=".$_SESSION["User_ID"]." OR merchant.Res_AssignUserID=".$_SESSION['User_ID'].")";
			;
		}
		if($_REQUEST['Book_ID']<>'')
		{
			$Book_ID=str_ireplace("DI","",$_REQUEST['Book_ID']);
			$sq.= " AND orders.id='".mysqli_real_escape_string($con,$Book_ID)."'";
		}
		else if($_REQUEST['Activity_ID']<>'')
		{
			$sq.= " AND orders.merchantID=".mysqli_real_escape_string($con,$_REQUEST['Activity_ID']);
		}
		else
		{
		if($_REQUEST['fromDate']<>'')
			//$sq.=" AND orderDate >= '".$fromDate."' AND orderDate <= '".$toDate."'";
			$sq.=" AND orderDate = '".$fromDate."'";
		if($_REQUEST['F_Name']<>'')
			$sq.= " AND tbl_registeration.firstname LIKE '%".mysqli_real_escape_string($con,$_REQUEST['F_Name'])."%' ";
		if($_REQUEST['L_Name']<>'')
			$sq.= " AND tbl_registeration.lastname LIKE '%".mysqli_real_escape_string($con,$_REQUEST['L_Name'])."%' ";
		}
		$sq.=" ORDER BY orderPlacedOn DESC"; 
		
		$arow = mysqli_query($con, $sq);
	    $TotalRecordCount=mysqli_num_rows($arow);		
		$Totalpages = mysqli_query($con, "SELECT * FROM `tbl_control`");
		$exec_paging= mysqli_fetch_array($Totalpages);
		$this->end_count 	= $exec_paging['no_of_pages'];
		$this->Limits		= $exec_paging['results_per_page'];
		$eu = ((($page-1)*$this->Limits) -0);		
		$sql1 .= " LIMIT ".$eu.", ".$this->Limits;
		$query=$sq.$sql1;
		$res = mysqli_query($con, $query);
		$contact = array();
		$i=0;
		while($aRow=mysqli_fetch_array($res))
		{	
			$contact[$i]['id'] = $aRow['Order_ID'];
			$contact[$i]['merchantID']=$aRow['merchantID'];
			$contact[$i]['merchantName']=$aRow['merchantName'];
			$contact[$i]['orderDate']=$aRow['orderDate'];
			$contact[$i]['orderTime']=$aRow['orderTime'];
			$contact[$i]['orderPlacedOn']=$aRow['orderPlacedOn'];
			$contact[$i]['orderType']=$aRow['orderType'];
			$contact[$i]['logo']=$aRow['logo'];
			$contact[$i]['orderAmount']=$aRow['orderAmount'];
			$contact[$i]['discount']=$aRow['discount'];
			$contact[$i]['orderStatus']=$aRow['orderStatus'];
			$contact[$i]['paymentStatus']=$aRow['paymentStatus'];
			$contact[$i]['orderCompleted']=$aRow['orderCompleted'];
			$contact[$i]['paymentType']=$aRow['paymentType'];
			$contact[$i]['orderTaxAmount']=$aRow['orderTaxAmount'];
			$contact[$i]['deliveryFee']=$aRow['deliveryFee'];
			$contact[$i]['customerID'] = $aRow['customerID'];
			$contact[$i]['firstname'] = $aRow['firstname'];
			$contact[$i]['display_name'] = $aRow['display_name'];
			$contact[$i]['lastname'] = $aRow['lastname'];
			$contact[$i]['email_add'] = $aRow['email_add'];
			$contact[$i]['comments'] = $aRow['comments'];
			$contact[$i]['DeliveryAddress'] = $aRow['DeliveryAddress'];
			$contact[$i]['contactAddress'] = $aRow['contactAddress'];
			$contact[$i]['RES_CITY'] = $aRow['RES_CITY'];
			$contact[$i]['RES_STATE'] = $aRow['RES_STATE'];
			$contact[$i]['Redeem'] = $aRow['Redeem'];
			$contact[$i]['street'] = $aRow['street'];
			$contact[$i]['C_CITY'] = $aRow['C_CITY'];
			$contact[$i]['C_STATE'] = $aRow['C_STATE'];
			$contact[$i]['contact_principle'] = $aRow['contact_principle'];
			$contact[$i]['home_phone'] = $aRow['home_phone'];
			$contact[$i]['mobile_phone'] = $aRow['mobile_phone'];
			$contact[$i]['other_phone'] = $aRow['other_phone'];
			$contact[$i]['work_phone'] = $aRow['work_phone'];
			
			$i++;
		}		
	return array($contact,$TotalRecordCount);
	}
	function tablebookings()
	{
		GLOBAL $con;
		if($_REQUEST["page"]=="")
		{
			$page=1;
		}
		else
		{
			$page=$_REQUEST["page"];
		}	
		$fromDate = date('Y-m-d',strtotime($_REQUEST['fromDate']));
		$toDate = date('Y-m-d',strtotime($_REQUEST['toDate']));
			
		if($_REQUEST["TxnID"]!='')
		{
			$sq="SELECT tbl_tablebooking_bookings.Book_ID AS Order_ID,merchant.logo,tbl_registeration.firstname,tbl_registeration.display_name,tbl_registeration.email_add,tbl_tablebooking_bookings.*,merchant.merchantName,merchant.contactAddress,merchant.city AS RES_CITY,merchant.state AS RES_STATE,tbl_registeration.street,tbl_registeration.city AS C_CITY,tbl_registeration.state AS C_STATE FROM tbl_tablebooking_bookings
			INNER JOIN tbl_registeration ON tbl_registeration.id=tbl_tablebooking_bookings.Book_UserID 
			INNER JOIN merchant ON merchant.id=tbl_tablebooking_bookings.Book_Restaurant 			
			WHERE tbl_tablebooking_bookings.Book_ID=".mysqli_real_escape_string($con,$_REQUEST["TxnID"])." AND Book_Owner=".$_SESSION["User_ID"]; 
			//$sql="Select tbl_tablebooking_bookings.*,tbl_registeration.* from tbl_tablebooking_bookings INNER JOIN tbl_registeration ON tbl_registeration.id=tbl_tablebooking_bookings.Book_UserID WHERE Book_Restaurant=".$_REQUEST['Res_ID']." order by Book_Created desc";
		}
		else
		{
			$sq="SELECT tbl_tablebooking_bookings.Book_ID AS Order_ID,merchant.logo,tbl_registeration.firstname,tbl_registeration.display_name,tbl_registeration.email_add,tbl_tablebooking_bookings.*,merchant.merchantName,merchant.contactAddress,merchant.city AS RES_CITY,merchant.state AS RES_STATE,tbl_registeration.street,tbl_registeration.city AS C_CITY,tbl_registeration.state AS C_STATE FROM tbl_tablebooking_bookings
			INNER JOIN tbl_registeration ON tbl_registeration.id=tbl_tablebooking_bookings.Book_UserID 
			INNER JOIN merchant ON merchant.id=tbl_tablebooking_bookings.Book_Restaurant 			
			WHERE Book_Owner=".$_SESSION["User_ID"]; 
		}
		if($_REQUEST['Book_ID']<>'')
		{
			$sq.= " AND tbl_tablebooking_bookings.code='".mysqli_real_escape_string($con,$_REQUEST['Book_ID'])."'";
		}
		else if($_REQUEST['Activity_ID']<>'')
		{
			$sq.= " AND tbl_tablebooking_bookings.Book_Restaurant=".mysqli_real_escape_string($con,$_REQUEST['Activity_ID']);
		}
		else
		{
		if($_REQUEST['fromDate']<>'')
			//$sq.=" AND orderDate >= '".$fromDate."' AND orderDate <= '".$toDate."'";
			$sq.=" AND Book_date = '".$fromDate."'";
		if($_REQUEST['F_Name']<>'')
			$sq.= " AND tbl_registeration.firstname LIKE '%".mysqli_real_escape_string($con,$_REQUEST['F_Name'])."%' ";
		if($_REQUEST['L_Name']<>'')
			$sq.= " AND tbl_registeration.lastname LIKE '%".mysqli_real_escape_string($con,$_REQUEST['L_Name'])."%' ";
		}
		$sq.=" ORDER BY Book_Created DESC"; 
		//echo $sq;
		$arow = mysqli_query($con, $sq);
	    $TotalRecordCount=mysqli_num_rows($arow);		
		$Totalpages = mysqli_query($con, "SELECT * FROM `tbl_control`");
		$exec_paging= mysqli_fetch_array($Totalpages);
		$this->end_count 	= $exec_paging['no_of_pages'];
		$this->Limits		= $exec_paging['results_per_page'];
		$eu = ((($page-1)*$this->Limits) -0);		
		$sql1 .= " LIMIT ".$eu.", ".$this->Limits;
		$query=$sq.$sql1;
		$res = mysqli_query($con, $query);
		$contact = array();
		$i=0;
		while($aRow=mysqli_fetch_array($res))
		{	
			$contact[$i]['id'] = $aRow['Order_ID'];
			$contact[$i]['merchantID']=$aRow['merchantID'];
			$contact[$i]['merchantName']=$aRow['merchantName'];
			$contact[$i]['orderDate']=$aRow['orderDate'];
			$contact[$i]['orderTime']=$aRow['orderTime'];
			$contact[$i]['Book_datetime']=$aRow['Book_datetime'];
			$contact[$i]['Book_Created']=$aRow['Book_Created'];
			$contact[$i]['logo']=$aRow['logo'];
			$contact[$i]['Book_Name']=$aRow['Book_Name'];
			$contact[$i]['Book_Meal']=$aRow['Book_Meal'];
			$contact[$i]['Book_Size']=$aRow['Book_Size'];
			$contact[$i]['Book_Notes']=$aRow['Book_Notes'];
			$contact[$i]['Book_Phone']=$aRow['Book_Phone'];
			$contact[$i]['special_events']=$aRow['special_events'];
			$contact[$i]['deliveryFee']=$aRow['deliveryFee'];
			$contact[$i]['customerID'] = $aRow['customerID'];
			$contact[$i]['firstname'] = $aRow['firstname'];
			$contact[$i]['display_name'] = $aRow['display_name'];
			$contact[$i]['lastname'] = $aRow['lastname'];
			$contact[$i]['email_add'] = $aRow['email_add'];
			$contact[$i]['comments'] = $aRow['comments'];
			$contact[$i]['DeliveryAddress'] = $aRow['DeliveryAddress'];
			$contact[$i]['contactAddress'] = $aRow['contactAddress'];
			$contact[$i]['RES_CITY'] = $aRow['RES_CITY'];
			$contact[$i]['RES_STATE'] = $aRow['RES_STATE'];
			$contact[$i]['Redeem'] = $aRow['Redeem'];
			$contact[$i]['street'] = $aRow['street'];
			$contact[$i]['C_CITY'] = $aRow['C_CITY'];
			$contact[$i]['C_STATE'] = $aRow['C_STATE'];
			$contact[$i]['contact_principle'] = $aRow['contact_principle'];
			$contact[$i]['home_phone'] = $aRow['home_phone'];
			$contact[$i]['mobile_phone'] = $aRow['mobile_phone'];
			$contact[$i]['other_phone'] = $aRow['other_phone'];
			$contact[$i]['work_phone'] = $aRow['work_phone'];
			
			$i++;
		}		
	return array($contact,$TotalRecordCount);
	}
	function PrintOrder()
	{
		GLOBAL $con;
		GLOBAL $EncryptKey;
		if($_REQUEST["page"]=="")
		{
			$page=1;
		}
		else
		{
			$page=$_REQUEST["page"];
		}	
		$fromDate = date('Y-m-d',strtotime($_REQUEST['fromDate']));
		$toDate = date('Y-m-d',strtotime($_REQUEST['toDate']));
			
		if($_REQUEST["TxnID"]!='')
		{
			$sq="SELECT orders.id AS Order_ID,merchant.logo,tbl_registeration.firstname,tbl_registeration.*,tbl_registeration.email_add,orders.*,merchant.merchantName,merchant.contactAddress,merchant.city AS RES_CITY,merchant.state AS RES_STATE,tbl_registeration.street,tbl_registeration.city AS C_CITY,tbl_registeration.state AS C_STATE,orders.PaymentDesc,orders.DeliveryAddress,merchant.postalCode,AES_DECRYPT(orders.Card_Number,'".$EncryptKey."') AS Card_No FROM orders
			INNER JOIN tbl_registeration ON tbl_registeration.id=orders.customerID
			INNER JOIN merchant ON merchant.id=orders.merchantID 			
			 WHERE orders.id=".mysqli_real_escape_string($con,$_REQUEST["TxnID"]); 
			
		}
		
		$arow = mysqli_query($con, $sq);
	    $TotalRecordCount=mysqli_num_rows($arow);		
		$Totalpages = mysqli_query($con, "SELECT * FROM `tbl_control`");
		$exec_paging= mysqli_fetch_array($Totalpages);
		$this->end_count 	= $exec_paging['no_of_pages'];
		$this->Limits		= $exec_paging['results_per_page'];
		$eu = ((($page-1)*$this->Limits) -0);		
		$sql1 .= " LIMIT ".$eu.", ".$this->Limits;
		$query=$sq.$sql1;
		$res = mysqli_query($con, $query);
		$contact = array();
		$i=0;
		while($aRow=mysqli_fetch_array($res))
		{	
			$contact[$i]['id'] = $aRow['Order_ID'];
			$contact[$i]['merchantID']=$aRow['merchantID'];
			$contact[$i]['merchantName']=$aRow['merchantName'];
			$contact[$i]['orderDate']=$aRow['orderDate'];
			$contact[$i]['orderTime']=$aRow['orderTime'];
			$contact[$i]['orderPlacedOn']=$aRow['orderPlacedOn'];
			$contact[$i]['orderType']=$aRow['orderType'];
			$contact[$i]['logo']=$aRow['logo'];
			$contact[$i]['orderAmount']=$aRow['orderAmount'];
			$contact[$i]['orderStatus']=$aRow['orderStatus'];
			$contact[$i]['paymentStatus']=$aRow['paymentStatus'];
			$contact[$i]['orderCompleted']=$aRow['orderCompleted'];
			$contact[$i]['paymentType']=$aRow['paymentType'];
			$contact[$i]['orderTaxAmount']=$aRow['orderTaxAmount'];
			$contact[$i]['discount']=$aRow['discount'];
			$contact[$i]['deliveryFee']=$aRow['deliveryFee'];
			$contact[$i]['customerID'] = $aRow['customerID'];
			$contact[$i]['firstname'] = $aRow['firstname'];
			$contact[$i]['display_name'] = $aRow['display_name'];
			$contact[$i]['lastname'] = $aRow['lastname'];
			$contact[$i]['email_add'] = $aRow['email_add'];
			$contact[$i]['contact_principle'] = $aRow['contact_principle'];
			$contact[$i]['home_phone'] = $aRow['home_phone'];
			$contact[$i]['mobile_phone'] = $aRow['mobile_phone'];
			$contact[$i]['work_phone'] = $aRow['work_phone'];
			
			$contact[$i]['code'] = $aRow['code'];
			$contact[$i]['Card_Address'] = $aRow['Card_Address'];
			$contact[$i]['Card_State'] = $aRow['Card_State'];
			$contact[$i]['Card_City'] = $aRow['Card_City'];
			$contact[$i]['Card_Type'] = $aRow['Card_Type'];
			$contact[$i]['Card_Name'] = $aRow['Card_Name'];
			$contact[$i]['Card_Number'] = $aRow['Card_No'];
			
			$contact[$i]['DeliveryAddress'] = $aRow['DeliveryAddress'];
			$contact[$i]['contactAddress'] = $aRow['contactAddress'];
			$contact[$i]['RES_CITY'] = $aRow['RES_CITY'];
			$contact[$i]['RES_STATE'] = $aRow['RES_STATE'];
			$contact[$i]['Redeem'] = $aRow['Redeem'];
			$contact[$i]['street'] = $aRow['street'];
			$contact[$i]['C_CITY'] = $aRow['C_CITY'];
			$contact[$i]['C_STATE'] = $aRow['C_STATE'];
			$contact[$i]['postalCode'] = $aRow['postalCode'];
			$contact[$i]['PaymentDesc'] = $aRow['PaymentDesc'];
			$contact[$i]['points_earned'] = $aRow['points_earned'];
			$i++;
		}		
	return array($contact,$TotalRecordCount);
	}
	function GetAReservation($TxnID)
	{
		GLOBAL $con;
		if($TxnID!='')
		{
			$query="SELECT orders.id AS Order_ID,merchant.logo,tbl_registeration.firstname,tbl_registeration.display_name,tbl_registeration.email_add,orders.*,merchant.merchantName,merchant.contactAddress,merchant.city AS RES_CITY,merchant.state AS RES_STATE,tbl_registeration.street,tbl_registeration.city AS C_CITY,tbl_registeration.state AS C_STATE,merchant.postalCode,merchant.telephone,orders.PaymentDesc FROM orders
			INNER JOIN tbl_registeration ON tbl_registeration.id=orders.customerID
			INNER JOIN merchant ON merchant.id=orders.merchantID 			
			 WHERE orders.id=".$TxnID; 
			
		}
		
		$res = mysqli_query($con, $query);
		$TotalRecordCount=mysqli_num_rows($res);
		$contact = array();
		$i=0;
		while($aRow=mysqli_fetch_array($res))
		{	
			$contact[$i]['id'] = $aRow['Order_ID'];
			$contact[$i]['merchantID']=$aRow['merchantID'];
			$contact[$i]['merchantName']=$aRow['merchantName'];
			$contact[$i]['orderDate']=$aRow['orderDate'];
			$contact[$i]['orderTime']=$aRow['orderTime'];
			$contact[$i]['orderPlacedOn']=$aRow['orderPlacedOn'];
			$contact[$i]['orderType']=$aRow['orderType'];
			$contact[$i]['logo']=$aRow['logo'];
			$contact[$i]['orderAmount']=$aRow['orderAmount'];
			$contact[$i]['orderStatus']=$aRow['orderStatus'];
			$contact[$i]['paymentStatus']=$aRow['paymentStatus'];
			$contact[$i]['orderCompleted']=$aRow['orderCompleted'];
			$contact[$i]['DeliveryAddress']=$aRow['DeliveryAddress'];	
			$contact[$i]['paymentType']=$aRow['paymentType'];
			$contact[$i]['orderTaxAmount']=$aRow['orderTaxAmount'];
			$contact[$i]['deliveryFee']=$aRow['deliveryFee'];
			$contact[$i]['customerID'] = $aRow['customerID'];
			$contact[$i]['firstname'] = $aRow['firstname'];
			$contact[$i]['display_name'] = $aRow['display_name'];
			$contact[$i]['lastname'] = $aRow['lastname'];
			$contact[$i]['email_add'] = $aRow['email_add'];
			$contact[$i]['comments'] = $aRow['comments'];
			$contact[$i]['contactAddress'] = $aRow['contactAddress'];
			$contact[$i]['RES_CITY'] = $aRow['RES_CITY'];
			$contact[$i]['RES_STATE'] = $aRow['RES_STATE'];
			$contact[$i]['postalCode'] = $aRow['postalCode'];
			$contact[$i]['telephone'] = $aRow['telephone'];
			$contact[$i]['street'] = $aRow['street'];
			$contact[$i]['C_CITY'] = $aRow['C_CITY'];
			$contact[$i]['C_STATE'] = $aRow['C_STATE'];
			$contact[$i]['code'] = $aRow['code'];
			$contact[$i]['PaymentDesc'] = $aRow['PaymentDesc'];
			$contact[$i]['points_earned'] = $aRow['points_earned'];
			$i++;
		}		
	return array($contact,$TotalRecordCount);
	}
	function GetOrderItems($order_ID)
	{
		GLOBAL $con;
		$sql="SELECT order_items.*,items.itemName,items.Cus_Pizza,orders.PaymentDesc,menus.menuName FROM order_items 
		INNER JOIN items ON items.id=order_items.itemID 
		INNER JOIN orders ON orders.id=order_items.orderID 
		INNER JOIN menus ON menus.id=items.menuID 
		WHERE orderID=".$order_ID;
		$exe=mysqli_query($con, $sql);
		$num=mysqli_num_rows($exe);
		$i=0;
		$record=array();
		while($rec=mysqli_fetch_array($exe))
		{
			$record[$i]['id']	=	$rec['id'];
			$record[$i]['itemID']	=	$rec['itemID'];
			$record[$i]['itemName']	=	$rec['itemName'];
			$record[$i]['menuName']	=	$rec['menuName'];
			$record[$i]['quantity']	=	$rec['quantity'];
			$record[$i]['price']	=	$rec['price'];
			$record[$i]['size']	=	$rec['size'];
			$record[$i]['notes']=	$rec['notes'];
			$record[$i]['crust']	=	$rec['crust'];
			$record[$i]['Cus_Pizza']=	$rec['Cus_Pizza'];
			$record[$i]['PaymentDesc']	=	$rec['PaymentDesc'];
			$record[$i]['Size']	=	$rec['size'];
			$i++;
		}
		return array($record,$num);
	}
	function GetOrderSubItems($orderItemID)
	{
		GLOBAL $con;
		$sql="SELECT * FROM order_subitems WHERE orderItemID=".$orderItemID;
		$exe=mysqli_query($con, $sql);
		$num=mysqli_num_rows($exe);
		$i=0;
		$record=array();
		while($rec=mysqli_fetch_array($exe))
		{
			$record[$i]['subItemID']		=	$rec['subItemID'];
			$record[$i]['subItemName']		=	$rec['subItemName'];
			$record[$i]['subItemPrice']		=	$rec['subItemPrice'];
			$record[$i]['subitem_value']		=	$rec['subitem_value'];
			$record[$i]['subgroup_x2']		=	$rec['subgroup_x2'];
			$i++;
		}
		return array($record,$num);
	}
	function view_coupons() {
			GLOBAL $con;
			$sql="Select * from tbl_coupons WHERE Coupon_UserId=".$_SESSION['User_ID']." AND Coupon_Restaurant=".mysqli_real_escape_string($con,$_REQUEST['Res_ID'])." order by Coupon_Name ASC";
			$result = mysqli_query($con, $sql);
			$TotalRecordCount=mysqli_num_rows($result);
			$Totalpages 		= mysqli_query($con, "SELECT * FROM `tbl_control`");
			$exec_paging		= mysqli_fetch_array($Totalpages);
			$end_count 			= $exec_paging['no_of_pages'];
			$Limits				= $exec_paging['results_per_page'];
			$Limits = $Limits;
			$Page = $_REQUEST['page'];
			if($Page==""){
				$Page=1;
			}
			$pageCount = ceil($TotalRecordCount/$Limits); 
			$StartLimit = (($Page-1)*$Limits);
			if($TotalRecordCount > ($Page*$Limits)){
				$EndLimit = $Page*$Limits;
			}else{
				$EndLimit = $TotalRecordCount;
			}
			$sql1=" LIMIT ".$StartLimit.",".$Limits;
			$query=$sql.$sql1;
			$Records=array();
			$i=1;
			$Res=mysqli_query($con, $query);
			$Record	=	array();
			$i			=	0;
			while($res=mysqli_fetch_array($Res)) {
				$Record[$i]['Coupon_ID']		=$res['Coupon_ID'];
				$Record[$i]['Coupon_Name']=$res['Coupon_Name'];
				$Record[$i]['Coupon_Expiry']=$res['Coupon_Expiry'];
				$Record[$i]['Coupon_Amount']=$res['Coupon_Amount'];
				$Record[$i]['Coupon_Redeem']=$res['Coupon_Redeem'];
				$Record[$i]['Coupon_Limit']=$res['Coupon_Limit'];
				$Record[$i]['Coupon_Created']=$res['Coupon_Created'];
				$Record[$i]['Coupon_Status']=$res['Coupon_Status'];
				$Record[$i]['Coupon_Type']=$res['Coupon_Type'];
				$i++;
			}
		return array($Record,$TotalRecordCount);
	}
	function Add_coupon()
	{
		GLOBAL $con;
		if($_REQUEST['Coupon_ID']>0)
		{
			$sql="UPDATE tbl_coupons SET Coupon_Name='".mysqli_real_escape_string($con,$_REQUEST['Coupon_Name'])."',Coupon_Expiry='".date("Y-m-d",strtotime($_REQUEST['Coupon_Expiry']))." 00:00:00"."',Coupon_Type='".mysqli_real_escape_string($con,$_REQUEST['Coupon_Type'])."',Coupon_Amount='".mysqli_real_escape_string($con,$_REQUEST['Coupon_Amount'])."',Coupon_Limit='".$_REQUEST['Coupon_Limit']."',Coupon_Redeem='".$_REQUEST['Coupon_Redeem']."',Coupon_Desc='".$_REQUEST['Coupon_Desc']."' WHERE Coupon_ID=".$_REQUEST['Coupon_ID'];
			mysqli_query($con, $sql);
			$sql="UPDATE merchant SET coupons_expiry='".date("Y-m-d",strtotime($_REQUEST['Coupon_Expiry']))." 00:00:00"."' WHERE id=".$_REQUEST['Res_ID'];
			mysqli_query($con, $sql);
		}
		else
		{
			$sql = "INSERT INTO tbl_coupons (Coupon_Name,Coupon_Expiry,Coupon_Type,Coupon_Amount,Coupon_Limit,Coupon_Redeem,Coupon_UserId,Coupon_Restaurant,Coupon_Desc,Coupon_Status,Coupon_Created) VALUES ('".mysqli_real_escape_string($con,$_REQUEST['Coupon_Name'])."','".date("Y-m-d",strtotime($_REQUEST['Coupon_Expiry']))." 00:00:00"."','".$_REQUEST['Coupon_Type']."','".mysqli_real_escape_string($con,$_REQUEST['Coupon_Amount'])."','".mysqli_real_escape_string($con,$_REQUEST['Coupon_Limit'])."','".mysqli_real_escape_string($con,$_REQUEST['Coupon_Redeem'])."',".$_SESSION["User_ID"].",".mysqli_real_escape_string($con,$_REQUEST['Res_ID']).",'".mysqli_real_escape_string($con,$_REQUEST['Coupon_Desc'])."','1',Now())";
			mysqli_query($con, $sql);
			$query="SELECT id,coupons FROM merchant WHERE id=".$_REQUEST['Res_ID'];
			$exe=mysqli_query($con, $query);
			$res=mysqli_fetch_array($exe);
			$CNT=$res['coupons'];
			$CNT++;
			$sql="UPDATE merchant SET coupons=".$CNT.",coupons_expiry='".date("Y-m-d",strtotime($_REQUEST['Coupon_Expiry']))." 00:00:00"."', WHERE id=".$_REQUEST['Res_ID'];
			mysqli_query($con, $sql);
		}
				
		echo '<script language="javascript">location.href="dine.php?act=coupons&Res_ID='.$_REQUEST['Res_ID'].'&rep=1";</script>'; 
		exit;
	}
	function Delete_Coupon()
	{
		GLOBAL $con;
		$qry="DELETE FROM tbl_coupons WHERE Coupon_ID=".mysqli_real_escape_string($con,$_REQUEST['Coupon_ID']);
	    mysqli_query($con, $qry);
		
		// coupons cnt in merchant
		$query="SELECT id,coupons FROM merchant WHERE id=".mysqli_real_escape_string($con,$_REQUEST['Res_ID']);
		$exe=mysqli_query($con, $query);
		$res=mysqli_fetch_array($exe);
		$CNT=$res['coupons'];
		$CNT--;
		if($CNT<0)	
			$CNT=0;
		$sql="UPDATE merchant SET coupons=".$CNT." WHERE id=".mysqli_real_escape_string($con,$_REQUEST['Res_ID']);
		mysqli_query($con, $sql);
	    echo '<script language="javascript">location.href="dine.php?act=coupons&Res_ID='.$_REQUEST['Res_ID'].'";</script>'; 
		exit;
	}
	function Coupon_Status()
	{
		GLOBAL $con;
	   $qry="UPDATE tbl_coupons SET Coupon_Status=".mysqli_real_escape_string($con,$_GET[status])." WHERE Coupon_ID=".mysqli_real_escape_string($con,$_REQUEST['Coupon_ID']); 
	   mysqli_query($con, $qry);
	   // coupons cnt in merchant
	   $query="SELECT id,coupons FROM merchant WHERE id=".mysqli_real_escape_string($con,$_REQUEST['Res_ID']);
	   $exe=mysqli_query($con, $query);
	   $res=mysqli_fetch_array($exe);
	   $CNT=$res['coupons'];
	   if($_GET[status]==0)
		$CNT--;
	   else
		$CNT++;
	   	if($CNT<0)	
			$CNT=0;
		$sql="UPDATE merchant SET coupons=".$CNT." WHERE id=".mysqli_real_escape_string($con,$_REQUEST['Res_ID']);
		
		mysqli_query($con, $sql);
    }
   
   function Update_OrderStatus()
   {
	   GLOBAL $con;
	   $Order_ID=mysqli_real_escape_string($con,$_REQUEST['Order_ID']);
	   $orderStatus=mysqli_real_escape_string($con,$_REQUEST['orderStatus']);
	   $i=0;
		foreach($Order_ID as $Order_IDs)
		{
			$qry="UPDATE orders SET orderStatus='".$orderStatus[$i]."' WHERE id=".$Order_IDs; 
			mysqli_query($con, $qry);
			$i++;
		}
		
		echo '<script language="javascript">location.href="dine.php?act=reser";</script>'; 
		exit;
	   
   }
   function GetCoupons($rest)
	{
		GLOBAL $con;
		$dt = date('Y-m-d H:i:s');
		$sql="SELECT * FROM tbl_coupons WHERE Coupon_Status=1 AND Coupon_Restaurant=".$rest." AND Coupon_Expiry>='".$dt."'";
		$exe=mysqli_query($con, $sql);
		$num=mysqli_num_rows($exe);
		$i=0;
		$record=array();
		while($rec=mysqli_fetch_array($exe))
		{
			$record[$i]['Coupon_ID']		=	$rec['Coupon_ID'];
			$record[$i]['Coupon_Name']		=	$rec['Coupon_Name'];
			$record[$i]['Coupon_Type']		=	$rec['Coupon_Type'];
			$record[$i]['Coupon_Amount']	=	$rec['Coupon_Amount'];
			$i++;
		}
		return array($record,$num);
	}
	function GetTotalpoints()
	{
		GLOBAL $con;
		$sql = "SELECT Points FROM tbl_registeration WHERE id=".$_SESSION['User_ID'];
		$res = @mysqli_query($con, $sql);
		$row = @mysqli_fetch_array($res);
		if($row['Points']>0)
			return $row['Points'];
		else
			return 0;
	}
	function GetDiscount($coupon)
	{
		GLOBAL $con;
		$sql="SELECT * FROM tbl_coupons WHERE Coupon_ID=".$coupon;
		$exe=mysqli_query($con, $sql);
		$CC=mysqli_fetch_array($exe);
		if($CC['Coupon_Type']=="%") {
			$discount=($order_total*$CC['Coupon_Amount'])/100;
		}
		else {
			$discount=$CC['Coupon_Amount'];
		}
		return $discount;
	}
	function Restaurant_Open($id,$week)
	{
		GLOBAL $con;
		$sql="SELECT closed FROM merchant_hours WHERE merchantID=".$id." AND weekDay=".$week;
		$exe=mysqli_query($con, $sql);
		$CC=mysqli_fetch_array($exe);
		return $CC['closed'];
	}
	function Restaurant_OpenHours($id,$week,$orderfor)
	{
		GLOBAL $con;
		if($orderfor=='Delivery')
			$sql="SELECT deliveryStartTime AS ST,deliveryEndTime AS ET,deliveryStartTimeOther AS STO,deliveryEndTimeOther AS ETO FROM merchant_hours WHERE merchantID=".$id." AND weekDay=".$week;
		else
			$sql="SELECT pickupStartTime AS ST,pickupEndTime AS ET,pickupStartTimeOther AS STO,pickupEndTimeOther AS ETO FROM merchant_hours WHERE merchantID=".$id." AND weekDay=".$week;
		//echo $sql;
		$exe=mysqli_query($con, $sql);
		$CC=mysqli_fetch_array($exe);
		if($CC['ST']==$CC['ET']) {
			$ST="";$ET=""; } 
		else {$ST=$CC['ST'];$ET=$CC['ET']; }
		if($CC['STO']==$CC['ETO']) {
			$STO="";$ETO=""; } 
		else {$STO=$CC['STO'];$ETO=$CC['ETO']; }
		return array($ST,$ET,$STO,$ETO);
	}
    function Restaurant_OpenHours_allday($id,$orderfor)
	{
		GLOBAL $con;
		if($orderfor=='Delivery')
			$sql="SELECT deliveryStartTime AS ST,deliveryEndTime AS ET,deliveryStartTimeOther AS STO,deliveryEndTimeOther AS ETO FROM merchant_hours WHERE merchantID=".$id;
		else
			$sql="SELECT weekDay,pickupStartTime AS ST,pickupEndTime AS ET,pickupStartTimeOther AS STO,pickupEndTimeOther AS ETO FROM merchant_hours WHERE merchantID=".$id;
		//echo $sql;
		$exe=mysqli_query($con, $sql);
		while($CC=mysqli_fetch_array($exe)){
		if($CC['ST']==$CC['ET']) {
			$ST="";$ET=""; } 
		else {$ST=$CC['ST'];$ET=$CC['ET']; }
		if($CC['STO']==$CC['ETO']) {
			$STO="";$ETO=""; } 
		else {$STO=$CC['STO'];$ETO=$CC['ETO']; }
        $array[$CC['weekDay']]= array($ST,$ET,$STO,$ETO);
       }
		return $array;
	}
	function sendFax($orderID = 0) {

	GLOBAL $con;
        //$orderInfo = getOrderDetails($orderID);

        //$info = getMerchantDetails($orderInfo->merchantID);

        //$custInfo = getCustomerInformation($orderInfo->customerID);

		$faxNumber="+914442047660";

        //if ($orderInfo) {
            $data['order'] = $orderInfo;
            $data['merchant'] = $info;
            //$data['items'] = getOrderItems($orderID);
            $data['orderID'] = $orderID;
            $data['custInfo'] = $custInfo;
            $this->load->helper("file");
            $invoicex = $this->load->view("merchantorderfax", $data, true);
            write_file('./orders/order_' . $orderID . '.txt', $invoicex, 'w+');


            $content = $this->load->view("merchantorderhtmlfax", $data, true);


                $this->load->library('pdf');
                // set font
                //$this->pdf->SetFont('times', '', 10);
                //set auto page breaks
                $this->pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
                // add a page
                $this->pdf->AddPage();
                $this->pdf->writeHTML($content, true, false, true, false, '');
                $this->pdf->Output('./orders/order_' . $orderID . '.pdf', 'F');
                
                sendOrderFax($faxNumber, $orderID);
            
       // }
    }
	/*function sendSMS($orderID = 0)
    {
        require_once('twilio.php');
       
	$ApiVersion = $this->config->item("ApiVersion");
	
	$AccountSid = $this->config->item("AccountSid");
	$AuthToken = $this->config->item("AuthToken");
        $fromSMSNumber = $this->config->item("fromSMSNumber");

        $orderInfo = getOrderDetails($orderID);
        $info = getMerchantDetails($orderInfo->merchantID);
        $custInfo = getCustomerInformation($orderInfo->customerID);
        $text = "An order of amount $".$orderInfo->orderAmount." and order id ".$orderInfo->id." has been placed at ".date("m/d/Y", strtotime($orderInfo->orderDate))." ".$orderInfo->orderTime;
        $mobileNumber = $info->gsmNumber;

        $client = new TwilioRestClient($AccountSid, $AuthToken);

	

		$response = $client->request("/$ApiVersion/Accounts/$AccountSid/SMS/Messages",
			"POST", array(
			"To" => $mobileNumber,
			"From" => $fromSMSNumber,
			"Body" => $text
		));

               

		
    }
*/
    function voiceCall($orderID = 0)
    {
		GLOBAL $con;
		GLOBAL $version,$sid,$token,$phonenumber;
		$_SESSION['WAY_ORDER']=$orderID;
		require 'Services/Twilio.php';
        $client = new Services_Twilio($sid, $token, $version);
		try {
			// Initiate a new outbound call
			//$to=4082500375;
			$call = $client->account->calls->create(
				$phonenumber, // The number of the phone initiating the call
				'+919962614221', // The number of the phone receiving call
				'http://way.com/dine/handle-incoming-call.php?orderID='.$orderID // The URL Twilio will request when the call is answered
			);
			//echo 'Started call: ' . $call->sid;
		} catch (Exception $e) {
			echo 'Error: ' . $e->getMessage();
			exit;
		}
		
       // $orderInfo = getOrderDetails($orderID);
       // $info = getMerchantDetails($orderInfo->merchantID);
       // $custInfo = getCustomerInformation($orderInfo->customerID);
        //$text = "An order of amount $".$orderInfo->orderAmount." and order id ".$orderInfo->id." has been placed at ".date("m/d/Y", strtotime($orderInfo->orderDate))." ".$orderInfo->orderTime." from Way.com";
		$text = "An Test order of has been placed from Way.com";
    }

	public function handle_call($orderID = 0)
	{
		GLOBAL $con;
        require_once('twilio.php');

		if($orderID)
		{
			//$orderInfo = getOrderDetails($orderID);

			//$script = "An order of amount $".$orderInfo->orderAmount." and order id ".$orderInfo->id." has been placed at ".date("m/d/Y", strtotime($orderInfo->orderDate))." ".$orderInfo->orderTime." from Ebids.com .";
			$script = "An order of has been placed from way.com .";

		$twilio = new Response();
		$twilio->addSay($script, array('loop' => 1));
		$twilio->Respond();
		}

	}	
	function MakeMenuStatus()
	{
		GLOBAL $con;
		$qry="UPDATE menus SET menuStatus='".$_REQUEST['menu_status']."' WHERE id=".mysqli_real_escape_string($con,$_REQUEST['Menu_ID']);
		mysqli_query($con, $qry);
	}
	function MakeRestaurantStatus()
	{
		GLOBAL $con;
		$sql="UPDATE merchant SET status='".$_REQUEST['res_status']."' WHERE id=".$_REQUEST['id'];
		
		mysqli_query($con, $sql);
	}
	function MakeMenuItemStatus()
	{
		GLOBAL $con;
		$sql="UPDATE items SET itemStatus='".$_REQUEST['item_status']."' WHERE id=".$_REQUEST['id'];
		
		mysqli_query($con, $sql);
	}
	function Traffic_Status()
	 {
		 GLOBAL $con;
		  $sql="Select merchant.* from merchant WHERE Deleted=0 AND Res_UserID=".$_SESSION[User_ID]." order by createdOn desc"; 
		  $result = mysqli_query($con, $sql) or die(mysqli_error($con));
		  $TotalRecordCount=mysqli_num_rows($result);
	
			$Totalpages 		= mysqli_query($con, "SELECT * FROM `tbl_control`");
			$exec_paging		= mysqli_fetch_array($Totalpages);
			$end_count 			= $exec_paging['no_of_pages'];
			$Limits				= $exec_paging['results_per_page'];
			$Limits = $Limits;
			$Page = $_REQUEST['page'];
			if($Page==""){
				$Page=1;
			}
			$pageCount = ceil($TotalRecordCount/$Limits); 
			$StartLimit = (($Page-1)*$Limits);
			if($TotalRecordCount > ($Page*$Limits)){
				$EndLimit = $Page*$Limits;
			}else{
				$EndLimit = $TotalRecordCount;
			}
			$sql1=" LIMIT ".$StartLimit.",".$Limits;
			$query=$sql.$sql1;
			$Records=array();
			$i=1;
			$Res=mysqli_query($con, $query) or die (mysqli_error($con));
			$Record	=	array();
			$i			=	0;
			while($res=mysqli_fetch_array($Res)) {
				$Record[$i]['id']		=$res['id'];
				$Record[$i]['P_UserID']=$res['Res_UserID'];
				$Record[$i]['views']	=$res['views'];
				$Record[$i]['merchantName']	=$res['merchantName'];
				$Record[$i]['cdate']	=$res['createdOn'];
				$Record[$i]['clicks']	=$res['clicks'];
				$i++;
			}
		return array($Record,$TotalRecordCount);
	}
	function VerifyReedeemCode()
	{
		GLOBAL $con;
		$sql="SELECT code FROM orders WHERE code='".mysqli_real_escape_string($con,$_REQUEST['R_Code'])."' AND id=".mysqli_real_escape_string($con,$_REQUEST['TxnID']);
		$exe=mysqli_query($con, $sql);
		$num=mysqli_num_rows($exe);
		if($num>0)
			return 1;
		else
			return 0;
	}
	function VerifyReedeemCodetablebooking()
	{
		GLOBAL $con;
		$sql="SELECT code FROM tbl_tablebooking_bookings WHERE code='".mysqli_real_escape_string($con,$_REQUEST['R_Code'])."' AND Book_ID=".mysqli_real_escape_string($con,$_REQUEST['TxnID']);
		$exe=mysqli_query($con, $sql);
		$num=mysqli_num_rows($exe);
		if($num>0)
			return 1;
		else
			return 0;
	}
	function UpdateParkingReedeemtablebooking()
	{
		GLOBAL $con;
		$sql="SELECT code FROM tbl_tablebooking_bookings WHERE code='".mysqli_real_escape_string($con,$_REQUEST['R_Code'])."' AND Book_ID=".mysqli_real_escape_string($con,$_REQUEST['TxnID']);
		
		$exe=mysqli_query($con, $sql);
		$num=mysqli_num_rows($exe);
		$res=mysqli_fetch_array($exe);
		if($num>0)
		{
			if($res['orderType']=='TakeOut')
				$orderStatus="Picked Up";
			else
				$orderStatus="Delivered";
			$sql="UPDATE tbl_tablebooking_bookings SET Redeem=1 WHERE Book_ID=".mysqli_real_escape_string($con,$_REQUEST['TxnID']);
			mysqli_query($con, $sql);
			
			return 1;
		}
		else
		{
			return 0;
		}
	}
	function UpdateParkingReedeem()
	{
		GLOBAL $con;
		$sql="SELECT code FROM orders WHERE code='".mysqli_real_escape_string($con,$_REQUEST['R_Code'])."' AND id=".mysqli_real_escape_string($con,$_REQUEST['TxnID']);
		$exe=mysqli_query($con, $sql);
		$num=mysqli_num_rows($exe);
		$res=mysqli_fetch_array($exe);
		if($num>0)
		{
			if($res['orderType']=='TakeOut')
				$orderStatus="Picked Up";
			else
				$orderStatus="Delivered";
			$sql="UPDATE orders SET Redeem=1,orderCompleted='Yes',orderStatus='".$orderStatus."' WHERE id=".mysqli_real_escape_string($con,$_REQUEST['TxnID']);
			mysqli_query($con, $sql);
			
			return 1;
		}
		else
		{
			return 0;
		}
	}
	function Updatepaystatus()
	{
		GLOBAL $con;
		GLOBAL $reply_mail,$adminmail;
		if($_REQUEST['DeliveryMethod']=='Electronic Ticket')
		{
		$sql="UPDATE tbl_paymenttransaction SET OrderStatus='Completed',DeliveryEmail='".$_REQUEST['Email']."' WHERE TxnID=".mysqli_real_escape_string($con,$_REQUEST['TxnID']);
		// Send Attachment
		$upload_image_to_folder='../admin/Temp/';
		$file = $_FILES['image_file'];
		$file_name = $file['name'];
		$ext = strtolower(substr(strrchr($file_name, "."), 1));
		$google_icon_file = strtolower($file_name);
	    $google_icon_file = str_replace(' ', '-', $google_icon_file);
	    $google_icon_file = substr($google_icon_file, 0, -(strlen($ext)+1));
	    $google_icon_file .= strtotime(date("F j, Y, g:i a")).".".$extension;
		move_uploaded_file($file['tmp_name'], $upload_image_to_folder.$google_icon_file.$ext);
		$image_path=$upload_image_to_folder.$google_icon_file.$ext;
		$to =	 $_REQUEST['Email'];
$subject =	 'Electronic Ticket from way.com';
$bound_text =	"way.com";
$bound =	"--".$bound_text."\r\n";
$bound_last =	"--".$bound_text."--\r\n";
 	 
$headers =	"From: $adminmail\r\n";
$headers .=	"MIME-Version: 1.0\r\n"
 	."Content-Type: multipart/mixed; boundary=\"$bound_text\"";
 	 
$message .=	"If you can see this MIME than your client doesn't accept MIME types!\r\n"
 	.$bound;
 	 
$message .=	"Content-Type: text/html; charset=\"iso-8859-1\"\r\n"
 	."Content-Transfer-Encoding: 7bit\r\n\r\n"
 	."Attached electronic Ticket\r\n"
 	.$bound;
 	 
$file =	file_get_contents($image_path);
 	 
$message .=	"Content-Type: image/$ext; name=\"$google_icon_file.$ext\"\r\n"
 	."Content-Transfer-Encoding: base64\r\n"
 	."Content-disposition: attachment; file=\"$google_icon_file.$ext\"\r\n"
 	."\r\n"
 	.chunk_split(base64_encode($file))
 	.$bound_last;
		mail($to, $subject, $message, $headers); 

		}
		
		if($_REQUEST['orderType']=='TakeOut')
			$orderStatus="Picked Up";
		else
			$orderStatus="Delivered";
		$sql="UPDATE orders SET orderStatus='".$orderStatus."',comments='".mysqli_real_escape_string($con,$_REQUEST['Desc'])."' WHERE id=".mysqli_real_escape_string($con,$_REQUEST['TxnID']);
		
				
		mysqli_query($con, $sql);
		return 1;
	}
	function CheckDeliveryDetails($User_ID,$Email,$street,$city,$state,$zipcode)
		{
			GLOBAL $con;
			$sql="SELECT DE_EmailID FROM tbl_deliveryEmail WHERE DE_EmailID='".$Email."' AND DE_UserID=".$User_ID;
			$exe=mysqli_query($con, $sql);
			$num=mysqli_num_rows($exe);
			if($num<1)
			{
				$sql="INSERT INTO tbl_deliveryEmail(DE_UserID,DE_EmailID,DE_Created)VALUES(".$User_ID.",'".$Email."',Now())";
				mysqli_query($con, $sql);
			}
			$sql="SELECT DE_Address FROM tbl_deliveryaddress WHERE DE_Address='".$street."' AND DE_Zipcode='".$zipcode."' AND DE_State='".$state."' AND DE_City='".$city."' AND DE_UserID=".$User_ID;
			$exe=mysqli_query($con, $sql);
			$num=mysqli_num_rows($exe);
			if($num<1)
			{
				$sql="INSERT INTO tbl_deliveryaddress(DE_UserID,DE_Address,DE_State,DE_City,DE_Zipcode,DE_Created)VALUES(".$User_ID.",'".mysqli_real_escape_string($con,$street)."','".mysqli_real_escape_string($con,$state)."','".mysqli_real_escape_string($con,$city)."','".mysqli_real_escape_string($con,$zipcode)."',Now())";
				mysqli_query($con, $sql);
			}
		
		}
	
	function GetDeliveryZipcodes($merchant)
	{
		GLOBAL $con;
		$sql="SELECT * FROM deliveryzipcode_fees WHERE merchantID=".$merchant;
		$exe=mysqli_query($con, $sql);
		$num=mysqli_num_rows($exe);
		$Fee = array();
		$i=0;
		while ($result=mysqli_fetch_array($exe))
		{
			$Fee[$i]['postalCode'] = $result['postalCode'];
			$Fee[$i]['fees'] = $result['fees'];
			$Fee[$i]['minFee'] = $result['minFee'];
			$i++;
		}
		return array($Fee,$num);
	}
	function GetDeliveryAddress($User_ID)
	{
		GLOBAL $con;
		$sql="SELECT * FROM tbl_deliveryaddress WHERE DE_UserID=".$User_ID;
		$exe=mysqli_query($con, $sql);
		$num=mysqli_num_rows($exe);
		$cart = array();
		$i=0;
		while ($result=mysqli_fetch_array($exe))
		{
			$cart[$i]['DE_ID'] = $result['DE_ID'];
			$cart[$i]['DE_Name'] = $result['DE_Name']; //Changed by LogicTree
			$cart[$i]['DE_Address'] = $result['DE_Address'];
			$cart[$i]['DE_State'] = $result['DE_State'];
			$cart[$i]['DE_City'] = $result['DE_City'];
			$cart[$i]['DE_Zipcode'] = $result['DE_Zipcode'];
			$i++;
		}
		return array($cart,$num);
	}
	function View_DineReviews() {
		GLOBAL $con;
			$sql="SELECT * FROM tbl_reviews WHERE list_id=".mysqli_real_escape_string($con,$_REQUEST['id'])." AND R_Type='Dine' ";
			if($_REQUEST['sort']=='dateasc')
				$sql.="ORDER BY Date_Created ASC"; 
			else if($_REQUEST['sort']=='ratingasc')
				$sql.="ORDER BY txt0 ASC";
			else if($_REQUEST['sort']=='ratingdesc')
				$sql.="ORDER BY txt0 DESC";
			else if($_REQUEST['sort']=='helpdesc')
				$sql.="ORDER BY help_cnt DESC";
			else if($_REQUEST['sort']=='helpasc')
				$sql.="ORDER BY help_cnt ASC";
			else
				$sql.="order by Date_Created DESC";
			
			$result = mysqli_query($con, $sql) or die(mysqli_error($con));
			$TotalRecordCount=mysqli_num_rows($result);
			$Totalpages 		= mysqli_query($con, "SELECT * FROM `tbl_control`");
			$exec_paging		= mysqli_fetch_array($Totalpages);
			$end_count 			= $exec_paging['no_of_pages'];
			$Limits				= $exec_paging['results_per_page'];
			$Limits = $Limits;
			$Page = $_REQUEST['page'];
			if($Page=="" || $Page=="undefined"){
				$Page=1;
			}
			$pageCount = ceil($TotalRecordCount/$Limits); 
			$StartLimit = (($Page-1)*$Limits);
			if($TotalRecordCount > ($Page*$Limits)){
				$EndLimit = $Page*$Limits;
			}else{
				$EndLimit = $TotalRecordCount;
			}
			$sql1=" LIMIT ".$StartLimit.",".$Limits;
			$query=$sql.$sql1;
			$Records=array();
			$i=1;
			$Res=mysqli_query($con, $query) or die (mysqli_error($con));
			$Record	=	array();
			$i			=	0;
			while($res=mysqli_fetch_array($Res)) {
					$Record[$i]['Review_ID']		=$res['Review_ID'];
					$Record[$i]['user_id']=$res['user_id'];
					$Record[$i]['R_Type']=$res['R_Type'];
					$Record[$i]['txt0']=$res['txt0'];
					$Record[$i]['txt1']=$res['txt1'];
					$Record[$i]['txt2']=$res['txt2'];
					$Record[$i]['txt3']=$res['txt3'];
					$Record[$i]['txt4']=$res['txt4'];
					$Record[$i]['txt5']=$res['txt5'];
					if($res['R_Type']=='Dine')
						$Record[$i]['category']=72;
					else if($res['R_Type']=='Parking')
						$Record[$i]['category']=45;
					else if($res['R_Type']=='Activities')
						$Record[$i]['category']=64;
					$Record[$i]['list_id']		=$res['list_id'];
					$Record[$i]['Review']	=$res['Review'];
					$Record[$i]['Date_Created']	=$res['Date_Created'];
					$Record[$i]['status']	=	$res['status'];
				$i++;
			}
		return array($Record,$TotalRecordCount);
	}
	function AddHelpfull()
	{
		GLOBAL $con;
		//help_cnt
		$sql="SELECT help_cnt FROM tbl_reviews WHERE Review_ID=".mysqli_real_escape_string($con,$_REQUEST['Review_ID']);
		$exe=mysqli_query($con, $sql);
		$res=mysqli_fetch_array($exe);
		if($res['help_cnt']>0)
			$help_cnt=$res['help_cnt'];
		else
			$help_cnt=0;
		$help_cnt++;
		if($SESSION['User_ID']>0)
			$User_ID=$SESSION['User_ID'];
		else
			$User_ID=0;
		$sql="UPDATE tbl_reviews SET help_cnt=".$help_cnt." WHERE Review_ID=".mysqli_real_escape_string($con,$_REQUEST['Review_ID']);
		mysqli_query($con, $sql);
		$sql="INSERT INTO tbl_reviewhelpful(Help_Review,Help_Restaurant,Help_UserID,Help_Created)VALUES(".mysqli_real_escape_string($con,$_REQUEST['Review_ID']).",'".mysqli_real_escape_string($con,$_REQUEST['ID'])."','".$User_ID."',Now())";
		mysqli_query($con, $sql);
	}
	function AddLike()
	{
		GLOBAL $con;
		$sql="INSERT INTO tbl_like(Like_Restaurant,Like_User,Like_Created)VALUES('".mysqli_real_escape_string($con,$_REQUEST['ID'])."','".$_SESSION['User_ID']."',Now())";
		mysqli_query($con, $sql);
	}
	function GetCuisineImages()
	{ 
		GLOBAL $con;
		$sql="Select * from tbl_cuisine WHERE Cuisine_Status=1 AND Cuisine_Image<>'' ORDER BY Cuisine_Name ASC";
		$result = mysqli_query($con, $sql);
		$TotalRecordCount=mysqli_num_rows($result);
		$i=0;
		$Record	=array();
		while($res=mysqli_fetch_array($result)) {
			$Record[$i]['Cuisine_ID']=$res['Cuisine_ID'];
			$Record[$i]['Cuisine_Name']=$res['Cuisine_Name'];
			$Record[$i]['Cuisine_Image']=$res['Cuisine_Image'];
			$i++;
		}
		return array($Record,$TotalRecordCount);
	}
	function GetBrowseRestbycity()
	{
		GLOBAL $con;
		$latlong=GetLatandLang($_SESSION['State'],$_SESSION['City']);
		
		$lat=$latlong[0];
		$long=$latlong[1];
		if($lat=='' || $long=='')
		{
			$where = stripslashes($loc);
			$whereurl = urlencode($where);
			$geocode=file_get_contents('https://maps.google.com/maps/api/geocode/json?address='.$whereurl.'&sensor=false');
			$output= json_decode($geocode);
			$lat = $output->results[0]->geometry->location->lat;
			$long = $output->results[0]->geometry->location->lng;
		}
		$sql_lat_col="merchant.geoLat";
		$sql_long_col="merchant.geoLong";
		
		// Check with Expire Records
		$dt2 = date('Y-m-d H:i:s');
		$Weekday=idate("w");
		$today=time();
		$current_time = date("H:i:s",strtotime($dt2));
		GLOBAL $Time_Zone;
		
		$sql="Select (SELECT count(*) FROM merchant_hours AS INR_TBL WHERE INR_TBL.merchantID=merchant.id AND INR_TBL.weekDay=MH.weekDay AND ('".$current_time.".+((merchant.timezone-merchant.timezone)*60*60)' BETWEEN INR_TBL.startTime AND INR_TBL.endTime OR '".$current_time."' BETWEEN INR_TBL.startTimeOther AND INR_TBL.endTimeOther)) AS OPEN_STATUS,MH.closed,3956 * 2 * ASIN(SQRT(POWER(SIN(($lat - abs($sql_lat_col)) * pi()/180 / 2), 2) + COS($lat * pi()/180 ) * COS(abs($sql_lat_col) * pi()/180) * POWER(SIN(($long-$sql_long_col) * pi()/180 / 2), 2) )) as distance,merchant.*,COUNT(merchant.id) AS NUM FROM merchant
		INNER JOIN tbl_registeration ON tbl_registeration.id= merchant.Res_UserID
		INNER JOIN merchant_hours AS MH ON MH.merchantID= merchant.id
		WHERE merchant.status='Active' AND tbl_registeration.dine_control=1 AND MH.weekDay=".$Weekday;
		$sql.=" GROUP BY merchant.city ORDER BY distance ASC LIMIT 12";
		//echo $sql;
		$result = mysqli_query($con, $sql);
		$TotalRecordCount=mysqli_num_rows($result);
		$i=0;
		$Record	=array();
		while($res=mysqli_fetch_array($result)) {
			$Record[$i]['id']=$res['id'];
			$Record[$i]['merchantName']=$res['merchantName'];
			$Record[$i]['state']=$res['state'];
			$Record[$i]['city']=$res['city'];
			$Record[$i]['NUM']=$res['NUM'];
			$i++;
		}
		return array($Record,$TotalRecordCount);
	}
	function GetCuisines($limit)
	{
		GLOBAL $con;
		$sql="Select * from tbl_cuisine WHERE Cuisine_Status=1 ORDER BY RAND()";
		if($limit>0)
			$sql.=" LIMIT ".$limit;
		$result = mysqli_query($con, $sql);
		$TotalRecordCount=mysqli_num_rows($result);
		$i=0;
		$Record	=array();
		while($res=mysqli_fetch_array($result)) {
			$Record[$i]['Cuisine_ID']=$res['Cuisine_ID'];
			$Record[$i]['Cuisine_Name']=$res['Cuisine_Name'];
			$Record[$i]['Cuisine_Image']=$res['Cuisine_Image'];
			$i++;
		}
		return array($Record,$TotalRecordCount);
	}
}
?>