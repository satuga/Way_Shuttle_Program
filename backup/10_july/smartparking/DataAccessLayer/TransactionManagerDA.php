<?php  
/************************************************************
FILE :	TransactionManagerDA.php
PURPOSE: data acess for smart parking transactions 
AUTHOR: Gopalan Mani
DATE  : 18 JAN 2016
**************************************************************/

class TransactionManagerDA
{

  function UpdateGateEntryStatus($SGT_ID,$LastCheckinUserID)
	{
		GLOBAL $DBSERVER,$DBUSER,$DBPWD,$DBDATABASE;
		try 
		{
			
			$dbh = new PDO("mysql:host=$DBSERVER;dbname=$DBDATABASE", $DBUSER, $DBPWD);
			$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$stmt = $dbh->prepare("CALL USP_UpdateGateEntryStatus(:txt_SGT_ID,:txt_SGT_LastCheckinUser)");
			
			$stmt->bindParam(':txt_SGT_ID', $SGT_ID);
			$stmt->bindParam(':txt_SGT_LastCheckinUser', $LastCheckinUserID);
			  
			$stmt->execute();
			
			$stmt= null;
			$dbh = null;
			
			return $objUMDO;
		   
		}
		catch(PDOException  $e)
		{
			echo $e->getMessage();
		}
	
	}
 	
   

function GetGateStatus($SGT_ID,$SGT_GarageID)
	{ 
		GLOBAL $DBSERVER,$DBUSER,$DBPWD,$DBDATABASE;
		try 
		{
			$dbh = new PDO("mysql:host=$DBSERVER;dbname=$DBDATABASE", $DBUSER, $DBPWD);
			$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$stmt = $dbh->prepare("CALL USP_GetGateStatus(:txt_SGT_ID,:txt_SGT_GarageID)");
			
			$stmt->bindParam(':txt_SGT_ID', $SGT_ID); 
			$stmt->bindParam(':txt_SGT_GarageID', $SGT_GarageID); 
			 			
			$stmt->execute();
			$result = $stmt->fetchAll();
			
			$IsGateOpen =-1;
			foreach($result as $Gr=>$GO)
			{
			  $IsGateOpen = $GO['SGT_IsGateOpen'];
			}
			$stmt= null;
			$dbh = null;
		
			return $IsGateOpen;
	   
		}
			
		catch(PDOException  $e)
		{
			echo "<br>error :".$e->getMessage();
		}
	
	}
	
	
	function IsValidGateEntry($SGT_ID,$SGT_GarageID,$SGT_GateType)
	{ 
		GLOBAL $DBSERVER,$DBUSER,$DBPWD,$DBDATABASE;
		try 
		{
			$dbh = new PDO("mysql:host=$DBSERVER;dbname=$DBDATABASE", $DBUSER, $DBPWD);
			$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$stmt = $dbh->prepare("CALL USP_IsValidGateEntry(:txt_SGT_ID,:txt_SGT_GarageID,:txt_SGT_GateType)");
			
			$stmt->bindParam(':txt_SGT_ID', $SGT_ID); 
			$stmt->bindParam(':txt_SGT_GarageID', $SGT_GarageID); 
			$stmt->bindParam(':txt_SGT_GateType', $SGT_GateType);
 			
			$stmt->execute();
			$result = $stmt->fetchAll();
			
			$IsValid =0;
			foreach($result as $Gr=>$GO)
			{
			  $IsValid = $GO['IsValid'];
			}
			$stmt= null;
			$dbh = null;
		
			return $IsValid;
	   
		}
			
		catch(PDOException  $e)
		{
			echo "<br>error :".$e->getMessage();
		}
	
	}
	
	
	
	function CheckIsValidLogin($UserName,$Password,$EncryptKey)
	{ 
		GLOBAL $DBSERVER,$DBUSER,$DBPWD,$DBDATABASE;
		try 
		{
			$dbh = new PDO("mysql:host=$DBSERVER;dbname=$DBDATABASE", $DBUSER, $DBPWD);
			$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$stmt = $dbh->prepare("CALL USP_CheckIsValidLogin(:txtUserName,:txtPassword,:txtEncriptKey)");
			
			$stmt->bindParam(':txtUserName', $UserName); 
			$stmt->bindParam(':txtPassword', $Password); 
			$stmt->bindParam(':txtEncriptKey', $EncryptKey);
 			
			$stmt->execute();
			$result = $stmt->fetchAll();
			
			$IsValid =0;
			foreach($result as $Gr=>$GO)
			{
			  $IsValid = $GO['StatusCode'];
			}
			$stmt= null;
			$dbh = null;
		
			return $IsValid;
	   
		}
			
		catch(PDOException  $e)
		{
			echo "<br>error :".$e->getMessage();
		}
	
	}
	
	
}


	
	?>