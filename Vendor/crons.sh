#!/bin/bash

INTERVAL=$1

if [ -z $1 ]
then
	INTERVAL=50
else
	INTERVAL=$1
fi

while [ 1 ]
do
	./cake BackgroundJobs.BackgroundWorker runCrons --quiet
	sleep $INTERVAL
done
