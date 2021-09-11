#!/bin/bash
count=1
while [ 1 ]
do
	pid=$(pidof fingerprint)
	#echo $pid
	if [ ! $pid ]; then
		echo "Biometrics failed to run.."
		/projects/biometrics/fingerprint
		#if [ $count -eq 5 ]; then
		#	reboot
		#fi
		#((count++))
		#echo $count
	fi
	sleep 10
done
