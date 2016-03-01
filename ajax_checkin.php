<?php
/**
 * @author Kevin Bohinski <bohinsk1@tcnj.edu>
 * @version 1.0
 * @since 2016-1-27
 *
 * Project Name:  HackTCNJ 2016
 * Description:   HackTCNJ 2016 registration site.
 *
 * Filename:      /ajax_checkin.php
 * Description:   Checkin script for HackTCNJ 2016 site.
 * Last Modified: 2016-1-27
 *
 * Copyright (c) 2015 Kevin Bohinski. All rights reserved.
 */

require_once('WebContent/db.php');

$err = array();

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin']) && !$_SESSION['admin']) {
    $err['permissions'] = 'User does not have permission to perform this action.';
}

if (empty($_POST['id'])) {
    $err['id'] = 'Required field';
}

$id = mysql_entities_fix_string($db, $_POST['id']);

/* See if user is a minor, or already checked in in db */
$sql = "SELECT checked_in, minor, mlh_id FROM hackers WHERE id='{$id}'";
$result = mysqli_query($db, $sql) or die('Query failed: ' . mysqli_error($db));
$row = mysqli_fetch_assoc($result);
$checked_in = $row['checked_in'];
$mlh_id = $row['mlh_id'];
$minor = $row['minor'];

if ($checked_in) {
    $err['already_checked_in'] = "User has already been checked in.";
}

if (sizeof($err) == 0) {
    /* Checkin user on db */
    $sql = "UPDATE `hackers` SET `checked_in`=1 WHERE id='{$id}'";
    $result = mysqli_query($db, $sql) or die('Query failed: ' . mysqli_error($db));

    /* Get users info */
    $mlh_users = file_get_contents($MLH_USERS_JSON);
    $mlh_users = json_decode($mlh_users, true);
    $mlh_users = $mlh_users['data'];
    foreach ($mlh_users as $u) {
        if ($mlh_id == $u['id']) {
            $first = $u['first_name'];
            $last = $u['last_name'];
            $email = $u['email'];
        }
    }

    /* Send a welcome email */
    error_reporting(E_ALL & ~E_NOTICE);
    $msg = "Dear " . $first . " " . $last . ",\n\n";
    $msg .= "Thanks for checking in!\n";
    $msg .= "We will start shortly, please check your dashboard for updates!\n";
    $msg .= "\nPlease share with your friends and follow us on twitter for updates.\n";
    $msg .= "\nThanks Again!\nThe HackTCNJ Team\nhttp://www.twitter.com/hacktcnj";
    mail($email, "HackTCNJ - Thanks for checking in", $msg, "From: noreply@hacktcnj.com");

    /* Create JSON array */
    $json = array("status" => "success", "action" => "checkin", "minor" => $minor, "id" => $id);
} else {
    /* Create JSON array */
    $json = array("status" => "failure", "action" => "checkin", "err" => $err);
}

echo json_encode($json);
?>