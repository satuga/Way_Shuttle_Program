<?php
session_start();

require_once('config.php');
GLOBAL $con;

$res = mysqli_query($con, $query1) or die("Trascation Error");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN"  "http://www.w3.org/TR/html4/frameset.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml">
    <head>
        <title> Way - Smart Parking </title>
        <meta name="Generator" content="EditPlus">
        <meta name="Author" content="Way">
        <meta name="Keywords" content="Way">
        <meta name="Description" content="Way">
    </head>
    <body>
        <h2 align="center" class="text-center"><?php echo $Vendor_Name.' - '.$date?></h2>
        <div class="container-fluid">
            <div class="row col-md-12">
                <table border="1" align="center">
                    <thead>
                        <tr>
                            <th style="width:20%">User Name</th>
                            <th style="width:20%">Entry Date and Time</th>
                            <th style="width:20%">Exit Date and Time</th>
                            <th style="width:20%">Amount charged</th>
                            <th style="width:20%">Duration</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        while ($rec = mysqli_fetch_assoc($res)) {
                            $checkoutDate = $rec['out_time'];
                            $duration = getDateDiff($rec['in_time'], $rec['out_time']);
                            $actualAmount = $rec['is_postpaid'] == 1 ? $rec['amount'] : $rec['TotalAmount'];
                            if($rec['entry_mode'] == 1){
                                $actualAmount = 'N/A';
                                $duration = "N/A";
                                $checkoutDate = "N/A";
                            }
                            ?>
                            <tr>
                                <td style="width:20%"><?php echo $rec['Customer_Name'];?></td>
                                <td><?php echo $rec['in_time'];?></td>
                                <td><?php echo $checkoutDate; ?></td>
                                <td><?php echo $actualAmount;?></td>
                                <td><?php echo $duration; ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </body>
</html>
<?php

function getDateDiff($date1, $date2) {

// Creating a date object
    $date1 = date_create($date1);

// Creating a date object
    $date2 = date_create($date2);

// Calculating the difference between dates
    $diff = date_diff($date2, $date1);

// Accesing days
    $days = $diff->d;

// Accesing months
    $months = $diff->m;

// Accesing years
    $years = $diff->y;

// Accesing hours
    $hours = $diff->h;

// Accesing minutes
    $minutes = $diff->i;

// Accesing seconds
    $seconds = $diff->s;

    return $hours . ' hours, ' . $minutes . ' minutes, ' . $seconds . ' seconds';
}
?>
