<?php

$device_id = sanitize($_REQUEST['device_id']);
$user_id = sanitize($_REQUEST['User_ID']);
$Dine_Obj = new Dine();
$cart = array();
$main_cart = array();
$main_total = 0;
$subtotal = 0;
$order_total = 0;
$tax_total = 0;
$deliveryFee = 0;
$lat = '';
$long = '';

function GetTotalRatings($list_id, $cat) {
    GLOBAL $con;
    if ($list_id == '') {
        $list_id = 0;
    }
    $sql = "SELECT * FROM tbl_reviews WHERE R_Type='" . $cat . "' AND list_id=" . $list_id;
    $execity = mysqli_query($con, $sql);
    $num = mysqli_num_rows($execity);
    if ($num > 0)
        return $num;
    else
        return 0;
}

function getMyAverageRatings($list_id, $cat, $user_id) {
    GLOBAL $con;
    if ($list_id == '') {
        $list_id = 0;
    }

    $sql = "select * from tbl_reviews where user_id='" . $user_id . "' AND R_Type='" . $cat . "' AND list_id=" . $list_id;
    $execity = mysqli_query($con, $sql);
    $Total = 0;
    $Average = 0;
    $num = mysqli_num_rows($execity);
    $onestar = 0;
    $twostar = 0;
    $threestar = 0;
    $fourstar = 0;
    $fivestar = 0;
    $i = 0;
    while ($v = mysqli_fetch_array($execity)) {
        $Total+=$v['Average'];
        $i++;
    }
    if ($Total > 0)
        $Average = round($Total / $i, 2);
    else
        $Average = 0;

    if ($Average > 0)
        $Average = round($Average * 2, 0) / 2;
    else
        $Average = 0;
    //$Average = floor($Total*$i)/$i;

    if ($Average > 5)
        $Average = 5;
    return $Average;
}

