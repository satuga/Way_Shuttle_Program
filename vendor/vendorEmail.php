<?php

require_once('config.php');


// get the HTML

$sql = "SELECT tbl_parking.*, concat(tbl_registeration.firstname,' ',tbl_registeration.lastname) AS Vendor_Name FROM tbl_parking "
        . "INNER JOIN tbl_parkinglocations ON tbl_parking.P_Location = tbl_parkinglocations.Park_ID "
        . "INNER JOIN tbl_registeration ON tbl_parking.P_UserID = tbl_registeration.id "
        . "ORDER BY  `tbl_parking`.`P_ID` DESC ";
$result = $con->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {

        $pid = $row['P_ID'];
        $Vendor_Name = $row['Vendor_Name'];
        //   print_r($pid);
        //   echo "<pre>";
        //$date = date('Y-m-d');
        $date = '2016-10-07';
        $query1 = "SELECT concat(r.firstname,' ',r.lastname) AS Customer_Name, concat(r1.firstname,' ',r1.lastname) AS Vendor_Name , gpt.in_time,gpt.out_time,gpt.entry_mode,gpt.amount,pt.TotalAmount, pt.is_postpaid
            FROM tbl_gate_parking_transaction gpt
            INNER JOIN tbl_smartgate sg ON sg.SGT_GarageID = gpt.garage_id
            INNER JOIN tbl_paymenttransaction pt ON pt.TxnID = gpt.txn_id
            INNER JOIN tbl_parking p ON p.P_ID = gpt.garage_id
            INNER JOIN tbl_parkinglocations pl ON p.P_Location = pl.Park_ID
            INNER JOIN tbl_registeration r ON r.id = gpt.user_id
            INNER JOIN tbl_registeration r1 ON r1.id = p.P_UserID
            WHERE gpt.garage_id = '" . $pid . "' and (DATE(gpt.in_time) = '" . $date . "' OR DATE(gpt.out_time) = '" . $date . "' )
            GROUP BY gpt.transaction_id DESC";
        $result1 = $con->query($query1);
        if ($result1->num_rows > 0) {
            generatePdf($query1, $pid, $date, $Vendor_Name);
        }
    }
}

function generatePdf($query1, $pid, $date, $Vendor_Name) {
    Global $Base_Path;

    ob_start();
    require_once('html2pdf.class.php');
    include('vendorEmailHtml.php');
    $content = ob_get_clean();

    $html2pdf = new HTML2PDF('P', 'A4', 'en');
    $html2pdf->setDefaultFont('Arial');
    $html2pdf->writeHTML($content);
    $filepath = $Base_Path.'/vendor/pdfs/'; 
    $filename=$pid.'myfile_'.date('YmdHis').".pdf";
    $html2pdf->Output($filepath . $filename);
    
    $mailto = !empty($_GET['emailto'])?$_GET['emailto']:'ujash.joshi@letsnurture.com';
    $subject = 'Order report of Customers - '. date('Y-m-d');
    $message = 'Check attached daily report of the orders';

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