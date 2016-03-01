<?php
/**
 * @author Kevin Bohinski <bohinsk1@tcnj.edu>
 * @version 1.0
 * @since 2016-1-3
 *
 * Project Name:  HackTCNJ 2016
 * Description:   HackTCNJ 2016 registration site.
 *
 * Filename:      /update.php
 * Description:   User update script for HackTCNJ 2016 site.
 * Last Modified: 2016-1-3
 *
 * Copyright (c) 2015 Kevin Bohinski. All rights reserved.
 */

require_once('WebContent/db.php');
session_start();

/* If not logged in, take them to login page */
if (!isset($_SESSION['id'])) {
    header("Location: " . $MLH_LOGIN_URL);
    exit();
}

/* Run query to fill fields with info from db */
$sql = "SELECT `website`, `linkedin`, `github` FROM `hackers` WHERE id='{$_SESSION['id']}'";
$result = mysqli_query($db, $sql) or die('Query failed: ' . mysqli_error($db));
$row = mysqli_fetch_assoc($result);

/* Create an empty error array so we can inform the user on what the issue with their submission is */
$err = array();

/* If form is submitted */
if (isset($_POST['submit'])) {
    $errText = "Required field";
    $email = mysql_entities_fix_string($db, $_SESSION['mlh_user']['data']['email']);
    $first = mysql_entities_fix_string($db, $_SESSION['mlh_user']['data']['first_name']);
    $last = mysql_entities_fix_string($db, $_SESSION['mlh_user']['data']['last_name']);

    /* Upload new resume */
    if (!empty($_FILES["resume"])) {
        $f = $_FILES["resume"];

        if ($f["error"] !== UPLOAD_ERR_OK) {
            $err['resume'] = "An error occured";
        }

        $ext = pathinfo($_FILES['resume']['name'], PATHINFO_EXTENSION);

        if ($ext != "pdf" && $ext != "doc" && $ext != "docx" && $ext != "txt") {
            $err['resume'] = "Only pdf, doc, docx, or txt please";
        }

        $name = $first . "_" . $last . "_" . $email . "_" . "Resume" . "." . $ext;
        $success = move_uploaded_file($f["tmp_name"], UPLOAD_DIR . $name);

        if (!$success) {
            $err['resume'] = "An error occured";
        }
    } else {
        $err['resume'] = $errText;
    }

    /* If there are no errors, update user info */
    if (sizeof($err) == 0) {
        /* Clean input to avoid sql injection */
        $website = mysql_entities_fix_string($db, $_POST['website']);
        $linkedin = mysql_entities_fix_string($db, $_POST['linkedin']);
        $github = mysql_entities_fix_string($db, $_POST['github']);

        /* Update db */
        $sql = "UPDATE `hackers` SET `website`='{$website}',`linkedin`='{$linkedin}',`github`='{$github}' WHERE id='{$_SESSION['id']}'";
        $result = mysqli_query($db, $sql) or die('Query failed: ' . mysqli_error($db));

        /* Redirect them to the dashboard page, with a little success message. */
        header("Location: " . $dir . "/dashboard.php?success=true");
        exit();
    }
}

$CURRENT_PAGE = "dashboard";
require_once('header.php');
?>

    <h1>Update</h1>

    <form method="post" id="register-form" action="update.php" enctype="multipart/form-data">

        <div class="form-group">
            <label for="website">Website</label>
            <input id="website" name="website" type="url" value="<?php if (isset($row['website'])) {
                echo $row['website'];
            } ?>"
                   class="form-control"/>
        </div>

        <div class="form-group">
            <label for="linkedin">LinkedIn</label>
            <input id="linkedin" name="linkedin" type="url" value="<?php if (isset($row['linkedin'])) {
                echo $row['linkedin'];
            } ?>"
                   class="form-control"/>
        </div>

        <div class="form-group">
            <label for="github">GitHub</label>
            <input id="github" name="github" type="url" value="<?php if (isset($row['github'])) {
                echo $row['github'];
            } ?>"
                   class="form-control"/>
        </div>

        <div class="form-group">
            <label for="resume">Resume<span class="text-danger">*</span></label>
            <input id="resume" name="resume" type="file" class="form-control"/>
                    <span class="text-danger"><?php if (isset($err['resume'])) {
                            echo $err['resume'];
                        } ?></span>
        </div>

        <div class="form-group">
            <input style="margin-top: 8px;" name="submit" type="submit" value="Submit" class="btn btn-primary"/>
        </div>
    </form>

    <p style="padding-top: 15px;">Back to dashboard? <a href="dashboard.php">Back</a>!</p>

<?php require_once('footer.php'); ?>