<?php
/**
 * @author Kevin Bohinski <bohinsk1@tcnj.edu>
 * @version 1.0
 * @since 2016-1-3
 *
 * Project Name:  HackTCNJ 2016
 * Description:   HackTCNJ 2016 registration site.
 *
 * Filename:      /register_mlh.php
 * Description:   Registration script for HackTCNJ 2016 site.
 * Last Modified: 2016-1-3
 *
 * Copyright (c) 2015 Kevin Bohinski. All rights reserved.
 */

session_start();
require_once('WebContent/db.php');

/* If already logged in, take them to their dashboard */
if (isset($_SESSION['id'])) {
    header("Location: dashboard.php");
    exit();
}

/* If we got a reply from MLH OAuth store it and continue */
if (isset($_GET['code'])) {
    $url = "https://my.mlh.io/oauth/token?client_id=" . $MLH_APP_ID . "&client_secret=" . $MLH_SECRET . "&code=" . $_GET['code'] . "&redirect_uri=" . $dir . "/register_mlh.php&grant_type=authorization_code";

    $options = array(
        'http' => array(
            'header'  => "Content-type: application/json\r\n",
            'method'  => 'POST',
        ),
    );
    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);

    $tmp = explode("\"access_token\":\"", $result);
    $tmp = explode("\",\"", $tmp[1]);
    $token = $tmp[0];

    $userjson = file_get_contents($MLH_API_URL . "/user?access_token=" . $token);
    $_SESSION['mlh_user'] = json_decode($userjson, true);

    /* Check if user exists in db */
    $mlh_id = mysql_entities_fix_string($db, $_SESSION['mlh_user']['data']['id']);
    $sql = "select id from hackers where mlh_id = '{$mlh_id}'";
    $result = mysqli_query($db, $sql) or die('Query failed: ' . mysqli_error($db));
    $row = mysqli_fetch_assoc($result);
    if (mysqli_num_rows($result) > 0) {
        $_SESSION['id'] = $row['id'];
        $_SESSION['mlh_id'] = $mlh_id;
        $_SESSION['first_name'] = mysql_entities_fix_string($db, $_SESSION['mlh_user']['data']['first_name']);;
        header("Location: dashboard.php");
    }

    header("Location: register_start.php");
    exit();
}

$CURRENT_PAGE = "register";
require_once('header.php');
?>

    <h1>My MLH</h1>

    <h3>As a MLH Member Event we support the MyMLH OAuth system to register for events. Please use the button below to authenticate with MyMLH.</h3>

    <br>

    <b>By registering, you agree to abide and conform to the MLH Code of Conduct, and the TCNJ ACM Code of Conduct while at the event.</b></br>
    <i>We participate in Major League Hacking (MLH) as a MLH Member Event. You authorize us to share certain application/registration information for event administration, ranking, MLH administration, and occasional messages about hackathons in line with the <a href="https://mlh.io/privacy">MLH Privacy Policy</a>.</i>
    <br>
    <br>

    <?php echo '<a href="' . $MLH_LOGIN_URL . '" class="hvr-bounce-in btn-primary btn-xl">Login with MyMLH</a>'; ?>


    <div style="margin-top: 45px;">
        <img class="img-responsive" style="max-height: 60px; margin:auto;" src="./WebContent/s1.png" alt="Step 1 of 3"/>
        <p style="font-size: 20px;"><b>Step 1 of 3 to register.</b></p>
    </div>

<?php require_once('footer.php'); ?>