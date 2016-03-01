<?php
/**
 * @author Kevin Bohinski <bohinsk1@tcnj.edu>
 * @version 1.0
 * @since 2016-1-27
 *
 * Project Name:  HackTCNJ 2016
 * Description:   HackTCNJ 2016 registration site.
 *
 * Filename:      /cron_checkwaitlist.php
 * Description:   Waitlist script for HackTCNJ 2016 site.
 * Last Modified: 2016-1-27
 *
 * Copyright (c) 2015 Kevin Bohinski. All rights reserved.
 */

require_once('WebContent/db.php');

/* Get info from db */
/* Get num not waitlisted */
$sql = "SELECT id FROM hackers WHERE waitlisted=0";
$result = mysqli_query($db, $sql) or die('Query failed: ' . mysqli_error($db));
$num_attendees = mysqli_num_rows($result);
$result->close();

/* Get waitlisted hackers */
$sql = "SELECT id FROM hackers WHERE waitlisted=1 ORDER BY id";
$result = mysqli_query($db, $sql) or die('Query failed: ' . mysqli_error($db));
$num_waitlisted = mysqli_num_rows($result);

/* How many can we promote? */
$num_to_promote = $REGRISTRATION_CAP_WAITLIST_START - $num_attendees;

if ($num_to_promote > $num_waitlisted) {
    $num_to_promote = $num_waitlisted;
}

$num_to_promote_copy = $num_to_promote;
$num_promoted = 0;
$err_count = 0;
$err_json = "";

$ids = array();
while ($row = mysqli_fetch_assoc($result)) {
    array_push($ids, $row['id']);
}
$result->close();

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$_SESSION['admin'] = true;

foreach ($ids as $id) {
    if ($num_to_promote > 0) {
        $url = $dir . '/ajax_promotewaitlist.php';
        $data = array('id' => $id);

        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data),
            ),
        );

        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        $err = json_decode($result, true);
        if ($err['status'] == 'failure') {
            $err_count++;
            $err_json .= json_encode($err);
        }

        $num_promoted++;
        $num_to_promote--;
    } else {
        break;
    }
}

session_destroy();

if (file_exists("error_log")) {
    $err_log = fopen("error_log", "r") or die ("Unable to open file!");
    $err_log_contents = fread($err_log, filesize("error_log"));
    fclose($err_log);
} else {
    $err_log_contents = "";
}


$msg = "Hi, here is your daily waitlist report\n";
$msg .= "\nBefore Promotion:\n";
$msg .= "  Reg Cap:        " . $REGRISTRATION_CAP_WAITLIST_START . "\n";
$msg .= "  Num Attendees:  " . $num_attendees . "\n";
$msg .= "  Num Waitlisted: " . $num_waitlisted . "\n";
$msg .= "  Num to Promote: " . $num_to_promote_copy . "\n";
$msg .= "\nAfter Promotion:\n";
$msg .= "  Num Promoted:   " . $num_promoted . "\n";
$msg .= "  Error Count :   " . $err_count . "\n";
$msg .= "  Num To Promote: " . $num_to_promote . "\n";
$msg .= "\nPromotion Error Messages:\n";
$msg .= "  " . $err_json . "\n";
$msg .= "\nPHP Global Error Log:\n";
$msg .= "  " . $err_log_contents;

mail("acm@tcnj.edu", "HackTCNJ - Daily Waitlist Report!", $msg, "From: noreply@hacktcnj.com");
mail("hacktcnj.general@getfranke.com", "HackTCNJ - Daily Waitlist Report!", $msg, "From: noreply@hacktcnj.com");

echo $msg;
?>