<?php
//***getting sub items
include ('config.php');
//include '../common/config.php';
include ('function.php');
include "../dine/dineclassbk.php";
header('Content-Type: application/json');
$objdine = new Dine();
$item=$_REQUEST['item'];
        $subitems = $objdine->GetSubItemsGroupname($item);
        if($subitems[1]>0) {
            $subitem_array=array();
            $subgroup_name_array=array();
            foreach($subitems[0] as $subitem) {
                $subgroupname=$subgroup_name_array[]=SubGroupname($subitem['subgroup_id']);
                $subitems1 = $objdine->GetSubItemsGroupnameItems($item,$subitem['subgroup_id']);
                    foreach($subitems1[0] as $subitem1){
                        $subitem_array[$subgroupname][]=array("id"=>$subitem1['id'],"subgroupid"=>$subitem['subgroup_id'],"itemid"=>$subitem['item_id'],"subitemname"=>$subitem1['subitemName'],"subitemprice"=>$subitem1['subitemPrice']);
                    }
            }

        }
        else{
            $subitem_array=array();
            $subgroup_name_array=array();
        }
$content=array("status" => "1","subitems"=>$subitem_array,"subgroup_name"=>$subgroup_name_array);
echo json_encode($content);
exit;
?>
