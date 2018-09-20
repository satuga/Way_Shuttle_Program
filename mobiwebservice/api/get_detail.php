<?php

header("Content-Type: application/json");
include ('config.php');
//include '../common/config.php';
include ('function.php');
include "../dine/dineclassbk.php";
//header('Content-Type: application/json');
$objdine = new Dine();
$id = sanitize($_REQUEST['id']);
if ($id == null) {
    $content = array("status" => '0', "message" => "Missing parameter Id");
    echo json_encode($content);
    exit;
}
$objdine->ClearCart();
$t = date('d-m-Y');
$day = date("l", strtotime($t));
if ($day == 'Sunday') {
    $var = '1';
} elseif ($day == 'Monday') {
    $var = '2';
} elseif ($day == 'Tuesday') {
    $var = '3';
} elseif ($day == 'Wednesday') {
    $var = '4';
} elseif ($day == 'Thursday') {
    $var = '5';
} elseif ($day == 'Friday') {
    $var = '6';
} elseif ($day == 'Saturday') {
    $var = '7';
} else {
    
}
$query = "SELECT * FROM `merchant` WHERE `id`=" . $id;
//echo $query;
$result = mysqli_query($con, $query) or die(mysqli_error($con));
$row = mysqli_fetch_assoc($result);
if (empty($row)) {
    $content = array("status" => '0', "message" => "Records not found");
    echo json_encode($content);
    exit;
}
$merchant_logo_path = $Host_Path . '/admin/upload/users/';
/* if($row['logo']!="")
  {
  $row['logo']=$merchant_logo_path.$row['logo'];
  } */

$query_img = "SELECT items.itemImage FROM merchant
INNER JOIN menus ON menus.merchantID=merchant.id
INNER JOIN items ON items.menuID=menus.id where merchantID = '" . $id . "' and items.itemImage != '' LIMIT 0,1";
$result_img = mysqli_query($con, $query_img) or die(mysqli_error($con));
$row_img = mysqli_fetch_assoc($result_img);
if ($row_img['itemImage'] != "") {
    $row['logo'] = $row_img['itemImage'];
}



// get the menu of merchant full menu


