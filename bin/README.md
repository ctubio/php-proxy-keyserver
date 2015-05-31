### sks-init.sh
To run your keyserver automatically at startup, run the following command:
```
 $ cp sks-init.sh /etc/init.d/sks
```

### sks-install-database.sh
After a fresh install of ```sks```, or at anytime that you need to download and install a new database from a recent online dump, run the following command:
```
 $ ./sks-install-database.sh
```
then, you may see the following example output:
```
 $ ./sks-install-database.sh
Before install a new database,
the following paths will be deleted:
 - /var/lib/sks/dump
 - /var/lib/sks/KDB
 - /var/lib/sks/PTree
Are you sure you want to delete this? [y/N]
> y
Please note, you have a database currently installed at /var/lib/sks/KDB
Are you sure you want to delete it before install a new database? [y/N]
> n
Nothing was deleted or downloaded.
all done.. Thank you!
```
If you choose to deleted/download/install a database, you will see all the files downloading first, and when they finish, you will be prompted for what type of installation do you preffer (fastbuild or normalbuild), you may want to choose the recommended second option, normalbuild.

After the database is installed, you may be able to start the sks daemons, and check the number of keys in your stats page.
Also, the content of /var/lib/sks/dump directory can be removed, and additionally, can be replaced by daily dumps of your own database using [sks-dump.sh](sks-dump.sh).

### sks-dump.sh
To create your own daily dumps of your working database, customize [sks-dump.sh](sks-dump.sh), and add a crontab line similar to:
```
0 1 * * * /var/www/your.domain.name/bin/sks-dump.sh &
```
### sks-dump-alert.sh
To send you an alert when the disk is almost full, customize [sks-dump-alert.sh](sks-dump-alert.sh), and add a crontab line similar to:
```
1 0 * * * /var/www/your.domain.name/bin/sks-dump-alert.sh
```
to prevent this from happening again, you must define a lower number of backups in [sks-dump.sh](sks-dump.sh).
