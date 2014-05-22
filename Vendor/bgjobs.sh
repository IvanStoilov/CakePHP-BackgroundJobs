#!/bin/bash

INTERVAL=$1

if [ -z $1 ]
then
	INTERVAL=10
else
	INTERVAL=$1
fi

while [ 1 ]
do
	#./cake BackgroundJobs runQueued
	echo $INTERVAL
	sleep $INTERVAL
done
