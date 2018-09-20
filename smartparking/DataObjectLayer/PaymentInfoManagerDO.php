<?php


class PaymentInfoManagerDO {

private $p_UserID= null;
private $p_ParkingID= null;
private $p_TotalAmount= null;
private $p_Quantity= null;
private $p_Discount= null;
private $p_ExternalFees= null;
private $p_OverNightFee= null;
private $p_PayAtLot= null;
private $p_WayFee= null;
private $p_OwnerID= null;
private $p_CartType= null;
private $p_FromDate= null;
private $p_ToDate= null;
private $p_Amount= null;
 
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

protected function ParkingID($value = "")
{
if (empty($value)) // getter
	{
		if ($this-> p_ParkingID != null)
			return $this->p_ParkingID;
	}
	else // setter
	{
		$this-> p_ParkingID = $value;
	}

	return null;
}

protected function TotalAmount($value = "")
{
if (empty($value)) // getter
	{
		if ($this-> p_TotalAmount != null)
			return $this->p_TotalAmount;
	}
	else // setter
	{
		$this-> p_TotalAmount = $value;
	}

	return null;
}

protected function Quantity($value = "")
{
if (empty($value)) // getter
	{
		if ($this-> p_Quantity != null)
			return $this->p_Quantity;
	}
	else // setter
	{
		$this-> p_Quantity = $value;
	}

	return null;
}



protected function Discount($value = "")
{
if (empty($value)) // getter
	{
		if ($this-> p_Discount != null)
			return $this->p_Discount;
	}
	else // setter
	{
		$this-> p_Discount = $value;
	}

	return null;
}

protected function ExternalFees($value = "")
{
if (empty($value)) // getter
	{
		if ($this-> p_ExternalFees != null)
			return $this->p_ExternalFees;
	}
	else // setter
	{
		$this-> p_ExternalFees = $value;
	}

	return null;
}


protected function OverNightFee($value = "")
{
if (empty($value)) // getter
	{
		if ($this-> p_OverNightFee != null)
			return $this->p_OverNightFee;
	}
	else // setter
	{
		$this-> p_OverNightFee = $value;
	}

	return null;
}


protected function PayAtLot($value = "")
{
if (empty($value)) // getter
	{
		if ($this-> p_PayAtLot != null)
			return $this->p_PayAtLot;
	}
	else // setter
	{
		$this-> p_PayAtLot = $value;
	}

	return null;
}


protected function WayFee($value = "")
{
if (empty($value)) // getter
	{
		if ($this-> p_WayFee != null)
			return $this->p_WayFee;
	}
	else // setter
	{
		$this-> p_WayFee = $value;
	}

	return null;
}




protected function CartType($value = "")
{
if (empty($value)) // getter
	{
		if ($this-> p_CartType != null)
			return $this->p_CartType;
	}
	else // setter
	{
		$this-> p_CartType = $value;
	}

	return null;
}


protected function OwnerID($value = "")
{
if (empty($value)) // getter
	{
		if ($this-> p_OwnerID != null)
			return $this->p_OwnerID;
	}
	else // setter
	{
		$this-> p_OwnerID = $value;
	}

	return null;
}


protected function FromDate($value = "")
{
if (empty($value)) // getter
	{
		if ($this-> p_FromDate != null)
			return $this->p_FromDate;
	}
	else // setter
	{
		$this-> p_FromDate = $value;
	}

	return null;
}

protected function ToDate($value = "")
{
if (empty($value)) // getter
	{
		if ($this-> p_ToDate != null)
			return $this->p_ToDate;
	}
	else // setter
	{
		$this-> p_ToDate = $value;
	}

	return null;
}


protected function Amount($value = "")
{
if (empty($value)) // getter
	{
		if ($this-> p_Amount != null)
			return $this->p_Amount;
	}
	else // setter
	{
		$this-> p_Amount = $value;
	}

	return null;
}
  
}

?>
