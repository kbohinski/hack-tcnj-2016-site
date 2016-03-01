<?php
/**
 * @author Kevin Bohinski <bohinsk1@tcnj.edu>
 * @version 1.0
 * @since 2016-1-27
 *
 * Project Name:  HackTCNJ 2016
 * Description:   HackTCNJ 2016 registration site.
 *
 * Filename:      /ajax_promotewaitlist.php
 * Description:   Waitlist script for HackTCNJ 2016 site.
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

/* Get info from db */
$sql = "SELECT `mlh_id`, `waitlisted` FROM `hackers` WHERE `id`='{$id}'";
$result = mysqli_query($db, $sql) or die('Query failed: ' . mysqli_error($db));
$row = mysqli_fetch_assoc($result);
$mlh_id = $row['mlh_id'];
$waitlist = $row['waitlisted'];

if ($waitlist == 0) {
    $err['not_waitlisted'] = "User is not waitlisted.";
}

if (sizeof($err) == 0) {
    /* Modify user on db */
    $sql = "UPDATE `hackers` SET `waitlisted`=false WHERE `id`='{$id}'";
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
    $msg .= "You are off the waitlist!\n";
    $msg .= "Room has opened up, and you are now welcome to come, we look forward to seeing you!\n";
    $msg .= "Please accept the subscription request email we sent you with MailChimp.\n";
    $msg .= "\nPlease share with your friends and follow us on twitter for updates.\n";
    $msg .= "\nThanks Again!\nThe HackTCNJ Team\nhttp://www.twitter.com/hacktcnj";
    mail($email, "HackTCNJ - You're In!", $msg, "From: noreply@hacktcnj.com");

    /* Swap mailchimp lists */
    require_once('mc.php');
    $mc = new mc();
    $mc->removeEmail($email, $id, true);
    $mc->addEmail($email, $first, $last, $id, false);

    /* Create JSON array */
    $json = array("status" => "success", "action" => "waitlist", "id" => $id);
} else {
    /* Create JSON array */
    $json = array("status" => "failure", "action" => "waitlist", "err" => $err);
}

echo json_encode($json);
?>