if (isset($device_id) || isset($user_id)) {
    $Cart = $Dine_Obj->MergeShowCartPayLater();
//print_r($Cart);
    $cart = array();
    if (isset($Cart[0]) && !empty($Cart[0])) {
        $i = 0; // for dinning
        $j = 0; // for parking
        foreach ($Cart[0] as $cart_data) {
            if (strtolower($cart_data['Cart_Type']) == 'parking') {
                $cart['parking'][$j] = $cart_data;
                $j++;
            } // end if type parking condition
        } // end cart main outer loop
//print_r($cart['parking']);die;
        
        if (isset($cart['parking']) && !empty($cart['parking'])) {
            $temp_array = "";

            $parking_index = 0;

            foreach ($cart['parking'] as $park_traverse_data) {
                if ($park_traverse_data['Payat_Lot'] > 0) {

                    $Payat_Lot = $park_traverse_data['Payat_Lot'];
                } else {
                    $Payat_Lot = 0;
                }

                if ($park_traverse_data['Payment_Type'] == 'partial') {
                    $Payment_Type = $park_traverse_data['Payment_Type'];
                } else {
                    $Payment_Type = 'full';
                }

                $get_loc = @mysqli_fetch_assoc(mysqli_query($con, "select P_Daily_Price_Type,P_Pricingtype,P_Weeklyprice,P_Monthlyprice,P_FAmt,Event_price,Special_Price_Desc,P_FMIN,tbl_parkinglocations.Park_Address AS Title,tbl_parkinglocations.Park_Name from tbl_parking INNER JOIN tbl_parkinglocations ON tbl_parkinglocations.Park_ID=tbl_parking.P_Location where P_ID=" . $park_traverse_data['Cart_ServiceID']));
                //    print_r($get_loc); die;
                $temp_array['Cart_ID'] = $park_traverse_data['Cart_ID'];
                $temp_array['title'] = $get_loc['Title'];
                $temp_array['park_name'] = $get_loc['Park_Name'];
                $temp_array['cart_quantity'] = $park_traverse_data['Cart_Quantity'];
                $temp_array['Amount'] = "$" . number_format($park_traverse_data['Amount'], 2);
                $temp_array['arrival_date'] = $park_traverse_data['from_date'];
                $temp_array['departs_date'] = $park_traverse_data['to_date'];
                $temp_array['P_Pricingtype'] = ucfirst($get_loc['P_Pricingtype']);
                // Add price rate for parking - 29-apr-2016 - Bindra Shah
                $cartServiceId = $park_traverse_data['Cart_ServiceID'];
                $price = '';
                if ($get_loc['P_Pricingtype'] == 'daily') {
                    $PriceArray = array();
                    $fdate = date("Y-m-d", strtotime($park_traverse_data['from_date']));
                    $tdate = date("Y-m-d", strtotime($park_traverse_data['to_date']));
                    $Dayprice = $DayCount = $Weekendprice = $WeekendCount = 0;
                    if ($get_loc['P_Daily_Price_Type'] == 'Week') {
                        $Pricequery = "SELECT P_fromDate as startdate,PA_P_Dailyprice AS price FROM tbl_parkingweekdayavailability where P_ID='$cartServiceId' AND P_fromDate BETWEEN '$fdate' AND '$tdate' order by P_fromDate"; // die;
                        $Priceres = mysqli_query($con, $Pricequery);
                        while ($Pricerow = mysqli_fetch_assoc($Priceres))
                            $PriceArray[] = $Pricerow;
                        $ranges = array();
                        $x = 0;
                        $last = count($PriceArray) - 1;
                        foreach ($PriceArray as $key => $row) {
                            $week = date('D', strtotime($row['startdate']));
                            if ($week == 'Mon' || $week == 'Tue' || $week == 'Wed' || $week == 'Thu' || $week == 'Fri') {
                                $Dayprice+=$row['price'];
                                $DayCount++;
                            } else {
                                $Weekendprice+=$row['price'];
                                $WeekendCount++;
                            }
                            //if range startdate not set, create the range startdate and price
                            if (!isset($ranges[$x]['startdate'])) {
                                $daysdiff = 1;
                                $ranges[$x] = array('startdate' => $row['startdate'], 'price' => $row['price']);
                            }

                            //if the last row set the enddate
                            if ($key == $last) {
                                $ranges[$x]['enddate'] = $row['startdate'];
                                $ranges[$x]['days'] = $daysdiff;
                            }
                            //if the next price is not the same, set the enddate and increase the counter (single date range)
                            else if ($row['price'] != $PriceArray[$key + 1]['price']) {
                                $ranges[$x]['enddate'] = $row['startdate'];
                                $ranges[$x]['days'] = $daysdiff;
                                $x++;
                            }
                            //if the price is not the same as the current range price, set the enddate and increase the counter
                            else if ($row['price'] != $ranges[$x]['price']) {
                                $ranges[$x]['enddate'] = $rows[$key - 1]['startdate'];
                                $ranges[$x]['days'] = $daysdiff;
                                $x++;
                            }
                            $daysdiff++;
                        }
                        $price = $ranges;
                    }
                } else if ($get_loc['P_Pricingtype'] == 'weekly')
                    $price = number_format($get_loc['P_Weeklyprice'], 2) . " per Weekly";
                else if ($get_loc['P_Pricingtype'] == 'monthly')
                    $price = number_format($get_loc['P_Monthlyprice'], 2) . " per Monthly";
                else if ($get_loc['P_Pricingtype'] == 'hourly')
                    $price = number_format($get_loc['P_FAmt'], 2);
                else if ($get_loc['P_Pricingtype'] == 'minute')
                    $price = number_format($get_loc['P_FAmt'], 2);
                else if ($get_loc['P_Pricingtype'] == 'event')
                    $price = number_format($get_loc['Event_price'], 2);
                else if ($get_loc['P_Pricingtype'] == 'special')
                    $price = number_format($get_loc['Event_price'], 2);

                $Special_Price_Desc = explode("(", $get_loc['Special_Price_Desc']);
                $Special_Price_Des = $Special_Price_Desc[0];

                if ($get_loc['P_Pricingtype'] == 'hourly' && $get_loc['P_FMIN'] == 1)
                    $price.= " per Every Hour";
                else if ($get_loc['P_Pricingtype'] == 'hourly' && $get_loc['P_FMIN'] > 1)
                    $price.= " Per " . $get_loc['P_FMIN'] . " Hours";
                else if ($get_loc['P_Pricingtype'] == 'hourly')
                    $price.= $get_loc['P_H1'] . " per Hour";
                else if ($get_loc['P_Pricingtype'] == 'minute')
                    $price.= $get_loc['P_FMIN'] . " per Minutes";
                else if ($get_loc['P_Pricingtype'] == 'special' && $Special_Price_Des <> '')
                    $price.= "&nbsp;" . ucwords($Special_Price_Des);

                if (is_array($price)) {

                    $Totaldays = ceil(abs(strtotime($tdate) - strtotime($fdate)) / 86400) + 1;
                    $temp_array['Totaldays'] = $Totaldays;
                    $temp_array['TotalWorkingDays'] = $DayCount;
                    $temp_array['TotalWeekendDays'] = $WeekendCount;
                    $temp_array['TotalWorkingPrice'] = $Dayprice;
                    $temp_array['TotalWeekendPrice'] = $Weekendprice;
                    $temp_array['price_rate'] = $price;
                } else {
                    $temp_array['price_rate'] = strtolower($price);
                    unset($temp_array['Totaldays']);
                    unset($temp_array['TotalWorkingDays']);
                    unset($temp_array['TotalWeekendDays']);
                    unset($temp_array['TotalWorkingPrice']);
                    unset($temp_array['TotalWeekendPrice']);
                }


                // End Add price rate for parking - 29-apr-2016 - Bindra Shah
                // -----get point percentage-----------//
                $cat = '45';
                $Point = @mysqli_fetch_assoc(mysqli_query($con, "SELECT Point_Percentage FROM tbl_categories WHERE Cat_ID=" . $cat));
                $PointPercentage = $Point['Point_Percentage'];
                $Points1 = ($park_traverse_data['TotalAmount'] * $PointPercentage) / 100;
                $Points = $Points + $Points1;

                $temp_array['points'] = $Points1;
//
                //----------getting the parking Fee--------//
                $fees = GetParkingfee($park_traverse_data['Cart_ServiceID']);
                $totalfees = 0;

                $temp_array['fees'] = array();
                if (count($fees) > 0) {
                    foreach ($fees[0] as $pp => $qq) {
                        if ($qq['format'] == '%')
                            $Setting_Charge = $park_traverse_data['Amount'] * $qq['Setting_Value'] / 100;
                        else
                            $Setting_Charge = $qq['Setting_Value'];

                        if ($qq['format'] == '%') {
                            $temp_array['fees'][$qq['Setting_Name']] = "$" . number_format($Setting_Charge, 2);
                            $totalfees += number_format($Setting_Charge, 2);
                        } else {
                            $temp_array['fees'][$qq['Setting_Name']] = '$' . number_format($Setting_Charge, 2);
                            $totalfees += number_format($Setting_Charge, 2);
                        }
                    }
                }
                //--------------overnight fee----------//
                if ($park_traverse_data['Overnight_Fee'] > 0) {
                    $temp_array['overnight_fee'] = '$' . number_format($park_traverse_data['Overnight_Fee'], 2);
                }
                $temp_array['arrival_date'] = $park_traverse_data['from_date'];
                $temp_array['departs_date'] = $park_traverse_data['to_date'];


                // $main_cart['parking']['items'][] = $temp_array;
                //$temp_array['total']=number_format(($temp_array['cart_quantity'] * 	$park_traverse_data['Amount']),2);
                $temp_array['total'] = number_format(($park_traverse_data['Amount']), 2) + $totalfees;
                $subtotal +=number_format(($park_traverse_data['Amount']), 2);
                $tax_total +=$totalfees;
                $main_cart['parking'][] = $temp_array;
                $main_total = $main_total + $temp_array['total'];
                $park_total+= number_format(($park_traverse_data['Amount']), 2);
                $parking_index++;
            } //end $park data traverse
            // Final total calculations
            $main_total = $park_total + $totalfees;
            //-----------getting the total amount of the cart----------//

            if (sanitize($_REQUEST['User_ID']) != "") {
                $total_park = @mysqli_fetch_array(mysqli_query($con, "select SUM(TotalAmount) AS TOTAL from tbl_cart where Cart_Type='Parking' AND Cart_UserID ='" . $user_id . "'"));
            } else {
                $total_park = @mysqli_fetch_array(mysqli_query($con, "select SUM(TotalAmount) AS TOTAL from tbl_cart where Cart_Type='Parking' AND Sess_ID='" . $device_id . "' "));
            }


            //---------if partial payment-----------//
            if ($Payment_Type == 'partial') {
                $pay_now = "$" . number_format((($total['TOTAL']) - $Payat_Lot), 2);
                $balance_due = "$" . number_format(($Payat_Lot), 2);
            } else {
                $pay_now = '';
                $balance_due = '';
            }
            $way_amount = number_format(round($Points, 2), 2);
            // $main_total=$main_total+$total_park['TOTAL'];
            $main_cart['paynow'] = $pay_now;
            $main_cart['balance_due'] = $balance_due;
            $main_cart['way_buck'] = $way_amount;
            $main_cart['park_total'] = $park_total;
        } else {
            $main_cart['parking'] = array();
        } // end parking if


        $main_cart['sub_total'] = $subtotal;
        $main_cart['tax_total'] = $tax_total;
        $main_cart['total_delivery'] = $deliveryFee;
        $main_cart['total_amount'] = $subtotal + $tax_total + $deliveryFee;
        $total_cart = 0;
        if ($user_id != "") {
            $sql_count = "select count(*) as total_cart from tbl_cart where Cart_Type in('Parking') and Cart_UserID='" . $user_id . "'";
        } else {
            $sql_count = "select count(*) as total_cart from tbl_cart where Cart_Type in('Parking') and Sess_ID='" . $_device_id . "'";
        }
        //  echo  $sql_count; die;
        $query_count = mysqli_query($con, $sql_count);
        if (mysqli_num_rows($query_count)) {
            $counts = mysqli_fetch_array($query_count);
            $total_cart = $counts['total_cart'];
        }
        $main_cart['total_cart'] = $total_cart;

        if (!empty($main_cart['parking'])) {
            if ($user_id != '') {
                $EncryptKey = ENCRYPTKEY;
                $sql = "Select *,RIGHT(AES_DECRYPT(Card_Number,'" . $EncryptKey . "'),4) AS Card_No From tbl_creditcarddetails WHERE Card_User_ID=" . $user_id . " AND Card_Default='1'";
                $res2 = @mysqli_fetch_assoc(mysqli_query($con, $sql));
                if (empty($res2)) {
                    $sql = "Select *,RIGHT(AES_DECRYPT(Card_Number,'" . $EncryptKey . "'),4) AS Card_No From tbl_creditcarddetails WHERE Card_User_ID=" . $user_id . "  ORDER BY Card_Created ASC LIMIT 1";
                    $res2 = @mysqli_fetch_assoc(mysqli_query($con, $sql));
                    if (!empty($res2)) {
                        $sql2 = "update tbl_creditcarddetails set Card_Default=1 where Card_ID='" . $res2['Card_ID'] . "'";
                        if (mysqli_query($con, $sql2)) {
                            $res2['Card_Default'] = 1;
                        }
                    }
                }
                if (!empty($res2)) {
                    $card_type = $res2['Card_Type'];
                    if ($card_type == 'Visa')
                        $card_type = 'VISA';
                    else if ($card_type == 'MasterCard')
                        $card_type = 'MASTER CARD';
                    else if ($card_type == 'American Express')
                        $card_type = 'AMEX';
                    else if ($card_type == 'Discover')
                        $card_type = 'DISCOVER';
                    else if ($card_type == 'Dinners Club')
                        $card_type = 'DINERSCLUB';
                    else
                        $card_type = $card_type;
                    $card_exp_year = substr($res2['Card_Exp_Year'], -2);
                    $card_array[] = array("Card_ID" => $res2['Card_ID'], "Card_Type" => $card_type, "CARD_NO" => base64_encode($res2['Card_No']), "Card_Exp_Year" => $card_exp_year,
                        "Card_Exp_Month" => $res2['Card_Exp_Month'], "Card_FirstName" => $res2['Card_FirstName'], "Card_Street" => $res2['Card_Street'],
                        "Card_State" => $res2['Card_State'], "Card_City" => $res2['Card_City'], "Card_Zip" => $res2['Card_Zip'], "Stripe_UserId" => (isset($res2['Stripe_UserId']) ? $res2['Stripe_UserId'] : '0'), "Card_StripeCustID" => (isset($res2['Card_StripeCustID']) ? $res2['Card_StripeCustID'] : '0'), "Card_Default" => $res2['Card_Default']);
                    $main_cart['Card_details'] = $card_array;
                } else
                    $main_cart['Card_details'] = array();
            } else
                $main_cart['Card_details'] = array();
            
            /*inclued because of payment API - step2*/
            include("booknow_pay_later_step2.php");
            
            //echo json_encode(array('status' => 1, 'cart' => $main_cart), true);
            exit;
        }
        else {
            echo json_encode(array('status' => 0, 'message' => "Your cart is empty"), true);
            exit;
        }
    } else {
        echo json_encode(array('status' => 0, 'message' => "Your cart is empty"), true);
        exit;
    } // end main cart if
} else {
    $content = array("status" => 0, "message" => PARAMETER_MSG);
    echo json_encode($content);
    exit;
}

