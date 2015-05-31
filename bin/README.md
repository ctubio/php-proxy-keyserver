### dump.sh
To enable daily dumps, please customize [dump.sh](dump.sh), and add a crontab line for the root user similar to:
```crontab
0 1 * * * /var/www/pgp.key-server.io/bin/dump.sh &
```
