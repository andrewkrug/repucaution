#!/bin/bash

# ---------------------------------------
# START
# ---------------------------------------

# check if parameter passed to define script namespaces for cron, supervisord, etc
if [ -n "$1" ]; then
    ns=$1
else
    ns="ik-script"
fi
# underscore version
ns_u=${ns//-/_}

# restart cron
/etc/init.d/cron restart

# start activemq
/etc/init.d/activemq start $ns

# unlink supervisor if it is already running
unlink /tmp/supervisor.sock

# start/restart supervisor
service supervisord start