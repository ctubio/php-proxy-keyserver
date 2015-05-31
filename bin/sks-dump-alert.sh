#!/bin/bash
MAIL="carles.tubio@key-server.io"
THRESHOLD="80"
PARTITION="/dev/hdv1"

if (($(df -h / | grep "${PARTITION}" | awk '{ print $5 }' | sed 's/%//g') > ${THRESHOLD})); then
  echo "${PARTITION} at $(hostname -f) reached ${THRESHOLD}%" | \
    mail -s "${PARTITION} at $(hostname -f) reached ${THRESHOLD}%" "${MAIL}"
fi;
