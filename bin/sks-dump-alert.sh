#!/bin/bash
mail="carles.tubio@key-server.io"
threshold="80"
partition="/dev/hdv1"

percent=$(df -h / | grep "$partition" | awk '{ print $5 }' | sed 's/%//g')
if ((percent > threshold))
then
  echo "$partition at $(hostname -f) reached $threshold%" | mail -s "$partition at $(hostname -f) reached $threshold%" "$mail"
fi
