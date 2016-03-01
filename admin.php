<?php
/**
 * @author Kevin Bohinski <bohinsk1@tcnj.edu>
 * @version 1.0
 * @since 2016-1-3
 *
 * Project Name:  HackTCNJ 2016
 * Description:   HackTCNJ 2016 registration site.
 *
 * Filename:      /admin.php
 * Description:   Admin script for HackTCNJ 2016 site.
 * Last Modified: 2016-1-3
 *
 * Copyright (c) 2015 Kevin Bohinski. All rights reserved.
 */

$CURRENT_PAGE = "dashboard";
require_once('WebContent/db.php');
session_start();

$sql = "select * from hackers where id = '{$_SESSION['id']}'";
$result = mysqli_query($db, $sql) or die('Query failed: ' . mysqli_error($db));
$row = mysqli_fetch_assoc($result);

/* If not logged in, or not an admin take them to their dashboard */
if (!isset($_SESSION['id']) || !$row['admin']) {
    header("Location: dashboard.php");
    exit();
}

if ($row['admin']) {
    $_SESSION['admin'] = true;
}

$FLUID = true;
require_once('header.php');

$waitlist_count = 0;
$total_count = 0;
$checked_in_count = 0;

$xs_count = 0;
$s_count = 0;
$m_count = 0;
$l_count = 0;
$xl_count = 0;
?>

<h1>Admin Dashboard</h1>

