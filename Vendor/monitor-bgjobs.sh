#!/bin/bash

while [ 1 ]
do
	if [ `ps aux | grep bgjobs | grep -v grep | wc -l` == 0 ]
	then
		echo "no workers"
	fi
	sleep 5
done