#!/bin/bash

# This script will delete outdated backups, stop the sks server,
# dump its contents to the $OUTDIR, then restart the sks server.

HOSTNAME='pgp.key-server.io'
CONTACT='carles.tubio@key-server.io'
BACKUPS=7
USER="debian-sks"
GROUP="www-data"
INDIR="/var/lib/sks"
PREDIR="dump"
SKSDATE=`date +%Y-%m-%d`
OUTDIR="$INDIR/$PREDIR/$SKSDATE"
COUNT="10000"
TZ='UTC'

cd $INDIR;
for DEL in `ls -1t dump | grep -v current | tail -n +$((BACKUPS+1))`; do
  echo "Deleting old directory $PREDIR/$DEL";
  rm -rf $PREDIR/$DEL;
done;

/usr/sbin/service sks stop;
sleep 2;
if [ `ps -eaf | grep "sks " | grep -v 'grep sks' | wc -l` == "0" ]; then
  rm -rf $OUTDIR && mkdir -p $OUTDIR && \
  chown -R $USER:$GROUP $PREDIR && \
  time /usr/local/bin/sks dump $COUNT $OUTDIR/ sks-dump;

  if [ `ps -eaf | grep "sks " | grep -v 'grep sks' | wc -l` == "0" ]; then
    /usr/sbin/service sks start;
  else
    echo "Unable to start SKS since it was already running.";
    exit 1;
  fi;

  cd $PREDIR/;
  rm -f current;
  ln -s $OUTDIR current;
else
  echo "Unable run backup, SKS is still running.";
  exit 1;
fi;

SIZE=`du -shc $OUTDIR |grep 'total' |awk '{ print $1 }'`;
DCOUNT=`grep "#Key-Count" $OUTDIR/metadata-sks-dump.txt |awk '{ print $2 }'`;
FILES=`grep "#Files-Count" $OUTDIR/metadata-sks-dump.txt |awk '{ print $2 }'`;
echo "This is the PGP key server dump from ${HOSTNAME} created at: `date -u`

These files were created basically with the following command: $ sks dump $COUNT $SKSDATE/ sks-dump

The current archive size is approximately $SIZE, holding $DCOUNT keys in $FILES files.

On a linux/unix system, you may download this directory via the following command:

  $ cd /var/lib/sks
  $ rm -rf dump
  $ mkdir dump
  $ cd dump
  $ wget -c -r -p -e robots=off --timestamping --level=1 --cut-dirs=3 --no-host-directories https://${HOSTNAME}/dump/current/

After downloading the dump files, you can import them into a new database with the following command:

  $ cd /var/lib/sks
  $ rm -rf KDB PTree
  $ /usr/local/bin/sks_build.sh

and choose the option 2 (normalbuild).

If all goes smoothly you'll end up with KDB and PTree directories in /var/lib/sks, and you are ready to start the daemons.

The content of /var/lib/sks/dump directory can be removed, and additionally, can be replaced by daily dumps of your own database.

Also, if you would like to peer with this server, please send an email to <${CONTACT}> with your membership line." > $OUTDIR/README.txt;

cd $INDIR;
chown -R $USER:$GROUP $PREDIR;
