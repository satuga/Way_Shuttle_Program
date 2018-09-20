<?php  
/************************************************************
FILE :	GateManagerDA.php
PURPOSE: data acess for smart parking transactions 
AUTHOR: Gopalan Mani
DATE  : 21 JUNE 2016
**************************************************************/
//include("../DataObjectLayer/GateManagerDO.php");

class GateManagerDA
{
 
function AddGateInfo(GateManagerDO $ObjGateInfo)
	{ 
		GLOBAL $DBSERVER,$DBUSER,$DBPWD,$DBDATABASE;
		try 
		{
			$dbh = new PDO("mysql:host=$DBSERVER;dbname=$DBDATABASE", $DBUSER, $DBPWD);
			$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$stmt = $dbh->prepare("CALL USP_InsertSmartGate(:txt_SGT_GateName,:txt_SGT_GateType,:txt_SGT_GarageID,:txt_SGT_SocketMac,:txt_SGT_BeaconMac,:txt_SGT_TxMax,:txt_SGT_OS)");
			
			//$stmt->bindParam(':txt_SGT_GateID', $ObjGateInfo->GateID); 
			$stmt->bindParam(':txt_SGT_GateName', $ObjGateInfo->GateName); 
			$stmt->bindParam(':txt_SGT_GateType', $ObjGateInfo->GateType); 
			$stmt->bindParam(':txt_SGT_GarageID', $ObjGateInfo->GarageID);
			$stmt->bindParam(':txt_SGT_SocketMac', $ObjGateInfo->SocketMac); 
			$stmt->bindParam(':txt_SGT_BeaconMac', $ObjGateInfo->BeaconMac);
			$stmt->bindParam(':txt_SGT_TxMax', $ObjGateInfo->TxMax); 
			$stmt->bindParam(':txt_SGT_OS', $ObjGateInfo->OS);
			 
			$stmt->execute();
			$result = $stmt->fetchAll();
			
			$GateID =-1;
			foreach($result as $Gr=>$GO)
			{
			  $GateID = $GO['GateID'];
			}
			$stmt= null;
			$dbh = null;
		
			return $GateID;
	   
		}
			
		catch(PDOException  $e)
		{
			return -1;
			echo "<br>error :".$e->getMessage();
		}
	
	}
	
	function UpdateGateInfo(GateManagerDO $ObjGateInfo)
	{ 
		GLOBAL $DBSERVER,$DBUSER,$DBPWD,$DBDATABASE;
		try 
		{
			$dbh = new PDO("mysql:host=$DBSERVER;dbname=$DBDATABASE", $DBUSER, $DBPWD);
			$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$stmt = $dbh->prepare("CALL USP_UpdateSmartGate(:txt_SGT_GateID,:txt_SGT_GateName,:txt_SGT_GateType,:txt_SGT_GarageID,:txt_SGT_SocketMac,:txt_SGT_BeaconMac,:txt_SGT_TxMax,:txt_SGT_OS)");
			
			$stmt->bindParam(':txt_SGT_GateID', $ObjGateInfo->GateID); 
			$stmt->bindParam(':txt_SGT_GateName', $ObjGateInfo->GateName); 
			$stmt->bindParam(':txt_SGT_GateType', $ObjGateInfo->GateType); 
			$stmt->bindParam(':txt_SGT_GarageID', $ObjGateInfo->GarageID);
			$stmt->bindParam(':txt_SGT_SocketMac', $ObjGateInfo->SocketMac); 
			$stmt->bindParam(':txt_SGT_BeaconMac', $ObjGateInfo->BeaconMac);
			$stmt->bindParam(':txt_SGT_TxMax', $ObjGateInfo->TxMax); 
			$stmt->bindParam(':txt_SGT_OS', $ObjGateInfo->OS);
			 
			$stmt->execute();
			$result = $stmt->fetchAll();
			
			$GateID =-1;
			foreach($result as $Gr=>$GO)
			{
			  $GateID = $GO['GateID'];
			}
			$stmt= null;
			$dbh = null;
		
			return $GateID;
	   
		}
			
		catch(PDOException  $e)
		{
			return -1;
			
		}
	
	}
	 
	 
	 
