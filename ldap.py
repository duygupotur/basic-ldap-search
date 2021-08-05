#!/usr/bin/python3
from ldap.controls import SimplePagedResultsControl
import ldap
import sys

## Connection 
domainIpAdress = "10.154.25.14"
domainDotFormat ="speed.lab"
domainDCFormat = "dc=" + ",dc=".join(domainDotFormat.split("."))
username = "administrator"
userdn = "cn=administrator,cn=users,dc=domain,dc=tr"
password = "Passw0rd"

def main(args):
    ## Set connection
    ldapConnection = setSambaConnection()
    
    ## Filter for domain users
    filter = "(&(&(objectClass=organizationalPerson)(!(isCriticalSystemObject=TRUE))(!(objectClass=computer))(!(objectClass=group))))"

    ## Get names of users in domain
    sambaSearchUsers = getObject(filter,ldapConnection,domainDCFormat,"name")

    ## Print result of search
    print(sambaSearchUsers)

    ## Close ldap connection
    ldapConnection.unbind()
    
def setSambaConnection():
    print("setSambaConnection")
    global ldapConnection

    try:
        host = 'ldaps://' + domainIpAdress + ':636'
        dn = username + '@' + domainDotFormat
        pw = password
        ldapConnection = ldap.initialize(host)
        ldapConnection.set_option(ldap.OPT_NETWORK_TIMEOUT, 20.0)
        ldapConnection.simple_bind_s(dn, pw)
        return ldapConnection

    except Exception as e:
        print("Samba/AD sunucusuna baglanamadi")
        sys.exit(e)

        
def getObject(filter,Connection,DCFormat,attirbute):
    ldapUsers = []
    page_control = SimplePagedResultsControl(True, size=1000, cookie='')

    base_dn =  DCFormat
    filter = filter
    attrs = [attirbute]
    response = Connection.search_ext(base_dn,ldap.SCOPE_SUBTREE,filter,attrs,serverctrls=[page_control])
    try:
        pages = 0
        while True:
            pages += 1
            rtype, rdata, rmsgid, serverctrls = Connection.result3(response)
            ldapUsers.extend(rdata)
            controls = [control for control in serverctrls if control.controlType == SimplePagedResultsControl.controlType]
            if not controls:
                print('The server ignores RFC 2696 control')
                break
            if not controls[0].cookie:
                break
            page_control.cookie = controls[0].cookie
            response = Connection.search_ext(base_dn,ldap.SCOPE_SUBTREE,filter, attrs,serverctrls=[page_control])

    except:
        print()
    return ldapUsers

if __name__ == '__main__':
    sys.exit(main(sys.argv))