<?php
###############################################################
###
###         VARIABLES
###
###############################################################
$domainname= "home.lab";
$user = "administrator@".$domainname;
$pass = "Passw0rd!!!";
$server = 'ldaps://192.168.5.3';
$port="636";
$binddn = "DC=home,DC=lab";
$searchbase = "DC=home,DC=lab";


###############################################################
###
###         BIND
###
###############################################################
$ldap = ldap_connect($server);
ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
ldap_set_option($ldap, LDAP_OPT_X_TLS_REQUIRE_CERT, LDAP_OPT_X_TLS_NEVER);
ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);

// ldap_start_tls($ldap);

$bind=ldap_bind($ldap, $user, $pass);
if (!$bind) {
    exit('Binding failed');
}

###############################################################
###
###         SEARCH
###
###############################################################
ldapsearchuser("administrator",$searchbase,$domainname,$ldap);
ldapsearch("cn=administrator",$searchbase,$domainname,$ldap,["cn","samaccountname"]);

###############################################################
###
###         functions:
###
###############################################################

# USAGE:
# ldapsearch($filter,$domainname,$ldap,$attributes);
function ldapsearch($filter,$dn,$domainname,$ldap,$attributes) {
    $search = ldap_search($ldap, $dn, $filter, $attributes);
    $info = ldap_get_entries($ldap, $search);
    print_r($info) ;
}


# ldapadduser("cn=administrator,DC=example,DC=lab",$domainname,$ldap);
function ldapsearchuser($cn,$dn,$domainname,$ldap) {
    $dn_user="CN=".$cn;
    $search = ldap_search($ldap, $dn, $dn_user);
    $info = ldap_get_entries($ldap, $search);
    print_r($info) ;
}
