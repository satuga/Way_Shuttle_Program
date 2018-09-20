<?php
class Dine
{
	function RequestApps($id,$type)
	{
		$up = "UPDATE tbl_registeration SET ".$type."_control=2 WHERE id=".$id; 
		$res = mysql_query($up);
		
		print "<script language=javascript>window.location='index.php';</script>";
		exit;
	}
	function view_Restaurants() {
			$sql="Select * from merchant WHERE Deleted=0 AND (Res_UserID=".$_SESSION['User_ID']." OR Res_AssignUserID=".$_SESSION['User_ID'].")";
			if($_REQUEST['Keyword']<>'')
			$sql.=" AND (merchantName LIKE '%".mysql_real_escape_string($_REQUEST['Keyword'])."%' OR merchantName LIKE '%".mysql_real_escape_string(strtoupper($_REQUEST['Keyword']))."%' OR merchantName LIKE '%".mysql_real_escape_string(strtolower($_REQUEST['Keyword']))."%' OR merchantName LIKE '%".mysql_real_escape_string(ucwords($_REQUEST['Keyword']))."%' OR merchantName LIKE '%".mysql_real_escape_string(ucfirst($_REQUEST['Keyword']))."%' OR contactName LIKE '%".mysql_real_escape_string($_REQUEST['Keyword'])."%' OR contactAddress LIKE '%".mysql_real_escape_string($_REQUEST['Keyword'])."%' OR crossStreet LIKE '%".mysql_real_escape_string($_REQUEST['Keyword'])."%')";
			$sql.=" order by id desc";
			$result = mysql_query($sql);
			$TotalRecordCount=mysql_num_rows($result);
			$Totalpages 		= mysql_query("SELECT * FROM `tbl_control`");
			$exec_paging		= mysql_fetch_array($Totalpages);
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
			$Res=mysql_query($query);
			$Record	=	array();
			$i			=	0;
			while($res=mysql_fetch_array($Res)) {
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
		$State=mysql_real_escape_string($_REQUEST['State']);
		$City=mysql_real_escape_string($_REQUEST['City']);
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
			$sql="UPDATE merchant SET merchantName='".mysql_real_escape_string($_REQUEST['merchantname'])."',contactName='".mysql_real_escape_string($_REQUEST['contactname'])."',cuisine='".$cuisines."',state='".trim($State)."',city='".$City."',contactAddress='".mysql_real_escape_string($_REQUEST['streetaddress'])."',crossStreet='".mysql_real_escape_string($_REQUEST['crossstreets'])."',postalCode='".mysql_real_escape_string($_REQUEST['Zip_Code'])."',telephone='".mysql_real_escape_string($_REQUEST['phonenumber'])."',faxNumber='".mysql_real_escape_string($_REQUEST['faxnumber'])."',gsmNumber='".mysql_real_escape_string($_REQUEST['gsmnumber'])."',email='".mysql_real_escape_string($_REQUEST['email'])."',delivery='".mysql_real_escape_string($_REQUEST['deliveryavailable'])."',takeout='".mysql_real_escape_string($_REQUEST['takeoutavailable'])."',tablebooking='".mysql_real_escape_string($_REQUEST['tablebooking'])."',phoneorderonly='".mysql_real_escape_string($_REQUEST['phoneorderonly'])."',payment_cash='".mysql_real_escape_string($_REQUEST['payment_cash'])."',payment_cc='".mysql_real_escape_string($_REQUEST['payment_cc'])."',Neighborhood='".mysql_real_escape_string($_REQUEST['Neighborhood'])."',executive_chef='".mysql_real_escape_string($_REQUEST['executive_chef'])."',dress_code='".mysql_real_escape_string($_REQUEST['dress_code'])."',additional_service='".mysql_real_escape_string($_REQUEST['additional_service'])."',public_transit='".mysql_real_escape_string($_REQUEST['public_transit'])."',parking='".mysql_real_escape_string($_REQUEST['parking'])."',private_party_facility='".mysql_real_escape_string($_REQUEST['private_party_facility'])."',private_party_contact='".mysql_real_escape_string($_REQUEST['private_party_contact'])."',catering='".mysql_real_escape_string($_REQUEST['catering'])."',aboutUs='".mysql_real_escape_string($_REQUEST['aboutus'])."',dining_style='".mysql_real_escape_string($_REQUEST['dining_style'])."',promotions='".mysql_real_escape_string($_REQUEST['promotions'])."',entertainment='".mysql_real_escape_string($_REQUEST['entertainment'])."',timezone='".mysql_real_escape_string($_REQUEST['timezone'])."',geoLong='".$long."',geoLat='".$lat."' WHERE id=".mysql_real_escape_string($_REQUEST['ID']);
			$rep=2;
			$rec=mysql_query($sql); 
			$Merchant_ID=$_REQUEST['ID'];
		}
		else
		{
			$sql="INSERT INTO merchant(Res_UserID,merchantName,contactName,state,city,contactAddress,cuisine,crossStreet,postalCode,telephone,faxNumber,gsmNumber,email,status,delivery,takeout,tablebooking,phoneorderonly,payment_cash,payment_cc,createdOn,Neighborhood,executive_chef,dress_code,additional_service,public_transit,parking,private_party_facility,private_party_contact,catering,aboutUs,dining_style,promotions,entertainment,timezone,geoLong,geoLat) VALUES('".$_SESSION['User_ID']."','".mysql_real_escape_string($_REQUEST['merchantname'])."','".mysql_real_escape_string($_REQUEST['contactname'])."','".trim($State)."','".$City."','".mysql_real_escape_string($_REQUEST['streetaddress'])."','".$cuisines."','".mysql_real_escape_string($_REQUEST['crossstreets'])."','".mysql_real_escape_string($_REQUEST['Zip_Code'])."','".mysql_real_escape_string($_REQUEST['phonenumber'])."','".mysql_real_escape_string($_REQUEST['faxnumber'])."','".mysql_real_escape_string($_REQUEST['gsmnumber'])."','".mysql_real_escape_string($_REQUEST['email'])."','1','".mysql_real_escape_string($_REQUEST['deliveryavailable'])."','".mysql_real_escape_string($_REQUEST['takeoutavailable'])."','".mysql_real_escape_string($_REQUEST['tablebooking'])."','".mysql_real_escape_string($_REQUEST['phoneorderonly'])."','".mysql_real_escape_string($_REQUEST['payment_cash'])."','".mysql_real_escape_string($_REQUEST['payment_cc'])."',Now(),'".mysql_real_escape_string($_REQUEST['Neighborhood'])."','".mysql_real_escape_string($_REQUEST['executive_chef'])."','".mysql_real_escape_string($_REQUEST['dress_code'])."','".mysql_real_escape_string($_REQUEST['additional_service'])."','".mysql_real_escape_string($_REQUEST['public_transit'])."','".mysql_real_escape_string($_REQUEST['parking'])."','".mysql_real_escape_string($_REQUEST['private_party_facility'])."','".mysql_real_escape_string($_REQUEST['private_party_contact'])."','".mysql_real_escape_string($_REQUEST['catering'])."','".mysql_real_escape_string($_REQUEST['aboutus'])."','".mysql_real_escape_string($_REQUEST['dining_style'])."','".mysql_real_escape_string($_REQUEST['promotions'])."','".mysql_real_escape_string($_REQUEST['entertainment'])."','".mysql_real_escape_string($_REQUEST['timezone'])."','".$long."','".$lat."')";
			$rep=1;
			$rec=mysql_query($sql); 
			$Merchant_ID=mysql_insert_id($rec);
		}
		//echo $sql;exit;
		$sql="DELETE FROM merchant_cuisine WHERE merchantID=".$Merchant_ID;
		mysql_query($sql);
		
		$Cuisines=explode(",",$cuisines);
		$cuisinecnt=0;
		foreach($Cuisines as $u=>$v)
		{
			if($v<>'')
			{
			$sql="INSERT INTO merchant_cuisine(merchantID,cuisineID) VALUES(".$Merchant_ID.",".$v.")";
			mysql_query($sql);
			}
		}
		if($cuisinecnt==0)
		{
		$sql="INSERT INTO merchant_cuisine(merchantID,cuisineID) VALUES(".$Merchant_ID.",0)";
			mysql_query($sql);
		}
		
			echo '<script language="javascript">location.href="dine.php?act=my_restaurants&rep='.$rep.'";</script>'; 
		exit;
	}
	
	function add_item()
	{
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
			//  Racks Space implementation
			include("../rackspaceconfig.php");
			
			$objstore = $connection->objectStoreService('cloudFiles', 'IAD');

			// 3. Get container.
			$container = $objstore->getContainer('dine');

			// 4. Upload an object to the container.
			$localFileName  = $upload_image_to_folder.$new_file_name;
			$remoteFileName = $new_file_name;

			$fileData = fopen($localFileName, 'r');
			$container->uploadObject($remoteFileName, $fileData);
			unlink($upload_image_to_folder.$new_file_name);
			// End Racks Space implementation
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
			$sql="UPDATE items SET itemName='".mysql_real_escape_string($_REQUEST['itemname'])."',itemImage='".$new_file_name."',
			itemDescription='".mysql_real_escape_string($_REQUEST['description'])."',
			itemStatus='".$_REQUEST['status']."',popular='".$popular."',spicy='".$spicy."',
			veggie='".$veggie."',chef_special='".$chef_special."',itemPrice='".mysql_real_escape_string($price1)."',
			itemPrice1='".mysql_real_escape_string($price2)."',itemPrice2='".mysql_real_escape_string($price3)."',
			itemPrice3='".mysql_real_escape_string($price4)."',itemPrice4='".mysql_real_escape_string($price5)."',
			itemPrice5='".mysql_real_escape_string($price6)."',itemPrice6='".mysql_real_escape_string($price7)."',
			itemPrice7='".mysql_real_escape_string($price8)."',itemPrice8='".mysql_real_escape_string($price9)."',
			itemPrice9='".mysql_real_escape_string($price10)."',
			itemPrice10='".mysql_real_escape_string($price11)."',
			itemPrice11='".mysql_real_escape_string($price12)."',
			itemPrice12='".mysql_real_escape_string($price13)."',
			itemPrice13='".mysql_real_escape_string($price14)."',
			itemPrice14='".mysql_real_escape_string($price15)."',
			itemPrice15='".mysql_real_escape_string($price16)."',
			itemPrice16='".mysql_real_escape_string($price17)."',
			itemPrice17='".mysql_real_escape_string($price18)."',
			itemPrice18='".mysql_real_escape_string($price19)."',
			itemPrice19='".mysql_real_escape_string($price20)."',
			Cus_Pizza='".mysql_real_escape_string($Cus_Pizza)."',
			Multiple_Toppings='".mysql_real_escape_string($Multiple_Toppings)."',
			Free_Toppings='".mysql_real_escape_string($Free_Toppings)."',
			Max_Toppings='".mysql_real_escape_string($Max_Toppings)."',lastUpdatedOn=Now() WHERE id=".mysql_real_escape_string($_REQUEST['Item_ID']);
			
			$rec=mysql_query($sql); 
			$Item_ID=$_REQUEST['Item_ID'];
		}
		else
		{
			$sql="INSERT INTO items(menuID,itemName,itemImage,itemDescription,itemStatus,popular,spicy,veggie,chef_special,itemPrice,itemPrice1,itemPrice2,itemPrice3,itemPrice4,itemPrice5,itemPrice6,itemPrice7,itemPrice8,itemPrice9,Cus_Pizza,Multiple_Toppings,Free_Toppings,Max_Toppings,lastUpdatedOn) VALUES('".mysql_real_escape_string($_REQUEST['Menu'])."','".mysql_real_escape_string($_REQUEST['itemname'])."','".$new_file_name."','".mysql_real_escape_string($_REQUEST['description'])."','".$_REQUEST['status']."','".$popular."','".$spicy."','".$veggie."','".$chef_special."','".mysql_real_escape_string($price1)."','".mysql_real_escape_string($price2)."','".mysql_real_escape_string($price3)."','".mysql_real_escape_string($price4)."','".mysql_real_escape_string($price5)."','".mysql_real_escape_string($price6)."','".mysql_real_escape_string($price7)."','".mysql_real_escape_string($price8)."','".mysql_real_escape_string($price9)."','".mysql_real_escape_string($price10)."','".$Cus_Pizza."','".$Multiple_Toppings."','".$Max_Toppings."','".$Free_Toppings."',Now())";
			$rec=mysql_query($sql); 
			$Item_ID=mysql_insert_id();
		}
		//echo $sql;exit;
		