$query_menu = "SELECT * FROM menus WHERE$row_item menuStatus='Active' and merchantID = '" . $id . "' order by Main_Menu <> 'Holla Meal', menuName ASC";
$result_menu = mysqli_query($con, $query_menu) or die(mysqli_error($con));
$menu_details = array();
$i = 0;
while ($row_menu = mysqli_fetch_assoc($result_menu)) {
    $menu_id = $row_menu['id'];
    $isholla = 0;
    if(strtolower($row_menu['Main_Menu'])== 'holla meal'){
        $isholla = 1;
    }
    $row_menu = changeHollaWithLebleu($row_menu);
    $menu_details[$i] = $row_menu;

    // get the menus items ///

    if ($isholla == '1') {
        $query_item = "select * from items where itemStatus='Active' and menuID= '" . $menu_id . "' and WeekDay = '" . $var . "'";
    } else {
        $query_item = "select * from items where itemStatus='Active' and menuID=" . $menu_id;
    }
    $result_item = mysqli_query($con, $query_item) or die(mysqli_error($con));
    $j = 0;
    $menu_items = array();
    while ($row_item = mysqli_fetch_assoc($result_item)) {
        $price_array = array();
        if ($row_item['itemPrice']) {
            $price_array[] = array("price" => $row_item['itemPrice'], "size" => $row_menu['menuSize1']);
        } if ($row_item['itemPrice1']) {
            $price_array[] = array("price" => $row_item['itemPrice1'], "size" => $row_menu['menuSize2']);
        } if ($row_item['itemPrice2'] > 0) {
            $price_array[] = array("price" => $row_item['itemPrice2'], "size" => $row_menu['menuSize3']);
        } if ($row_item['itemPrice3'] > 0) {
            $price_array[] = array("price" => $row_item['itemPrice3'], "size" => $row_menu['menuSize4']);
        } if ($row_item['itemPrice4'] > 0) {
            $price_array[] = array("price" => $row_item['itemPrice4'], "size" => $row_menu['menuSize5']);
        } if ($row_item['itemPrice5'] > 0) {
            $price_array[] = array("price" => $row_item['itemPrice5'], "size" => $row_menu['menuSize6']);
        } if ($row_item['itemPrice6'] > 0) {
            $price_array[] = array("price" => $row_item['itemPrice6'], "size" => $row_menu['menuSize7']);
        } if ($row_item['itemPrice7'] > 0) {
            $price_array[] = array("price" => $row_item['itemPrice7'], "size" => $row_menu['menuSize8']);
        } if ($row_item['itemPrice8'] > 0) {
            $price_array[] = array("price" => $row_item['itemPrice8'], "size" => $row_menu['menuSize9']);
        } if ($row_item['itemPrice9'] > 0) {
            $price_array[] = array("price" => $row_item['itemPrice9'], "size" => $row_menu['menuSize10']);
        } if ($row_item['itemPrice10'] > 0) {
            $price_array[] = array("price" => $row_item['menuSize11'], "size" => $row_menu['menuSize11']);
        } if ($row_item['itemPrice11'] > 0) {
            $price_array[] = array("price" => $row_item['itemPrice11'], "size" => $row_menu['menuSize12']);
        } if ($row_item['itemPrice12'] > 0) {
            $price_array[] = array("price" => $row_item['itemPrice12'], "size" => $row_menu['menuSize13']);
        } if ($row_item['itemPrice13'] > 0) {
            $price_array[] = array("price" => $row_item['itemPrice13'], "size" => $row_menu['menuSize14']);
        } if ($row_item['itemPrice14'] > 0) {
            $price_array[] = array("price" => $row_item['itemPrice14'], "size" => $row_menu['menuSize15']);
        } if ($row_item['itemPrice15'] > 0) {
            $price_array[] = array("price" => $row_item['itemPrice15'], "size" => $row_menu['menuSize16']);
        } if ($row_item['itemPrice16'] > 0) {
            $price_array[] = array("price" => $row_item['itemPrice16'], "size" => $row_menu['menuSize17']);
        } if ($row_item['itemPrice17'] > 0) {
            $price_array[] = array("price" => $row_item['itemPrice17'], "size" => $row_menu['menuSize18']);
        } if ($row_item['itemPrice18'] > 0) {
            $price_array[] = array("price" => $row_item['itemPrice18'], "size" => $row_menu['menuSize19']);
        } if ($row_item['itemPrice19'] > 0) {
            $price_array[] = array("price" => $row_item['itemPrice19'], "size" => $row_menu['menuSize20']);
        }

        if (!count($price_array)) {

            $price_array[] = array("price" => "0.00", "size" => "NORMAL");
        }
        if ($price_array[0]['price'] != 0 || $price_array[0]['price'] != 0.00) {
            $menu_items[$j] = array(
                "item_id" => $row_item['id'],
                "item_name" => $row_item["itemName"],
                "description" => $row_item['itemDescription'],
                "item_price" => $price_array,
                "item_image" => $row_item['itemImage'],
                "cus_pizza" => $row_item['Cus_Pizza']
            );


            $j++;
        }
    }


    $menu_details[$i]['items'] = $menu_items;

    $i++;
}
// get the table book avaialable of table //
$table_book_query = "SELECT id,Main_Menu,Start_Time,End_Time FROM menus WHERE menuStatus='Active' AND merchantID=" .
        $id . " GROUP BY Main_Menu LIMIT 4";
$table_book_result = mysqli_query($con, $table_book_query) or die(mysqli_error($con));
$k = 0;
$table_book = array();
while ($table_book_row = mysqli_fetch_array($table_book_result)) {
    $table_book_row = changeHollaWithLebleu($table_book_row);
    $table_book[$k]['id'] = $table_book_row['id'];
    $table_book[$k]['Main_Menu'] = $table_book_row['Main_Menu'];
    $table_book[$k]['Start_Time'] = $table_book_row['Start_Time'];
    $table_book[$k]['End_Time'] = $table_book_row['End_Time'];
    $k++;
}

