<?php
###############################################################
###
###         VARIABLES
###
###############################################################
$domainname= "home.lab";
$user = "administrator@".$domainname;
$pass = "Passw0rd";
$server = 'ldaps://192.168.1.10';
$port="636";
$binddn = "DC=home,DC=lab";

###############################################################
###
###         MAIN
###
###############################################################
$ldap = ldap_connect($server);
ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
ldap_set_option($ldap, LDAP_OPT_X_TLS_REQUIRE_CERT, LDAP_OPT_X_TLS_NEVER);
ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);

ldap_start_tls($ldap);

$bind=ldap_bind($ldap, $user, $pass);
if (!$bind) {
    exit('Binding failed');
}

###############################################################
###
###         START TIME
###
###############################################################
$time_start = microtime(true);
echo "START: ".date("M,d,Y h:i:s A - ".$time_start) . $server . "\n";

###############################################################
###
###         SEARCH
###
###############################################################
ldapsearchuser("administrator",$searchbase,$domainname,$ldap);

###############################################################
###
###         STOP TIME
###
###############################################################
$time_stop = microtime(true);
echo "STOP: ".date("M,d,Y h:i:s A - ".$time_stop) . "\n";

###############################################################
###
###         functions:
###
###############################################################

# USAGE:

# ldapadduser("OU=bir,DC=yeni,DC=lab",$domainname,$ldap);
function ldapsearchuser($cn,$dn,$domainname,$ldap) {
    $dn_user="CN=".$cn;
    $search = ldap_search($ldap, $dn, $dn_user);
    $info = ldap_get_entries($ldap, $search);
    print_r($info) ;
}
