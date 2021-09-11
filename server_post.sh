#!/bin/sh
file="/var/txtalert/fp_id"
file2="/var/txtalert/fp_id_display"
while [ : ]
do
	if [ -f $file ]; then	
		cp $file $file2
		php -f /projects/biometrics/server_post.php
		rm -f $file
	fi
	sleep 1
done