//the review of business
$review_query = "select * from tbl_reviewsothersites where status=1 AND Review_Type='Dine' AND Res_ID=" .
        $id . " ORDER BY Date_Created DESC ";
$result_review = mysqli_query($con, $review_query)or die(mysqli_error($con));
$external_review = array();
$totalrecords = mysqli_num_rows($result_review);
$i = 0;
while ($review_row = mysqli_fetch_array($result_review)) {
    $external_review[$i]['Review_ID'] = $review_row['Review_ID'];
    $external_review[$i]['user_id'] = $review_row['user_id'];
    $external_review[$i]['Site_Name'] = $review_row['Site_Name'];
    $external_review[$i]['Title'] = $review_row['Title'];
    $external_review[$i]['Site_URL'] = $review_row['Site_URL'];
    $external_review[$i]['status'] = $review_row['status'];
    $external_review[$i]['Date_Created'] = $review_row['Date_Created'];
    $i++;
}

//Merchant working days
$sql_merchanthours = "SELECT * FROM `merchant_hours` WHERE `merchantID`=" . $id;
$result = mysqli_query($con, $sql_merchanthours) or die(mysqli_error($con));
//$row = mysqli_fetch_assoc($result);
$merchanthours = array();
$totalrecords = mysqli_num_rows($result);
$i=0;
while ($review_row = mysqli_fetch_array($result)) {
   //$merchanthours[$i]['weekDay'] = $review_row['weekDay'];
   $last_sunday = strtotime('last Sunday');
   $merchanthours[$i]['weekDay'] = date('l', strtotime('+'.$review_row['weekDay'].' day', $last_sunday));
   $merchanthours[$i]['closed'] = $review_row['closed'];
   $i++;
}

$c_countFavorite = 0;
if (isset($_REQUEST['user_id']) && $_REQUEST['user_id'] != "") {
    $chkFavorite = "select * from tbl_favorite where Dine_ID = '".$id."' and user_id = '".$_REQUEST['user_id']."'";
    $chk_queryFavorite = mysqli_query($con,$chkFavorite);
    $c_countFavorite = mysqli_num_rows($chk_queryFavorite);
}
if($c_countFavorite > 0 )
{
    $row['favourite_status'] = "1";
} else {
    $row['favourite_status'] = "0";
}

$row['Working_Days'] = $merchanthours;
$row['full_menu'] = $menu_details;
$row['review'] = $external_review;
usort($table_book, "order_by_member_key");
$row['available_table'] = $table_book;
//***getting days
$today = $selected = strtotime(date("Y-m-d"));
$oneday = 24 * 3600;

for ($i = $today; $i <= $today + 4 * $oneday; $i += $oneday) {
    if (strtotime(date("Y-m-d")) == $i)
        $label = "TODAY";
    else if (strtotime(date('Y-m-d', strtotime('tomorrow'))) == $i)
        $label = "TOMORROW";
    else
        $label = strtoupper(date("l", $i));
    if ($selected == '')
        $selected = $i;
    $weekday = idate("w", $i);
    $closed = $objdine->Restaurant_Open($id, $weekday);
    if ($closed == 'No') {
        $date[] = array("label" => $label, "date" => date("Y-m-d", $i));
    }
}
//***getting times
//GLOBAL $Time_Zone;
$currenttime = strtotime(date("F j, Y, g:i a"));
$ordertime = $currenttime;
$selectedtime = strtotime($currenttime);
$order_times = array();
$weekday = idate("w", $selected);
$Open_Times = $objdine->Restaurant_OpenHours($id, $weekday, $_REQUEST['orderfor']);
$Open_Time = strtotime($Open_Times[0]);
$End_Time = strtotime($Open_Times[1]);
$timer = date("g:i A", strtotime('0 minutes', $Open_Time));

