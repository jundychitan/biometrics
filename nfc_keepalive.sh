#!/bin/bash
timestamp(){
	date +"%Y-%m-%d %H:%M:%S"
}
nfc_enabled=0
defaults_dir=/projects/biometrics/defaults.cfg
while IFS= read -r line;
do
	param=$(echo $line |awk -F= '{print $1}' |tr -d " \"\t\r\n")
	value=$(echo $line |awk -F= '{print $2}' |tr -d " \"\t\r\n")
	#echo -e "*"$param"*"$value"*"
	if [ "$param" == "nfc_enabled" ]
	then	
		nfc_enabled=$value
	fi
done <$defaults_dir

if [ $nfc_enabled -eq 1 ]
then
	while [ : ]
	do
		/projects/biometrics/nfc_read.py
		/projects/biometrics/nfc_detect.py
	done
fi
