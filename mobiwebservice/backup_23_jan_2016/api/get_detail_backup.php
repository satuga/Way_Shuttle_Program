<?php
//header("Content-Type:Application/json");
include ('config.php');
include '../common/config.php';
include ('function.php');
include "../dine/dineclassbk.php";
$objdine = new Dine();
$id = $_REQUEST['id'];
$objdine->ClearCart();
$query = "SELECT * FROM `merchant` WHERE `id`=" . $id;
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_assoc($result);
$merchant_logo_path = $Host_Path . '/admin/upload/users/';
if($row['logo']!="")
{
    $row['logo']=$merchant_logo_path.$row['logo'];
}

// get the menu of merchant full menu


$query_menu = "SELECT * FROM `menus` WHERE menuStatus='Active' and `merchantID`=" . $id;
$result_menu = mysql_query($query_menu) or die(mysql_error());
$menu_details = array();
$i = 0;
while ($row_menu = mysql_fetch_assoc($result_menu)) {
    $menu_id = $row_menu['id'];
    $menu_details[$i] = $row_menu;

    // get the menus items ///


    $query_item = "select * from items where itemStatus='Active' and menuID=" . $menu_id;
    $result_item = mysql_query($query_item) or die(mysql_error());
    $j = 0;
    $menu_items = array();
    while ($row_item = mysql_fetch_assoc($result_item)) {
        $price_array = array(); 
        if ($row_item['itemPrice']) {
            $price_array[] = array("price"=>$row_item['itemPrice'],"size"=>$row_menu['menuSize1']);
        } if ($row_item['itemPrice1']) {
            $price_array[] = array("price"=>$row_item['itemPrice1'],"size"=>$row_menu['menuSize2']);
        } if ($row_item['itemPrice2'] > 0) {
             $price_array[] = array("price"=>$row_item['itemPrice2'],"size"=>$row_menu['menuSize3']);
        } if ($row_item['itemPrice3'] > 0) {
             $price_array[] = array("price"=>$row_item['itemPrice3'],"size"=>$row_menu['menuSize4']);
        } if ($row_item['itemPrice4'] > 0) {
             $price_array[] = array("price"=>$row_item['itemPrice4'],"size"=>$row_menu['menuSize5']);
        } if ($row_item['itemPrice5'] > 0) {
             $price_array[] = array("price"=>$row_item['itemPrice5'],"size"=>$row_menu['menuSize6']);
        } if ($row_item['itemPrice6'] > 0) {
             $price_array[] = array("price"=>$row_item['itemPrice6'],"size"=>$row_menu['menuSize7']);
        } if ($row_item['itemPrice7'] > 0) {
             $price_array[] = array("price"=>$row_item['itemPrice7'],"size"=>$row_menu['menuSize8']);
        } if ($row_item['itemPrice8'] > 0) {
             $price_array[] = array("price"=>$row_item['itemPrice8'],"size"=>$row_menu['menuSize9']);
        } if ($row_item['itemPrice9'] > 0) {
             $price_array[] = array("price"=>$row_item['itemPrice9'],"size"=>$row_menu['menuSize10']);
        } if ($row_item['itemPrice10'] > 0) {
             $price_array[] = array("price"=>$row_item['menuSize11'],"size"=>$row_menu['menuSize11']);
        } if ($row_item['itemPrice11'] > 0) {
             $price_array[] = array("price"=>$row_item['itemPrice11'],"size"=>$row_menu['menuSize12']);
        } if ($row_item['itemPrice12'] > 0) {
             $price_array[] = array("price"=>$row_item['itemPrice12'],"size"=>$row_menu['menuSize13']);
        } if ($row_item['itemPrice13'] > 0) {
             $price_array[] = array("price"=>$row_item['itemPrice13'],"size"=>$row_menu['menuSize14']);
        } if ($row_item['itemPrice14'] > 0) {
             $price_array[] = array("price"=>$row_item['itemPrice14'],"size"=>$row_menu['menuSize15']);
        } if ($row_item['itemPrice15'] > 0) {
             $price_array[] = array("price"=>$row_item['itemPrice15'],"size"=>$row_menu['menuSize16']);
        } if ($row_item['itemPrice16'] > 0) {
             $price_array[] = array("price"=>$row_item['itemPrice16'],"size"=>$row_menu['menuSize17']);
        } if ($row_item['itemPrice17'] > 0) {
             $price_array[] = array("price"=>$row_item['itemPrice17'],"size"=>$row_menu['menuSize18']);
        } if ($row_item['itemPrice18'] > 0) {
             $price_array[] = array("price"=>$row_item['itemPrice18'],"size"=>$row_menu['menuSize19']);
        } if ($row_item['itemPrice19'] > 0) {
             $price_array[] = array("price"=>$row_item['itemPrice19'],"size"=>$row_menu['menuSize20']);
        }
        
        if(!count($price_array)){
            $price_array=array("price"=>"0.00","size"=>"NORMAL");
        }
        $menu_items[$j] = array(
            "item_id" => $row_item['id'],
            "item_name" => $row_item["itemName"],
            "description" => $row_item['itemDescription'],
            "item_price" => $price_array,
            "cus_pizza"=>$row_item['Cus_Pizza']
            );
        $j++;
    }
    $menu_details[$i]['items']=$menu_items;
    $i++;
}
// get the table book avaialable of table //
$table_book_query = "SELECT id,Main_Menu,Start_Time,End_Time FROM menus WHERE menuStatus='Active' AND merchantID=" .
    $id . " GROUP BY Main_Menu LIMIT 4";
$table_book_result = mysql_query($table_book_query) or die(mysql_error());
$k = 0;
$table_book = array();
while ($table_book_row = mysql_fetch_array($table_book_result)) {
    $table_book[$k]['id'] = $table_book_row['id'];
    $table_book[$k]['Main_Menu'] = $table_book_row['Main_Menu'];
    $table_book[$k]['Start_Time'] = $table_book_row['Start_Time'];
    $table_book[$k]['End_Time'] = $table_book_row['End_Time'];
    $k++;
}

//the review of business
$review_query = "select * from tbl_reviewsothersites where status=1 AND Review_Type='Dine' AND Res_ID=" .
    $id . " ORDER BY Date_Created DESC ";
$result_review = mysql_query($review_query)or die(mysql_error());
$external_review = array();
$totalrecords = mysql_num_rows($result_review);
$i = 0;
while ($review_row = mysql_fetch_array($result_review)) {
    $external_review[$i]['Review_ID'] = $review_row['Review_ID'];
    $external_review[$i]['user_id'] = $review_row['user_id'];
    $external_review[$i]['Site_Name'] = $review_row['Site_Name'];
    $external_review[$i]['Title'] = $review_row['Title'];
    $external_review[$i]['Site_URL'] = $review_row['Site_URL'];
    $external_review[$i]['status'] = $review_row['status'];
    $external_review[$i]['Date_Created'] = $review_row['Date_Created'];
    $i++;
}

$row['full_menu']=$menu_details;
$row['review']=$external_review;
$row['available_table']=$table_book;
$row['opening_times']=$objdine->Restaurant_OpenHours_allday($id,$_REQUEST['orderfor']);

echo json_encode($row);
exit;
?>