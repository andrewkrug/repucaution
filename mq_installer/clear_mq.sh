#!/bin/bash

# ---------------------------------------
# CLEAR
# ---------------------------------------

# check if parameter passed to define script namespaces for cron, supervisord, etc
if [ -n "$1" ]; then
    ns=$1
else
    ns="ik-script"
fi
# underscore version
ns_u=${ns//-/_}

# remove activemq config folders
rm -rf /etc/activemq/instances-available/$ns
rm -rf /etc/activemq/instances-enabled/$ns

# remove cron script config
rm -rf /etc/cron.d/$ns

# remove supervisord config and daemon managing script
# rm -rf /etc/supervisord.conf
rm -rf /etc/init.d/supervisord
rm -rf /var/log/supervisord_${ns_u}

update-rc.d activemq -f remove
update-rc.d supervisord -f remove