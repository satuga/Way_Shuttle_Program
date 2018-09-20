<?php

require_once('config.php');


// get the HTML
$currentdate = !empty($_GET['currentdate']) ? date('Y-m-d', strtotime($_GET['currentdate'])) : date('Y-m-d');

$query = "SELECT pl.Park_Name, t.DLG_TripName AS DLG_TripName, ts.DTP_StopName FROM tbl_shuttle_idle_time_on_stop AS itos
        INNER JOIN tbl_shuttle_drivers AS dvr ON dvr.DVR_DriverID = itos.ITS_SPD_DVR_DriverID
        INNER JOIN tbl_registeration AS r ON dvr.DVR_UserID = r.id
        INNER JOIN tbl_parking p ON p.P_ID = itos.ITS_SPD_ParkingLotID
        INNER JOIN tbl_parkinglocations pl ON p.P_Location = pl.Park_ID
        INNER JOIN tbl_shuttle_driver_trip t ON itos.ITS_SPD_DLG_DriverTripLogID = t.DLG_DriverTripLogID
        INNER JOIN tbl_shuttle_trip_stops ts ON itos.ITS_DTP_TripStopID = ts.DTP_TripStopID
        where STR_TO_DATE(ITS_CreatedDate, '%Y-%m-%d') = '" . $currentdate . "'";
$res = mysqli_query($con, $query) or die("Error");

$row = mysqli_num_rows($res);
if (!empty($row)) {
    generateCsv($query, $currentdate);
}

function generateCsv($exportQuery, $currentdate) {
    Global $Base_Path, $con;

    $export = mysqli_query($con, $exportQuery);
    $filepath = $Base_Path . '/vendor/pdfs/';
    //$filename = 'shuttleIdleTime_' . date('YmdHis') . ".csv";
    $filename = 'shuttleIdleTime.csv';

    $data = '" Parking Name","Trip Name","Trip Stop Name",'. "\n";
    while ($row = mysqli_fetch_row($export)) {
        $line = '';
        foreach ($row as $value) {
            if ((!isset($value) ) || ( $value == "" )) {
                $value = ",";
            } else {
                $value = str_replace('"', '""', $value);
                $value = '"' . $value . '"' . ",";
            }
            $line .= $value;
        }
        $data .= trim($line) . "\n";
    }
    $data = str_replace("\r", "", $data);
    $csv_handler = fopen($filepath . $filename, 'w');
    fwrite($csv_handler, $data);
    fclose($csv_handler);

    $mailto = !empty($_GET['emailto']) ? $_GET['emailto'] : 'ujash.joshi@letsnurture.com';
    $subject = 'Shuttle Idle Time report of Drivers - ' . $currentdate;
    $message = 'Check attached shuttle idle time report';

    mail_attachmentnew($filename, $filepath, $mailto, $subject, $message = '');
}

function mail_attachmentnew($filename, $filepath, $mailto, $subject, $message) {
    $fileatt = $filepath . $filename; // Path to the file
    $fileatt_type = "application/html"; // File Type
    $fileatt_name = $filename; // Filename that will be used for the file as the attachment

    $email_from = "support@way.com"; // Who the email is from
    $email_subject = $subject; // The Subject of the email
    $email_message = $message;
    $from_name = "way";
    // Message that the email has in it

    $email_to = $mailto; // Who the email is to

    $headers = "From: " . $email_from;


    $file = fopen($fileatt, 'rb');
    $data = fread($file, filesize($fileatt));
    fclose($file);

    $semi_rand = md5(time());
    $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";

    $headers .= "\nMIME-Version: 1.0\n" .
            "Content-Type: multipart/mixed;\n" .
            " boundary=\"{$mime_boundary}\";Return-Path: support@way.com\n";

    $email_message .= "This is a multi-part message in MIME format.\n\n" .
            "--{$mime_boundary}\n" .
            "Content-Type:text/html; charset=\"iso-8859-1\"\n" .
            "Content-Transfer-Encoding: 7bit\n\n" .
            $email_message .= "\n\n";

    $data = chunk_split(base64_encode($data));

    $email_message .= "--{$mime_boundary}\n" .
            "Content-Type: {$fileatt_type};\n" .
            " name=\"{$fileatt_name}\"\n" .
            "Content-Disposition: attachment;\n" .
            " filename=\"{$fileatt_name}\"\n" .
            "Content-Transfer-Encoding: base64\n\n" .
            $data .= "\n\n" .
            "--{$mime_boundary}--\n";

    //$ok = @mail($email_to, $email_subject, $email_message, $headers);
    $ok = @mail($email_to, $email_subject, $email_message, $headers);

    if ($ok) {
        return '1';
    } else {
        return '0';
    }
}

?>