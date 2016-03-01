<?php
/**
 * @author Kevin Bohinski <bohinsk1@tcnj.edu>
 * @version 1.0
 * @since 2016-1-18
 *
 * Project Name:  HackTCNJ 2016
 * Description:   HackTCNJ 2016 registration site.
 *
 * Filename:      /ajax_drop.php
 * Description:   Drop script for HackTCNJ 2016 site.
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
    if ($_POST['id'] != $_SESSION['id']) {
        $err['permissions'] = 'User does not have permission to perform this action.';
    }
}

if (empty($_POST['id'])) {
    $err['id'] = 'Required field';
}

$id = mysql_entities_fix_string($db, $_POST['id']);

if (sizeof($err) == 0) {
    /* Get user info from db */
    $sql = "SELECT * FROM `hackers` WHERE id='{$id}'";
    $result = mysqli_query($db, $sql) or die('Query failed: ' . mysqli_error($db));
    $row = mysqli_fetch_assoc($result);

    /* Delete user from db */
    $sql = "DELETE FROM `hackers` WHERE id='{$id}'";
    $result = mysqli_query($db, $sql) or die('Query failed: ' . mysqli_error($db));

    /* Delete users resume */
    $mlh_users = file_get_contents($MLH_USERS_JSON);
    $mlh_users = json_decode($mlh_users, true);
    $mlh_users = $mlh_users['data'];
    foreach ($mlh_users as $u) {
        if ($row['mlh_id'] == $u['id']) {
            $first = $u['first_name'];
            $last = $u['last_name'];
            $email = $u['email'];
        }
    }

    $name = $first . "_" . $last . "_" . $email . "_" . "Resume" . ".";

    $fileArray = array(
        $name . "txt",
        $name . "pdf",
        $name . "doc",
        $name . "docx"
    );

    foreach ($fileArray as $value) {
        if (file_exists(UPLOAD_DIR . $value)) {
            unlink(UPLOAD_DIR . $value);
        }
    }

    /* Delete user from mailchimp */
    require_once('mc.php');
    $mc = new mc();
    $mc->removeEmail($email, $id, $row['waitlisted']);

    /* Send an email */
    error_reporting(E_ALL & ~E_NOTICE);
    $msg = "Dear " . $first . " " . $last . ",\n\n";
    $msg .= "Your application was dropped, sorry to see you go.\n";
    $msg .= "\nPlease share with your friends and follow us on twitter for updates.\n";
    $msg .= "\nThanks Again!\nThe HackTCNJ Team\nhttp://www.twitter.com/hacktcnj";
    mail($email, "HackTCNJ - Application Dropped!", $msg, "From: noreply@hacktcnj.com");

    $msg = $first . " " . $last . "'s application has been dropped";
    mail("hacktcnj.general@getfranke.com", "HackTCNJ - Application Dropped!", $msg, "From: noreply@hacktcnj.com");

    /* Create JSON array */
    $json = array("status" => "success", "action" => "drop", "id" => $id);
} else {
    /* Create JSON array */
    $json = array("status" => "failure", "action" => "drop", "err" => $err);
}

echo json_encode($json);
?>