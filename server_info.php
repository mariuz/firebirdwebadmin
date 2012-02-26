<?php
    // get server version and implementation strings
    if (($service = fbird_service_attach('localhost', 'sysdba', 'masterkey')) != FALSE) {
        $server_info  = fbird_server_info($service, IBASE_SVC_SERVER_VERSION) 
                      . ' / '
                      . fbird_server_info($service, IBASE_SVC_IMPLEMENTATION);
        fbird_service_detach($service);
    }
    else {
        $ib_error = fbird_errmsg();
    }
echo $server_info;   
?>