if ($Open_Time <> '' && $End_Time <> '') {
    if ($Open_Time < $End_Time) {
        while ($Open_Time < $End_Time) {
            $timer = date("g:i A", strtotime('15 minutes', $Open_Time));
            $Open_Time = date(strtotime('15 minutes', $Open_Time));
            if ($selected == $today) {
                if ($Open_Time > $currenttime) {
                    if ($selectedtime == strtotime('15 minutes', $Open_Time))
                    //$order_times.= '<option value="'.date("H:i:s",strtotime('0 minutes',$Open_Time)).'" selected>'.$timer.'</option>';
                        $order_times[] = array("timer" => $timer, "time" => date("H:i:s", strtotime('0 minutes', $Open_Time)));
                    else
                    //$order_times.= '<option value="'.date("H:i:s",strtotime('0 minutes',$Open_Time)).'">'.$timer.'</option>';
                        $order_times[] = array("timer" => $timer, "time" => date("H:i:s", strtotime('0 minutes', $Open_Time)));
                }
            }
            else {
                if ($selectedtime == strtotime('15 minutes', $Open_Time))
                //$order_times.= '<option value="'.date("H:i:s",strtotime('0 minutes',$Open_Time)).'" selected>'.$timer.'</option>';
                    $order_times[] = array("timer" => $timer, "time" => date("H:i:s", strtotime('0 minutes', $Open_Time)));
                else
                //$order_times.= '<option value="'.date("H:i:s",strtotime('0 minutes',$Open_Time)).'">'.$timer.'</option>';
                    $order_times[] = array("timer" => $timer, "time" => date("H:i:s", strtotime('0 minutes', $Open_Time)));
            }
        }
    }
    else {
        // Times comes in  2 ranges
        //if($Open_Time>$End_Time)
        //First Range
        $End_Time = strtotime($Open_Times[1]);
        $Open_Time = strtotime("12:00 AM");
        if ($selected <> $today)
        //$order_times.= '<option value="'.date("H:i:s",strtotime('12:00 AM')).'">12:00 AM</option>';
            $order_times[] = array("timer" => "12:00 AM", "time" => date("H:i:s", strtotime('12:00 AM')));
        while ($Open_Time < $End_Time) {
            $timer = date("g:i A", strtotime('15 minutes', $Open_Time));
            $Open_Time = date(strtotime('15 minutes', $Open_Time));
            //echo "<br>selected:".date("Y-m-d",$selected);
            //echo "<br>today:".date("Y-m-d",$today);
            if ($selected == $today) {
                if ($Open_Time > $currenttime) {
                    if ($selectedtime == strtotime('15 minutes', $Open_Time))
                    //$order_times.= '<option value="'.date("H:i:s",strtotime('0 minutes',$Open_Time)).'" selected>'.$timer.'</option>';
                        $order_times[] = array("timer" => $timer, "time" => date("H:i:s", strtotime('0 minutes', $Open_Time)));
                    else
                    //$order_times.= '<option value="'.date("H:i:s",strtotime('0 minutes',$Open_Time)).'">'.$timer.'</option>';
                        $order_times[] = array("timer" => $timer, "time" => date("H:i:s", strtotime('0 minutes', $Open_Time)));
                }
            }
            else {
                if ($selectedtime == strtotime('15 minutes', $Open_Time))
                //$order_times.= '<option value="'.date("H:i:s",strtotime('0 minutes',$Open_Time)).'" selected>'.$timer.'</option>';
                    $order_times[] = array("timer" => $timer, "time" => date("H:i:s", strtotime('0 minutes', $Open_Time)));
                else
                //$order_times.= '<option value="'.date("H:i:s",strtotime('0 minutes',$Open_Time)).'">'.$timer.'</option>';
                    $order_times[] = array("timer" => $timer, "time" => date("H:i:s", strtotime('0 minutes', $Open_Time)));
            }
        }
        // End First Range
        // Second Range
        $Open_Time = strtotime($Open_Times[0]);
        $End_Time = strtotime("11:45 PM");
        //echo "<br>OT:".date("H:i:s",$Open_Time);
        //echo "<br>ET:".date("H:i:s",$End_Time);
        if ($selected == $today) {
            if ($Open_Time > $currenttime) {
                if ($selectedtime == strtotime('0 minutes', $Open_Time))
                //$order_times.= '<option value="'.date("H:i:s",strtotime('0 minutes',$Open_Time)).'" selected>'.$timer.'</option>';
                    $order_times[] = array("timer" => $timer, "time" => date("H:i:s", strtotime('0 minutes', $Open_Time)));
                //else
                //$order_times.= '<option value="'.date("H:i:s",strtotime('0 minutes',$Open_Time)).'">'.$timer.'222222</option>';
            }
        }
        else {
            if ($selectedtime == strtotime('0 minutes', $Open_Time))
            //$order_times.= '<option value="'.date("H:i:s",strtotime('0 minutes',$Open_Time)).'" selected>'.$timer.'</option>';
                $order_times[] = array("timer" => $timer, "time" => date("H:i:s", strtotime('0 minutes', $Open_Time)));
            else
            //$order_times.= '<option value="'.date("H:i:s",strtotime('0 minutes',$Open_Time)).'">'.$timer.'</option>';
                $order_times[] = array("timer" => $timer, "time" => date("H:i:s", strtotime('0 minutes', $Open_Time)));
        }
        while ($Open_Time < $End_Time) {
            $timer = date("g:i A", strtotime('15 minutes', $Open_Time));
            $Open_Time = date(strtotime('15 minutes', $Open_Time));
            if ($selected == $today) {
                if ($Open_Time > $currenttime) {
                    if ($selectedtime == strtotime('15 minutes', $Open_Time))
                    //$order_times.= '<option value="'.date("H:i:s",strtotime('0 minutes',$Open_Time)).'" selected>'.$timer.'</option>';
                        $order_times[] = array("timer" => $timer, "time" => date("H:i:s", strtotime('0 minutes', $Open_Time)));
                    else
                    //$order_times.= '<option value="'.date("H:i:s",strtotime('0 minutes',$Open_Time)).'">'.$timer.'</option>';
                        $order_times[] = array("timer" => $timer, "time" => date("H:i:s", strtotime('0 minutes', $Open_Time)));
                }
            }
            else {
                if ($selectedtime == strtotime('15 minutes', $Open_Time))
                //$order_times.= '<option value="'.date("H:i:s",strtotime('0 minutes',$Open_Time)).'" selected>'.$timer.'</option>';
                    $order_times[] = array("timer" => $timer, "time" => date("H:i:s", strtotime('0 minutes', $Open_Time)));
                else
                //$order_times.= '<option value="'.date("H:i:s",strtotime('0 minutes',$Open_Time)).'">'.$timer.'</option>';
                    $order_times[] = array("timer" => $timer, "time" => date("H:i:s", strtotime('0 minutes', $Open_Time)));
            }
        }

        /* if(date("H:i:s",strtotime("11:45 PM"))==date("H:i:s",$End_Time))
          {
          $order_times.= '<option value="'.date("H:i:s",strtotime('15 minutes',$End_Time)).'">12:00 AM</option>';
          } */
        // End Second range
// Times comes in  2 ranges
    }
// Time other
    $Open_Time = strtotime($Open_Times[2]);
    $End_Time = strtotime($Open_Times[3]);
    $timer = date("g:i A", strtotime('0 minutes', $Open_Time));
    if ($Open_Time <> '' && $End_Time <> '') {
        if ($selected == $today) {
            if ($Open_Time > $currenttime) {
                if ($selectedtime == strtotime('0 minutes', $Open_Time))
                //$order_times.= '<option value="'.date("H:i:s",strtotime('0 minutes',$Open_Time)).'" selected>'.$timer.'</option>';
                    $order_times[] = array("timer" => $timer, "time" => date("H:i:s", strtotime('0 minutes', $Open_Time)));
                else
                //$order_times.= '<option value="'.date("H:i:s",strtotime('0 minutes',$Open_Time)).'">'.$timer.'</option>';
                    $order_times[] = array("timer" => $timer, "time" => date("H:i:s", strtotime('0 minutes', $Open_Time)));
            }
        }
        else {
            if ($selectedtime == strtotime('0 minutes', $Open_Time) && $ordertime <> '00:00:00')
            //$order_times.= '<option value="'.date("H:i:s",strtotime('0 minutes',$Open_Time)).'" selected>'.$timer.'</option>';
                $order_times[] = array("timer" => $timer, "time" => date("H:i:s", strtotime('0 minutes', $Open_Time)));
            else if ($timer <> '12:00 AM')
            //$order_times.= '<option value="'.date("H:i:s",strtotime('0 minutes',$Open_Time)).'">'.$timer.'</option>';
                $order_times[] = array("timer" => $timer, "time" => date("H:i:s", strtotime('0 minutes', $Open_Time)));
        }
        while ($Open_Time < $End_Time) {
            $timer = date("g:i A", strtotime('15 minutes', $Open_Time));
            $Open_Time = date(strtotime('15 minutes', $Open_Time));
            if ($selected == $today) {
                if ($Open_Time > $currenttime) {
                    if ($selectedtime == strtotime('15 minutes', $Open_Time))
                    //$order_times.= '<option value="'.date("H:i:s",strtotime('0 minutes',$Open_Time)).'" selected>'.$timer.'</option>';
                        $order_times[] = array("timer" => $timer, "time" => date("H:i:s", strtotime('0 minutes', $Open_Time)));
                    else
                    //$order_times.= '<option value="'.date("H:i:s",strtotime('0 minutes',$Open_Time)).'">'.$timer.'</option>';
                        $order_times[] = array("timer" => $timer, "time" => date("H:i:s", strtotime('0 minutes', $Open_Time)));
                }
            }
            else {
                if ($selectedtime == strtotime('15 minutes', $Open_Time))
                //$order_times.= '<option value="'.date("H:i:s",strtotime('0 minutes',$Open_Time)).'" selected>'.$timer.'</option>';
                    $order_times[] = array("timer" => $timer, "time" => date("H:i:s", strtotime('0 minutes', $Open_Time)));
                else
                //$order_times.= '<option value="'.date("H:i:s",strtotime('0 minutes',$Open_Time)).'">'.$timer.'</option>';
                    $order_times[] = array("timer" => $timer, "time" => date("H:i:s", strtotime('0 minutes', $Open_Time)));
            }
        }
    }
}
else {
    $order_times = "";
}


