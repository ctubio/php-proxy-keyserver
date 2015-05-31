### sks-init.sh
To run your keyserver automatically at startup, please run the following command:
```bash
cp sks-init.sh /etc/init.d/sks
```
### sks-dump.sh
To create your own daily dumps, please customize [dump.sh](dump.sh), and add a crontab line for the root user similar to:
```crontab
0 1 * * * /var/www/pgp.key-server.io/bin/sks-dump.sh &
```
