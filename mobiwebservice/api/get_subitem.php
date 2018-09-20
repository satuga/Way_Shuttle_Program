<?php
  //***getting sub items
  include ('config.php');
  //include '../common/config.php';
  include ('function.php');
  include "../dine/dineclassbk.php";
  header('Content-Type: application/json');
  $objdine = new Dine();
  $item=sanitize($_REQUEST['item']);
  $cus_pizza = sanitize($_REQUEST['cus_pizza']); // define it is create own pizza so add crust staticly
  $subitems = $objdine->GetSubItemsGroupname($item);
  
  if($subitems[1]>0) {
    $subitem_array=array();
    $subgroup_name_array=array();
	/*if($cus_pizza==1)
	{
		$array=array("Thin","Thick","Regular");
		foreach($array as $key=>$val)
			$subitem_array['Crust'][]=array("id"=>'0',"subgroupid"=>'0',"itemid"=>'0',"subitemname"=>$val,	"subitemprice"=>'0');
		$subgroup_name_array[]="Crust";
		
	}*/
    foreach($subitems[0] as $subitem) {
		//var_dump($subitem['subgroup_id']);
      $subgroupname=$subgroup_name_array[]=SubGroupname($subitem['subgroup_id']);
	  //var_dump(SubGroupname($subitem['subgroup_id']));die;
      $subitems1 = $objdine->GetSubItemsGroupnameItems($item,$subitem['subgroup_id']);
      foreach($subitems1[0] as $subitem1){
		  
		  if(isset($subgroupname) && !empty($subgroupname)) {
			$subitem_array[$subgroupname][]=array("id"=>$subitem1['id'],"subgroupid"=>$subitem['subgroup_id'],"itemid"=>$subitem['item_id'],"subitemname"=>$subitem1['subitemName'],"subitemprice"=>$subitem1['subitemPrice']);
		  }else{
			$subitem_array['Group'][]=array("id"=>$subitem1['id'],"subgroupid"=>$subitem['subgroup_id'],"itemid"=>$subitem['item_id'],"subitemname"=>$subitem1['subitemName'],"subitemprice"=>$subitem1['subitemPrice']);
		  }
    }
		/*foreach($subitems1[0] as $subitem1){
			if(isset($subitem1['id']) && isset($subitem1['id'])  != NULL) {
				$subitem_array[$subgroupname][]=array("id"=>$subitem1['id'],"subgroupid"=>$subitem['subgroup_id'],"itemid"=>$subitem['item_id'],"subitemname"=>$subitem1['subitemName'],"subitemprice"=>$subitem1['subitemPrice']);
			}
		}*/
    }
	
  }
  else{
    $subitem_array=array();
    $subgroup_name_array=array();
  }
  $response = array();
  if(!empty(array_filter($subgroup_name_array))){
	  $response['status'] = '1';
	  $response['subitems'] = $subitem_array;
	  $response['subgroup_name'] = $subgroup_name_array;
  }else{
	  $response['status'] = '1';
	  $response['subitems'] = $subitem_array;
	  
  }
  
  //$content=array("status" => "1","subitems"=>$subitem_array,"subgroup_name"=>$subgroup_name_array);
  //print_r($subgroup_name_array); exit;
  /*if(!empty(array_filter($subgroup_name_array))){
	$content=array("status" => "1","subitems"=>$subitem_array,"subgroup_name"=>$subgroup_name_array);
  } else{
	$content=array("status" => "1","subitems"=>$subitem_array);
  }*/
  echo json_encode($response);
  exit;

?>
