#!/bin/sh

now=`date`
DT=`date +%Y%m%d`
echo "$now: Backing up Mysql databases on $DT"
mariadb-dump --all-databases --single-transaction --skip-lock-tables | gzip > /srv/http/pyangelo.com/backups/pyangelo-backup-$DT.sql.gz

find /srv/http/pyangelo.com/backups/ -name pyangelo-backup-\* -mtime +14 -exec rm {} \;
now=`date`
echo "$now: Mysql backup finished"

