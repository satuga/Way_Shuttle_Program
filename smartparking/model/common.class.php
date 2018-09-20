<?php
ob_start();
require_once "class.inputfilter_clean.php";
class CommonClass {
	var $fields_val = array();
	 
   
	function fnAssignVal() {
		GLOBAL $con;
		$_FILTER["input"] = $_REQUEST;
		$_FILTER["tags"] = '';
		$_FILTER["attr"] = 'src';
		$_FILTER["tagmethod"] = 1;
		$_FILTER["attrmethod"] = 0;
		$_FILTER["xssauto"] = 'y';

		$myFilter = new InputFilter($tags, $attr, $tag_method, $attr_method, $xss_auto);
		$result = $myFilter->process($_FILTER["input"]);
		foreach($result as $key=>$val) {
			if($key != 'FCKeditor') {
				if(!empty($val)) {
					if(is_string($val)) {
						if (get_magic_quotes_gpc()) $val = stripslashes($val);
						$val = htmlspecialchars(trim($val));
					}
					
					
					if(is_string($val)) {
						$fields_val[$key] = mysqli_real_escape_string($con,$val);
					}
					else {
						$fields_val[$key] = $val;
					}
				}else {
					$fields_val[$key] = $val;
				}
			}else {
				
				$fields_val[$key] = mysqli_real_escape_string($con,$val);
			}
		}
		$this->fields_val = $fields_val;
		return $fields_val;
	}	
}//class ends.
ob_end_flush();