//The function returns the no. of business days between two dates and it skips the holidays
function getWorkingDays($startDate, $endDate, $holidays) {
    // do strtotime calculations just once
    $endDate = strtotime($endDate);
    $startDate = strtotime($startDate);


    //The total number of days between the two dates. We compute the no. of seconds and divide it to 60*60*24
    //We add one to inlude both dates in the interval.
    $days = ($endDate - $startDate) / 86400 + 1;

    $no_full_weeks = floor($days / 7);
    $no_remaining_days = fmod($days, 7);

    //It will return 1 if it's Monday,.. ,7 for Sunday
    $the_first_day_of_week = date("N", $startDate);
    $the_last_day_of_week = date("N", $endDate);

    //---->The two can be equal in leap years when february has 29 days, the equal sign is added here
    //In the first case the whole interval is within a week, in the second case the interval falls in two weeks.
    if ($the_first_day_of_week <= $the_last_day_of_week) {
        if ($the_first_day_of_week <= 6 && 6 <= $the_last_day_of_week)
            $no_remaining_days--;
        if ($the_first_day_of_week <= 7 && 7 <= $the_last_day_of_week)
            $no_remaining_days--;
    }
    else {
        // (edit by Tokes to fix an edge case where the start day was a Sunday
        // and the end day was NOT a Saturday)
        // the day of the week for start is later than the day of the week for end
        if ($the_first_day_of_week == 7) {
            // if the start date is a Sunday, then we definitely subtract 1 day
            $no_remaining_days--;

            if ($the_last_day_of_week == 6) {
                // if the end date is a Saturday, then we subtract another day
                $no_remaining_days--;
            }
        } else {
            // the start date was a Saturday (or earlier), and the end date was (Mon..Fri)
            // so we skip an entire weekend and subtract 2 days
            $no_remaining_days -= 2;
        }
    }

    //The no. of business days is: (number of weeks between the two dates) * (5 working days) + the remainder
//---->february in none leap years gave a remainder of 0 but still calculated weekends between first and last day, this is one way to fix it
    $workingDays = $no_full_weeks * 5;
    if ($no_remaining_days > 0) {
        $workingDays += $no_remaining_days;
    }

    //We subtract the holidays
    foreach ($holidays as $holiday) {
        $time_stamp = strtotime($holiday);
        //If the holiday doesn't fall in weekend
        if ($startDate <= $time_stamp && $time_stamp <= $endDate && date("N", $time_stamp) != 6 && date("N", $time_stamp) != 7)
            $workingDays--;
    }

    return $workingDays;
}

?>
