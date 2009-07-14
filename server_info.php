<?php
    // get server version and implementation strings
    if (($service = ibase_service_attach('localhost', 'sysdba', 'masterkey')) != FALSE) {
        $server_info  = ibase_server_info($service, IBASE_SVC_SERVER_VERSION) 
                      . ' / '
                      . ibase_server_info($service, IBASE_SVC_IMPLEMENTATION);
        ibase_service_detach($service);
    }
    else {
        $ib_error = ibase_errmsg();
    }
echo $server_info;   
?>
