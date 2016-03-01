<?php
/**
 * @author Kevin Bohinski <bohinsk1@tcnj.edu>
 * @version 1.0
 * @since 2016-1-18
 *
 * Project Name:  HackTCNJ 2016
 * Description:   HackTCNJ 2016 registration site.
 *
 * Filename:      /ajax/email.php
 * Description:   Email script for HackTCNJ 2016 site.
 * Last Modified: 2016-1-18
 *
 * Copyright (c) 2015 Kevin Bohinski. All rights reserved.
 */

$err = array();

if (empty($_POST['email'])) {
    $err['email'] = 'Required field';
}

if (sizeof($err) == 0) {
    /* Send email */
    $msg = "Email from contact form\n";
    $msg .= "From: " . $_POST['email'] . "\n";
    $msg .= "\n" . stripslashes($_POST['msg']);
    mail("acm@tcnj.edu", "Email from contact form", $msg, "From: noreply@hacktcnj.com");

    /* Create JSON array */
    $json = array("status" => "success");
} else {
    /* Create JSON array */
    $json = array("status" => "failure", "err" => $err);
}

echo json_encode($json);
?>