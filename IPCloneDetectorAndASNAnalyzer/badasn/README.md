The list of bad ASNs primarily consists of ASNs considered to be VPN/Proxy/hosting/server/services that are outside of Internet service providers. 
To complete the list of bad ASNs, a method needs to be found, such as an SQL table that contains at least a column indicating if it is a VPN, 
and another column indicating the ASN. Then, a selection of all the VPNs should be made, and all the unique ASNs should be retrieved and added to 
the file list.txt.