<script>
    var default_err = '<strong>Error!</strong></br>Your last action was unsuccessful!';
    var default_err_title = 'Error!';
    var default_err_message = 'Action was unsuccessful!';

    var success_color = "#18BC9C";
    var error_color = "#E74C3C";

    /* Drop, Checkin, Drop Admin, Promote Admin, Promote Waitlist */

    $(document).ready(function () {
        $("a.ajax_checkin").click(function (e) {
            e.preventDefault();
            var id = $(this).data('id');
            console.log("checkingin " + id);
            ajax_checkin(id);
        });

        $("a.ajax_drop").click(function (e) {
            e.preventDefault();
            var id = $(this).data('id');
            console.log("droppingadmin " + id);
            ajax_drop(id, "drop");
        });

        $("a.ajax_dropadmin").click(function (e) {
            e.preventDefault();
            var id = $(this).data('id');
            console.log("droppingadmin " + id);
            ajax_admin(id, "drop");
        });

        $("a.ajax_makeadmin").click(function (e) {
            e.preventDefault();
            var id = $(this).data('id');
            console.log("makingadmin " + id);
            ajax_admin(id, "make");
        });

        $("a.ajax_promote").click(function (e) {
            e.preventDefault();
            var id = $(this).data('id');
            console.log("promoting " + id);
            ajax_promote(id);
        });
    });

    function ajax_promote(id) {
        /* Hide Confirmations */
        $('#confirmation').hide();
        $('#err').hide();

        swal({
            title: "Promote hacker " + id + " off the waitlist?",
            text: "Are you sure you wish to promote this hacker off the waitlist?",
            type: "info",
            showCancelButton: true,
            closeOnConfirm: false,
            confirmButtonText: "Yes, promote!",
            confirmButtonColor: success_color
        }, function () {
            /* Make the AJAX call */
            $.ajax({
                    type: "POST",
                    url: "ajax_promotewaitlist.php",
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
                        /* Display confirmation */
                        $('#confirmation').fadeIn();
                        title = "Promoted!";
                        msg = "The hacker was successfully promoted off the waitlist!";
                        type = "success";
                    } else {
                        /* Display confirmation */
                        var htmlStr = $('#err').html;
                        htmlStr = default_err + '<br>' + JSON.stringify(data);
                        $('#err').html(htmlStr);
                        $('#err').fadeIn();

                        title = default_err_title;
                        msg = "Failure promoting! Error Message:" + JSON.stringify(data);
                        type = "error";
                    }

                    swal(title, msg, type);

                    if (data.status == 'success') {
                        /* Display confirmation */
                        $('#confirmation').fadeIn();
                    } else {
                        /* Display confirmation */
                        var htmlStr = $('#err').html;
                        htmlStr = default_err + '<br>' + JSON.stringify(data);
                        $('#err').html(htmlStr);
                        $('#err').fadeIn();
                    }
                    if (data.status == 'success' && data.action == 'waitlist') {
                        var htmlStr = $('#users tr[data-id="' + data.id + '"]').html();
                        var tmp = htmlStr.split('data-field="waitlist">');
                        var tmp2 = tmp[1].split('</td>');
                        tmp2[0] = 0;
                        htmlStr = tmp[0] + 'data-field="waitlist">';
                        for (var i = 0; i < tmp2.length; i++) {
                            htmlStr += tmp2[i];
                        }
                        var tmp3 = htmlStr.split('<li><a class="ajax_promote" data-id="' + data.id + '" href="#">Promote from waitlist</a></li>');
                        htmlStr = tmp3[0] + tmp3[1];

                        $('#users tr[data-id="' + data.id + '"]').html(htmlStr);
                    }
                })
                .error(function (data) {
                    swal("Oops!", "We ran into an error!<br>" + '<div class="alert alert-danger"><strong>Error JSON</strong><br>' + JSON.stringify(data) + "</div>", "error");
                });
        });
    }

    function ajax_admin(id, action) {
        /* Hide Confirmations */
        $('#confirmation').hide();
        $('#err').hide();

        swal({
            title: "Modify:" + action + " administrative privileges on hacker " + id + " ?",
            text: "Are you sure you wish to modify:" + action + " this hacker's administrative privileges?",
            type: "warning",
            showCancelButton: true,
            closeOnConfirm: false,
            confirmButtonText: "Yes, " + action + "!",
            confirmButtonColor: error_color
        }, function () {
            /* Make the AJAX call */
            $.ajax({
                    type: "POST",
                    url: "ajax_admin.php",
                    data: {
                        'id': id,
                        'action': action
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
                        /* Display confirmation */
                        $('#confirmation').fadeIn();
                        title = "Modified!";
                        msg = "The hacker's administrative privileges have been modified:" + action + "!";
                        type = "success";
                    } else {
                        /* Display confirmation */
                        var htmlStr = $('#err').html;
                        htmlStr = default_err + '<br>' + JSON.stringify(data);
                        $('#err').html(htmlStr);
                        $('#err').fadeIn();

                        title = default_err_title;
                        msg = "Failure modifying hacker's administrative privileges :" + action + "! Error Message:" + JSON.stringify(data);
                        type = "error";
                    }

                    if (data.status == 'success') {
                        /* Display confirmation */
                        $('#confirmation').fadeIn();
                    } else {
                        /* Display confirmation */
                        var htmlStr = $('#err').html;
                        htmlStr = default_err + '<br>' + JSON.stringify(data);
                        $('#err').html(htmlStr);
                        $('#err').fadeIn();
                    }

                    swal({
                        title: title,
                        text: msg,
                        type: type
                    }, function () {
                        window.location.reload(true);
                    });
                })
                .error(function (data) {
                    swal("Oops!", "We ran into an error!<br>" + '<div class="alert alert-danger"><strong>Error JSON</strong><br>' + JSON.stringify(data) + "</div>", "error");
                });
        });
    }

    function ajax_drop(id) {
        /* Hide Confirmations */
        $('#confirmation').hide();
        $('#err').hide();

        swal({
            title: "Drop hacker " + id + " ?",
            text: "Are you sure you wish to drop this hacker's application?",
            type: "warning",
            showCancelButton: true,
            closeOnConfirm: false,
            confirmButtonText: "Yes, drop!",
            confirmButtonColor: error_color
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
                        /* Display confirmation */
                        $('#confirmation').fadeIn();
                        title = "Dropped!";
                        msg = "The hacker's application was successfully dropped!";
                        type = "success";
                    } else {
                        /* Display confirmation */
                        var htmlStr = $('#err').html;
                        htmlStr = default_err + '<br>' + JSON.stringify(data);
                        $('#err').html(htmlStr);
                        $('#err').fadeIn();

                        title = default_err_title;
                        msg = "Failure dropping application! Error Message:" + JSON.stringify(data);
                        type = "error";
                    }

                    swal(title, msg, type);

                    if (data.status == 'success') {
                        $('#confirmation').fadeIn();
                    } else {
                        var htmlStr = $('#err').html;
                        htmlStr = default_err + '<br>' + JSON.stringify(data);
                        $('#err').html(htmlStr);
                        $('#err').fadeIn();
                    }
                    if (data.status == 'success' && data.action == 'drop') {
                        $('#users tr[data-id="' + data.id + '"]').remove();
                    }
                })
                .error(function (data) {
                    swal("Oops!", "We ran into an error!<br>" + '<div class="alert alert-danger"><strong>Error JSON</strong><br>' + JSON.stringify(data) + "</div>", "error");
                });
        });
    }

    function ajax_checkin(id) {
        /* Hide Confirmations */
        $('#confirmation').hide();
        $('#err').hide();

        swal({
            title: "Check in hacker " + id + " ?",
            text: "Are you sure you wish to check in this hacker?",
            type: "info",
            showCancelButton: true,
            closeOnConfirm: false,
            confirmButtonText: "Yes, check in!",
            confirmButtonColor: success_color
        }, function () {
            /* Make the AJAX call */
            $.ajax({
                    type: "POST",
                    url: "ajax_checkin.php",
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
                        /* Display confirmation */
                        $('#confirmation').fadeIn();
                        title = "Checked in!";
                        msg = "The hacker was successfully checked in!";
                        type = "success";
                    } else {
                        /* Display confirmation */
                        var htmlStr = $('#err').html;
                        htmlStr = default_err + '<br>' + JSON.stringify(data);
                        $('#err').html(htmlStr);
                        $('#err').fadeIn();

                        title = default_err_title;
                        msg = "Failure checking in! Error Message:" + JSON.stringify(data);
                        type = "error";
                    }

                    if (data.status == 'success' && data.action == 'checkin' && data.minor == 1) {
                        msg += " Hacker is a minor, please ensure they have the minor consent form!";
                    }

                    swal(title, msg, type);

                    if (data.status == 'success' && data.action == 'checkin') {
                        var htmlStr = $('#users tr[data-id="' + data.id + '"]').html();
                        var tmp = htmlStr.split('data-field="checkin">');
                        var tmp2 = tmp[1].split('</td>');
                        tmp2[0] = 1;
                        htmlStr = tmp[0] + 'data-field="checkin">';
                        for (var i = 0; i < tmp2.length; i++) {
                            htmlStr += tmp2[i];
                        }
                        var tmp3 = htmlStr.split('<li><a class="ajax_checkin" data-id="' + data.id + '" href="#">Check In</a></li>');
                        htmlStr = tmp3[0] + tmp3[1];

                        $('#users tr[data-id="' + data.id + '"]').html(htmlStr);
                    }
                })
                .error(function (data) {
                    swal("Oops!", "We ran into an error!<br>" + '<div class="alert alert-danger"><strong>Error JSON</strong><br>' + JSON.stringify(data) + "</div>", "error");
                });
        });
    }
