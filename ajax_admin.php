<?php
/**
 * @author Kevin Bohinski <bohinsk1@tcnj.edu>
 * @version 1.0
 * @since 2016-1-27
 *
 * Project Name:  HackTCNJ 2016
 * Description:   HackTCNJ 2016 registration site.
 *
 * Filename:      /ajax_admin.php
 * Description:   Admin script for HackTCNJ 2016 site.
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
if (empty($_POST['action'])) {
    $err['action'] = 'Required field';
}

$id = mysql_entities_fix_string($db, $_POST['id']);
$action = mysql_entities_fix_string($db, $_POST['action']);

if (sizeof($err) == 0) {
    if ($action == "make") {
        $sql = "UPDATE `hackers` SET `admin`=1 WHERE id='{$id}'";
    } else if ($action == "drop") {
        $sql = "UPDATE `hackers` SET `admin`=0 WHERE id='{$id}'";
    }

    /* Modify user on db */
    $result = mysqli_query($db, $sql) or die('Query failed: ' . mysqli_error($db));

    /* Create JSON array */
    $json = array("status" => "success", "action" => "admin", "id" => $id);
} else {
    /* Create JSON array */
    $json = array("status" => "failure", "action" => "admin", "err" => $err);
}

echo json_encode($json);
?>