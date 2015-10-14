#!/bin/bash

# ---------------------------------------
# STOP
# ---------------------------------------

# check if parameter passed to define script namespaces for cron, supervisord, etc
if [ -n "$1" ]; then
    ns=$1
else
    ns="ik-script"
fi
# underscore version
ns_u=${ns//-/_}

# stop supervisord
service supervisord stop

# stop activemq if running
/etc/init.d/activemq stop $ns