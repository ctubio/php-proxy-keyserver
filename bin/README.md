### sks-init.sh
If your keyserver doesn't run automatically at startup, you may want to run the following command:
```
 $ sudo cp sks-init.sh /etc/init.d/sks
 $ sudo chmod +x /etc/init.d/sks
 $ sudo update-rc.d sks defaults
 $ sudo update-rc.d sks enable
```
In case you like to follow the output of the keyserver often, here is how to create for example the alias ```SKS```:
```
 $ echo "alias SKS='tail -f /var/lib/sks/db.log /var/lib/sks/recon.log';" >> ~/.bash_aliases
 $ source ~/.bash_aliases
```
or if you preffer to get rid of the logs, you can setup a crontab line like:
```
0 0 1 1 * ls /var/lib/sks/*.log -t1 | xargs truncate -s 0
```
### sks-install-database.sh
After a fresh install of ```sks```, or at anytime that you need to download and install a new database from a recent online dump, run the following command:
```
 $ ./sks-install-database.sh
```
then, you should step into an output similar to:
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
If you would like to create your own daily dumps of your working database, customize [sks-dump.sh](sks-dump.sh), and add a crontab line similar to:
```
59 23 * * * /var/www/your.domain.name/bin/sks-dump.sh &
```
Note: remove the last ampersand [&] if you want to receive an email with the output.

Please understand, in order to avoid downtimes, you must have your keyserver behind a load balancer running additional keyserver instances. That is because the dump process stops the keyserver daemons for about 1 or 2 minutes, locking all the database files to be able to export all keys, and when all files are finally generated, it starts the keyserver daemons again.

In order to generate the dump from a secondary keyserver instance (or if you have the webserver or ftp serving the dump in another machine), you can make use of [sks-dump-nfs.sh](sks-dump-nfs.sh), that is the same script but making the assumption that the dump directory is a remote directory mounted over [NFS](https://help.ubuntu.com/14.04/serverguide/network-file-system.html).

Remember that there's no need to duplicate nothing, and is possible to run remote sourced scripts from any machine in your private network with a crontab line similar to:
```
59 23 * * * bash -c "$(ssh user@host -C cat /var/www/your.domain.name/bin/sks-dump-nfs.sh)" &
```
