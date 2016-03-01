<?php
/**
 * @author Kevin Bohinski <bohinsk1@tcnj.edu>
 * @version 1.0
 * @since 2015-12-1
 *
 * Project Name:  HackTCNJ 2016
 * Description:   HackTCNJ 2016 registration site.
 *
 * Filename:      /dashboard.php
 * Description:   Dashboard page for HackTCNJ 2016 site.
 * Last Modified: 2015-12-4
 *
 * Copyright (c) 2015 Kevin Bohinski. All rights reserved.
 */

require_once('WebContent/db.php');
require_once('mc.php');
session_start();

/* If not logged in, take them to the login page */
if (!isset($_SESSION['id'])) {
    header("Location: " . $MLH_LOGIN_URL);
    exit();
}

$CURRENT_PAGE = "dashboard";
require_once('header.php');

$mc = new mc();
?>

    <h1>Hey, <?php if(isset($_SESSION['first_name'])) {echo $_SESSION['first_name'];} ?></h1>
    <h2 style="padding-bottom: 20px;">Welcome to your application dashboard.</h2>

    <?php
        /*{
            "data": {
                "id": 1,
                "email": "test@example.com",
                "created_at": "2015-07-08T18:52:43Z",
                "updated_at": "2015-07-27T19:52:28Z",
                "first_name": "John",
                "last_name": "Doe",
                "graduation": "2012-09-01",
                "major": "Computer Science",
                "shirt_size": "Unisex - L",
                "dietary_restrictions": "None",
                "special_needs": "None",
                "date_of_birth": "1985-10-18",
                "gender": "Male",
                "phone_number": "+1 555 555 5555",
                "school": {
                    "id": 1,
                  "name": "Rutgers University"
                }
          }
        }*/
        $sql = "select * from hackers where id = '{$_SESSION['id']}'";
        $result = mysqli_query($db, $sql) or die('Query failed: ' . mysqli_error($db));
        $row = mysqli_fetch_assoc($result);

        if (isset($_GET['success'])) {
            echo '<div class="alert alert-success"><b>Success</b></br>Your last action was successful!</div>';
        }

        if ($row['waitlisted'] == 0) {
            echo '<div class="alert alert-success"><b>Application Status</b></br>You are not waitlisted, we look forward to seeing you!</div>';
        } else {
            echo '<div class="alert alert-warning"><b>Application Status</b></br>You are waitlisted, we will let you know if space opens up!</div>';
        }

        if ($row['minor'] == 1) {
            echo '<div class="alert alert-warning"><b>Minor Consent</b></br>Looks like you are a minor. Please remember to fill out the minor consent form!</div>';
        }
    ?>

    <h3><b>Day Of:</b></h3>
    <a href="https://goo.gl/11CJhG"><p>Introduction Slidedeck</p></a>

    <h3 style="margin-top: 65px;"><b>Schedule</b></h3>
    <img class="hvr-bob logo img-responsive" src="./WebContent/schedule1.png" alt="HackTCNJ Schedule"/>
    <br>
    <img class="hvr-bob logo img-responsive" src="./WebContent/schedule2.png" alt="HackTCNJ Schedule"/>

    <h3 style="margin-top: 65px;"><b>Your available actions:</b></h3>
        <a href="<?php echo $dir; ?>/update.php"><p>Update Resume, Website, LinkedIn, or Github</p></a>
        <a href="https://my.mlh.io/edit"><p>Update MyMLH</p></a>
        <a href="<?php echo $dir; ?>/drop.php"><p>Drop Application</p></a>
        <a href="<?php echo $dir; ?>/contact.php"><p>Contact Us</p></a>
        <a href="<?php if($row['waitlisted']){echo $mc->getWaitlistArchive();} else {echo $mc->getArchive();} ?>"><p>View Past Emails</p></a>
        <a href="<?php echo $dir; ?>/policies.php"><p>View Code of Conducts and Privacy Policies</p></a>
        <?php if ($row['admin']) {echo '<a href="' . $dir . '/admin.php"><p>Admin Dashboard</p></a>';} ?>

<?php require_once('footer.php'); ?>