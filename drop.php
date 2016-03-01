<?php
/**
 * @author Kevin Bohinski <bohinsk1@tcnj.edu>
 * @version 1.0
 * @since 2016-1-3
 *
 * Project Name:  HackTCNJ 2016
 * Description:   HackTCNJ 2016 registration site.
 *
 * Filename:      /drop.php
 * Description:   Drop script for HackTCNJ 2016 site.
 * Last Modified: 2016-1-3
 *
 * Copyright (c) 2015 Kevin Bohinski. All rights reserved.
 */

$CURRENT_PAGE = "dashboard";
require_once('WebContent/db.php');
require_once('mailchimp-api-php/src/Mailchimp.php');
session_start();

/* If not logged in, take them to their dashboard */
if (!isset($_SESSION['id'])) {
    header("Location: dashboard.php");
    exit();
}

require_once('header.php');
?>

<script>
    function ajax_drop(id) {
        swal({
            title: "Are you sure you want to drop your application?",
            text: "You will not be able to recover your application.",
            type: "warning",
            showCancelButton: true,
            closeOnConfirm: false,
            confirmButtonText: "Yes I'm sure",
            confirmButtonColor: "#E74C3C"
        }, function () {
            /* Make the AJAX call */
            $.ajax({
                    type: "POST",
                    url: "ajax_drop.php",
                    data: {
                        'id': id
                    },
                    dataType: "json",
                    success: function (data) {
                    }
                })
                .done(function (data) {
                    var title = "";
                    var msg = "";
                    var type = "";

                    if (data.status == 'success') {
                        title = "Dropped!";
                        msg = "Your application was dropped, sorry to see you go.";
                        type = "success";
                    } else {
                        title = "Error!";
                        msg = "Failure dropping application! Error Message:" + JSON.stringify(data);
                        type = "error";
                    }

                    swal(title, msg, type);

                    if (data.status == 'success') {
                        window.location = "<?php echo $dir; ?>/index.php?drop=true";
                    }
                })
                .error(function (data) {
                    swal("Oops!", "We ran into an error!<br>" + '<div class="alert alert-danger"><strong>Error JSON</strong><br>' + JSON.stringify(data) + "</div>", "error");
                });
        });
    }

    /**
     * Listens for when the user submits the form.
     */
    $(document).ready(function () {
        $('#form').submit(function (e) {
            /* Don't submit the form */
            e.preventDefault();
            /* Send AJAX Request */
            ajax_drop(<?php echo $_SESSION['id']; ?>);
        });
    });
</script>

<h1>Drop Application</h1>

<h2>Are you sure you want to drop your application? <b>You will not be able to recover your application.</b></h2>
<form method="post" id="form">
    <div class="form-group">
        <input style="margin-top: 8px;" name="submit" type="submit" value="Yes I'm sure" class="btn-danger btn-lg"/>
    </div>
</form>

<a href="<?php echo $dir; ?>/dashboard.php"><button class="btn-primary btn-lg">No! Get me out of here</button></a>

<p style="padding-top: 15px;">Back to dashboard? <a href="dashboard.php">Back</a>!</p>

<?php require_once('footer.php'); ?>
