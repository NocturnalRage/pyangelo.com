#!/bin/sh

now=`date`
DT=`date +%Y%m%d`
echo "$now: Backing up Mysql databases on $DT"
mysqldump --all-databases --single-transaction --master-data=2 | gzip > /srv/http/pyangelo.com/backups/pyangelo-backup-$DT.sql.gz

find /srv/http/pyangelo.com/backups/ -name pyangelo-backup-\* -mtime +14 -exec rm {} \;
now=`date`
echo "$now: Mysql backup finished"