</script>

<div id="confirmation" class="alert alert-success" style="display: none;"><strong>Success!</strong></br>Your last action
    was successful!
</div>
<div id="err" class="alert alert-danger" style="display: none;"><strong>Failure!</strong></br>Your last action was
    unsuccessful!
</div>

<table id="users" class="table table-striped table-hover table-condensed sortable">
    <thead>
    <tr>
        <th>Options</th>
        <th>Checked In</th>
        <th>Waitlist</th>
        <th>Minor</th>
        <th>MLH ID</th>
        <th>DB ID</th>
        <th>Email</th>
        <th>Name</th>
        <th>Phone</th>
        <th>Diet</th>
        <th>Special</th>
        <th>School</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $mlh_users = file_get_contents($MLH_USERS_JSON);
    $mlh_users = json_decode($mlh_users, true);
    $mlh_users = $mlh_users['data'];
    //$phoneUtil = \libphonenumber\PhoneNumberUtil::getInstance();
    foreach ($mlh_users as $u) {
        $sql = "select `checked_in`, `waitlisted`, `id`, `minor`,`admin` from hackers where mlh_id = '{$u['id']}'";
        $result = mysqli_query($db, $sql) or die('Query failed: ' . mysqli_error($db));
        $row = mysqli_fetch_assoc($result);

        if (isset($row['id'])) {
            if ($row['waitlisted']) {
                $waitlist_count++;
            }
            $total_count++;
            $tmp = explode(" ", $u['shirt_size']);
            if ($tmp[2] == "XS") {
                $xs_count++;
            } else if ($tmp[2] == "S") {
                $s_count++;
            } else if ($tmp[2] == "M") {
                $m_count++;
            } else if ($tmp[2] == "L") {
                $l_count++;
            } else if ($tmp[2] == "XL") {
                $xl_count++;
            } else {
                echo "Error while parsing shirt size";
            }
            echo '<tr data-id="' . $row['id'] . '">';
            echo '<td><div class="btn-group">
  <a href="#" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></a>
  <ul class="dropdown-menu">';
            if (!$row['checked_in']) {
                echo '<li><a class="ajax_checkin" data-id="' . $row['id'] . '" href="#">Check In</a></li>';
            } else {
                $checked_in_count++;
            }
            if ($row['waitlisted']) {
                echo '<li><a class="ajax_promote" data-id="' . $row['id'] . '" href="#">Promote from waitlist</a></li>';
            }
            echo '<li class="divider"></li>
    <li><a class="ajax_drop" data-id="' . $row['id'] . '" href="#">Drop Application</a></li>';
            if ($row['admin']) {
                echo '<li><a class="ajax_dropadmin" data-id="' . $row['id'] . '" href="#">Drop Admin</a></li>';
            } else {
                echo '<li><a class="ajax_makeadmin" data-id="' . $row['id'] . '" href="#">Promote to Admin</a></li>';
            }
            echo '<td data-field="checkin">' . $row['checked_in'] . '</td>';
            echo '<td data-field="waitlist">' . $row['waitlisted'] . '</td>';
            echo '<td>' . $row['minor'] . '</td>';
            echo '<td>' . $u['id'] . '</td>';
            echo '<td>' . $row['id'] . '</td>';
            echo '<td>' . $u['email'] . '</td>';
            echo '<td>' . $u['first_name'] . " " . $u['last_name'] . '</td>';
            echo '<td>' . $u['phone_number'] . '</td>';
            //$num = $phoneUtil->parse($u['phone_number'], "US");
            //echo '<td>' . $phoneUtil->format($num, \libphonenumber\PhoneNumberFormat::NATIONAL) . '</td>';
            echo '<td>' . $u['dietary_restrictions'] . '</td>';
            echo '<td>' . $u['special_needs'] . '</td>';
            echo '<td>' . $u['school']['name'] . '</td>';
            echo '</tr>';
        }
    }
    ?>
    </tbody>
</table>

<?php
echo '<br><h1>Total: ' . $total_count . '<br>Waitlist: ' . $waitlist_count . '<br>Attendees: ' . ($total_count - $waitlist_count) . '<br>Checked In: ' . $checked_in_count . '</h1><br>';
echo '<br><h2>XS: ' . $xs_count . '<br>S: ' . $s_count . '<br>M:' . $m_count . '<br>L: ' .$l_count . '<br>XL: ' . $xl_count . '</h2>';
?>

<br>

<a href="<?php echo $MLH_USERS_JSON; ?>">
    <button class="hvr-bounce-in btn-primary btn-lg">Get JSON object of users from MLH.<br><b>Do not share this url.</b>
    </button>
</a>

<p style="padding-top: 15px;">Back to dashboard? <a href="dashboard.php">Back</a>!</p>

<?php require_once('footer.php'); ?>