$row['opening_times'] = $objdine->Restaurant_OpenHours_allday($id, $_REQUEST['orderfor']);

/* ------------- Cart Count -----------* /
  if($_REQUEST['user_id'] != '')
  {
  $cart_status = "select * from tbl_cart where Cart_UserID = '".$_REQUEST['user_id']."'";
  $cart_status_que = mysqli_query($con,$cart_status);
  $cart_count = mysqli_num_rows($cart_status_que);
  }
  else
  {
  $cart_count = "0";
  } */
if (isset($_REQUEST['user_id']) && $_REQUEST['user_id'] != "") {
    $sql_count = "select count(*) as total_cart from tbl_cart where Cart_Type in('Dine','Parking') and Cart_UserID='" . $_REQUEST['user_id'] . "'";
} else {
    $sql_count = "select count(*) as total_cart from tbl_cart where Cart_Type in('Dine','Parking') and Sess_ID='" . $_REQUEST['device_id'] . "'";
}
$query_count = mysqli_query($con, $sql_count);
if (mysqli_num_rows($query_count)) {
    $counts = mysqli_fetch_array($query_count);
    $cart_count = $counts['total_cart'];
}
$row['cart_count'] = "$cart_count";
$row = removeNull($row);
echo json_encode(utf8ize($row));
exit;

function utf8ize($d) {
    if (is_array($d)) {
        foreach ($d as $k => $v) {
            $d[$k] = utf8ize($v);
        }
    } else if (is_string ($d)) {
        return utf8_encode($d);
    }
    return $d;
}
function order_by_member_key($a)
{
  if ($a['Main_Menu'] == 'Le Bleu')
  {
    return 0;
  }
  // sort the higher membkey first:
  return 1;
}
function changeHollaWithLebleu($a){
    if(strtolower($a['Main_Menu']) == 'holla meal' || strtolower($a['Main_Menu']) == '$5 holla menu' || strtolower($a['Main_Menu']) == 'holla menu'){
        $a['Main_Menu'] = 'Le Bleu';   
        $a['menuName'] = 'Le Bleu';
    }
    return $a;
}
?>
