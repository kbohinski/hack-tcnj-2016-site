<?php
include('cas/CAS.php');
//login - https://cas1.tcnj.edu:443/cas/login     (you don't need the specify the 443 unless they require the port #)
//logout - https://cas1.tcnj.edu:443/cas/logout
//validate - https://cas1.tcnj.edu:443/cas/serviceValidate
$cas_host = 'cas1.tcnj.edu';
$cas_port = 443;
$cas_context = '/cas';
phpCAS::client(CAS_VERSION_2_0, $cas_host, $cas_port, $cas_context);

// For production use set the CA certificate that is the issuer of the cert
// on the CAS server and uncomment the line below
// phpCAS::setCasServerCACert($cas_server_ca_cert_path);

// For quick testing you can disable SSL validation of the CAS server.
// THIS SETTING IS NOT RECOMMENDED FOR PRODUCTION.
// VALIDATING THE CAS SERVER IS CRUCIAL TO THE SECURITY OF THE CAS PROTOCOL!
phpCAS::setNoCasServerValidation();
?>