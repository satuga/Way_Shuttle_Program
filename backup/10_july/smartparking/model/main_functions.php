<?php

include("DataObjectLayer/GateParkingTransactionManagerDO.php");

class MyClass {

  
  private $_propertyName = null;
  private $ID;
  protected $ID2;

	protected function PropertyName($value = "")
	{
	if (empty($value)) // getter
		{
			if ($this-> _propertyName != null)
				return $this->_propertyName;
		}
		else // setter
		{
			$this-> _propertyName = $value;
		}

		return null;
	}




  
 
}


$object = new GateParkingTransactionManagerDO();
//$object->ID = 'foo'; //setID('foo') will be called
//$object->ID2 = 'bar'; //setID2('bar') will be called

$object->propertyName = 'bar';


//echo "<br>ID :" .$object->ID;
echo "test";
echo "<br>propertyName :" .$object->propertyName;
?>
