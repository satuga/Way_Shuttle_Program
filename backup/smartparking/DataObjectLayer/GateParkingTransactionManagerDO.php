<?php

/************************************************************
FILE :	GateParkingTransactionManagerDO.php
PURPOSE: Gate Parking Transaction 
AUTHOR: Gopalan Mani
DATE  : 18 FEB 2015
**************************************************************/

class GateParkingTransactionManagerDO {
private $p_GateID= null;  
private $p_GarageID= null;
private $p_UserID= null;
private $p_UserDetails= null;
private $p_EntryMode= null;
private $p_IPAddress= null;
private $p_UserAgent= null;
private $p_HTTPUserAgent= null;
private $p_IsValidEnty=null;
private $p_FeaturedBookingID=null;

protected function FeaturedBookingID($value = "")
{
if (empty($value)) // getter
	{
		if ($this-> p_FeaturedBookingID != null)
			return $this->p_FeaturedBookingID;
	}
	else // setter
	{
		$this-> p_FeaturedBookingID = $value;
	}

	return null;
}

protected function GateID($value = "")
{
if (empty($value)) // getter
	{
		if ($this-> p_GateID != null)
			return $this->p_GateID;
	}
	else // setter
	{
		$this-> p_GateID = $value;
	}

	return null;
}

protected function IsValidEnty($value = "")
{
if (empty($value)) // getter
	{
		if ($this-> p_IsValidEnty != null)
			return $this->p_IsValidEnty;
	}
	else // setter
	{
		$this-> p_IsValidEnty = $value;
	}

	return null;
}

protected function GarageID($value = "")
{
if (empty($value)) // getter
	{
		if ($this-> p_GarageID != null)
			return $this->p_GarageID;
	}
	else // setter
	{
		$this-> p_GarageID = $value;
	}

	return null;
}

protected function UserID($value = "")
{
if (empty($value)) // getter
	{
		if ($this-> p_UserID != null)
			return $this->p_UserID;
	}
	else // setter
	{
		$this-> p_UserID = $value;
	}

	return null;
}



protected function EntryMode($value = "")
{
if (empty($value)) // getter
	{
		if ($this-> p_EntryMode != null)
			return $this->p_EntryMode;
	}
	else // setter
	{
		$this-> p_EntryMode = $value;
	}

	return null;
}

protected function IPAddress($value = "")
{
if (empty($value)) // getter
	{
		if ($this-> p_IPAddress != null)
			return $this->p_IPAddress;
	}
	else // setter
	{
		$this-> p_IPAddress = $value;
	}

	return null;
}


protected function UserAgent($value = "")
{
if (empty($value)) // getter
	{
		if ($this-> p_UserAgent != null)
			return $this->p_UserAgent;
	}
	else // setter
	{
		$this-> p_UserAgent = $value;
	}

	return null;
}


protected function HTTPUserAgent($value = "")
{
if (empty($value)) // getter
	{
		if ($this-> p_HTTPUserAgent != null)
			return $this->p_HTTPUserAgent;
	}
	else // setter
	{
		$this-> p_HTTPUserAgent = $value;
	}

	return null;
}


protected function UserDetails($value = "")
{
if (empty($value)) // getter
	{
		if ($this-> p_UserDetails != null)
			return $this->p_UserDetails;
	}
	else // setter
	{
		$this-> p_UserDetails = $value;
	}

	return null;
}
}
?>