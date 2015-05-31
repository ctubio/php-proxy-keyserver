#! /bin/sh

DAEMON=/usr/local/bin/sks
DIR=/var/lib/sks

test -e $DAEMON || exit 0
test -d $DIR || exit 0

case "$1" in
        start)
                cd $DIR
                echo -n "Starting SKS:"
                echo -n \ sks_db
                $DAEMON db &
                echo -n \ sks_recon
                $DAEMON recon &
                echo "."
        ;;
        stop)
                echo -n "Stopping SKS:"
                killall sks
                while [ "`pidof sks`" ]; do sleep 1; done # wait until SKS processes have exited
                echo "."
        ;;
        restart)
                $0 stop
                sleep 1
                $0 start
        ;;
        *)
                echo "Usage: $0 {start|stop|restart}"
                exit 1
        ;;
esac

exit 0
