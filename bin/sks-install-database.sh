#!/bin/bash
SKS_PATH="/var/lib/sks"
DUMP_PATH="dump"

CURRENT_DUMP_URL="http://pgp.key-server.io/dump/current/"

test ! -e ${SKS_PATH} && echo "Error: install sks first or if you already compiled it manually, create /var/lib/sks before install the database. && exit 1;
echo "Before install a new database,"
echo "the following paths will be deleted:";
echo " - ${SKS_PATH}/${DUMP_PATH}";
echo " - ${SKS_PATH}/KDB";
echo " - ${SKS_PATH}/PTree";
read -p "Are you sure you want to delete this? [y/N] `echo $'\n> '`" -n 1 -r && echo;
case $REPLY in
  [yY][eE][sS]|[yY])
    test -e ${SKS_PATH}/KDB && echo "Please note, you have a database currently installed at ${SKS_PATH}/KDB" && \
    read -p "Are you sure you want to delete it before install a new database? [y/N] `echo $'\n> '`" -n 1 -r && echo;
    ;;
esac;
case $REPLY in
  [yY][eE][sS]|[yY])
    cd ${SKS_PATH} && rm -rf ${DUMP_PATH} KDB PTree && \
    mkdir ${SKS_PATH}/${DUMP_PATH} && cd ${SKS_PATH}/${DUMP_PATH} && \
    wget -c -r -p -e robots=off --timestamping --level=1 --cut-dirs=3 --no-host-directories ${CURRENT_DUMP_URL} && \
    md5sum --strict --check metadata-sks-dump.txt && \
    cd ${SKS_PATH} && \
    /usr/local/bin/sks_build.sh;
    ;;
  *)
    echo "Nothing was deleted or downloaded.";
    ;;
esac;
if [ "$?" -ne "0" ]; then
  echo "oh well, something went wrong.. Please see the output above.";
else
  echo "all done.. Thank you!";
fi;
