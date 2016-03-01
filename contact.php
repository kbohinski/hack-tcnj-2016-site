<?php
/**
 * @author Kevin Bohinski <bohinsk1@tcnj.edu>
 * @version 1.0
 * @since 2015-12-1
 *
 * Project Name:  HackTCNJ 2016
 * Description:   HackTCNJ 2016 registration site.
 *
 * Filename:      /contact.php
 * Description:   Contact script for HackTCNJ 2016 site.
 * Last Modified: 2015-12-5
 *
 * Copyright (c) 2015 Kevin Bohinski. All rights reserved.
 */

$CURRENT_PAGE = "contact";
if (isset($_GET['sponsor'])) {
    $CURRENT_PAGE = "sponsor";
}
require_once('header.php');
?>

<script>
    /**
     * Listens for when the user submits the form.
     */
    $(document).ready(function () {
        $('#form').submit(function (e) {
            /* Don't submit the form */
            e.preventDefault();
            /* Send AJAX Request */
            sendAJAX();
        });
    });

    /**
     * Sends the request.
     */
    function sendAJAX() {
        /* Make the AJAX call */
        $.ajax({
            type: "POST",
            url: "ajax_email.php",
            data: $('#form').serialize(),
            dataType: "json",
            success: returnAJAX
        });
        /* Hide Confirmation */
        $('#confirmation').hide();
        /* Hide Errors */
        $('#err').css("visibility", "hidden");
        $('#submit').val("Sending your email...");
    }

    /**
     * Receive reply.
     */
    function returnAJAX(json) {
        /* Check for success */
        if (json.status == 'success') {
            /* Display confirmation */
            $('#confirmation').fadeIn();
            /* Remove any input */
            $('#email').val('');
            $('#msg').val('');
        } else {
            if (json.err.email) {
                $('#err').html(json.err.email);
                $('#err').css("visibility", "visible");
            }
        }
        /* Reset Indicator */
        $('#submit').val("Submit!");
    }
</script>

<?php
if (isset($_GET['sponsor'])) {
    echo '<h1>Sponsor Us!</h1>';
    echo '<p style="padding-top: 20px;">HackTCNJ is a hackathon, where student engineers, designers, and entrepreneurs gather to create innovative software projects.
HackTCNJ is a 24-hour event that is free for students to participate in.
Sponsor companies support the event by sending monetary donations and representatives.
We would love to work with you to be one of our valued sponsors for this event.
HackTCNJ is taking place on February 27th and 28th, 2016 at The College of New Jersey in Ewing, NJ (about an hour from New York City and 45 minutes from Philadelphia).


</p>
<p>HackTCNJ is on the smaller spectrum of hackathons, with about 200-300 hackers, which allows our sponsors to interact closely with hackers.
Each year, our hackathon attracts some of the most talented students from colleges and high schools in the tri-state area and the country.
There are many perks to being a sponsor for HackTCNJ including recruiting opportunities, great press coverage, and company exposure.
We use our sponsors\' investment wisely and we work hard to ensure that each sponsor gets the most out of their experience at HackTCNJ.
Non-monetary sponsorship opportunities are available, as every bit of support greatly benefits our event. </p>

<br>
<p style="padding-bottom: 20px;"><strong>We would love to work with you if you are interested in sponsoring us.</strong></br>Please contact us using the form below to start the conversation!</p>';
}
?>

<?php
if (!isset($_GET['sponsor'])) {
    echo '<h1 style="padding-bottom: 20px;">Contact Us</h1>';
}
?>

<div id="confirmation" class="alert alert-success" style="display: none;"><strong>Success!</strong></br>Your email has
    been sent!
</div>

<form id="form">
    <div class="form-group">
        <label for="email">What is your email address?</label>
        <input id="email" name="email" type="text" placeholder="Email" class="form-control"/>
        <div id="err" class="alert alert-danger" style="visibility: hidden;"></div>
    </div>
    <div class="form-group">
        <label for="msg">Please type out your message</label>
        <textarea name="msg" id="msg" placeholder="Message" class="form-control"></textarea>
    </div>
    <div class="form-group">
        <input name="submit" id="submit" type="submit" value="Send" class="btn btn-primary"/>
    </div>
</form>

<?php require_once('footer.php'); ?>
