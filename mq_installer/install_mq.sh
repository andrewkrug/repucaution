#!/bin/bash

# this script must be run from directory, where it is located

# ---------------------------------------
# DEFINE
# ---------------------------------------

# project root directory
# this script must be run from it's directory
# directory must be in the project's root
projectdir="$(dirname "$PWD")"

# check if parameter passed to define script namespaces for cron, supervisord, etc
if [ -n "$1" ]; then
    ns=$1
else
    ns="ik-script"
fi
# underscore version
ns_u=${ns//-/_}

# ---------------------------------------
# CLEAR
# ---------------------------------------

# remove cron script config
rm -rf /etc/cron.d/$ns

# remove supervisord config and daemon managing script
rm -rf /etc/supervisord.conf
rm -rf /etc/init.d/supervisord
rm -rf /var/log/supervisord_${ns_u}

update-rc.d supervisord -f remove

# ---------------------------------------
# SUPERVISORD
# ---------------------------------------

# install python-setuptools from repository to be able to use easy_install
apt-get install python-setuptools -y

# install supervisor
easy_install supervisor

# generate default supervisor config if it does not exist
if [ ! -f /etc/supervisord.conf ]; then
    echo_supervisord_conf > /etc/supervisord.conf
fi

# programm for running mq
supervisordstring="
[program:${ns_u}_mq_router]
command=/usr/bin/php index.php mq/mq_router
process_name= ${ns_u}_mq_router
numprocs=1
directory=$projectdir
autostart=true           
autorestart=true         
user=root
stdout_logfile=/var/log/supervisord_${ns_u}/${ns_u}_mq_router/output.log
stdout_logfile_maxbytes=5MB
stderr_logfile=/var/log/supervisord_${ns_u}/${ns_u}_mq_router/error.log
stderr_logfile_maxbytes=5MB
startsecs=0

[program:${ns_u}_mq_router_convert]
command= /usr/bin/php index.php mq/mq_router/convert
process_name= ${ns_u}_mq_router_converter
numprocs=1
directory=$projectdir
autostart=true
autorestart=true
user=root
stdout_logfile=/var/log/supervisord_${ns_u}/${ns_u}_mq_router/convert_output.log
stdout_logfile_maxbytes=5MB
stderr_logfile=/var/log/supervisord_${ns_u}/${ns_u}_mq_router/convert_error.log
stderr_logfile_maxbytes=5MB
startsecs=0

[program:${ns_u}_mq_router_scheduled]
command= /usr/bin/php index.php mq/mq_router/scheduled
process_name= ${ns_u}_mq_router_scheduled
numprocs=1
directory=$projectdir
autostart=true           
autorestart=true         
user=root
stdout_logfile=/var/log/supervisord_${ns_u}/${ns_u}_mq_router/output-scheduled.log
stdout_logfile_maxbytes=5MB
stderr_logfile=/var/log/supervisord_${ns_u}/${ns_u}_mq_router/error-scheduled.log
stderr_logfile_maxbytes=5MB
startsecs=0

[program:${ns_u}_mq_router_mentions]
command= /usr/bin/php index.php mq/mq_router/mentions
process_name= ${ns_u}_mq_router_mentions
numprocs=1
directory=$projectdir
autostart=true           
autorestart=true         
user=root
stdout_logfile=/var/log/supervisord_${ns_u}/${ns_u}_mq_router/output-mentions.log
stdout_logfile_maxbytes=5MB
stderr_logfile=/var/log/supervisord_${ns_u}/${ns_u}_mq_router/error-mentions.log
stderr_logfile_maxbytes=5MB
startsecs=0

[program:${ns_u}_mq_router_social]
command= /usr/bin/php index.php mq/mq_router/social
process_name= ${ns_u}_mq_router_social
numprocs=1
directory=$projectdir
autostart=true           
autorestart=true         
user=root
stdout_logfile=/var/log/supervisord_${ns_u}/${ns_u}_mq_router/output-social.log
stdout_logfile_maxbytes=5MB
stderr_logfile=/var/log/supervisord_${ns_u}/${ns_u}_mq_router/error-social.log
stderr_logfile_maxbytes=5MB
startsecs=0

[program:${ns_u}_mq_router_google_rank]
command= /usr/bin/php index.php mq/mq_router/google_rank
process_name= ${ns_u}_mq_router_google_rank
numprocs=1
directory=$projectdir
autostart=true           
autorestart=true         
user=root
stdout_logfile=/var/log/supervisord_${ns_u}/${ns_u}_mq_router/output-google_rank.log
stdout_logfile_maxbytes=5MB
stderr_logfile=/var/log/supervisord_${ns_u}/${ns_u}_mq_router/error-google_rank.log
stderr_logfile_maxbytes=5MB
startsecs=0

[program:${ns_u}_mq_router_crm]
command= /usr/bin/php index.php mq/mq_router/crm
process_name= ${ns_u}_mq_router_crm
numprocs=1
directory=$projectdir
autostart=true           
autorestart=true         
user=root
stdout_logfile=/var/log/supervisord_${ns_u}/${ns_u}_mq_router/output-crm.log
stdout_logfile_maxbytes=5MB
stderr_logfile=/var/log/supervisord_${ns_u}/${ns_u}_mq_router/error-crm.log
stderr_logfile_maxbytes=5MB
startsecs=0

[program:${ns_u}_mq_router_rss]
command= /usr/bin/php index.php mq/mq_router/rss
process_name= ${ns_u}_mq_router_rss
numprocs=1
directory=$projectdir
autostart=true           
autorestart=true         
user=root
stdout_logfile=/var/log/supervisord_${ns_u}/${ns_u}_mq_router/output-rss.log
stdout_logfile_maxbytes=5MB
stderr_logfile=/var/log/supervisord_${ns_u}/${ns_u}_mq_router/error-rss.log
stderr_logfile_maxbytes=5MB
startsecs=0

[program:${ns_u}_mq_router_post_cron]
command= /usr/bin/php index.php mq/mq_router/post_cron
process_name= ${ns_u}_mq_router_post_cron
numprocs=1
directory=$projectdir
autostart=true           
autorestart=true         
user=root
stdout_logfile=/var/log/supervisord_${ns_u}/${ns_u}_mq_router/output-post_cron.log
stdout_logfile_maxbytes=5MB
stderr_logfile=/var/log/supervisord_${ns_u}/${ns_u}_mq_router/error-post_cron.log
stderr_logfile_maxbytes=5MB
startsecs=0
"
# appending programm for running mq to supervisor config
echo -e "$supervisordstring" >> /etc/supervisord.conf

# create supervisor log directory
mkdir -p /var/log/supervisord_${ns_u}/${ns_u}_mq_router

# copy supervisor manager script to default location
cp supervisord /etc/init.d/supervisord -a

# make script executable
chmod +x /etc/init.d/supervisord

# add script to startup
update-rc.d supervisord defaults

# ---------------------------------------
# CRON
# ---------------------------------------

# install cron from repository
apt-get install cron -y

# script cron config
cronstring="
* * * * * root php $projectdir/index.php cron minutely
*/10 * * * * root php $projectdir/index.php cron tenminutely
0 * * * * root php $projectdir/index.php cron hourly
0 */4 * * * root php $projectdir/index.php cron fourhourly
0 0 * * * root php $projectdir/index.php cron daily
"

# add individual cron config for script
echo -e "$cronstring" >> /etc/cron.d/$ns

# make script executable
chmod +x /etc/cron.d/$ns

# RESTART

# ---------------------------------------
# STOP
# ---------------------------------------

# stop supervisord
service supervisord stop

# ---------------------------------------
# RESTART
# ---------------------------------------

# restart cron
/etc/init.d/cron restart

# unlink supervisor if it is already running
unlink /tmp/supervisor.sock

# start/restart supervisor
service supervisord start