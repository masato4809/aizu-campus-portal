#!/bin/sh

# get current user setting.
USER=`grep apache /etc/passwd`

# set user if user is not exist.
if [ -z "$USER" ] && [ ! -z "$APACHE_UID" ]; then
  useradd -u $APACHE_UID  apache
  mkdir -m 755 /home/apache
  chown apache /home/apache
  export APACHE_RUN_USER=apache
fi

# start apache.
apache2-foreground

wait