	 function DeleteSmartGate($Gate_ID)
	{ 
		GLOBAL $DBSERVER,$DBUSER,$DBPWD,$DBDATABASE;
		try 
		{
			$dbh = new PDO("mysql:host=$DBSERVER;dbname=$DBDATABASE", $DBUSER, $DBPWD);
			$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$stmt = $dbh->prepare("CALL USP_DeleteSmartGate(:txt_SGT_GateID)");
			
			$stmt->bindParam(':txt_SGT_GateID', $Gate_ID); 
			 
			$stmt->execute();
			$result = $stmt->fetchAll();
			
			$$ReturnValue =0;
			foreach($result as $Gr=>$GO)
			{
			  $ReturnValue = $GO['Status'];
			}
			$stmt= null;
			$dbh = null;
		
			return $ReturnValue;
	   
		}
			
		catch(PDOException  $e)
		{
			return -1;
			
		}
	
	}
	
	function GetGateList()
	{
		try 
		{
   			GLOBAL $DBSERVER,$DBUSER,$DBPWD,$DBDATABASE;
			$dbh = new PDO("mysql:host=$DBSERVER;dbname=$DBDATABASE", $DBUSER, $DBPWD);
			$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$stmt = $dbh->prepare("CALL USP_GetGateList()");;
				 
			$stmt->execute();

			$result = $stmt->fetchAll();
			$i=0;
			$ObjGateList = array();
			foreach($result as $Gr=>$GO)
			{
				$ObjGateList[$i]['ID'] = $GO['SGT_ID'];
				$ObjGateList[$i]['GateName'] = $GO['SGT_GateName'];
				$ObjGateList[$i]['GateType'] = $GO['SGT_GateType'];
				$ObjGateList[$i]['arageID'] = $GO['SGT_GarageID'];
				$ObjGateList[$i]['SocketMac'] = $GO['SGT_SocketMac'];
				$ObjGateList[$i]['BeaconMac'] = $GO['SGT_BeaconMac'];
				$ObjGateList[$i]['TxMax'] = $GO['SGT_TxMax'];
				$ObjGateList[$i]['OS'] = $GO['SGT_OS'];
				$ObjGateList[$i]['CreatedDateTime'] = $GO['SGT_CreatedDateTime'];
				
				$i++;
			}
			 
			$stmt= null;
			$dbh = null;
			
			return $ObjGateList;
		}
		catch(PDOException  $e)
		{
			return -1;
		}
	
	}
	
	
	function GetSmartGateLocations()
	{
		try 
		{
   			GLOBAL $DBSERVER,$DBUSER,$DBPWD,$DBDATABASE;
			$dbh = new PDO("mysql:host=$DBSERVER;dbname=$DBDATABASE", $DBUSER, $DBPWD);
			$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$stmt = $dbh->prepare("CALL USP_GetSmartGateLocations()");;
				 
			$stmt->execute();

			$result = $stmt->fetchAll();
			$i=0;
			$ObjLocationList = array();
			foreach($result as $Gr=>$GO)
			{
				$ObjLocationList[$i]['GarageID'] = $GO['P_ID'];
				$ObjLocationList[$i]['GarageName'] = $GO['Park_Name'];
				$ObjLocationList[$i]['Address'] = $GO['Park_Address'];
				$ObjLocationList[$i]['City'] = $GO['Park_City'];
				$ObjLocationList[$i]['State'] = $GO['Park_State'];
				$ObjLocationList[$i]['Zip'] = $GO['Park_Zip'];
				 
				
				$i++;
			}
			 
			  
			 
			$stmt= null;
			$dbh = null;
			
			return $ObjLocationList;
		}
		catch(PDOException  $e)
		{
			return -1;
		}
	
	}
	
	
	 
}


	
	?>