		$sql="DELETE FROM subitems WHERE itemID=".$Item_ID;
		mysql_query($sql);
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
			$sql="INSERT INTO subitems(itemID,subgroup_id,subitemName,subitemPrice,Sel,status,lastUpdatedOn,sortOrder) VALUES(".$Item_ID.",'".mysql_real_escape_string($subgroup[$i])."','".mysql_real_escape_string(str_replace("\"", "&quot;",$suboption[$i]))."','".mysql_real_escape_string($suboptionprice[$i])."',".$_REQUEST['suboptionsel'.$pp].",'Active',Now(),".$orderid.")";
			mysql_query($sql);
			}
			$pp++;
		}
		
		// Update MAX & MIN Price for Merchant Table
		$sql="SELECT DISTINCT(id) AS MENU FROM menus WHERE merchantID=".$_REQUEST['Res_ID'];
		$exe=mysql_query($sql);
		$menus="";
		while($res=mysql_fetch_array($exe))
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
		$exe=mysql_query($sql);
		$res=mysql_fetch_array($exe);
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
		mysql_query($sql);
		
		// Groups
		echo '<script language="javascript">location.href="dine.php?act=menu_item&Res_ID='.$_REQUEST['Res_ID'].'&Menu='.$_REQUEST['Menu'].'&rep=1";</script>'; 
		exit;
	}
	function duplicate_item()
	{
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
			
			
		
			$sql="INSERT INTO items(menuID,itemName,itemImage,itemDescription,itemStatus,popular,spicy,veggie,chef_special,itemPrice,itemPrice1,itemPrice2,itemPrice3,itemPrice4,itemPrice5,itemPrice6,itemPrice7,itemPrice8,itemPrice9,itemPrice10,itemPrice11,itemPrice12,itemPrice13,itemPrice14,itemPrice15,itemPrice16,itemPrice17,itemPrice18,itemPrice19,Cus_Pizza,Multiple_Toppings,Free_Toppings,Max_Toppings,lastUpdatedOn) VALUES('".mysql_real_escape_string($_REQUEST['Menu'])."','".mysql_real_escape_string($_REQUEST['itemname'])."','".$new_file_name."','".mysql_real_escape_string($_REQUEST['description'])."','".$_REQUEST['status']."','".$popular."','".$spicy."','".$veggie."','".$chef_special."','".mysql_real_escape_string($price1)."','".mysql_real_escape_string($price2)."','".mysql_real_escape_string($price3)."','".mysql_real_escape_string($price4)."','".mysql_real_escape_string($price5)."','".mysql_real_escape_string($price6)."','".mysql_real_escape_string($price7)."','".mysql_real_escape_string($price8)."','".mysql_real_escape_string($price9)."','".mysql_real_escape_string($price10)."','".mysql_real_escape_string($price11)."','".mysql_real_escape_string($price12)."','".mysql_real_escape_string($price13)."','".mysql_real_escape_string($price14)."','".mysql_real_escape_string($price15)."','".mysql_real_escape_string($price16)."','".mysql_real_escape_string($price17)."','".mysql_real_escape_string($price18)."','".mysql_real_escape_string($price19)."','".mysql_real_escape_string($price20)."','".$Cus_Pizza."','".$Multiple_Toppings."','".$Max_Toppings."','".$Free_Toppings."',Now())";
			
			$rec=mysql_query($sql); 
			$Item_ID=mysql_insert_id();
		
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
			$sql="INSERT INTO subitems(itemID,subgroup_id,subitemName,subitemPrice,Sel,status,lastUpdatedOn,sortOrder) VALUES(".$Item_ID.",'".mysql_real_escape_string($subgroup[$i])."','".mysql_real_escape_string(str_replace("\"", "&quot;",$suboption[$i]))."','".mysql_real_escape_string($suboptionprice[$i])."',".$_REQUEST['suboptionsel'.$pp].",'Active',Now(),".$orderid.")";
			mysql_query($sql);
			}
			$pp++;
		}
		//exit;
		// Groups
			echo '<script language="javascript">location.href="dine.php?act=menu_item&Res_ID='.$_REQUEST['Res_ID'].'&Menu='.$_REQUEST['Menu'].'&rep=1";</script>'; 
		exit;
	}
	function view_menus() {
			$sql="SELECT * from menus WHERE Deleted=0 AND merchantID=".mysql_real_escape_string($_REQUEST['Res_ID']);
			if($_REQUEST['Keyword']<>'')
			$sql.=" AND (menuName LIKE '%".mysql_real_escape_string($_REQUEST['Keyword'])."%' OR menuDescription LIKE '%".mysql_real_escape_string($_REQUEST['Keyword'])."%')";
			$sql.=" ORDER BY menuName ASC";
			$result = mysql_query($sql);
			$TotalRecordCount=mysql_num_rows($result);
			$Totalpages 		= mysql_query("SELECT * FROM `tbl_control`");
			$exec_paging		= mysql_fetch_array($Totalpages);
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
			$Res=mysql_query($query);
			$Record	=	array();
			$i			=	0;
			while($res=mysql_fetch_array($Res)) {
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
			$sql="Select * from menus WHERE Deleted=0 AND id=".$menu." order by menuName ASC";
			$result = mysql_query($sql);
			$TotalRecordCount=mysql_num_rows($result);
			$Totalpages 		= mysql_query("SELECT * FROM `tbl_control`");
			$exec_paging		= mysql_fetch_array($Totalpages);
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
			$Res=mysql_query($query);
			$Record	=	array();
			$i			=	0;
			while($res=mysql_fetch_array($Res)) {
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
			$sql="Select * from items WHERE Deleted=0 AND menuID=".mysql_real_escape_string($_REQUEST['Menu']);
			if($_REQUEST['Keyword']<>'')
			$sql.=" AND (itemName LIKE '%".mysql_real_escape_string($_REQUEST['Keyword'])."%' OR itemDescription LIKE '%".mysql_real_escape_string($_REQUEST['Keyword'])."%')";
			$sql.=" order by itemName ASC";
			$result = mysql_query($sql);
			$TotalRecordCount=mysql_num_rows($result);
			$Totalpages 		= mysql_query("SELECT * FROM `tbl_control`");
			$exec_paging		= mysql_fetch_array($Totalpages);
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
			$Res=mysql_query($query);
			$Record	=	array();
			$i			=	0;
			while($res=mysql_fetch_array($Res)) {
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
			$sql="Select * from menus WHERE Deleted=0 AND id=".$id." order by menuName ASC";
			$result = mysql_query($sql);
			$TotalRecordCount=mysql_num_rows($result);
			$i=0;
			$Record	=	array();
			while($res=mysql_fetch_array($result)) {
				$Record[$i]['id']		=$res['id'];
				$Record[$i]['menuName']=$res['menuName'];
				$Record[$i]['menuDescription']=$res['menuDescription'];
				$i++;
			}
		return array($Record,$TotalRecordCount);
	}
	function GetSubgroups($id) {
			$sql="Select * from subgroups WHERE merchant_id=".$id." order by subgroup_name ASC";
			$result = mysql_query($sql);
			$TotalRecordCount=mysql_num_rows($result);
			$i=0;
			$Record	=	array();
			while($res=mysql_fetch_array($result)) {
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
			$sql="Select subitems.*,subgroups.subgroup_name,subgroups.id AS GID,subitems.id AS SID from subitems INNER JOIN subgroups ON subgroups.id=subitems.subgroup_id WHERE itemID=".$id." order by subgroup_id,subitemName ASC";
			$result = mysql_query($sql);
			$TotalRecordCount=mysql_num_rows($result);
			$i=0;
			$Record	=	array();
			while($res=mysql_fetch_array($result)) {
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
			$sql="Select subgroup_id,subgroup_value from tbl_cartsubitems WHERE Cart_ID=".$id;
			$result = mysql_query($sql);
			$TotalRecordCount=mysql_num_rows($result);
			$i=0;
			$Record	=	array();
			while($res=mysql_fetch_array($result)) {
				$Record[$i]['subgroup_id']		=$res['subgroup_id'];
				$Record[$i]['subgroup_value']		=$res['subgroup_value'];
				$i++;
			}
		return array($Record,$TotalRecordCount);
	}
	function GetSubItemsGroupname($id) {
			$sql="Select subitems.*,subgroups.subgroup_name,subgroups.id AS GID,subitems.id AS SID,subgroups.* from subitems INNER JOIN subgroups ON subgroups.id=subitems.subgroup_id WHERE itemID=".$id." GROUP BY subgroup_id order by subgroup_name ASC";
			$result = mysql_query($sql);
			$TotalRecordCount=mysql_num_rows($result);
			$i=0;
			$Record	=	array();
			while($res=mysql_fetch_array($result)) {
				$Record[$i]['id']		=$res['SID'];
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
			$sql="Select subitems.*,subgroups.subgroup_name,subgroups.id AS GID,subitems.id AS SID from subitems INNER JOIN subgroups ON subgroups.id=subitems.subgroup_id WHERE itemID=".$id." AND subgroup_id=".$groupid." order by subitemName ASC";
			$result = mysql_query($sql);
			$TotalRecordCount=mysql_num_rows($result);
			$i=0;
			$Record	=	array();
			while($res=mysql_fetch_array($result)) {
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
		$sql="Select * from delivery_fees WHERE merchantID=".mysql_real_escape_string($_REQUEST['ID'])." order by postalCode ASC";
		$result = mysql_query($sql);
		$TotalRecordCount=mysql_num_rows($result);
		$i=0;
		$Record	=array();
		while($res=mysql_fetch_array($result)) {
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
		$sql="Select * from deliveryzipcode_fees WHERE merchantID=".mysql_real_escape_string($_REQUEST['ID'])." order by postalCode ASC";
		$result = mysql_query($sql);
		$TotalRecordCount=mysql_num_rows($result);
		$i=0;
		$Record	=array();
		while($res=mysql_fetch_array($result)) {
			$Record[$i]['id']=$res['id'];
			$Record[$i]['postalCode']=$res['postalCode'];
			$Record[$i]['fees']=$res['fees'];
			$Record[$i]['minFee']=$res['minFee'];
			$i++;
		}
		return array($Record,$TotalRecordCount);
	}
	function GeGetOpenCloseHours() {
			 $sql="Select * from merchant_hours WHERE merchantID=".mysql_real_escape_string($_REQUEST['ID'])." order by id ASC";
			$result = mysql_query($sql);
			$TotalRecordCount=mysql_num_rows($result);
			$i=0;
			$Record	=	array();
			while($res=mysql_fetch_array($result)) {
				$Record[$i]['id']		=$res['id'];
				$Record[$i]['closed']		=$res['closed'];
				$Record[$i]['24hours']		=$res['24hours'];
				$Record[$i]['deliveryClosed']		=$res['deliveryClosed'];
				$Record[$i]['delivery24hours']		=$res['delivery24hours'];
				$Record[$i]['pickupClosed']		=$res['pickupClosed'];
				$Record[$i]['pickup24hours']	=$res['pickup24hours'];
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
			$sql="UPDATE menus SET menuName='".mysql_real_escape_string($_REQUEST['menuname'])."',Main_Menu='".mysql_real_escape_string($_REQUEST['Main_Menu'])."',parentMenu='".mysql_real_escape_string($parentmenu)."',menuDescription='".mysql_real_escape_string($_REQUEST['description'])."',Start_Time='".$_REQUEST['ST1']."',End_Time='".$_REQUEST['ET1']."',menuStatus='".$_REQUEST['status']."',menuSize1='".mysql_real_escape_string($_REQUEST['size1'])."',menuSize2='".mysql_real_escape_string($_REQUEST['size2'])."',menuSize3='".mysql_real_escape_string($_REQUEST['size3'])."',menuSize4='".mysql_real_escape_string($_REQUEST['size4'])."',menuSize5='".mysql_real_escape_string($_REQUEST['size5'])."',menuSize6='".mysql_real_escape_string($_REQUEST['size6'])."',menuSize7='".mysql_real_escape_string($_REQUEST['size7'])."',menuSize8='".mysql_real_escape_string($_REQUEST['size8'])."',menuSize9='".mysql_real_escape_string($_REQUEST['size9'])."',
			menuSize10='".mysql_real_escape_string($_REQUEST['size10'])."',
			menuSize11='".mysql_real_escape_string($_REQUEST['size11'])."',
			menuSize12='".mysql_real_escape_string($_REQUEST['size12'])."',
			menuSize13='".mysql_real_escape_string($_REQUEST['size13'])."',
			menuSize14='".mysql_real_escape_string($_REQUEST['size14'])."',
			menuSize15='".mysql_real_escape_string($_REQUEST['size15'])."',
			menuSize16='".mysql_real_escape_string($_REQUEST['size16'])."',
			menuSize17='".mysql_real_escape_string($_REQUEST['size17'])."',
			menuSize18='".mysql_real_escape_string($_REQUEST['size18'])."',
			menuSize19='".mysql_real_escape_string($_REQUEST['size19'])."',
			menuSize20='".mysql_real_escape_string($_REQUEST['size20'])."',
			menuLang1='".mysql_real_escape_string($_REQUEST['lang1'])."',menuLang2='".mysql_real_escape_string($_REQUEST['lang2'])."',menuLang3='".mysql_real_escape_string($_REQUEST['lang3'])."',pizza='".$_REQUEST['pizza']."',menuOrder='".$menuOrder."',partyMenu='".$party."' WHERE id=".$_REQUEST['Menu_ID'];
			
		}
		else
		{
			$sql="INSERT INTO menus(merchantID,Main_Menu,parentMenu,menuName,menuDescription,Start_Time,End_Time,menuStatus,menuSize1,menuSize2,menuSize3,menuSize4,menuSize5,menuSize6,menuSize7,menuSize8,menuSize9,menuSize10,menuSize11,menuSize12,menuSize13,menuSize14,menuSize15,menuSize16,menuSize17,menuSize18,menuSize19,menuSize20,createdOn,menuLang1,menuLang2,menuLang3,pizza,partyMenu,menuOrder) VALUES('".$_REQUEST['Res_ID']."','".mysql_real_escape_string($_REQUEST['Main_Menu'])."','".mysql_real_escape_string($parentmenu)."','".mysql_real_escape_string($_REQUEST['menuname'])."','".mysql_real_escape_string($_REQUEST['description'])."','".$_REQUEST['ST1']."','".$_REQUEST['ET1']."','".$_REQUEST['status']."','".mysql_real_escape_string($_REQUEST['size1'])."','".mysql_real_escape_string($_REQUEST['size2'])."','".mysql_real_escape_string($_REQUEST['size3'])."','".mysql_real_escape_string($_REQUEST['size4'])."','".mysql_real_escape_string($_REQUEST['size5'])."','".mysql_real_escape_string($_REQUEST['size6'])."','".mysql_real_escape_string($_REQUEST['size7'])."',
			'".mysql_real_escape_string($_REQUEST['size8'])."',
			'".mysql_real_escape_string($_REQUEST['size9'])."',
			'".mysql_real_escape_string($_REQUEST['size10'])."',
			'".mysql_real_escape_string($_REQUEST['size11'])."',
			'".mysql_real_escape_string($_REQUEST['size12'])."',
			'".mysql_real_escape_string($_REQUEST['size13'])."',
			'".mysql_real_escape_string($_REQUEST['size14'])."',
			'".mysql_real_escape_string($_REQUEST['size15'])."',
			'".mysql_real_escape_string($_REQUEST['size16'])."',
			'".mysql_real_escape_string($_REQUEST['size17'])."',
			'".mysql_real_escape_string($_REQUEST['size18'])."',
			'".mysql_real_escape_string($_REQUEST['size19'])."',
			'".mysql_real_escape_string($_REQUEST['size20'])."',
			Now(),'".mysql_real_escape_string($_REQUEST['lang1'])."','".mysql_real_escape_string($_REQUEST['lang2'])."','".mysql_real_escape_string($_REQUEST['lang3'])."','".$_REQUEST['pizza']."','".$party."','".$menuOrder."')";
			
		}
		
		$rec=mysql_query($sql); 
		$menunames=$_REQUEST['menunames'];
		//echo $sql;exit;
		for($i=0;$i<COUNT($menunames);$i+=1)
		{
			
			if($menunames[$i]<>'') {
			$sql="INSERT INTO menus(merchantID,Main_Menu,parentMenu,menuName,menuDescription,Start_Time,End_Time,menuStatus,menuSize1,menuSize2,menuSize3,menuSize4,menuSize5,menuSize6,menuSize7,menuSize8,menuSize9,menuSize10,menuSize11,menuSize12,menuSize13,menuSize14,menuSize15,menuSize16,menuSize17,menuSize18,menuSize19,menuSize20,createdOn,menuLang1,menuLang2,menuLang3,pizza,partyMenu) VALUES('".$_REQUEST['Res_ID']."','".mysql_real_escape_string($_REQUEST['Main_Menu'])."','".mysql_real_escape_string($parentmenu)."','".mysql_real_escape_string($menunames[$i])."','".mysql_real_escape_string($_REQUEST['description'])."','".$_REQUEST['ST1']."','".$_REQUEST['ET1']."','".$_REQUEST['status']."','".mysql_real_escape_string($_REQUEST['size1'])."','".mysql_real_escape_string($_REQUEST['size2'])."','".mysql_real_escape_string($_REQUEST['size3'])."','".mysql_real_escape_string($_REQUEST['size4'])."','".mysql_real_escape_string($_REQUEST['size5'])."','".mysql_real_escape_string($_REQUEST['size6'])."','".mysql_real_escape_string($_REQUEST['size7'])."','".mysql_real_escape_string($_REQUEST['size8'])."','".mysql_real_escape_string($_REQUEST['size9'])."','".mysql_real_escape_string($_REQUEST['size10'])."',
			'".mysql_real_escape_string($_REQUEST['size8'])."',
			'".mysql_real_escape_string($_REQUEST['size9'])."',
			'".mysql_real_escape_string($_REQUEST['size10'])."',
			'".mysql_real_escape_string($_REQUEST['size11'])."',
			'".mysql_real_escape_string($_REQUEST['size12'])."',
			'".mysql_real_escape_string($_REQUEST['size13'])."',
			'".mysql_real_escape_string($_REQUEST['size14'])."',
			'".mysql_real_escape_string($_REQUEST['size15'])."',
			'".mysql_real_escape_string($_REQUEST['size16'])."',
			'".mysql_real_escape_string($_REQUEST['size17'])."',
			'".mysql_real_escape_string($_REQUEST['size18'])."',
			'".mysql_real_escape_string($_REQUEST['size19'])."',
			'".mysql_real_escape_string($_REQUEST['size20'])."',
			Now(),'".mysql_real_escape_string($_REQUEST['lang1'])."','".mysql_real_escape_string($_REQUEST['lang2'])."','".mysql_real_escape_string($_REQUEST['lang3'])."','".$_REQUEST['pizza']."','".$party."')";
			
			mysql_query($sql);
			}
		}
			echo '<script language="javascript">location.href="dine.php?act=my_menus&Res_ID='.$_REQUEST['Res_ID'].'&rep=1";</script>'; 
		exit;
	}
	function Settings_Restaurant()
	{
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
			$sql="UPDATE merchant SET logo='".$new_file_name."' WHERE id=".mysql_real_escape_string($_REQUEST['ID']);
			mysql_query($sql);
		}
		if($mapiconerror=='') {
			$sql="UPDATE merchant SET google_icon='".$google_icon_file."' WHERE id=".mysql_real_escape_string($_REQUEST['ID']);
			mysql_query($sql);
		}
		if($_REQUEST['showTax']=='')
			$showtax="No";
		else
			$showtax=mysql_real_escape_string($_REQUEST['showTax']);
		if($_REQUEST['tax']=='')
			$tax=0;
		else
			$tax=mysql_real_escape_string($_REQUEST['tax']);
		if($_REQUEST['minimumamount']=='')
			$minimumamount=0;
		else
			$minimumamount=mysql_real_escape_string($_REQUEST['minimumamount']);
		if($_REQUEST['deliveryfee']=='')
			$deliveryfee=0;
		else
			$deliveryfee=mysql_real_escape_string($_REQUEST['deliveryfee']);
		if($_REQUEST['deliverymiles']=='')
			$deliverymiles=0;
		else
			$deliverymiles=mysql_real_escape_string($_REQUEST['deliverymiles']);
		if($_REQUEST['deliverymiles']=='')
			$deliverymiles=0;
		else
			$deliverymiles=mysql_real_escape_string($_REQUEST['deliverymiles']);
		$sql="UPDATE merchant SET minimumDeliveryAmount='".$minimumamount."',deliveryFee='".$deliveryfee."',deliverymiles='".$deliverymiles."',deliveryareas='".mysql_real_escape_string($_REQUEST['deliveryareas'])."',deliveryzipcodes='".mysql_real_escape_string($_REQUEST['deliveryzipcodes'])."',delivery_basedon='".mysql_real_escape_string($_REQUEST['delivery_basedon'])."',deliveryWaitTime='".mysql_real_escape_string($_REQUEST['deliverywaittime'])."',takeout_time='".mysql_real_escape_string($_REQUEST['takeout_time'])."',showTax='".$showtax."',tax='".$tax."' WHERE id=".mysql_real_escape_string($_REQUEST['ID']);
		
		mysql_query($sql);
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
					$sql="UPDATE subgroups SET subgroup_name='".mysql_real_escape_string($subgroups[$i])."',required=".$subgroups_required[$i].",multiple=".$subgroups_multiple[$i]." WHERE id=".$sbids[$i];
					mysql_query($sql);
					$subids.=",".$sbids[$i];
				}
				else
				{
					$sql="INSERT INTO subgroups(merchant_id,subgroup_name,required,multiple) VALUES(".mysql_real_escape_string($_REQUEST['ID']).",'".mysql_real_escape_string($subgroups[$i])."',".$subgroups_required[$i].",".$subgroups_multiple[$i].")";
					mysql_query($sql);
					$insid=mysql_insert_id();
					$subids.=",".$insid;
				}
			}
		}
		$subids=trim($subids,",");
		$sql="DELETE FROM subgroups WHERE merchant_id=".mysql_real_escape_string($_REQUEST['ID'])." AND id NOT IN (".$subids.")";
		mysql_query($sql);
		}
		
		//End Sub Groups
		
		// Fees based on Miles
		$sql="DELETE FROM delivery_fees WHERE merchantID=".mysql_real_escape_string($_REQUEST['ID']);
		mysql_query($sql);
	
		$zonalfees_postcode=$_REQUEST['zonalfees_postcode'];
		
		$zonalfees_charge=$_REQUEST['zonalfees_charge'];
		$zonalfees_minfee=$_REQUEST['zonalfees_minfee'];
		
		for($i=0;$i<COUNT($zonalfees_postcode);$i+=1)
		{
			//".$zonalfees_minfee[$i];
			if($zonalfees_postcode[$i]<>'') {
			$sql="INSERT INTO delivery_fees(merchantID,postalCode,fees,minFee) VALUES(".mysql_real_escape_string($_REQUEST['ID']).",'".$zonalfees_postcode[$i]."',".$zonalfees_charge[$i].",0)";
			mysql_query($sql);}
		}
		
		// Fees based on Zipcodes
		$sql="DELETE FROM deliveryzipcode_fees WHERE merchantID=".mysql_real_escape_string($_REQUEST['ID']);
		mysql_query($sql);
	
		$zonalfees_zipcode=$_REQUEST['zonalfees_zipcode'];
		$zonalfees_zipcodecharge=$_REQUEST['zonalfees_zipcodecharge'];
		
		for($i=0;$i<COUNT($zonalfees_zipcode);$i+=1)
		{
			if($zonalfees_zipcode[$i]<>'') {
			$sql="INSERT INTO deliveryzipcode_fees(merchantID,postalCode,fees) VALUES(".mysql_real_escape_string($_REQUEST['ID']).",'".$zonalfees_zipcode[$i]."',".$zonalfees_zipcodecharge[$i].")";
			mysql_query($sql);}
		}
		
		// Open close time
		$sql="DELETE FROM merchant_hours WHERE merchantID=".mysql_real_escape_string($_REQUEST['ID']);
		mysql_query($sql);
	
		$bussinessclose=$_REQUEST['bussinessclose'];
		$bussiness24hours=$_REQUEST['bussiness24hours'];
		$delivery24hours=$_REQUEST['delivery24hours'];
		$pickup24hours=$_REQUEST['pickup24hours'];
		
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
			if($bussiness24hours[$i]==1)
				$bussiness_24hours="Yes";
			else
				$bussiness_24hours="No";
			if($deliveryclosed[$i]==1)
				$deliveryclose="Yes";
			else
				$deliveryclose="No";
			
			if($delivery24hours[$i]==1)
				$delivery_24hours="Yes";
			else
				$delivery_24hours="No";
			
			if($pickupclosed[$i]==1)
				$pickupclose="Yes";
			else
				$pickupclose="No";
				
			if($pickup24hours[$i]==1)
				$pickup_24hours="Yes";
			else
				$pickup_24hours="No";
			
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
			if($bussiness_24hours=="Yes")
			{
				$startA="00:00:00";
				$endA="23:59:00";
			}
			//echo $start[$i];
			 $sql="INSERT INTO merchant_hours(merchantID,weekDay,closed,24hours,startTime,endTime,startTimeOther,endTimeOther,deliveryClosed,delivery24hours,deliveryStartTime,deliveryEndTime,deliveryStartTimeOther,deliveryEndTimeOther,pickupClosed,pickup24hours,pickupStartTime,pickupEndTime,pickupStartTimeOther,pickupEndTimeOther,lastUpdatedOn) VALUES(".$_REQUEST['ID'].",'".$i."','".$close."','".$bussiness_24hours."','".$startA."','".$endA."','".$start1A."','".$end1A."','".$deliveryclose."','".$delivery_24hours."','".$deliverystartA."','".$deliveryendA."','".$deliverystart1A."','".$deliveryend1A."','".$pickupclose."','".$pickup_24hours."','".$pickupstartA."','".$pickupendA."','".$pickupstart1A."','".$pickupend1A."',Now())";
			
			
			mysql_query($sql);
		
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
		//$qry="DELETE FROM menus WHERE id=".$_REQUEST['Menu_ID'];
		$qry="UPDATE menus SET Deleted=1,menuStatus='Inactive' WHERE id=".mysql_real_escape_string($_REQUEST['Menu_ID']);
		
		   mysql_query($qry);
		  // Delete Menu Items
		  //$sql="DELETE FROM items WHERE menuID=".$_REQUEST['Menu_ID'];
		  $sql="UPDATE items SET Deleted=1,itemStatus='Inactive' WHERE menuID=".mysql_real_escape_string($_REQUEST['Menu_ID']);
		  mysql_query($sql);
		   echo '<script>window.location="dine.php?act=my_menus&Res_ID='.$_REQUEST['Res_ID'].'";</script>';
		   exit;
	}
	function Delete_menuitem()
	{
			// $sql="DELETE FROM items WHERE id=".$_REQUEST['Item_ID'];
			  $sql="UPDATE items SET Deleted=1,itemStatus='Inactive' WHERE id=".mysql_real_escape_string($_REQUEST['Item_ID']);
			mysql_query($sql);
		   echo '<script language="javascript">location.href="dine.php?act=menu_item&Res_ID='.$_REQUEST['Res_ID'].'&Menu='.$_REQUEST['Menu'].'";</script>'; 
		   exit;
	}
	function Delete_ItemImage()
	{
		$sql="UPDATE items SET itemImage='' WHERE id=".mysql_real_escape_string($_REQUEST['Item_ID']);
		mysql_query($sql);
		unlink("../admin/upload/users/".$_REQUEST['img']);
		echo '<script language="javascript">location.href="dine.php?act=add_item&Res_ID='.$_REQUEST['Res_ID'].'&Menu='.$_REQUEST['Menu'].'&Item_ID='.$_REQUEST['Item_ID'].'";</script>'; 
		exit;
	}
	function Delete_Restaurant()
	{
		   //$qry="DELETE FROM merchant WHERE id=".$_REQUEST['ID'];
		   $qry="UPDATE merchant SET Deleted=1,status='Inactive' WHERE id=".mysql_real_escape_string($_REQUEST['ID']);
		   mysql_query($qry);
		   echo '<script>window.location="dine.php?act=my_restaurants";</script>';
		   exit;
	}
	function BookATable($mobile=NULL)
	{
		$id=mysql_real_escape_string($_REQUEST["id"]);
		/*foreach($_POST['layouts']  as  $value)  {
		$layouts .= "$value,";
		}
		
		$layouts=rtrim($layouts,",");*/
		
		$book_date=date("Y-m-d",strtotime($_REQUEST["book_date"]));
		$Start_Time=date("g:i a",$_REQUEST["book_time"]);
		$book_fulldate=date("Y-m-d G:i:s",strtotime($_REQUEST["book_date"]." ".$Start_Time));
		
		//$meal=mysql_real_escape_string($_REQUEST["meal"]);
		
		// calculate End Time
		$End_Time=date("G:i:s",$_REQUEST["book_totime"]);
		//$Look_Ahead=mysql_real_escape_string($_REQUEST["Look_Ahead"]);
		$Size=mysql_real_escape_string($_REQUEST["size"]);
		$Book_Name=mysql_real_escape_string($_REQUEST["Diner_Name"]);
		if($Book_Name=='First Name')
			$Book_Name="";
			
		$Book_LName=mysql_real_escape_string($_REQUEST["Diner_LName"]);
		if($Book_LName=='Last Name')
			$Book_LName="";
		if($Book_LName<>'')
			$Book_Name.=" ".$Book_LName;
		$Book_Email=mysql_real_escape_string($_REQUEST["Diner_Email"]);
		if($Book_Email=='For Reservation Confirmation')
			$Book_Email="";
		$Book_Phone=mysql_real_escape_string($_REQUEST["Diner_Phone"]);
		if($Book_Phone=='555-555-5555')
			$Book_Phone="";
		$Book_Phone_Contact=mysql_real_escape_string($_REQUEST["contact_principle"]);
		//$Book_FirstTime=mysql_real_escape_string($_REQUEST["first_time"]);
		$Book_Notes=mysql_real_escape_string($_REQUEST["Special_Request"]);
		if($Book_Notes=='Please note that not all requests can be accommodated')
			$Book_Notes="";
		$special_events=$_REQUEST["special_events"];
		
		$User_ID=$_SESSION['User_ID'];
		if($_SESSION['User_ID']=='')
		{
			// Create an New User
			// get random values
			$query="SELECT id,email_add FROM tbl_registeration WHERE email_add='".$Book_Email."'";
			$qexe=mysql_query($query);
			$qnum=mysql_num_rows($qexe);
			$qres=mysql_fetch_array($qexe);
			if($qnum>0)
			{
				$book_date1=date("m/d/Y",$_REQUEST["book_date"]);
				$book_time1=date("g:i a",$_REQUEST["book_time"]);
				$User_ID=$qres['id'];
				//echo "<script language='javascript'>location.href='table_confirm.php?id=".$_REQUEST['id']."&err=email&book_date=".$book_date1."&meal=".$_REQUEST['meal']."&size=".$_REQUEST['size']."&book_time=".$book_time1."&book_totime=".$_REQUEST['book_totime']."&Diner_Name=".$_REQUEST['Diner_Name']."&Diner_Phone=".$_REQUEST['Diner_Phone']."&Diner_Email=".$_REQUEST['Diner_Email']."&lastname=".$Book_LName."&Special_Request=".$Book_Notes."';</script>";
				//exit;
			}
			else
			{
				$length = 30;
				$characters = "0123456789abcdefghijklmnopqrstuvwxyz";
				$string = "";    
				for ($p = 0; $p < $length; $p++) {
					$string .= $characters[mt_rand(0, strlen($characters))];
				}
				$checksum_register=$string;
				$password=get_rand_letters('6');
				GLOBAL $EncryptKey;
				$sql ="insert into tbl_registeration(email_add,encrypt_password,firstname,display_name,mobile_phone,contact_principle,checksum_register,status,cdate) VALUES('".$Book_Email."',AES_ENCRYPT('".$password."','".$EncryptKey."'),'".$_REQUEST['Diner_Name']."','".$_REQUEST['Diner_Name']."','".$_REQUEST['Diner_Phone']."','Mobile','".$checksum_register."',1,now())";
				
				$rec=mysql_query($sql);
				$User_ID=mysql_insert_id();
			
			
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
		}
		$rand=genRandomString();
		$code=$rand;
		$sql="INSERT INTO tbl_tablebooking_bookings(Book_Owner,Book_UserID,Book_Restaurant,Book_date,Book_datetime,Book_Meal,Book_Start_Time,Book_End_Time,Book_Size,Book_Name,Book_Email,Book_Phone,Book_Contact,Book_FirstTime,special_events,Book_Notes,code,Book_Created) 
		VALUES (".$_REQUEST['Res_UserID'].",'".$User_ID."',".$id.",'".$book_date."','".$book_fulldate."','".$meal."','".$Start_Time."','".$End_Time."','".$Size."','".$Book_Name."','".$Book_Email."','".$Book_Phone."','".$Book_Phone_Contact."','".$Book_FirstTime."','".$special_events."','".$Book_Notes."','".$code."',now())";
		
		mysql_query($sql);
		$Booking_ID=mysql_insert_id();
		if($Booking_ID>0)
		{
		GLOBAL $adminmail,$Host_Path,$contactmail;
		
		$sql="SELECT email,contactName,merchantName,contactAddress,state,city,postalCode,telephone,faxNumber FROM merchant WHERE id=".$_REQUEST['id'];
		$exe=mysql_query($sql);
		$res=mysql_fetch_array($exe);
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
					  </tr>';
					  if($Book_Notes<>'')
					  $message.='<tr>
						<td scope="col" width="30%" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;" >Special Request: </td><td style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;" >: '.$Book_Notes.'</td>
					  </tr>';
					  
					  $message.='<tr>
						<td scope="col" width="30%" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;" >Booked for </td><td style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;" >: '.$book_date.'</td>
					  </tr>
					   <tr>
						<td scope="col" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;" >Start Time </td><td style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;" >: '.$Start_Time.'</td>
					  </tr>
					  
					   <tr>
						<td scope="col" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;" >Party Size </td><td style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;" >: '.$Size.'</td>
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
			// Send a copy to Support
			mail($adminmail, $Subject, $message, $headers);
			// Fax using EMAIL with Provider Ringcentral
		$my_file = 'fax/TB-8885194198-'.$Booking_ID.'.html';
		$my_file1 = 'TB-8885194198-'.$Booking_ID.'.html';
		$handle = fopen($my_file, 'w');
		fwrite($handle, $faxmessage);
			
		$my_path = $_SERVER['DOCUMENT_ROOT']."/dine/fax/";
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
		// Enable Below when make it Live
		$status=mail_attachmentnew($my_file1, $my_path, $faxNumber."@rcfax.com", $my_mail, $my_name, $my_replyto, $my_subject, $my_message);
		
		// Store in DB
		$sql="INSERT INTO tbl_fax(Fax_From,Fax_To,Fax_Restaurant,Fax_User,Fax_Content,Fax_Status,Fax_Created) VALUES('888-781-8138','".$faxNumber1."','".$id."',".$User_ID.",'".$my_file1."',".$status.",Now())";
		
		mysql_query($sql);
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
						<td scope="col" width="30%" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;" >Booked for </td><td style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;" >: '.$book_date.'</td>
					  </tr>
					   <tr>
						<td scope="col" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;" >Start Time </td><td style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;" >: '.$Start_Time.'</td>
					  </tr>
					  <tr>
						<td scope="col" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;" >Party Size </td><td style="font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#4e4e4e; line-height:18px;" >: '.$Size.'</td>
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
				//changed by logictreeit solutions
				if($mobile==1){
				 	return  $Booking_ID;
				}	
				else{
					print "<script language=javascript>window.location='table_book_confirm.php?rep=sucess&TxnID=".$Booking_ID."&id=".$_REQUEST['id']."';</script>";
					exit;
				}
				//end
			}
			else
			{
				if($mobile==1){
				 	return  0;
				}	
				else{
					// falied to Book
					print "<script language=javascript>window.location='table_book_confirm.php?rep=fail&TxnID=0&id=".$_REQUEST['id']."';</script>";
					exit;
				}
			}			
	}
	function GetATableReservation($TxnID)
	{
		$query="SELECT tbl_tablebooking_bookings.*,merchant.*,tbl_registeration.firstname,tbl_registeration.display_name FROM tbl_tablebooking_bookings 
			INNER JOIN merchant ON merchant.id=tbl_tablebooking_bookings.Book_Restaurant 
			INNER JOIN tbl_registeration ON tbl_registeration.id=tbl_tablebooking_bookings.Book_UserID 
			WHERE Book_ID=".$TxnID; 
	
		$res = mysql_query($query);
		$TotalRecordCount=mysql_num_rows($res);
		$contact = array();
		
		while($aRow=mysql_fetch_array($res))
		{	
			$contact['Book_ID'] = $aRow['Book_ID'];
			$contact['Book_Restaurant']=$aRow['Book_Restaurant'];
			$contact['Book_date']=$aRow['Book_date'];
			$contact['Book_datetime']=$aRow['Book_datetime'];
			$contact['Book_Start_Time']=$aRow['Book_Start_Time'];
			$contact['Book_Size']=$aRow['Book_Size'];
			$contact['Book_Name']=$aRow['Book_Name'];
			$contact['Book_Email']=$aRow['Book_Email'];
			$contact['Book_Contact']=$aRow['Book_Contact'];
			$contact['Book_Phone']=$aRow['Book_Phone'];
			$contact['Book_Notes']=$aRow['Book_Notes'];
			$contact['Book_Created']=$aRow['Book_Created'];
			$contact['DeliveryAddress']=$aRow['DeliveryAddress'];	
			$contact['firstname']=$aRow['firstname'];
			$contact['display_name']=$aRow['display_name'];
			$contact['merchantName']=$aRow['merchantName'];
			$contact['contactAddress']=$aRow['contactAddress'];
			$contact['city']=$aRow['city'];
			$contact['state']=$aRow['state'];
			$contact['telephone']=$aRow['telephone'];
			$contact['postalCode']=$aRow['postalCode'];
		}		
	return $contact;
	}
	function view_Reservations() {
			$sql="Select tbl_tablebooking_bookings.*,tbl_registeration.* from tbl_tablebooking_bookings INNER JOIN tbl_registeration ON tbl_registeration.id=tbl_tablebooking_bookings.Book_UserID WHERE Book_Restaurant=".$_REQUEST['Res_ID']." order by Book_Created desc";
			$result = mysql_query($sql);
			$TotalRecordCount=mysql_num_rows($result);
			$Totalpages 		= mysql_query("SELECT * FROM `tbl_control`");
			$exec_paging		= mysql_fetch_array($Totalpages);
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
			$Res=mysql_query($query);
			$Record	=	array();
			$i			=	0;
			while($res=mysql_fetch_array($Res)) {
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
		$Book_Tables=explode(",",$Book_Table);
		foreach($Book_Tables as $p=>$q)
		{
			$exe 		= mysql_query("SELECT * FROM `tbl_tablebookinglayouts` WHERE TB_LID=".$q);
			$res		= mysql_fetch_array($exe);
			echo "<br>".$res['TB_LName'];
		}
	}
	function EditSettings()
	{
		$TBS_Restaurant=mysql_real_escape_string($_REQUEST["TBS_Restaurant"]);
		$TBS_Demo=mysql_real_escape_string($_REQUEST["TBS_Demo"]);
		$TBS_Seats=mysql_real_escape_string($_REQUEST["TBS_Seats"]);
		
		$TBS_Breakfast_Start=mysql_real_escape_string($_REQUEST["TBS_Breakfast_Start"]);
		$TBS_Breakfast_End=mysql_real_escape_string($_REQUEST["TBS_Breakfast_End"]);
		$TBS_Breakfast=mysql_real_escape_string($_REQUEST["TBS_Breakfast"]);
		$TBS_Lunch_Start=mysql_real_escape_string($_REQUEST["TBS_Lunch_Start"]);
		$TBS_Lunch_End=mysql_real_escape_string($_REQUEST["TBS_Lunch_End"]);
		$TBS_Lunch=mysql_real_escape_string($_REQUEST["TBS_Lunch"]);
		$TBS_Dinner_Start=mysql_real_escape_string($_REQUEST["TBS_Dinner_Start"]);
		$TBS_Dinner_End=mysql_real_escape_string($_REQUEST["TBS_Dinner_End"]);
		$TBS_Dinner=mysql_real_escape_string($_REQUEST["TBS_Dinner"]);
		$TBS_Before_Breakfast=mysql_real_escape_string($_REQUEST["TBS_Before_Breakfast"]);
		$TBS_Before_Lunch=mysql_real_escape_string($_REQUEST["TBS_Before_Lunch"]);
		$TBS_Before_Dinner=mysql_real_escape_string($_REQUEST["TBS_Before_Dinner"]);
		$TBS_Reserve_Buffer=mysql_real_escape_string($_REQUEST["TBS_Reserve_Buffer"]);
		$TBS_Desc=mysql_real_escape_string($_REQUEST["TBS_Desc"]);
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
		$exe=mysql_query($sql);
		$num=mysql_num_rows($exe);
		if($num>0)
		{
			//$sql="UPDATE tbl_tablebooking_settings SET TBS_Seats='".$_REQUEST['TBS_Seats']."',TBS_Breakfast_Monday_Start='".$_REQUEST['TBS_Breakfast_Monday_Start']."',TBS_Breakfast_Monday_End='".$_REQUEST['TBS_Breakfast_Monday_End']."',TBS_Breakfast_Tuesday_Start='".$_REQUEST['TBS_Breakfast_Tuesday_Start']."',TBS_Breakfast_Tuesday_End='".$_REQUEST['TBS_Breakfast_Tuesday_End']."',TBS_Breakfast_Wednesday_Start='".$_REQUEST['TBS_Breakfast_Wednesday_Start']."',TBS_Breakfast_Wednesday_End='".$_REQUEST['TBS_Breakfast_Wednesday_End']."',TBS_Breakfast_Thursday_Start='".$_REQUEST['TBS_Breakfast_Thursday_Start']."',TBS_Breakfast_Thursday_End='".$_REQUEST['TBS_Breakfast_Thursday_End']."',TBS_Breakfast_Friday_Start='".$_REQUEST['TBS_Breakfast_Friday_Start']."',TBS_Breakfast_Friday_End='".$_REQUEST['TBS_Breakfast_Friday_End']."',TBS_Breakfast_Saturday_Start='".$_REQUEST['TBS_Breakfast_Saturday_Start']."',TBS_Breakfast_Saturday_End='".$_REQUEST['TBS_Breakfast_Saturday_End']."',TBS_Breakfast_Sunday_Start='".$_REQUEST['TBS_Breakfast_Sunday_Start']."',TBS_Breakfast_Sunday_End='".$_REQUEST['TBS_Breakfast_Sunday_End']."',TBS_Breakfast='".$TBS_Breakfast."',TBS_Lunch_Monday_Start='".$_REQUEST['TBS_Lunch_Monday_Start']."',TBS_Lunch_Monday_End='".$_REQUEST['TBS_Lunch_Monday_End']."',TBS_Lunch_Tuesday_Start='".$_REQUEST['TBS_Lunch_Tuesday_Start']."',TBS_Lunch_Tuesday_End='".$_REQUEST['TBS_Lunch_Tuesday_End']."',TBS_Lunch_Wednesday_Start='".$_REQUEST['TBS_Lunch_Wednesday_Start']."',TBS_Lunch_Wednesday_End='".$_REQUEST['TBS_Lunch_Wednesday_End']."',TBS_Lunch_Thursday_Start='".$_REQUEST['TBS_Lunch_Thursday_Start']."',TBS_Lunch_Thursday_End='".$_REQUEST['TBS_Lunch_Thursday_End']."',TBS_Lunch_Friday_Start='".$_REQUEST['TBS_Lunch_Friday_Start']."',TBS_Lunch_Friday_End='".$_REQUEST['TBS_Lunch_Friday_End']."',TBS_Lunch_Saturday_Start='".$_REQUEST['TBS_Lunch_Saturday_Start']."',TBS_Lunch_Saturday_End='".$_REQUEST['TBS_Lunch_Saturday_End']."',TBS_Lunch_Sunday_Start='".$_REQUEST['TBS_Lunch_Sunday_Start']."',TBS_Lunch_Sunday_End='".$_REQUEST['TBS_Lunch_Sunday_End']."',TBS_Lunch='".$TBS_Lunch."',TBS_Dinner_Monday_Start='".$_REQUEST['TBS_Dinner_Monday_Start']."',TBS_Dinner_Monday_End='".$_REQUEST['TBS_Dinner_Monday_End']."',TBS_Dinner_Tuesday_Start='".$_REQUEST['TBS_Dinner_Tuesday_Start']."',TBS_Dinner_Tuesday_End='".$_REQUEST['TBS_Dinner_Tuesday_End']."',TBS_Dinner_Wednesday_Start='".$_REQUEST['TBS_Dinner_Wednesday_Start']."',TBS_Dinner_Wednesday_End='".$_REQUEST['TBS_Dinner_Wednesday_End']."',TBS_Dinner_Thursday_Start='".$_REQUEST['TBS_Dinner_Thursday_Start']."',TBS_Dinner_Thursday_End='".$_REQUEST['TBS_Dinner_Thursday_End']."',TBS_Dinner_Friday_Start='".$_REQUEST['TBS_Dinner_Friday_Start']."',TBS_Dinner_Friday_End='".$_REQUEST['TBS_Dinner_Friday_End']."',TBS_Dinner_Saturday_Start='".$_REQUEST['TBS_Dinner_Saturday_Start']."',TBS_Dinner_Saturday_End='".$_REQUEST['TBS_Dinner_Saturday_End']."',TBS_Dinner_Sunday_Start='".$_REQUEST['TBS_Dinner_Sunday_Start']."',TBS_Dinner_Sunday_End='".$_REQUEST['TBS_Dinner_Sunday_End']."',TBS_Dinner='".$TBS_Dinner."',TBS_Before_Breakfast='".$TBS_Before_Breakfast."',TBS_Before_Lunch='".$TBS_Before_Lunch."',TBS_Before_Dinner='".$TBS_Before_Dinner."',TBS_Reserve_Buffer='".$TBS_Reserve_Buffer."',TBS_Desc='".$TBS_Desc."' WHERE merchant=".$_REQUEST['Res_ID']; 
			$sql="UPDATE tbl_tablebooking_settings SET TBS_Breakfast_Monday_Closed='".$TBS_Breakfast_Monday_Closed."',TBS_Breakfast_Tuesday_Closed='".$TBS_Breakfast_Tuesday_Closed."',TBS_Breakfast_Wednesday_Closed='".$TBS_Breakfast_Wednesday_Closed."',TBS_Breakfast_Thursday_Closed='".$TBS_Breakfast_Thursday_Closed."',TBS_Breakfast_Friday_Closed='".$TBS_Breakfast_Friday_Closed."',TBS_Breakfast_Saturday_Closed='".$TBS_Breakfast_Saturday_Closed."',TBS_Breakfast_Sunday_Closed='".$TBS_Breakfast_Sunday_Closed."',TBS_Seats='".$_REQUEST['TBS_Seats']."',TBS_Breakfast_Monday_Start='".$TBS_Breakfast_Monday_Start."',TBS_Breakfast_Monday_End='".$TBS_Breakfast_Monday_End."',TBS_Breakfast_Tuesday_Start='".$TBS_Breakfast_Tuesday_Start."',TBS_Breakfast_Tuesday_End='".$TBS_Breakfast_Tuesday_End."',TBS_Breakfast_Wednesday_Start='".$TBS_Breakfast_Wednesday_Start."',TBS_Breakfast_Wednesday_End='".$TBS_Breakfast_Wednesday_End."',TBS_Breakfast_Thursday_Start='".$TBS_Breakfast_Thursday_Start."',TBS_Breakfast_Thursday_End='".$TBS_Breakfast_Thursday_End."',TBS_Breakfast_Friday_Start='".$TBS_Breakfast_Friday_Start."',TBS_Breakfast_Friday_End='".$TBS_Breakfast_Friday_End."',TBS_Breakfast_Saturday_Start='".$TBS_Breakfast_Saturday_Start."',TBS_Breakfast_Saturday_End='".$TBS_Breakfast_Saturday_End."',TBS_Breakfast_Sunday_Start='".$TBS_Breakfast_Sunday_Start."',TBS_Breakfast_Sunday_End='".$TBS_Breakfast_Sunday_End."',TBS_Breakfast='".$TBS_Breakfast."',TBS_Before_Breakfast='".$TBS_Before_Breakfast."',TBS_Before_Lunch='".$TBS_Before_Lunch."',TBS_Before_Dinner='".$TBS_Before_Dinner."',TBS_Reserve_Buffer='".$TBS_Reserve_Buffer."',TBS_Desc='".$TBS_Desc."' WHERE merchant=".$_REQUEST['Res_ID']; 
			$exe=mysql_query($sql);
			//exit;
		}
		else
		{
			/*$sql="INSERT INTO tbl_tablebooking_settings(merchant,TBS_Seats,TBS_UserID,TBS_Breakfast_Monday_Start,TBS_Breakfast_Monday_End,TBS_Breakfast_Tuesday_Start,TBS_Breakfast_Tuesday_End,TBS_Breakfast_Wednesday_Start,TBS_Breakfast_Wednesday_End,TBS_Breakfast_Thursday_Start,TBS_Breakfast_Thursday_End,TBS_Breakfast_Friday_Start,TBS_Breakfast_Friday_End,TBS_Breakfast_Saturday_Start,TBS_Breakfast_Saturday_End,TBS_Breakfast_Sunday_Start,TBS_Breakfast_Sunday_End,TBS_Lunch_Monday_Start,TBS_Lunch_Monday_End,TBS_Lunch_Tuesday_Start,TBS_Lunch_Tuesday_End,TBS_Lunch_Wednesday_Start,TBS_Lunch_Wednesday_End,TBS_Lunch_Thursday_Start,TBS_Lunch_Thursday_End,TBS_Lunch_Friday_Start,TBS_Lunch_Friday_End,TBS_Lunch_Saturday_Start,TBS_Lunch_Saturday_End,TBS_Lunch_Sunday_Start,TBS_Lunch_Sunday_End,TBS_Dinner_Monday_Start,TBS_Dinner_Monday_End,TBS_Dinner_Tuesday_Start,TBS_Dinner_Tuesday_End,TBS_Dinner_Wednesday_Start,TBS_Dinner_Wednesday_End,TBS_Dinner_Thursday_Start,TBS_Dinner_Thursday_End,TBS_Dinner_Friday_Start,TBS_Dinner_Friday_End,TBS_Dinner_Saturday_Start,TBS_Dinner_Saturday_End,TBS_Dinner_Sunday_Start,TBS_Dinner_Sunday_End,TBS_Breakfast,TBS_Dinner,TBS_Before_Breakfast,TBS_Before_Lunch,TBS_Before_Dinner,TBS_Reserve_Buffer,TBS_Desc,TBS_Created) 
			VALUES('".$_REQUEST['Res_ID']."','".$_REQUEST['TBS_Seats']."','".$_SESSION['User_ID']."','".$_REQUEST['TBS_Breakfast_Monday_Start']."','".$_REQUEST['TBS_Breakfast_Monday_End']."','".$_REQUEST['TBS_Breakfast_Tuesday_Start']."','".$_REQUEST['TBS_Breakfast_Tuesday_End']."','".$_REQUEST['TBS_Breakfast_Wednesday_Start']."','".$_REQUEST['TBS_Breakfast_Wednesday_End']."','".$_REQUEST['TBS_Breakfast_Thursday_Start']."','".$_REQUEST['TBS_Breakfast_Thursday_End']."','".$_REQUEST['TBS_Breakfast_Friday_Start']."','".$_REQUEST['TBS_Breakfast_Friday_End']."','".$_REQUEST['TBS_Breakfast_Saturday_Start']."','".$_REQUEST['TBS_Breakfast_Saturday_End']."','".$_REQUEST['TBS_Breakfast_Sunday_Start']."','".$_REQUEST['TBS_Breakfast_Sunday_End']."','".$_REQUEST['TBS_Lunch_Monday_Start']."','".$_REQUEST['TBS_Lunch_Monday_End']."','".$_REQUEST['TBS_Lunch_Tuesday_Start']."','".$_REQUEST['TBS_Lunch_Tuesday_End']."','".$_REQUEST['TBS_Lunch_Wednesday_Start']."','".$_REQUEST['TBS_Lunch_Wednesday_End']."','".$_REQUEST['TBS_Lunch_Thursday_Start']."','".$_REQUEST['TBS_Lunch_Thursday_End']."','".$_REQUEST['TBS_Lunch_Friday_Start']."','".$_REQUEST['TBS_Lunch_Friday_End']."','".$_REQUEST['TBS_Lunch_Saturday_Start']."','".$_REQUEST['TBS_Lunch_Saturday_End']."','".$_REQUEST['TBS_Lunch_Sunday_Start']."','".$_REQUEST['TBS_Lunch_Sunday_End']."','".$_REQUEST['TBS_Dinner_Monday_Start']."','".$_REQUEST['TBS_Dinner_Monday_End']."','".$_REQUEST['TBS_Dinner_Tuesday_Start']."','".$_REQUEST['TBS_Dinner_Tuesday_End']."','".$_REQUEST['TBS_Dinner_Wednesday_Start']."','".$_REQUEST['TBS_Dinner_Wednesday_End']."','".$_REQUEST['TBS_Dinner_Thursday_Start']."','".$_REQUEST['TBS_Dinner_Thursday_End']."','".$_REQUEST['TBS_Dinner_Friday_Start']."','".$_REQUEST['TBS_Dinner_Friday_End']."','".$_REQUEST['TBS_Dinner_Saturday_Start']."','".$_REQUEST['TBS_Dinner_Saturday_End']."','".$_REQUEST['TBS_Dinner_Sunday_Start']."','".$_REQUEST['TBS_Dinner_Sunday_End']."','".$TBS_Breakfast."','".$TBS_Dinner."','".$TBS_Before_Breakfast."','".$TBS_Before_Lunch."','".$TBS_Before_Dinner."','".$TBS_Reserve_Buffer."','".$TBS_Desc."',Now())";*/
			$sql="INSERT INTO tbl_tablebooking_settings(merchant,TBS_Seats,TBS_UserID,TBS_Breakfast_Monday_Closed,TBS_Breakfast_Tuesday_Closed,TBS_Breakfast_Wednesday_Closed,TBS_Breakfast_Thursday_Closed,TBS_Breakfast_Friday_Closed,TBS_Breakfast_Saturday_Closed,TBS_Breakfast_Sunday_Closed,TBS_Breakfast_Monday_Start,TBS_Breakfast_Monday_End,TBS_Breakfast_Tuesday_Start,TBS_Breakfast_Tuesday_End,TBS_Breakfast_Wednesday_Start,TBS_Breakfast_Wednesday_End,TBS_Breakfast_Thursday_Start,TBS_Breakfast_Thursday_End,TBS_Breakfast_Friday_Start,TBS_Breakfast_Friday_End,TBS_Breakfast_Saturday_Start,TBS_Breakfast_Saturday_End,TBS_Breakfast_Sunday_Start,TBS_Breakfast_Sunday_End,TBS_Breakfast,TBS_Before_Breakfast,TBS_Reserve_Buffer,TBS_Desc,TBS_Created) 
			VALUES('".$_REQUEST['Res_ID']."','".$_REQUEST['TBS_Seats']."','".$_SESSION['User_ID']."','".$TBS_Breakfast_Monday_Closed."','".$TBS_Breakfast_Tuesday_Closed."','".$TBS_Breakfast_Wednesday_Closed."','".$TBS_Breakfast_Thursday_Closed."','".$TBS_Breakfast_Friday_Closed."','".$TBS_Breakfast_Saturday_Closed."','".$TBS_Breakfast_Sunday_Closed."','".$TBS_Breakfast_Monday_Start."','".$TBS_Breakfast_Monday_End."','".$TBS_Breakfast_Tuesday_Start."','".$TBS_Breakfast_Tuesday_End."','".$TBS_Breakfast_Wednesday_Start."','".$TBS_Breakfast_Wednesday_End."','".$TBS_Breakfast_Thursday_Start."','".$TBS_Breakfast_Thursday_End."','".$TBS_Breakfast_Friday_Start."','".$TBS_Breakfast_Friday_End."','".$TBS_Breakfast_Saturday_Start."','".$TBS_Breakfast_Saturday_End."','".$TBS_Breakfast_Sunday_Start."','".$TBS_Breakfast_Sunday_End."','".$TBS_Breakfast."','".$TBS_Before_Breakfast."','".$TBS_Reserve_Buffer."','".$TBS_Desc."',Now())";
			$exe=mysql_query($sql);
		}
		//echo $sql;exit;
		echo '<script language="javascript">location.href="dine.php?act=settings&Res_ID='.$_REQUEST['Res_ID'].'";</script>'; 
		exit;
	}
	function EditNotification()
	{
		$GS_Noti_frommail=mysql_real_escape_string($_REQUEST["GS_Noti_frommail"]);
		$GS_Noti_fromname=mysql_real_escape_string($_REQUEST["GS_Noti_fromname"]);
		$GS_Send_Confirmation=mysql_real_escape_string($_REQUEST["GS_Send_Confirmation"]);
		$GS_Cus_NotiSubject=mysql_real_escape_string($_REQUEST["GS_Cus_NotiSubject"]);
		$GS_Cus_NotiBody=mysql_real_escape_string($_REQUEST["GS_Cus_NotiBody"]);
		$GS_Cancel_Subject=mysql_real_escape_string($_REQUEST["GS_Cancel_Subject"]);
		$GS_Cancel_Body=mysql_real_escape_string($_REQUEST["GS_Cancel_Body"]);
		$GS_Noti_Email=mysql_real_escape_string($_REQUEST["GS_Noti_Email"]);
		
		$GS_Noti_CC=mysql_real_escape_string($_REQUEST["GS_Noti_CC"]);
		$GS_Attach_Report=mysql_real_escape_string($_REQUEST["GS_Attach_Report"]);
		$GS_Noti_Mail_Subject=mysql_real_escape_string($_REQUEST["GS_Noti_Mail_Subject"]);
		$GS_Noti_Body=mysql_real_escape_string($_REQUEST["GS_Noti_Body"]);
		$GS_Ext_Cancel_Subject=mysql_real_escape_string($_REQUEST["GS_Ext_Cancel_Subject"]);
		$GS_Ext_Cancel_Body=mysql_real_escape_string($_REQUEST["GS_Ext_Cancel_Body"]);
		
		/*$sql1="SELECT TBS_ID FROM tbl_tablebooking_settings WHERE TBS_UserID=".$_SESSION['User_ID'];
		$exe1=mysql_query($sql1);
		$num1=mysql_num_rows($exe1);
		if($num1>0)
		{
			$res=mysql_fetch_array($exe1);
			$id=$res['TBS_ID'];
		} else {
			$sql="INSERT INTO tbl_tablebooking_settings(merchant) VALUES(".$_SESSION['User_ID'].")";
			$exe=mysql_query($sql);
			$id=mysql_insert_id();
		}*/
		
		$sql="SELECT GS_ID FROM tbl_tablebooking_globalsettings WHERE GS_Restaurant=".$_REQUEST['Res_ID'];
		$exe=mysql_query($sql);
		$num=mysql_num_rows($exe);
		if($num>0)
		{
			$sql="UPDATE tbl_tablebooking_globalsettings SET GS_Noti_frommail='".$GS_Noti_frommail."',GS_Noti_fromname='".$GS_Noti_fromname."',GS_Send_Confirmation='".$GS_Send_Confirmation."',GS_Cus_NotiSubject='".$GS_Cus_NotiSubject."',GS_Cus_NotiBody='".$GS_Cus_NotiBody."',GS_Cancel_Subject='".$GS_Cancel_Subject."',GS_Cancel_Body='".$GS_Cancel_Body."',GS_Noti_Email='".$GS_Noti_Email."',GS_Noti_CC='".$GS_Noti_CC."',GS_Attach_Report='".$GS_Attach_Report."',GS_Noti_Mail_Subject='".$GS_Noti_Mail_Subject."',GS_Noti_Body='".$GS_Noti_Body."',GS_Ext_Cancel_Subject='".$GS_Ext_Cancel_Subject."',GS_Ext_Cancel_Body='".$GS_Ext_Cancel_Body."' WHERE GS_Restaurant=".$_REQUEST['Res_ID']; 
			$exe=mysql_query($sql);
		}
		else
		{
			$sql="INSERT INTO tbl_tablebooking_globalsettings(GS_Restaurant,GS_UserID,GS_Noti_frommail,GS_Noti_fromname,GS_Send_Confirmation,GS_Cus_NotiSubject,GS_Cus_NotiBody,GS_Cancel_Subject,GS_Cancel_Body,GS_Noti_Email,GS_Noti_CC,GS_Attach_Report,GS_Noti_Mail_Subject,GS_Noti_Body,GS_Ext_Cancel_Subject,GS_Ext_Cancel_Body,GS_Created) 
			VALUES('".$_REQUEST['Res_ID']."','".$_SESSION['User_ID']."','".$GS_Noti_frommail."','".$GS_Noti_fromname."','".$GS_Send_Confirmation."','".$GS_Cus_NotiSubject."','".$GS_Cus_NotiBody."','".$GS_Cancel_Subject."','".$GS_Cancel_Body."','".$GS_Noti_Email."','".$GS_Noti_CC."','".$GS_Attach_Report."','".$GS_Noti_Mail_Subject."','".$GS_Noti_Body."','".$GS_Ext_Cancel_Subject."','".$GS_Ext_Cancel_Body."',Now())";
			$exe=mysql_query($sql);
		}
		//echo $sql;exit;
		echo '<script language="javascript">location.href="dine.php?act=notify&Res_ID='.$_REQUEST['Res_ID'].'";</script>'; 
		exit;
	}
	function view_review()
	  {
			$Record=array();
			$i=0;
			$sql="SELECT * FROM tbl_reviews WHERE Review_ID=".mysql_real_escape_string($_REQUEST['Review_ID'])." order by Date_Created desc";
			$rec=mysql_query($sql);
			$count=mysql_num_rows($rec);
			while($res=mysql_fetch_array($rec))
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
		//user_id=".$_SESSION['User_ID']." AND
		$sql="SELECT * FROM tbl_reviews WHERE R_Type='Dine' ";
		if($_REQUEST['Res_ID']!='')
		 $sql.=" AND list_id=".mysql_real_escape_string($_REQUEST['Res_ID']);
		$sql.=" order by Date_Created desc";
				$result = mysql_query($sql) or die(mysql_error());
		$TotalRecordCount=mysql_num_rows($result);
	
			$Totalpages 		= mysql_query("SELECT * FROM `tbl_control`");
			$exec_paging		= mysql_fetch_array($Totalpages);
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
			$Res=mysql_query($query) or die (mysql_error());
			$Record	=	array();
			$i			=	0;
			while($res=mysql_fetch_array($Res)) {
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
		   $qry="DELETE FROM tbl_reviews WHERE Review_ID=".mysql_real_escape_string($_REQUEST[id]);
		   mysql_query($qry) or die(mysql_error);
	  }
	  function Review_Status()
	   {
		   $qry="UPDATE tbl_reviews SET status=".mysql_real_escape_string($_GET[status])." WHERE Review_ID=".mysql_real_escape_string($_REQUEST['id']); 
		  mysql_query($qry) or die(mysql_error);
	  }
	  function GetAverageReviews($model_id)
		{
			$sql="SELECT txt1,txt2,txt3,txt4,txt5,txt6,txt7 FROM tbl_reviews WHERE model_id=".$model_id;
			$Exe=@mysql_query($sql);
			$Total=0;
			$i=0;
			while($res=@mysql_fetch_array($Exe))
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
			$sql="select * from tbl_reviewsothersites where status=1 AND Review_Type='Dine' AND Res_ID=".$Res_ID." ORDER BY Date_Created DESC LIMIT 2";
			$result= mysql_query($sql);
			$Records=array();
			$totalrecords=mysql_num_rows($result);
			$i = 0; 
			while ($aRow=mysql_fetch_array($result))
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

		$sql="select * from tbl_reviewsothersites where Review_ID=".$ID;
		$result= mysql_query($sql);
		$Records=array();
		$totalrecords=mysql_num_rows($result);
		$i = 0; 
		while ($aRow=mysql_fetch_array($result))
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
		  $sql="Select * from  tbl_activities WHERE Act_UserID='".$_SESSION[User_ID]."' order by Act_Created desc"; 
		  $result = mysql_query($sql) or die(mysql_error());
		  $TotalRecordCount=mysql_num_rows($result);
	
			$Totalpages 		= mysql_query("SELECT * FROM `tbl_control`");
			$exec_paging		= mysql_fetch_array($Totalpages);
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
			$Res=mysql_query($query) or die (mysql_error());
			$Record	=	array();
			$i			=	0;
			while($res=mysql_fetch_array($Res)) {
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
		$sql="SELECT id,Main_Menu,Start_Time,End_Time FROM menus WHERE menuStatus='Active' AND merchantID=".mysql_real_escape_string($_REQUEST['id'])." GROUP BY Main_Menu LIMIT 4";
		$exe=mysql_query($sql) or die(mysql_error());
		$i=0;
		$record=array();
		while($rec=mysql_fetch_array($exe))
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
		$sql="SELECT id,Main_Menu,Start_Time,End_Time FROM menus WHERE menuStatus='Active' AND merchantID=".$id." GROUP BY Main_Menu";
		$exe=mysql_query($sql);
		$i=0;
		$record=array();
		while($rec=mysql_fetch_array($exe))
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
		$sql="SELECT Start_Time,End_Time FROM menus WHERE Main_Menu='".mysql_real_escape_string($Menu)."' AND merchantID=".mysql_real_escape_string($_REQUEST['id']);
		$exe=mysql_query($sql) or die(mysql_error());
		$rec=mysql_fetch_array($exe);
		return array($rec['Start_Time'],$rec['End_Time']);
	}
	function GetMenuFromItem($item)
	{
		$sql="SELECT menuID FROM items WHERE id=".$item;
		$exe=mysql_query($sql) or die(mysql_error());
		$rec=mysql_fetch_array($exe);
		return $rec['menuID'];
	}
	function GetCus_Pizza($item)
	{
		$sql="SELECT Cus_Pizza FROM items WHERE id=".$item;
		$exe=mysql_query($sql) or die(mysql_error());
		$rec=mysql_fetch_array($exe);
		return $rec['Cus_Pizza'];
	}
	function GetFree_Toppings($item)
	{
		$sql="SELECT Free_Toppings FROM items WHERE id=".$item;
		$exe=mysql_query($sql) or die(mysql_error());
		$rec=mysql_fetch_array($exe);
		return $rec['Free_Toppings'];
	}
	
	function getMerchantOpenCloseStatus_Search($MID,$time,$orderType)
	{
		GLOBAL $Time_Zone;
		
		if($time == "") {
		$day = date("w");
		$currTime = date("H:i:s");
		} else {
			$day = date("w", strtotime($time));
			$currTime = date("H:i:s",strtotime($time));
		}
		
		// get Merchant Info 
		$msql="SELECT * FROM merchant WHERE id=".$MID;
		$mexe=mysql_query($msql) or die(mysql_error());
		$merchantInfo=mysql_fetch_array($mexe);
		
		$NowTime = strtotime(date("Ymd"));
		$NowTime = 	toSeconds($NowTime);
		$diff = $merchantInfo['timezone'] - $Time_Zone;               
		$NowTime += ($diff * 60 * 60);
		
		$BookTime = strtotime($time);
		$BookTime = 	toSeconds($BookTime);
		$diff = $merchantInfo['timezone'] - $Time_Zone;               
		$BookTime += ($diff * 60 * 60);
		
		
		
		$sql="SELECT * FROM merchant_hours WHERE merchantID=".$MID." AND weekDay=".$day;
		$exe=mysql_query($sql) or die(mysql_error());
		$num=mysql_num_rows($exe);
		if($num>0)
		{
			$row=mysql_fetch_array($exe);
			$currentTime = 	toSeconds($currTime);
			// mandatory for timezone diff calculation
			$diff = $merchantInfo['timezone'] - $Time_Zone;               
			//$currentTime += ($diff * 60 * 60);
			
			 
			if($orderType == "Delivery")
            {	
				if($row['delivery24hours'] == "Yes")
					return "Open";
				else if($row['deliveryClosed'] == "Yes")
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
				if($row['pickup24hours'] == "Yes")
					return "Open";
				else if($row['pickupClosed'] == "Yes")
				return "Close";
						else if( isset($row['pickupStartTime']) && isset($row['pickupEndTime']) && $row['pickupStartTime'] != "00:00:00" && $row['pickupEndTime'] != "00:00:00" && (($row['pickupEndTime'] >= $row->pickupStartTime && $currentTime >= toSeconds($row['pickupStartTime']) && $currentTime<= toSeconds($row['pickupEndTime'])) || ($row['pickupEndTime'] < $row['pickupStartTime'] && $currentTime >= toSeconds($row['pickupStartTime']) && $currentTime<= (toSeconds($row['pickupEndTime'])+24*60*60)))) {
				return "Open";
						} else if(isset($row['pickupStartTimeOther']) && isset($row['pickupEndTimeOther']) && $row['pickupStartTimeOther'] != "00:00:00" && $row['pickupEndTimeOther'] != "00:00:00" && (($row['pickupEndTimeOther'] >= $row['pickupStartTimeOther'] && $currentTime >= toSeconds($row['pickupStartTimeOther']) && $currentTime<= toSeconds($row['pickupEndTimeOther'])) || ($row['pickupEndTimeOther'] < $row['pickupStartTimeOther'] && $currentTime >= toSeconds($row['pickupStartTimeOther']) && $currentTime<= (toSeconds($row['pickupEndTimeOther'])+24*60*60)))) {
				return "Open";
						} else {
				return "Close";
						}
			} else {  // restaurant open time

                    if($row['24hours'] == "Yes")
					return "Open";
				else if($row['closed'] == "Yes")
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
		if($time == "") {
		$day = date("w");
		$currTime = date("H:i:s");
		} else {
			$day = date("w", strtotime($time));
			$currTime = date("H:i:s",strtotime($time));
		}
		
		// get Merchant Info 
		$msql="SELECT * FROM merchant WHERE id=".$MID;
		$mexe=mysql_query($msql) or die(mysql_error());
		$merchantInfo=mysql_fetch_array($mexe);
		
		$currentTime = strtotime(date("F j, Y, g:i a"));

		$diff = $merchantInfo['timezone'] - $Time_Zone;               
		$currentTime += ($diff * 60 * 60);

		$sql="SELECT * FROM merchant_hours WHERE merchantID=".$MID." AND weekDay=".$day;
		$exe=mysql_query($sql) or die(mysql_error());
		$num=mysql_num_rows($exe);
		if($num>0)
		{
			$row=mysql_fetch_array($exe);
			if($row['24hours'] == "Yes")
					return "Open";
			else if($row['closed'] == "Yes")
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
		if($time == "") {
		$day = date("w");
		$currTime = date("H:i:s");
		} else {
			$day = date("w", strtotime($time));
			$currTime = date("H:i:s",strtotime($time));
		}
		
		// get Merchant Info 
		$msql="SELECT * FROM merchant WHERE id=".$MID;
		$mexe=mysql_query($msql) or die(mysql_error());
		$merchantInfo=mysql_fetch_array($mexe);
		
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
		$exe=mysql_query($sql) or die(mysql_error());
		$num=mysql_num_rows($exe);
		if($num>0)
		{
			$row=mysql_fetch_array($exe);
			//$currentTime = 	toSeconds($currTime);
			$currTime = strtotime($currentTime);
			// mandatory for timezone diff calculation
			$diff = $merchantInfo['timezone'] - $Time_Zone;               
			$currentTime += ($diff * 60 * 60);
			
			 
			if($orderType == "Delivery")
            {	
				if($row['delivery24hours'] == "Yes")
					return "Open";
			else if($row['deliveryClosed'] == "Yes")
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
				if($row['pickup24hours'] == "Yes")
					return "Open";
			else if($row['pickupClosed'] == "Yes")
				return "Close";
						else if( isset($row['pickupStartTime']) && isset($row['pickupEndTime']) && $row['pickupStartTime'] != "00:00:00" && $row['pickupEndTime'] != "00:00:00" && (($row['pickupEndTime'] >= $row->pickupStartTime && $currentTime >= strtotime($row['pickupStartTime']) && $currentTime<= strtotime($row['pickupEndTime'])) || ($row['pickupEndTime'] < $row['pickupStartTime'] && $currentTime >= strtotime($row['pickupStartTime']) && $currentTime<= (strtotime($row['pickupEndTime'])+24*60*60)))) {
				return "Open";
						} else if(isset($row['pickupStartTimeOther']) && isset($row['pickupEndTimeOther']) && $row['pickupStartTimeOther'] != "00:00:00" && $row['pickupEndTimeOther'] != "00:00:00" && (($row['pickupEndTimeOther'] >= $row['pickupStartTimeOther'] && $currentTime >= strtotime($row['pickupStartTimeOther']) && $currentTime<= strtotime($row['pickupEndTimeOther'])) || ($row['pickupEndTimeOther'] < $row['pickupStartTimeOther'] && $currentTime >= strtotime($row['pickupStartTimeOther']) && $currentTime<= (strtotime($row['pickupEndTimeOther'])+24*60*60)))) {
				return "Open";
						} else {
				return "Close";
						}
			} else {  // restaurant open time

            if($row['24hours'] == "Yes")
					return "Open";
			else  if($row['closed'] == "Yes")
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
		$sql="SELECT id,menuName,menuDescription,Start_Time,End_Time,menuSize1,menuSize2,menuSize3,menuSize4,menuSize5,menuSize6,menuSize7,menuSize8,menuSize9,menuSize10,menuSize11,menuSize12,menuSize13,menuSize14,menuSize15,menuSize16,menuSize17,menuSize18,menuSize19,menuSize20 FROM menus WHERE menuStatus='Active' AND merchantID=".mysql_real_escape_string($_REQUEST['id'])." AND Main_Menu='".mysql_real_escape_string($MainMenu)."' ORDER BY menuOrder,menuName";
		$exe=mysql_query($sql) or die(mysql_error());
		$num=mysql_num_rows($exe);
		$i=0;
		$record=array();
		while($rec=mysql_fetch_array($exe))
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
		$sql="SELECT id,menuName,menuDescription,Start_Time,End_Time,menuSize1,menuSize2,menuSize3,menuSize4,menuSize5,menuSize6,menuSize7,menuSize8,menuSize9,menuSize10,menuSize11,menuSize12,menuSize13,menuSize14,menuSize15,menuSize16,menuSize17,menuSize18,menuSize19,menuSize20 FROM menus WHERE menuStatus='Active' AND merchantID=".$id." AND Main_Menu='".mysql_real_escape_string($MainMenu)."' ORDER BY menuOrder,menuName";
		$exe=mysql_query($sql);
		$num=mysql_num_rows($exe);
		$i=0;
		$record=array();
		while($rec=mysql_fetch_array($exe))
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
		$query="SELECT Count(*) AS CNT FROM items INNER JOIN menus ON menus.id=items.menuID WHERE items.itemStatus='Active' AND menus.Main_Menu='".mysql_real_escape_string($tab)."' AND menus.merchantID=".mysql_real_escape_string($_REQUEST['id']);
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
		$exe=mysql_query($query);
		$row=mysql_fetch_array($exe);
		return $row['CNT'];
	}
	function GetMenuItems($Menu_ID)
	{
		$sql="SELECT id,itemName,itemDescription,itemPrice,itemPrice1,itemPrice2,itemPrice3,itemPrice4,itemPrice5,itemPrice6,itemPrice7,itemPrice8,itemPrice9,itemPrice10,itemPrice11,itemPrice12,itemPrice13,itemPrice14,itemPrice15,itemPrice16,itemPrice17,itemPrice18,itemPrice19,popular,spicy,veggie,chef_special,itemImage,Cus_Pizza FROM items WHERE itemStatus='Active' AND menuID=".mysql_real_escape_string($Menu_ID);
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
		
		$exe=mysql_query($sql) or die(mysql_error());
		$num=mysql_num_rows($exe);
		$i=0;
		$record=array();
		while($rec=mysql_fetch_array($exe))
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
		$sql="SELECT * FROM merchant_hours WHERE merchantID=".mysql_real_escape_string($_REQUEST['id'])." ORDER BY weekDay ASC";
		$exe=mysql_query($sql) or die(mysql_error());
		$i=0;
		$record=array();
		while($rec=mysql_fetch_array($exe))
		{
			$record[$i]['id']				=	$rec['id'];
			$record[$i]['weekDay']			=	$rec['weekDay'];
			$record[$i]['closed']			=	$rec['closed'];
			$record[$i]['deliveryClosed']			=	$rec['deliveryClosed'];
			$record[$i]['pickupClosed']			=	$rec['pickupClosed'];
			$record[$i]['24hours']			=	$rec['24hours'];
			$record[$i]['delivery24hours']			=	$rec['delivery24hours'];
			$record[$i]['pickup24hours']			=	$rec['pickup24hours'];
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
			// changed by logictree
			$record[$i]['pickupStartTimeOther']	=	$rec['pickupStartTimeOther'];
			$record[$i]['pickupEndTimeOther']		=	$rec['pickupEndTimeOther'];
			$record[$i]['pickupStartTime']		=	$rec['pickupStartTime'];
			$record[$i]['pickupEndTime']			=	$rec['pickupEndTime'];
			// end by logictree
			$i++;
		}
		return array($record);
	}
	function GetMerchantOpenHours()
	{
		$sql="SELECT * FROM merchant_hours WHERE merchantID=".mysql_real_escape_string($_REQUEST['id'])." ORDER BY weekDay ASC";
		$exe=mysql_query($sql) or die(mysql_error());
		$i=0;
		$record=array();
		while($rec=mysql_fetch_array($exe))
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
		$sql="SELECT payment_cc,payment_check,payment_cash,payment_ticket,payment_paypal FROM merchant WHERE id=".mysql_real_escape_string($_REQUEST['id']);
		$exe=mysql_query($sql) or die(mysql_error());
		$payments="";
		$rec=mysql_fetch_array($exe);
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
		$Record	=	array();
		$i			=	0;
		if($delivery_basedon=='miles')
		{
			$sql="SELECT * FROM delivery_fees WHERE merchantID=".$id." GROUP BY fees";
			$exe=mysql_query($sql);
			$fee="";
			while($res=mysql_fetch_array($exe))
			{
				$Record[$i]['postalCode']=$res['postalCode'];
				$Record[$i]['fees']=$res['fees'];
				$i++;
			}
		}
		else if($delivery_basedon=='zipcode')
		{
			$sql="SELECT * FROM deliveryzipcode_fees WHERE merchantID=".$id." GROUP BY fees";
			$exe=mysql_query($sql);
			$fee="";
			while($res=mysql_fetch_array($exe))
			{
				$Record[$i]['postalCode']=$res['postalCode'];
				$Record[$i]['fees']=$res['fees'];
				$i++;
			}
		}
		
		return array($Record);
	}
	function view_ServiceReviews() {
		 $sql="SELECT tbl_reviews.*,tbl_registeration.* FROM tbl_reviews INNER JOIN tbl_registeration On tbl_registeration.id=tbl_reviews.user_id WHERE tbl_reviews.list_id=".mysql_real_escape_string($_REQUEST['id'])." AND R_Type='Dine' order by tbl_reviews.Date_Created desc"; 
		$result = mysql_query($sql);
		$TotalRecordCount=mysql_num_rows($result);
	
			$Record	=	array();
			$i			=	0;
			while($res=mysql_fetch_array($result)) {
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
		$Query="SELECT tbl_cart.*,items.itemName,items.Cus_Pizza from tbl_cart INNER JOIN items ON items.id=tbl_cart.Cart_ServiceID where Cart_Type='Dine' AND Sess_ID='".session_id()."'";
		$res = mysql_query($Query);
		$num=mysql_num_rows($res);
		$cart = array();
		$i=0;
		while ($result=mysql_fetch_array($res))
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
		$Query="SELECT Owner_Restaurant from tbl_cart WHERE Cart_Type='Dine' AND Sess_ID='".session_id()."' LIMIT 1";
		$res = mysql_query($Query);
		$num=mysql_num_rows($res);
		if($num>0)
		{
			$result=mysql_fetch_array($res);
			if($result['Owner_Restaurant']<>$_REQUEST['id'])
			{
				$sql="DELETE FROM tbl_cart WHERE Cart_Type='Dine' AND Sess_ID='".session_id()."'";
				mysql_query($sql);
			}
		}
	}
	function getMinimunDeliveryCharges($rest)
	{
		$rescity = @mysql_fetch_array(mysql_query("select minimumDeliveryAmount from merchant where id=".$rest));
		return $rescity['minimumDeliveryAmount'];
	}
	function reservations()
	{
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
		//echo $_REQUEST["Book_ID"];
		if($_REQUEST["TxnID"]!='')
		{
			$sq="SELECT orders.id AS Order_ID,merchant.logo,tbl_registeration.firstname,tbl_registeration.display_name,tbl_registeration.email_add,merchant.merchantName,merchant.contactAddress,merchant.city AS RES_CITY,merchant.state AS RES_STATE,tbl_registeration.street,tbl_registeration.city AS C_CITY,tbl_registeration.state AS C_STATE FROM orders
			INNER JOIN tbl_registeration ON tbl_registeration.id=orders.customerID
			INNER JOIN merchant ON merchant.id=orders.merchantID 			
			WHERE orders.id=".mysql_real_escape_string($_REQUEST["TxnID"])." AND Owner_ID=".$_SESSION["User_ID"]; 
			
		}
		if($_REQUEST["Book_ID"]!='')
		{
			$pos = strpos($_REQUEST['Book_ID'], "DI");
			if ($pos === false) {
				$pos = strpos($_REQUEST['Book_ID'], "TB");
				$Book_ID=str_ireplace("TB","",$_REQUEST['Book_ID']);
				if ($pos === false)
					$sq="";
				else
				$sq="SELECT tbl_tablebooking_bookings.Book_ID AS Order_ID,Book_date AS orderDate,Book_Start_Time AS orderTime,Book_Size AS quantity,merchant.logo,tbl_registeration.firstname,tbl_registeration.display_name,tbl_registeration.email_add,tbl_registeration.mobile_phone,merchant.merchantName,merchant.contactAddress,merchant.city AS RES_CITY,merchant.state AS RES_STATE,tbl_registeration.street,tbl_registeration.city AS C_CITY,tbl_registeration.state AS C_STATE,tbl_registeration.*,2 AS MAIN,Book_Created AS CREATED,0 AS paymentType,0 AS orderAmount,0 AS discount,0 AS orderType,0 AS DeliveryAddress FROM tbl_tablebooking_bookings
			INNER JOIN tbl_registeration ON tbl_registeration.id=tbl_tablebooking_bookings.Book_Owner 
			INNER JOIN merchant ON merchant.id=tbl_tablebooking_bookings.Book_Restaurant 
			 WHERE (Book_Owner=".$_SESSION["User_ID"].")AND Book_ID=".$Book_ID;
			}
			else {
				$Book_ID=str_ireplace("DI","",$_REQUEST['Book_ID']);
				$sq="SELECT orders.id AS Order_ID,orderDate,orderTime,0 AS quantity,merchant.logo,tbl_registeration.firstname,tbl_registeration.display_name,tbl_registeration.email_add,tbl_registeration.mobile_phone,merchant.merchantName,merchant.contactAddress,merchant.city AS RES_CITY,merchant.state AS RES_STATE,tbl_registeration.street,tbl_registeration.city AS C_CITY,tbl_registeration.state AS C_STATE,tbl_registeration.*,1 AS MAIN,orderPlacedOn AS CREATED,orders.paymentType,orderAmount,discount,orderType,DeliveryAddress FROM orders
			INNER JOIN tbl_registeration ON tbl_registeration.id=orders.customerID 
			INNER JOIN merchant ON merchant.id=orders.merchantID 
			 WHERE (Owner_ID=".$_SESSION["User_ID"]." OR merchant.Res_AssignUserID=".$_SESSION['User_ID'].") AND orders.id=".$Book_ID;
			} 
		} else
		{
			$sq="SELECT orders.id AS Order_ID,orderDate,orderTime,0 AS quantity,merchant.logo,tbl_registeration.firstname,tbl_registeration.display_name,tbl_registeration.email_add,tbl_registeration.mobile_phone,merchant.merchantName,merchant.contactAddress,merchant.city AS RES_CITY,merchant.state AS RES_STATE,tbl_registeration.street,tbl_registeration.city AS C_CITY,tbl_registeration.state AS C_STATE,tbl_registeration.*,1 AS MAIN,orderPlacedOn AS CREATED,orders.paymentType,orderAmount,discount,orderType,DeliveryAddress FROM orders
			INNER JOIN tbl_registeration ON tbl_registeration.id=orders.customerID 
			INNER JOIN merchant ON merchant.id=orders.merchantID 
			 WHERE (Owner_ID=".$_SESSION["User_ID"]." OR merchant.Res_AssignUserID=".$_SESSION['User_ID'].")";
			 
			$sq1="SELECT tbl_tablebooking_bookings.Book_ID AS Order_ID,Book_date AS orderDate,Book_Start_Time AS orderTime,Book_Size AS quantity,merchant.logo,tbl_registeration.firstname,tbl_registeration.display_name,tbl_registeration.email_add,tbl_registeration.mobile_phone,merchant.merchantName,merchant.contactAddress,merchant.city AS RES_CITY,merchant.state AS RES_STATE,tbl_registeration.street,tbl_registeration.city AS C_CITY,tbl_registeration.state AS C_STATE,tbl_registeration.*,2 AS MAIN,Book_Created AS CREATED,0 AS paymentType,0 AS orderAmount,0 AS discount,0 AS orderType,0 AS DeliveryAddress FROM tbl_tablebooking_bookings
			INNER JOIN tbl_registeration ON tbl_registeration.id=tbl_tablebooking_bookings.Book_Owner 
			INNER JOIN merchant ON merchant.id=tbl_tablebooking_bookings.Book_Restaurant 
			 WHERE (Book_Owner=".$_SESSION["User_ID"].")";
			if($_REQUEST['Activity_ID']<>'')
			{
				$sq.= " AND orders.merchantID=".mysql_real_escape_string($_REQUEST['Activity_ID']);
				$sq1.= " AND orders.merchantID=".mysql_real_escape_string($_REQUEST['Activity_ID']);
			}
			else
			{
			
			if($_REQUEST['fromDate']<>'') {
				//$sq.=" AND orderDate >= '".$fromDate."' AND orderDate <= '".$toDate."'";
				$sq.=" AND orderDate = '".$fromDate."'";
				$sq1.=" AND Book_date = '".$fromDate."'";
			}
			if($_REQUEST['F_Name']<>'') {
				$sq.= " AND tbl_registeration.firstname LIKE '%".mysql_real_escape_string($_REQUEST['F_Name'])."%' ";
				$sq1.= " AND tbl_registeration.firstname LIKE '%".mysql_real_escape_string($_REQUEST['F_Name'])."%' ";
			}
			if($_REQUEST['L_Name']<>'') {
				$sq.= " AND tbl_registeration.lastname LIKE '%".mysql_real_escape_string($_REQUEST['L_Name'])."%' ";
				$sq1.= " AND tbl_registeration.lastname LIKE '%".mysql_real_escape_string($_REQUEST['L_Name'])."%' ";
			}
			}
		}
		/*if($_REQUEST['Book_ID']<>'')
		{
			$Book_ID=str_ireplace("DI","",$_REQUEST['Book_ID']);
			$sq.= " AND orders.id='".mysql_real_escape_string($Book_ID)."'";
		}*/
		if($_REQUEST['Book_ID']<>'')
			$query=$sq." ORDER BY CREATED DESC";
		else
			$query=$sq." UNION ".$sq1." ORDER BY CREATED DESC";
		$arow = mysql_query($query);
	    $TotalRecordCount=mysql_num_rows($arow);		
		$Totalpages = mysql_query("SELECT * FROM `tbl_control`");
		$exec_paging= mysql_fetch_array($Totalpages);
		$this->end_count 	= $exec_paging['no_of_pages'];
		$this->Limits		= $exec_paging['results_per_page'];
		$eu = ((($page-1)*$this->Limits) -0);		
		$sql1 .= " LIMIT ".$eu.", ".$this->Limits;
		$query=$query.$sql1;
		$res = mysql_query($query);
		$contact = array();
		$i=0;
		while($aRow=mysql_fetch_array($res))
		{	
			$contact[$i]['id'] = $aRow['Order_ID'];
			$contact[$i]['merchantID']=$aRow['merchantID'];
			$contact[$i]['Owner_ID']=$aRow['Owner_ID'];
			$contact[$i]['merchantName']=$aRow['merchantName'];
			$contact[$i]['orderDate']=$aRow['orderDate'];
			$contact[$i]['orderTime']=$aRow['orderTime'];
			$contact[$i]['orderPlacedOn']=$aRow['orderPlacedOn'];
			$contact[$i]['CREATED']=$aRow['CREATED'];
			$contact[$i]['quantity']=$aRow['quantity'];
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
			$contact[$i]['MAIN'] = $aRow['MAIN'];
			
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
			WHERE tbl_tablebooking_bookings.Book_ID=".mysql_real_escape_string($_REQUEST["TxnID"])." AND Book_Owner=".$_SESSION["User_ID"]; 
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
			$sq.= " AND tbl_tablebooking_bookings.code='".mysql_real_escape_string($_REQUEST['Book_ID'])."'";
		}
		else if($_REQUEST['Activity_ID']<>'')
		{
			$sq.= " AND tbl_tablebooking_bookings.Book_Restaurant=".mysql_real_escape_string($_REQUEST['Activity_ID']);
		}
		else
		{
		if($_REQUEST['fromDate']<>'')
			//$sq.=" AND orderDate >= '".$fromDate."' AND orderDate <= '".$toDate."'";
			$sq.=" AND Book_date = '".$fromDate."'";
		if($_REQUEST['F_Name']<>'')
			$sq.= " AND tbl_registeration.firstname LIKE '%".mysql_real_escape_string($_REQUEST['F_Name'])."%' ";
		if($_REQUEST['L_Name']<>'')
			$sq.= " AND tbl_registeration.lastname LIKE '%".mysql_real_escape_string($_REQUEST['L_Name'])."%' ";
		}
		$sq.=" ORDER BY Book_Created DESC"; 
		//echo $sq;
		$arow = mysql_query($sq);
	    $TotalRecordCount=mysql_num_rows($arow);		
		$Totalpages = mysql_query("SELECT * FROM `tbl_control`");
		$exec_paging= mysql_fetch_array($Totalpages);
		$this->end_count 	= $exec_paging['no_of_pages'];
		$this->Limits		= $exec_paging['results_per_page'];
		$eu = ((($page-1)*$this->Limits) -0);		
		$sql1 .= " LIMIT ".$eu.", ".$this->Limits;
		$query=$sq.$sql1;
		$res = mysql_query($query);
		$contact = array();
		$i=0;
		while($aRow=mysql_fetch_array($res))
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
			 WHERE orders.id=".mysql_real_escape_string($_REQUEST["TxnID"]); 
			
		}
		
		$arow = mysql_query($sq);
	    $TotalRecordCount=mysql_num_rows($arow);		
		$Totalpages = mysql_query("SELECT * FROM `tbl_control`");
		$exec_paging= mysql_fetch_array($Totalpages);
		$this->end_count 	= $exec_paging['no_of_pages'];
		$this->Limits		= $exec_paging['results_per_page'];
		$eu = ((($page-1)*$this->Limits) -0);		
		$sql1 .= " LIMIT ".$eu.", ".$this->Limits;
		$query=$sq.$sql1;
		$res = mysql_query($query);
		$contact = array();
		$i=0;
		while($aRow=mysql_fetch_array($res))
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
		if($TxnID!='')
		{
			$query="SELECT orders.id AS Order_ID,merchant.logo,tbl_registeration.firstname,tbl_registeration.display_name,tbl_registeration.email_add,orders.*,merchant.merchantName,merchant.contactAddress,merchant.city AS RES_CITY,merchant.state AS RES_STATE,tbl_registeration.street,tbl_registeration.city AS C_CITY,tbl_registeration.state AS C_STATE,merchant.postalCode,merchant.telephone,orders.PaymentDesc FROM orders
			INNER JOIN tbl_registeration ON tbl_registeration.id=orders.customerID
			INNER JOIN merchant ON merchant.id=orders.merchantID 			
			 WHERE orders.id=".$TxnID; 
			
		}
		
		$res = mysql_query($query);
		$TotalRecordCount=mysql_num_rows($res);
		$contact = array();
		$i=0;
		while($aRow=mysql_fetch_array($res))
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
		$sql="SELECT order_items.*,items.itemName,items.Cus_Pizza,orders.PaymentDesc,menus.menuName FROM order_items 
		INNER JOIN items ON items.id=order_items.itemID 
		INNER JOIN orders ON orders.id=order_items.orderID 
		INNER JOIN menus ON menus.id=items.menuID 
		WHERE orderID=".$order_ID;
		$exe=mysql_query($sql);
		$num=mysql_num_rows($exe);
		$i=0;
		$record=array();
		while($rec=mysql_fetch_array($exe))
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
		$sql="SELECT * FROM order_subitems WHERE orderItemID=".$orderItemID;
		$exe=mysql_query($sql);
		$num=mysql_num_rows($exe);
		$i=0;
		$record=array();
		while($rec=mysql_fetch_array($exe))
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
			$sql="Select * from tbl_coupons WHERE Coupon_UserId=".$_SESSION['User_ID']." AND Coupon_Restaurant=".mysql_real_escape_string($_REQUEST['Res_ID'])." order by Coupon_Name ASC";
			$result = mysql_query($sql);
			$TotalRecordCount=mysql_num_rows($result);
			$Totalpages 		= mysql_query("SELECT * FROM `tbl_control`");
			$exec_paging		= mysql_fetch_array($Totalpages);
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
			$Res=mysql_query($query);
			$Record	=	array();
			$i			=	0;
			while($res=mysql_fetch_array($Res)) {
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
		
		if($_REQUEST['Coupon_ID']>0)
		{
			$sql="UPDATE tbl_coupons SET Coupon_Name='".mysql_real_escape_string($_REQUEST['Coupon_Name'])."',Coupon_Expiry='".date("Y-m-d",strtotime($_REQUEST['Coupon_Expiry']))." 00:00:00"."',Coupon_Type='".mysql_real_escape_string($_REQUEST['Coupon_Type'])."',Coupon_Amount='".mysql_real_escape_string($_REQUEST['Coupon_Amount'])."',Coupon_Limit='".$_REQUEST['Coupon_Limit']."',Coupon_Redeem='".$_REQUEST['Coupon_Redeem']."',Coupon_Desc='".$_REQUEST['Coupon_Desc']."' WHERE Coupon_ID=".$_REQUEST['Coupon_ID'];
			mysql_query($sql);
			$sql="UPDATE merchant SET coupons_expiry='".date("Y-m-d",strtotime($_REQUEST['Coupon_Expiry']))." 00:00:00"."' WHERE id=".$_REQUEST['Res_ID'];
			mysql_query($sql);
		}
		else
		{
			$sql = "INSERT INTO tbl_coupons (Coupon_Name,Coupon_Expiry,Coupon_Type,Coupon_Amount,Coupon_Limit,Coupon_Redeem,Coupon_UserId,Coupon_Restaurant,Coupon_Desc,Coupon_Status,Coupon_Created) VALUES ('".mysql_real_escape_string($_REQUEST['Coupon_Name'])."','".date("Y-m-d",strtotime($_REQUEST['Coupon_Expiry']))." 00:00:00"."','".$_REQUEST['Coupon_Type']."','".mysql_real_escape_string($_REQUEST['Coupon_Amount'])."','".mysql_real_escape_string($_REQUEST['Coupon_Limit'])."','".mysql_real_escape_string($_REQUEST['Coupon_Redeem'])."',".$_SESSION["User_ID"].",".mysql_real_escape_string($_REQUEST['Res_ID']).",'".mysql_real_escape_string($_REQUEST['Coupon_Desc'])."','1',Now())";
			mysql_query($sql);
			$query="SELECT id,coupons FROM merchant WHERE id=".$_REQUEST['Res_ID'];
			$exe=mysql_query($query);
			$res=mysql_fetch_array($exe);
			$CNT=$res['coupons'];
			$CNT++;
			$sql="UPDATE merchant SET coupons=".$CNT.",coupons_expiry='".date("Y-m-d",strtotime($_REQUEST['Coupon_Expiry']))." 00:00:00"."', WHERE id=".$_REQUEST['Res_ID'];
			mysql_query($sql);
		}
				
		echo '<script language="javascript">location.href="dine.php?act=coupons&Res_ID='.$_REQUEST['Res_ID'].'&rep=1";</script>'; 
		exit;
	}
	function Delete_Coupon()
	{
		$qry="DELETE FROM tbl_coupons WHERE Coupon_ID=".mysql_real_escape_string($_REQUEST['Coupon_ID']);
	    mysql_query($qry);
		
		// coupons cnt in merchant
		$query="SELECT id,coupons FROM merchant WHERE id=".mysql_real_escape_string($_REQUEST['Res_ID']);
		$exe=mysql_query($query);
		$res=mysql_fetch_array($exe);
		$CNT=$res['coupons'];
		$CNT--;
		if($CNT<0)	
			$CNT=0;
		$sql="UPDATE merchant SET coupons=".$CNT." WHERE id=".mysql_real_escape_string($_REQUEST['Res_ID']);
		mysql_query($sql);
	    echo '<script language="javascript">location.href="dine.php?act=coupons&Res_ID='.$_REQUEST['Res_ID'].'";</script>'; 
		exit;
	}
	function Coupon_Status()
	{
	   $qry="UPDATE tbl_coupons SET Coupon_Status=".mysql_real_escape_string($_GET[status])." WHERE Coupon_ID=".mysql_real_escape_string($_REQUEST['Coupon_ID']); 
	   mysql_query($qry);
	   // coupons cnt in merchant
	   $query="SELECT id,coupons FROM merchant WHERE id=".mysql_real_escape_string($_REQUEST['Res_ID']);
	   $exe=mysql_query($query);
	   $res=mysql_fetch_array($exe);
	   $CNT=$res['coupons'];
	   if($_GET[status]==0)
		$CNT--;
	   else
		$CNT++;
	   	if($CNT<0)	
			$CNT=0;
		$sql="UPDATE merchant SET coupons=".$CNT." WHERE id=".mysql_real_escape_string($_REQUEST['Res_ID']);
		
		mysql_query($sql);
    }
   
   function Update_OrderStatus()
   {
	   $Order_ID=mysql_real_escape_string($_REQUEST['Order_ID']);
	   $orderStatus=mysql_real_escape_string($_REQUEST['orderStatus']);
	   $i=0;
		foreach($Order_ID as $Order_IDs)
		{
			$qry="UPDATE orders SET orderStatus='".$orderStatus[$i]."' WHERE id=".$Order_IDs; 
			mysql_query($qry);
			$i++;
		}
		
		echo '<script language="javascript">location.href="dine.php?act=reser";</script>'; 
		exit;
	   
   }
   function GetCoupons($rest)
	{
		$dt = date('Y-m-d H:i:s');
		$sql="SELECT * FROM tbl_coupons WHERE Coupon_Status=1 AND Coupon_Restaurant=".$rest." AND Coupon_Expiry>='".$dt."'";
		$exe=mysql_query($sql);
		$num=mysql_num_rows($exe);
		$i=0;
		$record=array();
		while($rec=mysql_fetch_array($exe))
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
		$sql = "SELECT Points FROM tbl_registeration WHERE id=".$_SESSION['User_ID'];
		$res = @mysql_query($sql);
		$row = @mysql_fetch_array($res);
		if($row['Points']>0)
			return $row['Points'];
		else
			return 0;
	}
	function GetDiscount($coupon)
	{
		$sql="SELECT * FROM tbl_coupons WHERE Coupon_ID=".$coupon;
		$exe=mysql_query($sql);
		$CC=mysql_fetch_array($exe);
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
		$sql="SELECT closed FROM merchant_hours WHERE merchantID=".$id." AND weekDay=".$week;
		$exe=mysql_query($sql);
		$CC=mysql_fetch_array($exe);
		return $CC['closed'];
	}
	function Restaurant_OpenHours($id,$week,$orderfor)
	{
		if($orderfor=='Delivery')
			$sql="SELECT deliveryStartTime AS ST,deliveryEndTime AS ET,deliveryStartTimeOther AS STO,deliveryEndTimeOther AS ETO,delivery24hours,pickup24hours FROM merchant_hours WHERE merchantID=".$id." AND weekDay=".$week;
		else
			$sql="SELECT pickupStartTime AS ST,pickupEndTime AS ET,pickupStartTimeOther AS STO,pickupEndTimeOther AS ETO,pickup24hours,pickup24hours FROM merchant_hours WHERE merchantID=".$id." AND weekDay=".$week;
		//echo $sql;
		$exe=mysql_query($sql);
		$CC=mysql_fetch_array($exe);
		if($CC['delivery24hours']=='Yes') {
		$ST='00:15:00';$ET='00:00:00'; }
		else if($CC['ST']==$CC['ET']) {
			$ST="";$ET=""; } 
		else {$ST=$CC['ST'];$ET=$CC['ET']; }
		
		if($CC['pickup24hours']=='Yes') {
		$ST='00:15:00';$ET='00:00:00'; }
		else if($CC['STO']==$CC['ETO']) {
			$STO="";$ETO=""; } 
		else {$STO=$CC['STO'];$ETO=$CC['ETO']; }
		return array($ST,$ET,$STO,$ETO);
	}
	function sendFax($orderID = 0) {

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
		$qry="UPDATE menus SET menuStatus='".$_REQUEST['menu_status']."' WHERE id=".mysql_real_escape_string($_REQUEST['Menu_ID']);
		mysql_query($qry);
	}
	function MakeRestaurantStatus()
	{
		$sql="UPDATE merchant SET status='".$_REQUEST['res_status']."' WHERE id=".$_REQUEST['id'];
		
		mysql_query($sql);
	}
	function MakeMenuItemStatus()
	{
		
		$sql="UPDATE items SET itemStatus='".$_REQUEST['item_status']."' WHERE id=".$_REQUEST['id'];
		
		mysql_query($sql);
	}
	function Traffic_Status()
	 {
		  $sql="Select merchant.* from merchant WHERE Deleted=0 AND Res_UserID=".$_SESSION[User_ID]." order by createdOn desc"; 
		  $result = mysql_query($sql) or die(mysql_error());
		  $TotalRecordCount=mysql_num_rows($result);
	
			$Totalpages 		= mysql_query("SELECT * FROM `tbl_control`");
			$exec_paging		= mysql_fetch_array($Totalpages);
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
			$Res=mysql_query($query) or die (mysql_error());
			$Record	=	array();
			$i			=	0;
			while($res=mysql_fetch_array($Res)) {
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
		$sql="SELECT code FROM orders WHERE code='".mysql_real_escape_string($_REQUEST['R_Code'])."' AND id=".mysql_real_escape_string($_REQUEST['TxnID']);
		$exe=mysql_query($sql);
		$num=mysql_num_rows($exe);
		if($num>0)
			return 1;
		else
			return 0;
	}
	function VerifyReedeemCodetablebooking()
	{
		$sql="SELECT code FROM tbl_tablebooking_bookings WHERE code='".mysql_real_escape_string($_REQUEST['R_Code'])."' AND Book_ID=".mysql_real_escape_string($_REQUEST['TxnID']);
		$exe=mysql_query($sql);
		$num=mysql_num_rows($exe);
		if($num>0)
			return 1;
		else
			return 0;
	}
	function UpdateParkingReedeemtablebooking()
	{
		$sql="SELECT code FROM tbl_tablebooking_bookings WHERE code='".mysql_real_escape_string($_REQUEST['R_Code'])."' AND Book_ID=".mysql_real_escape_string($_REQUEST['TxnID']);
		
		$exe=mysql_query($sql);
		$num=mysql_num_rows($exe);
		$res=mysql_fetch_array($exe);
		if($num>0)
		{
			if($res['orderType']=='TakeOut')
				$orderStatus="Picked Up";
			else
				$orderStatus="Delivered";
			$sql="UPDATE tbl_tablebooking_bookings SET Redeem=1 WHERE Book_ID=".mysql_real_escape_string($_REQUEST['TxnID']);
			mysql_query($sql);
			
			return 1;
		}
		else
		{
			return 0;
		}
	}
	function UpdateParkingReedeem()
	{
		$sql="SELECT code FROM orders WHERE code='".mysql_real_escape_string($_REQUEST['R_Code'])."' AND id=".mysql_real_escape_string($_REQUEST['TxnID']);
		$exe=mysql_query($sql);
		$num=mysql_num_rows($exe);
		$res=mysql_fetch_array($exe);
		if($num>0)
		{
			if($res['orderType']=='TakeOut')
				$orderStatus="Picked Up";
			else
				$orderStatus="Delivered";
			$sql="UPDATE orders SET Redeem=1,orderCompleted='Yes',orderStatus='".$orderStatus."' WHERE id=".mysql_real_escape_string($_REQUEST['TxnID']);
			mysql_query($sql);
			
			return 1;
		}
		else
		{
			return 0;
		}
	}
	function Updatepaystatus()
	{
		GLOBAL $reply_mail,$adminmail;
		if($_REQUEST['DeliveryMethod']=='Electronic Ticket')
		{
		$sql="UPDATE tbl_paymenttransaction SET OrderStatus='Completed',DeliveryEmail='".$_REQUEST['Email']."' WHERE TxnID=".mysql_real_escape_string($_REQUEST['TxnID']);
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
		$sql="UPDATE orders SET orderStatus='".$orderStatus."',comments='".mysql_real_escape_string($_REQUEST['Desc'])."' WHERE id=".mysql_real_escape_string($_REQUEST['TxnID']);
		
				
		mysql_query($sql);
		return 1;
	}
	function CheckDeliveryDetails($User_ID,$Email,$street,$city,$state,$zipcode)
		{
			$sql="SELECT DE_EmailID FROM tbl_deliveryEmail WHERE DE_EmailID='".$Email."' AND DE_UserID=".$User_ID;
			$exe=mysql_query($sql);
			$num=mysql_num_rows($exe);
			if($num<1)
			{
				$sql="INSERT INTO tbl_deliveryEmail(DE_UserID,DE_EmailID,DE_Created)VALUES(".$User_ID.",'".$Email."',Now())";
				mysql_query($sql);
			}
			$sql="SELECT DE_Address FROM tbl_deliveryaddress WHERE DE_Address='".$street."' AND DE_Zipcode='".$zipcode."' AND DE_State='".$state."' AND DE_City='".$city."' AND DE_UserID=".$User_ID;
			$exe=mysql_query($sql);
			$num=mysql_num_rows($exe);
			if($num<1)
			{
				$sql="INSERT INTO tbl_deliveryaddress(DE_UserID,DE_Address,DE_State,DE_City,DE_Zipcode,DE_Created)VALUES(".$User_ID.",'".mysql_real_escape_string($street)."','".mysql_real_escape_string($state)."','".mysql_real_escape_string($city)."','".mysql_real_escape_string($zipcode)."',Now())";
				mysql_query($sql);
			}
		
		}
	
	function GetDeliveryZipcodes($merchant)
	{
		$sql="SELECT * FROM deliveryzipcode_fees WHERE merchantID=".$merchant;
		$exe=mysql_query($sql);
		$num=mysql_num_rows($exe);
		$Fee = array();
		$i=0;
		while ($result=mysql_fetch_array($exe))
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
		$sql="SELECT * FROM tbl_deliveryaddress WHERE DE_UserID=".$User_ID;
		$exe=mysql_query($sql);
		$num=mysql_num_rows($exe);
		$cart = array();
		$i=0;
		while ($result=mysql_fetch_array($exe))
		{
		    $cart[$i]['DE_ID'] = $result['DE_ID'];
			$cart[$i]['DE_Name'] = $result['DE_Name']; //changed by logictree
			$cart[$i]['DE_Address'] = $result['DE_Address'];
			$cart[$i]['DE_State'] = $result['DE_State'];
			$cart[$i]['DE_City'] = $result['DE_City'];
			$cart[$i]['DE_Zipcode'] = $result['DE_Zipcode'];
			$cart[$i]['DE_Phone'] = $result['DE_Phone']; //changed by logictree
			$i++;
		}
		return array($cart,$num);
	}
	function View_DineReviews() {
			$sql="SELECT * FROM tbl_reviews WHERE list_id=".mysql_real_escape_string($_REQUEST['id'])." AND R_Type='Dine' ";
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
			
			$result = mysql_query($sql) or die(mysql_error());
			$TotalRecordCount=mysql_num_rows($result);
			$Totalpages 		= mysql_query("SELECT * FROM `tbl_control`");
			$exec_paging		= mysql_fetch_array($Totalpages);
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
			$Res=mysql_query($query) or die (mysql_error());
			$Record	=	array();
			$i			=	0;
			while($res=mysql_fetch_array($Res)) {
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
		//help_cnt
		$sql="SELECT help_cnt FROM tbl_reviews WHERE Review_ID=".mysql_real_escape_string($_REQUEST['Review_ID']);
		$exe=mysql_query($sql);
		$res=mysql_fetch_array($exe);
		if($res['help_cnt']>0)
			$help_cnt=$res['help_cnt'];
		else
			$help_cnt=0;
		$help_cnt++;
		$sql="UPDATE tbl_reviews SET help_cnt=".$help_cnt." WHERE Review_ID=".mysql_real_escape_string($_REQUEST['Review_ID']);
		mysql_query($sql);
		$sql="INSERT INTO tbl_reviewhelpful(Help_Review,Help_Restaurant,Help_UserID,Help_Created)VALUES(".mysql_real_escape_string($_REQUEST['Review_ID']).",'".mysql_real_escape_string($_REQUEST['ID'])."','".$SESSION['User_ID']."',Now())";
		mysql_query($sql);
	}
	function AddLike()
	{
		$sql="INSERT INTO tbl_like(Like_Restaurant,Like_User,Like_Created)VALUES('".mysql_real_escape_string($_REQUEST['ID'])."','".$_SESSION['User_ID']."',Now())";
		mysql_query($sql);
	}
	function GetCuisineImages()
	{
		$sql="Select * from tbl_cuisine WHERE Cuisine_Status=1 AND Cuisine_Image<>'' ORDER BY Cuisine_Name ASC";
		$result = mysql_query($sql);
		$TotalRecordCount=mysql_num_rows($result);
		$i=0;
		$Record	=array();
		while($res=mysql_fetch_array($result)) {
			$Record[$i]['Cuisine_ID']=$res['Cuisine_ID'];
			$Record[$i]['Cuisine_Name']=$res['Cuisine_Name'];
			$Record[$i]['Cuisine_Image']=$res['Cuisine_Image'];
			$i++;
		}
		return array($Record,$TotalRecordCount);
	}
	function GetBrowseRestbycity()
	{
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
		$result = mysql_query($sql);
		$TotalRecordCount=mysql_num_rows($result);
		$i=0;
		$Record	=array();
		while($res=mysql_fetch_array($result)) {
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
		$sql="Select * from tbl_cuisine WHERE Cuisine_Status=1 ORDER BY RAND()";
		if($limit>0)
			$sql.=" LIMIT ".$limit;
		$result = mysql_query($sql);
		$TotalRecordCount=mysql_num_rows($result);
		$i=0;
		$Record	=array();
		while($res=mysql_fetch_array($result)) {
			$Record[$i]['Cuisine_ID']=$res['Cuisine_ID'];
			$Record[$i]['Cuisine_Name']=$res['Cuisine_Name'];
			$Record[$i]['Cuisine_Image']=$res['Cuisine_Image'];
			$i++;
		}
		return array($Record,$TotalRecordCount);
	}
}
?>