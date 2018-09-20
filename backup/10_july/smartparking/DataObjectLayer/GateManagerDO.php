<?php


class GateManagerDO {

private $p_GateID= null;
private $p_GateName= null;
private $p_GateType= null;
private $p_GarageID= null;
private $p_SocketMac= null;
private $p_BeaconMac= null;
private $p_TxMax= null;
private $p_OS= null; 
 
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

protected function GateName($value = "")
{
if (empty($value)) // getter
	{
		if ($this-> p_GateName != null)
			return $this->p_GateName;
	}
	else // setter
	{
		$this-> p_GateName = $value;
	}

	return null;
}

protected function GateType($value = "")
{
if (empty($value)) // getter
	{
		if ($this-> p_GateType != null)
			return $this->p_GateType;
	}
	else // setter
	{
		$this-> p_GateType = $value;
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



protected function SocketMac($value = "")
{
if (empty($value)) // getter
	{
		if ($this-> p_SocketMac != null)
			return $this->p_SocketMac;
	}
	else // setter
	{
		$this-> p_SocketMac = $value;
	}

	return null;
}

protected function BeaconMac($value = "")
{
if (empty($value)) // getter
	{
		if ($this-> p_BeaconMac != null)
			return $this->p_BeaconMac;
	}
	else // setter
	{
		$this-> p_BeaconMac = $value;
	}

	return null;
}


protected function TxMax($value = "")
{
if (empty($value)) // getter
	{
		if ($this-> p_TxMax != null)
			return $this->p_TxMax;
	}
	else // setter
	{
		$this-> p_TxMax = $value;
	}

	return null;
}


protected function OS($value = "")
{
if (empty($value)) // getter
	{
		if ($this-> p_OS != null)
			return $this->p_OS;
	}
	else // setter
	{
		$this-> p_OS = $value;
	}

	return null;
}


protected function ActionType($value = "")
{
if (empty($value)) // getter
	{
		if ($this-> p_ActionType != null)
			return $this->p_ActionType;
	}
	else // setter
	{
		$this-> p_ActionType = $value;
	}

	return null;
}

 
  
}

?>
